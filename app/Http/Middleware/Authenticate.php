<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate extends Middleware
{
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {
            return route('login');
        }
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string[]  ...$guards
     * @return mixed
     */
    public function handle($request, \Closure $next, ...$guards)
    {
        // On vérifie d'abord si l'utilisateur est authentifié
        if (! Auth::check()) {
            // Si non, on utilise la logique standard de Laravel pour la redirection
            $this->authenticate($request, $guards);
        }

        $user = Auth::user();

        // On vérifie le statut de l'utilisateur
        if ($user && $user->status !== 'active') {
            // Si le statut n'est pas "active", on déconnecte l'utilisateur
            Auth::logout();

            // On invalide sa session pour la sécurité
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            // On le redirige vers la page de connexion avec un message d'erreur
            return redirect()->route('login')->with('error', 'Votre compte est inactif ou suspendu. Veuillez contacter l\'administrateur.');
        }

        // Si tout est bon, on laisse l'accès
        return $next($request);
    }
}
