<!-- Popup Modal de Satisfaction -->
<div id="satisfactionModal" class="satisfaction-modal" style="display: none;">
    <div class="modal-overlay"></div>
    <div class="modal-container">
        <div class="modal-header">
            <h2>📋 Évaluez votre formation</h2>
            <button type="button" class="close-btn" onclick="closeSatisfactionModal()">
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <div class="modal-body">
            <div class="formation-info">
                <h3 id="formationName">Formation</h3>
                <p class="formation-subtitle">Votre avis nous aide à améliorer nos formations</p>
            </div>

            <form id="satisfactionForm" method="POST" action="{{ route('satisfaction.store') }}">
                @csrf
                <input type="hidden" name="inscription_id" id="inscriptionId">
                <input type="hidden" name="formation_id" id="formationId">

                <!-- Évaluations par étoiles -->
                <div class="rating-section">
                    <div class="rating-group">
                        <label>Qualité du contenu</label>
                        <div class="stars" data-rating-name="content_quality">
                            <span class="star" data-value="1">★</span>
                            <span class="star" data-value="2">★</span>
                            <span class="star" data-value="3">★</span>
                            <span class="star" data-value="4">★</span>
                            <span class="star" data-value="5">★</span>
                        </div>
                        <input type="hidden" name="content_quality" required>
                    </div>

                    <div class="rating-group">
                        <label>Évaluation du formateur</label>
                        <div class="stars" data-rating-name="instructor_rating">
                            <span class="star" data-value="1">★</span>
                            <span class="star" data-value="2">★</span>
                            <span class="star" data-value="3">★</span>
                            <span class="star" data-value="4">★</span>
                            <span class="star" data-value="5">★</span>
                        </div>
                        <input type="hidden" name="instructor_rating" required>
                    </div>

                    <div class="rating-group">
                        <label>Organisation</label>
                        <div class="stars" data-rating-name="organization_rating">
                            <span class="star" data-value="1">★</span>
                            <span class="star" data-value="2">★</span>
                            <span class="star" data-value="3">★</span>
                            <span class="star" data-value="4">★</span>
                            <span class="star" data-value="5">★</span>
                        </div>
                        <input type="hidden" name="organization_rating" required>
                    </div>

                    <div class="rating-group">
                        <label>Support et assistance</label>
                        <div class="stars" data-rating-name="support_rating">
                            <span class="star" data-value="1">★</span>
                            <span class="star" data-value="2">★</span>
                            <span class="star" data-value="3">★</span>
                            <span class="star" data-value="4">★</span>
                            <span class="star" data-value="5">★</span>
                        </div>
                        <input type="hidden" name="support_rating" required>
                    </div>

                    <div class="rating-group">
                        <label>Satisfaction générale</label>
                        <div class="stars" data-rating-name="overall_satisfaction">
                            <span class="star" data-value="1">★</span>
                            <span class="star" data-value="2">★</span>
                            <span class="star" data-value="3">★</span>
                            <span class="star" data-value="4">★</span>
                            <span class="star" data-value="5">★</span>
                        </div>
                        <input type="hidden" name="overall_satisfaction" required>
                    </div>
                </div>

                <!-- Questions ouvertes -->
                <div class="feedback-section">
                    <div class="form-group">
                        <label for="positive_feedback">Ce qui vous a le plus plu</label>
                        <textarea name="positive_feedback" id="positive_feedback" rows="3" 
                                  placeholder="Qu'avez-vous apprécié dans cette formation ?"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="improvement_suggestions">Suggestions d'amélioration</label>
                        <textarea name="improvement_suggestions" id="improvement_suggestions" rows="3"
                                  placeholder="Comment pouvons-nous améliorer cette formation ?"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="additional_comments">Commentaires additionnels</label>
                        <textarea name="additional_comments" id="additional_comments" rows="2"
                                  placeholder="Autres remarques..."></textarea>
                    </div>
                </div>

                <!-- Recommandation -->
                <div class="recommendation-section">
                    <label>Recommanderiez-vous cette formation ?</label>
                    <div class="radio-group">
                        <label class="radio-label">
                            <input type="radio" name="would_recommend" value="1" required>
                            <span>Oui</span>
                        </label>
                        <label class="radio-label">
                            <input type="radio" name="would_recommend" value="0" required>
                            <span>Non</span>
                        </label>
                    </div>
                </div>

                <!-- Boutons -->
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="remindLater()">
                        Me le rappeler plus tard
                    </button>
                    <button type="submit" class="btn-primary">
                        Soumettre l'évaluation
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

