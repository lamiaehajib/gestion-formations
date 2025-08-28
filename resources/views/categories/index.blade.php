@extends('layouts.app')

@section('title', 'Gestion des Catégories')

@section('content')
<div class="container-fluid animate__animated animate__fadeIn">
    <div class="row">
        <div class="col-12">
            <div class="card modern-card shadow-lg border-0 rounded-4">
                <div class="card-header modern-header text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0 d-flex align-items-center">
                            <i class="fas fa-layer-group me-3 floating-icon"></i>
                            Gestion des Catégories
                        </h3>
                        <div class="d-flex gap-3">
                            {{-- Button to open the Create Category Modal --}}
                            <button type="button" class="btn btn-primary btn-modern shadow-sm" data-bs-toggle="modal" data-bs-target="#createCategoryModal">
                                <i class="fas fa-plus pulse-icon"></i>
                                <span>Ajouter Catégorie</span>
                            </button>

                            <a href="{{ route('categories.export') }}" class="btn btn-success btn-modern shadow-sm">
                                <i class="fas fa-download bounce-icon"></i>
                                <span>Exporter CSV</span>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form method="GET" class="mb-4 animate__animated animate__fadeInUp">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="input-group-modern">
                                    <span class="input-icon">
                                        <i class="fas fa-search search-icon"></i>
                                    </span>
                                    <input type="text"
                                            name="search"
                                            class="form-control form-control-modern"
                                            placeholder="Rechercher par nom..."
                                            value="{{ request('search') }}">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <select name="status" class="form-control form-control-modern">
                                    <option value="">Tous les statuts</option>
                                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactif</option>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <button type="submit" class="btn btn-primary btn-modern w-100">
                                    <i class="fas fa-filter rotate-icon"></i>
                                    <span>Filtrer</span>
                                </button>
                            </div>
                            <div class="col-md-3">
                                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary btn-modern w-100">
                                    <i class="fas fa-refresh spin-icon"></i>
                                    <span>Réinitialiser</span>
                                </a>
                            </div>
                        </div>
                    </form>

                    <form id="bulk-action-form" method="POST" action="{{ route('categories.bulk-action') }}">
                        @csrf
                        <div class="row mb-4 animate__animated animate__fadeInUp animate__delay-1s">
                            <div class="col-md-6">
                                <div class="input-group input-group-modern">
                                    <select name="action" class="form-control form-control-modern" id="bulk-action">
                                        <option value="">Actions groupées...</option>
                                        <option value="activate">Activer</option>
                                        <option value="deactivate">Désactiver</option>
                                        <option value="delete">Supprimer</option>
                                    </select>
                                    <div class="input-group-append">
                                        <button type="submit" class="btn btn-secondary btn-modern" id="apply-bulk-action" disabled>
                                            <i class="fas fa-play"></i>
                                            Appliquer
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-modern align-middle">
                                <thead> {{-- Removed table-head-modern here, it will be added by CSS for thead --}}
                                    <tr>
                                       <th width="50">
            {{-- CORRECTED: The actual input is INSIDE the custom label, like your individual checkboxes --}}
            <label class="checkbox-modern">
                <input type="checkbox" id="select-all" class="form-check-input">
                <span class="checkmark"></span>
            </label>
        </th>
                                        <th>
                                            <i class="fas fa-tag me-2"></i>Nom
                                        </th>
                                        <th>
                                            <i class="fas fa-align-left me-2"></i>Description
                                        </th>
                                        <th>
                                            <i class="fas fa-toggle-on me-2"></i>Statut
                                        </th>
                                        <th>
                                            <i class="fas fa-graduation-cap me-2"></i>Formations
                                        </th>
                                        <th>
                                            <i class="fas fa-calendar me-2"></i>Date de création
                                        </th>
                                        @can('category-create')
                                        <th width="200">
                                            <i class="fas fa-cogs me-2"></i>Actions
                                        </th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($categories as $category)
                                    <tr class="table-row-modern animate__animated animate__fadeIn animate__delay-2s">
                                        <td>
                                            {{-- Keep the input inside the label here, as the CSS fix should handle it --}}
                                            <label class="checkbox-modern">
                                                <input type="checkbox" name="categories[]" value="{{ $category->id }}" class="form-check-input category-checkbox">
                                                <span class="checkmark"></span>
                                            </label>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="category-icon me-3">
                                                    <i class="fas fa-folder"></i>
                                                </div>
                                                <strong>{{ $category->name }}</strong>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="text-muted">{{ Str::limit($category->description, 80) }}</span>
                                        </td>
                                        <td>
                                            @if($category->is_active)
                                                <span class="badge badge-success">
                                                    <i class="fas fa-check-circle me-1"></i>Actif
                                                </span>
                                            @else
                                                <span class="badge badge-secondary">
                                                    <i class="fas fa-times-circle me-1"></i>Inactif
                                                </span>
                                            @endif
                                        </td>
                                        <td>
    <span class="badge badge-info">
        <i class="fas fa-graduation-cap me-1"></i>
        {{ $category->formations_count ?? 0 }}
    </span>
