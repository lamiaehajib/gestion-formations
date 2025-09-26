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
                    <div class="filter-body {{ request()->except(['page_consultant', 'page_etudiant', 'page_admin']) ? 'show' : '' }}" id="filterCollapse">
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
                                            @php
                                                $roleValue = strtolower($role->name);
                                                // Traiter 'Admin' كـ 'admis' للـ View
                                                $displayValue = ($roleValue === 'admin') ? 'admis' : $roleValue;
                                            @endphp
                                            <option value="{{ $displayValue }}" {{ request('role') == $displayValue ? 'selected' : '' }}>{{ ucfirst($role->name) }}</option>
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

        <div class="row mb-5" id="consultantTableContainer" style="{{ request('role') && request('role') !== 'consultant' ? 'display: none;' : '' }}">
            <div class="col-12">
                <div class="table-card" data-group="consultant">
                    <div class="table-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user-tie me-2"></i>
                            Liste des Consultants
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th><div class="form-check"><input class="form-check-input" type="checkbox" id="selectAllConsultants"></div></th>
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
                            <tbody id="consultantsTableBody">
                                @forelse($consultantsPaginated as $user)
                                    @include('users.partials.user_row', ['user' => $user])
                                @empty
                                    <tr>
                                        <td colspan="9">@include('users.partials.empty_state')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <div class="pagination-info">
                            Affichage de {{ $consultantsPaginated->firstItem() }} à {{ $consultantsPaginated->lastItem() }} sur {{ $consultantsPaginated->total() }} résultats
                        </div>
                        <div class="pagination-wrapper">
                            {{-- Important: on passe 'page_consultant' comme nom de page pour différencier les paginations --}}
                            {{ $consultantsPaginated->appends(request()->except('page_consultant'))->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr id="hrConsultant" style="{{ request('role') && request('role') !== 'consultant' ? 'display: none;' : '' }}">

        <div class="row mb-5" id="etudiantTableContainer" style="{{ request('role') && request('role') !== 'etudiant' ? 'display: none;' : '' }}">
            <div class="col-12">
                <div class="table-card" data-group="etudiant">
                    <div class="table-header">
                        <h5 class="mb-0">
                            <i class="fas fa-graduation-cap me-2"></i>
                            Liste des Étudiants
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th><div class="form-check"><input class="form-check-input" type="checkbox" id="selectAllEtudiants"></div></th>
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
                            <tbody id="etudiantsTableBody">
                                @forelse($etudiantsPaginated as $user)
                                    @include('users.partials.user_row', ['user' => $user])
                                @empty
                                    <tr>
                                        <td colspan="9">@include('users.partials.empty_state')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <div class="pagination-info">
                            Affichage de {{ $etudiantsPaginated->firstItem() }} à {{ $etudiantsPaginated->lastItem() }} sur {{ $etudiantsPaginated->total() }} résultats
                        </div>
                        <div class="pagination-wrapper">
                            {{ $etudiantsPaginated->appends(request()->except('page_etudiant'))->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr id="hrEtudiant" style="{{ request('role') && request('role') !== 'etudiant' ? 'display: none;' : '' }}">

        <div class="row mb-5" id="admisTableContainer" style="{{ request('role') && request('role') !== 'admis' ? 'display: none;' : '' }}">
            <div class="col-12">
                <div class="table-card" data-group="admis">
                    <div class="table-header">
                        <h5 class="mb-0">
                            <i class="fas fa-user-graduate me-2"></i>
                            Liste des Admis
                        </h5>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-modern">
                            <thead>
                                <tr>
                                    <th><div class="form-check"><input class="form-check-input" type="checkbox" id="selectAllAdmis"></div></th>
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
                            <tbody id="admisTableBody">
                                @forelse($admisPaginated as $user)
                                    @include('users.partials.user_row', ['user' => $user])
                                @empty
                                    <tr>
                                        <td colspan="9">@include('users.partials.empty_state')</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="table-footer">
                        <div class="pagination-info">
                            Affichage de {{ $admisPaginated->firstItem() }} à {{ $admisPaginated->lastItem() }} sur {{ $admisPaginated->total() }} résultats
                        </div>
                        <div class="pagination-wrapper">
                            {{ $admisPaginated->appends(request()->except('page_admin'))->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="deleteModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title"><i class="fas fa-exclamation-triangle me-2"></i> Confirmer la suppression</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fas fa-trash-alt fa-3x text-danger mb-3 animate__animated animate__tada animate__infinite"></i>
                    <p class="lead">Êtes-vous sûr de vouloir supprimer cet utilisateur ?</p>
                    <p class="text-danger small">Cette action est irréversible et supprimera toutes les données associées.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary px-4 py-2" data-bs-dismiss="modal">Annuler</button>
                    <button type="button" class="btn btn-danger px-4 py-2" id="confirmDelete"><i class="fas fa-trash me-2"></i> Supprimer Définitivement</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    <style>
        :root { --primary-color: #D32F2F; --secondary-color: #C2185B; --success-color: #4CAF50; --warning-color: #FF9800; --danger-color: #D32F2F; --info-color: #C2185B; --light-color: #f8f9fa; --dark-color: #2c3e50; --border-radius: 12px; --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); --transition: all 0.3s ease; }
        body { background-color: #f0f2f5; font-family: 'Poppins', sans-serif; }
        .page-title { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); -webkit-background-clip: text; -webkit-text-fill-color: transparent; background-clip: text; font-weight: 700; font-size: 2.5rem; margin-bottom: 0.5rem; }
        .stats-card { background: white; border-radius: var(--border-radius); box-shadow: var(--box-shadow); transition: var(--transition); border: none; overflow: hidden; position: relative; }
        .stats-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
        .stats-card-body { padding: 1.5rem; display: flex; align-items: center; justify-content: space-between; }
        .stats-icon { width: 60px; height: 60px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; color: white; flex-shrink: 0; }
        .stats-card-primary .stats-icon { background: linear-gradient(135deg, var(--primary-color), #EF4444); }
        .stats-card-success .stats-icon { background: linear-gradient(135deg, #4CAF50, #45a049); }
        .stats-card-warning .stats-icon { background: linear-gradient(135deg, #FF9800, #f57c00); }
        .stats-card-info .stats-icon { background: linear-gradient(135deg, var(--secondary-color), #D32F2F); }
        .stats-number { font-size: 2rem; font-weight: 700; color: var(--dark-color); margin: 0; flex-grow: 1; text-align: right; }
        .stats-label { color: #666; font-size: 0.9rem; margin: 0; text-align: right; }
        .stats-content { margin-left: 1rem; }
        .filter-card { background: white; border-radius: var(--border-radius); box-shadow: var(--box-shadow); border: none; overflow: hidden; }
        .filter-header { padding: 1rem 1.5rem; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; cursor: pointer; }
        .filter-body { padding: 1.5rem; display: none; }
        .filter-body.show { display: block; animation: slideDown 0.3s ease; }
        @keyframes slideDown { from { opacity: 0; transform: translateY(-10px); } to { opacity: 1; transform: translateY(0); } }
        .table-card { background: white; border-radius: var(--border-radius); box-shadow: var(--box-shadow); border: none; overflow: hidden; }
        .table-header { padding: 1rem 1.5rem; background: linear-gradient(135deg, #f8f9fa, #e9ecef); border-bottom: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; }
        .table-modern { margin: 0; border: none; width: 100%; }
        .table-modern thead th { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; border: none; padding: 1rem 0.75rem; font-weight: 600; text-transform: uppercase; font-size: 0.8rem; letter-spacing: 0.5px; vertical-align: middle; }
        .table-modern tbody tr { transition: var(--transition); border-bottom: 1px solid #f1f3f4; }
        .table-modern tbody tr:hover { background: linear-gradient(135deg, #fff2f2, #fff); transform: scale(1.01); }
        .table-modern tbody td { vertical-align: middle; padding: 0.85rem 0.75rem; white-space: nowrap; }
        .table-row { animation: fadeInUp 0.5s ease; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .user-avatar { position: relative; display: inline-block; }
        .avatar-img { width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 3px solid white; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        .avatar-placeholder { width: 45px; height: 45px; border-radius: 50%; background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; display: flex; align-items: center; justify-content: center; font-weight: 700; font-size: 1.2rem; border: 3px solid white; box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1); }
        .status-indicator { position: absolute; bottom: 0; right: 0; width: 14px; height: 14px; border-radius: 50%; border: 2px solid white; }
        .status-indicator.status-active { background: var(--success-color); }
        .status-indicator.status-inactive { background: var(--danger-color); }
        .user-info { display: flex; flex-direction: column; }
        .user-name { font-weight: 600; color: var(--dark-color); }
        .user-id { color: #666; font-size: 0.8rem; }
        .user-email, .user-phone { color: #666; font-size: 0.9rem; }
        .badge-role { background: linear-gradient(135deg, var(--info-color), var(--primary-color)); color: white; border-radius: 20px; font-size: 0.75rem; padding: 0.4rem 0.8rem; font-weight: 600; margin-right: 0.25rem; }
        .badge-secondary { background: #6c757d; color: white; border-radius: 20px; font-size: 0.75rem; padding: 0.4rem 0.8rem; font-weight: 600; margin-right: 0.25rem; }
        .form-switch { min-width: 90px; }
        .form-switch .form-check-input { width: 3.5em; height: 1.8em; cursor: pointer; background-color: #dee2e6; border-color: #adb5bd; transition: background-color .15s ease-in-out, border-color .15s ease-in-out, box-shadow .15s ease-in-out; float: none; margin-left: 0; display: inline-block; vertical-align: middle; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e"); }
        .form-switch .form-check-input:checked { background-color: var(--success-color); border-color: var(--success-color); background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='-4 -4 8 8'%3e%3ccircle r='3' fill='%23fff'/%3e%3c/svg%3e"); }
        .form-switch .form-check-label { font-weight: 600; color: var(--dark-color); font-size: 0.9rem; vertical-align: middle; }
        .status-label.active { color: var(--success-color); }
        .status-label.inactive { color: var(--danger-color); }
        .date-text { font-weight: 600; color: var(--dark-color); }
        .date-time { color: #666; display: block; font-size: 0.8rem; }
        .action-buttons { display: flex; gap: 0.5rem; justify-content: center; align-items: center; }
        .btn-action { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; border: none; transition: var(--transition); font-size: 1rem; padding: 0; }
        .btn-view { background: rgba(194, 24, 91, 0.1); color: var(--info-color); }
        .btn-view:hover { background: var(--info-color); color: white; transform: scale(1.1); }
        .btn-edit { background: rgba(255, 152, 0, 0.1); color: var(--warning-color); }
        .btn-edit:hover { background: var(--warning-color); color: white; transform: scale(1.1); }
        .btn-delete { background: rgba(211, 47, 47, 0.1); color: var(--danger-color); }
        .btn-delete:hover { background: var(--danger-color); color: white; transform: scale(1.1); }
        .btn-floating { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border: none; border-radius: 50px; padding: 0.75rem 1.5rem; color: white; font-weight: 600; transition: var(--transition); box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3); }
        .btn-floating:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(211, 47, 47, 0.4); color: white; }
        .table-footer { padding: 1rem 1.5rem; background: #f8f9fa; border-top: 1px solid #dee2e6; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; }
        .pagination-info { color: #666; font-size: 0.9rem; }
        .empty-state { text-align: center; padding: 4rem 2rem; }
        .empty-icon { font-size: 4rem; color: #ccc; margin-bottom: 1rem; }
        .empty-state h4 { color: var(--dark-color); margin-bottom: 1rem; }
        .empty-state p { color: #666; margin-bottom: 2rem; }
        .custom-alert { border-radius: var(--border-radius); border: none; box-shadow: var(--box-shadow); animation: slideInDown 0.5s ease; }
        @keyframes slideInDown { from { opacity: 0; transform: translateY(-30px); } to { opacity: 1; transform: translateY(0); } }
        /* ... (CSS pour les cartes et les animations) ... */
        .pagination { gap: 0.5rem; margin-bottom: 0; }
        .pagination .page-link { border: none; border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; color: var(--primary-color); transition: var(--transition); }
        .pagination .page-link:hover { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; transform: scale(1.1); }
        .pagination .page-item.active .page-link { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); color: white; box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3); }
        .table-responsive::-webkit-scrollbar { height: 8px; }
        .table-responsive::-webkit-scrollbar-track { background: #f1f3f4; border-radius: 10px; }
        .table-responsive::-webkit-scrollbar-thumb { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); border-radius: 10px; }
        .table-responsive::-webkit-scrollbar-thumb:hover { background: linear-gradient(135deg, var(--secondary-color), var(--primary-color)); }
        @media (prefers-color-scheme: dark) {
            body { background-color: #1a202c; }
            .stats-card, .filter-card, .table-card, .modal-content, .user-card { background: #2d3748; color: white; }
            .table-modern tbody tr:hover, .user-card:hover { background: linear-gradient(135deg, #4a5568, #2d3748); }
            .filter-header, .table-header, .table-footer, .modal-header, .modal-footer { background: #4a5568; color: white; border-color: #2d3748; }
            .modal-header.bg-danger { background-color: var(--danger-color) !important; }
            .btn-close-white { filter: brightness(2); }
            .stats-number, .user-name, h4, h5 { color: white; }
            .stats-label, .user-id, .user-email, .user-phone, .date-text, .date-time, .pagination-info, .text-muted, .text-gray { color: #a0aec0; }
            .tooltip-custom::before, .tooltip-custom::after { background: #4a5568; }
        }
        @media print {
            .btn, .action-buttons, .filter-card, .table-header, .table-footer { display: none !important; }
            .table-card { box-shadow: none; border: 1px solid #000; }
            .table-modern { font-size: 0.8rem; }
            .stats-card { break-inside: avoid; }
        }
        i.fas.fa-eye { color: #ee1111 !important; }
        button.btn.btn-sm.btn-outline-secondary.toggle-filters, button#toggleView, button.btn.btn-sm.btn-outline-secondary, a.btn.btn-outline-secondary.w-100 { background: linear-gradient(135deg, var(--primary-color), var(--secondary-color)); }
    </style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
<script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        animateNumbers();
        attachStatusToggleListeners(); 

        // Fonction principale pour récupérer et filtrer les données par groupe (avec AJAX)
        async function filterAndFetchGroup(group, page = 1) {
            // Le Controller utilise 'admin' pour 'admis'
            const apiGroup = (group === 'admis') ? 'admin' : group; 

            // Construire les filtres
            const filters = {
                search: document.getElementById('searchInput').value,
                status: document.getElementById('statusFilter').value,
                role: document.getElementById('roleFilter').value, // La valeur dans le filtre du form (admin ou admis)
                group: group, // Le nom du groupe dans le Blade (consultant, etudiant, admis)
                [`page_${apiGroup}`]: page // Paramètre de pagination correct pour l'API
            };
            
            try {
                const tableCard = document.querySelector(`.table-card[data-group="${group}"]`);
                if(tableCard) tableCard.style.opacity = 0.5;

                const response = await axios.get("{{ route('users.index') }}", {
                    params: filters,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                // Mettre à jour le tableau et la pagination
                updateGroupTable(group, response.data.users, response.data.pagination); 

            } catch (error) {
                console.error(`Erreur lors du chargement des utilisateurs pour le groupe ${group}:`, error);
                showCustomAlert(`Erreur de communication avec le serveur pour ${group}.`, 'danger');
            } finally {
                const tableCard = document.querySelector(`.table-card[data-group="${group}"]`);
                if(tableCard) tableCard.style.opacity = 1;
            }
        }
        
        // Fonction pour mettre à jour le contenu de la table spécifique
        function updateGroupTable(group, usersObject, paginationHtml) {
            const tableCard = document.querySelector(`.table-card[data-group="${group}"]`);
            if (!tableCard) return;

            const users = usersObject.data;
            const tbody = tableCard.querySelector('tbody');
            const paginationWrapper = tableCard.querySelector('.pagination-wrapper');
            const paginationInfo = tableCard.querySelector('.pagination-info');
            
            if (users.length > 0) {
                let html = '';
                users.forEach(user => {
                    // Construction du HTML de la ligne (similaire à user_row.blade.php)
                    const avatarHtml = user.avatar ? `<img src="/storage/${user.avatar}" alt="Avatar de ${user.name}" class="avatar-img">` : `<div class="avatar-placeholder">${user.name.substring(0, 1).toUpperCase()}</div>`;
                    const rolesHtml = user.roles.map(role => `<span class="badge badge-role">${role.name}</span>`).join('') || '<span class="badge badge-secondary">Aucun rôle</span>';
                    const statusClass = user.status === 'active' ? 'checked' : '';
                    const dateText = new Date(user.created_at).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' });
                    const dateTime = new Date(user.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' });

                    html += `
                        <tr class="table-row" data-user-id="${user.id}">
                            <td><div class="form-check"><input class="form-check-input user-checkbox" type="checkbox" value="${user.id}"></div></td>
                            <td><div class="user-avatar">${avatarHtml}<div class="status-indicator status-${user.status}"></div></div></td>
                            <td><div class="user-info"><span class="user-name">${user.name}</span><small class="user-id">ID: ${user.id}</small></div></td>
                            <td><span class="user-email">${user.email}</span></td>
                            <td><span class="user-phone">${user.phone || '-'}</span></td>
                            <td>${rolesHtml}</td>
                            <td>
                                <div class="form-check form-switch d-flex align-items-center justify-content-center">
                                    <input class="form-check-input status-toggle-switch" type="checkbox" id="statusSwitch-${user.id}"
                                        data-user-id="${user.id}"
                                        ${statusClass}>
                                    <label class="form-check-label ms-2 status-label ${user.status}" for="statusSwitch-${user.id}">${user.status === 'active' ? 'Actif' : 'Inactif'}</label>
                                </div>
                            </td>
                            <td>
                                <span class="date-text">${dateText}</span>
                                <small class="date-time">${dateTime}</small>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <a href="/users/${user.id}" class="btn btn-action btn-view tooltip-custom" data-tooltip="Voir" title="Voir"><i class="fas fa-eye"></i></a>
                                    <a href="/users/${user.id}/edit" class="btn btn-action btn-edit tooltip-custom" data-tooltip="Modifier" title="Modifier"><i class="fas fa-edit"></i></a>
                                    <button class="btn btn-action btn-delete tooltip-custom" data-tooltip="Supprimer" onclick="deleteUser(${user.id})" title="Supprimer"><i class="fas fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    `;
                });
                tbody.innerHTML = html;
                paginationWrapper.innerHTML = paginationHtml;
                
                const firstItem = usersObject.from || 0;
                const lastItem = usersObject.to || 0;
                const total = usersObject.total || 0;
                paginationInfo.textContent = `Affichage de ${firstItem} à ${lastItem} sur ${total} résultats`;
                
                // Afficher le conteneur du tableau (nécessaire pour la logique de filtrage par rôle)
                tableCard.parentElement.style.display = 'block'; 
                
                attachStatusToggleListeners(); 
                animateTableRows();
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-users"></i></div>
                                <h4>Aucun utilisateur trouvé</h4>
                                <p>Aucun utilisateur ne correspond à vos critères de recherche dans ce groupe.</p>
                            </div>
                        </td>
                    </tr>
                `;
                paginationWrapper.innerHTML = '';
                paginationInfo.textContent = `Affichage de 0 à 0 sur ${usersObject.total || 0} résultats`;
                tableCard.parentElement.style.display = 'block';
            }
        }

        // --- ÉVÉNEMENTS DE FILTRAGE/RECHERCHE ---
        document.getElementById('filterForm').addEventListener('submit', function(e) {
            e.preventDefault(); 
            const selectedRole = document.getElementById('roleFilter').value;
            const groups = ['consultant', 'etudiant', 'admis'];

            // 1. Masquer tous les conteneurs de tableau et les <hr>
            groups.forEach(group => {
                const container = document.getElementById(`${group}TableContainer`);
                const hr = document.getElementById(`hr${group.charAt(0).toUpperCase() + group.slice(1)}`);
                if (container) container.style.display = 'none';
                if (hr) hr.style.display = 'none';
            });

            // 2. Récupérer et afficher seulement les groupes pertinents
            groups.forEach(group => {
                // Si aucun rôle n'est sélectionné OU si le rôle sélectionné correspond au groupe actuel
                if (selectedRole === '' || selectedRole === group) {
                    const container = document.getElementById(`${group}TableContainer`);
                    const hr = document.getElementById(`hr${group.charAt(0).toUpperCase() + group.slice(1)}`);
                    
                    if (container) container.style.display = 'block';
                    if (hr) hr.style.display = 'block';
                    
                    // Lancer la requête AJAX pour charger les données filtrées à la première page
                    filterAndFetchGroup(group, 1);
                }
            });
        });
        
        // Lancer le filtrage automatiquement lors du changement de statut ou de rôle
        document.getElementById('roleFilter').addEventListener('change', function() {
            document.getElementById('filterForm').dispatchEvent(new Event('submit'));
        });

        document.getElementById('statusFilter').addEventListener('change', function() {
            document.getElementById('filterForm').dispatchEvent(new Event('submit'));
        });

        // --- LISTENER DE PAGINATION (FIXED) ---
        document.querySelectorAll('.pagination-wrapper').forEach(wrapper => {
            wrapper.addEventListener('click', async function(e) {
                const link = e.target.closest('a');
                // S'assurer que le clic est sur un lien de pagination et qu'il n'est pas désactivé
                if (link && link.href && !link.classList.contains('disabled')) { 
                    e.preventDefault();
                    
                    const url = new URL(link.href);
                    const tableCard = this.closest('.table-card');
                    const group = tableCard.getAttribute('data-group');
                    
                    let page = 1;
                    
                    // Déterminer le paramètre de page correct (page_consultant, page_etudiant, page_admin)
                    const pageParamName = `page_${(group === 'admis' ? 'admin' : group)}`; // Utiliser 'admin' pour le paramètre d'URL si le groupe est 'admis'
                    
                    // Extraire le numéro de page
                    page = url.searchParams.get(pageParamName) || 1; 
                    
                    if (group) {
                        filterAndFetchGroup(group, page);
                        window.scrollTo({ top: tableCard.offsetTop - 50, behavior: 'smooth' });
                    }
                }
            });
        });

        // Fonction pour attacher les listeners au switch de statut
        function attachStatusToggleListeners() {
            document.querySelectorAll('.status-toggle-switch').forEach(toggle => {
                if (toggle.getAttribute('data-listener-attached')) return;
                toggle.setAttribute('data-listener-attached', 'true');

                toggle.addEventListener('change', function() {
                    const userId = this.getAttribute('data-user-id');
                    const newStatus = this.checked ? 'active' : 'inactive';
                    const statusLabel = this.nextElementSibling;
                    const initialChecked = !this.checked;

                    const loadingHtml = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                    statusLabel.innerHTML = loadingHtml;
                    this.disabled = true;

                    axios.put(`/users/${userId}/toggle-status/${newStatus}`, {
                        _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    })
                    .then(response => {
                        // ... (Logique de succès) ...
                        if (response.data.success) {
                            statusLabel.textContent = newStatus === 'active' ? 'Actif' : 'Inactif';
                            statusLabel.classList.remove('active', 'inactive');
                            statusLabel.classList.add(newStatus);
                            
                            const statusIndicator = document.querySelector(`.table-row[data-user-id="${userId}"] .status-indicator`);
                            if (statusIndicator) {
                                statusIndicator.classList.remove('status-active', 'status-inactive');
                                statusIndicator.classList.add(`status-${newStatus}`);
                            }
                            showCustomAlert(response.data.message, 'success');
                        } else {
                            // ... (Logique d'échec) ...
                            this.checked = initialChecked;
                            statusLabel.textContent = initialChecked ? 'Actif' : 'Inactif';
                            statusLabel.classList.remove('active', 'inactive');
                            statusLabel.classList.add(initialChecked ? 'active' : 'inactive');
                            showCustomAlert(response.data.message, 'danger');
                        }
                    })
                    .catch(error => {
                        // ... (Logique d'erreur) ...
                        this.checked = initialChecked;
                        statusLabel.textContent = initialChecked ? 'Actif' : 'Inactif';
                        statusLabel.classList.remove('active', 'inactive');
                        statusLabel.classList.add(initialChecked ? 'active' : 'inactive');
                        showCustomAlert('Une erreur est survenue. Veuillez réessayer.', 'danger');
                    })
                    .finally(() => {
                        this.disabled = false;
                    });
                });
            });
        }
        
        // ... (Les autres fonctions utilitaires : toggle filter, reset filters, showCustomAlert, animateTableRows, animateNumbers) ...

        document.querySelector('.filter-header').addEventListener('click', function() {
            const filterBody = document.getElementById('filterCollapse');
            const icon = this.querySelector('.toggle-filters i');
            filterBody.classList.toggle('show');
            icon.classList.toggle('fa-chevron-up');
            icon.classList.toggle('fa-chevron-down');
        });

        document.getElementById('resetFilters').addEventListener('click', function(e) {
            e.preventDefault();
            window.location.href = "{{ route('users.index') }}";
        });

        function showCustomAlert(message, type) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show custom-alert" role="alert">
                    <i class="fas ${type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            const container = document.querySelector('.container-fluid');
            container.insertAdjacentHTML('afterbegin', alertHtml);

            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) {
                    alert.style.animation = 'slideOutUp 0.5s ease forwards';
                    setTimeout(() => alert.remove(), 500);
                }
            }, 5000);
        }

        function animateTableRows() {
            const rows = document.querySelectorAll('.table-row');
            rows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`;
                row.classList.remove('animate__fadeInUp');
                void row.offsetWidth;
                row.classList.add('animate__animated', 'animate__fadeInUp');
            });
        }

        function animateNumbers() {
            const counters = document.querySelectorAll('.stats-number');
            counters.forEach(counter => {
                const target = parseInt(counter.getAttribute('data-count'));
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

    });
</script>
@endpush