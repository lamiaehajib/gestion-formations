@extends('layouts.app')

@section('title', 'Mes Attestations')

@section('content')
<style>
    .attestations-header {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(194, 24, 91, 0.3);
    }
    
    .attestations-header h1 {
        color: white;
        font-weight: 700;
        margin: 0;
        font-size: 2rem;
    }
    
    .btn-new-request {
        background: white;
        color: #C2185B;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }
    
    .btn-new-request:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.15);
        color: #C2185B;
    }
    
    .alert-custom-success {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        border: none;
        border-radius: 12px;
        color: white;
        padding: 1rem 1.5rem;
        box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2);
    }
    
    .alert-custom-error {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        border-radius: 12px;
        color: white;
        padding: 1rem 1.5rem;
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.2);
    }
    
    .info-box-custom {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        border-radius: 15px;
        color: white;
        padding: 1.25rem 1.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 6px 20px rgba(59, 130, 246, 0.2);
    }
    
    .attestation-card {
        border: none;
        border-radius: 20px;
        overflow: hidden;
        transition: all 0.4s ease;
        box-shadow: 0 5px 20px rgba(0, 0, 0, 0.08);
        background: white;
    }
    
    .attestation-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(194, 24, 91, 0.15);
    }
    
    .card-header-custom {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        color: white;
        padding: 1.5rem;
        border: none;
    }
    
    .card-header-custom h5 {
        margin: 0;
        font-weight: 700;
        font-size: 1.1rem;
    }
    
    .card-body-custom {
        padding: 1.5rem;
    }
    
    .info-label {
        color: #6b7280;
        font-size: 0.85rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 0.25rem;
    }
    
    .info-value {
        color: #1f2937;
        font-weight: 600;
        font-size: 1rem;
        margin-bottom: 1rem;
    }
    
    .badge-custom {
        padding: 0.5rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.85rem;
    }
    
    .badge-pending {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        color: white;
    }
    
    .badge-processing {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }
    
    .badge-ready {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .badge-level {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        padding: 0.4rem 0.9rem;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.8rem;
    }
    
    .admin-message-box {
        background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
        border-left: 4px solid #C2185B;
        padding: 1rem;
        border-radius: 10px;
        margin-bottom: 1rem;
    }
    
    .btn-download {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        width: 100%;
    }
    
    .btn-download:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(16, 185, 129, 0.3);
        color: white;
    }
    
    .btn-disabled-custom {
        background: linear-gradient(135deg, #9ca3af 0%, #6b7280 100%);
        color: white;
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        width: 100%;
        cursor: not-allowed;
    }
    
    .empty-state {
        background: linear-gradient(135deg, #f9fafb 0%, #f3f4f6 100%);
        border-radius: 20px;
        padding: 4rem 2rem;
        text-align: center;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    
    .empty-state-icon {
        color: #C2185B;
        opacity: 0.6;
        font-size: 4rem;
        margin-bottom: 1.5rem;
    }
    
    .card-footer-custom {
        background: #f9fafb;
        border: none;
        padding: 1.25rem;
    }
    
    .btn-close-custom {
        filter: brightness(0) invert(1);
    }
</style>

<div class="container py-4">
    <div class="attestations-header d-flex justify-content-between align-items-center">
        <h1>
            <i class="fas fa-file-alt me-2"></i> Mes Attestations
        </h1>
        <a href="{{ route('student.attestations.create') }}" class="btn-new-request">
            <i class="fas fa-plus me-2"></i> Nouvelle demande
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-custom-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close btn-close-custom" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-custom-error alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close btn-close-custom" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Info Box -->
    <div class="info-box-custom">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Information:</strong> Vous pouvez demander une attestation de scolarité pour vos inscriptions actives ou terminées. 
        Le traitement prend généralement 2-3 jours ouvrables.
    </div>

    <div class="row">
        @forelse($attestations as $attestation)
        <div class="col-md-6 col-lg-4 mb-4">
            <div class="card attestation-card h-100">
                <div class="card-header-custom">
                    <h5>
                        <i class="fas fa-graduation-cap me-2"></i>
                        {{ Str::limit($attestation->inscription->formation->title ?? 'Formation', 30) }}
                    </h5>
                </div>
                <div class="card-body-custom">
                    <div class="mb-3">
                        <div class="info-label">Niveau</div>
                        <span class="badge-level">
                            {{ $attestation->inscription->formation->category->name ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Année académique</div>
                        <div class="info-value">{{ $attestation->academic_year }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Date de demande</div>
                        <div class="info-value">{{ $attestation->created_at->format('d/m/Y') }}</div>
                    </div>
                    <div class="mb-3">
                        <div class="info-label">Status</div>
                        @if($attestation->status === 'pending')
                            <span class="badge-custom badge-pending">
                                <i class="fas fa-clock me-1"></i> En attente de traitement
                            </span>
                        @elseif($attestation->status === 'en_traitement')
                            <span class="badge-custom badge-processing">
                                <i class="fas fa-spinner me-1"></i> En cours de traitement
                            </span>
                        @else
                            <span class="badge-custom badge-ready">
                                <i class="fas fa-check-circle me-1"></i> Prête à télécharger
                            </span>
                        @endif
                    </div>

                    @if($attestation->admin_message)
                        <div class="admin-message-box">
                            <small><strong>Message admin:</strong> {{ $attestation->admin_message }}</small>
                        </div>
                    @endif

                    @if($attestation->processed_at)
                        <small class="text-muted d-block">
                            <i class="fas fa-clock me-1"></i> Traité le: {{ $attestation->processed_at->format('d/m/Y à H:i') }}
                        </small>
                    @endif
                </div>
                <div class="card-footer-custom">
                    @if($attestation->canBeDownloaded())
                        <a href="{{ route('student.attestations.download', $attestation) }}" class="btn-download">
                            <i class="fas fa-download me-2"></i> Télécharger l'attestation
                        </a>
                    @else
                        <button class="btn-disabled-custom" disabled>
                            <i class="fas fa-hourglass-half me-2"></i> En cours de traitement...
                        </button>
                    @endif
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h4 style="color: #1f2937; font-weight: 700;">Aucune attestation demandée</h4>
                <p style="color: #6b7280; margin-top: 1rem;">Vous n'avez pas encore fait de demande d'attestation de scolarité.</p>
                <a href="{{ route('student.attestations.create') }}" class="btn-new-request mt-4" style="display: inline-block;">
                    <i class="fas fa-plus me-2"></i> Faire une demande
                </a>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection