<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExamRattrapageStudent extends Model
{
    protected $table = 'exam_rattrapage_students';

    protected $fillable = [
        'rattrapage_id',
        'user_id',
        'inscription_id',
        'eligibility_reason',
        'original_score',
    ];

    protected $casts = [
        'original_score' => 'decimal:2',
    ];

    public function rattrapage()
    {
        return $this->belongsTo(ExamRattrapage::class, 'rattrapage_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    public function getReasonLabel(): string
    {
        return match ($this->eligibility_reason) {
            'absent' => 'Absent (0 tentative)',
            'failed' => 'Échoué',
            'manual' => 'Sélection manuelle',
            default  => 'Inconnu',
        };
    }

    public function getReasonBadgeClass(): string
    {
        return match ($this->eligibility_reason) {
            'absent' => 'bg-secondary',
            'failed' => 'bg-danger',
            'manual' => 'bg-primary',
            default  => 'bg-dark',
        };
    }
}