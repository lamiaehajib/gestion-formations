@extends('layouts.app')

@section('title', 'Détails de la Réclamation #' . $reclamation->id)

@push('styles')
<style>
    /* Variables de Couleurs - Basées sur VOS COULEURS DEMANDÉES */
    :root {
        --primary-red: #D32F2F; /* Rouge principal */
        --secondary-pink: #C2185B; /* Rose accent */
        --accent-red: #ef4444; /* Rouge vibrant / danger */
        --light-red-bg: rgba(211, 47, 47, 0.08); /* Fond rouge très clair */
        --light-pink-bg: rgba(194, 24, 91, 0.08); /* Fond rose très clair */

        --light-bg: #f8f9fa; /* Fond général clair */
        --card-bg: #ffffff; /* Fond des cartes */
        --text-dark: #343a40; /* Texte foncé */
        --text-muted: #6c757d; /* Texte clair */
        --border-light: #e9ecef; /* Bordures claires */

        --status-open-bg: #fff3cd; --status-open-color: #856404; /* Jaune standard */
        --status-in-progress-bg: #d1ecf1; --status-in-progress-color: #0c5460; /* Cyan standard */
        --status-resolved-bg: #d4edda; --status-resolved-color: #155724; /* Vert standard */
        --status-closed-bg: #e2e3e5; --status-closed-color: #495057; /* Gris standard */

        --category-paiement-bg: #ffe0b2; --category-paiement-color: #e65100; /* Orange */
        --category-contenu-bg: #b3e5fc; --category-contenu-color: #0277bd; /* Bleu clair */
        --category-technique-bg: #d7ccc8; --category-technique-color: #5d4037; /* Marron */
        --category-pedagogique-bg: #c8e6c9; --category-pedagogique-color: #388e3c; /* Vert */
        --category-administrative-bg: #bbdefb; --category-administrative-color: #1976d2; /* Bleu */
        --category-autre-bg: #e1bee7; --category-autre-color: #8e24aa; /* Violet */

        --priority-basse-bg: #e8f5e9; --priority-basse-color: #3c763d; /* Vert */
        --priority-moyenne-bg: #fffde7; --priority-moyenne-color: #856404; /* Jaune */
        --priority-haute-bg: #fbe9e7; --priority-haute-color: #e64a19; /* Orange */
        --priority-urgente-bg: #ffebee; --priority-urgente-color: var(--accent-red); /* Votre rouge accent */
    }

    /* Styles Généraux pour le corps de la page */
    body {
        background-color: var(--light-bg);
        font-family: 'Segoe UI', Arial, sans-serif; /* Police moderne */
    }

    /* Section d'en-tête (Hero Section) */
    .hero-section {
        background: linear-gradient(45deg, var(--primary-red), var(--secondary-pink)); /* Dégradé de vos rouges */
        color: white;
        padding: 60px 0;
        margin-bottom: 40px;
        text-align: center;
        position: relative;
        overflow: hidden;
        border-bottom-left-radius: 60px; /* Bords arrondis */
        border-bottom-right-radius: 60px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2); /* Ombre douce */
    }

    .hero-section::before { /* Effet de fond subtil */
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(255,255,255,0.1);
        z-index: 0;
        opacity: 0.2;
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .hero-title {
        font-size: 3.5rem;
        font-weight: 700;
        margin-bottom: 10px;
        animation: fadeInDown 0.8s ease-out; /* Animation d'apparition */
    }

    .hero-subtitle {
        font-size: 1.25rem;
        font-weight: 300;
        animation: fadeInUp 0.8s ease-out 0.2s; /* Animation décalée */
    }

    /* Conteneur principal de la réclamation */
    .reclamation-container {
        background: var(--card-bg);
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
        padding: 40px;
        margin-bottom: 30px;
        animation: fadeIn 1s ease-out; /* Animation d'apparition */
        border: 1px solid var(--border-light); /* Ajout d'une bordure subtile */
    }

    /* Section des informations */
    .info-section h3 {
        color: var(--primary-red); /* Titres en rouge principal */
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 25px;
        border-bottom: 2px solid var(--border-light);
        padding-bottom: 15px;
        display: flex;
        align-items: center;
    }

    .info-section h3 i {
        margin-right: 15px;
        font-size: 2.2rem;
        color: var(--secondary-pink); /* Icones de titres en rose accent */
    }

    .info-item {
        margin-bottom: 20px;
    }

    .info-label {
        font-weight: 600;
        color: var(--text-dark);
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: block;
        margin-bottom: 8px;
    }

    .info-value {
        font-size: 1.1rem;
        color: var(--text-muted);
        line-height: 1.6;
        display: flex;
        align-items: center;
    }

    .info-value i {
        margin-right: 10px;
        color: var(--primary-red); /* Icones de valeurs en rouge principal */
        font-size: 1.2rem;
    }
    .info-value.no-icon {
        padding-left: 0; /* Pas d'indentation si pas d'icône */
    }

    /* Badges de statut, catégorie, priorité */
    .badge-styled {
        display: inline-flex;
        align-items: center;
        padding: 8px 15px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-right: 10px;
        margin-bottom: 5px;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }
    .badge-styled i { margin-right: 8px; }

    /* Couleurs spécifiques pour les badges */
    .badge-status-ouverte { background-color: var(--status-open-bg); color: var(--status-open-color); }
    .badge-status-en_traitement { background-color: var(--status-in-progress-bg); color: var(--status-in-progress-color); }
    .badge-status-resolue { background-color: var(--status-resolved-bg); color: var(--status-resolved-color); }
    .badge-status-fermee { background-color: var(--status-closed-bg); color: var(--status-closed-color); }

    .badge-category-paiement { background-color: var(--category-paiement-bg); color: var(--category-paiement-color); }
    .badge-category-contenu { background-color: var(--category-contenu-bg); color: var(--category-contenu-color); }
    .badge-category-technique { background-color: var(--category-technique-bg); color: var(--category-technique-color); }
    .badge-category-pedagogique { background-color: var(--category-pedagogique-bg); color: var(--category-pedagogique-color); }
    .badge-category-administrative { background-color: var(--category-administrative-bg); color: var(--category-administrative-color); }
    .badge-category-autre { background-color: var(--category-autre-bg); color: var(--category-autre-color); }

    .badge-priority-basse { background-color: var(--priority-basse-bg); color: var(--priority-basse-color); }
    .badge-priority-moyenne { background-color: var(--priority-moyenne-bg); color: var(--priority-moyenne-color); }
    .badge-priority-haute { background-color: var(--priority-haute-bg); color: var(--priority-haute-color); }
    .badge-priority-urgente {
        background-color: var(--priority-urgente-bg);
        color: var(--priority-urgente-color); /* Votre rouge accent */
        animation: pulseEffect 1.5s infinite; /* Animation pour l'urgence */
    }

    @keyframes pulseEffect {
        0% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0.7); } /* Utilise votre accent-red pour l'ombre */
        70% { box-shadow: 0 0 0 15px rgba(239, 68, 68, 0); }
        100% { box-shadow: 0 0 0 0 rgba(239, 68, 68, 0); }
    }

    /* Section Réponse et Évaluation */
    .response-evaluation-section {
        background: var(--light-red-bg); /* Fond rouge très clair */
        border-radius: 15px;
        padding: 30px;
        margin-top: 30px;
        border: 1px solid var(--primary-red); /* Bordure rouge principal */
        box-shadow: inset 0 2px 10px rgba(0, 0, 0, 0.03);
    }

    .response-evaluation-section h4 {
        color: var(--primary-red); /* Titres en rouge principal */
        font-size: 1.5rem;
        font-weight: 600;
        margin-bottom: 20px;
        display: flex;
        align-items: center;
        border-bottom: 1px solid var(--border-light);
        padding-bottom: 10px;
    }

    .response-evaluation-section h4 i {
        margin-right: 10px;
        font-size: 1.8rem;
        color: var(--secondary-pink); /* Icones de titres en rose accent */
    }

    .response-content {
        background: var(--light-pink-bg); /* Fond rose très clair */
        border-left: 5px solid var(--secondary-pink); /* Bordure rose accent */
        border-radius: 10px;
        padding: 20px;
        margin-bottom: 20px;
        font-style: italic;
        color: var(--text-dark);
        line-height: 1.8;
    }

    .response-meta {
        text-align: right;
        font-size: 0.9rem;
        color: var(--text-muted);
        margin-top: 10px;
    }

    /* Étoiles d'évaluation */
    .rating-stars {
        display: flex;
        gap: 5px;
        margin-top: 15px;
    }

    .rating-stars .star {
        font-size: 2rem;
        color: #ccc; /* Couleur par défaut */
        cursor: pointer;
        transition: color 0.2s ease;
    }

    .rating-stars .star.filled {
        color: var(--warning-color); /* Jaune doré pour les étoiles remplies */
    }

    /* Boutons d'action */
    .action-buttons-group {
        display: flex;
        flex-wrap: wrap; /* Permet le retour à la ligne */
        gap: 15px; /* Espacement entre les boutons */
        justify-content: flex-end; /* Alignement à droite */
        padding-top: 25px;
        border-top: 1px solid var(--border-light);
        margin-top: 30px;
    }

    .btn-custom {
        padding: 12px 25px;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 10px rgba(0,0,0,0.1);
    }
    .btn-custom i { margin-right: 8px; }

    .btn-back-to-list { background: var(--secondary-color); color: white; }
    .btn-back-to-list:hover { background: #5a6268; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); }

    .btn-edit-reclamation { background: var(--primary-red); color: white; } /* Utilise votre rouge principal */
    .btn-edit-reclamation:hover { background: var(--secondary-pink); transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); }

    .btn-assign-reclamation { background: var(--secondary-pink); color: white; } /* Utilise votre rose accent */
    .btn-assign-reclamation:hover { background: var(--primary-red); transform: translateY(-2px); }

    .btn-respond-reclamation { background: var(--accent-red); color: white; } /* Utilise votre rouge accent */
    .btn-respond-reclamation:hover { background: var(--primary-red); transform: translateY(-2px); }

    .btn-delete-reclamation { background: var(--danger-color); color: white; } /* Reste le rouge danger de Bootstrap pour la suppression */
    .btn-delete-reclamation:hover { background: #bd2130; transform: translateY(-2px); box-shadow: 0 6px 15px rgba(0,0,0,0.15); }

    .btn-rate-reclamation { background: var(--secondary-pink); color: white; } /* Rose accent pour l'évaluation */
    .btn-rate-reclamation:hover { background: var(--primary-red); transform: translateY(-2px); }

    /* Section des Statistiques */
    .statistics-section {
        background: var(--card-bg);
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
        padding: 40px;
        margin-top: 40px;
        text-align: center;
        overflow: hidden;
        border: 1px solid var(--border-light);
    }

    .statistics-section h3 {
        color: var(--primary-red); /* Titre stats en rouge principal */
        font-size: 2rem;
        font-weight: 700;
        margin-bottom: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .statistics-section h3 i {
        margin-right: 15px;
        font-size: 2.5rem;
        color: var(--secondary-pink); /* Icône de titre stats en rose accent */
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); /* Adaptatif */
        gap: 25px; /* Espacement entre les éléments */
    }

    .stat-card {
        background: var(--light-bg);
        border-radius: 15px;
        padding: 20px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        border: 1px solid var(--border-light);
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .stat-card .icon-wrapper {
        font-size: 3rem;
        margin-bottom: 10px;
        line-height: 1;
    }

    /* Couleurs des icônes de statistiques - Basées sur vos rouges */
    .stat-card.total-stats .icon-wrapper { color: var(--primary-red); }
    .stat-card.open-stats .icon-wrapper { color: var(--secondary-pink); }
    .stat-card.progress-stats .icon-wrapper { color: var(--accent-red); }
    .stat-card.resolved-stats .icon-wrapper { color: #28a745; } /* Vert de succès standard */
    .stat-card.closed-stats .icon-wrapper { color: var(--text-muted); }
    .stat-card.rating-stats .icon-wrapper { color: #ffc107; } /* Jaune standard */

    .stat-card .value {
        font-size: 2.5rem;
        font-weight: 700;
        color: var(--text-dark);
        line-height: 1;
        margin-bottom: 5px;
    }

    .stat-card .label {
        font-size: 0.9rem;
        color: var(--text-muted);
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Animations personnalisées */
    @keyframes fadeInDown {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeInUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    /* Responsive Design */
    @media (max-width: 992px) {
        .hero-title { font-size: 2.8rem; }
        .hero-subtitle { font-size: 1.1rem; }
        .reclamation-container, .statistics-section { padding: 30px; }
        .info-section h3 { font-size: 1.6rem; }
        .info-section h3 i { font-size: 2rem; }
        .info-value { font-size: 1rem; }
        .stat-card .value { font-size: 2rem; }
    }

    @media (max-width: 768px) {
        .hero-section { border-bottom-left-radius: 30px; border-bottom-right-radius: 30px; }
        .hero-title { font-size: 2.2rem; }
        .hero-subtitle { font-size: 1rem; }
        .reclamation-container, .statistics-section { padding: 20px; }
        .info-section h3 { font-size: 1.4rem; justify-content: center; text-align: center; }
        .info-section h3 i { margin-right: 0; margin-bottom: 10px; display: block; }
        .info-item { text-align: center; }
        .info-value { justify-content: center; }
        .info-value.no-icon { padding-left: 0; }
        .action-buttons-group { flex-direction: column; align-items: stretch; }
        .btn-custom { width: 100%; }
        .stats-grid { grid-template-columns: 1fr; }
        .statistics-section h3 { font-size: 1.6rem; }
    }

    /* Special styles for statistics section when it's just a message */
    .statistics-message-box {
        background-color: var(--light-red-bg); /* Use a light version of your primary color */
        color: var(--primary-red);
        border: 1px solid var(--primary-red);
        border-radius: 15px; /* Rounded corners for the message box */
        padding: 20px 25px;
        margin-top: 30px;
        text-align: center;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        font-weight: 500;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
    }
    .statistics-message-box i {
        margin-right: 12px;
        font-size: 1.4rem;
        color: var(--primary-red);
    }
</style>
@endpush

@section('content')
<div class="hero-section">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">
                <i class="fas fa-search me-3"></i>
                Détails de la Réclamation #{{ $reclamation->id }}
            </h1>
            <p class="hero-subtitle">
                Toutes les informations et le statut actuel de cette réclamation.
            </p>
        </div>
    </div>
</div>

<div class="container">
    {{-- Alerts for success/error messages --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-times-circle me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="reclamation-container">
        <section class="info-section">
            <h3><i class="fas fa-info-circle"></i> Détails Principaux</h3>
            <div class="row">
                <div class="col-md-6">
                    <div class="info-item">
                        <span class="info-label">Sujet:</span>
                        <span class="info-value"><i class="fas fa-tag"></i> {{ $reclamation->subject }}</span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Catégorie:</span>
                        <span class="info-value">
                            <span class="badge-styled badge-category-{{ $reclamation->category }}">
                                <i class="fas fa-{{
                                    $reclamation->category == 'technique' ? 'cog' : (
                                    $reclamation->category == 'paiement' ? 'credit-card' : (
                                    $reclamation->category == 'contenu' ? 'book-open' : (
                                    $reclamation->category == 'pedagogique' ? 'chalkboard-teacher' : (
                                    $reclamation->category == 'administrative' ? 'file-alt' : 'ellipsis-h'
                                    )))) }} me-1"></i>
                                {{ App\Models\Reclamation::CATEGORIES[$reclamation->category] ?? $reclamation->category }}
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Priorité:</span>
                        <span class="info-value">
                            <span class="badge-styled badge-priority-{{ $reclamation->priority }}">
                                <i class="fas fa-{{
                                    $reclamation->priority == 'urgente' ? 'exclamation-triangle' : (
                                    $reclamation->priority == 'haute' ? 'arrow-up' : (
                                    $reclamation->priority == 'moyenne' ? 'minus' : 'arrow-down'
                                    )) }} me-1"></i>
                                {{ App\Models\Reclamation::PRIORITIES[$reclamation->priority] ?? $reclamation->priority }}
                            </span>
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Statut:</span>
                        <span class="info-value">
                            <span class="badge-styled badge-status-{{ $reclamation->status }}">
                                <i class="fas fa-{{
                                    $reclamation->status == 'ouverte' ? 'folder-open' : (
                                    $reclamation->status == 'en_traitement' ? 'hourglass-half' : (
                                    $reclamation->status == 'resolue' ? 'check-circle' : 'times-circle'
                                    )) }} me-1"></i>
                                {{ App\Models\Reclamation::STATUSES[$reclamation->status] ?? $reclamation->status }}
                            </span>
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-item">
                        <span class="info-label">Demandeur:</span>
                        <span class="info-value">
                            <i class="fas fa-user-circle"></i> {{ $reclamation->user->name }}
                            @if($reclamation->user->email) ({{ $reclamation->user->email }}) @endif
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Formation concernée:</span>
                        <span class="info-value">
                            <i class="fas fa-graduation-cap"></i> {{ $reclamation->formation->title }}
                        </span>
                    </div>
                    <div class="info-item">
                        <span class="info-label">Date de création:</span>
                        <span class="info-value">
                            <i class="fas fa-calendar-alt"></i> {{ $reclamation->created_at->format('d/m/Y H:i') }}
                        </span>
                    </div>
                    @if($reclamation->assignedTo)
                    <div class="info-item">
                        <span class="info-label">Assignée à:</span>
                        <span class="info-value">
                            <i class="fas fa-user-tie"></i> {{ $reclamation->assignedTo->name }}
                        </span>
                    </div>
                    @endif
                </div>
            </div>

            <div class="info-item mt-4">
                <span class="info-label">Description détaillée:</span>
                <p class="info-value no-icon"><i class="fas fa-align-left"></i> {{ $reclamation->description }}</p>
            </div>
        </section>

        <section class="response-evaluation-section mt-4">
            @if($reclamation->response)
                <h4><i class="fas fa-reply-all"></i> Réponse à la Réclamation</h4>
                <p class="response-content">{{ $reclamation->response }}</p>
                <span class="response-date">Répondu le {{ $reclamation->response_date->format('d/m/Y H:i') }}</span>
            @else
                <h4><i class="fas fa-envelope-open-text"></i> Aucune Réponse pour l'instant</h4>
                <p class="text-muted">Cette réclamation n'a pas encore reçu de réponse de notre équipe.</p>
            @endif
        </section>

        {{-- Satisfaction Rating Section --}}
        @if($reclamation->status == 'resolue' && Auth::user()->id === $reclamation->user_id)
            <section class="rating-section mt-4">
                <h4><i class="fas fa-star-half-alt"></i> Évaluer cette réclamation</h4>
                <p class="text-muted">Votre avis est important pour nous aider à améliorer nos services.</p>
                <form action="{{ route('reclamations.rate', $reclamation->id) }}" method="POST">
                    @csrf
                    <div class="star-rating" id="satisfactionStars">
                        @for($i = 1; $i <= 5; $i++)
                            <i class="far fa-star star {{ $reclamation->satisfaction_rating >= $i ? 'filled' : '' }}" data-rating="{{ $i }}"></i>
                        @endfor
                        <input type="hidden" name="satisfaction_rating" id="satisfactionRatingInput" value="{{ old('satisfaction_rating', $reclamation->satisfaction_rating) }}">
                    </div>
                    <button type="submit" class="btn btn-action-custom btn-submit-rating">
                        <i class="fas fa-paper-plane"></i> Envoyer l'évaluation
                    </button>
                </form>
            </section>
        @elseif($reclamation->satisfaction_rating)
            <section class="rating-section mt-4">
                <h4><i class="fas fa-star"></i> Évaluation donnée:</h4>
                <div class="star-rating">
                    @for($i = 1; $i <= 5; $i++)
                        <i class="fas fa-star star {{ $reclamation->satisfaction_rating >= $i ? 'filled' : '' }}"></i>
                    @endfor
                </div>
                <p class="text-muted mt-2">Vous avez déjà évalué cette réclamation.</p>
            </section>
        @endif


        {{-- Action Buttons --}}
        <div class="action-buttons-group">
            <a href="{{ route('reclamations.index') }}" class="btn-custom btn-back-to-list">
                <i class="fas fa-arrow-left"></i> Retour à la liste
            </a>

            @can('reclamation-edit')
                @if(Auth::user()->hasRole('Etudiant') && $reclamation->user_id === Auth::user()->id && $reclamation->status === 'ouverte')
                    <a href="{{ route('reclamations.edit', $reclamation->id) }}" class="btn-custom btn-edit-reclamation">
                        <i class="fas fa-pencil-alt"></i> Modifier
                    </a>
                @elseif(!Auth::user()->hasRole('Etudiant'))
                     <a href="{{ route('reclamations.edit', $reclamation->id) }}" class="btn-custom btn-edit-reclamation">
                        <i class="fas fa-pencil-alt"></i> Modifier
                    </a>
                @endif
            @endcan

            @can('reclamation-assign')
                @if(!$reclamation->assigned_to && $reclamation->status === 'ouverte' && !Auth::user()->hasRole('Etudiant'))
                    <button type="button" class="btn-custom btn-assign-reclamation" data-bs-toggle="modal" data-bs-target="#assignModal">
                        <i class="fas fa-user-plus"></i> Assigner
                    </button>
                @endif
            @endcan

            @can('reclamation-respond')
                @if($reclamation->status !== 'resolue' && $reclamation->status !== 'fermee' && (!Auth::user()->hasRole('Etudiant') || $reclamation->assigned_to === Auth::user()->id))
                    <button type="button" class="btn-custom btn-respond-reclamation" data-bs-toggle="modal" data-bs-target="#respondModal">
                        <i class="fas fa-comments"></i> Répondre / Statut
                    </button>
                @endif
            @endcan

            @can('reclamation-delete')
                @if(Auth::user()->hasAnyRole(['Admin', 'Super Admin']))
                    <button type="button" class="btn-custom btn-delete-reclamation" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fas fa-trash-alt"></i> Supprimer
                    </button>
                @endif
            @endcan
        </div>
    </div>

    {{-- Statistics Section - Changed to a grid layout as originally intended, but keeping colors related to your theme --}}
    <div class="statistics-section" id="reclamationStats">
        <h3><i class="fas fa-chart-bar"></i> Statistiques Générales des Réclamations</h3>
        <div class="stats-grid">
            <div class="stat-card total-stats">
                <div class="icon-wrapper"><i class="fas fa-database"></i></div>
                <div class="value" id="stat-total">0</div>
                <div class="label">Total Réclamations</div>
            </div>
            <div class="stat-card open-stats">
                <div class="icon-wrapper"><i class="fas fa-inbox"></i></div>
                <div class="value" id="stat-open">0</div>
                <div class="label">Ouvertes</div>
            </div>
            <div class="stat-card progress-stats">
                <div class="icon-wrapper"><i class="fas fa-sync-alt"></i></div>
                <div class="value" id="stat-in-progress">0</div>
                <div class="label">En Traitement</div>
            </div>
            <div class="stat-card resolved-stats">
                <div class="icon-wrapper"><i class="fas fa-check-double"></i></div>
                <div class="value" id="stat-resolved">0</div>
                <div class="label">Résolues</div>
            </div>
            <div class="stat-card closed-stats">
                <div class="icon-wrapper"><i class="fas fa-archive"></i></div>
                <div class="value" id="stat-closed">0</div>
                <div class="label">Fermées</div>
            </div>
            <div class="stat-card rating-stats">
                <div class="icon-wrapper"><i class="fas fa-award"></i></div>
                <div class="value" id="stat-avg-rating">N/A</div>
                <div class="label">Moyenne Évaluation</div>
            </div>
        </div>
    </div>
</div>

{{-- Modals for Actions --}}

{{-- Assign Modal --}}
{{-- Assign Modal --}}
@can('reclamation-assign')
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(45deg, var(--primary-red), var(--secondary-pink)); color: white;">
                <h5 class="modal-title" id="assignModalLabel"><i class="fas fa-user-plus me-2"></i> Assigner la Réclamation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reclamations.assign', $reclamation->id) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assigned_to" class="form-label">Assigner à un utilisateur:</label>
                        <select class="form-control" id="assigned_to" name="assigned_to" required>
                            <option value="">Sélectionnez un utilisateur</option>
                            @foreach($assignableUsers as $user)
                                <option value="{{ $user->id }}">{{ $user->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Assigner</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Respond Modal --}}
{{-- Respond Modal --}}
@can('reclamation-respond')
<div class="modal fade" id="respondModal" tabindex="-1" aria-labelledby="respondModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(45deg, var(--primary-red), var(--secondary-pink)); color: white;">
                <h5 class="modal-title" id="respondModalLabel"><i class="fas fa-comments me-2"></i> Répondre à la Réclamation</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('reclamations.respond', $reclamation->id) }}" method="POST">
                @csrf
                @method('PATCH') {{-- <--- A DÉCLARER POUR QUE LARAVEL COMPRENNE LE PATCH --}}
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="response" class="form-label">Votre Réponse:</label>
                        <textarea class="form-control" id="response" name="response" rows="4" required placeholder="Tapez votre réponse ici..."></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Mettre à jour le Statut:</label>
                        <select class="form-control" id="status" name="status" required>
                            @foreach(App\Models\Reclamation::STATUSES as $key => $label)
                                <option value="{{ $key }}" {{ $reclamation->status == $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-primary">Envoyer la Réponse</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

{{-- Delete Modal --}}
@can('reclamation-delete')
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteModalLabel"><i class="fas fa-exclamation-triangle me-2"></i> Confirmer la Suppression</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                Êtes-vous sûr de vouloir supprimer cette réclamation **#{{ $reclamation->id }}**? Cette action est irréversible.
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <form action="{{ route('reclamations.destroy', $reclamation->id) }}" method="POST">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Supprimer Définitivement</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endcan


@push('scripts')
{{-- Assurez-vous d'avoir Bootstrap 5 JS et Font Awesome liés globalement dans votre layouts/app.blade.php --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script> --}}
{{-- Chart.js n'est plus nécessaire si on n'affiche pas de graphes --}}
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Star Rating Logic
        const satisfactionStars = document.getElementById('satisfactionStars');
        if (satisfactionStars) {
            const ratingInput = document.getElementById('satisfactionRatingInput');
            const stars = satisfactionStars.querySelectorAll('.star');

            // Set initial state based on existing rating
            const initialRating = ratingInput.value;
            stars.forEach(s => {
                if (s.dataset.rating <= initialRating) {
                    s.classList.add('fas', 'filled');
                    s.classList.remove('far');
                } else {
                    s.classList.remove('fas', 'filled');
                    s.classList.add('far');
                }
            });

            stars.forEach(star => {
                star.addEventListener('click', function() {
                    const rating = this.dataset.rating;
                    ratingInput.value = rating;
                    stars.forEach(s => {
                        if (s.dataset.rating <= rating) {
                            s.classList.add('fas', 'filled');
                            s.classList.remove('far');
                        } else {
                            s.classList.remove('fas', 'filled');
                            s.classList.add('far');
                        }
                    });
                });

                star.addEventListener('mouseover', function() {
                    // Only highlight if no rating is definitively set (or if user is changing existing rating)
                    if (!ratingInput.value || (ratingInput.value && this.dataset.rating !== ratingInput.value)) {
                        const hoverRating = this.dataset.rating;
                        stars.forEach(s => {
                            if (s.dataset.rating <= hoverRating) {
                                s.classList.add('fas'); // Use solid on hover
                                s.classList.remove('far');
                            } else {
                                s.classList.remove('fas');
                                s.classList.add('far');
                            }
                        });
                    }
                });

                star.addEventListener('mouseout', function() {
                    // Reset to current rating or empty if no rating is set
                    const currentRating = ratingInput.value;
                    stars.forEach(s => {
                        if (s.dataset.rating <= currentRating) {
                            s.classList.add('fas', 'filled');
                            s.classList.remove('far');
                        } else {
                            s.classList.remove('fas', 'filled');
                            s.classList.add('far');
                        }
                    });
                });
            });
        }

        // Fetch and display statistics (now in a grid, as was the original plan for "better")
        function fetchStatistics() {
            fetch('{{ route("reclamations.statistics") }}')
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    document.getElementById('stat-total').textContent = data.total;
                    document.getElementById('stat-open').textContent = data.ouverte;
                    document.getElementById('stat-in-progress').textContent = data.en_traitement;
                    document.getElementById('stat-resolved').textContent = data.resolue;
                    document.getElementById('stat-closed').textContent = data.fermee;
                    document.getElementById('stat-avg-rating').textContent = data.average_rating ? data.average_rating.toFixed(1) + '/5' : 'N/A';
                    
                    // Remove any previous message box if present
                    const messageBox = document.querySelector('.statistics-message-box');
                    if (messageBox) messageBox.remove();

                })
                .catch(error => {
                    console.error('Error fetching statistics:', error);
                    // If error, display a custom message box instead of the grid
                    const statsSection = document.getElementById('reclamationStats');
                    statsSection.innerHTML = `
                        <div class="statistics-message-box">
                            <i class="fas fa-exclamation-triangle"></i>
                            <span>Impossible de charger les statistiques pour le moment.</span>
                        </div>
                    `;
                });
        }

        fetchStatistics();
    });
</script>
@endpush
@endsection