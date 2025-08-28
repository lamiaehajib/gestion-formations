@extends('layouts.app')

@section('title', 'Gestion des Cours')

@push('styles')
<style>
    .course-header {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(229, 62, 62, 0.3);
        position: relative;
        overflow: hidden;
    }

    .course-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        transform: rotate(45deg);
        pointer-events: none;
    }

    .course-header h1 {
        color: white;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .course-header p {
        color: rgba(255,255,255,0.9);
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    .btn-new-course {
        background: linear-gradient(135deg, #fff 0%, #f7fafc 100%);
        color: #e53e3e;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        position: relative;
        z-index: 2;
    }

    .btn-new-course:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        color: #c53030;
    }

    .filter-section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border-left: 5px solid #e53e3e;
    }

    .filter-title {
        color: #2d3748;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-title i {
        color: #e53e3e;
        font-size: 1.2rem;
    }

    .form-control {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #e53e3e;
        box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
    }

    .btn-filter {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(229, 62, 62, 0.4);
    }

    .btn-reset {
        background: #6b7280;
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-reset:hover {
        background: #4b5563;
        transform: translateY(-2px);
    }

    .course-card {
        background: white;
        border-radius: 20px;
        padding: 0;
        margin-bottom: 2rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .course-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .course-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #e53e3e 0%, #c53030 100%);
    }

    .course-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f7fafc;
    }

    .course-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .course-icon i {
        color: white;
        font-size: 1.5rem;
    }

    .course-title {
        color: #2d3748;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .course-formation {
        color: #e53e3e;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0;
    }

    .course-date-badge {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .course-card-body {
        padding: 1.5rem;
    }

    .course-description {
        color: #4a5568;
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 1.5rem;
    }

    .course-info {
        margin-bottom: 1.5rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 0.8rem;
        color: #4a5568;
        font-size: 0.9rem;
    }

    .info-item i {
        color: #e53e3e;
        width: 16px;
        text-align: center;
    }

    .course-actions {
        display: flex;
        gap: 10px;
    }

    .btn-action {
        flex: 1;
        padding: 0.6rem 1rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-view {
        background: linear-gradient(135deg, #3182ce 0%, #2c5282 100%);
        color: white;
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(49, 130, 206, 0.3);
        color: white;
    }

    .btn-edit {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        color: white;
    }

    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(56, 161, 105, 0.3);
        color: white;
    }

    .btn-delete {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
        flex: 0 0 auto;
        width: 40px;
        padding: 0.6rem;
    }

    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        margin: 2rem 0;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .empty-icon i {
        color: #a0aec0;
        font-size: 2rem;
    }

    .empty-title {
        color: #2d3748;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .empty-description {
        color: #4a5568;
        font-size: 1rem;
        margin-bottom: 2rem;
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }

    .modal-header {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 1.5rem;
        border-bottom: none;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.2rem;
    }

    .modal-body {
        padding: 2rem;
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: none;
    }

    .modal-form-label {
        font-weight: 600;
        color: #2d3748;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeInUp 0.6s ease-out;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .course-header h1 {
            font-size: 2rem;
        }
        
        .course-actions {
            flex-direction: column;
        }
        
        .btn-action {
            flex: none;
        }
    }
    .modal-form-label {
        font-weight: 600;
        color: #2d3748;
    }

    /* New style for the Join button on cards */
    .btn-join-card {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex; /* Make it a flex container */
        align-items: center; /* Vertically center icon and text */
        justify-content: center; /* Horizontally center content */
        gap: 8px; /* Space between icon and text */
        flex: 1; /* Allow it to grow in flex container */
        box-shadow: 0 5px 15px rgba(66, 153, 225, 0.3);
    }

    .btn-join-card:hover {
        background: linear-gradient(135deg, #3182ce 0%, #2c5282 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(66, 153, 225, 0.4);
        color: white; /* Ensure text color remains white on hover */
    }

    /* Adjust existing buttons slightly if needed, or add new specific classes for them */
    .course-actions .btn-action {
        /* Ensure consistent styling with the new join button if it also uses btn-action */
        flex: 1; /* Make all action buttons in the card take equal width */
        white-space: nowrap; /* Prevent text wrapping on smaller buttons */
        font-size: 0.85rem; /* Slightly smaller font for more buttons */
    }

    /* Specific adjustments for smaller screens if buttons get too cramped */
    @media (max-width: 576px) {
        .course-actions {
            flex-direction: column; /* Stack buttons vertically on very small screens */
        }
        .course-actions .btn-action,
        .course-actions .btn-join-card {
            width: 100%; /* Make them full width */
        }
    }
    a.btn.btn-reset {
    color: #fffefe !important;
    background-color: #e93535;
}
.btn-view {
    background: linear-gradient(135deg,rgb(206, 49, 49) 0%, #ff5a1b 100%);
    color: white;
}
</style>
@endpush

@section('content')
<div class="animate-fade-in">
    <div class="course-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1><i class="fas fa-graduation-cap me-3"></i>Gestion des Cours</h1>
                <p>Gérez vos formations et cours efficacement</p>
            </div>
            <div class="col-md-4 text-end">
                
                @can('course-create')
                    <button type="button" class="btn-new-course" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                        <i class="fas fa-plus me-2"></i>Nouveau Cours
                    </button>
                @endcan
            </div>
        </div>
    </div>

    <div class="filter-section">
        <div class="filter-title">
            <i class="fas fa-filter"></i>
            Filtres de recherche
        </div>

        <form method="GET" action="{{ route('courses.index') }}">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Recherche</label>
                    <div class="position-relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                               class="form-control ps-4" placeholder="Titre du cours...">
                        <i class="fas fa-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #a0aec0;"></i>
                    </div>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Date début</label>
                    <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                </div>

                {{-- Filter by Formation (Visible to all users, but list depends on controller) --}}
                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold">Formation</label>
                    <select name="filter_formation_id" class="form-control">
                        <option value="">Toutes les formations</option>
                        @foreach($formationsForFilter as $formation)
                            <option value="{{ $formation->id }}" {{ request('filter_formation_id') == $formation->id ? 'selected' : '' }}>
                                {{ $formation->title }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3 mb-3">
                    <label class="form-label fw-semibold"> </label>
                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-filter">
                            <i class="fas fa-search me-2"></i>Filtrer
                        </button>
                        <a href="{{ route('courses.index') }}" class="btn btn-reset">
                            <i class="fas fa-undo me-2"></i>Reset
                        </a>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="row">
        @forelse($courses as $course)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="course-card">
                    <div class="course-card-header">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="course-icon">
                                    <i class="fas fa-book"></i>
                                </div>
                                <div>
                                    <h5 class="course-title">{{ Str::limit($course->title, 30) }}</h5>
                                    <p class="course-formation">{{ $course->formation->title }}</p>
                                </div>
                            </div>
                            <div class="course-date-badge">
                                {{ \Carbon\Carbon::parse($course->course_date)->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>

                    <div class="course-card-body">
                        <p class="course-description">{{ Str::limit($course->description, 100) }}</p>

                        <div class="course-info">
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <span>{{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}</span>
                            </div>

                            @if($course->zoom_link)
                                <div class="info-item">
                                    <i class="fas fa-video"></i>
                                    <span>Lien Zoom disponible</span>
                                </div>
                            @endif

                            @if($course->documents && count($course->documents) > 0)
                                <div class="info-item">
                                    <i class="fas fa-file-alt"></i>
                                    <span>{{ count($course->documents) }} document(s)</span>
                                </div>
                            @endif
                            @if($course->consultant)
                                <div class="info-item">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Consultant: <strong>{{ $course->consultant->name }}</strong></span>
                                </div>
                            @endif
                        </div>

                        <div class="course-actions">
                            {{-- "Rejoindre" button --}}
                           
                                @if($course->zoom_link)
                                    <form action="{{ route('courses.join', $course) }}" method="POST" class="d-inline flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn-join-card">
                                            <i class="fas fa-door-open"></i> Rejoindre
                                        </button>
                                    </form>
                                @endif
                         

                           
                                <a href="{{ route('courses.show', $course) }}" class="btn-action btn-view">
                                    <i class="fas fa-eye"></i>
                                    <span>Voir</span>
                                </a>
                            

                            {{-- "Modifier" button --}}
                            @can('course-edit') {{-- Policy check for 'update' on this specific course --}}
                                <button type="button" 
      class="btn-action btn-edit"
        data-bs-toggle="modal" 
        data-bs-target="#editCourseModal"
        data-course-id="{{ $course->id }}" 
        data-formation-id="{{ $course->formation_id }}"
        data-consultant-id="{{ $course->consultant_id }}"
        data-title="{{ $course->title }}" 
        data-description="{{ $course->description }}"
        data-course-date="{{ \Carbon\Carbon::parse($course->course_date)->format('Y-m-d') }}"
        data-start-time="{{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }}"
        data-end-time="{{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}"
        data-zoom-link="{{ $course->zoom_link }}"
        data-recording-url="{{ $course->recording_url }}"
        data-documents="{{ json_encode($course->documents) }}">
    <i class="fas fa-edit"></i> Modifier
</button>
                            @endcan

                            {{-- "Supprimer" button --}}
                            @can('course-delete') {{-- Policy check for 'delete' on this specific course --}}
                                <button onclick="confirmDelete({{ $course->id }})" class="btn-action btn-delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-book-open"></i>
                    </div>
                    <h3 class="empty-title">Aucun cours trouvé</h3>
                    <p class="empty-description">
                        @can('course-create')
                            Commencez par créer votre premier cours
                        @else
                            Aucun cours n'est visible pour le moment.
                        @endcan
                    </p>
                    {{-- Only show "Créer un cours" button if user can create courses --}}
                    @can('course-create')
                        <button type="button" class="btn-new-course" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                            <i class="fas fa-plus me-2"></i>Créer un cours
                        </button>
                    @endcan
                </div>
            </div>
        @endforelse
    </div>

    @if($courses->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <div class="bg-white rounded-3 shadow-sm p-3">
                {{ $courses->links() }}
            </div>
        </div>
    @endif
</div>

{{-- Modals (Create & Edit) and Delete Confirmation are conditionally shown by Policy check in Controller --}}

<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmer la suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="mb-0">Êtes-vous sûr de vouloir supprimer ce cours ? Cette action est irréversible.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
 @can('course-create')
{{-- This modal itself doesn't need @can, as access to its content is controlled by the form submission/controller --}}
{{-- However, the button that *triggers* it is protected by @can --}}
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCourseModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>
                    Créer un Nouveau Cours
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="formation_id" class="form-label modal-form-label">Formation</label>
                            <select name="formation_id" id="formation_id" class="form-control @error('formation_id') is-invalid @enderror" required>
                                <option value="">Sélectionnez une formation</option>
                                @foreach($formationsForModals as $formation)
                                    <option value="{{ $formation->id }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                                        {{ $formation->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('formation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_course_title" class="form-label modal-form-label">Titre du Cours</label>
                            <input type="text" name="title" id="create_course_title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="consultant_id" class="form-label modal-form-label">Consultant</label>
                        <select name="consultant_id" id="consultant_id" class="form-control @error('consultant_id') is-invalid @enderror">
                            <option value="">Sélectionnez un consultant (Optionnel)</option>
                            @foreach($consultants as $consultant)
                                <option value="{{ $consultant->id }}" {{ old('consultant_id') == $consultant->id ? 'selected' : '' }}>
                                    {{ $consultant->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('consultant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="create_course_description" class="form-label modal-form-label">Description</label>
                        <textarea name="description" id="create_course_description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="create_course_date" class="form-label modal-form-label">Date du Cours</label>
                            <input type="date" name="course_date" id="create_course_date" class="form-control @error('course_date') is-invalid @enderror" value="{{ old('course_date') }}" required>
                            @error('course_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="create_start_time" class="form-label modal-form-label">Heure de Début</label>
                            <input type="time" name="start_time" id="create_start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="create_end_time" class="form-label modal-form-label">Heure de Fin</label>
                            <input type="time" name="end_time" id="create_end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_zoom_link" class="form-label modal-form-label">Lien Zoom (Optionnel)</label>
                        <input type="url" name="zoom_link" id="create_zoom_link" class="form-control @error('zoom_link') is-invalid @enderror" value="{{ old('zoom_link') }}" placeholder="https://zoom.us/j/123456789">
                        @error('zoom_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="create_documents" class="form-label modal-form-label">Documents (PDF, DOCX, PPTX - Max 10MB par fichier)</label>
                        <input type="file" name="documents[]" id="create_documents" class="form-control @error('documents.*') is-invalid @enderror" multiple>
                        @error('documents.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-new-course">
                        <i class="fas fa-save"></i> Créer le Cours
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
   @endcan
 @can('course-edit')
{{-- This modal itself doesn't need @can, as access to its content is controlled by the form submission/controller --}}
{{-- However, the button that *triggers* it is protected by @can --}}
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCourseModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    Modifier le Cours
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCourseForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="edit_formation_id" class="form-label modal-form-label">Formation</label>
                            <select name="formation_id" id="edit_formation_id" class="form-control @error('formation_id') is-invalid @enderror" required>
                                <option value="">Sélectionnez une formation</option>
                                @foreach($formationsForModals as $formation)
                                    <option value="{{ $formation->id }}">{{ $formation->title }}</option>
                                @endforeach
                            </select>
                            @error('formation_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="edit_title" class="form-label modal-form-label">Titre du Cours</label>
                            <input type="text" name="title" id="edit_title" class="form-control @error('title') is-invalid @enderror" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_consultant_id" class="form-label modal-form-label">Consultant</label>
                        <select name="consultant_id" id="edit_consultant_id" class="form-control @error('consultant_id') is-invalid @enderror">
                            <option value="">Sélectionnez un consultant (Optionnel)</option>
                            @foreach($consultants as $consultant)
                                <option value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                            @endforeach
                        </select>
                        @error('consultant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label modal-form-label">Description</label>
                       <textarea name="description" id="edit_description" class="form-control @error('description') is-invalid @enderror" rows="4" required></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                   <div class="col-md-4 mb-3">
    <label for="edit_course_date" class="form-label modal-form-label">Date du Cours</label>
    {{-- Khwi la valeur hna --}}
    <input type="date" name="course_date" id="edit_course_date" class="form-control @error('course_date') is-invalid @enderror" required>
    @error('course_date')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-4 mb-3">
    <label for="edit_start_time" class="form-label modal-form-label">Heure de Début</label>
    {{-- Khwi la valeur hna --}}
    <input type="time" name="start_time" id="edit_start_time" class="form-control @error('start_time') is-invalid @enderror" required>
    @error('start_time')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

<div class="col-md-4 mb-3">
    <label for="edit_end_time" class="form-label modal-form-label">Heure de Fin</label>
    {{-- Khwi la valeur hna --}}
    <input type="time" name="end_time" id="edit_end_time" class="form-control @error('end_time') is-invalid @enderror" required>
    @error('end_time')
        <div class="invalid-feedback">{{ $message }}</div>
    @enderror
</div>

                    <div class="mb-3">
                        <label for="edit_zoom_link" class="form-label modal-form-label">Lien Zoom (Optionnel)</label>
                        <input type="url" name="zoom_link" id="edit_zoom_link" class="form-control @error('zoom_link') is-invalid @enderror" placeholder="https://zoom.us/j/123456789">
                        @error('zoom_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit_recording_url" class="form-label modal-form-label">Lien d'enregistrement (Optionnel)</label>
                        <input type="url" name="recording_url" id="edit_recording_url" class="form-control @error('recording_url') is-invalid @enderror" placeholder="https://youtube.com/watch?v=...">
                        @error('recording_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit_documents" class="form-label modal-form-label">Documents (PDF, DOCX, PPTX - Max 10MB par fichier)</label>
                        <input type="file" name="documents[]" id="edit_documents" class="form-control @error('documents.*') is-invalid @enderror" multiple>
                        @error('documents.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="existing_documents" class="mt-2">
                            {{-- Existing documents will be loaded here by JavaScript --}}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-edit">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
   @endcan
@endsection

@push('scripts')
<script>
    function confirmDelete(courseId) {
        document.getElementById('deleteForm').action = `/courses/${courseId}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.course-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        // Show create course modal if there are validation errors on page load
        @if ($errors->hasAny(['formation_id', 'title', 'description', 'course_date', 'start_time', 'end_time', 'zoom_link', 'documents.*', 'consultant_id']))
            var createCourseModal = new bootstrap.Modal(document.getElementById('createCourseModal'));
            createCourseModal.show();
        @endif

        // Show edit course modal if there are validation errors for an update
        @if ($errors->any() && session('edit_course_id'))
            var courseId = {{ session('edit_course_id') }};
            var editCourseModalElement = document.getElementById('editCourseModal');
            var editModal = new bootstrap.Modal(editCourseModalElement);

            // Manually re-populate the edit form with old input in case of validation errors
            editCourseModalElement.querySelector('#edit_formation_id').value = "{{ old('formation_id', '') }}";
            editCourseModalElement.querySelector('#edit_consultant_id').value = "{{ old('consultant_id', '') }}";
            editCourseModalElement.querySelector('#edit_title').value = "{{ old('title', '') }}";
            editCourseModalElement.querySelector('#edit_description').value = "{{ old('description', '') }}";
            editCourseModalElement.querySelector('#edit_course_date').value = "{{ old('course_date', '') }}";
            editCourseModalElement.querySelector('#edit_start_time').value = "{{ old('start_time', '') }}";
            editCourseModalElement.querySelector('#edit_end_time').value = "{{ old('end_time', '') }}";
            editCourseModalElement.querySelector('#edit_zoom_link').value = "{{ old('zoom_link', '') }}";
            editCourseModalElement.querySelector('#edit_recording_url').value = "{{ old('recording_url', '') }}";

            editCourseModalElement.querySelector('#editCourseForm').action = `/courses/${courseId}`;

            editModal.show();
        @endif


        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert) {
                    alert.classList.add('fade');
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);

        // --- JavaScript for Edit Course Modal ---
        const editCourseModal = document.getElementById('editCourseModal');
        editCourseModal.addEventListener('show.bs.modal', event => {
            const button = event.relatedTarget;

            const courseId = button.getAttribute('data-course-id');
            const formationId = button.getAttribute('data-formation-id');
            const consultantId = button.getAttribute('data-consultant-id');
            const title = button.getAttribute('data-title');
            const description = button.getAttribute('data-description');
            const courseDate = button.getAttribute('data-course-date');
            const startTime = button.getAttribute('data-start-time');
            const endTime = button.getAttribute('data-end-time');
            const zoomLink = button.getAttribute('data-zoom-link');
            const recordingUrl = button.getAttribute('data-recording-url');
            const documents = JSON.parse(button.getAttribute('data-documents'));

            const modalForm = editCourseModal.querySelector('#editCourseForm');
            const modalFormationId = editCourseModal.querySelector('#edit_formation_id');
            const modalConsultantId = editCourseModal.querySelector('#edit_consultant_id');
            const modalTitle = editCourseModal.querySelector('#edit_title');
            const modalDescription = editCourseModal.querySelector('#edit_description');
            const modalCourseDate = editCourseModal.querySelector('#edit_course_date');
            const modalStartTime = editCourseModal.querySelector('#edit_start_time');
            const modalEndTime = editCourseModal.querySelector('#edit_end_time');
            const modalZoomLink = editCourseModal.querySelector('#edit_zoom_link');
            const modalRecordingUrl = editCourseModal.querySelector('#edit_recording_url');
            const existingDocumentsDiv = editCourseModal.querySelector('#existing_documents');

            modalForm.action = `/courses/${courseId}`;

            modalFormationId.value = formationId;
            modalConsultantId.value = consultantId;
            modalTitle.value = title;
            modalDescription.value = description;
            modalCourseDate.value = courseDate;
            modalStartTime.value = startTime;
            modalEndTime.value = endTime;
            modalZoomLink.value = zoomLink;
            modalRecordingUrl.value = recordingUrl;

            existingDocumentsDiv.innerHTML = '';

            if (documents && documents.length > 0) {
                const documentsList = document.createElement('ul');
                documentsList.classList.add('list-group', 'mt-2');
                documents.forEach((doc, index) => {
                    const listItem = document.createElement('li');
                    listItem.classList.add('list-group-item', 'd-flex', 'justify-content-between', 'align-items-center');
                    listItem.innerHTML = `
                        <span>
                            <i class="fas fa-file-alt me-2 text-primary"></i>
                            ${doc.name}
                        </span>
                        <span>
                            <a href="/courses/${courseId}/download-document?document_index=${index}" class="btn btn-sm btn-info me-2" download>
                                <i class="fas fa-download"></i>
                            </a>
                            <button type="button" class="btn btn-sm btn-danger remove-document-btn" data-course-id="${courseId}" data-document-index="${index}">
                                <i class="fas fa-times"></i>
                            </button>
                        </span>
                    `;
                    documentsList.appendChild(listItem);
                });
                existingDocumentsDiv.appendChild(documentsList);

                existingDocumentsDiv.querySelectorAll('.remove-document-btn').forEach(button => {
                    button.addEventListener('click', function() {
                        const courseId = this.dataset.courseId;
                        const documentIndex = this.dataset.documentIndex;
                        const listItem = this.closest('.list-group-item');

                        if (confirm('Are you sure you want to remove this document?')) {
                            fetch(`/courses/${courseId}/remove-document?document_index=${documentIndex}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                    'Content-Type': 'application/json'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                if (data.success) {
                                    listItem.remove();
                                    const infoItem = document.querySelector(`.course-card [data-course-id="${courseId}"]`).closest('.course-card-body').querySelector('.info-item span');
                                    if (infoItem) {
                                        let currentCount = parseInt(infoItem.textContent.match(/\d+/)) || 0;
                                        infoItem.textContent = `${currentCount - 1} document(s)`;
                                        if (currentCount - 1 === 0) {
                                            infoItem.closest('.info-item').remove();
                                        }
                                    }
                                    alert('Document removed successfully!');
                                } else {
                                    alert('Failed to remove document.');
                                }
                            })
                            .catch(error => {
                                console.error('Error removing document:', error);
                                alert('An error occurred while removing the document.');
                            });
                        }
                    });
                });
            }
        });
    });

</script>
@endpush

