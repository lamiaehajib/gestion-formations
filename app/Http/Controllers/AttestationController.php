<?php

namespace App\Http\Controllers;

use App\Models\Attestation;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail; // Zid had la ligne
use App\Mail\AttestationReadyMail;
class AttestationController extends Controller
{
    /**
     * Afficher toutes les attestations (pour Admin)
     */
    public function index()
    {
        $attestations = Attestation::with(['user', 'inscription.formation.category', 'processedBy'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        return view('admin.attestations.index', compact('attestations'));
    }

    /**
     * Afficher les attestations de l'étudiant connecté
     */
    public function myAttestations()
    {
        $attestations = auth()->user()->attestations()
            ->with(['inscription.formation.category'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.attestations.index', compact('attestations'));
    }

    /**
     * Formulaire de demande d'attestation (pour étudiant)
     */
    public function create()
    {
        // Les catégories de formation autorisées pour la demande d'attestation
        $allowedCategories = [
            'LICENCE PROFESSIONNELLE RECONNU', 
            'Licence Professionnelle', 
            'Master Professionnelle'
        ];

        // 1. Récupérer les inscriptions de l'utilisateur avec la relation formation et category
        $inscriptions = auth()->user()->inscriptions()
            ->whereIn('status', ['active', 'completed'])
            ->with(['formation.category']) // Charger la catégorie
            ->get();
        
        // 2. Filtrer les inscriptions pour ne garder que celles qui ont une catégorie autorisée
        $inscriptions = $inscriptions->filter(function ($inscription) use ($allowedCategories) {
            // Vérifier si la formation et la catégorie existent
            if ($inscription->formation && $inscription->formation->category) {
                // Vérifier si le nom de la catégorie est dans le tableau des catégories autorisées
                return in_array($inscription->formation->category->name, $allowedCategories);
            }
            return false;
        });

        return view('student.attestations.create', compact('inscriptions'));
    }

    /**
     * Soumettre une demande d'attestation
     */
    public function store(Request $request)
    {
        $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'birth_date' => 'required|date|before:today',
        ]);

        $inscription = Inscription::findOrFail($request->inscription_id);
        
        // Vérifier que l'inscription appartient bien à l'utilisateur
        if ($inscription->user_id !== auth()->id()) {
            return back()->with('error', 'Inscription non autorisée');
        }

        // Calculer l'année académique automatiquement
        $academicYear = $this->calculateAcademicYear($inscription->inscription_date);

        $attestation = Attestation::create([
            'user_id' => auth()->id(),
            'inscription_id' => $request->inscription_id,
            'birth_date' => $request->birth_date,
            'academic_year' => $academicYear,
            'status' => 'pending',
        ]);

        return redirect()->route('student.attestations.index')
            ->with('success', 'Votre demande d\'attestation a été soumise avec succès');
    }

    /**
     * Télécharger le PDF et changer le statut en "en traitement"
     * (Pour Admin - Premier téléchargement)
     */
    public function downloadForProcessing(Attestation $attestation)
    {
        // Mettre à jour le statut si c'est la première fois
        if ($attestation->status === 'pending') {
            $attestation->update([
                'status' => 'en_traitement',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);
        }

        // Générer et télécharger le PDF
        return $this->generatePdf($attestation);
    }

    /**
     * Générer le PDF de l'attestation (style IGATE)
     */
    public function generatePdf(Attestation $attestation)
    {
        $user = $attestation->user;
        $inscription = $attestation->inscription;
        $formation = $inscription->formation;
        $category = $formation->category;

        $data = [
            'student_name' => strtoupper($user->name),
            'birth_date' => $attestation->birth_date->format('d/m/Y'),
            'nationality' => 'MAROCAINE',
            'cin' => $user->cin ?? 'N/A',
            'formation_date' => $formation->start_date->format('d/m/Y'),
            'formation_title' => strtoupper($formation->title),
            'level' => strtoupper($category->name),
            'academic_year' => $attestation->academic_year,
            'current_date' => Carbon::now()->format('d/m/Y'),
        ];

        $pdf = Pdf::loadView('pdf.attestation', $data);
        
        return $pdf->download('attestation_' . $user->name . '.pdf');
    }

    /**
     * Upload l'attestation signée et scannée
     */
    /**
     * Upload l'attestation signée et scannée
     */
    public function uploadSigned(Request $request, Attestation $attestation)
    {
        // 1. Validation de la requête
        $request->validate([
            'signed_document' => 'required|file|mimes:pdf|max:5120',
            'admin_message' => 'nullable|string|max:500',
        ]);

        if ($request->hasFile('signed_document')) {
            
            // 2. Suppression de l'ancien fichier si existe
            if ($attestation->signed_document_path) {
                Storage::disk('public')->delete($attestation->signed_document_path);
            }

            // 3. Stockage du nouveau document
            $path = $request->file('signed_document')->store('attestations', 'public');

            // 4. Mise à jour de l'Attestation
            $attestation->update([
                'signed_document_path' => $path,
                'admin_message' => $request->admin_message,
                'status' => 'termine',
                'processed_by' => auth()->id(),
                'processed_at' => now(),
            ]);

            // 5. ENVOI DE L'EMAIL À L'ÉTUDIANT
            try {
                // Charger l'utilisateur s'il n'est pas déjà chargé
                $student = $attestation->user; 
                
                // Vérifier que l'utilisateur existe et qu'il a une adresse email
                if ($student && $student->email) {
                    // Envoi de l'email
                    Mail::to($student->email)->send(new AttestationReadyMail($attestation));
                }
            } catch (\Exception $e) {
                // Optionnel: loguer l'erreur d'envoi d'email sans bloquer la réponse de l'admin
                \Log::error("Erreur lors de l'envoi de l'email pour attestation #{$attestation->id}: " . $e->getMessage());
            }

            // 6. Retour à l'Admin
            return back()->with('success', 'L\'attestation signée a été uploadée et l\'étudiant a été notifié par email.');
        }

        return back()->with('error', 'Erreur lors de l\'upload du fichier');
    }

    /**
     * Télécharger l'attestation signée (pour étudiant et admin)
     */
    public function download(Attestation $attestation)
    {
        // Vérifier les permissions
        if (auth()->user()->role === 'student' && $attestation->user_id !== auth()->id()) {
            return back()->with('error', 'Accès non autorisé');
        }

        if ($attestation->status !== 'termine' || !$attestation->signed_document_path) {
            return back()->with('error', 'L\'attestation n\'est pas encore disponible.');
        }

        $filePath = $attestation->signed_document_path;

        if (!Storage::disk('public')->exists($filePath)) {
            return back()->with('error', 'Le document n\'a pas été trouvé.');
        }

        return Storage::disk('public')->download($filePath, 'attestation_' . $attestation->user->name . '.pdf');
    }

    /**
     * Calculer l'année académique automatiquement
     */
    private function calculateAcademicYear($inscriptionDate)
    {
        $year = $inscriptionDate->year;
        $month = $inscriptionDate->month;

        if ($month >= 1 && $month <= 8) {
            return ($year - 1) . '-' . $year;
        }

        return $year . '-' . ($year + 1);
    }

    /**
     * Supprimer une demande (Admin seulement)
     */
    public function destroy(Attestation $attestation)
    {
        if ($attestation->signed_document_path) {
            Storage::disk('public')->delete($attestation->signed_document_path);
        }

        $attestation->delete();

        return back()->with('success', 'Attestation supprimée avec succès');
    }
}