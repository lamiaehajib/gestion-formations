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
     * Display a listing of the formations with their module count.
     */
public function index()
{
    $user = Auth::user();
    $formations = collect();

    if ($user) {
        if ($user->hasRole('Admin')) {
            // Ila kan Admin, kan affichiw lih ga3 les formations
            $formations = Formation::withCount('modules')->get();

        } elseif ($user->hasRole('Consultant')) {
            // Ila kan Consultant, kan affichiw ghir les formations li m'assigné lih fihom modules
            $formationIds = Module::where('user_id', $user->id)
                                ->pluck('formation_id')
                                ->unique();

            $formations = Formation::whereIn('id', $formationIds)
                                 ->withCount(['modules' => function ($query) use ($user) {
                                     $query->where('user_id', $user->id);
                                 }])
                                 ->get();

        } elseif ($user->hasRole('Etudiant')) {
            // Hna zedna l'logic dyal l'Etudiant
            // Najbdo les IDs dyal les formations li dayer fihom l'inscription
            $formationIds = Inscription::where('user_id', $user->id)
                                        ->pluck('formation_id')
                                        ->unique();
            
            // Najbdo les formations b l'IDs dyalhom
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
        }, 'modules.user']); // Kan7amlou l'user li m'assigné l'module
    }

    // Najbdo ga3 les consultants bach n3amrou bihom l'modal dyal l'ajout w l'modification
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
public function update(Request $request, Module $module)
{
    // Validate the incoming data from the AJAX request
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
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
        'modules' => $formation->modules->sortBy('order')->values() // Use values() to reset the array keys for JavaScript
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
        if (Auth::id() !== $module->user_id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $module->update(['progress' => $request->progress]);

        return redirect()->back()->with('success', 'Module progress updated successfully!');
    }


    public function updateAjax(Request $request, Module $module)
    {
        $validatedData = $request->validate([
            'title' => 'sometimes|required|string|max:255',
            'order' => 'sometimes|required|integer',
            'status' => 'sometimes|required|in:draft,published',
            'content' => 'sometimes|required|string',
            'user_id' => 'sometimes|required|exists:users,id',
        ]);
        
        // Ila kan l'content f validated data, ghadi n7awloh l'array
        if (isset($validatedData['content'])) {
            $validatedData['content'] = explode("\n", $validatedData['content']);
        }

        $module->update($validatedData);

        // Nreje3 des informations JSON bhal success message
        return response()->json(['success' => 'Module updated successfully!', 'module' => $module]);
    }

    /**
     * Remove the specified module from storage using AJAX.
     * Hadchi li tzad
     */
    public function destroyAjax(Module $module)
    {
        $module->delete();

        // Nreje3 des informations JSON bhal success message
        return response()->json(['success' => 'Module deleted successfully!']);
    }

    public function getModuleData(Module $module)
{
    $consultants = User::role('consultant')->get(['id', 'name']); // Katjib ghir l'id w l'name
    
    return response()->json([
        'module' => $module,
        'consultants' => $consultants
    ]);
}
}