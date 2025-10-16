@extends('layouts.app')

@section('content')
<style>
    .gradient-bg {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
    }
    
    .stat-card {
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }
    
    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(194, 24, 91, 0.2) !important;
    }
    
    .stat-card-1 { border-left-color: #C2185B; }
    .stat-card-2 { border-left-color: #D32F2F; }
    .stat-card-3 { border-left-color: #ef4444; }
    .stat-card-4 { border-left-color: #C2185B; }
    
    .progress-gradient-1 {
        background: linear-gradient(90deg, #C2185B, #D32F2F) !important;
    }
    
    .progress-gradient-2 {
        background: linear-gradient(90deg, #D32F2F, #ef4444) !important;
    }
    
    .progress-gradient-3 {
        background: linear-gradient(90deg, #ef4444, #C2185B) !important;
    }
    
    .survey-card {
        transition: all 0.3s ease;
        border-left: 3px solid #C2185B;
    }
    
    .survey-card:hover {
        box-shadow: 0 5px 20px rgba(194, 24, 91, 0.15);
        transform: translateX(5px);
    }
    
    .badge-custom {
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        color: white;
    }
    
    .btn-back {
        background: white;
        color: #C2185B;
        border: 2px solid #C2185B;
        transition: all 0.3s ease;
    }
    
    .btn-back:hover {
        background: #C2185B;
        color: white;
        border-color: #C2185B;
    }
    
    .btn-stats {
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        color: white;
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-stats:hover {
        background: linear-gradient(135deg, #D32F2F, #ef4444);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(194, 24, 91, 0.3);
    }
    
    .icon-circle {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto;
    }
    
    .icon-circle-1 { background: linear-gradient(135deg, #C2185B, #D32F2F); }
    .icon-circle-2 { background: linear-gradient(135deg, #D32F2F, #ef4444); }
    .icon-circle-3 { background: linear-gradient(135deg, #ef4444, #C2185B); }
    .icon-circle-4 { background: linear-gradient(135deg, #C2185B, #ef4444); }
    
    .header-section {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
        border-radius: 15px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(194, 24, 91, 0.3);
    }
    
    .card-modern {
        border-radius: 15px;
        border: none;
        overflow: hidden;
    }
    
    .card-header-custom {
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        color: white;
        border: none;
    }
    
    .star-rating i {
        font-size: 1.1rem;
    }
    .btn-back {
    background: white;
    color: #C2185A !important;
    border: 2px solid #C2185B;
    transition: all 0.3s 
ease;
}
</style>

<div class="container-fluid px-4 py-5">
    <!-- Boutons de navigation -->
    <div class="mb-4 d-flex gap-2">
        <a href="{{ route('formations.index') }}" class="btn btn-back">
            <i class="fas fa-arrow-left"></i> Retour
        </a>
        <a href="{{ route('satisfaction.statistics', $formation->id) }}" 
           class="btn btn-stats" 
           title="Voir les statistiques">
            <i class="fas fa-chart-line"></i> Statistiques détaillées
        </a>
    </div>

    <!-- En-tête avec gradient -->
    <div class="header-section">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-3 fw-bold">
                    <i class="fas fa-star"></i> Évaluations de la formation
                </h2>
                <h4 class="mb-3 fw-light">{{ $formation->title }}</h4>
                <p class="mb-0 opacity-90">
                    <i class="fas fa-user me-2"></i>{{ $formation->consultant->name ?? 'N/A' }}
                    <span class="mx-3">|</span>
                    <i class="fas fa-folder me-2"></i>{{ $formation->category->name ?? 'N/A' }}
                </p>
            </div>
            <div class="col-md-4 text-end">
                <i class="fas fa-clipboard-check" style="font-size: 5rem; opacity: 0.2;"></i>
            </div>
        </div>
    </div>

    <!-- Statistiques globales -->
    <div class="row mb-4 g-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card stat-card-1 card-modern">
                <div class="card-body text-center py-4">
                    <div class="icon-circle icon-circle-1 mb-3">
                        <i class="fas fa-users fa-2x text-white"></i>
                    </div>
                    <h2 class="mb-2 fw-bold" style="color: #C2185B;">{{ $statistics['total_surveys'] }}</h2>
                    <small class="text-muted fw-semibold">Évaluations totales</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card stat-card-2 card-modern">
                <div class="card-body text-center py-4">
                    <div class="icon-circle icon-circle-2 mb-3">
                        <i class="fas fa-star fa-2x text-white"></i>
                    </div>
                    <h2 class="mb-2 fw-bold" style="color: #D32F2F;">{{ $statistics['average_overall'] ?? 'N/A' }}/5</h2>
                    <small class="text-muted fw-semibold">Satisfaction globale</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card stat-card-3 card-modern">
                <div class="card-body text-center py-4">
                    <div class="icon-circle icon-circle-3 mb-3">
                        <i class="fas fa-thumbs-up fa-2x text-white"></i>
                    </div>
                    <h2 class="mb-2 fw-bold" style="color: #ef4444;">{{ $statistics['recommendation_rate'] }}%</h2>
                    <small class="text-muted fw-semibold">Recommandent</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card border-0 shadow-sm stat-card stat-card-4 card-modern">
                <div class="card-body text-center py-4">
                    <div class="icon-circle icon-circle-4 mb-3">
                        <i class="fas fa-chalkboard-teacher fa-2x text-white"></i>
                    </div>
                    <h2 class="mb-2 fw-bold" style="color: #C2185B;">{{ $statistics['average_instructor_rating'] ?? 'N/A' }}/5</h2>
                    <small class="text-muted fw-semibold">Note formateur</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Détails des moyennes -->
    <div class="card border-0 shadow-sm mb-4 card-modern">
        <div class="card-header card-header-custom py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-chart-bar me-2"></i> Moyennes par critère</h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <div class="col-md-4">
                    <label class="fw-bold mb-2" style="color: #C2185B;">
                        <i class="fas fa-book-open me-2"></i>Qualité du contenu
                    </label>
                    <div class="progress" style="height: 30px; border-radius: 15px;">
                        <div class="progress-bar progress-gradient-1" role="progressbar" 
                             style="width: {{ ($statistics['average_content_quality'] ?? 0) * 20 }}%">
                            <strong>{{ $statistics['average_content_quality'] ?? 'N/A' }}/5</strong>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="fw-bold mb-2" style="color: #D32F2F;">
                        <i class="fas fa-tasks me-2"></i>Organisation
                    </label>
                    <div class="progress" style="height: 30px; border-radius: 15px;">
                        <div class="progress-bar progress-gradient-2" role="progressbar" 
                             style="width: {{ ($statistics['average_organization'] ?? 0) * 20 }}%">
                            <strong>{{ $statistics['average_organization'] ?? 'N/A' }}/5</strong>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label class="fw-bold mb-2" style="color: #ef4444;">
                        <i class="fas fa-file-alt me-2"></i>Support pédagogique
                    </label>
                    <div class="progress" style="height: 30px; border-radius: 15px;">
                        <div class="progress-bar progress-gradient-3" role="progressbar" 
                             style="width: {{ ($statistics['average_support'] ?? 0) * 20 }}%">
                            <strong>{{ $statistics['average_support'] ?? 'N/A' }}/5</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des évaluations -->
    <div class="card border-0 shadow-sm card-modern">
        <div class="card-header card-header-custom py-3">
            <h5 class="mb-0 fw-bold"><i class="fas fa-comments me-2"></i> Évaluations détaillées</h5>
        </div>
        <div class="card-body p-4">
            @if($surveys->count() > 0)
                @foreach($surveys as $survey)
                    <div class="card mb-3 survey-card">
                        <div class="card-header bg-light py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <strong style="color: #C2185B;">
                                        <i class="fas fa-user-circle me-2"></i>{{ $survey->user->name }}
                                    </strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-calendar me-1"></i> 
                                        {{ $survey->submitted_at->format('d/m/Y à H:i') }}
                                    </small>
                                </div>
                                <div class="text-end">
                                    <span class="badge badge-custom fs-6 px-3 py-2 me-2">
                                        <i class="fas fa-star me-1"></i> {{ $survey->overall_satisfaction }}/5
                                    </span>
                                    @if($survey->would_recommend)
                                        <span class="badge px-3 py-2" style="background: #10b981; color: white;">
                                            <i class="fas fa-thumbs-up me-1"></i> Recommande
                                        </span>
                                    @else
                                        <span class="badge px-3 py-2" style="background: #ef4444; color: white;">
                                            <i class="fas fa-thumbs-down me-1"></i> Ne recommande pas
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <!-- Notes par critère -->
                            <div class="row mb-4 g-3">
                                <div class="col-md-3">
                                    <div class="text-center p-3" style="background: #f8f9fa; border-radius: 10px;">
                                        <small class="text-muted fw-semibold d-block mb-2">Contenu</small>
                                        <div class="star-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $survey->content_quality ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3" style="background: #f8f9fa; border-radius: 10px;">
                                        <small class="text-muted fw-semibold d-block mb-2">Formateur</small>
                                        <div class="star-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $survey->instructor_rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3" style="background: #f8f9fa; border-radius: 10px;">
                                        <small class="text-muted fw-semibold d-block mb-2">Organisation</small>
                                        <div class="star-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $survey->organization_rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center p-3" style="background: #f8f9fa; border-radius: 10px;">
                                        <small class="text-muted fw-semibold d-block mb-2">Support</small>
                                        <div class="star-rating">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $survey->support_rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Commentaires -->
                            @if($survey->positive_feedback)
                                <div class="mb-3 p-3" style="background: #f0fdf4; border-left: 4px solid #10b981; border-radius: 8px;">
                                    <strong class="d-block mb-2" style="color: #10b981;">
                                        <i class="fas fa-plus-circle me-2"></i>Points positifs
                                    </strong>
                                    <p class="mb-0 text-muted">{{ $survey->positive_feedback }}</p>
                                </div>
                            @endif

                            @if($survey->improvement_suggestions)
                                <div class="mb-3 p-3" style="background: #fef3c7; border-left: 4px solid #f59e0b; border-radius: 8px;">
                                    <strong class="d-block mb-2" style="color: #f59e0b;">
                                        <i class="fas fa-lightbulb me-2"></i>Suggestions d'amélioration
                                    </strong>
                                    <p class="mb-0 text-muted">{{ $survey->improvement_suggestions }}</p>
                                </div>
                            @endif

                            @if($survey->additional_comments)
                                <div class="p-3" style="background: #eff6ff; border-left: 4px solid #3b82f6; border-radius: 8px;">
                                    <strong class="d-block mb-2" style="color: #3b82f6;">
                                        <i class="fas fa-comment me-2"></i>Commentaires additionnels
                                    </strong>
                                    <p class="mb-0 text-muted">{{ $survey->additional_comments }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $surveys->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <div class="icon-circle icon-circle-1 mb-4" style="width: 100px; height: 100px; margin: 0 auto;">
                        <i class="fas fa-inbox fa-3x text-white"></i>
                    </div>
                    <h5 class="text-muted">Aucune évaluation pour cette formation</h5>
                    <p class="text-muted">Les évaluations apparaîtront ici une fois soumises.</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection