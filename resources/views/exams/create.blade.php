@extends('layouts.app')

@section('title', 'Créer un Examen')

@section('content')
<div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInDown">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('exams.index') }}" class="btn btn-light rounded-circle" style="width: 40px; height: 40px; padding: 0;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="mb-1 fw-bold" style="color: var(--primary-color);">
                    <i class="fas fa-plus-circle me-2"></i>
                    Créer un Nouvel Examen
                </h4>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-info-circle me-1"></i>
                    Remplissez les informations de base de l'examen
                </p>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('exams.store') }}" method="POST" id="examForm">
    @csrf

    <div class="row g-4">
        <!-- Left Column: Basic Info -->
        <div class="col-lg-8">
            <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4 fw-bold text-danger">
                        <i class="fas fa-info-circle me-2"></i>
                        Informations de Base
                    </h5>

                    <!-- Module Selection -->
<div class="mb-4">
    <label class="form-label fw-semibold">
        <i class="fas fa-book-open me-1 text-danger"></i>
        Module <span class="text-danger">*</span>
        <small class="text-muted">(Progrès 100% uniquement)</small>
    </label>
    <select name="module_id" class="form-select form-control @error('module_id') is-invalid @enderror" required id="module-select">
        <option value="">-- Sélectionnez un module --</option>
        @foreach($modules as $module)
            <option value="{{ $module->id }}" 
                    {{ old('module_id', $moduleId ?? '') == $module->id ? 'selected' : '' }}
                    data-formations="{{ $module->formations->pluck('title')->implode(' | ') }}"
                    data-formations-count="{{ $module->formations->count() }}">
                {{ $module->title }} ({{ $module->progress }}%)
                @if($module->formations->isNotEmpty())
                    - {{ $module->formations->count() }} formation(s)
                @endif
            </option>
        @endforeach
    </select>
    @error('module_id')
    <div class="invalid-feedback">{{ $message }}</div>
    @enderror
    
    {{-- ✅ Affichage dynamique des formations avec toutes les formations --}}
    <div id="module-formations-info" class="mt-2" style="display: none;">
        <div class="alert alert-info mb-0 small">
            <i class="fas fa-graduation-cap me-1"></i>
            <strong>Formations associées (<span id="formations-count">0</span>):</strong>
            <div id="formations-list" class="mt-2"></div>
        </div>
    </div>
