@extends('layouts.app')

@section('title', 'Détails de la Tentative')

@section('content')
<div class="card border-0 rounded-4 shadow-hover mb-4">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('exams.attempts', $exam) }}" class="btn btn-light rounded-circle" style="width: 40px; height: 40px; padding: 0;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <a href="{{ route('exams.security-logs', $attempt) }}" 
   class="btn btn-sm btn-secondary rounded-pill ms-1">
    <i class="fas fa-shield-alt me-1"></i>Logs
</a>
                <div>
                    <h4 class="mb-1 fw-bold" style="color: var(--primary-color);">
                        Tentative de {{ $attempt->user->name }}
                    </h4>
                    <p class="text-muted mb-0 small">
                        {{ $exam->title }} - Tentative N°{{ $attempt->attempt_number }}
                    </p>
                </div>
            </div>
            
            <div class="text-end">
                @if($attempt->passed)
                    <span class="badge bg-success fs-6 mb-2">
                        <i class="fas fa-check-circle me-1"></i>Réussi
                    </span>
                @else
                    <span class="badge bg-danger fs-6 mb-2">
                        <i class="fas fa-times-circle me-1"></i>Échoué
                    </span>
                @endif
                <div class="fw-bold fs-3 {{ $attempt->passed ? 'text-success' : 'text-danger' }}">
                    {{ round($attempt->score, 2) }}%
                </div>
                <small class="text-muted">{{ $attempt->total_points }}/{{ $attempt->max_points }} points</small>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <!-- Questions & Answers -->
        <div class="card border-0 rounded-4 shadow-hover">
            <div class="card-body p-4">
                <h5 class="card-title mb-4 fw-bold text-danger">
                    <i class="fas fa-list-ol me-2"></i>
                    Questions & Réponses
                </h5>

                @foreach($exam->questions as $index => $question)
                    @php
                        $userAnswer = $attempt->answers[$question->id] ?? null;
                        $result = $attempt->results[$question->id] ?? null;
                        $isCorrect = $result['is_correct'] ?? false;
                        $pointsEarned = $result['points_earned'] ?? 0;
                    @endphp

                    <div class="card mb-3 border {{ $isCorrect ? 'border-success' : ($result ? 'border-danger' : 'border-warning') }}">
                        <div class="card-body p-3">
                            <!-- Question Header -->
                            <div class="d-flex justify-content-between align-items-start mb-3">
                                <div>
                                    <span class="badge bg-danger rounded-pill me-2">Q{{ $index + 1 }}</span>
                                    <span class="badge bg-secondary rounded-pill me-2">{{ $question->getTypeLabel() }}</span>
                                    <span class="badge {{ $isCorrect ? 'bg-success' : 'bg-danger' }} rounded-pill">
                                        {{ round($pointsEarned, 2) }}/{{ $question->points }} pts
                                    </span>
                                </div>
                                <div>
                                    @if($isCorrect)
                                        <i class="fas fa-check-circle text-success fa-2x"></i>
                                    @elseif($result && !$isCorrect)
                                        <i class="fas fa-times-circle text-danger fa-2x"></i>
                                    @else
                                        <i class="fas fa-question-circle text-warning fa-2x"></i>
                                    @endif
                                </div>
                            </div>

                            <!-- Question Text -->
                            <p class="mb-3 fw-semibold">{{ $question->question_text }}</p>

                            <!-- Question Image -->
                            @if($question->question_image)
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $question->question_image) }}" 
                                     class="img-fluid rounded shadow-sm" 
                                     style="max-height: 300px;">
                            </div>
                            @endif

                            <!-- User Answer -->
                            <div class="mb-3 p-3 bg-light rounded-3">
                                <div class="small text-muted mb-2">
                                    <i class="fas fa-user-edit me-1"></i>Réponse de l'étudiant:
                                </div>
                                <div class="fw-semibold">
                                    @if($question->type == 'qcm' || $question->type == 'true_false' || $question->type == 'text')
                                        {{ $userAnswer ?? 'Aucune réponse' }}
                                    
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
                                    
                                    @elseif($question->type == 'essay')
                                        <div class="text-break">{{ $userAnswer ?? 'Aucune réponse' }}</div>
                                    
                                    @elseif($question->type == 'fill_blanks')
                                        @if(is_array($userAnswer) && count($userAnswer) > 0)
                                            <ol class="mb-0">
                                                @foreach($userAnswer as $blank)
                                                    <li>{{ $blank }}</li>
                                                @endforeach
                                            </ol>
                                        @else
                                            Aucune réponse
                                        @endif
                                    
                                    @elseif($question->type == 'matching')
                                        @if(is_array($userAnswer) && count($userAnswer) > 0)
                                            <div class="table-responsive">
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Gauche</th>
                                                            <th class="text-center">→</th>
                                                            <th>Droite</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($userAnswer as $pair)
                                                            <tr>
                                                                <td class="fw-semibold">{{ $pair['left'] ?? '' }}</td>
                                                                <td class="text-center"><i class="fas fa-arrow-right text-muted"></i></td>
                                                                <td>{{ $pair['right'] ?? '' }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
                                                </table>
                                            </div>
                                        @else
                                            Aucune réponse
                                        @endif
                                    
                                    @elseif($question->type == 'ordering')
                                        @if(is_array($userAnswer) && count($userAnswer) > 0)
                                            <ol class="mb-0">
                                                @foreach($userAnswer as $item)
                                                    <li>{{ $item }}</li>
                                                @endforeach
                                            </ol>
                                        @else
                                            Aucune réponse
                                        @endif
                                    
                                    @elseif($question->type == 'numeric')
                                        @php
                                            $numericData = $question->getNumericData();
                                        @endphp
                                        {{ $userAnswer ?? 'Aucune réponse' }}
                                        @if($numericData && $numericData['unit'])
                                            <span class="text-muted">{{ $numericData['unit'] }}</span>
                                        @endif
                                    @endif
                                </div>
                            </div>

                            <!-- Correct Answer (if allowed) -->
                            @if($exam->show_correct_answers && $question->type != 'essay')
                                <div class="p-3 bg-success bg-opacity-10 rounded-3 border border-success">
                                    <div class="small text-muted mb-2">
                                        <i class="fas fa-check-circle me-1 text-success"></i>Réponse correcte:
                                    </div>
                                    <div class="fw-semibold text-success">
                                        @if($question->type == 'qcm' || $question->type == 'true_false' || $question->type == 'text')
                                            {{ $question->correct_answer }}
                                        
                                        @elseif($question->type == 'checkbox')
                                            <ul class="mb-0">
                                                @foreach($question->formatted_options as $opt)
                                                    @if($opt['is_correct'])
                                                        <li>{{ $opt['text'] }}</li>
                                                    @endif
                                                @endforeach
                                            </ul>
                                        
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
                                                <table class="table table-sm table-bordered mb-0">
                                                    <thead>
                                                        <tr>
                                                            <th>Gauche</th>
                                                            <th class="text-center">→</th>
                                                            <th>Droite</th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        @foreach($matchingData['correct_pairs'] as $pair)
                                                            <tr>
                                                                <td class="fw-semibold">{{ $pair['left'] }}</td>
                                                                <td class="text-center"><i class="fas fa-arrow-right text-success"></i></td>
                                                                <td>{{ $pair['right'] }}</td>
                                                            </tr>
                                                        @endforeach
                                                    </tbody>
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
                                        @endif
                                    </div>
                                </div>
                            @endif

                            <!-- Feedback -->
                            @if(isset($result['feedback']) && $result['feedback'])
                                <div class="mt-3 p-3 bg-info bg-opacity-10 rounded-3 border border-info">
                                    <div class="small text-muted mb-1">
                                        <i class="fas fa-comment-dots me-1"></i>Feedback:
                                    </div>
                                    <div>{{ $result['feedback'] }}</div>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <!-- Summary Card -->
        <div class="card border-0 rounded-4 shadow-hover mb-4">
            <div class="card-body p-4">
                <h5 class="card-title mb-4 fw-bold text-danger">
                    <i class="fas fa-info-circle me-2"></i>
                    Résumé
                </h5>

                <div class="mb-3">
                    <div class="small text-muted mb-1">Statut</div>
                    <span class="badge bg-{{ $attempt->status == 'graded' ? 'success' : 'warning' }}">
                        {{ $attempt->getStatusLabel() }}
                    </span>
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

                    <div class="mb-3">
                        <div class="small text-muted mb-1">Durée</div>
                        <div class="fw-semibold">{{ $attempt->getDuration() }}</div>
                    </div>
                @endif

                <div class="mb-3">
                    <div class="small text-muted mb-1">Score</div>
                    <div class="fw-bold fs-4 {{ $attempt->passed ? 'text-success' : 'text-danger' }}">
                        {{ round($attempt->score, 2) }}%
                    </div>
                    <small>{{ $attempt->total_points }}/{{ $attempt->max_points }} points</small>
                </div>

                <div class="mb-0">
                    <div class="small text-muted mb-1">Résultat</div>
                    @if($attempt->passed)
                        <span class="badge bg-success">
                            <i class="fas fa-check-circle me-1"></i>Réussi
                        </span>
                    @else
                        <span class="badge bg-danger">
                            <i class="fas fa-times-circle me-1"></i>Échoué
                        </span>
                    @endif
                    <div class="small text-muted mt-1">
                        (Score minimum: {{ $exam->passing_score }}%)
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection