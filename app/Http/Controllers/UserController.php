<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection; // Correct namespace for Collection
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use App\Mail\UserCreatedMail;

class UserController extends Controller
{
    // Constructor to apply middleware for permissions
    public function __construct()
    {
        // Apply permission middlewares to control access to methods
        // Ensure these permissions are defined in your seeder and assigned to appropriate roles
        $this->middleware('permission:user-list', ['only' => ['index', 'show', 'export']]); // Added show and export
        $this->middleware('permission:user-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:user-edit', ['only' => ['edit', 'update', 'toggleStatus']]);
        $this->middleware('permission:user-delete', ['only' => ['destroy', 'bulkAction']]);
    }

    /**
     * Display a listing of the users.
     */
 
    
    /**
     * Display a listing of the users.
     * (FiX: Tri et filtrage par rôle corrigés).
     */
  public function index(Request $request)
    {
        // 1. Construire la requête de base avec eager loading
        $baseQuery = User::with('roles');

        // 2. Appliquer les filtres de base sur la requête
        if ($request->filled('search')) {
            $search = $request->get('search');
            $baseQuery->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $baseQuery->where('status', $request->get('status'));
        }

        // 3. Appliquer le filtre de rôle si spécifié
        if ($request->filled('role')) {
            $roleFilter = $request->get('role');
            $roleToFilter = ($roleFilter === 'admis') ? 'Admin' : ucfirst($roleFilter);
            
            $baseQuery->whereHas('roles', function ($q) use ($roleToFilter) {
                $q->where('name', $roleToFilter);
            });
        }

        // 4. Tri par date de création
        $baseQuery->orderBy('created_at', 'desc');

        // 5. Pour les requêtes AJAX (pagination spécifique à un groupe)
        if ($request->ajax()) {
            $group = $request->get('group');
            
            // Cloner la requête de base pour ce groupe spécifique
            $groupQuery = clone $baseQuery;
            
            // Appliquer le filtre de rôle spécifique au groupe
            switch ($group) {
                case 'consultant':
                    $groupQuery->whereHas('roles', function ($q) {
                        $q->where('name', 'Consultant');
                    });
                    $pageParam = 'page_consultant';
                    break;
                    
                case 'etudiant':
                    $groupQuery->whereHas('roles', function ($q) {
                        $q->where('name', 'Etudiant');
                    });
                    $pageParam = 'page_etudiant';
                    break;
                    
                case 'admis':
                    $groupQuery->whereHas('roles', function ($q) {
                        $q->where('name', 'Admin');
                    });
                    $pageParam = 'page_admin';
                    break;
                    
                default:
                    return response()->json(['success' => false, 'message' => 'Groupe invalide']);
            }

            // Pagination directe avec Eloquent
            $perPage = 2;
            $page = $request->get($pageParam, 1);
            
            $paginatedUsers = $groupQuery->paginate($perPage, ['*'], $pageParam, $page);
            
            // Conserver les paramètres de requête pour la pagination
            $paginatedUsers->appends($request->except([$pageParam, 'group']));

            // Générer le HTML de pagination
            $paginationHtml = $paginatedUsers->links('vendor.pagination.bootstrap-5')->toHtml();

            return response()->json([
                'success' => true,
                'users' => $paginatedUsers,
                'pagination' => $paginationHtml,
                'stats' => $this->getStats()
            ]);
        }

        // 6. Pour l'affichage initial (non-AJAX)
        $allFilteredUsers = $baseQuery->get();

        // Séparer les utilisateurs par rôle
        $consultants = $allFilteredUsers->filter(function ($user) {
            return $user->hasRole('Consultant');
        });

        $etudiants = $allFilteredUsers->filter(function ($user) {
            return $user->hasRole('Etudiant');
        });

        $admis = $allFilteredUsers->filter(function ($user) {
            return $user->hasRole('Admin');
        });

        // Pagination manuelle pour l'affichage initial
        $perPage = 2;
        
        $consultantsPaginated = $this->paginateCollection($consultants, $perPage, $request->get('page_consultant'), 'page_consultant');
        $etudiantsPaginated = $this->paginateCollection($etudiants, $perPage, $request->get('page_etudiant'), 'page_etudiant');
        $admisPaginated = $this->paginateCollection($admis, $perPage, $request->get('page_admin'), 'page_admin');

        // Conserver les paramètres de requête
        $consultantsPaginated->appends($request->except('page_consultant'));
        $etudiantsPaginated->appends($request->except('page_etudiant'));
        $admisPaginated->appends($request->except('page_admin'));

        // Stats et rôles
        $stats = $this->getStats();
        $allRoles = Role::all();

        return view('users.index', compact(
            'consultantsPaginated',
            'etudiantsPaginated', 
            'admisPaginated',
            'stats',
            'allRoles'
        ));
    }

