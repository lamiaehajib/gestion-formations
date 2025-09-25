@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-lg border-0 animate__animated animate__fadeIn">
                <div class="card-header bg-primary-gradient text-white text-center py-4 rounded-top-card">
                    <h2 class="mb-0 fw-bold"><i class="fas fa-user-circle me-2"></i> Détails de l'utilisateur</h2>
                </div>
                <div class="card-body p-4 text-center">
                    <div class="mb-4">
                        @if($user->avatar)
                            <img src="{{ asset('storage/' . $user->avatar) }}" 
                                 alt="Avatar de {{ $user->name }}" 
                                 class="img-fluid rounded-circle shadow-lg avatar-profile animate__animated animate__zoomIn" 
                                 style="width: 150px; height: 150px; object-fit: cover; border: 4px solid white;">
                        @else
                            <div class="bg-primary-gradient rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold shadow-lg animate__animated animate__zoomIn" 
                                 style="width: 150px; height: 150px; font-size: 4em; border: 4px solid white;">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <h3 class="mt-3 mb-1 fw-bold text-dark">{{ $user->name }}</h3>
                        <p class="text-muted"><i class="fas fa-envelope me-1"></i> {{ $user->email }}</p>
                    </div>

                    <hr class="my-4">

                    <div class="row text-start details-grid animate__animated animate__fadeInUp">
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary fw-bold mb-1"><i class="fas fa-phone-alt me-2"></i> Téléphone :</h6>
                            <p class="text-dark">{{ $user->phone ?? 'Non spécifié' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary fw-bold mb-1"><i class="fas fa-circle-notch me-2"></i> Statut :</h6>
                            <p class="mb-0">
                                @if($user->status === 'active')
                                    <span class="badge bg-success status-badge-lg animate__animated animate__pulse">Actif</span>
                                @else
                                    <span class="badge bg-danger status-badge-lg animate__animated animate__pulse">Inactif</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-12 mb-3">
                            <h6 class="text-primary fw-bold mb-1"><i class="fas fa-user-tag me-2"></i> Rôles :</h6>
                            <p class="mb-0">
                                @if($user->getRoleNames()->count() > 0)
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="badge bg-info role-badge-lg me-1 animate__animated animate__fadeIn">{{ $role }}</span>
                                    @endforeach
                                @else
                                    <span class="badge bg-secondary role-badge-lg">Aucun rôle</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary fw-bold mb-1"><i class="fas fa-calendar-alt me-2"></i> Date de création :</h6>
                            <p class="text-dark">{{ $user->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <h6 class="text-primary fw-bold mb-1"><i class="fas fa-sync-alt me-2"></i> Dernière mise à jour :</h6>
                            <p class="text-dark">{{ $user->updated_at->format('d/m/Y H:i') }}</p>
                        </div>
                    </div>

                    {{-- Nouveau Bloc pour les Documents --}}
                    @if($user->documents && count($user->documents) > 0)
                    <hr class="my-4">
                    <div class="animate__animated animate__fadeInUp">
                        <h4 class="text-primary fw-bold mb-3"><i class="fas fa-folder-open me-2"></i> Documents</h4>
                        <ul class="list-group list-group-flush text-start">
                            @foreach($user->documents as $document)
                                @if(isset($document['path']))
                                    <li class="list-group-item d-flex justify-content-between align-items-center document-item">
                                        <div class="d-flex align-items-center">
                                            @php
                                                $icon = 'fas fa-file-alt';
                                                if (isset($document['type'])) {
                                                    if ($document['type'] == 'pdf') $icon = 'fas fa-file-pdf text-danger';
                                                    else if (in_array($document['type'], ['jpg', 'jpeg', 'png', 'gif'])) $icon = 'fas fa-file-image text-info';
                                                    else if (in_array($document['type'], ['doc', 'docx'])) $icon = 'fas fa-file-word text-primary';
                                                }
                                            @endphp
                                            <i class="{{ $icon }} me-2 document-icon"></i>
                                            <span class="fw-medium">{{ $document['name'] ?? 'Document sans nom' }}</span>
                                        </div>
                                        <a href="{{ asset('storage/' . $document['path']) }}" 
                                           target="_blank" 
                                           class="btn btn-sm btn-outline-secondary">
                                            <i class="fas fa-eye me-1"></i> Voir
                                        </a>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                    @else
                    <hr class="my-4">
                    <div class="alert alert-info text-center animate__animated animate__fadeIn">
                        <i class="fas fa-info-circle me-2"></i> Aucun document n'a été téléchargé pour cet utilisateur.
                    </div>
                    @endif
                </div>
                <div class="card">
    <div class="card-header">
        Détails de l'utilisateur
    </div>
    <div class="card-body">
        <p><strong>Dernière connexion :</strong> {{ optional($user->last_login_at)->format('d/m/Y H:i') ?? 'N/A' }}</p>
        <p><strong>Nombre de connexions :</strong> {{ $user->login_count ?? 0 }}</p>
    </div>
</div>
                <div class="card-footer d-flex justify-content-between p-3 bg-light-subtle rounded-bottom-card">
                    <a href="{{ route('users.index') }}" class="btn btn-outline-primary animated-button-outline">
                        <i class="fas fa-arrow-alt-circle-left me-2"></i> Retour à la liste
                    </a>
                    <a href="{{ route('users.edit', $user->id) }}" class="btn btn-edit-gradient animated-button">
                        <i class="fas fa-edit me-2"></i> Modifier l'utilisateur
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
{{-- Animate.css pour des animations plus riches --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    :root {
        /* New Color Palette */
        --dark-red: #D32F2F;
        --crimson-pink: #C2185B;
        --light-red-accent: #EF4444;
        /* Mapping to existing variables for global use */
        --primary-color: var(--dark-red);
        --primary-gradient-start: var(--light-red-accent); /* Using light red for gradient start */
        --primary-gradient-end: var(--dark-red); /* Using dark red for gradient end */
        --success-color: #28a745; /* Green, unchanged */
        --danger-color: var(--dark-red); /* Dark red for danger */
        --warning-color: #ffc107; /* Yellow, keeping for general warnings, not for the button */
        --edit-gradient-start: var(--light-red-accent); /* New gradient for edit button */
        --edit-gradient-end: var(--crimson-pink); /* New gradient for edit button */
        --info-color: var(--crimson-pink); /* Crimson pink for info/roles */
    }

    body {
        background-color: #f8f9fa;
        font-family: 'Poppins', sans-serif;
    }

    .card {
        border: none;
        border-radius: 1rem;
        box-shadow: 0 0.5rem 1.5rem rgba(0, 0, 0, 0.08);
        overflow: hidden;
    }

    .rounded-top-card {
        border-top-left-radius: 1rem !important;
        border-top-right-radius: 1rem !important;
    }
    .rounded-bottom-card {
        border-bottom-left-radius: 1rem !important;
        border-bottom-right-radius: 1rem !important;
    }

    .card-header {
        border-bottom: none;
    }

    .bg-primary-gradient {
        background: linear-gradient(to right, var(--primary-gradient-start), var(--primary-gradient-end));
    }

    /* New style for the Edit button */
    .btn-edit-gradient {
        background: linear-gradient(to right, var(--edit-gradient-start), var(--edit-gradient-end));
        border: none;
        transition: all 0.3s ease;
        color: white;
    }
    .btn-edit-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        color: white;
    }

    .animated-button {
        overflow: hidden;
        position: relative;
        z-index: 1;
        padding: 0.75rem 1.5rem; /* Ensure consistent padding */
        border-radius: 0.5rem; /* Slightly less rounded than full for better aesthetics */
    }

    .animated-button::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background-color: rgba(255,255,255,0.1);
        border-radius: 0.5rem; /* Match button border-radius */
        z-index: -2;
    }

    .animated-button::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0%;
        height: 100%;
        background-color: rgba(255,255,255,0.3);
        transition: all 0.3s;
        border-radius: 0.5rem; /* Match button border-radius */
        z-index: -1;
    }

    .animated-button:hover::before {
        width: 100%;
    }

    /* Updated outline button to use --primary-color */
    .btn-outline-primary {
        border-color: var(--primary-color) !important;
        color: var(--primary-color) !important;
    }

    .animated-button-outline {
        overflow: hidden;
        position: relative;
        z-index: 1;
        padding: 0.75rem 1.5rem; /* Ensure consistent padding */
        border-radius: 0.5rem; /* Slightly less rounded than full for better aesthetics */
    }

    .animated-button-outline::before {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 0%;
        height: 100%;
        background-color: var(--primary-color);
        transition: all 0.3s;
        border-radius: 0.5rem; /* Match button border-radius */
        z-index: -1;
    }

    .animated-button-outline:hover::before {
        width: 100%;
    }

    .animated-button-outline:hover {
        color: white !important;
    }

    .avatar-profile {
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .avatar-profile:hover {
        transform: scale(1.05);
        box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.2) !important;
    }

    .details-grid h6 {
        font-size: 1.05em;
        margin-bottom: 0.25rem;
    }
    .details-grid p {
        font-size: 1.1em;
        font-weight: 500;
        color: #333;
    }

    .status-badge-lg, .role-badge-lg {
        padding: 0.6em 1em;
        font-size: 0.9em;
        border-radius: 0.5rem;
        font-weight: 600;
        letter-spacing: 0.03em;
    }

    .status-badge-lg.bg-success { background-color: var(--success-color) !important; }
    .status-badge-lg.bg-danger { background-color: var(--danger-color) !important; }
    .role-badge-lg.bg-info { background-color: var(--info-color) !important; }
    .role-badge-lg.bg-secondary { background-color: #6c757d !important; }
    
    .document-item {
        border-bottom: 1px solid #eee;
        transition: all 0.2s ease;
    }
    .document-item:hover {
        background-color: #f8f9fa;
    }
    .document-icon {
        font-size: 1.25em;
    }

    a.btn.btn-sm.btn-outline-secondary {
    background-color: forestgreen;
}
</style>
@endpush

@push('scripts')
{{-- Inclure Font Awesome pour les icônes --}}
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Simple animation trigger for content on load
        // Already handled by animate__animated classes
    });
</script>
@endpush