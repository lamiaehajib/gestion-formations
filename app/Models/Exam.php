<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'module_id',
        'title',
        'description',
        'duration_minutes',
        'passing_score',
        'max_attempts',
        'shuffle_questions',
        'show_results_immediately',
        'show_correct_answers',
        'available_from',
        'available_until',
        'status',
        'created_by',
    ];

    protected $casts = [
        'available_from' => 'datetime',
        'available_until' => 'datetime',
        'shuffle_questions' => 'boolean',
        'show_results_immediately' => 'boolean',
        'show_correct_answers' => 'boolean',
    ];

    /**
     * Module li khass had exam
     */
    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * Questions dyal had exam
     */
    public function questions()
    {
        return $this->hasMany(ExamQuestion::class)->orderBy('order');
    }

    /**
     * Attempts (tentatives) dyal had exam
     */
    public function attempts()
    {
        return $this->hasMany(ExamAttempt::class);
    }

    /**
     * User li dar had exam (consultant)
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check ila exam available daba
     */
    public function isAvailable()
    {
        $now = Carbon::now();
        
        if ($this->status !== 'published') {
            return false;
        }

        if ($this->available_from && $now->lt($this->available_from)) {
            return false;
        }

        if ($this->available_until && $now->gt($this->available_until)) {
            return false;
        }

        return true;
    }

    /**
     * Check ila user yemken lih idawez exam
     */
    public function canUserAttempt($userId)
    {
        if (!$this->isAvailable()) {
            return [
                'can_attempt' => false,
                'reason' => 'L\'examen n\'est pas disponible actuellement.'
            ];
        }

        // Check 3dad attempts li dar user
        $userAttemptsCount = $this->attempts()
            ->where('user_id', $userId)
            ->whereIn('status', ['submitted', 'graded', 'timed_out'])
            ->count();

        if ($userAttemptsCount >= $this->max_attempts) {
            return [
                'can_attempt' => false,
                'reason' => 'Vous avez atteint le nombre maximum de tentatives (' . $this->max_attempts . ').'
            ];
        }

        // Check ila kayn attempt en cours
        $ongoingAttempt = $this->attempts()
            ->where('user_id', $userId)
            ->where('status', 'in_progress')
            ->first();

        if ($ongoingAttempt) {
            return [
                'can_attempt' => false,
                'reason' => 'Vous avez déjà un examen en cours.',
                'ongoing_attempt' => $ongoingAttempt
            ];
        }

        return [
            'can_attempt' => true,
            'attempts_left' => $this->max_attempts - $userAttemptsCount
        ];
    }

    /**
     * Get total points fl exam
     */
    public function getTotalPointsAttribute()
    {
        return $this->questions()->sum('points');
    }

    /**
     * Get meilleur score dyal user
     */
    public function getUserBestScore($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->whereIn('status', ['submitted', 'graded'])
            ->max('score');
    }

    /**
     * Get dernière attempt dyal user
     */
    public function getUserLastAttempt($userId)
    {
        return $this->attempts()
            ->where('user_id', $userId)
            ->latest('created_at')
            ->first();
    }

    /**
     * Get statistiques dyal exam
     */
    public function getStatistics()
    {
        $attempts = $this->attempts()
            ->whereIn('status', ['submitted', 'graded'])
            ->get();

        if ($attempts->isEmpty()) {
            return [
                'total_attempts' => 0,
                'average_score' => 0,
                'pass_rate' => 0,
                'highest_score' => 0,
                'lowest_score' => 0,
            ];
        }

        $passedCount = $attempts->where('passed', true)->count();

        return [
            'total_attempts' => $attempts->count(),
            'average_score' => round($attempts->avg('score'), 2),
            'pass_rate' => round(($passedCount / $attempts->count()) * 100, 2),
            'highest_score' => $attempts->max('score'),
            'lowest_score' => $attempts->min('score'),
            'total_students' => $attempts->unique('user_id')->count(),
        ];
    }



    public function rattrapages()
    {
        return $this->hasMany(\App\Models\ExamRattrapage::class, 'original_exam_id');
    }

    /**
     * Rattrapage record where this exam IS the rattrapage exam
     */
    public function rattrapageOf()
    {
        return $this->hasOne(\App\Models\ExamRattrapage::class, 'rattrapage_exam_id');
    }

    /**
     * Is this exam a rattrapage?
     */
    public function isRattrapage(): bool
    {
        return $this->rattrapageOf()->exists();
    }

    /**
     * Scope: only normal (non-rattrapage) exams
     */
    public function scopeNormal($query)
    {
        return $query->whereDoesntHave('rattrapageOf');
    }

    /**
     * Check if user is allowed to take this exam when it IS a rattrapage.
     * Overrides canUserAttempt to also check the whitelist.
     */
    public function canUserAttemptRattrapage(int $userId): array
    {
        // First run the standard checks
        $standard = $this->canUserAttempt($userId);

        if (!$standard['can_attempt']) {
            return $standard;
        }

        // If this exam is a rattrapage, verify the whitelist
        $rattrapageRecord = $this->rattrapageOf;
        if ($rattrapageRecord) {
            $allowed = $rattrapageRecord->isUserAllowed($userId);
            if (!$allowed) {
                return [
                    'can_attempt' => false,
                    'reason'      => 'Vous n\'êtes pas autorisé à passer ce rattrapage.',
                ];
            }
        }

        return $standard;
    }
}