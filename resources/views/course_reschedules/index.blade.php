@extends('layouts.app')

@section('title', 'Reprogrammations des Cours')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
<style>
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
        background: linear-gradient(135deg, #f8f9ff 0%, #fff0f5 100%);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
    }
    
    .card-modern {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        background: rgba(255,255,255,0.95);
    }
    
    .card-modern:hover {
        transform: translateY(-8px) scale(1.02);
        box-shadow: 0 25px 50px rgba(211, 47, 47, 0.15);
    }
    
    .gradient-header {
        background: var(--gradient-primary);
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 25px 30px;
        position: relative;
        overflow: hidden;
    }
    
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
        border-radius: 30px;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .btn-create {
        background: var(--gradient-secondary);
        color: white;
        box-shadow: 0 8px 25px var(--shadow-red);
    }
    
    .btn-create:hover {
        transform: translateY(-3px) scale(1.05);
        box-shadow: 0 15px 35px var(--shadow-red);
        color: white;
    }
    
    .btn-create::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }
    
    .btn-create:hover::before {
        left: 100%;
    }
    
    .stats-card {
        background: var(--gradient-primary);
        color: white;
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 20px;
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 10px 30px var(--shadow-red);
    }
    
    .stats-card::before {
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
        50% { transform: scale(1.1); opacity: 1; }
    }
    
    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 20px 40px var(--shadow-red);
    }
    
    .stats-card.variant-1 {
        background: linear-gradient(135deg, #C2185B 0%, #8E24AA 100%);
    }
    
    .stats-card.variant-2 {
        background: linear-gradient(135deg, #ef4444 0%, #f97316 100%);
    }
    
    .stats-card.variant-3 {
        background: linear-gradient(135deg, #D32F2F 0%, #1976D2 100%);
    }
    
    .filter-card {
        background: var(--gradient-light);
        border-radius: 20px;
        border: 2px solid rgba(211,47,47,0.1);
        backdrop-filter: blur(10px);
        transition: all 0.3s ease;
    }
    
    .filter-card:hover {
        border-color: rgba(211,47,47,0.3);
        box-shadow: 0 10px 30px rgba(211,47,47,0.1);
    }
    
    .form-control, .form-select {
        border-radius: 15px;
        border: 2px solid rgba(211,47,47,0.1);
        padding: 12px 18px;
        font-weight: 500;
        transition: all 0.3s ease;
    }
    
    .form-control:focus, .form-select:focus {
        border-color: var(--primary-red);
        box-shadow: 0 0 20px rgba(211,47,47,0.2);
    }
    
    .table-modern {
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        backdrop-filter: blur(10px);
    }
    
    .table-modern th {
        background: var(--gradient-primary);
        color: white;
        font-weight: 700;
        border: none;
        padding: 25px 20px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-size: 13px;
        position: relative;
    }
    
    .table-modern th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 3px;
        background: linear-gradient(90deg, transparent 0%, rgba(255,255,255,0.3) 50%, transparent 100%);
    }
    
    .table-modern td {
        padding: 20px;
        vertical-align: middle;
        border-color: rgba(211,47,47,0.1);
        background: rgba(255,255,255,0.8);
        transition: all 0.3s ease;
    }
    
    .table-modern tbody tr:hover td {
        background: var(--gradient-light);
        transform: scale(1.02);
    }
    
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
    
    .badge-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    
    .bg-danger.badge-modern {
        background: var(--gradient-secondary) !important;
        box-shadow: 0 4px 15px var(--shadow-red);
    }
    
    .bg-success.badge-modern {
        background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%) !important;
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
    }
    
    .search-container {
        position: relative;
    }
    
    .search-container input {
        border-radius: 30px;
        padding-left: 55px;
        border: 2px solid rgba(211,47,47,0.2);
        transition: all 0.3s ease;
        background: rgba(255,255,255,0.9);
    }
    
    .search-container input:focus {
        border-color: var(--primary-red);
        box-shadow: 0 0 25px var(--shadow-red);
    }
    
    .search-container .search-icon {
        position: absolute;
        left: 20px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-red);
        z-index: 10;
    }
    
    .action-buttons .btn {
        margin: 0 3px;
        border-radius: 12px;
        width: 40px;
        height: 40px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }
    
    .action-buttons .btn:hover {
        transform: translateY(-2px) scale(1.1);
    }
    
    .btn-outline-info{
        background: linear-gradient(135deg, #17a2b8 0%, #20c997 100%);
        border-color: transparent;
        box-shadow: 0 8px 20px rgba(23, 162, 184, 0.3);
    }
    
    .btn-outline-warning{
        background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);
        border-color: transparent;
        box-shadow: 0 8px 20px rgba(255, 193, 7, 0.3);
    }
    
    .btn-outline-danger{
        background: var(--gradient-secondary);
        border-color: transparent;
        box-shadow: 0 8px 20px var(--shadow-red);
    }
    
    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #6c757d;
        background: var(--gradient-light);
        border-radius: 20px;
        margin: 20px;
    }
    
    .empty-state i {
        font-size: 100px;
        margin-bottom: 30px;
        opacity: 0.6;
        color: var(--primary-red);
        animation: bounce 2s infinite;
    }
    
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-20px); }
        60% { transform: translateY(-10px); }
    }
    
    .pagination-modern .page-link {
        border-radius: 15px;
        margin: 0 5px;
        border: none;
        color: var(--primary-red);
        font-weight: 600;
        padding: 12px 18px;
        transition: all 0.3s ease;
    }
    
    .pagination-modern .page-link:hover {
        background: var(--gradient-light);
        transform: translateY(-2px);
        box-shadow: 0 5px 15px var(--shadow-red);
    }
    
    .pagination-modern .page-item.active .page-link {
        background: var(--gradient-primary);
        border: none;
        box-shadow: 0 5px 15px var(--shadow-red);
    }
    
    .page-header {
        background: var(--gradient-light);
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        border: 2px solid rgba(211,47,47,0.1);
    }
    
    .page-header h2 {
        color: var(--primary-red);
        font-weight: 800;
        margin-bottom: 10px;
    }
    
    .consultant-avatar {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        background: var(--gradient-primary);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 15px;
        box-shadow: 0 5px 15px var(--shadow-red);
        transition: all 0.3s ease;
    }
    
    .consultant-avatar:hover {
        transform: scale(1.1) rotate(10deg);
    }
    
    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    
    .modal-header {
        background: var(--gradient-primary);
        color: white;
        border-radius: 20px 20px 0 0;
        border: none;
    }
    
    .alert-warning {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 2px solid #ffc107;
        border-radius: 15px;
        color: #856404;
    }
    
    .text-truncate-custom {
        max-width: 200px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        display: inline-block;
        padding: 8px 12px;
        background: rgba(211,47,47,0.1);
        border-radius: 10px;
        font-weight: 500;
    }
    
    @media (max-width: 768px) {
        .card-modern:hover {
            transform: none;
        }
        
        .stats-card {
            margin-bottom: 15px;
        }
        
        .table-responsive {
            border-radius: 15px;
        }
    }
    

