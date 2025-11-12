@extends('layouts.app')
@section('title', 'Gestion des Utilisateurs')

@section('content')
    <div class="container-fluid px-4">
        <div class="row mb-4">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="page-title">
                            <i class="fas fa-users me-2"></i>
                            Gestion des Utilisateurs
                        </h1>
                        <p class="text-muted mb-0">Gérez tous vos utilisateurs en un seul endroit</p>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('users.corbeille') }}" class="btn btn-danger">
                            <i class="fa fa-trash"></i> Corbeille
                        </a>
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-floating">
                            <i class="fas fa-plus me-2"></i>
                            Nouvel Utilisateur
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-card-primary">
                    <div class="stats-card-body">
                        <div class="stats-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stats-content">
                            <h3 class="stats-number" data-count="{{ $stats['total'] }}">0</h3>
                            <p class="stats-label">Total Utilisateurs</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-card-success">
                    <div class="stats-card-body">
                        <div class="stats-icon">
                            <i class="fas fa-user-check"></i>
                        </div>
                        <div class="stats-content">
                            <h3 class="stats-number" data-count="{{ $stats['active'] }}">0</h3>
                            <p class="stats-label">Utilisateurs Actifs</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-card-warning">
                    <div class="stats-card-body">
                        <div class="stats-icon">
                            <i class="fas fa-user-clock"></i>
                        </div>
                        <div class="stats-content">
                            <h3 class="stats-number" data-count="{{ $stats['inactive'] }}">0</h3>
                            <p class="stats-label">Utilisateurs Inactifs</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-xl-3 col-md-6 mb-4">
                <div class="stats-card stats-card-info">
                    <div class="stats-card-body">
                        <div class="stats-icon">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="stats-content">
                            <h3 class="stats-number" data-count="{{ $stats['recent'] }}">0</h3>
                            <p class="stats-label">Nouveaux (30j)</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-card">
                    <div class="filter-header" onclick="toggleFilterCard()">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>
                            Filtres et Recherche
                        </h5>
                        <button class="btn btn-sm btn-outline-secondary toggle-filters" type="button">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="filter-body {{ request()->hasAny(['search', 'status', 'role']) ? 'show' : '' }}" id="filterCollapse">
                        <form id="filterForm">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Recherche</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" name="search" id="searchInput" class="form-control" 
                                               placeholder="Nom, email, téléphone..." value="{{ request('search') }}">
                                    </div>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Statut</label>
                                    <select name="status" id="statusFilter" class="form-select">
                                        <option value="">Tous les statuts</option>
                                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Actif</option>
                                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                        <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                    </select>
                                </div>
                                <div class="col-md-3 mb-3">
                                    <label class="form-label">Rôle</label>
                                    <select name="role" id="roleFilter" class="form-select">
                                        <option value="">Tous les rôles</option>
                                        @foreach($allRoles as $role)
                                            @php
                                                $roleValue = strtolower($role->name);
                                                if ($roleValue === 'admin') {
                                                    $displayValue = 'admis';
                                                } elseif ($role->name === 'Équipe Technique') {
                                                    $displayValue = 'equipe-technique';
                                                } else {
                                                    $displayValue = $roleValue;
                                                }
                                            @endphp
                                            <option value="{{ $displayValue }}" {{ request('role') == $displayValue ? 'selected' : '' }}>
                                                {{ $role->name === 'Admin' ? 'Admis' : $role->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end gap-2">
                                    <button type="button" class="btn btn-primary flex-fill" onclick="applyFilters()">
                                        <i class="fas fa-search me-2"></i>Filtrer
                                    </button>
                                    <button type="button" class="btn btn-outline-secondary flex-fill" onclick="resetFilters()">
                                        <i class="fas fa-undo me-2"></i>Reset
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Messages d'alerte -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show custom-alert" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show custom-alert" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <!-- Container pour les messages d'alerte AJAX -->
        <div id="ajax-alerts"></div>

        <!-- Table des Consultants -->
        <div class="row mb-5" id="consultantTableContainer" style="{{ request('role') && request('role') !== 'consultant' ? 'display: none;' : '' }}">
            <div class="col-12">
                <div class="table-card" data-group="consultant">
                    <div class="table-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user-tie me-2"></i>
                            Liste des Consultants
                        </h5>
                        <div class="loading-spinner" style="display: none;">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th><input class="form-check-input" type="checkbox" id="selectAllConsultants"></th>
                                    <th>Avatar</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Date création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="consultantsTableBody">
                                @forelse($consultantsPaginated as $user)
                                    @include('users.partials.user_row', ['user' => $user])
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            @include('users.partials.empty_state')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <div class="pagination-info">
                            @if($consultantsPaginated->count() > 0)
                                Affichage de {{ $consultantsPaginated->firstItem() }} à {{ $consultantsPaginated->lastItem() }} sur {{ $consultantsPaginated->total() }} résultats
                            @else
                                Aucun résultat trouvé
                            @endif
                        </div>
                        <div class="pagination-wrapper">
                            {{ $consultantsPaginated->appends(request()->except('page_consultant'))->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table des Étudiants -->
        <div class="row mb-5" id="etudiantTableContainer" style="{{ request('role') && request('role') !== 'etudiant' ? 'display: none;' : '' }}">
            <div class="col-12">
                <div class="table-card" data-group="etudiant">
                    <div class="table-header">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Liste des Étudiants
                        </h5>
                        <div class="loading-spinner" style="display: none;">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th><input class="form-check-input" type="checkbox" id="selectAllEtudiants"></th>
                                    <th>Avatar</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Date création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="etudiantsTableBody">
                                @forelse($etudiantsPaginated as $user)
                                    @include('users.partials.user_row', ['user' => $user])
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            @include('users.partials.empty_state')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <div class="pagination-info">
                            @if($etudiantsPaginated->count() > 0)
                                Affichage de {{ $etudiantsPaginated->firstItem() }} à {{ $etudiantsPaginated->lastItem() }} sur {{ $etudiantsPaginated->total() }} résultats
                            @else
                                Aucun résultat trouvé
                            @endif
                        </div>
                        <div class="pagination-wrapper">
                            {{ $etudiantsPaginated->appends(request()->except('page_etudiant'))->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table des Admis -->
        <div class="row mb-5" id="admisTableContainer" style="{{ request('role') && request('role') !== 'admis' ? 'display: none;' : '' }}">
            <div class="col-12">
                <div class="table-card" data-group="admis">
                    <div class="table-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user-shield me-2"></i>
                            Liste des Admis
                        </h5>
                        <div class="loading-spinner" style="display: none;">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th><input class="form-check-input" type="checkbox" id="selectAllAdmis"></th>
                                    <th>Avatar</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Date création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="admisTableBody">
                                @forelse($admisPaginated as $user)
                                    @include('users.partials.user_row', ['user' => $user])
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            @include('users.partials.empty_state')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <div class="pagination-info">
                            @if($admisPaginated->count() > 0)
                                Affichage de {{ $admisPaginated->firstItem() }} à {{ $admisPaginated->lastItem() }} sur {{ $admisPaginated->total() }} résultats
                            @else
                                Aucun résultat trouvé
                            @endif
                        </div>
                        <div class="pagination-wrapper">
                            {{ $admisPaginated->appends(request()->except('page_admin'))->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Table de l'Équipe Technique -->
        <div class="row mb-5" id="equipeTechniqueTableContainer" style="{{ request('role') && request('role') !== 'equipe-technique' ? 'display: none;' : '' }}">
            <div class="col-12">
                <div class="table-card" data-group="equipe-technique">
                    <div class="table-header">
                        <h5 class="mb-0">
                            <i class="fas fa-cogs me-2"></i>
                            Liste de l'Équipe Technique
                        </h5>
                        <div class="loading-spinner" style="display: none;">
                            <span class="spinner-border spinner-border-sm text-primary" role="status"></span>
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th><input class="form-check-input" type="checkbox" id="selectAllEquipeTechnique"></th>
                                    <th>Avatar</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Date création</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="equipeTechniqueTableBody">
                                @forelse($equipeTechniquePaginated as $user)
                                    @include('users.partials.user_row', ['user' => $user])
                                @empty
                                    <tr>
                                        <td colspan="9">
                                            @include('users.partials.empty_state')
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <div class="pagination-info">
                            @if($equipeTechniquePaginated->count() > 0)
                                Affichage de {{ $equipeTechniquePaginated->firstItem() }} à {{ $equipeTechniquePaginated->lastItem() }} sur {{ $equipeTechniquePaginated->total() }} résultats
                            @else
                                Aucun résultat trouvé
                            @endif
                        </div>
                        <div class="pagination-wrapper">
                            {{ $equipeTechniquePaginated->appends(request()->except('page_equipe_technique'))->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmation de suppression -->
    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Confirmer la suppression
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3"></i>
                    <p class="lead">Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
                    <p class="text-danger small">Cette action est irréversible.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary px-4 py-2" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger px-4 py-2" id="confirmDelete">
                        <i class="fas fa-trash me-2"></i>
                        Supprimer
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root {
            --primary-color: #D32F2F;
            --secondary-color: #C2185B;
            --success-color: #4CAF50;
            --warning-color: #FF9800;
            --danger-color: #D32F2F;
            --info-color: #C2185B;
            --light-color: #f8f9fa;
            --dark-color: #2c3e50;
            --border-radius: 12px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
        }

        body {
            background-color: #f0f2f5;
            font-family: 'Poppins', sans-serif;
        }

        .page-title {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 700;
            font-size: 2.5rem;
            margin-bottom: 0.5rem;
        }

        .stats-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            transition: var(--transition);
            border: none;
            overflow: hidden;
            position: relative;
        }

        .stats-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
        }

        .stats-card-body {
            padding: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            flex-shrink: 0;
        }

        .stats-card-primary .stats-icon {
            background: linear-gradient(135deg, var(--primary-color), #EF4444);
        }

        .stats-card-success .stats-icon {
            background: linear-gradient(135deg, #4CAF50, #45a049);
        }

        .stats-card-warning .stats-icon {
            background: linear-gradient(135deg, #FF9800, #f57c00);
        }

        .stats-card-info .stats-icon {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
        }

        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            color: var(--dark-color);
            margin: 0;
            text-align: right;
        }

        .stats-label {
            color: #666;
            font-size: 0.9rem;
            margin: 0;
            text-align: right;
        }

        .stats-content {
            margin-left: 1rem;
            flex: 1;
        }

        .filter-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: none;
            overflow: hidden;
        }

        .filter-header {
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
        }

        .filter-body {
            padding: 1.5rem;
            display: none;
        }

        .filter-body.show {
            display: block;
            animation: slideDown 0.3s ease;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .table-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--box-shadow);
            border: none;
            overflow: hidden;
            position: relative;
        }

        .table-header {
            padding: 1rem 1.5rem;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            border-bottom: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .loading-spinner {
            position: absolute;
            right: 1.5rem;
            top: 50%;
            transform: translateY(-50%);
        }

        .table-modern {
            margin: 0;
            border: none;
            width: 100%;
        }

        .table-modern thead th {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            border: none;
            padding: 1rem 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.8rem;
            letter-spacing: 0.5px;
            vertical-align: middle;
        }

        .table-modern tbody tr {
            transition: var(--transition);
            border-bottom: 1px solid #f1f3f4;
        }

        .table-modern tbody tr:hover {
            background: linear-gradient(135deg, #fff2f2, #fff);
            transform: scale(1.01);
        }

        .table-modern tbody td {
            vertical-align: middle;
            padding: 0.85rem 0.75rem;
            white-space: nowrap;
        }

        .table-row {
            animation: fadeInUp 0.5s ease;
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .user-avatar {
            position: relative;
            display: inline-block;
        }

        .avatar-img {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .avatar-placeholder {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.2rem;
            border: 3px solid white;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .status-indicator {
            position: absolute;
            bottom: 0;
            right: 0;
            width: 14px;
            height: 14px;
            border-radius: 50%;
            border: 2px solid white;
        }

        .status-indicator.status-active {
            background: var(--success-color);
        }

        .status-indicator.status-inactive {
            background: var(--danger-color);
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            color: var(--dark-color);
        }

        .user-id {
            color: #666;
            font-size: 0.8rem;
        }

        .user-email, .user-phone {
            color: #666;
            font-size: 0.9rem;
        }

        .badge-role {
            background: linear-gradient(135deg, var(--info-color), var(--primary-color));
            color: white;
            border-radius: 20px;
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            font-weight: 600;
            margin-right: 0.25rem;
        }

        .badge-secondary {
            background: #6c757d;
            color: white;
            border-radius: 20px;
            font-size: 0.75rem;
            padding: 0.4rem 0.8rem;
            font-weight: 600;
        }

        .form-switch {
            min-width: 90px;
        }

        .form-switch .form-check-input {
            width: 3.5em;
            height: 1.8em;
            cursor: pointer;
            background-color: #dee2e6;
            border-color: #adb5bd;
            transition: background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        }

        .form-switch .form-check-input:checked {
            background-color: var(--success-color);
            border-color: var(--success-color);
        }

        .form-switch .form-check-label {
            font-weight: 600;
            color: var(--dark-color);
            font-size: 0.9rem;
        }

        .status-label.active {
            color: var(--success-color);
        }

        .status-label.inactive {
            color: var(--danger-color);
        }

        .date-text {
            font-weight: 600;
            color: var(--dark-color);
        }

        .date-time {
            color: #666;
            display: block;
            font-size: 0.8rem;
        }

        .action-buttons {
            display: flex;
            gap: 0.5rem;
            justify-content: center;
            align-items: center;
        }

        .btn-action {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            border: none;
            transition: var(--transition);
            font-size: 1rem;
            padding: 0;
        }

        .btn-view {
            background: rgba(194, 24, 91, 0.1);
            color: var(--info-color);
        }

        .btn-view:hover {
            background: var(--info-color);
            color: white;
            transform: scale(1.1);
        }

        .btn-edit {
            background: rgba(255, 152, 0, 0.1);
            color: var(--warning-color);
        }

        .btn-edit:hover {
            background: var(--warning-color);
            color: white;
            transform: scale(1.1);
        }

        .btn-delete {
            background: rgba(211, 47, 47, 0.1);
            color: var(--danger-color);
        }

        .btn-delete:hover {
            background: var(--danger-color);
            color: white;
            transform: scale(1.1);
        }

        .btn-floating {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            border-radius: 50px;
            padding: 0.75rem 1.5rem;
            color: white;
            font-weight: 600;
            transition: var(--transition);
            box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3);
        }

        .btn-floating:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(211, 47, 47, 0.4);
            color: white;
        }

        .table-footer {
            padding: 1rem 1.5rem;
            background: #f8f9fa;
            border-top: 1px solid #dee2e6;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .pagination-info {
            color: #666;
            font-size: 0.9rem;
        }

        .empty-state {
            text-align: center;
            padding: 4rem 2rem;
        }

        .empty-icon {
            font-size: 4rem;
            color: #ccc;
            margin-bottom: 1rem;
        }

        .empty-state h4 {
            color: var(--dark-color);
            margin-bottom: 1rem;
        }

        .empty-state p {
            color: #666;
            margin-bottom: 2rem;
        }

        .custom-alert {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--box-shadow);
            animation: slideInDown 0.5s ease;
        }

        @keyframes slideInDown {
            from {
                opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .pagination {
            gap: 0.5rem;
            margin-bottom: 0;
        }

        .pagination .page-link {
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            transition: var(--transition);
        }

        .pagination .page-link:hover {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            transform: scale(1.1);
        }

        .pagination .page-item.active .page-link {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3);
        }

        .btn-primary, .btn-outline-secondary {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            border: none;
            color: white;
        }

        .btn-primary:hover, .btn-outline-secondary:hover {
            background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
            color: white;
        }

        .table-loading {
            opacity: 0.6;
            pointer-events: none;
        }

        @media (max-width: 768px) {
            .stats-card-body {
                flex-direction: column;
                text-align: center;
            }
            
            .stats-content {
                margin-left: 0;
                margin-top: 1rem;
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .btn-action {
                width: 35px;
                height: 35px;
            }
        }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Variables globales
    let currentDeleteUserId = null;
    
    // Initialisation
    animateNumbers();
    attachStatusToggleListeners();
    attachPaginationListeners();
    
    // === FONCTIONS PRINCIPALES ===
    
    // Fonction pour appliquer les filtres
    window.applyFilters = function() {
        const search = document.getElementById('searchInput').value;
        const status = document.getElementById('statusFilter').value;
        const role = document.getElementById('roleFilter').value;
        
        // Déterminer quels groupes afficher
        const groups = ['consultant', 'etudiant', 'admis', 'equipe-technique'];
        
        // Masquer tous les containers
        groups.forEach(group => {
            const container = document.getElementById(`${group === 'equipe-technique' ? 'equipeTechnique' : group}TableContainer`);
            if (container) {
                container.style.display = 'none';
            }
        });
        
        // Afficher et charger les groupes appropriés
        if (role === '') {
            // Si aucun rôle spécifique, charger tous les groupes
            groups.forEach(group => {
                showAndLoadGroup(group, 1);
            });
        } else {
            // Charger seulement le groupe sélectionné
            showAndLoadGroup(role, 1);
        }
    };
    
    // Fonction pour réinitialiser les filtres
    window.resetFilters = function() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('roleFilter').value = '';
        
        // Afficher tous les groupes et recharger depuis page 1
        const groups = ['consultant', 'etudiant', 'admis', 'equipe-technique'];
        groups.forEach(group => {
            showAndLoadGroup(group, 1);
        });
    };
    
    // Fonction pour afficher et charger un groupe
    function showAndLoadGroup(group, page = 1) {
        const containerId = group === 'equipe-technique' ? 'equipeTechniqueTableContainer' : `${group}TableContainer`;
        const container = document.getElementById(containerId);
        if (container) {
            container.style.display = 'block';
            loadGroupData(group, page);
        }
    }
    
    // Fonction principale pour charger les données d'un groupe via AJAX
    async function loadGroupData(group, page = 1) {
        const tableCard = document.querySelector(`.table-card[data-group="${group}"]`);
        if (!tableCard) return;
        
        const loadingSpinner = tableCard.querySelector('.loading-spinner');
        const tbody = tableCard.querySelector('tbody');
        
        try {
            // Afficher le spinner et désactiver le tableau
            if (loadingSpinner) loadingSpinner.style.display = 'block';
            if (tableCard) tableCard.classList.add('table-loading');
            
            // Préparer les paramètres
            const params = {
                search: document.getElementById('searchInput').value,
                status: document.getElementById('statusFilter').value,
                role: document.getElementById('roleFilter').value,
                group: group,
            };
            
            // Ajouter le bon paramètre de page selon le groupe
            let pageParam;
            if (group === 'admis') {
                pageParam = 'page_admin';
            } else if (group === 'equipe-technique') {
                pageParam = 'page_equipe_technique';
            } else {
                pageParam = `page_${group}`;
            }
            params[pageParam] = page;
            
            console.log('Params envoyés:', params); // Debug
            
            // Requête AJAX
            const response = await axios.get(window.location.pathname, {
                params: params,
                headers: { 
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            console.log('Réponse reçue:', response.data); // Debug
            
            if (response.data.success) {
                updateGroupTable(group, response.data.users, response.data.pagination);
                if (response.data.stats) {
                    updateStats(response.data.stats);
                }
            } else {
                console.error('Erreur dans la réponse:', response.data);
                showAlert('Erreur lors du chargement des données', 'danger');
            }
            
        } catch (error) {
            console.error(`Erreur lors du chargement du groupe ${group}:`, error);
            console.error('Détails de l\'erreur:', error.response?.data);
            showAlert(`Erreur de communication pour ${group}`, 'danger');
        } finally {
            // Masquer le spinner et réactiver le tableau
            if (loadingSpinner) loadingSpinner.style.display = 'none';
            if (tableCard) tableCard.classList.remove('table-loading');
        }
    }
    
    // Fonction pour mettre à jour le tableau d'un groupe
    function updateGroupTable(group, paginatedData, paginationHtml) {
        const tableCard = document.querySelector(`.table-card[data-group="${group}"]`);
        if (!tableCard) return;
        
        const tbody = tableCard.querySelector('tbody');
        const paginationWrapper = tableCard.querySelector('.pagination-wrapper');
        const paginationInfo = tableCard.querySelector('.pagination-info');
        
        console.log('Données de pagination reçues:', paginatedData); // Debug
        
        // Les données viennent directement de Laravel paginate()
        const users = paginatedData.data || [];
        const total = paginatedData.total || 0;
        const from = paginatedData.from || 0;
        const to = paginatedData.to || 0;
        
        if (users.length > 0) {
            // Construire le HTML des lignes
            let html = '';
            users.forEach(user => {
                html += buildUserRowHtml(user);
            });
            
            tbody.innerHTML = html;
            paginationWrapper.innerHTML = paginationHtml;
            
            // Mettre à jour les informations de pagination
            paginationInfo.textContent = `Affichage de ${from} à ${to} sur ${total} résultats`;
            
        } else {
            // Afficher l'état vide
            tbody.innerHTML = `
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <div class="empty-icon"><i class="fas fa-users"></i></div>
                            <h4>Aucun utilisateur trouvé</h4>
                            <p>Aucun utilisateur ne correspond à vos critères de recherche.</p>
                            <div class="empty-actions mt-3">
                                <button type="button" class="btn btn-outline-primary" onclick="resetFilters()">
                                    <i class="fas fa-refresh me-2"></i>
                                    Réinitialiser les filtres
                                </button>
                            </div>
                        </div>
                    </td>
                </tr>
            `;
            paginationWrapper.innerHTML = '';
            paginationInfo.textContent = `Aucun résultat trouvé sur ${total} utilisateurs`;
        }
        
        // Réattacher les événements
        attachStatusToggleListeners();
        animateTableRows();
    }
    
    // Fonction pour construire le HTML d'une ligne utilisateur
    function buildUserRowHtml(user) {
        const avatarHtml = user.avatar 
            ? `<img src="${window.location.origin}/storage/${user.avatar}" alt="Avatar de ${user.name}" class="avatar-img">`
            : `<div class="avatar-placeholder">${user.name.charAt(0).toUpperCase()}</div>`;
            
        const rolesHtml = user.roles && user.roles.length > 0
            ? user.roles.map(role => `<span class="badge badge-role">${role.name}</span>`).join('')
            : '<span class="badge badge-secondary">Aucun rôle</span>';
            
        const statusChecked = user.status === 'active' ? 'checked' : '';
        const statusLabel = user.status === 'active' ? 'Actif' : 'Inactif';
        
        const createdDate = new Date(user.created_at);
        const dateText = createdDate.toLocaleDateString('fr-FR');
        const timeText = createdDate.toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });
        
        return `
            <tr class="table-row" data-user-id="${user.id}">
                <td>
                    <input class="form-check-input user-checkbox" type="checkbox" value="${user.id}">
                </td>
                <td>
                    <div class="user-avatar">
                        ${avatarHtml}
                        <div class="status-indicator status-${user.status}"></div>
                    </div>
                </td>
                <td>
                    <div class="user-info">
                        <span class="user-name">${user.name}</span>
                        <small class="user-id">ID: ${user.id}</small>
                    </div>
                </td>
                <td>
                    <span class="user-email">${user.email}</span>
                </td>
                <td>
                    <span class="user-phone">${user.phone || '-'}</span>
                </td>
                <td>
                    ${rolesHtml}
                </td>
                <td>
                    <div class="form-check form-switch d-flex align-items-center justify-content-center">
                        <input class="form-check-input status-toggle-switch" type="checkbox" 
                               id="statusSwitch-${user.id}" data-user-id="${user.id}" ${statusChecked}>
                        <label class="form-check-label ms-2 status-label ${user.status}" 
                               for="statusSwitch-${user.id}">${statusLabel}</label>
                    </div>
                </td>
                <td>
                    <span class="date-text">${dateText}</span>
                    <small class="date-time">${timeText}</small>
                </td>
                <td>
                    <div class="action-buttons">
                        <a href="${window.location.origin}/users/${user.id}" class="btn btn-action btn-view" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="${window.location.origin}/users/${user.id}/edit" class="btn btn-action btn-edit" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-action btn-delete" onclick="deleteUser(${user.id})" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </td>
            </tr>
        `;
    }
    
    // === FONCTIONS UTILITAIRES ===
    
    // Fonction pour gérer la pagination
    function attachPaginationListeners() {
        document.addEventListener('click', function(e) {
            // Rechercher spécifiquement les liens dans les wrappers de pagination
            const paginationLink = e.target.closest('.pagination-wrapper a.page-link');
            
            if (paginationLink && !paginationLink.parentElement.classList.contains('disabled')) {
                e.preventDefault();
                
                console.log('Lien pagination cliqué:', paginationLink.href); // Debug
                
                const url = new URL(paginationLink.href);
                const tableCard = paginationLink.closest('.table-card');
                
                if (!tableCard) {
                    console.error('Table card non trouvé');
                    return;
                }
                
                const group = tableCard.getAttribute('data-group');
                console.log('Groupe détecté:', group); // Debug
                
                // Déterminer le paramètre de page correct
                let pageParam;
                if (group === 'admis') {
                    pageParam = 'page_admin';
                } else if (group === 'equipe-technique') {
                    pageParam = 'page_equipe_technique';
                } else {
                    pageParam = `page_${group}`;
                }
                const page = url.searchParams.get(pageParam) || 1;
                
                console.log('Paramètre de page:', pageParam, 'Page:', page); // Debug
                
                // Charger les données pour cette page
                loadGroupData(group, page);
                
                // Scroll vers le tableau
                tableCard.scrollIntoView({ behavior: 'smooth', block: 'start' });
            }
        });
    }
    
    // Fonction pour attacher les listeners des switchs de statut
    function attachStatusToggleListeners() {
        document.querySelectorAll('.status-toggle-switch:not([data-listener])').forEach(toggle => {
            toggle.setAttribute('data-listener', 'true');
            
            toggle.addEventListener('change', async function() {
                const userId = this.getAttribute('data-user-id');
                const newStatus = this.checked ? 'active' : 'inactive';
                const statusLabel = this.nextElementSibling;
                const originalChecked = !this.checked;
                
                // Interface de chargement
                const originalText = statusLabel.textContent;
                statusLabel.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
                this.disabled = true;
                
                try {
                    const response = await axios.put(`${window.location.origin}/users/${userId}/toggle-status/${newStatus}`, {}, {
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });
                    
                    if (response.data.success) {
                        statusLabel.textContent = newStatus === 'active' ? 'Actif' : 'Inactif';
                        statusLabel.classList.remove('active', 'inactive');
                        statusLabel.classList.add(newStatus);
                        
                        // Mettre à jour l'indicateur de statut
                        const statusIndicator = document.querySelector(`tr[data-user-id="${userId}"] .status-indicator`);
                        if (statusIndicator) {
                            statusIndicator.classList.remove('status-active', 'status-inactive');
                            statusIndicator.classList.add(`status-${newStatus}`);
                        }
                        
                        showAlert(response.data.message, 'success');
                    } else {
                        throw new Error(response.data.message || 'Erreur lors de la mise à jour');
                    }
                    
                } catch (error) {
                    console.error('Erreur toggle status:', error);
                    this.checked = originalChecked;
                    statusLabel.textContent = originalText;
                    showAlert('Erreur lors de la mise à jour du statut', 'danger');
                } finally {
                    this.disabled = false;
                }
            });
        });
    }
    
    // Fonction pour afficher les alertes
    function showAlert(message, type = 'info') {
        const alertContainer = document.getElementById('ajax-alerts');
        if (!alertContainer) return;
        
        const alertId = 'alert-' + Date.now();
        
        const alertHtml = `
            <div id="${alertId}" class="alert alert-${type} alert-dismissible fade show custom-alert" role="alert">
                <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        alertContainer.insertAdjacentHTML('beforeend', alertHtml);
        
        // Auto-remove après 5 secondes
        setTimeout(() => {
            const alert = document.getElementById(alertId);
            if (alert) {
                alert.remove();
            }
        }, 5000);
    }
    
    // Fonction pour mettre à jour les statistiques
    function updateStats(stats) {
        if (stats) {
            const elements = {
                total: document.querySelector('[data-count]'),
                // Ajoutez d'autres sélecteurs si nécessaire
            };
            
            if (elements.total) {
                elements.total.setAttribute('data-count', stats.total);
                elements.total.textContent = stats.total;
            }
        }
    }
    
    // Fonction d'animation des lignes de tableau
    function animateTableRows() {
        const rows = document.querySelectorAll('.table-row');
        rows.forEach((row, index) => {
            row.style.animationDelay = `${index * 0.05}s`;
            row.classList.remove('animate__fadeInUp');
            void row.offsetWidth; // Force reflow
            row.classList.add('animate__animated', 'animate__fadeInUp');
        });
    }
    
    // Fonction d'animation des nombres dans les stats
    function animateNumbers() {
        const counters = document.querySelectorAll('.stats-number');
        counters.forEach(counter => {
            const target = parseInt(counter.getAttribute('data-count'));
            if (isNaN(target)) return;
            
            const duration = 1500;
            const start = performance.now();
            
            const update = (timestamp) => {
                const progress = (timestamp - start) / duration;
                if (progress < 1) {
                    counter.textContent = Math.floor(progress * target);
                    requestAnimationFrame(update);
                } else {
                    counter.textContent = target;
                }
            };
            requestAnimationFrame(update);
        });
    }
    
    // Fonction pour toggle le card de filtres
    window.toggleFilterCard = function() {
        const filterBody = document.getElementById('filterCollapse');
        const icon = document.querySelector('.toggle-filters i');
        
        if (filterBody) filterBody.classList.toggle('show');
        if (icon) {
            icon.classList.toggle('fa-chevron-up');
            icon.classList.toggle('fa-chevron-down');
        }
    };
    
    // Fonction pour la suppression d'utilisateur
    window.deleteUser = function(userId) {
        currentDeleteUserId = userId;
        const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
        modal.show();
    };
    
    // Event listener pour la confirmation de suppression
    const confirmDeleteBtn = document.getElementById('confirmDelete');
    if (confirmDeleteBtn) {
        confirmDeleteBtn.addEventListener('click', async function() {
            if (!currentDeleteUserId) return;
            
            try {
                const response = await axios.delete(`${window.location.origin}/users/${currentDeleteUserId}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                if (response.data.success) {
                    showAlert(response.data.message, 'success');
                    // Recharger les données
                    applyFilters();
                } else {
                    showAlert(response.data.message || 'Erreur lors de la suppression', 'danger');
                }
                
            } catch (error) {
                console.error('Erreur suppression:', error);
                showAlert('Erreur lors de la suppression', 'danger');
            } finally {
                const modal = bootstrap.Modal.getInstance(document.getElementById('deleteModal'));
                if (modal) modal.hide();
                currentDeleteUserId = null;
            }
        });
    }
    
    // Écouteurs d'événements pour les changements de filtres
    const searchInput = document.getElementById('searchInput');
    const statusFilter = document.getElementById('statusFilter');
    const roleFilter = document.getElementById('roleFilter');
    
    if (searchInput) {
        searchInput.addEventListener('input', debounce(applyFilters, 500));
    }
    
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }
    
    if (roleFilter) {
        roleFilter.addEventListener('change', applyFilters);
    }
    
    // Fonction debounce pour la recherche
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }
});

</script>
@endpush