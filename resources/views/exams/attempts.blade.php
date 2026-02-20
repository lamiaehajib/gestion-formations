
@extends('layouts.app')

@section('title', 'Tentatives - ' . $exam->title)

@section('content')
<div class="card border-0 rounded-4 shadow-hover mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('exams.show', $exam) }}" class="btn btn-light rounded-circle" style="width: 40px; height: 40px; padding: 0;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="mb-1 fw-bold" style="color: var(--primary-color);">
                    Tentatives: {{ $exam->title }}
                </h4>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-users me-1"></i>
                    {{ $attempts->total() }} tentative(s) au total
                </p>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 rounded-4 shadow-hover">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
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
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="avatar-sm bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                    <i class="fas fa-user text-primary"></i>
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $attempt->user->name }}</div>
                                    <small class="text-muted">{{ $attempt->user->email }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">Tentative {{ $attempt->attempt_number }}</span>
                        </td>
                        <td>
                            <div>{{ $attempt->started_at->format('d/m/Y') }}</div>
                            <small class="text-muted">{{ $attempt->started_at->format('H:i') }}</small>
                        </td>
                        <td>
                            @if($attempt->status == 'graded' || $attempt->status == 'submitted')
                                <div class="fw-bold fs-5 {{ $attempt->passed ? 'text-success' : 'text-danger' }}">
                                    {{ round($attempt->score, 2) }}%
                                </div>
                                <small class="text-muted">{{ $attempt->total_points }}/{{ $attempt->max_points }} pts</small>
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            @if($attempt->status == 'in_progress')
                                <span class="badge bg-info">{{ $attempt->getStatusLabel() }}</span>
                            @elseif($attempt->status == 'submitted')
                                <span class="badge bg-warning">{{ $attempt->getStatusLabel() }}</span>
                            @elseif($attempt->status == 'graded')
                                <span class="badge bg-success">{{ $attempt->getStatusLabel() }}</span>
                            @else
                                <span class="badge bg-danger">{{ $attempt->getStatusLabel() }}</span>
                            @endif
                        </td>
                        <td>
                            @if($attempt->status == 'graded' || $attempt->status == 'submitted')
                                @if($attempt->passed)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Réussi
                                    </span>
                                @else
                                    <span class="badge bg-danger">
                                        <i class="fas fa-times-circle me-1"></i>Échoué
                                    </span>
                                @endif
                            @else
                                <span class="text-muted">-</span>
                            @endif
                        </td>
                        <td>
                            <a href="{{ route('exams.attempt-details', $attempt) }}" 
                               class="btn btn-sm btn-primary rounded-pill">
                                <i class="fas fa-eye me-1"></i>Voir détails
                            </a>
                            
                            @if($attempt->status == 'submitted' && $attempt->results)
                                @php
                                    $hasEssay = collect($attempt->results)->contains(function($result) {
                                        return ($result['feedback'] ?? '') === 'En attente de correction manuelle.';
                                    });
                                @endphp
                                
                                @if($hasEssay)
                                    <button class="btn btn-sm btn-warning rounded-pill ms-1" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#gradeModal{{ $attempt->id }}">
                                        <i class="fas fa-edit me-1"></i>Corriger
                                    </button>
                                @endif
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-5">
                            <i class="fas fa-clipboard-list fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Aucune tentative pour le moment</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $attempts->links() }}
        </div>
    </div>
</div>
@endsection

<!-- Modals de Correction pour chaque attempt -->
@foreach($attempts as $attempt)
    @if($attempt->status == 'submitted' && $attempt->results)
        @php
            $hasEssay = collect($attempt->results)->contains(function($result) {
                return ($result['feedback'] ?? '') === 'En attente de correction manuelle.';
            });
        @endphp
        
        @if($hasEssay)
        <!-- Modal de correction pour attempt #{{ $attempt->id }} -->
        <div class="modal fade" id="gradeModal{{ $attempt->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-edit me-2"></i>
                            Correction Manuelle - {{ $attempt->user->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    
                    <form action="{{ route('exams.attempts.grade', $attempt) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="alert alert-info">
                                <i class="fas fa-info-circle me-2"></i>
                                Corrigez les questions de type "Réponse longue" ci-dessous
                            </div>

                            @foreach($attempt->exam->questions as $question)
                                @if($question->type == 'essay')
                                    @php
                                        $userAnswer = $attempt->answers[$question->id] ?? null;
                                        $currentGrade = $attempt->results[$question->id]['points_earned'] ?? 0;
                                    @endphp
                                    
                                    <div class="card mb-3">
                                        <div class="card-body">
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <h6 class="fw-bold">{{ $question->question_text }}</h6>
                                                <span class="badge bg-primary">{{ $question->points }} pts max</span>
                                            </div>

                                            <div class="mb-3 p-3 bg-light rounded">
                                                <div class="small text-muted mb-2">
                                                    <i class="fas fa-user-edit me-1"></i>Réponse de l'étudiant:
                                                </div>
                                                <div class="text-break">
                                                    {{ $userAnswer ?? 'Aucune réponse' }}
                                                </div>
                                            </div>

                                            <div class="mb-0">
                                                <label class="form-label fw-semibold">
                                                    Points attribués
                                                    <span class="text-danger">*</span>
                                                </label>
                                                <input type="number" 
                                                       name="grades[{{ $question->id }}]" 
                                                       class="form-control" 
                                                       min="0" 
                                                       max="{{ $question->points }}" 
                                                       step="0.5"
                                                       value="{{ $currentGrade }}"
                                                       required>
                                                <small class="text-muted">
                                                    Maximum: {{ $question->points }} points
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>

                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                Annuler
                            </button>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-check me-2"></i>
                                Enregistrer la Correction
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endif
@endforeach

@push('styles')
<style>
    .avatar-sm {
        width: 40px;
        height: 40px;
    }
</style>
@endpush