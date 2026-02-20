@extends('layouts.app')

@section('title', 'Mes Tentatives d\'Examen')

@section('content')
<div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInDown">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                     style="width: 60px; height: 60px;">
                    <i class="fas fa-history fa-2x text-danger"></i>
                </div>
                <div>
                    <h4 class="mb-1 fw-bold" style="color: var(--primary-color);">
                        Historique de mes Tentatives
                    </h4>
                    <p class="text-muted mb-0 small">
                        <i class="fas fa-info-circle me-1"></i>
                        Tous vos essais d'examens
                    </p>
                </div>
            </div>
            <a href="{{ route('exams.available') }}" class="btn btn-danger rounded-pill px-4">
                <i class="fas fa-clipboard-check me-2"></i>Examens Disponibles
            </a>
        </div>
    </div>
</div>

@if($attempts->isEmpty())
    <div class="card border-0 rounded-4 shadow-sm animate__animated animate__fadeInUp">
        <div class="card-body text-center py-5">
            <i class="fas fa-clipboard-list fa-5x text-muted mb-3"></i>
            <h5 class="text-muted mb-3">Aucune Tentative</h5>
            <p class="text-muted mb-4">Vous n'avez pas encore passé d'examen</p>
            <a href="{{ route('exams.available') }}" class="btn btn-danger rounded-pill px-4">
                <i class="fas fa-clipboard-check me-2"></i>Voir les Examens Disponibles
            </a>
        </div>
    </div>
@else
    <div class="row g-4">
        @foreach($attempts as $attempt)
        <div class="col-12 animate__animated animate__fadeInUp" style="animation-delay: {{ $loop->index * 0.05 }}s;">
            <div class="card border-0 rounded-4 shadow-hover">
                <div class="card-body p-4">
                    <div class="row align-items-center">
                        <!-- Exam Info -->
                        <div class="col-lg-4 mb-3 mb-lg-0">
                            <div class="d-flex align-items-start gap-3">
                                <div class="flex-shrink-0">
                                    <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                         style="width: 50px; height: 50px;">
                                        <i class="fas fa-file-alt fa-lg text-danger"></i>
                                    </div>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="mb-2 fw-bold">{{ $attempt->exam->title }}</h6>
                                    <p class="text-muted mb-1 small">
                                        <i class="fas fa-book-open me-1"></i>
                                        {{ $attempt->exam->module->title }}
                                    </p>
                                    <p class="text-muted mb-0 small">
                                        <i class="fas fa-calendar me-1"></i>
                                        {{ $attempt->started_at->format('d/m/Y H:i') }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Stats -->
                        <div class="col-lg-4 mb-3 mb-lg-0">
                            <div class="row g-2">
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded-3">
                                        <div class="small text-muted mb-1">Score</div>
                                        @if($attempt->score !== null)
                                            <div class="fw-bold {{ $attempt->passed ? 'text-success' : 'text-danger' }}">
                                                {{ number_format($attempt->score, 2) }}%
                                            </div>
                                        @else
                                            <div class="small text-muted">En attente</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded-3">
                                        <div class="small text-muted mb-1">Points</div>
                                        @if($attempt->total_points !== null)
                                            <div class="fw-bold text-primary">
                                                {{ $attempt->total_points }}/{{ $attempt->max_points }}
                                            </div>
                                        @else
                                            <div class="small text-muted">-</div>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded-3">
                                        <div class="small text-muted mb-1">Tentative</div>
                                        <div class="fw-bold text-secondary">
                                            #{{ $attempt->attempt_number }}
                                        </div>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="text-center p-2 bg-light rounded-3">
                                        <div class="small text-muted mb-1">Durée</div>
                                        <div class="fw-bold text-secondary">
                                            {{ $attempt->getDuration() ?? '-' }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status & Actions -->
                        <div class="col-lg-4">
                            <div class="text-center">
                                <!-- Status Badge -->
                                <div class="mb-3">
                                    @if($attempt->status == 'in_progress')
                                        <span class="badge bg-warning rounded-pill px-3 py-2">
                                            <i class="fas fa-clock me-1"></i>En Cours
                                        </span>
                                    @elseif($attempt->status == 'submitted')
                                        <span class="badge bg-info rounded-pill px-3 py-2">
                                            <i class="fas fa-hourglass-half me-1"></i>Soumis
                                        </span>
                                    @elseif($attempt->status == 'timed_out')
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">
                                            <i class="fas fa-stopwatch me-1"></i>Temps Écoulé
                                        </span>
                                    @elseif($attempt->status == 'graded')
                                        @if($attempt->passed)
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                <i class="fas fa-check-circle me-1"></i>Réussi
                                            </span>
                                        @else
                                            <span class="badge bg-danger rounded-pill px-3 py-2">
                                                <i class="fas fa-times-circle me-1"></i>Échoué
                                            </span>
                                        @endif
                                    @endif
                                </div>

                                <!-- Action Button -->
                                @if($attempt->status == 'in_progress')
                                    <a href="{{ route('exams.take', $attempt) }}" 
                                       class="btn btn-warning rounded-pill px-4 w-100">
                                        <i class="fas fa-arrow-right me-2"></i>Continuer
                                    </a>
                                @else
                                    <a href="{{ route('exams.result', $attempt) }}" 
                                       class="btn btn-danger rounded-pill px-4 w-100">
                                        <i class="fas fa-eye me-2"></i>Voir le Résultat
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Pagination -->
    @if($attempts->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $attempts->links() }}
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
</style>
@endpush