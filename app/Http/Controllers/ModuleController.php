<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Inscription;
use App\Models\Module;
use App\Models\Course; // Zidna had l'import
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
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
    $uniqueModules = collect(); // ← Hna ghadi n7tafdo b les modules uniques

    if ($user) {
        if ($user->hasRole('Admin')) {
            // Admin sees all formations
            $formations = Formation::withCount('modules')->get();
        } 
        elseif ($user->hasRole('Consultant')) {
            // ✅ Solution: N-afficher ghir les modules uniques dyal l-Consultant
            
            // 1. Njibbou ga3 les modules dyal had l-Consultant (UNIQUE)
            $uniqueModules = Module::where('user_id', $user->id)
                ->with('formations') // N-load les formations dyal kola module
                ->get()
                ->unique('id'); // Assurer li kayna ghir mara wa7da
            
            // Makaynach formations f had l-cas, ghir modules
            $formations = collect(); // Vide
        } 
        elseif ($user->hasRole('Etudiant')) {
            // Student sees formations they are inscribed in
            $formationIds = Inscription::where('user_id', $user->id)
                ->pluck('formation_id')
                ->unique();
            $formations = Formation::whereIn('id', $formationIds)
                ->withCount('modules')
                ->get();
        }
    }

    return view('modules.index', compact('formations', 'uniqueModules'));
}

    /**
     * Display the specified formation and its modules.
     */
   public function show(Formation $formation)
{
    $user = Auth::user();
    
    // ✅ 1. Ncharge les consultants f l'awal bach ikon mojoud f l-view kaml.
    $consultants = User::role('consultant')->get(['id', 'name']);

    if ($user->hasRole('Admin')) {
        // L'Admin kaychouf ga3 les modules, w l'modules()->orderBy('pivot_order') f l'Model Formation ghadi tssortihom
        $formation->load('modules.user'); 
    } elseif ($user->hasRole('Consultant')) {
        // L'Consultant kaychouf ghir les modules dyalo f had l'formation
        $formation->load(['modules' => function ($query) use ($user) {
            // Had l'query khas tfiltéri f tableau 'modules'
            $query->where('user_id', $user->id);
        }, 'modules.user']);
    } elseif ($user->hasRole('Etudiant')) {
        // ... L'étudiant, khas ykoun inscrit 
        $is_inscribed = Inscription::where('user_id', $user->id)
                                   ->where('formation_id', $formation->id)
                                   ->exists();
        if ($is_inscribed) {
            // Ila kan inscrit, kaychouf ga3 les modules
            $formation->load('modules.user');
        } else {
            // Machi inscrit, kanrddouh l'index m3a un message d'erreur
            return redirect()->route('modules.index')->with('error', 'You are not authorized to view this formation.');
        }
    } else {
        // Rôles khrin ma 3ndhomch l7a9
        return redirect()->route('modules.index')->with('error', 'You do not have permission to view this content.');
    }

    // Modules msourtin bdik l'order li f l'pivot table.
    // ✅ 2. Kanpassiw $consultants l'view
    return view('modules.show', compact('formation', 'consultants')); 
}
// F-App\Http\Controllers\ModuleController.php
public function details(Module $module)
{
    $user = Auth::user();
    
    if ($user->hasRole('Consultant') && $module->user_id !== $user->id) {
        return redirect()->route('modules.index')
            ->with('error', 'Vous n\'êtes pas autorisé à voir ce module.');
    }
    
    // 1. Load relationships
    $module->load([
        'formations', 
        'user', 
        'courses' => function($query) {
            $query->orderBy('course_date', 'asc')->orderBy('start_time', 'asc'); 
        }
    ]);
    
    // 2. 🔥 Calculate w update progress
    $progress = $this->calculateAndUpdateProgress($module);
    
    // 3. 🔥 CRITICAL: Reload l'module mn DB bach najbdou l'progress l'jdid
    $module->refresh();

    // 4. Get unique courses
    $uniqueCourses = $module->courses->unique(function($course) {
        return $course->module_id . '-' . 
               $course->course_date . '-' . 
               $course->start_time . '-' . 
               $course->title;
    });

    $totalCourses = $uniqueCourses->count();
    
    // 5. Count completed courses (end_time fat)
    $completedCourses = $uniqueCourses->filter(function ($course) {
        $courseDate = $course->course_date->format('Y-m-d');
        $endTime = $course->end_time;
        
        // Add seconds if missing
        if (strlen($endTime) == 5) {
            $endTime .= ':00';
        }
        
        $courseEndDateTime = Carbon::parse($courseDate . ' ' . $endTime);
        
        return $courseEndDateTime->isPast();
    })->count();

    // 6. Count upcoming courses
    $upcomingCourses = $uniqueCourses->filter(function ($course) {
        return $course->course_date->isFuture();
    })->count();

    $coursesList = $uniqueCourses; 
    
    // 7. 🐛 DEBUG (TEMPORARY - DELETE after testing)
    \Log::info('Module Progress Debug', [
        'module_id' => $module->id,
        'total_seances' => $module->number_seance,
        'completed_courses' => $completedCourses,
        'progress_calculated' => $progress,
        'progress_in_db' => $module->progress,
    ]);
    
    return view('modules.details', compact(
        'module', 
        'totalCourses', 
        'completedCourses', 
        'upcomingCourses',
        'coursesList'
    ));
}