    /**
     * Pagination manuelle pour les collections
     */


     private function getStats()
    {
        return [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'recent' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];
    }
    protected function paginateCollection($items, $perPage = 2, $page = null, $pageName = 'page')
    {
        $page = $page ?: (Paginator::resolveCurrentPage($pageName) ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        
        // Tri des éléments par date de création
        $items = $items->sortByDesc('created_at')->values();
        
        // Récupération des éléments pour la page courante
        $paginatedItems = $items->forPage($page, $perPage);

        return new LengthAwarePaginator(
            $paginatedItems,
            $items->count(),
            $perPage,
            $page,
            [
                'path' => Paginator::resolveCurrentPath(),
                'pageName' => $pageName,
            ]
        );
    }

    /**
     * Toggle user status (active/inactive).
     */
    
    /**
     * Show the form for creating a new user.
     */
    public function create()
    {
        // Permission is already checked by the middleware
        $roles = Role::all(); // Get all roles for the dropdown
        return view('users.create', compact('roles'));
    }

    /**
     * Store a newly created user in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive,suspended',
            'role' => 'required|string|exists:roles,name',

            'documents' => 'nullable|array',
            'documents.*.name' => 'nullable|string|max:255',
            'documents.*.file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
        ]);

        $temporaryPassword = \Illuminate\Support\Str::random(10);
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'password' => Hash::make($temporaryPassword),
            'email_verified_at' => now(),
        ];

        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $userData['avatar'] = $avatarPath;
        }

        $documentsData = [];
        if ($request->has('documents')) {
            foreach ($request->input('documents') as $index => $document) {
                if ($request->hasFile("documents.{$index}.file")) {
                    $file = $request->file("documents.{$index}.file");

                    // S'assurer que le fichier est valide avant de le stocker
                    if ($file->isValid()) {
                        $path = $file->store('documents', 'public');

                        // Utiliser le nom du document s'il est fourni, sinon le nom de fichier original
                        $docName = $document['name'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                        $documentsData[] = [
                            'name' => $docName,
                            'path' => $path,
                            'type' => $file->getClientOriginalExtension(),
                        ];
                    }
                }
            }
        }
        $userData['documents'] = $documentsData;


        // 5. Création de l'utilisateur
        $user = User::create($userData);

        // 6. Assignation du rôle
        if ($request->filled('role')) {
            $user->syncRoles($request->role);
        }

        // 7. Envoi de l'email avec le mot de passe temporaire
        Mail::to($user->email)->send(new UserCreatedMail($user, $temporaryPassword));

        return redirect()->route('users.index')->with('success', 'Utilisateur créé avec succès et un mot de passe temporaire a été envoyé par email!');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user)
    {
        // Load roles for display
        $user->load('roles'); 

        // Les informations 'last_login_at' et 'login_count' sont directement
        // disponibles via l'objet $user
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user)
    {
        // Permission is already checked by the middleware
        $roles = Role::all();
        $user->load('roles');
        // Get the first role name assigned to the user, or null if no roles
        $userRole = $user->roles->first() ? $user->roles->first()->name : null;
        return view('users.edit', compact('user', 'roles', 'userRole'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(Request $request, User $user)
    {
        // 1. Validation des données, y compris les documents
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'phone' => 'nullable|string|max:20',
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive,suspended',
            'role' => 'required|string|exists:roles,name',

            'documents' => 'nullable|array',
            'documents.*.name' => 'nullable|string|max:255',
            'documents.*.file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:10240',
            'documents.*.id' => 'nullable|string',
            'removed_documents_paths' => 'nullable|string', // Nouvelle validation pour le champ caché
        ]);

        // 2. Préparation des données de l'utilisateur
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $userData['avatar'] = $avatarPath;
        } elseif ($request->input('clear_avatar')) {
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }
            $userData['avatar'] = null;
        }

        // 3. Gestion des documents
        $updatedDocuments = $user->documents ?? [];

        // Gérer la suppression des documents existants
        if ($request->filled('removed_documents_paths')) {
            // Décoder la chaîne JSON en tableau PHP
            $removedPaths = json_decode($request->input('removed_documents_paths'), true);

            // Filtrer les documents existants pour ne garder que ceux qui ne sont pas à supprimer
            $updatedDocuments = array_filter($updatedDocuments, function ($doc) use ($removedPaths) {
                $isRemoved = in_array($doc['path'], $removedPaths);
                if ($isRemoved) {
                    if (Storage::disk('public')->exists($doc['path'])) {
                        Storage::disk('public')->delete($doc['path']);
                    }
                }
                return !$isRemoved;
            });
            // Ré-indexer le tableau après le filtrage
            $updatedDocuments = array_values($updatedDocuments);
        }

        // Parcourir les documents soumis pour les mettre à jour ou en ajouter de nouveaux
        if ($request->has('documents')) {
            foreach ($request->input('documents') as $index => $document) {
                // Si c'est un document existant (il a un 'id')
                if (isset($document['id']) && $document['id'] !== null) {
                    // Mettre à jour le nom du document existant s'il a changé
                    foreach ($updatedDocuments as &$doc) {
                        if (isset($doc['path']) && $doc['path'] === $document['id']) {
                            $doc['name'] = $document['name'] ?? $doc['name'];
                            break;
                        }
                    }
                } else if ($request->hasFile("documents.{$index}.file")) {
                    // S'il y a un nouveau fichier, l'ajouter
                    $file = $request->file("documents.{$index}.file");
                    if ($file->isValid()) {
                        $path = $file->store('documents', 'public');
                        $docName = $document['name'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                        $updatedDocuments[] = [
                            'name' => $docName,
                            'path' => $path,
                            'type' => $file->getClientOriginalExtension(),
                        ];
                    }
                }
            }
        }

        $userData['documents'] = $updatedDocuments;

        // 4. Mettre à jour l'utilisateur et son rôle
        $user->update($userData);
        $user->syncRoles($request->role);

        return redirect()->route('users.index')->with('success', 'Utilisateur mis à jour avec succès!');
    }
    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user)
    {
        
        // Prevent deleting the currently authenticated user
        if (Auth::id() === $user->id) {
            return redirect()->route('users.index')
                ->with('error', 'Impossible de supprimer votre propre compte!');
        }

        // Check if the user is the last admin
        // Get the 'Admin' role ID for more robust check
        $adminRole = Role::where('name', 'Admin')->first();
        if ($adminRole && $user->hasRole($adminRole) && User::role($adminRole)->count() <= 1) {
            return redirect()->route('users.index')
                ->with('error', 'Impossible de supprimer le dernier administrateur!');
        }
        // Also check for Super Admin if it's the only one left
        $superAdminRole = Role::where('name', 'Super Admin')->first();
        if ($superAdminRole && $user->hasRole($superAdminRole) && User::role($superAdminRole)->count() <= 1 && ($adminRole && User::role($adminRole)->count() === 0)) {
            return redirect()->route('users.index')
                ->with('error', 'Impossible de supprimer le dernier Super Administrateur si aucun Admin n\'existe!');
        }


        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->delete();

        return redirect()->route('users.index')->with('success', 'Utilisateur supprimé avec succès!');
    }

    /**
     * Toggle user status (via AJAX).
     */
    // Le code de ton contrôleur est déjà bien pour cette partie :

 public function toggleStatus(User $user, $status)
    {
        try {
            $user->update(['status' => $status]);
            
            return response()->json([
                'success' => true,
                'message' => 'Statut mis à jour avec succès',
                'status' => $status
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour du statut'
            ], 500);
        }
    }

    /**
     * Perform bulk actions for users.
     */
    public function bulkAction(Request $request)
    {
        // Permission is already checked by the middleware
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate,suspend', // Added 'suspend'
            'users' => 'required|array',
            'users.*' => 'exists:users,id'
        ]);

