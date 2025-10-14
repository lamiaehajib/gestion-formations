<?php

namespace App\Http\Controllers;

use App\Models\Documentation;
use App\Models\Module;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class DocumentationController extends Controller
{
    /**
     * Afficher les modules avec 100% de progression pour le consultant
     * + Ses documentations déjà soumises
     */


    public function __construct()
    {
        $this->middleware('auth');
        
        // Permissions Consultant
        $this->middleware('permission:documentation-view-own')->only(['index', 'show']);
        $this->middleware('permission:documentation-create')->only(['create', 'store']);
        $this->middleware('permission:documentation-edit')->only(['edit', 'update']);
        $this->middleware('permission:documentation-delete')->only(['destroy']);
        $this->middleware('permission:documentation-download')->only(['download']);
        
        // Permissions Admin
        $this->middleware('permission:documentation-list')->only(['adminIndex']);
        $this->middleware('permission:documentation-view')->only(['adminShow']);
        $this->middleware('permission:documentation-approve')->only(['approve']);
        $this->middleware('permission:documentation-reject')->only(['reject']);
        $this->middleware('permission:documentation-pending-count')->only(['pendingCount']);
    }
    public function index()
    {
        $consultant = Auth::user();
        
        // 1️⃣ Récupérer les modules du consultant avec 100% de progression
        $completedModules = Module::where('user_id', $consultant->id)
            ->where('progress', 100)
            ->with(['formations', 'documentations' => function($query) use ($consultant) {
                $query->where('consultant_id', $consultant->id);
            }])
            ->get();
        
        // 2️⃣ Récupérer toutes les documentations du consultant
        $documentations = Documentation::where('consultant_id', $consultant->id)
            ->with(['module', 'verifiedBy'])
            ->latest()
            ->paginate(10);

        // 3️⃣ Calculer les statistiques
        $stats = [
            'pending' => Documentation::where('consultant_id', $consultant->id)->pending()->count(),
            'approved' => Documentation::where('consultant_id', $consultant->id)->approved()->count(),
            'rejected' => Documentation::where('consultant_id', $consultant->id)->rejected()->count(),
            'total' => Documentation::where('consultant_id', $consultant->id)->count(),
            'completed_modules' => $completedModules->count(),
        ];

        return view('consultant.documentations.index', compact('completedModules', 'documentations', 'stats'));
    }

    /**
     * Afficher le formulaire de création de documentation pour un module
     */
    public function create($moduleId)
    {
        $module = Module::findOrFail($moduleId);
        $consultant = Auth::user();
        
        // ✅ Vérifier que le module appartient au consultant
        if ($module->user_id !== $consultant->id) {
            return redirect()
                ->route('consultant.documentations.index')
                ->with('error', 'Vous n\'êtes pas autorisé à soumettre une documentation pour ce module.');
        }
        
        // ✅ Vérifier que le module est à 100%
        if ($module->progress < 100) {
            return redirect()
                ->route('consultant.documentations.index')
                ->with('error', 'Vous devez compléter 100% du module avant de soumettre la documentation.');
        }
        
        // ✅ Vérifier si le consultant a déjà une documentation pour ce module
        $existingDoc = Documentation::where('module_id', $moduleId)
            ->where('consultant_id', $consultant->id)
            ->first();

        if ($existingDoc) {
            return redirect()
                ->route('consultant.documentations.show', $existingDoc->id)
                ->with('info', 'Vous avez déjà une documentation pour ce module.');
        }

        return view('consultant.documentations.create', compact('module'));
    }

    /**
     * Enregistrer une nouvelle documentation
     */
    public function store(Request $request)
    {
        $request->validate([
            'module_id' => 'required|exists:modules,id',
            'description' => 'required|string|min:20',
            'documentation_file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
            'documentation_files.*' => 'nullable|file|mimes:pdf,doc,docx,zip,jpg,jpeg,png|max:10240',
        ]);

        $consultant = Auth::user();
        
        // ✅ Vérifier que le module appartient au consultant
        $module = Module::findOrFail($request->module_id);
        if ($module->user_id !== $consultant->id) {
            return redirect()
                ->route('consultant.documentations.index')
                ->with('error', 'Vous n\'êtes pas autorisé à soumettre une documentation pour ce module.');
        }
        
        // ✅ Vérifier que le module est à 100%
        if ($module->progress < 100) {
            return redirect()
                ->route('consultant.documentations.index')
                ->with('error', 'Vous devez compléter 100% du module avant de soumettre la documentation.');
        }

        // ✅ Vérifier s'il existe déjà une documentation
        $existingDoc = Documentation::where('module_id', $request->module_id)
            ->where('consultant_id', $consultant->id)
            ->first();

        if ($existingDoc) {
            return redirect()
                ->route('consultant.documentations.show', $existingDoc->id)
                ->with('error', 'Vous avez déjà une documentation pour ce module.');
        }

        $data = [
            'module_id' => $request->module_id,
            'consultant_id' => $consultant->id,
            'description' => $request->description,
            'status' => 'pending',
        ];

        // Gérer le fichier unique
        if ($request->hasFile('documentation_file')) {
            $filePath = $request->file('documentation_file')
                ->store('documentations/' . $consultant->id, 'public');
            $data['file_path'] = $filePath;
        }

        // Gérer les fichiers multiples
        if ($request->hasFile('documentation_files')) {
            $files = [];
            foreach ($request->file('documentation_files') as $file) {
                $files[] = $file->store('documentations/' . $consultant->id, 'public');
            }
            $data['files'] = $files;
        }

        $documentation = Documentation::create($data);

        return redirect()
            ->route('consultant.documentations.index')
            ->with('success', 'Documentation soumise avec succès ! En attente de vérification par l\'administrateur.');
    }

    /**
     * Afficher une documentation spécifique
     */
    public function show($id)
    {
        $documentation = Documentation::with(['module', 'verifiedBy'])
            ->findOrFail($id);

        $user = Auth::user();
        
        // Autorisation: Admin ou Consultant propriétaire
        if (!$user->hasRole('Admin') && $documentation->consultant_id !== $user->id) {
            return redirect()
                ->route('consultant.documentations.index')
                ->with('error', 'Vous n\'êtes pas autorisé à voir cette documentation.');
        }

        return view('consultant.documentations.show', compact('documentation'));
    }

    /**
     * Afficher le formulaire de modification
     */
    public function edit($id)
    {
        $documentation = Documentation::findOrFail($id);
        $consultant = Auth::user();

        // Vérifier que c'est le consultant propriétaire
        if ($documentation->consultant_id !== $consultant->id) {
            return redirect()
                ->route('consultant.documentations.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette documentation.');
        }

        if (!$documentation->isPending()) {
            return redirect()
                ->route('consultant.documentations.show', $documentation->id)
                ->with('error', 'Impossible de modifier une documentation déjà vérifiée.');
        }

        return view('consultant.documentations.edit', compact('documentation'));
    }

    /**
     * Mettre à jour la documentation
     */
    public function update(Request $request, $id)
    {
        $documentation = Documentation::findOrFail($id);
        $consultant = Auth::user();

        // Vérifier autorisation
        if ($documentation->consultant_id !== $consultant->id) {
            return redirect()
                ->route('consultant.documentations.index')
                ->with('error', 'Vous n\'êtes pas autorisé à modifier cette documentation.');
        }

        if (!$documentation->isPending()) {
            return redirect()
                ->route('consultant.documentations.show', $documentation->id)
                ->with('error', 'Impossible de modifier une documentation déjà vérifiée.');
        }

        $request->validate([
            'description' => 'required|string|min:20',
            'documentation_file' => 'nullable|file|mimes:pdf,doc,docx,zip|max:10240',
            'documentation_files.*' => 'nullable|file|mimes:pdf,doc,docx,zip,jpg,jpeg,png|max:10240',
        ]);

        $data = [
            'description' => $request->description,
        ];

        // Mettre à jour le fichier unique
        if ($request->hasFile('documentation_file')) {
            if ($documentation->file_path) {
                Storage::disk('public')->delete($documentation->file_path);
            }
            
            $filePath = $request->file('documentation_file')
                ->store('documentations/' . $consultant->id, 'public');
            $data['file_path'] = $filePath;
            $data['files'] = null;
        }

        // Mettre à jour les fichiers multiples
        if ($request->hasFile('documentation_files')) {
            if ($documentation->files) {
                foreach ($documentation->files as $oldFile) {
                    Storage::disk('public')->delete($oldFile);
                }
            }

            $files = [];
            foreach ($request->file('documentation_files') as $file) {
                $files[] = $file->store('documentations/' . $consultant->id, 'public');
            }
            $data['files'] = $files;
            $data['file_path'] = null;
        }

        $documentation->update($data);

        return redirect()
            ->route('consultant.documentations.show', $documentation->id)
            ->with('success', 'Documentation mise à jour avec succès !');
    }

    /**
     * Supprimer la documentation
     */
    public function destroy($id)
    {
        $documentation = Documentation::findOrFail($id);
        $consultant = Auth::user();

        // Vérifier autorisation
        if ($documentation->consultant_id !== $consultant->id) {
            return redirect()
                ->route('consultant.documentations.index')
                ->with('error', 'Vous n\'êtes pas autorisé à supprimer cette documentation.');
        }

        if (!$documentation->isPending()) {
            return redirect()
                ->route('consultant.documentations.index')
                ->with('error', 'Impossible de supprimer une documentation déjà vérifiée.');
        }

        // Supprimer les fichiers
        if ($documentation->file_path) {
            Storage::disk('public')->delete($documentation->file_path);
        }
        if ($documentation->files) {
            foreach ($documentation->files as $file) {
                Storage::disk('public')->delete($file);
            }
        }

        $documentation->delete();

        return redirect()
            ->route('consultant.documentations.index')
            ->with('success', 'Documentation supprimée avec succès.');
    }

    /**
     * Télécharger le fichier de documentation
     */
    public function download($id, $fileIndex = null)
    {
        $documentation = Documentation::findOrFail($id);
        $user = Auth::user();

        // Vérifier autorisation: Admin ou Consultant propriétaire
        if (!$user->hasRole('Admin') && $documentation->consultant_id !== $user->id) {
            abort(403, 'Non autorisé');
        }
       
        // Télécharger fichier unique
        if ($fileIndex === null && $documentation->file_path) {
            return Storage::disk('public')->download($documentation->file_path);
        }

        // Télécharger fichier multiple
        if ($fileIndex !== null && $documentation->files) {
            if (isset($documentation->files[$fileIndex])) {
                return Storage::disk('public')->download($documentation->files[$fileIndex]);
            }
        }

        abort(404, 'Fichier non trouvé.');
    }

    // ========================================
    // ADMIN METHODS (inchangés)
    // ========================================

    public function adminIndex(Request $request)
    {
        $status = $request->get('status', 'all');

        $query = Documentation::with(['module', 'consultant', 'verifiedBy'])
            ->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        $documentations = $query->paginate(15);

        $stats = [
            'pending' => Documentation::pending()->count(),
            'approved' => Documentation::approved()->count(),
            'rejected' => Documentation::rejected()->count(),
            'total' => Documentation::count(),
        ];

        return view('admin.documentations.index', compact('documentations', 'stats', 'status'));
    }

    public function adminShow($id)
    {
        $documentation = Documentation::with(['module', 'consultant', 'verifiedBy'])
            ->findOrFail($id);

        return view('admin.documentations.show', compact('documentation'));
    }

    public function approve(Request $request, $id)
    {
        $request->validate([
            'admin_comment' => 'nullable|string|max:500',
        ]);

        $documentation = Documentation::findOrFail($id);

        if (!$documentation->isPending()) {
            return redirect()
                ->route('documentations.adminShow', $documentation->id)
                ->with('error', 'Cette documentation est déjà vérifiée.');
        }

        $documentation->approve(Auth::id(), $request->admin_comment);

        return redirect()
            ->route('documentations.adminShow', $documentation->id)
            ->with('success', 'Documentation approuvée avec succès !');
    }

    public function reject(Request $request, $id)
    {
        $request->validate([
            'admin_comment' => 'required|string|min:10|max:1000',
        ]);

        $documentation = Documentation::findOrFail($id);

        if (!$documentation->isPending()) {
            return redirect()
                ->route('documentations.adminShow', $documentation->id)
                ->with('error', 'Cette documentation est déjà vérifiée.');
        }

        $documentation->reject(Auth::id(), $request->admin_comment);

        return redirect()
            ->route('documentations.adminShow', $documentation->id)
            ->with('success', 'Documentation rejetée. Le consultant verra la raison.');
    }

    public function pendingCount()
    {
        $count = Documentation::pending()->count();
        return response()->json(['pending_count' => $count]);
    }
}