<?php

namespace App\Policies;

use App\Models\Course;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class CoursePolicy
{
    use HandlesAuthorization;

    /**
     * دالة قبلية: تسمح للمشرفين الكبار (Admin/super admin) بتجاوز جميع التحققات.
     *
     * @param  \App\Models\User  $user
     * @param  string  $ability
     * @return \Illuminate\Auth\Access\Response|bool|null
     */
    public function before(User $user, $ability)
    {
        if ($user->hasRole('Admin') || $user->hasRole('Super Admin') || $user->hasRole('Finance')) {
            \Log::info("CoursePolicy@before: المستخدم Admin/Super Admin. السماح بالوصول لـ {$ability}.");
            return true; // المشرفون الكبار لديهم وصول كامل
        }
        // إذا لم يكن المستخدم Admin/Super Admin، فسيستمر التحقق في الدوال الأخرى
        return null; 
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه رؤية أي كورسات (مثل قائمة الكورسات).
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function viewAny(User $user)
    {
        // المشرفون الكبار يتم التعامل معهم بواسطة دالة before()
        if ($user->hasRole('Consultant')) {
            \Log::info("CoursePolicy@viewAny: السماح للمستشار برؤية قائمة الكورسات.");
            return true; // يمكن للمستشارين رؤية قوائم الكورسات الخاصة بهم
        }
        if ($user->hasRole('Etudiant')) {
            \Log::info("CoursePolicy@viewAny: السماح للطالب برؤية قائمة الكورسات.");
            return true; // يمكن للطلاب رؤية قائمة الكورسات بشكل عام، لكن دالة 'view' ستتحقق من الوصول لكل كورس على حدة.
        }
        \Log::info("CoursePolicy@viewAny: منع الوصول. دور المستخدم: " . json_encode($user->getRoleNames()));
        return false; // بقية الأدوار لا يمكنهم رؤية القائمة افتراضياً
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه رؤية كورس معين.
     * هذا هو الجزء الأهم لتقييد الوصول بناءً على الدفع.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function view(User $user, Course $course)
    {
        \Log::info("CoursePolicy@view: User ID: {$user->id}, Course ID: {$course->id}");
        \Log::info("CoursePolicy@view: User Roles: " . json_encode($user->getRoleNames()));

        // المشرفون الكبار يتجاوزون جميع التحققات (من دالة before())
        // لذا إذا كان المستخدم Admin، لن يتم تنفيذ هذا الجزء

        // المستشارون يمكنهم رؤية الكورسات المخصصة لهم
        if ($user->hasRole('Consultant') && $course->consultant_id === $user->id) {
            \Log::info("CoursePolicy@view: السماح بالوصول للمستشار (كورسه الخاص).");
            return true;
        }

        // الطلاب: التحقق من التسجيل وحالة التقييد
        if ($user->hasRole('Etudiant')) {
            // يجب تحميل علاقة 'inscriptions' للمستخدم إذا لم تكن محملة مسبقاً
            // أو التأكد من أن InscriptionController@index يقوم بتحميلها
            $inscription = $user->inscriptions()
                                ->where('formation_id', $course->formation_id)
                                ->whereIn('status', ['active', 'completed'])
                                ->first();

            \Log::info("CoursePolicy@view (Etudiant): هل تم العثور على تسجيل للتكوين ID {$course->formation_id}: " . ($inscription ? 'نعم' : 'لا'));
            if ($inscription) {
                \Log::info("CoursePolicy@view (Etudiant): Inscription ID: {$inscription->id}, Status: {$inscription->status}, Access Restricted: {$inscription->access_restricted}");
            }

            if ($inscription && !$inscription->access_restricted) { // هذا هو الشرط الحاسم
                \Log::info("CoursePolicy@view (Etudiant): السماح بالوصول (التسجيل نشط/مكتمل وغير مقيد).");
                return true;
            }
            \Log::info("CoursePolicy@view (Etudiant): منع الوصول (لم يتم العثور على تسجيل، أو التسجيل مقيد).");
        }

        \Log::info("CoursePolicy@view: منع الوصول افتراضياً للأدوار الأخرى أو فشل تحققات الطالب.");
        return false; // منع الوصول افتراضياً
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه إنشاء كورس.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function create(User $user)
    {
        // السماح للمشرفين (Admin) والمستشارين (Consultant) بإنشاء الكورسات
        if ($user->hasRole('Admin') || $user->hasRole('Super Admin') || $user->hasRole('Finance')) {
            \Log::info("CoursePolicy@create: السماح بالوصول للمشرف/المستشار لإنشاء كورس.");
            return true;
        }

        \Log::info("CoursePolicy@create: منع الوصول. دور المستخدم: " . json_encode($user->getRoleNames()));
        return false; // منع الأدوار الأخرى
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه تحديث كورس.
     * (هذه الدالة تؤثر على دوال edit و update)
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function update(User $user, Course $course)
    {
        // السماح للمشرفين (Admin) بتعديل أي كورس
        if ($user->hasRole('Admin') || $user->hasRole('Super Admin') || $user->hasRole('Finance')) {
            \Log::info("CoursePolicy@update: السماح بالوصول للمشرف لتعديل الكورس.");
            return true;
        }
        // السماح للمستشارين (Consultant) بتعديل الكورسات الخاصة بهم فقط
        if ($user->hasRole('Consultant') && $course->consultant_id === $user->id) {
            \Log::info("CoursePolicy@update: السماح بالوصول للمستشار (كورسه الخاص) لتعديل الكورس.");
            return true;
        }

        \Log::info("CoursePolicy@update: منع الوصول. User ID: {$user->id}, Course ID: {$course->id}. User Roles: " . json_encode($user->getRoleNames()));
        return false;
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه حذف كورس.
     * (هذه الدالة تؤثر على دالة destroy)
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function delete(User $user, Course $course)
    {
        // السماح للمشرفين (Admin) فقط بحذف الكورسات
        if ($user->hasRole('Admin') || $user->hasRole('Super Admin') || $user->hasRole('Finance')) {
            \Log::info("CoursePolicy@delete: السماح بالوصول للمشرف لحذف الكورس.");
            return true;
        }

        \Log::info("CoursePolicy@delete: منع الوصول. User ID: {$user->id}, Course ID: {$course->id}. User Roles: " . json_encode($user->getRoleNames()));
        return false;
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه الانضمام إلى كورس (مثلاً عبر رابط Zoom).
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function join(User $user, Course $course)
    {
        // المشرفون والمستشارون يمكنهم الانضمام دائماً (سيتم التعامل معهم بواسطة before() أو منطقهم الخاص)
        if ($user->hasRole('Admin') || ($user->hasRole('Consultant') && $course->consultant_id === $user->id)) {
            \Log::info("CoursePolicy@join: السماح بالوصول للمشرف/المستشار للانضمام.");
            return true;
        }

        // الطلاب: يجب أن يكونوا مسجلين في التكوين الخاص بالكورس وأن وصولهم غير مقيد
        if ($user->hasRole('Etudiant')) {
            $inscription = $user->inscriptions()
                                ->where('formation_id', $course->formation_id)
                                ->whereIn('status', ['active', 'completed'])
                                ->first();

            if ($inscription && !$inscription->access_restricted) {
                \Log::info("CoursePolicy@join: السماح بالوصول للطالب (تسجيل نشط وغير مقيد).");
                return true;
            }
            \Log::info("CoursePolicy@join: منع الوصول للطالب (تسجيل غير موجود أو مقيد).");
        }

        \Log::info("CoursePolicy@join: منع الوصول افتراضياً.");
        return false;
    }

    /**
     * تحديد ما إذا كان المستخدم يمكنه تحميل مستند من كورس.
     *
     * @param  \App\Models\User  $user
     * @param  \App\Models\Course  $course
     * @return \Illuminate\Auth\Access\Response|bool
     */
    public function downloadDocument(User $user, Course $course)
    {
        // المشرفون والمستشارون يمكنهم التحميل دائماً (سيتم التعامل معهم بواسطة before() أو منطقهم الخاص)
        if ($user->hasRole('Admin') || ($user->hasRole('Consultant') && $course->consultant_id === $user->id)) {
            \Log::info("CoursePolicy@downloadDocument: السماح بالوصول للمشرف/المستشار للتحميل.");
            return true;
        }

        // الطلاب: يجب أن يكونوا مسجلين في التكوين الخاص بالكورس وأن وصولهم غير مقيد
        if ($user->hasRole('Etudiant')) {
            $inscription = $user->inscriptions()
                                ->where('formation_id', $course->formation_id)
                                ->whereIn('status', ['active', 'completed'])
                                ->first();

            if ($inscription && !$inscription->access_restricted) {
                \Log::info("CoursePolicy@downloadDocument: السماح بالوصول للطالب (تسجيل نشط وغير مقيد).");
                return true;
            }
            \Log::info("CoursePolicy@downloadDocument: منع الوصول للطالب (تسجيل غير موجود أو مقيد).");
        }

        \Log::info("CoursePolicy@downloadDocument: منع الوصول افتراضياً.");
        return false;
    }
}
