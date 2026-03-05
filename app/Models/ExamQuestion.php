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
        'points'  => 'decimal:2',
        // ✅ correct_answer is NOT cast — each type handles it manually
    ];

    const TYPE_QCM         = 'qcm';
    const TYPE_TRUE_FALSE  = 'true_false';
    const TYPE_TEXT        = 'text';
    const TYPE_ESSAY       = 'essay';
    const TYPE_CHECKBOX    = 'checkbox';
    const TYPE_FILL_BLANKS = 'fill_blanks';
    const TYPE_MATCHING    = 'matching';
    const TYPE_ORDERING    = 'ordering';
    const TYPE_NUMERIC     = 'numeric';

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    // ─────────────────────────────────────────────────────────────────────
    // HELPER A: for plain-string types (qcm, true_false, text)
    // Handles both raw "b" and JSON-encoded "\"b\"" (old data stored with cast)
    // ─────────────────────────────────────────────────────────────────────
   private function getCorrectAnswerString(): string
{
    $raw = $this->getRawOriginal('correct_answer');
    
    if (is_null($raw)) return '';
    
    // MySQL JSON column always stores strings with quotes: "b" → needs json_decode
    $decoded = json_decode($raw, true);
    
    // json_decode("\"b\"") = "b" (string) ✅
    // json_decode("b") = null (not valid JSON) → use raw
    return trim((string) (is_null($decoded) ? $raw : $decoded));
}

    // ─────────────────────────────────────────────────────────────────────
    // HELPER B: for JSON types (fill_blanks, matching, ordering, numeric)
    // Returns decoded array, handles double-encoding
    // ─────────────────────────────────────────────────────────────────────
    private function getCorrectAnswerArray()
    {
        $raw = $this->getRawOriginal('correct_answer');

        if (is_null($raw)) {
            return null;
        }

        $decoded = json_decode($raw, true);

        // Handle double-encoded JSON (stored when array cast was active)
        if (is_string($decoded)) {
            $decoded = json_decode($decoded, true);
        }

        return $decoded;
    }

    /**
     * ✅ FULLY FIXED checkAnswer
     */
    public function checkAnswer($userAnswer)
    {
        $result = [
            'is_correct'    => false,
            'points_earned' => 0,
            'feedback'      => '',
        ];

        switch ($this->type) {

           case self::TYPE_QCM:
    // ✅ correct_answer is stored in options (is_correct flag), not in correct_answer column
    $correctOption = collect($this->options)
        ->firstWhere('is_correct', true);
    
    $correct = $correctOption['text'] ?? '';
    
    \Log::info('QCM DEBUG v2', [
        'options'    => $this->options,
        'correct'    => $correct,
        'userAnswer' => $userAnswer,
        'match'      => (trim((string) $userAnswer) === trim($correct)),
    ]);
    
    if (trim((string) $userAnswer) === trim($correct)) {
        $result['is_correct']    = true;
        $result['points_earned'] = $this->points;
    }
    break;

            // ── TRUE / FALSE ──────────────────────────────────────────────
            case self::TYPE_TRUE_FALSE:
                $correct = $this->getCorrectAnswerString();
                if (strtolower(trim((string) $userAnswer)) === strtolower($correct)) {
                    $result['is_correct']    = true;
                    $result['points_earned'] = $this->points;
                }
                break;

            // ── TEXT ──────────────────────────────────────────────────────
            case self::TYPE_TEXT:
                $correct = $this->getCorrectAnswerString();
                if (strtolower(trim((string) $userAnswer)) === strtolower($correct)) {
                    $result['is_correct']    = true;
                    $result['points_earned'] = $this->points;
                } else {
                    similar_text(
                        strtolower(trim((string) $userAnswer)),
                        strtolower($correct),
                        $percent
                    );
                    if ($percent >= 80) {
                        $result['is_correct']    = true;
                        $result['points_earned'] = $this->points * 0.5;
                        $result['feedback']      = 'Réponse partiellement correcte.';
                    }
                }
                break;

            // ── CHECKBOX ──────────────────────────────────────────────────
            case self::TYPE_CHECKBOX:
                $correctOptions = collect($this->options)
                    ->filter(fn($opt) => $opt['is_correct'] ?? false)
                    ->pluck('text')
                    ->sort()
                    ->values()
                    ->toArray();

                $userAnswers = is_array($userAnswer)
                    ? collect($userAnswer)->sort()->values()->toArray()
                    : [];

                if ($correctOptions === $userAnswers) {
                    $result['is_correct']    = true;
                    $result['points_earned'] = $this->points;
                } else {
                    $correctCount = count(array_intersect($correctOptions, $userAnswers));
                    $totalCorrect = count($correctOptions);
                    if ($correctCount > 0 && $correctCount < $totalCorrect) {
                        $result['points_earned'] = ($correctCount / $totalCorrect) * $this->points;
                        $result['feedback']      = "Partiellement correct ($correctCount/$totalCorrect réponses correctes).";
                    }
                }
                break;

            // ── FILL BLANKS ───────────────────────────────────────────────
            case self::TYPE_FILL_BLANKS:
                $data           = $this->getCorrectAnswerArray();
                $correctAnswers = $data['blanks'] ?? [];
                $userAnswers    = is_array($userAnswer) ? $userAnswer : [];
                $totalBlanks    = count($correctAnswers);
                $correctBlanks  = 0;

                foreach ($correctAnswers as $index => $correctBlank) {
                    $userBlank = $userAnswers[$index] ?? '';
                    if (strtolower(trim($userBlank)) === strtolower(trim($correctBlank))) {
                        $correctBlanks++;
                    }
                }

                if ($totalBlanks > 0) {
                    if ($correctBlanks === $totalBlanks) {
                        $result['is_correct']    = true;
                        $result['points_earned'] = $this->points;
                    } elseif ($correctBlanks > 0) {
                        $result['points_earned'] = ($correctBlanks / $totalBlanks) * $this->points;
                        $result['feedback']      = "Partiellement correct ($correctBlanks/$totalBlanks blancs corrects).";
                    }
                }
                break;

            // ── MATCHING ──────────────────────────────────────────────────
            case self::TYPE_MATCHING:
                $data         = $this->getCorrectAnswerArray();
                $correctPairs = $data['pairs'] ?? [];
                $userPairs    = is_array($userAnswer) ? $userAnswer : [];
                $totalPairs   = count($correctPairs);

                if ($totalPairs === 0) {
                    $result['feedback'] = 'Aucune paire correcte définie.';
                    break;
                }

                $correctPairsMap = [];
                foreach ($correctPairs as $pair) {
                    $left  = trim($pair['left']  ?? '');
                    $right = trim($pair['right'] ?? '');
                    if ($left !== '' && $right !== '') {
                        $correctPairsMap[$left] = $right;
                    }
                }

                $correctMatches = 0;
                foreach ($userPairs as $userPair) {
                    if (!isset($userPair['left'], $userPair['right'])) continue;
                    $userLeft  = trim($userPair['left']);
                    $userRight = trim($userPair['right']);
                    if (isset($correctPairsMap[$userLeft]) &&
                        $correctPairsMap[$userLeft] === $userRight) {
                        $correctMatches++;
                    }
                }

                if ($correctMatches === $totalPairs) {
                    $result['is_correct']    = true;
                    $result['points_earned'] = $this->points;
                } elseif ($correctMatches > 0) {
                    $result['points_earned'] = ($correctMatches / $totalPairs) * $this->points;
                    $result['feedback']      = "Partiellement correct ($correctMatches/$totalPairs associations correctes).";
                } else {
                    $result['feedback'] = 'Aucune association correcte.';
                }
                break;

            // ── ORDERING ──────────────────────────────────────────────────
            case self::TYPE_ORDERING:
                $data         = $this->getCorrectAnswerArray();
                $correctOrder = $data['items'] ?? [];
                $userOrder    = is_array($userAnswer) ? $userAnswer : [];

                if ($correctOrder === $userOrder) {
                    $result['is_correct']    = true;
                    $result['points_earned'] = $this->points;
                } else {
                    $correctPositions = 0;
                    $totalItems       = count($correctOrder);
                    for ($i = 0; $i < $totalItems; $i++) {
                        if (isset($userOrder[$i]) && $userOrder[$i] === $correctOrder[$i]) {
                            $correctPositions++;
                        }
                    }
                    if ($correctPositions > 0) {
                        $result['points_earned'] = ($correctPositions / $totalItems) * $this->points;
                        $result['feedback']      = "Partiellement correct ($correctPositions/$totalItems éléments bien positionnés).";
                    }
                }
                break;

            // ── NUMERIC ───────────────────────────────────────────────────
            case self::TYPE_NUMERIC:
                $userValue = is_numeric($userAnswer) ? floatval($userAnswer) : null;

                if ($userValue === null) {
                    $result['feedback'] = 'Réponse invalide. Veuillez entrer un nombre.';
                    break;
                }

                $data = $this->getCorrectAnswerArray();

                if (is_array($data)) {
                    $correctValue = floatval($data['value']     ?? 0);
                    $tolerance    = floatval($data['tolerance'] ?? 0);
                    if (abs($userValue - $correctValue) <= $tolerance) {
                        $result['is_correct']    = true;
                        $result['points_earned'] = $this->points;
                    }
                } else {
                    $correctValue = floatval($this->getRawOriginal('correct_answer'));
                    if (abs($userValue - $correctValue) < 0.001) {
                        $result['is_correct']    = true;
                        $result['points_earned'] = $this->points;
                    }
                }
                break;

            // ── ESSAY ─────────────────────────────────────────────────────
            case self::TYPE_ESSAY:
                $result['feedback']      = 'Cette question nécessite une correction manuelle.';
                $result['points_earned'] = 0;
                break;
        }

        if ($result['is_correct'] && $this->explanation) {
            $result['feedback'] = $this->explanation;
        }

        return $result;
    }

    // ─────────────────────────────────────────────────────────────────────
    // ATTRIBUTE HELPERS
    // ─────────────────────────────────────────────────────────────────────

    public function getFormattedOptionsAttribute()
    {
        if (!$this->options) return [];

        return collect($this->options)->map(function ($option, $index) {
            return [
                'id'         => $index,
                'text'       => $option['text']       ?? $option,
                'is_correct' => $option['is_correct'] ?? false,
            ];
        })->values()->toArray();
    }

    public function getTypeLabel()
    {
        return match($this->type) {
            self::TYPE_QCM         => 'QCM (Choix unique)',
            self::TYPE_TRUE_FALSE  => 'Vrai/Faux',
            self::TYPE_TEXT        => 'Réponse courte',
            self::TYPE_ESSAY       => 'Réponse longue',
            self::TYPE_CHECKBOX    => 'Choix multiples',
            self::TYPE_FILL_BLANKS => 'Remplir les blancs',
            self::TYPE_MATCHING    => 'Correspondance',
            self::TYPE_ORDERING    => 'Tri/Ordre',
            self::TYPE_NUMERIC     => 'Réponse numérique',
            default                => 'Inconnu',
        };
    }

    public function getMatchingData()
    {
        if ($this->type !== self::TYPE_MATCHING) return null;

        $data  = $this->getCorrectAnswerArray();
        $pairs = $data['pairs'] ?? [];

        if (empty($pairs)) return null;

        return [
            'left_items'    => collect($pairs)->pluck('left')->values()->toArray(),
            'right_items'   => collect($pairs)->pluck('right')->shuffle()->values()->toArray(),
            'correct_pairs' => $pairs,
        ];
    }

    public function getOrderingData()
    {
        if ($this->type !== self::TYPE_ORDERING) return null;

        $data  = $this->getCorrectAnswerArray();
        $items = $data['items'] ?? [];

        if (empty($items)) return null;

        return [
            'correct_order'  => $items,
            'shuffled_items' => collect($items)->shuffle()->values()->toArray(),
        ];
    }

    public function getBlanksData()
    {
        if ($this->type !== self::TYPE_FILL_BLANKS) return null;

        preg_match_all('/\[___\]/', $this->question_text, $matches);
        $blankCount = count($matches[0]);

        $data = $this->getCorrectAnswerArray();

        return [
            'blank_count'     => $blankCount,
            'correct_answers' => $data['blanks'] ?? [],
        ];
    }

    public function getNumericData()
    {
        if ($this->type !== self::TYPE_NUMERIC) return null;

        $data = $this->getCorrectAnswerArray();

        if (is_array($data)) {
            return [
                'value'     => $data['value']     ?? 0,
                'tolerance' => $data['tolerance'] ?? 0,
                'unit'      => $data['unit']      ?? '',
            ];
        }

        return [
            'value'     => floatval($this->getRawOriginal('correct_answer')),
            'tolerance' => 0,
            'unit'      => '',
        ];
    }
}