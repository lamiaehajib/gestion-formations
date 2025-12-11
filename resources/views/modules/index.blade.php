@extends('layouts.app')
@section('content')
<style>
    .formations-container {
        min-height: 100vh;
        padding: 1rem 0;
    }
    
    .content-wrapper {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin: 0 0.5rem;
    }
    
    .header-section {
        background: linear-gradient(45deg, #C2185B, #D32F2F);
        color: white;
        padding: 1.5rem;
        position: relative;
    }
    
    .header-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="20" cy="20" r="1" fill="white" opacity="0.1"/><circle cx="80" cy="40" r="0.5" fill="white" opacity="0.1"/><circle cx="40" cy="80" r="1.5" fill="white" opacity="0.1"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }
    
    .header-content {
        position: relative;
        z-index: 1;
    }
    
    .main-title {
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 1rem 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }
    
    .create-btn {
        background: linear-gradient(45deg, #ef4444, #C2185B);
        border: none;
        padding: 10px 20px;
        border-radius: 25px;
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        display: inline-block;
        font-size: 0.9rem;
        width: 100%;
        text-align: center;
    }
    
    .create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.6);
        color: white;
    }
    
    .success-alert {
        background: linear-gradient(45deg, #4caf50, #2e7d32);
        color: white;
        border: none;
        border-radius: 15px;
        padding: 1rem;
        margin: 1rem;
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        font-size: 0.9rem;
    }
    
    .table-container {
        padding: 1rem;
        background: white;
    }
    
    /* Mobile Card View */
    .mobile-card {
        background: white;
        border-radius: 15px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.08);
        border-left: 4px solid #C2185B;
        transition: all 0.3s ease;
    }
    
    .mobile-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(194, 24, 91, 0.15);
    }
    
    .module-title {
        font-weight: 600;
        color: #333;
        font-size: 1.1rem;
        margin-bottom: 0.75rem;
    }
    
    .module-description {
        color: #666;
        font-size: 0.9rem;
        margin-top: 0.25rem;
    }
    
    .formations-badge {
        background: linear-gradient(45deg, #C2185B, #ef4444);
        color: white;
        padding: 6px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        margin: 0.5rem 0;
        display: inline-block;
    }
    
    .view-btn {
        background: linear-gradient(45deg, #D32F2F, #ef4444);
        border: none;
        color: white;
        padding: 10px 20px;
        border-radius: 20px;
        text-decoration: none;
        font-weight: 500;
        transition: all 0.3s ease;
        box-shadow: 0 3px 10px rgba(211, 47, 47, 0.3);
        display: block;
        text-align: center;
        width: 100%;
        margin-top: 0.75rem;
    }
    
    .view-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(211, 47, 47, 0.5);
        color: white;
    }
    
    /* Desktop Table View - Hidden on Mobile */
    .table-modern {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: none;
        display: none;
    }
    
    .table-modern thead {
        background: linear-gradient(45deg, #C2185B, #D32F2F);
        color: white;
    }
    
    .table-modern thead th {
        border: none;
        padding: 1.2rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .table-modern tbody tr {
        transition: all 0.3s ease;
        border: none;
    }
    
    .table-modern tbody tr:hover {
        background: linear-gradient(45deg, rgba(194, 24, 91, 0.05), rgba(211, 47, 47, 0.05));
        transform: translateX(5px);
        box-shadow: 0 5px 15px rgba(194, 24, 91, 0.1);
    }
    
    .table-modern tbody td {
        padding: 1.2rem;
        border: none;
        vertical-align: middle;
        border-bottom: 1px solid rgba(194, 24, 91, 0.1);
    }
    
    .mobile-view {
        display: block;
    }
    
    /* Tablet and Desktop */
    @media (min-width: 768px) {
        .formations-container {
            padding: 2rem 0;
        }
        
        .content-wrapper {
            margin: 0;
        }
        
        .header-section {
            padding: 2rem;
        }
        
        .main-title {
            font-size: 2.5rem;
            margin: 0;
        }
        
        .header-content {
            display: flex !important;
            justify-content: space-between;
            align-items: center;
        }
        
        .create-btn {
            width: auto;
            padding: 12px 24px;
        }
        
        .success-alert {
            margin: 1.5rem;
        }
        
        .table-container {
            padding: 2rem;
        }
        
        .table-modern {
            display: table;
        }
        
        .mobile-view {
            display: none;
        }
        
        .view-btn {
            width: auto;
            display: inline-block;
        }
    }
    
    /* Large Desktop */
    @media (min-width: 1200px) {
        .content-wrapper {
            max-width: 1200px;
            margin: 0 auto;
        }
    }
    
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
    }
    
    .empty-state p {
        color: #999;
        font-size: 1rem;
    }
</style>

<div class="formations-container">
    <div class="container">
        <div class="content-wrapper">
            <div class="header-section">
                <div class="header-content">
                    <h2 class="main-title">
                        @if(auth()->user()->hasRole('Consultant'))
                            📚 Mes Modules
                        @else
                            Formations & Modules
                        @endif
                    </h2>
                    @can('module-create')
                    <a href="{{ route('modules.create') }}" class="create-btn">
                        ✨ Créer un nouveau Module
                    </a>
                    @endcan
                </div>
            </div>
            
            @if (session('success'))
                <div class="success-alert">
                    🎉 {{ session('success') }}
                </div>
            @endif
            
            <div class="table-container">
                {{-- ✅ Consultant: Modules uniques --}}
                @if(auth()->user()->hasRole('Consultant') && $uniqueModules->count() > 0)
                    
                    {{-- Mobile View --}}
                    <div class="mobile-view">
                        @foreach($uniqueModules as $module)
                        <div class="mobile-card">
                            <div class="module-title">{{ $module->title }}</div>
                            <a href="{{ route('modules.details', $module->id) }}" class="view-btn">
                                👁️ Voir Détails
                            </a>
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- Desktop Table --}}
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Titre du Module</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($uniqueModules as $module)
                            <tr>
                                <td>
                                    <div class="module-title">{{ $module->title }}</div>
                                </td>
                                <td>
                                    <a href="{{ route('modules.details', $module->id) }}" class="view-btn">
                                        👁️ Voir Détails
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                
                {{-- ✅ Formations normales --}}
                @elseif($formations->count() > 0)
                    
                    {{-- Mobile View --}}
                    <div class="mobile-view">
                        @foreach($formations as $formation)
                        <div class="mobile-card">
                            <div class="module-title">{{ $formation->title }}</div>
                            <span class="formations-badge">{{ $formation->modules_count }} Modules</span>
                            <a href="{{ route('modules.show', ['formation' => $formation->id]) }}" class="view-btn">
                                Voir les Modules
                            </a>
                        </div>
                        @endforeach
                    </div>
                    
                    {{-- Desktop Table --}}
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Titre de la Formation</th>
                                <th>Nombre de Modules</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formations as $formation)
                            <tr>
                                <td>
                                    <div class="module-title">{{ $formation->title }}</div>
                                </td>
                                <td>
                                    <span class="formations-badge">{{ $formation->modules_count }} Modules</span>
                                </td>
                                <td>
                                    <a href="{{ route('modules.show', ['formation' => $formation->id]) }}" class="view-btn">
                                        Voir les Modules
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                @else
                    <div class="empty-state">
                        <p>Aucun module disponible</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection