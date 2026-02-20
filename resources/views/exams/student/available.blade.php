@extends('layouts.app')

@section('title', 'Mes Examens Disponibles')

@section('content')
<div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInDown">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                 style="width: 60px; height: 60px;">
                <i class="fas fa-clipboard-check fa-2x text-danger"></i>
            </div>
            <div>
                <h4 class="mb-1 fw-bold" style="color: var(--primary-color);">
                    Mes Examens Disponibles
                </h4>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-info-circle me-1"></i>
                    Liste des examens que vous pouvez passer maintenant
                </p>
            </div>
        </div>
    </div>
</div>

{{-- ═══════════════════════════════════════════════════════════════════════════
    EMPTY STATE - Only if both normal exams AND rattrapages are empty
    ═══════════════════════════════════════════════════════════════════════════ --}}
@if($availableExams->isEmpty() && (!isset($rattrapageExams) || $rattrapageExams->isEmpty()))
    <div class="card border-0 rounded-4 shadow-sm animate__animated animate__fadeInUp">
        <div class="card-body text-center py-5">
            <div class="mb-4">
                <i class="fas fa-clipboard-list fa-5x text-muted"></i>
            </div>
            <h5 class="text-muted mb-3">Aucun Examen Disponible</h5>
            <p class="text-muted mb-4">
                Vous n'avez actuellement aucun examen disponible.<br>
                Les examens seront accessibles une fois que vous aurez terminé le module (progression 100%).
            </p>
            <a href="{{ route('dashboard') }}" class="btn btn-danger rounded-pill px-4">
                <i class="fas fa-home me-2"></i>Retour au Tableau de Bord
            </a>
        </div>
    </div>
