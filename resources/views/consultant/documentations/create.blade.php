@extends('layouts.app')

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-10">
            {{-- En-tête --}}
            <div class="card module-header mb-4">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="fas fa-cloud-upload-alt"></i> 
                        Soumettre une documentation
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h5 class="module-title mb-3">
                                <i class="fas fa-book"></i> {{ $module->title }}
                            </h5>
                            <div class="module-info">
                                <span class="info-badge">
                                    <i class="fas fa-clock"></i> 
                                    {{ $module->duration_hours }} heures
                                </span>
                                <span class="info-badge">
                                    <i class="fas fa-list-ol"></i> 
                                    {{ $module->number_seance }} séances
                                </span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="completion-badge">
                                <div class="completion-icon">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="completion-text">
                                    <strong>100%</strong>
                                    <span>Complété</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formulaire --}}
            <div class="card form-card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-file-upload"></i> 
                        Informations de la documentation
                    </h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('consultant.documentations.store') }}" 
                          method="POST" 
                          enctype="multipart/form-data"
                          id="documentationForm">
                        @csrf
                        
                        <input type="hidden" name="module_id" value="{{ $module->id }}">

                        {{-- Module (lecture seule) --}}
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-book label-icon"></i> Module
                            </label>
                            <input type="text" 
                                   class="form-control readonly-input" 
                                   value="{{ $module->title }}" 
                                   disabled>
                        </div>

                        {{-- Description --}}
                        <div class="form-group">
                            <label for="description" class="form-label">
                                <i class="fas fa-align-left label-icon"></i> 
                                Description de la documentation <span class="required">*</span>
                            </label>
                            <textarea name="description" 
                                      id="description" 
                                      class="form-control @error('description') is-invalid @enderror" 
                                      rows="6" 
                                      placeholder="Décrivez le contenu de votre documentation (minimum 20 caractères)..."
                                      required>{{ old('description') }}</textarea>
                            <small class="form-hint">
                                <i class="fas fa-info-circle"></i> 
                                Minimum 20 caractères - Décrivez les principaux points abordés
                            </small>
                            @error('description')
                                <div class="invalid-feedback">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Mode d'upload --}}
                        <div class="form-group">
                            <label class="form-label">
                                <i class="fas fa-upload label-icon"></i> 
                                Mode d'upload des fichiers
                            </label>
                            <div class="upload-mode-selector">
                                <div class="mode-option">
                                    <input class="mode-radio" 
                                           type="radio" 
                                           name="upload_mode" 
                                           id="single_file" 
                                           value="single" 
                                           checked>
                                    <label class="mode-label" for="single_file">
                                        <div class="mode-icon">
                                            <i class="fas fa-file"></i>
                                        </div>
                                        <div class="mode-content">
                                            <strong>Fichier unique</strong>
                                            <span>Télécharger un seul fichier (PDF, DOC, DOCX ou ZIP)</span>
                                        </div>
                                    </label>
                                </div>
                                <div class="mode-option">
                                    <input class="mode-radio" 
                                           type="radio" 
                                           name="upload_mode" 
                                           id="multiple_files" 
                                           value="multiple">
                                    <label class="mode-label" for="multiple_files">
                                        <div class="mode-icon">
                                            <i class="fas fa-copy"></i>
                                        </div>
                                        <div class="mode-content">
                                            <strong>Plusieurs fichiers</strong>
                                            <span>Télécharger plusieurs fichiers simultanément</span>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        {{-- Upload fichier unique --}}
                        <div class="form-group" id="single_upload">
                            <label for="documentation_file" class="form-label">
                                <i class="fas fa-file-pdf label-icon"></i> 
                                Fichier de documentation
                            </label>
                            <div class="custom-file-upload">
                                <input type="file" 
                                       name="documentation_file" 
                                       id="documentation_file" 
                                       class="file-input @error('documentation_file') is-invalid @enderror"
                                       accept=".pdf,.doc,.docx,.zip">
                                <label for="documentation_file" class="file-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Cliquez ou glissez votre fichier ici</span>
                                </label>
                            </div>
                           <small class="form-hint">
    <i class="fas fa-info-circle"></i> 
    <strong>Formats:</strong> PDF, DOC, DOCX, ZIP | 
    <strong>Taille max:</strong> 100 MB