// 🔥 NEW METHOD: Calculate AND Update Progress
private function calculateAndUpdateProgress(Module $module)
{
    // Check if module has valid number_seance
    if (!$module->number_seance || $module->number_seance <= 0) {
        $module->update(['progress' => 0]);
        return 0;
    }

    // Get unique courses
    $uniqueCourses = $module->courses->unique(function($course) {
        return $course->module_id . '-' . 
               $course->course_date . '-' . 
               $course->start_time . '-' . 
               $course->title;
    });

    // Count completed courses (where end_time has passed)
    $completedCount = $uniqueCourses->filter(function ($course) {
        $courseDate = $course->course_date->format('Y-m-d');
        $endTime = $course->end_time;
        
        // Add seconds if missing
        if (strlen($endTime) == 5) {
            $endTime .= ':00';
        }
        
        $courseEndDateTime = Carbon::parse($courseDate . ' ' . $endTime);
        
        return $courseEndDateTime->isPast();
    })->count();

    // Calculate progress percentage
    $progress = min(100, round(($completedCount / $module->number_seance) * 100, 2));

    // Update in database
    $module->update(['progress' => $progress]);
    
    return $progress;
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
        // 1. Validation: Kanntwa993ou les champs dial Module wa7ed w Array dial formation_ids
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:draft,published',
            'content' => 'required|string', // Ghadi n7awloh l'array
            'user_id' => 'required|exists:users,id',
            'duration_hours' => 'nullable|integer|min:0', 
            'number_seance' => 'nullable|integer|min:1', 
           
            
            // L-7a9l l-mohim: Array dial les IDs dial les Formations lli tkhayyar
            'formation_ids' => 'required|array|min:1', // Khass au moins formation wa7da
            'formation_ids.*' => 'required|exists:formations,id', // Kulla ID khassha tkun mojoda
        ]);

        // 2. N7awlou l-Content (lli ja sseff sseff) l'Array bach n7afdouh f عمود JSON 'content'
        $contentArray = explode("\n", $validatedData['content']);

        // 3. N-Createw l-Module k-entity mosta9ill
        $module = Module::create([
            'title' => $validatedData['title'],
            'status' => $validatedData['status'],
            'content' => $contentArray,
            'user_id' => $validatedData['user_id'],
            'progress' => 0, 
            'duration_hours' => $validatedData['duration_hours'] ?? null,
            'number_seance' => $validatedData['number_seance'] ?? null,
            // 'description' is not in $validatedData, so we omit it for now
        ]);

        // 4. N7adddou l-Order (Tartib) lli ghadi ikon f kolla Formation
        $pivotData = [];
        
        // Kan3awdou ndirou loop 3la kolla ID dial Formation lli tkhayyar
        foreach ($validatedData['formation_ids'] as $formationId) {
            
            // Nl9aw akhir Order (tartib) f tableau intermédiare (formation_module)
            // l hadik l-Formation bo7dha.
            $lastOrder = DB::table('formation_module')
                ->where('formation_id', $formationId)
                ->max('order'); 
                
            // L-Order jdid ghadi ikon (akhir Order + 1), wla 1 ila ma kan hatta module
            $nextOrder = ($lastOrder ?? 0) + 1; 

            // Kanwjeddou l-Array dial l'Pivot Data: formation_id => ['order' => nextOrder]
            $pivotData[$formationId] = ['order' => $nextOrder];
        }

        // 5. Nlinkiw l-Module m3a kolla Formation mkhyyra b l'Order l-khas biha
        // Kansta3mlou attach() 3la Model dial Module
        $module->formations()->attach($pivotData);

        // 6. Redirect w message success
        return redirect()->route('modules.index')->with('success', 'Module ' . $module->title . ' created and linked to ' . count($validatedData['formation_ids']) . ' formation(s) successfully!');
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

// In app/Http/Controllers/ModuleController.php

