<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Formation;
use App\Models\Module; // Zidna had l'import
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewCourseNotification;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;

class CourseController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
       
        $this->middleware('permission:course-join', ['only' => ['join']]);
        $this->middleware('permission:course-download-document', ['only' => ['downloadDocument']]);
    }


public function index(Request $request)
    {
        $user = Auth::user();

        // ⚠️ MODIFICATION 1 : Si l'utilisateur est un étudiant, on bloque la navigation vers les semaines futures.
        $weekOffset = $request->get('week_offset', 0);
        $viewMode = $request->get('view_mode', 'list');

        if ($user->hasRole('Etudiant') && $weekOffset > 0) {
            $weekOffset = 0;
        }


        if ($user->hasRole('Etudiant')) {
        $viewMode = $request->get('view_mode', 'planning'); // Planning par défaut pour les étudiants
        
        // Bloquer les semaines futures pour les étudiants
        if ($weekOffset > 0) {
            $weekOffset = 0;
        }
    } else {
        $viewMode = $request->get('view_mode', 'list'); // Liste par défaut pour les autres rôles
    }

        // 🗓️ Calcul de la semaine
        $weekStart = Carbon::now()->startOfWeek()->addWeeks($weekOffset);
        $weekEnd = Carbon::now()->startOfWeek()->addWeeks($weekOffset)->endOfWeek();

        // Load relations
        $query = Course::with(['consultant', 'formation', 'module']);

        // ... (Logique de filtrage par rôle pour la requête principale)
        if ($user->hasRole('Admin') || $user->hasRole('Super Admin') || $user->hasRole('Finance')) {
            // Admins see all courses
        } elseif ($user->hasRole('Consultant')) {
            $query->where('consultant_id', $user->id);
        } elseif ($user->hasRole('Etudiant')) {
            $enrolledFormationIds = $user->inscriptions()
                ->whereIn('status', ['active', 'completed'])
                ->where('access_restricted', false)
                ->pluck('formation_id');

            if ($enrolledFormationIds->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('formation_id', $enrolledFormationIds);
            }
        } else {
            $query->whereRaw('1 = 0');
        }

        // 🔥 Filtrage par semaine
       if ($user->hasRole('Etudiant')) {
    if ($viewMode === 'planning') {
        // En mode planning, l'étudiant voit uniquement la semaine en cours
        $query->whereBetween('course_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
    } else {
        // En mode liste, l'étudiant voit tous les cours jusqu'à la fin de la semaine en cours
        // (cours passés + cours de la semaine actuelle)
        $query->where('course_date', '<=', $weekEnd->format('Y-m-d'));
    }
} elseif ($viewMode === 'planning') {
    // En mode planning pour les autres rôles, filtrer par semaine
    $query->whereBetween('course_date', [$weekStart->format('Y-m-d'), $weekEnd->format('Y-m-d')]);
}
        
        // Filters based on request parameters
        if ($request->has('filter_formation_id') && $request->filter_formation_id) {
            $query->where('formation_id', $request->filter_formation_id);
        }

        // ✅ NOUVEAU FILTRE PAR MODULE
        if ($request->has('filter_module_id') && $request->filter_module_id) {
            $query->where('module_id', $request->filter_module_id);
        }
        // FIN NOUVEAU FILTRE

        if ($request->has('start_date') && $request->start_date) {
            $query->where('course_date', '>=', $request->start_date);
        }

        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // ... (Tri et pagination)

        // Tri par date et heure
        $query->orderBy('course_date', 'asc')->orderBy('start_time', 'asc');

     
            if ($viewMode === 'planning') {
                $courses = $query->get();
                // ... (Logique des coursesByDay)
                $courses = $courses->unique(function ($course) {
                    return $course->module_id . '-' .
                        $course->course_date . '-' .
                        $course->start_time . '-' .
                        $course->title;
                });

                $coursesByDay = $courses->groupBy(function ($course) {
                    return Carbon::parse($course->course_date)->format('Y-m-d');
                });
            
       } else {
    // 🔥 Mode Liste: Filtrer les duplicates POUR TOUS (Admin, Consultant, etc.)
    $allCourses = $query->get();
    
    // 🚨 NOUVELLE LOGIQUE: Grouper par module_id + date + time + title
    // et ne garder qu'un seul représentant par groupe
    $uniqueCourses = $allCourses->unique(function($course) {
        return $course->module_id . '-' . 
                    $course->course_date . '-' . 
                    $course->start_time . '-' . 
                    $course->title;
    });
    
    // 📁 Grouper les cours par module AVANT la pagination
    $groupedByModule = $uniqueCourses->groupBy(function ($course) {
        return optional($course->module)->title ?? 'Module Non Classé';
    });
    
    // 🔢 Paginer les MODULES (pas les cours)
    $perPage = 5; // 5 modules par page
    $currentPage = Paginator::resolveCurrentPage();
    
    // Récupérer les clés des modules
    $moduleKeys = $groupedByModule->keys()->toArray();
    $totalModules = count($moduleKeys);
    
    // Slice les clés des modules pour la page actuelle
    $currentPageModuleKeys = array_slice($moduleKeys, ($currentPage - 1) * $perPage, $perPage);
    
    // Récupérer uniquement les modules de la page actuelle avec leurs cours
    $currentPageModules = collect();
    foreach ($currentPageModuleKeys as $key) {
        $currentPageModules->put($key, $groupedByModule->get($key));
    }
    
    // Créer le paginator pour les modules
    $courses = new LengthAwarePaginator(
        $currentPageModules,
        $totalModules, // Total de modules
        $perPage,
        $currentPage,
        ['path' => Paginator::resolveCurrentPath()]
    );
    
    $coursesByDay = null;
}

        // ... (Formations for Modals and Filter - inchangé)
        $formationsForModals = Formation::where('status', 'published')->get();
        $formationsForFilter = collect();

        if ($user && ($user->hasRole('Admin') || $user->hasRole('Super Admin') || $user->hasRole('Finance'))) {
            $formationsForFilter = Formation::all();
        } elseif ($user && $user->hasRole('Etudiant')) {
            $enrolledFormationIds = $user->inscriptions()
                ->whereIn('status', ['active', 'completed'])
                ->where('access_restricted', false)
                ->pluck('formation_id');
            if ($enrolledFormationIds->isNotEmpty()) {
                $formationsForFilter = Formation::whereIn('id', $enrolledFormationIds)->get();
            }
        }
        
        $consultants = User::role('Consultant')->get();
        
        // ✅ NOUVEAU FILTRAGE POUR LES MODULES (pour le select/dropdown du filtre)
        $modulesQuery = Module::query();
        
        if ($user->hasRole('Etudiant')) {
            // L'étudiant voit uniquement les modules des formations auxquelles il est inscrit
            $enrolledFormationIds = $user->inscriptions()
                ->whereIn('status', ['active', 'completed','pending'])
                ->where('access_restricted', false)
                ->pluck('formation_id');
            
            if ($enrolledFormationIds->isNotEmpty()) {
                $modulesQuery->whereHas('formations', function ($q) use ($enrolledFormationIds) {
                    $q->whereIn('formations.id', $enrolledFormationIds);
                });
            } else {
                $modulesQuery->whereRaw('1 = 0'); // Aucun module
            }
        } elseif ($user->hasRole('Consultant')) {
            // Le consultant voit uniquement les modules qu'il enseigne (ceux qui sont associés à SES cours)
            $moduleIds = Course::where('consultant_id', $user->id)
                                ->pluck('module_id')
                                ->unique();
            
            if ($moduleIds->isNotEmpty()) {
                $modulesQuery->whereIn('id', $moduleIds);
            } else {
                 $modulesQuery->whereRaw('1 = 0'); // Aucun module
            }
        }
        
        $modules = $modulesQuery->where('status', 'published')->get();
        // FIN NOUVEAU FILTRAGE

        return view('courses.index', compact(
            'courses', 
            'coursesByDay', 
            'viewMode', 
            'weekStart', 
            'weekEnd', 
            'weekOffset',
            'formationsForModals', 
            'formationsForFilter', 
            'consultants',
            'modules' // Had l'variable ba9a kima hiya
        ));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // 🔥 Jdida: Kanjibu Modules bach l'user i9ad ichd men 3andhom
        $modules = Module::where('status', 'published')->get();
        $consultants = User::role('Consultant')->get();
        
        return view('courses.create', compact('modules', 'consultants'));
    }

    // 🔥 NEW: AJAX Method bach njibo Formations men Module
    public function getFormationsByModule(Module $module)
    {
        // Kanjibo ga3 les Formations li fihom had l-Module
        $formations = $module->formations()->where('status', 'published')->get();
        
        return response()->json([
            'success' => true,
            'formations' => $formations
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'module_id' => 'required|exists:modules,id',
            'consultant_id' => 'nullable|exists:users,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'course_date' => 'required|date|after_or_equal:today',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
            'zoom_link' => 'nullable|url',
            'documents.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240'
        ]);
        
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }
        
        $documentPaths = [];
        
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $file) {
                $path = $file->store('courses/documents', 'public');
                $documentPaths[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path,
                    'size' => $file->getSize(),
                    'type' => $file->getMimeType()
                ];
            }
        }
        
        // 🔥 Jdida: Kanjibo ga3 les Formations li fihom had l-Module
        $module = Module::findOrFail($request->module_id);
        $formations = $module->formations()->where('status', 'published')->get();
        
        // 🔥 Kancréew Course f kol Formation
        $createdCoursesCount = 0;
        foreach ($formations as $formation) {
            Course::create([
                'module_id' => $request->module_id,
                'formation_id' => $formation->id, // Kol formation 3andha l-course dyalha
                'consultant_id' => $request->consultant_id,
                'title' => $request->title,
                'description' => $request->description,
                'course_date' => $request->course_date,
                'start_time' => $request->start_time,
                'end_time' => $request->end_time,
                'zoom_link' => $request->zoom_link,
                'documents' => $documentPaths
            ]);
            $createdCoursesCount++;
        }
        

        // Update module progress
        $this->updateModuleProgress($request->module_id);
        
        return redirect()->route('courses.index')
            ->with('success', "Course created successfully in {$createdCoursesCount} formation(s).");
    }

   

    /**
     * Display the specified resource.
     */