</td>
                                        <td>
                                            <span class="text-muted">{{ $category->created_at->format('d/m/Y H:i') }}</span>
                                        </td>
                                        @can('category-create')
                                        <td>
                                            <div class="btn-group-modern" role="group">
                                                @can('category-create')
                                                <a href="{{ route('categories.show', $category) }}"
                                                   class="btn btn-sm btn-info btn-action"
                                                   title="Voir"
                                                   data-bs-toggle="tooltip">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                @endcan
                                                @can('category-edit')
                                                {{-- Modified Edit Button to open modal --}}
                                                <button type="button"
                                                        class="btn btn-sm btn-warning btn-action"
                                                        title="Modifier"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#editCategoryModal"
                                                        data-category-id="{{ $category->id }}"
                                                        data-category-name="{{ $category->name }}"
                                                        data-category-description="{{ $category->description }}"
                                                        data-category-is-active="{{ $category->is_active }}">
                                                    <i class="fas fa-edit"></i>
                                                </button>
                                                @endcan
                                                <form method="POST" action="{{ route('categories.toggle-status', $category) }}" style="display: inline;">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit"
                                                            class="btn btn-sm {{ $category->is_active ? 'btn-secondary' : 'btn-success' }} btn-action"
                                                            title="{{ $category->is_active ? 'Désactiver' : 'Activer' }}"
                                                            data-bs-toggle="tooltip">
                                                           <i class="fas {{ $category->is_active ? 'fa-ban' : 'fa-check' }}"></i>
                                                    </button>
                                                </form>
                                                {{-- Changed to button to open modal, removed onsubmit --}}
                                                <button type="button" class="btn btn-sm btn-danger btn-action" title="Supprimer"
                                                            data-bs-toggle="modal" data-bs-target="#deleteCategoryModal" data-category-id="{{ $category->id }}">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                        @endcan
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="8" class="text-center text-muted py-5">
                                            <div class="empty-state">
                                                <i class="fas fa-folder-open empty-icon"></i>
                                                <p class="mt-3 mb-0">Aucune catégorie trouvée</p>
                                                <small class="text-muted">Créez votre première catégorie pour commencer</small>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </form>

                    <div class="d-flex justify-content-center mt-4">
                        {{ $categories->links('pagination::bootstrap-5') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Delete Category Modal --}}
<div class="modal fade" id="deleteCategoryModal" tabindex="-1" aria-labelledby="deleteCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content animate__animated animate__zoomIn">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteCategoryModalLabel"><i class="fas fa-exclamation-triangle me-2"></i>Confirmer la Suppression</h5>
                <button type="button" class="btn-close btn-close-black" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center py-4">
                <p class="lead">Êtes-vous sûr de vouloir supprimer cette catégorie ?</p>
                <p class="text-muted">Cette action est irréversible et supprimera également toutes les données associées.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><i class="fas fa-times me-1"></i> Annuler</button>
                <form id="deleteCategoryForm" method="POST" action="">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger"><i class="fas fa-trash-alt me-1"></i> Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Create Category Modal --}}
