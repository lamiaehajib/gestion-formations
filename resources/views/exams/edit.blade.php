@extends('layouts.app')

@section('title', 'Modifier l\'Examen')

@section('content')
<div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInDown">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('exams.index') }}" class="btn btn-light rounded-circle" style="width: 40px; height: 40px; padding: 0;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-1 fw-bold" style="color: var(--primary-color);">
                        <i class="fas fa-edit me-2"></i>
                        Modifier l'Examen
                    </h4>
                    <p class="text-muted mb-0 small">
                        {{ $exam->title }}
                    </p>
                </div>
            </div>
            <a href="{{ route('exams.show', $exam) }}" class="btn btn-danger rounded-pill px-4">
                <i class="fas fa-eye me-2"></i>Voir l'examen
            </a>
        </div>
    </div>
</div>

<!-- Tab Navigation -->
<ul class="nav nav-pills mb-4 animate__animated animate__fadeInUp" role="tablist">
    <li class="nav-item" role="presentation">
        <button class="nav-link active rounded-pill px-4 me-2" id="basic-info-tab" 
                data-bs-toggle="tab" data-bs-target="#basic-info" type="button">
            <i class="fas fa-info-circle me-2"></i>Informations de Base
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4" id="questions-tab" 
                data-bs-toggle="tab" data-bs-target="#questions" type="button">
            <i class="fas fa-question-circle me-2"></i>
            Questions 
            <span class="badge bg-danger ms-1">{{ $exam->questions->count() }}</span>
        </button>
    </li>
</ul>

