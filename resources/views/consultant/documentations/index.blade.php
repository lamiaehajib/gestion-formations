@extends('layouts.app')

@section('content')
<div class="container py-4">
    {{-- En-tête --}}
    <div class="row mb-4 align-items-center">
        <div class="col-md-8">
            <h2 class="fw-bold gradient-text mb-0">
                <i class="fas fa-file-alt me-2"></i> Mes Documentations
            </h2>
            <p class="text-muted mt-2 mb-0">Gérez vos documentations de modules</p>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('modules.index') }}" class="btn btn-outline-dark rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i> Retour aux modules
            </a>
        </div>
    </div>

    {{-- Messages Flash --}}
    @if(session('success'))
        <div class="alert alert-success alert-modern alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="flex-grow-1">{{ session('success') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-modern alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon">
                    <i class="fas fa-exclamation-circle"></i>
                </div>
                <div class="flex-grow-1">{{ session('error') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    @if(session('info'))
        <div class="alert alert-info alert-modern alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="alert-icon">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="flex-grow-1">{{ session('info') }}</div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        </div>
    @endif

    {{-- Statistiques en cartes --}}
    <div class="row mb-5 g-4">
        <div class="col-md-3">
            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-check-double"></i>
                </div>
                <div class="stat-content">
                    <h2 class="stat-number">{{ $stats['completed_modules'] }}</h2>
                    <p class="stat-label">Modules à 100%</p>
                </div>
                <div class="stat-wave"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-warning">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <h2 class="stat-number">{{ $stats['pending'] }}</h2>
                    <p class="stat-label">En attente</p>
                </div>
                <div class="stat-wave"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-check"></i>
                </div>
                <div class="stat-content">
                    <h2 class="stat-number">{{ $stats['approved'] }}</h2>
                    <p class="stat-label">Approuvées</p>
                </div>
                <div class="stat-wave"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-danger">
                <div class="stat-icon">
                    <i class="fas fa-times"></i>
                </div>
                <div class="stat-content">
                    <h2 class="stat-number">{{ $stats['rejected'] }}</h2>
                    <p class="stat-label">Rejetées</p>
                </div>
                <div class="stat-wave"></div>
            </div>
        </div>
    </div>

    {{-- Section: Modules complétés à 100% --}}
    @if($completedModules->count() > 0)
        <div class="card modern-card mb-5">
            <div class="card-header-modern bg-gradient-success">
                <div class="d-flex align-items-center">
                    <div class="header-icon me-3">
                        <i class="fas fa-trophy"></i>
                    </div>
                    <div>
                        <h5 class="mb-0">Modules complétés à 100%</h5>
                        <small class="opacity-90">Soumettez votre documentation</small>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th><i class="fas fa-book me-2"></i> Module</th>
                                <th class="text-center"><i class="fas fa-chart-line me-2"></i> Progression</th>
                                <th class="text-center"><i class="fas fa-flag me-2"></i> Statut</th>
                                <th class="text-center"><i class="fas fa-cogs me-2"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedModules as $module)
                                <tr>
                                    <td>
                                        <div class="module-info">
                                            <strong class="module-title">{{ $module->title }}</strong>
                                            <div class="module-meta">
                                                <span class="badge badge-light">
                                                    <i class="fas fa-clock me-1"></i>{{ $module->duration_hours }}h
                                                </span>
                                                <span class="badge badge-light ms-2">
                                                    <i class="fas fa-layer-group me-1"></i>{{ $module->number_seance }} séances
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="progress-modern">
                                            <div class="progress-bar-modern bg-success" style="width: 100%">
                                                <span class="progress-text">100%</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @php
                                            $doc = $module->documentations->first();
                                        @endphp
                                        
                                        @if($doc)
                                            @if($doc->status === 'pending')
                                                <span class="status-badge status-warning">
                                                    <i class="fas fa-hourglass-half me-1"></i> En attente
                                                </span>
                                            @elseif($doc->status === 'approved')
                                                <span class="status-badge status-success">
                                                    <i class="fas fa-check-circle me-1"></i> Approuvée
                                                </span>
                                            @else
                                                <span class="status-badge status-danger">
                                                    <i class="fas fa-times-circle me-1"></i> Rejetée
                                                </span>
                                            @endif
                                        @else
                                            <span class="status-badge status-secondary">
                                                <i class="fas fa-file-upload me-1"></i> Non soumise
                                            </span>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group-modern">
                                            @if($doc)
                                                <a href="{{ route('consultant.documentations.show', $doc->id) }}" 
                                                   class="btn-modern btn-modern-primary">
                                                    <i class="fas fa-eye"></i> Voir
                                                </a>
                                                
                                                @if($doc->isPending())
                                                    <a href="{{ route('consultant.documentations.edit', $doc->id) }}" 
                                                       class="btn-modern btn-modern-warning">
                                                        <i class="fas fa-edit"></i> Modifier
                                                    </a>
                                                @endif
                                            @else
                                                <a href="{{ route('consultant.documentations.create', $module->id) }}" 
                                                   class="btn-modern btn-modern-gradient">
                                                    <i class="fas fa-cloud-upload-alt me-2"></i> Soumettre Documentation
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @else
        <div class="empty-state mb-5">
            <div class="empty-state-icon">
                <i class="fas fa-info-circle"></i>
            </div>
            <h5 class="empty-state-title">Aucun module complété</h5>
            <p class="empty-state-text">
                Vous devez compléter 100% de la progression d'un module avant de pouvoir soumettre une documentation.
            </p>
            <a href="{{ route('modules.index') }}" class="btn btn-gradient-primary rounded-pill px-4 mt-3">
                <i class="fas fa-arrow-right me-2"></i> Retourner à vos modules
            </a>
        </div>
    @endif

    {{-- Section: Historique des documentations --}}
    <div class="card modern-card">
        <div class="card-header-modern bg-gradient-primary">
            <div class="d-flex align-items-center">
                <div class="header-icon me-3">
                    <i class="fas fa-history"></i>
                </div>
                <div>
                    <h5 class="mb-0">Historique des documentations</h5>
                    <small class="opacity-90">Toutes vos documentations soumises</small>
                </div>
            </div>
        </div>
        <div class="card-body">
            @if($documentations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th><i class="fas fa-book me-2"></i> Module</th>
                                <th><i class="fas fa-align-left me-2"></i> Description</th>
                                <th class="text-center"><i class="fas fa-flag me-2"></i> Statut</th>
                                <th><i class="fas fa-calendar-alt me-2"></i> Date</th>
                                <th class="text-center"><i class="fas fa-tools me-2"></i> Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documentations as $doc)
                                <tr>
                                    <td>
                                        <strong class="text-dark">{{ $doc->module->title }}</strong>
                                    </td>
                                    <td>
                                        <span class="text-muted">{{ Str::limit($doc->description, 60) }}</span>
                                    </td>
                                    <td class="text-center">
                                        @if($doc->status === 'pending')
                                            <span class="status-badge status-warning">
                                                <i class="fas fa-clock me-1"></i> En attente
                                            </span>
                                        @elseif($doc->status === 'approved')
                                            <span class="status-badge status-success">
                                                <i class="fas fa-check me-1"></i> Approuvée
                                            </span>
                                        @else
                                            <span class="status-badge status-danger">
                                                <i class="fas fa-times me-1"></i> Rejetée
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="date-info">
                                            <span class="date-day">{{ $doc->created_at->format('d/m/Y') }}</span>
                                            <small class="date-time">{{ $doc->created_at->format('H:i') }}</small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group-modern">
                                            <a href="{{ route('consultant.documentations.show', $doc->id) }}" 
                                               class="btn-icon btn-icon-primary"
                                               title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($doc->isPending())
                                                <a href="{{ route('consultant.documentations.edit', $doc->id) }}" 
                                                   class="btn-icon btn-icon-warning"
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <form action="{{ route('consultant.documentations.destroy', $doc->id) }}" 
                                                      method="POST" 
                                                      class="d-inline"
                                                      onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette documentation ?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            class="btn-icon btn-icon-danger"
                                                            title="Supprimer">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="d-flex justify-content-center mt-4">
                    {{ $documentations->links() }}
                </div>
            @else
                <div class="empty-state-small">
                    <i class="fas fa-folder-open"></i>
                    <h5>Aucune documentation soumise</h5>
                    <p>Commencez par compléter vos modules à 100% pour pouvoir soumettre vos documentations.</p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    /* === Variables de couleurs === */
    :root {
        --primary-pink: #C2185B;
        --primary-red: #D32F2F;
        --primary-red-light: #ef4444;
        --gradient-primary: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        --gradient-success: linear-gradient(135deg, #10b981 0%, #059669 100%);
    }

    /* === Typographie === */
    .gradient-text {
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    /* === Alertes modernes === */
    .alert-modern {
        border: none;
        border-radius: 16px;
        padding: 1.25rem;
        backdrop-filter: blur(10px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .alert-modern .alert-icon {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        font-size: 1.2rem;
    }

    .alert-success.alert-modern {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.1) 0%, rgba(5, 150, 105, 0.1) 100%);
        border-left: 4px solid #10b981;
    }

    .alert-success.alert-modern .alert-icon {
        background: rgba(16, 185, 129, 0.2);
        color: #059669;
    }

    /* === Cartes de statistiques === */
    .stat-card {
        position: relative;
        background: white;
        border-radius: 20px;
        padding: 2rem;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--gradient-primary);
    }

    .stat-success::before {
        background: var(--gradient-success);
    }

    .stat-warning::before {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
    }

    .stat-primary::before {
        background: var(--gradient-primary);
    }

    .stat-danger::before {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 2;
    }

    .stat-success .stat-icon {
        background: linear-gradient(135deg, rgba(16, 185, 129, 0.2) 0%, rgba(5, 150, 105, 0.2) 100%);
        color: #059669;
    }

    .stat-warning .stat-icon {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.2) 0%, rgba(245, 158, 11, 0.2) 100%);
        color: #f59e0b;
    }

    .stat-primary .stat-icon {
        background: linear-gradient(135deg, rgba(194, 24, 91, 0.2) 0%, rgba(211, 47, 47, 0.2) 100%);
        color: var(--primary-pink);
    }

    .stat-danger .stat-icon {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.2) 100%);
        color: #dc2626;
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        background: var(--gradient-primary);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        font-size: 0.95rem;
        color: #6b7280;
        font-weight: 500;
        margin: 0;
    }

    .stat-wave {
        position: absolute;
        bottom: -10px;
        right: -10px;
        width: 100px;
        height: 100px;
        background: radial-gradient(circle, rgba(194, 24, 91, 0.1) 0%, transparent 70%);
        border-radius: 50%;
        z-index: 1;
    }

    /* === Cartes modernes === */
    .modern-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .modern-card:hover {
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
    }

    .card-header-modern {
        padding: 1.75rem 2rem;
        border: none;
        color: white;
    }

    .bg-gradient-success {
        background: var(--gradient-success);
    }

    .bg-gradient-primary {
        background: var(--gradient-primary);
    }

    .header-icon {
        width: 50px;
        height: 50px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    /* === Table moderne === */
    .table-modern {
        margin: 0;
    }

    .table-modern thead th {
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border: none;
        padding: 1.25rem 1.5rem;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #374151;
    }

    .table-modern tbody tr {
        border-bottom: 1px solid #f3f4f6;
        transition: all 0.3s ease;
    }

    .table-modern tbody tr:hover {
        background: linear-gradient(135deg, rgba(194, 24, 91, 0.02) 0%, rgba(211, 47, 47, 0.02) 100%);
        transform: scale(1.01);
    }

    .table-modern tbody td {
        padding: 1.5rem 1.5rem;
        vertical-align: middle;
        border: none;
    }

    /* === Module Info === */
    .module-info {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .module-title {
        color: #111827;
        font-weight: 600;
        font-size: 1rem;
    }

    .module-meta {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .badge-light {
        background: linear-gradient(135deg, #bebebeff 0%, #cacacaff 100%);
        color: #4b5563;
        padding: 0.4rem 0.8rem;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* === Progress moderne === */
    .progress-modern {
        height: 40px;
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-radius: 12px;
        overflow: hidden;
        position: relative;
        box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.06);
    }

    .progress-bar-modern {
        height: 100%;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        border-radius: 12px;
        transition: width 0.6s ease;
    }

    .progress-bar-modern.bg-success {
        background: var(--gradient-success);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.4);
    }

    .progress-text {
        font-weight: 700;
        color: white;
        font-size: 0.9rem;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
    }

    /* === Status badges === */
    .status-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.6rem 1.2rem;
        border-radius: 12px;
        font-size: 0.85rem;
        font-weight: 600;
        white-space: nowrap;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .status-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        color: #92400e;
    }

    .status-success {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
    }

    .status-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #991b1b;
    }

    .status-secondary {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        color: #374151;
    }

    /* === Boutons modernes === */
    .btn-modern {
        padding: 0.65rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        border: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .btn-modern:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .btn-modern-primary {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .btn-modern-warning {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
    }

    .btn-modern-gradient {
        background: var(--gradient-primary);
        color: white;
    }

    .btn-group-modern {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
        flex-wrap: wrap;
    }

    /* === Boutons icône === */
    .btn-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: all 0.3s ease;
        cursor: pointer;
        font-size: 0.95rem;
    }

    .btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(0, 0, 0, 0.15);
    }

    .btn-icon-primary {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.2) 0%, rgba(37, 99, 235, 0.2) 100%);
        color: #2563eb;
    }

    .btn-icon-primary:hover {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .btn-icon-warning {
        background: linear-gradient(135deg, rgba(251, 191, 36, 0.2) 0%, rgba(245, 158, 11, 0.2) 100%);
        color: #f59e0b;
    }

    .btn-icon-warning:hover {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
    }

    .btn-icon-danger {
        background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.2) 100%);
        color: #dc2626;
    }

    .btn-icon-danger:hover {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }

    /* === Date info === */
    .date-info {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .date-day {
        color: #111827;
        font-weight: 600;
        font-size: 0.95rem;
    }

    .date-time {
        color: #6b7280;
        font-size: 0.8rem;
    }

    /* === Empty state === */
    .empty-state {
        background: linear-gradient(135deg, rgba(194, 24, 91, 0.05) 0%, rgba(211, 47, 47, 0.05) 100%);
        border-radius: 20px;
        padding: 4rem 2rem;
        text-align: center;
        border: 2px dashed rgba(194, 24, 91, 0.3);
    }

    .empty-state-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 1.5rem;
        background: linear-gradient(135deg, rgba(194, 24, 91, 0.1) 0%, rgba(211, 47, 47, 0.1) 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: var(--primary-pink);
    }

    .empty-state-title {
        color: #111827;
        font-weight: 700;
        bottom: 1rem;
    }

    .empty-state-text {
        color: #6b7280;
        font-size: 1rem;
        max-width: 600px;
        margin: 0 auto;
    }

    .empty-state-small {
        text-align: center;
        padding: 3rem 2rem;
    }

    .empty-state-small i {
        font-size: 4rem;
        color: #d1d5db;
        margin-bottom: 1.5rem;
    }

    .empty-state-small h5 {
        color: #6b7280;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .empty-state-small p {
        color: #9ca3af;
        margin: 0;
    }

    /* === Boutons avec gradient === */
    .btn-gradient-primary {
        background: var(--gradient-primary);
        color: white;
        border: none;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(194, 24, 91, 0.3);
    }

    .btn-gradient-primary:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(194, 24, 91, 0.4);
        color: white;
    }

    .btn-outline-dark {
        border: 2px solid #374151;
        color: #374151;
        background: transparent;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-outline-dark:hover {
        background: #374151;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 16px rgba(55, 65, 81, 0.3);
    }

    /* === Animations === */
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

    .stat-card,
    .modern-card,
    .empty-state {
        animation: fadeInUp 0.6s ease-out;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }

    /* === Responsive === */
    @media (max-width: 768px) {
        .stat-card {
            padding: 1.5rem;
        }

        .stat-number {
            font-size: 2rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
        }

        .table-modern thead th,
        .table-modern tbody td {
            padding: 1rem;
            font-size: 0.85rem;
        }

        .btn-group-modern {
            flex-direction: column;
            align-items: stretch;
        }

        .btn-modern {
            width: 100%;
            justify-content: center;
        }

        .card-header-modern {
            padding: 1.5rem;
        }

        .header-icon {
            width: 40px;
            height: 40px;
            font-size: 1.2rem;
        }

        .empty-state {
            padding: 3rem 1.5rem;
        }

        .empty-state-icon {
            width: 80px;
            height: 80px;
            font-size: 2.5rem;
        }
    }

    /* === Scrollbar personnalisée === */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f3f4f6;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: var(--gradient-primary);
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #a91750 0%, #b71c1c 100%);
    }

    /* === Effets de survol supplémentaires === */
    .module-title {
        transition: color 0.3s ease;
    }

    .table-modern tbody tr:hover .module-title {
        color: var(--primary-pink);
    }

    /* === Badges avec animations === */
    .status-badge {
        position: relative;
        overflow: hidden;
    }

    .status-badge::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }

    .status-badge:hover::before {
        left: 100%;
    }

    /* === Pagination personnalisée === */
    .pagination {
        gap: 0.5rem;
    }

    .pagination .page-link {
        border: none;
        border-radius: 10px;
        padding: 0.6rem 1rem;
        color: #374151;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .pagination .page-link:hover {
        background: var(--gradient-primary);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(194, 24, 91, 0.3);
    }

    .pagination .page-item.active .page-link {
        background: var(--gradient-primary);
        color: white;
        box-shadow: 0 4px 12px rgba(194, 24, 91, 0.3);
    }

    .pagination .page-item.disabled .page-link {
        background: #f3f4f6;
        color: #9ca3af;
        box-shadow: none;
    }

    /* === Effet de brillance sur les cartes === */
    .stat-card::after {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            45deg,
            transparent 30%,
            rgba(255, 255, 255, 0.1) 50%,
            transparent 70%
        );
        transform: rotate(45deg);
        transition: all 0.6s ease;
        opacity: 0;
    }

    .stat-card:hover::after {
        opacity: 1;
        left: 100%;
    }

    /* === Amélioration des ombres === */
    .modern-card,
    .stat-card {
        position: relative;
    }

    .modern-card::before,
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        border-radius: inherit;
        box-shadow: 0 0 0 0 rgba(194, 24, 91, 0.4);
        transition: box-shadow 0.3s ease;
        pointer-events: none;
    }

    .modern-card:hover::before {
        box-shadow: 0 0 0 4px rgba(194, 24, 91, 0.1);
    }

    /* === Styles pour les formulaires dans les modals === */
    .btn-close {
        transition: all 0.3s ease;
    }

    .btn-close:hover {
        transform: rotate(90deg) scale(1.1);
    }

    /* === Amélioration de l'accessibilité === */
    .btn-modern:focus,
    .btn-icon:focus,
    .btn-outline-dark:focus {
        outline: 3px solid rgba(194, 24, 91, 0.3);
        outline-offset: 2px;
    }

    /* === Style pour les tooltips === */
    [title] {
        position: relative;
        cursor: help;
    }

    /* === Effet de pulsation pour les nouveaux éléments === */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.8;
        }
    }

    .status-badge.status-warning {
        animation: pulse 2s ease-in-out infinite;
    }

    /* === Gradient animé pour le header === */
    @keyframes gradientShift {
        0% {
            background-position: 0% 50%;
        }
        50% {
            background-position: 100% 50%;
        }
        100% {
            background-position: 0% 50%;
        }
    }

    .card-header-modern {
        background-size: 200% 200%;
        animation: gradientShift 8s ease infinite;
    }

    /* === Amélioration du contraste === */
    .text-muted {
        color: #6b7280 !important;
    }

    strong {
        font-weight: 600;
    }

    /* === Style pour les liens === */
    a {
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .alert-link {
        color: var(--primary-pink);
        font-weight: 600;
        text-decoration: underline;
    }

    .alert-link:hover {
        color: var(--primary-red);
    }

    /* === Amélioration de la lisibilité === */
    body {
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        line-height: 1.6;
        color: #111827;
    }

    h2, h5 {
        font-weight: 700;
        letter-spacing: -0.5px;
    }

    /* === Style pour les icônes === */
    .fas {
        vertical-align: middle;
    }

    /* === Effet de zoom sur les images/icônes au survol === */
    .stat-icon i,
    .header-icon i {
        transition: transform 0.3s ease;
    }

    .stat-card:hover .stat-icon i,
    .card-header-modern:hover .header-icon i {
        transform: scale(1.1) rotate(5deg);
    }
    a.btn.btn-outline-dark.rounded-pill.px-4 {
    background-color: black !important;
}
</style>
@endpush
@endsection