<div class="modal fade" id="createCategoryModal" tabindex="-1" aria-labelledby="createCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content animate__animated animate__fadeInDown">
            <div class="modal-header modern-header text-white">
                <h5 class="modal-title" id="createCategoryModalLabel"><i class="fas fa-plus-circle me-2"></i>Ajouter une nouvelle catégorie</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="{{ route('categories.store') }}">
                @csrf
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="modal-name" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control form-control-modern @error('name') is-invalid @enderror"
                                       id="modal-name"
                                       name="name"
                                       value="{{ old('name') }}"
                                       placeholder="Ex: Développement Web"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="modal-description" class="form-label">Description</label>
                                <textarea class="form-control form-control-modern @error('description') is-invalid @enderror"
                                          id="modal-description"
                                          name="description"
                                          rows="4"
                                          placeholder="Description de la catégorie...">{{ old('description') }}</textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check form-switch mt-3"> {{-- Use Bootstrap 5 form-switch --}}
                                    <input type="checkbox"
                                           class="form-check-input"
                                           id="modal-is_active"
                                           name="is_active"
                                           value="1"
                                           {{ old('is_active', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="modal-is_active">
                                        Catégorie active
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Les catégories inactives ne sont pas visibles dans les formulaires
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-modern" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary btn-modern">
                        <i class="fas fa-save me-1"></i> Enregistrer la catégorie
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

{{-- New Edit Category Modal --}}
<div class="modal fade" id="editCategoryModal" tabindex="-1" aria-labelledby="editCategoryModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content animate__animated animate__fadeInDown">
            <div class="modal-header modern-header text-white">
                <h5 class="modal-title" id="editCategoryModalLabel"><i class="fas fa-edit me-2"></i>Modifier la catégorie</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCategoryForm" method="POST" action="">
                @csrf
                @method('PUT')
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="edit-name" class="form-label">Nom de la catégorie <span class="text-danger">*</span></label>
                                <input type="text"
                                       class="form-control form-control-modern @error('name') is-invalid @enderror"
                                       id="edit-name"
                                       name="name"
                                       placeholder="Ex: Développement Web"
                                       required>
                                @error('name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-12">
                            <div class="form-group">
                                <label for="edit-description" class="form-label">Description</label>
                                <textarea class="form-control form-control-modern @error('description') is-invalid @enderror"
                                          id="edit-description"
                                          name="description"
                                          rows="4"
                                          placeholder="Description de la catégorie..."></textarea>
                                @error('description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <div class="form-check form-switch mt-3">
                                    <input type="checkbox"
                                           class="form-check-input"
                                           id="edit-is_active"
                                           name="is_active"
                                           value="1">
                                    <label class="form-check-label" for="edit-is_active">
                                        Catégorie active
                                    </label>
                                </div>
                                <small class="form-text text-muted">
                                    Les catégories inactives ne sont pas visibles dans les formulaires
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-modern" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Annuler
                    </button>
                    <button type="submit" class="btn btn-primary btn-modern">
                        <i class="fas fa-save me-1"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


{{-- The Floating Action Button (FAB) will now open the modal --}}
@can('category-create')
<button type="button" class="fab-button shadow-lg" data-bs-toggle="modal" data-bs-target="#createCategoryModal" title="Ajouter une nouvelle catégorie">
    <i class="fas fa-plus fab-icon animate-pulse-static"></i>
</button>
@endcan

@push('styles')
<style>
    :root {
    --primary-color: #D32F2F; /* Dark Red */
    --secondary-color: #C2185B; /* Crimson */
    --success-color: #4CAF50;
    --warning-color: #FF9800;
    --danger-color: #D32F2F; /* Matching primary for danger */
    --info-color: #C2185B; /* Matching secondary for info */
    --light-color: #f8f9fa;
    --dark-color: #2c3e50;
    --border-radius: 12px;
    --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    --transition: all 0.3s ease;
    --gradient-primary: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    --gradient-accent: linear-gradient(135deg, #17a2b8, #6f42c1); /* Example accent for icons */
    --shadow-light: 0 2px 10px rgba(0, 0, 0, 0.05);
    --shadow-medium: 0 5px 15px rgba(0, 0, 0, 0.1);
    --shadow-heavy: 0 10px 20px rgba(0, 0, 0, 0.15);
}

    /* Card moderne */
    .modern-card {
        background: #fff;
        border: 1px solid rgba(211, 47, 47, 0.1);
        box-shadow: var(--shadow-light);
        transition: var(--transition);
        overflow: hidden;
    }

    .modern-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-heavy);
    }

    .modern-header {
        background: var(--gradient-primary);
        padding: 1.5rem 2rem;
        border-bottom: none;
        position: relative;
        overflow: hidden;
    }

    .modern-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(45deg, transparent, rgba(255,255,255,0.1), transparent);
        transform: translateX(-100%);
        animation: shimmer 3s infinite;
    }

    /* Boutons modernes */
    .btn-modern {
        padding: 0.75rem 1.5rem;
        border-radius: var(--border-radius);
        font-weight: 500;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
        border: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-modern::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.3s ease, height 0.3s ease;
    }

    .btn-modern:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: var(--shadow-medium);
    }

    .btn-primary.btn-modern {
        background: var(--gradient-primary);
        color: white;
    }

    .btn-success.btn-modern {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .btn-outline-secondary.btn-modern {
        background: transparent;
        border: 2px solid #6c757d;
        color: #6c757d;
    }

    .btn-outline-secondary.btn-modern:hover {
        background: #6c757d;
        color: white;
    }

    /* Inputs modernes */
    .input-group-modern {
        position: relative;
    }

    .input-icon {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-color);
        z-index: 10;
    }

    .form-control-modern {
        padding: 0.75rem 1rem 0.75rem 3rem;
        border: 2px solid #e9ecef;
        border-radius: var(--border-radius);
        transition: var(--transition);
        background: #fff;
    }

    .form-control-modern:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 0.2rem rgba(211, 47, 47, 0.1);
        background: #fff;
    }

    .form-control-modern::placeholder {
        color: #adb5bd;
        font-style: italic;
    }

    /* Table moderne */
    .table-modern {
        background: #fff;
        border-radius: var(--border-radius);
        overflow: hidden;
        box-shadow: var(--shadow-light);
    }

    .table-head-modern {
        background: var(--gradient-primary);
        color: white;
    }

    .table-head-modern th {
        padding: 1rem;
        font-weight: 600;
        border: none;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        vertical-align: middle;
    }

    .table-row-modern {
        transition: var(--transition);
        border-bottom: 1px solid rgba(211, 47, 47, 0.1);
    }

    .table-row-modern:hover {
        background: rgba(211, 47, 47, 0.05);
        transform: scale(1.01);
    }

    .table-row-modern td {
        padding: 1rem;
        vertical-align: middle;
        border: none;
    }

    /* Icônes catégorie */
    .category-icon {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        background: var(--gradient-accent);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 0.9rem;
    }

    /* Badges modernes */
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 500;
        font-size: 0.8rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .badge-success {
        background: linear-gradient(135deg, #28a745, #20c997);
        color: white;
    }

    .badge-secondary {
        background: linear-gradient(135deg, #6c757d, #adb5bd);
        color: white;
    }

    .badge-info {
        background: linear-gradient(135deg, #17a2b8, #6f42c1);
        color: white;
    }

    /* Groupe de boutons d'actions */
    .btn-group-modern {
        display: flex;
        gap: 0.5rem;
    }

    .btn-action {
        width: 35px;
        height: 35px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .btn-action::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        background: rgba(255,255,255,0.3);
        border-radius: 50%;
        transform: translate(-50%, -50%);
        transition: width 0.3s ease, height 0.3s ease;
    }

    .btn-action:hover::before {
        width: 100px;
        height: 100px;
    }

    .btn-action:hover {
        transform: translateY(-2px) scale(1.1);
    }

    /* --- Checkbox moderne FIX --- */
    .checkbox-modern {
        display: block;
        position: relative;
        padding-left: 30px; /* Space for the custom checkbox */
        margin-bottom: 0;
        cursor: pointer;
        font-size: 1rem;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
    }

    .checkbox-modern input {
        position: absolute;
        opacity: 0;
        cursor: pointer;
        height: 100%; /* Make the hidden input take up full height of its container */
        width: 100%;  /* Make the hidden input take up full width of its container */
        top: 0;
        left: 0;
        z-index: 1; /* Ensure it's above the checkmark for click events */
    }

    .checkmark {
        position: absolute;
        top: 0;
        left: 0;
        height: 20px;
        width: 20px;
        background-color: #eee;
        border: 2px solid var(--primary-color);
        border-radius: 4px;
        transition: background-color 0.3s ease, border-color 0.3s ease;
    }

    .checkbox-modern:hover input ~ .checkmark {
        background-color: #ccc;
    }

    .checkbox-modern input:checked ~ .checkmark {
        background: var(--gradient-primary);
        border-color: var(--primary-color);
    }

    .checkmark:after {
        content: "";
        position: absolute;
        display: none;
    }

    .checkbox-modern input:checked ~ .checkmark:after {
        display: block;
    }

    .checkbox-modern .checkmark:after {
        left: 5px;
        top: 2px;
        width: 6px;
        height: 10px;
        border: solid white;
        border-width: 0 2px 2px 0;
        -webkit-transform: rotate(45deg);
        -ms-transform: rotate(45deg);
        transform: rotate(45deg);
    }
    /* --- END Checkbox moderne FIX --- */

    /* État vide */
    .empty-state {
        padding: 3rem 2rem;
    }

    .empty-icon {
        font-size: 4rem;
        color: var(--primary-color);
        opacity: 0.3;
        animation: float 3s ease-in-out infinite;
    }

    /* Animations */
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes bounce {
        0%, 100% { transform: translateY(0); }
        50% { transform: translateY(-5px); }
    }

    @keyframes rotate {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Icônes animées */
    .floating-icon {
        animation: float 3s ease-in-out infinite;
    }

    .pulse-icon {
        animation: pulse 2s infinite;
    }

    .bounce-icon {
        animation: bounce 2s infinite;
    }

    .search-icon {
        animation: pulse 2s infinite;
    }

    .rotate-icon {
        transition: transform 0.3s ease;
    }

    .btn-modern:hover .rotate-icon {
        transform: rotate(180deg);
    }

    .spin-icon {
        transition: transform 0.3s ease;
    }

    .btn-modern:hover .spin-icon {
        animation: spin 1s linear infinite;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .modern-header {
            padding: 1rem;
        }

        .modern-header h3 {
            font-size: 1.2rem;
        }

        .btn-modern {
            padding: 0.5rem 1rem;
            font-size: 0.9rem;
        }

        .btn-group-modern {
            flex-wrap: wrap;
        }

        .table-modern {
            font-size: 0.9rem;
        }
    }

    /* Améliorations d'accessibilité */
    .btn-modern:focus,
    .form-control-modern:focus,
    .btn-action:focus {
        outline: 2px solid var(--primary-color);
        outline-offset: 2px;
    }

    /* Transitions fluides pour tous les éléments interactifs */
    .btn, .form-control, .badge, .card, .table-row-modern {
        transition: var(--transition);
    }
    .shadow-sm {
    box-shadow: 0 .125rem .25rem rgba(0, 0, 0, .075) !important;
    background-color: black !important;
    border: none !important;
}
.btn-outline-secondary.btn-modern {
    background: #000000;
    border: 2px solid #080808;
    color: #6c757d;
}
.table-head-modern {
    background-color: #1a1a1a; /* A strong black, slightly off-black for depth */
    color: white; /* Make the text white */
}

.table-modern thead th {
    background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    color: white;
    border: none;
    padding: 1rem 0.75rem;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.8rem;
    letter-spacing: 0.5px;
    vertical-align: middle; /* Align text vertically */
}
.fab-button {
    position: fixed;
    bottom: 120px; /* Distance from bottom */
    right: 30px;  /* Distance from right */
    width: 60px;  /* Width of the circle */
    height: 60px; /* Height of the circle */
    border-radius: 50%; /* Makes it perfectly round */
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); /* Your red/crimson gradient */
    color: white; /* Icon color */
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1.8rem; /* Size of the plus icon */
    text-decoration: none; /* Remove underline */
    z-index: 1050; /* Ensure it's above other content and modals */
    transition: transform 0.3s ease, box-shadow 0.3s ease, background 0.3s ease;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3); /* Initial shadow */
}

.fab-button:hover {
    transform: translateY(-5px) scale(1.05); /* Slight lift and scale on hover */
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.4); /* Enhanced shadow on hover */
    /* Subtle gradient shift on hover - you can adjust colors if needed */
    background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
    color: white;
}

/* Animation for the FAB icon (continuous pulse) */
.fab-icon.animate-pulse-static {
    animation: fabPulse 2s infinite ease-in-out;
}

@keyframes fabPulse {
    0% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
    100% { transform: scale(1); opacity: 1; }
}
button.btn.btn-primary.btn-modern.w-100 {
    background-color: #0d6efd !important;
}

.mb-0 {
    margin-bottom: 0 !important;
    color: #dc3545 !important;
}

    /* Modal specific styles */
    .modal-content {
        border-radius: var(--border-radius);
        overflow: hidden; /* Ensures background gradient rounds correctly */
    }

    .modal-header.modern-header {
        /* Already styled by .modern-header */
        padding: 1.5rem 2rem; /* Consistent with your card header */
    }

    .modal-header .btn-close-white {
        filter: invert(1) grayscale(100%) brightness(200%); /* Makes close button white */
    }

    .modal-footer {
        background-color: #f8f9fa; /* Light background for footer */
        border-top: 1px solid #e9ecef;
        padding: 1.25rem 2rem;
    }

    .form-check.form-switch { /* For Bootstrap 5 switch style */
        padding-left: 3.5em; /* Adjust padding for switch */
    }
    .form-check-input:checked {
        background-color: var(--primary-color);
        border-color: var(--primary-color);
    }
    .form-check-input:focus {
        box-shadow: 0 0 0 0.25rem rgba(211, 47, 47, 0.25);
    }
    /* Modal specific styles */
.modal-content {
    border-radius: var(--border-radius);
    overflow: hidden; /* Ensures background gradient rounds correctly */
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); /* Deeper shadow for modals */
    border: none;
}

.modal-header {
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color)); /* Gradient header */
    color: white;
    padding: 1.5rem 2rem; /* Consistent with your card header */
    border-bottom: none;
    position: relative;
    overflow: hidden;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.modal-header::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, transparent, rgba(255,255,255,0.15), transparent);
    transform: translateX(-100%);
    animation: shimmer 3s infinite;
}

