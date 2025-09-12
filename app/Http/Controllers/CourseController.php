<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Formation;
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

    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
    {
        $user = Auth::user();

        // On charge la relation 'formations' et les autres relations.
        // On prépare la requête sans la limiter pour les formations, on le fera plus tard pour l'étudiant.
        $query = Course::with(['consultant', 'formations']);

   
        if ($user->hasRole('Admin') || $user->hasRole('Super Admin') || $user->hasRole('Finance')) {
            // Pas de filtre sur les cours pour ces rôles.
        } elseif ($user->hasRole('Consultant')) {
            $query->where('consultant_id', $user->id);
        } elseif ($user->hasRole('Etudiant')) {
            $enrolledFormationIds = $user->inscriptions()
                ->whereIn('status', ['active', 'completed'])
                ->where('access_restricted', false)
                ->pluck('formation_id');

            if ($enrolledFormationIds->isEmpty()) {
                // S'il n'est inscrit à aucune formation, ne lui montrer aucun cours.
                $query->whereRaw('1 = 0');
            } else {
                // Modifié pour utiliser whereHas sur la relation belongsToMany
                $query->whereHas('formations', function ($q) use ($enrolledFormationIds) {
                    $q->whereIn('formation_id', $enrolledFormationIds);
                });
                
                // C'est ici qu'on filtre les formations chargées pour n'afficher que celles
                // auxquelles l'étudiant est inscrit.
                $query->with(['formations' => function ($q) use ($enrolledFormationIds) {
                    $q->whereIn('formations.id', $enrolledFormationIds);
                }]);
            }
        } else {
            // Ne rien montrer si le rôle n'est pas reconnu.
            $query->whereRaw('1 = 0');
        }
        // --- End role-based visibility filters ---

        // --- Apply general search and filter options ---
        if ($request->has('filter_formation_id') && $request->filter_formation_id) {
            $query->whereHas('formations', function ($q) use ($request) {
                $q->where('formation_id', $request->filter_formation_id);
            });
        }
        if ($request->has('start_date') && $request->start_date) {
            $query->where('course_date', '>=', $request->start_date);
        }
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // --- Time-based visibility ---
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
        // --- End time-based visibility ---

        $courses = $query->orderBy('created_at', 'desc')->paginate(15);

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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
         $validator = Validator::make($request->all(), [
     'formation_ids' => 'required|array',
'formation_ids.*' => 'exists:formations,id',
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
            'consultant_id' => $request->consultant_id,
            'title' => $request->title,
            'description' => $request->description,
            'course_date' => $request->course_date,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'zoom_link' => $request->zoom_link,
            'documents' => $documentPaths
        ]);

        // MODIFICATION ICI : On utilise `formation_ids` pour la synchronisation
       $course->formations()->sync($request->formation_ids);
        
        return redirect()->route('courses.index')
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        // La relation a été changée de 'formation' à 'formations'
        $course->load(['formations', 'consultant', 'evaluations']);
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
    $validator = Validator::make($request->all(), [
        'formation_ids' => 'required|array', // Hna bdelt smit "formations" b "formation_ids"
        'formation_ids.*' => 'exists:formations,id', // Hna bdelt smit "formations.*" b "formation_ids.*"
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
    
    $course->update([
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

    // Hna ghadi n-bedlou "formations" b "formation_ids"
    $course->formations()->sync($request->formation_ids);
    
    return redirect()->route('courses.show', $course)
        ->with('success', 'Course updated successfully.');
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        if ($course->documents) {
            foreach ($course->documents as $document) {
                if (isset($document['path'])) {
                    Storage::disk('public')->delete($document['path']);
                }
            }
        }
        
        $course->delete();
        
        return redirect()->route('courses.index')
            ->with('success', 'Course deleted successfully.');
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
