@extends('layouts.app')

@section('content')
<style>
    .modern-container {
       
        min-height: 100vh;
        padding: 40px 0;
    }
    
    .modern-card {
        background: #ffffff;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        overflow: hidden;
        animation: slideUp 0.6s ease-out;
    }
    
    @keyframes slideUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    .modern-header {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        color: white;
        padding: 30px;
        position: relative;
        overflow: hidden;
    }
    
    .modern-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        animation: pulse 3s ease-in-out infinite;
    }
    
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.1); }
    }
    
    .modern-header h4 {
        margin: 0;
        font-size: 28px;
        font-weight: 700;
        position: relative;
        z-index: 1;
    }
    
    .status-badge {
        padding: 8px 20px;
        border-radius: 50px;
        font-weight: 600;
        font-size: 14px;
        position: relative;
        z-index: 1;
        display: inline-block;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
    }
    
    .status-pending {
        background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        color: white;
    }
    
    .status-approved {
        background: linear-gradient(135deg, #10b981 0%, #059669 100%);
        color: white;
    }
    
    .status-rejected {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .modern-body {
        padding: 40px;
        max-height: 70vh;
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #C2185B #f1f1f1;
    }
    
    .modern-body::-webkit-scrollbar {
        width: 8px;
    }
    
    .modern-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .modern-body::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        border-radius: 10px;
    }
    
    .modern-body::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #D32F2F 0%, #ef4444 100%);
    }
    
    .info-row {
        padding: 20px;
        margin-bottom: 15px;
        border-radius: 15px;
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        transition: all 0.3s ease;
        border-left: 4px solid #C2185B;
        display: flex;
        align-items: flex-start;
        gap: 15px;
    }
    
    .info-row:hover {
        transform: translateX(10px);
        box-shadow: 0 8px 25px rgba(194, 24, 91, 0.2);
    }
    
    .info-icon {
        width: 45px;
        height: 45px;
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 4px 15px rgba(194, 24, 91, 0.3);
    }
    
    .info-icon i {
        color: white;
        font-size: 20px;
    }
    
    .info-content {
        flex: 1;
    }
    
    .info-label {
        color: #C2185B;
        font-weight: 700;
        font-size: 16px;
        margin-bottom: 8px;
        display: block;
    }
    
    .info-value {
        color: #333;
        font-size: 15px;
        line-height: 1.6;
    }
    
    .alert-modern {
        border-radius: 15px;
        border: none;
        padding: 20px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        animation: fadeIn 0.5s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }
    
    .alert-success-modern {
        background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
        color: #065f46;
        border-left: 5px solid #10b981;
    }
    
    .alert-danger-modern {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        color: #7f1d1d;
        border-left: 5px solid #ef4444;
    }
    
    .alert-info-modern {
        background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
        color: #1e3a8a;
        border-left: 5px solid #3b82f6;
    }
    
    .modern-btn {
        padding: 12px 30px;
        border-radius: 50px;
        font-weight: 600;
        border: none;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        position: relative;
        overflow: hidden;
    }
    
    .modern-btn::before {
        content: '';
        position: absolute;
        top: 50%;
        left: 50%;
        width: 0;
        height: 0;
        border-radius: 50%;
        background: rgba(255,255,255,0.3);
        transform: translate(-50%, -50%);
        transition: width 0.6s, height 0.6s;
    }
    
    .modern-btn:hover::before {
        width: 300px;
        height: 300px;
    }
    
    .modern-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.3);
    }
    
    .btn-primary-modern {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        color: white;
    }
    
    .btn-secondary-modern {
        background: linear-gradient(135deg, #6b7280 0%, #4b5563 100%);
        color: white;
    }
    
    .btn-warning-modern {
        background: linear-gradient(135deg, #f59e0b 0%, #f97316 100%);
        color: white;
    }
    
    .btn-danger-modern {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
    }
    
    .modern-footer {
        background: #f8f9fa;
        padding: 25px 40px;
        border-top: 3px solid #C2185B;
    }
    
    .file-download-btn {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        color: white;
        padding: 10px 25px;
        border-radius: 50px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        margin-right: 10px;
        margin-bottom: 10px;
        transition: all 0.3s ease;
        box-shadow: 0 4px 15px rgba(194, 24, 91, 0.3);
    }
    
    .file-download-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(194, 24, 91, 0.5);
        color: white;
    }
    
    .comment-box {
        background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
        border-left: 5px solid #f59e0b;
        padding: 20px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }
    
    .comment-box.danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
        border-left: 5px solid #ef4444;
    }
</style>

