<?php

namespace App\Http\Controllers;

use App\Models\Module;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Formation;
use App\Models\Inscription;
use App\Models\Payment;
use App\Models\Reclamation;
use App\Models\Course;
use App\Models\CourseReschedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

use App\Models\Notification;
class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $data = [];

        if ($user->hasRole('Admin') || $user->hasRole('Super Admin')) {
            $data = $this->getAdminDashboardData($request);
            return view('dashboard.admin', $data);
        }
        elseif ($user->hasRole('Consultant')) {
            $data = $this->getConsultantDashboardData($user, $request);
            return view('dashboard.consultant', $data);
        }
        elseif ($user->hasRole('Etudiant')) {
            $data = $this->getEtudiantDashboardData($user, $request);
            return view('dashboard.etudiant', $data);
        }
        elseif ($user->hasRole('Finance')) {
            $data = $this->getFinanceDashboardData($request);
            return view('dashboard.finance', $data);
        }

        return view('dashboard.default', $data);
    }

      private function getAdminDashboardData(Request $request): array
{
    // Get the selected month from the request, default to null
    $selectedMonth = $request->input('selected_month');
    $selectedYear = $request->input('selected_year', Carbon::now()->year);

    $startDate = null;
    $endDate = null;
    $applyDateFilter = false; // NEW: Flag to control date filtering

    if ($selectedMonth) {
        // If a month is selected, set date range for that month
        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfDay();
        $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->endOfDay();
        $applyDateFilter = true;
    }
    // REMOVED: The fallback else block that was applying 30-day filter

    // Prepare the list of months for the dropdown
    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $months[$i] = Carbon::create()->month($i)->translatedFormat('F');
    }

    // --- Apply date filters conditionally ---
    $totalUsersQuery = User::query();
    if ($applyDateFilter && $startDate && $endDate) {
        $totalUsersQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    $totalUsers = $totalUsersQuery->count();

    // New registrations (filtered if month selected)
    $newRegistrationsQuery = User::query();
    if ($applyDateFilter && $startDate && $endDate) {
        $newRegistrationsQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    $newRegistrationsLast30Days = $newRegistrationsQuery->count();

    // Total formations
    $totalFormationsQuery = Formation::query();
    if ($applyDateFilter && $startDate && $endDate) {
        $totalFormationsQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    $totalFormations = $totalFormationsQuery->count();

    // Total inscriptions
    $totalInscriptionsQuery = Inscription::query();
    if ($applyDateFilter && $startDate && $endDate) {
        $totalInscriptionsQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    $totalInscriptions = $totalInscriptionsQuery->count();

    // Open reclamations
    $openReclamationsQuery = Reclamation::where('status', 'ouverte');
    if ($applyDateFilter && $startDate && $endDate) {
        $openReclamationsQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    $openReclamations = $openReclamationsQuery->count();

    // Pending payments
    $pendingPaymentsQuery = Payment::where('status', 'pending');
    if ($applyDateFilter && $startDate && $endDate) {
        $pendingPaymentsQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    $pendingPaymentsCount = $pendingPaymentsQuery->count();

    // Notifications
    $notifications = Notification::where('user_id', Auth::id())
        ->orderBy('created_at', 'desc')
        ->take(10)
        ->get();

    $unreadNotificationsCount = Notification::where('user_id', Auth::id())
        ->where('is_read', false)
        ->count();

    // Users by role
    $usersByRoleQuery = User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
        ->select('roles.name as role', DB::raw('count(*) as count'));
    if ($applyDateFilter && $startDate && $endDate) {
        $usersByRoleQuery->whereBetween('users.created_at', [$startDate, $endDate]);
    }
    $usersByRole = $usersByRoleQuery->groupBy('roles.name')->get()->pluck('count', 'role');

    // Users by status
    $usersByStatusQuery = User::select('status', DB::raw('count(*) as count'));
    if ($applyDateFilter && $startDate && $endDate) {
        $usersByStatusQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    $usersByStatus = $usersByStatusQuery->groupBy('status')->get()->pluck('count', 'status');

    // Formations by status
    $formationsByStatusQuery = Formation::select('status', DB::raw('count(*) as count'));
    if ($applyDateFilter && $startDate && $endDate) {
        $formationsByStatusQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    $formationsByStatus = $formationsByStatusQuery->groupBy('status')->get()->pluck('count', 'status');

    // Inscriptions by status
    $inscriptionsByStatusQuery = Inscription::select('status', DB::raw('count(*) as count'));
    if ($applyDateFilter && $startDate && $endDate) {
        $inscriptionsByStatusQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    $inscriptionsByStatus = $inscriptionsByStatusQuery->groupBy('status')->get()->pluck('count', 'status');

    // Total revenue
    $totalRevenueQuery = Payment::where('status', 'paid');
    if ($applyDateFilter && $startDate && $endDate) {
        $totalRevenueQuery->whereBetween('paid_date', [$startDate, $endDate]);
    }
    $totalRevenue = $totalRevenueQuery->sum('amount');

    // Outstanding amount
    $outstandingAmountQuery = Payment::whereIn('status', ['pending', 'late']);
    if ($applyDateFilter && $startDate && $endDate) {
        $outstandingAmountQuery->whereBetween('due_date', [$startDate, $endDate]);
    }
    $outstandingAmount = $outstandingAmountQuery->sum('amount');

    // Payments by method
    $paymentsByMethodQuery = Payment::select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
        ->groupBy('payment_method');
    if ($applyDateFilter && $startDate && $endDate) {
        $paymentsByMethodQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    $paymentsByMethod = $paymentsByMethodQuery->get();

    // Monthly revenue trend
    $monthlyRevenueTrendQuery = Payment::where('status', 'paid')
        ->select(DB::raw('DATE_FORMAT(paid_date, "%Y-%m") as month'), DB::raw('SUM(amount) as total_amount'))
        ->groupBy('month')
        ->orderBy('month');
    if ($applyDateFilter && $startDate && $endDate) {
        $monthlyRevenueTrendQuery->whereBetween('paid_date', [$startDate, $endDate]);
    }
    $monthlyRevenueTrend = $monthlyRevenueTrendQuery->get();

    // Overdue payments
    $overduePaymentsQuery = Payment::with(['inscription.user', 'inscription.formation'])
        ->where('status', 'pending')
        ->where('due_date', '<', Carbon::now());
    if ($applyDateFilter && $startDate && $endDate) {
        $overduePaymentsQuery->whereBetween('due_date', [$startDate, $endDate]);
    }
    $overduePaymentsList = $overduePaymentsQuery->orderBy('due_date', 'asc')->take(10)->get();

    // Top formations by enrollment
    $topFormationsByEnrollment = Formation::withCount(['inscriptions' => function($q) use ($applyDateFilter, $startDate, $endDate) {
        $q->whereIn('status', ['active', 'completed']);
        if ($applyDateFilter && $startDate && $endDate) {
            $q->whereBetween('created_at', [$startDate, $endDate]);
        }
    }])
    ->orderByDesc('inscriptions_count')
    ->take(10)
    ->get();

    // Upcoming formations (no date filter - always show upcoming)
    $upcomingFormations = Formation::where('start_date', '>=', Carbon::now())
        ->orderBy('start_date', 'asc')
        ->take(10)
        ->get();

    // Reclamations by status
    $reclamationsByStatusQuery = Reclamation::select('status', DB::raw('count(*) as count'));
    if ($applyDateFilter && $startDate && $endDate) {
        $reclamationsByStatusQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    $reclamationsByStatus = $reclamationsByStatusQuery->groupBy('status')->get()->pluck('count', 'status');

    // Recent reclamations
    $recentReclamationsQuery = Reclamation::with('user');
    if ($applyDateFilter && $startDate && $endDate) {
        $recentReclamationsQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    $recentReclamations = $recentReclamationsQuery->orderBy('created_at', 'desc')->take(5)->get();

    // Completion rate
    $completedInscriptionsQuery = Inscription::where('status', 'completed');
    $totalInscriptionsForRateQuery = Inscription::query();
    
    if ($applyDateFilter && $startDate && $endDate) {
        $completedInscriptionsQuery->whereBetween('created_at', [$startDate, $endDate]);
        $totalInscriptionsForRateQuery->whereBetween('created_at', [$startDate, $endDate]);
    }
    
    $completedCount = $completedInscriptionsQuery->count();
    $totalCount = $totalInscriptionsForRateQuery->count();
    $completionRate = $totalCount > 0 ? ($completedCount / $totalCount) * 100 : 0;

    return [
        'totalUsers' => $totalUsers,
        'usersByRole' => $usersByRole,
        'usersByStatus' => $usersByStatus,
        'totalFormations' => $totalFormations,
        'formationsByStatus' => $formationsByStatus,
        'totalInscriptions' => $totalInscriptions,
        'inscriptionsByStatus' => $inscriptionsByStatus,
        'openReclamations' => $openReclamations,
        'pendingPaymentsCount' => $pendingPaymentsCount,
        'totalRevenue' => $totalRevenue,
        'outstandingAmount' => $outstandingAmount,
        'paymentsByMethod' => $paymentsByMethod,
        'monthlyRevenueTrend' => $monthlyRevenueTrend,
        'overduePaymentsList' => $overduePaymentsList,
        'topFormationsByEnrollment' => $topFormationsByEnrollment,
        'upcomingFormations' => $upcomingFormations,
        'newRegistrationsLast30Days' => $newRegistrationsLast30Days,
        'reclamationsByStatus' => $reclamationsByStatus,
        'recentReclamations' => $recentReclamations,
        'completionRate' => $completionRate,
        'startDate' => $startDate ? $startDate->toDateString() : null,
        'endDate' => $endDate ? $endDate->toDateString() : null,
        'months' => $months,
        'selectedMonth' => $selectedMonth,
        'selectedYear' => $selectedYear,
        'notifications' => $notifications,
        'unreadNotificationsCount' => $unreadNotificationsCount,
    ];
}

    // ... (all the other code in your controller remains the same)

    
private function getConsultantDashboardData(User $user, Request $request)
{
    // Date filtering logic
    $selectedMonth = $request->input('selected_month');
    $selectedYear = $request->input('selected_year', Carbon::now()->year);

    $startDate = null;
    $endDate = null;

    if ($selectedMonth) {
        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfDay();
        $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->endOfDay();
    } else {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfDay();
    }

    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $months[$i] = Carbon::create()->month($i)->translatedFormat('F');
    }

    // 🔥 JDID: Kanjibu ga3 les courses dyal consultant (bila pagination)
    $allCoursesToday = Course::where('consultant_id', $user->id)
        ->whereDate('course_date', Carbon::today())
        ->orderBy('start_time', 'asc')
        ->with('module')
        ->get();

    // 🔥 Filtration des doublons (nfes l'logique kif f index)
    $coursesToday = $allCoursesToday->unique(function($course) {
        return $course->module_id . '-' . 
               $course->course_date . '-' . 
               $course->start_time . '-' . 
               $course->title;
    })->values(); // values() bach n-reset les keys

    // 🔥 Nfes l'ḥaja l upcoming courses
    $allUpcomingCourses = Course::where('consultant_id', $user->id)
        ->where('course_date', '>', Carbon::today())
        ->orderBy('course_date', 'asc')
        ->orderBy('start_time', 'asc')
        ->with('module')
        ->get();

    $upcomingCourses = $allUpcomingCourses->unique(function($course) {
        return $course->module_id . '-' . 
               $course->course_date . '-' . 
               $course->start_time . '-' . 
               $course->title;
    })->values();

    // 🔥 Total courses (après filtration)
    $allCourses = Course::where('consultant_id', $user->id)
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->with('module')
        ->get();

    $uniqueCourses = $allCourses->unique(function($course) {
        return $course->module_id . '-' . 
               $course->course_date . '-' . 
               $course->start_time . '-' . 
               $course->title;
    });

    $totalCourses = $uniqueCourses->count();

    // Recent reschedules (ma khasshomch filtration)
    $recentReschedules = CourseReschedule::whereHas('course', function ($q) use ($user) {
        $q->where('consultant_id', $user->id);
    })
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->with('course.module')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

    // Modules data (unchanged)
    $consultantModules = Module::where('user_id', $user->id)
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->get();

    $totalModules = $consultantModules->count();
    $totalCompletedModules = $consultantModules->where('progress', 100)->count();
    $totalInProgressModules = $consultantModules->whereBetween('progress', [1, 99])->count();
    $totalNotStartedModules = $consultantModules->where('progress', 0)->count();
    $overallAverageProgress = $totalModules > 0 ? round($consultantModules->avg('progress'), 2) : 0;

    $globalModulesChart = [
        'labels' => $consultantModules->pluck('title')->toArray(),
        'data' => $consultantModules->pluck('progress')->toArray(),
        'backgroundColor' => $consultantModules->map(function ($module) {
            if ($module->progress >= 80) return '#28a745';
            elseif ($module->progress >= 60) return '#17a2b8';
            elseif ($module->progress >= 40) return '#ffc107';
            elseif ($module->progress >= 20) return '#fd7e14';
            else return '#dc3545';
        })->toArray()
    ];

    $moduleProgressStats = [
        'completed' => [
            'count' => $totalCompletedModules, 
            'percentage' => $totalModules > 0 ? round(($totalCompletedModules / $totalModules) * 100, 1) : 0
        ],
        'in_progress' => [
            'count' => $totalInProgressModules, 
            'percentage' => $totalModules > 0 ? round(($totalInProgressModules / $totalModules) * 100, 1) : 0
        ],
        'not_started' => [
            'count' => $totalNotStartedModules, 
            'percentage' => $totalModules > 0 ? round(($totalNotStartedModules / $totalModules) * 100, 1) : 0
        ]
    ];

    $recentModuleUpdates = Module::where('user_id', $user->id)
        ->whereHas('courses', function ($q) use ($user) {
            $q->where('consultant_id', $user->id)
              ->where('created_at', '>=', Carbon::now()->subDays(7));
        })
        ->with([
            'courses' => function ($q) use ($user) {
                $q->where('consultant_id', $user->id)
                  ->where('created_at', '>=', Carbon::now()->subDays(7))
                  ->orderBy('created_at', 'desc');
            }
        ])
        ->orderBy('updated_at', 'desc')
        ->take(5)
        ->get();

    // 🔥 Courses trend (avec unique courses)
    $allCoursesForTrend = Course::where('consultant_id', $user->id)
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->with('module')
        ->get();

    $uniqueCoursesForTrend = $allCoursesForTrend->unique(function($course) {
        return $course->module_id . '-' . 
               $course->course_date . '-' . 
               $course->start_time . '-' . 
               $course->title;
    });

    // Group by month
    $coursesTrend = $uniqueCoursesForTrend->groupBy(function($course) {
        return Carbon::parse($course->created_at)->format('Y-m');
    })->map(function($courses, $month) {
        return [
            'month' => $month,
            'total_courses' => $courses->count()
        ];
    })->values();

    return [
        'consultant' => $user,
        'coursesToday' => $coursesToday,
        'upcomingCourses' => $upcomingCourses,
        'totalCourses' => $totalCourses,
        'recentReschedules' => $recentReschedules,
        'coursesTrend' => $coursesTrend,
        'months' => $months,
        'selectedMonth' => $selectedMonth,
        'selectedYear' => $selectedYear,
        'startDate' => $startDate ? $startDate->toDateString() : null,
        'endDate' => $endDate ? $endDate->toDateString() : null,
        'consultantModules' => $consultantModules,
        'totalModules' => $totalModules,
        'totalCompletedModules' => $totalCompletedModules,
        'totalInProgressModules' => $totalInProgressModules,
        'totalNotStartedModules' => $totalNotStartedModules,
        'overallAverageProgress' => $overallAverageProgress,
        'globalModulesChart' => $globalModulesChart,
        'moduleProgressStats' => $moduleProgressStats,
        'recentModuleUpdates' => $recentModuleUpdates,
    ];
}

// ... (all the other code in your controller remains the same)

    private function getEtudiantDashboardData(User $user, Request $request): array
{
    $selectedMonth = $request->input('selected_month');
    $selectedYear = $request->input('selected_year', Carbon::now()->year);

    $startDate = null;
    $endDate = null;

    if ($selectedMonth) {
        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfDay();
        $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->endOfDay();
    }

    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $months[$i] = Carbon::create()->month($i)->translatedFormat('F');
    }

    $inscriptions = Inscription::where('user_id', $user->id)
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->with('formation')
        ->get();

    $currentInscriptions = $inscriptions->where('status', 'active');
    $pendingInscriptions = $inscriptions->where('status', 'pending');
     $completedInscriptions = $inscriptions->where('status', 'completed');

    $totalPaid = Payment::whereHas('inscription', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('status', 'paid')
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('paid_date', [$startDate, $endDate]);
        })->sum('amount');

    $totalOutstanding = Payment::whereHas('inscription', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->whereIn('status', ['pending', 'late'])
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('due_date', [$startDate, $endDate]);
        })->sum('amount');

    $upcomingPayments = Payment::whereHas('inscription', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->where('status', 'pending')
        ->where('due_date', '>=', Carbon::now())
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('due_date', [$startDate, $endDate]);
        })
        ->with('inscription.formation')
        ->orderBy('due_date', 'asc')
        ->get();

    $recentPayments = Payment::whereHas('inscription', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->with('inscription.formation')
        ->get();

    // MISE À JOUR IMPORTANTE POUR LES COURS DU JOUR
    $enrolledFormationIds = $user->inscriptions()
        ->where('status', 'active')
        ->where('access_restricted', false)
        ->pluck('formation_id');

    $today = Carbon::today();
    $coursesToday = Course::whereDate('course_date', $today)
        ->orderBy('start_time', 'asc')
        // Nta daba katsta3mel l'relationship l's7i7a (formation)
        ->whereHas('formation', function ($q) use ($enrolledFormationIds) { 
            $q->whereIn('formations.id', $enrolledFormationIds);
        })
        ->with(['consultant', 'formation' => function ($q) use ($enrolledFormationIds) { // Hna tbedlat 'formations' l 'formation'
            $q->whereIn('formations.id', $enrolledFormationIds);
        }])
        ->get();

    // Zid t3dil l'nafs l'mouchkil f had l'partie dyal CourseReschedule
    $recentCourseReschedules = CourseReschedule::whereHas('course.formation', function ($q) use ($enrolledFormationIds) { // CHANGE: 'course.formations' -> 'course.formation'
            $q->whereIn('formations.id', $enrolledFormationIds);
        })
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->with('course')
        ->get();

    // 🔥 HAD HIYA L'PARTIE L'JDIDA: Jib les formations m3a modules o progress dyalhom
    $formationsWithModulesProgress = Formation::whereIn('id', $enrolledFormationIds)
        ->with(['modules' => function($query) {
            $query->orderBy('order', 'asc');
        }])
        ->get()
        ->map(function($formation) {
            // Kan-calculate l'overall progress dyal l'formation
            $totalModules = $formation->modules->count();
            $totalProgress = $formation->modules->sum('progress');
            $formation->overall_progress = $totalModules > 0 ? round($totalProgress / $totalModules, 2) : 0;
            
            // Kan-prepare data dyal modules for charts
            $formation->modules_chart_data = [
                'labels' => $formation->modules->pluck('title')->toArray(),
                'data' => $formation->modules->pluck('progress')->toArray(),
                'backgroundColor' => $formation->modules->map(function($module) {
                    // Kan-choose color based 3la progress
                    if ($module->progress >= 80) return '#28a745'; // Green - Excellent
                    elseif ($module->progress >= 60) return '#17a2b8'; // Blue - Good  
                    elseif ($module->progress >= 40) return '#ffc107'; // Yellow - Average
                    elseif ($module->progress >= 20) return '#fd7e14'; // Orange - Low
                    else return '#dc3545'; // Red - Very Low
                })->toArray(),
                'borderColor' => '#fff',
                'borderWidth' => 2
            ];
            
            return $formation;
        });

    // 🔥 Kan-prepare global modules progress chart (ga3 les modules dyal ga3 les formations)
    $allModules = $formationsWithModulesProgress->flatMap(function($formation) {
    return $formation->modules->map(function($module) use ($formation) {
        $module->formation_title = $formation->title;
        return $module;
    });
});

   $globalModulesChart = [
    'labels' => $allModules->pluck('title')->toArray(), // 🔥 HADI TBEDLET: ghir title dyal module
    'data' => $allModules->pluck('progress')->toArray(),
    'backgroundColor' => $allModules->map(function($module) {
        if ($module->progress >= 80) return '#28a745';
        elseif ($module->progress >= 60) return '#17a2b8';
        elseif ($module->progress >= 40) return '#ffc107';
        elseif ($module->progress >= 20) return '#fd7e14';
        else return '#dc3545';
    })->toArray(),
];

    // Données pour les graphiques (existing code)
    $paymentStatusDistribution = Payment::whereHas('inscription', function($q) use ($user) {
            $q->where('user_id', $user->id);
        })
        ->select('status', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
        ->groupBy('status')
        ->get();

    $paymentChartLabels = $paymentStatusDistribution->pluck('status')->map(fn($status) => ucfirst($status))->toArray();
    $paymentChartData = $paymentStatusDistribution->pluck('total')->toArray();
    $paymentChartColors = [
        'paid' => '#28a745', 
        'pending' => '#ffc107',
        'late' => '#dc3545',
    ];
    $paymentChartBackgroundColors = $paymentStatusDistribution->pluck('status')->map(fn($status) => $paymentChartColors[$status] ?? '#6c757d')->toArray();

    $inscriptionStatusDistribution = Inscription::where('user_id', $user->id)
        ->select('status', DB::raw('count(*) as count'))
        ->groupBy('status')
        ->get();

    $inscriptionChartLabels = $inscriptionStatusDistribution->pluck('status')->map(fn($status) => ucfirst($status))->toArray();
    $inscriptionChartData = $inscriptionStatusDistribution->pluck('count')->toArray();
    $inscriptionChartColors = [
        'active' => '#17a2b8',
        'pending' => '#ffc107',
        'completed' => '#28a745',
        'cancelled' => '#6c757d',
    ];
    $inscriptionChartBackgroundColors = $inscriptionStatusDistribution->pluck('status')->map(fn($status) => $inscriptionChartColors[$status] ?? '#007bff')->toArray();

    $progressByFormation = $formationsWithModulesProgress;

    return [
        'activeInscriptions' => $currentInscriptions,
        'pendingInscriptions' => $pendingInscriptions,
        'completedInscriptions' => $completedInscriptions,
        'totalPaid' => $totalPaid,
        'totalOutstanding' => $totalOutstanding,
        'inscriptions' => $inscriptions,
        'completedFormations' => Inscription::where('user_id', $user->id)
            ->where('status', 'completed')
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('updated_at', [$startDate, $endDate]);
            })->count(),
        'myReclamations' => Reclamation::where('user_id', $user->id)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get(),
        'progressByFormation' => $progressByFormation,
        'months' => $months,
        'selectedMonth' => $selectedMonth,
        'selectedYear' => $selectedYear,
        'upcomingPayments' => $upcomingPayments,
        'recentPayments' => $recentPayments,
        'coursesToday' => $coursesToday,
        'recentCourseReschedules' => $recentCourseReschedules,
        'paymentChartLabels' => $paymentChartLabels,
        'paymentChartData' => $paymentChartData,
        'paymentChartBackgroundColors' => $paymentChartBackgroundColors,
        'inscriptionChartLabels' => $inscriptionChartLabels,
        'inscriptionChartData' => $inscriptionChartData,
        'inscriptionChartBackgroundColors' => $inscriptionChartBackgroundColors,
        
        // 🔥 NEW DATA FOR MODULES PROGRESS
        'formationsWithModulesProgress' => $formationsWithModulesProgress,
        'globalModulesChart' => $globalModulesChart,
        'totalModulesCount' => $allModules->count(),
        'averageModuleProgress' => $allModules->count() > 0 ? round($allModules->avg('progress'), 2) : 0,
        'completedModulesCount' => $allModules->where('progress', 100)->count(),
        'inProgressModulesCount' => $allModules->whereBetween('progress', [1, 99])->count(),
        'notStartedModulesCount' => $allModules->where('progress', 0)->count(),
    ];
}
   private function getFinanceDashboardData(Request $request): array
{
    $selectedMonth = $request->input('selected_month');
    $selectedYear = $request->input('selected_year', Carbon::now()->year);

    $startDate = null;
    $endDate = null;

    if ($selectedMonth) {
        $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfDay();
        $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->endOfDay();
    } else {
        $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30)->startOfDay();
        $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfDay();
    }

    $months = [];
    for ($i = 1; $i <= 12; $i++) {
        $months[$i] = Carbon::create()->month($i)->translatedFormat('F');
    }

    // New queries for Finance Dashboard
    $overdueInscriptions = Inscription::where('access_restricted', true)
        ->whereNotNull('next_installment_due_date')
        ->where('next_installment_due_date', '<', Carbon::now())
        ->with('user', 'formation')
        ->get();

    $upcomingPaymentsDue = Inscription::where('access_restricted', false)
        ->whereNotNull('next_installment_due_date')
        ->where('next_installment_due_date', '>=', Carbon::now()->startOfDay())
        ->where('next_installment_due_date', '<=', Carbon::now()->addDays(7)->endOfDay())
        ->with('user', 'formation')
        ->get();
    
    return [
        'totalRevenue' => Payment::where('status', 'paid')
                               ->whereBetween('paid_date', [$startDate, $endDate])
                               ->sum('amount'),
        'pendingPayments' => Payment::where('status', 'pending')
                                   ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                                       $query->whereBetween('created_at', [$startDate, $endDate]);
                                   })->sum('amount'),
        'overduePayments' => Payment::where('status', 'pending')
                                   ->where('due_date', '<', Carbon::now())
                                   ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                                       $query->whereBetween('due_date', [$startDate, $endDate]);
                                   })->sum('amount'),
        'paidToday' => Payment::where('status', 'paid')
                             ->whereDate('paid_date', Carbon::today())
                             ->sum('amount'),
        'paymentsByMethod' => Payment::whereBetween('created_at', [$startDate, $endDate])
                                   ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
                                   ->groupBy('payment_method')
                                   ->get(),
        'monthlyRevenueTrend' => Payment::where('status', 'paid')
                                        ->whereBetween('paid_date', [$startDate, $endDate])
                                        ->select(DB::raw('DATE_FORMAT(paid_date, "%Y-%m") as month'), DB::raw('SUM(amount) as total_amount'))
                                        ->groupBy('month')
                                        ->orderBy('month')
                                        ->get(),
        'overduePaymentsList' => Payment::with(['inscription.user', 'inscription.formation'])
                                        ->where('status', 'pending')
                                        ->where('due_date', '<', Carbon::now())
                                        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                                            $query->whereBetween('due_date', [$startDate, $endDate]);
                                        })
                                        ->orderBy('due_date', 'asc')
                                        ->get(),
        'recentPayments' => Payment::with(['inscription.user', 'inscription.formation'])
                                   ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                                       $query->whereBetween('created_at', [$startDate, $endDate]);
                                   })
                                   ->orderBy('created_at', 'desc')
                                   ->take(10)
                                   ->get(),
        'paymentStatusDistribution' => Payment::select('status', DB::raw('count(*) as count'))
                                             ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                                                 $query->whereBetween('created_at', [$startDate, $endDate]);
                                             })
                                             ->groupBy('status')
                                             ->get()
                                             ->pluck('count', 'status'),
        'averagePaymentAmount' => Payment::where('status', 'paid')
                                         ->whereBetween('paid_date', [$startDate, $endDate])
                                         ->avg('amount'),
        'startDate' => $startDate ? $startDate->toDateString() : null,
        'endDate' => $endDate ? $endDate->toDateString() : null,
        'months' => $months,
        'selectedMonth' => $selectedMonth,
        'selectedYear' => $selectedYear,
        'overdueInscriptions' => $overdueInscriptions, // New data
        'upcomingPaymentsDue' => $upcomingPaymentsDue, // New data
    ];
}
}