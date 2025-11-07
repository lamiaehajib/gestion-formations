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

    // 🔥 Load courses with module for grouping
    $query = CourseReschedule::with(['course.module', 'consultant'])
        ->orderBy('created_at', 'desc');

    // Filter based on user role
    if ($user->hasRole('Consultant') && !$user->can('course-manage-all')) {
        $query->whereHas('course', function ($q) use ($user) {
            $q->where('consultant_id', $user->id);
        });
    } elseif ($user->hasRole('Etudiant')) {
        $enrolledFormationIds = $user->inscriptions()
                                    ->where('status', 'active')
                                    ->pluck('formation_id');

        $query->whereHas('course', function ($q) use ($enrolledFormationIds) {
            $q->whereIn('formation_id', $enrolledFormationIds);
        });
    }

    // Search filters
    if ($request->filled('course_id')) {
        $query->where('course_id', $request->course_id);
    }
    if ($user->can('course-manage-all') && $request->filled('consultant_id')) {
        $query->where('consultant_id', $request->consultant_id);
    }
    if ($request->filled('date_from')) {
        $query->whereDate('new_date', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('new_date', '<=', $request->date_to);
    }

    // 🔥 Get ALL reschedules BEFORE pagination
    $allReschedules = $query->get();

    // 🔥 Group duplicates by course attributes
    $groupedReschedules = $allReschedules->unique(function ($reschedule) {
        $course = $reschedule->course;
        return $course->module_id . '-' .
               $course->course_date . '-' .
               $course->start_time . '-' .
               $course->title . '-' .
               $reschedule->new_date; // Include new_date in grouping
    });

    // 🔥 Manual pagination
    $perPage = 15;
    $currentPage = \Illuminate\Pagination\Paginator::resolveCurrentPage();
    $currentPageItems = $groupedReschedules->slice(($currentPage - 1) * $perPage, $perPage)->values();

    $reschedules = new \Illuminate\Pagination\LengthAwarePaginator(
        $currentPageItems,
        $groupedReschedules->count(),
        $perPage,
        $currentPage,
        ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
    );

    // --- Adjust Courses for Filters ---
    $courses = collect();
    if ($user->hasRole('Etudiant')) {
        $enrolledFormationIds = $user->inscriptions()
                                    ->where('status', 'active')
                                    ->pluck('formation_id');
                                    
        $courses = Course::whereIn('formation_id', $enrolledFormationIds)
                         ->select('id', 'title')->get();
                         
    } elseif ($user->hasRole('Consultant') && !$user->can('course-manage-all')) {
        $courses = Course::where('consultant_id', $user->id)->select('id', 'title')->get();
    } else {
        $courses = Course::select('id', 'title')->get();
    }

    $consultants = collect();
    if ($user->can('course-manage-all')) {
        $consultants = User::role('Consultant')->select('id', 'name')->get();
    }

    return view('course_reschedules.index', compact('reschedules', 'courses', 'consultants'));
}

    /**
     * 🔥 NEW: Show the form for creating a new course reschedule
     * Admin/Consultant shof ghir 1 course (grouped), machi duplicates
     */
public function create(Request $request)
{
    $user = Auth::user();
    $courseId = $request->get('course_id');
    $consultants = collect();
    $courses = collect();
    $selectedConsultantId = null;

    $todayStart = Carbon::now()->startOfDay();
    $tomorrowStart = Carbon::now()->addDay()->startOfDay();

    // 1. Determine consultants
    if ($user->can('course-manage-all')) {
        $consultants = User::role('Consultant')->select('id', 'name')->get();
        $selectedConsultantId = old('consultant_id', $request->get('consultant_id'));
    } else {
        if ($user->hasRole('Consultant')) {
            $selectedConsultantId = $user->id;
        }
    }

    // 2. 🔥 Get courses and GROUP them (like CourseController::index)
    $coursesQuery = Course::query();

    if ($user->can('course-manage-all') && $selectedConsultantId) {
        // Admin: courses min selected consultant from today onwards
        $coursesQuery->where('consultant_id', $selectedConsultantId)
                     ->whereDate('course_date', '>=', $todayStart);
    } elseif ($user->hasRole('Consultant') && !$user->can('course-manage-all')) {
        // Consultant: their courses from tomorrow onwards
        $coursesQuery->where('consultant_id', $user->id)
                     ->whereDate('course_date', '>=', $tomorrowStart);
    } elseif ($user->hasRole('Etudiant')) {
        // Student: enrolled courses from today onwards
        $coursesQuery->whereHas('formation.inscriptions', function ($query) use ($user) {
            $query->where('user_id', $user->id)->where('status', 'active');
        })
        ->whereDate('course_date', '>=', $todayStart);
    }

    // 🔥 Fetch and GROUP duplicates
    $allCourses = $coursesQuery->select('id', 'title', 'course_date', 'start_time', 'end_time', 'module_id')
                                ->orderBy('course_date', 'asc')
                                ->get();

    // Group by: module_id + course_date + start_time + title
    $courses = $allCourses->unique(function ($course) {
        return $course->module_id . '-' .
               $course->course_date . '-' .
               $course->start_time . '-' .
               $course->title;
    });

    $selectedCourse = null;
    if ($courseId) {
        $selectedCourse = Course::find($courseId);
    }

    return view('course_reschedules.create', compact('courses', 'selectedCourse', 'consultants', 'selectedConsultantId'));
}

    /**
     * 🔥 NEW: Store reschedule + reschedule ALL duplicate courses
     */
