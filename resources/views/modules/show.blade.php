@extends('layouts.app')

@section('content')
<style>
    /* Your CSS styles, no changes needed */
    :root {
        --primary-color: #C2185B;
        --secondary-color: #D32F2F;
        --accent-color: #ef4444;
        --light-pink: #fce4ec;
        --gradient-bg: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
    }

    .custom-container {
        min-height: 100vh;
        padding: 2rem 0;
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
    }

    .formation-info {
        background: rgba(255,255,255,0.1);
        border-radius: 15px;
        padding: 1.5rem;
        margin-top: 1rem;
        backdrop-filter: blur(10px);
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

    .module-card {
        border: none;
        border-radius: 18px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
        background: linear-gradient(145deg, #ffffff 0%, #fafafa 100%);
    }

    .module-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(194, 24, 91, 0.2);
    }

    .module-header {
        background: var(--gradient-bg);
        color: white;
        padding: 1.5rem;
        position: relative;
    }

    .module-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        right: 0;
        height: 3px;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    }

    .module-title {
        font-weight: 700;
        font-size: 1.1rem;
        margin: 0;
    }

    .module-actions {
        display: flex;
        gap: 0.5rem;
    }

    .btn-edit {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        border-radius: 10px;
        padding: 0.5rem 0.75rem;
        transition: all 0.3s ease;
    }

    .btn-edit:hover {
        background: rgba(255,255,255,0.3);
        color: white;
        transform: scale(1.05);
    }

    .btn-delete {
        background: var(--accent-color);
        border: none;
        color: white;
        border-radius: 10px;
        padding: 0.5rem 0.75rem;
        transition: all 0.3s ease;
    }

    .btn-delete:hover {
        background: #dc2626;
        transform: scale(1.05);
        color: white;
    }

    .module-body {
        padding: 2rem;
    }

    .info-item {
        margin-bottom: 1.5rem;
        padding: 1rem;
        background: var(--light-pink);
        border-radius: 12px;
        border-left: 4px solid var(--primary-color);
    }
    
    .info-label {
        font-weight: 600;
        color: #000000;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }

    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.85rem;
    }

    .status-published {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
    }

    .status-draft {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }

    .content-list {
        background: #f8fafc;
        border-radius: 12px;
        overflow: hidden;
        border: 1px solid #e2e8f0;
    }

    .content-item {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        transition: background 0.2s ease;
    }

    .content-item:hover {
        background: var(--light-pink);
    }

    .content-item:last-child {
        border-bottom: none;
    }

    .progress-section {
        background: #f1f5f9;
        padding: 1.5rem;
        border-radius: 15px;
        margin-top: 1rem;
    }

    .custom-progress {
        height: 30px;
        border-radius: 15px;
        background: #e2e8f0;
        overflow: hidden;
        position: relative;
    }

    .custom-progress-bar {
        background: var(--gradient-bg);
        height: 100%;
        border-radius: 15px;
        transition: width 0.6s ease;
        position: relative;
        overflow: hidden;
    }

    .custom-progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(
            90deg,
            transparent,
            rgba(255,255,255,0.3),
            transparent
        );
        animation: shimmer 2s infinite;
    }

    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }

    .progress-text {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-weight: 600;
        text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
    }

    .update-progress-form {
        background: white;
        padding: 1.5rem;
        border-radius: 12px;
        margin-top: 1rem;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
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

    .alert-info {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
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

    .module-card:nth-child(even) .module-header { background: linear-gradient(135deg, #D32F2F, #ef4444); }
    .module-card:nth-child(even) .info-item { border-left-color: #ef4444; }

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
                            <p class="mb-0">{{ $formation->duration_hours }} hours</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="modules-section">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h4 class="section-title mb-0">
                        <i class="fas fa-cubes"></i>
                        Modules for this Formation ({{ $formation->modules->count() }})
                    </h4>
                    @can('module-create')
                    <button type="button" class="add-module-btn" data-bs-toggle="modal" data-bs-target="#createModuleModal">
                        <i class="fas fa-plus-circle"></i> Add New Module
                    </button>
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
                    <div class="row row-cols-1 g-4" id="modules-list">
                        @foreach($formation->modules->sortBy('order') as $module)
                        <div class="col" id="module-card-{{ $module->id }}">
                            <div class="module-card h-100">
                                <div class="module-header">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h5 class="module-title">
                                            <i class="fas fa-cube"></i>
                                            Module {{ $module->order }}: {{ $module->title }}
                                        </h5>
                                        <div class="module-actions">
                                            @can('module-edit')
                                            <button class="btn-edit edit-btn" data-id="{{ $module->id }}">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            @endcan
                                            @can('module-delete')
                                            <button class="btn-delete delete-btn" data-id="{{ $module->id }}">
                                                <i class="fas fa-trash-alt"></i>
                                            </button>
                                            @endcan
                                        </div>
                                    </div>
                                </div>
                                <div class="module-body">
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-user-tie"></i>
                                            Assigned Consultant:
                                        </div>
                                        <span class="module-consultant">{{ $module->user->name ?? 'N/A' }}</span>
                                    </div>
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-flag"></i>
                                            Status:
                                        </div>
                                        <span class="status-badge module-status {{ $module->status == 'published' ? 'status-published' : 'status-draft' }}" data-status="{{ $module->status }}">
                                            <i class="fas {{ $module->status == 'published' ? 'fa-check-circle' : 'fa-edit' }}"></i>
                                            {{ $module->status }}
                                        </span>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-clock"></i>
                                            Duration:
                                        </div>
                                        <span class="module-duration">{{ $module->duration_hours ?? 'N/A' }} hours</span>
                                    </div>
                                    
                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-calendar-alt"></i>
                                            Sessions:
                                        </div>
                                        <span class="module-sessions">{{ $module->number_seance ?? 'N/A' }}</span>
                                    </div>

                                    <div class="info-item">
                                        <div class="info-label">
                                            <i class="fas fa-list-ul"></i>
                                            Content:
                                        </div>
                                        <ul class="content-list module-content">
                                            @forelse($module->content as $item)
                                            <li class="content-item">
                                                <i class="fas fa-chevron-right text-primary"></i>
                                                {{ $item }}
                                            </li>
                                            @empty
                                            <li class="content-item text-muted">
                                                <i class="fas fa-info-circle"></i>
                                                No content available.
                                            </li>
                                            @endforelse
                                        </ul>
                                    </div>

                                    <div class="progress-section">
                                        <div class="info-label">
                                            <i class="fas fa-chart-line"></i>
                                            Module Progress:
                                        </div>
                                        <div class="custom-progress">
                                            <div class="custom-progress-bar" style="width: {{ $module->progress }}%;">
                                                <div class="progress-text">{{ $module->progress }}%</div>
                                            </div>
                                        </div>
                                        
                                        @if(Auth::check() && $module->user_id === Auth::id() && Auth::user()->can('module-update-progress'))
                                        <div class="update-progress-form">
                                            <form action="{{ route('modules.updateProgress', $module->id) }}" method="POST">
                                                @csrf
                                                <div class="input-group">
                                                    <input type="number" name="progress" class="form-control progress-input" placeholder="Update progress (0-100)" min="0" max="100" value="{{ $module->progress }}" required>
                                                    <button type="submit" class="btn-update-progress">
                                                        <i class="fas fa-sync-alt"></i> Update Progress
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
                        <i class="fas fa-arrow-left"></i> Back to Formations List
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
                    <input type="hidden" name="formation_id" value="{{ $formation->id }}">
                    <div class="mb-3">
                        <label for="create-title" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-heading"></i> Module Title
                        </label>
                        <input type="text" class="form-control" id="create-title" name="title" required style="border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label for="create-order" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-sort-numeric-up"></i> Order
                        </label>
                        <input type="number" class="form-control" id="create-order" name="order" required min="1" value="{{ $formation->modules->max('order') + 1 ?? 1 }}" style="border-radius: 10px; border: 2px solid #e2e8f0;">
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
                        <select class="form-control" id="create-user" name="user_id" required style="border-radius: 10px; border: 2px solid #e2e8f0;"></select>
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
                    <div class="mb-3">
                        <label for="edit-title" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-heading"></i> Module Title
                        </label>
                        <input type="text" class="form-control" id="edit-title" name="title" required style="border-radius: 10px; border: 2px solid #e2e8f0;">
                    </div>
                    <div class="mb-3">
                        <label for="edit-order" class="form-label" style="color: var(--primary-color); font-weight: 600;">
                            <i class="fas fa-sort-numeric-up"></i> Order
                        </label>
                        <input type="number" class="form-control" id="edit-order" name="order" required min="1" style="border-radius: 10px; border: 2px solid #e2e8f0;">
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
                        <select class="form-control" id="edit-user" name="user_id" required style="border-radius: 10px; border: 2px solid #e2e8f0;"></select>
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

<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modulesList = document.getElementById('modules-list');
        const alertContainer = document.getElementById('alert-container');
        const editModalElement = document.getElementById('editModuleModal');
        const createModalElement = document.getElementById('createModuleModal');
        
        const editModuleModal = new bootstrap.Modal(editModalElement);
        const createModuleModal = new bootstrap.Modal(createModalElement);
        
        const consultants = @json($consultants);

        // Populate consultants dropdown
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

        // Initialize create modal
        document.querySelector('[data-bs-target="#createModuleModal"]').addEventListener('click', function () {
            populateConsultantsSelect(document.getElementById('create-user'));
            document.getElementById('createModuleForm').reset();
            // Set default order value
            document.getElementById('create-order').value = {{ $formation->modules->max('order') + 1 ?? 1 }};
        });

        // Handle module actions (edit/delete)
        if (modulesList) {
            modulesList.addEventListener('click', function (e) {
                if (e.target.closest('.delete-btn')) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to delete this module?')) {
                        const moduleId = e.target.closest('.delete-btn').dataset.id;
                        deleteModule(moduleId);
                    }
                }

                if (e.target.closest('.edit-btn')) {
                    const moduleId = e.target.closest('.edit-btn').dataset.id;
                    fetchModuleData(moduleId);
                }
            });
        }

        // Fetch module data for editing
        function fetchModuleData(moduleId) {
            const url = `/modules/${moduleId}/get-data`;
            axios.get(url)
                .then(response => {
                    const { module } = response.data;
                    document.getElementById('edit-module-id').value = module.id;
                    document.getElementById('edit-title').value = module.title;
                    document.getElementById('edit-duration_hours').value = module.duration_hours || '';
                    document.getElementById('edit-number_seance').value = module.number_seance || '';
                    document.getElementById('edit-order').value = module.order;
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

        // Create module
        document.getElementById('createModuleBtn').addEventListener('click', function (e) {
            e.preventDefault();
            const contentValue = document.getElementById('create-content').value.trim();
            const formData = {
                formation_id: {{ $formation->id }},
                modules: [
                    {
                        title: document.getElementById('create-title').value,
                        duration_hours: document.getElementById('create-duration_hours').value || null,
                        number_seance: document.getElementById('create-number_seance').value || null,
                        order: document.getElementById('create-order').value,
                        status: document.getElementById('create-status').value,
                        content: contentValue || 'No content specified', // Provide default if empty
                        user_id: document.getElementById('create-user').value
                    }
                ]
            };
            
            axios.post('{{ route('modules.store') }}', formData)
                .then(response => {
                    createModuleModal.hide();
                    location.reload(); // Simple reload to show the new module
                    showAlert('Module created successfully!', 'success');
                })
                .catch(error => {
                    handleFormError(error);
                });
        });

        // Update module
        document.getElementById('editModuleBtn').addEventListener('click', function (e) {
            e.preventDefault();
            const moduleId = document.getElementById('edit-module-id').value;
            const url = `/modules/${moduleId}`;
            const formData = {
                title: document.getElementById('edit-title').value,
                duration_hours: document.getElementById('edit-duration_hours').value || null,
                number_seance: document.getElementById('edit-number_seance').value || null,
                order: document.getElementById('edit-order').value,
                status: document.getElementById('edit-status').value,
                content: document.getElementById('edit-content').value,
                user_id: document.getElementById('edit-user').value,
            };

            axios.put(url, formData)
                .then(response => {
                    editModuleModal.hide();
                    location.reload(); // Simple reload to show updated data
                    showAlert('Module updated successfully!', 'success');
                })
                .catch(error => {
                    handleFormError(error);
                });
        });

        // Delete module
        function deleteModule(moduleId) {
            const url = `/modules/${moduleId}`;
            axios.delete(url)
                .then(response => {
                    const moduleCard = document.getElementById('module-card-' + moduleId);
                    if (moduleCard) {
                        moduleCard.style.animation = 'fadeOutUp 0.5s ease';
                        setTimeout(() => {
                            moduleCard.remove();
                            showAlert('Module deleted successfully!', 'success');
                            // Check if no modules left
                            if (document.querySelectorAll('.module-card').length === 0) {
                                document.getElementById('modules-list').innerHTML = `<div class="no-modules">
                                    <i class="fas fa-inbox"></i>
                                    <h5>No modules have been added to this formation yet.</h5>
                                    <p class="text-muted">Start building your formation by adding the first module!</p>
                                </div>`;
                            }
                        }, 500);
                    }
                })
                .catch(error => {
                    console.error('Deletion error:', error);
                    showAlert('Failed to delete module.', 'danger');
                });
        }

        // Handle form errors
        function handleFormError(error) {
            console.error('Form error:', error);
            let errorMessage = 'An error occurred. Please try again.';
            
            if (error.response && error.response.data) {
                if (error.response.data.errors) {
                    const errors = error.response.data.errors;
                    errorMessage = 'Please fix the following errors:<br>';
                    for (const key in errors) {
                        errorMessage += `- ${errors[key][0]}<br>`;
                    }
                } else if (error.response.data.message) {
                    errorMessage = error.response.data.message;
                }
            }
            
            showAlert(errorMessage, 'danger');
        }

        // Show alert messages
        function showAlert(message, type) {
            const alertHtml = `
            <div class="alert alert-${type} alert-custom alert-dismissible fade show" role="alert" style="animation: fadeInDown 0.5s ease">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            `;
            alertContainer.innerHTML = alertHtml;
            
            // Auto-hide after 5 seconds
            setTimeout(() => {
                const alert = alertContainer.querySelector('.alert');
                if (alert) {
                    alert.style.animation = 'fadeOutUp 0.5s ease';
                    setTimeout(() => {
                        alert.remove();
                    }, 500);
                }
            }, 5000);
        }

        // Handle modal cleanup
        editModalElement.addEventListener('hidden.bs.modal', function () {
            document.getElementById('editModuleForm').reset();
        });

        createModalElement.addEventListener('hidden.bs.modal', function () {
            document.getElementById('createModuleForm').reset();
        });
    });
</script>
@endsection