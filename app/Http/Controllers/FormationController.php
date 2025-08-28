<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Category;
use App\Models\User;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Symfony\Component\HttpFoundation\StreamedResponse;
class FormationController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth'); // Ensure user is authenticated for all formation actions

        // View/List permissions
        $this->middleware('permission:formation-list')->only(['index']);
        $this->middleware('permission:formation-view')->only(['show']);
        $this->middleware('permission:formation-view-calendar')->only(['calendar']);
        $this->middleware('permission:formation-get-active-inscriptions')->only(['getActiveInscriptionsCount']);
        $this->middleware('permission:formation-get-by-category')->only(['getByCategory']); // Often more permissive for public display

        // Management permissions
        $this->middleware('permission:formation-create')->only(['create', 'store']);
        $this->middleware('permission:formation-edit')->only(['editModalContent', 'edit', 'update']);
        $this->middleware('permission:formation-delete')->only(['destroy']);
        $this->middleware('permission:formation-duplicate')->only(['duplicate']);
        $this->middleware('permission:formation-toggle-status')->only(['toggleStatus']);
        $this->middleware('permission:formation-view-statistics')->only(['statistics']);
        $this->middleware('permission:formation-export')->only(['export']);

        // Middleware to enforce "manage own courses" for consultants if applicable
        // This is a more complex authorization logic that might be better in a Policy
        // but can be partially handled here for initial setup.
        // For 'edit', 'update', 'destroy', 'duplicate', 'toggleStatus', 'statistics'
        // consultants should only act on formations they own.
        $this->middleware(function ($request, $next) {
            $user = Auth::user();
            if ($user && $user->hasRole('Consultant')) {
                // For methods that operate on a specific formation instance
                if (in_array($request->route()->getName(), [
                    'formations.show',
                    'formations.editModalContent',
                    'formations.edit',
                    'formations.update',
                    'formations.destroy',
                    'formations.duplicate',
                    'formations.toggleStatus',
                    'formations.statistics',
                    'formations.getActiveInscriptionsCount'
                ])) {
                    $formation = $request->route('formation'); // Get the formation model from route
                    if ($formation && $formation->consultant_id !== $user->id && !$user->can('formation-manage-all')) {
                        abort(403, 'Unauthorized action. You can only manage your own formations.');
                    }
                }
            }
            return $next($request);
        })->except(['index', 'create', 'store', 'getByCategory', 'calendar']); // Methods where a consultant might interact with all formations (viewing list) or creating new ones.
    }
    /**
     * Display a listing of formations.
     * Gère les filtres et la recherche.
     */
    public function index(Request $request)
    {
        $query = Formation::with(['category', 'consultant', 'inscriptions']);

        // Filtrer par catégorie
        if ($request->has('category_id') && $request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        // Filtrer par statut
        if ($request->has('status') && $request->status) {
            $query->where('status', $request->status);
        }

        // Pour les consultants, afficher uniquement leurs formations
        if (Auth::check() && Auth::user()->role === 'consultant') {
            $query->where('consultant_id', Auth::id());
        }

        // Rechercher par titre ou description
        if ($request->has('search') && $request->search) {
            $query->where(function($q) use ($request) {
                $q->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        // Tri par défaut (les plus récents en premier)
        $formations = $query->orderBy('created_at', 'desc')->paginate(12);

        $categories = Category::where('is_active', true)->get();
        $consultants = User::role('consultant')->where('status', 'active')->get();
        
        // --- ADD THIS LINE ---
        $durationUnits = ['heures', 'jours', 'mois']; // Make sure these match your ENUM values from the migration
        // ---------------------

        // Pass $durationUnits along with other variables to the view
        return view('formations.index', compact('formations', 'categories', 'consultants', 'durationUnits')); // <--- MODIFIED THIS LINE
    }

    /**
     * Show the form for creating a new formation.
     */
     public function create()
    {
        $categories = Category::where('is_active', true)->get();
        $consultants = User::role('consultant')->where('status', 'active')->get();
        $durationUnits = ['heures', 'jours', 'mois'];
        return view('formations.create', compact('categories', 'consultants', 'durationUnits'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'duration_unit' => ['required', Rule::in(['heures', 'jours', 'mois'])], 
            
            'capacity' => 'required|integer|min:1',
            'start_date' => 'required|date|after_or_equal:today',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'consultant_id' => 'required|exists:users,id',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'nullable|string|max:255',
            
            'documents_files' => 'nullable|array',
            'documents_files.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048',
            
            'status' => ['required', Rule::in(['draft', 'published', 'completed'])],
            'available_payment_options' => 'required|array|min:1',
            'available_payment_options.*' => 'integer|min:1|max:12',
        ]);

        if (Auth::check() && Auth::user()->hasRole('Consultant')) {
            $validated['consultant_id'] = Auth::id();
        }
        
        if (isset($validated['prerequisites'])) {
            $validated['prerequisites'] = array_filter($validated['prerequisites'], fn($value) => !is_null($value) && $value !== '');
        } else {
            $validated['prerequisites'] = [];
        }

        $documentsData = [];
        if ($request->hasFile('documents_files')) {
            foreach ($request->file('documents_files') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('formation_documents', 'public'); 
                    $documentsData[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path
                    ];
                }
            }
        }
        $validated['documents_required'] = $documentsData; 

        $formation = Formation::create($validated);

        return redirect()->route('formations.index')
                            ->with('success', 'Formation  ');
    }

    /**
     * Display the specified formation.
     */
    public function show(Formation $formation)
    {
        $formation->load(['category', 'consultant', 'inscriptions.user', 'courses', 'evaluations']);
        $averageRating = $formation->evaluations()->avg('rating');
        $availableSpots = $formation->capacity - $formation->inscriptions()->whereIn('status', ['active', 'pending'])->count();
        $isEnrolled = false;
        $userInscription = null;
        if (Auth::check()) {
            $userInscription = $formation->inscriptions()->where('user_id', Auth::id())->first();
            $isEnrolled = $userInscription !== null;
        }

        return view('formations.show', compact(
            'formation', 
            'averageRating', 
            'availableSpots', 
            'isEnrolled', 
            'userInscription'
        ));
    }

    /**
     * ⭐ NEW METHOD: Show the form for editing the specified formation for a modal.
     */
     public function editModalContent(Formation $formation)
    {
        $categories = Category::where('is_active', true)->get();
        $consultants = User::role('consultant')->where('status', 'active')->get();
        $durationUnits = ['heures', 'jours', 'mois'];
        return view('formations.edit-modal-content', compact('formation', 'categories', 'consultants', 'durationUnits'));
    }

    public function edit(Formation $formation)
    {
        $categories = Category::where('is_active', true)->get();
        $consultants = User::role('consultant')->where('status', 'active')->get();
        $durationUnits = ['heures', 'jours', 'mois'];
        return view('formations.edit', compact('formation', 'categories', 'consultants', 'durationUnits'));
    }

    public function update(Request $request, Formation $formation)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'duration_hours' => 'required|integer|min:1',
            'duration_unit' => ['required', Rule::in(['heures', 'jours', 'mois'])], 
            
            'capacity' => 'required|integer|min:1',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'category_id' => 'required|exists:categories,id',
            'consultant_id' => 'required|exists:users,id',
            'prerequisites' => 'nullable|array',
            'prerequisites.*' => 'nullable|string|max:255',
            
            // Validation for newly uploaded files
            'documents_files' => 'nullable|array',
            'documents_files.*' => 'nullable|file|mimes:pdf,doc,docx,jpg,jpeg,png|max:2048', // Max 2MB per file
            
            // Validation for existing document data (name and path)
            // These are the hidden fields that carry over existing document info
            'existing_documents_names' => 'nullable|array',
            'existing_documents_names.*' => 'nullable|string|max:255',
            'existing_documents_paths' => 'nullable|array',
            'existing_documents_paths.*' => 'nullable|string|max:255',

            'status' => ['required', Rule::in(['draft', 'published', 'completed'])],
            'available_payment_options' => 'required|array|min:1',
            'available_payment_options.*' => 'integer|min:1|max:12',
        ]);

        if (Auth::check() && Auth::user()->hasRole('Consultant')) {
            unset($validated['consultant_id']); // Consultant cannot change assignment
        }

        if (isset($validated['prerequisites'])) {
            $validated['prerequisites'] = array_filter($validated['prerequisites'], fn($value) => !is_null($value) && $value !== '');
        } else {
            $validated['prerequisites'] = [];
        }

        // Handle documents for update: combine existing and new uploads
        $updatedDocuments = [];

        // 1. Process existing documents (if their names/paths were submitted)
        // These come from the hidden inputs for documents that were already there
        if ($request->has('existing_documents_names') && is_array($request->input('existing_documents_names'))) {
            foreach ($request->input('existing_documents_names') as $index => $name) {
                $path = $request->input('existing_documents_paths')[$index] ?? null;
                // Only include if both name and path are present and not empty
                if (!is_null($name) && $name !== '' && !is_null($path) && $path !== '') {
                    $updatedDocuments[] = ['name' => $name, 'path' => $path];
                }
            }
        }

        // 2. Process newly uploaded files
        if ($request->hasFile('documents_files')) {
            foreach ($request->file('documents_files') as $file) {
                if ($file && $file->isValid()) {
                    $path = $file->store('formation_documents', 'public');
                    $updatedDocuments[] = [
                        'name' => $file->getClientOriginalName(),
                        'path' => $path
                    ];
                }
            }
        }
        
        $validated['documents_required'] = $updatedDocuments; // Assign the new structured array

        $formation->update($validated);

        return redirect()->route('formations.index')
                            ->with('success', 'Formation ');
    }

    /**
     * Remove the specified formation from storage.
     */
    public function destroy(Formation $formation)
    {
        $activeInscriptions = $formation->inscriptions()->whereIn('status', ['active', 'pending'])->count();

        if ($activeInscriptions > 0) {
            return redirect()->back()
                             ->with('error', 'Impossible de supprimer cette formation car elle a des inscriptions actives.');
        }

        $formation->delete();

        return redirect()->route('formations.index')
                         ->with('success', 'Formation supprimée avec succès!');
    }

    /**
     * Get the count of active inscriptions for a formation.
     */
    public function getActiveInscriptionsCount(Formation $formation)
    {
        $activeInscriptions = $formation->inscriptions()->whereIn('status', ['active', 'pending'])->count();
        return response()->json(['active_inscriptions' => $activeInscriptions]);
    }

    /**
     * Get formations by category (AJAX).
     */
    public function getByCategory($categoryId)
    {
        $formations = Formation::where('category_id', $categoryId)
                               ->where('status', 'published')
                               ->select('id', 'title', 'price', 'start_date')
                               ->get();

        return response()->json($formations);
    }

    /**
     * Duplicate a formation.
     */
    public function duplicate(Formation $formation)
    {
        $newFormation = $formation->replicate();
        $newFormation->title = $formation->title . ' (Copie)';
        $newFormation->status = 'draft';
        $newFormation->start_date = now()->addWeek();
        $newFormation->end_date = now()->addWeeks(2);
        $newFormation->available_payment_options = [1];
        // --- ADDED duration_unit to duplication ---
        $newFormation->duration_unit = $formation->duration_unit; 

        if (Auth::check() && Auth::user()->role === 'consultant') {
            $newFormation->consultant_id = Auth::id();
        }

        $newFormation->save();

        return redirect()->route('formations.index', $newFormation)
                         ->with('success', 'Formation dupliquée avec succès!');
    }

    /**
     * Get formation statistics (for admins/consultants).
     */
    public function statistics(Formation $formation)
    {
        $stats = [
            'total_inscriptions' => $formation->inscriptions()->count(),
            'active_inscriptions' => $formation->inscriptions()->where('status', 'active')->count(),
            'completed_inscriptions' => $formation->inscriptions()->where('status', 'completed')->count(),
            'total_revenue' => $formation->inscriptions()->sum('paid_amount'),
            'average_rating' => round($formation->evaluations()->avg('rating'), 2),
            'completion_rate' => 0,
        ];

        if ($stats['total_inscriptions'] > 0) {
            $stats['completion_rate'] = round(
                ($stats['completed_inscriptions'] / $stats['total_inscriptions']) * 100, 
                2
            );
        }

        return response()->json($stats);
    }

    /**
     * Publish/Unpublish formation.
     */
    public function toggleStatus(Formation $formation)
    {
        $newStatus = $formation->status === 'published' ? 'draft' : 'published';
        $formation->update(['status' => $newStatus]);

        $message = $newStatus === 'published' ? 'Formation publiée!' : 'Formation mise en brouillon!';

        return redirect()->back()->with('success', $message);
    }

    /**
     * Get formations for calendar view.
     */
    public function calendar()
    {
        $formations = Formation::where('status', 'published')
                               ->select('id', 'title', 'start_date', 'end_date', 'category_id')
                               ->with('category:id,name')
                               ->get()
                               ->map(function($formation) {
                                   return [
                                       'id' => $formation->id,
                                       'title' => $formation->title,
                                       'start' => $formation->start_date,
                                       'end' => $formation->end_date,
                                       'color' => $this->getCategoryColor($formation->category_id),
                                       'url' => route('formations.show', $formation->id)
                                   ];
                               });

        return response()->json($formations);
    }

    /**
     * Helper method to get a consistent color for categories.
     */
    private function getCategoryColor($categoryId)
    {
        $colors = [
            '#3498db', '#e74c3c', '#2ecc71', '#f39c12', 
            '#9b59b6', '#1abc9c', '#34495e', '#e67e22',
            '#f1c40f', '#95a5a6', '#d35400', '#c0392b'
        ];
        
        return $colors[$categoryId % count($colors)];
    }

    /**
     * Export formations to CSV.
     */
    public function exportCsv()
    {
        // Ensure the user has the 'formation-export' permission
        if (!Auth::user()->can('formation-export')) {
            abort(403, 'Unauthorized action. You do not have permission to export formations.');
        }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="formations_' . date('Ymd_His') . '.csv"',
        ];

        $callback = function() {
            $formations = Formation::with(['category', 'consultant'])->get();
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID',
                'Titre',
                'Description',
                'Prix',
                'Durée',
                'Unité de Durée',
                'Capacité',
                'Date de Début',
                'Date de Fin',
                'Catégorie',
                'Consultant',
                'Prérequis',
                'Documents Requis',
                'Statut',
                'Options de Paiement'
            ]);

            foreach ($formations as $formation) {
                fputcsv($file, [
                    $formation->id,
                    $formation->title,
                    strip_tags($formation->description), // Remove HTML tags from description
                    $formation->price,
                    $formation->duration_hours,
                    $formation->duration_unit,
                    $formation->capacity,
                    $formation->start_date ? $formation->start_date->format('Y-m-d') : 'N/A',
                    $formation->end_date ? $formation->end_date->format('Y-m-d') : 'N/A',
                    $formation->category ? $formation->category->name : 'N/A',
                    $formation->consultant ? $formation->consultant->name : 'N/A',
                    json_encode($formation->prerequisites), // Prerequisites are stored as JSON
                    json_encode($formation->documents_required), // Documents are stored as JSON
                    $formation->status,
                    json_encode($formation->available_payment_options) // Payment options are stored as JSON
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}