<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Inscription;
use App\Models\Module;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;

class ModuleController extends Controller
{
    /**
     * Set up middleware for permissions.
     */
    public function __construct()
    {
        
        $this->middleware('permission:module-list', ['only' => ['index']]);
        
        
        $this->middleware('permission:module-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:module-edit', ['only' => ['edit', 'update', 'updateAjax']]);
        $this->middleware('permission:module-delete', ['only' => ['destroy', 'destroyAjax']]);
        $this->middleware('permission:module-view-own', ['only' => ['show']]);
        $this->middleware('permission:module-update-progress', ['only' => ['updateProgress']]);
        
       
        // $this->middleware('permission:module-manage-all', ['only' => ['index', 'show', 'create', 'store', 'edit', 'update', 'destroy']]);
    }

    /**
     * Display a listing of the formations with their module count.
     */
    public function index()
    {
        $user = Auth::user();
        $formations = collect();

        if ($user) {
            if ($user->hasRole('Admin')) {
                // Admin or user with manage-all permission sees all formations
                $formations = Formation::withCount('modules')->get();
            } elseif ($user->hasRole('Consultant')) {
                // Consultant sees only formations with their assigned modules
                $formationIds = Module::where('user_id', $user->id)
                                    ->pluck('formation_id')
                                    ->unique();
                $formations = Formation::whereIn('id', $formationIds)
                                     ->withCount(['modules' => function ($query) use ($user) {
                                         $query->where('user_id', $user->id);
                                     }])
                                     ->get();
            } elseif ($user->hasRole('Etudiant')) {
                // Student sees only formations they are inscribed in
                $formationIds = Inscription::where('user_id', $user->id)
                                            ->pluck('formation_id')
                                            ->unique();
                $formations = Formation::whereIn('id', $formationIds)
                                     ->withCount('modules')
                                     ->get();
            }
        }

        return view('modules.index', compact('formations'));
    }

    /**
     * Display the specified formation and its modules.
     */
   public function show(Formation $formation)
{
    $user = Auth::user();
    
    // Ntafa9na ila kan admin ychouf koulchi, wla ghadi nfiltrio 3la 7sab l'consultant
    if ($user->hasRole('Admin')) {
        // L'Admin kaychouf ga3 les modules
        $formation->load('modules.user');
    } elseif ($user->hasRole('Consultant')) {
        // L'Consultant kaychouf ghir les modules dyalo f had l'formation
        $formation->load(['modules' => function ($query) use ($user) {
            $query->where('user_id', $user->id);
        }, 'modules.user']);
    } elseif ($user->hasRole('Etudiant')) {
        // L'étudiant kaychouf ga3 les modules ila kan dayer inscription
        $is_inscribed = Inscription::where('user_id', $user->id)
                                    ->where('formation_id', $formation->id)
                                    ->exists();
        if ($is_inscribed) {
            $formation->load('modules.user');
        } else {
            return redirect()->route('modules.index')->with('error', 'You are not authorized to view this formation.');
        }
    } else {
        // Ila kan chi user akhor, ma 3andouch l'7aq ychouf.
        return redirect()->route('modules.index')->with('error', 'You do not have permission to view this content.');
    }

    $consultants = User::role('consultant')->get(['id', 'name']);
    return view('modules.show', compact('formation', 'consultants'));
}

    /**
     * Show the form for creating a new module.
     */
    public function create()
    {
        $formations = Formation::all();
        $consultants = User::role('consultant')->get();
        return view('modules.create', compact('formations', 'consultants'));
    }

