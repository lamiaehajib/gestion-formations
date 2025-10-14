@extends('layouts.app')

@section('content')

<style>
    /* Styles Personnalisés pour intégrer la palette */
    .bg-primary-dark { background-color: #C2185B; }
    .text-primary-dark { color: #C2185B; }
    .border-primary-dark { border-color: #C2185B; }
    
    /* Bouton d'Approbation (Principal Action) */
    .btn-approve {
        background-color: #D32F2F; /* Rouge intense pour approuver */
        border-color: #D32F2F;
        color: white;
        transition: all 0.2s;
    }
    .btn-approve:hover {
        background-color: #C2185B; /* Bordeaux au hover */
        border-color: #C2185B;
        color: white;
    }

    /* Bouton de Rejet (Alerte Action) */
    .btn-reject {
        background-color: #ef4444; /* Rouge vif pour rejeter */
        border-color: #ef4444;
        color: white;
        transition: all 0.2s;
    }
    .btn-reject:hover {
        background-color: #D32F2F;
        border-color: #D32F2F;
        color: white;
    }

    /* Styles pour les Badges de Statut */
    .badge-pending {
        background-color: #ffc107; /* Jaune pour En Attente */
        color: #333;
    }
    .badge-approved {
        background-color: #28a745; /* Vert pour Approuvé */
        color: white;
    }
    .badge-rejected {
        background-color: #dc3545; /* Rouge pour Rejeté */
        color: white;
    }

    .info-card-header {
        background-color: #C2185B;
        color: white;
        border-bottom: 3px solid #D32F2F;
    }

    .form-action-group {
        border: 1px solid #ddd;
        border-radius: 0.5rem;
        padding: 1.5rem;
        margin-bottom: 1rem;
        transition: box-shadow 0.3s ease;
    }
    .form-action-group.approve:hover {
        box-shadow: 0 0 10px rgba(211, 47, 47, 0.3); /* Ombre rouge au survol */
    }
    .form-action-group.reject {
        border-color: #ef4444;
    }
    .form-action-group.reject:hover {
        box-shadow: 0 0 10px rgba(239, 68, 68, 0.5); /* Ombre rouge vif au survol */
    }

</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-10">
            
            <h1 class="mb-4 fw-bold text-primary-dark">
                <i class="fas fa-search-plus me-2"></i> Revue de la Documentation
            </h1>
            
            {{-- Messages de Session --}}
            @if(session('success'))
                <div class="alert alert-success d-flex align-items-center" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            
            @if(session('error'))
                <div class="alert alert-danger d-flex align-items-center" role="alert">
                    <i class="fas fa-times-circle me-2"></i>
                    <div>{{ session('error') }}</div>
                </div>
            @endif
            
            {{-- Carte d'Information Principale --}}
            <div class="card shadow-lg border-0 rounded-xl mb-4">
                <div class="card-header info-card-header rounded-top-xl">
                    <h3 class="h5 m-0 fw-bold">Détails de la Soumission</h3>
                </div>
                <div class="card-body p-4">
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Consultant:</strong> <span class="fw-semibold">{{ $documentation->consultant->name ?? 'N/A' }}</span></p>
                            <p class="mb-1"><strong>Module:</strong> <span class="fw-semibold">{{ $documentation->module->title ?? 'N/A' }}</span></p>
                            <p class="mb-1"><strong>Date de Soumission:</strong> {{ $documentation->created_at->format('d/m/Y H:i') }}</p>
                        </div>
                        <div class="col-md-6 text-md-end">
                            <h4 class="mb-1">Statut Actuel:</h4>
                            <span class="badge badge-lg rounded-pill p-2 text-uppercase badge-{{ $documentation->status == 'approved' ? 'approved' : ($documentation->status == 'rejected' ? 'rejected' : 'pending') }}">
                                {{ ucfirst($documentation->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <hr class="my-3">

                    <div class="mb-3">
                        <p class="fw-bold mb-1 text-primary-dark">Description:</p>
                        <div class="p-3 bg-light rounded border">
                            <p class="m-0">{{ $documentation->description }}</p>
                        </div>
                    </div>

                    @if($documentation->admin_comment)
                        <div class="mb-3">
                            <p class="fw-bold mb-1 text-secondary">Commentaire Précédent:</p>
                            <div class="alert alert-warning p-2 m-0">{{ $documentation->admin_comment }}</div>
                        </div>
                    @endif
                    
                    @if($documentation->verified_at)
                        <p class="text-muted small">
                            <i class="fas fa-user-check me-1"></i> Dernièrement vérifié par **{{ $documentation->verifiedBy->name ?? 'N/A' }}** le {{ $documentation->verified_at->format('d/m/Y H:i') }}
                        </p>
                    @endif
                    
                </div>
            </div>

            {{-- Bloc des Fichiers --}}
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-light">
                    <h5 class="m-0 fw-bold text-dark"><i class="fas fa-paperclip me-2"></i> Pièces Jointes</h5>
                </div>
                <div class="card-body d-flex flex-wrap gap-3">
                    @if($documentation->file_path)
                        <a href="{{ route('documentations.download', $documentation->id) }}" class="btn btn-sm btn-approve shadow-sm px-3">
                            <i class="fas fa-file-download me-1"></i> Télécharger Fichier Unique
                        </a>
                    @elseif(is_array($documentation->files))
                        @foreach($documentation->files as $index => $file)
                            <a href="{{ route('documentations.download', [$documentation->id, $index]) }}" class="btn btn-sm btn-approve shadow-sm">
                                <i class="fas fa-file me-1"></i> Fichier {{ $index + 1 }}
                            </a>
                        @endforeach
                    @else
                        <span class="text-muted">Aucun fichier n'est attaché à cette documentation.</span>
                    @endif
                </div>
            </div>

            {{-- Bloc des Actions (Approuver/Rejeter) --}}
            @if($documentation->isPending())
                <div class="mt-5">
                    <h4 class="fw-bold text-primary-dark mb-3"><i class="fas fa-hammer me-2"></i> Décision de Vérification</h4>
                    
                    <div class="row">
                        {{-- Formulaire d'Approbation --}}
                        <div class="col-md-6">
                            <div class="form-action-group approve">
                                <h5 class="text-success fw-bold">Approuver la Documentation</h5>
                                <form action="{{ route('documentations.approve', $documentation->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="admin_comment_approve" class="form-label small">Commentaire pour le consultant (Optionnel)</label>
                                        <textarea name="admin_comment" id="admin_comment_approve" class="form-control" rows="2" maxlength="500" placeholder="Ex: Excellent travail, approuvé."></textarea>
                                    </div>
                                    <button type="submit" class="btn btn-approve w-100 shadow-sm">
                                        <i class="fas fa-check-circle me-1"></i> Approuver
                                    </button>
                                </form>
                            </div>
                        </div>
                        
                        {{-- Formulaire de Rejet --}}
                        <div class="col-md-6">
                            <div class="form-action-group reject">
                                <h5 class="text-danger fw-bold">Rejeter la Documentation</h5>
                                <form action="{{ route('documentations.reject', $documentation->id) }}" method="POST">
                                    @csrf
                                    <div class="form-group mb-3">
                                        <label for="admin_comment_reject" class="form-label small text-danger fw-bold">Commentaire de Rejet (Requis)</label>
                                        <textarea name="admin_comment" id="admin_comment_reject" 
                                                  class="form-control @error('admin_comment') is-invalid @enderror" 
                                                  rows="2" required maxlength="1000" 
                                                  placeholder="Ex: Le fichier manque d'informations sur..."></textarea>
                                        @error('admin_comment')
                                            <div class="invalid-feedback">{{ $message }}</div>
                                        @enderror
                                    </div>
                                    <button type="submit" class="btn btn-reject w-100 shadow-sm">
                                        <i class="fas fa-ban me-1"></i> Rejeter
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                {{-- Message si déjà vérifié --}}
                <div class="alert alert-info text-center mt-4 p-3">
                    <i class="fas fa-info-circle me-2"></i> Cette documentation a déjà été **{{ $documentation->status == 'approved' ? 'approuvée' : 'rejetée' }}**. Aucune action supplémentaire n'est requise.
                </div>
            @endif

            <div class="text-center mt-4">
                <a href="{{ route('documentations.adminIndex') }}" class="btn btn-secondary shadow-sm px-4">
                    <i class="fas fa-arrow-left me-1"></i> Retour à la Liste
                </a>
            </div>

        </div>
    </div>
</div>
@endsection