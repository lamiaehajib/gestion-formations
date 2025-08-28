<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes; // Added for completeness, if you plan to use soft deletes

class Reclamation extends Model
{
    use HasFactory, SoftDeletes; // Added SoftDeletes trait

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'formation_id',
        'category',
        'subject',
        'description',
        'status',
        'priority',
        'assigned_to',
        'response',
        'response_date',
        'satisfaction_rating',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'response_date' => 'datetime',
        'satisfaction_rating' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Available reclamation categories.
     *
     * @var array<string, string>
     */
    const CATEGORIES = [
        'paiement' => 'Paiement',
        'contenu' => 'Contenu',
        'technique' => 'Technique',
        'autre' => 'Autre'
    ];

    /**
     * Available reclamation statuses.
     *
     * @var array<string, string>
     */
    const STATUSES = [
        'ouverte' => 'Ouverte',
        'en_traitement' => 'En traitement',
        'resolue' => 'Résolue',
        'fermee' => 'Fermée'
    ];

    /**
     * Available reclamation priorities.
     *
     * @var array<string, string>
     */
    const PRIORITIES = [
        'basse' => 'Basse',
        'moyenne' => 'Moyenne',
        'haute' => 'Haute'
    ];

    /**
     * Get the user that owns the reclamation.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the formation associated with the reclamation.
     */
    public function formation()
    {
        return $this->belongsTo(Formation::class);
    }

    /**
     * Get the user to whom the reclamation is assigned.
     */
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    /**
     * Scope a query to only include reclamations of a given status.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $status
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope a query to only include reclamations of a given category.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include reclamations of a given priority.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $priority
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByPriority($query, $priority)
    {
        return $query->where('priority', $priority);
    }

    /**
     * Scope a query to only include reclamations by a given user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope a query to only include reclamations related to a given formation.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $formationId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByFormation($query, $formationId)
    {
        return $query->where('formation_id', $formationId);
    }

    /**
     * Scope a query to only include reclamations assigned to a specific user.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  int  $userId
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    /**
     * Scope a query to only include open reclamations (ouverte or en_traitement).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOpen($query)
    {
        return $query->whereIn('status', ['ouverte', 'en_traitement']);
    }

    /**
     * Scope a query to only include closed reclamations (resolue or fermee).
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeClosed($query)
    {
        return $query->whereIn('status', ['resolue', 'fermee']);
    }
}