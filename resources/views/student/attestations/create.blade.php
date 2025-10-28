@extends('layouts.app')

@section('title', 'Demander une Attestation')

@section('content')
<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }

    @keyframes slideInLeft {
        from {
            opacity: 0;
            transform: translateX(-50px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes shimmer {
        0% {
            background-position: -1000px 0;
        }
        100% {
            background-position: 1000px 0;
        }
    }

    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-10px); }
        75% { transform: translateX(10px); }
    }

    @keyframes float {
        0%, 100% {
            transform: translateY(0) rotate(0deg);
        }
        50% {
            transform: translateY(-20px) rotate(180deg);
        }
    }

    @keyframes ripple {
        to {
            transform: scale(2);
            opacity: 0;
        }
    }

    .page-header-custom {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
        border-radius: 25px;
        padding: 2.5rem;
        margin-bottom: 2rem;
        box-shadow: 0 15px 45px rgba(194, 24, 91, 0.4);
        color: white;
        position: relative;
        overflow: hidden;
        animation: fadeInUp 0.6s ease-out;
    }

    .page-header-custom::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: shimmer 3s infinite;
    }
    
    .page-header-custom h4 {
        margin: 0;
        font-weight: 800;
        font-size: 2rem;
        position: relative;
        z-index: 1;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.2);
    }
    
    .main-card-custom {
        border: none;
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 15px 50px rgba(0, 0, 0, 0.12);
        background: white;
        animation: fadeInUp 0.8s ease-out;
        transition: all 0.4s ease;
    }

    .main-card-custom:hover {
        box-shadow: 0 20px 60px rgba(194, 24, 91, 0.15);
        transform: translateY(-5px);
    }
    
    .alert-custom-danger {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        border: none;
        border-radius: 18px;
        color: white;
        padding: 1.5rem 2rem;
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.3);
        animation: slideInLeft 0.5s ease-out;
        position: relative;
        overflow: hidden;
    }

    .alert-custom-danger::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
        transition: left 0.5s ease;
    }

    .alert-custom-danger:hover::before {
        left: 100%;
    }
    
    .alert-custom-warning {
        background: linear-gradient(135deg, #fbbf24 0%, #f59e0b 100%);
        border: none;
        border-radius: 18px;
        color: white;
        padding: 2rem;
        box-shadow: 0 8px 25px rgba(251, 191, 36, 0.3);
        animation: pulse 2s infinite;
    }
    
    .alert-custom-info {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border: none;
        border-radius: 18px;
        color: white;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.3);
        animation: fadeInUp 1s ease-out;
        position: relative;
        overflow: hidden;
    }

    .alert-custom-info::after {
        content: '';
        position: absolute;
        width: 100px;
        height: 100px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        top: -50px;
        right: -50px;
        animation: pulse 3s infinite;
    }
    
    .alert-custom-info ul {
        margin-bottom: 0;
        padding-left: 1.5rem;
        position: relative;
        z-index: 1;
    }
    
    .alert-custom-info li {
        margin-bottom: 0.75rem;
        transition: transform 0.3s ease;
    }

    .alert-custom-info li:hover {
        transform: translateX(10px);
    }
    
    .form-label-custom {
        color: #1f2937;
        font-weight: 800;
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
        display: block;
        transition: all 0.3s ease;
        position: relative;
    }
    
    .form-label-custom i {
        color: #C2185B;
        margin-right: 0.75rem;
        font-size: 1.2rem;
        transition: transform 0.3s ease;
    }

    .form-label-custom:hover i {
        transform: scale(1.2) rotate(10deg);
    }
    
    .form-select-custom,
    .form-control-custom {
        border: 3px solid #e5e7eb;
        border-radius: 15px;
        padding: 1rem 1.25rem;
        font-size: 1rem;
        transition: all 0.4s ease;
        background: white;
        position: relative;
    }
    
    .form-select-custom:focus,
    .form-control-custom:focus {
        border-color: #C2185B;
        box-shadow: 0 0 0 6px rgba(194, 24, 91, 0.15);
        outline: none;
        transform: translateY(-3px);
    }
    
    .form-select-custom.is-invalid,
    .form-control-custom.is-invalid {
        border-color: #ef4444;
        animation: shake 0.5s;
    }
    
    .form-text-custom {
        color: #6b7280;
        font-size: 0.875rem;
        margin-top: 0.75rem;
        display: block;
        font-style: italic;
        transition: all 0.3s ease;
    }
    
    .profile-info-card {
        background: linear-gradient(135deg, #fef3f2 0%, #fee2e2 100%);
        border: 3px solid #C2185B;
        border-radius: 20px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 8px 30px rgba(194, 24, 91, 0.15);
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }

    .profile-info-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(194, 24, 91, 0.1) 0%, transparent 70%);
        transition: all 0.6s ease;
    }

    .profile-info-card:hover::before {
        top: 0;
        right: 0;
    }

    .profile-info-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 40px rgba(194, 24, 91, 0.25);
    }
    
    .profile-info-card h6 {
        color: #C2185B;
        font-weight: 800;
        margin-bottom: 1.5rem;
        font-size: 1.25rem;
        position: relative;
        z-index: 1;
    }
    
    .profile-info-card p {
        color: #374151;
        margin-bottom: 0.75rem;
        font-size: 1rem;
        position: relative;
        z-index: 1;
        transition: all 0.3s ease;
        padding-left: 0;
        border-left: none;
    }

    .profile-info-card p:hover {
        padding-left: 15px;
        border-left: 4px solid #C2185B;
    }
    
    .form-check-custom {
        background: linear-gradient(135deg, #fef3f2 0%, #fee2e2 100%);
        border: 3px solid #C2185B;
        border-radius: 15px;
        padding: 1.5rem 2rem;
        margin-bottom: 2rem;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
    }

    .form-check-custom::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(194, 24, 91, 0.1), transparent);
        transition: left 0.6s ease;
    }

    .form-check-custom:hover::before {
        left: 100%;
    }

    .form-check-custom:hover {
        box-shadow: 0 8px 25px rgba(194, 24, 91, 0.2);
        transform: scale(1.02);
    }
    
    .form-check-custom .form-check-input {
        width: 1.5rem;
        height: 1.5rem;
        border: 3px solid #C2185B;
        margin-top: 0.25rem;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-check-custom .form-check-input:hover {
        transform: scale(1.2);
    }
    
    .form-check-custom .form-check-input:checked {
        background-color: #C2185B;
        border-color: #C2185B;
        animation: pulse 0.5s;
    }
    
    .form-check-custom .form-check-label {
        color: #991b1b;
        font-weight: 600;
        margin-left: 0.75rem;
        line-height: 1.8;
        cursor: pointer;
    }
    
    .btn-primary-custom {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
        color: white;
        border: none;
        padding: 1rem 2.5rem;
        border-radius: 15px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.4s ease;
        box-shadow: 0 8px 25px rgba(194, 24, 91, 0.4);
        position: relative;
        overflow: hidden;
    }

    .btn-primary-custom::before {
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

    .btn-primary-custom:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .btn-primary-custom:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(194, 24, 91, 0.5);
        color: white;
    }

    .btn-primary-custom:active {
        transform: translateY(-2px);
    }
    
    .btn-secondary-custom {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
        border: none;
        padding: 1rem 2.5rem;
        border-radius: 15px;
        font-weight: 700;
        font-size: 1.1rem;
        transition: all 0.4s ease;
        box-shadow: 0 8px 25px rgba(107, 114, 128, 0.3);
        position: relative;
        overflow: hidden;
    }
    
    .btn-secondary-custom:hover {
        transform: translateY(-5px);
        box-shadow: 0 15px 35px rgba(107, 114, 128, 0.4);
        color: white;
        background: linear-gradient(135deg, #4b5563 0%, #374151 100%);
    }
    
    .help-card-custom {
        border: none;
        border-radius: 25px;
        overflow: hidden;
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.1);
        margin-top: 2rem;
        animation: fadeInUp 1.2s ease-out;
        transition: all 0.4s ease;
    }

    .help-card-custom:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 50px rgba(139, 92, 246, 0.2);
    }
    
    .help-card-header {
        background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%);
        color: white;
        padding: 2rem;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .help-card-header::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
        animation: shimmer 4s infinite;
    }
    
    .help-card-header h5 {
        margin: 0;
        font-weight: 800;
        font-size: 1.5rem;
        position: relative;
        z-index: 1;
    }
    
    .help-card-body {
        padding: 2.5rem;
        background: white;
    }
    
    .help-card-body ul {
        list-style: none;
        padding-left: 0;
    }
    
    .help-card-body ul li {
        padding: 1rem 0 1rem 30px;
        border-bottom: 2px solid #f3f4f6;
        color: #374151;
        transition: all 0.3s ease;
        position: relative;
    }

    .help-card-body ul li::before {
        content: '●';
        position: absolute;
        left: 0;
        color: #C2185B;
        font-size: 1.5rem;
        transition: all 0.3s ease;
    }

    .help-card-body ul li:hover::before {
        transform: scale(1.5) rotate(360deg);
    }

    .help-card-body ul li:hover {
        padding-left: 40px;
        color: #C2185B;
    }
    
    .help-card-body ul li:last-child {
        border-bottom: none;
    }
    
    .help-card-body ul li strong:first-child {
        color: #C2185B;
        font-size: 1.05rem;
    }
    
    .help-card-body .contact-info {
        background: linear-gradient(135deg, #fef3f2 0%, #fee2e2 100%);
        padding: 1.5rem;
        border-radius: 15px;
        margin-top: 1.5rem;
        border-left: 5px solid #C2185B;
        box-shadow: 0 4px 15px rgba(194, 24, 91, 0.1);
        transition: all 0.3s ease;
    }

    .contact-info:hover {
        transform: translateX(10px);
        box-shadow: 0 6px 20px rgba(194, 24, 91, 0.2);
    }
    
    .btn-close-custom {
        filter: brightness(0) invert(1);
        transition: transform 0.3s ease;
    }

    .btn-close-custom:hover {
        transform: rotate(90deg) scale(1.2);
    }
    
    .invalid-feedback {
        color: #ef4444;
        font-weight: 600;
        margin-top: 0.75rem;
        animation: slideInLeft 0.3s ease-out;
    }
</style>

<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="page-header-custom">
                <h4>
                    <i class="fas fa-file-alt me-2"></i> Demander une Attestation de Scolarité
                </h4>
            </div>

            <div class="main-card-custom">
                <div class="card-body p-4">
                    @if(session('error'))
                        <div class="alert-custom-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-triangle me-2"></i> {{ session('error') }}
                            <button type="button" class="btn-close btn-close-custom" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if($inscriptions->isEmpty())
                        <div class="alert-custom-warning" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Aucune inscription disponible</strong>
                            <p class="mb-0 mt-2">Vous devez avoir au moins une inscription active ou terminée pour demander une attestation.</p>
                        </div>
                    @else
                        <form action="{{ route('student.attestations.store') }}" method="POST" id="attestationForm">
                            @csrf

                            <div class="alert-custom-info">
                                <i class="fas fa-info-circle me-2"></i>
                                <strong>Information importante:</strong>
                                <ul class="mt-2">
                                    <li>L'attestation sera générée en format PDF</li>
                                    <li>Le traitement prend généralement 2-3 jours ouvrables</li>
                                    <li>Vous recevrez une notification une fois l'attestation prête</li>
                                    <li>Assurez-vous que votre date de naissance est correcte</li>
                                </ul>
                            </div>

                            <div class="mb-4">
                                <label for="inscription_id" class="form-label-custom">
                                    <i class="fas fa-graduation-cap"></i> Sélectionnez votre formation *
                                </label>
                                <select name="inscription_id" id="inscription_id" class="form-select form-select-custom @error('inscription_id') is-invalid @enderror" required>
                                    <option value="">-- Choisissez une formation --</option>
                                    @foreach($inscriptions as $inscription)
                                        <option value="{{ $inscription->id }}" {{ old('inscription_id') == $inscription->id ? 'selected' : '' }}>
                                            {{ $inscription->formation->title }} 
                                            ({{ $inscription->formation->category->name }}) 
                                            - Inscrit le {{ $inscription->inscription_date->format('d/m/Y') }}
                                            @if($inscription->status === 'completed')
                                                - ✓ Terminé
                                            @endif
                                        </option>
                                    @endforeach
                                </select>
                                @error('inscription_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text-custom">Choisissez la formation pour laquelle vous souhaitez une attestation</small>
                            </div>

                            <div class="mb-4">
                                <label for="birth_date" class="form-label-custom">
                                    <i class="fas fa-calendar-alt"></i> Date de naissance *
                                </label>
                                <input type="date" 
                                       name="birth_date" 
                                       id="birth_date" 
                                       class="form-control form-control-custom @error('birth_date') is-invalid @enderror" 
                                       value="{{ old('birth_date') }}"
                                       max="{{ date('Y-m-d', strtotime('-15 years')) }}"
                                       required>
                                @error('birth_date')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="form-text-custom">Cette date apparaîtra sur votre attestation officielle</small>
                            </div>

                            <div class="profile-info-card">
                                <h6><i class="fas fa-user me-2"></i> Informations du profil</h6>
                                <p><strong>Nom complet:</strong> {{ auth()->user()->name }}</p>
                                <p><strong>Email:</strong> {{ auth()->user()->email }}</p>
                                @if(auth()->user()->cin)
                                    <p class="mb-0"><strong>CIN:</strong> {{ auth()->user()->cin }}</p>
                                @endif
                            </div>

                            <div class="form-check-custom">
                                <input class="form-check-input" type="checkbox" id="confirm" required>
                                <label class="form-check-label" for="confirm">
                                    Je confirme que les informations fournies sont exactes et que je comprends que 
                                    toute fausse déclaration peut entraîner l'annulation de ma demande.
                                </label>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('student.attestations.index') }}" class="btn-secondary-custom">
                                    <i class="fas fa-arrow-left me-2"></i> Retour
                                </a>
                                <button type="submit" class="btn-primary-custom" id="submitBtn">
                                    <i class="fas fa-paper-plane me-2"></i> Soumettre la demande
                                </button>
                            </div>
                        </form>
                    @endif
                </div>
            </div>

            <div class="help-card-custom">
                <div class="help-card-header">
                    <h5><i class="fas fa-question-circle me-2"></i> Besoin d'aide ?</h5>
                </div>
                <div class="help-card-body">
                    <p><strong style="color: #1f2937; font-size: 1.15rem;">Questions fréquentes:</strong></p>
                    <ul>
                        <li><strong>Combien de temps pour recevoir mon attestation ?</strong> Généralement 2-3 jours ouvrables</li>
                        <li><strong>Puis-je demander plusieurs attestations ?</strong> Oui, pour chaque formation inscrite</li>
                        <li><strong>L'attestation est-elle officielle ?</strong> Oui, signée par le directeur pédagogique</li>
                        <li><strong>Comment je serais notifié ?</strong> Par email et notification sur votre dashboard</li>
                    </ul>
                    <div class="contact-info">
                        <p class="mb-0">
                            <i class="fas fa-envelope me-2" style="color: #C2185B;"></i>
                            Pour toute question, contactez l'administration à: <strong style="color: #C2185B;">contact@uits.ma</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inscriptionSelect = document.getElementById('inscription_id');
        const birthDateInput = document.getElementById('birth_date');
        const confirmCheckbox = document.getElementById('confirm');
        const submitBtn = document.getElementById('submitBtn');
        const form = document.getElementById('attestationForm');

        if (inscriptionSelect) {
            inscriptionSelect.addEventListener('change', function() {
                this.style.borderColor = '#10b981';
                this.style.transform = 'scale(1.02)';
                
                setTimeout(() => {
                    this.style.borderColor = '';
                    this.style.transform = '';
                }, 300);
            });
        }

        if (birthDateInput) {
            birthDateInput.addEventListener('change', function() {
                const selectedDate = new Date(this.value);
                const today = new Date();
                const age = today.getFullYear() - selectedDate.getFullYear();
                
                if (age < 15) {
                    this.classList.add('is-invalid');
                    this.style.animation = 'shake 0.5s';
                } else {
                    this.classList.remove('is-invalid');
                    this.style.borderColor = '#10b981';
                    
                    setTimeout(() => {
                        this.style.borderColor = '';
                    }, 1000);
                }
            });
        }

        if (confirmCheckbox) {
            confirmCheckbox.addEventListener('change', function() {
                if (this.checked) {
                    const label = this.nextElementSibling;
                    label.style.color = '#059669';
                    label.style.fontWeight = '700';
                    
                    setTimeout(() => {
                        label.style.color = '';
                        label.style.fontWeight = '';
                    }, 1000);
                }
            });
        }

        if (submitBtn && form) {
            form.addEventListener('submit', function(e) {
                if (!form.checkValidity()) {
                    e.preventDefault();
                    const invalidFields = form.querySelectorAll(':invalid');
                    invalidFields.forEach(field => {
                        field.style.animation = 'shake 0.5s';
                        field.addEventListener('animationend', () => {
                            field.style.animation = '';
                        }, { once: true });
                    });
                    return;
                }

                submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Envoi en cours...';
                submitBtn.disabled = true;
                submitBtn.style.background = 'linear-gradient(135deg, #6b7280 0%, #4b5563 100%)';
            });
        }

        const icons = document.querySelectorAll('.form-label-custom i');
        icons.forEach((icon, index) => {
            icon.style.animation = `float 3s ease-in-out ${index * 0.2}s infinite`;
        });

        const requiredFields = document.querySelectorAll('[required]');
        requiredFields.forEach(field => {
            field.addEventListener('focus', function() {
                const label = this.previousElementSibling;
                if (label && label.classList.contains('form-label-custom')) {
                    label.style.animation = 'pulse 0.5s';
                    label.addEventListener('animationend', () => {
                        label.style.animation = '';
                    }, { once: true });
                }
            });
        });

        form.addEventListener('invalid', function(e) {
            e.preventDefault();
            const firstInvalid = form.querySelector(':invalid');
            if (firstInvalid) {
                firstInvalid.scrollIntoView({ behavior: 'smooth', block: 'center' });
                firstInvalid.focus();
            }
        }, true);

        const buttons = document.querySelectorAll('.btn-primary-custom, .btn-secondary-custom');
        buttons.forEach(button => {
            button.addEventListener('click', function(e) {
                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.6);
                    transform: scale(0);
                    animation: ripple 0.6s ease-out;
                    pointer-events: none;
                `;
                
                this.appendChild(ripple);
                setTimeout(() => ripple.remove(), 600);
            });
        });

        const formFields = form.querySelectorAll('input[required], select[required]');
        
        function updateProgress() {
            let completedFields = 0;
            formFields.forEach(field => {
                if (field.type === 'checkbox') {
                    if (field.checked) completedFields++;
                } else if (field.value.trim() !== '') {
                    completedFields++;
                }
            });

            const progress = (completedFields / formFields.length) * 100;
            
            if (progress === 100) {
                submitBtn.style.animation = 'pulse 1s infinite';
            } else {
                submitBtn.style.animation = '';
            }
        }

        formFields.forEach(field => {
            field.addEventListener('change', updateProgress);
        });

        const helperTexts = document.querySelectorAll('.form-text-custom');
        helperTexts.forEach(text => {
            const parent = text.parentElement;
            const input = parent.querySelector('input, select');
            
            if (input) {
                input.addEventListener('focus', () => {
                    text.style.color = '#C2185B';
                    text.style.fontWeight = '600';
                    text.style.transform = 'translateX(5px)';
                });
                
                input.addEventListener('blur', () => {
                    text.style.color = '';
                    text.style.fontWeight = '';
                    text.style.transform = '';
                });
            }
        });

        const alerts = document.querySelectorAll('[class*="alert-custom"]');
        alerts.forEach((alert, index) => {
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.style.transition = 'all 0.5s ease';
                alert.style.opacity = '1';
                alert.style.transform = 'translateY(0)';
            }, index * 100);
        });

        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'Enter') {
                if (form.checkValidity()) {
                    form.submit();
                }
            }
            
            if (e.key === 'Escape') {
                if (confirm('Voulez-vous vraiment réinitialiser le formulaire ?')) {
                    form.reset();
                }
            }
        });

        console.log('🎉 Formulaire d\'attestation initialisé avec succès!');
    });
</script>
@endpush