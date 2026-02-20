<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Module extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'title',
        'description',
        'duration_hours',
        'number_seance',
        'status',
        'content',
        'progress',
        'user_id',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Get the formations that owns the module.
     */
    public function formations() 
    {
        return $this->belongsToMany(Formation::class)->withPivot('order'); 
    }

    

    public function courses()
    {
        return $this->hasMany(Course::class);
    }

    /**
     * Get the user (consultant) that is assigned to the module.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // ========================================
    // ✨ RELATION - Documentation
    // ========================================

    /**
     * Documentation dyal had module
     */
    public function documentations()
    {
        return $this->hasMany(Documentation::class);
    }

    /**
     * Documentation pending dyal had module
     */
    public function pendingDocumentations()
    {
        return $this->hasMany(Documentation::class)->where('status', 'pending');
    }

    /**
     * Documentation approved dyal had module
     */
    public function approvedDocumentations()
    {
        return $this->hasMany(Documentation::class)->where('status', 'approved');
    }

    /**
     * Get documentation dyal consultant m3ayen
     */
    public function getConsultantDocumentation($consultantId)
    {
        return $this->documentations()->where('consultant_id', $consultantId)->first();
    }

    // ========================================
    // ✨ NOUVELLE RELATION - Exams
    // ========================================

    /**
     * Exams dyal had module
     */
    public function exams()
    {
        return $this->hasMany(Exam::class);
    }

    /**
     * Published exams dyal had module
     */
    public function publishedExams()
    {
        return $this->hasMany(Exam::class)->where('status', 'published');
    }

    /**
     * Available exams daba (published + f wa9t)
     * ✅ MODIFIER: Ghir ila progress = 100%
     */
    public function availableExams()
    {
        // Ghir ila module kamel (progress = 100%)
        if ($this->progress < 100) {
            return $this->hasMany(Exam::class)->whereRaw('1 = 0'); // Return empty
        }

        $now = \Carbon\Carbon::now();
        
        return $this->hasMany(Exam::class)
            ->where('status', 'published')
            ->where(function($query) use ($now) {
                $query->whereNull('available_from')
                      ->orWhere('available_from', '<=', $now);
            })
            ->where(function($query) use ($now) {
                $query->whereNull('available_until')
                      ->orWhere('available_until', '>=', $now);
            });
    }

    /**
     * Check ila module kamel (progress = 100%) bash n3arDou exams
     */
    public function isCompleted()
    {
        return $this->progress >= 100;
    }

    // ========================================
    // ✨ NOUVELLE MÉTHODE - Formations des catégories spécifiques
    // ========================================

    /**
     * Get formations li 3andhom les catégories: 
     * - Licence Professionnelle
     * - Master Professionnelle  
     * - LICENCE PROFESSIONNELLE RECONNU
     */
    public function eligibleFormations()
    {
        return $this->formations()
            ->whereHas('category', function($query) {
                $query->whereIn('name', [
                    'Licence Professionnelle',
                    'Master Professionnelle',
                    'LICENCE PROFESSIONNELLE RECONNU'
                ]);
            });
    }

    /**
     * ✅ SCOPE: Modules complétés (progress = 100%)
     */
    public function scopeCompleted($query)
    {
        return $query->where('progress', '>=', 100);
    }

    /**
     * ✅ SCOPE: Modules des catégories éligibles
     */
    public function scopeFromEligibleCategories($query)
    {
        return $query->whereHas('formations.category', function($q) {
            $q->whereIn('name', [
                'Licence Professionnelle',
                'Master Professionnelle',
                'LICENCE PROFESSIONNELLE RECONNU'
            ]);
        });
    }
}