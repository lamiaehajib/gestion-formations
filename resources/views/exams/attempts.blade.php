@extends('layouts.app')

@section('title', 'Tentatives - ' . $exam->title)

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@300;400;500;600;700&family=DM+Serif+Display:ital@0;1&display=swap');

    .attempts-wrapper {
        font-family: 'DM Sans', sans-serif;
        padding: 1.5rem 0;
    }

    /* ---- HERO HEADER ---- */
    .attempts-hero {
        background: linear-gradient(135deg, #1a0010 0%, #3d0020 40%, #6b0030 70%, #C2185B 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 20px 60px rgba(194, 24, 91, 0.35);
    }

    .attempts-hero::before {
        content: '';
        position: absolute;
        top: -80px; right: -80px;
        width: 320px; height: 320px;
        background: radial-gradient(circle, rgba(255,255,255,0.07) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .attempts-hero::after {
        content: '';
        position: absolute;
        bottom: -60px; left: 25%;
        width: 220px; height: 220px;
        background: radial-gradient(circle, rgba(211,47,47,0.22) 0%, transparent 70%);
        border-radius: 50%;
        pointer-events: none;
    }

    .attempts-hero .back-btn {
        width: 42px; height: 42px;
        border-radius: 12px;
        background: rgba(255,255,255,0.12);
        border: 1px solid rgba(255,255,255,0.2);
        color: white;
        display: flex; align-items: center; justify-content: center;
        text-decoration: none;
        transition: all 0.3s ease;
        backdrop-filter: blur(10px);
        flex-shrink: 0;
    }

    .attempts-hero .back-btn:hover {
        background: rgba(255,255,255,0.25);
        transform: translateX(-3px);
        color: white;
    }

    .attempts-hero .hero-title {
        font-family: 'DM Serif Display', serif;
        font-size: 1.7rem;
        color: white;
        margin: 0 0 4px;
        line-height: 1.2;
    }

    .attempts-hero .hero-subtitle {
        color: rgba(255,255,255,0.62);
        font-size: 0.85rem;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .attempts-hero .hero-stat {
        text-align: right;
        position: relative;
        z-index: 2;
    }

    .attempts-hero .stat-number {
        font-family: 'DM Serif Display', serif;
        font-size: 2.8rem;
        color: white;
        line-height: 1;
    }

    .attempts-hero .stat-label {
        color: rgba(255,255,255,0.55);
        font-size: 0.8rem;
        letter-spacing: 0.5px;
        text-transform: uppercase;
        font-weight: 600;
    }

    /* ---- MAIN CARD ---- */
    .attempts-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 4px 28px rgba(0,0,0,0.08);
        border: 1px solid rgba(0,0,0,0.05);
        overflow: hidden;
    }

    .attempts-card-header {
        padding: 1.25rem 1.75rem;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .attempts-card-header .header-left {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-icon {
        width: 36px; height: 36px;
        border-radius: 10px;
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        display: flex; align-items: center; justify-content: center;
        color: white;
        font-size: 0.85rem;
        flex-shrink: 0;
    }

    .attempts-card-header h5 {
        font-family: 'DM Serif Display', serif;
        font-size: 1.1rem;
        color: #1a1a2e;
        margin: 0;
    }

    /* ---- TABLE ---- */
    .attempts-table {
        width: 100%;
        border-collapse: collapse;
    }

    .attempts-table thead tr {
        background: #fafafa;
    }

    .attempts-table thead th {
        padding: 12px 16px;
        font-size: 0.72rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        color: #9ca3af;
        border-bottom: 1px solid #f3f4f6;
        white-space: nowrap;
    }

    .attempts-table tbody tr {
        border-bottom: 1px solid #f9fafb;
        transition: background 0.2s ease;
        animation: fadeIn 0.4s ease both;
    }

    .attempts-table tbody tr:hover {
        background: #fdf2f6;
    }

    .attempts-table tbody tr:last-child {
        border-bottom: none;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(8px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .attempts-table tbody tr:nth-child(1)  { animation-delay: 0.05s; }
    .attempts-table tbody tr:nth-child(2)  { animation-delay: 0.10s; }
    .attempts-table tbody tr:nth-child(3)  { animation-delay: 0.15s; }
    .attempts-table tbody tr:nth-child(4)  { animation-delay: 0.20s; }
    .attempts-table tbody tr:nth-child(5)  { animation-delay: 0.25s; }
    .attempts-table tbody tr:nth-child(n+6){ animation-delay: 0.30s; }

    .attempts-table td {
        padding: 14px 16px;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #374151;
    }

    /* Student cell */
    .student-avatar {
        width: 38px; height: 38px;
        border-radius: 12px;
        background: linear-gradient(135deg, rgba(194,24,91,0.12), rgba(211,47,47,0.12));
        display: flex; align-items: center; justify-content: center;
        color: #C2185B;
        font-size: 0.9rem;
        flex-shrink: 0;
    }

    .student-name {
        font-weight: 600;
        color: #1f2937;
        font-size: 0.88rem;
        line-height: 1.2;
    }

    .student-email {
        font-size: 0.75rem;
        color: #9ca3af;
    }

    /* Attempt number badge */
    .attempt-num-badge {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 10px;
        border-radius: 20px;
        background: #f3f4f6;
        color: #6b7280;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 0.3px;
    }

    /* Score display */
    .score-display {
        font-family: 'DM Serif Display', serif;
        font-size: 1.3rem;
        line-height: 1;
    }

    .score-display.passed { color: #059669; }
    .score-display.failed { color: #dc2626; }

    .score-pts {
        font-size: 0.72rem;
        color: #9ca3af;
        font-family: 'DM Sans', sans-serif;
        font-weight: 500;
    }

    /* Status chips */
    .status-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.3px;
        white-space: nowrap;
    }

    .chip-info      { background: #eff6ff; color: #2563eb; }
    .chip-warning   { background: #fffbeb; color: #d97706; }
    .chip-success   { background: #f0fdf4; color: #059669; }
    .chip-danger    { background: #fef2f2; color: #dc2626; }

    /* Result chips */
    .result-chip {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 4px 12px;
        border-radius: 50px;
        font-size: 0.72rem;
        font-weight: 700;
    }

    .result-chip.passed { background: #d1fae5; color: #059669; }
    .result-chip.failed { background: #fee2e2; color: #dc2626; }

    /* Action buttons */
    .btn-view {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        color: white;
        border: none;
        text-decoration: none;
        transition: all 0.25s ease;
        letter-spacing: 0.3px;
        cursor: pointer;
    }

    .btn-view:hover {
        color: white;
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(194,24,91,0.35);
    }

    .btn-correct {
        display: inline-flex;
        align-items: center;
        gap: 5px;
        padding: 6px 14px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        background: #fffbeb;
        color: #d97706;
        border: 1.5px solid #fcd34d;
        text-decoration: none;
        transition: all 0.25s ease;
        cursor: pointer;
    }

    .btn-correct:hover {
        background: #fef3c7;
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(217,119,6,0.2);
    }

    /* Empty state */
    .empty-state {
        padding: 4rem 2rem;
        text-align: center;
    }

    .empty-icon-wrap {
        width: 80px; height: 80px;
        border-radius: 20px;
        background: linear-gradient(135deg, rgba(194,24,91,0.08), rgba(211,47,47,0.08));
        display: flex; align-items: center; justify-content: center;
        margin: 0 auto 1rem;
        font-size: 1.8rem;
        color: #C2185B;
        opacity: 0.6;
    }

    .empty-state p {
        color: #9ca3af;
        font-size: 0.9rem;
        margin: 0;
    }

    /* Pagination wrapper */
    .pagination-wrap {
        padding: 1rem 1.75rem 1.5rem;
        border-top: 1px solid #f3f4f6;
    }

    /* Divider */
    .stylish-divider {
        height: 2px;
        background: linear-gradient(90deg, #C2185B, #D32F2F, transparent);
        border-radius: 2px;
        opacity: 0.2;
        margin: 0;
    }

    /* ---- MODAL ---- */
    .grade-modal .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 25px 80px rgba(0,0,0,0.15);
        overflow: hidden;
    }

    .grade-modal .modal-header {
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        padding: 1.25rem 1.5rem;
        border: none;
    }

    .grade-modal .modal-header .modal-title {
        font-family: 'DM Serif Display', serif;
        color: white;
        font-size: 1.1rem;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .grade-modal .modal-header .btn-close {
        filter: invert(1) brightness(2);
        opacity: 0.8;
    }

    .grade-modal .modal-body {
        padding: 1.5rem;
    }

    .grade-modal .essay-card {
        border-radius: 16px;
        border: 1.5px solid #e5e7eb;
        overflow: hidden;
        margin-bottom: 1rem;
        transition: box-shadow 0.2s;
    }

    .grade-modal .essay-card:hover {
        box-shadow: 0 6px 24px rgba(0,0,0,0.08);
    }

    .grade-modal .essay-card-header {
        padding: 0.85rem 1.1rem;
        background: #fafafa;
        border-bottom: 1px solid #f3f4f6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .grade-modal .essay-q-text {
        font-weight: 600;
        font-size: 0.9rem;
        color: #1f2937;
        margin: 0;
    }

    .grade-modal .pts-badge {
        padding: 3px 10px;
        border-radius: 20px;
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        color: white;
        font-size: 0.72rem;
        font-weight: 700;
        white-space: nowrap;
    }

    .grade-modal .essay-card-body {
        padding: 1rem 1.1rem;
    }

    .grade-modal .student-answer-box {
        background: #f9fafb;
        border-radius: 10px;
        padding: 0.85rem 1rem;
        margin-bottom: 1rem;
    }

    .grade-modal .answer-label {
        font-size: 0.7rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #9ca3af;
        margin-bottom: 6px;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    .grade-modal .answer-text {
        font-size: 0.88rem;
        color: #374151;
        line-height: 1.55;
    }

    .grade-modal .points-input-wrap label {
        font-size: 0.8rem;
        font-weight: 700;
        color: #374151;
        margin-bottom: 6px;
        display: block;
    }

    .grade-modal .points-input {
        border: 2px solid #e5e7eb;
        border-radius: 10px;
        padding: 8px 14px;
        font-size: 0.9rem;
        font-weight: 600;
        color: #1f2937;
        width: 100%;
        transition: border-color 0.25s ease;
        outline: none;
    }

    .grade-modal .points-input:focus {
        border-color: #C2185B;
        box-shadow: 0 0 0 3px rgba(194,24,91,0.1);
    }

    .grade-modal .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #f3f4f6;
        gap: 8px;
    }

    .btn-cancel-modal {
        padding: 8px 20px;
        border-radius: 20px;
        border: 1.5px solid #e5e7eb;
        background: white;
        color: #6b7280;
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }

    .btn-cancel-modal:hover { background: #f9fafb; }

    .btn-save-grade {
        padding: 8px 20px;
        border-radius: 20px;
        background: linear-gradient(135deg, #059669, #047857);
        color: white;
        border: none;
        font-size: 0.82rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.25s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }

    .btn-save-grade:hover {
        transform: translateY(-1px);
        box-shadow: 0 6px 20px rgba(5,150,105,0.3);
    }

    .info-alert {
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 12px;
        padding: 10px 14px;
        font-size: 0.82rem;
        color: #2563eb;
        display: flex;
        align-items: center;
        gap: 8px;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="attempts-wrapper">

    {{-- ===== HERO ===== --}}
    <div class="attempts-hero">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('exams.show', $exam) }}" class="back-btn">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="hero-title">{{ $exam->title }}</h1>
                    <p class="hero-subtitle">
                        <i class="fas fa-clipboard-list" style="opacity:.7;"></i>
                        Liste des tentatives des étudiants
                    </p>
                </div>
            </div>
            <div class="hero-stat">
                <div class="stat-number">{{ $attempts->total() }}</div>
                <div class="stat-label">Tentative{{ $attempts->total() > 1 ? 's' : '' }}</div>
            </div>
        </div>
    </div>

    {{-- ===== MAIN TABLE CARD ===== --}}
    <div class="attempts-card">
        <div class="attempts-card-header">
            <div class="header-left">
                <div class="header-icon"><i class="fas fa-users"></i></div>
                <h5>Toutes les tentatives</h5>
            </div>
        </div>
        <div class="stylish-divider"></div>

        <div style="overflow-x:auto;">
            <table class="attempts-table">
                <thead>
                    <tr>
                        <th>Étudiant</th>
                        <th>Tentative N°</th>
                        <th>Date</th>
                        <th>Score</th>
                        <th>Statut</th>
                        <th>Résultat</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($attempts as $attempt)
                    <tr>
                        {{-- Student --}}
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="student-avatar">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div>
                                    <div class="student-name">{{ $attempt->user->name }}</div>
                                    <div class="student-email">{{ $attempt->user->email }}</div>
                                </div>
                            </div>
                        </td>

                        {{-- Attempt number --}}
                        <td>
                            <span class="attempt-num-badge">
                                <i class="fas fa-hashtag" style="font-size:.65rem;"></i>
                                {{ $attempt->attempt_number }}
                            </span>
                        </td>

                        {{-- Date --}}
                        <td>
                            <div style="font-weight:600;font-size:.85rem;color:#1f2937;">
                                {{ $attempt->started_at->format('d/m/Y') }}
                            </div>
                            <div style="font-size:.75rem;color:#9ca3af;">
                                {{ $attempt->started_at->format('H:i') }}
                            </div>
                        </td>

                        {{-- Score --}}
                        <td>
                            @if(in_array($attempt->status, ['graded', 'submitted']))
                                <div class="score-display {{ $attempt->passed ? 'passed' : 'failed' }}">
                                    {{ round($attempt->score, 2) }}%
                                </div>
                                <div class="score-pts">{{ $attempt->total_points }}/{{ $attempt->max_points }} pts</div>
                            @else
                                <span style="color:#d1d5db;font-size:.85rem;">—</span>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td>
                            @if($attempt->status == 'in_progress')
                                <span class="status-chip chip-info">
                                    <i class="fas fa-spinner fa-spin" style="font-size:.65rem;"></i>
                                    {{ $attempt->getStatusLabel() }}
                                </span>
                            @elseif($attempt->status == 'submitted')
                                <span class="status-chip chip-warning">
                                    <i class="fas fa-clock" style="font-size:.65rem;"></i>
                                    {{ $attempt->getStatusLabel() }}
                                </span>
                            @elseif($attempt->status == 'graded')
                                <span class="status-chip chip-success">
                                    <i class="fas fa-check" style="font-size:.65rem;"></i>
                                    {{ $attempt->getStatusLabel() }}
                                </span>
                            @else
                                <span class="status-chip chip-danger">
                                    <i class="fas fa-times" style="font-size:.65rem;"></i>
                                    {{ $attempt->getStatusLabel() }}
                                </span>
                            @endif
                        </td>

                        {{-- Result --}}
                        <td>
                            @if(in_array($attempt->status, ['graded', 'submitted']))
                                <span class="result-chip {{ $attempt->passed ? 'passed' : 'failed' }}">
                                    <i class="fas {{ $attempt->passed ? 'fa-trophy' : 'fa-times-circle' }}" style="font-size:.65rem;"></i>
                                    {{ $attempt->passed ? 'Réussi' : 'Échoué' }}
                                </span>
                            @else
                                <span style="color:#d1d5db;font-size:.85rem;">—</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <a href="{{ route('exams.attempt-details', $attempt) }}" class="btn-view">
                                    <i class="fas fa-eye"></i> Voir
                                </a>

                                @if($attempt->status == 'submitted' && $attempt->results)
                                    @php
                                        $hasEssay = collect($attempt->results)->contains(fn($r) =>
                                            ($r['feedback'] ?? '') === 'En attente de correction manuelle.'
                                        );
                                    @endphp
                                    @if($hasEssay)
                                    <button class="btn-correct"
                                            data-bs-toggle="modal"
                                            data-bs-target="#gradeModal{{ $attempt->id }}">
                                        <i class="fas fa-pen"></i> Corriger
                                    </button>
                                    @endif
                                @endif
                            </div>
                        </td>
                    </tr>

                    @empty
                    <tr>
                        <td colspan="7">
                            <div class="empty-state">
                                <div class="empty-icon-wrap">
                                    <i class="fas fa-clipboard-list"></i>
                                </div>
                                <p>Aucune tentative pour le moment</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attempts->hasPages())
        <div class="pagination-wrap">
            {{ $attempts->links() }}
        </div>
        @endif
    </div>

</div>
@endsection

{{-- ===== GRADING MODALS ===== --}}
@foreach($attempts as $attempt)
    @if($attempt->status == 'submitted' && $attempt->results)
        @php
            $hasEssay = collect($attempt->results)->contains(fn($r) =>
                ($r['feedback'] ?? '') === 'En attente de correction manuelle.'
            );
        @endphp
        @if($hasEssay)
        <div class="modal fade grade-modal" id="gradeModal{{ $attempt->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-pen-nib"></i>
                            Correction — {{ $attempt->user->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>

                    <form action="{{ route('exams.attempts.grade', $attempt) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="info-alert">
                                <i class="fas fa-info-circle"></i>
                                Attribuez des points aux questions de type «&nbsp;Réponse longue&nbsp;» ci-dessous.
                            </div>

                            @foreach($attempt->exam->questions as $question)
                                @if($question->type == 'essay')
                                    @php
                                        $userAnswer   = $attempt->answers[$question->id] ?? null;
                                        $currentGrade = $attempt->results[$question->id]['points_earned'] ?? 0;
                                    @endphp
                                    <div class="essay-card">
                                        <div class="essay-card-header">
                                            <p class="essay-q-text">{{ $question->question_text }}</p>
                                            <span class="pts-badge">{{ $question->points }} pts max</span>
                                        </div>
                                        <div class="essay-card-body">
                                            <div class="student-answer-box">
                                                <div class="answer-label">
                                                    <i class="fas fa-user-edit"></i> Réponse de l'étudiant
                                                </div>
                                                <div class="answer-text">{{ $userAnswer ?? 'Aucune réponse' }}</div>
                                            </div>
                                            <div class="points-input-wrap">
                                                <label>
                                                    Points attribués <span style="color:#C2185B;">*</span>
                                                    <span style="color:#9ca3af;font-weight:400;">(max {{ $question->points }})</span>
                                                </label>
                                                <input type="number"
                                                       name="grades[{{ $question->id }}]"
                                                       class="points-input"
                                                       min="0"
                                                       max="{{ $question->points }}"
                                                       step="0.5"
                                                       value="{{ $currentGrade }}"
                                                       required>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn-cancel-modal" data-bs-dismiss="modal">
                                Annuler
                            </button>
                            <button type="submit" class="btn-save-grade">
                                <i class="fas fa-check"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endif
@endforeach