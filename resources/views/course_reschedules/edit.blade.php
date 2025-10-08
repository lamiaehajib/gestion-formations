@extends('layouts.app')

@section('title', 'Modifier le report de séance')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<style>
    /* Consistent Color Variables from Index Page */
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
        background: linear-gradient(135deg, #f8f9ff 0%, #fff0f5 100%); /* Consistent background */
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #333; /* Default text color */
    }

    .card-modern {
        border: none;
        border-radius: 20px; /* Consistent border-radius */
        box-shadow: 0 15px 35px rgba(0,0,0,0.08); /* Consistent shadow */
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        background: rgba(255,255,255,0.95);
    }

    /* Gradient header (for modal or specific card headers) */
    .gradient-header {
        background: var(--gradient-primary); /* Use primary gradient */
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 25px 30px;
        position: relative;
        overflow: hidden;
    }

    /* Header ::before animation remains the same */
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

    .btn-modern {
        border-radius: 30px; /* Consistent button radius */
        padding: 12px 30px;
        font-weight: 600;
        font-size: 14px; /* Consistent font size */
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Primary action button (Submit/Update) */
    .btn-primary-modern {
        background: var(--gradient-secondary); /* Use the secondary gradient for primary action */
        color: white;
        box-shadow: 0 8px 25px var(--shadow-red); /* Consistent shadow for primary buttons */
    }

    .btn-primary-modern:hover {
        transform: translateY(-3px) scale(1.05); /* Consistent hover effect */
        box-shadow: 0 15px 35px var(--shadow-red);
        color: white;
    }

    .btn-primary-modern::before {
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

    /* Secondary action button (Back to List) */
    .btn-secondary-modern {
        background-color: #6c757d; /* Standard Bootstrap secondary color for consistency */
        color: white;
        box-shadow: 0 5px 15px rgba(108, 117, 125, 0.2); /* Subtle shadow for secondary */
    }

    .btn-secondary-modern:hover {
        background-color: #5a6268; /* Darker on hover */
        color: white;
        transform: translateY(-2px); /* Subtle lift on hover */
    }

    /* Outline secondary for "Reset Form" */
    .btn-outline-secondary.btn-modern {
        border: 2px solid var(--secondary-pink); /* Use a primary theme color for outline */
        color: var(--secondary-pink);
        background-color: transparent;
        box-shadow: none; /* No initial shadow for outline */
    }

    .btn-outline-secondary.btn-modern:hover {
        background: var(--secondary-pink); /* Fill with color on hover */
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px var(--shadow-pink); /* Add shadow on hover */
    }

    .form-label {
        font-weight: 700;
        color: #4a4a4a; /* Darker text for labels */
        margin-bottom: 8px;
    }

    .form-control-modern, .form-select-modern {
        border-radius: 15px; /* Consistent with index page inputs */
        border: 2px solid rgba(211,47,47,0.1); /* Consistent border */
        padding: 12px 18px; /* Consistent padding */
        font-weight: 500;
        transition: all 0.3s ease;
        background-color: #ffffff;
    }

    .form-control-modern:focus, .form-select-modern:focus {
        border-color: var(--primary-red); /* Consistent focus color */
        box-shadow: 0 0 20px rgba(211,47,47,0.2); /* Consistent focus shadow */
    }

    .form-text {
        font-size: 0.85em;
        color: #777;
        margin-top: 5px;
    }

    /* Info boxes for displaying current data */
    .info-box {
        background: var(--gradient-light); /* Use light gradient for info boxes */
        border: 2px solid rgba(211,47,47,0.1);
        border-radius: 15px; /* Consistent border-radius */
        padding: 20px 25px; /* More padding */
        margin-bottom: 20px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.05); /* Subtle shadow */
    }
    .info-box strong {
        color: var(--primary-red); /* Strong text in info box uses primary red */
        font-weight: 700;
        display: block; /* Ensures strong takes full width */
        margin-bottom: 5px;
        font-size: 0.9em; /* Slightly smaller for labels */
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .info-box span {
        color: #555; /* Value text color */
        font-size: 1.1em; /* Larger value text */
        font-weight: 600;
    }

    /* Badge styles (copied from index for consistency) */
    .badge-modern {
        border-radius: 20px;
        padding: 10px 18px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }
    .bg-danger.badge-modern {
        background: var(--gradient-secondary) !important;
        box-shadow: 0 4px 15px var(--shadow-red);
    }
    .bg-success.badge-modern {
        background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%) !important;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }

    /* Alert styles (adjusting existing ones to fit theme) */
    .alert-danger {
        background: linear-gradient(135deg, #fde7e7 0%, #fcdede 100%); /* Lighter, themed background */
        border: 1px solid #e57373; /* Slightly darker red border */
        border-radius: 15px; /* Consistent with other rounded elements */
        color: #c62828; /* Stronger red text */
        padding: 15px 20px;
    }

    /* Page header specific to this "Edit" page */
    .py-4 {
        padding-top: 2.5rem !important;
        padding-bottom: 2.5rem !important;
    }

    .mb-4 {
        margin-bottom: 2.5rem !important;
    }

    h2 {
        font-size: 2rem;
        color: var(--primary-red); /* Main heading color from index theme */
    }

    .text-muted {
        color: #888 !important;
    }

    .fas.fa-calendar-alt { /* Icon in page header */
        color: var(--primary-red) !important; /* Icon color consistent with primary theme */
    }

    /* Keyframes for button ripple (if not already global) */
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* Basic entrance animation for the card */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .card-modern {
        animation: fadeIn 0.6s ease-out forwards;
    }

    .btn-outline-secondary.btn-modern {
    border: 2px solid var(--secondary-pink);
    color: var(--secondary-pink) !important;
    background-color: transparent;
    box-shadow: none;
}

a.btn.btn-secondary-modern.btn-modern {
    color: white !important; /* Changed to white for better contrast on secondary button style */
    background-color: #6c757d; /* Ensuring consistency with secondary color */
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-calendar-alt me-3"></i>
                        Modifier le report de séance
                    </h2>
                    <p class="text-muted mb-0">Modifier les détails du report pour la séance **{{ $reschedule->course->title }}**</p>
                </div>
                <a href="{{ route('course_reschedules.index') }}" class="btn btn-secondary-modern btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-header gradient-header">
            <h5 class="mb-0 fw-bold">Détails du Report</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Oups !</strong> Il y a eu des problèmes avec votre saisie.
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <div class="row">
                <div class="col-md-6">
                    <div class="info-box">
                        <p class="mb-2"><strong>Titre de la Séance :</strong> <span class="d-block">{{ $reschedule->course->title }}</span></p>
                        <p class="mb-2"><strong>Consultant :</strong> <span class="d-block">{{ $reschedule->consultant->name }}</span></p>
                        <p class="mb-0"><strong>Date Initiale :</strong> <span class="badge bg-danger badge-modern">
                            <i class="fas fa-calendar-times me-1"></i>
                             {{ 
            \Carbon\Carbon::parse($reschedule->course->course_date)
                ->setTimeFromTimeString($reschedule->course->start_time)
                ->format('d/m/Y H:i') 
        }}
                        </span></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <p class="mb-2"><strong>Nouvelle Date de Report Actuelle :</strong> <span class="badge bg-success badge-modern">
                            <i class="fas fa-calendar-check me-1"></i>
                            {{ \Carbon\Carbon::parse($reschedule->new_date)->format('d/m/Y H:i') }}
                        </span></p>
                        <p class="mb-2"><strong>Reporté par :</strong> <span class="d-block">{{ $reschedule->consultant->name }}</span></p>
                        <p class="mb-0"><strong>Date du Report :</strong> <span class="d-block text-primary fw-bold">{{ $reschedule->created_at->format('d/m/Y H:i') }} ({{ $reschedule->created_at->diffForHumans() }})</span></p>
                    </div>
                </div>
            </div>

            <hr class="my-4"> {{-- Added more vertical space for the separator --}}

            <form action="{{ route('course_reschedules.update', $reschedule->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="new_date" class="form-label fw-bold">Nouvelle Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="new_date" id="new_date" 
                               class="form-control form-control-modern @error('new_date') is-invalid @enderror" 
                               value="{{ old('new_date', \Carbon\Carbon::parse($reschedule->new_date)->format('Y-m-d\TH:i')) }}" required>
                        @error('new_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Sélectionnez la nouvelle date et heure pour la séance.</small>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="reason" class="form-label fw-bold">Raison du Report</label>
                    <textarea name="reason" id="reason" rows="4" 
                                 class="form-control form-control-modern @error('reason') is-invalid @enderror" 
                                 placeholder="Fournir une brève raison pour le report (facultatif)">{{ old('reason', $reschedule->reason) }}</textarea>
                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-save me-2"></i>Mettre à jour le Report
                    </button>
                    <button type="reset" class="btn btn-outline-secondary btn-modern">
                        <i class="fas fa-redo-alt me-2"></i>Réinitialiser le Formulaire
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
    // Initialize Flatpickr for the new date input
    flatpickr("#new_date", {
        dateFormat: "Y-m-d H:i", // Include time for more precision
        enableTime: true,
        altInput: true,
        altFormat: "F j, Y H:i",
        minDate: "today", // New date must be in the future (from current time)
        allowInput: true,
        theme: 'material_red', // Consistent theme with index page
        locale: {
            firstDayOfWeek: 1
        }
    });

    // Add ripple effect to buttons
    document.querySelectorAll('.btn-modern').forEach(btn => {
        btn.addEventListener('click', function(e) {
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
                z-index: 1; /* Ensure ripple is above button content but below text */
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Add basic entrance animation for the card
    const card = document.querySelector('.card-modern');
    if (card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.animation = 'fadeIn 0.6s ease-out forwards';
    }
});
</script>
@endpush