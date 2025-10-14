@extends('layouts.app')

@section('content')

<style>
    /* Couleurs personnalisées pour un look plus harmonieux */
    .bg-primary-dark { background-color: #C2185B; }
    .text-primary-dark { color: #C2185B; }
    .border-primary-dark { border-color: #C2185B; }
    .btn-primary-custom {
        background-color: #D32F2F; /* Rouge intense pour le bouton principal */
        border-color: #D32F2F;
        color: white;
        transition: all 0.2s ease-in-out;
    }
    .btn-primary-custom:hover {
        background-color: #C2185B; /* Un bordeaux plus profond au hover */
        border-color: #C2185B;
        color: white;
    }
    .btn-secondary-custom {
        background-color: #6c757d;
        border-color: #6c757d;
        color: white;
    }
    .btn-secondary-custom:hover {
        background-color: #5a6268;
        border-color: #545b62;
    }
    .card-header-custom {
        background-color: #C2185B;
        color: white;
        border-bottom: 2px solid #D32F2F;
        padding: 1rem 1.5rem;
    }
    .alert-info-custom {
        background-color: #fce4ec; /* Rose très clair */
        color: #C2185B;
        border-left: 5px solid #D32F2F;
    }
    .alert-warning-custom {
        background-color: #ffe0b2; /* Orange très clair */
        color: #D32F2F;
        border-left: 5px solid #ef4444;
    }
</style>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-9">
            <div class="card shadow-lg border-0 rounded-xl">
                
                {{-- Entête de la carte avec la couleur bordeaux/rouge --}}
                <div class="card-header-custom rounded-top-xl">
                    <h2 class="text-xl font-semibold m-0">
                        <i class="fas fa-edit me-2"></i> Modifier la documentation - <span class="fw-bold">{{ $documentation->module->name }}</span>
                    </h2>
                </div>

                <div class="card-body p-5">
                    <form action="{{ route('consultant.documentations.update', $documentation->id) }}"
                          method="POST"
                          enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        {{-- Champ Module Désactivé --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-secondary-dark">Module</label>
                            <input type="text" 
                                   class="form-control form-control-lg bg-light" 
                                   value="{{ $documentation->module->title }}" 
                                   disabled>
                        </div>

                        {{-- Champ Description --}}
                        <div class="mb-4">
                            <label for="description" class="form-label fw-bold text-primary-dark">Description *</label>
                            <textarea name="description"
                                      id="description"
                                      class="form-control @error('description') is-invalid border-danger @enderror"
                                      rows="5"
                                      placeholder="Décrivez la mise à jour de la documentation..."
                                      required>{{ old('description', $documentation->description) }}</textarea>
                            <small class="text-muted">Minimum 20 caractères.</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Fichiers Actuels --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary-dark">Fichiers actuels:</label>
                            <div class="alert alert-info-custom d-flex align-items-center p-3 rounded-lg">
                                @if($documentation->file_path)
                                    <i class="fas fa-file fa-lg me-3"></i> 
                                    <span>1 fichier uploadé</span>
                                    <a href="{{ route('documentations.download', $documentation->id) }}"
                                       class="btn btn-sm btn-primary-custom ms-auto shadow-sm">
                                        <i class="fas fa-download"></i> Télécharger
                                    </a>
                                @elseif(is_array($documentation->files) && count($documentation->files) > 0)
                                    <i class="fas fa-files fa-lg me-3"></i> 
                                    <span>{{ count($documentation->files) }} fichiers uploadés</span>
                                    {{-- Affichage des boutons de téléchargement pour plusieurs fichiers --}}
                                    <div class="ms-3 d-flex flex-wrap gap-2">
                                        @foreach($documentation->files as $index => $file)
                                            <a href="{{ route('documentations.download', [$documentation->id, $index]) }}"
                                               class="btn btn-sm btn-primary-custom shadow-sm">
                                                <i class="fas fa-download"></i> Fichier {{ $index + 1 }}
                                            </a>
                                        @endforeach
                                    </div>
                                @else
                                    <i class="fas fa-times-circle fa-lg me-3"></i>
                                    <span>Aucun fichier</span>
                                @endif
                            </div>
                        </div>

                        {{-- Mode d'upload --}}
                        <div class="mb-4">
                            <label class="form-label fw-bold text-primary-dark">Mode d'upload:</label>
                            <div class="d-flex gap-4">
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="upload_mode"
                                           id="single_file"
                                           value="single"
                                           {{ $documentation->file_path ? 'checked' : '' }}>
                                    <label class="form-check-label" for="single_file">
                                        Fichier unique
                                    </label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input"
                                           type="radio"
                                           name="upload_mode"
                                           id="multiple_files"
                                           value="multiple"
                                           {{ is_array($documentation->files) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="multiple_files">
                                        Plusieurs fichiers
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Upload Fichier Unique --}}
                        <div class="mb-4" id="single_upload" style="{{ $documentation->file_path ? 'display: block;' : 'display: none;' }}">
                            <label for="documentation_file" class="form-label fw-bold text-primary-dark">Nouveau fichier (optionnel)</label>
                            <input type="file"
                                   name="documentation_file"
                                   id="documentation_file"
                                   class="form-control form-control-lg @error('documentation_file') is-invalid @enderror">
                            <small class="text-muted">Formats acceptés: **PDF, DOC, DOCX, ZIP**. Taille max: 10 MB. Laissez vide pour garder l'actuel.</small>
                            @error('documentation_file')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Upload Plusieurs Fichiers --}}
                        <div class="mb-4" id="multiple_upload" style="{{ is_array($documentation->files) ? 'display: block;' : 'display: none;' }}">
                            <label for="documentation_files" class="form-label fw-bold text-primary-dark">Nouveaux fichiers (optionnel)</label>
                            <input type="file"
                                   name="documentation_files[]"
                                   id="documentation_files"
                                   class="form-control form-control-lg @error('documentation_files.*') is-invalid @enderror"
                                   multiple>
                            <small class="text-muted">Formats acceptés: **PDF, DOC, DOCX, ZIP, JPG, PNG**. Taille max: 10 MB par fichier. Laissez vide pour garder les actuels.</small>
                            @error('documentation_files.*')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Alerte Remplacement --}}
                        <div class="alert alert-warning-custom p-3 rounded-lg mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <strong>Attention:</strong> Si vous uploadez de nouveaux fichiers, **les anciens seront remplacés**.
                        </div>

                        {{-- Boutons d'Action --}}
                        <div class="d-flex justify-content-between mt-4">
                            <a href="{{ route('consultant.documentations.show', $documentation->id) }}" 
                               class="btn btn-secondary-custom shadow-sm px-4">
                                <i class="fas fa-arrow-left me-2"></i> Annuler
                            </a>
                            <button type="submit" class="btn btn-primary-custom shadow-lg px-4">
                                <i class="fas fa-save me-2"></i> Mettre à jour
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const singleRadio = document.getElementById('single_file');
        const multipleRadio = document.getElementById('multiple_files');
        const singleUpload = document.getElementById('single_upload');
        const multipleUpload = document.getElementById('multiple_upload');
        
        // Initial state logic (assurez-vous que le bon bloc est affiché au chargement)
        if (singleRadio.checked) {
            singleUpload.style.display = 'block';
            multipleUpload.style.display = 'none';
        } else if (multipleRadio.checked) {
            singleUpload.style.display = 'none';
            multipleUpload.style.display = 'block';
        } else {
             // Si aucun n'est coché (pas de fichier actuel), on affiche le single par défaut
            singleUpload.style.display = 'block';
            multipleUpload.style.display = 'none';
            singleRadio.checked = true;
        }

        // Gestion du changement de mode
        singleRadio.addEventListener('change', function() {
            if (this.checked) {
                singleUpload.style.display = 'block';
                multipleUpload.style.display = 'none';
                document.getElementById('documentation_files').value = ''; // Réinitialiser le champ multiple
            }
        });
        
        multipleRadio.addEventListener('change', function() {
            if (this.checked) {
                singleUpload.style.display = 'none';
                multipleUpload.style.display = 'block';
                document.getElementById('documentation_file').value = ''; // Réinitialiser le champ single
            }
        });
    });
</script>
@endpush
@endsection