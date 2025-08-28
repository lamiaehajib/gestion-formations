@extends('layouts.app')

@section('content')
<style>
    /* * Global & Layout Styles 
     */
    .page-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
        padding: 32px 16px;
    }

    .details-container {
        background: white;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .details-container:hover {
        transform: translateY(-4px);
        box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.15);
    }

    /* * Header Section Styles 
     */
    .header-section {
        background: linear-gradient(135deg, #D32F2F, #C2185B, #ef4444);
        padding: 32px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .header-section::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 100%;
        background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
        animation: float 6s ease-in-out infinite;
    }
    
    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-20px); }
    }
    
    .header-icon {
        width: 64px;
        height: 64px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 16px;
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    .breadcrumb {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
        font-size: 14px;
        color: rgba(255, 255, 255, 0.8);
    }
    
    .breadcrumb-item {
        display: flex;
        align-items: center;
    }
    
    .breadcrumb-separator {
        margin: 0 12px;
        opacity: 0.6;
    }

    .back-btn {
        background: linear-gradient(135deg, #6b7280, #374151);
        color: white;
        text-decoration: none;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    
    .back-btn:hover {
        background: linear-gradient(135deg, #374151, #1f2937);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(107, 114, 128, 0.3);
        color: white;
        text-decoration: none;
    }

    /* * Content Section Styles 
     */
    .content-section {
        padding: 40px;
    }
    
    .info-card {
        background: #f8fafc;
        border-radius: 20px;
        padding: 32px;
        border: 1px solid #e2e8f0;
        margin-bottom: 32px;
    }

    .info-header {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
    }

    .info-icon {
        width: 48px;
        height: 48px;
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 20px;
        margin-right: 16px;
    }

    .detail-item {
        margin-bottom: 24px;
        border-bottom: 1px solid #e5e7eb;
        padding-bottom: 16px;
    }
    
    .detail-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }

    .detail-label {
        display: block;
        font-size: 16px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 8px;
    }
    
    .detail-value {
        font-size: 18px;
        font-weight: 600;
        color: #1f2937;
        background-color: #e5e7eb;
        padding: 8px 12px;
        border-radius: 8px;
        display: inline-block;
    }

    .permissions-grid {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-top: 20px;
    }

    .permission-badge {
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 14px;
        transition: transform 0.2s ease;
    }

    .permission-badge:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
    }
    
    .no-permissions {
        font-style: italic;
        color: #6b7280;
        font-size: 16px;
        padding: 12px;
        background: #f3f4f6;
        border-radius: 12px;
        border: 1px dashed #d1d5db;
    }
</style>

<div class="page-bg">
    <div class="container mx-auto max-w-4xl">
        <div class="details-container">
            <div class="header-section">
                <div class="breadcrumb">
                    <div class="breadcrumb-item">
                        <i class="fas fa-home"></i>
                        <span class="ml-2">Accueil</span>
                    </div>
                    <div class="breadcrumb-separator">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="breadcrumb-item">
                        <i class="fas fa-shield-alt"></i>
                        <span class="ml-2">Rôles</span>
                    </div>
                    <div class="breadcrumb-separator">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="breadcrumb-item">
                        <span>Détails du Rôle</span>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="header-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="ml-6">
                            <h1 class="text-4xl font-black mb-2">Détails du Rôle</h1>
                            <p class="text-lg opacity-90">Visualisation des informations pour le rôle "{{ $role->name }}"</p>
                        </div>
                    </div>

                    <a href="{{ route('roles.index') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour</span>
                    </a>
                </div>
            </div>

            <div class="content-section">
                <div class="info-card">
                    <div class="info-header">
                        <div class="info-icon">
                            <i class="fas fa-id-badge"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">Informations sur le Rôle</h3>
                            <p class="text-gray-600 mt-1">Détails de base et permissions assignées.</p>
                        </div>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Nom du Rôle:</span>
                        <span class="detail-value">{{ $role->name }}</span>
                    </div>
                    
                    <div class="detail-item">
                        <span class="detail-label">Permissions:</span>
                        <div class="permissions-grid">
                            @if(!empty($rolePermissions))
                                @foreach($rolePermissions as $v)
                                    <span class="permission-badge">{{ $v->name }}</span>
                                @endforeach
                            @else
                                <span class="no-permissions">Aucune permission assignée à ce rôle.</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection