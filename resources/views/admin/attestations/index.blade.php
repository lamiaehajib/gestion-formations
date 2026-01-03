@extends('layouts.app')

@section('title', 'Gestion des Attestations')

@section('content')
<style>
    :root {
        --primary-pink: #C2185B;
        --primary-red: #D32F2F;
        --accent-red: #ef4444;
        --dark-bg: #1a1a2e;
        --card-bg: #ffffff;
        --text-dark: #2d3436;
        --text-muted: #636e72;
        --border-light: #e1e8ed;
        --shadow-sm: 0 2px 8px rgba(194, 24, 91, 0.08);
        --shadow-md: 0 4px 16px rgba(194, 24, 91, 0.12);
        --shadow-lg: 0 8px 32px rgba(194, 24, 91, 0.16);
    }

    body {
        background: linear-gradient(135deg, #fef5f8 0%, #fef9fa 100%);
        font-family: 'Inter', 'Segoe UI', system-ui, sans-serif;
    }

    .container-fluid {
        animation: fadeIn 0.6s ease-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Header Section */
    .page-header {
        background: linear-gradient(135deg, var(--primary-pink) 0%, var(--primary-red) 100%);
        padding: 2.5rem 2rem;
        border-radius: 20px;
        color: white;
        box-shadow: var(--shadow-lg);
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 400px;
        height: 400px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .page-header h1 {
        font-size: 2.5rem;
        font-weight: 800;
        margin: 0;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    /* Alert Styles */
    .alert {
        border: none;
        border-radius: 12px;
        padding: 1rem 1.5rem;
        box-shadow: var(--shadow-sm);
        animation: slideDown 0.4s ease-out;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateY(-10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .alert-success {
        background: linear-gradient(135deg, #00b894 0%, #00cec9 100%);
        color: white;
    }

    .alert-danger {
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--accent-red) 100%);
        color: white;
    }

    /* Stats Cards */
    .stats-card {
        background: var(--card-bg);
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: var(--shadow-md);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .stats-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-pink), var(--primary-red), var(--accent-red));
        transform: scaleX(0);
        transition: transform 0.3s ease;
    }

    .stats-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
        border-color: var(--primary-pink);
    }

    .stats-card:hover::before {
        transform: scaleX(1);
    }

    .stats-card.warning::before { background: linear-gradient(90deg, #ff9800, #ff5722); }
    .stats-card.info::before { background: linear-gradient(90deg, #00bcd4, #2196f3); }
    .stats-card.success::before { background: linear-gradient(90deg, #4caf50, #00e676); }
    .stats-card.primary::before { background: linear-gradient(90deg, var(--primary-pink), var(--primary-red)); }

    .stats-icon {
        width: 60px;
        height: 60px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        background: linear-gradient(135deg, rgba(194, 24, 91, 0.1) 0%, rgba(211, 47, 47, 0.1) 100%);
        color: var(--primary-pink);
        transition: all 0.3s ease;
    }

    .stats-card:hover .stats-icon {
        transform: rotate(10deg) scale(1.1);
    }

    .stats-card.warning .stats-icon {
        background: linear-gradient(135deg, rgba(255, 152, 0, 0.1), rgba(255, 87, 34, 0.1));
        color: #ff9800;
    }

    .stats-card.info .stats-icon {
        background: linear-gradient(135deg, rgba(0, 188, 212, 0.1), rgba(33, 150, 243, 0.1));
        color: #00bcd4;
    }

    .stats-card.success .stats-icon {
        background: linear-gradient(135deg, rgba(76, 175, 80, 0.1), rgba(0, 230, 118, 0.1));
        color: #4caf50;
    }

    .stats-label {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 700;
        letter-spacing: 0.5px;
        color: var(--text-muted);
        margin-bottom: 0.5rem;
    }

    .stats-value {
        font-size: 2.5rem;
        font-weight: 800;
        color: var(--text-dark);
        line-height: 1;
    }

    /* Main Card */
    .main-card {
        background: var(--card-bg);
        border-radius: 20px;
        box-shadow: var(--shadow-md);
        overflow: hidden;
        border: none;
    }

    .main-card .card-header {
        background: linear-gradient(135deg, var(--primary-pink) 0%, var(--primary-red) 100%);
        color: white;
        padding: 1.5rem 2rem;
        border: none;
    }

    .main-card .card-header h6 {
        font-size: 1.25rem;
        font-weight: 700;
        margin: 0;
        letter-spacing: 0.3px;
    }

    /* Table Styles */
    .table-responsive {
        border-radius: 12px;
        overflow: hidden;
    }

    .table {
        margin-bottom: 0;
    }

    .table thead th {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        color: var(--text-dark);
        font-weight: 700;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 1rem;
        border: none;
    }

    .table tbody tr {
        transition: all 0.2s ease;
        border-bottom: 1px solid var(--border-light);
    }

    .table tbody tr:hover {
        background: linear-gradient(135deg, rgba(194, 24, 91, 0.03) 0%, rgba(211, 47, 47, 0.03) 100%);
        transform: scale(1.01);
        box-shadow: 0 2px 8px rgba(194, 24, 91, 0.08);
    }

    .table tbody td {
        padding: 1rem;
        vertical-align: middle;
        color: var(--text-dark);
        border: none;
    }

    /* Badge Styles */
    .badge {
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 600;
        font-size: 0.75rem;
        letter-spacing: 0.3px;
        text-transform: uppercase;
    }

    .badge.bg-warning {
        background: linear-gradient(135deg, #ff9800 0%, #ff5722 100%) !important;
        color: white !important;
    }

    .badge.bg-info {
        background: linear-gradient(135deg, #00bcd4 0%, #2196f3 100%) !important;
        color: white !important;
    }

    .badge.bg-success {
        background: linear-gradient(135deg, #4caf50 0%, #00e676 100%) !important;
        color: white !important;
    }

    .badge.bg-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%) !important;
        color: white !important;
    }

    /* Button Styles */
    .btn {
        border-radius: 10px;
        font-weight: 600;
        padding: 0.5rem 1rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }

    .btn:hover::before {
        width: 300px;
        height: 300px;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-pink) 0%, var(--primary-red) 100%);
        box-shadow: 0 4px 12px rgba(194, 24, 91, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(194, 24, 91, 0.4);
    }

    .btn-success {
        background: linear-gradient(135deg, #4caf50 0%, #00e676 100%);
        box-shadow: 0 4px 12px rgba(76, 175, 80, 0.3);
    }

    .btn-success:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(76, 175, 80, 0.4);
    }

    .btn-info {
        background: linear-gradient(135deg, #00bcd4 0%, #2196f3 100%);
        box-shadow: 0 4px 12px rgba(0, 188, 212, 0.3);
    }

    .btn-info:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 188, 212, 0.4);
    }

    .btn-danger {
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--accent-red) 100%);
        box-shadow: 0 4px 12px rgba(211, 47, 47, 0.3);
    }

    .btn-danger:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(211, 47, 47, 0.4);
    }

    .btn-secondary {
        background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
    }

    .btn-group-sm > .btn {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }

    /* Modal Styles */
    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: var(--shadow-lg);
    }

    .modal-header {
        background: linear-gradient(135deg, var(--primary-pink) 0%, var(--primary-red) 100%);
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 1.5rem 2rem;
        border: none;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.25rem;
    }

    .modal-body {
        padding: 2rem;
    }

    .form-label {
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
    }

    .form-control {
        border-radius: 10px;
        border: 2px solid var(--border-light);
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary-pink);
        box-shadow: 0 0 0 0.2rem rgba(194, 24, 91, 0.15);
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: var(--text-muted);
        opacity: 0.3;
        margin-bottom: 1rem;
    }

    /* Pagination */
    .pagination {
        gap: 0.5rem;
    }

    .page-link {
        border-radius: 10px;
        border: none;
        color: var(--primary-pink);
        font-weight: 600;
        padding: 0.5rem 1rem;
        transition: all 0.3s ease;
    }

    .page-link:hover {
        background: linear-gradient(135deg, var(--primary-pink) 0%, var(--primary-red) 100%);
        color: white;
        transform: translateY(-2px);
    }

    .page-item.active .page-link {
        background: linear-gradient(135deg, var(--primary-pink) 0%, var(--primary-red) 100%);
        border: none;
    }

    /* User Info */
    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .user-avatar {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: linear-gradient(135deg, var(--primary-pink) 0%, var(--primary-red) 100%);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 1rem;
    }

    /* Scrollbar */
    ::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }

    ::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--primary-pink) 0%, var(--primary-red) 100%);
        border-radius: 10px;
    }

    ::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--primary-red) 0%, var(--accent-red) 100%);
    }
</style>

<div class="container-fluid px-4">
    <div class="page-header">
        <h1><i class="fas fa-certificate me-3"></i>Gestion des Attestations</h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Statistiques -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card warning">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-label">En attente</div>
                        <div class="stats-value">{{ $attestations->where('status', 'pending')->count() }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card info">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-label">En traitement</div>
                        <div class="stats-value">{{ $attestations->where('status', 'en_traitement')->count() }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-spinner"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card success">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-label">Terminées</div>
                        <div class="stats-value">{{ $attestations->where('status', 'termine')->count() }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="stats-card primary">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="stats-label">Total</div>
                        <div class="stats-value">{{ $attestations->total() }}</div>
                    </div>
                    <div class="stats-icon">
                        <i class="fas fa-file-alt"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des attestations -->
    <div class="main-card card shadow mb-4">
        <div class="card-header">
            <h6><i class="fas fa-list me-2"></i>Liste des demandes d'attestations</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>#ID</th>
                            <th>Étudiant</th>
                            <th>Formation</th>
                            <th>Niveau</th>
                            <th>Date de naissance</th>
                            <th>Année académique</th>
                            <th>Date demande</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attestations as $attestation)
                        <tr>
                            <td class="fw-bold">#{{ $attestation->id }}</td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">
                                        {{ strtoupper(substr($attestation->user->name, 0, 1)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold">{{ $attestation->user->name }}</div>
                                        <small class="text-muted">{{ $attestation->user->email }}</small>
                                    </div>
                                </div>
                            </td>
                            <td>{{ $attestation->inscription->formation->title ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-secondary">
                                    {{ $attestation->inscription->formation->category->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $attestation->birth_date->format('d/m/Y') }}</td>
                            <td>{{ $attestation->academic_year }}</td>
                            <td>{{ $attestation->created_at->format('d/m/Y H:i') }}</td>
                            <td>
                                @if($attestation->status === 'pending')
                                    <span class="badge bg-warning">
                                        <i class="fas fa-clock me-1"></i>En attente
                                    </span>
                                @elseif($attestation->status === 'en_traitement')
                                    <span class="badge bg-info">
                                        <i class="fas fa-spinner me-1"></i>En traitement
                                    </span>
                                @else
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Terminé
                                    </span>
                                @endif
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm" role="group">
                                    @if($attestation->status === 'pending')
                                        <a href="{{ route('admin.attestations.download-for-processing', $attestation) }}" 
                                           class="btn btn-primary" 
                                           title="Télécharger et commencer le traitement">
                                            <i class="fas fa-download me-1"></i>Télécharger
                                        </a>
                                    @endif

                                    @if($attestation->status === 'en_traitement')
                                        <button type="button" 
                                                class="btn btn-success" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#uploadModal{{ $attestation->id }}"
                                                title="Upload attestation signée">
                                            <i class="fas fa-upload me-1"></i>Upload Signé
                                        </button>
                                    @endif

                                    @if($attestation->status === 'termine' && $attestation->signed_document_path)
                                        <a href="{{ route('admin.attestations.download', $attestation) }}" 
                                           class="btn btn-info" 
                                           title="Télécharger l'attestation signée">
                                            <i class="fas fa-download me-1"></i>Télécharger
                                        </a>
                                    @endif

                                    <form action="{{ route('admin.attestations.destroy', $attestation) }}" 
                                          method="POST" 
                                          class="d-inline" 
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette attestation ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </form>

                                   
                                </div>
                            </td>
                        </tr>

                        @if($attestation->status === 'en_traitement')
                        <div class="modal fade" id="uploadModal{{ $attestation->id }}" tabindex="-1">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <form action="{{ route('admin.attestations.upload-signed', $attestation) }}" 
                                          method="POST" 
                                          enctype="multipart/form-data">
                                        @csrf
                                        <div class="modal-header">
                                            <h5 class="modal-title"><i class="fas fa-upload me-2"></i>Upload Attestation Signée</h5>
                                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body">
                                            <div class="mb-3">
                                                <label class="form-label"><i class="fas fa-user me-2"></i>Étudiant</label>
                                                <input type="text" class="form-control" value="{{ $attestation->user->name }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label class="form-label"><i class="fas fa-graduation-cap me-2"></i>Formation</label>
                                                <input type="text" class="form-control" value="{{ $attestation->inscription->formation->title ?? 'N/A' }}" readonly>
                                            </div>
                                            <div class="mb-3">
                                                <label for="signed_document" class="form-label"><i class="fas fa-file-pdf me-2"></i>Fichier PDF Signé *</label>
                                                <input type="file" 
                                                       name="signed_document" 
                                                       id="signed_document" 
                                                       class="form-control" 
                                                       accept=".pdf" 
                                                       required>
                                                <small class="text-muted">Taille max: 5MB - Format: PDF uniquement</small>
                                            </div>
                                            <div class="mb-3">
                                                <label for="admin_message" class="form-label"><i class="fas fa-comment me-2"></i>Message pour l'étudiant (optionnel)</label>
                                                <textarea name="admin_message" 
                                                          id="admin_message" 
                                                          class="form-control" 
                                                          rows="3" 
                                                          placeholder="Ajouter une remarque ou un message..."></textarea>
                                            </div>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                <i class="fas fa-times me-1"></i>Annuler
                                            </button>
                                            <button type="submit" class="btn btn-primary">
                                                <i class="fas fa-check me-1"></i>Valider et Terminer
                                            </button>
                                        </div>
                                    </form>

                                    <a href="{{ route('admin.attestations.download-for-processing', $attestation) }}" 
                                           class="btn btn-primary" 
                                           title="Télécharger et commencer le traitement">
                                            <i class="fas fa-download me-1"></i>Télécharger
                                        </a>
                                </div>
                            </div>
                        </div>
                        @endif

                        @empty
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-inbox"></i>
                                    <p class="text-muted mb-0">Aucune demande d'attestation pour le moment</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-center mt-4">
                {{ $attestations->links() }}
            </div>
        </div>
    </div>
</div>
@endsection