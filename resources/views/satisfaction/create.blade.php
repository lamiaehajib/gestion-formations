@extends('layouts.app')

@section('title', 'Évaluer la formation')

@section('content')
<div class="container">
    <div class="breadcrumb">
        <a href="{{ route('satisfaction.index') }}">Évaluations</a>
        <span>/</span>
        <span>{{ $inscription->formation->name ?? 'Formation' }}</span>
    </div>

    <div class="evaluation-wrapper">
        <div class="evaluation-header">
            <div class="formation-badge">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"></path>
                    <path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"></path>
                </svg>
            </div>
            <h1>Évaluer votre formation</h1>
            <h2>{{ $inscription->formation->name ?? 'Formation' }}</h2>
            <p class="evaluation-subtitle">
                Votre avis est précieux et nous aide à améliorer la qualité de nos formations
            </p>
        </div>

        <form action="{{ route('satisfaction.store') }}" method="POST" class="evaluation-form">
            @csrf
            <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
            <input type="hidden" name="formation_id" value="{{ $inscription->formation_id }}">

            <!-- Section Évaluations -->
            <div class="form-section">
                <h3 class="section-title">
                    <span class="section-icon">⭐</span>
                    Évaluez votre expérience
                </h3>

                <div class="rating-items">
                    <!-- Qualité du contenu -->
                    <div class="rating-item">
                        <div class="rating-label">
                            <label>Qualité du contenu</label>
                            <span class="required">*</span>
                        </div>
                        <p class="rating-help">Le contenu était-il pertinent et bien structuré ?</p>
                        <div class="stars-wrapper">
                            <div class="stars" data-rating-name="content_quality">
                                <span class="star" data-value="1">★</span>
                                <span class="star" data-value="2">★</span>
                                <span class="star" data-value="3">★</span>
                                <span class="star" data-value="4">★</span>
                                <span class="star" data-value="5">★</span>
                            </div>
                            <span class="rating-text"></span>
                        </div>
                        <input type="hidden" name="content_quality" required>
                        @error('content_quality')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Évaluation du formateur -->
                    <div class="rating-item">
                        <div class="rating-label">
                            <label>Évaluation du formateur</label>
                            <span class="required">*</span>
                        </div>
                        <p class="rating-help">Le formateur était-il compétent et pédagogue ?</p>
                        <div class="stars-wrapper">
                            <div class="stars" data-rating-name="instructor_rating">
                                <span class="star" data-value="1">★</span>
                                <span class="star" data-value="2">★</span>
                                <span class="star" data-value="3">★</span>
                                <span class="star" data-value="4">★</span>
                                <span class="star" data-value="5">★</span>
                            </div>
                            <span class="rating-text"></span>
                        </div>
                        <input type="hidden" name="instructor_rating" required>
                        @error('instructor_rating')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Organisation -->
                    <div class="rating-item">
                        <div class="rating-label">
                            <label>Organisation</label>
                            <span class="required">*</span>
                        </div>
                        <p class="rating-help">La formation était-elle bien organisée ?</p>
                        <div class="stars-wrapper">
                            <div class="stars" data-rating-name="organization_rating">
                                <span class="star" data-value="1">★</span>
                                <span class="star" data-value="2">★</span>
                                <span class="star" data-value="3">★</span>
                                <span class="star" data-value="4">★</span>
                                <span class="star" data-value="5">★</span>
                            </div>
                            <span class="rating-text"></span>
                        </div>
                        <input type="hidden" name="organization_rating" required>
                        @error('organization_rating')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Support et assistance -->
                    <div class="rating-item">
                        <div class="rating-label">
                            <label>Support et assistance</label>
                            <span class="required">*</span>
                        </div>
                        <p class="rating-help">Avez-vous reçu l'aide nécessaire en cas de besoin ?</p>
                        <div class="stars-wrapper">
                            <div class="stars" data-rating-name="support_rating">
                                <span class="star" data-value="1">★</span>
                                <span class="star" data-value="2">★</span>
                                <span class="star" data-value="3">★</span>
                                <span class="star" data-value="4">★</span>
                                <span class="star" data-value="5">★</span>
                            </div>
                            <span class="rating-text"></span>
                        </div>
                        <input type="hidden" name="support_rating" required>
                        @error('support_rating')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Satisfaction générale -->
                    <div class="rating-item highlight">
                        <div class="rating-label">
                            <label>Satisfaction générale</label>
                            <span class="required">*</span>
                        </div>
                        <p class="rating-help">Globalement, êtes-vous satisfait de cette formation ?</p>
                        <div class="stars-wrapper">
                            <div class="stars large" data-rating-name="overall_satisfaction">
                                <span class="star" data-value="1">★</span>
                                <span class="star" data-value="2">★</span>
                                <span class="star" data-value="3">★</span>
                                <span class="star" data-value="4">★</span>
                                <span class="star" data-value="5">★</span>
                            </div>
                            <span class="rating-text"></span>
                        </div>
                        <input type="hidden" name="overall_satisfaction" required>
                        @error('overall_satisfaction')
                            <span class="error-message">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section Feedback -->
            <div class="form-section">
                <h3 class="section-title">
                    <span class="section-icon">💭</span>
                    Partagez votre expérience
                </h3>

                <div class="form-group">
                    <label for="positive_feedback">Ce qui vous a le plus plu</label>
                    <textarea 
                        name="positive_feedback" 
                        id="positive_feedback" 
                        rows="4"
                        placeholder="Qu'avez-vous particulièrement apprécié dans cette formation ?"
                        maxlength="1000"
                    >{{ old('positive_feedback') }}</textarea>
                    <span class="char-count">0 / 1000</span>
                    @error('positive_feedback')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="improvement_suggestions">Suggestions d'amélioration</label>
                    <textarea 
                        name="improvement_suggestions" 
                        id="improvement_suggestions" 
                        rows="4"
                        placeholder="Comment pouvons-nous améliorer cette formation ?"
                        maxlength="1000"
                    >{{ old('improvement_suggestions') }}</textarea>
                    <span class="char-count">0 / 1000</span>
                    @error('improvement_suggestions')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="additional_comments">Commentaires additionnels</label>
                    <textarea 
                        name="additional_comments" 
                        id="additional_comments" 
                        rows="3"
                        placeholder="Autres remarques ou suggestions..."
                        maxlength="1000"
                    >{{ old('additional_comments') }}</textarea>
                    <span class="char-count">0 / 1000</span>
                    @error('additional_comments')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Section Recommandation -->
            <div class="form-section recommendation">
                <h3 class="section-title">
                    <span class="section-icon">👍</span>
                    Recommandation
                </h3>

                <div class="recommendation-box">
                    <p class="recommendation-question">
                        Recommanderiez-vous cette formation à d'autres étudiants ?
                        <span class="required">*</span>
                    </p>
                    <div class="radio-buttons">
                        <label class="radio-card">
                            <input type="radio" name="would_recommend" value="1" required>
                            <div class="radio-content">
                                <span class="radio-icon">👍</span>
                                <span class="radio-label">Oui, je recommande</span>
                            </div>
                        </label>
                        <label class="radio-card">
                            <input type="radio" name="would_recommend" value="0" required>
                            <div class="radio-content">
                                <span class="radio-icon">👎</span>
                                <span class="radio-label">Non, je ne recommande pas</span>
                            </div>
                        </label>
                    </div>
                    @error('would_recommend')
                        <span class="error-message">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="form-actions">
                <a href="{{ route('satisfaction.index') }}" class="btn-cancel">
                    Annuler
                </a>
                <button type="submit" class="btn-submit">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <polyline points="20 6 9 17 4 12"></polyline>
                    </svg>
                    Soumettre l'évaluation
                </button>
            </div>
        </form>
    </div>
</div>

<style>
.container {
    max-width: 800px;
    margin: 0 auto;
    padding: 40px 20px;
}

.breadcrumb {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 24px;
    font-size: 14px;
    color: #6b7280;
}

.breadcrumb a {
    color: #3b82f6;
    text-decoration: none;
}

.breadcrumb a:hover {
    text-decoration: underline;
}

.evaluation-wrapper {
    background: white;
    border-radius: 16px;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
    overflow: hidden;
}

.evaluation-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 48px 32px;
    text-align: center;
}

.formation-badge {
    width: 64px;
    height: 64px;
    background: rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto 16px;
}

.evaluation-header h1 {
    margin: 0 0 8px 0;
    font-size: 28px;
    font-weight: 600;
}

.evaluation-header h2 {
    margin: 0 0 12px 0;
    font-size: 20px;
    font-weight: 500;
    opacity: 0.95;
}

.evaluation-subtitle {
    margin: 0;
    font-size: 14px;
    opacity: 0.9;
}

.evaluation-form {
    padding: 32px;
}

.form-section {
    margin-bottom: 40px;
    padding-bottom: 32px;
    border-bottom: 2px solid #f3f4f6;
}

.form-section:last-of-type {
    border-bottom: none;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 12px;
    margin: 0 0 24px 0;
    font-size: 20px;
    color: #1f2937;
}

.section-icon {
    font-size: 24px;
}

.rating-items {
    display: flex;
    flex-direction: column;
    gap: 32px;
}

.rating-item {
    padding: 20px;
    background: #f9fafb;
    border-radius: 12px;
    transition: all 0.2s;
}

.rating-item:hover {
    background: #f3f4f6;
}

