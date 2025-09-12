<?php

namespace App\Http\Controllers;

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
        $selectedYear = $request->input('selected_year', Carbon::now()->year); // Default to current year

        $startDate = null;
        $endDate = null;

        if ($selectedMonth) {
            // If a month is selected, set startDate to the first day of that month/year
            // and endDate to the last day of that month/year.
            $startDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->startOfDay();
            $endDate = Carbon::createFromDate($selectedYear, $selectedMonth, 1)->endOfMonth()->endOfDay();
        } else {
            // Fallback to default date range if no month is selected
            $startDate = $request->input('start_date') ? Carbon::parse($request->input('start_date')) : Carbon::now()->subDays(30)->startOfDay();
            $endDate = $request->input('end_date') ? Carbon::parse($request->input('end_date')) : Carbon::now()->endOfDay();
        }

        // Prepare the list of months for the dropdown
        $months = [];
        for ($i = 1; $i <= 12; $i++) {
            $months[$i] = Carbon::create()->month($i)->translatedFormat('F'); // Full month name in current locale
        }

        // --- Apply date filters to your queries ---
        $totalUsersQuery = User::query();
        if ($startDate && $endDate) {
            $totalUsersQuery->whereBetween('created_at', [$startDate, $endDate]);
        }
        $totalUsers = $totalUsersQuery->count();

        $newRegistrationsLast30Days = User::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalFormations = Formation::whereBetween('created_at', [$startDate, $endDate])->count();
        $totalInscriptions = Inscription::whereBetween('created_at', [$startDate, $endDate])->count();
        $openReclamations = Reclamation::where('status', 'ouverte')->whereBetween('created_at', [$startDate, $endDate])->count();
        $pendingPaymentsCount = Payment::where('status', 'pending')->whereBetween('created_at', [$startDate, $endDate])->count();

        // NEW: Récupérer les notifications de l'utilisateur admin
        $notifications = Notification::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->take(10) // On affiche les 10 dernières notifications
            ->get();

        // NEW: Compter le nombre de notifications non lues
        $unreadNotificationsCount = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return [
            'totalUsers' => $totalUsers,
            'usersByRole' => User::join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                ->select('roles.name as role', DB::raw('count(*) as count'))
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('users.created_at', [$startDate, $endDate]);
                })
                ->groupBy('roles.name')
                ->get()
                ->pluck('count', 'role'),
            'usersByStatus' => User::select('status', DB::raw('count(*) as count'))
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status'),
            'totalFormations' => $totalFormations, // Already filtered above
            'formationsByStatus' => Formation::select('status', DB::raw('count(*) as count'))
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status'),
            'totalInscriptions' => $totalInscriptions, // Already filtered above
            'inscriptionsByStatus' => Inscription::select('status', DB::raw('count(*) as count'))
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status'),
            'openReclamations' => $openReclamations, // Already filtered above
            'pendingPaymentsCount' => $pendingPaymentsCount, // Already filtered above
            'totalRevenue' => Payment::where('status', 'paid')
                ->whereBetween('paid_date', [$startDate, $endDate])
                ->sum('amount'),
            'outstandingAmount' => Payment::whereIn('status', ['pending', 'late'])
                ->whereBetween('due_date', [$startDate, $endDate])
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
                    // If filtering by month, only show overdue payments for that month's due dates
                    $query->whereBetween('due_date', [$startDate, $endDate]);
                })
                ->orderBy('due_date', 'asc')
                ->take(5)
                ->get(),
            'topFormationsByEnrollment' => Formation::withCount(['inscriptions' => function($q) use ($startDate, $endDate) {
                    $q->whereIn('status', ['active', 'completed']);
                    if ($startDate && $endDate) {
                        $q->whereBetween('created_at', [$startDate, $endDate]);
                    }
                }])
                ->orderByDesc('inscriptions_count')
                ->take(5)
                ->get(),
            'upcomingFormations' => Formation::where('start_date', '>=', Carbon::now())
    // Remove the `when` block that filters by $startDate and $endDate
    ->orderBy('start_date', 'asc')
    ->take(5)
    ->get(),
            'newRegistrationsLast30Days' => $newRegistrationsLast30Days, // Already filtered above
            'reclamationsByStatus' => Reclamation::select('status', DB::raw('count(*) as count'))
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->groupBy('status')
                ->get()
                ->pluck('count', 'status'),
            'recentReclamations' => Reclamation::with('user')
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })
                ->orderBy('created_at', 'desc')
                ->take(5)
                ->get(),

            'completionRate' => Inscription::where('status', 'completed')
                ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })->count() / (Inscription::when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                    $query->whereBetween('created_at', [$startDate, $endDate]);
                })->count() ?: 1) * 100, // Avoid division by zero
            'startDate' => $startDate ? $startDate->toDateString() : null,
            'endDate' => $endDate ? $endDate->toDateString() : null,
            'months' => $months, // Pass months to the view
            'selectedMonth' => $selectedMonth, // Pass selected month back to the view
            'selectedYear' => $selectedYear, // Pass selected year back to the view
            
            // NEW: Add notification data to the returned array
            'notifications' => $notifications,
            'unreadNotificationsCount' => $unreadNotificationsCount,
        ];
    }

    // ... (all the other code in your controller remains the same)

    private function getConsultantDashboardData(User $user, Request $request): array
    {
        // Date filtering logic, similar to other dashboards
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

        // Fetch courses for today
        $today = Carbon::today();
        $coursesToday = Course::where('consultant_id', $user->id)
            ->whereDate('course_date', $today)
            ->orderBy('start_time', 'asc')
            ->with('formation')
            ->get();

        // Fetch upcoming courses (in the future)
        $upcomingCourses = Course::where('consultant_id', $user->id)
        ->where('course_date', '>', $today)
        ->orderBy('course_date', 'asc')
        ->orderBy('start_time', 'asc')
        ->with('formation')
        ->get(); 

        // Fetch formations assigned to the consultant
        $myFormations = Formation::where('consultant_id', $user->id)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->get();

        // Count total number of students in the consultant's formations
        $totalStudents = Inscription::whereHas('formation', function($q) use ($user) {
            $q->where('consultant_id', $user->id);
        })
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->count();

        // Count total number of courses the consultant has
        $totalCourses = Course::where('consultant_id', $user->id)
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->count();

        // Get recent reschedules for the consultant's courses
        $recentReschedules = CourseReschedule::whereHas('course', function ($q) use ($user) {
            $q->where('consultant_id', $user->id);
        })
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->with('course')
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->get();

        // Get enrollments per formation for the consultant
        // CORRECTED LINE: Using 'title' instead of 'name'
        $enrollmentsByFormation = Inscription::with(['formation:id,title'])
            ->select('formation_id', DB::raw('count(*) as total_students'))
            ->whereHas('formation', function($q) use ($user) {
                $q->where('consultant_id', $user->id);
            })
            ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            })
            ->groupBy('formation_id')
            ->get();

        // Prepare data for a chart showing student enrollment over time for the consultant's formations
        $studentEnrollmentTrend = Inscription::whereHas('formation', function($q) use ($user) {
            $q->where('consultant_id', $user->id);
        })
        ->when($startDate && $endDate, function ($query) use ($startDate, $endDate) {
            $query->whereBetween('created_at', [$startDate, $endDate]);
        })
        ->select(DB::raw('DATE_FORMAT(created_at, "%Y-%m") as month'), DB::raw('count(*) as total_students'))
        ->groupBy('month')
        ->orderBy('month')
        ->get();

        return [
            'consultant' => $user,
            'coursesToday' => $coursesToday,
            'upcomingCourses' => $upcomingCourses,
            'myFormations' => $myFormations,
            'totalStudents' => $totalStudents,
            'totalCourses' => $totalCourses,
            'recentReschedules' => $recentReschedules,
            'enrollmentsByFormation' => $enrollmentsByFormation,
            'studentEnrollmentTrend' => $studentEnrollmentTrend,
            'months' => $months,
            'selectedMonth' => $selectedMonth,
            'selectedYear' => $selectedYear,
            'startDate' => $startDate ? $startDate->toDateString() : null,
            'endDate' => $endDate ? $endDate->toDateString() : null,
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
        ->whereHas('formations', function ($q) use ($enrolledFormationIds) {
            $q->whereIn('formations.id', $enrolledFormationIds);
        })
        ->with(['consultant', 'formations' => function ($q) use ($enrolledFormationIds) {
            $q->whereIn('formations.id', $enrolledFormationIds);
        }])
        ->get();

    // NOUVELLE: Récupérer les récentes reprogrammations de cours
    $recentCourseReschedules = CourseReschedule::whereHas('course.formations', function ($q) use ($enrolledFormationIds) {
            $q->whereIn('formations.id', $enrolledFormationIds);
        })
        ->orderBy('created_at', 'desc')
        ->take(5)
        ->with('course')
        ->get();

    // Données pour les graphiques
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

    $progressByFormation = Inscription::where('user_id', $user->id)
        ->whereIn('status', ['active', 'completed'])
        ->with(['formation.courses'])
        ->get()
        ->map(function($inscription) {
            $totalCourses = $inscription->formation->courses->count();
            $completedCourses = 0; // Requires logic to track completed courses
            $inscription->progress = $totalCourses > 0 ? ($completedCourses / $totalCourses) * 100 : 0;
            return $inscription;
        });

    return [
        'activeInscriptions' => $currentInscriptions,
        'pendingInscriptions' => $pendingInscriptions,
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