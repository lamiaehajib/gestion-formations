<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class SatisfactionSurvey extends Model
{
    // Utilise les fonctionnalités de création en usine et de suppression douce (Soft Deletes)
    use HasFactory, SoftDeletes;

    /**
     * Les attributs qui peuvent être massivement assignés.
     * @var array
     */
    protected $fillable = [
        // Clés étrangères
        'user_id',          // Étudiant
        'formation_id',     // Formation
        'inscription_id',   // Inscription
        
        // Notes (1-5)
        'content_quality',
        'instructor_rating',
        'organization_rating',
        'support_rating',
        'overall_satisfaction',
        
        // Feedback
        'positive_feedback',
        'improvement_suggestions',
        'additional_comments',
        
        // Statut et Suivi
        'would_recommend',
        'status',
        'submitted_at',
        'reviewed_at',
        'reviewed_by',
    ];

    /**
     * Les attributs qui doivent être castés en types natifs.
     * @var array
     */
    protected $casts = [
        'would_recommend' => 'boolean',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    // ========================================
    // Relations (Relationships)
    // ========================================

    /**
     * Récupère l'utilisateur (étudiant) qui a soumis le sondage.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Récupère la formation associée à ce sondage.
     */
    public function formation()
    {
        // Supposant que vous avez un modèle 'Formation'
        return $this->belongsTo(Formation::class);
    }

    /**
     * Récupère l'inscription associée à ce sondage.
     */
    public function inscription()
    {
        // Supposant que vous avez un modèle 'Inscription'
        return $this->belongsTo(Inscription::class);
    }

    /**
     * Récupère l'utilisateur qui a révisé (reviewer) le sondage.
     */
    public function reviewer()
    {
        // Clé étrangère personnalisée 'reviewed_by'
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    // ========================================
    // Accesseurs & Aides (Accessors & Helpers)
    // ========================================

    /**
     * Accesseur pour calculer la note moyenne globale du sondage.
     * @return float
     */
    public function getAverageRatingAttribute()
    {
        $ratings = [
            $this->content_quality,
            $this->instructor_rating,
            $this->organization_rating,
            $this->support_rating,
            $this->overall_satisfaction,
        ];

        // Filtre les notes non nulles
        $validRatings = array_filter($ratings, fn($rating) => !is_null($rating));

        if (empty($validRatings)) {
            return 0.00;
        }

        // Calcule la moyenne et l'arrondit à 2 décimales
        return round(array_sum($validRatings) / count($validRatings), 2);
    }

    /**
     * Accesseur pour vérifier si le questionnaire a été soumis (ou révisé).
     * @return bool
     */
    public function getIsSubmittedAttribute()
    {
        return $this->status === 'submitted' || $this->status === 'reviewed';
    }

    /**
     * Accesseur pour obtenir une étiquette (label) en français pour l'état du sondage.
     * @return string
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'submitted' => 'Soumis',
            'reviewed' => 'Révisé',
            default => 'Inconnu',
        };
    }

    // ========================================
    // Scopes (Requêtes Réutilisables)
    // ========================================

    /**
     * Scope pour récupérer uniquement les sondages qui ont été soumis (ou révisés).
     */
    public function scopeSubmitted($query)
    {
        return $query->whereIn('status', ['submitted', 'reviewed']);
    }

    /**
     * Scope pour récupérer uniquement les sondages en attente de soumission.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour filtrer les sondages par ID de Formation.
     */
    public function scopeForFormation($query, $formationId)
    {
        return $query->where('formation_id', $formationId);
    }

    /**
     * Scope pour filtrer les sondages par ID d'Utilisateur (étudiant).
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}