.modal-header .modal-title {
    font-weight: 600;
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 1.4rem;
}

.modal-header .btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: rgba(255, 255, 255, 0.8);
    opacity: 1;
    transition: color 0.3s ease;
}

.modal-header .btn-close:hover {
    color: white;
    transform: rotate(90deg);
}

.modal-body {
    padding: 2.5rem 2rem; /* More generous padding */
    background-color: #fff;
    color: #333;
}

.modal-footer {
    background-color: #f8f9fa; /* Light background for footer */
    border-top: 1px solid #e9ecef;
    padding: 1.25rem 2rem;
    display: flex;
    justify-content: flex-end; /* Align buttons to the right */
    gap: 1rem; /* Space between buttons */
}

/* Specific styles for Delete Confirmation Modal */
#deleteCategoryModal .modal-header {
    background: linear-gradient(45deg, #dc3545, #fd7e14); /* Red-orange gradient for danger */
}

#deleteCategoryModal .modal-title {
    color: white;
}

#deleteCategoryModal .modal-body {
    background-color: #fff;
    border-bottom: 1px solid #eee;
}

#deleteCategoryModal .modal-body p.lead {
    font-weight: 600;
    color: #343a40;
}

#deleteCategoryModal .modal-body p.text-muted {
    font-size: 0.9rem;
    color: #6c757d !important;
}