public function update(Request $request, Module $module)
{
    // ... l'validation kima hiya (bla 'order' ila knti ghadi tbdelha b'pivot)
    $validatedData = $request->validate([
        'title' => 'required|string|max:255',
        'duration_hours' => 'nullable|integer|min:0',
        'number_seance' => 'nullable|integer|min:1',
        // ❌ Ghadi t7ayed 'order' men l'validation, wla tzid 'formation_id' bach ta3raf achmen pivot tbdel.
        'new_order' => 'required|integer|min:1', // 🆕 Khasna n3arfo achmen order jdida
        'formation_id' => 'required|exists:formations,id', // 🆕 Khasna n3arfo achmen formation
        'status' => 'required|in:draft,published',
        'content' => 'required|string',
        'user_id' => 'required|exists:users,id',
    ]);
    
    $formationId = $validatedData['formation_id'];
    $newOrder = $validatedData['new_order'];

    // 1. ✅ T-update Module data
    $contentArray = explode("\n", $validatedData['content']);
    $module->update(array_merge($validatedData, [
        'content' => $contentArray,
        'number_seance' => $validatedData['number_seance'] ?? null,
    ]));

    // 2. ✅ T-update l'Order f l'Pivot Table
    $pivotData = \DB::table('formation_module')
        ->where('formation_id', $formationId)
        ->where('module_id', $module->id)
        ->first();

    if ($pivotData) {
        $oldOrder = $pivotData->order;

        if ($oldOrder != $newOrder) {
            // Swap l'Order: N9albo 3la l'Module li 3andou had l'order wnbdloha lih
            \DB::table('formation_module')
                ->where('formation_id', $formationId)
                ->where('order', $newOrder)
                ->update(['order' => $oldOrder]);

            // Nbdlo l'order dyal l'Module li kan7awlo n-updatew
            \DB::table('formation_module')
                ->where('formation_id', $formationId)
                ->where('module_id', $module->id)
                ->update(['order' => $newOrder]);
        }
    }
    
    // ... baqi l'code kima howa
    $this->updateModuleProgressAutomatically($module);

    $formation = Formation::find($formationId); // Kanb9aw nsta3mlo Formation Id 
    $formation->load('modules.user');
    
    return response()->json([
        'success' => 'Module updated successfully!', 
        'modules' => $formation->modules // Hadou msourtin f l'Model
    ]);
}

    /**
     * 🔥 Had hiya l'method l'jdida: T-calculate automatically l'progress based 3la courses created
     */
   public function updateModuleProgress(Module $module)
{
    // 1. N-akhdou l-3adad total dyal séances f l-module
    $totalModuleSessions = $module->number_seance;
    
    if (!$totalModuleSessions || $totalModuleSessions <= 0) {
        $module->update(['progress' => 0]);
        return 0;
    }

    // 2. Kan-loadiw l-courses dyal l-module
    $module->load('courses');
    
    // 3. N-filtériw l-courses unique (bach ma n-countéwch duplicates)
    $uniqueCourses = $module->courses->unique(function($course) {
        return $course->module_id . '-' . 
               $course->course_date . '-' . 
               $course->start_time . '-' . 
               $course->title;
    });

    // 4. 🔥 N-countéw GHIR l-courses li l-END TIME dyalhom FAT
    $completedCoursesCount = $uniqueCourses->filter(function ($course) {
        // N-formatiw date w end_time f format kamil
        $courseDate = $course->course_date->format('Y-m-d');
        $endTime = $course->end_time; // "13:00:00"
        
        // N-combiniw date + end_time w n-parseéwh
        $courseEndDateTime = Carbon::parse($courseDate . ' ' . $endTime);
        
        // 🐛 DEBUG: Nchoufou results (DELETE ba3d testing)
        \Log::info('Course Check', [
            'title' => $course->title,
            'date' => $courseDate,
            'end_time' => $endTime,
            'end_datetime' => $courseEndDateTime->toDateTimeString(),
            'is_past' => $courseEndDateTime->isPast(),
            'now' => Carbon::now()->toDateTimeString()
        ]);
        
        // ✅ N-checkéw wach l-wa9t dyal DBA FAT (date + end_time)
        return $courseEndDateTime->isPast();
    })->count();

    // 5. N-7assbou l-progress: (Completed / Total) * 100
    $progress = min(100, round(($completedCoursesCount / $totalModuleSessions) * 100, 2));

    // 6. N-updateéw l-module
    $module->update(['progress' => $progress]);
    
    return $progress;
}

    /**
     * Handle the progress update (Manual update - kept for manual overrides if needed).
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
        try {
            // Check permissions (ila bghiti)
            // $this->authorize('module-delete', $module);

            // Soft delete (ymchi l corbeille)
            $module->delete();

            return response()->json([
                'success' => true,
                'message' => 'Module supprimé avec succès!'
            ], 200);

        } catch (\Exception $e) {
            \Log::error('Module deletion error: ' . $e->getMessage());

            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la suppression: ' . $e->getMessage()
            ], 500);
        }
    }

    public function corbeille()
{
    $modules = Module::onlyTrashed()
                  ->with([ 'user']) 
                  ->orderBy('deleted_at', 'desc')
                  ->get();

    return view('modules.corbeille', compact('modules'));
}

// N°2. Restauration d'un Module
public function restore($id)
{
    $module = Module::withTrashed()->findOrFail($id);
    $module->restore();

    return redirect()->route('modules.corbeille')->with('success', 'Module restauré avec succès!');
}

// N°3. Suppression Définitive
public function forceDelete($id)
{
    $module = Module::withTrashed()->findOrFail($id);
    
    // Matnsach tmass7 les fichiers ila 3endek
    // if ($module->content) {
    //    foreach (json_decode($module->content, true) as $file) {
    //       Storage::disk('public')->delete($file['path']);
    //    }
    // }

    $module->forceDelete(); 

    return redirect()->route('modules.corbeille')->with('success', 'Module supprimé définitivement!');
}
}