<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CrmAuth
{
    /**
     * Vérifier si l'utilisateur est connecté au CRM
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::guard('crm')->check()) {
            return redirect()->route('crm.login');
        }

        return $next($request);
    }
}