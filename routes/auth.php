<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use App\Http\Controllers\EtudiantController; // تأكد من استيراد EtudiantController هنا
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])->name('register');
    Route::post('register', [RegisteredUserController::class, 'store']);

    // مسارات تسجيل الطلاب المخصصة (تبقى هنا لأنها للضيوف)
    Route::get('/register/etudiant', [RegisteredUserController::class, 'createEtudiantRegistrationForm'])->name('register.etudiant');
    Route::post('/register/etudiant', [RegisteredUserController::class, 'storeEtudiantRegistration']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])->name('login');
    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])->name('password.request');
    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])->name('password.reset');
    Route::post('reset-password', [NewPasswordController::class, 'store'])->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)->name('verification.notice');
    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)->middleware(['signed', 'throttle:6,1'])->name('verification.verify');
    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])->middleware('throttle:6,1')->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])->name('password.confirm');
    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])->name('logout');

    // ===============================================
    // مسارات الطلاب (Etudiant Role) - تتطلب مصادقة ودور 'etudiant'
    // ===============================================
   
        Route::get('/etudiant/choose-formation', [EtudiantController::class, 'showChooseFormationForm'])->name('etudiant.choose_formation');
       
        Route::post('/etudiant/enroll-formation', [EtudiantController::class, 'enrollFormation'])->name('etudiant.enroll_formation');
        
        // 🔥 IMPORTANT CHANGE: Make the inscription_id a required parameter for this route
        Route::get('/etudiant/inscription-pending/{inscription_id}', [EtudiantController::class, 'showInscriptionPending'])->name('etudiant.inscription.pending');
    // ===============================================
    // مسارات المسؤولين (Admin Role) - تتطلب مصادقة ودور 'admin'
    // ===============================================
    Route::middleware('role:admin')->group(function () {
        // إدارة حسابات الطلاب المعلقة
        Route::get('/admin/pending-students', [EtudiantController::class, 'showPendingStudents'])->name('admin.pending_students');
        Route::post('/admin/validate-student/{user}', [EtudiantController::class, 'validateStudent'])->name('admin.validate_student');

        // إدارة تسجيلات الدورات المعلقة
        Route::get('/admin/pending-inscriptions', [EtudiantController::class, 'showPendingInscriptions'])->name('admin.pending_inscriptions');
        Route::post('/admin/validate-inscription/{inscription}', [EtudiantController::class, 'validateInscription'])->name('admin.validate_inscription');
    });
});