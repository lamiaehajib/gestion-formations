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
                    <a href="{{ route('users.create') }}" class="btn btn-primary btn-floating">
                        <i class="fas fa-plus me-2"></i>
                        Nouvel Utilisateur
                    </a>
                </div>
            </div>
        </div>

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

        <div class="row mb-4">
            <div class="col-12">
                <div class="filter-card">
                    <div class="filter-header">
                        <h5 class="mb-0">
                            <i class="fas fa-filter me-2"></i>
                            Filtres et Recherche
                        </h5>
                        <button class="btn btn-sm btn-outline-secondary toggle-filters" type="button">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>
                    <div class="filter-body" id="filterCollapse">
                        <form id="filterForm" action="{{ route('users.index') }}" method="GET">
                            <div class="row">
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Recherche</label>
                                    <div class="input-group">
                                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                                        <input type="text" name="search" id="searchInput" class="form-control" placeholder="Nom, email, téléphone..." value="{{ request('search') }}">
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
                                            <option value="{{ $role->name }}" {{ request('role') == $role->name ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-2 mb-3 d-flex align-items-end">
                                    <button type="submit" class="btn btn-primary w-100 me-2"><i class="fas fa-search me-2"></i>Filtrer</button>
                                    <a href="{{ route('users.index') }}" class="btn btn-outline-secondary w-100" id="resetFilters"><i class="fas fa-undo me-2"></i>Réinitialiser</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

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

        <div class="row">
            <div class="col-12">
                <div class="table-card">
                    <div class="table-header">
                        <h5 class="mb-0">
                            <i class="fas fa-table me-2"></i>
                            Liste des Utilisateurs
                        </h5>
                        <div class="table-actions">
                            <button class="btn btn-sm btn-outline-secondary me-2" id="toggleView">
                                <i class="fas fa-th-large"></i> {{-- Icon will change with JS --}}
                            </button>
                           
                        </div>
                    </div>

                    @if($users->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-modern" id="usersTable">
                                <thead>
                                <tr>
                                    <th>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="selectAll">
                                        </div>
                                    </th>
                                    <th>Avatar</th>
                                    <th>Nom</th>
                                    <th>Email</th>
                                    <th>Téléphone</th>
                                    <th>Rôle</th>
                                    <th>Statut</th>
                                    <th>Date de création</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody id="usersTableBody">
                                @foreach($users as $user)
                                    <tr class="table-row" data-user-id="{{ $user->id }}">
                                        <td>
                                            <div class="form-check">
                                                <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}">
                                            </div>
                                        </td>
                                        <td>
                                            <div class="user-avatar">
                                                @if($user->avatar)
                                                    <img src="{{ asset('storage/' . $user->avatar) }}"
                                                         alt="Avatar de {{ $user->name }}"
                                                         class="avatar-img">
                                                @else
                                                    <div class="avatar-placeholder">
                                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                                    </div>
                                                @endif
                                                <div class="status-indicator status-{{ $user->status }}"></div>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="user-info">
                                                <span class="user-name">{{ $user->name }}</span>
                                                <small class="user-id">ID: {{ $user->id }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="user-email">{{ $user->email }}</span>
                                        </td>
                                        <td>
                                            <span class="user-phone">{{ $user->phone ?? '-' }}</span>
                                        </td>
                                        <td>
                                            @if($user->getRoleNames()->count() > 0)
                                                @foreach($user->getRoleNames() as $role)
                                                    <span class="badge badge-role">{{ $role }}</span>
                                                @endforeach
                                            @else
                                                <span class="badge badge-secondary">Aucun rôle</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{-- Toggle Switch pour le statut --}}
                                            <div class="form-check form-switch d-flex align-items-center justify-content-center">
                                                <input class="form-check-input status-toggle-switch" type="checkbox" id="statusSwitch-{{ $user->id }}"
                                                       data-user-id="{{ $user->id }}" {{ $user->status === 'active' ? 'checked' : '' }}>
                                                <label class="form-check-label ms-2 status-label {{ $user->status }}" for="statusSwitch-{{ $user->id }}">
                                                    {{ $user->status === 'active' ? 'Actif' : 'Inactif' }}
                                                </label>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="date-text">{{ $user->created_at->format('d/m/Y') }}</span>
                                            <small class="date-time">{{ $user->created_at->format('H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="{{ route('users.show', $user->id) }}"
                                                   class="btn btn-action btn-view tooltip-custom"
                                                   data-tooltip="Voir"
                                                   title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('users.edit', $user->id) }}"
                                                   class="btn btn-action btn-edit tooltip-custom"
                                                   data-tooltip="Modifier"
                                                   title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                <button class="btn btn-action btn-delete tooltip-custom"
                                                        data-tooltip="Supprimer"
                                                        onclick="deleteUser({{ $user->id }})"
                                                        title="Supprimer">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="table-footer">
                            <div class="pagination-info">
                                Affichage de {{ $users->firstItem() }} à {{ $users->lastItem() }} sur {{ $users->total() }} résultats
                            </div>
                            <div class="pagination-wrapper">
                                {{ $users->links() }}
                            </div>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="empty-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <h4>Aucun utilisateur trouvé</h4>
                            <p>Commencez par créer votre premier utilisateur pour voir la liste ici.</p>
                            <a href="{{ route('users.create') }}" class="btn btn-primary btn-floating">
                                <i class="fas fa-plus me-2"></i>
                                Créer un utilisateur
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Grid View Container (Initially hidden) --}}
        <div class="grid-container" id="gridContainer">
            @foreach($users as $user)
                <div class="user-card animate__animated animate__fadeInUp" data-user-id="{{ $user->id }}">
                    <div class="user-card-header">
                        <div class="user-card-avatar user-avatar">
                            @if($user->avatar)
                                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar de {{ $user->name }}" class="avatar-img">
                            @else
                                <div class="avatar-placeholder">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="status-indicator status-{{ $user->status }}"></div>
                        </div>
                        <div class="user-card-info">
                            <h5>{{ $user->name }}</h5>
                            <p>ID: {{ $user->id }}</p>
                        </div>
                    </div>
                    <div class="user-card-details">
                        <div class="user-card-detail">
                            <span><i class="fas fa-envelope me-2 text-muted"></i>Email:</span>
                            <span>{{ $user->email }}</span>
                        </div>
                        <div class="user-card-detail">
                            <span><i class="fas fa-phone me-2 text-muted"></i>Téléphone:</span>
                            <span>{{ $user->phone ?? '-' }}</span>
                        </div>
                        <div class="user-card-detail">
                            <span><i class="fas fa-user-tag me-2 text-muted"></i>Rôle:</span>
                            <span>
                                @if($user->getRoleNames()->count() > 0)
                                    @foreach($user->getRoleNames() as $role)
                                        <span class="badge badge-role">{{ $role }}</span>
                                    @endforeach
                                @else
                                    <span class="badge badge-secondary">Aucun rôle</span>
                                @endif
                            </span>
                        </div>
                        <div class="user-card-detail">
                            <span><i class="fas fa-power-off me-2 text-muted"></i>Statut:</span>
                            <div class="form-check form-switch d-flex align-items-center justify-content-center">
                                <input class="form-check-input status-toggle-switch" type="checkbox" id="gridStatusSwitch-{{ $user->id }}"
                                       data-user-id="{{ $user->id }}" {{ $user->status === 'active' ? 'checked' : '' }}>
                                <label class="form-check-label ms-2 status-label {{ $user->status }}" for="gridStatusSwitch-{{ $user->id }}">
                                    {{ $user->status === 'active' ? 'Actif' : 'Inactif' }}
                                </label>
                            </div>
                        </div>
                        <div class="user-card-detail">
                            <span><i class="fas fa-calendar-alt me-2 text-muted"></i>Créé le:</span>
                            <span>{{ $user->created_at->format('d/m/Y H:i') }}</span>
                        </div>
                    </div>
                    <div class="user-card-actions">
                        <a href="{{ route('users.show', $user->id) }}" class="btn btn-action btn-view tooltip-custom" data-tooltip="Voir" title="Voir">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-action btn-edit tooltip-custom" data-tooltip="Modifier" title="Modifier">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button class="btn btn-action btn-delete tooltip-custom" data-tooltip="Supprimer" onclick="deleteUser({{ $user->id }})" title="Supprimer">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

    </div>

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
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3 animate__animated animate__tada animate__infinite"></i>
                    <p class="lead">Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
                    <p class="text-danger small">Cette action est irréversible et supprimera toutes les données associées.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary px-4 py-2" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger px-4 py-2" id="confirmDelete">
                        <i class="fas fa-trash me-2"></i>
                        Supprimer Définitivement
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
        --primary-color: #D32F2F; /* Dark Red */
        --secondary-color: #C2185B; /* Crimson */
        --success-color: #4CAF50;
        --warning-color: #FF9800;
        --danger-color: #D32F2F; /* Matching primary for danger */
        --info-color: #C2185B; /* Matching secondary for info */
        --light-color: #f8f9fa;
        --dark-color: #2c3e50;
        --border-radius: 12px;
        --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        --transition: all 0.3s ease;
    }

    body {
        background-color: #f0f2f5; /* Light grey background */
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

    /* Stats Cards */
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
        flex-shrink: 0; /* Prevent shrinking */
    }

    .stats-card-primary .stats-icon {
        background: linear-gradient(135deg, var(--primary-color), #EF4444); /* Using EF4444 as an accent */
    }

    .stats-card-success .stats-icon {
        background: linear-gradient(135deg, #4CAF50, #45a049);
    }

    .stats-card-warning .stats-icon {
        background: linear-gradient(135deg, #FF9800, #f57c00);
    }

    .stats-card-info .stats-icon {
        background: linear-gradient(135deg, var(--secondary-color), #D32F2F); /* Using secondary and primary for info */
    }

    .stats-number {
        font-size: 2rem;
        font-weight: 700;
        color: var(--dark-color);
        margin: 0;
        flex-grow: 1; /* Allow content to grow */
        text-align: right;
    }

    .stats-label {
        color: #666;
        font-size: 0.9rem;
        margin: 0;
        text-align: right;
    }

    .stats-content {
        margin-left: 1rem; /* Space between icon and text */
    }

    /* Filter Card */
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
        cursor: pointer; /* To indicate it's collapsible */
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

    /* Table Card */
    .table-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        border: none;
        overflow: hidden;
    }

    .table-header {
        padding: 1rem 1.5rem;
        background: linear-gradient(135deg, #f8f9fa, #e9ecef);
        border-bottom: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .table-modern {
        margin: 0;
        border: none;
        width: 100%; /* Ensure table takes full width */
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
        vertical-align: middle; /* Align text vertically */
    }

    .table-modern tbody tr {
        transition: var(--transition);
        border-bottom: 1px solid #f1f3f4;
    }

    .table-modern tbody tr:hover {
        background: linear-gradient(135deg, #fff2f2, #fff); /* Lighter red tint on hover */
        transform: scale(1.01);
    }

    .table-modern tbody td {
        vertical-align: middle; /* Align cell content vertically */
        padding: 0.85rem 0.75rem;
        white-space: nowrap; /* Prevent content from wrapping */
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

    /* Avatar */
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

    /* User Info */
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

    /* Badges (Rôles) */
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
        margin-right: 0.25rem;
    }

    /* Status Toggle Switch */
    .form-switch {
        min-width: 90px; /* Adjust as needed */
    }
    .form-switch .form-check-input {
        width: 3.5em; /* Larger switch */
        height: 1.8em;
        cursor: pointer;
        background-color: #dee2e6;
        border-color: #adb5bd;
        transition: background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out;
        float: none; /* Override default Bootstrap float */
        margin-left: 0;
        display: inline-block;
        vertical-align: middle;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e"); /* white circle */
    }
    .form-switch .form-check-input:checked {
        background-color: var(--success-color);
        border-color: var(--success-color);
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e");
    }
    .form-switch .form-check-label {
        font-weight: 600;
        color: var(--dark-color);
        font-size: 0.9rem;
        vertical-align: middle;
    }
    .status-label.active {
        color: var(--success-color);
    }
    .status-label.inactive {
        color: var(--danger-color);
    }


    /* Date */
    .date-text {
        font-weight: 600;
        color: var(--dark-color);
    }

    .date-time {
        color: #666;
        display: block;
        font-size: 0.8rem;
    }

    /* Action Buttons */
    .action-buttons {
        display: flex;
        gap: 0.5rem;
        justify-content: center; /* Center actions in their column */
        align-items: center;


    }

    .btn-action {
        width: 40px; /* Slightly larger buttons */
        height: 40px; /* Slightly larger buttons */
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        transition: var(--transition);
        font-size: 1rem; /* Larger icon */
        padding: 0; /* Remove padding to ensure size */
    }

    .btn-view {
        background: rgba(194, 24, 91, 0.1); /* Using secondary color with transparency */
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
        background: rgba(211, 47, 47, 0.1); /* Using primary color with transparency */
        color: var(--danger-color);
    }

    .btn-delete:hover {
        background: var(--danger-color);
        color: white;
        transform: scale(1.1);
    }

    /* Floating Button */
    .btn-floating {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border: none;
        border-radius: 50px;
        padding: 0.75rem 1.5rem;
        color: white;
        font-weight: 600;
        transition: var(--transition);
        box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3); /* Adjust shadow to primary color */
    }

    .btn-floating:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(211, 47, 47, 0.4); /* Adjust shadow to primary color */
        color: white;
    }

    /* Table Footer */
    .table-footer {
        padding: 1rem 1.5rem;
        background: #f8f9fa;
        border-top: 1px solid #dee2e6;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap; /* Allow wrapping on smaller screens */
        gap: 1rem;
    }

    .pagination-info {
        color: #666;
        font-size: 0.9rem;
    }

    /* Empty State */
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

    /* Custom Alerts */
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

    /* Vue grille pour le tableau (Cache the default table and show grid) */
    .table-modern.grid-hidden {
        display: none;
    }

    .grid-container {
        display: none; /* Hidden by default */
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 1.5rem;
        padding: 1.5rem;
        animation: fadeInUp 0.5s ease; /* Animation for the whole grid */
    }

    .grid-container.grid-show {
        display: grid; /* Show when active */
    }

    .user-card {
        background: white;
        border-radius: var(--border-radius);
        box-shadow: var(--box-shadow);
        padding: 1.5rem;
        transition: var(--transition);
        border: 1px solid #f1f3f4;
    }

    .user-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    }

    .user-card-header {
        display: flex;
        align-items: center;
        margin-bottom: 1rem;
    }

    .user-card-avatar {
        margin-right: 1rem;
    }

    .user-card-info h5 {
        margin: 0;
        color: var(--dark-color);
        font-weight: 600;
    }

    .user-card-info p {
        margin: 0;
        color: #666;
        font-size: 0.9rem;
    }

    .user-card-details {
        margin-bottom: 1rem;
    }

    .user-card-detail {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.5rem;
        padding: 0.5rem 0;
        border-bottom: 1px solid #f8f9fa;
    }

    .user-card-detail:last-child {
        border-bottom: none;
    }

    .user-card-actions {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
    }

    /* Animations supplémentaires */
    @keyframes pulse {
        0% { transform: scale(1); }
        50% { transform: scale(1.05); }
        100% { transform: scale(1); }
    }

    .stats-card:hover .stats-icon {
        animation: pulse 1s infinite;
    }

    @keyframes slideOutUp {
        from {
            opacity: 1;
            transform: translateY(0);
        }
        to {
            opacity: 0;
            transform: translateY(-30px);
        }
    }

    /* Loader pour les actions asynchrones */
    .btn-loading {
        position: relative;
        pointer-events: none;
    }

    .btn-loading::after {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        width: 16px;
        height: 16px;
        border: 2px solid transparent;
        border-top: 2px solid currentColor;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: translate(-50%, -50%) rotate(0deg); }
        100% { transform: translate(-50%, -50%) rotate(360deg); }
    }

    /* Tooltips personnalisés */
    .tooltip-custom {
        position: relative;
    }

    .tooltip-custom::before {
        content: '';
        position: absolute;
        bottom: calc(100% + 5px); /* Position above the element */
        left: 50%;
        transform: translateX(-50%);
        border-width: 5px;
        border-style: solid;
        border-color: var(--dark-color) transparent transparent transparent; /* Arrow pointing down */
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
        z-index: 1001;
    }

    .tooltip-custom::after {
        content: attr(data-tooltip);
        position: absolute;
        bottom: calc(100% + 10px); /* Position above the element */
        left: 50%;
        transform: translateX(-50%);
        background: var(--dark-color);
        color: white;
        padding: 0.5rem 0.75rem;
        border-radius: 6px;
        font-size: 0.8rem;
        white-space: nowrap;
        opacity: 0;
        visibility: hidden;
        transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease;
        z-index: 1000;
    }

    .tooltip-custom:hover::before,
    .tooltip-custom:hover::after {
        opacity: 1;
        visibility: visible;
        transform: translateX(-50%) translateY(-5px); /* Move up slightly on hover */
    }


    /* Pagination personnalisée */
    .pagination {
        gap: 0.5rem;
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
        box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3); /* Adjust shadow to primary color */
    }

    /* Scrollbar personnalisée */
    .table-responsive::-webkit-scrollbar {
        height: 8px;
    }

    .table-responsive::-webkit-scrollbar-track {
        background: #f1f3f4;
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        border-radius: 10px;
    }

    .table-responsive::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, var(--secondary-color), var(--primary-color));
    }

    /* Dark mode support */
    @media (prefers-color-scheme: dark) {
        body {
            background-color: #1a202c;
        }
        .stats-card,
        .filter-card,
        .table-card,
        .modal-content,
        .user-card { /* Added user-card for dark mode */
            background: #2d3748;
            color: white;
        }

        .table-modern tbody tr:hover,
        .user-card:hover { /* Added user-card hover for dark mode */
            background: linear-gradient(135deg, #4a5568, #2d3748);
        }

        .filter-header,
        .table-header,
        .table-footer,
        .modal-header,
        .modal-footer {
            background: #4a5568;
            color: white;
            border-color: #2d3748;
        }

        .modal-header.bg-danger {
            background-color: var(--danger-color) !important;
        }

        .btn-close-white {
            filter: brightness(2); /* Make white close button more visible on dark backgrounds */
        }

        .stats-number, .user-name, h4, h5 { /* Added h5 for dark mode */
            color: white;
        }
        .stats-label, .user-id, .user-email, .user-phone, .date-text, .date-time, .pagination-info, .text-muted, .text-gray {
            color: #a0aec0; /* Lighter gray for readability */
        }
        .tooltip-custom::before, .tooltip-custom::after {
            background: #4a5568; /* Darker tooltip background */
        }

    }

    /* Print styles */
    @media print {
        .btn,
        .action-buttons,
        .filter-card,
        .table-header,
        .table-footer {
            display: none !important;
        }

        .table-card {
            box-shadow: none;
            border: 1px solid #000;
        }

        .table-modern {
            font-size: 0.8rem;
        }

        .stats-card {
            break-inside: avoid;
        }
    }
    i.fas.fa-eye {
        color: #ee1111 !important;
    }


    button.btn.btn-sm.btn-outline-secondary.toggle-filters {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }

    button#toggleView {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }

    button.btn.btn-sm.btn-outline-secondary {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }


    a.btn.btn-outline-secondary.w-100 {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
    }
</style>
@endpush
@push('scripts')
{{-- Inclure Axios pour les requêtes Ajax --}}
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
{{-- Inclure Font Awesome pour les icônes (si non déjà inclus globalement) --}}
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Initialiser les animations au chargement
        animateTableRows();
        animateNumbers(); // Pour les compteurs des statistiques

        // Toggle des filtres
        document.querySelector('.filter-header').addEventListener('click', function() { // Attach to header for click
            const filterBody = document.getElementById('filterCollapse');
            const icon = this.querySelector('.toggle-filters i'); // Get icon from the button

            if (filterBody.classList.contains('show')) {
                filterBody.classList.remove('show');
                icon.classList.remove('fa-chevron-up');
                icon.classList.add('fa-chevron-down');
            } else {
                filterBody.classList.add('show');
                icon.classList.remove('fa-chevron-down');
                icon.classList.add('fa-chevron-up');
            }
        });

        // Recherche en temps réel
        document.getElementById('searchInput').addEventListener('input', function() {
            filterTableAndFetch(); // Call a combined function
        });

        // Filtres
        document.getElementById('statusFilter').addEventListener('change', filterTableAndFetch);
        document.getElementById('roleFilter').addEventListener('change', filterTableAndFetch);

        // Reset des filtres
        document.getElementById('resetFilters').addEventListener('click', function(e) {
            e.preventDefault(); // Prevent the default link behavior
            window.location.href = "{{ route('users.index') }}";
        });

        // Sélection multiple
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Auto-hide des alertes
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.animation = 'slideOutUp 0.5s ease forwards'; // Use forwards to keep final state
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Fonction globale pour le userToDelete pour le modal de suppression
        let userToDelete = null;

        window.deleteUser = function(userId) { // Make it global
            userToDelete = userId;
            const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
            deleteModal.show();
        }

        document.getElementById('confirmDelete').addEventListener('click', function() {
            if (userToDelete) {
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = `/users/${userToDelete}`;
                form.style.display = 'none';

                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);

                const methodInput = document.createElement('input');
                methodInput.type = 'hidden';
                methodInput.name = '_method';
                methodInput.value = 'DELETE';
                form.appendChild(methodInput);

                document.body.appendChild(form);
                form.submit();
            }
        });

        // Fonction pour l'animation des lignes du tableau
        function animateTableRows() {
            const rows = document.querySelectorAll('.table-row');
            rows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`; // Slightly faster
                row.classList.add('animate__animated', 'animate__fadeInUp'); // Use Animate.css class
            });
        }

        // Fonction pour basculer entre vue tableau et vue grille
        document.getElementById('toggleView').addEventListener('click', function() {
            const table = document.getElementById('usersTable');
            const gridContainer = document.getElementById('gridContainer');
            const icon = this.querySelector('i');

            if (table.closest('.table-card').style.display !== 'none') { // Currently in table view, switch to grid
                table.closest('.table-card').style.display = 'none';
                gridContainer.style.display = 'grid';
                icon.classList.remove('fa-th-large');
                icon.classList.add('fa-table');
            } else { // Currently in grid view, switch to table
                table.closest('.table-card').style.display = 'block';
                gridContainer.style.display = 'none';
                icon.classList.remove('fa-table');
                icon.classList.add('fa-th-large');
            }
        });


        // Fonction pour exporter les données (Placeholder)
        document.querySelector('.table-actions .btn:last-child').addEventListener('click', function() {
            alert('Fonctionnalité d\'exportation en cours de développement !');
            // Implémentez ici la logique d'appel à une route d'exportation (CSV, PDF, etc.)
            // window.location.href = "{{ route('users.export', ['format' => 'csv']) }}";
        });

        // Fonction pour animer les compteurs (stats)
        function animateNumbers() {
            const counters = document.querySelectorAll('.stats-number');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
                const duration = 1500; // ms
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

        // Function to update the table body and pagination
        async function updateTableContent(data) {
            // Update the table body with the new HTML
            document.getElementById('usersTableBody').innerHTML = data.html;

            // Update the pagination links
            document.querySelector('.pagination-wrapper').innerHTML = data.pagination;

            // Update pagination info text
            document.querySelector('.pagination-info').textContent = `Affichage de ${data.users.from} à ${data.users.to} sur ${data.users.total} résultats`;

            // Update the stats cards
            document.querySelector('.stats-card-primary .stats-number').setAttribute('data-count', data.stats.total);
            document.querySelector('.stats-card-success .stats-number').setAttribute('data-count', data.stats.active);
            document.querySelector('.stats-card-warning .stats-number').setAttribute('data-count', data.stats.inactive);
            document.querySelector('.stats-card-info .stats-number').setAttribute('data-count', data.stats.recent);
            animateNumbers(); // Re-animate the counters

            // Re-apply row animations
            animateTableRows();

            // Update the grid view as well
            updateGridView(data.users.data); // Pass the users data from the response

            // Reset select all checkbox
            document.getElementById('selectAll').checked = false;
        }

        // New function to update the grid view
        function updateGridView(users) {
            const gridContainer = document.getElementById('gridContainer');
            gridContainer.innerHTML = ''; // Clear existing cards

            if (users.length > 0) {
                users.forEach((user, index) => {
                    const userCard = document.createElement('div');
                    userCard.className = 'user-card animate__animated animate__fadeInUp';
                    userCard.setAttribute('data-user-id', user.id);
                    userCard.style.animationDelay = `${index * 0.05}s`;

                    let avatarHtml = '';
                    if (user.avatar) {
                        avatarHtml = `<img src="/storage/${user.avatar}" alt="Avatar de ${user.name}" class="avatar-img">`;
                    } else {
                        avatarHtml = `<div class="avatar-placeholder">${user.name.substring(0, 1).toUpperCase()}</div>`;
                    }

                    const rolesHtml = user.roles.map(role => `<span class="badge badge-role">${role.name}</span>`).join('');

                    userCard.innerHTML = `
                        <div class="user-card-header">
                            <div class="user-card-avatar user-avatar">
                                ${avatarHtml}
                                <div class="status-indicator status-${user.status}"></div>
                            </div>
                            <div class="user-card-info">
                                <h5>${user.name}</h5>
                                <p>ID: ${user.id}</p>
                            </div>
                        </div>
                        <div class="user-card-details">
                            <div class="user-card-detail">
                                <span><i class="fas fa-envelope me-2 text-muted"></i>Email:</span>
                                <span>${user.email}</span>
                            </div>
                            <div class="user-card-detail">
                                <span><i class="fas fa-phone me-2 text-muted"></i>Téléphone:</span>
                                <span>${user.phone || '-'}</span>
                            </div>
                            <div class="user-card-detail">
                                <span><i class="fas fa-user-tag me-2 text-muted"></i>Rôle:</span>
                                <span>${rolesHtml || '<span class="badge badge-secondary">Aucun rôle</span>'}</span>
                            </div>
                            <div class="user-card-detail">
                                <span><i class="fas fa-power-off me-2 text-muted"></i>Statut:</span>
                                <div class="form-check form-switch d-flex align-items-center justify-content-center">
                                    <input class="form-check-input status-toggle-switch" type="checkbox" id="gridStatusSwitch-${user.id}"
                                           data-user-id="${user.id}" ${user.status === 'active' ? 'checked' : ''}>
                                    <label class="form-check-label ms-2 status-label ${user.status}" for="gridStatusSwitch-${user.id}">
                                        ${user.status === 'active' ? 'Actif' : 'Inactif'}
                                    </label>
                                </div>
                            </div>
                            <div class="user-card-detail">
                                <span><i class="fas fa-calendar-alt me-2 text-muted"></i>Créé le:</span>
                                <span>${new Date(user.created_at).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric', hour: '2-digit', minute: '2-digit' }).replace(',', '')}</span>
                            </div>
                        </div>
                        <div class="user-card-actions">
                            <a href="/users/${user.id}" class="btn btn-action btn-view tooltip-custom" data-tooltip="Voir" title="Voir">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="/users/${user.id}/edit" class="btn btn-action btn-edit tooltip-custom" data-tooltip="Modifier" title="Modifier">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-action btn-delete tooltip-custom" data-tooltip="Supprimer" onclick="deleteUser(${user.id})" title="Supprimer">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    `;
                    gridContainer.appendChild(userCard);
                });
            } else {
                gridContainer.innerHTML = `
                    <div class="col-12 empty-state">
                        <div class="empty-icon"><i class="fas fa-users"></i></div>
                        <h4>Aucun utilisateur trouvé</h4>
                        <p>Commencez par créer votre premier utilisateur pour voir la liste ici.</p>
                        <a href="{{ route('users.create') }}" class="btn btn-primary btn-floating">
                            <i class="fas fa-plus me-2"></i> Créer un utilisateur
                        </a>
                    </div>
                `;
            }
        }


        // Fonction de filtrage et de récupération des données (AJAX)
        async function filterTableAndFetch() {
            const searchTerm = document.getElementById('searchInput').value;
            const statusFilter = document.getElementById('statusFilter').value;
            const roleFilter = document.getElementById('roleFilter').value;

            try {
                const response = await axios.get("{{ route('users.index') }}", {
                    params: {
                        search: searchTerm,
                        status: statusFilter,
                        role: roleFilter,
                        page: 1 // Toujours revenir à la première page lors d'un nouveau filtre
                    },
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });

                updateTableContent(response.data); // Use the new function to update all sections

            } catch (error) {
                console.error("Erreur lors du filtrage ou de la récupération des données:", error);
                // Gérer l'erreur, par exemple afficher un message
                showCustomAlert('Erreur lors du filtrage des utilisateurs.', 'danger');
            }
        }

        // Handle pagination links click via AJAX
        document.querySelector('.pagination-wrapper').addEventListener('click', async function(e) {
            if (e.target.tagName === 'A' && e.target.href.includes('page=')) {
                e.preventDefault();
                const url = new URL(e.target.href);
                const page = url.searchParams.get('page');

                const searchTerm = document.getElementById('searchInput').value;
                const statusFilter = document.getElementById('statusFilter').value;
                const roleFilter = document.getElementById('roleFilter').value;

                try {
                    const response = await axios.get("{{ route('users.index') }}", {
                        params: {
                            search: searchTerm,
                            status: statusFilter,
                            role: roleFilter,
                            page: page
                        },
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    updateTableContent(response.data); // Use the new function to update all sections

                    // Scroll to top of table
                    window.scrollTo({
                        top: document.querySelector('.table-card').offsetTop - 50,
                        behavior: 'smooth'
                    });

                } catch (error) {
                    console.error("Error fetching paginated users:", error);
                    // Show an error message
                    showCustomAlert('Erreur lors du chargement des utilisateurs paginés.', 'danger');
                }
            }
        });


        // Gestion du changement de statut (Toggle Switch)
        // LE CODE FIXÉ EST ICI
        document.addEventListener('change', async function(event) {
            if (event.target.classList.contains('status-toggle-switch')) {
                const userId = event.target.dataset.userId;
                const newStatus = event.target.checked ? 'active' : 'inactive';

                // Trouver tous les éléments de statut liés à cet utilisateur (pour les deux vues)
                const statusLabels = document.querySelectorAll(`.status-label[for$="-${userId}"]`);
                const statusIndicators = document.querySelectorAll(`.status-indicator[class*="status-"][class*="status-${newStatus === 'active' ? 'inactive' : 'active'}"]`);
                const toggleSwitches = document.querySelectorAll(`.status-toggle-switch[data-user-id="${userId}"]`);

                // Afficher un loader si vous le souhaitez
                toggleSwitches.forEach(sw => sw.disabled = true);

                try {
                    const response = await axios.patch(`/users/${userId}/toggle-status`, {
                        status: newStatus,
                        _token: "{{ csrf_token() }}"
                    });

                    if (response.data.success) {
                        // Mettre à jour tous les éléments pertinents pour cet utilisateur
                        statusLabels.forEach(label => {
                            label.textContent = newStatus === 'active' ? 'Actif' : 'Inactif';
                            label.classList.remove('active', 'inactive');
                            label.classList.add(newStatus);
                        });

                        document.querySelectorAll(`.status-indicator`).forEach(indicator => {
                            if (indicator.closest(`[data-user-id="${userId}"]`)) {
                                indicator.classList.remove('status-active', 'status-inactive');
                                indicator.classList.add('status-' + newStatus);
                            }
                        });


                        // S'assurer que tous les interrupteurs sont synchronisés
                        toggleSwitches.forEach(sw => {
                            sw.checked = (newStatus === 'active');
                            sw.disabled = false;
                        });

                        showCustomAlert('Statut mis à jour avec succès!', 'success');
                        // Optionnel: Recharger les statistiques si nécessaire
                        filterTableAndFetch();

                    } else {
                        // Revenir à l'état initial si le serveur renvoie une erreur
                        event.target.checked = !event.target.checked;
                        toggleSwitches.forEach(sw => sw.disabled = false);
                        showCustomAlert(`Erreur: ${response.data.message || 'Impossible de mettre à jour le statut.'}`, 'danger');
                    }
                } catch (error) {
                    console.error("Erreur AJAX lors du changement de statut:", error);
                    // Revenir à l'état initial si une erreur réseau ou serveur se produit
                    event.target.checked = !event.target.checked;
                    toggleSwitches.forEach(sw => sw.disabled = false);
                    showCustomAlert('Erreur réseau ou serveur. Veuillez réessayer.', 'danger');
                }
            }
        });

        // Custom alert function
        function showCustomAlert(message, type = 'success') {
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${type} alert-dismissible fade show custom-alert`;
            alertDiv.innerHTML = `
                <i class="fas fa-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;
            document.querySelector('.container-fluid.px-4').prepend(alertDiv);

            setTimeout(() => {
                alertDiv.style.animation = 'slideOutUp 0.5s ease forwards';
                alertDiv.addEventListener('animationend', () => alertDiv.remove());
            }, 3000);
        }
    });
</script>
@endpush