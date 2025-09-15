<?php

namespace App\Http\Controllers;

use App\Models\CourseReschedule;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\CourseRescheduledMail;


class CourseRescheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        // The permissions here are broad, we will handle row-level security in the index method.
        $this->middleware('permission:course-list|course-manage-own', ['only' => ['index', 'show']]);
        $this->middleware('permission:course-edit|course-manage-own', ['only' => ['create', 'store', 'edit', 'update']]);
        $this->middleware('permission:course-delete|course-manage-own', ['only' => ['destroy']]);
    }

    /**
     * Display a listing of course reschedules
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        $query = CourseReschedule::with(['course', 'consultant'])
            ->orderBy('created_at', 'desc');

        // Filter based on user role
        if ($user->hasRole('Consultant') && !$user->can('course-manage-all')) {
            // Consultant: Only see reschedules for their own courses
            $query->whereHas('course', function ($q) use ($user) {
                $q->where('consultant_id', $user->id);
            });
        } elseif ($user->hasRole('Etudiant')) {
            // Student: Only see reschedules for courses they are enrolled in
            $query->whereHas('course.formations', function ($q) use ($user) {
                // Hna l'ajustement: b-delle l-`course.formation.inscriptions` b-`course.formations`.
                // o men ba3d kanzidou whereHas 3la l-`inscriptions`
                $q->whereHas('inscriptions', function ($q2) use ($user) {
                    $q2->where('user_id', $user->id)->where('status', 'active');
                });
            });
        }
        // If the user has 'course-manage-all' permission (e.g., Admin),
        // no additional filtering is applied at this stage, so they see all.

        // Search filters
        if ($request->filled('course_id')) {
            $query->where('course_id', $request->course_id);
        }

        // Only apply consultant_id filter if the user is an admin or has 'course-manage-all'
        // and a consultant_id is provided in the request.
        if ($user->can('course-manage-all') && $request->filled('consultant_id')) {
            $query->where('consultant_id', $request->consultant_id);
        }


        if ($request->filled('date_from')) {
            $query->whereDate('new_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('new_date', '<=', $request->date_to);
        }

        $reschedules = $query->paginate(15);

        // Get courses and consultants for filters
        // Adjust the courses and consultants dropdowns based on user role for filtering purposes
        $courses = collect();
        if ($user->hasRole('Etudiant')) {
            // For students, the filter dropdown should only show courses they are enrolled in
            $courses = Course::whereHas('formations', function ($q) use ($user) {
                // Hna l'ajustement: b-delle l-`formation.inscriptions` b-`formations`.
                // o men ba3d kanzidou whereHas 3la l-`inscriptions`
                $q->whereHas('inscriptions', function ($q2) use ($user) {
                    $q2->where('user_id', $user->id)->where('status', 'active');
                });
            })->select('id', 'title')->get();
        } elseif ($user->hasRole('Consultant') && !$user->can('course-manage-all')) {
            // For consultants, the filter dropdown should only show their courses
            $courses = Course::where('consultant_id', $user->id)->select('id', 'title')->get();
        } else {
            // For Admins or users with 'course-manage-all' permission, show all courses
            $courses = Course::select('id', 'title')->get();
        }


        $consultants = collect();
        if ($user->can('course-manage-all')) {
            $consultants = User::role('Consultant')->select('id', 'name')->get();
        }
        // Consultants and Students don't need to filter by other consultants in the view,
        // so `consultants` collection can remain empty for them if not `course-manage-all`.

        return view('course_reschedules.index', compact('reschedules', 'courses', 'consultants'));
    }

    // ... rest of your methods (create, store, show, edit, update, destroy, getCourseHistory, getAvailableSlots, getCoursesByConsultant, bulkReschedule, notifyStudentsAboutReschedule) remain the same or as per your existing code.
    // I've included the rest of the controller code below for completeness, but the primary change is in the index method.

    /**
     * Show the form for creating a new course reschedule
     */
    public function create(Request $request)
    {
        $user = Auth::user();
        $courseId = $request->get('course_id');
        $consultants = collect();
        $courses = collect();
        $selectedConsultantId = null;

        // Had l'variable jdida kat7edded l'date dyal ghedda f 00:00:00
        $minDate = Carbon::now()->addDay()->startOfDay();

        // 1. تحديد المستشارين الذين يمكن عرضهم للاختيار:
        if ($user->can('course-manage-all')) {
            $consultants = User::role('Consultant')->select('id', 'name')->get();
            $selectedConsultantId = old('consultant_id', $request->get('consultant_id'));
        } else {
            if ($user->hasRole('Consultant')) {
                $selectedConsultantId = $user->id;
            }
        }

        // 2. تحديد الدورات التي ستظهر في القائمة المنسدلة:
        if ($user->can('course-manage-all') && $selectedConsultantId) {
            // إذا كان مشرفًا وقد اختار مستشارًا: اعرض دورات هذا المستشار فقط ابتداءً من الغد.
            $courses = Course::where('consultant_id', $selectedConsultantId)
                                 ->whereDate('course_date', '>=', $minDate) // <-- Ajout de cette ligne
                                 ->select('id', 'title', 'course_date', 'start_time', 'end_time')->get();
        } elseif ($user->hasRole('Consultant') && !$user->can('course-manage-all')) {
            // إذا كان مستشارًا وليس لديه صلاحية 'course-manage-all': اعرض دوراته هو فقط ابتداءً من الغد.
            $courses = Course::where('consultant_id', $user->id)
                                 ->whereDate('course_date', '>=', $minDate) // <-- Ajout de cette ligne
                                 ->select('id', 'title', 'course_date', 'start_time', 'end_time')->get();
        } elseif ($user->hasRole('Etudiant')) {
            // إذا كان طالبًا: اعرض الدورات التي سجل فيها ابتداءً من الغد.
            $courses = Course::whereHas('formation.inscriptions', function ($query) use ($user) {
                $query->where('user_id', $user->id)->where('status', 'active');
            })
            ->whereDate('course_date', '>=', $minDate) // <-- Ajout de cette ligne
            ->select('id', 'title', 'course_date', 'start_time', 'end_time')->get();
        }

        $selectedCourse = null;
        if ($courseId) {
            $selectedCourse = Course::find($courseId);
        }

        return view('course_reschedules.create', compact('courses', 'selectedCourse', 'consultants', 'selectedConsultantId'));
    }

    /**
     * Store a newly created course reschedule
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        // Admin needs to select a consultant; consultant's ID is implicit for non-admins
        $validationRules = [
            'course_id' => 'required|exists:courses,id',
            'new_date' => 'required|date|after:now',
            'reason' => 'nullable|string|max:1000',
        ];

        if ($user->can('course-manage-all')) { // If admin, require consultant_id
            $validationRules['consultant_id'] = 'required|exists:users,id';
        }

        $request->validate($validationRules);

        // Get the course and verify permissions
        $course = Course::findOrFail($request->course_id);
        
        // Determine the consultant_id for the reschedule record
        $consultantToRecordId = $user->id; // Default to current user
        if ($user->can('course-manage-all') && $request->filled('consultant_id')) {
            $consultantToRecordId = $request->consultant_id; // Admin specified a consultant
        } elseif ($user->hasRole('Consultant') && !$user->can('course-manage-all')) {
            // If consultant, ensure the selected course belongs to them
            // بما أن `Course` الآن يحتوي على `consultant_id`، تحقق من تطابق معرف المستشار
            if ($course->consultant_id !== $user->id) { // <-- التغيير هنا
                abort(403, 'Unauthorized to reschedule this course.');
            }
        } else {
            // Fallback for other roles not explicitly handled, or if a non-consultant
            // tries to create without 'course-manage-all'
            abort(403, 'Unauthorized to perform this action.');
        }

        DB::beginTransaction();
        
        try {
            // Create reschedule record
            $reschedule = CourseReschedule::create([
                'course_id' => $request->course_id,
                'consultant_id' => $consultantToRecordId, // Use the determined consultant ID
                'original_date' => $course->course_date,
                'new_date' => $request->new_date,
                'reason' => $request->reason,
            ]);

            // Update the course with new date
            $course->update([
                'course_date' => $request->new_date,
                'updated_at' => now(),
            ]);

             $this->notifyStudentsAboutReschedule($course, $reschedule);

            // Here you can add notification logic to inform students
            // $this->notifyStudentsAboutReschedule($course, $reschedule);

            DB::commit();

            return redirect()->route('course_reschedules.index')
                ->with('success', 'Course has been successfully rescheduled.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->withInput()
                ->with('error', 'An error occurred while rescheduling the course: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified course reschedule
     */
    public function show($id)
    {
        $reschedule = CourseReschedule::with(['course.formation', 'consultant'])
            ->findOrFail($id);

        // Check permissions
        if (Auth::user()->hasRole('Consultant') && !Auth::user()->can('course-manage-all')) {
            // بما أن `Course` الآن يحتوي على `consultant_id`، تحقق من تطابق معرف المستشار
            if ($reschedule->course->consultant_id !== Auth::id()) { // <-- التغيير هنا (الوصول عبر course)
                abort(403, 'Unauthorized to view this reschedule.');
            }
        }

        // Add check for students
        if (Auth::user()->hasRole('Etudiant')) {
            $isEnrolled = $reschedule->course->formation->inscriptions->where('user_id', Auth::id())->where('status', 'active')->isNotEmpty();
            if (!$isEnrolled) {
                abort(403, 'Unauthorized to view this reschedule. You are not enrolled in this course.');
            }
        }

        return view('course_reschedules.show', compact('reschedule'));
    }

    /**
     * Show the form for editing the specified course reschedule
     */
    public function edit($id)
    {
        $reschedule = CourseReschedule::with('course')->findOrFail($id);

        // Check permissions
        if (Auth::user()->hasRole('Consultant') && !Auth::user()->can('course-manage-all')) {
            // بما أن `Course` الآن يحتوي على `consultant_id`، تحقق من تطابق معرف المستشار
            if ($reschedule->course->consultant_id !== Auth::id()) { // <-- التغيير هنا (الوصول عبر course)
                abort(403, 'Unauthorized to edit this reschedule.');
            }
        }

        return view('course_reschedules.edit', compact('reschedule'));
    }

    /**
     * Update the specified course reschedule
     */
    public function update(Request $request, $id)
    {
        $reschedule = CourseReschedule::findOrFail($id);

        // Check permissions
        if (Auth::user()->hasRole('Consultant') && !Auth::user()->can('course-manage-all')) {
            // بما أن `Course` الآن يحتوي على `consultant_id`، تحقق من تطابق معرف المستشار
            if ($reschedule->course->consultant_id !== Auth::id()) { // <-- التغيير هنا (الوصول عبر course)
                abort(403, 'Unauthorized to update this reschedule.');
            }
        }

        $request->validate([
            'new_date' => 'required|date|after:now',
            'reason' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        
        try {
            // Update reschedule record
            $reschedule->update([
                'new_date' => $request->new_date,
                'reason' => $request->reason,
            ]);

            // Update the associated course
            $reschedule->course->update([
                'course_date' => $request->new_date,
            ]);

             $this->notifyStudentsAboutReschedule($reschedule->course, $reschedule);

            DB::commit();

            return redirect()->route('course_reschedules.index')
                ->with('success', 'Course reschedule has been updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->withInput()
                ->with('error', 'An error occurred while updating the reschedule: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified course reschedule
     */
    public function destroy($id)
    {
        $reschedule = CourseReschedule::findOrFail($id);

        // Check permissions
        if (Auth::user()->hasRole('Consultant') && !Auth::user()->can('course-manage-all')) {
            // بما أن `Course` الآن يحتوي على `consultant_id`، تحقق من تطابق معرف المستشار
            if ($reschedule->course->consultant_id !== Auth::id()) { // <-- التغيير هنا (الوصول عبر course)
                abort(403, 'Unauthorized to delete this reschedule.');
            }
        }

        try {
            $reschedule->delete();

            return redirect()->route('course_reschedules.index')
                ->with('success', 'Course reschedule has been deleted successfully.');

        } catch (\Exception $e) {
            return back()
                ->with('error', 'An error occurred while deleting the reschedule: ' . $e->getMessage());
        }
    }

    /**
     * Get course reschedule history for a specific course
     */
    public function getCourseHistory($courseId)
    {
        $course = Course::findOrFail($courseId);
        
        // Check permissions
        if (Auth::user()->hasRole('Consultant') && !Auth::user()->can('course-manage-all')) {
            // بما أن `Course` الآن يحتوي على `consultant_id`، تحقق من تطابق معرف المستشار
            if ($course->consultant_id !== Auth::id()) { // <-- التغيير هنا (الوصول مباشرة إلى consultant_id)
                abort(403, 'Unauthorized to view this course history.');
            }
        }

        // Add check for students
        if (Auth::user()->hasRole('Etudiant')) {
            $isEnrolled = $course->formation->inscriptions->where('user_id', Auth::id())->where('status', 'active')->isNotEmpty();
            if (!$isEnrolled) {
                abort(403, 'Unauthorized to view this course history. You are not enrolled in this course.');
            }
        }

        $reschedules = CourseReschedule::with('consultant')
            ->where('course_id', $courseId)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
            'course' => $course->only(['id', 'title', 'course_date']),
            'reschedules' => $reschedules
        ]);
    }

    /**
     * Get available time slots for rescheduling
     */
    public function getAvailableSlots(Request $request)
    {
        $request->validate([
            'date' => 'required|date|after:now',
            'consultant_id' => 'required|exists:users,id',
        ]);

        $date = Carbon::parse($request->date)->format('Y-m-d');
        
        // Get existing courses for this consultant on this date
        // بما أن `Course` الآن يحتوي على `consultant_id`، غير الاستعلام ليستخدمه مباشرة
        $existingCourses = Course::where('consultant_id', $request->consultant_id) // <-- التغيير هنا
            ->whereDate('course_date', $date)
            ->select('start_time', 'end_time')
            ->get();

        // Define available time slots (you can customize this)
        $availableSlots = [
            ['start' => '09:00', 'end' => '10:30'],
            ['start' => '11:00', 'end' => '12:30'],
            ['start' => '14:00', 'end' => '15:30'],
            ['start' => '16:00', 'end' => '17:30'],
        ];

        // Filter out conflicting slots
        $freeSlots = collect($availableSlots)->filter(function ($slot) use ($existingCourses) {
            foreach ($existingCourses as $course) {
                $courseStart = Carbon::parse($course->start_time);
                $courseEnd = Carbon::parse($course->end_time);
                $slotStart = Carbon::parse($slot['start']);
                $slotEnd = Carbon::parse($slot['end']);

                // Check for overlap
                if ($slotStart < $courseEnd && $slotEnd > $courseStart) {
                    return false;
                }
            }
            return true;
        })->values();

        return response()->json(['available_slots' => $freeSlots]);
    }

    /**
     * Fetch courses based on a consultant ID (for AJAX)
     */
    public function getCoursesByConsultant(Request $request)
{
    try {
        // التحقق من أن الطلب يحتوي على consultant_id وأن المعرف موجود في جدول المستخدمين
        $request->validate([
            'consultant_id' => 'required|exists:users,id',
        ]);

        $consultantId = $request->input('consultant_id');
        Log::info("AJAX: getCoursesByConsultant called for consultant ID: {$consultantId}");

        // التحقق من أن المستخدم لديه الصلاحية المطلوبة
        if (!Auth::user()->can('course-manage-all')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // التحقق من أن المستشار المختار لديه دور "Consultant"
        $consultant = User::find($consultantId);
        if (!$consultant || !$consultant->hasRole('Consultant')) {
            return response()->json(['error' => 'Selected user is not a consultant'], 400);
        }

        // جلب الدورات المرتبطة مباشرة بهذا المستشار
        $courses = Course::where('consultant_id', $consultantId)
            ->select('id', 'title', 'course_date', 'start_time', 'end_time')
            ->orderBy('course_date', 'asc') // ترتيب الدورات حسب التاريخ
            ->get();
        
        Log::info("AJAX: Found " . $courses->count() . " courses for consultant ID {$consultantId}.");

        return response()->json($courses);

    } catch (\Illuminate\Validation\ValidationException $e) {
        Log::error("AJAX: Validation error in getCoursesByConsultant: " . json_encode($e->errors()));
        return response()->json(['error' => 'Invalid consultant ID'], 400);
    } catch (\Exception $e) {
        Log::error("AJAX: Error in getCoursesByConsultant: " . $e->getMessage());
        return response()->json(['error' => 'Server error occurred'], 500);
    }
}

    /**
     * Bulk reschedule courses
     */
    public function bulkReschedule(Request $request)
    {
        $request->validate([
            'course_ids' => 'required|array',
            'course_ids.*' => 'exists:courses,id',
            'days_to_add' => 'required|integer|min:1',
            'reason' => 'nullable|string|max:1000',
        ]);

        $successCount = 0;
        $errorCount = 0;

        DB::beginTransaction();
        
        try {
            foreach ($request->course_ids as $courseId) {
                $course = Course::find($courseId);
                
                // Check permissions for each course
                if (Auth::user()->hasRole('Consultant') && !Auth::user()->can('course-manage-all')) {
                    // بما أن `Course` الآن يحتوي على `consultant_id`، تحقق من تطابق معرف المستشار
                    if ($course->consultant_id !== Auth::id()) { // <-- التغيير هنا (الوصول مباشرة إلى consultant_id)
                        $errorCount++;
                        continue;
                    }
                }

                $originalDate = $course->course_date;
                $newDate = Carbon::parse($originalDate)->addDays($request->days_to_add);

                // Create reschedule record
                CourseReschedule::create([
                    'course_id' => $courseId,
                    'consultant_id' => Auth::id(), // من قام بالتعديل هو المستخدم الحالي
                    'original_date' => $originalDate,
                    'new_date' => $newDate,
                    'reason' => $request->reason,
                ]);

                // Update course date
                $course->update(['course_date' => $newDate]);
                
                $successCount++;
            }

            DB::commit();

            $message = "Bulk reschedule completed. {$successCount} courses rescheduled successfully";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} courses failed due to permissions.";
            }

            return redirect()->route('course_reschedules.index')
                ->with('success', $message);

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->with('error', 'Bulk reschedule failed: ' . $e->getMessage());
        }
    }

    /**
     * Private method to notify students about reschedule
     * You can implement this based on your notification system
     */
  private function notifyStudentsAboutReschedule($course, $reschedule)
{
    // Get all students enrolled in this course's formation
    $students = $course->formation->inscriptions()
        ->where('status', 'active')
        ->with('user')
        ->get()
        ->pluck('user');

    // Send notifications to each student
    foreach ($students as $student) {
        // Create notification record
        $student->notifications()->create([
            'title' => 'Course Rescheduled',
            'message' => "The course '{$course->title}' has been rescheduled from " . 
                         Carbon::parse($reschedule->original_date)->format('d/m/Y H:i') . 
                         " to " . Carbon::parse($reschedule->new_date)->format('d/m/Y H:i'),
            'type' => 'cours',
            'data' => json_encode([
                'course_id' => $course->id,
                'reschedule_id' => $reschedule->id,
                'original_date' => $reschedule->original_date,
                'new_date' => $reschedule->new_date,
                'reason' => $reschedule->reason
            ]),
            'is_read' => false,
        ]);

        // HNA khassk t'active la ligne li katseft l'email
        Mail::to($student->email)->send(new CourseRescheduledMail($course, $reschedule));
    }
}
}