<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserAccess
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handle(Request $request, Closure $next): Response
    {
        // نتحقق من أن المستخدم مسجل الدخول ولديه دور 'Etudiant'
        if (Auth::check() && Auth::user()->hasRole('Etudiant')) {
            $user = Auth::user();
            
            // قائمة بالمسارات المسموحة للمستخدمين الذين لم يتم تفعيل اشتراكهم
            $allowedRoutes = [
                'etudiant.choose_formation',
                'etudiant.enroll_formation',
                'etudiant.inscription.pending',
                'logout' // ضروري لتمكين المستخدم من تسجيل الخروج
            ];
            
            // نتحقق من وجود اشتراك نشط باستخدام الـ Accessor
            // هذا سيتحقق مباشرة من قاعدة البيانات في كل مرة
            if (!$user->has_active_inscription) {
                // إذا كان يحاول الوصول إلى مسار غير مسموح، نوجهه إلى الصفحة الصحيحة
                if (!in_array($request->route()->getName(), $allowedRoutes)) {
                    // نبحث عن اشتراك قيد الانتظار
                    $pendingInscription = $user->inscriptions()->where('status', 'pending')->first();
                    
                    if ($pendingInscription) {
                        // إذا كان هناك اشتراك قيد الانتظار، نوجهه إلى صفحة الانتظار
                        return redirect()->route('etudiant.inscription.pending', ['inscription_id' => $pendingInscription->id])
                                         ->with('info', 'Votre inscription est en attente de validation. Accès restreint.');
                    } else {
                        // إذا لم يكن لديه اشتراك، نوجهه إلى صفحة اختيار الفورماسيون
                        return redirect()->route('etudiant.choose_formation')
                                         ->with('info', 'Veuillez vous inscrire à une formation pour continuer. Accès restreint.');
                    }
                }
            }
        }
        
        // نسمح بالوصول إذا لم يتم استيفاء الشروط أعلاه
        return $next($request);
    }
}