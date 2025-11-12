<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EquipeTechniqueMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->hasRole('Équipe Technique')) {
            // List of allowed routes for Équipe Technique
            $allowedRoutes = [
                'reclamations.index',
                'reclamations.show',
                'reclamations.respond',
                'reclamations.updateStatus',
                'profile.edit',
                'profile.update',
                'logout',
            ];

            $currentRoute = $request->route()->getName();

            // If route is not allowed, redirect to reclamations index
            if (!in_array($currentRoute, $allowedRoutes)) {
                return redirect()->route('reclamations.index')
                    ->with('warning', 'Accès limité aux réclamations assignées uniquement.');
            }
        }

        return $next($request);
    }
}