</small>
                            @error('documentation_file')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Upload fichiers multiples --}}
                        <div class="form-group" id="multiple_upload" style="display: none;">
                            <label for="documentation_files" class="form-label">
                                <i class="fas fa-files-medical label-icon"></i> 
                                Fichiers de documentation (multiple)
                            </label>
                            <div class="custom-file-upload">
                                <input type="file" 
                                       name="documentation_files[]" 
                                       id="documentation_files" 
                                       class="file-input @error('documentation_files.*') is-invalid @enderror" 
                                       multiple
                                       accept=".pdf,.doc,.docx,.zip,.jpg,.jpeg,.png">
                                <label for="documentation_files" class="file-label">
                                    <i class="fas fa-cloud-upload-alt"></i>
                                    <span>Cliquez ou glissez vos fichiers ici</span>
                                </label>
                            </div>
                            <small class="form-hint">
    <i class="fas fa-info-circle"></i> 
    <strong>Formats:</strong> PDF, DOC, DOCX, ZIP, JPG, PNG | 
    <strong>Taille max:</strong> 100 MB par fichier
</small>
                            @error('documentation_files.*')
                                <div class="invalid-feedback d-block">
                                    <i class="fas fa-exclamation-circle"></i> {{ $message }}
                                </div>
                            @enderror
                        </div>

                        {{-- Aperçu fichier sélectionné --}}
                        <div class="file-preview d-none" id="file-preview">
                            <div class="preview-header">
                                <i class="fas fa-file-alt"></i> 
                                <strong>Fichier(s) sélectionné(s)</strong>
                            </div>
                            <ul id="selected-files" class="preview-list"></ul>
                        </div>

                        {{-- Boutons d'action --}}
                        <div class="form-actions">
                            <a href="{{ route('consultant.documentations.index') }}" 
                               class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> Soumettre la documentation
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Aide --}}
            <div class="help-card">
                <div class="help-icon">
                    <i class="fas fa-lightbulb"></i>
                </div>
                <div class="help-content">
                    <h6>Conseils pour une bonne documentation</h6>
                    <ul>
                        <li>Assurez-vous que votre documentation couvre tous les aspects du module</li>
                        <li>Utilisez des formats de fichiers standards (PDF recommandé)</li>
                        <li>Nommez vos fichiers de manière claire et descriptive</li>
                        <li>Vérifiez que tous les fichiers sont lisibles avant de les soumettre</li>
                    </ul>
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
    const filePreview = document.getElementById('file-preview');
    const selectedFilesList = document.getElementById('selected-files');
    
    // Toggle entre single et multiple upload
    singleRadio.addEventListener('change', function() {
        if (this.checked) {
            singleUpload.style.display = 'block';
            multipleUpload.style.display = 'none';
            document.getElementById('documentation_files').value = '';
            filePreview.classList.add('d-none');
        }
    });
    
    multipleRadio.addEventListener('change', function() {
        if (this.checked) {
            singleUpload.style.display = 'none';
            multipleUpload.style.display = 'block';
            document.getElementById('documentation_file').value = '';
            filePreview.classList.add('d-none');
        }
    });
    
    // Preview fichier unique
    document.getElementById('documentation_file').addEventListener('change', function(e) {
        if (this.files.length > 0) {
            selectedFilesList.innerHTML = '';
            const li = document.createElement('li');
            li.innerHTML = `<i class="fas fa-file"></i> <span>${this.files[0].name}</span> <small>(${formatFileSize(this.files[0].size)})</small>`;
            selectedFilesList.appendChild(li);
            filePreview.classList.remove('d-none');
        } else {
            filePreview.classList.add('d-none');
        }
    });
    
    // Preview fichiers multiples
    document.getElementById('documentation_files').addEventListener('change', function(e) {
        if (this.files.length > 0) {
            selectedFilesList.innerHTML = '';
            Array.from(this.files).forEach(file => {
                const li = document.createElement('li');
                li.innerHTML = `<i class="fas fa-file"></i> <span>${file.name}</span> <small>(${formatFileSize(file.size)})</small>`;
                selectedFilesList.appendChild(li);
            });
            filePreview.classList.remove('d-none');
        } else {
            filePreview.classList.add('d-none');
        }
    });
    
    // Format file size
    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return Math.round(bytes / Math.pow(k, i) * 100) / 100 + ' ' + sizes[i];
    }
});
</script>
@endpush