public function store(Request $request)
{
    $user = Auth::user();

    $validationRules = [
        'course_id' => 'required|exists:courses,id',
        'new_date' => 'required|date|after:now',
        'reason' => 'nullable|string|max:1000',
    ];

    if ($user->can('course-manage-all')) {
        $validationRules['consultant_id'] = 'required|exists:users,id';
    }

    $request->validate($validationRules);

    // Get the selected course
    $course = Course::findOrFail($request->course_id);
    
    // Determine consultant_id
    $consultantToRecordId = $user->id;
    if ($user->can('course-manage-all') && $request->filled('consultant_id')) {
        $consultantToRecordId = $request->consultant_id;
    } elseif ($user->hasRole('Consultant') && !$user->can('course-manage-all')) {
        if ($course->consultant_id !== $user->id) {
            abort(403, 'Unauthorized to reschedule this course.');
        }
    } else {
        abort(403, 'Unauthorized to perform this action.');
    }

    DB::beginTransaction();
    
    try {
        // 🔥 Find ALL duplicate courses (same module, date, time, title)
        $duplicateCourses = Course::where('module_id', $course->module_id)
            ->where('course_date', $course->course_date)
            ->where('start_time', $course->start_time)
            ->where('title', $course->title)
            ->get();

        // 🔥 Reschedule EACH duplicate course
        foreach ($duplicateCourses as $dupCourse) {
            // Create reschedule record
            CourseReschedule::create([
                'course_id' => $dupCourse->id,
                'consultant_id' => $consultantToRecordId,
                'original_date' => $dupCourse->course_date,
                'new_date' => $request->new_date,
                'reason' => $request->reason,
            ]);

            // Update course date
            $dupCourse->update([
                'course_date' => $request->new_date,
                'updated_at' => now(),
            ]);

            // Notify students for this specific course
            $this->notifyStudentsAboutReschedule($dupCourse, CourseReschedule::where('course_id', $dupCourse->id)->latest()->first());
        }

        DB::commit();

        $count = $duplicateCourses->count();
        return redirect()->route('course_reschedules.index')
            ->with('success', "Course rescheduled successfully! {$count} course(s) have been updated.");

    } catch (\Exception $e) {
        DB::rollback();
        
        return back()
            ->withInput()
            ->with('error', 'An error occurred while rescheduling: ' . $e->getMessage());
    }
}

    /**
     * Display the specified course reschedule
     */
    public function show($id)
    {
        $reschedule = CourseReschedule::with(['course.formation', 'consultant'])
            ->findOrFail($id);

        if (Auth::user()->hasRole('Consultant') && !Auth::user()->can('course-manage-all')) {
            if ($reschedule->course->consultant_id !== Auth::id()) {
                abort(403, 'Unauthorized to view this reschedule.');
            }
        }

        if (Auth::user()->hasRole('Etudiant')) {
            $isEnrolled = $reschedule->course->formation->inscriptions->where('user_id', Auth::id())->where('status', 'active')->isNotEmpty();
            if (!$isEnrolled) {
                abort(403, 'Unauthorized to view this reschedule.');
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

        if (Auth::user()->hasRole('Consultant') && !Auth::user()->can('course-manage-all')) {
            if ($reschedule->course->consultant_id !== Auth::id()) {
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

        if (Auth::user()->hasRole('Consultant') && !Auth::user()->can('course-manage-all')) {
            if ($reschedule->course->consultant_id !== Auth::id()) {
                abort(403, 'Unauthorized to update this reschedule.');
            }
        }

        $request->validate([
            'new_date' => 'required|date|after:now',
            'reason' => 'nullable|string|max:1000',
        ]);

        DB::beginTransaction();
        
        try {
            $reschedule->update([
                'new_date' => $request->new_date,
                'reason' => $request->reason,
            ]);

            $reschedule->course->update([
                'course_date' => $request->new_date,
            ]);

            $this->notifyStudentsAboutReschedule($reschedule->course, $reschedule);

            DB::commit();

            return redirect()->route('course_reschedules.index')
                ->with('success', 'Course reschedule updated successfully.');

        } catch (\Exception $e) {
            DB::rollback();
            
            return back()
                ->withInput()
                ->with('error', 'An error occurred: ' . $e->getMessage());
        }
    }

    /**
     * Get course reschedule history
     */
    public function getCourseHistory($courseId)
    {
        $course = Course::findOrFail($courseId);
        
        if (Auth::user()->hasRole('Consultant') && !Auth::user()->can('course-manage-all')) {
            if ($course->consultant_id !== Auth::id()) {
                abort(403, 'Unauthorized.');
            }
        }

        if (Auth::user()->hasRole('Etudiant')) {
            $isEnrolled = $course->formation->inscriptions->where('user_id', Auth::id())->where('status', 'active')->isNotEmpty();
            if (!$isEnrolled) {
                abort(403, 'Unauthorized.');
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
        
        $existingCourses = Course::where('consultant_id', $request->consultant_id)
            ->whereDate('course_date', $date)
            ->select('start_time', 'end_time')
            ->get();

        $availableSlots = [
            ['start' => '09:00', 'end' => '10:30'],
            ['start' => '11:00', 'end' => '12:30'],
            ['start' => '14:00', 'end' => '15:30'],
            ['start' => '16:00', 'end' => '17:30'],
        ];

        $freeSlots = collect($availableSlots)->filter(function ($slot) use ($existingCourses) {
            foreach ($existingCourses as $course) {
                $courseStart = Carbon::parse($course->start_time);
                $courseEnd = Carbon::parse($course->end_time);
                $slotStart = Carbon::parse($slot['start']);
                $slotEnd = Carbon::parse($slot['end']);

                if ($slotStart < $courseEnd && $slotEnd > $courseStart) {
                    return false;
                }
            }
            return true;
        })->values();

        return response()->json(['available_slots' => $freeSlots]);
    }

    /**
     * 🔥 FIXED: Fetch courses by consultant (for AJAX in create form)
     */
public function getCoursesByConsultant(Request $request)
{
    try {
        $request->validate([
            'consultant_id' => 'required|exists:users,id',
        ]);

        $consultantId = $request->input('consultant_id');

        if (!Auth::user()->can('course-manage-all')) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $consultant = User::find($consultantId);
        if (!$consultant || !$consultant->hasRole('Consultant')) {
            return response()->json(['error' => 'Not a consultant'], 400);
        }

        // 🔥 Get courses from today onwards
        $allCourses = Course::where('consultant_id', $consultantId)
            ->whereDate('course_date', '>=', Carbon::now()->startOfDay())
            ->select('id', 'title', 'course_date', 'start_time', 'end_time', 'module_id')
            ->orderBy('course_date', 'asc')
            ->get();
        
        // 🔥 Group duplicates
        $courses = $allCourses->unique(function ($course) {
            return $course->module_id . '-' .
                   $course->course_date . '-' .
                   $course->start_time . '-' .
                   $course->title;
        })->values();

        return response()->json($courses);

    } catch (\Exception $e) {
        Log::error("AJAX Error: " . $e->getMessage());
        return response()->json(['error' => 'Server error'], 500);
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
                
                if (Auth::user()->hasRole('Consultant') && !Auth::user()->can('course-manage-all')) {
                    if ($course->consultant_id !== Auth::id()) {
                        $errorCount++;
                        continue;
                    }
                }

                $originalDate = $course->course_date;
                $newDate = Carbon::parse($originalDate)->addDays($request->days_to_add);

                CourseReschedule::create([
                    'course_id' => $courseId,
                    'consultant_id' => Auth::id(),
                    'original_date' => $originalDate,
                    'new_date' => $newDate,
                    'reason' => $request->reason,
                ]);

                $course->update(['course_date' => $newDate]);
                
                $successCount++;
            }

            DB::commit();

            $message = "Bulk reschedule completed. {$successCount} courses rescheduled";
            if ($errorCount > 0) {
                $message .= ", {$errorCount} failed.";
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
     * Notify students about reschedule
     */
private function notifyStudentsAboutReschedule($course, $reschedule)
{
    $students = $course->formation->inscriptions()
        ->where('status', 'active')
        ->with('user')
        ->get()
        ->pluck('user');

    foreach ($students as $student) {
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

        Mail::to($student->email)->send(new CourseRescheduledMail($course, $reschedule));
    }
}

    /**
     * Remove the specified course reschedule
     */
public function destroy($id)
{
    $reschedule = CourseReschedule::with('course')->findOrFail($id);
    $user = Auth::user();

    if (!$user->can('course-delete') && !$user->can('course-manage-own')) {
        abort(403, 'Unauthorized to delete this reschedule.');
    }

    if ($user->hasRole('Consultant') && !$user->can('course-delete')) {
        if ($reschedule->course->consultant_id !== $user->id) {
            abort(403, 'Unauthorized to delete this specific reschedule.');
        }
    }

    try {
        $reschedule->delete();

        return redirect()->route('course_reschedules.index')
            ->with('success', 'Course reschedule deleted successfully.');

    } catch (\Exception $e) {
        Log::error("Error deleting reschedule {$id}: " . $e->getMessage());
        return back()
            ->with('error', 'An error occurred: ' . $e->getMessage());
    }
}
}