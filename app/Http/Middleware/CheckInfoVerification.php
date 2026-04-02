<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class CheckInfoVerification
{
    public function handle($request, Closure $next)
    {
        $user = Auth::user();
        
        if ($user && $user->hasRole('Etudiant') && $user->needsInfoVerification()) {
            // Passer un flag à la vue pour afficher le pop-up
            view()->share('showInfoVerificationModal', true);
            view()->share('missingFields', $this->getMissingFields($user));
        }

        return $next($request);
    }

    private function getMissingFields($user): array
{
    $missing = [];
    
    // Zid hado hna bach l-etudiant i-3mmerhom darori
    if (empty($user->nom))             $missing[] = 'nom';
    if (empty($user->prenom))          $missing[] = 'prenom';
    
    if (empty($user->cin))             $missing[] = 'cin';
    if (empty($user->birth_date))      $missing[] = 'birth_date';
    if (empty($user->lieu_naissance))  $missing[] = 'lieu_naissance';
    if (empty($user->nationalite))     $missing[] = 'nationalite';
    if (empty($user->address))         $missing[] = 'address';
    if (empty($user->phone))           $missing[] = 'phone';
    
    return $missing;
}
}