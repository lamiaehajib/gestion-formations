<?php

use App\Http\Controllers\AjaxController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CourseController;
use App\Http\Controllers\CourseRescheduleController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\EtudiantController; // تأكد من استيراد EtudiantController
use App\Http\Controllers\FormationController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\ModuleController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\PromotionController;
use App\Http\Controllers\ReclamationController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});



Route::middleware(['auth', 'role:Etudiant'])->group(function () {
    Route::get('/choose-formation', [EtudiantController::class, 'chooseFormation'])->name('etudiant.choose_formation');
    Route::post('/enroll-formation', [EtudiantController::class, 'enrollFormation'])->name('etudiant.enroll_formation');
    Route::get('/inscription-pending/{inscription_id}', [EtudiantController::class, 'showInscriptionPending'])->name('etudiant.inscription.pending');
});



Route::middleware('auth')->group(function () {
  Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

Route::resource('roles', RoleController::class);

Route::get('/users/corbeille', [UserController::class, 'corbeille'])
      ->name('users.corbeille');

// 2. Route dyal Restauration
Route::put('/users/{id}/restore', [UserController::class, 'restore'])
      ->name('users.restore');

// 3. Route dyal Suppression Définitive
Route::delete('/users/{id}/forceDelete', [UserController::class, 'forceDelete'])
      ->name('users.forceDelete');
    Route::resource('users', UserController::class); // Cette ligne définit users.index, users.create, users.show, etc.

    // Ces routes khas ykouno mgroupin m3a users.resource bach tkoun la syntaxe users.toggle-status
    Route::patch('users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggle-status');
    Route::post('users/bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
    Route::get('users/export/data', [UserController::class, 'export'])->name('users.export'); // Bdelna "/export/data" l "users/export/data" w smitha "users.export"
Route::post('/users/{user}/toggle-status', [UserController::class, 'toggleStatus'])->name('users.toggleStatus');

    Route::prefix('categories')->name('categories.')->group(function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::patch('/{category}/toggle-status', [CategoryController::class, 'toggleStatus'])->name('toggle-status');
       Route::patch('/bulk-action', [CategoryController::class, 'bulkAction'])->name('bulk-action');
        Route::get('/export/csv', [CategoryController::class, 'export'])->name('export');
    });

    // Route API pour récupérer les catégories actives (pour les formulaires)
    Route::get('/api/categories/active', [CategoryController::class, 'getActiveCategories'])->name('api.categories.active');
 Route::get('/formations/export-csv', [FormationController::class, 'exportCsv'])->name('formations.export-csv');
    Route::resource('formations', FormationController::class);
    Route::get('/formations/category/{categoryId}', [FormationController::class, 'getByCategory'])->name('formations.getByCategory');
    Route::post('/formations/{formation}/duplicate', [FormationController::class, 'duplicate'])->name('formations.duplicate');
    Route::get('/formations/{formation}/statistics', [FormationController::class, 'statistics'])->name('formations.statistics');
    Route::patch('/formations/{formation}/toggle-status', [FormationController::class, 'toggleStatus'])->name('formations.toggleStatus');
    Route::get('/formations/calendar', [FormationController::class, 'calendar'])->name('formations.calendar');
   
Route::get('/formations/{formation}/edit-modal', [FormationController::class, 'editModalContent'])->name('formations.editModalContent');
Route::get('/formations/{formation}/inscriptions-count', [App\Http\Controllers\FormationController::class, 'getActiveInscriptionsCount']);



Route::get('/inscriptions/export', [InscriptionController::class, 'export'])->name('inscriptions.export');
    Route::get('/inscriptions/{inscription}/details-json', [App\Http\Controllers\InscriptionController::class, 'detailsJson'])->name('inscriptions.detailsJson');
Route::post('inscriptions/{inscription}/record-payment', [App\Http\Controllers\InscriptionController::class, 'recordPayment'])->name('inscriptions.recordPayment');
Route::put('inscriptions/{inscription}/update-status', [App\Http\Controllers\InscriptionController::class, 'updateStatus'])->name('inscriptions.updateStatus');
Route::get('inscriptions/{inscription}/add-payment', [App\Http\Controllers\InscriptionController::class, 'showAddPaymentForm'])->name('inscriptions.showAddPaymentForm'); // Admin/Finance form


// Route to handle the submission of the add payment form
Route::post('/inscriptions/{inscription}/add-payment', [InscriptionController::class, 'addPayment'])->name('inscriptions.addPayment');
Route::resource('inscriptions', App\Http\Controllers\InscriptionController::class);
    // Routes API pour les inscriptions
    Route::prefix('api/inscriptions')->name('api.inscriptions.')->group(function () {
        Route::get('my-inscriptions', [InscriptionController::class, 'myInscriptions'])->name('my-inscriptions');
    });
    Route::post('inscriptions/bulk-action', [InscriptionController::class, 'bulkAction'])->name('inscriptions.bulk-action');




   
Route::get('/payments/overdue-students', [PaymentController::class, 'getOverdueStudents'])->name('payments.overdue-students');
    
Route::get('/payments/export', [PaymentController::class, 'export'])->name('payments.export');
    // Custom routes for payments. Place these AFTER the resource route.
    Route::prefix('payments')->name('payments.')->group(function () {
        Route::patch('{payment}/status', [PaymentController::class, 'updateStatus'])->name('updateStatus');
        Route::post('generate-schedule', [PaymentController::class, 'generateSchedule'])->name('generate-schedule');
        Route::post('mark-late', [PaymentController::class, 'markLatePayments'])->name('mark-late');
        Route::post('bulk-update', [PaymentController::class, 'bulkUpdate'])->name('bulk-update');
        Route::get('inscription/{inscriptionId}', [PaymentController::class, 'byInscription'])->name('by-inscription');
        Route::get('api/stats', [PaymentController::class, 'stats'])->name('stats');

      
    });
    Route::resource('payments', PaymentController::class);
    
     Route::resource('courses', CourseController::class);
    
    // Additional course routes
    Route::get('formations/{formation}/courses', [CourseController::class, 'getByFormation'])
    ->name('courses.by-formation');

Route::post('courses/{course}/remove-document', [CourseController::class, 'removeDocument'])
    ->name('courses.remove-document');

Route::get('courses/{course}/download-document', [CourseController::class, 'downloadDocument'])
    ->name('courses.download-document'); // <-- THIS ONE!

Route::post('courses/{course}/join', [CourseController::class, 'join'])
    ->name('courses.join');

// API routes for AJAX calls
Route::middleware(['auth:sanctum'])->prefix('api')->group(function () {
    Route::get('formations/{formation}/courses', [CourseController::class, 'getByFormation'])
        ->name('api.courses.by-formation');
});

// Public routes (if needed)
Route::get('public/courses', [CourseController::class, 'publicIndex'])
    ->name('courses.public');



Route::get('/course-reschedules/get-courses-by-consultant', [CourseRescheduleController::class, 'getCoursesByConsultant']);
    Route::resource('course-reschedules', CourseRescheduleController::class, [
    'names' => [
        'index' => 'course_reschedules.index',
        'create' => 'course_reschedules.create', 
        'store' => 'course_reschedules.store',
        'show' => 'course_reschedules.show',
        'edit' => 'course_reschedules.edit',
        'update' => 'course_reschedules.update',
        'destroy' => 'course_reschedules.destroy'
    ]
]);

// مسارات إضافية مخصصة لـ Course Reschedules (داخل نفس المجموعة)
Route::prefix('course-reschedules')->name('course_reschedules.')->group(function () {
    
    // جلب سجل التعديلات لدورة معينة
    Route::get('course/{course}/history', [CourseRescheduleController::class, 'getCourseHistory'])
        ->name('course.history');
    
    // جلب الفترات الزمنية المتاحة للتعديل (عادة ما تكون POST)
    Route::post('available-slots', [CourseRescheduleController::class, 'getAvailableSlots'])
        ->name('available_slots');
    
    // تعديل جماعي لعدة دورات
    Route::post('bulk-reschedule', [CourseRescheduleController::class, 'bulkReschedule'])
        ->name('bulk_reschedule');

    // **المسار الذي تم فقدانه سابقًا ويجب أن يكون هنا:**
    // جلب الدورات الخاصة بمستشار معين (لطلبات AJAX)
    
});

// مسار تعديل سريع من إدارة الدورات (إذا كنت تستخدمه)
Route::post('courses/{course}/reschedule', [CourseRescheduleController::class, 'store'])
    ->name('courses.reschedule');


   
    
    // Routes spéciales pour les réclamations
    Route::prefix('reclamations')->name('reclamations.')->group(function () {
        

        Route::get('statistics', [ReclamationController::class, 'statistics'])
         ->name('statistics')
         ->middleware('permission:reclamation-statistics');
        // Assigner une réclamation à un utilisateur
        Route::patch('{reclamation}/assign', [ReclamationController::class, 'assign'])
             ->name('assign')
             ->middleware('permission:reclamation-assign');
        
        // Répondre à une réclamation
        Route::patch('{reclamation}/respond', [ReclamationController::class, 'respond'])
             ->name('respond')
             ->middleware('permission:reclamation-respond');
        
        // Mettre à jour le statut d'une réclamation
        Route::patch('{reclamation}/status', [ReclamationController::class, 'updateStatus'])
             ->name('update-status')
             ->middleware('permission:reclamation-edit');
        
        // Évaluer la résolution d'une réclamation (satisfaction)
        Route::patch('{reclamation}/rate', [ReclamationController::class, 'rate'])
             ->name('rate')
             ->middleware('permission:reclamation-rate');
        
        // Statistiques des réclamations (AJAX)
        Route::get('statistics', [ReclamationController::class, 'statistics'])
             ->name('statistics')
             ->middleware('permission:reclamation-statistics');
        
        // Exporter les réclamations
        Route::get('export', [ReclamationController::class, 'export'])
             ->name('export')
             ->middleware('permission:reclamation-export');
    });
     Route::resource('reclamations', ReclamationController::class);


       Route::get('/admin/notifications', [NotificationController::class, 'index'])->name('admin.notifications.index');
    Route::post('/admin/notifications/{id}/mark-as-read', [NotificationController::class, 'markAsRead'])->name('admin.notifications.markAsRead');
    Route::post('/admin/notifications/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('admin.notifications.markAllAsRead');
    

    Route::get('/exemples', function () {
    return view('exemple');
})->name('exemples');

Route::get('api/eligible-formations', [PromotionController::class, 'getEligibleFormations']);

Route::resource('promotions', PromotionController::class);
Route::post('promotions/bulk-create', [PromotionController::class, 'bulkCreate'])->name('promotions.bulk-create');
Route::get('promotions/{promotion}/report', [PromotionController::class, 'generateReport'])->name('promotions.generateReport');

Route::get('promotions/{promotion}/student/{user}/payments', [PromotionController::class, 'showStudentPayments'])
     ->name('promotions.studentPaymentHistory');


    Route::get('/modules/create', [ModuleController::class, 'create'])->name('modules.create');
Route::post('/modules', [ModuleController::class, 'store'])->name('modules.store');

// Routes dyal CRUD dyal les modules
Route::get('/modules/{module}/edit', [ModuleController::class, 'edit'])->name('modules.edit');
Route::put('/modules/{module}', [ModuleController::class, 'update'])->name('modules.update');
Route::delete('/modules/{module}', [ModuleController::class, 'destroy'])->name('modules.destroy');

// Route bach tchouf les modules dyal une formation
Route::get('/formations/{formation}/modules', [ModuleController::class, 'show'])->name('modules.show');

// Route bach l'consultant y'update l'progress
Route::post('/modules/{module}/progress', [ModuleController::class, 'updateProgress'])->name('modules.updateProgress');

// Route bach l'admin ychouf l'list dyal les formations m3a le count dyal les modules
Route::get('/modules', [ModuleController::class, 'index'])->name('modules.index');

Route::post('/modules/{module}/update-ajax', [ModuleController::class, 'updateAjax'])->name('modules.updateAjax');
Route::delete('/modules/{module}/destroy-ajax', [ModuleController::class, 'destroyAjax'])->name('modules.destroyAjax');
Route::get('/modules/{module}/get-data', [ModuleController::class, 'getModuleData'])->name('modules.getData');


Route::get('/get-modules/{formationId}', [AjaxController::class, 'getModules']);
});

require __DIR__.'/auth.php';