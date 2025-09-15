@extends('layouts.app')
@section('content')
<style>
    .formations-container {
       
        min-height: 100vh;
        padding: 2rem 0;
    }
    
    .content-wrapper {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
        overflow: hidden;
    }
    
    .header-section {
        background: linear-gradient(45deg, #C2185B, #D32F2F);
        color: white;
        padding: 2rem;
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
        font-size: 2.5rem;
        font-weight: 700;
        margin: 0;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        background: linear-gradient(45deg, #ffffff, #f8f9fa);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .create-btn {
        background: linear-gradient(45deg, #ef4444, #C2185B);
        border: none;
        padding: 12px 24px;
        border-radius: 25px;
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(239, 68, 68, 0.4);
        position: relative;
        overflow: hidden;
    }
    
    .create-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: left 0.5s ease;
    }
    
    .create-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(239, 68, 68, 0.6);
        color: white;
    }
    
    .create-btn:hover::before {
        left: 100%;
    }
    
    .success-alert {
        background: linear-gradient(45deg, #4caf50, #2e7d32);
        color: white;
        border: none;
        border-radius: 15px;
        padding: 1rem;
        margin: 1.5rem;
        box-shadow: 0 5px 15px rgba(76, 175, 80, 0.3);
        animation: slideIn 0.5s ease;
    }
    
    @keyframes slideIn {
        from { opacity: 0; transform: translateY(-20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    .card-modern {
        border: none;
        border-radius: 0 0 20px 20px;
        box-shadow: none;
        background: transparent;
    }
    
    .table-container {
        padding: 2rem;
        background: white;
    }
    
    .table-modern {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        border: none;
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
        position: relative;
    }
    
    .table-modern thead th::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 2px;
        background: linear-gradient(90deg, #ef4444, transparent);
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
    
    .formation-title {
        font-weight: 600;
        color: #333;
        font-size: 1.1rem;
    }
    
    .modules-badge {
        background: linear-gradient(45deg, #C2185B, #ef4444);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        box-shadow: 0 3px 10px rgba(194, 24, 91, 0.3);
        border: none;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% { box-shadow: 0 3px 10px rgba(194, 24, 91, 0.3); }
        50% { box-shadow: 0 5px 20px rgba(194, 24, 91, 0.5); }
        100% { box-shadow: 0 3px 10px rgba(194, 24, 91, 0.3); }
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
        position: relative;
        overflow: hidden;
    }
    
    .view-btn::before {
        content: '👁️';
        position: absolute;
        left: -30px;
        transition: left 0.3s ease;
    }
    
    .view-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(211, 47, 47, 0.5);
        color: white;
        padding-left: 35px;
    }
    
    .view-btn:hover::before {
        left: 10px;
    }
    
    .floating-shapes {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
        overflow: hidden;
    }
    
    .shape {
        position: absolute;
        border-radius: 50%;
        animation: float 6s ease-in-out infinite;
        opacity: 0.1;
    }
    
    .shape1 {
        width: 100px;
        height: 100px;
        background: #C2185B;
        top: 20%;
        left: 10%;
        animation-delay: 0s;
    }
    
    .shape2 {
        width: 150px;
        height: 150px;
        background: #D32F2F;
        top: 60%;
        right: 15%;
        animation-delay: 2s;
    }
    
    .shape3 {
        width: 80px;
        height: 80px;
        background: #ef4444;
        bottom: 20%;
        left: 20%;
        animation-delay: 4s;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        25% { transform: translateY(-20px) rotate(90deg); }
        50% { transform: translateY(-10px) rotate(180deg); }
        75% { transform: translateY(-15px) rotate(270deg); }
    }
    
    @media (max-width: 768px) {
        .main-title {
            font-size: 1.8rem;
        }
        
        .header-section {
            padding: 1.5rem;
        }
        
        .table-container {
            padding: 1rem;
        }
        
        .table-modern {
            font-size: 0.9rem;
        }
    }
</style>

<div class="floating-shapes">
    <div class="shape shape1"></div>
    <div class="shape shape2"></div>
    <div class="shape shape3"></div>
</div>

<div class="formations-container">
    <div class="container">
        <div class="content-wrapper">
            <div class="header-section">
                <div class="header-content d-flex justify-content-between align-items-center">
                    <h2 class="main-title">Formations & Modules</h2>
                    @can('module-create')
                    <a href="{{ route('modules.create') }}" class="create-btn">
                        ✨ Create New Module
                    </a>
                    @endcan
                </div>
            </div>
            
            @if (session('success'))
                <div class="success-alert">
                    🎉 {{ session('success') }}
                </div>
            @endif
            
            <div class="card-modern">
                <div class="table-container">
                    <table class="table table-modern">
                        <thead>
                            <tr>
                                <th>Formation Title</th>
                                <th>Number of Modules</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($formations as $formation)
                            <tr>
                                <td>
                                    <div class="formation-title">{{ $formation->title }}</div>
                                </td>
                                <td>
                                    <span class="modules-badge">{{ $formation->modules_count }} Modules</span>
                                </td>
                                <td>
                                    <a href="{{ route('modules.show', ['formation' => $formation->id]) }}" class="view-btn">
                                        View Modules
                                    </a>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection