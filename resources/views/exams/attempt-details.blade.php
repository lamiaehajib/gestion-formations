@extends('layouts.app')

@section('title', 'Détails de la Tentative')

@push('styles')
<style>
    /* =========================================
       ATTEMPT DETAILS - PREMIUM DESIGN
       Colors: #C2185B (primary) | #D32F2F (accent)
    ========================================= */

    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap');

    .attempt-wrapper {
        font-family: 'DM Sans', sans-serif;
        padding: 1.5rem 0;
    }

    /* ---- HERO HEADER CARD ---- */
    .attempt-hero {
        background: linear-gradient(135deg, #1a0010 0%, #3d0020 40%, #6b0030 70%, #C2185B 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 20px 60px rgba(194, 24, 91, 0.35);
    }

    .attempt-hero::before {
        content: '';
        position: absolute;
        top: -80px;
        right: -80px;
        width: 300px;
        height: 300px;
        background: radial-gradient(circle, rgba(255,255,255,0.07) 0%, transparent 70%);
        border-radius: 50%;
    }

    .attempt-hero::after {
        content: '';
        position: absolute;
        bottom: -50px;
        left: 30%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(211,47,47,0.25) 0%, transparent 70%);
        border-radius: 50%;
    }

    .attempt-hero .back-btn {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        flex-shrink: 0;
    }

    .attempt-hero .back-btn:hover {
        background: rgba(255,255,255,0.25);
        transform: translateX(-3px);
        color: white;
    }

    .attempt-hero .logs-btn {
        height: 36px;
        padding: 0 16px;
        border-radius: 20px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.25);
        color: rgba(255,255,255,0.9);
        font-size: 0.78rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        text-decoration: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        text-transform: uppercase;
    }

    .attempt-hero .logs-btn:hover {
        background: rgba(255,255,255,0.22);
        color: white;
        transform: translateY(-1px);
    }

    .attempt-hero .hero-title {
        font-family: 'DM Serif Display', serif;
        font-size: 1.6rem;
        color: white;
        margin: 0 0 4px;
        line-height: 1.2;
    }

    .attempt-hero .hero-subtitle {
        color: rgba(255,255,255,0.65);
        font-size: 0.85rem;
        margin: 0;
    }

    .attempt-hero .score-badge {
        text-align: right;
        position: relative;
        z-index: 2;
    }

    .attempt-hero .result-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 5px 14px;
        border-radius: 50px;
        font-size: 0.78rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 8px;
    }

    .result-pill.passed {
        background: rgba(16, 185, 129, 0.2);
        color: #6ee7b7;
        border: 1px solid rgba(16,185,129,0.35);
    }

    .result-pill.failed {
        background: rgba(239, 68, 68, 0.2);
        color: #fca5a5;
        border: 1px solid rgba(239,68,68,0.35);
    }

    .attempt-hero .score-number {
        font-family: 'DM Serif Display', serif;
        font-size: 3rem;
        line-height: 1;
        font-weight: 400;
    }

    .score-number.passed { color: #6ee7b7; }
    .score-number.failed { color: #fca5a5; }

    .attempt-hero .score-pts {
        color: rgba(255,255,255,0.5);
        font-size: 0.8rem;
    }

    /* ---- LAYOUT ---- */
    .attempt-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 1.5rem;
        align-items: start;
    }

    @media (max-width: 992px) {
        .attempt-grid { grid-template-columns: 1fr; }
    }

    /* ---- SECTION CARDS ---- */
    .section-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
    }

    .section-card-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .section-card-header .header-icon {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .section-card-header h5 {
        font-family: 'DM Serif Display', serif;
        font-size: 1.1rem;
        color: #1a1a2e;
        margin: 0;
    }

    .section-card-body {
        padding: 1.5rem 1.75rem;
    }

    /* ---- QUESTION CARDS ---- */
    .question-card {
        border-radius: 16px;
        margin-bottom: 1.25rem;
        border: 1.5px solid #e5e7eb;
        overflow: hidden;
        transition: box-shadow 0.3s ease;
        animation: fadeSlideUp 0.4s ease both;
    }

    .question-card:hover {
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
    }

    .question-card.correct {
        border-color: #10b981;
        border-left: 4px solid #10b981;
    }

    .question-card.incorrect {
        border-color: #ef4444;
        border-left: 4px solid #ef4444;
    }

    .question-card.unanswered {
        border-color: #f59e0b;
        border-left: 4px solid #f59e0b;
    }

    @keyframes fadeSlideUp {
        from { opacity: 0; transform: translateY(16px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .question-card:nth-child(1)  { animation-delay: 0.05s; }
    .question-card:nth-child(2)  { animation-delay: 0.10s; }
    .question-card:nth-child(3)  { animation-delay: 0.15s; }
    .question-card:nth-child(4)  { animation-delay: 0.20s; }
    .question-card:nth-child(5)  { animation-delay: 0.25s; }
    .question-card:nth-child(6)  { animation-delay: 0.30s; }
    .question-card:nth-child(n+7){ animation-delay: 0.35s; }

    .question-inner {
        padding: 1.25rem 1.5rem;
    }

    /* Question header row */
    .q-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .q-badges {
        display: flex;
        align-items: center;
        gap: 6px;
        flex-wrap: wrap;
    }

    .badge-num {
        width: 30px;
        height: 30px;
        border-radius: 8px;
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        color: white;
        font-size: 0.75rem;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .badge-type {
        padding: 3px 10px;
        border-radius: 20px;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 0.72rem;
        font-weight: 600;
        letter-spacing: 0.3px;
    }

    .badge-pts {
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .badge-pts.correct  { background: #d1fae5; color: #059669; }
    .badge-pts.incorrect{ background: #fee2e2; color: #dc2626; }

    .q-status-icon {
        font-size: 1.3rem;
        flex-shrink: 0;
    }

    /* Question text */
    .q-text {
        font-size: 0.95rem;
        font-weight: 600;
        color: #1f2937;
        margin-bottom: 0.85rem;
        line-height: 1.5;
    }

    /* Question image */
    .q-image {
        border-radius: 12px;
        margin-bottom: 0.85rem;
        overflow: hidden;
    }

    .q-image img {
        width: 100%;
        max-height: 260px;
        object-fit: cover;
    }

    /* Answer blocks */
    .answer-block {
        border-radius: 12px;
        padding: 0.85rem 1rem;
        margin-bottom: 0.6rem;
    }

    .answer-block.student-answer {
        background: #fafafa;
        border: 1px solid #e5e7eb;
    }

    .answer-block.correct-answer {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
    }

    .answer-block.feedback-block {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
    }

    .answer-label {
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 5px;
    }

    .answer-label.student  { color: #6b7280; }
    .answer-label.correct  { color: #059669; }
    .answer-label.feedback { color: #2563eb; }

    .answer-content {
        font-size: 0.9rem;
        color: #374151;
        font-weight: 500;
    }

    .answer-content ul,
    .answer-content ol {
        margin: 0;
        padding-left: 1.2rem;
    }

    /* Matching table */
    .matching-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
        font-size: 0.85rem;
    }

    .matching-table th {
        background: #f9fafb;
        padding: 6px 10px;
        font-weight: 700;
        color: #6b7280;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.4px;
        border-bottom: 1px solid #e5e7eb;
    }

    .matching-table td {
        padding: 6px 10px;
        border-bottom: 1px solid #f3f4f6;
        color: #374151;
    }

    .matching-table tr:last-child td { border-bottom: none; }

    /* ---- SUMMARY SIDEBAR ---- */
    .summary-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 24px rgba(0,0,0,0.07);
        overflow: hidden;
        border: 1px solid rgba(0,0,0,0.05);
        position: sticky;
        top: 1.5rem;
    }

    .summary-header {
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        padding: 1.25rem 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .summary-header h5 {
        font-family: 'DM Serif Display', serif;
        color: white;
        margin: 0;
        font-size: 1.05rem;
    }

    .summary-header .header-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        background: rgba(255,255,255,0.2);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.8rem;
    }

    .summary-body {
        padding: 1.25rem 1.5rem;
    }

    .summary-item {
        padding: 0.85rem 0;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        flex-direction: column;
        gap: 3px;
    }

    .summary-item:last-child { border-bottom: none; }

    .summary-label {
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #9ca3af;
    }

    .summary-value {
        font-size: 0.92rem;
        font-weight: 600;
        color: #1f2937;
    }

    .summary-score {
        font-family: 'DM Serif Display', serif;
        font-size: 2.5rem;
        line-height: 1;
    }

    .summary-score.passed { color: #059669; }
    .summary-score.failed { color: #dc2626; }

    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.75rem;
        font-weight: 700;
    }

    .status-chip.graded  { background: #d1fae5; color: #059669; }
    .status-chip.pending { background: #fef3c7; color: #d97706; }
    .status-chip.result-passed { background: #d1fae5; color: #059669; }
    .status-chip.result-failed { background: #fee2e2; color: #dc2626; }

    /* ---- PROGRESS RING (Score Visual) ---- */
    .score-ring-wrapper {
        display: flex;
        justify-content: center;
        padding: 1rem 0 0.5rem;
    }

    .score-ring-svg {
        width: 120px;
        height: 120px;
        transform: rotate(-90deg);
    }

    .score-ring-track {
        fill: none;
        stroke: #f3f4f6;
        stroke-width: 10;
    }

    .score-ring-progress {
        fill: none;
        stroke-width: 10;
        stroke-linecap: round;
        transition: stroke-dashoffset 1.5s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .score-ring-progress.passed { stroke: #10b981; }
    .score-ring-progress.failed { stroke: #ef4444; }

    .score-ring-text {
        position: absolute;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        inset: 0;
    }

    .score-ring-container {
        position: relative;
        width: 120px;
        height: 120px;
    }

    .score-ring-inner {
        position: absolute;
        inset: 0;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
    }

    .score-ring-number {
        font-family: 'DM Serif Display', serif;
        font-size: 1.5rem;
        line-height: 1;
        color: #1f2937;
    }

    .score-ring-label {
        font-size: 0.65rem;
        color: #9ca3af;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* ---- DIVIDER ---- */
    .stylish-divider {
        height: 2px;
        background: linear-gradient(90deg, #C2185B, #D32F2F, transparent);
        border-radius: 2px;
        margin: 0.5rem 0 1.5rem;
        opacity: 0.3;
    }
</style>
@endpush

@section('content')
<div class="attempt-wrapper">

    {{-- ===== HERO HEADER ===== --}}
    <div class="attempt-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('exams.attempts', $exam) }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <a href="{{ route('exams.security-logs', $attempt) }}" class="logs-btn">
                    <i class="fas fa-shield-alt"></i> Logs
                </a>
                <div>
                    <h1 class="hero-title">{{ $attempt->user->name }}</h1>
                    <p class="hero-subtitle">
                        <i class="fas fa-file-alt me-1" style="opacity:.7;"></i>
                        {{ $exam->title }} &nbsp;•&nbsp; Tentative N°{{ $attempt->attempt_number }}
                    </p>
                </div>
            </div>

            <div class="score-badge">
                <div class="result-pill {{ $attempt->passed ? 'passed' : 'failed' }}">
                    <i class="fas {{ $attempt->passed ? 'fa-check-circle' : 'fa-times-circle' }}"></i>
                    {{ $attempt->passed ? 'Réussi' : 'Échoué' }}
                </div>
                <div class="score-number {{ $attempt->passed ? 'passed' : 'failed' }}">
                    {{ round($attempt->score, 2) }}<span style="font-size:1.4rem;">%</span>
                </div>
                <div class="score-pts">{{ $attempt->total_points }}/{{ $attempt->max_points }} pts</div>
            </div>
        </div>
    </div>

    {{-- ===== MAIN GRID ===== --}}
    <div class="attempt-grid">

        {{-- ===== LEFT: QUESTIONS ===== --}}
        <div>
            <div class="section-card">
                <div class="section-card-header">
                    <div class="header-icon"><i class="fas fa-list-ol"></i></div>
                    <h5>Questions & Réponses</h5>
                </div>
                <div class="section-card-body">

                    @foreach($exam->questions as $index => $question)
                        @php
                            $userAnswer   = $attempt->answers[$question->id] ?? null;
                            $result       = $attempt->results[$question->id] ?? null;
                            $isCorrect    = $result['is_correct'] ?? false;
                            $pointsEarned = $result['points_earned'] ?? 0;
                            $cardClass    = $isCorrect ? 'correct' : ($result ? 'incorrect' : 'unanswered');
                        @endphp

                        <div class="question-card {{ $cardClass }}">
                            <div class="question-inner">

                                {{-- Header --}}
                                <div class="q-header">
                                    <div class="q-badges">
                                        <div class="badge-num">{{ $index + 1 }}</div>
                                        <span class="badge-type">{{ $question->getTypeLabel() }}</span>
                                        <span class="badge-pts {{ $isCorrect ? 'correct' : 'incorrect' }}">
                                            {{ round($pointsEarned, 2) }}/{{ $question->points }} pts
                                        </span>
                                    </div>
                                    <div class="q-status-icon">
                                        @if($isCorrect)
                                            <i class="fas fa-check-circle text-success"></i>
                                        @elseif($result)
                                            <i class="fas fa-times-circle text-danger"></i>
                                        @else
                                            <i class="fas fa-question-circle text-warning"></i>
                                        @endif
                                    </div>
                                </div>

                                {{-- Question text --}}
                                <p class="q-text">{{ $question->question_text }}</p>

                                {{-- Question image --}}
                                @if($question->question_image)
                                <div class="q-image mb-3">
                                    <img src="{{ asset('storage/' . $question->question_image) }}" alt="Question image">
                                </div>
                                @endif

                                {{-- Student Answer --}}
                                <div class="answer-block student-answer">
                                    <div class="answer-label student">
                                        <i class="fas fa-user-edit"></i> Réponse de l'étudiant
                                    </div>
                                    <div class="answer-content">
                                        @if(in_array($question->type, ['qcm', 'true_false', 'text', 'essay']))
                                            {{ $userAnswer ?? 'Aucune réponse' }}

                                        @elseif($question->type == 'checkbox')
                                            @if(is_array($userAnswer) && count($userAnswer) > 0)
                                                <ul>@foreach($userAnswer as $a)<li>{{ $a }}</li>@endforeach</ul>
                                            @else Aucune réponse @endif

                                        @elseif($question->type == 'fill_blanks')
                                            @if(is_array($userAnswer) && count($userAnswer) > 0)
                                                <ol>@foreach($userAnswer as $b)<li>{{ $b }}</li>@endforeach</ol>
                                            @else Aucune réponse @endif

                                        @elseif($question->type == 'matching')
                                            @if(is_array($userAnswer) && count($userAnswer) > 0)
                                                <table class="matching-table">
                                                    <thead><tr><th>Gauche</th><th style="text-align:center;width:30px;">→</th><th>Droite</th></tr></thead>
                                                    <tbody>
                                                        @foreach($userAnswer as $pair)
                                                        <tr>
                                                            <td class="fw-semibold">{{ $pair['left'] ?? '' }}</td>
                                                            <td style="text-align:center;color:#9ca3af;"><i class="fas fa-arrow-right"></i></td>
                                                            <td>{{ $pair['right'] ?? '' }}</td>
                                                        </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            @else Aucune réponse @endif

                                        @elseif($question->type == 'ordering')
                                            @if(is_array($userAnswer) && count($userAnswer) > 0)
                                                <ol>@foreach($userAnswer as $item)<li>{{ $item }}</li>@endforeach</ol>
                                            @else Aucune réponse @endif

                                        @elseif($question->type == 'numeric')
                                            @php $numericData = $question->getNumericData(); @endphp
                                            {{ $userAnswer ?? 'Aucune réponse' }}
                                            @if($numericData && $numericData['unit'])
                                                <span style="color:#9ca3af;">{{ $numericData['unit'] }}</span>
                                            @endif
                                        @endif
                                    </div>
                                </div>

                                {{-- Correct Answer --}}
                                @if($exam->show_correct_answers && $question->type != 'essay')
                                <div class="answer-block correct-answer">
                                    <div class="answer-label correct">
                                        <i class="fas fa-check-circle"></i> Réponse correcte
                                    </div>
                                    <div class="answer-content">
                                        @if($question->type == 'qcm')
    @php
        $correctOpt = collect($question->options)->firstWhere('is_correct', true);
    @endphp
    {{ $correctOpt['text'] ?? '—' }}

@elseif(in_array($question->type, ['true_false', 'text']))
    {{ $question->correct_answer }}

                                        @elseif($question->type == 'checkbox')
                                            <ul>
                                                @foreach($question->formatted_options as $opt)
                                                    @if($opt['is_correct'])<li>{{ $opt['text'] }}</li>@endif
                                                @endforeach
                                            </ul>

                                        @elseif($question->type == 'fill_blanks')
                                            @php $blanksData = $question->getBlanksData(); @endphp
                                            <ol>@foreach($blanksData['correct_answers'] as $blank)<li>{{ $blank }}</li>@endforeach</ol>

                                        @elseif($question->type == 'matching')
                                            @php $matchingData = $question->getMatchingData(); @endphp
                                            <table class="matching-table">
                                                <thead><tr><th>Gauche</th><th style="text-align:center;width:30px;">→</th><th>Droite</th></tr></thead>
                                                <tbody>
                                                    @foreach($matchingData['correct_pairs'] as $pair)
                                                    <tr>
                                                        <td class="fw-semibold">{{ $pair['left'] }}</td>
                                                        <td style="text-align:center;color:#059669;"><i class="fas fa-arrow-right"></i></td>
                                                        <td>{{ $pair['right'] }}</td>
                                                    </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>

                                        @elseif($question->type == 'ordering')
                                            @php $orderingData = $question->getOrderingData(); @endphp
                                            <ol>@foreach($orderingData['correct_order'] as $item)<li>{{ $item }}</li>@endforeach</ol>

                                        @elseif($question->type == 'numeric')
                                            @php $numericData = $question->getNumericData(); @endphp
                                            {{ $numericData['value'] }}
                                            @if($numericData['unit'])
                                                <span style="color:#9ca3af;">{{ $numericData['unit'] }}</span>
                                            @endif
                                            @if($numericData['tolerance'] > 0)
                                                <div style="font-size:0.78rem;color:#9ca3af;margin-top:4px;">(±{{ $numericData['tolerance'] }} accepté)</div>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                                @endif

                                {{-- Feedback --}}
                                @if(isset($result['feedback']) && $result['feedback'])
                                <div class="answer-block feedback-block">
                                    <div class="answer-label feedback">
                                        <i class="fas fa-comment-dots"></i> Feedback
                                    </div>
                                    <div class="answer-content">{{ $result['feedback'] }}</div>
                                </div>
                                @endif

                            </div>
                        </div>
                    @endforeach

                </div>
            </div>
        </div>

        {{-- ===== RIGHT: SUMMARY ===== --}}
        <div>
            <div class="summary-card">
                <div class="summary-header">
                    <div class="header-icon"><i class="fas fa-info-circle"></i></div>
                    <h5>Résumé</h5>
                </div>
                <div class="summary-body">

                    {{-- Score Ring --}}
                    <div class="score-ring-wrapper">
                        <div class="score-ring-container">
                            @php
                                $circumference = 2 * M_PI * 50;
                                $offset = $circumference - ($attempt->score / 100) * $circumference;
                            @endphp
                            <svg class="score-ring-svg" viewBox="0 0 120 120">
                                <circle class="score-ring-track" cx="60" cy="60" r="50"/>
                                <circle class="score-ring-progress {{ $attempt->passed ? 'passed' : 'failed' }}"
                                    cx="60" cy="60" r="50"
                                    stroke-dasharray="{{ $circumference }}"
                                    stroke-dashoffset="{{ $circumference }}"
                                    id="scoreRingCircle"
                                    data-offset="{{ $offset }}"
                                />
                            </svg>
                            <div class="score-ring-inner">
                                <div class="score-ring-number">{{ round($attempt->score) }}%</div>
                                <div class="score-ring-label">Score</div>
                            </div>
                        </div>
                    </div>

                    <div class="stylish-divider"></div>

                    <div class="summary-item">
                        <span class="summary-label">Statut</span>
                        <span>
                            <span class="status-chip {{ $attempt->status == 'graded' ? 'graded' : 'pending' }}">
                                <i class="fas {{ $attempt->status == 'graded' ? 'fa-check' : 'fa-clock' }}"></i>
                                {{ $attempt->getStatusLabel() }}
                            </span>
                        </span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">Commencé le</span>
                        <span class="summary-value">{{ $attempt->started_at->format('d/m/Y H:i') }}</span>
                    </div>

                    @if($attempt->submitted_at)
                    <div class="summary-item">
                        <span class="summary-label">Soumis le</span>
                        <span class="summary-value">{{ $attempt->submitted_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="summary-item">
                        <span class="summary-label">Durée</span>
                        <span class="summary-value"><i class="fas fa-stopwatch me-1" style="color:#C2185B;"></i>{{ $attempt->getDuration() }}</span>
                    </div>
                    @endif

                    <div class="summary-item">
                        <span class="summary-label">Points</span>
                        <span class="summary-value" style="font-size:1.1rem;">
                            <span style="color:{{ $attempt->passed ? '#059669' : '#dc2626' }};font-weight:700;">{{ $attempt->total_points }}</span>
                            <span style="color:#9ca3af;font-weight:400;"> / {{ $attempt->max_points }}</span>
                        </span>
                    </div>

                    <div class="summary-item">
                        <span class="summary-label">Résultat</span>
                        <span>
                            <span class="status-chip {{ $attempt->passed ? 'result-passed' : 'result-failed' }}">
                                <i class="fas {{ $attempt->passed ? 'fa-trophy' : 'fa-times-circle' }}"></i>
                                {{ $attempt->passed ? 'Réussi' : 'Échoué' }}
                            </span>
                        </span>
                        <span class="summary-label" style="margin-top:4px;">Score minimum: {{ $exam->passing_score }}%</span>
                    </div>

                </div>
            </div>
        </div>

    </div>{{-- /attempt-grid --}}
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animate score ring
    const circle = document.getElementById('scoreRingCircle');
    if (circle) {
        const targetOffset = parseFloat(circle.dataset.offset);
        // Start at full (hidden) then animate to actual value
        setTimeout(() => {
            circle.style.strokeDashoffset = targetOffset;
        }, 300);
    }

    // Stagger question cards
    const cards = document.querySelectorAll('.question-card');
    cards.forEach((card, i) => {
        card.style.animationDelay = (i * 0.07) + 's';
    });
});
</script>
@endpush