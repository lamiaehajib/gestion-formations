@extends('layouts.app')

@section('title', 'Résultats de l\'Examen')

@section('content')
<div class="container">
    <!-- Result Header -->
    <div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInDown">
        <div class="card-body p-4">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h4 class="mb-2 fw-bold" style="color: var(--primary-color);">
                        <i class="fas fa-chart-line me-2"></i>
                        Résultat de l'Examen
                    </h4>
                    <p class="text-muted mb-0">{{ $exam->title }}</p>
                </div>
                <div class="col-md-4 text-end">
                    <a href="{{ route('exams.available') }}" class="btn btn-secondary rounded-pill px-4">
                        <i class="fas fa-arrow-left me-2"></i>Retour aux Examens
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Score Card -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp">
                <div class="card-body p-5 text-center">
                    @if($attempt->status == 'in_progress')
                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                            <h5>Examen en Cours</h5>
                            <p class="mb-0">Votre examen n'est pas encore terminé.</p>
                        </div>
                    @elseif($attempt->status == 'submitted' && !$attempt->score)
                        <div class="alert alert-info">
                            <i class="fas fa-clock fa-3x mb-3 text-primary"></i>
                            <h5>En Attente de Correction</h5>
                            <p class="mb-0">Votre examen contient des questions qui nécessitent une correction manuelle.</p>
                        </div>
                    @else
                        <!-- Score Display -->
                        <div class="mb-4">
                            <div class="display-1 fw-bold {{ $attempt->passed ? 'text-success' : 'text-danger' }}">
                                {{ number_format($attempt->score, 2) }}%
                            </div>
                            <p class="text-muted mb-0">Score Final</p>
                        </div>

                        <!-- Pass/Fail Badge -->
                        @if($attempt->passed)
                            <div class="mb-4">
                                <span class="badge bg-success rounded-pill px-4 py-3 fs-5">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Examen Réussi
                                </span>
                            </div>
                            <div class="alert alert-success">
                                <i class="fas fa-trophy me-2"></i>
                                <strong>Félicitations!</strong> Vous avez réussi cet examen.
                            </div>
                        @else
                            <div class="mb-4">
                                <span class="badge bg-danger rounded-pill px-4 py-3 fs-5">
                                    <i class="fas fa-times-circle me-2"></i>
                                    Examen Échoué
                                </span>
                            </div>
                            <div class="alert alert-danger">
                                <i class="fas fa-info-circle me-2"></i>
                                Score minimum requis: <strong>{{ $exam->passing_score }}%</strong>
                            </div>

                            @php
                                $canAttempt = $exam->canUserAttempt(Auth::id());
                            @endphp
                            @if($canAttempt['can_attempt'])
                                <a href="{{ route('exams.available') }}" class="btn btn-danger rounded-pill px-4">
                                    <i class="fas fa-redo me-2"></i>Réessayer
                                </a>
                            @else
                                <div class="alert alert-warning mt-3">
                                    {{ $canAttempt['reason'] }}
                                </div>
                            @endif
                        @endif

                        <!-- Points Info -->
                        <div class="row mt-4 pt-4 border-top">
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="text-muted small mb-1">Points Obtenus</div>
                                    <div class="fs-4 fw-bold text-success">{{ $attempt->total_points }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="text-muted small mb-1">Points Maximum</div>
                                    <div class="fs-4 fw-bold text-primary">{{ $attempt->max_points }}</div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="p-3 bg-light rounded-3">
                                    <div class="text-muted small mb-1">Durée</div>
                                    <div class="fs-4 fw-bold text-secondary">{{ $attempt->getDuration() }}</div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Attempt Info -->
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-4 text-danger">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations
                    </h6>

                    <div class="mb-3">
                        <div class="small text-muted mb-1">Tentative</div>
                        <div class="fw-semibold">
                            #{{ $attempt->attempt_number }} / {{ $exam->max_attempts }}
                        </div>
                    </div>

                    <div class="mb-3">
                        <div class="small text-muted mb-1">Statut</div>
                        <div class="fw-semibold">{{ $attempt->getStatusLabel() }}</div>
                    </div>

                    <div class="mb-3">
                        <div class="small text-muted mb-1">Commencé le</div>
                        <div class="fw-semibold">{{ $attempt->started_at->format('d/m/Y H:i') }}</div>
                    </div>

                    @if($attempt->submitted_at)
                    <div class="mb-3">
                        <div class="small text-muted mb-1">Soumis le</div>
                        <div class="fw-semibold">{{ $attempt->submitted_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @endif

                    <div class="mb-3">
                        <div class="small text-muted mb-1">Total Questions</div>
                        <div class="fw-semibold">{{ $exam->questions->count() }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Results -->
    @if($exam->show_results_immediately && $attempt->status == 'graded' && $attempt->results)
    <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
        <div class="card-body p-4">
            <h5 class="card-title mb-4 fw-bold text-danger">
                <i class="fas fa-clipboard-list me-2"></i>
                Résultats Détaillés
            </h5>

            @foreach($exam->questions as $index => $question)
                @php
                    $result = $attempt->results[$question->id] ?? null;
                    $userAnswer = $result['user_answer'] ?? null;
                @endphp

                <div class="card mb-3 border {{ $result['is_correct'] ? 'border-success' : 'border-danger' }}">
                    <div class="card-body p-4">
                        <!-- Question Header -->
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <span class="badge {{ $result['is_correct'] ? 'bg-success' : 'bg-danger' }} rounded-pill me-2">
                                    Question {{ $index + 1 }}
                                </span>
                                <span class="badge bg-secondary rounded-pill">
                                    {{ $question->getTypeLabel() }}
                                </span>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold {{ $result['is_correct'] ? 'text-success' : 'text-danger' }}">
                                    {{ $result['points_earned'] }} / {{ $question->points }} pts
                                </div>
                            </div>
                        </div>

                        <!-- Question Text -->
                        <div class="mb-3">
                            <h6 class="fw-semibold">{{ $question->question_text }}</h6>
                        </div>

                        <!-- Question Image -->
                        @if($question->question_image)
                        <div class="mb-3">
                            <img src="{{ asset('storage/' . $question->question_image) }}" 
                                 class="img-fluid rounded shadow-sm" 
                                 style="max-height: 300px;">
                        </div>
                        @endif

                        <!-- User Answer -->
                       <!-- User Answer -->
<div class="mb-3">
    <div class="small text-muted mb-1">Votre Réponse:</div>
    <div class="p-3 bg-light rounded-3">
        @if($question->type == 'fill_blanks')
            @if(is_array($userAnswer))
                <ol class="mb-0">
                    @foreach($userAnswer as $blank)
                        <li>{{ $blank }}</li>
                    @endforeach
                </ol>
            @else
                Aucune réponse
            @endif
        
        @elseif($question->type == 'matching')
            @if(is_array($userAnswer))
                <div class="table-responsive">
                    <table class="table table-sm mb-0">
                        @foreach($userAnswer as $pair)
                            <tr>
                                <td class="fw-semibold">{{ $pair['left'] ?? '' }}</td>
                                <td class="text-center"><i class="fas fa-arrow-right text-muted"></i></td>
                                <td>{{ $pair['right'] ?? '' }}</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            @else
                Aucune réponse
            @endif
        
        @elseif($question->type == 'ordering')
            @if(is_array($userAnswer))
                <ol class="mb-0">
                    @foreach($userAnswer as $item)
                        <li>{{ $item }}</li>
                    @endforeach
                </ol>
            @else
                Aucune réponse
            @endif
        
        @elseif($question->type == 'numeric')
            {{ $userAnswer ?? 'Aucune réponse' }}
            @php
                $numericData = $question->getNumericData();
            @endphp
            @if($numericData && $numericData['unit'])
                <span class="text-muted">{{ $numericData['unit'] }}</span>
            @endif
        
        @elseif($question->type == 'checkbox')
            @if(is_array($userAnswer) && count($userAnswer) > 0)
                <ul class="mb-0">
                    @foreach($userAnswer as $ans)
                        <li>{{ $ans }}</li>
                    @endforeach
                </ul>
            @else
                Aucune réponse
            @endif
        
        @else
            {{ $userAnswer ?? 'Aucune réponse' }}
        @endif
    </div>
</div>

<!-- Correct Answer (if show_correct_answers is enabled) -->
@if($exam->show_correct_answers && !$result['is_correct'])
<div class="mb-3">
    <div class="small text-muted mb-1">Réponse Correcte:</div>
    <div class="p-3 bg-success bg-opacity-10 border border-success rounded-3">
        @if($question->type == 'qcm' || $question->type == 'checkbox')
            @foreach($question->formatted_options as $opt)
                @if($opt['is_correct'])
                    <div class="mb-1">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        {{ $opt['text'] }}
                    </div>
                @endif
            @endforeach
        
        @elseif($question->type == 'fill_blanks')
            @php
                $blanksData = $question->getBlanksData();
            @endphp
            <ol class="mb-0">
                @foreach($blanksData['correct_answers'] as $blank)
                    <li>{{ $blank }}</li>
                @endforeach
            </ol>
        
        @elseif($question->type == 'matching')
            @php
                $matchingData = $question->getMatchingData();
            @endphp
            <div class="table-responsive">
                <table class="table table-sm mb-0">
                    @foreach($matchingData['correct_pairs'] as $pair)
                        <tr>
                            <td class="fw-semibold">{{ $pair['left'] }}</td>
                            <td class="text-center"><i class="fas fa-arrow-right text-success"></i></td>
                            <td>{{ $pair['right'] }}</td>
                        </tr>
                    @endforeach
                </table>
            </div>
        
        @elseif($question->type == 'ordering')
            @php
                $orderingData = $question->getOrderingData();
            @endphp
            <ol class="mb-0">
                @foreach($orderingData['correct_order'] as $item)
                    <li>{{ $item }}</li>
                @endforeach
            </ol>
        
        @elseif($question->type == 'numeric')
            @php
                $numericData = $question->getNumericData();
            @endphp
            {{ $numericData['value'] }}
            @if($numericData['unit'])
                <span class="text-muted">{{ $numericData['unit'] }}</span>
            @endif
            @if($numericData['tolerance'] > 0)
                <div class="small text-muted mt-1">
                    (±{{ $numericData['tolerance'] }} accepté)
                </div>
            @endif
        
        @else
            {{ $question->correct_answer }}
        @endif
    </div>
</div>
@endif

                        {{-- <!-- Correct Answer (if show_correct_answers is enabled) -->
                        @if($exam->show_correct_answers && !$result['is_correct'])
                        <div class="mb-3">
                            <div class="small text-muted mb-1">Réponse Correcte:</div>
                            <div class="p-3 bg-success bg-opacity-10 border border-success rounded-3">
                                @if($question->type == 'qcm' || $question->type == 'checkbox')
                                    @foreach($question->formatted_options as $opt)
                                        @if($opt['is_correct'])
                                            <div class="mb-1">
                                                <i class="fas fa-check-circle text-success me-2"></i>
                                                {{ $opt['text'] }}
                                            </div>
                                        @endif
                                    @endforeach
                                @else
                                    {{ $question->correct_answer }}
                                @endif
                            </div>
                        </div>
                        @endif --}}

                        <!-- Explanation/Feedback -->
                        @if($result['feedback'])
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Explication:</strong> {{ $result['feedback'] }}
                        </div>
                        @elseif($question->explanation && $exam->show_correct_answers)
                        <div class="alert alert-info mb-0">
                            <i class="fas fa-lightbulb me-2"></i>
                            <strong>Explication:</strong> {{ $question->explanation }}
                        </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    @elseif(!$exam->show_results_immediately)
        <div class="card border-0 rounded-4 shadow-hover">
            <div class="card-body p-4 text-center">
                <i class="fas fa-eye-slash fa-3x text-muted mb-3"></i>
                <h5 class="text-muted">Résultats Détaillés Masqués</h5>
                <p class="text-muted mb-0">
                    L'enseignant a choisi de ne pas afficher les résultats détaillés immédiatement.
                </p>
            </div>
        </div>
    @endif

    <!-- Action Buttons -->
    <div class="text-center mt-4">
        <a href="{{ route('exams.my-attempts') }}" class="btn btn-secondary rounded-pill px-4 me-2">
            <i class="fas fa-history me-2"></i>Voir Toutes mes Tentatives
        </a>
        <a href="{{ route('exams.available') }}" class="btn btn-danger rounded-pill px-4">
            <i class="fas fa-arrow-left me-2"></i>Retour aux Examens
        </a>
    </div>
</div>
@endsection

@push('styles')
<style>
    .display-1 {
        font-size: 5rem;
        animation: scaleIn 0.5s ease-out;
    }

    @keyframes scaleIn {
        0% {
            transform: scale(0);
            opacity: 0;
        }
        100% {
            transform: scale(1);
            opacity: 1;
        }
    }

    .badge {
        font-weight: 600;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confetti animation on pass
        @if($attempt->passed && $attempt->status == 'graded')
            Swal.fire({
                title: 'Félicitations! 🎉',
                text: 'Vous avez réussi l\'examen avec {{ number_format($attempt->score, 2) }}%',
                icon: 'success',
                confirmButtonColor: '#10b981',
                confirmButtonText: 'Génial!'
            });
        @endif
    });
</script>
@endpush