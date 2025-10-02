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
        $today = $now->toDateString();

        // ----------------------------------------------------
        // 1. تحديد الكورسات التي ستبدأ خلال الـ 30 دقيقة القادمة
        // ----------------------------------------------------

        // نحسب الوقت بعد 30 دقيقة من الآن
        $thirtyMinutesFromNow = $now->copy()->addMinutes(30);

        $courses = Course::whereDate('course_date', $today)
                         // نختار الكورسات التي ستبدأ خلال 30 دقيقة القادمة
                         ->whereRaw("TIME_TO_SEC(start_time) BETWEEN TIME_TO_SEC(?) AND TIME_TO_SEC(?)", [
                             $now->toTimeString(),
                             $thirtyMinutesFromNow->toTimeString()
                         ])
                         // شرط إضافي لتحسين الأداء: نختار فقط الكورسات التي لم ترسل لها الإشعارات بالكامل بعد
                         ->where('notification_count', '<', 2)
                         ->get();

        if ($courses->isEmpty()) {
            $this->info("Aucun cours à notifier dans les 30 prochaines minutes.");
            return;
        }

        foreach ($courses as $course) {
            // ✅ تم تصحيح المشكل هنا: نستخدم format('Y-m-d') و format('H:i:s') لتكوين سترينغ تاريخ صحيح.
            $startTime = Carbon::parse($course->course_date->format('Y-m-d') . ' ' . $course->start_time->format('H:i:s'));
            
            $minutesToStart = $now->diffInMinutes($startTime, false); // فرق الوقت بالدقائق (سالب إذا فات)

            // نحدد هل نحن في الفترة (15-30) أو (0-15)
            $isSecondReminderPeriod = $minutesToStart > 0 && $minutesToStart <= 15;
            $isFirstReminderPeriod  = $minutesToStart > 15 && $minutesToStart <= 30;
            
            // ----------------------------------------------------------------------------------
            // المنطق النهائي للإرسال مرتين فقط بناءً على notification_count
            // ----------------------------------------------------------------------------------
            
            $canSend = false;
            
            if ($course->notification_count < 2) { // أقصى عدد إشعارات هو 2
                if ($course->notification_count == 0 && $isFirstReminderPeriod) {
                    // الإرسال الأول: في فترة 15-30 دقيقة
                    $canSend = true;
                } elseif ($course->notification_count == 1 && $isSecondReminderPeriod) {
                    // الإرسال الثاني: في فترة 0-15 دقيقة
                    $canSend = true;
                }
            }

            if ($canSend) {
                $this->sendNotification($course);
                $course->update([
                    'notification_count' => $course->notification_count + 1,
                    'last_notification_time' => $now,
                ]);
                $this->info("Emails de notification envoyés pour le cours: {$course->title}. Reste {$minutesToStart} minutes.");
            }
        }
        $this->info("Tâche de notification terminée.");
    }
    
    // ----------------------------------------------------
    // دالة جديدة لإرسال الإيميلات
    // ----------------------------------------------------
    protected function sendNotification(Course $course)
    {
        // 1. Envoyer l'email au consultant
        if ($course->consultant_id) {
            $consultant = User::find($course->consultant_id);
            if ($consultant) {
                Mail::to($consultant->email)->send(new NewCourseNotification($course));
            }
        }

        // 2. Envoyer l'email aux étudiants
        // نستخدم العلاقة usersJoined المحددة في Model Course لجلب الطلاب
        $students = $course->usersJoined;
        
        foreach ($students as $student) {
            Mail::to($student->email)->send(new NewCourseNotification($course));
        }
    }
}
