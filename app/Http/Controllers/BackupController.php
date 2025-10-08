<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Storage;

class BackupController extends Controller
{
    public function download() 
    {
        // 1. تشغيل أمر النسخ الاحتياطي لإنشاء ملف جديد بتاريخ اليوم
        // نستخدم try-catch فقط لمنع ظهور خطأ 500 على المتصفح في حال فشل الباك اب
        try {
            Artisan::call('backup:run', ['--only-db' => true]);
        } catch (\Exception $e) {
            \Log::error("Backup failed during download: " . $e->getMessage());
        }

        // 2. البحث عن الملفات في الجذر (المكان الذي رأينا فيه ملفاتك .zip)
        $backupDisk = Storage::disk('local');
        $files = $backupDisk->files(); 

        // 3. فلترة الملفات وترتيبها للحصول على الأحدث
        $latestBackup = collect($files)
            ->filter(fn($file) => str_ends_with($file, '.zip')) // فلترة ملفات الـ ZIP فقط
            ->sortByDesc(fn($file) => $backupDisk->lastModified($file)) // الترتيب حسب وقت التعديل
            ->first(); // الحصول على الملف الأحدث

        if ($latestBackup) {
            // 4. إرسال الملف الأحدث للتنزيل
            return $backupDisk->download($latestBackup, basename($latestBackup));
        }

        // 5. رسالة فشل
        return back()->with('error', 'No backup file found to download. (Check permissions)');
    }
}