public function show(Course $course)
{
    $user = Auth::user();
    
    // Load relations de base
    $course->load(['formation', 'module', 'consultant', 'evaluations', 'usersJoined']); 
    
    // Compteur et liste des utilisateurs qui ont joint
    $joinCount = $course->usersJoined->count();
    $joinedUsers = $course->usersJoined->pluck('name')->all();

    // 🔥 NOUVELLE LOGIQUE: Récupérer TOUS les cours du même module avec la même date/heure/titre
    $relatedCourses = Course::with(['formation', 'consultant'])
        ->where('module_id', $course->module_id)
        ->where('course_date', $course->course_date)
        ->where('start_time', $course->start_time)
        ->where('title', $course->title)
        ->orderBy('formation_id')
        ->get();

    // Filtrer selon le rôle de l'utilisateur
    if ($user->hasRole('Consultant')) {
        // Le consultant ne voit que ses cours
        $relatedCourses = $relatedCourses->where('consultant_id', $user->id);
    } elseif ($user->hasRole('Etudiant')) {
        // L'étudiant ne voit que les cours des formations auxquelles il est inscrit
        $enrolledFormationIds = $user->inscriptions()
            ->whereIn('status', ['active', 'completed'])
            ->where('access_restricted', false)
            ->pluck('formation_id');
        
        $relatedCourses = $relatedCourses->whereIn('formation_id', $enrolledFormationIds);
    }
    // Admin/Super Admin/Finance voient tous les cours (pas de filtre)

    return view('courses.show', compact('course', 'joinCount', 'joinedUsers', 'relatedCourses'));
}

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        $formationsForModals = Formation::where('status', 'published')->get();
        $consultants = User::role('Consultant')->get();
        return view('courses.edit', compact('course', 'formationsForModals', 'consultants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
{
    // 1. Validation: Kan7aydou 'formation_ids' array w kanzidou 'formation_id' w 'module_id'
    $validator = Validator::make($request->all(), [
        'formation_id' => 'required|exists:formations,id',
        'module_id' => 'required|exists:modules,id',
        'consultant_id' => 'nullable|exists:users,id',
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'course_date' => 'required|date',
        'start_time' => 'required|date_format:H:i',
        'end_time' => 'required|date_format:H:i|after:start_time',
        'zoom_link' => 'nullable|url',
        'recording_url' => 'nullable|url',
        'documents.*' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240'
    ]);
    
    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput()->with('edit_course_id', $course->id);
    }
    
    $documentPaths = $course->documents ?? [];
    
    // Logic for handling document uploads (unchanged)
    if ($request->hasFile('documents')) {
        foreach ($request->file('documents') as $file) {
            $path = $file->store('courses/documents', 'public');
            $documentPaths[] = [
                'name' => $file->getClientOriginalName(),
                'path' => $path,
                'size' => $file->getSize(),
                'type' => $file->getMimeType()
            ];
        }
    }

    // 🔥 Kan7sab l'old module_id 9bal ma n-update
    $oldModuleId = $course->module_id;
    
    // 2. Update the Course: Kan7aydouch ghir 'consultant_id' w kanzidou 'formation_id' w 'module_id'
    $course->update([
        'formation_id' => $request->formation_id,
        'module_id' => $request->module_id,
        'consultant_id' => $request->consultant_id,
        'title' => $request->title,
        'description' => $request->description,
        'course_date' => $request->course_date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'zoom_link' => $request->zoom_link,
        'recording_url' => $request->recording_url,
        'documents' => $documentPaths
    ]);

    // 🔥 Had hiya l'partie l'jdida: N-update progress for both old and new modules
    if ($oldModuleId != $request->module_id) {
        $this->updateModuleProgress($oldModuleId); 
        $this->updateModuleProgress($request->module_id);
    } else {
        $this->updateModuleProgress($request->module_id);
    }
    
    // 3. Kan7aydou sync() : Had l'ligne ma bqatsh 3andha m3na 7itach db l'course belongsTo formation wahda.
    // $course->formations()->sync($request->formation_ids);
    
    return redirect()->route('courses.show', $course)
        ->with('success', 'Course updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        // 🔥 Kan7sab l'module_id 9bal ma n7ayd l'course
        $moduleId = $course->module_id;

        if ($course->documents) {
            foreach ($course->documents as $document) {
                if (isset($document['path'])) {
                    Storage::disk('public')->delete($document['path']);
                }
            }
        }
        
        $course->delete();

        // 🔥 N-update l'progress dyal l'module ba3d ma 7ayydna l'course
        if ($moduleId) {
            $this->updateModuleProgress($moduleId);
        }
        
        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }

    /**
     * 🔥 Had hiya l'method l'jdida: T-update automatically l'progress dyal l'module
     */
private function updateModuleProgress($moduleId)
{
    $module = Module::find($moduleId);
    
    if (!$module || !$module->number_seance || $module->number_seance <= 0) {
        if ($module && $module->progress !== 0) {
            $module->update(['progress' => 0]);
        }
        return;
    }

    // Load courses
    $module->load('courses');
    
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
        
        // Add seconds if missing (HH:MM -> HH:MM:SS)
        if (strlen($endTime) == 5) {
            $endTime .= ':00';
        }
        
        $courseEndDateTime = Carbon::parse($courseDate . ' ' . $endTime);
        
        return $courseEndDateTime->isPast();
    })->count();
    
    // Calculate progress
    $progress = min(100, round(($completedCount / $module->number_seance) * 100, 2));

    // Update database
    $module->update(['progress' => $progress]);
}
    
    /**
     * Get courses for a specific formation (AJAX) - Removed, as a course can now have many formations.
     * This method is no longer relevant with the many-to-many relationship.
     */
    // public function getByFormation(Formation $formation)
    // {
    //     // ... Code supprimé
    // }
    
    /**
     * Remove a document from course
     */
    public function removeDocument(Course $course, Request $request)
    {
        $this->authorize('update', $course);

        $documentIndex = $request->get('document_index');
        $documents = $course->documents ?? [];
        
        if (isset($documents[$documentIndex])) {
            if (isset($documents[$documentIndex]['path'])) {
                Storage::disk('public')->delete($documents[$documentIndex]['path']);
            }
            
            unset($documents[$documentIndex]);
            $documents = array_values($documents);
            
            $course->update(['documents' => $documents]);
            
            return response()->json(['success' => true]);
        }
        
        return response()->json(['success' => false], 404);
    }
    
    /**
     * Download course document
     */
    public function downloadDocument(Course $course, Request $request)
    {
        $documentIndex = $request->get('document_index');
        $documents = $course->documents ?? [];
        
        if (isset($documents[$documentIndex]) && isset($documents[$documentIndex]['path'])) {
            $document = $documents[$documentIndex];
            $filePath = storage_path('app/public/' . $document['path']);
            
            if (file_exists($filePath)) {
                return response()->download($filePath, $document['name']);
            }
        }
        
        abort(404, 'Document not found.');
    }
    
    /**
     * Join course (for students)
     */
    public function join(Course $course)
    {
        $user = Auth::user();

        // 🔥 Jdida: Kan enregistriw belli l'utilisateur dar join l'had l'course
        // attach() kat'ajouté l'enregistrement f l'table course_joins
        // w kaychecki 3la l'unique constraint ila kayn.
        try {
            $course->usersJoined()->attach($user->id);
        } catch (\Illuminate\Database\QueryException $e) {
            // Had l'exception katla3 ila l'user déjà dar join (li m'assuré b unique constraint)
            // Mais ma khasshach t'blocki l'redirection.
            // Nقدرو ndirou log hna ila bghina, wela n'ignoréwha.
        }

        if ($course->zoom_link) {
            return redirect($course->zoom_link);
        }

        return redirect()->back()->with('error', 'No meeting link available for this course.');
    }

     public function duplicate(Course $course)
    {
        // 1. Kanakhdou les attributes dyal l'course l'9dim
        $newCourse = $course->replicate();

        // 2. ✅ Kanbeddlou ghir l'title bach n3erfoha duplicate
        $newCourse->title = 'COPY: ' . $course->title;
        
        // 🔥 Les autres champs dyal course_date, start_time, end_time, w recording_url
        // kaytkaffaw kima kanou f l'course l'9dim 7itach ma baddalnaha walou!
        
        // 3. Kan-savew l'course l'jdid
        $newCourse->save();
        
        // 4. Redirect l'page dyal l'edit bach l'user ybeddel ghir li bgha
        return redirect()->route('courses.index', $newCourse->id)
            ->with('success', 'Course duplicated successfully! Please review and update the details.');
    }
}