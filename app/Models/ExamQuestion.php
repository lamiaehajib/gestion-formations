<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamQuestion extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_id',
        'type',
        'question_text',
        'question_image',
        'points',
        'order',
        'options',
        'correct_answer',
        'explanation',
    ];

    protected $casts = [
        'options' => 'array',
        'points' => 'decimal:2',
        'correct_answer' => 'array', 
    ];

    const TYPE_QCM = 'qcm';
    const TYPE_TRUE_FALSE = 'true_false';
    const TYPE_TEXT = 'text';
    const TYPE_ESSAY = 'essay';
    const TYPE_CHECKBOX = 'checkbox';
    const TYPE_FILL_BLANKS = 'fill_blanks';
    const TYPE_MATCHING = 'matching';
    const TYPE_ORDERING = 'ordering';
    const TYPE_NUMERIC = 'numeric';

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    /**
     * ✅ FIXED: Check ila jawab s7i7 - CORRECTED MATCHING LOGIC
     */
    public function checkAnswer($userAnswer)
    {
        $result = [
            'is_correct' => false,
            'points_earned' => 0,
            'feedback' => '',
        ];

        switch ($this->type) {
            case self::TYPE_QCM:
                if ($userAnswer == $this->correct_answer) {
                    $result['is_correct'] = true;
                    $result['points_earned'] = $this->points;
                }
                break;

            case self::TYPE_TRUE_FALSE:
                if (strtolower($userAnswer) == strtolower($this->correct_answer)) {
                    $result['is_correct'] = true;
                    $result['points_earned'] = $this->points;
                }
                break;

            case self::TYPE_TEXT:
                if (strtolower(trim($userAnswer)) == strtolower(trim($this->correct_answer))) {
                    $result['is_correct'] = true;
                    $result['points_earned'] = $this->points;
                } else {
                    similar_text(
                        strtolower(trim($userAnswer)), 
                        strtolower(trim($this->correct_answer)), 
                        $percent
                    );
                    
                    if ($percent >= 80) {
                        $result['is_correct'] = true;
                        $result['points_earned'] = $this->points * 0.5;
                        $result['feedback'] = 'Réponse partiellement correcte.';
                    }
                }
                break;

            case self::TYPE_CHECKBOX:
                $correctOptions = collect($this->options)
                    ->filter(fn($opt) => $opt['is_correct'] ?? false)
                    ->pluck('text')
                    ->sort()
                    ->values()
                    ->toArray();

                $userAnswers = is_array($userAnswer) ? collect($userAnswer)->sort()->values()->toArray() : [];

                if ($correctOptions === $userAnswers) {
                    $result['is_correct'] = true;
                    $result['points_earned'] = $this->points;
                } else {
                    $correctCount = count(array_intersect($correctOptions, $userAnswers));
                    $totalCorrect = count($correctOptions);
                    
                    if ($correctCount > 0 && $correctCount < $totalCorrect) {
                        $result['points_earned'] = ($correctCount / $totalCorrect) * $this->points;
                        $result['feedback'] = "Partiellement correct ($correctCount/$totalCorrect réponses correctes).";
                    }
                }
                break;

            case self::TYPE_FILL_BLANKS:
                // Parse correct_answer if it's a string
                $correctAnswerData = $this->correct_answer;
                if (is_string($correctAnswerData)) {
                    $correctAnswerData = json_decode($correctAnswerData, true);
                }
                
                $correctAnswers = $correctAnswerData['blanks'] ?? [];
                $userAnswers = is_array($userAnswer) ? $userAnswer : [];
                
                $totalBlanks = count($correctAnswers);
                $correctBlanks = 0;
                
                foreach ($correctAnswers as $index => $correctBlank) {
                    $userBlank = $userAnswers[$index] ?? '';
                    
                    if (strtolower(trim($userBlank)) == strtolower(trim($correctBlank))) {
                        $correctBlanks++;
                    }
                }
                
                if ($correctBlanks == $totalBlanks) {
                    $result['is_correct'] = true;
                    $result['points_earned'] = $this->points;
                } elseif ($correctBlanks > 0) {
                    $result['points_earned'] = ($correctBlanks / $totalBlanks) * $this->points;
                    $result['feedback'] = "Partiellement correct ($correctBlanks/$totalBlanks blancs corrects).";
                }
                break;

            case self::TYPE_MATCHING:
                // ✅ FIXED: Matching pairs comparison
                
                // Parse correct_answer if it's a string
                $correctAnswerData = $this->correct_answer;
                if (is_string($correctAnswerData)) {
                    $correctAnswerData = json_decode($correctAnswerData, true);
                }
                
                $correctPairs = $correctAnswerData['pairs'] ?? [];
                $userPairs = is_array($userAnswer) ? $userAnswer : [];
                
                $totalPairs = count($correctPairs);
                
                if ($totalPairs === 0) {
                    $result['feedback'] = 'Aucune paire correcte définie.';
                    break;
                }
                
                // ✅ Create a map of correct pairs: left => right
                $correctPairsMap = [];
                foreach ($correctPairs as $pair) {
                    $left = trim($pair['left'] ?? '');
                    $right = trim($pair['right'] ?? '');
                    if ($left && $right) {
                        $correctPairsMap[$left] = $right;
                    }
                }
                
                // ✅ Count correct matches
                $correctMatches = 0;
                foreach ($userPairs as $userPair) {
                    if (!isset($userPair['left']) || !isset($userPair['right'])) {
                        continue;
                    }
                    
                    $userLeft = trim($userPair['left']);
                    $userRight = trim($userPair['right']);
                    
                    // ✅ Check if this exact pairing is correct
                    if (isset($correctPairsMap[$userLeft]) && 
                        $correctPairsMap[$userLeft] === $userRight) {
                        $correctMatches++;
                    }
                }
                
                // ✅ Calculate points
                if ($correctMatches == $totalPairs) {
                    $result['is_correct'] = true;
                    $result['points_earned'] = $this->points;
                } elseif ($correctMatches > 0) {
                    $result['points_earned'] = ($correctMatches / $totalPairs) * $this->points;
                    $result['feedback'] = "Partiellement correct ($correctMatches/$totalPairs associations correctes).";
                } else {
                    $result['feedback'] = "Aucune association correcte.";
                }
                break;

            case self::TYPE_ORDERING:
                // Parse correct_answer if it's a string
                $correctAnswerData = $this->correct_answer;
                if (is_string($correctAnswerData)) {
                    $correctAnswerData = json_decode($correctAnswerData, true);
                }
                
                $correctOrder = $correctAnswerData['items'] ?? [];
                $userOrder = is_array($userAnswer) ? $userAnswer : [];
                
                if ($correctOrder === $userOrder) {
                    $result['is_correct'] = true;
                    $result['points_earned'] = $this->points;
                } else {
                    $correctPositions = 0;
                    $totalItems = count($correctOrder);
                    
                    for ($i = 0; $i < $totalItems; $i++) {
                        if (isset($userOrder[$i]) && $userOrder[$i] === $correctOrder[$i]) {
                            $correctPositions++;
                        }
                    }
                    
                    if ($correctPositions > 0) {
                        $result['points_earned'] = ($correctPositions / $totalItems) * $this->points;
                        $result['feedback'] = "Partiellement correct ($correctPositions/$totalItems éléments bien positionnés).";
                    }
                }
                break;

            case self::TYPE_NUMERIC:
                $userValue = is_numeric($userAnswer) ? floatval($userAnswer) : null;
                
                if ($userValue === null) {
                    $result['feedback'] = 'Réponse invalide. Veuillez entrer un nombre.';
                    break;
                }
                
                // Parse correct_answer if it's a string
                $correctAnswerData = $this->correct_answer;
                if (is_string($correctAnswerData)) {
                    $correctAnswerData = json_decode($correctAnswerData, true);
                }
                
                if (is_array($correctAnswerData)) {
                    $correctValue = floatval($correctAnswerData['value'] ?? 0);
                    $tolerance = floatval($correctAnswerData['tolerance'] ?? 0);
                    
                    $difference = abs($userValue - $correctValue);
                    
                    if ($difference <= $tolerance) {
                        $result['is_correct'] = true;
                        $result['points_earned'] = $this->points;
                    }
                } else {
                    $correctValue = floatval($correctAnswerData);
                    
                    if (abs($userValue - $correctValue) < 0.001) {
                        $result['is_correct'] = true;
                        $result['points_earned'] = $this->points;
                    }
                }
                break;

            case self::TYPE_ESSAY:
                $result['feedback'] = 'Cette question nécessite une correction manuelle.';
                $result['points_earned'] = 0;
                break;
        }

        if ($result['is_correct'] && $this->explanation) {
            $result['feedback'] = $this->explanation;
        }

        return $result;
    }

    public function getFormattedOptionsAttribute()
    {
        if (!$this->options) {
            return [];
        }

        return collect($this->options)->map(function ($option, $index) {
            return [
                'id' => $index,
                'text' => $option['text'] ?? $option,
                'is_correct' => $option['is_correct'] ?? false,
            ];
        })->values()->toArray();
    }

    public function getTypeLabel()
    {
        return match($this->type) {
            self::TYPE_QCM => 'QCM (Choix unique)',
            self::TYPE_TRUE_FALSE => 'Vrai/Faux',
            self::TYPE_TEXT => 'Réponse courte',
            self::TYPE_ESSAY => 'Réponse longue',
            self::TYPE_CHECKBOX => 'Choix multiples',
            self::TYPE_FILL_BLANKS => 'Remplir les blancs',
            self::TYPE_MATCHING => 'Correspondance',
            self::TYPE_ORDERING => 'Tri/Ordre',
            self::TYPE_NUMERIC => 'Réponse numérique',
            default => 'Inconnu',
        };
    }

    public function getMatchingData()
    {
        if ($this->type !== self::TYPE_MATCHING) {
            return null;
        }

        $correctAnswer = $this->correct_answer;
        if (is_string($correctAnswer)) {
            $correctAnswer = json_decode($correctAnswer, true);
        }

        $pairs = $correctAnswer['pairs'] ?? [];
        
        if (empty($pairs)) {
            return null;
        }
        
        return [
            'left_items' => collect($pairs)->pluck('left')->values()->toArray(),
            'right_items' => collect($pairs)->pluck('right')->shuffle()->values()->toArray(),
            'correct_pairs' => $pairs,
        ];
    }

    public function getOrderingData()
    {
        if ($this->type !== self::TYPE_ORDERING) {
            return null;
        }

        $correctAnswer = $this->correct_answer;
        if (is_string($correctAnswer)) {
            $correctAnswer = json_decode($correctAnswer, true);
        }

        $items = $correctAnswer['items'] ?? [];
        
        if (empty($items)) {
            return null;
        }
        
        return [
            'correct_order' => $items,
            'shuffled_items' => collect($items)->shuffle()->values()->toArray(),
        ];
    }

    public function getBlanksData()
    {
        if ($this->type !== self::TYPE_FILL_BLANKS) {
            return null;
        }

        preg_match_all('/\[___\]/', $this->question_text, $matches);
        $blankCount = count($matches[0]);

        $correctAnswer = $this->correct_answer;
        if (is_string($correctAnswer)) {
            $correctAnswer = json_decode($correctAnswer, true);
        }

        $correctAnswers = $correctAnswer['blanks'] ?? [];

        return [
            'blank_count' => $blankCount,
            'correct_answers' => $correctAnswers,
        ];
    }

    public function getNumericData()
    {
        if ($this->type !== self::TYPE_NUMERIC) {
            return null;
        }

        $correctAnswer = $this->correct_answer;
        if (is_string($correctAnswer)) {
            $correctAnswer = json_decode($correctAnswer, true);
        }

        if (is_array($correctAnswer)) {
            return [
                'value' => $correctAnswer['value'] ?? 0,
                'tolerance' => $correctAnswer['tolerance'] ?? 0,
                'unit' => $correctAnswer['unit'] ?? '',
            ];
        }

        return [
            'value' => floatval($correctAnswer),
            'tolerance' => 0,
            'unit' => '',
        ];
    }
}