@push('styles')
<style>
    :root {
        --primary: #C2185B;
        --secondary: #D32F2F;
        --accent: #ef4444;
        --dark: #1a1a2e;
        --light: #f8f9fa;
        --border: #e0e0e0;
        --shadow: rgba(194, 24, 91, 0.15);
    }

    body {
        background: linear-gradient(135deg, #fef3f7 0%, #fff 100%);
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .card {
        border: none;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    /* Module Header Card */
    .module-header .card-header {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        padding: 1.5rem;
        border: none;
    }

    .module-header .card-header h4 {
        font-weight: 600;
        font-size: 1.4rem;
    }

    .module-header .card-body {
        background: white;
        padding: 2rem;
    }

    .module-title {
        color: var(--primary);
        font-weight: 600;
        font-size: 1.3rem;
        margin-bottom: 1rem;
    }

    .module-info {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .info-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);
        color: var(--primary);
        padding: 0.5rem 1rem;
        border-radius: 50px;
        font-weight: 500;
        font-size: 0.95rem;
    }

    .completion-badge {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        background: linear-gradient(135deg, #e8f5e9 0%, #c8e6c9 100%);
        padding: 1.5rem;
        border-radius: 16px;
        border: 2px solid #81c784;
    }

    .completion-icon {
        font-size: 3rem;
        color: #4caf50;
    }

    .completion-text {
        display: flex;
        flex-direction: column;
        color: #2e7d32;
    }

    .completion-text strong {
        font-size: 1.8rem;
        font-weight: 700;
    }

    .completion-text span {
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Form Card */
    .form-card .card-header {
        background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%);
        color: white;
        padding: 1.5rem;
        border: none;
    }

    .form-card .card-header h5 {
        font-weight: 600;
        font-size: 1.2rem;
    }

    .form-card .card-body {
        padding: 2.5rem;
    }

    /* Form Groups */
    .form-group {
        margin-bottom: 2rem;
    }

    .form-label {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        color: var(--dark);
        font-size: 1.05rem;
        margin-bottom: 0.75rem;
    }

    .label-icon {
        color: var(--primary);
        font-size: 1.1rem;
    }

    .required {
        color: var(--accent);
        font-weight: 700;
    }

    .form-control {
        border: 2px solid var(--border);
        border-radius: 12px;
        padding: 0.875rem 1.25rem;
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(194, 24, 91, 0.1);
        outline: none;
    }

    .readonly-input {
        background: linear-gradient(135deg, #fce4ec 0%, #f8bbd0 100%);
        color: var(--primary);
        font-weight: 600;
        border: 2px solid #f48fb1;
    }

    .form-hint {
        display: block;
        margin-top: 0.5rem;
        color: #666;
        font-size: 0.9rem;
    }

    /* Upload Mode Selector */
    .upload-mode-selector {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1rem;
    }

    .mode-option {
        position: relative;
    }

    .mode-radio {
        position: absolute;
        opacity: 0;
        pointer-events: none;
    }

    .mode-label {
        display: flex;
        align-items: center;
        gap: 1rem;
        padding: 1.5rem;
        background: white;
        border: 3px solid var(--border);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .mode-label:hover {
        border-color: var(--primary);
        background: #fef3f7;
        transform: translateY(-2px);
    }

    .mode-radio:checked + .mode-label {
        border-color: var(--primary);
        background: linear-gradient(135deg, #fce4ec 0%, #ffffff 100%);
        box-shadow: 0 4px 15px var(--shadow);
    }

    .mode-icon {
        font-size: 2rem;
        color: var(--primary);
        flex-shrink: 0;
    }

    .mode-content {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }

    .mode-content strong {
        color: var(--dark);
        font-size: 1.05rem;
    }

    .mode-content span {
        color: #666;
        font-size: 0.9rem;
    }

    /* Custom File Upload */
    .custom-file-upload {
        position: relative;
    }

    .file-input {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .file-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        gap: 1rem;
        padding: 3rem 2rem;
        background: linear-gradient(135deg, #fef3f7 0%, #ffffff 100%);
        border: 3px dashed var(--primary);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
    }

    .file-label:hover {
        background: linear-gradient(135deg, #fce4ec 0%, #ffffff 100%);
        border-color: var(--secondary);
        transform: scale(1.02);
    }

    .file-label i {
        font-size: 3rem;
        color: var(--primary);
    }

    .file-label span {
        font-weight: 500;
        color: var(--dark);
        font-size: 1.05rem;
    }

    /* File Preview */
    .file-preview {
        background: linear-gradient(135deg, #e3f2fd 0%, #ffffff 100%);
        border: 2px solid #2196f3;
        border-left: 6px solid #2196f3;
        border-radius: 12px;
        padding: 1.5rem;
        margin-top: 1.5rem;
    }

    .preview-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        color: #1565c0;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    .preview-header i {
        font-size: 1.2rem;
    }

    .preview-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .preview-list li {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: white;
        border-radius: 8px;
        margin-bottom: 0.5rem;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
    }

    .preview-list li:last-child {
        margin-bottom: 0;
    }

    .preview-list li i {
        color: var(--primary);
        font-size: 1.2rem;
    }

    .preview-list li span {
        flex: 1;
        font-weight: 500;
        color: var(--dark);
    }

    .preview-list li small {
        color: #666;
        font-size: 0.85rem;
    }

    /* Form Actions */
    .form-actions {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding-top: 2rem;
        border-top: 2px solid var(--border);
        margin-top: 2rem;
    }

    .btn {
        padding: 0.875rem 2rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 1.05rem;
        border: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
        color: white;
        box-shadow: 0 4px 15px var(--shadow);
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, var(--secondary) 0%, var(--accent) 100%);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(211, 47, 47, 0.3);
    }

    .btn-secondary {
        background: #6c757d;
        color: white;
        box-shadow: 0 4px 15px rgba(108, 117, 125, 0.2);
    }

    .btn-secondary:hover {
        background: #5a6268;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(108, 117, 125, 0.3);
    }

    /* Help Card */
    .help-card {
        display: flex;
        gap: 1.5rem;
        background: linear-gradient(135deg, #fff9e6 0%, #ffffff 100%);
        border: 2px solid #ffd54f;
        border-left: 6px solid #ffa726;
        border-radius: 16px;
        padding: 2rem;
        margin-top: 2rem;
        box-shadow: 0 4px 15px rgba(255, 152, 0, 0.1);
    }

    .help-icon {
        font-size: 2.5rem;
        color: #ff9800;
        flex-shrink: 0;
    }

    .help-content h6 {
        color: #e65100;
        font-weight: 600;
        font-size: 1.15rem;
        margin-bottom: 1rem;
    }

    .help-content ul {
        margin: 0;
        padding-left: 1.5rem;
        color: #5d4037;
    }

    .help-content li {
        margin-bottom: 0.5rem;
        line-height: 1.6;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .module-info {
            flex-direction: column;
        }

        .completion-badge {
            margin-top: 1rem;
        }

        .form-actions {
            flex-direction: column;
            gap: 1rem;
        }

        .btn {
            width: 100%;
            justify-content: center;
        }

        .upload-mode-selector {
            grid-template-columns: 1fr;
        }

        .help-card {
            flex-direction: column;
            text-align: center;
        }

        .help-icon {
            font-size: 3rem;
        }
    }

    /* Animations */
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .card, .help-card {
        animation: fadeIn 0.5s ease-out;
    }

    .invalid-feedback {
        display: block;
        color: var(--accent);
        font-weight: 500;
        margin-top: 0.5rem;
    }
</style>
@endpush
@endsection