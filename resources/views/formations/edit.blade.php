@extends('layouts.app')

@section('title', 'Modifier la Formation')

@section('content')

    <div class="py-12 bg-gray-100 min-h-screen">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            {{-- Header avec Breadcrumbs et boutons --}}
            <div class="mb-6 flex flex-col sm:flex-row justify-between items-center animated-header">
                {{-- Breadcrumbs --}}
                <nav aria-label="breadcrumb" class="mb-4 sm:mb-0">
                    <ol class="flex items-center space-x-2 text-sm text-gray-500">
                        <li><a href="{{ route('formations.index') }}" class="hover:text-indigo-600 transition-colors duration-200">Formations</a></li>
                        <li class="flex items-center">
                            <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
                            <a href="{{ route('formations.show', $formation) }}" class="hover:text-indigo-600 transition-colors duration-200">{{ $formation->title }}</a>
                        </li>
                        <li class="flex items-center text-gray-700 font-semibold">
                            <i class="fas fa-chevron-right mx-2 text-gray-400 text-xs"></i>
                            Modifier
                        </li>
                    </ol>
                </nav>
                
                {{-- Titre et actions --}}
                <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-4 w-full sm:w-auto justify-between sm:justify-start">
                    <h1 class="text-3xl font-extrabold text-gray-800 animate-text-pop">
                        Modifier la Formation
                    </h1>
                    <div class="flex space-x-3">
                        <a href="{{ route('formations.show', $formation) }}" class="btn-secondary-outline group">
                            <i class="fas fa-eye mr-2 group-hover:scale-110 transition-transform duration-200"></i>
                            Voir
                        </a>
                        <a href="{{ route('formations.index') }}" class="btn-secondary-outline group">
                            <i class="fas fa-arrow-left mr-2 group-hover:scale-110 transition-transform duration-200"></i>
                            Retour
                        </a>
                    </div>
                </div>
            </div>

            <form action="{{ route('formations.update', $formation) }}" method="POST" id="formation-form" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                @csrf
                @method('PUT')
                
                <div class="lg:col-span-2 space-y-8"> {{-- Utilisation de lg:col-span-2 pour les deux tiers de la largeur sur les grands écrans --}}
                    <div class="border border-indigo-200 rounded-lg p-6 bg-white transition-all duration-300 hover:shadow-lg section-card animated-section">
                        <h4 class="text-xl font-bold text-indigo-700 mb-5 flex items-center">
                            <i class="fas fa-info-circle mr-3 text-indigo-500 animate-icon-fade"></i> Informations Générales
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            
                            <div class="md:col-span-1">
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre de la Formation <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="text" name="title" id="title" class="form-input" value="{{ old('title', $formation->title) }}" required placeholder="Ex: Développement Web avec Laravel">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-book-open text-gray-400"></i>
                                    </div>
                                </div>
                                @error('title')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-1">
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Catégorie <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="category_id" name="category_id" class="form-select" required>
                                        <option value="">Choisir une catégorie</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" 
                                                    {{ old('category_id', $formation->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-tags text-gray-400"></i>
                                    </div>
                                </div>
                                @error('category_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="consultant_id" class="block text-sm font-medium text-gray-700 mb-1">
                                    Consultant <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select class="form-select {{ auth()->user()->role === 'consultant' ? 'bg-gray-100 cursor-not-allowed' : '' }}" 
                                            id="consultant_id" name="consultant_id" required
                                            {{ auth()->user()->role === 'consultant' ? 'disabled' : '' }}>
                                        @if(auth()->user()->role === 'consultant')
                                            <option value="{{ $formation->consultant_id }}" selected>
                                                {{ $formation->consultant->name }}
                                            </option>
                                        @else
                                            <option value="">Choisir un consultant</option>
                                            @foreach($consultants as $consultant)
                                                <option value="{{ $consultant->id }}" 
                                                        {{ old('consultant_id', $formation->consultant_id) == $consultant->id ? 'selected' : '' }}>
                                                    {{ $consultant->name }}
                                                </option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user-tie text-gray-400"></i>
                                    </div>
                                </div>
                                @if(auth()->user()->role === 'consultant')
                                    <input type="hidden" name="consultant_id" value="{{ $formation->consultant_id }}">
                                    <p class="mt-1 text-xs text-gray-500">En tant que consultant, vous ne pouvez pas modifier l'assignation.</p>
                                @endif
                                @error('consultant_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div class="md:col-span-2">
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <textarea class="form-textarea" id="description" name="description" rows="6" required placeholder="Décrivez le contenu, les objectifs et les bénéfices de la formation.">{{ old('description', $formation->description) }}</textarea>
                                    <div class="absolute top-3 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-align-left text-gray-400"></i>
                                    </div>
                                </div>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="border border-emerald-200 rounded-lg p-6 bg-white transition-all duration-300 hover:shadow-lg section-card animated-section">
                        <h4 class="text-xl font-bold text-emerald-700 mb-5 flex items-center">
                            <i class="fas fa-dollar-sign mr-3 text-emerald-500 animate-icon-fade"></i> Détails Pratiques
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                            
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Prix (MAD) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" step="0.01" min="0" 
                                           class="form-input" 
                                           id="price" name="price" value="{{ old('price', $formation->price) }}" required placeholder="0.00">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-money-bill-wave text-gray-400"></i>
                                    </div>
                                </div>
                                @error('price')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-1">Durée (heures) <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" min="1" 
                                           class="form-input" 
                                           id="duration_hours" name="duration_hours" value="{{ old('duration_hours', $formation->duration_hours) }}" required>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-clock text-gray-400"></i>
                                    </div>
                                </div>
                                @error('duration_hours')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                           
                            
                            <div>
                                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Capacité <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" min="1" 
                                           class="form-input" 
                                           id="capacity" name="capacity" value="{{ old('capacity', $formation->capacity) }}" required>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-users text-gray-400"></i>
                                    </div>
                                </div>
                                @error('capacity')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                <p class="mt-1 text-xs text-gray-500">Nombre maximum de participants.</p>
                            </div>
                            
                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date de Début <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="datetime-local" 
                                           class="form-input" 
                                           id="start_date" name="start_date" 
                                           value="{{ old('start_date', $formation->start_date ? $formation->start_date->format('Y-m-d\TH:i') : '') }}" required>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-alt text-gray-400"></i>
                                    </div>
                                </div>
                                @error('start_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date de Fin <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="datetime-local" 
                                           class="form-input" 
                                           id="end_date" name="end_date" 
                                           value="{{ old('end_date', $formation->end_date ? $formation->end_date->format('Y-m-d\TH:i') : '') }}" required>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-check text-gray-400"></i>
                                    </div>
                                </div>
                                @error('end_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="lg:col-span-3">
                                <label for="payment_installments" class="block text-sm font-medium text-gray-700 mb-1">Nombre de Versements <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="number" name="payment_installments" id="payment_installments" 
                                           class="form-input" 
                                           value="{{ old('payment_installments', $formation->payment_installments) }}" min="1" required>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-credit-card text-gray-400"></i>
                                    </div>
                                </div>
                                @error('payment_installments')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="border border-red-200 rounded-lg p-6 bg-white transition-all duration-300 hover:shadow-lg section-card animated-section">
                        <h4 class="text-xl font-bold text-red-700 mb-5 flex items-center">
                            <i class="fas fa-question-circle mr-3 text-red-500 animate-icon-fade"></i> Prérequis (Optionnel)
                        </h4>
                        <div id="prerequisites-container" class="space-y-4">
                            @php
                                $prerequisites = old('prerequisites', $formation->prerequisites ?? []);
                                // Ensure it's an array and filter out null/empty strings
                                $prerequisites = array_filter(is_array($prerequisites) ? $prerequisites : []);
                            @endphp
                            
                            @forelse($prerequisites as $prerequisite)
                                <div class="flex items-center gap-x-3 prerequisite-item animate-item-enter">
                                    <div class="relative flex-grow">
                                        <input type="text" name="prerequisites[]" class="form-input" value="{{ $prerequisite }}" placeholder="Ajouter un prérequis...">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-check-circle text-gray-400"></i>
                                        </div>
                                    </div>
                                    <button type="button" class="remove-prerequisite-btn text-red-600 hover:text-red-800 transition-colors duration-200 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            @empty
                                <div class="flex items-center gap-x-3 prerequisite-item animate-item-enter">
                                    <div class="relative flex-grow">
                                        <input type="text" name="prerequisites[]" class="form-input" placeholder="Ajouter un prérequis...">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-check-circle text-gray-400"></i>
                                        </div>
                                    </div>
                                    <button type="button" class="remove-prerequisite-btn text-red-600 hover:text-red-800 transition-colors duration-200 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            @endforelse
                        </div>
                        
                        <button type="button" id="add-prerequisite-btn" class="mt-6 inline-flex items-center px-5 py-2.5 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105 active:scale-95 animate-pulse-btn">
                            <i class="fas fa-plus-circle -ml-1 mr-3 text-lg"></i>
                            Ajouter un prérequis
                        </button>
                        @error('prerequisites')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">Listez les compétences ou connaissances requises pour suivre cette formation.</p>
                    </div>

                    <div class="border border-blue-200 rounded-lg p-6 bg-white transition-all duration-300 hover:shadow-lg section-card animated-section">
                        <h4 class="text-xl font-bold text-blue-700 mb-5 flex items-center">
                            <i class="fas fa-file-alt mr-3 text-blue-500 animate-icon-fade"></i> Documents Requis (Optionnel)
                        </h4>
                        <div id="documents-container" class="space-y-4">
                             @php
                                $documents = old('documents_required', $formation->documents_required ?? []);
                                // Ensure it's an array and filter out null/empty strings
                                $documents = array_filter(is_array($documents) ? $documents : []);
                            @endphp
                            
                            @forelse($documents as $document)
                                <div class="flex items-center gap-x-3 document-item animate-item-enter">
                                    <div class="relative flex-grow">
                                        <input type="text" name="documents_required[]" class="form-input" value="{{ $document }}" placeholder="Ajouter un document requis...">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-file-check text-gray-400"></i>
                                        </div>
                                    </div>
                                    <button type="button" class="remove-document-btn text-red-600 hover:text-red-800 transition-colors duration-200 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            @empty
                                <div class="flex items-center gap-x-3 document-item animate-item-enter">
                                    <div class="relative flex-grow">
                                        <input type="text" name="documents_required[]" class="form-input" placeholder="Ajouter un document requis...">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="fas fa-file-check text-gray-400"></i>
                                        </div>
                                    </div>
                                    <button type="button" class="remove-document-btn text-red-600 hover:text-red-800 transition-colors duration-200 flex-shrink-0">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </div>
                            @endforelse
                        </div>
                        
                        <button type="button" id="add-document-btn" class="mt-6 inline-flex items-center px-5 py-2.5 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 active:scale-95 animate-pulse-btn">
                            <i class="fas fa-plus-circle -ml-1 mr-3 text-lg"></i>
                            Ajouter un document
                        </button>
                        @error('documents_required')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                        <p class="mt-2 text-xs text-gray-500">Indiquez les documents que les participants doivent fournir lors de l'inscription.</p>
                    </div>
                </div>

                <div class="lg:col-span-1 space-y-8">
                    <div class="border border-gray-200 rounded-lg p-6 bg-white shadow-lg sticky-card animated-section" style="top: 20px;">
                        <h4 class="text-xl font-bold text-gray-700 mb-5 flex items-center">
                            <i class="fas fa-chart-line mr-3 text-gray-500 animate-icon-fade"></i> Publication & Actions
                        </h4>
                        
                        <div class="mb-5">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <select class="form-select" id="status" name="status" required>
                                    <option value="draft" {{ old('status', $formation->status) == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                    <option value="published" {{ old('status', $formation->status) == 'published' ? 'selected' : '' }}>Publié</option>
                                    <option value="completed" {{ old('status', $formation->status) == 'completed' ? 'selected' : '' }}>Terminé</option>
                                </select>
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <i class="fas fa-clipboard-check text-gray-400"></i>
                                </div>
                            </div>
                            @error('status')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                            <p class="mt-2 text-xs text-gray-500">
                                <strong>Brouillon:</strong> Visible uniquement par vous.<br>
                                <strong>Publié:</strong> Visible par tous les utilisateurs.<br>
                                <strong>Terminé:</strong> Formation achevée.
                            </p>
                        </div>

                        <div class="space-y-3">
                            <button type="submit" name="action" value="save" id="save-btn" class="btn-gradient w-full group">
                                <i class="fas fa-save mr-2 group-hover:scale-110 transition-transform duration-200"></i> Enregistrer
                                <span class="loading-spinner ml-2 hidden">
                                    <i class="fas fa-circle-notch fa-spin"></i>
                                </span>
                            </button>
                            
                            <button type="submit" name="action" value="save_and_continue" class="btn-secondary-gradient w-full group">
                                <i class="fas fa-arrow-right mr-2 group-hover:scale-110 transition-transform duration-200"></i> Enregistrer et Continuer
                            </button>
                            
                            <a href="{{ route('formations.show', $formation) }}" class="btn-secondary-outline w-full group">
                                <i class="fas fa-times mr-2 group-hover:scale-110 transition-transform duration-200"></i> Annuler
                            </a>
                        </div>

                        <hr class="my-6 border-gray-200">

                        <div class="space-y-3">
                            @if($formation->status !== 'published')
                                <form action="{{ route('formations.toggleStatus', $formation) }}" method="POST" class="d-inline w-full">
                                    @csrf
                                    <button type="submit" class="btn-green-gradient w-full group">
                                        <i class="fas fa-eye mr-2 group-hover:scale-110 transition-transform duration-200"></i> Publier
                                    </button>
                                </form>
                            @else
                                <form action="{{ route('formations.toggleStatus', $formation) }}" method="POST" class="d-inline w-full">
                                    @csrf
                                    <button type="submit" class="btn-orange-gradient w-full group">
                                        <i class="fas fa-eye-slash mr-2 group-hover:scale-110 transition-transform duration-200"></i> Dépublier
                                    </button>
                                </form>
                            @endif
                            
                            <a href="{{ route('formations.duplicate', $formation) }}" class="btn-info-gradient w-full group">
                                <i class="fas fa-copy mr-2 group-hover:scale-110 transition-transform duration-200"></i> Dupliquer
                            </a>
                        </div>
                    </div>

                    @if($formation->inscriptions->count() > 0)
                    <div class="border border-purple-200 rounded-lg p-6 bg-white shadow-lg section-card animated-section">
                        <h4 class="text-xl font-bold text-purple-700 mb-5 flex items-center">
                            <i class="fas fa-chart-bar mr-3 text-purple-500 animate-icon-fade"></i> Statistiques Actuelles
                        </h4>
                        <div class="grid grid-cols-2 gap-4 text-center">
                            <div>
                                <h6 class="text-indigo-600 text-2xl font-bold">{{ $formation->inscriptions->count() }}</h6>
                                <small class="text-gray-600">Inscriptions</small>
                            </div>
                            <div>
                                <h6 class="text-emerald-600 text-2xl font-bold">{{ $formation->capacity - $formation->inscriptions->count() }}</h6>
                                <small class="text-gray-600">Places restantes</small>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="border border-yellow-200 rounded-lg p-6 bg-white shadow-lg section-card animated-section">
                        <h4 class="text-xl font-bold text-yellow-700 mb-5 flex items-center">
                            <i class="fas fa-question-circle mr-3 text-yellow-500 animate-icon-fade"></i> Aide & Conseils
                        </h4>
                        <div class="text-sm text-gray-700 space-y-3">
                            <h6 class="font-semibold text-gray-800">Conseils pour modifier une formation :</h6>
                            <ul class="list-none p-0 m-0 space-y-2">
                                <li class="flex items-start">
                                    <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3 flex-shrink-0"></i>
                                    <span>Vérifiez attentivement les inscriptions avant de modifier les dates ou la capacité.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3 flex-shrink-0"></i>
                                    <span>Prévenez les participants des changements importants pour éviter les confusions.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3 flex-shrink-0"></i>
                                    <span>La réduction de capacité doit être gérée avec prudence si des inscrits dépassent la nouvelle limite.</span>
                                </li>
                                <li class="flex items-start">
                                    <i class="fas fa-lightbulb text-yellow-500 mt-1 mr-3 flex-shrink-0"></i>
                                    <span>Sauvegardez régulièrement vos modifications pour ne perdre aucune donnée.</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @push('styles')
    <style>
        /* Base styles from previous Create Form */
        .py-12 { padding-top: 3rem; padding-bottom: 3rem; }
        .bg-gray-100 { background-color: #f3f4f6; }
        .max-w-7xl { max-width: 80rem; }
        .max-w-4xl { max-width: 56rem; } /* Adjusted for a slightly narrower form */
        .mx-auto { margin-left: auto; margin-right: auto; }
        .sm\:px-6 { padding-left: 1.5rem; padding-right: 1.5rem; }
        .lg\:px-8 { padding-left: 2rem; padding-right: 2rem; }
        .min-h-screen { min-height: 100vh; }
        .relative { position: relative; }
        .inset-0 { top: 0; right: 0; bottom: 0; left: 0; }
        .-z-10 { z-index: -10; }
        .space-y-8 > * + * { margin-top: 2rem; }
        .space-y-6 > * + * { margin-top: 1.5rem; }
        .space-y-4 > * + * { margin-top: 1rem; }
        .space-y-3 > * + * { margin-top: 0.75rem; }
        .space-x-3 > * + * { margin-left: 0.75rem; }
        .space-x-4 > * + * { margin-left: 1rem; }
        .sm\:space-y-0 > * + * { margin-top: 0; }
        .sm\:space-x-4 > * + * { margin-left: 1rem; }
        .mb-1 { margin-bottom: 0.25rem; }
        .mb-2 { margin-bottom: 0.5rem; }
        .mb-3 { margin-bottom: 0.75rem; } /* for original bootstrap classes */
        .mb-4 { margin-bottom: 1rem; }
        .mb-5 { margin-bottom: 1.25rem; }
        .mb-6 { margin-bottom: 1.5rem; }
        .mb-8 { margin-bottom: 2rem; }
        .mt-1 { margin-top: 0.25rem; }
        .mt-2 { margin-top: 0.5rem; }
        .mt-4 { margin-top: 1rem; }
        .mt-6 { margin-top: 1.5rem; }
        .mt-8 { margin-top: 2rem; }
        .mr-2 { margin-right: 0.5rem; }
        .mr-3 { margin-right: 0.75rem; }
        .-ml-1 { margin-left: -0.25rem; }

        .flex { display: flex; }
        .items-center { align-items: center; }
        .justify-between { justify-content: space-between; }
        .justify-end { justify-content: flex-end; }
        .justify-start { justify-content: flex-start; }
        .flex-col { flex-direction: column; }
        .flex-grow { flex-grow: 1; }
        .flex-shrink-0 { flex-shrink: 0; }

        .grid { display: grid; }
        .grid-cols-1 { grid-template-columns: repeat(1, minmax(0, 1fr)); }
        .gap-x-6 { column-gap: 1.5rem; }
        .gap-x-8 { column-gap: 2rem; }
        .gap-y-6 { row-gap: 1.5rem; }
        .gap-y-8 { row-gap: 2rem; }
        .gap-4 { gap: 1rem; } /* for stats */
        
        .sm\:flex-row { flex-direction: row; }
        .sm\:col-span-4 { grid-column: span 4 / span 4; }
        .sm\:col-span-2 { grid-column: span 2 / span 2; }
        .sm\:col-span-full { grid-column: span 6 / span 6; }
        .sm\:w-auto { width: auto; }
        .sm\:justify-start { justify-content: flex-start; }

        .md\:grid-cols-2 { grid-template-columns: repeat(2, minmax(0, 1fr)); }
        .md\:col-span-1 { grid-column: span 1 / span 1; }
        .md\:col-span-2 { grid-column: span 2 / span 2; }

        .lg\:grid-cols-3 { grid-template-columns: repeat(3, minmax(0, 1fr)); }
        .lg\:col-span-1 { grid-column: span 1 / span 1; }
        .lg\:col-span-2 { grid-column: span 2 / span 2; }
        .lg\:col-span-3 { grid-column: span 3 / span 3; }

        .w-full { width: 100%; }
        .h-6 { height: 1.5rem; }
        .w-6 { width: 1.5rem; }
        .h-7 { height: 1.75rem; } /* For delete icons */
        .w-7 { width: 1.75rem; } /* For delete icons */

        .text-sm { font-size: 0.875rem; }
        .text-base { font-size: 1rem; }
        .text-lg { font-size: 1.125rem; }
        .text-xl { font-size: 1.25rem; }
        .text-2xl { font-size: 1.5rem; }
        .text-3xl { font-size: 1.875rem; }
        .font-medium { font-weight: 500; }
        .font-semibold { font-weight: 600; }
        .font-bold { font-weight: 700; }
        .font-extrabold { font-weight: 800; }
        .leading-6 { line-height: 1.5rem; }

        .text-gray-700 { color: #374151; }
        .text-gray-800 { color: #1f2937; }
        .text-gray-900 { color: #111827; }
        .text-gray-400 { color: #9ca3af; }
        .text-gray-500 { color: #6b7280; }
        .text-gray-600 { color: #4b5563; }
        .text-white { color: #ffffff; }
        .text-red-500 { color: #ef4444; }
        .text-red-600 { color: #dc2626; }
        .text-red-700 { color: #b91c1c; }
        .text-red-800 { color: #991b1b; }
        .text-indigo-500 { color: #6366f1; }
        .text-indigo-600 { color: #4f46e5; }
        .text-indigo-700 { color: #4338ca; }
        .text-emerald-500 { color: #10b981; }
        .text-emerald-600 { color: #059669; }
        .text-emerald-700 { color: #047857; }
        .text-blue-500 { color: #3b82f6; }
        .text-blue-600 { color: #2563eb; }
        .text-blue-700 { color: #1d4ed8; }
        .text-purple-500 { color: #a855f7; }
        .text-purple-600 { color: #9333ea; }
        .text-purple-700 { color: #7e22ce; }
        .text-yellow-500 { color: #f59e0b; }
        .text-yellow-700 { color: #b45309; }

        .text-center { text-align: center; }

        .bg-white { background-color: #ffffff; }
        .bg-gray-200 { background-color: #e5e7eb; }
        .hover\:bg-gray-300:hover { background-color: #d1d5db; }
        .rounded-md { border-radius: 0.375rem; }
        .rounded-lg { border-radius: 0.5rem; }
        .shadow-sm { box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); }
        .shadow-lg { box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05); }
        .shadow-xl { box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04); }
        .shadow-2xl { box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25); }

        .overflow-hidden { overflow: hidden; }
        .p-6 { padding: 1.5rem; }
        .p-8 { padding: 2rem; }
        
        .border { border-width: 1px; }
        .border-gray-300 { border-color: #d1d5db; }
        .border-gray-200 { border-color: #e5e7eb; }
        .border-indigo-200 { border-color: #c7d2fe; }
        .border-emerald-200 { border-color: #a7f3d0; }
        .border-red-200 { border-color: #fecaca; }
        .border-blue-200 { border-color: #bfdbfe; }
        .border-purple-200 { border-color: #e9d5ff; }
        .border-b { border-bottom-width: 1px; }
        .border-gray-900\/10 { border-color: rgba(17, 24, 39, 0.1); }
        .border-transparent { border-color: transparent; }

        /* Form Input overrides and custom styles */
        .form-input, .form-select, .form-textarea {
            display: block;
            width: 100%;
            border-radius: 0.375rem; /* rounded-md */
            border-width: 1px;
            border-color: #d1d5db; /* border-gray-300 */
            padding-top: 0.375rem; /* py-1.5 */
            padding-bottom: 0.375rem; /* py-1.5 */
            padding-left: 2.5rem !important; /* pl-10 for icon */
            color: #1f2937; /* text-gray-900 */
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); /* shadow-sm */
            transition: all 0.15s ease-in-out; /* transition duration-150 ease-in-out */
        }
        .form-input::placeholder, .form-textarea::placeholder {
            color: #9ca3af; /* placeholder:text-gray-400 */
        }
        .form-input:focus, .form-select:focus, .form-textarea:focus {
            outline: 2px solid transparent;
            outline-offset: 2px;
            box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.45); /* focus:ring-indigo-500 with opacity */
            border-color: #6366F1; /* focus:border-indigo-500 */
        }
        /* Specific focus colors for sections */
        .border-emerald-200 .form-input:focus, .border-emerald-200 .form-select:focus, .border-emerald-200 .form-textarea:focus {
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.45); /* emerald-500 with opacity */
            border-color: #10b981; /* emerald-500 */
        }
        .border-red-200 .form-input:focus, .border-red-200 .form-select:focus, .border-red-200 .form-textarea:focus {
            box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.45); /* red-500 with opacity */
            border-color: #ef4444; /* red-500 */
        }
        .border-blue-200 .form-input:focus, .border-blue-200 .form-select:focus, .border-blue-200 .form-textarea:focus {
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.45); /* blue-500 with opacity */
            border-color: #3b82f6; /* blue-500 */
        }

        /* Buttons styles (Common for create and edit) */
        .btn-base {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem; /* px-5 py-2.5 */
            font-size: 1rem; /* text-base */
            font-weight: 500; /* font-medium */
            border-radius: 0.375rem; /* rounded-md */
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06); /* shadow-sm / shadow-lg */
            transition: all 0.3s ease-in-out;
            transform-origin: center;
            border-width: 1px;
            border-color: transparent;
            cursor: pointer;
        }

        .btn-gradient {
            @extend .btn-base;
            background-image: linear-gradient(to right, #6366F1, #8B5CF6); /* indigo-600 to purple-600 */
            color: #ffffff;
        }
        .btn-gradient:hover {
            background-image: linear-gradient(to right, #4F46E5, #7C3AED); /* darker shades */
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .btn-gradient:active {
            transform: scale(0.95);
        }

        .btn-secondary-gradient {
            @extend .btn-base;
            background-image: linear-gradient(to right, #4b5563, #6b7280); /* gray-700 to gray-500 */
            color: #ffffff;
        }
        .btn-secondary-gradient:hover {
            background-image: linear-gradient(to right, #374151, #4b5563); /* darker shades */
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .btn-secondary-gradient:active {
            transform: scale(0.95);
        }

        .btn-green-gradient {
            @extend .btn-base;
            background-image: linear-gradient(to right, #10b981, #059669); /* emerald-500 to emerald-700 */
            color: #ffffff;
        }
        .btn-green-gradient:hover {
            background-image: linear-gradient(to right, #059669, #047857); /* darker shades */
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .btn-green-gradient:active {
            transform: scale(0.95);
        }

        .btn-orange-gradient {
            @extend .btn-base;
            background-image: linear-gradient(to right, #f97316, #ea580c); /* orange-500 to orange-600 */
            color: #ffffff;
        }
        .btn-orange-gradient:hover {
            background-image: linear-gradient(to right, #c2410c, #9a3412); /* darker shades */
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .btn-orange-gradient:active {
            transform: scale(0.95);
        }

        .btn-info-gradient {
            @extend .btn-base;
            background-image: linear-gradient(to right, #0ea5e9, #0284c7); /* sky-500 to sky-700 */
            color: #ffffff;
        }
        .btn-info-gradient:hover {
            background-image: linear-gradient(to right, #0369a1, #075985); /* darker shades */
            transform: scale(1.05);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }
        .btn-info-gradient:active {
            transform: scale(0.95);
        }

        /* Outline button style (e.g. for Annuler / Retour) */
        .btn-secondary-outline {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0.625rem 1.25rem; /* px-5 py-2.5 */
            font-size: 1rem; /* text-base */
            font-weight: 500; /* font-medium */
            border-radius: 0.375rem; /* rounded-md */
            background-color: #e5e7eb; /* bg-gray-200 */
            color: #374151; /* text-gray-700 */
            border-width: 1px;
            border-color: transparent;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            transition: all 0.2s ease-in-out;
            cursor: pointer;
            text-decoration: none; /* Remove underline for links */
        }
        .btn-secondary-outline:hover {
            background-color: #d1d5db; /* hover:bg-gray-300 */
            transform: scale(1.05);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        .btn-secondary-outline:active {
            transform: scale(0.95);
        }

        /* Specific animations from Create Form */
        .animated-form {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInSlideUp 0.8s ease-out forwards;
        }
        .animated-header {
            animation: fadeInSlideDown 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(-20px);
        }
        @keyframes fadeInSlideUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeInSlideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-text-pop {
            animation: textPop 1s ease-out 0.5s forwards;
            opacity: 0;
            transform: scale(0.8);
        }
        @keyframes textPop {
            0% { opacity: 0; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.05); }
            100% { opacity: 1; transform: scale(1); }
        }

        .animate-icon-fade {
            animation: iconFade 0.8s ease-out 0.8s forwards;
            opacity: 0;
        }
        @keyframes iconFade {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-item-enter {
            animation: itemEnter 0.5s ease-out forwards;
            opacity: 0;
            transform: translateX(-10px);
        }
        @keyframes itemEnter {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }
        .animate-item-exit {
            animation: itemExit 0.5s ease-out forwards;
            opacity: 0;
            transform: translateX(10px);
        }
        @keyframes itemExit {
            from { opacity: 1; transform: translateX(0); }
            to { opacity: 0; transform: translateX(10px); }
        }

        .animate-pulse-btn {
            animation: pulse-effect 2s infinite ease-in-out;
        }
        @keyframes pulse-effect {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7); }
            70% { transform: scale(1.02); box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
        }

        .pattern-dots {
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px); /* gray-200 */
            background-size: 20px 20px;
        }

        .text-red-600 {
            color: #ef4444;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            animation: fadeIn 0.3s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .loading-spinner {
            display: inline-block;
            animation: spin 1s linear infinite;
        }
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Specific styles for section cards */
        .section-card {
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 10px 15px rgba(0, 0, 0, 0.1);
        }
        .section-card:hover {
            box-shadow: 0 10px 15px rgba(0, 0, 0, 0.1), 0 20px 25px rgba(0, 0, 0, 0.2);
            transform: translateY(-3px);
        }

        /* Sticky sidebar card */
        .sticky-card {
            position: sticky;
            top: 2rem; /* Adjusted for better spacing */
            align-self: flex-start; /* Ensures it sticks to the top of its flex container */
            z-index: 10; /* Ensure it stays above other content when scrolling */
        }

        /* Hide default Bootstrap invalid feedback, as we use Tailwind text-red-600 */
        .invalid-feedback {
            display: none !important;
        }

    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formation-form');
            const saveBtn = document.getElementById('save-btn'); // Get specific save button
            
            // Logic for adding/removing Prerequisites
            const prerequisitesContainer = document.getElementById('prerequisites-container');
            const addPrerequisiteBtn = document.getElementById('add-prerequisite-btn'); // Changed ID

            addPrerequisiteBtn.addEventListener('click', function () {
                addInputField(prerequisitesContainer, 'prerequisites[]', 'prerequisite-item', 'fas fa-check-circle');
            });

            prerequisitesContainer.addEventListener('click', function (event) {
                if (event.target.closest('.remove-prerequisite-btn')) { // Changed class
                    removeInputField(event.target.closest('.prerequisite-item'));
                }
            });

            // Logic for adding/removing Documents
            const documentsContainer = document.getElementById('documents-container');
            const addDocumentBtn = document.getElementById('add-document-btn'); // Changed ID

            addDocumentBtn.addEventListener('click', function () {
                addInputField(documentsContainer, 'documents_required[]', 'document-item', 'fas fa-file-check');
            });

            documentsContainer.addEventListener('click', function (event) {
                if (event.target.closest('.remove-document-btn')) { // Changed class
                    removeInputField(event.target.closest('.document-item'));
                }
            });

            // Helper function to add a new input field with icon
            function addInputField(container, name, itemClass, iconClass) {
                const div = document.createElement('div');
                div.className = `flex items-center gap-x-3 ${itemClass} animate-item-enter`;
                div.innerHTML = `
                    <div class="relative flex-grow">
                        <input type="text" name="${name}" class="form-input" placeholder="Ex: ${name.includes('prerequisites') ? 'Avoir des connaissances en HTML' : 'Copie de la CIN'}">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <i class="${iconClass} text-gray-400"></i>
                        </div>
                    </div>
                    <button type="button" class="remove-prerequisite-btn text-red-600 hover:text-red-800 transition-colors duration-200 flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                    </button>
                `;
                container.appendChild(div);
            }

            // Helper function to remove an input field
            function removeInputField(item) {
                const container = item.parentElement;
                const inputElement = item.querySelector('input[type="text"]');
                // Only remove if more than one item, OR if it's the only item and its input is empty
                if (container.querySelectorAll(`.${item.classList[2]}`).length > 1 || (inputElement && inputElement.value.trim() === '')) {
                    item.classList.add('animate-item-exit'); // Add exit animation
                    item.addEventListener('animationend', () => {
                        item.remove();
                    });
                } else {
                    alert('Au moins un champ est requis. Vous pouvez le laisser vide si non applicable.');
                }
            }
            
            // Date validation (from previous script)
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            
            if (startDate && endDate) {
                startDate.addEventListener('change', function() {
                    endDate.min = this.value;
                    if (endDate.value && endDate.value < this.value) {
                        endDate.value = this.value;
                    }
                });
                // Set min for endDate on page load if startDate is already set
                if (startDate.value) {
                    endDate.min = startDate.value;
                }
            }
            
            // Form validation with warnings for existing inscriptions
            const formElement = document.getElementById('formation-form'); // Renamed to avoid conflict with 'form' variable in auto-save section
            const originalCapacity = {{ $formation->capacity }};
            const currentInscriptions = {{ $formation->inscriptions->count() }};
            
            formElement.addEventListener('submit', function(e) {
                // Check capacity reduction
                const newCapacity = parseInt(document.getElementById('capacity').value);
                if (newCapacity < currentInscriptions) {
                    if (!confirm(`Attention: Vous tentez de réduire la capacité à ${newCapacity} places alors qu'il y a déjà ${currentInscriptions} inscriptions. Continuer?`)) {
                        e.preventDefault();
                        return false;
                    }
                }
                
                // Custom validation logic (client-side basic)
                const title = document.getElementById('title').value.trim();
                const description = document.getElementById('description').value.trim();
                
                if (title.length < 5) {
                    e.preventDefault();
                    alert('Le titre doit contenir au moins 5 caractères.');
                    return false;
                }
                
                if (description.length < 20) {
                    e.preventDefault();
                    alert('La description doit contenir au moins 20 caractères.');
                    return false;
                }
                
                // Show loading state for the specific save button
                const submittingBtn = e.submitter; // Get the button that triggered the submit
                if (submittingBtn && submittingBtn.id === 'save-btn') { // Only apply loading to 'Enregistrer'
                    submittingBtn.disabled = true;
                    submittingBtn.querySelector('.loading-spinner').classList.remove('hidden');
                    submittingBtn.classList.add('opacity-75', 'cursor-not-allowed');
                }
            });
            
            // Auto-save functionality (optional, client-side only)
            let autoSaveTimer;
            const formInputs = formElement.querySelectorAll('input, textarea, select');
            
            formInputs.forEach(input => {
                input.addEventListener('input', function() {
                    clearTimeout(autoSaveTimer);
                    autoSaveTimer = setTimeout(() => {
                        // Implement auto-save logic here if needed (e.g., an AJAX call)
                        // console.log('Auto-saving...');
                        // Example: send data via AJAX
                        /*
                        fetch(formElement.action, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content') // Ensure CSRF token is available
                            },
                            body: JSON.stringify({
                                _method: 'PUT', // For Laravel's PUT method
                                // Collect relevant form data here manually or using FormData API
                                title: document.getElementById('title').value,
                                description: document.getElementById('description').value,
                                // ... other fields
                            })
                        })
                        .then(response => response.json())
                        .then(data => console.log('Auto-saved:', data))
                        .catch(error => console.error('Auto-save error:', error));
                        */
                    }, 30000); // Auto-save every 30 seconds
                });
            });
            
            // Warn about unsaved changes
            let formChanged = false;
            formInputs.forEach(input => {
                input.addEventListener('change', function() {
                    formChanged = true;
                });
            });
            
            window.addEventListener('beforeunload', function(e) {
                if (formChanged) {
                    e.preventDefault();
                    e.returnValue = ''; // Required for Chrome
                }
            });
            
            // Reset form changed flag on successful submit
            formElement.addEventListener('submit', function() {
                formChanged = false;
            });
        });
    </script>
    @endpush
@endsection