<div class="modern-container">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-10">
                @if(session('success'))
                    <div class="alert-modern alert-success-modern mb-4">
                        {{ session('success') }}
                        <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('error'))
                    <div class="alert-modern alert-danger-modern mb-4">
                        {{ session('error') }}
                        <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                @if(session('info'))
                    <div class="alert-modern alert-info-modern mb-4">
                        {{ session('info') }}
                        <button type="button" class="btn-close float-end" data-bs-dismiss="alert"></button>
                    </div>
                @endif

                <div class="modern-card">
                    <div class="modern-header d-flex justify-content-between align-items-center">
                        <h4><i class="fas fa-file-alt me-2"></i> Détails de la documentation</h4>
                        <div>
                            @if($documentation->status === 'pending')
                                <span class="status-badge status-pending"><i class="fas fa-clock me-1"></i> En attente</span>
                            @elseif($documentation->status === 'approved')
                                <span class="status-badge status-approved"><i class="fas fa-check-circle me-1"></i> Approuvée</span>
                            @else
                                <span class="status-badge status-rejected"><i class="fas fa-times-circle me-1"></i> Rejetée</span>
                            @endif
                        </div>
                    </div>
                    
                    <div class="modern-body">
                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-book"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Module</span>
                                <div class="info-value">{{ $documentation->module->title }}</div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Consultant</span>
                                <div class="info-value">{{ $documentation->consultant->name }}</div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-align-left"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Description</span>
                                <div class="info-value">{{ $documentation->description }}</div>
                            </div>
                        </div>

                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-calendar-alt"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Date de soumission</span>
                                <div class="info-value">{{ $documentation->created_at->format('d/m/Y H:i') }}</div>
                            </div>
                        </div>

                        @if($documentation->verified_at)
                            <div class="info-row">
                                <div class="info-icon">
                                    <i class="fas fa-check"></i>
                                </div>
                                <div class="info-content">
                                    <span class="info-label">Date de vérification</span>
                                    <div class="info-value">{{ $documentation->verified_at->format('d/m/Y H:i') }}</div>
                                </div>
                            </div>

                            @if($documentation->verifiedBy)
                                <div class="info-row">
                                    <div class="info-icon">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div class="info-content">
                                        <span class="info-label">Vérifié par</span>
                                        <div class="info-value">{{ $documentation->verifiedBy->name }}</div>
                                    </div>
                                </div>
                            @endif
                        @endif

                        @if($documentation->admin_comment)
                            <div class="info-row">
                                <div class="info-icon">
                                    <i class="fas fa-comment"></i>
                                </div>
                                <div class="info-content">
                                    <span class="info-label">Commentaire admin</span>
                                    <div class="info-value">
                                        <div class="comment-box {{ $documentation->status === 'rejected' ? 'danger' : '' }}">
                                            {{ $documentation->admin_comment }}
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif

                        <div class="info-row">
                            <div class="info-icon">
                                <i class="fas fa-paperclip"></i>
                            </div>
                            <div class="info-content">
                                <span class="info-label">Fichiers</span>
                                <div class="info-value">
                                    @if($documentation->file_path)
                                        <a href="{{ route('documentations.download', $documentation->id) }}" 
                                           class="file-download-btn">
                                            <i class="fas fa-download"></i> Télécharger le fichier
                                        </a>
                                    @elseif($documentation->files)
                                        @foreach($documentation->files as $index => $file)
                                            <a href="{{ route('documentations.download', [$documentation->id, $index]) }}" 
                                               class="file-download-btn">
                                                <i class="fas fa-download"></i> Télécharger fichier {{ $index + 1 }}
                                            </a>
                                        @endforeach
                                    @else
                                        <span class="text-muted">Aucun fichier</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="modern-footer">
                        <div class="d-flex justify-content-between align-items-center">
                            <a href="{{ route('consultant.documentations.index') }}" class="modern-btn btn-secondary-modern">
                                <i class="fas fa-arrow-left me-2"></i> Retour
                            </a>
                            
                            @if($documentation->isPending() && Auth::id() === $documentation->consultant_id)
                                <div>
                                    <a href="{{ route('consultant.documentations.edit', $documentation->id) }}" 
                                       class="modern-btn btn-warning-modern me-2">
                                        <i class="fas fa-edit me-2"></i> Modifier
                                    </a>
                                    
                                    <form action="{{ route('consultant.documentations.destroy', $documentation->id) }}" 
                                          method="POST" 
                                          class="d-inline"
                                          onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette documentation ?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="modern-btn btn-danger-modern">
                                            <i class="fas fa-trash me-2"></i> Supprimer
                                        </button>
                                    </form>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection