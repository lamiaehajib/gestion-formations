@extends('layouts.app')

@section('title', 'Détails de l\'Examen')

@section('content')
<div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInDown">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('exams.index') }}" class="btn btn-red rounded-circle" style="width:40px;height:40px;padding:0; background-color: var(--primary-color);">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-1 fw-bold" style="color: var(--primary-color);">
                        {{ $exam->title }}
                    </h4>
                    <p class="text-muted mb-0 small">
                        <i class="fas fa-book-open me-1"></i>
                        {{ $exam->module->title }}
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2">
                @can('exam-edit')
                <a href="{{ route('exams.edit', $exam) }}" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-edit me-2"></i>Modifier
                </a>
                <a href="{{ route('exams.attempts', $exam) }}" class="btn btn-info rounded-pill px-4">
    <i class="fas fa-users me-2"></i>
    Voir les Tentatives ({{ $stats['total_attempts'] }})
</a>
<a href="{{ route('exams.rattrapages.index', $exam) }}"
   class="btn btn-warning rounded-pill px-3">
    <i class="fas fa-retweet me-1"></i>
    Rattrapages
    @php $rattrapageCount = $exam->rattrapages()->count(); @endphp
    @if($rattrapageCount > 0)
        <span class="badge bg-warning text-dark ms-1">{{ $rattrapageCount }}</span>
    @endif