<!-- Tab Content -->
<div class="tab-content">
    <!-- Basic Info Tab -->
    <div class="tab-pane fade show active" id="basic-info">
        <form action="{{ route('exams.update', $exam) }}" method="POST" id="examUpdateForm">
            @csrf
            @method('PUT')

            <div class="row g-4">
                <!-- Left Column -->
                <div class="col-lg-8">
                    <div class="card border-0 rounded-4 shadow-hover">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4 fw-bold text-danger">
                                <i class="fas fa-info-circle me-2"></i>
                                Informations de Base
                            </h5>

                            <!-- Module -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-book-open me-1 text-danger"></i>
                                    Module <span class="text-danger">*</span>
                                </label>
                                <select name="module_id" class="form-select @error('module_id') is-invalid @enderror" required>
                                    @foreach($modules as $module)
                                    <option value="{{ $module->id }}" 
                                            {{ old('module_id', $exam->module_id) == $module->id ? 'selected' : '' }}>
                                        {{ $module->title }}
                                    </option>
                                    @endforeach
                                </select>
                                @error('module_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Title -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-heading me-1 text-danger"></i>
                                    Titre <span class="text-danger">*</span>
                                </label>
                                <input type="text" name="title" 
                                       class="form-control @error('title') is-invalid @enderror" 
                                       value="{{ old('title', $exam->title) }}" required>
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
                                          class="form-control @error('description') is-invalid @enderror">{{ old('description', $exam->description) }}</textarea>
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
                                           value="{{ old('duration_minutes', $exam->duration_minutes) }}"
                                           min="1" max="300" required>
                                    @error('duration_minutes')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="fas fa-check-circle me-1 text-danger"></i>
                                        Score Minimum (%) <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="passing_score" 
                                           class="form-control @error('passing_score') is-invalid @enderror"
                                           value="{{ old('passing_score', $exam->passing_score) }}"
                                           min="0" max="100" required>
                                    @error('passing_score')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Max Attempts -->
                            <div class="mb-4">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-redo me-1 text-danger"></i>
                                    Nombre Maximum de Tentatives <span class="text-danger">*</span>
                                </label>
                                <select name="max_attempts" class="form-select @error('max_attempts') is-invalid @enderror" required>
                                    @for($i = 1; $i <= 10; $i++)
                                    <option value="{{ $i }}" {{ old('max_attempts', $exam->max_attempts) == $i ? 'selected' : '' }}>
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

                <!-- Right Column -->
                <div class="col-lg-4">
                    <!-- Settings -->
                    <div class="card border-0 rounded-4 shadow-hover mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4 fw-bold text-danger">
                                <i class="fas fa-cog me-2"></i>
                                Paramètres
                            </h5>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" 
                                       name="shuffle_questions" id="shuffle_questions" value="1"
                                       {{ old('shuffle_questions', $exam->shuffle_questions) ? 'checked' : '' }}>
                                <label class="form-check-label" for="shuffle_questions">
                                    <i class="fas fa-random me-1 text-muted"></i>
                                    Mélanger les questions
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" 
                                       name="show_results_immediately" id="show_results_immediately" value="1"
                                       {{ old('show_results_immediately', $exam->show_results_immediately) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_results_immediately">
                                    <i class="fas fa-eye me-1 text-muted"></i>
                                    Afficher résultats immédiatement
                                </label>
                            </div>

                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" 
                                       name="show_correct_answers" id="show_correct_answers" value="1"
                                       {{ old('show_correct_answers', $exam->show_correct_answers) ? 'checked' : '' }}>
                                <label class="form-check-label" for="show_correct_answers">
                                    <i class="fas fa-check-double me-1 text-muted"></i>
                                    Afficher les bonnes réponses
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Availability -->
                    <div class="card border-0 rounded-4 shadow-hover mb-4">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4 fw-bold text-danger">
                                <i class="fas fa-calendar-alt me-2"></i>
                                Disponibilité
                            </h5>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-calendar-check me-1 text-success"></i>
                                    Disponible dès
                                </label>
                                <input type="datetime-local" name="available_from" 
                                       class="form-control @error('available_from') is-invalid @enderror"
                                       value="{{ old('available_from', $exam->available_from?->format('Y-m-d\TH:i')) }}">
                                @error('available_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-calendar-times me-1 text-warning"></i>
                                    Disponible jusqu'au
                                </label>
                                <input type="datetime-local" name="available_until" 
                                       class="form-control @error('available_until') is-invalid @enderror"
                                       value="{{ old('available_until', $exam->available_until?->format('Y-m-d\TH:i')) }}">
                                @error('available_until')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="card border-0 rounded-4 shadow-hover">
                        <div class="card-body p-4">
                            <h5 class="card-title mb-4 fw-bold text-danger">
                                <i class="fas fa-toggle-on me-2"></i>
                                Statut
                            </h5>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="status" 
                                       id="status_draft" value="draft"
                                       {{ old('status', $exam->status) == 'draft' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_draft">
                                    <i class="fas fa-pencil-alt me-1 text-secondary"></i>
                                    Brouillon
                                </label>
                            </div>

                            <div class="form-check mb-2">
                                <input class="form-check-input" type="radio" name="status" 
                                       id="status_published" value="published"
                                       {{ old('status', $exam->status) == 'published' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_published">
                                    <i class="fas fa-check-circle me-1 text-success"></i>
                                    Publié
                                </label>
                            </div>

                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="status" 
                                       id="status_archived" value="archived"
                                       {{ old('status', $exam->status) == 'archived' ? 'checked' : '' }}>
                                <label class="form-check-label" for="status_archived">
                                    <i class="fas fa-archive me-1 text-dark"></i>
                                    Archivé
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Submit Buttons - INSIDE THE FORM -->
            <div class="row mt-4">
                <div class="col-12">
                    <div class="card border-0 rounded-4 shadow-hover">
                        <div class="card-body p-4">
                            <div class="d-flex justify-content-between gap-3">
                                <a href="{{ route('exams.index') }}" class="btn btn-secondary rounded-pill px-4">
                                    <i class="fas fa-times me-2"></i>Annuler
                                </a>
                                <button type="submit" class="btn btn-danger rounded-pill px-4">
                                    <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Questions Tab -->
    <div class="tab-pane fade" id="questions">
        <div class="row g-4">
            <!-- Questions List -->
            <div class="col-lg-8">
                <div class="card border-0 rounded-4 shadow-hover">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="card-title mb-0 fw-bold text-danger">
                                <i class="fas fa-list-ol me-2"></i>
                                Liste des Questions
                            </h5>
                            <button type="button" class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                                <i class="fas fa-plus-circle me-2"></i>Ajouter une Question
                            </button>
                        </div>

                        <div id="questionsList">
                            @forelse($exam->questions as $question)
                            <div class="question-item card mb-3 border shadow-sm" data-question-id="{{ $question->id }}">
                                <div class="card-body p-3">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center gap-3 mb-2">
                                                <div class="drag-handle" style="cursor: move;">
                                                    <i class="fas fa-grip-vertical text-muted"></i>
                                                </div>
                                                <span class="badge bg-danger rounded-pill">{{ $question->getTypeLabel() }}</span>
                                                <span class="badge bg-success rounded-pill">{{ $question->points }} pts</span>
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
                                        
                                        <div class="d-flex gap-2">
                                            <button type="button" class="btn btn-sm btn-primary rounded-pill edit-question-btn" 
                                                    data-question-id="{{ $question->id }}"
                                                    data-bs-toggle="tooltip" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-danger rounded-pill delete-question-btn" 
                                                    data-question-id="{{ $question->id }}"
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5">
                                <i class="fas fa-question-circle fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Aucune question ajoutée</h5>
                                <p class="text-muted mb-3">Commencez par ajouter des questions à votre examen</p>
                                <button type="button" class="btn btn-danger rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addQuestionModal">
                                    <i class="fas fa-plus-circle me-2"></i>Ajouter la Première Question
                                </button>
                            </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="col-lg-4">
                <div class="card border-0 rounded-4 shadow-hover">
                    <div class="card-body p-4">
                        <h5 class="card-title mb-4 fw-bold text-danger">
                            <i class="fas fa-chart-bar me-2"></i>
                            Statistiques Rapides
                        </h5>

                        <div class="mb-3 p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Total Questions</span>
                                <span class="fw-bold fs-4 text-danger">{{ $exam->questions->count() }}</span>
                            </div>
                        </div>

                        <div class="mb-3 p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="text-muted">Points Totaux</span>
                                <span class="fw-bold fs-4 text-danger">{{ $exam->total_points }}</span>
                            </div>
                        </div>

                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-1"></i>
                            <strong>Astuce:</strong> Glissez-déposez les questions pour les réorganiser
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Question Modal -->
@include('exams.partials.add-question-modal', ['exam' => $exam])

<!-- Edit Question Modal -->
@include('exams.partials.edit-question-modal')

@endsection

@push('styles')
<style>
    .nav-pills .nav-link {
        color: #6c757d;
        transition: all 0.3s ease;
    }
    
    .nav-pills .nav-link:hover {
        color: var(--primary-color);
        background-color: rgba(211, 47, 47, 0.1);
    }
    
    .nav-pills .nav-link.active {
        background-color: var(--primary-color);
        color: white;
    }

    .question-item {
        transition: all 0.3s ease;
    }

    .question-item:hover {
        box-shadow: 0 5px 15px rgba(211, 47, 47, 0.15) !important;
    }

    .drag-handle {
        opacity: 0.5;
        transition: opacity 0.3s ease;
    }

    .drag-handle:hover {
        opacity: 1;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Form submit confirmation
        const examForm = document.getElementById('examUpdateForm');
        if (examForm) {
            examForm.addEventListener('submit', function(e) {
                console.log('Form is being submitted');
            });
        }

        // Initialize Sortable for drag and drop
        const questionsList = document.getElementById('questionsList');
        
        if (questionsList && questionsList.children.length > 0) {
            new Sortable(questionsList, {
                handle: '.drag-handle',
                animation: 150,
                onEnd: function(evt) {
                    const questions = Array.from(questionsList.children)
                        .filter(el => el.classList.contains('question-item'))
                        .map(el => el.dataset.questionId);
                    
                    // Save new order via AJAX
                    fetch('{{ route("exams.questions.reorder", $exam) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ questions: questions })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                toast: true,
                                position: 'top-end',
                                icon: 'success',
                                title: 'Ordre mis à jour!',
                                showConfirmButton: false,
                                timer: 2000
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: 'Erreur',
                            text: 'Impossible de sauvegarder l\'ordre',
                            confirmButtonColor: '#D32F2F'
                        });
                    });
                }
            });
        }

        // Delete question
        document.addEventListener('click', function(e) {
            if (e.target.closest('.delete-question-btn')) {
                e.preventDefault();
                const btn = e.target.closest('.delete-question-btn');
                const questionId = btn.dataset.questionId;
                
                Swal.fire({
                    title: 'Supprimer cette question?',
                    text: "Cette action est irréversible!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#D32F2F',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Oui, supprimer!',
                    cancelButtonText: 'Annuler'
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/exam-questions/${questionId}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                btn.closest('.question-item').remove();
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Supprimé!',
                                    text: data.message,
                                    confirmButtonColor: '#D32F2F'
                                }).then(() => {
                                    location.reload();
                                });
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Erreur',
                                text: 'Impossible de supprimer la question',
                                confirmButtonColor: '#D32F2F'
                            });
                        });
                    }
                });
            }
        });

        // Initialize tooltips
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    });
</script>
@endpush