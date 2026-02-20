<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamRattrapage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'original_exam_id',
        'rattrapage_exam_id',
        'created_by',
        'include_absent',
        'include_failed',
        'score_threshold',
        'notes',
    ];

    protected $casts = [
        'include_absent'  => 'boolean',
        'include_failed'  => 'boolean',
        'score_threshold' => 'decimal:2',
    ];

    // ─── Relations ────────────────────────────────────────────────────────────

    public function originalExam()
    {
        return $this->belongsTo(Exam::class, 'original_exam_id');
    }

    public function rattrapageExam()
    {
        return $this->belongsTo(Exam::class, 'rattrapage_exam_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function students()
    {
        return $this->hasMany(ExamRattrapageStudent::class, 'rattrapage_id');
    }

    public function allowedUsers()
    {
        return $this->belongsToMany(User::class, 'exam_rattrapage_students', 'rattrapage_id', 'user_id')
                    ->withPivot(['inscription_id', 'eligibility_reason', 'original_score'])
                    ->withTimestamps();
    }

    // ─── Helpers ──────────────────────────────────────────────────────────────

    /**
     * Check if a given user is allowed to take this rattrapage
     */
    public function isUserAllowed(int $userId): bool
    {
        return $this->students()->where('user_id', $userId)->exists();
    }

    /**
     * Get the student pivot record for a user
     */
    public function getStudentRecord(int $userId): ?ExamRattrapageStudent
    {
        return $this->students()->where('user_id', $userId)->first();
    }

    /**
     * Compute eligible students from the original exam (without saving)
     * Returns a collection of arrays: [user, inscription, reason, original_score]
     */
    public function computeEligibleStudents(): \Illuminate\Support\Collection
    {
        $originalExam = $this->originalExam;
        $eligible     = collect();

        // Get all active inscriptions for the module's formations
        $module = $originalExam->module;

        $inscriptions = \App\Models\Inscription::where('status', 'active')
            ->whereHas('formation.modules', function ($q) use ($module) {
                $q->where('modules.id', $module->id);
            })
            ->whereHas('formation.category', function ($q) {
                $q->whereIn('name', [
                    'Licence Professionnelle',
                    'Master Professionnelle',
                    'LICENCE PROFESSIONNELLE RECONNU',
                ]);
            })
            ->with('user')
            ->get();

        foreach ($inscriptions as $inscription) {
            $user = $inscription->user;

            // Get completed attempts for the original exam
            $attempts = \App\Models\ExamAttempt::where('exam_id', $originalExam->id)
                ->where('user_id', $user->id)
                ->whereIn('status', ['submitted', 'graded', 'timed_out'])
                ->get();

            $attemptCount = $attempts->count();
            $bestScore    = $attemptCount > 0 ? $attempts->max('score') : null;

            $reason = null;

            if ($attemptCount === 0 && $this->include_absent) {
                $reason = 'absent';
            } elseif ($attemptCount > 0 && $this->include_failed) {
                $threshold = $this->score_threshold ?? $originalExam->passing_score;
                if ($bestScore < $threshold) {
                    $reason = 'failed';
                }
            }

            if ($reason) {
                $eligible->push([
                    'user'           => $user,
                    'inscription'    => $inscription,
                    'reason'         => $reason,
                    'original_score' => $bestScore,
                ]);
            }
        }

        return $eligible;
    }
}