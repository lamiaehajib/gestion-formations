<?php

namespace App\Http\Controllers;

use App\Models\SatisfactionSurvey;
use App\Models\Formation;
use App\Models\Inscription;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class SatisfactionSurveyController extends Controller
{
    /**
     * Affiche les formations qui nécessitent une évaluation pour l'étudiant connecté
     */
    public function index()
    {
        $user = Auth::user();
        
        // Récupère les inscriptions de l'étudiant qui sont terminées OU actives
        // et qui n'ont pas encore de sondage de satisfaction
        $inscriptions = Inscription::with('formation')
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'active']) // Formations terminées OU actives
            ->whereDoesntHave('satisfactionSurvey') // Sans sondage existant
            ->get();

        return view('satisfaction.index', compact('inscriptions'));
    }

    /**
     * Affiche le formulaire de satisfaction pour une inscription spécifique
     */
    public function create($inscriptionId)
    {
        $user = Auth::user();
        
        $inscription = Inscription::with('formation')
            ->where('id', $inscriptionId)
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Vérifie si un sondage existe déjà
        $existingSurvey = SatisfactionSurvey::where('user_id', $user->id)
            ->where('inscription_id', $inscriptionId)
            ->first();

        if ($existingSurvey) {
            return redirect()->route('satisfaction.index')
                ->with('error', 'Vous avez déjà soumis un sondage pour cette formation.');
        }

        return view('satisfaction.create', compact('inscription'));
    }

    /**
     * Enregistre le sondage de satisfaction
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'inscription_id' => 'required|exists:inscriptions,id',
            'formation_id' => 'required|exists:formations,id',
            'content_quality' => 'required|integer|min:1|max:5',
            'instructor_rating' => 'required|integer|min:1|max:5',
            'organization_rating' => 'required|integer|min:1|max:5',
            'support_rating' => 'required|integer|min:1|max:5',
            'overall_satisfaction' => 'required|integer|min:1|max:5',
            'positive_feedback' => 'nullable|string|max:1000',
            'improvement_suggestions' => 'nullable|string|max:1000',
            'additional_comments' => 'nullable|string|max:1000',
            'would_recommend' => 'required|boolean',
        ]);

        $user = Auth::user();

        // Vérifie que l'inscription appartient bien à l'utilisateur
        $inscription = Inscription::where('id', $validated['inscription_id'])
            ->where('user_id', $user->id)
            ->firstOrFail();

        // Crée le sondage
        $survey = SatisfactionSurvey::create([
            'user_id' => $user->id,
            'formation_id' => $validated['formation_id'],
            'inscription_id' => $validated['inscription_id'],
            'content_quality' => $validated['content_quality'],
            'instructor_rating' => $validated['instructor_rating'],
            'organization_rating' => $validated['organization_rating'],
            'support_rating' => $validated['support_rating'],
            'overall_satisfaction' => $validated['overall_satisfaction'],
            'positive_feedback' => $validated['positive_feedback'],
            'improvement_suggestions' => $validated['improvement_suggestions'],
            'additional_comments' => $validated['additional_comments'],
            'would_recommend' => $validated['would_recommend'],
            'status' => 'submitted',
            'submitted_at' => now(),
        ]);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Merci pour votre évaluation !',
            ]);
        }

        return redirect()->route('satisfaction.index')
            ->with('success', 'Merci pour votre évaluation !');
    }

    /**
     * Récupère les inscriptions à évaluer pour le popup (AJAX)
     */
    public function getPendingSurveys()
    {
        $user = Auth::user();
        
        $inscriptions = Inscription::with('formation')
            ->where('user_id', $user->id)
            ->whereIn('status', ['completed', 'active']) // Formations terminées OU actives
            ->whereDoesntHave('satisfactionSurvey')
            ->get()
            ->map(function($inscription) {
                return [
                    'id' => $inscription->id,
                    'formation_id' => $inscription->formation_id,
                    'formation_name' => $inscription->formation->name ?? 'Formation',
                    'completed_at' => $inscription->completed_at,
                    'status' => $inscription->status,
                ];
            });

        return response()->json([
            'success' => true,
            'inscriptions' => $inscriptions,
        ]);
    }

    /**
     * Affiche les statistiques des sondages (pour les admins)
     */
    public function statistics($formationId = null)
    {
        $query = SatisfactionSurvey::submitted();

        if ($formationId) {
            $query->forFormation($formationId);
        }

        $statistics = [
            'total_surveys' => $query->count(),
            'average_content_quality' => round($query->avg('content_quality'), 2),
            'average_instructor_rating' => round($query->avg('instructor_rating'), 2),
            'average_organization' => round($query->avg('organization_rating'), 2),
            'average_support' => round($query->avg('support_rating'), 2),
            'average_overall' => round($query->avg('overall_satisfaction'), 2),
            'recommendation_rate' => round($query->where('would_recommend', true)->count() / max($query->count(), 1) * 100, 2),
        ];

        $surveys = $query->with(['user', 'formation'])->latest()->paginate(20);

        return view('satisfaction.statistics', compact('statistics', 'surveys'));
    }


    public function formationEvaluations($formationId)
    {
        // Vérifier les permissions
        if (!Auth::user()->can('formation-view-statistics')) {
            abort(403, 'Accès non autorisé.');
        }

        $formation = Formation::with('category', 'consultant')->findOrFail($formationId);
        
        // Si c'est un consultant, vérifier qu'il est propriétaire de la formation
        if (Auth::user()->hasRole('Consultant') && $formation->consultant_id !== Auth::id()) {
            abort(403, 'Vous pouvez uniquement voir les évaluations de vos propres formations.');
        }

        // Récupérer toutes les évaluations soumises pour cette formation
        $surveys = SatisfactionSurvey::with('user')
            ->where('formation_id', $formationId)
            ->where('status', 'submitted')
            ->latest('submitted_at')
            ->paginate(15);

        // Calculer les statistiques
        $statistics = [
            'total_surveys' => $surveys->total(),
            'average_content_quality' => round(
                SatisfactionSurvey::where('formation_id', $formationId)
                    ->where('status', 'submitted')
                    ->avg('content_quality'), 
                2
            ),
            'average_instructor_rating' => round(
                SatisfactionSurvey::where('formation_id', $formationId)
                    ->where('status', 'submitted')
                    ->avg('instructor_rating'), 
                2
            ),
            'average_organization' => round(
                SatisfactionSurvey::where('formation_id', $formationId)
                    ->where('status', 'submitted')
                    ->avg('organization_rating'), 
                2
            ),
            'average_support' => round(
                SatisfactionSurvey::where('formation_id', $formationId)
                    ->where('status', 'submitted')
                    ->avg('support_rating'), 
                2
            ),
            'average_overall' => round(
                SatisfactionSurvey::where('formation_id', $formationId)
                    ->where('status', 'submitted')
                    ->avg('overall_satisfaction'), 
                2
            ),
            'would_recommend_count' => SatisfactionSurvey::where('formation_id', $formationId)
                ->where('status', 'submitted')
                ->where('would_recommend', true)
                ->count(),
            'would_not_recommend_count' => SatisfactionSurvey::where('formation_id', $formationId)
                ->where('status', 'submitted')
                ->where('would_recommend', false)
                ->count(),
        ];

        // Calculer le taux de recommandation
        $totalSurveys = $statistics['would_recommend_count'] + $statistics['would_not_recommend_count'];
        $statistics['recommendation_rate'] = $totalSurveys > 0 
            ? round(($statistics['would_recommend_count'] / $totalSurveys) * 100, 2) 
            : 0;

        return view('satisfaction.formation-evaluations', compact('formation', 'surveys', 'statistics'));
    }
}