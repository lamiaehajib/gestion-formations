<?php
namespace App\Console\Commands;
use App\Models\Course;
use App\Models\User;
use App\Mail\NewCourseNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendCourseNotifications extends Command
{
    protected $signature = 'app:send-course-notifications';
    protected $description = 'Sends email notifications for courses scheduled for today.';

    public function handle()
    {
        $now = Carbon::now();
        $fiveMinutesFromNow = $now->copy()->addMinutes(5);

        $courses = Course::whereDate('course_date', $now->toDateString())
                         ->where('notification_sent', false)
                         ->whereRaw("TIME_TO_SEC(start_time) BETWEEN TIME_TO_SEC(?) AND TIME_TO_SEC(?)", [
                             $now->toTimeString(),
                             $fiveMinutesFromNow->toTimeString()
                         ])
                         ->get();

        if ($courses->isEmpty()) {
            $this->info("Aucun cours à notifier dans les 5 prochaines minutes.");
            return;
        }

        foreach ($courses as $course) {
            // 1. Envoyer l'email au consultant
            if ($course->consultant_id) {
                $consultant = User::find($course->consultant_id);
                if ($consultant) {
                    Mail::to($consultant->email)->send(new NewCourseNotification($course));
                }
            }

            // 2. Envoyer l'email aux étudiants
            $students = User::whereHas('inscriptions', function ($query) use ($course) {
                $query->where('formation_id', $course->formation_id)
                      ->where('status', 'active')
                      ->where('access_restricted', false);
            })->get();

            foreach ($students as $student) {
                Mail::to($student->email)->send(new NewCourseNotification($course));
            }

            // 3. Kan markiw l'cours belli t-seft l'email
            $course->update(['notification_sent' => true]);
            $this->info("Emails de notification envoyés pour le cours: {$course->title}");
        }
        $this->info("Tâche de notification terminée.");
    }
}