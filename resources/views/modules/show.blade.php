@extends('layouts.app')

@section('content')
<style>
    :root {
        --primary-color: #C2185B;
        --secondary-color: #D32F2F;
        --accent-color: #ef4444;
        --light-pink: #fce4ec;
        --gradient-bg: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
        --card-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        --card-hover-shadow: 0 8px 30px rgba(194, 24, 91, 0.2);
    }

    .custom-container {
        min-height: 100vh;
        padding: 2rem 0;
        background: #f8f9fa;
    }

    .main-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(194, 24, 91, 0.15);
        overflow: hidden;
        border: none;
    }

    .formation-header {
        background: var(--gradient-bg);
        color: white;
        padding: 2rem;
        position: relative;
        overflow: hidden;
    }

    .formation-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="2" fill="rgba(255,255,255,0.1)"/></svg>') repeat;
        animation: float 20s infinite linear;
    }

    @keyframes float {
        0% { transform: translateX(0) translateY(0) rotate(0deg); }
        100% { transform: translateX(-100px) translateY(-100px) rotate(360deg); }
    }

    .formation-title {
        font-size: 2rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        position: relative;
        z-index: 1;
    }

    .formation-info {
        background: rgba(255,255,255,0.1);
        border-radius: 15px;
        padding: 1.5rem;
        margin-top: 1rem;
        backdrop-filter: blur(10px);
        position: relative;
        z-index: 1;
    }

    .modules-section {
        padding: 2rem;
    }

    .section-title {
        color: var(--primary-color);
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .add-module-btn {
        background: var(--gradient-bg);
        border: none;
        border-radius: 15px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(194, 24, 91, 0.3);
    }

    .add-module-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(194, 24, 91, 0.4);
        color: white;
    }

    .module-card-modern {
        background: white;
        border-radius: 20px;
        padding: 0;
        box-shadow: var(--card-shadow);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        overflow: hidden;
    }

    .module-card-modern:hover {
        transform: translateY(-5px);
        box-shadow: var(--card-hover-shadow);
    }

    .module-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #e2e8f0;
    }

    .module-header-top {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .module-icon {
        width: 48px;
        height: 48px;
        background: var(--gradient-bg);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 1.5rem;
    }

    .module-title-modern {
        font-size: 1.25rem;
        font-weight: 700;
        color: #1a202c;
        margin: 0.5rem 0 0.25rem 0;
    }

    .module-order-badge {
        background: #f1f5f9;
        color: var(--primary-color);
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        display: inline-block;
    }

    .module-meta {
        display: flex;
        gap: 1.5rem;
        flex-wrap: wrap;
        margin-top: 1rem;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #64748b;
        font-size: 0.875rem;
    }

    .meta-item i {
        color: var(--primary-color);
        font-size: 1rem;
    }

    .meta-value {
        font-weight: 600;
        color: #334155;
    }

    .module-card-body {
        padding: 1.5rem;
    }

    .status-badge-modern {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.875rem;
        text-transform: capitalize;
    }

    .status-published {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .status-draft {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .info-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }

    .info-row:last-child {
        border-bottom: none;
    }

    .info-label-modern {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #64748b;
        font-size: 0.875rem;
    }

    .info-label-modern i {
        color: var(--primary-color);
    }

    .info-value {
        font-weight: 600;
        color: #334155;
    }

    .content-toggle-btn {
        background: var(--light-pink);
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
        border-radius: 10px;
        padding: 0.5rem 1rem;
        font-weight: 600;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        width: 100%;
        margin-top: 1rem;
    }

    .content-toggle-btn:hover {
        background: var(--primary-color);
        color: white;
    }

    .content-toggle-btn i {
        transition: transform 0.3s ease;
    }

    .content-toggle-btn.active i {
        transform: rotate(180deg);
    }

    .module-content-collapsible {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease;
    }

    .module-content-collapsible.show {
        max-height: 500px;
        overflow-y: auto;
    }

    .content-list-modern {
        background: #f8fafc;
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
    }

    .content-item-modern {
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        background: white;
        border-radius: 8px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border-left: 3px solid var(--primary-color);
    }

    .content-item-modern:last-child {
        margin-bottom: 0;
    }

    .content-item-modern i {
        color: var(--primary-color);
    }

    .progress-section-modern {
        background: #f8fafc;
        padding: 1rem;
        border-radius: 12px;
        margin-top: 1rem;
    }

    .progress-label {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.75rem;
    }

    .progress-label-text {
        font-weight: 600;
        color: #334155;
        font-size: 0.875rem;
    }

    .progress-percentage {
        font-weight: 700;
        color: var(--primary-color);
        font-size: 1rem;
    }

    .custom-progress {
        height: 8px;
        border-radius: 10px;
        background: #e2e8f0;
        overflow: hidden;
    }

    .custom-progress-bar {
        background: var(--gradient-bg);
        height: 100%;
        border-radius: 10px;
        transition: width 0.6s ease;
        position: relative;
    }

    .module-actions-modern {
        display: flex;
        gap: 0.5rem;
    }

    .btn-icon {
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-edit-modern {
        background: #f1f5f9;
        color: var(--primary-color);
    }

    .btn-edit-modern:hover {
        background: var(--primary-color);
        color: white;
    }

    .btn-delete-modern {
        background: #fee2e2;
        color: #ef4444;
    }

    .btn-delete-modern:hover {
        background: #ef4444;
        color: white;
    }

    .update-progress-form {
        margin-top: 1rem;
    }

    .progress-input {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem;
        transition: border-color 0.3s ease;
    }

    .progress-input:focus {
        border-color: var(--primary-color);
        box-shadow: 0 0 0 3px rgba(194, 24, 91, 0.1);
    }

    .btn-update-progress {
        background: var(--primary-color);
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-update-progress:hover {
        background: var(--secondary-color);
        transform: translateY(-1px);
        color: white;
    }

    .back-btn {
        background: #6b7280;
        border: none;
        border-radius: 15px;
        padding: 0.75rem 2rem;
        color: white;
        font-weight: 600;
        transition: all 0.3s ease;
        margin-top: 2rem;
    }

    .back-btn:hover {
        background: #4b5563;
        transform: translateY(-2px);
        color: white;
    }

    .alert-custom {
        border: none;
        border-radius: 15px;
        padding: 1rem 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .alert-success {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .alert-danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .no-modules {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--primary-color);
    }

    .no-modules i {
        font-size: 4rem;
        margin-bottom: 1rem;
        opacity: 0.5;
    }

    .module-selection-card {
        transition: all 0.3s ease;
    }

    .module-selection-card:hover .border {
        border-color: var(--primary-color) !important;
        box-shadow: 0 4px 15px rgba(194, 24, 91, 0.2);
    }

    @keyframes fadeOutUp {
        from { opacity: 1; transform: translateY(0); }
        to { opacity: 0; transform: translateY(-30px); }
    }
    
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-30px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="custom-container">
    <div class="container">
        <div class="main-card">
            <div class="formation-header">
                <h1 class="formation-title">
                    <i class="fas fa-graduation-cap"></i>
                    Formation: {{ $formation->title }}
                </h1>
                <div class="formation-info">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-label">
                                <i class="fas fa-align-left"></i>
                                Description:
                            </div>
                            <p class="mb-0">{{ $formation->description }}</p>
                        </div>
                        <div class="col-md-6">
                            <div class="info-label">
                                <i class="fas fa-clock"></i>
                                Duration:
                            </div>
                            <p class="mb-0">{{ $formation->duration_hours }} mois</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modules-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="section-title mb-0">
                        <i class="fas fa-cubes"></i>
                        Modules pour cette Formation ({{ $formation->modules->count() }})
                    </h4>
                    @can('module-create')
<div class="d-flex gap-2">
    <div class="dropdown">
        <button type="button" 
                class="add-module-btn dropdown-toggle" 
                id="addModuleDropdown" 
                data-bs-toggle="dropdown" 
                aria-expanded="false">
            <i class="fas fa-plus-circle"></i> Ajouter un module
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="addModuleDropdown">
            <li>
                <a class="dropdown-item" 
                   href="#" 
                   data-bs-toggle="modal" 
                   data-bs-target="#createModuleModal">
                    <i class="fas fa-plus-circle text-primary"></i> Créer un nouveau module
                </a>
            </li>
            <li><hr class="dropdown-divider"></li>
            <li>
                <a class="dropdown-item" 
                   href="#" 
                   data-bs-toggle="modal" 
                   data-bs-target="#selectModuleModal">
                    <i class="fas fa-link text-success"></i> Sélectionner un module existant
                </a>
            </li>
        </ul>
    </div>
    <a href="{{ route('modules.corbeille') }}" class="btn btn-danger" style="border-radius: 15px;">
        <i class="fa fa-trash"></i> Corbeille
    </a>
</div>
@endcan
                </div>

                <div id="alert-container"></div>

                @if ($formation->modules->isEmpty())
                    <div class="no-modules">
                        <i class="fas fa-inbox"></i>
                        <h5>No modules have been added to this formation yet.</h5>
                        <p class="text-muted">Start building your formation by adding the first module!</p>
                    </div>
                @else
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="modules-list">
                        @foreach($formation->modules->sortBy('pivot.order') as $module)
                        <div class="col" id="module-card-{{ $module->id }}">
                            <div class="module-card-modern h-100">
                                <div class="module-card-header">
                                    <div class="module-header-top">
                                        <div class="flex-grow-1">
                                            <span class="module-order-badge">Module {{ $module->pivot->order }}</span>
                                            <h5 class="module-title-modern">{{ $module->title }}</h5>
                                        </div>
                                        <div class="module-actions-modern">
                                            @can('module-edit')
                                            <button class="btn-icon btn-edit-modern edit-btn" 
                                                    data-id="{{ $module->id }}" 
                                                    data-order="{{ $module->pivot->order }}"
                                                    title="Edit module">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @endcan
                                            @can('module-delete')
                                            <button class="btn-icon btn-delete-modern delete-btn" 
                                                    data-id="{{ $module->id }}"
                                                    title="Delete module">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                    
                                    <div class="module-meta">
                                        <div class="meta-item">
                                            <i class="fas fa-clock"></i>
                                            <span class="meta-value module-duration">{{ $module->duration_hours ?? 'N/A' }} heures</span>
                                        </div>
                                        <div class="meta-item">
                                            <i class="fas fa-calendar-alt"></i>
                                            <span class="meta-value module-sessions">{{ $module->number_seance ?? 'N/A' }} séances</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="module-card-body">
                                    <div class="info-row">
                                        <div class="info-label-modern">
                                            <i class="fas fa-user-tie"></i>
                                            Consultant
                                        </div>
                                        <div class="info-value module-consultant">{{ $module->user->name ?? 'N/A' }}</div>
                                    </div>

                                    <div class="info-row">
                                        <div class="info-label-modern">
                                            <i class="fas fa-flag"></i>
                                            Statut
                                        </div>
                                        <div>
                                            <span class="status-badge-modern module-status {{ $module->status == 'published' ? 'status-published' : 'status-draft' }}" data-status="{{ $module->status }}">
                                                <i class="fas {{ $module->status == 'published' ? 'fa-check-circle' : 'fa-edit' }}"></i>
                                                {{ $module->status }}
                                            </span>
                                        </div>
                                    </div>

                                    <button class="content-toggle-btn" data-module-id="{{ $module->id }}">
                                        <i class="fas fa-chevron-down"></i> Voir le contenu
                                    </button>

                                    <div class="module-content-collapsible" id="content-{{ $module->id }}">
                                        <div class="content-list-modern">
                                            @forelse($module->content as $item)
                                            <div class="content-item-modern">
                                                <i class="fas fa-check-circle"></i>
                                                <span>{{ $item }}</span>
                                            </div>
                                            @empty
                                            <div class="content-item-modern">
                                                <i class="fas fa-info-circle"></i>
                                                <span class="text-muted">Aucun contenu disponible.</span>
                                            </div>
                                            @endforelse
                                        </div>
                                    </div>

                                    <div class="progress-section-modern">
                                        <div class="progress-label">
                                            <span class="progress-label-text">
                                                <i class="fas fa-chart-line"></i> Progression
                                            </span>
                                            <span class="progress-percentage">{{ $module->progress }}%</span>
                                        </div>
                                        <div class="custom-progress">
                                            <div class="custom-progress-bar" style="width: {{ $module->progress }}%;"></div>
                                        </div>
                                        
                                        @if(Auth::check() && $module->user_id === Auth::id() && Auth::user()->can('module-update-progress'))
                                        <div class="update-progress-form">
                                            <form action="{{ route('modules.updateProgress', $module->id) }}" method="POST">
                                                @csrf
                                                <div class="input-group">
                                                    <input type="number" name="progress" class="form-control progress-input" placeholder="Mise à jour (0-100)" min="0" max="100" value="{{ $module->progress }}" required>
                                                    <button type="submit" class="btn-update-progress">
                                                        <i class="fas fa-sync-alt"></i> Mettre à jour
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @endif

                <div class="text-center">
                    <a href="{{ route('modules.index') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i> Retour à la liste des formations
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Create Module Modal --}}
<div class="modal fade" id="createModuleModal" tabindex="-1" aria-labelledby="createModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(194, 24, 91, 0.3);">
            <div class="modal-header" style="background: var(--gradient-bg); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title" id="createModuleModalLabel">
                    <i class="fas fa-plus-circle"></i> Add New Module
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <form id="createModuleForm">
                    @csrf
                    <input type="hidden" name="formation_ids[]" value="{{ $formation->id }}">
                    <div class="mb-3">
                        <label for="create-title" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-heading"></i> Module Title
                        </label>
                        <input type="text" class="form-control" id="create-title" name="title" required style="border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label for="create-duration_hours" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-clock"></i> Duration (in hours)
                        </label>
                        <input type="number" class="form-control" id="create-duration_hours" name="duration_hours" min="0" style="border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label for="create-number_seance" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-calendar-alt"></i> Number of Sessions
                        </label>
                        <input type="number" class="form-control" id="create-number_seance" name="number_seance" min="1" style="border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label for="create-status" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-flag"></i> Status
                        </label>
                        <select class="form-control" id="create-status" name="status" required style="border-radius: 10px; border: 2px solid #e2e8f0;">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="create-user" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-user-tie"></i> Assigned Consultant
                        </label>
                        <select class="form-control" id="create-user" name="user_id" required style="border-radius: 10px; border: 2px solid #e2e8f0;">
                            <option value="">Loading consultants...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="create-content" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-list-ul"></i> Content (one per line)
                        </label>
                        <textarea class="form-control" id="create-content" name="content" rows="4" placeholder="Enter content items, one per line..." style="border-radius: 10px; border: 2px solid #e2e8f0;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" id="createModuleBtn" class="btn" style="background: var(--gradient-bg); color: white; border: none; border-radius: 10px;">
                    <i class="fas fa-save"></i> Create Module
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Edit Module Modal --}}
<div class="modal fade" id="editModuleModal" tabindex="-1" aria-labelledby="editModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(194, 24, 91, 0.3);">
            <div class="modal-header" style="background: var(--gradient-bg); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title" id="editModuleModalLabel">
                    <i class="fas fa-edit"></i> Edit Module
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                <form id="editModuleForm">
                    @csrf
                    @method('PUT')
                    <input type="hidden" id="edit-module-id" name="id">
                    <input type="hidden" id="edit-formation-id" name="formation_id" value="{{ $formation->id }}">
                    <div class="mb-3">
                        <label for="edit-title" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-heading"></i> Module Title
                        </label>
                        <input type="text" class="form-control" id="edit-title" name="title" required style="border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label for="edit-new_order" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-sort-numeric-up"></i> Order
                        </label>
                        <input type="number" class="form-control" id="edit-new_order" name="new_order" required min="1" style="border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label for="edit-duration_hours" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-clock"></i> Duration (in hours)
                        </label>
                        <input type="number" class="form-control" id="edit-duration_hours" name="duration_hours" min="0" style="border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label for="edit-number_seance" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-calendar-alt"></i> Number of Sessions
                        </label>
                        <input type="number" class="form-control" id="edit-number_seance" name="number_seance" min="1" style="border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label for="edit-status" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-flag"></i> Status
                        </label>
                        <select class="form-control" id="edit-status" name="status" required style="border-radius: 10px; border: 2px solid #e2e8f0;">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-user" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-user-tie"></i> Assigned Consultant
                        </label>
                        <select class="form-control" id="edit-user" name="user_id" required style="border-radius: 10px; border: 2px solid #e2e8f0;">
                            <option value="">Loading consultants...</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-content" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-list-ul"></i> Content (one per line)
                        </label>
                        <textarea class="form-control" id="edit-content" name="content" rows="4" style="border-radius: 10px; border: 2px solid #e2e8f0;"></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" style="border-radius: 10px;">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="button" id="editModuleBtn" class="btn" style="background: var(--gradient-bg); color: white; border: none; border-radius: 10px;">
                    <i class="fas fa-save"></i> Update Module
                </button>
            </div>
        </div>
    </div>
</div>

{{-- Select Existing Module Modal --}}
<div class="modal fade" id="selectModuleModal" tabindex="-1" aria-labelledby="selectModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: none; box-shadow: 0 10px 30px rgba(194, 24, 91, 0.3);">
            <div class="modal-header" style="background: var(--gradient-bg); color: white; border-radius: 20px 20px 0 0;">
                <h5 class="modal-title" id="selectModuleModalLabel">
                    <i class="fas fa-link"></i> Sélectionner un module existant
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" style="padding: 2rem;">
                @if($availableModules && $availableModules->count() > 0)
                    <div class="mb-3">
                        <input type="text" id="searchModule" class="form-control" placeholder="🔍 Rechercher un module..." style="border-radius: 10px; border: 2px solid #e2e8f0; padding: 0.75rem;">
                    </div>
                    <div id="modulesList" style="max-height: 400px; overflow-y: auto;">
                        @foreach($availableModules as $module)
                        <div class="module-selection-card" data-module-id="{{ $module->id }}" data-module-title="{{ strtolower($module->title) }}">
                            <div class="d-flex justify-content-between align-items-start p-3 border rounded mb-2" style="cursor: pointer; transition: all 0.3s ease; border: 2px solid #e2e8f0;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1" style="color: var(--primary-color); font-weight: 600;">
                                        <i class="fas fa-book"></i> {{ $module->title }}
                                    </h6>
                                    <div class="d-flex gap-3 text-muted small">
                                        <span><i class="fas fa-user-tie"></i> {{ $module->user->name ?? 'N/A' }}</span>
                                        <span><i class="fas fa-clock"></i> {{ $module->duration_hours ?? 'N/A' }}h</span>
                                        <span><i class="fas fa-calendar"></i> {{ $module->number_seance ?? 'N/A' }} séances</span>
                                    </div>
                                </div>
                                <button class="btn btn-sm select-module-btn" data-module-id="{{ $module->id }}" style="background: var(--gradient-bg); color: white; border: none; border-radius: 8px; padding: 0.5rem 1rem;">
                                    <i class="fas fa-plus"></i> Sélectionner
                                </button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-5">
                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Aucun module disponible à sélectionner.</p>
                        <small>Tous les modules sont déjà attachés à cette formation ou vous n'avez pas créé de modules.</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
// ==================== CORRIGER LA GESTION DU DROPDOWN ====================
// Le problème: L'événement click sur les dropdowns interfère avec Bootstrap

document.addEventListener('DOMContentLoaded', function () {
    console.log('🔥 Script loaded');
    
    const modulesList = document.getElementById('modules-list');
    const alertContainer = document.getElementById('alert-container');
    const editModalElement = document.getElementById('editModuleModal');
    const createModalElement = document.getElementById('createModuleModal');
    const selectModuleModalElement = document.getElementById('selectModuleModal');
    
    // Initialize modals
    let editModuleModal = null;
    let createModuleModal = null;
    let selectModuleModal = null;
    
    if (typeof bootstrap !== 'undefined') {
        if (editModalElement) editModuleModal = new bootstrap.Modal(editModalElement);
        if (createModalElement) createModuleModal = new bootstrap.Modal(createModalElement);
        if (selectModuleModalElement) selectModuleModal = new bootstrap.Modal(selectModuleModalElement);
        console.log('✅ Bootstrap modals initialized');
    }
    
    const consultants = @json($consultants);
    const formationId = {{ $formation->id }};

    // ==================== FIX: GÉRER LES CLICS DROPDOWN CORRECTEMENT ====================
    document.addEventListener('click', function(e) {
        const dropdownItem = e.target.closest('[data-modal-target]');
        if (dropdownItem) {
            e.preventDefault();
            e.stopPropagation();
            
            const modalTarget = dropdownItem.dataset.modalTarget;
            console.log('Modal target:', modalTarget);
            
            // Fermer le dropdown proprement avec Bootstrap
            const dropdownButton = document.getElementById('addModuleDropdown');
            if (dropdownButton) {
                const dropdownInstance = bootstrap.Dropdown.getInstance(dropdownButton);
                if (dropdownInstance) {
                    dropdownInstance.hide();
                }
            }
            
            // Attendre que le dropdown se ferme avant d'ouvrir le modal
            setTimeout(() => {
                if (modalTarget === 'create' && createModuleModal) {
                    populateConsultantsSelect(document.getElementById('create-user'));
                    document.getElementById('createModuleForm').reset();
                    createModuleModal.show();
                } else if (modalTarget === 'select' && selectModuleModal) {
                    selectModuleModal.show();
                }
            }, 300); // Augmenté à 300ms pour laisser le temps au dropdown de se fermer
        }
    });

    // ==================== ALTERNATIVE: UTILISER DATA-BS-TOGGLE ET DATA-BS-TARGET ====================
    // Si la méthode ci-dessus ne fonctionne pas, utilisez cette approche dans votre HTML:
    /*
    <button class="dropdown-item" 
            type="button" 
            data-bs-toggle="modal" 
            data-bs-target="#createModuleModal"
            style="border-radius: 10px; padding: 0.75rem 1rem;">
        <i class="fas fa-plus-circle text-primary"></i> Créer un nouveau module
    </button>
    */

    // ==================== POPULATE CONSULTANTS DROPDOWN ====================
    function populateConsultantsSelect(selectElement, selectedUserId = null) {
        selectElement.innerHTML = '';
        consultants.forEach(consultant => {
            const option = document.createElement('option');
            option.value = consultant.id;
            option.textContent = consultant.name;
            if (consultant.id === selectedUserId) {
                option.selected = true;
            }
            selectElement.appendChild(option);
        });
    }

    // ==================== TOGGLE CONTENT VISIBILITY ====================
    document.addEventListener('click', function(e) {
        if (e.target.closest('.content-toggle-btn')) {
            const button = e.target.closest('.content-toggle-btn');
            const moduleId = button.dataset.moduleId;
            const contentDiv = document.getElementById('content-' + moduleId);
            
            if (contentDiv.classList.contains('show')) {
                contentDiv.classList.remove('show');
                button.innerHTML = '<i class="fas fa-chevron-down"></i> Voir le contenu';
                button.classList.remove('active');
            } else {
                contentDiv.classList.add('show');
                button.innerHTML = '<i class="fas fa-chevron-up"></i> Masquer le contenu';
                button.classList.add('active');
            }
        }
    });

    // ==================== HANDLE MODULE ACTIONS (EDIT/DELETE) ====================
    if (modulesList) {
        modulesList.addEventListener('click', function (e) {
            if (e.target.closest('.delete-btn')) {
                e.preventDefault();
                if (confirm('Êtes-vous sûr de vouloir supprimer ce module ?')) {
                    const moduleId = e.target.closest('.delete-btn').dataset.id;
                    deleteModule(moduleId);
                }
            }

            if (e.target.closest('.edit-btn')) {
                const button = e.target.closest('.edit-btn');
                const moduleId = button.dataset.id;
                const currentOrder = button.dataset.order;
                fetchModuleData(moduleId, currentOrder);
            }
        });
    }

    // ==================== FETCH MODULE DATA FOR EDITING ====================
    function fetchModuleData(moduleId, currentOrder) {
        const url = `/modules/${moduleId}/get-data`;
        axios.get(url)
            .then(response => {
                const { module } = response.data;
                document.getElementById('edit-module-id').value = module.id;
                document.getElementById('edit-title').value = module.title;
                document.getElementById('edit-duration_hours').value = module.duration_hours || '';
                document.getElementById('edit-number_seance').value = module.number_seance || '';
                document.getElementById('edit-new_order').value = currentOrder;
                document.getElementById('edit-status').value = module.status;
                document.getElementById('edit-content').value = Array.isArray(module.content) ? module.content.join('\n') : '';
                
                populateConsultantsSelect(document.getElementById('edit-user'), module.user_id);
                editModuleModal.show();
            })
            .catch(error => {
                console.error('Error fetching module data:', error);
                handleFormError(error);
            });
    }

    // ==================== CREATE MODULE ====================
    const createModuleBtn = document.getElementById('createModuleBtn');
    if (createModuleBtn) {
        createModuleBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const contentValue = document.getElementById('create-content').value.trim();
            
            const formData = new FormData();
            formData.append('title', document.getElementById('create-title').value);
            formData.append('duration_hours', document.getElementById('create-duration_hours').value || '');
            formData.append('number_seance', document.getElementById('create-number_seance').value || '');
            formData.append('status', document.getElementById('create-status').value);
            formData.append('content', contentValue || 'No content specified');
            formData.append('user_id', document.getElementById('create-user').value);
            formData.append('formation_ids[]', formationId);
            
            axios.post('{{ route('modules.store') }}', formData)
                .then(response => {
                    createModuleModal.hide();
                    showAlert('Module créé avec succès!', 'success');
                    setTimeout(() => location.reload(), 1000);
                })
                .catch(error => {
                    handleFormError(error);
                });
        });
    }

    // ==================== UPDATE MODULE ====================
    const editModuleBtn = document.getElementById('editModuleBtn');
    if (editModuleBtn) {
        editModuleBtn.addEventListener('click', function (e) {
            e.preventDefault();
            const moduleId = document.getElementById('edit-module-id').value;
            const url = `/modules/${moduleId}`;
            
            const formData = {
                title: document.getElementById('edit-title').value,
                duration_hours: document.getElementById('edit-duration_hours').value || null,
                number_seance: document.getElementById('edit-number_seance').value || null,
                new_order: document.getElementById('edit-new_order').value,
                formation_id: formationId,
                status: document.getElementById('edit-status').value,
                content: document.getElementById('edit-content').value,
                user_id: document.getElementById('edit-user').value,
            };

            axios.put(url, formData)
                .then(response => {
                    editModuleModal.hide();
                    showAlert('Module mis à jour avec succès!', 'success');
                    
                    if (response.data.modules) {
                        updateModulesList(response.data.modules);
                    } else {
                        setTimeout(() => location.reload(), 1000);
                    }
                })
                .catch(error => {
                    handleFormError(error);
                });
        });
    }

    // ==================== HELPER FUNCTIONS ====================
    function deleteModule(moduleId) {
        axios.post(`/modules/${moduleId}/destroy-ajax`, {
            _method: 'DELETE',
            _token: '{{ csrf_token() }}'
        })
        .then(response => {
            const moduleCard = document.getElementById('module-card-' + moduleId);
            if (moduleCard) {
                moduleCard.style.animation = 'fadeOutUp 0.5s ease';
                setTimeout(() => {
                    moduleCard.remove();
                    showAlert(response.data.message || 'Module supprimé avec succès!', 'success');
                }, 500);
            }
        })
        .catch(error => {
            handleFormError(error);
        });
    }

    function handleFormError(error) {
        console.error('Form error:', error);
        let errorMessage = 'Une erreur s\'est produite. Veuillez réessayer.';
        
        if (error.response && error.response.data) {
            if (error.response.data.errors) {
                const errors = error.response.data.errors;
                errorMessage = 'Veuillez corriger les erreurs suivantes:<br>';
                for (const key in errors) {
                    errorMessage += `- ${errors[key][0]}<br>`;
                }
            } else if (error.response.data.message) {
                errorMessage = error.response.data.message;
            }
        }
        
        showAlert(errorMessage, 'danger');
    }

    function showAlert(message, type) {
        const alertHtml = `
        <div class="alert alert-${type} alert-dismissible fade show" role="alert">
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>`;
        alertContainer.innerHTML = alertHtml;
        
        setTimeout(() => {
            const alert = alertContainer.querySelector('.alert');
            if (alert) alert.remove();
        }, 5000);
    }

    function updateModulesList(modules) {
        // Votre fonction existante
    }

    // ==================== SELECT EXISTING MODULE ====================
    if (selectModuleModalElement) {
        const searchInput = document.getElementById('searchModule');
        
        if (searchInput) {
            searchInput.addEventListener('input', function(e) {
                const searchTerm = e.target.value.toLowerCase();
                document.querySelectorAll('.module-selection-card').forEach(card => {
                    const title = card.dataset.moduleTitle;
                    card.style.display = title.includes(searchTerm) ? 'block' : 'none';
                });
            });
        }
        
        document.querySelectorAll('.select-module-btn').forEach(btn => {
            btn.addEventListener('click', function(e) {
                e.stopPropagation();
                const moduleId = this.dataset.moduleId;
                attachModule(moduleId);
            });
        });
    }

    function attachModule(moduleId) {
        const btn = document.querySelector(`.select-module-btn[data-module-id="${moduleId}"]`);
        if (!btn) return;
        
        const originalContent = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Ajout...';
        btn.disabled = true;
        
        axios.post(`/formations/${formationId}/modules/attach`, {
            module_id: moduleId,
            _token: '{{ csrf_token() }}'
        })
        .then(response => {
            if (selectModuleModal) selectModuleModal.hide();
            showAlert('Module attaché avec succès!', 'success');
            setTimeout(() => location.reload(), 1000);
        })
        .catch(error => {
            btn.innerHTML = originalContent;
            btn.disabled = false;
            handleFormError(error);
        });
    }
});
</script>

@endsection