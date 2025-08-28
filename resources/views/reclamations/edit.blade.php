@extends('layouts.app')

@section('title', 'Modifier une Réclamation')

@push('styles')
<style>
    /* Had les styles hna homa nefs homa dyal create.blade.php */
    :root {
        --primary-red: #D32F2F;
        --secondary-pink: #C2185B;
        --accent-red: #ef4444;
        --light-red: rgba(211, 47, 47, 0.1);
        --gradient-bg: linear-gradient(135deg, var(--primary-red) 0%, var(--secondary-pink) 100%);
    }

    .hero-section {
        background: var(--gradient-bg);
        color: white;
        padding: 3rem 0;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }

    .hero-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grain" width="100" height="100" patternUnits="userSpaceOnUse"><circle cx="25" cy="25" r="1" fill="white" opacity="0.1"/><circle cx="75" cy="75" r="1" fill="white" opacity="0.1"/><circle cx="50" cy="10" r="0.5" fill="white" opacity="0.05"/></pattern></defs><rect width="100" height="100" fill="url(%23grain)"/></svg>');
        opacity: 0.3;
    }

    .hero-content {
        position: relative;
        z-index: 1;
    }

    .form-container {
        background: white;
        border-radius: 20px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .form-header {
        background: var(--gradient-bg);
        color: white;
        padding: 2rem;
        text-align: center;
        position: relative;
    }

    .form-header::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 50%;
        transform: translateX(-50%);
        width: 0;
        height: 0;
        border-left: 20px solid transparent;
        border-right: 20px solid transparent;
        border-top: 10px solid var(--secondary-pink);
    }

    .form-body {
        padding: 3rem;
    }

    .form-group {
        margin-bottom: 2rem;
        position: relative;
    }

    .form-label {
        display: block;
        margin-bottom: 0.8rem;
        font-weight: 600;
        color: #2d3748;
        font-size: 0.95rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-label.required::after {
        content: ' *';
        color: var(--accent-red);
        font-weight: bold;
    }

    .form-control {
        width: 100%;
        padding: 1rem 1.2rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 1rem;
        transition: all 0.3s ease;
        background: #f8fafc;
        position: relative;
    }

    .form-control:focus {
        outline: none;
        border-color: var(--primary-red);
        box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.1);
        background: white;
        transform: translateY(-2px);
    }

    .form-control:hover {
        border-color: var(--secondary-pink);
        background: white;
    }

    .form-select {
        appearance: none;
        background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='m6 8 4 4 4-4'/%3e%3c/svg%3e");
        background-position: right 0.8rem center;
        background-repeat: no-repeat;
        background-size: 1.5em 1.5em;
        padding-right: 3rem;
    }

    .textarea-resize {
        resize: vertical;
        min-height: 120px;
        font-family: inherit;
    }

    .priority-pills {
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        margin-top: 0.5rem;
    }

    .priority-pill {
        position: relative;
    }

    .priority-pill input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .priority-pill label {
        display: inline-block;
        padding: 0.7rem 1.5rem;
        background: #f1f5f9;
        border: 2px solid #e2e8f0;
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-weight: 500;
        text-transform: uppercase;
        font-size: 0.85rem;
        letter-spacing: 0.5px;
    }

    .priority-pill input[type="radio"]:checked + label {
        background: var(--gradient-bg);
        color: white;
        border-color: var(--primary-red);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3);
    }

    .priority-pill label:hover {
        border-color: var(--secondary-pink);
        transform: translateY(-1px);
    }

    .btn-container {
        display: flex;
        gap: 1rem;
        justify-content: flex-end;
        margin-top: 3rem;
        padding-top: 2rem;
        border-top: 1px solid #e2e8f0;
    }

    .btn {
        padding: 1rem 2.5rem;
        border-radius: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        cursor: pointer;
        border: none;
        font-size: 0.9rem;
        position: relative;
        overflow: hidden;
    }

    .btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn:hover::before {
        left: 100%;
    }

    .btn-primary {
        background: var(--gradient-bg);
        color: white;
        box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3);
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(211, 47, 47, 0.4);
    }

    .btn-secondary {
        background: #6b7280;
        color: white;
        box-shadow: 0 4px 15px rgba(107, 114, 128, 0.3);
    }

    .btn-secondary:hover {
        background: #4b5563;
        transform: translateY(-2px);
    }

    .alert {
        padding: 1rem 1.5rem;
        border-radius: 12px;
        margin-bottom: 2rem;
        border: none;
        position: relative;
        overflow: hidden;
    }

    .alert-danger {
        background: rgba(239, 68, 68, 0.1);
        color: #dc2626;
        border-left: 4px solid var(--accent-red);
    }

    .floating-label {
        position: relative;
    }

    .floating-label input:focus + .label-text,
    .floating-label input:not(:placeholder-shown) + .label-text {
        transform: translateY(-140%) scale(0.85);
        color: var(--primary-red);
    }

    .label-text {
        position: absolute;
        left: 1.2rem;
        top: 1rem;
        transition: all 0.3s ease;
        pointer-events: none;
        color: #6b7280;
        background: white;
        padding: 0 0.5rem;
    }

    .form-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 2rem;
    }

    @media (max-width: 768px) {
        .form-row {
            grid-template-columns: 1fr;
            gap: 1rem;
        }

        .priority-pills {
            flex-direction: column;
            gap: 0.5rem;
        }

        .btn-container {
            flex-direction: column;
        }

        .form-body {
            padding: 2rem 1.5rem;
        }
    }

    .icon-wrapper {
        position: absolute;
        right: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--primary-red);
        font-size: 1.2rem;
    }

    .form-group.has-icon .form-control {
        padding-right: 3rem;
    }

    .category-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-top: 0.5rem;
    }

    .category-option {
        position: relative;
    }

    .category-option input[type="radio"] {
        position: absolute;
        opacity: 0;
        width: 0;
        height: 0;
    }

    .category-option label {
        display: block;
        padding: 1rem;
        background: #f8fafc;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        text-align: center;
        font-weight: 500;
    }

    .category-option input[type="radio"]:checked + label {
        background: var(--light-red);
        border-color: var(--primary-red);
        color: var(--primary-red);
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(211, 47, 47, 0.2);
    }

    .category-option label:hover {
        border-color: var(--secondary-pink);
        transform: translateY(-1px);
    }