/* Stylisation améliorée du tableau */
.table-modern {
    border-radius: 20px;
    overflow: hidden; /* Assure l'application des coins arrondis */
    box-shadow: 0 10px 40px rgba(0,0,0,0.1); /* Ombre douce pour la profondeur */
    backdrop-filter: blur(10px); /* Maintient l'effet de verre dépoli */
    background-color: rgba(255, 255, 255, 0.9); /* Fond légèrement plus opaque pour les lignes */
    border: 1px solid rgba(211, 47, 47, 0.15); /* Bordure subtile pour la définition */
}

.table-modern th {
    background: var(--gradient-primary); /* Conserve votre dégradé existant */
    color: white;
    font-weight: 700;
    border: none;
    padding: 20px 25px; /* Rembourrage légèrement plus important pour un meilleur espacement */
    text-transform: uppercase;
    letter-spacing: 1px;
    font-size: 14px; /* Police légèrement plus grande pour les en-têtes */
    position: relative;
    /* Ajout d'une bordure inférieure pour séparer l'en-tête du corps, le rendant plus distinct */
    border-bottom: 3px solid rgba(255, 255, 255, 0.3); 
    text-align: left; /* Alignement du texte à gauche pour la cohérence */
}

/* Suppression de l'élément pseudo si vous utilisez une bordure inférieure directe */
.table-modern th::after {
    content: none; 
}

.table-modern td {
    padding: 18px 25px; /* Rembourrage ajusté pour les lignes */
    vertical-align: middle;
    border-color: rgba(211,47,47,0.1); /* Bordure plus claire pour la séparation entre les cellules */
    background: rgba(197, 77, 77, 0.95); /* Assure un fond de ligne clair */
    transition: all 0.3s ease-in-out; /* Transitions fluides */
}