.satisfaction-modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 9999;
    display: flex;
    align-items: center;
    justify-content: center;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.modal-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(194, 24, 91, 0.85) 0%, rgba(211, 47, 47, 0.85) 50%, rgba(239, 68, 68, 0.85) 100%);
    backdrop-filter: blur(12px);
    animation: overlayFadeIn 0.4s ease-out;
}

@keyframes overlayFadeIn {
    from {
        opacity: 0;
    }
    to {
        opacity: 1;
    }
}

.modal-container {
    position: relative;
    background: linear-gradient(to bottom, #ffffff 0%, #fafafa 100%);
    border-radius: 24px;
    max-width: 700px;
    width: 92%;
    max-height: 92vh;
    overflow-y: auto;
    box-shadow: 
        0 0 0 1px rgba(194, 24, 91, 0.1),
        0 25px 80px rgba(194, 24, 91, 0.35),
        0 10px 40px rgba(211, 47, 47, 0.25);
    animation: slideUp 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
    border: 3px solid transparent;
    background-clip: padding-box;
}

.modal-container::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 4px;
    background: linear-gradient(90deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
    border-radius: 24px 24px 0 0;
}

@keyframes slideUp {
    from {
        opacity: 0;
        transform: translateY(50px) scale(0.9);
    }
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

/* Custom Scrollbar */
.modal-container::-webkit-scrollbar {
    width: 10px;
}

.modal-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 0 24px 24px 0;
}

.modal-container::-webkit-scrollbar-thumb {
    background: linear-gradient(180deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
    border-radius: 10px;
    border: 2px solid #f1f1f1;
}

.modal-container::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(180deg, #a91650 0%, #b71c1c 50%, #dc2626 100%);
}

.modal-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 28px 32px;
    background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
    border-radius: 24px 24px 0 0;
    position: sticky;
    top: 0;
    z-index: 10;
}

.modal-header h2 {
    margin: 0;
    font-size: 26px;
    color: white;
    font-weight: 700;
    text-shadow: 0 2px 8px rgba(0, 0, 0, 0.2);
    letter-spacing: 0.3px;
}

.close-btn {
    background: rgba(255, 255, 255, 0.2);
    border: 2px solid rgba(255, 255, 255, 0.3);
    cursor: pointer;
    padding: 10px;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    color: white;
    backdrop-filter: blur(10px);
}

.close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg) scale(1.1);
    border-color: rgba(255, 255, 255, 0.5);
}

.close-btn svg {
    display: block;
}

.modal-body {
    padding: 32px;
}

.formation-info {
    margin-bottom: 32px;
    padding: 24px;
    background: linear-gradient(135deg, rgba(194, 24, 91, 0.05) 0%, rgba(239, 68, 68, 0.05) 100%);
    border-radius: 16px;
    border-left: 5px solid #C2185B;
    box-shadow: 0 4px 12px rgba(194, 24, 91, 0.08);
}

.formation-info h3 {
    margin: 0 0 10px 0;
    font-size: 22px;
    color: #C2185B;
    font-weight: 700;
}

.formation-subtitle {
    margin: 0;
    color: #666;
    font-size: 15px;
    line-height: 1.5;
}

.rating-section {
    margin-bottom: 32px;
}

.rating-group {
    margin-bottom: 24px;
    padding: 20px;
    background: white;
    border-radius: 14px;
    border: 2px solid #f0f0f0;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.04);
}

.rating-group:hover {
    border-color: rgba(194, 24, 91, 0.3);
    box-shadow: 0 4px 16px rgba(194, 24, 91, 0.12);
    transform: translateY(-2px);
}

.rating-group label {
    display: block;
    margin-bottom: 12px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 15px;
}

