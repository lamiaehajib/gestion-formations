@extends('layouts.app')

@section('title', 'Évaluer la formation')

@section('content')
<style>
    .gradient-primary {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
    }
    
    .gradient-light {
        background: linear-gradient(135deg, rgba(194, 24, 91, 0.05), rgba(239, 68, 68, 0.05));
    }
    
    .breadcrumb-custom {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 1.5rem;
        font-size: 0.875rem;
        color: #6b7280;
        padding: 0.75rem 1rem;
        background: white;
        border-radius: 10px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    .breadcrumb-custom a {
        color: #C2185B;
        text-decoration: none;
        font-weight: 500;
        transition: color 0.2s;
    }
    
    .breadcrumb-custom a:hover {
        color: #D32F2F;
        text-decoration: underline;
    }
    
    .evaluation-wrapper-custom {
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 25px rgba(194, 24, 91, 0.15);
        overflow: hidden;
    }
    
    .evaluation-header-custom {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
        color: white;
        padding: 3rem 2rem;
        text-align: center;
        position: relative;
        overflow: hidden;
    }
    
    .evaluation-header-custom::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 4s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); opacity: 0.5; }
        50% { transform: scale(1.1); opacity: 0.3; }
    }
    
    .formation-badge-custom {
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.25);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        position: relative;
        z-index: 1;
    }
    
    .evaluation-header-custom h1 {
        margin: 0 0 0.5rem 0;
        font-size: 2rem;
        font-weight: 700;
        position: relative;
        z-index: 1;
    }
    
    .evaluation-header-custom h2 {
        margin: 0 0 1rem 0;
        font-size: 1.5rem;
        font-weight: 500;
        opacity: 0.95;
        position: relative;
        z-index: 1;
    }
    
    .evaluation-subtitle-custom {
        margin: 0;
        font-size: 1rem;
        opacity: 0.9;
        position: relative;
        z-index: 1;
    }
    
    .evaluation-form-custom {
        padding: 2.5rem;
    }
    
    .form-section-custom {
        margin-bottom: 3rem;
        padding-bottom: 2rem;
        border-bottom: 3px solid #f3f4f6;
    }
    
    .form-section-custom:last-of-type {
        border-bottom: none;
    }
    
    .section-title-custom {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin: 0 0 2rem 0;
        font-size: 1.5rem;
        color: #1f2937;
        font-weight: 700;
    }
    
    .section-icon-custom {
        font-size: 2rem;
        filter: drop-shadow(0 2px 4px rgba(194, 24, 91, 0.2));
    }
    
    .rating-items-custom {
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }
    
    .rating-item-custom {
        padding: 1.5rem;
        background: linear-gradient(135deg, #f9fafb, #f3f4f6);
        border-radius: 15px;
        transition: all 0.3s ease;
        border: 2px solid transparent;
    }
    
    .rating-item-custom:hover {
        background: linear-gradient(135deg, #f3f4f6, #e5e7eb);
        transform: translateX(5px);
        border-color: rgba(194, 24, 91, 0.2);
    }
    
    .rating-item-custom.highlight {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border: 3px solid #f59e0b;
        box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
    }
    
    .rating-item-custom.error {
        border: 2px solid #ef4444 !important;
        background: linear-gradient(135deg, #fee2e2, #fecaca);
    }
    
    .rating-label-custom {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-bottom: 0.5rem;
    }
    
    .rating-label-custom label {
        font-weight: 600;
        color: #374151;
        font-size: 1.1rem;
    }
    
    .required-custom {
        color: #ef4444;
        font-size: 1.2rem;
    }
    
    .rating-help-custom {
        margin: 0 0 1rem 0;
        font-size: 0.875rem;
        color: #6b7280;
        font-style: italic;
    }
    
    .stars-wrapper-custom {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }
    
    .stars-custom {
        display: flex;
        gap: 0.5rem;
        cursor: pointer;
    }
    
    .stars-custom.large .star-custom {
        font-size: 3rem;
    }
    
    .star-custom {
        font-size: 2.5rem;
        color: #d1d5db;
        transition: all 0.2s ease;
        user-select: none;
        cursor: pointer;
    }
    
    .star-custom:hover,
    .star-custom.active {
        color: #fbbf24;
        transform: scale(1.15) rotate(-10deg);
        filter: drop-shadow(0 0 8px rgba(251, 191, 36, 0.5));
    }
    
    .rating-text-custom {
        font-size: 1rem;
        color: #C2185B;
        font-weight: 600;
        min-width: 140px;
        padding: 0.5rem 1rem;
        background: white;
        border-radius: 8px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        display: none;
    }
    
    .form-group-custom {
        margin-bottom: 1.5rem;
    }
    
    .form-group-custom label {
        display: block;
        margin-bottom: 0.75rem;
        font-weight: 600;
        color: #374151;
        font-size: 1.05rem;
    }
    
    .form-group-custom textarea {
        width: 100%;
        padding: 1rem;
        border: 2px solid #e5e7eb;
        border-radius: 12px;
        font-family: inherit;
        font-size: 0.95rem;
        line-height: 1.6;
        resize: vertical;
        transition: all 0.3s ease;
        background: #f9fafb;
    }
    
    .form-group-custom textarea:focus {
        outline: none;
        border-color: #C2185B;
        box-shadow: 0 0 0 4px rgba(194, 24, 91, 0.1);
        background: white;
    }
    
    .char-count-custom {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.75rem;
        color: #9ca3af;
        text-align: right;
        font-weight: 500;
    }
    
    .error-message-custom {
        display: block;
        margin-top: 0.5rem;
        font-size: 0.875rem;
        color: #ef4444;
        font-weight: 500;
    }
    
    .recommendation-box-custom {
        background: linear-gradient(135deg, #f9fafb, #f3f4f6);
        padding: 2rem;
        border-radius: 15px;
        border: 2px solid rgba(194, 24, 91, 0.1);
    }
    
    .recommendation-question-custom {
        margin: 0 0 1.5rem 0;
        font-size: 1.15rem;
        font-weight: 600;
        color: #374151;
    }
    
    .radio-buttons-custom {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
    }
    
    .radio-card-custom {
        position: relative;
        cursor: pointer;
    }
    
    .radio-card-custom input[type="radio"] {
        position: absolute;
        opacity: 0;
    }
    
    .radio-content-custom {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        padding: 1.5rem;
        background: white;
        border: 3px solid #e5e7eb;
        border-radius: 12px;
        transition: all 0.3s ease;
    }
    
    .radio-card-custom input[type="radio"]:checked + .radio-content-custom {
        border-color: #C2185B;
        background: linear-gradient(135deg, rgba(194, 24, 91, 0.05), rgba(239, 68, 68, 0.05));
        box-shadow: 0 4px 15px rgba(194, 24, 91, 0.2);
        transform: scale(1.05);
    }
    
    .radio-icon-custom {
        font-size: 3rem;
    }
    
    .radio-label-custom {
        font-weight: 600;
        color: #374151;
        text-align: center;
        font-size: 1rem;
    }
    
    .form-actions-custom {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 2rem;
        padding-top: 2rem;
        border-top: 3px solid #f3f4f6;
    }
    
    .btn-cancel-custom,
    .btn-submit-custom {
        padding: 1rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1rem;
        cursor: pointer;
        transition: all 0.3s ease;
        border: none;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }
    
    .btn-cancel-custom {
        background: white;
        color: #374151;
        border: 2px solid #d1d5db;
    }
    
    .btn-cancel-custom:hover {
        background: #f9fafb;
        border-color: #9ca3af;
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }
    
    .btn-submit-custom {
        background: linear-gradient(135deg, #C2185B, #D32F2F);
        color: white;
        box-shadow: 0 4px 15px rgba(194, 24, 91, 0.3);
    }
    
    .btn-submit-custom:hover {
        background: linear-gradient(135deg, #D32F2F, #ef4444);
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(194, 24, 91, 0.4);
    }
    
    @media (max-width: 768px) {
        .evaluation-form-custom {
            padding: 1.5rem;
        }
        
        .form-actions-custom {
            flex-direction: column;
        }
        
        .btn-cancel-custom,
        .btn-submit-custom {
            width: 100%;
            justify-content: center;
        }
        
        .star-custom {
            font-size: 2rem;
        }
        
        .stars-custom.large .star-custom {
            font-size: 2.5rem;
        }
    }
</style>

<div class="container-fluid px-4 py-5" style="max-width: 900px;">
    <div class="breadcrumb-custom">
        <a href="{{ route('satisfaction.index') }}">
            <i class="fas fa-arrow-left me-1"></i> Évaluations
        </a>
        <span>/</span>
        <span class="fw-semibold">{{ $inscription->formation->name ?? 'Formation' }}</span>
    </div>

    <div class="evaluation-wrapper-custom">
        <div class="evaluation-header-custom">
            <div class="formation-badge-custom">
                <i class="fas fa-book-open fa-2x text-white"></i>
            </div>
            <h1>Évaluer votre formation</h1>
            <h2>{{ $inscription->formation->name ?? 'Formation' }}</h2>
            <p class="evaluation-subtitle-custom">
                Votre avis est précieux et nous aide à améliorer la qualité de nos formations
            </p>
        </div>

        <form action="{{ route('satisfaction.store') }}" method="POST" class="evaluation-form-custom" id="satisfactionForm">
            @csrf
            <input type="hidden" name="inscription_id" value="{{ $inscription->id }}">
            <input type="hidden" name="formation_id" value="{{ $inscription->formation_id }}">

            <!-- Section Évaluations -->
            <div class="form-section-custom">
                <h3 class="section-title-custom">
                    <span class="section-icon-custom">⭐</span>
                    Évaluez votre expérience
                </h3>

                <div class="rating-items-custom">
                    <!-- Qualité du contenu -->
                    <div class="rating-item-custom" data-rating="content_quality">
                        <div class="rating-label-custom">
                            <label>Qualité du contenu</label>
                            <span class="required-custom">*</span>
                        </div>
                        <p class="rating-help-custom">Le contenu était-il pertinent et bien structuré ?</p>
                        <div class="stars-wrapper-custom">
                            <div class="stars-custom" data-rating-name="content_quality">
                                <span class="star-custom" data-value="1">★</span>
                                <span class="star-custom" data-value="2">★</span>
                                <span class="star-custom" data-value="3">★</span>
                                <span class="star-custom" data-value="4">★</span>
                                <span class="star-custom" data-value="5">★</span>
                            </div>
                            <span class="rating-text-custom" data-rating-text="content_quality"></span>
                        </div>
                        <input type="hidden" name="content_quality" id="content_quality" required>
                        @error('content_quality')
                            <span class="error-message-custom">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Évaluation du formateur -->
                    <div class="rating-item-custom" data-rating="instructor_rating">
                        <div class="rating-label-custom">
                            <label>Évaluation du formateur</label>
                            <span class="required-custom">*</span>
                        </div>
                        <p class="rating-help-custom">Le formateur était-il compétent et pédagogue ?</p>
                        <div class="stars-wrapper-custom">
                            <div class="stars-custom" data-rating-name="instructor_rating">
                                <span class="star-custom" data-value="1">★</span>
                                <span class="star-custom" data-value="2">★</span>
                                <span class="star-custom" data-value="3">★</span>
                                <span class="star-custom" data-value="4">★</span>
                                <span class="star-custom" data-value="5">★</span>
                            </div>
                            <span class="rating-text-custom" data-rating-text="instructor_rating"></span>
                        </div>
                        <input type="hidden" name="instructor_rating" id="instructor_rating" required>
                        @error('instructor_rating')
                            <span class="error-message-custom">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Organisation -->
                    <div class="rating-item-custom" data-rating="organization_rating">
                        <div class="rating-label-custom">
                            <label>Organisation</label>
                            <span class="required-custom">*</span>
                        </div>
                        <p class="rating-help-custom">La formation était-elle bien organisée ?</p>
                        <div class="stars-wrapper-custom">
                            <div class="stars-custom" data-rating-name="organization_rating">
                                <span class="star-custom" data-value="1">★</span>
                                <span class="star-custom" data-value="2">★</span>
                                <span class="star-custom" data-value="3">★</span>
                                <span class="star-custom" data-value="4">★</span>
                                <span class="star-custom" data-value="5">★</span>
                            </div>
                            <span class="rating-text-custom" data-rating-text="organization_rating"></span>
                        </div>
                        <input type="hidden" name="organization_rating" id="organization_rating" required>
                        @error('organization_rating')
                            <span class="error-message-custom">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Support et assistance -->
                    <div class="rating-item-custom" data-rating="support_rating">
                        <div class="rating-label-custom">
                            <label>Support et assistance</label>
                            <span class="required-custom">*</span>
                        </div>
                        <p class="rating-help-custom">Avez-vous reçu l'aide nécessaire en cas de besoin ?</p>
                        <div class="stars-wrapper-custom">
                            <div class="stars-custom" data-rating-name="support_rating">
                                <span class="star-custom" data-value="1">★</span>
                                <span class="star-custom" data-value="2">★</span>
                                <span class="star-custom" data-value="3">★</span>
                                <span class="star-custom" data-value="4">★</span>
                                <span class="star-custom" data-value="5">★</span>
                            </div>
                            <span class="rating-text-custom" data-rating-text="support_rating"></span>
                        </div>
                        <input type="hidden" name="support_rating" id="support_rating" required>
                        @error('support_rating')
                            <span class="error-message-custom">{{ $message }}</span>
                        @enderror
                    </div>

                    <!-- Satisfaction générale -->
                    <div class="rating-item-custom highlight" data-rating="overall_satisfaction">
                        <div class="rating-label-custom">
                            <label>Satisfaction générale</label>
                            <span class="required-custom">*</span>
                        </div>
                        <p class="rating-help-custom">Globalement, êtes-vous satisfait de cette formation ?</p>
                        <div class="stars-wrapper-custom">
                            <div class="stars-custom large" data-rating-name="overall_satisfaction">
                                <span class="star-custom" data-value="1">★</span>
                                <span class="star-custom" data-value="2">★</span>
                                <span class="star-custom" data-value="3">★</span>
                                <span class="star-custom" data-value="4">★</span>
                                <span class="star-custom" data-value="5">★</span>
                            </div>
                            <span class="rating-text-custom" data-rating-text="overall_satisfaction"></span>
                        </div>
                        <input type="hidden" name="overall_satisfaction" id="overall_satisfaction" required>
                        @error('overall_satisfaction')
                            <span class="error-message-custom">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Section Feedback -->
            <div class="form-section-custom">
                <h3 class="section-title-custom">
                    <span class="section-icon-custom">💭</span>
                    Partagez votre expérience
                </h3>

                <div class="form-group-custom">
                    <label for="positive_feedback">
                        <i class="fas fa-thumbs-up me-2" style="color: #10b981;"></i>Ce qui vous a le plus plu
                    </label>
                    <textarea 
                        name="positive_feedback" 
                        id="positive_feedback" 
                        rows="4"
                        placeholder="Qu'avez-vous particulièrement apprécié dans cette formation ?"
                        maxlength="1000"
                    >{{ old('positive_feedback') }}</textarea>
                    <span class="char-count-custom" data-counter="positive_feedback">0 / 1000</span>
                    @error('positive_feedback')
                        <span class="error-message-custom">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group-custom">
                    <label for="improvement_suggestions">
                        <i class="fas fa-lightbulb me-2" style="color: #f59e0b;"></i>Suggestions d'amélioration
                    </label>
                    <textarea 
                        name="improvement_suggestions" 
                        id="improvement_suggestions" 
                        rows="4"
                        placeholder="Comment pouvons-nous améliorer cette formation ?"
                        maxlength="1000"
                    >{{ old('improvement_suggestions') }}</textarea>
                    <span class="char-count-custom" data-counter="improvement_suggestions">0 / 1000</span>
                    @error('improvement_suggestions')
                        <span class="error-message-custom">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group-custom">
                    <label for="additional_comments">
                        <i class="fas fa-comment-dots me-2" style="color: #3b82f6;"></i>Commentaires additionnels
                    </label>
                    <textarea 
                        name="additional_comments" 
                        id="additional_comments" 
                        rows="3"
                        placeholder="Autres remarques ou suggestions..."
                        maxlength="1000"
                    >{{ old('additional_comments') }}</textarea>
                    <span class="char-count-custom" data-counter="additional_comments">0 / 1000</span>
                    @error('additional_comments')
                        <span class="error-message-custom">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Section Recommandation -->
            <div class="form-section-custom">
                <h3 class="section-title-custom">
                    <span class="section-icon-custom">👍</span>
                    Recommandation
                </h3>

                <div class="recommendation-box-custom">
                    <p class="recommendation-question-custom">
                        Recommanderiez-vous cette formation à d'autres étudiants ?
                        <span class="required-custom">*</span>
                    </p>
                    <div class="radio-buttons-custom">
                        <label class="radio-card-custom">
                            <input type="radio" name="would_recommend" value="1" {{ old('would_recommend') == '1' ? 'checked' : '' }} required>
                            <div class="radio-content-custom">
                                <span class="radio-icon-custom">👍</span>
                                <span class="radio-label-custom">Oui, je recommande</span>
                            </div>
                        </label>
                        <label class="radio-card-custom">
                            <input type="radio" name="would_recommend" value="0" {{ old('would_recommend') == '0' ? 'checked' : '' }} required>
                            <div class="radio-content-custom">
                                <span class="radio-icon-custom">👎</span>
                                <span class="radio-label-custom">Non, je ne recommande pas</span>
                            </div>
                        </label>
                    </div>
                    @error('would_recommend')
                        <span class="error-message-custom">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="form-actions-custom">
                <a href="{{ route('satisfaction.index') }}" class="btn-cancel-custom">
                    <i class="fas fa-times"></i>
                    Annuler
                </a>
                <button type="submit" class="btn-submit-custom">
                    <i class="fas fa-paper-plane"></i>
                    Soumettre l'évaluation
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    console.log('Script de notation initialisé');
    
    // Textes descriptifs pour les notes
    const ratingTexts = {
        1: 'Très insatisfait',
        2: 'Insatisfait',
        3: 'Neutre',
        4: 'Satisfait',
        5: 'Très satisfait'
    };

    // Initialiser tous les groupes d'étoiles
    const starGroups = document.querySelectorAll('.stars-custom');
    console.log('Nombre de groupes d\'étoiles trouvés:', starGroups.length);
    
    starGroups.forEach(group => {
        const ratingName = group.getAttribute('data-rating-name');
        const stars = group.querySelectorAll('.star-custom');
        const input = document.getElementById(ratingName);
        const ratingTextEl = document.querySelector(`[data-rating-text="${ratingName}"]`);
        
        console.log(`Initialisation du groupe: ${ratingName}`, {
            stars: stars.length,
            inputFound: !!input,
            textElementFound: !!ratingTextEl
        });
        
        if (!input) {
            console.error(`Input non trouvé pour: ${ratingName}`);
            return;
        }
        
        // Gestion du clic sur les étoiles
        stars.forEach(star => {
            star.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                
                const value = this.getAttribute('data-value');
                console.log(`Étoile cliquée - ${ratingName}: ${value}`);
                
                // Définir la valeur dans l'input caché
                input.value = value;
                
                // Afficher le texte descriptif
                if (ratingTextEl) {
                    ratingTextEl.textContent = ratingTexts[value];
                    ratingTextEl.style.display = 'block';
                }
                
                // Supprimer la bordure d'erreur si présente
                const ratingItem = input.closest('.rating-item-custom');
                if (ratingItem) {
                    ratingItem.classList.remove('error');
                }
                
                // Mettre à jour l'affichage des étoiles
                updateStars(stars, value);
            });
            
            // Effet au survol
            star.addEventListener('mouseenter', function() {
                const value = this.getAttribute('data-value');
                updateStars(stars, value);
            });
        });
        
        // Restaurer l'affichage quand la souris quitte le groupe
        group.addEventListener('mouseleave', function() {
            const currentValue = input.value;
            if (currentValue) {
                updateStars(stars, currentValue);
            } else {
                resetStars(stars);
            }
        });
    });
    
    // Fonction pour mettre à jour l'affichage des étoiles
    function updateStars(stars, value) {
        stars.forEach(star => {
            const starValue = parseInt(star.getAttribute('data-value'));
            if (starValue <= parseInt(value)) {
                star.classList.add('active');
            } else {
                star.classList.remove('active');
            }
        });
    }
    
    // Fonction pour réinitialiser les étoiles
    function resetStars(stars) {
        stars.forEach(star => {
            star.classList.remove('active');
        });
    }
    
    // Compteur de caractères pour les textareas
    const textareas = document.querySelectorAll('textarea[maxlength]');
    textareas.forEach(textarea => {
        const counterId = textarea.id;
        const counter = document.querySelector(`[data-counter="${counterId}"]`);
        
        if (counter) {
            // Initialiser le compteur avec la valeur existante
            counter.textContent = `${textarea.value.length} / ${textarea.maxLength}`;
            
            // Mettre à jour le compteur lors de la saisie
            textarea.addEventListener('input', function() {
                counter.textContent = `${this.value.length} / ${this.maxLength}`;
            });
        }
    });
    
    // Validation du formulaire avant soumission
    const form = document.getElementById('satisfactionForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            console.log('Soumission du formulaire...');
            
            const requiredRatings = [
                'content_quality',
                'instructor_rating',
                'organization_rating',
                'support_rating',
                'overall_satisfaction'
            ];
            
            let hasErrors = false;
            const missingRatings = [];
            
            // Vérifier toutes les notes obligatoires
            requiredRatings.forEach(ratingName => {
                const input = document.getElementById(ratingName);
                const value = input ? input.value : '';
                
                console.log(`Vérification ${ratingName}:`, value);
                
                if (!value) {
                    hasErrors = true;
                    missingRatings.push(ratingName);
                    
                    // Ajouter la classe d'erreur
                    const ratingItem = document.querySelector(`[data-rating="${ratingName}"]`);
                    if (ratingItem) {
                        ratingItem.classList.add('error');
                        
                        // Faire défiler jusqu'au premier champ manquant
                        if (missingRatings.length === 1) {
                            ratingItem.scrollIntoView({ 
                                behavior: 'smooth', 
                                block: 'center' 
                            });
                        }
                    }
                }
            });
            
            // Vérifier la recommandation
            const recommendInput = document.querySelector('input[name="would_recommend"]:checked');
            if (!recommendInput) {
                hasErrors = true;
                console.error('Recommandation non sélectionnée');
            }
            
            if (hasErrors) {
                e.preventDefault();
                console.error('Erreurs de validation:', missingRatings);
                
                let errorMsg = 'Veuillez compléter tous les champs obligatoires:\n';
                if (missingRatings.length > 0) {
                    errorMsg += '\n• Évaluations manquantes:\n';
                    missingRatings.forEach(rating => {
                        const labels = {
                            'content_quality': 'Qualité du contenu',
                            'instructor_rating': 'Évaluation du formateur',
                            'organization_rating': 'Organisation',
                            'support_rating': 'Support et assistance',
                            'overall_satisfaction': 'Satisfaction générale'
                        };
                        errorMsg += `  - ${labels[rating]}\n`;
                    });
                }
                if (!recommendInput) {
                    errorMsg += '\n• Veuillez indiquer si vous recommandez cette formation';
                }
                
                alert(errorMsg);
                return false;
            }
            
            console.log('Validation réussie, soumission du formulaire...');
            return true;
        });
    }
    
    // Restaurer les valeurs old() si elles existent (après erreur de validation)
    @if(old('content_quality'))
        const contentQualityInput = document.getElementById('content_quality');
        if (contentQualityInput) {
            contentQualityInput.value = '{{ old('content_quality') }}';
            const stars = document.querySelector('[data-rating-name="content_quality"]').querySelectorAll('.star-custom');
            const textEl = document.querySelector('[data-rating-text="content_quality"]');
            updateStars(stars, '{{ old('content_quality') }}');
            if (textEl) {
                textEl.textContent = ratingTexts['{{ old('content_quality') }}'];
                textEl.style.display = 'block';
            }
        }
    @endif
    
    @if(old('instructor_rating'))
        const instructorRatingInput = document.getElementById('instructor_rating');
        if (instructorRatingInput) {
            instructorRatingInput.value = '{{ old('instructor_rating') }}';
            const stars = document.querySelector('[data-rating-name="instructor_rating"]').querySelectorAll('.star-custom');
            const textEl = document.querySelector('[data-rating-text="instructor_rating"]');
            updateStars(stars, '{{ old('instructor_rating') }}');
            if (textEl) {
                textEl.textContent = ratingTexts['{{ old('instructor_rating') }}'];
                textEl.style.display = 'block';
            }
        }
    @endif
    
    @if(old('organization_rating'))
        const organizationRatingInput = document.getElementById('organization_rating');
        if (organizationRatingInput) {
            organizationRatingInput.value = '{{ old('organization_rating') }}';
            const stars = document.querySelector('[data-rating-name="organization_rating"]').querySelectorAll('.star-custom');
            const textEl = document.querySelector('[data-rating-text="organization_rating"]');
            updateStars(stars, '{{ old('organization_rating') }}');
            if (textEl) {
                textEl.textContent = ratingTexts['{{ old('organization_rating') }}'];
                textEl.style.display = 'block';
            }
        }
    @endif
    
    @if(old('support_rating'))
        const supportRatingInput = document.getElementById('support_rating');
        if (supportRatingInput) {
            supportRatingInput.value = '{{ old('support_rating') }}';
            const stars = document.querySelector('[data-rating-name="support_rating"]').querySelectorAll('.star-custom');
            const textEl = document.querySelector('[data-rating-text="support_rating"]');
            updateStars(stars, '{{ old('support_rating') }}');
            if (textEl) {
                textEl.textContent = ratingTexts['{{ old('support_rating') }}'];
                textEl.style.display = 'block';
            }
        }
    @endif
    
    @if(old('overall_satisfaction'))
        const overallSatisfactionInput = document.getElementById('overall_satisfaction');
        if (overallSatisfactionInput) {
            overallSatisfactionInput.value = '{{ old('overall_satisfaction') }}';
            const stars = document.querySelector('[data-rating-name="overall_satisfaction"]').querySelectorAll('.star-custom');
            const textEl = document.querySelector('[data-rating-text="overall_satisfaction"]');
            updateStars(stars, '{{ old('overall_satisfaction') }}');
            if (textEl) {
                textEl.textContent = ratingTexts['{{ old('overall_satisfaction') }}'];
                textEl.style.display = 'block';
            }
        }
    @endif
    
    console.log('Initialisation terminée');
});
</script>
@endsection