</div>


                    <!-- Title -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-heading me-1 text-danger"></i>
                            Titre de l'Examen <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" 
                               class="form-control @error('title') is-invalid @enderror" 
                               value="{{ old('title') }}"
                               placeholder="Ex: Examen Final - Module X"
                               required>
                        @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Description -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-align-left me-1 text-danger"></i>
                            Description
                        </label>
                        <textarea name="description" rows="4" 
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Description de l'examen, instructions générales...">{{ old('description') }}</textarea>
                        @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Duration & Passing Score -->
                    <div class="row mb-4">
                        <div class="col-md-6 mb-3 mb-md-0">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-clock me-1 text-danger"></i>
                                Durée (minutes) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="duration_minutes" 
                                   class="form-control @error('duration_minutes') is-invalid @enderror"
                                   value="{{ old('duration_minutes', 60) }}"
                                   min="1" max="300"
                                   required>
                            @error('duration_minutes')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Maximum 300 minutes (5 heures)</small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-check-circle me-1 text-danger"></i>
                                Score Minimum (%) <span class="text-danger">*</span>
                            </label>
                            <input type="number" name="passing_score" 
                                   class="form-control @error('passing_score') is-invalid @enderror"
                                   value="{{ old('passing_score', 50) }}"
                                   min="0" max="100"
                                   required>
                            @error('passing_score')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">Score requis pour réussir</small>
                        </div>
                    </div>

                    <!-- Max Attempts -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-redo me-1 text-danger"></i>
                            Nombre Maximum de Tentatives <span class="text-danger">*</span>
                        </label>
                        <select name="max_attempts" class="form-select form-control @error('max_attempts') is-invalid @enderror" required>
                            @for($i = 1; $i <= 10; $i++)
                            <option value="{{ $i }}" {{ old('max_attempts', 1) == $i ? 'selected' : '' }}>
                                {{ $i }} {{ $i == 1 ? 'tentative' : 'tentatives' }}
                            </option>
                            @endfor
                        </select>
                        @error('max_attempts')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Settings -->
        <div class="col-lg-4">
            <!-- Exam Settings -->
            <div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.1s;">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4 fw-bold text-danger">
                        <i class="fas fa-cog me-2"></i>
                        Paramètres
                    </h5>

                    <!-- Shuffle Questions -->
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" 
                               name="shuffle_questions" id="shuffle_questions" value="1"
                               {{ old('shuffle_questions') ? 'checked' : '' }}>
                        <label class="form-check-label" for="shuffle_questions">
                            <i class="fas fa-random me-1 text-muted"></i>
                            Mélanger les questions
                        </label>
                    </div>

                    <!-- Show Results Immediately -->
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" 
                               name="show_results_immediately" id="show_results_immediately" value="1"
                               {{ old('show_results_immediately', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="show_results_immediately">
                            <i class="fas fa-eye me-1 text-muted"></i>
                            Afficher résultats immédiatement
                        </label>
                    </div>

                    <!-- Show Correct Answers -->
                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" 
                               name="show_correct_answers" id="show_correct_answers" value="1"
                               {{ old('show_correct_answers', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="show_correct_answers">
                            <i class="fas fa-check-double me-1 text-muted"></i>
                            Afficher les bonnes réponses
                        </label>
                    </div>
                </div>
            </div>

            <!-- Availability -->
            <div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4 fw-bold text-danger">
                        <i class="fas fa-calendar-alt me-2"></i>
                        Disponibilité
                    </h5>

                    <!-- Available From -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar-check me-1 text-success"></i>
                            Disponible dès
                        </label>
                        <input type="datetime-local" name="available_from" 
                               class="form-control @error('available_from') is-invalid @enderror"
                               value="{{ old('available_from') }}">
                        @error('available_from')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Laisser vide = disponible immédiatement</small>
                    </div>

                    <!-- Available Until -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-calendar-times me-1 text-warning"></i>
                            Disponible jusqu'au
                        </label>
                        <input type="datetime-local" name="available_until" 
                               class="form-control @error('available_until') is-invalid @enderror"
                               value="{{ old('available_until') }}">
                        @error('available_until')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">Laisser vide = pas de limite</small>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp" style="animation-delay: 0.3s;">
                <div class="card-body p-4">
                    <h5 class="card-title mb-4 fw-bold text-danger">
                        <i class="fas fa-toggle-on me-2"></i>
                        Statut
                    </h5>

                    <div class="form-check mb-2">
                        <input class="form-check-input" type="radio" name="status" 
                               id="status_draft" value="draft"
                               {{ old('status', 'draft') == 'draft' ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_draft">
                            <i class="fas fa-pencil-alt me-1 text-secondary"></i>
                            Brouillon
                        </label>
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="status" 
                               id="status_published" value="published"
                               {{ old('status') == 'published' ? 'checked' : '' }}>
                        <label class="form-check-label" for="status_published">
                            <i class="fas fa-check-circle me-1 text-success"></i>
                            Publié
                        </label>
                    </div>

                    <div class="alert alert-info mt-3 mb-0 small">
                        <i class="fas fa-info-circle me-1"></i>
                        Vous pourrez ajouter des questions après création
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Submit Buttons -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                <div class="card-body p-4">
                    <div class="d-flex flex-wrap gap-3 justify-content-between align-items-center">
                        <button type="button" onclick="window.location.href='{{ route('exams.index') }}'" class="btn btn-secondary rounded-pill px-4">
                            <i class="fas fa-times me-2"></i>Annuler
                        </button>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-danger rounded-pill px-4 shadow-sm">
                                <i class="fas fa-save me-2"></i>Créer l'Examen
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(211, 47, 47, 0.15);
    }

    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }

    .form-check-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(211, 47, 47, 0.15);
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const moduleSelect = document.getElementById('module-select');
        const formationsInfo = document.getElementById('module-formations-info');
        const formationsList = document.getElementById('formations-list');
        const formationsCount = document.getElementById('formations-count');

        // ✅ Afficher TOUTES les formations quand on sélectionne un module
        moduleSelect.addEventListener('change', function() {
            const selectedOption = this.options[this.selectedIndex];
            const formations = selectedOption.getAttribute('data-formations');
            const count = selectedOption.getAttribute('data-formations-count');

            if (formations && formations.trim() !== '') {
                // Séparer les formations et créer une liste
                const formationsArray = formations.split(' | ');
                
                formationsCount.textContent = count;
                
                // Créer une liste HTML
                let formationsHTML = '<ul class="mb-0 ps-3">';
                formationsArray.forEach(formation => {
                    formationsHTML += `<li>${formation}</li>`;
                });
                formationsHTML += '</ul>';
                
                formationsList.innerHTML = formationsHTML;
                formationsInfo.style.display = 'block';
            } else {
                formationsInfo.style.display = 'none';
            }
        });

        // Trigger on page load if module already selected
        if (moduleSelect.value) {
            moduleSelect.dispatchEvent(new Event('change'));
        }

        // Form validation
        const form = document.getElementById('examForm');
        
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const availableFrom = document.querySelector('[name="available_from"]').value;
            const availableUntil = document.querySelector('[name="available_until"]').value;

            // Validate dates
            if (availableFrom && availableUntil) {
                const from = new Date(availableFrom);
                const until = new Date(availableUntil);

                if (until <= from) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur de Date',
                        text: 'La date de fin doit être après la date de début!',
                        confirmButtonColor: '#D32F2F'
                    });
                    return false;
                }
            }

            // Check required fields
            const moduleId = document.querySelector('[name="module_id"]').value;
            const title = document.querySelector('[name="title"]').value;
            const duration = document.querySelector('[name="duration_minutes"]').value;
            const passingScore = document.querySelector('[name="passing_score"]').value;

            if (!moduleId || !title || !duration || !passingScore) {
                Swal.fire({
                    icon: 'error',
                    title: 'Champs Requis',
                    text: 'Veuillez remplir tous les champs obligatoires!',
                    confirmButtonColor: '#D32F2F'
                });
                return false;
            }

            // Show loading
            if (typeof globalLoadingOverlay !== 'undefined') {
                document.getElementById('globalLoadingOverlay').classList.add('active');
            }

            // Submit form
            this.submit();
        });
    });
</script>
@endpush