.stars {
    display: flex;
    gap: 8px;
    cursor: pointer;
}

.star {
    font-size: 38px;
    color: #e0e0e0;
    transition: all 0.3s cubic-bezier(0.34, 1.56, 0.64, 1);
    user-select: none;
    text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
}

.star:hover {
    transform: scale(1.25) rotate(15deg);
}

.star:active {
    transform: scale(1.1);
}

.star.active {
    background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    filter: drop-shadow(0 2px 8px rgba(194, 24, 91, 0.4));
}

.feedback-section {
    margin-bottom: 32px;
}

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;
    margin-bottom: 10px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 15px;
}

.form-group textarea {
    width: 100%;
    padding: 16px;
    border: 2px solid #e0e0e0;
    border-radius: 12px;
    font-family: inherit;
    font-size: 14px;
    resize: vertical;
    transition: all 0.3s ease;
    background: white;
    line-height: 1.6;
}

.form-group textarea:focus {
    outline: none;
    border-color: #C2185B;
    box-shadow: 0 0 0 4px rgba(194, 24, 91, 0.1), 0 4px 12px rgba(194, 24, 91, 0.15);
    background: #fff;
}

.form-group textarea::placeholder {
    color: #999;
}

.recommendation-section {
    margin-bottom: 32px;
    padding: 24px;
    background: linear-gradient(135deg, rgba(211, 47, 47, 0.05) 0%, rgba(239, 68, 68, 0.05) 100%);
    border-radius: 16px;
    border: 2px solid rgba(211, 47, 47, 0.15);
    box-shadow: 0 4px 12px rgba(211, 47, 47, 0.08);
}

.recommendation-section > label {
    display: block;
    margin-bottom: 16px;
    font-weight: 600;
    color: #2c3e50;
    font-size: 16px;
}

.radio-group {
    display: flex;
    gap: 20px;
}

.radio-label {
    display: flex;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    padding: 14px 24px;
    background: white;
    border-radius: 12px;
    border: 2px solid #e0e0e0;
    transition: all 0.3s ease;
    font-weight: 500;
    flex: 1;
    justify-content: center;
}

.radio-label:hover {
    border-color: #D32F2F;
    background: rgba(211, 47, 47, 0.05);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(211, 47, 47, 0.15);
}

.radio-label input[type="radio"] {
    width: 22px;
    height: 22px;
    cursor: pointer;
    accent-color: #D32F2F;
}

.radio-label input[type="radio"]:checked + span {
    color: #D32F2F;
    font-weight: 600;
}

.modal-footer {
    display: flex;
    gap: 16px;
    justify-content: flex-end;
    padding-top: 24px;
    border-top: 2px solid #f0f0f0;
    flex-wrap: wrap;
}

.btn-primary,
.btn-secondary {
    padding: 16px 32px;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    border: none;
    font-size: 15px;
    letter-spacing: 0.3px;
    position: relative;
    overflow: hidden;
}

.btn-primary {
    background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
    color: white;
    box-shadow: 0 6px 20px rgba(194, 24, 91, 0.35);
}

.btn-primary::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
    transition: left 0.6s;
}

.btn-primary:hover::before {
    left: 100%;
}

.btn-primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 24px rgba(194, 24, 91, 0.45);
}

.btn-primary:active {
    transform: translateY(0);
}

.btn-secondary {
    background: white;
    color: #2c3e50;
    border: 2px solid #e0e0e0;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
}

.btn-secondary:hover {
    background: #f8f9fa;
    border-color: #C2185B;
    color: #C2185B;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(194, 24, 91, 0.15);
}

/* Responsive Design */
@media (max-width: 640px) {
    .modal-container {
        width: 96%;
        max-height: 95vh;
        border-radius: 20px;
    }

    .modal-header {
        padding: 20px 24px;
    }

    .modal-header h2 {
        font-size: 22px;
    }

    .modal-body {
        padding: 24px;
    }

    .star {
        font-size: 32px;
    }

    .modal-footer {
        flex-direction: column;
    }

    .btn-primary,
    .btn-secondary {
        width: 100%;
    }

    .radio-group {
        flex-direction: column;
    }
}
</style>

