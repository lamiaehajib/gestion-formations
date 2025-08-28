<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use App\Console\Commands\CheckInstallmentDueDates;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void // تأكد أن : void موجودة هنا
    {
        // تشغيل الأمر يومياً في الساعة 1 صباحاً
        $schedule->command(CheckInstallmentDueDates::class)->dailyAt('01:00');
        
        // بديل: إذا كنت تريد تشغيله فقط في اليوم السادس من كل شهر (بعد اليوم الخامس بيوم واحد)
        // $schedule->command(CheckInstallmentDueDates::class)->monthlyOn(6, '01:00'); 

        $schedule->command('app:send-course-notifications')->everyMinute();

         $schedule->command('app:send-payment-reminders')->dailyAt('08:00');

         $schedule->command('app:check-overdue-payments')->dailyAt('09:00');
    }

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    
   
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
