<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\CourseReschedule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class NotificationBannerController extends Controller
{
    /**
     * Get recent notifications for the banner
     */
    public function getRecentNotifications()
    {
        $user = Auth::user();
        $notifications = collect();

        // 1️⃣ Nouveaux cours créés AUJOURD'HUI uniquement
        $newCourses = $this->getTodayNewCourses($user);
        
        // 2️⃣ Cours reportés (tous)
        $rescheduledCourses = $this->getRescheduledCourses($user);

        // Fusionner toutes les notifications
        $notifications = $notifications
            ->merge($newCourses)
            ->merge($rescheduledCourses)
            ->sortByDesc('created_at')
            ->take(10); // Max 10 notifications

        return response()->json([
            'success' => true,
            'notifications' => $notifications->values()
        ]);
    }

    /**
     * Récupérer les nouveaux cours créés AUJOURD'HUI seulement
     */
    private function getTodayNewCourses($user)
    {
        $today = Carbon::today();
        
        $query = Course::with(['module', 'formation', 'consultant'])
            ->whereDate('course_date', $today); // 🔥 Juste aujourd'hui

        // Filtrer selon le rôle
        if ($user->hasRole('Etudiant')) {
            $enrolledFormationIds = $user->inscriptions()
                ->whereIn('status', ['active', 'completed'])
                ->where('access_restricted', false)
                ->pluck('formation_id');
            
            $query->whereIn('formation_id', $enrolledFormationIds);
        } elseif ($user->hasRole('Consultant')) {
            $query->where('consultant_id', $user->id);
        }

        // 🔥 Appliquer le filtre unique pour éviter les duplicates
        $allCourses = $query->get();
        
        $uniqueCourses = $allCourses->unique(function($course) {
            return $course->module_id . '-' . 
                   $course->course_date . '-' . 
                   $course->start_time . '-' . 
                   $course->title;
        });

        return $uniqueCourses->map(function ($course) {
            return [
                'id' => $course->id,
                'type' => 'new_course',
                'icon' => '📚',
                'color' => 'bg-green-100 text-green-800',
                'message' => "Nouveau cours ajouté: {$course->title} - " . 
                            Carbon::parse($course->course_date)->format('d/m/Y') . 
                            " à {$course->start_time}",
                'link' => route('courses.show', $course->id),
                'created_at' => $course->created_at
            ];
        });
    }

    /**
     * Récupérer TOUS les cours reportés (sans limite de date)
     */
    private function getRescheduledCourses($user)
    {
        $query = CourseReschedule::with(['course.module', 'course.formation'])
            ->orderBy('created_at', 'desc'); // Les plus récents en premier

        // Filtrer selon le rôle
        if ($user->hasRole('Etudiant')) {
            $enrolledFormationIds = $user->inscriptions()
                ->where('status', 'active')
                ->pluck('formation_id');
            
            $query->whereHas('course', function ($q) use ($enrolledFormationIds) {
                $q->whereIn('formation_id', $enrolledFormationIds);
            });
        } elseif ($user->hasRole('Consultant')) {
            $query->whereHas('course', function ($q) use ($user) {
                $q->where('consultant_id', $user->id);
            });
        }

        return $query->get()->map(function ($reschedule) {
            $course = $reschedule->course;
            
            // Format des dates
            $originalDate = Carbon::parse($reschedule->original_date)->format('d/m/Y');
            $newDate = Carbon::parse($reschedule->new_date)->format('d/m/Y');
            
            return [
                'id' => $reschedule->id,
                'type' => 'rescheduled',
                'icon' => '🔄',
                'color' => 'bg-orange-100 text-orange-800',
                'message' => "Cours reporté: {$course->title} - " .
                            "Du {$originalDate} reporté au {$newDate}",
                'link' => route('courses.show', $course->id),
                'created_at' => $reschedule->created_at
            ];
        });
    }
}