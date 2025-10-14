@extends('layouts.app')

@section('content')
<div class="container-fluid px-4 py-4">
    {{-- En-tête avec gradient --}}
    <div class="header-section mb-5">
        <div class="row align-items-center">
            <div class="col-md-8">
                <div class="d-flex align-items-center mb-2">
                    <div class="icon-wrapper me-3">
                        <i class="fas fa-tasks"></i>
                    </div>
                    <div>
                        <h2 class="mb-1 fw-bold">Gestion des Documentations</h2>
                        <p class="text-muted mb-0">Vérification et validation des documentations des consultants</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Messages Flash --}}
    @if(session('success'))
        <div class="alert alert-custom alert-success alert-dismissible fade show">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-2"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-custom alert-danger alert-dismissible fade show">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-2"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Statistiques avec cards modernes --}}
    <div class="row g-4 mb-5">
        <div class="col-md-3">
            <div class="stat-card stat-warning">
                <div class="stat-icon">
                    <i class="fas fa-hourglass-half"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['pending'] }}</h3>
                    <p class="stat-label">En attente</p>
                </div>
                <div class="stat-decoration"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-success">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['approved'] }}</h3>
                    <p class="stat-label">Approuvées</p>
                </div>
                <div class="stat-decoration"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-danger">
                <div class="stat-icon">
                    <i class="fas fa-times-circle"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['rejected'] }}</h3>
                    <p class="stat-label">Rejetées</p>
                </div>
                <div class="stat-decoration"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card stat-primary">
                <div class="stat-icon">
                    <i class="fas fa-file-alt"></i>
                </div>
                <div class="stat-content">
                    <h3 class="stat-number">{{ $stats['total'] }}</h3>
                    <p class="stat-label">Total</p>
                </div>
                <div class="stat-decoration"></div>
            </div>
        </div>
    </div>

    {{-- Filtres modernes --}}
    <div class="filter-card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('documentations.adminIndex') }}" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-filter me-2"></i>Filtrer par statut
                    </label>
                    <select name="status" class="form-select form-select-custom" onchange="this.form.submit()">
                        <option value="all" {{ $status === 'all' ? 'selected' : '' }}>
                            Tous les statuts
                        </option>
                        <option value="pending" {{ $status === 'pending' ? 'selected' : '' }}>
                            En attente
                        </option>
                        <option value="approved" {{ $status === 'approved' ? 'selected' : '' }}>
                            Approuvées
                        </option>
                        <option value="rejected" {{ $status === 'rejected' ? 'selected' : '' }}>
                            Rejetées
                        </option>
                    </select>
                </div>
                <div class="col-md-8">
                    <a href="{{ route('documentations.adminIndex') }}" class="btn btn-reset">
                        <i class="fas fa-redo me-2"></i>Réinitialiser
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Liste des documentations --}}
    <div class="main-card">
        <div class="card-header-custom">
            <h5 class="mb-0">
                <i class="fas fa-list me-2"></i> 
                Liste des documentations
                <span class="badge-count">{{ $documentations->total() }}</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($documentations->count() > 0)
                <div class="table-responsive">
                    <table class="table table-custom mb-0">
                        <thead>
                            <tr>
                              
                                <th width="15%"><i class="fas fa-user me-2"></i>Consultant</th>
                                <th width="15%"><i class="fas fa-book me-2"></i>Module</th>
                                <th width="25%"><i class="fas fa-align-left me-2"></i>Description</th>
                                <th width="10%" class="text-center"><i class="fas fa-flag me-2"></i>Statut</th>
                                <th width="12%"><i class="fas fa-calendar me-2"></i>Date</th>
                                <th width="18%" class="text-center"><i class="fas fa-cogs me-2"></i>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($documentations as $doc)
                                <tr class="table-row-hover">
                                   
                                    <td>
                                        <div class="user-info">
                                            <strong class="d-block">{{ $doc->consultant->name }}</strong>
                                            <small class="text-muted">{{ $doc->consultant->email }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="module-badge">{{ $doc->module->title }}</span>
                                    </td>
                                    <td>
                                        <small class="text-secondary">{{ Str::limit($doc->description, 80) }}</small>
                                    </td>
                                    <td class="text-center">
                                        @if($doc->status === 'pending')
                                            <span class="status-badge status-pending">
                                                <i class="fas fa-clock me-1"></i>En attente
                                            </span>
                                        @elseif($doc->status === 'approved')
                                            <span class="status-badge status-approved">
                                                <i class="fas fa-check me-1"></i>Approuvée
                                            </span>
                                        @else
                                            <span class="status-badge status-rejected">
                                                <i class="fas fa-times me-1"></i>Rejetée
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="date-info">
                                            <i class="fas fa-calendar-alt me-1"></i> 
                                            <span class="d-block">{{ $doc->created_at->format('d/m/Y') }}</span>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i> 
                                                {{ $doc->created_at->format('H:i') }}
                                            </small>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group-custom">
                                            <a href="{{ route('documentations.adminShow', $doc->id) }}" 
                                               class="btn btn-action btn-view"
                                               title="Voir et vérifier">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if($doc->isPending())
                                                <button type="button" 
                                                        class="btn btn-action btn-approve"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#approveModal{{ $doc->id }}"
                                                        title="Approuver">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                                
                                                <button type="button" 
                                                        class="btn btn-action btn-reject"
                                                        data-bs-toggle="modal" 
                                                        data-bs-target="#rejectModal{{ $doc->id }}"
                                                        title="Rejeter">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>

                                {{-- Modal Approbation --}}
                                @if($doc->isPending())
                                <div class="modal fade" id="approveModal{{ $doc->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content modal-custom">
                                            <form action="{{ route('documentations.approve', $doc->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header modal-header-success">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-check-circle me-2"></i> 
                                                        Approuver la documentation
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <p class="mb-3">Voulez-vous approuver cette documentation ?</p>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">Commentaire (optionnel)</label>
                                                        <textarea name="admin_comment" 
                                                                  class="form-control form-control-custom" 
                                                                  rows="3"
                                                                  placeholder="Ajoutez un commentaire..."></textarea>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">
                                                        Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-success-custom">
                                                        <i class="fas fa-check me-2"></i>Approuver
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                {{-- Modal Rejet --}}
                                <div class="modal fade" id="rejectModal{{ $doc->id }}" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content modal-custom">
                                            <form action="{{ route('documentations.reject', $doc->id) }}" method="POST">
                                                @csrf
                                                <div class="modal-header modal-header-danger">
                                                    <h5 class="modal-title">
                                                        <i class="fas fa-times-circle me-2"></i> 
                                                        Rejeter la documentation
                                                    </h5>
                                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                                </div>
                                                <div class="modal-body">
                                                    <div class="alert alert-warning-custom">
                                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                                        Le consultant sera notifié de votre décision
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label fw-semibold">
                                                            Raison du rejet <span class="text-danger">*</span>
                                                        </label>
                                                        <textarea name="admin_comment" 
                                                                  class="form-control form-control-custom" 
                                                                  rows="4"
                                                                  placeholder="Expliquez pourquoi vous rejetez cette documentation..."
                                                                  required></textarea>
                                                        <small class="text-muted">Minimum 10 caractères</small>
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary-custom" data-bs-dismiss="modal">
                                                        Annuler
                                                    </button>
                                                    <button type="submit" class="btn btn-danger-custom">
                                                        <i class="fas fa-times me-2"></i>Rejeter
                                                    </button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                @endif
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="card-footer-custom">
                    <div class="d-flex justify-content-center">
                        {{ $documentations->links() }}
                    </div>
                </div>
            @else
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-inbox"></i>
                    </div>
                    <h5>Aucune documentation trouvée</h5>
                    <p class="text-muted">
                        @if($status !== 'all')
                            Aucune documentation avec le statut "{{ $status }}"
                        @else
                            Aucune documentation n'a été soumise pour le moment
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>

@push('styles')
<style>
    :root {
        --primary-pink: #C2185B;
        --primary-red: #D32F2F;
        --accent-red: #ef4444;
        --bg-gradient: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        --shadow-sm: 0 2px 8px rgba(194, 24, 91, 0.1);
        --shadow-md: 0 4px 16px rgba(194, 24, 91, 0.15);
        --shadow-lg: 0 8px 24px rgba(194, 24, 91, 0.2);
    }

    body {
        background: #f8f9fa;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
    }

    /* En-tête */
    .header-section {
        background: var(--bg-gradient);
        padding: 2rem;
        border-radius: 16px;
        color: white;
        box-shadow: var(--shadow-lg);
    }

    .icon-wrapper {
        width: 60px;
        height: 60px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        backdrop-filter: blur(10px);
    }

    .header-section h2 {
        color: white;
        font-size: 2rem;
        font-weight: 700;
    }

    .header-section p {
        color: rgba(255, 255, 255, 0.9);
    }

    /* Alerts personnalisées */
    .alert-custom {
        border: none;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: var(--shadow-sm);
        border-left: 4px solid;
    }

    .alert-success {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        border-left-color: #28a745;
    }

    .alert-danger {
        background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
        border-left-color: var(--primary-red);
    }

    /* Cartes de statistiques */
    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.75rem;
        position: relative;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        transition: all 0.3s ease;
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-lg);
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 1rem;
        color: white;
    }

    .stat-warning .stat-icon {
        background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
    }

    .stat-success .stat-icon {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
    }

    .stat-danger .stat-icon {
        background: var(--bg-gradient);
    }

    .stat-primary .stat-icon {
        background: linear-gradient(135deg, var(--primary-pink) 0%, var(--primary-red) 100%);
    }

    .stat-number {
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        background: var(--bg-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.95rem;
        font-weight: 500;
        margin: 0;
    }

    .stat-decoration {
        position: absolute;
        right: -30px;
        bottom: -30px;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, rgba(194, 24, 91, 0.05) 0%, transparent 70%);
        border-radius: 50%;
    }

    /* Carte de filtres */
    .filter-card {
        background: white;
        border-radius: 16px;
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .form-select-custom {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
        font-weight: 500;
    }

    .form-select-custom:focus {
        border-color: var(--primary-pink);
        box-shadow: 0 0 0 0.2rem rgba(194, 24, 91, 0.15);
    }

    .btn-reset {
        background: linear-gradient(135deg, #6c757d 0%, #5a6268 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-reset:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(108, 117, 125, 0.3);
        color: white;
    }

    /* Carte principale */
    .main-card {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-md);
        border: 1px solid rgba(0, 0, 0, 0.05);
    }

    .card-header-custom {
        background: var(--bg-gradient);
        color: white;
        padding: 1.5rem 2rem;
        border-bottom: none;
    }

    .card-header-custom h5 {
        font-weight: 700;
        font-size: 1.25rem;
    }

    .badge-count {
        background: rgba(255, 255, 255, 0.25);
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.9rem;
        font-weight: 600;
        backdrop-filter: blur(10px);
    }

.table-custom {
    margin-bottom: 0;
    border-collapse: separate; /* Important pour les coins arrondis et les bordures espacées */
    border-spacing: 0;
    width: 100%; /* S'assurer qu'il prend toute la largeur */
}

/* En-tête */
.table-custom thead {
    background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
}

.table-custom thead th {
    font-weight: 700;
    text-transform: uppercase;
    font-size: 0.75rem;
    color: #495057;
    padding: 1.25rem 1rem;
    border: none;
    letter-spacing: 0.5px;
    /* Ajoutez une bordure en bas pour séparer l'en-tête du corps */
    border-bottom: 2px solid #dee2e6;
}

/* Corps du tableau et cellules */
.table-custom tbody tr {
    background-color: white; /* Assurer un fond blanc pour les lignes */
    border-radius: 8px; /* Si vous voulez des coins arrondis pour les lignes */
}

.table-custom tbody td {
    padding: 1.25rem 1rem;
    vertical-align: middle;
    /* Rendre la bordure plus visible */
    border-bottom: 1px solid #e2e6ea; 
    border-top: 1px solid #f1f3f5;
    color: #343a40; /* Couleur de texte par défaut pour les cellules */
}

/* Effet de survol de ligne */
.table-row-hover {
    transition: all 0.3s ease;
}

.table-row-hover:hover {
    background: linear-gradient(135deg, rgba(194, 24, 91, 0.05) 0%, rgba(211, 47, 47, 0.05) 100%); /* Légèrement plus foncé */
    transform: none; /* Enlever la transformation pour les lignes de tableau pour éviter les artefacts d'affichage */
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

    .user-info strong {
        color: #212529;
        font-weight: 600;
    }

    .module-badge {
        background: linear-gradient(135deg, rgba(194, 24, 91, 0.1) 0%, rgba(211, 47, 47, 0.1) 100%);
        color: var(--primary-pink);
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.875rem;
        display: inline-block;
    }

    /* Status badges */
    .status-badge {
        padding: 0.5rem 1rem;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    .status-pending {
        background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
        color: #856404;
    }

    .status-approved {
        background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
        color: #155724;
    }

    .status-rejected {
        background: var(--bg-gradient);
        color: white;
    }

    /* Boutons d'action */
    .btn-group-custom {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .btn-action {
        width: 38px;
        height: 38px;
        border-radius: 8px;
        border: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
        font-size: 0.9rem;
    }

    .btn-view {
        background: linear-gradient(135deg, var(--primary-pink) 0%, var(--primary-red) 100%);
        color: white;
    }

    .btn-view:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(194, 24, 91, 0.4);
        color: white;
    }

    .btn-approve {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
    }

    .btn-approve:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }

    .btn-reject {
        background: linear-gradient(135deg, var(--accent-red) 0%, var(--primary-red) 100%);
        color: white;
    }

    .btn-reject:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.4);
    }

    /* Modales */
    .modal-custom {
        border: none;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: var(--shadow-lg);
    }

    .modal-header-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
        padding: 1.5rem 2rem;
    }

    .modal-header-danger {
        background: var(--bg-gradient);
        color: white;
        border: none;
        padding: 1.5rem 2rem;
    }

    .modal-custom .modal-body {
        padding: 2rem;
    }

    .modal-custom .modal-footer {
        padding: 1.5rem 2rem;
        border-top: 1px solid #f1f3f5;
    }

    .form-control-custom {
        border: 2px solid #e9ecef;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control-custom:focus {
        border-color: var(--primary-pink);
        box-shadow: 0 0 0 0.2rem rgba(194, 24, 91, 0.15);
    }

    .alert-warning-custom {
        background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);
        border: none;
        border-left: 4px solid #ffc107;
        border-radius: 10px;
        padding: 1rem;
        color: #856404;
    }

    .btn-secondary-custom {
        background: #6c757d;
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-secondary-custom:hover {
        background: #5a6268;
        transform: translateY(-2px);
    }

    .btn-success-custom {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-success-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(40, 167, 69, 0.4);
    }

    .btn-danger-custom {
        background: var(--bg-gradient);
        color: white;
        border: none;
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-danger-custom:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(194, 24, 91, 0.4);
    }

    /* Footer */
    .card-footer-custom {
        background: #f8f9fa;
        padding: 1.5rem;
        border-top: 1px solid #e9ecef;
    }

    /* Empty state */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-icon {
        width: 100px;
        height: 100px;
        margin: 0 auto 2rem;
        background: var(--bg-gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 3rem;
        color: white;
    }

    .empty-state h5 {
        color: #495057;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    /* Pagination */
    .pagination {
        margin: 0;
    }

    .page-link {
        border: none;
        border-radius: 8px;
        margin: 0 0.25rem;
        color: var(--primary-pink);
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background: var(--bg-gradient);
        color: white;
    }

    .page-item.active .page-link {
        background: var(--bg-gradient);
        border: none;
        box-shadow: 0 4px 12px rgba(194, 24, 91, 0.3);
    }

    .page-item.disabled .page-link {
        background: #f8f9fa;
        color: #6c757d;
    }

    /* Animations */
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(20px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .stat-card,
    .filter-card,
    .main-card {
        animation: fadeInUp 0.6s ease-out;
    }

    .stat-card:nth-child(1) { animation-delay: 0.1s; }
    .stat-card:nth-child(2) { animation-delay: 0.2s; }
    .stat-card:nth-child(3) { animation-delay: 0.3s; }
    .stat-card:nth-child(4) { animation-delay: 0.4s; }

    /* Date info */
    .date-info {
        font-size: 0.9rem;
    }

    .date-info span {
        font-weight: 600;
        color: #495057;
    }

    /* Scrollbar personnalisée */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f3f5;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: var(--bg-gradient);
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #a01548 0%, #b32828 100%);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .header-section {
            padding: 1.5rem;
        }

        .icon-wrapper {
            width: 50px;
            height: 50px;
            font-size: 20px;
        }

        .header-section h2 {
            font-size: 1.5rem;
        }

        .stat-card {
            padding: 1.25rem;
        }

        .stat-number {
            font-size: 2rem;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 24px;
        }

        .btn-group-custom {
            flex-direction: column;
            gap: 0.25rem;
        }

        */
    }

    /* Focus visible pour l'accessibilité */
    .btn-action:focus-visible,
    .btn-reset:focus-visible,
    .btn-secondary-custom:focus-visible,
    .btn-success-custom:focus-visible,
    .btn-danger-custom:focus-visible {
        outline: 3px solid rgba(194, 24, 91, 0.5);
        outline-offset: 2px;
    }

    /* Amélioration des transitions */
    * {
        transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Effet de survol sur les lignes du tableau */
    .table-custom tbody tr {
        position: relative;
    }

   

    /* Badge avec animation pulse pour pending */
    @keyframes pulse {
        0%, 100% {
            opacity: 1;
        }
        50% {
            opacity: 0.7;
        }
    }

    .status-pending {
        animation: pulse 2s ease-in-out infinite;
    }

    /* Amélioration du modal backdrop */
    .modal-backdrop.show {
        opacity: 0.7;
        backdrop-filter: blur(4px);
    }

    /* Style pour les tooltips Bootstrap */
    .tooltip-inner {
        background: var(--bg-gradient);
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-weight: 600;
    }

    .tooltip.bs-tooltip-top .tooltip-arrow::before {
        border-top-color: var(--primary-pink);
    }

    .tooltip.bs-tooltip-bottom .tooltip-arrow::before {
        border-bottom-color: var(--primary-pink);
    }

    /* Loading state pour les boutons */
    .btn-action:disabled,
    .btn-reset:disabled,
    .btn-secondary-custom:disabled,
    .btn-success-custom:disabled,
    .btn-danger-custom:disabled {
        opacity: 0.6;
        cursor: not-allowed;
        transform: none !important;
    }

    /* Amélioration de l'accessibilité pour les lecteurs d'écran */
    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border: 0;
    }

    /* Style pour les inputs en erreur */
    .form-control-custom.is-invalid {
        border-color: var(--accent-red);
    }

    .form-control-custom.is-invalid:focus {
        box-shadow: 0 0 0 0.2rem rgba(239, 68, 68, 0.25);
    }

    /* Amélioration du contraste pour l'accessibilité */
    .text-muted {
        color: #6c757d !important;
    }

    /* Style pour les liens dans les modales */
    .modal-body a {
        color: var(--primary-pink);
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
    }

    .modal-body a:hover {
        color: var(--primary-red);
        text-decoration: underline;
    }

    /* Effet glassmorphism sur les cartes */
    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(135deg, rgba(255, 255, 255, 0.1) 0%, rgba(255, 255, 255, 0) 100%);
        pointer-events: none;
        border-radius: 16px;
    }

    /* Animation pour les alertes */
    @keyframes slideInDown {
        from {
            transform: translateY(-100%);
            opacity: 0;
        }
        to {
            transform: translateY(0);
            opacity: 1;
        }
    }

    .alert-custom {
        animation: slideInDown 0.4s ease-out;
    }

    /* Style pour le bouton de fermeture des modales */
    .btn-close:focus {
        box-shadow: 0 0 0 0.2rem rgba(255, 255, 255, 0.5);
    }

    /* Amélioration de la lisibilité des petits textes */
    small {
        font-size: 0.875rem;
        line-height: 1.5;
    }

    /* Style pour les éléments sélectionnés */
    ::selection {
        background: var(--primary-pink);
        color: white;
    }

    ::-moz-selection {
        background: var(--primary-pink);
        color: white;
    }

    /* Print styles */
    @media print {
        .btn-action,
        .btn-reset,
        .filter-card,
        .modal {
            display: none !important;
        }

        .stat-card,
        .main-card {
            box-shadow: none;
            border: 1px solid #dee2e6;
        }

        .table-custom {
            font-size: 0.85rem;
        }
    }
</style>
@endpush
@endsection