@else

    {{-- ═══════════════════════════════════════════════════════════════════════════
        SECTION: EXAMENS NORMAUX
        ═══════════════════════════════════════════════════════════════════════════ --}}
    @if($availableExams->isNotEmpty())
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="rounded-circle bg-danger d-flex align-items-center justify-content-center shadow"
             style="width:44px;height:44px;">
            <i class="fas fa-clipboard-check text-white"></i>
        </div>
        <div>
            <h5 class="fw-bold mb-0">Examens Réguliers</h5>
            <small class="text-muted">Examens des modules que vous avez complétés</small>
        </div>
        <span class="badge bg-danger rounded-pill ms-2">{{ $availableExams->count() }}</span>
    </div>

    <div class="row g-4 mb-5">
        @foreach($availableExams as $exam)
        <div class="col-12 animate__animated animate__fadeInUp" style="animation-delay: {{ $loop->index * 0.1 }}s;">
            <div class="card border-0 rounded-4 shadow-hover h-100">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <!-- Left: Exam Info -->
                        <div class="col-lg-5 mb-3 mb-lg-0">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-file-alt fa-lg text-danger"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-2 fw-bold text-dark">{{ $exam->title }}</h5>
                                    <p class="text-muted mb-2 small">
                                        <i class="fas fa-book-open me-1"></i>
                                        <strong>Module:</strong> {{ $exam->module_title }}
                                    </p>
                                    @if($exam->description)
                                    <p class="text-muted mb-0 small">{{ Str::limit($exam->description, 80) }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Middle: Stats -->
                        <div class="col-lg-4 mb-3 mb-lg-0">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded-3">
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-question-circle me-1"></i>Questions
                                        </div>
                                        <div class="fw-bold text-danger">{{ $exam->questions->count() }}</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded-3">
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-clock me-1"></i>Durée
                                        </div>
                                        <div class="fw-bold text-danger">{{ $exam->duration_minutes }} min</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded-3">
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-check-circle me-1"></i>Score Min
                                        </div>
                                        <div class="fw-bold text-danger">{{ $exam->passing_score }}%</div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded-3">
                                        <div class="small text-muted mb-1">
                                            <i class="fas fa-redo me-1"></i>Tentatives
                                        </div>
                                        <div class="fw-bold text-danger">
                                            {{ $exam->attempts->count() }} / {{ $exam->max_attempts }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Right: Actions -->
                        <div class="col-lg-3">
                            <div class="text-center">
                                @if($exam->can_attempt_data['can_attempt'])
                                    <!-- Show best score if exists -->
                                    @if($exam->attempts->count() > 0)
                                        @php
                                            $bestAttempt = $exam->attempts->sortByDesc('score')->first();
                                        @endphp
                                        <div class="mb-3">
                                            <div class="small text-muted mb-1">Meilleur Score</div>
                                            <div class="fs-4 fw-bold {{ $bestAttempt->passed ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($bestAttempt->score, 2) }}%
                                            </div>
                                            @if($bestAttempt->passed)
                                                <span class="badge bg-success rounded-pill">
                                                    <i class="fas fa-check me-1"></i>Réussi
                                                </span>
                                            @else
                                                <span class="badge bg-danger rounded-pill">
                                                    <i class="fas fa-times me-1"></i>Échoué
                                                </span>
                                            @endif
                                        </div>
                                    @endif

                                    <form action="{{ route('exams.start', $exam) }}" method="POST">
                                        @csrf
                                        <input type="hidden" name="inscription_id" value="{{ $exam->inscription_id }}">
                                        <button type="submit" class="btn btn-danger rounded-pill px-4 w-100 mb-2">
                                            <i class="fas fa-play-circle me-2"></i>
                                            {{ $exam->attempts->count() > 0 ? 'Nouvelle Tentative' : 'Commencer' }}
                                        </button>
                                    </form>

                                    <div class="small text-muted">
                                        <i class="fas fa-info-circle me-1"></i>
                                        {{ $exam->can_attempt_data['attempts_left'] }} 
                                        {{ $exam->can_attempt_data['attempts_left'] == 1 ? 'tentative restante' : 'tentatives restantes' }}
                                    </div>
                                @else
                                    <div class="alert alert-warning mb-0">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        <small>{{ $exam->can_attempt_data['reason'] }}</small>
                                    </div>

                                    @if(isset($exam->can_attempt_data['ongoing_attempt']))
                                        <a href="{{ route('exams.take', $exam->can_attempt_data['ongoing_attempt']) }}" 
                                           class="btn btn-warning rounded-pill px-4 w-100 mt-2">
                                            <i class="fas fa-arrow-right me-2"></i>Continuer l'Examen
                                        </a>
                                    @endif
                                @endif

                                <!-- View Previous Attempts -->
                                @if($exam->attempts->count() > 0)
                                    <a href="{{ route('exams.my-attempts') }}" class="btn btn-secondary rounded-pill px-4 w-100 mt-2">
                                        <i class="fas fa-history me-2"></i>Mes Tentatives
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Availability Info -->
                    @if($exam->available_from || $exam->available_until)
                    <div class="row mt-3 pt-3 border-top">
                        <div class="col-12">
                            <div class="d-flex flex-wrap gap-3 small">
                                @if($exam->available_from)
                                <div class="text-muted">
                                    <i class="fas fa-calendar-check me-1 text-success"></i>
                                    <strong>Disponible dès:</strong> {{ $exam->available_from->format('d/m/Y H:i') }}
                                </div>
                                @endif
                                @if($exam->available_until)
                                <div class="text-muted">
                                    <i class="fas fa-calendar-times me-1 text-warning"></i>
                                    <strong>Jusqu'au:</strong> {{ $exam->available_until->format('d/m/Y H:i') }}
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════════════
        SECTION: RATTRAPAGES
        ═══════════════════════════════════════════════════════════════════════════ --}}
    @if(isset($rattrapageExams) && $rattrapageExams->isNotEmpty())
    <div class="mt-5">
        <div class="d-flex align-items-center gap-3 mb-4">
            <div class="rounded-circle bg-warning d-flex align-items-center justify-content-center shadow"
                 style="width:44px;height:44px;">
                <i class="fas fa-retweet text-white"></i>
            </div>
            <div>
                <h5 class="fw-bold mb-0">Mes Rattrapages</h5>
                <small class="text-muted">Examens de rattrapage auxquels vous êtes éligible</small>
            </div>
            <span class="badge bg-warning text-dark rounded-pill ms-2">{{ $rattrapageExams->count() }}</span>
        </div>

        <div class="row g-4">
            @foreach($rattrapageExams as $item)
            @php
                $exam         = $item['exam'];
                $canAttempt   = $item['can_attempt_data'];
                $insc         = $item['inscription_id'];
                $modTitle     = $item['module_title'];
                $reason       = $item['eligibility_reason'];
                $origScore    = $item['original_score'];
                $myAttempts   = $exam->attempts;
                $bestScore    = $myAttempts->whereIn('status', ['submitted','graded'])->max('score');
                $attemptCount = $myAttempts->whereIn('status', ['submitted','graded','timed_out'])->count();
                $passed       = $myAttempts->where('passed', true)->count() > 0;
            @endphp

            <div class="col-md-6 col-xl-4 animate__animated animate__fadeInUp">
                <div class="card border-0 rounded-4 shadow-hover h-100 border-start border-4 border-warning">
                    <div class="card-body p-4">
                        {{-- Header --}}
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div class="flex-grow-1 me-2">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="badge bg-warning text-dark rounded-pill">
                                        <i class="fas fa-retweet me-1"></i>Rattrapage
                                    </span>
                                    @if($reason === 'absent')
                                        <span class="badge bg-secondary rounded-pill">Absent à l'examen</span>
                                    @else
                                        <span class="badge bg-danger rounded-pill">
                                            Score: {{ $origScore !== null ? number_format($origScore,1) . '%' : '—' }}
                                        </span>
                                    @endif
                                </div>
                                <h6 class="fw-bold mb-0">{{ $exam->title }}</h6>
                                <small class="text-muted">
                                    <i class="fas fa-book me-1"></i>{{ $modTitle }}
                                </small>
                            </div>
                        </div>

                        {{-- Info row --}}
                        <div class="row g-2 mb-3">
                            <div class="col-4 text-center p-2 rounded-3 bg-light">
                                <div class="fw-bold text-primary small">{{ $exam->duration_minutes }}min</div>
                                <div style="font-size:0.65rem;" class="text-muted">Durée</div>
                            </div>
                            <div class="col-4 text-center p-2 rounded-3 bg-light">
                                <div class="fw-bold text-success small">{{ $exam->passing_score }}%</div>
                                <div style="font-size:0.65rem;" class="text-muted">Score min.</div>
                            </div>
                            <div class="col-4 text-center p-2 rounded-3 bg-light">
                                <div class="fw-bold text-warning small">{{ $exam->max_attempts - $attemptCount }}</div>
                                <div style="font-size:0.65rem;" class="text-muted">Restantes</div>
                            </div>
                        </div>

                        {{-- Progress / best score --}}
                        @if($bestScore !== null)
                        <div class="mb-3">
                            <div class="d-flex justify-content-between small mb-1">
                                <span class="text-muted">Meilleur score (rattrapage)</span>
                                <span class="fw-bold {{ $passed ? 'text-success' : 'text-danger' }}">
                                    {{ number_format($bestScore, 1) }}%
                                </span>
                            </div>
                            <div class="progress rounded-pill" style="height:6px;">
                                <div class="progress-bar {{ $passed ? 'bg-success' : 'bg-danger' }}"
                                     style="width:{{ min($bestScore,100) }}%"></div>
                            </div>
                        </div>
                        @endif

                        {{-- Availability dates --}}
                        @if($exam->available_from || $exam->available_until)
                        <div class="small text-muted mb-3">
                            @if($exam->available_from)
                            <i class="fas fa-calendar-check me-1 text-success"></i>
                            Dès {{ $exam->available_from->format('d/m/Y H:i') }}<br>
                            @endif
                            @if($exam->available_until)
                            <i class="fas fa-calendar-times me-1 text-warning"></i>
                            Jusqu'au {{ $exam->available_until->format('d/m/Y H:i') }}
                            @endif
                        </div>
                        @endif

                        {{-- Action button --}}
                        @if($passed)
                        <div class="alert alert-success mb-0 py-2 text-center small rounded-pill">
                            <i class="fas fa-check-circle me-1"></i>Rattrapage réussi!
                        </div>
                        @elseif($canAttempt['can_attempt'])
                        <form action="{{ route('exams.start', $exam) }}" method="POST">
                            @csrf
                            <input type="hidden" name="inscription_id" value="{{ $insc }}">
                            <button type="submit" class="btn btn-warning w-100 rounded-pill fw-semibold">
                                <i class="fas fa-play me-2"></i>
                                {{ $attemptCount > 0 ? 'Retenter le Rattrapage' : 'Commencer le Rattrapage' }}
                            </button>
                        </form>
                        @elseif(isset($canAttempt['ongoing_attempt']))
                        <a href="{{ route('exams.take', $canAttempt['ongoing_attempt']) }}"
                           class="btn btn-info w-100 rounded-pill fw-semibold text-white">
                            <i class="fas fa-arrow-right me-2"></i>Continuer le Rattrapage
                        </a>
                        @else
                        <div class="alert alert-secondary mb-0 py-2 text-center small rounded-pill">
                            <i class="fas fa-ban me-1"></i>{{ $canAttempt['reason'] }}
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

