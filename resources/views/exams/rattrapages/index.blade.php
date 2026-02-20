@extends('layouts.app')

@section('title', 'Rattrapages – ' . $exam->title)

@section('content')
{{-- Header --}}
<div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInDown">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('exams.show', $exam) }}" class="btn btn-light rounded-circle"
                   style="width:40px;height:40px;padding:0;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-1 fw-bold" style="color:var(--primary-color);">
                        <i class="fas fa-retweet me-2"></i>Rattrapages
                    </h4>
                    <p class="text-muted mb-0 small">
                        <i class="fas fa-file-alt me-1"></i>{{ $exam->title }}
                    </p>
                </div>
            </div>
            <a href="{{ route('exams.rattrapages.create', $exam) }}"
               class="btn btn-danger rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus me-2"></i>Nouveau Rattrapage
            </a>
        </div>
    </div>
</div>

@if($rattrapages->isEmpty())
<div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp">
    <div class="card-body text-center py-5">
        <i class="fas fa-retweet fa-3x text-muted mb-3"></i>
        <h5 class="text-muted">Aucun rattrapage créé</h5>
        <p class="text-muted">Créez un rattrapage pour les étudiants absents ou ayant échoué.</p>
        <a href="{{ route('exams.rattrapages.create', $exam) }}"
           class="btn btn-danger rounded-pill px-4 mt-2">
            <i class="fas fa-plus me-2"></i>Créer un Rattrapage
        </a>
    </div>
</div>
@else

<div class="row g-4">
    @foreach($rattrapages as $rattrapage)
    @php
        $re  = $rattrapage->rattrapageExam;
        $cnt = $rattrapage->students->count();
    @endphp
    <div class="col-lg-6 animate__animated animate__fadeInUp">
        <div class="card border-0 rounded-4 shadow-hover h-100">
            <div class="card-body p-4">
                {{-- Title & Status --}}
                <div class="d-flex justify-content-between align-items-start mb-3">
                    <div>
                        <h5 class="fw-bold mb-1">{{ $re->title }}</h5>
                        <small class="text-muted">
                            <i class="fas fa-user me-1"></i>{{ $rattrapage->creator->name }}
                            &nbsp;·&nbsp;
                            <i class="fas fa-calendar me-1"></i>{{ $rattrapage->created_at->format('d/m/Y') }}
                        </small>
                    </div>
                    @php
                        $badgeClass = match($re->status) {
                            'published' => 'bg-success',
                            'draft'     => 'bg-secondary',
                            'archived'  => 'bg-dark',
                            default     => 'bg-secondary',
                        };
                        $statusLabel = match($re->status) {
                            'published' => 'Publié',
                            'draft'     => 'Brouillon',
                            'archived'  => 'Archivé',
                            default     => $re->status,
                        };
                    @endphp
                    <span class="badge {{ $badgeClass }} rounded-pill px-3">{{ $statusLabel }}</span>
                </div>

                {{-- Stats Row --}}
                <div class="row g-2 mb-3">
                    <div class="col-4 text-center p-2 rounded-3" style="background:#fff5f5;">
                        <div class="fw-bold text-danger">{{ $cnt }}</div>
                        <small class="text-muted">Étudiants</small>
                    </div>
                    <div class="col-4 text-center p-2 rounded-3" style="background:#f0f9ff;">
                        <div class="fw-bold text-info">{{ $re->duration_minutes }} min</div>
                        <small class="text-muted">Durée</small>
                    </div>
                    <div class="col-4 text-center p-2 rounded-3" style="background:#f0fff4;">
                        <div class="fw-bold text-success">{{ $re->passing_score }}%</div>
                        <small class="text-muted">Score min.</small>
                    </div>
                </div>

                {{-- Criteria badges --}}
                <div class="mb-3">
                    @if($rattrapage->include_absent)
                        <span class="badge bg-secondary me-1">
                            <i class="fas fa-user-slash me-1"></i>Absents inclus
                        </span>
                    @endif
                    @if($rattrapage->include_failed)
                        <span class="badge bg-danger me-1">
                            <i class="fas fa-times-circle me-1"></i>Échoués (< {{ $rattrapage->score_threshold }}%)
                        </span>
                    @endif
                </div>

                @if($rattrapage->notes)
                <p class="text-muted small mb-3">
                    <i class="fas fa-sticky-note me-1"></i>{{ $rattrapage->notes }}
                </p>
                @endif

                {{-- Actions --}}
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('exams.rattrapages.show', [$exam, $rattrapage]) }}"
                       class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="fas fa-eye me-1"></i>Détails
                    </a>
                    <a href="{{ route('exams.edit', $re) }}"
                       class="btn btn-sm btn-warning rounded-pill px-3">
                        <i class="fas fa-question-circle me-1"></i>Questions
                    </a>
                    <form action="{{ route('exams.rattrapages.destroy', [$exam, $rattrapage]) }}"
                          method="POST" class="d-inline delete-rattrapage-form">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger rounded-pill px-3">
                            <i class="fas fa-trash me-1"></i>Supprimer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection

@push('scripts')
<script>
document.querySelectorAll('.delete-rattrapage-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        Swal.fire({
            title: 'Supprimer ce rattrapage ?',
            text: 'L\'examen de rattrapage et toutes ses tentatives seront supprimés.',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#D32F2F',
            cancelButtonText: 'Annuler',
            confirmButtonText: 'Oui, supprimer',
        }).then(result => { if (result.isConfirmed) form.submit(); });
    });
});
</script>
@endpush