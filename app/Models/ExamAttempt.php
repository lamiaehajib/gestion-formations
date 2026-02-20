<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ExamAttempt extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_id',
        'user_id',
        'inscription_id',
        'attempt_number',
        'started_at',
        'submitted_at',
        'time_limit_at',
        'score',
        'total_points',
        'max_points',
        'status',
        'passed',
        'answers',
        'results',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'submitted_at' => 'datetime',
        'time_limit_at' => 'datetime',
        'score' => 'decimal:2',
        'total_points' => 'decimal:2',
        'max_points' => 'decimal:2',
        'passed' => 'boolean',
        'answers' => 'array',
        'results' => 'array',
    ];

    const STATUS_IN_PROGRESS = 'in_progress';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_TIMED_OUT = 'timed_out';
    const STATUS_GRADED = 'graded';

    /**
     * Exam li khass had attempt
     */
    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * User li dar had attempt
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Inscription li khass had attempt
     */
    public function inscription()
    {
        return $this->belongsTo(Inscription::class);
    }

    /**
     * Check ila wa9t kamel
     */
    public function isTimedOut()
    {
        return Carbon::now()->gt($this->time_limit_at);
    }

    /**
     * Get remaining time f seconds
     */
    public function getRemainingTime()
    {
        if ($this->status !== self::STATUS_IN_PROGRESS) {
            return 0;
        }

        $now = Carbon::now();
        
        if ($now->gt($this->time_limit_at)) {
            return 0;
        }

        return $now->diffInSeconds($this->time_limit_at);
    }

    /**
     * Save jawab dyal user
     */
    public function saveAnswer($questionId, $answer)
    {
        $answers = $this->answers ?? [];
        $answers[$questionId] = $answer;
        
        $this->answers = $answers;
        $this->save();
    }

    /**
     * Submit exam w calculate score
     */
    public function submit()
    {
        if ($this->status !== self::STATUS_IN_PROGRESS) {
            return false;
        }

        $this->submitted_at = Carbon::now();
        
        // Check ila wa9t fayt
        if ($this->isTimedOut()) {
            $this->status = self::STATUS_TIMED_OUT;
        } else {
            $this->status = self::STATUS_SUBMITTED;
        }

        // Calculate score
        $this->calculateScore();
        
        $this->save();
        
        return true;
    }

    /**
     * Calculate score automatically
     */
    public function calculateScore()
    {
        $exam = $this->exam;
        $questions = $exam->questions;
        
        $totalPoints = 0;
        $maxPoints = 0;
        $results = [];
        $hasEssayQuestions = false;

        foreach ($questions as $question) {
            $maxPoints += $question->points;
            
            $userAnswer = $this->answers[$question->id] ?? null;
            
            // Ila essay question, khass correction manuelle
            if ($question->type === ExamQuestion::TYPE_ESSAY) {
                $hasEssayQuestions = true;
                $results[$question->id] = [
                    'is_correct' => null,
                    'points_earned' => 0,
                    'feedback' => 'En attente de correction manuelle.',
                    'user_answer' => $userAnswer,
                ];
                continue;
            }
            
            // Check jawab
            $checkResult = $question->checkAnswer($userAnswer);
            $totalPoints += $checkResult['points_earned'];
            
            $results[$question->id] = array_merge($checkResult, [
                'user_answer' => $userAnswer,
            ]);
        }

        $this->total_points = $totalPoints;
        $this->max_points = $maxPoints;
        
        // Calculate percentage score
        if ($maxPoints > 0) {
            $this->score = ($totalPoints / $maxPoints) * 100;
        } else {
            $this->score = 0;
        }
        
        $this->results = $results;
        
        // Check ila na7 wla la
        $this->passed = $this->score >= $exam->passing_score;
        
        // Ila kayn essay questions, status = submitted, gher graded
        if ($hasEssayQuestions) {
            $this->status = self::STATUS_SUBMITTED;
        } else {
            $this->status = self::STATUS_GRADED;
        }
    }

    /**
     * Manual grading (for essay questions)
     * 
     * @param array $grades - ['question_id' => points_earned]
     */
    public function gradeManually(array $grades)
    {
        $results = $this->results ?? [];
        $exam = $this->exam;
        
        $totalPoints = 0;
        $maxPoints = 0;

        foreach ($exam->questions as $question) {
            $maxPoints += $question->points;
            
            if ($question->type === ExamQuestion::TYPE_ESSAY && isset($grades[$question->id])) {
                // Update results dyal essay question
                $results[$question->id]['points_earned'] = $grades[$question->id];
                $results[$question->id]['is_correct'] = $grades[$question->id] == $question->points;
                $results[$question->id]['feedback'] = 'Corrigé manuellement.';
                
                $totalPoints += $grades[$question->id];
            } else {
                // Add points from auto-graded questions
                $totalPoints += $results[$question->id]['points_earned'] ?? 0;
            }
        }

        $this->total_points = $totalPoints;
        $this->max_points = $maxPoints;
        
        if ($maxPoints > 0) {
            $this->score = ($totalPoints / $maxPoints) * 100;
        }
        
        $this->results = $results;
        $this->passed = $this->score >= $exam->passing_score;
        $this->status = self::STATUS_GRADED;
        
        $this->save();
    }

    /**
     * Get status label
     */
    public function getStatusLabel()
    {
        return match($this->status) {
            self::STATUS_IN_PROGRESS => 'En cours',
            self::STATUS_SUBMITTED => 'Soumis',
            self::STATUS_TIMED_OUT => 'Temps écoulé',
            self::STATUS_GRADED => 'Corrigé',
            default => 'Inconnu',
        };
    }

    /**
     * Get duration f human format
     */
    public function getDuration()
    {
        if (!$this->submitted_at || !$this->started_at) {
            return null;
        }

        $minutes = $this->started_at->diffInMinutes($this->submitted_at);
        
        if ($minutes < 60) {
            return $minutes . ' min';
        }
        
        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;
        
        return $hours . 'h ' . $remainingMinutes . 'min';
    }
}