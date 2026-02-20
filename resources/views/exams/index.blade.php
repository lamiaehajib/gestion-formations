@extends('layouts.app')

@section('title', 'Gestion des Examens')

@section('content')
<div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInDown">
    <div class="card-body p-4">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-3">
            <div>
                <h4 class="mb-2 fw-bold" style="color: var(--primary-color);">
                    <i class="fas fa-clipboard-check me-2"></i>
                    Gestion des Examens
                </h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Créez et gérez les examens de vos modules
                </p>
            </div>
            @can('exam-create')
            <a href="{{ route('exams.create') }}" class="btn btn-danger rounded-pill px-4 shadow-sm">
                <i class="fas fa-plus-circle me-2"></i>
                Nouvel Examen
            </a>
            @endcan
        </div>
    </div>
</div>

<!-- Filters Section -->
<div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInUp">
    <div class="card-body p-4">
        <form method="GET" action="{{ route('exams.index') }}" class="row g-3">
            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    <i class="fas fa-book-open me-1 text-danger"></i>
                    Module
                </label>
                <select name="module_id" class="form-select form-control-filter">
                    <option value="">-- Tous les modules --</option>
                    @foreach($modules as $module)
                    <option value="{{ $module->id }}" {{ request('module_id') == $module->id ? 'selected' : '' }}>
                        {{ $module->title }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    <i class="fas fa-toggle-on me-1 text-danger"></i>
                    Statut
                </label>
                <select name="status" class="form-select form-control-filter">
                    <option value="">-- Tous les statuts --</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publié</option>
                    <option value="archived" {{ request('status') == 'archived' ? 'selected' : '' }}>Archivé</option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label fw-semibold">
                    <i class="fas fa-search me-1 text-danger"></i>
                    Recherche
                </label>
                <input type="text" name="search" class="form-control form-control-filter" 
                       placeholder="Titre de l'examen..." value="{{ request('search') }}">
            </div>

            <div class="col-12">
                <button type="submit" class="btn btn-danger rounded-pill px-4">
                    <i class="fas fa-filter me-2"></i>Filtrer
                </button>
                <a href="{{ route('exams.index') }}" class="btn btn-secondary rounded-pill px-4">
                    <i class="fas fa-redo me-2"></i>Réinitialiser
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Exams List -->
<div class="row g-4">
    @forelse($exams as $exam)
    <div class="col-12 animate__animated animate__fadeInUp" style="animation-delay: {{ $loop->index * 0.1 }}s;">
        <div class="card border-0 rounded-4 shadow-hover h-100">
            <div class="card-body p-4">
                <div class="row align-items-center">
                    <!-- Left Section: Exam Info -->
                    <div class="col-lg-6 mb-3 mb-lg-0">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0">
                                <div class="bg-danger bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 60px; height: 60px;">
                                    <i class="fas fa-clipboard-list fa-2x text-danger"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1">
                                <h5 class="mb-2 fw-bold">
                                    <a href="{{ route('exams.show', $exam) }}" class="text-dark text-decoration-none hover-danger">
                                        {{ $exam->title }}
                                    </a>
                                </h5>
                                <p class="text-muted mb-2 small">
                                    <i class="fas fa-book-open me-1"></i>
                                    <strong>Module:</strong> {{ $exam->module->title }}
                                </p>
                                @if($exam->description)
                                <p class="text-muted mb-0 small">{{ Str::limit($exam->description, 100) }}</p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Middle Section: Stats -->
                    <div class="col-lg-3 mb-3 mb-lg-0">
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded-3">
                                    <div class="small text-muted mb-1">Questions</div>
                                    <div class="fw-bold text-danger">{{ $exam->questions->count() }}</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded-3">
                                    <div class="small text-muted mb-1">Durée</div>
                                    <div class="fw-bold text-danger">{{ $exam->duration_minutes }} min</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded-3">
                                    <div class="small text-muted mb-1">Score Min</div>
                                    <div class="fw-bold text-danger">{{ $exam->passing_score }}%</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="text-center p-2 bg-light rounded-3">
                                    <div class="small text-muted mb-1">Tentatives</div>
                                    <div class="fw-bold text-danger">{{ $exam->max_attempts }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right Section: Status & Actions -->
                    <div class="col-lg-3">
                        <div class="text-center mb-3">
                            @if($exam->status == 'draft')
                                <span class="badge bg-secondary rounded-pill px-3 py-2">
                                    <i class="fas fa-pencil-alt me-1"></i>Brouillon
                                </span>
                            @elseif($exam->status == 'published')
                                <span class="badge bg-success rounded-pill px-3 py-2">
                                    <i class="fas fa-check-circle me-1"></i>Publié
                                </span>
                            @else
                                <span class="badge bg-dark rounded-pill px-3 py-2">
                                    <i class="fas fa-archive me-1"></i>Archivé
                                </span>
                            @endif
                        </div>

                        <div class="d-flex gap-2 justify-content-center">
                            <a href="{{ route('exams.show', $exam) }}" 
                               class="btn btn-sm btn-danger rounded-pill" 
                               data-bs-toggle="tooltip" title="Voir les détails">
                                <i class="fas fa-eye"></i>
                            </a>
                            @can('exam-edit')
                            <a href="{{ route('exams.edit', $exam) }}" 
                               class="btn btn-sm btn-primary rounded-pill"
                               data-bs-toggle="tooltip" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endcan
                            @can('exam-delete')
                            <form action="{{ route('exams.destroy', $exam) }}" method="POST" class="d-inline delete-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" 
                                        class="btn btn-sm btn-danger rounded-pill"
                                        data-bs-toggle="tooltip" title="Supprimer">
                                    <i class="fas fa-trash-alt"></i>
                                </button>
                            </form>
                            @endcan
                        </div>
                    </div>
                </div>

                <!-- Availability Info -->
                @if($exam->available_from || $exam->available_until)
                <div class="row mt-3 pt-3 border-top">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-3 small text-muted">
                            @if($exam->available_from)
                            <div>
                                <i class="fas fa-calendar-check me-1 text-success"></i>
                                <strong>Disponible dès:</strong> {{ $exam->available_from->format('d/m/Y H:i') }}
                            </div>
                            @endif
                            @if($exam->available_until)
                            <div>
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
    @empty
    <div class="col-12">
        <div class="card border-0 rounded-4 shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard-list fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Aucun examen trouvé</h5>
                <p class="text-muted mb-3">Commencez par créer votre premier examen</p>
                @can('exam-create')
                <a href="{{ route('exams.create') }}" class="btn btn-danger rounded-pill px-4">
                    <i class="fas fa-plus-circle me-2"></i>Créer un examen
                </a>
                @endcan
            </div>
        </div>
    </div>
    @endforelse
</div>

<!-- Pagination -->
@if($exams->hasPages())
<div class="d-flex justify-content-center mt-4">
    {{ $exams->links() }}
</div>
@endif
@endsection

@push('styles')
<style>
    .hover-danger:hover {
        color: var(--primary-color) !important;
        transition: var(--transition);
    }
    
    .shadow-hover {
        transition: var(--transition);
    }
    
    .shadow-hover:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(211, 47, 47, 0.15) !important;
    }
</style>
@endpush

@push('scripts')
<script>
    // Initialize tooltips
    document.addEventListener('DOMContentLoaded', function() {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });

        // Delete confirmation
        document.querySelectorAll('.delete-form').forEach(form => {
            form.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Confirmer la suppression?',
                    text: "Cette action est irréversible!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#D32F2F',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Oui, supprimer!',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush