@extends('layouts.app')

@section('content')
<style>
    .page-bg {
        background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
        min-height: 100vh;
        padding: 32px 16px;
    }
    
    .form-container {
        background: white;
        border-radius: 24px;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
        border: 1px solid rgba(255, 255, 255, 0.2);
        overflow: hidden;
        transition: all 0.3s ease;
    }
    
    .form-container:hover {
        transform: translateY(-4px);
        box-shadow: 0 35px 60px -12px rgba(0, 0, 0, 0.15);
    }
    
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
    
    .form-content {
        padding: 40px;
    }
    
    .input-group {
        margin-bottom: 32px;
        position: relative;
    }
    
    .input-label {
        display: block;
        font-size: 16px;
        font-weight: 700;
        color: #374151;
        margin-bottom: 12px;
        position: relative;
    }
    
    .input-label::after {
        content: '';
        position: absolute;
        bottom: -4px;
        left: 0;
        width: 0;
        height: 3px;
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        transition: width 0.3s ease;
        border-radius: 2px;
    }
    
    .input-group:focus-within .input-label::after {
        width: 100%;
    }
    
    .modern-input {
        width: 100%;
        padding: 16px 20px;
        font-size: 16px;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        background: #f9fafb;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        font-weight: 500;
    }
    
    .modern-input:focus {
        outline: none;
        border-color: #D32F2F;
        background: white;
        box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.1);
        transform: translateY(-2px);
    }
    
    .modern-input::placeholder {
        color: #9ca3af;
        font-weight: 400;
    }
    
    .permissions-section {
        background: #f8fafc;
        border-radius: 20px;
        padding: 32px;
        border: 1px solid #e2e8f0;
        margin-bottom: 32px;
    }
    
    .permissions-header {
        display: flex;
        items-center;
        margin-bottom: 24px;
    }
    
    .permissions-icon {
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
    
    .permission-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
        margin-top: 20px;
    }
    
    .permission-card {
        background: white;
        border: 2px solid #e5e7eb;
        border-radius: 16px;
        padding: 20px;
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .permission-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(211, 47, 47, 0.05), transparent);
        transition: left 0.5s ease;
    }
    
    .permission-card:hover::before {
        left: 100%;
    }
    
    .permission-card:hover {
        border-color: #D32F2F;
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(211, 47, 47, 0.15);
    }
    
    .permission-card.selected {
        border-color: #D32F2F;
        background: linear-gradient(135deg, rgba(211, 47, 47, 0.05), rgba(194, 24, 91, 0.05));
        transform: scale(1.02);
    }
    
    .permission-checkbox {
        width: 20px;
        height: 20px;
        margin-right: 12px;
        accent-color: #D32F2F;
        transform: scale(1.2);
    }
    
    .permission-label {
        display: flex;
        align-items: center;
        font-size: 16px;
        font-weight: 600;
        color: #374151;
        cursor: pointer;
        position: relative;
        z-index: 1;
    }
    
    .permission-description {
        font-size: 14px;
        color: #6b7280;
        margin-top: 8px;
        margin-left: 32px;
    }
    
    .submit-section {
        text-align: center;
        padding-top: 20px;
        border-top: 1px solid #e5e7eb;
    }
    
    .submit-btn {
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        color: white;
        border: none;
        padding: 16px 48px;
        font-size: 18px;
        font-weight: 700;
        border-radius: 16px;
        cursor: pointer;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 1px;
    }
    
    .submit-btn::before {
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
    
    .submit-btn:hover::before {
        left: 0;
    }
    
    .submit-btn span {
        position: relative;
        z-index: 1;
    }
    
    .submit-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 15px 35px rgba(211, 47, 47, 0.4);
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
    
    .error-alert {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        border: 1px solid #f87171;
        color: #991b1b;
        padding: 20px;
        border-radius: 16px;
        margin-bottom: 24px;
        animation: shake 0.5s ease-in-out;
    }
    
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-5px); }
        75% { transform: translateX(5px); }
    }
    
    .error-icon {
        display: inline-block;
        width: 24px;
        height: 24px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        text-align: center;
        line-height: 24px;
        margin-right: 12px;
        font-size: 14px;
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
</style>

<div class="page-bg">
    <div class="container mx-auto max-w-4xl">
        <div class="form-container">
            <!-- Header Section -->
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
                        <span>Nouveau Rôle</span>
                    </div>
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="header-icon">
                            <i class="fas fa-plus-circle"></i>
                        </div>
                        <div class="ml-6">
                            <h1 class="text-4xl font-black mb-2">Créer un Nouveau Rôle</h1>
                            <p class="text-lg opacity-90">Définissez les permissions et accès pour ce rôle</p>
                        </div>
                    </div>
                    
                    <a href="{{ route('roles.index') }}" class="back-btn">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour</span>
                    </a>
                </div>
            </div>

            <!-- Form Content -->
            <div class="form-content">
                <!-- Error Messages -->
                @if (count($errors) > 0)
                    <div class="error-alert">
                        <div class="flex items-start">
                            <div class="error-icon">
                                <i class="fas fa-exclamation"></i>
                            </div>
                            <div class="flex-1">
                                <h3 class="font-bold text-lg mb-2">Attention! Des erreurs ont été détectées</h3>
                                <ul class="list-disc list-inside space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li class="font-medium">{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                {!! Form::open(array('route' => 'roles.store','method'=>'POST')) !!}
                
                <!-- Role Name Input -->
                <div class="input-group">
                    <label class="input-label">
                        <i class="fas fa-tag mr-2"></i>
                        Nom du Rôle
                    </label>
                    {!! Form::text('name', null, array(
                        'placeholder' => 'Ex: Administrateur, Modérateur, Utilisateur...', 
                        'class' => 'modern-input'
                    )) !!}
                </div>

                <!-- Permissions Section -->
                <div class="permissions-section">
                    <div class="permissions-header">
                        <div class="permissions-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div>
                            <h3 class="text-2xl font-bold text-gray-800">Permissions & Accès</h3>
                            <p class="text-gray-600 mt-1">Sélectionnez les permissions à attribuer à ce rôle</p>
                        </div>
                    </div>
                    
                    <div class="permission-grid">
                        @foreach($permission as $value)
                            <div class="permission-card" onclick="togglePermission(this, '{{ $value->name }}')">
                                <label class="permission-label">
                                    {{ Form::checkbox('permission[]', $value->name, false, array(
                                        'class' => 'permission-checkbox',
                                        'id' => 'perm_' . $loop->index
                                    )) }}
                                    <span class="permission-name">{{ ucfirst(str_replace('-', ' ', $value->name)) }}</span>
                                </label>
                                <div class="permission-description">
                                    Autorisation pour: {{ $value->name }}
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="submit-section">
                    <button type="submit" class="submit-btn">
                        <span class="flex items-center">
                            <i class="fas fa-save mr-3"></i>
                            Créer le Rôle
                        </span>
                    </button>
                    <p class="text-sm text-gray-500 mt-4">
                        <i class="fas fa-info-circle mr-1"></i>
                        Ce rôle sera immédiatement disponible après création
                    </p>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<script>
function togglePermission(card, permissionName) {
    const checkbox = card.querySelector('input[type="checkbox"]');
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        card.classList.add('selected');
    } else {
        card.classList.remove('selected');
    }
}

// Auto-select card when checkbox is clicked directly
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const card = this.closest('.permission-card');
            if (this.checked) {
                card.classList.add('selected');
            } else {
                card.classList.remove('selected');
            }
        });
    });
});
</script>

@endsection