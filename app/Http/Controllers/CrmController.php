<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationAccount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CrmController extends Controller
{
    /**
     * Récupérer l'admin CRM connecté
     */
    private function crmUser()
    {
        return Auth::guard('crm')->user();
    }

    /**
     * Page principale du CRM (index)
     */
    public function index()
    {
        $user = $this->crmUser();
        
        $applications = Application::active()
            ->with(['accounts' => function($query) use ($user) {
                $query->where('crm_admin_id', $user->id)
                      ->where('is_active', true)
                      ->orderBy('role_name');
            }])
            ->get();

        $stats = [
            'total_apps' => $applications->count(),
            'total_accounts' => ApplicationAccount::where('crm_admin_id', $user->id)->count(),
            'active_accounts' => ApplicationAccount::where('crm_admin_id', $user->id)
                ->where('is_active', true)
                ->count(),
        ];

        return view('crm.index', compact('applications', 'stats'));
    }

    /**
     * Formulaire d'ajout d'un compte
     */
    public function createAccount($applicationId)
    {
        $application = Application::findOrFail($applicationId);
        $availableRoles = $this->getAvailableRoles($application->slug);
        
        return view('crm.create-account', compact('application', 'availableRoles'));
    }

    /**
     * Enregistrer un nouveau compte
     */
    public function storeAccount(Request $request, $applicationId)
    {
        $validated = $request->validate([
            'role_name' => 'required|string|max:100',
            'username' => 'required|string|max:255',
            'password' => 'required|string|min:4',
            'notes' => 'nullable|string|max:500'
        ]);

        $user = $this->crmUser();

        // Vérifier si le rôle existe déjà
        $exists = ApplicationAccount::where('application_id', $applicationId)
            ->where('crm_admin_id', $user->id)
            ->where('role_name', $validated['role_name'])
            ->exists();

        if ($exists) {
            return back()->with('error', 'Ce rôle existe déjà !');
        }

        ApplicationAccount::create([
            'application_id' => $applicationId,
            'crm_admin_id' => $user->id,
            'role_name' => $validated['role_name'],
            'username' => $validated['username'],
            'password' => $validated['password'],
            'notes' => $validated['notes'] ?? null,
            'is_active' => true
        ]);

        return redirect()
            ->route('crm.index')
            ->with('success', "Compte ajouté avec succès !");
    }

    /**
     * Mettre à jour un compte
     */
    public function updateAccount(Request $request, $accountId)
    {
        $account = ApplicationAccount::where('id', $accountId)
            ->where('crm_admin_id', $this->crmUser()->id)
            ->firstOrFail();

        $validated = $request->validate([
            'username' => 'sometimes|string|max:255',
            'password' => 'sometimes|string|min:4',
            'notes' => 'nullable|string|max:500',
            'is_active' => 'sometimes|boolean'
        ]);

        $account->update($validated);

        return back()->with('success', 'Compte mis à jour avec succès !');
    }

    /**
     * Supprimer un compte
     */
    public function deleteAccount($accountId)
    {
        $account = ApplicationAccount::where('id', $accountId)
            ->where('crm_admin_id', $this->crmUser()->id)
            ->firstOrFail();

        $roleName = $account->role_name;
        $account->delete();

        return back()->with('success', "Compte {$roleName} supprimé !");
    }

    /**
     * Afficher le mot de passe (AJAX)
     */
    public function showPassword($accountId)
    {
        $account = ApplicationAccount::where('id', $accountId)
            ->where('crm_admin_id', $this->crmUser()->id)
            ->firstOrFail();

        $account->markAsUsed();

        return response()->json([
            'password' => $account->decrypted_password
        ]);
    }

    /**
     * Récupérer les credentials (pour connexion rapide)
     */
    public function getCredentials($accountId)
    {
        $account = ApplicationAccount::with('application')
            ->where('id', $accountId)
            ->where('crm_admin_id', $this->crmUser()->id)
            ->firstOrFail();

        $account->markAsUsed();

        return response()->json([
            'success' => true,
            'url' => $account->application->url,
            'username' => $account->username,
            'password' => $account->decrypted_password,
            'role' => $account->role_name
        ]);
    }

    /**
     * Rôles disponibles par application
     */
    private function getAvailableRoles(string $slug): array
    {
        $roles = [
            'uits-admin' => ['Admin', 'Admin2'],
            'uits-mgmt' => [
                'Sup_Admin',
                'USER_TECH', 
                'Custom_Admin',
                'Sales_Admin',
                'USER_TRAINING',
                'USER_MULTIMEDIA',
                'Client'
            ],
            'uits-portail' => [
                'Etudiant',
                'Consultant',
                'Admin',
                'Équipe Technique'
            ]
        ];

        return $roles[$slug] ?? [];
    }
}