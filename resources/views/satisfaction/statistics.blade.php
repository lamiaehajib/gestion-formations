@extends('layouts.app')

@section('title', 'Statistiques des Sondages de Satisfaction')

@section('content')
<style>
    .gradient-bg-1 {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
    }
    
    .gradient-bg-2 {
        background: linear-gradient(135deg, #D32F2F 0%, #ef4444 100%);
    }
    
    .gradient-bg-3 {
        background: linear-gradient(135deg, #ef4444 0%, #C2185B 100%);
    }
    
    .stat-card {
        transition: all 0.3s ease;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(194, 24, 91, 0.2);
    }
    
    .stat-card:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 12px 30px rgba(194, 24, 91, 0.35);
    }
    
    .progress-bar-1 {
        background: linear-gradient(90deg, #C2185B, #D32F2F) !important;
    }
    
    .progress-bar-2 {
        background: linear-gradient(90deg, #D32F2F, #ef4444) !important;
    }
    
    .progress-bar-3 {
        background: linear-gradient(90deg, #ef4444, #C2185B) !important;
    }
    
    .progress-bar-4 {
        background: linear-gradient(90deg, #C2185B, #ef4444) !important;
    }
    
    .survey-card {
        transition: all 0.3s ease;
        border-left: 4px solid #C2185B;
        border-radius: 12px;
    }
    
    .survey-card:hover {
        box-shadow: 0 8px 25px rgba(194, 24, 91, 0.2);
        transform: translateX(5px);
    }
    
    .badge-score {
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
    }
    
    .badge-recommend {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 25px;
        font-weight: 600;
    }
    
    .icon-wrapper {
        background: rgba(255, 255, 255, 0.25);
        border-radius: 50%;
        padding: 1rem;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .metric-box {
        background: #f8f9fa;
        border-radius: 12px;
        padding: 1rem;
        text-align: center;
        transition: all 0.3s ease;
    }
    
    .metric-box:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(194, 24, 91, 0.15);
    }
    
    .metric-box-1 { border-top: 3px solid #C2185B; }
    .metric-box-2 { border-top: 3px solid #D32F2F; }
    .metric-box-3 { border-top: 3px solid #ef4444; }
    .metric-box-4 { border-top: 3px solid #C2185B; }
    
    .feedback-section {
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 0.75rem;
    }
    
    .feedback-positive {
        background: linear-gradient(135deg, #d1fae5, #a7f3d0);
        border-left: 4px solid #10b981;
    }
    
    .feedback-improvement {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-left: 4px solid #f59e0b;
    }
    
    .feedback-comment {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border-left: 4px solid #3b82f6;
    }
    
    .progress-modern {
        height: 12px;
        border-radius: 10px;
        background: #e5e7eb;
        overflow: hidden;
    }
    
    .card-modern {
        background: white;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
        border: none;
    }
    
    .header-gradient {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
        border-radius: 15px;
        padding: 2.5rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(194, 24, 91, 0.3);
    }
</style>

<div class="container-fluid px-4 py-5">
    <!-- Header avec gradient -->
    <div class="header-gradient mb-5">
        <div class="row align-items-center">
            <div class="col-md-9">
                <h1 class="display-5 fw-bold mb-2">
                    <i class="fas fa-chart-line me-3"></i>Statistiques des Sondages
                </h1>
                <p class="fs-5 mb-0 opacity-90">Analyse détaillée des retours des étudiants</p>
            </div>
            <div class="col-md-3 text-end">
                <i class="fas fa-chart-bar" style="font-size: 5rem; opacity: 0.2;"></i>
            </div>
        </div>
    </div>

    <!-- Cards des statistiques principales -->
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="stat-card gradient-bg-1 p-4 text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-90 fw-semibold">Total des Sondages</p>
                        <h2 class="display-3 fw-bold mb-0">{{ $statistics['total_surveys'] }}</h2>
                    </div>
                    <div class="icon-wrapper">
                        <i class="fas fa-clipboard-list fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card gradient-bg-2 p-4 text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-90 fw-semibold">Satisfaction Globale</p>
                        <h2 class="display-3 fw-bold mb-0">
                            {{ $statistics['average_overall'] }}<span class="fs-3">/5</span>
                        </h2>
                        <div class="d-flex mt-2">
                            @for($i = 1; $i <= 5; $i++)
                                <i class="fas fa-star {{ $i <= round($statistics['average_overall']) ? 'text-warning' : 'opacity-50' }} me-1"></i>
                            @endfor
                        </div>
                    </div>
                    <div class="icon-wrapper">
                        <i class="fas fa-smile-beam fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card gradient-bg-3 p-4 text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-1 opacity-90 fw-semibold">Taux de Recommandation</p>
                        <h2 class="display-3 fw-bold mb-0">
                            {{ $statistics['recommendation_rate'] }}<span class="fs-3">%</span>
                        </h2>
                    </div>
                    <div class="icon-wrapper">
                        <i class="fas fa-thumbs-up fa-3x"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Évaluations détaillées -->
    <div class="card-modern p-5 mb-5">
        <h2 class="fs-3 fw-bold mb-4" style="color: #C2185B;">
            <i class="fas fa-chart-bar me-2"></i>Évaluations Détaillées
        </h2>
        
        <div class="row g-4">
            <!-- Qualité du contenu -->
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold" style="color: #374151;">
                        <i class="fas fa-book-open me-2" style="color: #C2185B;"></i>Qualité du Contenu
                    </span>
                    <span class="fs-4 fw-bold" style="color: #C2185B;">{{ $statistics['average_content_quality'] }}/5</span>
                </div>
                <div class="progress-modern">
                    <div class="progress-bar-1 h-100 transition-all" 
                         style="width: {{ ($statistics['average_content_quality'] / 5) * 100 }}%; transition: width 0.5s ease;"></div>
                </div>
            </div>

            <!-- Évaluation formateur -->
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold" style="color: #374151;">
                        <i class="fas fa-chalkboard-teacher me-2" style="color: #D32F2F;"></i>Évaluation du Formateur
                    </span>
                    <span class="fs-4 fw-bold" style="color: #D32F2F;">{{ $statistics['average_instructor_rating'] }}/5</span>
                </div>
                <div class="progress-modern">
                    <div class="progress-bar-2 h-100 transition-all" 
                         style="width: {{ ($statistics['average_instructor_rating'] / 5) * 100 }}%; transition: width 0.5s ease;"></div>
                </div>
            </div>

            <!-- Organisation -->
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold" style="color: #374151;">
                        <i class="fas fa-tasks me-2" style="color: #ef4444;"></i>Organisation
                    </span>
                    <span class="fs-4 fw-bold" style="color: #ef4444;">{{ $statistics['average_organization'] }}/5</span>
                </div>
                <div class="progress-modern">
                    <div class="progress-bar-3 h-100 transition-all" 
                         style="width: {{ ($statistics['average_organization'] / 5) * 100 }}%; transition: width 0.5s ease;"></div>
                </div>
            </div>

            <!-- Support -->
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <span class="fw-semibold" style="color: #374151;">
                        <i class="fas fa-life-ring me-2" style="color: #C2185B;"></i>Support et Accompagnement
                    </span>
                    <span class="fs-4 fw-bold" style="color: #C2185B;">{{ $statistics['average_support'] }}/5</span>
                </div>
                <div class="progress-modern">
                    <div class="progress-bar-4 h-100 transition-all" 
                         style="width: {{ ($statistics['average_support'] / 5) * 100 }}%; transition: width 0.5s ease;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des sondages -->
    <div class="card-modern p-5">
        <h2 class="fs-3 fw-bold mb-4" style="color: #C2185B;">
            <i class="fas fa-comments me-2"></i>Sondages Récents
        </h2>

        @if($surveys->count() > 0)
            <div class="row g-4">
                @foreach($surveys as $survey)
                    <div class="col-12">
                        <div class="survey-card border p-4">
                            <div class="d-flex justify-content-between align-items-start mb-4">
                                <div>
                                    <h4 class="fw-bold mb-1" style="color: #1f2937;">
                                        {{ $survey->formation->name ?? 'Formation' }}
                                    </h4>
                                    <p class="text-muted mb-0">
                                        <i class="fas fa-user me-1"></i>{{ $survey->user->name ?? 'Étudiant' }} 
                                        <span class="mx-2">•</span>
                                        <i class="fas fa-calendar me-1"></i>{{ $survey->submitted_at->format('d/m/Y') }}
                                    </p>
                                </div>
                                <div class="d-flex gap-2">
                                    <span class="badge-score">
                                        <i class="fas fa-star me-1"></i>{{ $survey->overall_satisfaction }}/5
                                    </span>
                                    @if($survey->would_recommend)
                                        <span class="badge-recommend">
                                            <i class="fas fa-thumbs-up me-1"></i>Recommande
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="row g-3 mb-4">
                                <div class="col-md-3">
                                    <div class="metric-box metric-box-1">
                                        <p class="text-muted small mb-1">Contenu</p>
                                        <p class="fs-4 fw-bold mb-0" style="color: #C2185B;">{{ $survey->content_quality }}/5</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-box metric-box-2">
                                        <p class="text-muted small mb-1">Formateur</p>
                                        <p class="fs-4 fw-bold mb-0" style="color: #D32F2F;">{{ $survey->instructor_rating }}/5</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-box metric-box-3">
                                        <p class="text-muted small mb-1">Organisation</p>
                                        <p class="fs-4 fw-bold mb-0" style="color: #ef4444;">{{ $survey->organization_rating }}/5</p>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="metric-box metric-box-4">
                                        <p class="text-muted small mb-1">Support</p>
                                        <p class="fs-4 fw-bold mb-0" style="color: #C2185B;">{{ $survey->support_rating }}/5</p>
                                    </div>
                                </div>
                            </div>

                            @if($survey->positive_feedback || $survey->improvement_suggestions || $survey->additional_comments)
                                <div class="border-top pt-4">
                                    @if($survey->positive_feedback)
                                        <div class="feedback-section feedback-positive">
                                            <p class="fw-bold mb-2" style="color: #10b981;">
                                                <i class="fas fa-check-circle me-2"></i>Points Positifs
                                            </p>
                                            <p class="mb-0 text-muted">{{ $survey->positive_feedback }}</p>
                                        </div>
                                    @endif

                                    @if($survey->improvement_suggestions)
                                        <div class="feedback-section feedback-improvement">
                                            <p class="fw-bold mb-2" style="color: #f59e0b;">
                                                <i class="fas fa-lightbulb me-2"></i>Suggestions d'Amélioration
                                            </p>
                                            <p class="mb-0 text-muted">{{ $survey->improvement_suggestions }}</p>
                                        </div>
                                    @endif

                                    @if($survey->additional_comments)
                                        <div class="feedback-section feedback-comment">
                                            <p class="fw-bold mb-2" style="color: #3b82f6;">
                                                <i class="fas fa-comment-dots me-2"></i>Commentaires Additionnels
                                            </p>
                                            <p class="mb-0 text-muted">{{ $survey->additional_comments }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-5">
                {{ $surveys->links() }}
            </div>
        @else
            <div class="text-center py-5">
                <div class="icon-wrapper d-inline-flex mb-4" style="background: linear-gradient(135deg, #C2185B, #D32F2F); width: 100px; height: 100px;">
                    <i class="fas fa-inbox fa-3x text-white"></i>
                </div>
                <h5 class="text-muted mb-2">Aucun sondage disponible</h5>
                <p class="text-muted">Les sondages apparaîtront ici une fois soumis par les étudiants.</p>
            </div>
        @endif
    </div>
</div>
@endsection