@extends('layouts.app')

@section('title', 'Créer un Report de séance ')

@push('styles')
{{-- Liens vers les styles externes (Font Awesome et Flatpickr) --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<style>
    /* Variables de Couleur Cohérentes de la Page d'Index */
    :root {
        --primary-red: #D32F2F;
        --secondary-pink: #C2185B;
        --accent-red: #ef4444;
        --gradient-primary: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
        --gradient-secondary: linear-gradient(135deg, #ef4444 0%, #D32F2F 100%);
        --gradient-light: linear-gradient(135deg, rgba(211,47,47,0.1) 0%, rgba(194,24,91,0.1) 100%);
        --shadow-red: rgba(211, 47, 47, 0.3);
        --shadow-pink: rgba(194, 24, 91, 0.3);
    }

    body {
        background: linear-gradient(135deg, #f8f9ff 0%, #fff0f5 100%); /* Arrière-plan cohérent */
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #333; /* Couleur de texte par défaut */
    }

    .card-modern {
        border: none;
        border-radius: 20px; /* Rayon de bordure cohérent */
        box-shadow: 0 15px 35px rgba(0,0,0,0.08); /* Ombre cohérente */
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        background: rgba(255,255,255,0.95);
        animation: fadeIn 0.6s ease-out forwards; /* Animation d'entrée de la carte */
    }

    /* En-tête en dégradé (pour les en-têtes de carte spécifiques) */
    .gradient-header {
        background: var(--gradient-primary); /* Utilisation du dégradé primaire */
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 25px 30px;
        position: relative;
        overflow: hidden;
    }

    /* Animation ::before de l'en-tête (reste la même) */
    .gradient-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='7' cy='7' r='7'/%3E%3Ccircle cx='53' cy='53' r='7'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    /* Boutons modernes */
    .btn-modern {
        border-radius: 30px; /* Rayon de bouton cohérent */
        padding: 12px 30px;
        font-weight: 600;
        font-size: 14px; /* Taille de police cohérente */
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-primary-modern {
        background: var(--gradient-secondary); /* Dégradé secondaire pour l'action principale */
        color: white;
        box-shadow: 0 8px 25px var(--shadow-red); /* Ombre cohérente */
    }

    .btn-primary-modern:hover {
        transform: translateY(-3px) scale(1.05); /* Effet de survol cohérent */
        box-shadow: 0 15px 35px var(--shadow-red);
        color: white;
    }

    .btn-primary-modern::before {
        /* Effet de brillance/vague au survol */
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary-modern:hover::before {
        left: 100%;
    }

    /* Bouton d'action secondaire (Retour à la Liste) */
    .btn-secondary-modern {
        background-color: #db3f3f; /* Couleur ajustée (était #6c757d) */
        color: black !important;
        box-shadow: 0 5px 15px rgba(108, 117, 125, 0.2);
    }

    .btn-secondary-modern:hover {
        background-color: #cc3939; /* Plus foncé au survol */
        color: white;
        transform: translateY(-2px); /* Léger soulèvement */
    }

    /* Bouton Outline secondaire pour "Effacer le Formulaire" */
    .btn-outline-secondary.btn-modern {
        border: 2px solid var(--secondary-pink); /* Couleur de thème principale pour le contour */
        color: var(--secondary-pink);
        background-color: transparent;
        box-shadow: none;
    }

    .btn-outline-secondary.btn-modern:hover {
        background: var(--secondary-pink); /* Remplissage de couleur au survol */
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px var(--shadow-pink); /* Ajout d'ombre au survol */
    }

    /* Styles des éléments de formulaire */
    .form-label {
        font-weight: 700;
        color: #4a4a4a;
        margin-bottom: 8px;
    }

    .form-control-modern, .form-select-modern {
        border-radius: 15px;
        border: 2px solid rgba(211,47,47,0.1);
        padding: 12px 18px;
        font-weight: 500;
        transition: all 0.3s ease;
        background-color: #ffffff;
    }

    .form-control-modern:focus, .form-select-modern:focus {
        border-color: var(--primary-red);
        box-shadow: 0 0 20px rgba(211,47,47,0.2);
    }

    .form-text {
        font-size: 0.85em;
        color: #777;
        margin-top: 5px;
    }

    /* Styles d'alerte */
    .alert-danger {
        background: linear-gradient(135deg, #fde7e7 0%, #fcdede 100%);
        border: 1px solid #e57373;
        border-radius: 15px;
        color: #c62828;
        padding: 15px 20px;
    }

    /* Styles de l'en-tête de page */
    h2 {
        font-size: 2rem;
        color: var(--primary-red);
    }

    .text-muted {
        color: #888 !important;
    }

    .fas.fa-calendar-plus {
        color: var(--primary-red) !important;
    }

    /* Animation d'entrée */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    {{-- En-tête de la Page --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-calendar-plus me-3"></i>
                        Créer un Nouveau Report de séance
                    </h2>
                    <p class="text-muted mb-0">Remplissez les détails pour reporter une séance.</p>
                </div>
                {{-- Bouton "Retour à la Liste" --}}
                <a href="{{ route('course_reschedules.index') }}" class="btn btn-secondary-modern btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la Liste
                </a>
            </div>
        </div>
    </div>

    {{-- Carte du Formulaire --}}
    <div class="card card-modern">
        <div class="card-header gradient-header">
            <h5 class="mb-0 fw-bold">Détails du Report</h5>
        </div>
        <div class="card-body">
            {{-- Affichage des Erreurs de Validation --}}
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Oups!</strong> Il y a eu quelques problèmes avec votre saisie.
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('course_reschedules.store') }}" method="POST">
                @csrf

                {{-- Sélection du Consultant (Visible uniquement si l'utilisateur a la permission 'course-manage-all', par exemple, un administrateur) --}}
                @can('course-manage-all') 
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="consultant_id" class="form-label fw-bold">Sélectionner un Consultant <span class="text-danger">*</span></label>
                        <select name="consultant_id" id="consultant_id" class="form-select form-select-modern @error('consultant_id') is-invalid @enderror" required>
                            <option value="">-- Sélectionner un Consultant --</option>
                            @foreach($consultants as $consultant)
                                <option value="{{ $consultant->id }}" {{ old('consultant_id', $selectedConsultantId ?? '') == $consultant->id ? 'selected' : '' }}>
                                    {{ $consultant->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('consultant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Sélectionnez le consultant dont vous voulez reporter la séance.</small>
                    </div>
                </div>
                @else {{-- Pour les consultants ou étudiants (où le consultant est l'utilisateur actuel) --}}
                    <input type="hidden" name="consultant_id" value="{{ Auth::id() }}">
                @endcan

                {{-- Sélection du Cours --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="course_id" class="form-label fw-bold">Sélectionner une Séance <span class="text-danger">*</span></label>
                        <select name="course_id" id="course_id" class="form-select form-select-modern @error('course_id') is-invalid @enderror" required>
                            <option value="">-- Sélectionner une Séance --</option>
                            {{-- Les cours seront chargés dynamiquement par JavaScript pour les admins, ou pré-chargés pour les autres --}}
                            @foreach($courses as $course)
                                <option 
                                    value="{{ $course->id }}" 
                                    data-original-date="{{ \Carbon\Carbon::parse($course->course_date)->format('Y-m-d') }}"
                                    data-start-time="{{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }}"
                                    data-end-time="{{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}"
                                    {{ old('course_id', $selectedCourse ? $selectedCourse->id : '') == $course->id ? 'selected' : '' }}
                                >
                                    {{ $course->title }} ({{ \Carbon\Carbon::parse($course->course_date)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="todayCourseError" class="alert alert-danger mt-2 d-none" role="alert">
                            ⚠️ Vous ne pouvez pas reporter une séance qui a lieu aujourd’hui
                        </div>
                    </div>
                    <div class="col-md-6">
                        <label for="original_date_display" class="form-label fw-bold">Date & Heure du séance Original</label>
                        <input type="text" id="original_date_display" class="form-control form-control-modern" readonly disabled 
                               value="{{ $selectedCourse ? \Carbon\Carbon::parse($selectedCourse->course_date)->format('d/m/Y') . ' ' . \Carbon\Carbon::parse($selectedCourse->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($selectedCourse->end_time)->format('H:i') : 'Sélectionnez une Séance' }}">
                        <small class="form-text text-muted">Ceci est la date et l'heure actuellement prévues pour la séance sélectionnée.</small>
                    </div>
                </div>

                {{-- Champ Nouvelle Date --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="new_date" class="form-label fw-bold">Nouvelle Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="new_date" id="new_date" class="form-control form-control-modern @error('new_date') is-invalid @enderror" 
                               value="{{ old('new_date') }}" required>
                        @error('new_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Sélectionnez la nouvelle date et heure pour la séance. (Minimum demain)</small>
                    </div>
                </div>

                {{-- Champ Raison --}}
                <div class="mb-4">
                    <label for="reason" class="form-label fw-bold">Raison du Report</label>
                    <textarea name="reason" id="reason" rows="4" class="form-control form-control-modern @error('reason') is-invalid @enderror" 
                             placeholder="Fournissez une brève raison pour le report (facultatif)">{{ old('reason') }}</textarea>
                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Boutons d'Action --}}
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-save me-2"></i>Reporter la séance
                    </button>
                    <button type="reset" class="btn btn-outline-secondary btn-modern">
                        <i class="fas fa-times me-2"></i>Effacer le Formulaire
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // b9a l'code dyal les tooltips hna (Code des infobulles)

    // Initialisation de Flatpickr pour le champ new_date
    flatpickr("#new_date", {
        dateFormat: "Y-m-d H:i",
        enableTime: true,
        altInput: true,
        altFormat: "F j, Y H:i",
        minDate: "tomorrow", // <-- Changement important: la date minimale est demain
        allowInput: true,
        theme: 'material_red',
        locale: {
            firstDayOfWeek: 1
        },
    });

    const courseSelect = document.getElementById('course_id');
    const originalDateDisplay = document.getElementById('original_date_display');
    const consultantSelect = document.getElementById('consultant_id');

    // Fonction pour mettre à jour l'affichage de la date de cours originale
    function updateOriginalDateDisplay() {
        const selectedOption = courseSelect.options[courseSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const originalDate = selectedOption.dataset.originalDate;
            const startTime = selectedOption.dataset.startTime;
            const endTime = selectedOption.dataset.endTime;
            
            // Formatage de la date pour l'affichage (jj/mm/aaaa)
            const formattedOriginalDate = new Date(originalDate + 'T' + startTime).toLocaleDateString('en-GB', {
                day: '2-digit', month: '2-digit', year: 'numeric'
            });

            originalDateDisplay.value = `${formattedOriginalDate} ${startTime}-${endTime}`;
        } else {
            originalDateDisplay.value = 'Sélectionnez une séance';
        }
    }

    // Appel initial et écouteur d'événement pour la sélection de cours
    updateOriginalDateDisplay();
    courseSelect.addEventListener('change', updateOriginalDateDisplay);

    // Logique de chargement des cours si l'utilisateur peut sélectionner un consultant (Admin)
    if (consultantSelect) {
        consultantSelect.addEventListener('change', function() {
            const selectedConsultantId = this.value;
            // Réinitialisation du sélecteur de cours et de l'affichage de la date originale
            courseSelect.innerHTML = '<option value="">-- Sélectionner une séance --</option>';
            originalDateDisplay.value = 'Sélectionnez une séance';

            if (selectedConsultantId) {
                courseSelect.innerHTML = '<option value="">Chargement...</option>';
                courseSelect.disabled = true;

                // Appel AJAX pour récupérer les cours du consultant sélectionné
                fetch('/course-reschedules/get-courses-by-consultant?consultant_id=' + selectedConsultantId)
                    .then(response => response.json())
                    .then(courses => {
                        courseSelect.innerHTML = '<option value="">-- Sélectionner une séance --</option>';
                        courseSelect.disabled = false;

                        if (courses.length === 0) {
                            courseSelect.innerHTML = '<option value="">Aucun séance trouvé</option>';
                            return;
                        }

                        // Remplissage du sélecteur de cours avec les données reçues
                        courses.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.id;
                            option.textContent = course.title + ' (' + new Date(course.course_date).toLocaleDateString() + ')';
                            option.dataset.originalDate = new Date(course.course_date).toISOString().split('T')[0];
                            option.dataset.startTime = course.start_time;
                            option.dataset.endTime = course.end_time;
                            courseSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.log('Erreur:', error);
                        courseSelect.innerHTML = '<option value="">Erreur de chargement des séances</option>';
                        courseSelect.disabled = false;
                    });
            } else {
                courseSelect.disabled = false;
            }
        });
    }

    // Code de validation pour empêcher la soumission si le cours est pour aujourd'hui
    document.querySelector('form').addEventListener('submit', function(event) {
        const selectedOption = courseSelect.options[courseSelect.selectedIndex];
        
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Réinitialisation de l'heure pour la comparaison

        if (selectedOption && selectedOption.value) {
            const originalDate = new Date(selectedOption.dataset.originalDate);
            originalDate.setHours(0, 0, 0, 0); // Réinitialisation de l'heure pour la comparaison

            // Si la date originale est égale à la date d'aujourd'hui
            if (originalDate.getTime() === today.getTime()) {
                const errorMessage = document.getElementById('todayCourseError');
                errorMessage.classList.remove('d-none'); // Afficher l'alerte
                event.preventDefault(); // Empêcher l'envoi du formulaire
            }
        }
    });

    // Ajout de l'effet "ripple" aux boutons (Animation au clic)
    document.querySelectorAll('.btn-modern').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // ... le code dyal ripple (Création et animation de l'onde au clic) ...
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
                background: rgba(255, 255, 255, 0.5);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
                z-index: 1;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
});
</script>
@endpush