@extends('layouts.app')

@section('title', 'Modifier la Catégorie')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title mb-0">Modifier : {{ $category->name }}</h3>
                        <div>
                            <a href="{{ route('categories.show', $category) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                            <a href="{{ route('categories.index') }}" class="btn btn-light">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('categories.update', $category) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="card-body">
                        <div class="row">
                            <!-- Nom de la catégorie -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">Nom de la catégorie <span class="text-danger">*</span></label>
                                    <input type="text" 
                                           class="form-control @error('name') is-invalid @enderror" 
                                           id="name" 
                                           name="name" 
                                           value="{{ old('name', $category->name) }}" 
                                           placeholder="Ex: Développement Web"
                                           required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Description -->
                            <div class="col-12">
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control @error('description') is-invalid @enderror" 
                                              id="description" 
                                              name="description" 
                                              rows="4" 
                                              placeholder="Description de la catégorie...">{{ old('description', $category->description) }}</textarea>
                                    @error('description')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <!-- Statut -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <div class="custom-control custom-switch">
                                        <input type="checkbox" 
                                               class="custom-control-input" 
                                               id="is_active" 
                                               name="is_active" 
                                               value="1" 
                                               {{ old('is_active', $category->is_active) ? 'checked' : '' }}>
                                        <label class="custom-control-label" for="is_active">
                                            Catégorie active
                                        </label>
                                    </div>
                                    <small class="form-text text-muted">
                                        Les catégories inactives ne sont pas visibles dans les formulaires
                                    </small>
                                </div>
                            </div>

                            <!-- Informations supplémentaires -->
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Informations :</label>
                                    <div class="info-box">
                                        <small class="text-muted">
                                            <strong>Créée le :</strong> {{ $category->created_at->format('d/m/Y à H:i') }}<br>
                                            <strong>Modifiée le :</strong> {{ $category->updated_at->format('d/m/Y à H:i') }}<br>
                                            <strong>Formations associées :</strong> 
                                            <span class="badge badge-info">{{ $category->formations()->count() }}</span>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Alert si des formations existent -->
                        @if($category->formations()->count() > 0)
                        <div class="alert alert-warning mt-3">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>Attention :</strong> Cette catégorie contient {{ $category->formations()->count() }} formation(s). 
                            Les modifications peuvent affecter l'affichage des formations associées.
                        </div>
                        @endif
                    </div>

                    <div class="card-footer bg-light">
                        <div class="d-flex justify-content-between">
                            <div>
                                <a href="{{ route('categories.index') }}" class="btn btn-outline-secondary">
                                    <i class="fas fa-times"></i> Annuler
                                </a>
                                <a href="{{ route('categories.show', $category) }}" class="btn btn-info">
                                    <i class="fas fa-eye"></i> Voir les détails
                                </a>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> Mettre à jour
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@push('styles')
<style>
.card {
    border-radius: 10px;
    overflow: hidden;
}

.card-header {
    border-bottom: none;
}

.form-control:focus {
    border-color: #007bff;
    box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
}

.btn-primary {
    background-color: #007bff;
    border-color: #007bff;
    transition: all 0.3s ease;
}

.btn-primary:hover {
    background-color: #0056b3;
    border-color: #0056b3;
}

.btn-info {
    transition: all 0.3s ease;
}

.btn-info:hover {
    background-color: #138496;
    border-color: #117a8b;
}

.info-box {
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 5px;
}
</style>
@endpush
@endsection