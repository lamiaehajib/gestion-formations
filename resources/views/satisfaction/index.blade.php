@extends('layouts.app')

@section('title', 'Mes Évaluations')

@section('content')
<style>
    .gradient-bg-main {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
    }
    
    .gradient-bg-1 {
        background: linear-gradient(135deg, #C2185B, #D32F2F);
    }
    
    .gradient-bg-2 {
        background: linear-gradient(135deg, #D32F2F, #ef4444);
    }
    
    .gradient-bg-3 {
        background: linear-gradient(135deg, #ef4444, #C2185B);
    }
    
    .page-header-custom {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
        border-radius: 15px;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(194, 24, 91, 0.3);
    }
    
    .alert-custom {
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 1.5rem;
        border: none;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .alert-success-custom {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        color: #065f46;
        border-left: 4px solid #10b981;
    }
    
    .alert-error-custom {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        color: #991b1b;
        border-left: 4px solid #ef4444;
    }
    
    .empty-state-custom {
        text-align: center;
        padding: 5rem 2rem;
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }
    
    .empty-icon-wrapper {
        width: 120px;
        height: 120px;
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
        box-shadow: 0 8px 20px rgba(194, 24, 91, 0.3);
    }
    
    .formation-card-custom {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #C2185B;
    }
    
    .formation-card-custom:hover {
        box-shadow: 0 12px 35px rgba(194, 24, 91, 0.25);
        transform: translateY(-8px);
        border-left-color: #ef4444;
    }
    
    .card-header-custom {
        padding: 1.5rem;
        background: linear-gradient(135deg, rgba(194, 24, 91, 0.05), rgba(239, 68, 68, 0.05));
        border-bottom: 2px solid rgba(194, 24, 91, 0.1);
    }
    
    .badge-pending-custom {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
        white-space: nowrap;
        border: 2px solid #f59e0b;
    }
    
    .card-body-custom {
        padding: 1.5rem;
    }
    
    .formation-description-custom {
        color: #6b7280;
        line-height: 1.7;
        margin-bottom: 1rem;
    }
    
    .meta-item-custom {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 0.875rem;
        color: #6b7280;
        background: #f9fafb;
        padding: 0.5rem 1rem;
        border-radius: 8px;
    }
    
    .meta-item-custom i {
        color: #C2185B;
    }
    
    .card-footer-custom {
        padding: 1.5rem;
        background: #f9fafb;
        border-top: 2px solid rgba(194, 24, 91, 0.1);
    }
    
    .btn-evaluate-custom {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        width: 100%;
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        color: white;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        border: none;
        box-shadow: 0 4px 12px rgba(194, 24, 91, 0.3);
    }
    
    .btn-evaluate-custom:hover {
        background: linear-gradient(135deg, #D32F2F, #ef4444);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(194, 24, 91, 0.4);
        color: white;
    }
    
    .formations-grid-custom {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
        gap: 1.5rem;
    }
    
    @media (max-width: 768px) {
        .formations-grid-custom {
            grid-template-columns: 1fr;
        }
    }
</style>

<div class="container-fluid px-4 py-5">
    <!-- Header avec gradient -->
    <div class="page-header-custom">
        <div class="row align-items-center">
            <div class="col-md-9">
                <h1 class="display-5 fw-bold mb-2">
                    <i class="fas fa-star me-3"></i>Évaluations de formations
                </h1>
                <p class="fs-5 mb-0 opacity-90">Donnez votre avis sur les formations que vous avez terminées</p>
            </div>
            <div class="col-md-3 text-end">
                <i class="fas fa-clipboard-check" style="font-size: 5rem; opacity: 0.2;"></i>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if(session('success'))
        <div class="alert-custom alert-success-custom">
            <div class="d-flex align-items-center">
                <i class="fas fa-check-circle me-3 fs-4"></i>
                <div>
                    <strong>Succès !</strong>
                    <p class="mb-0">{{ session('success') }}</p>
                </div>
            </div>
        </div>
    @endif

    @if(session('error'))
        <div class="alert-custom alert-error-custom">
            <div class="d-flex align-items-center">
                <i class="fas fa-exclamation-circle me-3 fs-4"></i>
                <div>
                    <strong>Erreur !</strong>
                    <p class="mb-0">{{ session('error') }}</p>
                </div>
            </div>
        </div>
    @endif

    <!-- Empty State -->
    @if($inscriptions->isEmpty())
        <div class="empty-state-custom">
            <div class="empty-icon-wrapper">
                <i class="fas fa-clipboard-list fa-4x text-white"></i>
            </div>
            <h3 class="fw-bold mb-3" style="color: #1f2937; font-size: 1.75rem;">
                Aucune évaluation en attente
            </h3>
            <p class="text-muted fs-5 mb-0">
                Vous avez évalué toutes vos formations terminées. Merci pour votre participation !
            </p>
        </div>
    @else
        <!-- Formations Grid -->
        <div class="formations-grid-custom">
            @foreach($inscriptions as $inscription)
                <div class="formation-card-custom">
                    <div class="card-header-custom">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <h3 class="fw-bold mb-0" style="color: #1f2937; font-size: 1.25rem;">
                                {{ $inscription->formation->name ?? 'Formation' }}
                            </h3>
                            <span class="badge-pending-custom">
                                <i class="fas fa-hourglass-half me-1"></i>À évaluer
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-body-custom">
                        <p class="formation-description-custom mb-3">
                            {{ Str::limit($inscription->formation->description ?? 'Aucune description disponible.', 100) }}
                        </p>
                        
                        <div class="meta-item-custom">
                            <i class="fas fa-calendar-check"></i>
                            <span>
                                Terminée le {{ $inscription->completed_at ? $inscription->completed_at->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-footer-custom">
                        <a href="{{ route('satisfaction.create', $inscription->id) }}" class="btn-evaluate-custom">
                            <i class="fas fa-star"></i>
                            Évaluer maintenant
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>
@endsection