<script>
// Gestion des étoiles
document.addEventListener('DOMContentLoaded', function() {
    const starGroups = document.querySelectorAll('.stars');
    
    starGroups.forEach(group => {
        const stars = group.querySelectorAll('.star');
        const ratingName = group.getAttribute('data-rating-name');
        const input = document.querySelector(`input[name="${ratingName}"]`);
        
        stars.forEach(star => {
            star.addEventListener('click', function() {
                const value = this.getAttribute('data-value');
                input.value = value;
                
                // Mise à jour visuelle
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.classList.add('active');
                    } else {
                        s.classList.remove('active');
                    }
                });
            });
            
            // Effet hover
            star.addEventListener('mouseenter', function() {
                const value = this.getAttribute('data-value');
                stars.forEach(s => {
                    if (s.getAttribute('data-value') <= value) {
                        s.style.color = '#D32F2F';
                    } else {
                        s.style.color = '#e0e0e0';
                    }
                });
            });
        });
        
        // Restaurer la couleur au départ de la souris
        group.addEventListener('mouseleave', function() {
            const currentValue = input.value;
            stars.forEach(s => {
                if (currentValue && s.getAttribute('data-value') <= currentValue) {
                    s.classList.add('active');
                    s.style.color = '';
                } else {
                    s.classList.remove('active');
                    s.style.color = '#e0e0e0';
                }
            });
        });
    });
});

// Soumission du formulaire
document.getElementById('satisfactionForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    
    fetch(this.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            closeSatisfactionModal();
            alert(data.message);
            // Masquer cette formation de la liste
            checkForPendingSurveys();
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        alert('Une erreur est survenue. Veuillez réessayer.');
    });
});

function closeSatisfactionModal() {
    const modal = document.getElementById('satisfactionModal');
    modal.style.animation = 'fadeOut 0.3s ease-out';
    setTimeout(() => {
        modal.style.display = 'none';
        modal.style.animation = '';
    }, 300);
}

function remindLater() {
    // Enregistrer dans localStorage pour rappeler plus tard
    localStorage.setItem('satisfactionReminder', Date.now());
    closeSatisfactionModal();
}

// Vérifier s'il y a des sondages en attente
function checkForPendingSurveys() {
    // Ne pas afficher si l'utilisateur a demandé un rappel il y a moins de 24h
    const reminder = localStorage.getItem('satisfactionReminder');
    if (reminder && (Date.now() - reminder) < 86400000) { // 24h en ms
        return;
    }
    
    fetch('{{ route("satisfaction.pending") }}')
        .then(response => response.json())
        .then(data => {
            if (data.success && data.inscriptions.length > 0) {
                // Afficher le popup pour la première formation
                const inscription = data.inscriptions[0];
                showSatisfactionModal(inscription);
            }
        })
        .catch(error => console.error('Erreur:', error));
}

function showSatisfactionModal(inscription) {
    document.getElementById('formationName').textContent = inscription.formation_name;
    document.getElementById('inscriptionId').value = inscription.id;
    document.getElementById('formationId').value = inscription.formation_id;
    document.getElementById('satisfactionForm').reset();
    
    // Réinitialiser les étoiles
    document.querySelectorAll('.star').forEach(star => {
        star.classList.remove('active');
        star.style.color = '#e0e0e0';
    });
    
    // Réinitialiser les inputs cachés
    document.querySelectorAll('input[type="hidden"][name*="rating"], input[type="hidden"][name*="quality"], input[type="hidden"][name*="satisfaction"]').forEach(input => {
        input.value = '';
    });
    
    document.getElementById('satisfactionModal').style.display = 'flex';
}

// Vérifier au chargement de la page
document.addEventListener('DOMContentLoaded', function() {
    // Attendre 3 secondes avant d'afficher le popup
    setTimeout(checkForPendingSurveys, 3000);
});

// Animation de fermeture
const style = document.createElement('style');
style.textContent = `
    @keyframes fadeOut {
        from {
            opacity: 1;
        }
        to {
            opacity: 0;
        }
    }
`;
document.head.appendChild(style);
</script>