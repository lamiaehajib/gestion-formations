<?php

namespace App\Http\Controllers;

use App\Models\Formation;
use App\Models\Inscription;
use App\Models\Module;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ModuleController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:module-list', ['only' => ['index']]);
        $this->middleware('permission:module-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:module-edit', ['only' => ['edit', 'update', 'updateAjax']]);
        $this->middleware('permission:module-delete', ['only' => ['destroy', 'destroyAjax']]);
        $this->middleware('permission:module-view-own', ['only' => ['show']]);
        $this->middleware('permission:module-update-progress', ['only' => ['updateProgress']]);
    }

    public function index()
    {
        $user = Auth::user();
        $formations = collect();
        $uniqueModules = collect();

        if ($user) {
            if ($user->hasRole('Admin')) {
                $formations = Formation::withCount('modules')->get();
            } 
            elseif ($user->hasRole('Consultant')) {
                $uniqueModules = Module::where('user_id', $user->id)
                    ->with('formations')
                    ->get()
                    ->unique('id');
                
                $formations = collect();
            } 
            elseif ($user->hasRole('Etudiant')) {
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

    public function show(Formation $formation)
    {
        $user = Auth::user();
        $consultants = User::role('consultant')->get(['id', 'name']);

        if ($user->hasRole('Admin')) {
            $formation->load('modules.user'); 
        } elseif ($user->hasRole('Consultant')) {
            $formation->load(['modules' => function ($query) use ($user) {
                $query->where('user_id', $user->id);
            }, 'modules.user']);
        } elseif ($user->hasRole('Etudiant')) {
            $is_inscribed = Inscription::where('user_id', $user->id)
                                       ->where('formation_id', $formation->id)
                                       ->exists();
            if ($is_inscribed) {
                $formation->load('modules.user');
            } else {
                return redirect()->route('modules.index')->with('error', 'You are not authorized to view this formation.');
            }
        } else {
            return redirect()->route('modules.index')->with('error', 'You do not have permission to view this content.');
        }

        return view('modules.show', compact('formation', 'consultants')); 
    }

    public function details(Module $module)
    {
        $user = Auth::user();
        
        if ($user->hasRole('Consultant') && $module->user_id !== $user->id) {
            return redirect()->route('modules.index')
                ->with('error', 'Vous n\'êtes pas autorisé à voir ce module.');
        }
        
        $module->load([
            'formations', 
            'user', 
            'courses' => function($query) {
                $query->orderBy('course_date', 'asc')->orderBy('start_time', 'asc'); 
            }
        ]);
        
        $progress = $this->calculateAndUpdateProgress($module);
        $module->refresh();

        $uniqueCourses = $module->courses->unique(function($course) {
            return $course->module_id . '-' . 
                   $course->course_date . '-' . 
                   $course->start_time . '-' . 
                   $course->title;
        });

        $totalCourses = $uniqueCourses->count();
        
        $completedCourses = $uniqueCourses->filter(function ($course) {
            $courseDate = $course->course_date->format('Y-m-d');
            $endTime = $course->end_time;
            
            if (strlen($endTime) == 5) {
                $endTime .= ':00';
            }
            
            $courseEndDateTime = Carbon::parse($courseDate . ' ' . $endTime);
            
            return $courseEndDateTime->isPast();
        })->count();

        $upcomingCourses = $uniqueCourses->filter(function ($course) {
            return $course->course_date->isFuture();
        })->count();

        $coursesList = $uniqueCourses; 
        
        return view('modules.details', compact(
            'module', 
            'totalCourses', 
            'completedCourses', 
            'upcomingCourses',
            'coursesList'
        ));
    }

    private function calculateAndUpdateProgress(Module $module)
    {
        if (!$module->number_seance || $module->number_seance <= 0) {
            $module->update(['progress' => 0]);
            return 0;
        }

        $uniqueCourses = $module->courses->unique(function($course) {
            return $course->module_id . '-' . 
                   $course->course_date . '-' . 
                   $course->start_time . '-' . 
                   $course->title;
        });

        $completedCount = $uniqueCourses->filter(function ($course) {
            $courseDate = $course->course_date->format('Y-m-d');
            $endTime = $course->end_time;
            
            if (strlen($endTime) == 5) {
                $endTime .= ':00';
            }
            
            $courseEndDateTime = Carbon::parse($courseDate . ' ' . $endTime);
            
            return $courseEndDateTime->isPast();
        })->count();

        $progress = min(100, round(($completedCount / $module->number_seance) * 100, 2));

        $module->update(['progress' => $progress]);
        
        return $progress;
    }

    public function create()
    {
        $formations = Formation::all();
        $consultants = User::role('consultant')->get();
        return view('modules.create', compact('formations', 'consultants'));
    }

    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'status' => 'required|in:draft,published',
            'content' => 'required|string',
            'user_id' => 'required|exists:users,id',
            'duration_hours' => 'nullable|integer|min:0', 
            'number_seance' => 'nullable|integer|min:1', 
            'formation_ids' => 'required|array|min:1',
            'formation_ids.*' => 'required|exists:formations,id',
        ]);

        $contentArray = explode("\n", $validatedData['content']);

        $module = Module::create([
            'title' => $validatedData['title'],
            'status' => $validatedData['status'],
            'content' => $contentArray,
            'user_id' => $validatedData['user_id'],
            'progress' => 0, 
            'duration_hours' => $validatedData['duration_hours'] ?? null,
            'number_seance' => $validatedData['number_seance'] ?? null,
        ]);

        $pivotData = [];
        
        foreach ($validatedData['formation_ids'] as $formationId) {
            $lastOrder = DB::table('formation_module')
                ->where('formation_id', $formationId)
                ->max('order'); 
                
            $nextOrder = ($lastOrder ?? 0) + 1; 
            $pivotData[$formationId] = ['order' => $nextOrder];
        }

        $module->formations()->attach($pivotData);

        return redirect()->route('modules.index')->with('success', 'Module ' . $module->title . ' created and linked to ' . count($validatedData['formation_ids']) . ' formation(s) successfully!');
    }

    public function edit(Module $module)
    {
        $formations = Formation::all();
        $consultants = User::role('consultant')->get();
        return view('modules.edit', compact('module', 'formations', 'consultants'));
    }

    // 🔥 FIXED: Update method with automatic progress recalculation
    public function update(Request $request, Module $module)
    {
        $validatedData = $request->validate([
            'title' => 'required|string|max:255',
            'duration_hours' => 'nullable|integer|min:0',
            'number_seance' => 'nullable|integer|min:1',
            'new_order' => 'required|integer|min:1',
            'formation_id' => 'required|exists:formations,id',
            'status' => 'required|in:draft,published',
            'content' => 'required|string',
            'user_id' => 'required|exists:users,id',
        ]);
        
        $formationId = $validatedData['formation_id'];
        $newOrder = $validatedData['new_order'];

        // 1. Update Module data
        $contentArray = explode("\n", $validatedData['content']);
        $module->update(array_merge($validatedData, [
            'content' => $contentArray,
            'number_seance' => $validatedData['number_seance'] ?? null,
        ]));

        // 2. Update Order in Pivot Table
        $pivotData = DB::table('formation_module')
            ->where('formation_id', $formationId)
            ->where('module_id', $module->id)
            ->first();

        if ($pivotData) {
            $oldOrder = $pivotData->order;

            if ($oldOrder != $newOrder) {
                DB::table('formation_module')
                    ->where('formation_id', $formationId)
                    ->where('order', $newOrder)
                    ->update(['order' => $oldOrder]);

                DB::table('formation_module')
                    ->where('formation_id', $formationId)
                    ->where('module_id', $module->id)
                    ->update(['order' => $newOrder]);
            }
        }
        
        // 🔥 3. CRITICAL: Recalculate progress automatically after update
        $module->refresh(); // Reload module with new data
        $module->load('courses'); // Load courses
        $this->calculateAndUpdateProgress($module); // Recalculate progress
        $module->refresh(); // Reload to get updated progress

        $formation = Formation::find($formationId);
        $formation->load('modules.user');
        
        return response()->json([
            'success' => 'Module updated successfully!', 
            'modules' => $formation->modules
        ]);
    }

    public function updateModuleProgress(Module $module)
    {
        $totalModuleSessions = $module->number_seance;
        
        if (!$totalModuleSessions || $totalModuleSessions <= 0) {
            $module->update(['progress' => 0]);
            return 0;
        }

        $module->load('courses');
        
        $uniqueCourses = $module->courses->unique(function($course) {
            return $course->module_id . '-' . 
                   $course->course_date . '-' . 
                   $course->start_time . '-' . 
                   $course->title;
        });

        $completedCoursesCount = $uniqueCourses->filter(function ($course) {
            $courseDate = $course->course_date->format('Y-m-d');
            $endTime = $course->end_time;
            
            $courseEndDateTime = Carbon::parse($courseDate . ' ' . $endTime);
            
            return $courseEndDateTime->isPast();
        })->count();

        $progress = min(100, round(($completedCoursesCount / $totalModuleSessions) * 100, 2));

        $module->update(['progress' => $progress]);
        
        return $progress;
    }

    public function updateProgress(Request $request, Module $module)
    {
        if (Auth::id() !== $module->user_id) {
            return redirect()->back()->with('error', 'You are not authorized to update progress for this module.');
        }

        $request->validate([
            'progress' => 'required|integer|min:0|max:100',
        ]);

        $module->update(['progress' => $request->progress]);

        return redirect()->back()->with('success', 'Module progress updated successfully!');
    }

    public function getModuleData(Module $module)
    {
        $consultants = User::role('Consultant')->get(['id', 'name']);
        return response()->json([
            'module' => $module,
            'consultants' => $consultants
        ]);
    }

    public function destroyAjax(Module $module)
    {
        try {
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
                      ->with(['user']) 
                      ->orderBy('deleted_at', 'desc')
                      ->get();

        return view('modules.corbeille', compact('modules'));
    }

    public function restore($id)
    {
        $module = Module::withTrashed()->findOrFail($id);
        $module->restore();

        return redirect()->route('modules.corbeille')->with('success', 'Module restauré avec succès!');
    }

    public function forceDelete($id)
    {
        $module = Module::withTrashed()->findOrFail($id);
        $module->forceDelete(); 

        return redirect()->route('modules.corbeille')->with('success', 'Module supprimé définitivement!');
    }
}