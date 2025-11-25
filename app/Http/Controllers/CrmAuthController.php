<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmAuthController extends Controller
{
    /**
     * Afficher le formulaire de connexion
     */
    public function showLoginForm()
    {
        // Si déjà connecté, rediriger vers le dashboard
        if (Auth::guard('crm')->check()) {
            return redirect()->route('crm.dashboard');
        }

        return view('crm.login');
    }

    /**
     * Traiter la connexion
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $remember = $request->boolean('remember');

        if (Auth::guard('crm')->attempt($credentials, $remember)) {
            $request->session()->regenerate();

            // Mettre à jour la dernière connexion
            Auth::guard('crm')->user()->updateLastLogin();

            return redirect()->intended(route('crm.dashboard'));
        }

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors([
                'email' => 'Les identifiants sont incorrects.',
            ]);
    }

    /**
     * Déconnexion
     */
    public function logout(Request $request)
    {
        Auth::guard('crm')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('crm.login');
    }
}