@extends('layouts.app')

@section('content')
<style>
    .page-wrapper {
        background: linear-gradient(135deg, #f1f5f9 0%, #e2e8f0 100%);
        min-height: 100vh;
        padding: 40px 20px;
    }
    
    .edit-container {
        background: white;
        border-radius: 28px;
        box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.12);
        border: 1px solid rgba(255, 255, 255, 0.3);
        overflow: hidden;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
    }
    
    .edit-container::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #D32F2F, #C2185B, #ef4444);
        z-index: 1;
    }
    
    .edit-container:hover {
        transform: translateY(-8px);
        box-shadow: 0 40px 80px -12px rgba(0, 0, 0, 0.18);
    }
    
    .header-zone {
        background: linear-gradient(135deg, #1e293b, #334155, #475569);
        padding: 40px;
        color: white;
        position: relative;
        overflow: hidden;
    }
    
    .header-zone::after {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(211, 47, 47, 0.1) 0%, transparent 50%);
        animation: rotate 20s linear infinite;
    }
    
    @keyframes rotate {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    
    .edit-badge {
        display: inline-flex;
        align-items: center;
        background: rgba(255, 255, 255, 0.15);
        padding: 8px 16px;
        border-radius: 20px;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 16px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .header-icon {
        width: 72px;
        height: 72px;
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        border-radius: 20px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 32px;
        margin-bottom: 20px;
        box-shadow: 0 12px 30px rgba(211, 47, 47, 0.3);
        animation: editPulse 3s ease-in-out infinite;
        position: relative;
        z-index: 2;
    }
    
    @keyframes editPulse {
        0%, 100% { 
            transform: scale(1);
            box-shadow: 0 12px 30px rgba(211, 47, 47, 0.3);
        }
        50% { 
            transform: scale(1.05);
            box-shadow: 0 16px 40px rgba(211, 47, 47, 0.4);
        }
    }
    
    .content-area {
        padding: 48px;
    }
    
    .form-section {
        margin-bottom: 40px;
    }
    
    .field-wrapper {
        margin-bottom: 36px;
        position: relative;
    }
    
    .field-label {
        display: block;
        font-size: 18px;
        font-weight: 800;
        color: #1f2937;
        margin-bottom: 16px;
        position: relative;
        padding-left: 24px;
    }
    
    .field-label::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 4px;
        height: 20px;
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        border-radius: 2px;
    }
    
    .field-label::after {
        content: '';
        position: absolute;
        bottom: -8px;
        left: 0;
        width: 0;
        height: 2px;
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        transition: width 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border-radius: 1px;
    }
    
    .field-wrapper:focus-within .field-label::after {
        width: 100%;
    }
    
    .edit-input {
        width: 100%;
        padding: 20px 24px;
        font-size: 18px;
        font-weight: 600;
        border: 3px solid #e2e8f0;
        border-radius: 20px;
        background: #f8fafc;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        color: #1f2937;
    }
    
    .edit-input:focus {
        outline: none;
        border-color: #D32F2F;
        background: white;
        box-shadow: 0 0 0 6px rgba(211, 47, 47, 0.1);
        transform: translateY(-4px);
    }
    
    .edit-input::placeholder {
        color: #94a3b8;
        font-weight: 500;
    }
    
    .permissions-zone {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-radius: 24px;
        padding: 40px;
        border: 2px solid #e2e8f0;
        margin-bottom: 40px;
        position: relative;
    }
    
    .permissions-zone::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #D32F2F, #C2185B, #ef4444);
        border-radius: 24px 24px 0 0;
    }
    
    .permissions-header {
        display: flex;
        align-items: center;
        margin-bottom: 32px;
    }
    
    .permissions-icon {
        width: 56px;
        height: 56px;
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-size: 24px;
        margin-right: 20px;
        box-shadow: 0 8px 25px rgba(211, 47, 47, 0.3);
    }
    
    .permission-matrix {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
        gap: 20px;
        margin-top: 24px;
    }
    
    .permission-tile {
        background: white;
        border: 3px solid #e5e7eb;
        border-radius: 20px;
        padding: 24px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    
    .permission-tile::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(211, 47, 47, 0.08), transparent);
        transition: left 0.6s ease;
    }
    
    .permission-tile:hover::before {
        left: 100%;
    }
    
    .permission-tile:hover {
        border-color: #D32F2F;
        transform: translateY(-6px) scale(1.02);
        box-shadow: 0 16px 40px rgba(211, 47, 47, 0.2);
    }
    
    .permission-tile.active {
        border-color: #D32F2F;
        background: linear-gradient(135deg, rgba(211, 47, 47, 0.08), rgba(194, 24, 91, 0.05));
        transform: scale(1.03);
        box-shadow: 0 12px 35px rgba(211, 47, 47, 0.25);
    }
    
    .permission-checkbox {
        width: 24px;
        height: 24px;
        margin-right: 16px;
        accent-color: #D32F2F;
        transform: scale(1.3);
        cursor: pointer;
    }
    
    .permission-text {
        display: flex;
        align-items: center;
        font-size: 18px;
        font-weight: 700;
        color: #374151;
        cursor: pointer;
        position: relative;
        z-index: 1;
    }
    
    .permission-meta {
        font-size: 14px;
        color: #6b7280;
        margin-top: 12px;
        margin-left: 40px;
        font-weight: 500;
    }
    
    .action-zone {
        text-align: center;
        padding-top: 32px;
        border-top: 2px solid #e5e7eb;
        position: relative;
    }
    
    .action-zone::before {
        content: '';
        position: absolute;
        top: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 2px;
        background: linear-gradient(135deg, #D32F2F, #C2185B);
    }
    
    .update-btn {
        background: linear-gradient(135deg, #D32F2F, #C2185B);
        color: white;
        border: none;
        padding: 20px 60px;
        font-size: 20px;
        font-weight: 800;
        border-radius: 20px;
        cursor: pointer;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 2px;
        box-shadow: 0 12px 30px rgba(211, 47, 47, 0.3);
    }
    
    .update-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #B71C1C, #AD1457);
        transition: left 0.4s ease;
        z-index: 0;
    }
    
    .update-btn:hover::before {
        left: 0;
    }
    
    .update-btn span {
        position: relative;
        z-index: 1;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 12px;
    }
    
    .update-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 20px 50px rgba(211, 47, 47, 0.4);
    }
    
    .back-link {
        background: linear-gradient(135deg, #64748b, #475569);
        color: white;
        text-decoration: none;
        padding: 16px 32px;
        border-radius: 16px;
        font-weight: 700;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 12px;
        position: relative;
        z-index: 2;
    }
    
    .back-link:hover {
        background: linear-gradient(135deg, #475569, #334155);
        transform: translateY(-3px);
        box-shadow: 0 12px 30px rgba(100, 116, 139, 0.3);
        color: white;
        text-decoration: none;
    }
    
    .error-notification {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        border: 2px solid #f87171;
        color: #991b1b;
        padding: 24px;
        border-radius: 20px;
        margin-bottom: 32px;
        animation: errorShake 0.6s ease-in-out;
        position: relative;
        overflow: hidden;
    }
    
    .error-notification::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(135deg, #ef4444, #dc2626);
    }
    
    @keyframes errorShake {
        0%, 100% { transform: translateX(0); }
        25% { transform: translateX(-8px); }
        75% { transform: translateX(8px); }
    }
    
    .error-header {
        display: flex;
        align-items: center;
        margin-bottom: 16px;
    }
    
    .error-icon {
        width: 32px;
        height: 32px;
        background: #ef4444;
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 16px;
        font-size: 16px;
    }
    
    .breadcrumb-nav {
        display: flex;
        align-items: center;
        margin-bottom: 24px;
        font-size: 16px;
        color: rgba(255, 255, 255, 0.8);
        position: relative;
        z-index: 2;
    }
    
    .breadcrumb-item {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    
    .breadcrumb-sep {
        margin: 0 16px;
        opacity: 0.6;
    }
    
    .role-name-display {
        background: rgba(255, 255, 255, 0.1);
        padding: 8px 16px;
        border-radius: 12px;
        font-weight: 700;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
</style>

<div class="page-wrapper">
    <div class="container mx-auto max-w-5xl">
        <div class="edit-container">
            <!-- Header Zone -->
            <div class="header-zone">
                <div class="breadcrumb-nav">
                    <div class="breadcrumb-item">
                        <i class="fas fa-home"></i>
                        <span>Accueil</span>
                    </div>
                    <div class="breadcrumb-sep">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="breadcrumb-item">
                        <i class="fas fa-shield-alt"></i>
                        <span>Rôles</span>
                    </div>
                    <div class="breadcrumb-sep">
                        <i class="fas fa-chevron-right"></i>
                    </div>
                    <div class="breadcrumb-item">
                        <span>Modification</span>
                    </div>
                </div>
                
                <div class="edit-badge">
                    <i class="fas fa-edit mr-2"></i>
                    Mode Édition
                </div>
                
                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <div class="header-icon">
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="ml-8">
                            <h1 class="text-5xl font-black mb-4">Modifier le Rôle</h1>
                            <p class="text-xl opacity-90 mb-4">Ajustez les permissions et paramètres</p>
                            <div class="role-name-display">
                                <i class="fas fa-tag mr-2"></i>
                                Rôle actuel: {{ $role->name }}
                            </div>
                        </div>
                    </div>
                    
                    <a href="{{ route('roles.index') }}" class="back-link">
                        <i class="fas fa-arrow-left"></i>
                        <span>Retour</span>
                    </a>
                </div>
            </div>

            <!-- Content Area -->
            <div class="content-area">
                <!-- Error Messages -->
                @if (count($errors) > 0)
                    <div class="error-notification">
                        <div class="error-header">
                            <div class="error-icon">
                                <i class="fas fa-exclamation-triangle"></i>
                            </div>
                            <h3 class="text-xl font-bold">Erreurs détectées dans le formulaire</h3>
                        </div>
                        <ul class="list-disc list-inside space-y-2 text-lg font-semibold">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {!! Form::model($role, ['method' => 'PATCH','route' => ['roles.update', $role->id]]) !!}
                
                <!-- Role Name Section -->
                <div class="form-section">
                    <div class="field-wrapper">
                        <label class="field-label">
                            <i class="fas fa-signature mr-2"></i>
                            Nom du Rôle
                        </label>
                        {!! Form::text('name', null, array(
                            'placeholder' => 'Entrez le nouveau nom du rôle...', 
                            'class' => 'edit-input'
                        )) !!}
                    </div>
                </div>

                <!-- Permissions Zone -->
                <div class="permissions-zone">
                    <div class="permissions-header">
                        <div class="permissions-icon">
                            <i class="fas fa-key"></i>
                        </div>
                        <div>
                            <h3 class="text-3xl font-black text-gray-800">Permissions & Droits d'Accès</h3>
                            <p class="text-lg text-gray-600 mt-2">Modifiez les autorisations de ce rôle</p>
                        </div>
                    </div>
                    
                    <div class="permission-matrix">
                        @foreach($permission as $value)
                            <div class="permission-tile {{ in_array($value->id, $rolePermissions) ? 'active' : '' }}" 
                                 onclick="toggleEditPermission(this, '{{ $value->name }}')">
                                <label class="permission-text">
                                    {{ Form::checkbox('permission[]', $value->name, 
                                        in_array($value->id, $rolePermissions) ? true : false, 
                                        array('class' => 'permission-checkbox', 'id' => 'edit_perm_' . $loop->index)
                                    ) }}
                                    <span>{{ ucfirst(str_replace('-', ' ', $value->name)) }}</span>
                                </label>
                                <div class="permission-meta">
                                    Permission: {{ $value->name }}
                                    @if(in_array($value->id, $rolePermissions))
                                        <span class="text-green-600 font-bold ml-2">✓ Activée</span>
                                    @else
                                        <span class="text-gray-400 font-medium ml-2">○ Désactivée</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Action Zone -->
                <div class="action-zone">
                    <button type="submit" class="update-btn">
                        <span>
                            <i class="fas fa-save"></i>
                            Mettre à Jour
                        </span>
                    </button>
                    <p class="text-gray-500 mt-6 text-lg">
                        <i class="fas fa-info-circle mr-2"></i>
                        Les modifications seront appliquées immédiatement
                    </p>
                </div>

                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>

<script>
function toggleEditPermission(tile, permissionName) {
    const checkbox = tile.querySelector('input[type="checkbox"]');
    checkbox.checked = !checkbox.checked;
    
    const metaText = tile.querySelector('.permission-meta');
    
    if (checkbox.checked) {
        tile.classList.add('active');
        metaText.innerHTML = `Permission: ${permissionName} <span class="text-green-600 font-bold ml-2">✓ Activée</span>`;
    } else {
        tile.classList.remove('active');
        metaText.innerHTML = `Permission: ${permissionName} <span class="text-gray-400 font-medium ml-2">○ Désactivée</span>`;
    }
}

// Initialize active states and handle direct checkbox clicks
document.addEventListener('DOMContentLoaded', function() {
    const checkboxes = document.querySelectorAll('.permission-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            const tile = this.closest('.permission-tile');
            const permissionName = this.value;
            const metaText = tile.querySelector('.permission-meta');
            
            if (this.checked) {
                tile.classList.add('active');
                metaText.innerHTML = `Permission: ${permissionName} <span class="text-green-600 font-bold ml-2">✓ Activée</span>`;
            } else {
                tile.classList.remove('active');
                metaText.innerHTML = `Permission: ${permissionName} <span class="text-gray-400 font-medium ml-2">○ Désactivée</span>`;
            }
        });
    });
});
</script>

@endsection