.table-modern tbody tr:hover td {
    background: var(--gradient-light); /* Fond plus clair au survol */
    transform: none; /* Suppression de l'échelle au survol pour une meilleure lisibilité */
    box-shadow: inset 0 0 10px rgba(0, 0, 0, 0.05); /* Ombre intérieure subtile au survol */
}

/* Assure que les boutons d'action sont visuellement distincts dans le tableau */
.action-buttons .btn {
    border: 1px solid currentColor; /* Ajout d'une fine bordure utilisant la couleur du bouton */
}
a.btn.btn-outline-secondary.btn-modern {
    background: grey;
}
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2 class="fw-bold mb-1">
                            <i class="fas fa-calendar-alt me-3"></i>
                            Gestion des Reprogrammations des Cours
                        </h2>
                        <p class="text-muted mb-0 fs-6">Gérez et suivez toutes les demandes de reprogrammation des cours avec style</p>
                    </div>
               @if(isset($reschedule) && (Auth::user()->can('course-manage-all') || $reschedule->consultant_id == Auth::id()))
                    <a href="{{ route('course_reschedules.create') }}" class="btn btn-create btn-modern">
                        <i class="fas fa-plus me-2"></i>Nouvelle Reprogrammation
                    </a>
                     @endif
                    
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="stats-card">
                <div class="d-flex align-items-center">
                    <i class="fas fa-calendar-check fa-3x me-3"></i>
                    <div>
                        <h3 class="mb-0">{{ $reschedules->total() }}</h3>
                        <small class="opacity-75">Total des Reprogrammations</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card variant-1">
                <div class="d-flex align-items-center">
                    <i class="fas fa-clock fa-3x me-3"></i>
                    <div>
                        <h3 class="mb-0">{{ $reschedules->where('created_at', '>=', now()->startOfWeek())->count() }}</h3>
                        <small class="opacity-75">Cette Semaine</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card variant-2">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-tie fa-3x me-3"></i>
                    <div>
                        <h3 class="mb-0">{{ $consultants->count() }}</h3>
                        <small class="opacity-75">Consultants Actifs</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stats-card variant-3">
                <div class="d-flex align-items-center">
                    <i class="fas fa-chalkboard-teacher fa-3x me-3"></i>
                    <div>
                        <h3 class="mb-0">{{ $courses->count() }}</h3>
                        <small class="opacity-75">Total des Cours</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="card filter-card mb-4">
        <div class="card-body p-4">
            <form method="GET" action="{{ route('course_reschedules.index') }}" id="filterForm">
                <div class="row g-4">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">Cours</label>
                        <select name="course_id" class="form-select">
                            <option value="">Tous les Cours</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ request('course_id') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    @can('course-manage-all')
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-dark">Consultant</label>
                        <select name="consultant_id" class="form-select">
                            <option value="">Tous les Consultants</option>
                            @foreach($consultants as $consultant)
                                <option value="{{ $consultant->id }}" {{ request('consultant_id') == $consultant->id ? 'selected' : '' }}>
                                    {{ $consultant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endcan
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark">Date de Début</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-dark">Date de Fin</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <div class="d-flex gap-3 w-100">
                            <button type="submit" class="btn btn-create btn-modern flex-grow-1">
                                <i class="fas fa-search me-2"></i>Filtrer
                            </button>
                            <a href="{{ route('course_reschedules.index') }}" class="btn btn-outline-secondary btn-modern">
                                <i class="fas fa-undo"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-header gradient-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0 fw-bold">
                    <i class="fas fa-list me-3"></i>Historique des Reprogrammations
                </h5>
                <div class="d-flex align-items-center">
                    <small class="me-3 opacity-75">
                        Affichage de {{ $reschedules->firstItem() ?? 0 }} à {{ $reschedules->lastItem() ?? 0 }} 
                        sur {{ $reschedules->total() }} résultats
                    </small>
                </div>
            </div>
        </div>
        
        <div class="card-body p-0">
            @if($reschedules->count() > 0)
                <div class="table-responsive">
                    <table class="table table-modern mb-0">
                        <thead>
                            <tr>
                                <th>Cours</th>
                                <th>Consultant</th>
                                <th>Date Initiale</th>
                                <th>Nouvelle Date</th>
                                <th>Raison</th>
                                <th>Reprogrammé le</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($reschedules as $reschedule)
                            <tr>
                                <td>
                                    <div>
                                        <strong class="text-dark">{{ $reschedule->course->title }}</strong>
                                        <br>
                                        <small class="text-muted">ID: #{{ $reschedule->course->id }}</small>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="consultant-avatar">
                                            <i class="fas fa-user text-white"></i>
                                        </div>
                                        <div>
                                            <strong class="text-dark">{{ $reschedule->consultant->name }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $reschedule->consultant->email }}</small>
                                        </div>
                                    </div>
                                </td>
                               <td>
    <span class="badge bg-danger badge-modern">
        <i class="fas fa-calendar-times me-1"></i>
        {{ 
            \Carbon\Carbon::parse($reschedule->course->course_date)
                ->setTimeFromTimeString($reschedule->course->start_time)
                ->format('d/m/Y H:i') 
        }}
    </span>
</td>
                                <td>
                                    <span class="badge bg-success badge-modern">
                                        <i class="fas fa-calendar-check me-1"></i>
                                        {{ \Carbon\Carbon::parse($reschedule->new_date)->format('d/m/Y H:i') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="text-truncate-custom" 
                                            data-bs-toggle="tooltip" title="{{ $reschedule->reason }}">
                                        {{ $reschedule->reason ? Str::limit($reschedule->reason, 50) : 'Aucune raison fournie' }}
                                    </span>
                                </td>
                                <td>
                                    <small class="text-muted fw-500">
                                        {{ $reschedule->created_at->format('d/m/Y H:i') }}
                                        <br>
                                        <span class="text-primary">{{ $reschedule->created_at->diffForHumans() }}</span>
                                    </small>
                                </td>
                                <td>
                                    <div class="action-buttons d-flex justify-content-center">
                                        <a href="{{ route('course_reschedules.show', $reschedule) }}" 
                                           class="btn btn-sm btn-outline-info" data-bs-toggle="tooltip" title="Voir les Détails">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                      
                                            @if(Auth::user()->can('course-manage-all') || $reschedule->consultant_id == Auth::id())
                                            <a href="{{ route('course_reschedules.edit', $reschedule) }}" 
                                               class="btn btn-sm btn-outline-warning" data-bs-toggle="tooltip" title="Modifier">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @endif
                                    
                                        
                                      
                                            @if(Auth::user()->can('course-manage-all') || $reschedule->consultant_id == Auth::id())
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDelete({{ $reschedule->id }})" 
                                                    data-bs-toggle="tooltip" title="Supprimer">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            @endif
                                       
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="empty-state">
                    <i class="fas fa-calendar-alt"></i>
                    <h4 class="fw-bold">Aucune Reprogrammation Trouvée</h4>
                    <p class="text-muted">Aucune reprogrammation de cours ne correspond à vos critères.</p>
                    @can('course-edit')
                    <a href="{{ route('course_reschedules.create') }}" class="btn btn-create btn-modern mt-3">
                        <i class="fas fa-plus me-2"></i>Créer la Première Reprogrammation
                    </a>
                    @endcan
                </div>
            @endif
        </div>
        
        @if($reschedules->hasPages())
        <div class="card-footer" style="background: var(--gradient-light); border-radius: 0 0 20px 20px;">
            <div class="d-flex justify-content-center py-3">
                {{ $reschedules->appends(request()->query())->links('pagination::bootstrap-4', ['class' => 'pagination-modern']) }}
            </div>
        </div>
        @endif
    </div>
</div>

<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmer la Suppression
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">Êtes-vous sûr de vouloir supprimer cet enregistrement de reprogrammation ?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Avertissement :</strong> Cette action est irréversible !
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Annuler
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-modern">
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialisation des infobulles
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Initialisation des sélecteurs de date avec style personnalisé
    flatpickr('input[type="date"]', {
        dateFormat: 'Y-m-d',
        allowInput: true,
        theme: 'material_red',
        animate: true,
        altInput: true,
        altFormat: 'j F Y',
        locale: {
            firstDayOfWeek: 1
        }
    });
    
    // Ajout de défilement fluide et d'animations d'entrée
    const cards = document.querySelectorAll('.card-modern, .stats-card');
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.style.opacity = '1';
                entry.target.style.transform = 'translateY(0)';
            }
        });
    }, { threshold: 0.1 });
    
    cards.forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(30px)';
        card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
        observer.observe(card);
    });
    
    // Ajout d'animation de chargement aux boutons
    document.querySelectorAll('.btn-modern').forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.type === 'submit') {
                const originalText = this.innerHTML;
                this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Chargement...';
                this.disabled = true;
                
                setTimeout(() => {
                    this.innerHTML = originalText;
                    this.disabled = false;
                }, 2000); // Réactiver le bouton après 2 secondes (ajustez selon les besoins)
            }
        });
    });
    
    // Effets de survol améliorés pour les lignes du tableau
    document.querySelectorAll('.table-modern tbody tr').forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.01)';
            this.style.zIndex = '10';
            this.style.position = 'relative';
        });
        
        row.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1)';
            this.style.zIndex = 'auto';
        });
    });
});