#deleteCategoryModal .modal-footer {
    justify-content: center; /* Center buttons for delete modal */
}

/* Form controls within modal */
.modal-body .form-label {
    font-weight: 500;
    color: #343a40;
    margin-bottom: 0.5rem;
}

.modal-body .form-control-modern {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid #ced4da;
    border-radius: var(--border-radius);
    transition: border-color 0.3s ease, box-shadow 0.3s ease;
    background-color: #f8f9fa;
}

.modal-body .form-control-modern:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(var(--primary-color-rgb), 0.25);
    background-color: #fff;
}

.modal-body .form-check.form-switch {
    padding-left: 3.5em;
    margin-top: 1rem;
    display: flex;
    align-items: center;
}

.modal-body .form-check-label {
    margin-bottom: 0;
    cursor: pointer;
    font-weight: 500;
    color: #343a40;
}

.modal-body .form-check-input {
    height: 1.5em;
    width: 2.5em;
    margin-right: 0.75rem;
}

.modal-body .form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}

.modal-body .form-text.text-muted {
    font-size: 0.85rem;
    color: #6c757d !important;
    margin-top: 0.25rem;
    display: block;
}

/* Animations for modals */
.animate__zoomIn {
    animation-duration: 0.4s;
}

.animate__fadeInDown {
    animation-duration: 0.5s;
}

