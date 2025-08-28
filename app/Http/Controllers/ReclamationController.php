<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Reclamation;
use App\Models\Formation;
                                                                           use App\Models\User; // Ensure User model is imported
use Illuminate\Http\Request;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
 use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail; // Zid had la ligne
use App\Mail\NewReclamationNotification; // Zid had la ligne
use Spatie\Permission\Models\Role; 
class ReclamationController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        // Apply middleware for authorization
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function index(Request $request)
    {
        try {
            $user = Auth::user();
            $query = Reclamation::with(['user', 'formation', 'assignedTo']);

            // Filter reclamations based on user role
            if ($user->hasRole('Etudiant') || $user->hasRole('Consultant')) {
                $query->where('user_id', $user->id);
            }

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

            $reclamations = $query->orderBy('created_at', 'desc')->paginate(10);
            $formations = Formation::select('id', 'title')->get();

            return view('reclamations.index', compact('reclamations', 'formations'));
        } catch (Exception $e) {
            Log::error('Error in ReclamationController@index: ' . $e->getMessage());
            return back()->with('error', 'Une erreur est survenue lors du chargement des réclamations.');
        }
    }

    /**
     * Show the form for creating a new reclamation.
     *
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function create()
{
    try {
        $user = Auth::user(); // Get the currently authenticated user
        $formations = collect(); // Initialize an empty collection

        if ($user) {
            // Check if the user is a Student or a Consultant
            if ($user->hasRole('Etudiant')) {
                // Get formations a student is enrolled in
                $formationIds = Inscription::where('user_id', $user->id)->pluck('formation_id');
                $formations = Formation::whereIn('id', $formationIds)->where('status', 'published')->get();
            } elseif ($user->hasRole('Consultant')) {
                // Get formations assigned to a consultant
                // Assuming 'consultant_id' is the foreign key in the 'formations' table
                $formations = Formation::where('consultant_id', $user->id)->where('status', 'published')->get();
            } else {
                // For other roles (e.g., Admin), show all published formations
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
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

            // === L'CORRECTION ===
            // 1. Create the reclamation first.
            $reclamation = Reclamation::create([
                'user_id' => Auth::id(),
                'formation_id' => $request->formation_id,
                'category' => $request->category,
                'subject' => $request->subject,
                'description' => $request->description,
                'priority' => $request->priority,
                'status' => 'ouverte'
            ]);

            // 2. Get users with the specified roles.
            $rolesToSendEmail = ['Admin', 'Finance', 'Super Admin'];
            $usersToNotify = User::role($rolesToSendEmail)->get();

            // 3. Send the notification email to each user.
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
     *
     * @param  \App\Models\Reclamation  $reclamation
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
// In App\Http\Controllers\ReclamationController.php

public function show(Reclamation $reclamation)
{
    try {
        $user = Auth::user();

        // Authorization check
        if (($user->hasRole('Etudiant') || $user->hasRole('Consultant')) && $reclamation->user_id !== $user->id) {
            abort(403, 'Accès non autorisé.');
        }

        $reclamation->load(['user', 'formation', 'assignedTo']);

        // Fetch users who can be assigned to the reclamation
        // This leverages the Spatie\Permission package's powerful scopes.
        $assignableUsers = User::role(['Admin', 'Finance', 'Super Admin'])->get();
        
        return view('reclamations.show', compact('reclamation', 'assignableUsers'));
    } catch (Exception $e) {
        Log::error('Error in ReclamationController@show: ' . $e->getMessage());
        return back()->with('error', 'Une erreur est survenue lors du chargement de la réclamation.');
    }
}

    /**
     * Show the form for editing the specified reclamation.
     *
     * @param  \App\Models\Reclamation  $reclamation
     * @return \Illuminate\View\View|\Illuminate\Http\RedirectResponse
     */
    public function edit(Reclamation $reclamation)
    {
        try {
            $user = Auth::user();

            // Authorization check: only creator can edit if status is 'ouverte', or an admin.
            // Also allow Consultants to edit their assigned reclamations
            if (($user->hasRole('Etudiant') && ($reclamation->user_id !== $user->id || $reclamation->status !== 'ouverte')) ||
                ($user->hasRole('Consultant') && ($reclamation->assigned_to !== $user->id && $reclamation->user_id !== $user->id))) {
                abort(403, 'Accès non autorisé.');
            }
            if (!$user->hasAnyRole(['Admin', 'Super Admin', 'Finance']) && $reclamation->status !== 'ouverte' && $reclamation->user_id !== $user->id && $reclamation->assigned_to !== $user->id) {
                abort(403, 'Accès non autorisé.');
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reclamation  $reclamation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Reclamation $reclamation)
    {
        try {
            $user = Auth::user();

            // Authorization check
            if (($user->hasRole('Etudiant') && ($reclamation->user_id !== $user->id || $reclamation->status !== 'ouverte')) ||
                ($user->hasRole('Consultant') && ($reclamation->assigned_to !== $user->id && $reclamation->user_id !== $user->id))) {
                abort(403, 'Accès non autorisé.');
            }
            if (!$user->hasAnyRole(['Admin', 'Super Admin', 'Finance']) && $reclamation->status !== 'ouverte' && $reclamation->user_id !== $user->id && $reclamation->assigned_to !== $user->id) {
                abort(403, 'Accès non autorisé.');
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
     *
     * @param  \App\Models\Reclamation  $reclamation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Reclamation $reclamation)
    {
        try {
            $user = Auth::user();

            // Authorization check: only admins can delete
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reclamation  $reclamation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function assign(Request $request, Reclamation $reclamation)
    {
        try {
            $request->validate([
                'assigned_to' => 'required|exists:users,id'
            ]);

            $reclamation->update([
                'assigned_to' => $request->assigned_to,
                'status' => 'en_traitement' // Status changes when assigned
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reclamation  $reclamation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function respond(Request $request, Reclamation $reclamation)
    {
        try {
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
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reclamation  $reclamation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus(Request $request, Reclamation $reclamation)
    {
        try {
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
     * Rate the resolution (satisfaction rating) for a reclamation.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Reclamation  $reclamation
     * @return \Illuminate\Http\RedirectResponse
     */
    public function rate(Request $request, Reclamation $reclamation)
    {
        try {
            $user = Auth::user();

            // Authorization check: only the creator can rate
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
     *
     * @return \Illuminate\Http\JsonResponse
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