<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Reclamation;
use App\Models\Formation;
use App\Models\User;
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewReclamationNotification;
use Spatie\Permission\Models\Role; 

class ReclamationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:reclamation-list|reclamation-create|reclamation-edit|reclamation-delete', ['only' => ['index', 'show']]);
        $this->middleware('permission:reclamation-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:reclamation-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:reclamation-delete', ['only' => ['destroy']]);
        $this->middleware('permission:reclamation-assign', ['only' => ['assign']]);
        $this->middleware('permission:reclamation-respond', ['only' => ['respond']]);
    }

    /**
     * Display a listing of reclamations.
     */
    public function index(Request $request)
{
    try {
        $user = Auth::user();
        $query = Reclamation::with(['user', 'formation', 'assignedTo']);

        // Filter based on role
        if ($user->hasRole('Etudiant') || $user->hasRole('Consultant')) {
            // Students and Consultants see only their own reclamations
            $query->where('user_id', $user->id);
        } elseif ($user->hasRole('Équipe Technique')) {
            // Équipe Technique sees only reclamations assigned to them
            $query->where('assigned_to', $user->id);
        }
        // Admin, Super Admin, Finance see all reclamations

        // Apply search filters
        if ($request->filled('search')) {
            $query->where(function($q) use ($request) {
                $q->where('subject', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Apply status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply category filter
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Apply priority filter
        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        // Apply formation filter
        if ($request->filled('formation_id')) {
            $query->where('formation_id', $request->formation_id);
        }

        // Order by status priority (ouverte, en_traitement, resolue, fermee), then by created_at
        $reclamations = $query->orderByRaw("
            CASE status
                WHEN 'ouverte' THEN 1
                WHEN 'en_traitement' THEN 2
                WHEN 'resolue' THEN 3
                WHEN 'fermee' THEN 4
                ELSE 5
            END
        ")
        ->orderBy('created_at', 'desc')
        ->paginate(10);
        
        $formations = Formation::select('id', 'title')->get();

        return view('reclamations.index', compact('reclamations', 'formations'));
    } catch (Exception $e) {
        Log::error('Error in ReclamationController@index: ' . $e->getMessage());
        return back()->with('error', 'Une erreur est survenue lors du chargement des réclamations.');
    }
}

    /**
     * Show the form for creating a new reclamation.
     */
    public function create()
    {
        try {
            $user = Auth::user();
            $formations = collect();

            if ($user) {
                if ($user->hasRole('Etudiant')) {
                    $formationIds = Inscription::where('user_id', $user->id)->pluck('formation_id');
                    $formations = Formation::whereIn('id', $formationIds)->where('status', 'published')->get();
                } elseif ($user->hasRole('Consultant')) {
                    $formations = Formation::where('consultant_id', $user->id)->where('status', 'published')->get();
                } else {
                    $formations = Formation::where('status', 'published')->get();
                }
            }
            
            return view('reclamations.create', compact('formations'));
        } catch (Exception $e) {
            Log::error('Error in ReclamationController@create: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du chargement du formulaire de création.');
        }
    }

    /**
     * Store a newly created reclamation in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'formation_id' => 'required|exists:formations,id',
                'category' => 'required|in:' . implode(',', array_keys(Reclamation::CATEGORIES)),
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|in:' . implode(',', array_keys(Reclamation::PRIORITIES))
            ]);

            // Create the reclamation
            $reclamation = Reclamation::create([
                'user_id' => Auth::id(),
                'formation_id' => $request->formation_id,
                'category' => $request->category,
                'subject' => $request->subject,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => 'ouverte'
            ]);

            // Send notification to admins
            $rolesToSendEmail = ['Admin', 'Finance', 'Super Admin'];
            $usersToNotify = User::role($rolesToSendEmail)->get();

            foreach ($usersToNotify as $user) {
                Mail::to($user->email)->send(new NewReclamationNotification($reclamation));
            }

            return redirect()->route('reclamations.index')
                ->with('success', 'Réclamation créée avec succès.');
        } catch (Exception $e) {
            Log::error('Error in ReclamationController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la création de la réclamation.');
        }
    }

    /**
     * Display the specified reclamation.
     */
    public function show(Reclamation $reclamation)
    {
        try {
            $user = Auth::user();

            // Authorization check
            if ($user->hasRole('Etudiant') || $user->hasRole('Consultant')) {
                if ($reclamation->user_id !== $user->id) {
                    abort(403, 'Accès non autorisé.');
                }
            } elseif ($user->hasRole('Équipe Technique')) {
                if ($reclamation->assigned_to !== $user->id) {
                    abort(403, 'Accès non autorisé.');
                }
            }

            $reclamation->load(['user', 'formation', 'assignedTo']);

            // Fetch users who can be assigned (only for Admin, Super Admin, Finance)
            $assignableUsers = collect();
            if ($user->hasAnyRole(['Admin', 'Super Admin', 'Finance'])) {
                $assignableUsers = User::role(['Admin', 'Finance', 'Super Admin', 'Équipe Technique'])->get();
            }
            
            return view('reclamations.show', compact('reclamation', 'assignableUsers'));
        } catch (Exception $e) {
            Log::error('Error in ReclamationController@show: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du chargement de la réclamation.');
        }
    }

    /**
     * Show the form for editing the specified reclamation.
     */
    public function edit(Reclamation $reclamation)
    {
        try {
            $user = Auth::user();

            // Authorization check
            if ($user->hasRole('Etudiant')) {
                if ($reclamation->user_id !== $user->id || $reclamation->status !== 'ouverte') {
                    abort(403, 'Accès non autorisé.');
                }
            } elseif ($user->hasRole('Consultant')) {
                if ($reclamation->user_id !== $user->id && $reclamation->assigned_to !== $user->id) {
                    abort(403, 'Accès non autorisé.');
                }
            } elseif ($user->hasRole('Équipe Technique')) {
                // Équipe Technique cannot edit, only respond
                abort(403, 'Accès non autorisé.');
            } elseif (!$user->hasAnyRole(['Admin', 'Super Admin', 'Finance'])) {
                if ($reclamation->status !== 'ouverte' && $reclamation->user_id !== $user->id && $reclamation->assigned_to !== $user->id) {
                    abort(403, 'Accès non autorisé.');
                }
            }

            $formations = Formation::where('status', 'published')->get();
            return view('reclamations.edit', compact('reclamation', 'formations'));
        } catch (Exception $e) {
            Log::error('Error in ReclamationController@edit: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du chargement du formulaire de modification.');
        }
    }

    /**
     * Update the specified reclamation in storage.
     */
    public function update(Request $request, Reclamation $reclamation)
    {
        try {
            $user = Auth::user();

            // Authorization check
            if ($user->hasRole('Etudiant')) {
                if ($reclamation->user_id !== $user->id || $reclamation->status !== 'ouverte') {
                    abort(403, 'Accès non autorisé.');
                }
            } elseif ($user->hasRole('Consultant')) {
                if ($reclamation->user_id !== $user->id && $reclamation->assigned_to !== $user->id) {
                    abort(403, 'Accès non autorisé.');
                }
            } elseif ($user->hasRole('Équipe Technique')) {
                // Équipe Technique cannot edit
                abort(403, 'Accès non autorisé.');
            } elseif (!$user->hasAnyRole(['Admin', 'Super Admin', 'Finance'])) {
                if ($reclamation->status !== 'ouverte' && $reclamation->user_id !== $user->id && $reclamation->assigned_to !== $user->id) {
                    abort(403, 'Accès non autorisé.');
                }
            }

            $request->validate([
                'formation_id' => 'required|exists:formations,id',
                'category' => 'required|in:' . implode(',', array_keys(Reclamation::CATEGORIES)),
                'subject' => 'required|string|max:255',
                'description' => 'required|string',
                'priority' => 'required|in:' . implode(',', array_keys(Reclamation::PRIORITIES))
            ]);

            $reclamation->update([
                'formation_id' => $request->formation_id,
                'category' => $request->category,
                'subject' => $request->subject,
                'description' => $request->description,
                'priority' => $request->priority
            ]);

            return redirect()->route('reclamations.index')
                             ->with('success', 'Réclamation mise à jour avec succès.');
        } catch (Exception $e) {
            Log::error('Error in ReclamationController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Une erreur est survenue lors de la mise à jour.');
        }
    }

    /**
     * Remove the specified reclamation from storage.
     */
    public function destroy(Reclamation $reclamation)
    {
        try {
            $user = Auth::user();

            // Only admins can delete
            if (!$user->hasAnyRole(['Admin', 'Super Admin', 'Finance'])) {
                abort(403, 'Accès non autorisé.');
            }

            $reclamation->delete();

            return redirect()->route('reclamations.index')
                             ->with('success', 'Réclamation supprimée avec succès.');
        } catch (Exception $e) {
            Log::error('Error in ReclamationController@destroy: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la suppression.');
        }
    }

    /**
     * Assign reclamation to a user.
     */
    public function assign(Request $request, Reclamation $reclamation)
    {
        try {
            $request->validate([
                'assigned_to' => 'required|exists:users,id'
            ]);

            $reclamation->update([
                'assigned_to' => $request->assigned_to,
                'status' => 'en_traitement'
            ]);

            return redirect()->back()
                             ->with('success', 'Réclamation assignée avec succès.');
        } catch (Exception $e) {
            Log::error('Error in ReclamationController@assign: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'assignation.');
        }
    }

    /**
     * Respond to a reclamation.
     */
    public function respond(Request $request, Reclamation $reclamation)
    {
        try {
            $user = Auth::user();

            // Authorization: Équipe Technique can only respond to assigned reclamations
            if ($user->hasRole('Équipe Technique') && $reclamation->assigned_to !== $user->id) {
                abort(403, 'Accès non autorisé.');
            }

            $request->validate([
                'response' => 'required|string',
                'status' => 'required|in:' . implode(',', array_keys(Reclamation::STATUSES))
            ]);

            $reclamation->update([
                'response' => $request->response,
                'response_date' => now(),
                'status' => $request->status
            ]);

            return redirect()->back()
                             ->with('success', 'Réponse ajoutée avec succès.');
        } catch (Exception $e) {
            Log::error('Error in ReclamationController@respond: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'ajout de la réponse.');
        }
    }

    /**
     * Update status of reclamation.
     */
    public function updateStatus(Request $request, Reclamation $reclamation)
    {
        try {
            $user = Auth::user();

            // Authorization: Équipe Technique can update status of assigned reclamations
            if ($user->hasRole('Équipe Technique') && $reclamation->assigned_to !== $user->id) {
                abort(403, 'Accès non autorisé.');
            }

            $request->validate([
                'status' => 'required|in:' . implode(',', array_keys(Reclamation::STATUSES))
            ]);

            $reclamation->update([
                'status' => $request->status
            ]);

            return redirect()->back()
                             ->with('success', 'Statut mis à jour avec succès.');
        } catch (Exception $e) {
            Log::error('Error in ReclamationController@updateStatus: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de la mise à jour du statut.');
        }
    }

    /**
     * Rate the resolution for a reclamation.
     */
    public function rate(Request $request, Reclamation $reclamation)
    {
        try {
            $user = Auth::user();

            // Only the creator can rate
            if ($reclamation->user_id !== $user->id) {
                abort(403, 'Accès non autorisé.');
            }

            $request->validate([
                'satisfaction_rating' => 'required|integer|min:1|max:5'
            ]);

            $reclamation->update([
                'satisfaction_rating' => $request->satisfaction_rating
            ]);

            return redirect()->back()
                             ->with('success', 'Évaluation enregistrée avec succès.');
        } catch (Exception $e) {
            Log::error('Error in ReclamationController@rate: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors de l\'évaluation.');
        }
    }

    /**
     * Get reclamations statistics.
     */
    public function statistics()
    {
        try {
            $stats = [
                'total' => Reclamation::count(),
                'ouverte' => Reclamation::where('status', 'ouverte')->count(),
                'en_traitement' => Reclamation::where('status', 'en_traitement')->count(),
                'resolue' => Reclamation::where('status', 'resolue')->count(),
                'fermee' => Reclamation::where('status', 'fermee')->count(),
                'by_category' => Reclamation::select('category', DB::raw('count(*) as total'))
                                             ->groupBy('category')
                                             ->get(),
                'by_priority' => Reclamation::select('priority', DB::raw('count(*) as total'))
                                             ->groupBy('priority')
                                             ->get(),
                'average_rating' => Reclamation::whereNotNull('satisfaction_rating')
                                             ->avg('satisfaction_rating')
            ];

            return response()->json($stats);
        } catch (Exception $e) {
            Log::error('Error in ReclamationController@statistics: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors du chargement des statistiques.'], 500);
        }
    }
}