.rating-item.highlight {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 2px solid #f59e0b;
}

.rating-label {
    display: flex;
    align-items: center;
    gap: 6px;
    margin-bottom: 6px;
}

.rating-label label {
    font-weight: 600;
    color: #374151;
    font-size: 16px;
}

.required {
    color: #ef4444;
}

.rating-help {
    margin: 0 0 12px 0;
    font-size: 14px;
    color: #6b7280;
}

.stars-wrapper {
    display: flex;
    align-items: center;
    gap: 16px;
}

.stars {
    display: flex;
    gap: 6px;
    cursor: pointer;
}

.stars.large .star {
    font-size: 40px;
}

.star {
    font-size: 36px;
    color: #d1d5db;
    transition: all 0.2s;
    user-select: none;
}

.star:hover,
.star.active {
    color: #fbbf24;
    transform: scale(1.1);
}

.rating-text {
    font-size: 14px;
    color: #6b7280;
    font-weight: 500;
    min-width: 100px;
}

.form-group {
    margin-bottom: 24px;
}

.form-group label {
    display: block;
    margin-bottom: 8px;
    font-weight: 500;
    color: #374151;
}

.form-group textarea {
    width: 100%;
    padding: 14px;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    font-family: inherit;
    font-size: 14px;
    line-height: 1.6;
    resize: vertical;
    transition: all 0.2s;
}

.form-group textarea:focus {
    outline: none;
    border-color: #3b82f6;
    box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
}

.char-count {
    display: block;
    margin-top: 6px;
    font-size: 12px;
    color: #9ca3af;
    text-align: right;
}

.error-message {
    display: block;
    margin-top: 6px;
    font-size: 13px;
    color: #ef4444;
}

.recommendation-box {
    background: #f9fafb;
    padding: 24px;
    border-radius: 12px;
}

.recommendation-question {
    margin: 0 0 16px 0;
    font-size: 16px;
    font-weight: 500;
    color: #374151;
}

.radio-buttons {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 16px;
}

.radio-card {
    position: relative;
    cursor: pointer;
}

.radio-card input[type="radio"] {
    position: absolute;
    opacity: 0;
}

.radio-content {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    padding: 20px;
    background: white;
    border: 2px solid #e5e7eb;
    border-radius: 10px;
    transition: all 0.2s;
}

.radio-card input[type="radio"]:checked + .radio-content {
    border-color: #3b82f6;
    background: #eff6ff;
}

.radio-icon {
    font-size: 32px;
}

.radio-label {
    font-weight: 500;
    color: #374151;
    text-align: center;
}

.form-actions {
    display: flex;
    gap: 16px;
    justify-content: flex-end;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 2px solid #f3f4f6;
}

.btn-cancel,
.btn-submit {
    padding: 14px 28px;
    border-radius: 10px;
    font-weight: 500;
    font-size: 15px;
    cursor: pointer;
    transition: all 0.2s;
    border: none;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-cancel {
    background: white;
    color: #374151;
    border: 2px solid #d1d5db;
}

.btn-cancel:hover {
    background: #f9fafb;
    border-color: #9ca3af;
}

.btn-submit {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 10px 20px rgba(102, 126, 234, 0.3);
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des étoiles avec texte descriptif
    const ratingTexts = {
        1: 'Très insatisfait',
        2: 'Insatisfait',
        3: 'Neutre',
        4: 'Satisfait',
        5: 'Très satisfait'
    };

    const starGroups = document.querySelectorAll('.stars');
    
    starGroups.forEach(group => {
        const stars = group.querySelectorAll('.star');
        const ratingName = group.getAttribute('data-rating-name');
        const input = document.querySelector(`input[name="${ratingName}"]`);
        const ratingTextEl = group.parentElement.querySelector('.rating-text');
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                input.value = value;
                
                if (ratingTextEl) {
                    ratingTextEl.textContent = ratingTexts[value];
                }
                
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
            
            star.addEventListener('mouseenter', function() {
                const value = this.getAttribute('data-value');
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.style.color = '#fbbf24';
                    } else {
                        s.style.color = '#d1d5db';
                    }
                });
            });
        });
        
        group.addEventListener('mouseleave', function() {
            const currentValue = input.value;
            stars.forEach(s => {
                if (currentValue && s.getAttribute('data-value') <= currentValue) {
                    s.style.color = '#fbbf24';
                } else {
                    s.style.color = '#d1d5db';
                }
            });
        });
    });

    // Compteur de caractères
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        const charCount = textarea.parentElement.querySelector('.char-count');
        
        textarea.addEventListener('input', function() {
            if (charCount) {
                charCount.textContent = `${this.value.length} / ${this.maxLength}`;
            }
        });
    });
});
</script>
@endsection