@extends('layouts.app')

@section('title', 'Gestion des Rappels de Paiement')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #D32F2F 0%, #C2185B 100%); border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-white">
                    <h2 class="mb-2 fw-bold">
                        <i class="fas fa-bell me-2"></i>Rappels de Paiement par Formation
                    </h2>
                    <p class="mb-0 opacity-90">
                        Sélectionnez une formation pour envoyer des rappels aux étudiants
                    </p>
                </div>
                <div class="text-white text-end">
                    <div class="fs-1 fw-bold">{{ $formations->count() }}</div>
                    <small class="opacity-90">Formations concernées</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body">
            <form method="GET" action="{{ route('payment-reminders.index') }}" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold">
                        <i class="fas fa-search text-primary me-2"></i>Rechercher une formation
                    </label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Titre de la formation..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-filter text-primary me-2"></i>Catégorie
                    </label>
                    <select name="category_id" class="form-select">
                        <option value="">Toutes les catégories</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Liste des formations -->
    <div class="row">
        @forelse($formations as $formation)
            <div class="col-xl-4 col-lg-6 mb-4">
                <div class="card border-0 shadow-sm h-100 formation-card" style="border-radius: 15px; transition: all 0.3s ease;">
                    <div class="card-body p-4">
                        <!-- Header -->
                        <div class="mb-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="mb-0 fw-bold text-dark">
                                    <i class="fas fa-graduation-cap text-primary me-2"></i>
                                    {{ $formation->title }}
                                </h5>
                                @if($formation->active_reminders_count > 0)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>{{ $formation->active_reminders_count }} actif(s)
                                    </span>
                                @endif
                            </div>
                            <p class="text-muted small mb-2">
                                <span class="badge bg-info">{{ $formation->category->name ?? 'N/A' }}</span>
                            </p>
                        </div>

                        <!-- Statistiques -->
                        <div class="row g-3 mb-3">
                            <div class="col-6">
                                <div class="p-3 rounded text-center" style="background-color: #fef2f2;">
                                    <div class="text-danger fw-bold fs-4">
                                        {{ $formation->students_with_debt }}
                                    </div>
                                    <small class="text-muted">Étudiants concernés</small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded text-center" style="background-color: #eff6ff;">
                                    <div class="text-primary fw-bold fs-5">
                                        {{ number_format($formation->total_remaining, 0) }} DH
                                    </div>
                                    <small class="text-muted">Total à payer</small>
                                </div>
                            </div>
                        </div>

                        <!-- Bouton -->
                        <a href="{{ route('payment-reminders.students', $formation->id) }}" 
                           class="btn text-white w-100" 
                           style="background: linear-gradient(135deg, #D32F2F, #C2185B);">
                            <i class="fas fa-users me-2"></i>Voir les étudiants
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                    <div class="card-body text-center py-5">
                        <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                        <h4 class="text-muted">Aucune formation trouvée</h4>
                        <p class="text-muted">
                            Aucune formation n'a d'étudiants avec des paiements en attente.
                        </p>
                    </div>
                </div>
            </div>
        @endforelse
    </div>
</div>

<style>
    .formation-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }
</style>
@endsection