</style>
@endpush

@section('content')
<div class="hero-section">
    <div class="container">
        <div class="hero-content text-center">
            <h1 class="display-4 font-weight-bold mb-3">
                <i class="fas fa-edit me-3"></i>
                Modifier la Réclamation #{{ $reclamation->id }}
            </h1>
            <p class="lead mb-0">Mettez à jour les détails de votre réclamation</p>
        </div>
    </div>
</div>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <div class="d-flex align-items-center mb-2">
                        <i class="fas fa-exclamation-circle me-2"></i>
                        <strong>Erreurs de validation</strong>
                    </div>
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">
                    {{ session('error') }}
                </div>
            @endif

            <div class="form-container">
                <div class="form-header">
                    <h2 class="mb-0">
                        <i class="fas fa-pencil-alt me-2"></i>
                        Modifier les informations
                    </h2>
                </div>

                <div class="form-body">
                    <form action="{{ route('reclamations.update', $reclamation->id) }}" method="POST">
                        @csrf
                        @method('PUT') {{-- N'oubliez pas ceci pour les requêtes PUT/PATCH --}}

                        <div class="form-row">
                            <div class="form-group has-icon">
                                <label for="formation_id" class="form-label required">Formation</label>
                                <select name="formation_id" id="formation_id" class="form-control form-select" required>
                                    <option value="">Sélectionnez une formation</option>
                                    @foreach($formations as $formation)
                                        <option value="{{ $formation->id }}" {{ (old('formation_id', $reclamation->formation_id) == $formation->id) ? 'selected' : '' }}>
                                            {{ $formation->title }}
                                        </option>
                                    @endforeach
                                </select>
                                <i class="fas fa-graduation-cap icon-wrapper"></i>
                            </div>

                            <div class="form-group has-icon">
                                <label for="subject" class="form-label required">Sujet</label>
                                <input type="text"
                                       name="subject"
                                       id="subject"
                                       class="form-control"
                                       value="{{ old('subject', $reclamation->subject) }}" {{-- Utilisation de old() avec valeur par défaut --}}
                                       placeholder="Résumez votre réclamation en quelques mots"
                                       required>
                                <i class="fas fa-tag icon-wrapper"></i>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Catégorie</label>
                            <div class="category-grid">
                                @foreach(App\Models\Reclamation::CATEGORIES as $key => $label)
                                    <div class="category-option">
                                        <input type="radio"
                                               name="category"
                                               id="category_{{ $key }}"
                                               value="{{ $key }}"
                                               {{ (old('category', $reclamation->category) == $key) ? 'checked' : '' }} {{-- Sélection basée sur la réclamation actuelle --}}
                                               required>
                                        <label for="category_{{ $key }}">
                                            <i class="fas fa-{{
                                                $key == 'technique' ? 'cog' : (
                                                $key == 'paiement' ? 'credit-card' : (
                                                $key == 'contenu' ? 'book-open' : (
                                                $key == 'pedagogique' ? 'chalkboard-teacher' : (
                                                $key == 'administrative' ? 'file-alt' : 'ellipsis-h'
                                                )))) }} mb-2 d-block"></i>
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label required">Priorité</label>
                            <div class="priority-pills">
                                @foreach(App\Models\Reclamation::PRIORITIES as $key => $label)
                                    <div class="priority-pill">
                                        <input type="radio"
                                               name="priority"
                                               id="priority_{{ $key }}"
                                               value="{{ $key }}"
                                               {{ (old('priority', $reclamation->priority) == $key) ? 'checked' : '' }} {{-- Sélection basée sur la réclamation actuelle --}}
                                               required>
                                        <label for="priority_{{ $key }}">
                                            <i class="fas fa-{{
                                                $key == 'urgente' ? 'exclamation-triangle' : (
                                                $key == 'haute' ? 'arrow-up' : (
                                                $key == 'moyenne' ? 'minus' : 'arrow-down'
                                                )) }} me-1"></i>
                                            {{ $label }}
                                        </label>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description" class="form-label required">Description détaillée</label>
                            <textarea name="description"
                                      id="description"
                                      class="form-control textarea-resize"
                                      placeholder="Décrivez votre réclamation en détail. Plus vous êtes précis, mieux nous pourrons vous aider."
                                      required>{{ old('description', $reclamation->description) }}</textarea> {{-- Utilisation de old() avec valeur par défaut --}}
                        </div>

                        <div class="btn-container">
                            <a href="{{ route('reclamations.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times me-2"></i>
                                Annuler
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save me-2"></i>
                                Enregistrer les modifications
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
        // Add smooth animations (same as create)
        const formGroups = document.querySelectorAll('.form-group');
        formGroups.forEach((group, index) => {
            group.style.opacity = '0';
            group.style.transform = 'translateY(20px)';
            setTimeout(() => {
                group.style.transition = 'all 0.6s ease';
                group.style.opacity = '1';
                group.style.transform = 'translateY(0)';
            }, index * 100);
        });

        // Character counter for description (same as create)
        const description = document.getElementById('description');
        const maxLength = 1000;

        const counter = document.createElement('div');
        counter.className = 'text-muted text-end mt-2';
        counter.style.fontSize = '0.85rem';
        description.parentNode.appendChild(counter);

        function updateCounter() {
            const remaining = maxLength - description.value.length;
            counter.textContent = `${description.value.length}/${maxLength} caractères`;

            if (remaining < 100) {
                counter.style.color = 'var(--accent-red)';
            } else {
                counter.style.color = '#6b7280';
            }
        }

        description.addEventListener('input', updateCounter);
        // Appeler updateCounter au chargement de la page pour les valeurs existantes
        updateCounter();

        // Form submission loading state (same as create)
        const form = document.querySelector('form');
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('.btn-primary');
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Enregistrement...';
            submitBtn.disabled = true;
        });

        // Auto-resize textarea (same as create)
        description.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = Math.max(this.scrollHeight, 120) + 'px';
        });

        // Appeler l'auto-resize au chargement pour s'assurer que le textarea est bien dimensionné avec le contenu existant
        description.dispatchEvent(new Event('input'));
    });
</script>
@endpush
@endsection