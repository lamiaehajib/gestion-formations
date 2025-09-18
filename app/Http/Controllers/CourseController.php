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

        // Kan3ayt 3la l'relations 'consultant' w 'formation'
        // 'formation' 7itach l'relation f l'Model Course hiya belongsTo
        $query = Course::with(['consultant', 'formation']);

        if ($user->hasRole('Admin') || $user->hasRole('Super Admin') || $user->hasRole('Finance')) {
            // L'admin kaychouf ga3 les courses, ma7tajnch nzidou filter
        } elseif ($user->hasRole('Consultant')) {
            // L'Consultant kaychouf ghir les courses dyalou
            $query->where('consultant_id', $user->id);
        } elseif ($user->hasRole('Etudiant')) {
            // L'étudiant kaychouf ghir les courses dyal les formations li dayer fihom inscription
            $enrolledFormationIds = $user->inscriptions()
                ->whereIn('status', ['active', 'completed'])
                ->where('access_restricted', false)
                ->pluck('formation_id');

            if ($enrolledFormationIds->isEmpty()) {
                // Ila ma kan 3andou 7ta formation, ma kaychouf walou
                $query->whereRaw('1 = 0');
            } else {
                // Kanfiltrio 3la 7sab 'formation_id'
                $query->whereIn('formation_id', $enrolledFormationIds);

                // Eager load ghir les formations li kaynin f had l'courses
                $query->with([
                    'formation' => function ($q) use ($enrolledFormationIds) {
                        $q->whereIn('id', $enrolledFormationIds);
                    }
                ]);
            }
        } else {
            // ila kan chi role akhor ma kaychouf walou
            $query->whereRaw('1 = 0');
        }

        // Filter 3la 7sab l'formation
        if ($request->has('filter_formation_id') && $request->filter_formation_id) {
            $query->where('formation_id', $request->filter_formation_id);
        }

        // Filter 3la 7sab date
        if ($request->has('start_date') && $request->start_date) {
            $query->where('course_date', '>=', $request->start_date);
        }

        // Filter 3la 7sab l'search bar
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // Filter l'étudiants باش يشوفو ghir les courses li sde9o
        if ($user && $user->hasRole('Etudiant')) {
            $now = Carbon::now();
            $query->where(function ($q) use ($now) {
                $q->where('course_date', '<', $now->toDateString())
                    ->orWhere(function ($q2) use ($now) {
                        $q2->where('course_date', $now->toDateString())
                            ->whereRaw("TIME_TO_SEC(CONCAT(course_date, ' ', start_time)) <= TIME_TO_SEC(?)", [
                                $now->copy()->addMinutes(5)->toDateTimeString()
                            ]);
                    });
            });
        }

        $courses = $query->orderBy('created_at', 'desc')->paginate(15);

        $formationsForModals = Formation::where('status', 'published')->get();
        $formationsForFilter = collect();

        // Kan7addou les formations li ghaybanou f l'filter
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

        return view('courses.index', compact('courses', 'formationsForModals', 'formationsForFilter', 'consultants'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $formationsForModals = Formation::where('status', 'published')->get();
        $consultants = User::role('Consultant')->get();
        return view('courses.create', compact('formationsForModals', 'consultants'));
    }

 
    public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'formation_id' => 'required|exists:formations,id', // Modifié pour être un seul ID, non un array
        'module_id' => 'required|exists:modules,id', // Ajouté le champ module_id
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
    
    $course = Course::create([
        'formation_id' => $request->formation_id, // Utilise le champ formation_id du formulaire
        'module_id' => $request->module_id, // Utilise le champ module_id du formulaire
        'consultant_id' => $request->consultant_id,
        'title' => $request->title,
        'description' => $request->description,
        'course_date' => $request->course_date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'zoom_link' => $request->zoom_link,
        'documents' => $documentPaths
    ]);

    // 🔥 Had hiya l'partie l'jdida: N-update automatically l'progress dyal l'module
    $this->updateModuleProgress($course->module_id);

    // La ligne suivante a été supprimée car la relation belongsToMany n'existe plus
    // $course->formations()->sync($request->formation_ids);
    
    return redirect()->route('courses.index')
        ->with('success', 'Course created successfully.');
}

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
{
 
    $course->load(['formation', 'module', 'consultant', 'evaluations']);
    
    return view('courses.show', compact('course'));
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
        // Ila baddal l'module, kan-update l'progress dyal both modules
        $this->updateModuleProgress($oldModuleId); // L'module l'9dim
        $this->updateModuleProgress($request->module_id); // L'module l'jdid
    } else {
        // Ila bqa f nafs l'module, kan-update ghir hadak
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
        // Kanjib l'module men database
        $module = Module::find($moduleId);
        
        // Ila ma l9inach l'module aw ma kandiroch number_seance, ma ndirou walou
        if (!$module || !$module->number_seance || $module->number_seance <= 0) {
            return;
        }

        // Kan7sab 3adad les courses li daru f had l'module
        $coursesCount = Course::where('module_id', $moduleId)->count();

        // Kan7sab l'progress: (3adad les courses / number_seance) * 100
        $progress = min(100, round(($coursesCount / $module->number_seance) * 100, 2));

        // Kan-update l'progress f l'database
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
        if ($course->zoom_link) {
            return redirect($course->zoom_link);
        }
        
        return redirect()->back()->with('error', 'No meeting link available for this course.');
    }
}