        $users = User::whereIn('id', $request->users);
        $loggedInUserId = Auth::id();

        // Prevent bulk actions on the currently authenticated user
        if (in_array($loggedInUserId, $request->users)) {
            return response()->json([
                'success' => false,
                'message' => 'Impossible d\'effectuer une action groupée incluant votre propre compte!'
            ]);
        }

        switch ($request->action) {
            case 'delete':
                $adminRole = Role::where('name', 'Admin')->first();
                $superAdminRole = Role::where('name', 'Super Admin')->first();

                $adminsToDelete = $users->whereHas('roles', function($q) use ($adminRole) {
                    $q->where('name', $adminRole->name);
                })->count();

                $superAdminsToDelete = $users->whereHas('roles', function($q) use ($superAdminRole) {
                    $q->where('name', $superAdminRole->name);
                })->count();

                $remainingAdmins = User::role($adminRole)->count() - $adminsToDelete;
                $remainingSuperAdmins = User::role($superAdminRole)->count() - $superAdminsToDelete;

                if ($adminRole && $remainingAdmins <= 0 && User::role($superAdminRole)->count() === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible de supprimer tous les administrateurs et super administrateurs!'
                    ]);
                }
                if ($superAdminRole && $remainingSuperAdmins <= 0 && User::role($adminRole)->count() === 0) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Impossible de supprimer tous les super administrateurs et administrateurs!'
                    ]);
                }


                $users->each(function ($user) {
                    if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                        Storage::disk('public')->delete($user->avatar);
                    }
                });

                $users->delete();
                $message = 'Utilisateurs supprimés avec succès!';
                break;

            case 'activate':
                $users->update(['status' => 'active']);
                $message = 'Utilisateurs activés avec succès!';
                break;

            case 'deactivate':
                $users->update(['status' => 'inactive']);
                $message = 'Utilisateurs désactivés avec succès!';
                break;
            
            case 'suspend': // New case for suspending users
                $users->update(['status' => 'suspended']);
                $message = 'Utilisateurs suspendus avec succès!';
                break;
        }

        return response()->json([
            'success' => true,
            'message' => $message
        ]);
    }

    /**
     * Export users data.
     */
    public function export(Request $request)
    {
        // Permission is already checked by the middleware
        $format = $request->get('format', 'csv');

        $users = User::with('roles')->get();

        if ($format === 'csv') {
            $filename = 'users_' . now()->format('Y-m-d_H-i-s') . '.csv';

            $headers = [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="' . $filename . '"',
            ];

            $callback = function () use ($users) {
                $file = fopen('php://output', 'w');

                // Headers
                fputcsv($file, ['ID', 'Nom', 'Email', 'Téléphone', 'Statut', 'Rôles', 'Date de création']);

                foreach ($users as $user) {
                    fputcsv($file, [
                        $user->id,
                        $user->name,
                        $user->email,
                        $user->phone,
                        $user->status,
                        $user->roles->pluck('name')->join(', '),
                        $user->created_at->format('d/m/Y H:i')
                    ]);
                }

                fclose($file);
            };

            return response()->stream($callback, 200, $headers);
        }

        return response()->json(['error' => 'Format non supporté'], 400);
    }

    public function corbeille()
{
    // Kanst3amlo onlyTrashed() bach njebdo GHI les Utilisateurs li mamsou7in
    $users = User::onlyTrashed()
                     ->orderBy('deleted_at', 'desc')
                     ->get();

    return view('users.corbeille', compact('users'));
}

// N°2. Restauration d'un Utilisateur
public function restore($id)
{
    $user = User::withTrashed()->findOrFail($id);
    $user->restore();

    return redirect()->route('users.corbeille')->with('success', 'Utilisateur restauré avec succès!');
}

// N°3. Suppression Définitive
public function forceDelete($id)
{
    $user = User::withTrashed()->findOrFail($id);
    
    // ⚠️ WARNING: Ila derti Force Delete, ghadi ytmss7 hta l-Historique dyal l'User (Ranks/Permissions...).
    
    // Khassk tmass7 les fichiers dyal l'User 9bel:
    // Storage::disk('public')->delete($user->avatar); 
    // Storage::disk('public')->deleteDirectory('users/' . $user->id); // Ila kan 3endek dossier personnel
    
    $user->forceDelete(); 

    return redirect()->route('users.corbeille')->with('success', 'Utilisateur supprimé définitivement!');
}
}
