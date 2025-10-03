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
    protected $description = 'Sends email notifications for courses scheduled to start in 5 minutes.';

    public function handle()
    {
        $now = Carbon::now();
        $today = $now->toDateString();

        // ----------------------------------------------------
        // 1. تحديد الكورسات التي ستبدأ خلال الـ 5 دقائق القادمة (5-0 دقائق)
        // ----------------------------------------------------

        // نحسب الوقت بعد 5 دقائق من الآن
        $fiveMinutesFromNow = $now->copy()->addMinutes(5);

        // **ملحوظة:** يجب التأكد من أن حقل course_date في الموديل Course نوعه Date وأن start_time نوعه Time.
        
        $courses = Course::whereDate('course_date', $today)
            // نختار الكورسات التي ستبدأ خلال 5 دقائق القادمة (بين الآن و 5 دقائق من الآن)
            // هذا الشرط يساعد على جلب الكورسات في النطاق الصحيح
            ->whereRaw("TIME_TO_SEC(start_time) BETWEEN TIME_TO_SEC(?) AND TIME_TO_SEC(?)", [
                $now->toTimeString(),
                $fiveMinutesFromNow->toTimeString()
            ])
            // شرط إضافي: نختار فقط الكورسات التي لم ترسل لها إشعارات بعد (نريد إرسال واحد فقط)
            ->where('notification_count', 0)
            ->get();

        if ($courses->isEmpty()) {
            $this->info("Aucun cours à notifier dans les 5 prochaines minutes ou déjà notifié.");
            return;
        }

        foreach ($courses as $course) {
            // ✅ دمج التاريخ والوقت لتكوين كائن Carbon لـ وقت البدء
            // يجب التأكد من أن start_time و course_date هي حقول من الموديل Course
            // إذا كانت course_date هي كائن Carbon، نستخدم format. إذا كانت string، نستخدمها مباشرة.
            // هنا نفترض أنها كائنات Carbon (كما كان في الكود الأصلي)
            // **تنقيح:** إذا كانت الأعمدة من نوع Carbon، فستحتاج إلى التأكد من أن الـ accessor تعمل بشكل صحيح.
            // الطريقة الأكثر أمانًا هي ربما افتراض أن course_date هو string/date و start_time هو string/time من قاعدة البيانات.
             try {
                // محاولة استخدام القيمة كما هي، مع الافتراض أنها قد تكون كائن تاريخ أو يجب تحويلها لـ string
                $courseDate = is_object($course->course_date) ? $course->course_date->toDateString() : $course->course_date;
                $startTime = Carbon::parse($courseDate . ' ' . $course->start_time);
            } catch (\Exception $e) {
                // في حالة وجود مشكلة في التحويل، قم بتسجيل الخطأ أو تخطي الدورة التدريبية
                $this->error("Erreur de parsing de l'heure pour le cours ID {$course->id}: {$e->getMessage()}");
                continue;
            }

            // فرق الوقت بالدقائق (سالب إذا فات)
            $minutesToStart = $now->diffInMinutes($startTime, false); 

            // ----------------------------------------------------------------------------------
            // المنطق النهائي للإرسال مرة واحدة عندما يتبقى 5 دقائق
            // ----------------------------------------------------------------------------------
            
            // نريد أن نرسل فقط إذا كان الوقت المتبقي هو 5 دقائق أو أقل (وليس سالبًا)
            // ونريد أن نتأكد من أننا نرسل مرة واحدة فقط (notification_count == 0)
            // يمكننا أن نختار نطاقًا ضيقًا جدًا، مثلاً (0 < minutesToStart <= 5)
            // وبما أننا قمنا بالتصفية في قاعدة البيانات على النطاق [الآن, الآن + 5 دقائق]، فيكفي التحقق من notification_count
            
            // نتحقق من أن الوقت المتبقي هو 5 دقائق أو أقل، لكن أكثر من 0 (لم يبدأ بعد)
            // ونتحقق أننا لم نرسل من قبل (notification_count == 0)
            $canSend = $course->notification_count == 0 && $minutesToStart > 0 && $minutesToStart <= 5;
            

            if ($canSend) {
                $this->sendNotification($course);
                $course->update([
                    // زيادة العداد لتجنب الإرسال مرة أخرى
                    'notification_count' => 1, 
                    'last_notification_time' => $now,
                ]);
                $this->info("Emails de notification envoyés pour le cours: {$course->title}. Reste {$minutesToStart} minutes.");
            } else {
                 $this->comment("Cours {$course->title} non éligible pour la notification (déjà envoyé ou en dehors du créneau de 5 min). Reste {$minutesToStart} minutes.");
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