function confirmDelete(rescheduleId) {
    const form = document.getElementById('deleteForm');
    form.action = `/course-reschedules/${rescheduleId}`; // Assurez-vous que cette route est correcte
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();
    
    // Ajout de l'animation de secousse à la modale
    const modalDialog = document.querySelector('#deleteModal .modal-dialog');
    modalDialog.style.animation = 'shake 0.5s ease-in-out';
    setTimeout(() => {
        modalDialog.style.animation = ''; // Supprimer l'animation après son exécution
    }, 500);
}

// Soumission automatique du formulaire lors du changement de filtre avec état de chargement
document.querySelectorAll('#filterForm select, #filterForm input[type="date"]').forEach(input => {
    input.addEventListener('change', function() {
        // Ajout d'une superposition de chargement
        const filterCard = document.querySelector('.filter-card');
        const overlay = document.createElement('div');
        overlay.style.cssText = `
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(211, 47, 47, 0.1);
            backdrop-filter: blur(2px);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1000;
            border-radius: 20px;
        `;
        overlay.innerHTML = '<i class="fas fa-spinner fa-spin fa-2x" style="color: #D32F2F;"></i>';
        
        filterCard.style.position = 'relative';
        filterCard.appendChild(overlay);
        
        // Soumettre le formulaire
        document.getElementById('filterForm').submit();
    });
});