</a>
                @endcan
                
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Exam Details -->
    <div class="col-lg-8">
        <!-- Basic Info -->
        <div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInUp">
            <div class="card-body p-4">
                <h5 class="card-title mb-4 fw-bold text-danger">
                    <i class="fas fa-info-circle me-2"></i>
                    Informations de l'Examen
                </h5>

                @if($exam->description)
                <div class="mb-4">
                    <h6 class="text-muted small mb-2">Description</h6>
                    <p class="mb-0">{{ $exam->description }}</p>
                </div>
                @endif

                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <div class="small text-muted mb-1">
                                <i class="fas fa-clock me-1"></i>Durée
                            </div>
                            <div class="fw-bold fs-5">{{ $exam->duration_minutes }} minutes</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <div class="small text-muted mb-1">
                                <i class="fas fa-check-circle me-1"></i>Score Minimum
                            </div>
                            <div class="fw-bold fs-5">{{ $exam->passing_score }}%</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <div class="small text-muted mb-1">
                                <i class="fas fa-question-circle me-1"></i>Questions
                            </div>
                            <div class="fw-bold fs-5">{{ $exam->questions->count() }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <div class="small text-muted mb-1">
                                <i class="fas fa-star me-1"></i>Points Totaux
                            </div>
                            <div class="fw-bold fs-5">{{ $exam->total_points }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <div class="small text-muted mb-1">
                                <i class="fas fa-redo me-1"></i>Tentatives Max
                            </div>
                            <div class="fw-bold fs-5">{{ $exam->max_attempts }}</div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <div class="small text-muted mb-1">
                                <i class="fas fa-toggle-on me-1"></i>Statut
                            </div>
                            <div class="fw-bold fs-5">
                                @if($exam->status == 'draft')
                                    <span class="badge bg-secondary">Brouillon</span>
                                @elseif($exam->status == 'published')
                                    <span class="badge bg-success">Publié</span>
                                @else
                                    <span class="badge bg-dark">Archivé</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Settings -->
                <div class="mt-4 pt-4 border-top">
                    <h6 class="text-muted small mb-3">Paramètres</h6>
                    <div class="d-flex flex-wrap gap-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" disabled 
                                   {{ $exam->shuffle_questions ? 'checked' : '' }}>
                            <label class="form-check-label">Mélanger les questions</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" disabled 
                                   {{ $exam->show_results_immediately ? 'checked' : '' }}>
                            <label class="form-check-label">Afficher résultats immédiatement</label>
                        </div>
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" disabled 
                                   {{ $exam->show_correct_answers ? 'checked' : '' }}>
                            <label class="form-check-label">Afficher bonnes réponses</label>
                        </div>
                    </div>
                </div>

                <!-- Availability -->
                @if($exam->available_from || $exam->available_until)
                <div class="mt-4 pt-4 border-top">
                    <h6 class="text-muted small mb-3">Disponibilité</h6>
                    <div class="row g-3">
                        @if($exam->available_from)
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">
                                <i class="fas fa-calendar-check me-1 text-success"></i>
                                Disponible dès
                            </div>
                            <div class="fw-semibold">{{ $exam->available_from->format('d/m/Y H:i') }}</div>
                        </div>
                        @endif
                        @if($exam->available_until)
                        <div class="col-md-6">
                            <div class="small text-muted mb-1">
                                <i class="fas fa-calendar-times me-1 text-warning"></i>
                                Jusqu'au
                            </div>
                            <div class="fw-semibold">{{ $exam->available_until->format('d/m/Y H:i') }}</div>
                        </div>
                        @endif
                    </div>
                </div>
                @endif
            </div>
        </div>

        <!-- Questions Preview -->
        <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
            <div class="card-body p-4">
                <h5 class="card-title mb-4 fw-bold text-danger">
                    <i class="fas fa-list-ol me-2"></i>
                    Aperçu des Questions
                </h5>

                @forelse($exam->questions as $index => $question)
                <div class="card mb-3 border">
                    <div class="card-body p-3">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="badge bg-danger rounded-pill me-2">Q{{ $index + 1 }}</span>
                                <span class="badge bg-secondary rounded-pill me-2">{{ $question->getTypeLabel() }}</span>
                                <span class="badge bg-success rounded-pill">{{ $question->points }} pts</span>
                            </div>
                        </div>
                        <p class="mb-2 fw-semibold">{{ $question->question_text }}</p>
                        
                        @if($question->type == 'qcm' || $question->type == 'checkbox')
                            <ul class="list-unstyled mb-0 small">
                                @foreach($question->formatted_options as $opt)
                                <li class="mb-1">
                                    <i class="fas {{ $opt['is_correct'] ? 'fa-check-circle text-success' : 'fa-circle text-muted' }} me-2"></i>
                                    {{ $opt['text'] }}
                                </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>
                </div>
                @empty
                <div class="text-center py-4 text-muted">
                    <i class="fas fa-question-circle fa-3x mb-3"></i>
                    <p class="mb-0">Aucune question ajoutée</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Right Column: Statistics -->
    <div class="col-lg-4">
        <!-- Statistics Card -->
        <div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
            <div class="card-body p-4">
                <h5 class="card-title mb-4 fw-bold text-danger">
                    <i class="fas fa-chart-bar me-2"></i>
                    Statistiques
                </h5>

                @if($stats['total_attempts'] > 0)
                    <div class="mb-3 p-3 bg-light rounded-3">
                        <div class="small text-muted mb-1">Total Tentatives</div>
                        <div class="fw-bold fs-4 text-primary">{{ $stats['total_attempts'] }}</div>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded-3">
                        <div class="small text-muted mb-1">Étudiants Uniques</div>
                        <div class="fw-bold fs-4 text-secondary">{{ $stats['total_students'] }}</div>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded-3">
                        <div class="small text-muted mb-1">Score Moyen</div>
                        <div class="fw-bold fs-4 text-info">{{ $stats['average_score'] }}%</div>
                    </div>

                    <div class="mb-3 p-3 bg-light rounded-3">
                        <div class="small text-muted mb-1">Taux de Réussite</div>
                        <div class="fw-bold fs-4 text-success">{{ $stats['pass_rate'] }}%</div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <div class="p-3 bg-success bg-opacity-10 rounded-3 border border-success">
                                <div class="small text-muted mb-1">Meilleur</div>
                                <div class="fw-bold text-success">{{ $stats['highest_score'] }}%</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="p-3 bg-danger bg-opacity-10 rounded-3 border border-danger">
                                <div class="small text-muted mb-1">Pire</div>
                                <div class="fw-bold text-danger">{{ $stats['lowest_score'] }}%</div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-chart-line fa-3x mb-3"></i>
                        <p class="mb-0">Aucune donnée statistique disponible</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Created By -->
        <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
            <div class="card-body p-4">
                <h6 class="card-title mb-3 fw-bold text-danger">
                    <i class="fas fa-user me-2"></i>
                    Informations de Création
                </h6>

                <div class="mb-3">
                    <div class="small text-muted mb-1">Créé par</div>
                    <div class="fw-semibold">{{ $exam->creator->name }}</div>
                </div>

                <div class="mb-3">
                    <div class="small text-muted mb-1">Créé le</div>
                    <div class="fw-semibold">{{ $exam->created_at->format('d/m/Y H:i') }}</div>
                </div>

                <div class="mb-0">
                    <div class="small text-muted mb-1">Dernière modification</div>
                    <div class="fw-semibold">{{ $exam->updated_at->format('d/m/Y H:i') }}</div>
                </div>
            </div>
        </div>
    </div>
</div>
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

    .form-check-input:disabled {
        opacity: 0.6;
    }
</style>
@endpush