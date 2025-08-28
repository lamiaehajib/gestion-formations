@extends('layouts.app')
@section('content')
<style>
    .card-shadow {
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    
    .gradient-text {
        background: linear-gradient(135deg, #D32F2F, #C2185B, #ef4444);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        border: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .btn-primary::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #B71C1C, #AD1457);
        transition: left 0.3s ease;
        z-index: 0;
    }
    
    .btn-primary:hover::before {
        left: 0;
    }
    
    .btn-primary span, .btn-primary i {
        position: relative;
        z-index: 1;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 15px 30px rgba(211, 47, 47, 0.4);
    }
    
    .modern-table {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid #e5e7eb;
    }
    
    .table-header {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-bottom: 2px solid #e2e8f0;
    }
    
    .table-row {
        transition: all 0.3s ease;
        border-bottom: 1px solid #f1f5f9;
    }
    
    .table-row:hover {
        background: linear-gradient(135deg, rgba(211, 47, 47, 0.03), rgba(194, 24, 91, 0.03));
        transform: translateX(4px);
        border-left: 4px solid #D32F2F;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
    }
    
    .action-btn {
        padding: 8px 16px;
        border-radius: 8px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 12px;
        transition: all 0.2s ease;
        border: 2px solid transparent;
        position: relative;
        overflow: hidden;
    }
    
    .btn-view {
        background: linear-gradient(135deg, #3b82f6, #1d4ed8);
        color: white;
    }
    
    .btn-view:hover {
        background: linear-gradient(135deg, #1d4ed8, #1e3a8a);
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(59, 130, 246, 0.3);
    }
    
    .btn-edit {
        background: linear-gradient(135deg, #f59e0b, #d97706);
        color: white;
    }
    
    .btn-edit:hover {
        background: linear-gradient(135deg, #d97706, #b45309);
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(245, 158, 11, 0.3);
    }
    
    .btn-delete {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
        border: none;
    }
    
    .btn-delete:hover {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        transform: scale(1.05);
        box-shadow: 0 8px 20px rgba(239, 68, 68, 0.3);
    }
    
    .header-card {
        background: white;
        border-radius: 20px;
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
    }
    
    .header-card:hover {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        transform: translateY(-2px);
    }
    
    .role-avatar {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 18px;
        font-weight: bold;
        margin-right: 16px;
        transition: all 0.3s ease;
    }
    
    .table-row:hover .role-avatar {
        transform: rotate(5deg) scale(1.1);
        box-shadow: 0 8px 20px rgba(211, 47, 47, 0.3);
    }
    
    .success-alert {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        border-radius: 12px;
        border: none;
        animation: slideDown 0.5s ease-out;
    }
    
    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .page-container {
        background: #ffffff;
        min-height: 100vh;
        padding: 32px 16px;
    }
    
    .floating-header {
        background: white;
        backdrop-filter: blur(10px);
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
    }
    
    .icon-container {
        width: 56px;
        height: 56px;
        border-radius: 16px;
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        box-shadow: 0 8px 20px rgba(211, 47, 47, 0.3);
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% {
            transform: scale(1);
        }
        50% {
            transform: scale(1.05);
        }
    }
    
    .stats-badge {
        background: linear-gradient(135deg, #f8fafc, #e2e8f0);
        color: #475569;
        padding: 4px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        border: 1px solid #e2e8f0;
    }
</style>

<div class="page-container">
    <div class="container mx-auto max-w-7xl">
        <!-- Header Section -->
        <div class="header-card floating-header p-8 mb-8 card-shadow">
            <div class="flex items-center justify-between">
                <div class="flex items-center">
                    <div class="icon-container">
                        <i class="fas fa-shield-alt"></i>
                    </div>
                    <div class="ml-6">
                        <h1 class="text-4xl font-black gradient-text mb-2">
                            Gestion des Rôles
                        </h1>
                        <p class="text-gray-600 text-lg">Contrôlez les accès et permissions de votre système</p>
                        <div class="flex items-center mt-3 space-x-4">
                            <span class="stats-badge">
                                <i class="fas fa-users mr-1"></i>
                                {{ count($roles) }} Rôles
                            </span>
                            <span class="stats-badge">
                                <i class="fas fa-clock mr-1"></i>
                                Mis à jour aujourd'hui
                            </span>
                        </div>
                    </div>
                </div>
                
                @can('role-create')
                    <a href="{{ route('roles.create') }}" 
                       class="btn-primary text-white px-8 py-4 rounded-xl font-bold text-lg flex items-center space-x-3 card-shadow">
                        <i class="fas fa-plus-circle"></i>
                        <span>Créer un Rôle</span>
                    </a>
                @endcan
            </div>
        </div>

        <!-- Success Message -->
        @if ($message = Session::get('success'))
            <div class="success-alert p-6 mb-8 card-shadow relative">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-check-circle text-2xl"></i>
                        <div>
                            <h3 class="font-bold text-lg">Succès!</h3>
                            <p class="opacity-90">{{ $message }}</p>
                        </div>
                    </div>
                    <button class="text-white hover:bg-white hover:bg-opacity-20 rounded-full p-2 transition-all" 
                            onclick="this.parentElement.parentElement.style.display='none';">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>
        @endif

        <!-- Main Content Card -->
        <div class="bg-white rounded-2xl card-shadow border border-gray-100 overflow-hidden">
            <!-- Table Header -->
            <div class="table-header p-6 border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <i class="fas fa-list-alt text-gray-600 text-xl"></i>
                        <h2 class="text-2xl font-bold text-gray-800">Liste des Rôles Système</h2>
                    </div>
                    <div class="text-sm text-gray-500">
                        Total: <span class="font-semibold text-gray-700">{{ count($roles) }} rôles</span>
                    </div>
                </div>
            </div>
            
            <!-- Table Content -->
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50 border-b border-gray-200">
                        <tr>
                            <th class="px-8 py-6 text-left">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-tag text-gray-400"></i>
                                    <span class="font-bold text-gray-700 text-sm uppercase tracking-wider">Rôle</span>
                                </div>
                            </th>
                            <th class="px-8 py-6 text-left">
                                <div class="flex items-center space-x-2">
                                    <i class="fas fa-tools text-gray-400"></i>
                                    <span class="font-bold text-gray-700 text-sm uppercase tracking-wider">Actions</span>
                                </div>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100">
                        @foreach ($roles as $key => $role)
                            <tr class="table-row">
                                <td class="px-8 py-6">
                                    <div class="flex items-center">
                                        <div class="role-avatar">
                                            {{ strtoupper(substr($role->name, 0, 2)) }}
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-900">{{ $role->name }}</h3>
                                            <p class="text-gray-500 text-sm mt-1">Rôle système • Créé le {{ date('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="flex items-center space-x-3">
                                        <!-- View Button -->
                                        <a href="{{ route('roles.show', $role->id) }}" 
                                           class="action-btn btn-view flex items-center space-x-2">
                                            <i class="fas fa-eye"></i>
                                            <span>Voir</span>
                                        </a>
                                        
                                        @can('role-edit')
                                            <!-- Edit Button -->
                                            <a href="{{ route('roles.edit', $role->id) }}" 
                                               class="action-btn btn-edit flex items-center space-x-2">
                                                <i class="fas fa-edit"></i>
                                                <span>Modifier</span>
                                            </a>
                                        @endcan
                                        
                                        @can('role-delete')
                                            <!-- Delete Button -->
                                            {!! Form::open(['method' => 'DELETE', 'route' => ['roles.destroy', $role->id], 'class' => 'inline']) !!}
                                                <button type="submit" 
                                                        class="action-btn btn-delete flex items-center space-x-2"
                                                        onclick="return confirm('⚠️ Êtes-vous absolument sûr de vouloir supprimer ce rôle? Cette action est irréversible.')">
                                                    <i class="fas fa-trash-alt"></i>
                                                    <span>Supprimer</span>
                                                </button>
                                            {!! Form::close() !!}
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pagination -->
        @if(method_exists($roles, 'links'))
            <div class="mt-10 flex justify-center">
                <div class="bg-white rounded-xl p-6 card-shadow border border-gray-100">
                    {!! $roles->links() !!}
                </div>
            </div>
        @endif
    </div>
</div>

<!-- Enhanced Pagination Styles -->
<style>
    .pagination {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 8px;
    }
    
    .pagination a,
    .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 44px;
        height: 44px;
        text-decoration: none;
        border-radius: 12px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: 2px solid transparent;
    }
    
    .pagination a {
        background: white;
        color: #374151;
        border-color: #e5e7eb;
    }
    
    .pagination a:hover {
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(211, 47, 47, 0.3);
        border-color: transparent;
    }
    
    .pagination .active span {
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        color: white;
        box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3);
    }
    
    .pagination .disabled span {
        background: #f9fafb;
        color: #d1d5db;
        border-color: #f3f4f6;
    }
    
    .pagination-info {
        margin-top: 16px;
        text-align: center;
        color: #6b7280;
        font-size: 14px;
    }
</style>
@endsection