// Ajout d'animations personnalisées par keyframes
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
    
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
    
    @keyframes slideInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }
    
    .fade-in-up {
        animation: fadeInUp 0.6s ease forwards;
    }
    
    .slide-in-right {
        animation: slideInRight 0.6s ease forwards;
    }
`;
document.head.appendChild(style);

// Ajout d'animations décalées pour les cartes de statistiques
setTimeout(() => {
    document.querySelectorAll('.stats-card').forEach((card, index) => {
        setTimeout(() => {
            card.classList.add('fade-in-up');
        }, index * 100);
    });
}, 100);

// Ajout d'un effet de parallaxe aux en-têtes dégradés
window.addEventListener('scroll', () => {
    const scrolled = window.pageYOffset;
    const gradientHeaders = document.querySelectorAll('.gradient-header');
    
    gradientHeaders.forEach(header => {
        const speed = 0.05; // Vitesse réduite pour un effet subtil
        header.style.transform = `translateY(${scrolled * speed}px)`;
    });
});

// Fonctionnalité de recherche améliorée (appliquée aux entrées de filtre similaires à une recherche)
const searchInputs = document.querySelectorAll('input[type="search"], .search-container input, .form-control');
searchInputs.forEach(input => {
    input.addEventListener('focus', function() {
        if (this.parentElement.classList.contains('search-container')) {
            this.parentElement.style.transform = 'scale(1.02)';
            this.parentElement.style.zIndex = '10';
        } else {
            this.style.transform = 'scale(1.01)'; // Léger agrandissement pour les autres entrées
        }
    });
    
    input.addEventListener('blur', function() {
        if (this.parentElement.classList.contains('search-container')) {
            this.parentElement.style.transform = 'scale(1)';
            this.parentElement.style.zIndex = 'auto';
        } else {
            this.style.transform = 'scale(1)';
        }
    });
});

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
        `;
        
        this.style.position = 'relative';
        this.style.overflow = 'hidden';
        this.appendChild(ripple);
        
        setTimeout(() => ripple.remove(), 600);
    });
});

// Ajout de l'animation d'ondulation
const rippleStyle = document.createElement('style');
rippleStyle.textContent = `
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }
`;
document.head.appendChild(rippleStyle);
</script>
@endpush