    /**
     * Store a newly created module(s) in storage.
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'modules' => 'required|array',
            'modules.*.title' => 'required|string|max:255',
            'modules.*.status' => 'required|in:draft,published',
            'modules.*.content' => 'required|string',
            'modules.*.user_id' => 'required|exists:users,id',
            'modules.*.duration_hours' => 'nullable|integer|min:0', // Zidna had l'validation l'jdida
        ]);

        $formationId = $validatedData['formation_id'];
        $lastModule = Module::where('formation_id', $formationId)
            ->orderBy('order', 'desc')
            ->first();

        $startOrder = $lastModule ? $lastModule->order + 1 : 1;

        foreach ($validatedData['modules'] as $index => $moduleData) {
            $contentArray = explode("\n", $moduleData['content']);

            Module::create([
                'formation_id' => $formationId,
                'title' => $moduleData['title'],
                'status' => $moduleData['status'],
                'content' => $contentArray,
                'user_id' => $moduleData['user_id'],
                'order' => $startOrder + $index,
                'progress' => 0,
                'duration_hours' => $moduleData['duration_hours'] ?? 0, // Zidna had l'parti bach nsavew la valeur
            ]);
        }

        return redirect()->route('modules.index')->with('success', 'Modules added successfully!');
    }

    /**
     * Show the form for editing the specified module.
     */
    public function edit(Module $module)
    {
        $formations = Formation::all();
        $consultants = User::role('consultant')->get();
        return view('modules.edit', compact('module', 'formations', 'consultants'));
    }

    /**
     * Update the specified module in storage.
     */
   // In app/Http/Controllers/ModuleController.php

public function update(Request $request, Module $module)
{
    // Validate the incoming data from the AJAX request
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'duration_hours' => 'nullable|integer|min:0', // Zidna had l'validation l'jdida
        'order' => 'required|integer|min:1',
        'status' => 'required|in:draft,published',
        'content' => 'required|string',
        'user_id' => 'required|exists:users,id',
    ]);
    
    // Convert the string content to an array of lines
    $contentArray = explode("\n", $validatedData['content']);

    // Get the old order before updating
    $oldOrder = $module->order;
    $newOrder = $validatedData['order'];

    // Swap the orders if the order has been changed
    if ($oldOrder != $newOrder) {
        $otherModule = Module::where('formation_id', $module->formation_id)
                             ->where('order', $newOrder)
                             ->first();
        
        if ($otherModule) {
            $otherModule->order = $oldOrder;
            $otherModule->save();
        }
    }
    
    // Update the current module
    $module->update(array_merge($validatedData, ['content' => $contentArray]));
    
    // Return all modules for the formation to re-render the list
    $formation = Formation::find($module->formation_id);
    $formation->load('modules.user');
    
    // Return a JSON response with the success message and the updated list of modules
    return response()->json([
        'success' => 'Module updated successfully!', 
        'modules' => $formation->modules->sortBy('order')->values()
    ]);
}
    /**
     * Remove the specified module from storage.
     */
    public function destroy(Module $module)
    {
        $module->delete();
        return redirect()->route('modules.index')->with('success', 'Module deleted successfully!');
    }

    /**
     * Handle the progress update.
     */
   public function updateProgress(Request $request, Module $module)
{
    // Awwal 7aja kanchekiw wach l'user li dakhél howa l'consultant dyal had l'module
    if (Auth::id() !== $module->user_id) {
        // Kanreje3 l'user l'page li kan fiha m3a un message d'erreur
        return redirect()->back()->with('error', 'You are not authorized to update progress for this module.');
    }

    $request->validate([
        'progress' => 'required|integer|min:0|max:100',
    ]);

    $module->update(['progress' => $request->progress]);

    return redirect()->back()->with('success', 'Module progress updated successfully!');
}
    /**
     * Get module data and consultants for AJAX requests.
     */
    public function getModuleData(Module $module)
    {
        $consultants = User::role('Consultant')->get(['id', 'name']);
        return response()->json([
            'module' => $module,
            'consultants' => $consultants
        ]);
    }

    /**
     * Remove the specified module from storage using AJAX.
     */
    public function destroyAjax(Module $module)
    {
        $module->delete();
        return response()->json(['success' => 'Module deleted successfully!']);
    }
}