/* Custom button styles for modal footer */
.modal-footer .btn-modern {
    min-width: 120px; /* Ensure buttons have a minimum width */
}

.modal-footer .btn-secondary {
    background-color: #6c757d;
    border-color: #6c757d;
    color: white;
}

.modal-footer .btn-secondary:hover {
    background-color: #5a6268;
    border-color: #545b62;
}

.modal-footer .btn-outline-secondary.btn-modern {
    background: rgb(12, 12, 12);
    border: 2px solid #6c757d;
    color:rgb(252, 254, 255);
}

.modal-footer .btn-outline-secondary.btn-modern:hover {
    background:rgb(12, 12, 12);
    color: white;
}

.modal-footer .btn-primary {
    background: linear-gradient(45deg, var(--primary-color), var(--secondary-color));
    border: none;
    color: white;
}

.modal-footer .btn-primary:hover {
    background: linear-gradient(45deg, var(--secondary-color), var(--primary-color));
}

.modal-footer .btn-danger {
    background: linear-gradient(45deg, #dc3545, #e83e8c);
    border: none;
    color: white;
}

.modal-footer .btn-danger:hover {
    background: linear-gradient(45deg, #e83e8c, #dc3545);
}

</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Gestion des checkboxes
    const selectAllCheckbox = document.getElementById('select-all');
    const categoryCheckboxes = document.querySelectorAll('.category-checkbox');
    const bulkActionSelect = document.getElementById('bulk-action');
    const applyBulkActionBtn = document.getElementById('apply-bulk-action');

    // Attach event listener to the "select all" checkbox
    selectAllCheckbox.addEventListener('change', function() {
        categoryCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        toggleBulkActionBtn();
    });

    // Attach event listeners to individual category checkboxes
    categoryCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', toggleBulkActionBtn);
    });

    // Attach event listener to the bulk action select
    bulkActionSelect.addEventListener('change', toggleBulkActionBtn);

    function toggleBulkActionBtn() {
        const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
        const hasAction = bulkActionSelect.value !== '';
        applyBulkActionBtn.disabled = !(checkedBoxes.length > 0 && hasAction);

        // Animation du bouton
        if (!applyBulkActionBtn.disabled) {
            applyBulkActionBtn.style.transform = 'scale(1.05)';
            setTimeout(() => {
                applyBulkActionBtn.style.transform = 'scale(1)';
            }, 200);
        }
    }

    // Gestion du formulaire d'actions groupées
    document.getElementById('bulk-action-form').addEventListener('submit', function(e) {
        const checkedBoxes = document.querySelectorAll('.category-checkbox:checked');
        const action = bulkActionSelect.value;

        if (checkedBoxes.length === 0) {
            e.preventDefault();
            alert('Veuillez sélectionner au moins une catégorie');
            return;
        }

        if (action === 'delete') {
            if (!confirm('Êtes-vous sûr de vouloir supprimer les catégories sélectionnées ?')) {
                e.preventDefault();
            }
        }
    });

    // Animation des lignes du tableau au survol
    const tableRows = document.querySelectorAll('.table-row-modern');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.boxShadow = '0 5px 15px rgba(211, 47, 47, 0.1)';
        });

        row.addEventListener('mouseleave', function() {
            this.style.boxShadow = 'none';
        });
    });

    // Animation des boutons au clic (Ripple effect)
    const buttons = document.querySelectorAll('.btn-modern, .btn-action');
    buttons.forEach(button => {
        button.addEventListener('click', function(e) {
            // Check if the button is within a modal that's about to be hidden
            if (this.closest('.modal') && this.getAttribute('data-bs-dismiss') === 'modal') {
                // Prevent ripple on modal close buttons to avoid visual glitches
                return;
            }

            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;

            ripple.style.width = ripple.style.height = size + 'px';
            ripple.style.left = x + 'px';
            ripple.style.top = y + 'px';
            ripple.classList.add('ripple');

            this.appendChild(ripple);

            setTimeout(() => {
                ripple.remove();
            }, 600);
        });
    });

    // Delete Confirmation Modal JS
    const deleteCategoryModal = document.getElementById('deleteCategoryModal');
    const deleteCategoryForm = document.getElementById('deleteCategoryForm');

    deleteCategoryModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const categoryId = button.getAttribute('data-category-id');
        deleteCategoryForm.action = `/categories/${categoryId}`; // Make sure this path is correct based on your routes
    });

    // Create Category Modal JS (Added)
    const createCategoryModal = document.getElementById('createCategoryModal');
    createCategoryModal.addEventListener('hidden.bs.modal', function () {
        // Reset form fields when the modal is closed
        const form = createCategoryModal.querySelector('form');
        form.reset(); // Resets all form fields
        // Remove validation feedback classes
        form.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
        form.querySelectorAll('.invalid-feedback').forEach(el => el.remove());
    });

    // Edit Category Modal JS (NEW)
    const editCategoryModal = document.getElementById('editCategoryModal');
    editCategoryModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Button that triggered the modal
        const categoryId = button.getAttribute('data-category-id');
        const categoryName = button.getAttribute('data-category-name');
        const categoryDescription = button.getAttribute('data-category-description');
        const categoryIsActive = button.getAttribute('data-category-is-active');

        const form = this.querySelector('#editCategoryForm');
        form.action = `/categories/${categoryId}`; // Set the form action dynamically

        this.querySelector('#edit-name').value = categoryName;
        this.querySelector('#edit-description').value = categoryDescription;
        this.querySelector('#edit-is_active').checked = (categoryIsActive === '1'); // Convert string to boolean
    });

    // If there are validation errors on submission from modal, show the modal again
    @if ($errors->any())
        // Check if the errors are specifically for the create form fields (e.g., 'name', 'description')
        const createFormFields = ['name', 'description', 'is_active']; // List fields in your create form
        const hasCreateErrors = createFormFields.some(field => @json($errors->has(field)));

        @if (old('_token') && $errors->any() && Request::routeIs('categories.store')) // Check if it was a submission to store route
            var createModal = new bootstrap.Modal(document.getElementById('createCategoryModal'));
            createModal.show();
        @elseif (old('_token') && $errors->any() && (Request::routeIs('categories.update') || Request::route()->named('categories.update')))
            // For update errors, try to re-open the edit modal and populate with old input
            // This is a more advanced scenario. For simplicity, we'll try to re-open.
            // A more robust solution might involve passing a 'category_id_with_errors' via session
            // or through the redirect to precisely re-open the correct modal.
            var editModal = new bootstrap.Modal(document.getElementById('editCategoryModal'));
            // Since we don't have the specific category data easily available after a redirect
            // due to validation errors, we might need to fetch it via an AJAX call
            // or ensure old input properly re-populates if the form is re-rendered.
            // For now, if any validation error on update, we'll assume the user was
            // on the edit modal and try to show it. The fields will be re-populated
            // by Laravel's `old()` helper if present.
            editModal.show();
        @endif
    @endif
});

// Styles CSS pour l'effet ripple (remains here)
const style = document.createElement('style');
style.textContent = `
    .ripple {
        position: absolute;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.6);
        transform: scale(0);
        animation: ripple-animation 0.6s linear;
        pointer-events: none;
        z-index: 999; /* Ensure ripple is above button content */
    }

    @keyframes ripple-animation {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection