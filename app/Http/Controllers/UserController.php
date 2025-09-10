<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log; // Don't forget to import Log facade!
use Illuminate\Support\Facades\Mail; // Importe la façade Mail
use App\Mail\UserCreatedMail;      // Importe la nouvelle Mailable

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
    public function index(Request $request)
    {
        // Permission is already checked by the middleware in the constructor
        $query = User::with('roles'); // Eager load roles for display and filtering

        // Filters
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%')
                    ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('role')) {
            $query->whereHas('roles', function ($q) use ($request) {
                $q->where('name', $request->get('role'));
            });
        }

        // Sorting
        $sortBy = $request->get('sort_by', 'created_at');
        $sortDirection = $request->get('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $users = $query->paginate(10);

        // Statistics
        $stats = [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'inactive' => User::where('status', 'inactive')->count(),
            'suspended' => User::where('status', 'suspended')->count(), // Added suspended status
            'recent' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];
        
        // Fetch all roles for the filter dropdown
        $allRoles = Role::all();

        if ($request->ajax()) {
            return response()->json([
                'users' => $users,
                'stats' => $stats,
                'html' => view('users.partials.table', compact('users'))->render()
            ]);
        }

        return view('users.index', compact('users', 'stats', 'allRoles'));
    }

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
        // 1. Validation des données, y compris les documents
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'status' => 'required|in:active,inactive,suspended',
            'role' => 'required|string|exists:roles,name',

            // Validation pour la structure de documents envoyée par le formulaire
            'documents' => 'nullable|array',
            'documents.*.name' => 'nullable|string|max:255',
            'documents.*.file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
        ]);

        // 2. Préparation des données de l'utilisateur
        $temporaryPassword = \Illuminate\Support\Str::random(10);
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
            'password' => Hash::make($temporaryPassword),
            'email_verified_at' => now(),
        ];

        // 3. Gestion de l'avatar
        if ($request->hasFile('avatar')) {
            $avatarPath = $request->file('avatar')->store('avatars', 'public');
            $userData['avatar'] = $avatarPath;
        }

        // 4. Gestion des documents (Nouveau code)
        $documentsData = [];
        if ($request->has('documents')) {
            foreach ($request->input('documents') as $index => $document) {
                // Vérifier s'il y a un fichier correspondant pour cet index
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
        // You can add a permission here if you want to control who can see user details
        $user->load('roles'); // Load roles for display
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

            // Validation pour la structure de documents
            'documents' => 'nullable|array',
            'documents.*.name' => 'nullable|string|max:255',
            'documents.*.file' => 'nullable|file|mimes:pdf,doc,docx,jpg,png|max:5120',
            'documents.*.id' => 'nullable|string', // Pour identifier les documents existants
            'removed_documents' => 'nullable|array',
        ]);

        // 2. Préparation des données de l'utilisateur
        $userData = [
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'status' => $request->status,
        ];

        // Gérer la mise à jour du mot de passe si un nouveau est fourni
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }

        // Gérer l'avatar
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

        // 3. Gestion des documents (Nouveau code)
        // Ne pas utiliser json_decode(), le modèle User le fait pour toi grâce à $casts
        $updatedDocuments = $user->documents ?? [];

        // Gérer la suppression des documents existants
        if ($request->has('removed_documents')) {
            $removedPaths = $request->input('removed_documents');
            // Filtrer les documents existants pour ne garder que ceux qui ne sont pas à supprimer
            $updatedDocuments = array_filter($updatedDocuments, function ($doc) use ($removedPaths) {
                $isRemoved = in_array($doc['path'], $removedPaths);
                if ($isRemoved) {
                    // Supprimer le fichier du stockage
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
                        if ($doc['path'] === $document['id']) {
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

public function toggleStatus(Request $request, User $user)
{
    // Permission is already checked by the middleware
    $request->validate([
        'status' => 'required|in:active,inactive,suspended', // 'suspended' est déjà inclus
    ]);

    // Prevent changing status of currently authenticated user if it makes them inactive/suspended
    if (Auth::id() === $user->id && ($request->status === 'inactive' || $request->status === 'suspended')) {
        return response()->json(['success' => false, 'message' => 'Impossible de changer votre propre statut en inactif ou suspendu.'], 403);
    }
    
    try {
        $user->status = $request->status;
        $user->save();
        return response()->json(['success' => true, 'message' => 'Statut mis à jour avec succès.', 'new_status' => $user->status]);
    } catch (\Exception $e) {
        Log::error('Erreur lors de la mise à jour du statut de l\'utilisateur : ' . $e->getMessage());
        return response()->json(['success' => false, 'message' => 'Erreur lors de la mise à jour du statut.', 'error' => $e->getMessage()], 500);
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
}