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

        <div class="row mb-5">
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
                            {{ $consultantsPaginated->appends(request()->except('page_consultant'))->links('vendor.pagination.bootstrap-5') }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <hr>

        <div class="row mb-5">
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
        <hr>

        <div class="row mb-5">
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
                            {{ $admisPaginated->appends(request()->except('page_admis'))->links('vendor.pagination.bootstrap-5') }}
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
        .grid-container { display: none; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 1.5rem; padding: 1.5rem; animation: fadeInUp 0.5s ease; }
        .grid-container.grid-show { display: grid; }
        .user-card { background: white; border-radius: var(--border-radius); box-shadow: var(--box-shadow); padding: 1.5rem; transition: var(--transition); border: 1px solid #f1f3f4; }
        .user-card:hover { transform: translateY(-5px); box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15); }
        .user-card-header { display: flex; align-items: center; margin-bottom: 1rem; }
        .user-card-avatar { margin-right: 1rem; }
        .user-card-info h5 { margin: 0; color: var(--dark-color); font-weight: 600; }
        .user-card-info p { margin: 0; color: #666; font-size: 0.9rem; }
        .user-card-details { display: flex; flex-direction: column; margin-bottom: 1rem; }
        .user-card-detail { display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; padding: 0.5rem 0; border-bottom: 1px solid #f8f9fa; }
        .user-card-detail:last-child { border-bottom: none; }
        .user-card-actions { display: flex; justify-content: center; gap: 0.5rem; }
        @keyframes pulse { 0% { transform: scale(1); } 50% { transform: scale(1.05); } 100% { transform: scale(1); } }
        .stats-card:hover .stats-icon { animation: pulse 1s infinite; }
        @keyframes slideOutUp { from { opacity: 1; transform: translateY(0); } to { opacity: 0; transform: translateY(-30px); } }
        .btn-loading { position: relative; pointer-events: none; }
        .btn-loading::after { content: ''; position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 16px; height: 16px; border: 2px solid transparent; border-top: 2px solid currentColor; border-radius: 50%; animation: spin 1s linear infinite; }
        @keyframes spin { 0% { transform: translate(-50%, -50%) rotate(0deg); } 100% { transform: translate(-50%, -50%) rotate(360deg); } }
        .tooltip-custom { position: relative; }
        .tooltip-custom::before { content: ''; position: absolute; bottom: calc(100% + 5px); left: 50%; transform: translateX(-50%); border-width: 5px; border-style: solid; border-color: var(--dark-color) transparent transparent transparent; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease; z-index: 1001; }
        .tooltip-custom::after { content: attr(data-tooltip); position: absolute; bottom: calc(100% + 10px); left: 50%; transform: translateX(-50%); background: var(--dark-color); color: white; padding: 0.5rem 0.75rem; border-radius: 6px; font-size: 0.8rem; white-space: nowrap; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease, transform 0.3s ease; z-index: 1000; }
        .tooltip-custom:hover::before, .tooltip-custom:hover::after { opacity: 1; visibility: visible; transform: translateX(-50%) translateY(-5px); }
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

        async function filterAndFetchGroup(group, page = 1) {
            const filters = {
                search: document.getElementById('searchInput').value,
                status: document.getElementById('statusFilter').value,
                role: document.getElementById('roleFilter').value,
                group: group,
                [`page_${group}`]: page
            };
            
            try {
                const response = await axios.get("{{ route('users.index') }}", {
                    params: filters,
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });

                updateGroupTable(group, response.data.users.data, response.data.pagination);
            } catch (error) {
                console.error(`Erreur lors du chargement des utilisateurs pour le groupe ${group}:`, error);
            }
        }
        
        function updateGroupTable(group, users, pagination) {
            const tableCard = document.querySelector(`.table-card[data-group="${group}"]`);
            if (!tableCard) return;

            const tbody = tableCard.querySelector('tbody');
            const paginationWrapper = tableCard.querySelector('.pagination-wrapper');
            const paginationInfo = tableCard.querySelector('.pagination-info');
            
            if (users.length > 0) {
                let html = '';
                users.forEach(user => {
                    const avatarHtml = user.avatar ? `<img src="/storage/${user.avatar}" alt="Avatar de ${user.name}" class="avatar-img">` : `<div class="avatar-placeholder">${user.name.substring(0, 1).toUpperCase()}</div>`;
                    const rolesHtml = user.roles.map(role => `<span class="badge badge-role">${role.name}</span>`).join('') || '<span class="badge badge-secondary">Aucun rôle</span>';

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
                                           {{ $user->status === 'active' ? 'checked' : '' }}>
                                    <label class="form-check-label ms-2 status-label ${user.status}" for="statusSwitch-${user.id}">${user.status === 'active' ? 'Actif' : 'Inactif'}</label>
                                </div>
                            </td>
                            <td>
                                <span class="date-text">${new Date(user.created_at).toLocaleDateString('fr-FR', { day: '2-digit', month: '2-digit', year: 'numeric' })}</span>
                                <small class="date-time">${new Date(user.created_at).toLocaleTimeString('fr-FR', { hour: '2-digit', minute: '2-digit' })}</small>
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
                paginationWrapper.innerHTML = pagination;
                
                const firstItem = users.from || 0;
                const lastItem = users.to || 0;
                const total = users.total || 0;
                paginationInfo.textContent = `Affichage de ${firstItem} à ${lastItem} sur ${total} résultats`;
                
                tableCard.style.display = 'block';
                animateTableRows();
            } else {
                tbody.innerHTML = `
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="fas fa-users"></i></div>
                                <h4>Aucun utilisateur trouvé</h4>
                                <p>Aucun utilisateur ne correspond à vos critères de recherche.</p>
                            </div>
                        </td>
                    </tr>
                `;
                paginationWrapper.innerHTML = '';
            }
        }

        document.getElementById('searchInput').addEventListener('input', () => {
            filterAndFetchGroup('consultant');
            filterAndFetchGroup('etudiant');
            filterAndFetchGroup('admis');
        });
        
        document.getElementById('statusFilter').addEventListener('change', () => {
            filterAndFetchGroup('consultant');
            filterAndFetchGroup('etudiant');
            filterAndFetchGroup('admis');
        });
        
        document.getElementById('roleFilter').addEventListener('change', () => {
            const selectedRole = document.getElementById('roleFilter').value;
            const groups = ['consultant', 'etudiant', 'admis'];
            
            groups.forEach(group => {
                const tableCard = document.querySelector(`.table-card[data-group="${group}"]`);
                if (selectedRole === '' || selectedRole === group) {
                    tableCard.parentElement.style.display = 'block';
                    filterAndFetchGroup(group);
                } else {
                    tableCard.parentElement.style.display = 'none';
                }
            });
        });
        
        document.querySelectorAll('.pagination-wrapper').forEach(wrapper => {
            wrapper.addEventListener('click', async function(e) {
                if (e.target.tagName === 'A' || e.target.closest('a')) {
                    e.preventDefault();
                    const link = e.target.closest('a');
                    const url = new URL(link.href);
                    
                    if(url.searchParams.has('page_consultant')) {
                        const page = url.searchParams.get('page_consultant');
                        filterAndFetchGroup('consultant', page);
                    } else if (url.searchParams.has('page_etudiant')) {
                        const page = url.searchParams.get('page_etudiant');
                        filterAndFetchGroup('etudiant', page);
                    } else if (url.searchParams.has('page_admis')) {
                        const page = url.searchParams.get('page_admis');
                        filterAndFetchGroup('admis', page);
                    }
                    window.scrollTo({ top: this.closest('.table-card').offsetTop - 50, behavior: 'smooth' });
                }
            });
        });

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

        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                alert.style.animation = 'slideOutUp 0.5s ease forwards';
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        let userToDelete = null;
        window.deleteUser = function(userId) {
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

        function animateTableRows() {
            const rows = document.querySelectorAll('.table-row');
            rows.forEach((row, index) => {
                row.style.animationDelay = `${index * 0.05}s`;
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

        // --- Code pour le toggle de statut ---
        document.querySelectorAll('.status-toggle-switch').forEach(toggle => {
            toggle.addEventListener('change', function() {
                const userId = this.getAttribute('data-user-id');
                const newStatus = this.checked ? 'active' : 'inactive';
                const statusLabel = this.nextElementSibling;
                const initialChecked = !this.checked;

                // Ajouter un état de chargement visuel
                const loadingHtml = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
                statusLabel.innerHTML = loadingHtml;
                this.disabled = true;

                axios.put(`/users/${userId}/toggle-status/${newStatus}`, {
                    _token: '{{ csrf_token() }}'
                })
                .then(response => {
                    if (response.data.success) {
                        // Mettre à jour le texte et la couleur du label de statut
                        statusLabel.textContent = newStatus === 'active' ? 'Actif' : 'Inactif';
                        statusLabel.classList.remove('active', 'inactive');
                        statusLabel.classList.add(newStatus);
                        
                        // Mettre à jour le point d'indicateur de statut sur l'avatar
                        const statusIndicator = document.querySelector(`.table-row[data-user-id="${userId}"] .status-indicator`);
                        if (statusIndicator) {
                            statusIndicator.classList.remove('status-active', 'status-inactive');
                            statusIndicator.classList.add(`status-${newStatus}`);
                        }

                        // Afficher une alerte de succès
                        showCustomAlert(response.data.message, 'success');
                    } else {
                        // Si la requête échoue, annuler le changement de la case à cocher et afficher une alerte d'erreur
                        this.checked = initialChecked;
                        statusLabel.textContent = initialChecked ? 'Actif' : 'Inactif';
                        statusLabel.classList.remove('active', 'inactive');
                        statusLabel.classList.add(initialChecked ? 'active' : 'inactive');
                        showCustomAlert(response.data.message, 'danger');
                    }
                })
                .catch(error => {
                    console.error('Erreur lors du changement de statut:', error);
                    // En cas d'erreur de la requête, annuler le changement et afficher une alerte
                    this.checked = initialChecked;
                    statusLabel.textContent = initialChecked ? 'Actif' : 'Inactif';
                    statusLabel.classList.remove('active', 'inactive');
                    statusLabel.classList.add(initialChecked ? 'active' : 'inactive');
                    showCustomAlert('Une erreur est survenue. Veuillez réessayer.', 'danger');
                })
                .finally(() => {
                    // Réactiver le bouton
                    this.disabled = false;
                });
            });
        });
        // --- Fin du code pour le toggle de statut ---

        // Fonction pour afficher une alerte personnalisée
        function showCustomAlert(message, type) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show custom-alert" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            const container = document.querySelector('.container-fluid');
            container.insertAdjacentHTML('afterbegin', alertHtml);

            // Faire disparaître l'alerte après 5 secondes
            setTimeout(() => {
                const alert = container.querySelector('.alert');
                if (alert) {
                    alert.style.animation = 'slideOutUp 0.5s ease forwards';
                    setTimeout(() => alert.remove(), 500);
                }
            }, 5000);
        }

    });
</script>
@endpush