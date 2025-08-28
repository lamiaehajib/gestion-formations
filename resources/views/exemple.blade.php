@extends('layouts.app')

@section('content')

<style>
/* Styles généraux pour la section */
.section-container {
    background-color: #f3f4f6;
    padding: 80px 20px;
}
.dark .section-container {
    background-color: #1f2937;
}

/* Styles pour le titre et le sous-titre */
.header-content {
    text-align: center;
    margin-bottom: 60px;
}
.main-title {
    font-size: 3.5rem;
    font-weight: 800;
    color: #111827;
    margin-bottom: 10px;
    line-height: 1.2;
}
.dark .main-title {
    color: #fff;
}
.main-title span {
    color: #D32F2F;
}
.subtitle {
    font-size: 1.25rem;
    color: #4b5563;
    max-width: 800px;
    margin: 0 auto;
}
.dark .subtitle {
    color: #d1d5db;
}

/* Styles de la galerie d'images avec Flexbox */
.gallery-container {
    display: flex;
    flex-wrap: wrap; /* On va écraser cette propriété pour les grands écrans */
    justify-content: center;
    align-items: center;
    gap: 40px;
    margin-bottom: 60px;
}

/* Styles pour chaque carte */
.card {
    background-color: #fff;
    border-radius: 20px;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.15);
    overflow: hidden;
    transition: transform 0.4s ease, box-shadow 0.4s ease;
    position: relative;
    text-align: center;
    cursor: pointer;
    display: flex;
    flex-direction: column;
}
.dark .card {
    background-color: #374151;
    box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4);
}
.card:hover {
    transform: scale(1.04);
    box-shadow: 0 20px 45px rgba(0, 0, 0, 0.25);
}
.card-img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}
.card:hover .card-img {
    transform: scale(1.1);
}

/* Styles de l'overlay et du contenu sur l'image */
.card-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(to top, rgba(0,0,0,0.8) 0%, rgba(0,0,0,0) 60%);
    display: flex;
    flex-direction: column;
    justify-content: flex-end;
    align-items: center;
    padding: 30px;
}
.card-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #fff;
    margin-bottom: 5px;
}
.card-subtitle {
    font-size: 1rem;
    color: #e5e7eb;
    text-transform: uppercase;
    letter-spacing: 1px;
}

/* Styles pour le bouton */
.card-btn {
    display: inline-block;
    padding: 14px 35px;
    margin-top: 20px;
    border-radius: 50px;
    color: #fff;
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s ease;
}
.card-btn:hover {
    opacity: 0.9;
    transform: translateY(-2px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

/* Tailles et styles spécifiques aux cartes */
.card-center {
    width: 650px;
    height: 750px;
    z-index: 10;
}
.card-side {
    width: 550px;
    height: 550px;
}
.card-center .card-title {
    font-size: 3.2rem;
}
.card-center .card-subtitle {
    font-size: 1.6rem;
}

/* Media query pour les écrans larges (ex: 1300px et plus) */
/* C'est la solution au problème. On empêche le retour à la ligne. */
@media (min-width: 1300px) {
    .gallery-container {
        flex-wrap: nowrap; /* Empêche les cartes de passer à la ligne */
        justify-content: center;
        gap: 30px; /* Réduit l'espace entre les cartes pour qu'elles tiennent */
    }
    .card-center {
        width: 550px; /* Réduit la largeur pour s'adapter */
        height: 650px;
    }
    .card-side {
        width: 450px; /* Réduit la largeur pour s'adapter */
        height: 450px;
    }
}
 
@media (max-width: 768px) {
    .main-title {
        font-size: 2.5rem;
    }
    .subtitle {
        font-size: 1rem;
    }
    .gallery-container {
        flex-direction: column;
        align-items: stretch;
    }
    .card-center, .card-side {
        width: 100%;
        height: 450px;
    }
    .card-center .card-title {
        font-size: 2rem;
    }
    .card-center .card-subtitle {
        font-size: 1.2rem;
    }
    .card-side .card-title {
        font-size: 1.8rem;
    }
    .card-side .card-subtitle {
        font-size: 1rem;
    }
}
/* ---------------- NOUVEAU STYLE DU MODAL AMÉLIORÉ ---------------- */
.modal {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(0, 0, 0, 0.9), rgba(30, 30, 60, 0.95));
    backdrop-filter: blur(10px);
    display: none;
    justify-content: center;
    align-items: center;
    z-index: 9999;
    cursor: pointer;
    opacity: 0;
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
}

.modal.active {
    display: flex;
    opacity: 1;
    animation: modalFadeIn 0.5s cubic-bezier(0.4, 0, 0.2, 1);
}

@keyframes modalFadeIn {
    from {
        opacity: 0;
        transform: scale(0.8);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

.modal-container {
    position: relative;
    max-width: 90vw;
    max-height: 90vh;
    display: flex;
    flex-direction: column;
    align-items: center;
    cursor: default;
    animation: modalSlideIn 0.6s cubic-bezier(0.34, 1.56, 0.64, 1);
}

@keyframes modalSlideIn {
    from {
        transform: scale(0.5) translateY(50px);
        opacity: 0;
    }
    to {
        transform: scale(1) translateY(0);
        opacity: 1;
    }
}

.modal-content {
    width: 100%;
    height: auto;
    max-height: 80vh;
    object-fit: contain;
    border-radius: 20px;
    box-shadow: 
        0 25px 50px rgba(0, 0, 0, 0.7),
        0 0 0 1px rgba(255, 255, 255, 0.1),
        inset 0 1px 0 rgba(255, 255, 255, 0.2);
    transition: transform 0.3s ease;
    background: #fff;
}

.modal-content:hover {
    transform: scale(1.02);
}

.modal-header {
    position: absolute;
    top: -60px;
    left: 50%;
    transform: translateX(-50%);
    text-align: center;
    color: #fff;
    z-index: 10;
}

.modal-title {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 8px;
    text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
}

.modal-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 2px;
}

.close-btn {
    position: absolute;
    top: -15px;
    right: -15px;
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #ff6b6b, #ee5a24);
    color: #fff;
    border: none;
    border-radius: 50%;
    font-size: 20px;
    font-weight: bold;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 
        0 10px 25px rgba(255, 107, 107, 0.4),
        0 0 0 3px rgba(255, 255, 255, 0.1);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    z-index: 11;
}

.close-btn:hover {
    transform: scale(1.1) rotate(90deg);
    background: linear-gradient(135deg, #ff5252, #d63031);
    box-shadow: 
        0 15px 35px rgba(255, 107, 107, 0.6),
        0 0 0 3px rgba(255, 255, 255, 0.2);
}

.close-btn::before {
    content: '×';
}

.modal-navigation {
    position: absolute;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(10px);
    color: #fff;
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 50%;
    width: 60px;
    height: 60px;
    display: flex;
    align-items: center;
    justify-content: center;
    cursor: pointer;
    font-size: 24px;
    font-weight: bold;
    transition: all 0.3s ease;
    user-select: none;
}

.modal-navigation:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-50%) scale(1.1);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.3);
}

.modal-prev {
    left: -80px;
}

.modal-next {
    right: -80px;
}

.modal-footer {
    position: absolute;
    bottom: -50px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    gap: 15px;
    align-items: center;
}

.download-btn {
    background: linear-gradient(135deg, #667eea, #764ba2);
    color: #fff;
    border: none;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
}

.download-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(102, 126, 234, 0.4);
    background: linear-gradient(135deg, #5a67d8, #6b46c1);
}

.zoom-controls {
    display: flex;
    gap: 10px;
    background: rgba(0, 0, 0, 0.5);
    backdrop-filter: blur(10px);
    padding: 10px;
    border-radius: 20px;
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.zoom-btn {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    border: none;
    background: rgba(255, 255, 255, 0.1);
    color: #fff;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    font-weight: bold;
}

.zoom-btn:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: scale(1.1);
}

/* Responsive pour le modal */
@media (max-width: 768px) {
    .modal-container {
        max-width: 95vw;
        max-height: 95vh;
    }
    
    .modal-header {
        top: -40px;
    }
    
    .modal-title {
        font-size: 1.5rem;
    }
    
    .modal-subtitle {
        font-size: 0.8rem;
    }
    
    .modal-navigation {
        width: 50px;
        height: 50px;
        font-size: 20px;
    }
    
    .modal-prev {
        left: -60px;
    }
    
    .modal-next {
        right: -60px;
    }
    
    .close-btn {
        width: 40px;
        height: 40px;
        font-size: 18px;
    }
    
    .modal-footer {
        bottom: -40px;
    }
    
    .download-btn {
        padding: 10px 20px;
        font-size: 14px;
    }
}
/* ---------------- FIN DU STYLE DU MODAL AMÉLIORÉ ---------------- */
</style>

<div class="section-container">
    <div class="header-content">
        <h1 class="main-title">
            Découvrez la qualité de nos <span>Diplômes</span>
        </h1>
        <p class="subtitle">
            Explorez des exemples d'attestations et de diplômes conçus pour vous ouvrir de nouvelles portes professionnelles.
        </p>
    </div>

    <div class="gallery-container">
        <div class="card card-side">
            <img src="{{ asset('edmate/assets/images/thumbs/uitsp.jpg') }}" alt="Diplôme de Spécialisation" class="card-img" data-modal-target="#modal-uitsp" data-title="Diplôme de Spécialisation" data-subtitle="Expertise dans un domaine précis">
            <div class="card-overlay">
                
                <a href="#" class="card-btn" style="background-color: #C2185B;">Voir l'exemple</a>
            </div>
        </div>

        <div class="card card-center">
            <img src="{{ asset('edmate/assets/images/thumbs/ciremoni.jpg') }}" alt="Diplôme Professionnel" class="card-img" data-modal-target="#modal-ciremoni" data-title="Diplôme Professionnel" data-subtitle="Un pas vers une carrière réussie">
            <div class="card-overlay">
                <h2 class="card-title">Une nouvelle étape vers le succès</h2>
                <p class="card-subtitle">Le début d'une aventure professionnelle passionnante</p>
                <a href="#" class="card-btn" style="background-color: #D32F2F;">Voir l'exemple</a>
            </div>
        </div>

        <div class="card card-side">
            <img src="{{ asset('edmate/assets/images/thumbs/uits-walid.jpg') }}" alt="Certificat Universitaire" class="card-img" data-modal-target="#modal-uits-walid" data-title="Certificat Universitaire" data-subtitle="Reconnu par les experts">
            <div class="card-overlay">
             
                <a href="#" class="card-btn" style="background-color: #C2185B;">Voir l'exemple</a>
            </div>
        </div>
    </div>

    <div class="gallery-container">
        <div class="card card-side">
            <img src="{{ asset('edmate/assets/images/thumbs/licence.jpg') }}" alt="Diplôme de Licence" class="card-img" data-modal-target="#modal-licence" data-title="Diplôme de Licence" data-subtitle="Formation universitaire complète">
            <div class="card-overlay">
                
                <a href="#" class="card-btn" style="background-color: #C2185B;">Voir l'exemple</a>
            </div>
        </div>

        <div class="card card-center">
            <img src="{{ asset('edmate/assets/images/thumbs/uitsc.jpg') }}" alt="Certificat Technique" class="card-img" data-modal-target="#modal-uitsc" data-title="Certificat Technique" data-subtitle="Compétences techniques avancées">
            <div class="card-overlay">
                <h2 class="card-title">Célébrez l'excellence académique</h2>
                <p class="card-subtitle">Célébrez votre réussite, commencez votre carrière</p>
                <a href="#" class="card-btn" style="background-color: #D32F2F;">Voir l'exemple</a>
            </div>
        </div>

        <div class="card card-side">
            <img src="{{ asset('edmate/assets/images/thumbs/all.jpeg') }}" alt="Collection de Certificats" class="card-img" data-modal-target="#modal-all" data-title="Collection de Certificats" data-subtitle="Diverses spécialisations disponibles">
            <div class="card-overlay">
              
                <a href="#" class="card-btn" style="background-color: #C2185B;">Voir l'exemple</a>
            </div>
        </div>
    </div>
</div>

<!-- Modals avec le nouveau design -->
<div id="modal-uitsp" class="modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Diplôme de Spécialisation</h3>
            <p class="modal-subtitle">Expertise dans un domaine précis</p>
        </div>
        <button class="close-btn"></button>
        <img src="{{ asset('edmate/assets/images/thumbs/uitsp.jpg') }}" alt="Diplôme de Spécialisation" class="modal-content">
        <div class="modal-footer">
            <div class="zoom-controls">
                <button class="zoom-btn zoom-in">+</button>
                <button class="zoom-btn zoom-out">-</button>
                <button class="zoom-btn zoom-reset">⌂</button>
            </div>
            
        </div>
    </div>
</div>

<div id="modal-ciremoni" class="modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Diplôme Professionnel</h3>
            <p class="modal-subtitle">Un pas vers une carrière réussie</p>
        </div>
        <button class="close-btn"></button>
        <img src="{{ asset('edmate/assets/images/thumbs/ciremoni.jpg') }}" alt="Diplôme Professionnel" class="modal-content">
        <div class="modal-footer">
            <div class="zoom-controls">
                <button class="zoom-btn zoom-in">+</button>
                <button class="zoom-btn zoom-out">-</button>
                <button class="zoom-btn zoom-reset">⌂</button>
            </div>
           
        </div>
    </div>
</div>

<div id="modal-uits-walid" class="modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Certificat Universitaire</h3>
            <p class="modal-subtitle">Reconnu par les experts</p>
        </div>
        <button class="close-btn"></button>
        <img src="{{ asset('edmate/assets/images/thumbs/uits-walid.jpg') }}" alt="Certificat Universitaire" class="modal-content">
        <div class="modal-footer">
            <div class="zoom-controls">
                <button class="zoom-btn zoom-in">+</button>
                <button class="zoom-btn zoom-out">-</button>
                <button class="zoom-btn zoom-reset">⌂</button>
            </div>
            
        </div>
    </div>
</div>

<div id="modal-licence" class="modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Diplôme de Licence</h3>
            <p class="modal-subtitle">Formation universitaire complète</p>
        </div>
        <button class="close-btn"></button>
        <img src="{{ asset('edmate/assets/images/thumbs/licence.jpg') }}" alt="Diplôme de Licence" class="modal-content">
        <div class="modal-footer">
            <div class="zoom-controls">
                <button class="zoom-btn zoom-in">+</button>
                <button class="zoom-btn zoom-out">-</button>
                <button class="zoom-btn zoom-reset">⌂</button>
            </div>
           
        </div>
    </div>
</div>

<div id="modal-uitsc" class="modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Certificat Technique</h3>
            <p class="modal-subtitle">Compétences techniques avancées</p>
        </div>
        <button class="close-btn"></button>
        <img src="{{ asset('edmate/assets/images/thumbs/uitsc.jpg') }}" alt="Certificat Technique" class="modal-content">
        <div class="modal-footer">
            <div class="zoom-controls">
                <button class="zoom-btn zoom-in">+</button>
                <button class="zoom-btn zoom-out">-</button>
                <button class="zoom-btn zoom-reset">⌂</button>
            </div>
            
        </div>
    </div>
</div>

<div id="modal-all" class="modal">
    <div class="modal-container">
        <div class="modal-header">
            <h3 class="modal-title">Collection de Certificats</h3>
            <p class="modal-subtitle">Diverses spécialisations disponibles</p>
        </div>
        <button class="close-btn"></button>
        <img src="{{ asset('edmate/assets/images/thumbs/all.jpeg') }}" alt="Collection de Certificats" class="modal-content">
        <div class="modal-footer">
            <div class="zoom-controls">
                <button class="zoom-btn zoom-in">+</button>
                <button class="zoom-btn zoom-out">-</button>
                <button class="zoom-btn zoom-reset">⌂</button>
            </div>
           
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        console.log('Script loaded'); // للتأكد من تحميل الScript
        
        const images = document.querySelectorAll('.card-img');
        const modals = document.querySelectorAll('.modal');
        const closeButtons = document.querySelectorAll('.close-btn');
        const cardBtns = document.querySelectorAll('.card-btn'); // إضافة أزرار الكروت
        const zoomInBtns = document.querySelectorAll('.zoom-in');
        const zoomOutBtns = document.querySelectorAll('.zoom-out');
        const zoomResetBtns = document.querySelectorAll('.zoom-reset');

        let currentZoom = 1;

        console.log('Found images:', images.length); // للتأكد من العثور على الصور

        // Ouvrir le modal - للصور
        images.forEach((image, index) => {
            console.log(`Adding click listener to image ${index}`); // تتبع إضافة المستمعات
            
            image.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                console.log('Image clicked!'); // تأكيد النقر على الصورة
                
                const modalId = image.getAttribute('data-modal-target');
                console.log('Modal ID:', modalId); // تتبع معرف النافذة
                
                const modal = document.querySelector(modalId);
                console.log('Modal found:', modal); // تأكيد العثور على النافذة
                
                if (modal) {
                    const modalTitle = modal.querySelector('.modal-title');
                    const modalSubtitle = modal.querySelector('.modal-subtitle');
                    
                    // Mise à jour du titre et sous-titre
                    if (modalTitle) {
                        modalTitle.textContent = image.getAttribute('data-title') || modalTitle.textContent;
                    }
                    if (modalSubtitle) {
                        modalSubtitle.textContent = image.getAttribute('data-subtitle') || modalSubtitle.textContent;
                    }
                    
                    modal.style.display = 'flex'; // إضافة هذا السطر
                    setTimeout(() => {
                        modal.classList.add('active');
                    }, 10); // تأخير قصير للانيميشن
                    
                    document.body.style.overflow = 'hidden';
                    currentZoom = 1;
                    const modalImage = modal.querySelector('.modal-content');
                    if (modalImage) {
                        modalImage.style.transform = `scale(${currentZoom})`;
                    }
                } else {
                    console.error('Modal not found:', modalId);
                }
            });
        });

        // Ouvrir le modal - للأزرار
        cardBtns.forEach((btn, index) => {
            btn.addEventListener('click', (event) => {
                event.preventDefault();
                event.stopPropagation();
                console.log('Button clicked!'); // تأكيد النقر على الزر
                
                // البحث عن الصورة المرتبطة
                const card = btn.closest('.card');
                const image = card.querySelector('.card-img');
                const modalId = image.getAttribute('data-modal-target');
                const modal = document.querySelector(modalId);
                
                if (modal) {
                    const modalTitle = modal.querySelector('.modal-title');
                    const modalSubtitle = modal.querySelector('.modal-subtitle');
                    
                    if (modalTitle) {
                        modalTitle.textContent = image.getAttribute('data-title') || modalTitle.textContent;
                    }
                    if (modalSubtitle) {
                        modalSubtitle.textContent = image.getAttribute('data-subtitle') || modalSubtitle.textContent;
                    }
                    
                    modal.style.display = 'flex';
                    setTimeout(() => {
                        modal.classList.add('active');
                    }, 10);
                    
                    document.body.style.overflow = 'hidden';
                    currentZoom = 1;
                    const modalImage = modal.querySelector('.modal-content');
                    if (modalImage) {
                        modalImage.style.transform = `scale(${currentZoom})`;
                    }
                }
            });
        });

        // Fermer le modal
        closeButtons.forEach(button => {
            button.addEventListener('click', (e) => {
                e.stopPropagation();
                console.log('Close button clicked!'); // تأكيد النقر على زر الإغلاق
                closeModal(button.closest('.modal'));
            });
        });

        modals.forEach(modal => {
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    console.log('Clicked outside modal'); // تأكيد النقر خارج النافذة
                    closeModal(modal);
                }
            });
        });

        function closeModal(modal) {
            if (modal) {
                console.log('Closing modal'); // تأكيد إغلاق النافذة
                modal.classList.remove('active');
                setTimeout(() => {
                    modal.style.display = 'none';
                }, 400); // مدة الانيميشن
                document.body.style.overflow = 'auto';
                currentZoom = 1;
                const modalImage = modal.querySelector('.modal-content');
                if (modalImage) {
                    modalImage.style.transform = `scale(${currentZoom})`;
                }
            }
        }

        // Contrôles de zoom
        zoomInBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const modal = btn.closest('.modal');
                const modalImage = modal.querySelector('.modal-content');
                currentZoom = Math.min(currentZoom + 0.2, 3);
                modalImage.style.transform = `scale(${currentZoom})`;
                modalImage.style.cursor = currentZoom > 1 ? 'move' : 'default';
            });
        });

        zoomOutBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const modal = btn.closest('.modal');
                const modalImage = modal.querySelector('.modal-content');
                currentZoom = Math.max(currentZoom - 0.2, 0.5);
                modalImage.style.transform = `scale(${currentZoom})`;
                modalImage.style.cursor = currentZoom > 1 ? 'move' : 'default';
            });
        });

        zoomResetBtns.forEach(btn => {
            btn.addEventListener('click', (e) => {
                e.stopPropagation();
                const modal = btn.closest('.modal');
                const modalImage = modal.querySelector('.modal-content');
                currentZoom = 1;
                modalImage.style.transform = `scale(${currentZoom})`;
                modalImage.style.cursor = 'default';
            });
        });

        // Navigation par clavier
        document.addEventListener('keydown', (e) => {
            const activeModal = document.querySelector('.modal.active');
            if (activeModal) {
                switch(e.key) {
                    case 'Escape':
                        closeModal(activeModal);
                        break;
                    case '+':
                    case '=':
                        e.preventDefault();
                        activeModal.querySelector('.zoom-in').click();
                        break;
                    case '-':
                        e.preventDefault();
                        activeModal.querySelector('.zoom-out').click();
                        break;
                    case '0':
                        e.preventDefault();
                        activeModal.querySelector('.zoom-reset').click();
                        break;
                }
            }
        });

        // Drag & drop pour les images zoomées
        let isDragging = false;
        let startX, startY, translateX = 0, translateY = 0;

        modals.forEach(modal => {
            const modalImage = modal.querySelector('.modal-content');
            
            modalImage.addEventListener('mousedown', (e) => {
                if (currentZoom > 1) {
                    isDragging = true;
                    startX = e.clientX - translateX;
                    startY = e.clientY - translateY;
                    modalImage.style.cursor = 'grabbing';
                }
            });

            document.addEventListener('mousemove', (e) => {
                if (isDragging && currentZoom > 1) {
                    e.preventDefault();
                    translateX = e.clientX - startX;
                    translateY = e.clientY - startY;
                    modalImage.style.transform = `scale(${currentZoom}) translate(${translateX}px, ${translateY}px)`;
                }
            });

            document.addEventListener('mouseup', () => {
                if (isDragging) {
                    isDragging = false;
                    modalImage.style.cursor = currentZoom > 1 ? 'move' : 'default';
                }
            });
        });

        // Animation d'entrée pour les cartes
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observer les cartes pour l'animation
        document.querySelectorAll('.card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(50px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });

        // Effet de particules sur hover (optionnel)
        function createParticle(x, y) {
            const particle = document.createElement('div');
            particle.style.cssText = `
                position: fixed;
                width: 4px;
                height: 4px;
                background: radial-gradient(circle, #667eea, transparent);
                border-radius: 50%;
                pointer-events: none;
                z-index: 10000;
                left: ${x}px;
                top: ${y}px;
                animation: particle-float 1s ease-out forwards;
            `;
            
            document.body.appendChild(particle);
            
            setTimeout(() => {
                particle.remove();
            }, 1000);
        }

        // Ajouter l'animation des particules
        const style = document.createElement('style');
        style.textContent = `
            @keyframes particle-float {
                0% {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
                100% {
                    opacity: 0;
                    transform: translateY(-50px) scale(0);
                }
            }
        `;
        document.head.appendChild(style);

        // Ajouter les particules sur hover des cartes
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('mouseenter', (e) => {
                const rect = card.getBoundingClientRect();
                for (let i = 0; i < 5; i++) {
                    setTimeout(() => {
                        createParticle(
                            rect.left + Math.random() * rect.width,
                            rect.top + Math.random() * rect.height
                        );
                    }, i * 100);
                }
            });
        });
    });
</script>
@endpush
@endsection