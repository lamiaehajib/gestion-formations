<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attestation extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'inscription_id',
        'birth_date',
        'academic_year',
        'status',
        'signed_document_path',
        'admin_message',
        'processed_by',
        'processed_at',
    ];

    protected $casts = [
        'birth_date' => 'date',
        'processed_at' => 'datetime',
    ];

    /**
     * Relation avec l'étudiant
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relation avec l'inscription
     */
    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    /**
     * L'admin li dar la validation
     */
    public function processedBy()
    {
        return $this->belongsTo(User::class, 'processed_by');
    }

    /**
     * Récupérer la formation via l'inscription
     */
    public function getFormationAttribute()
    {
        return $this->inscription->formation ?? null;
    }

    /**
     * Récupérer la catégorie via l'inscription
     */
    public function getCategoryAttribute()
    {
        return $this->inscription->formation->category ?? null;
    }

    /**
     * Status badge color pour l'interface
     */
    public function getStatusBadgeAttribute()
    {
        return match($this->status) {
            'pending' => 'warning',
            'en_traitement' => 'info',
            'termine' => 'success',
            default => 'secondary',
        };
    }

    /**
     * Status label en français
     */
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'En attente',
            'en_traitement' => 'En traitement',
            'termine' => 'Terminé',
            default => 'Inconnu',
        };
    }

    /**
     * Vérifier si l'attestation peut être téléchargée
     */
    public function canBeDownloaded()
    {
        return $this->status === 'termine' && $this->signed_document_path;
    }

    /**
     * Scope pour les demandes en attente
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope pour les demandes en traitement
     */
    public function scopeEnTraitement($query)
    {
        return $query->where('status', 'en_traitement');
    }

    /**
     * Scope pour les demandes terminées
     */
    public function scopeTermine($query)
    {
        return $query->where('status', 'termine');
    }
}