@endif
@endsection

@push('styles')
<style>
    .shadow-hover {
        transition: var(--transition);
    }
    
    .shadow-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(211, 47, 47, 0.15) !important;
    }

    .badge {
        font-size: 0.75rem;
        padding: 0.35em 0.65em;
    }

    .border-start.border-4.border-warning {
        border-left-width: 4px !important;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Confirm before starting exam
        document.querySelectorAll('form[action*="exams"][action*="start"]').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                const examTitle = this.closest('.card').querySelector('h5, h6').textContent.trim();
                const isRattrapage = this.closest('.border-warning') !== null;
                
                Swal.fire({
                    title: isRattrapage ? 'Commencer le Rattrapage?' : 'Commencer l\'Examen?',
                    html: `
                        <p class="mb-3">Vous êtes sur le point de commencer:</p>
                        <h5 class="${isRattrapage ? 'text-warning' : 'text-danger'}">${examTitle}</h5>
                        <div class="alert alert-info mt-3 text-start">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Important:</strong>
                            <ul class="mb-0 mt-2">
                                <li>Le chronomètre démarrera immédiatement</li>
                                <li>Vous ne pourrez pas mettre l'examen en pause</li>
                                <li>Assurez-vous d'avoir une connexion stable</li>
                                ${isRattrapage ? '<li class="text-warning fw-bold">Ceci est une session de rattrapage</li>' : ''}
                            </ul>
                        </div>
                    `,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: isRattrapage ? '#FFC107' : '#D32F2F',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Oui, je suis prêt!',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Show loading
                        document.getElementById('globalLoadingOverlay').classList.add('active');
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush