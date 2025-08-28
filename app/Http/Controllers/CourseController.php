<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Formation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon; // Import Carbon for date/time handling
use Illuminate\Support\Facades\Mail; // Ajout de la facade Mail
use App\Mail\NewCourseNotification; // Ajout de la nouvelle Mailable
class CourseController extends Controller
{
    // Constructor to apply policies and basic authorization
    // app/Http/Controllers/CourseController.php

// In the constructor for class-wide middleware
public function __construct()
    {
        $this->middleware('auth'); // Ensure user is logged in for all course actions

        // --- التعديل هنا: استخدام authorizeResource لضمان تطبيق السياسات ---
        // هذا السطر يطبق سياسة CoursePolicy على دوال CRUD تلقائياً
        // viewAny -> index
        // view -> show
        // create -> create, store
        // update -> edit, update
        // delete -> destroy
        $this->authorizeResource(Course::class, 'course'); 
        // ------------------------------------------------------------------

        // الصلاحيات المخصصة التي لا تغطيها authorizeResource بشكل مباشر
        // هذه تبقى كما هي لأنها ليست جزءاً من CRUD الأساسي
        $this->middleware('permission:course-join', ['only' => ['join']]);
        $this->middleware('permission:course-download-document', ['only' => ['downloadDocument']]);
        
        // يمكنك إزالة هذه الأسطر إذا كنت تعتمد بالكامل على authorizeResource
        // $this->middleware('permission:course-list', ['only' => ['index']]);
        // $this->middleware('permission:course-create', ['only' => ['create', 'store']]);
        // $this->middleware('permission:course-edit', ['only' => ['edit', 'update']]);
        // $this->middleware('permission:course-delete', ['only' => ['destroy']]);
        // $this->middleware('permission:course-view', ['only' => ['show']]); // هذا السطر يصبح زائداً مع authorizeResource
    }

    /**
     * Display a listing of the resource.
     */
     public function index(Request $request)
    {
        $user = Auth::user();

        $query = Course::with(['formation', 'formation.consultant', 'consultant']);

        // --- تطبيق فلاتر الرؤية بناءً على الدور (كما تم تعديله سابقاً) ---
        if ($user->hasRole('Admin') || $user->hasRole('Super Admin') || $user->hasRole('Finance') ) {
            
        } elseif ($user->hasRole('Consultant')) {
            $query->where('consultant_id', $user->id);
        } elseif ($user->hasRole('Etudiant')) {
            $enrolledFormations = $user->inscriptions()
                                       ->whereIn('status', ['active', 'completed'])
                                       ->where('access_restricted', false) // هنا الشرط الجديد
                                       ->pluck('formation_id');

            if ($enrolledFormations->isEmpty()) {
                $query->whereRaw('1 = 0');
            } else {
                $query->whereIn('formation_id', $enrolledFormations);
            }
        } else {
            $query->whereRaw('1 = 0');
        }
        // --- End role-based visibility filters ---

        // --- Apply general search and filter options ---
        if ($request->has('filter_formation_id') && $request->filter_formation_id) {
            $query->where('formation_id', $request->filter_formation_id);
        }
        if ($request->has('start_date') && $request->start_date) {
            $query->where('course_date', '>=', $request->start_date);
        }
        if ($request->has('search') && $request->search) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }

        // --- Time-based visibility ---
        if ($user && $user->hasRole('Etudiant')) { // Removed the 'Consultant' role
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
             $enrolledFormations = $user->inscriptions()
                                        ->whereIn('status', ['active', 'completed'])
                                        ->where('access_restricted', false)
                                        ->pluck('formation_id');
             if ($enrolledFormations->isNotEmpty()) {
                 $formationsForFilter = Formation::whereIn('id', $enrolledFormations)->get();
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
        // Policy check: Only users with 'course-create' permission can access this.
        // Handled by $this->authorizeResource(Course::class, 'course');
        $formationsForModals = Formation::where('status', 'published')->get();
        $consultants = User::role('Consultant')->get();
        return view('courses.create', compact('formationsForModals', 'consultants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    // ... (Ton code de validation) ...
    $validator = Validator::make($request->all(), [
        'formation_id' => 'required|exists:formations,id',
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
        'formation_id' => $request->formation_id,
        'consultant_id' => $request->consultant_id,
        'title' => $request->title,
        'description' => $request->description,
        'course_date' => $request->course_date,
        'start_time' => $request->start_time,
        'end_time' => $request->end_time,
        'zoom_link' => $request->zoom_link,
        'documents' => $documentPaths
    ]);

    // HNA KAN7EYYOU L'CODE LI KAYSEFT L'EMAIL
    // Had les lignes khassem ytkounou f'l'commande dial l'robot
    // if ($course->consultant_id) { ... }
    // $students = User::whereHas('inscriptions', ...)
    // foreach ($students as $student) { ... }

    return redirect()->route('courses.index')
        ->with('success', 'Course created successfully.');
}

    /**
     * Display the specified resource.
     */
   public function show(Course $course)
    {
        // Policy check: Handled by $this->authorizeResource(Course::class, 'course');
        // هذا السطر لم يعد ضرورياً هنا لأن authorizeResource يتكفل به
        // $this->authorize('view', $course); 
        
        $course->load(['formation', 'formation.consultant', 'consultant', 'evaluations']);
        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        // Policy check: Handled by $this->authorizeResource(Course::class, 'course');
        $formationsForModals = Formation::where('status', 'published')->get();
        $consultants = User::role('Consultant')->get();
        return view('courses.edit', compact('course', 'formationsForModals', 'consultants'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        // Policy check: Handled by $this->authorizeResource(Course::class, 'course');
        $validator = Validator::make($request->all(), [
            'formation_id' => 'required|exists:formations,id',
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
            'formation_id' => $request->formation_id,
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
        
        return redirect()->route('courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        // Policy check: Handled by $this->authorizeResource(Course::class, 'course');
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
     * Get courses for a specific formation (AJAX)
     */
    public function getByFormation(Formation $formation)
    {
        // This method might need its own policy check if it's accessible directly
        $courses = $formation->courses()
            ->orderBy('course_date', 'asc')
            ->get(['id', 'title', 'course_date', 'start_time', 'end_time']);
            
        return response()->json($courses);
    }
    
    /**
     * Remove a document from course
     */
    public function removeDocument(Course $course, Request $request)
    {
        // Policy check: Uses 'update' ability on CoursePolicy
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
        // Policy check: Handled by middleware('can:course-download-document,course') in constructor
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
        // Policy check: Handled by middleware('can:course-join,course') in constructor
        // The detailed enrollment check is part of the 'join' policy method.
        
        if ($course->zoom_link) {
            return redirect($course->zoom_link);
        }
        
        return redirect()->back()->with('error', 'No meeting link available for this course.');
    }


    
}