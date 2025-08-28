@extends('layouts.app') {{-- هنا تستخدم الـ layout التقليدي ديالك --}}

@section('title', 'Créer une nouvelle Formation') {{-- تعريف الـ title للصفحة --}}

@section('content') {{-- هنا سيبدأ محتوى الصفحة داخل الـ layout --}}

    <div class="py-12 bg-gray-100 min-h-screen"> {{-- Ajout d'une couleur de fond pour le body --}}
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8"> {{-- Reduction de la largeur pour un look plus centré --}}
            <div class="bg-white overflow-hidden shadow-2xl sm:rounded-lg p-8 animated-form relative"> {{-- Shadow plus prononcée, plus de padding, et position relative pour le pseudo-element --}}
                
                {{-- Ajout d'un pseudo-element pour un effet de fond subtil --}}
                <div class="absolute inset-0 bg-gradient-to-br from-indigo-50 to-purple-50 opacity-50 rounded-lg -z-10 pattern-dots"></div>


                <h3 class="text-3xl font-extrabold text-gray-800 mb-8 text-center animate-text-pop">
                    Créer une nouvelle Formation
                </h3>
                <p class="text-center text-gray-600 mb-8">
                    Remplissez les informations ci-dessous pour ajouter une formation à notre catalogue.
                </p>

                <form action="{{ route('formations.store') }}" method="POST" class="space-y-8" id="formation-form"> {{-- Plus d'espace entre les sections --}}
                    @csrf

                    <div class="border border-indigo-200 rounded-lg p-6 bg-white transition-all duration-300 hover:shadow-lg section-card"> {{-- Border, rounded, bg-white, hover effect --}}
                        <h4 class="text-xl font-bold text-indigo-700 mb-5 flex items-center">
                            <i class="fas fa-info-circle mr-3 text-indigo-500 animate-icon-fade"></i> Informations Générales
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6"> {{-- Utilisez md: pour les colonnes --}}
                            
                            <div class="md:col-span-1"> {{-- Adjusted to md:col-span-1 for 2 columns on medium screens --}}
                                <label for="title" class="block text-sm font-medium text-gray-700 mb-1">Titre de la Formation</label>
                                <div class="relative">
                                    <input type="text" name="title" id="title" class="form-input" value="{{ old('title') }}" placeholder="Ex: Développement Web avec Laravel" required>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-book-open text-gray-400"></i>
                                    </div>
                                </div>
                                @error('title')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-1">
                                <label for="category_id" class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                                <div class="relative">
                                    <select id="category_id" name="category_id" class="form-select" required>
                                        <option value="">Sélectionner une catégorie</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
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

                            <div class="md:col-span-2"> {{-- Full width on medium screens --}}
                                <label for="description" class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                                <div class="relative">
                                    <textarea id="description" name="description" rows="5" class="form-textarea" placeholder="Décrivez le contenu de la formation..." required>{{ old('description') }}</textarea>
                                    <div class="absolute top-3 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-align-left text-gray-400"></i>
                                    </div>
                                </div>
                                @error('description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-1">
                                <label for="consultant_id" class="block text-sm font-medium text-gray-700 mb-1">Consultant</label>
                                <div class="relative">
                                    <select id="consultant_id" name="consultant_id" class="form-select" required>
                                        <option value="">Sélectionner un consultant</option>
                                        @foreach($consultants as $consultant)
                                            <option value="{{ $consultant->id }}" {{ old('consultant_id') == $consultant->id ? 'selected' : '' }}>{{ $consultant->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-user-tie text-gray-400"></i>
                                    </div>
                                </div>
                                @error('consultant_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-1">
                                <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Statut</label>
                                <div class="relative">
                                    <select id="status" name="status" class="form-select" required>
                                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                        <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publiée</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                                    </select>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-clipboard-check text-gray-400"></i>
                                    </div>
                                </div>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="border border-emerald-200 rounded-lg p-6 bg-white transition-all duration-300 hover:shadow-lg section-card">
                        <h4 class="text-xl font-bold text-emerald-700 mb-5 flex items-center">
                            <i class="fas fa-dollar-sign mr-3 text-emerald-500 animate-icon-fade"></i> Détails et Paiement
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-x-8 gap-y-6">
                            
                            <div>
                                <label for="price" class="block text-sm font-medium text-gray-700 mb-1">Prix (MAD)</label>
                                <div class="relative">
                                    <input type="number" name="price" id="price" step="0.01" min="0" class="form-input" value="{{ old('price') }}" placeholder="0.00" required>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-money-bill-wave text-gray-400"></i>
                                    </div>
                                </div>
                                @error('price')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="duration_hours" class="block text-sm font-medium text-gray-700 mb-1">Durée (Heures)</label>
                                <div class="relative">
                                    <input type="number" name="duration_hours" id="duration_hours" min="1" class="form-input" value="{{ old('duration_hours') }}" required>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-clock text-gray-400"></i>
                                    </div>
                                </div>
                                @error('duration_hours')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="duration_minutes" class="block text-sm font-medium text-gray-700 mb-1">Durée (Minutes - Optionnel)</label>
                                <div class="relative">
                                    <input type="number" name="duration_minutes" id="duration_minutes" min="0" max="59" class="form-input" value="{{ old('duration_minutes') }}">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-hourglass-half text-gray-400"></i>
                                    </div>
                                </div>
                                @error('duration_minutes')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="capacity" class="block text-sm font-medium text-gray-700 mb-1">Capacité (Nb. Places)</label>
                                <div class="relative">
                                    <input type="number" name="capacity" id="capacity" min="1" class="form-input" value="{{ old('capacity') }}" required>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-users text-gray-400"></i>
                                    </div>
                                </div>
                                @error('capacity')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Date Début</label>
                                <div class="relative">
                                    <input type="date" name="start_date" id="start_date" class="form-input" value="{{ old('start_date') }}" required>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-alt text-gray-400"></i>
                                    </div>
                                </div>
                                @error('start_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">Date Fin</label>
                                <div class="relative">
                                    <input type="date" name="end_date" id="end_date" class="form-input" value="{{ old('end_date') }}" required>
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <i class="fas fa-calendar-check text-gray-400"></i>
                                    </div>
                                </div>
                                @error('end_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="lg:col-span-3">
                                <label class="block text-sm font-medium text-gray-700 mb-1">Options de Paiement Disponibles <span class="text-red-500">*</span></label>
                                <div class="flex flex-wrap gap-4 mt-2">
                                    @php
                                        // Define common payment options
                                        $paymentOptions = [
                                            1 => 'Paiement Complet (1 Versement)',
                                            2 => '2 Versements',
                                            3 => '3 Versements',
                                            4 => '4 Versements',
                                            6 => '6 Versements',
                                            10 => '10 Versements',
                                            12 => '12 Versements',
                                        ];
                                        // Use old input or default to [1] (full payment)
                                        $selectedOptions = old('available_payment_options', [1]);
                                    @endphp

                                    @foreach($paymentOptions as $value => $label)
                                        <div class="flex items-center">
                                            <input type="checkbox" name="available_payment_options[]" id="payment_option_{{ $value }}" value="{{ $value }}"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                                @checked(in_array($value, $selectedOptions))
                                            >
                                            <label for="payment_option_{{ $value }}" class="ml-2 block text-sm text-gray-900">
                                                {{ $label }}
                                            </label>
                                        </div>
                                    @endforeach
                                </div>
                                @error('available_payment_options')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                                @error('available_payment_options.*') {{-- Catch validation errors for individual items --}}
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="border border-red-200 rounded-lg p-6 bg-white transition-all duration-300 hover:shadow-lg section-card">
                        <h4 class="text-xl font-bold text-red-700 mb-5 flex items-center">
                            <i class="fas fa-question-circle mr-3 text-red-500 animate-icon-fade"></i> Prérequis (Optionnel)
                        </h4>
                        <div id="prerequisites-container" class="space-y-4">
                            @if(old('prerequisites'))
                                @foreach(old('prerequisites') as $prerequisite)
                                    <div class="flex items-center gap-x-3 prerequisite-item animate-item-enter">
                                        <div class="relative flex-grow">
                                            <input type="text" name="prerequisites[]" class="form-input" value="{{ $prerequisite }}" placeholder="Ex: Avoir des connaissances en HTML">
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
                                @endforeach
                            @else
                                <div class="flex items-center gap-x-3 prerequisite-item animate-item-enter">
                                    <div class="relative flex-grow">
                                        <input type="text" name="prerequisites[]" class="form-input" placeholder="Ex: Avoir des connaissances en HTML">
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
                            @endif
                        </div>
                        <button type="button" id="add-prerequisite-btn" class="mt-6 inline-flex items-center px-5 py-2.5 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 transform hover:scale-105 active:scale-95 animate-pulse-btn">
                            <i class="fas fa-plus-circle -ml-1 mr-3 text-lg"></i>
                            Ajouter un prérequis
                        </button>
                        @error('prerequisites')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="border border-blue-200 rounded-lg p-6 bg-white transition-all duration-300 hover:shadow-lg section-card">
                        <h4 class="text-xl font-bold text-blue-700 mb-5 flex items-center">
                            <i class="fas fa-file-alt mr-3 text-blue-500 animate-icon-fade"></i> Documents Requis (Optionnel)
                        </h4>
                        <div id="documents-container" class="space-y-4">
                             @if(old('documents_required'))
                                @foreach(old('documents_required') as $document)
                                    <div class="flex items-center gap-x-3 document-item animate-item-enter">
                                        <div class="relative flex-grow">
                                            <input type="text" name="documents_required[]" class="form-input" value="{{ $document }}" placeholder="Ex: Copie de la CIN">
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
                                @endforeach
                            @else
                                <div class="flex items-center gap-x-3 document-item animate-item-enter">
                                    <div class="relative flex-grow">
                                        <input type="text" name="documents_required[]" class="form-input" placeholder="Ex: Copie de la CIN">
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
                            @endif
                        </div>
                        <button type="button" id="add-document-btn" class="mt-6 inline-flex items-center px-5 py-2.5 border border-transparent text-base font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 transform hover:scale-105 active:scale-95 animate-pulse-btn">
                            <i class="fas fa-plus-circle -ml-1 mr-3 text-lg"></i>
                            Ajouter un document
                        </button>
                        @error('documents_required')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="mt-8 flex items-center justify-end gap-x-6">
                        <a href="{{ route('formations.index') }}" class="btn-secondary-outline group">
                            <i class="fas fa-times-circle mr-2 group-hover:scale-110 transition-transform duration-200"></i> Annuler
                        </a>
                        <button type="submit" id="submit-btn" class="btn-gradient group">
                            <i class="fas fa-save mr-2 group-hover:scale-110 transition-transform duration-200"></i> Enregistrer la Formation
                            <span class="loading-spinner ml-2 hidden">
                                <i class="fas fa-circle-notch fa-spin"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('styles')
    <style>
        /* Base form input styles to override default browser/edmate styles */
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
            line-height: 1.5; /* Ensure consistent line height */
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

        /* Buttons styles */
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
            text-decoration: none; /* For links */
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
        
        /* Animation for form entrance */
        .animated-form {
            opacity: 0;
            transform: translateY(20px);
            animation: fadeInSlideUp 0.8s ease-out forwards;
        }

        @keyframes fadeInSlideUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Text pop animation for main title */
        .animate-text-pop {
            animation: textPop 1s ease-out 0.5s forwards; /* Added delay */
            opacity: 0;
            transform: scale(0.8);
        }

        @keyframes textPop {
            0% { opacity: 0; transform: scale(0.8); }
            50% { opacity: 1; transform: scale(1.05); }
            100% { opacity: 1; transform: scale(1); }
        }

        /* Icon fade-in animation for section titles */
        .animate-icon-fade {
            animation: iconFade 0.8s ease-out 0.8s forwards; /* Added delay after form anim */
            opacity: 0;
        }

        @keyframes iconFade {
            from { opacity: 0; transform: translateX(-10px); }
            to { opacity: 1; transform: translateX(0); }
        }

        /* Item enter animation for dynamic fields */
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

        /* Button Pulse Animation */
        .animate-pulse-btn {
            animation: pulse-effect 2s infinite ease-in-out;
        }

        @keyframes pulse-effect {
            0% { transform: scale(1); box-shadow: 0 0 0 0 rgba(99, 102, 241, 0.7); }
            70% { transform: scale(1.02); box-shadow: 0 0 0 10px rgba(99, 102, 241, 0); }
            100% { transform: scale(1); box-shadow: 0 0 0 0 rgba(99, 102, 241, 0); }
        }

        /* Pattern background for the main form card */
        .pattern-dots {
            background-image: radial-gradient(#e5e7eb 1px, transparent 1px); /* gray-200 */
            background-size: 20px 20px;
        }

        /* Improved error message style */
        .text-red-600 {
            color: #ef4444; /* Tailwind red-500 */
            font-size: 0.75rem; /* Smaller font for errors */
            margin-top: 0.25rem;
            animation: fadeIn 0.3s ease-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Submit button loading spinner */
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
            transform: translateY(-3px); /* Slight lift on hover */
        }
    </style>
    @endpush

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const form = document.getElementById('formation-form');
            const submitBtn = document.getElementById('submit-btn');

            // Logic for adding/removing Prerequisites
            const prerequisitesContainer = document.getElementById('prerequisites-container');
            const addPrerequisiteBtn = document.getElementById('add-prerequisite-btn');

            addPrerequisiteBtn.addEventListener('click', function () {
                addInputField(prerequisitesContainer, 'prerequisites[]', 'prerequisite-item', 'fas fa-check-circle');
            });

            prerequisitesContainer.addEventListener('click', function (event) {
                if (event.target.closest('.remove-prerequisite-btn')) {
                    removeInputField(event.target.closest('.prerequisite-item'));
                }
            });

            // Logic for adding/removing Documents
            const documentsContainer = document.getElementById('documents-container');
            const addDocumentBtn = document.getElementById('add-document-btn');

            addDocumentBtn.addEventListener('click', function () {
                addInputField(documentsContainer, 'documents_required[]', 'document-item', 'fas fa-file-check');
            });

            documentsContainer.addEventListener('click', function (event) {
                if (event.target.closest('.remove-document-btn')) {
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
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1
                            0 00-1 1v3M4 7h16" />
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

            // Date validation (for create form, end_date must be after start_date)
            const startDate = document.getElementById('start_date');
            const endDate = document.getElementById('end_date');
            
            if (startDate && endDate) {
                startDate.addEventListener('change', function() {
                    endDate.min = this.value;
                    if (endDate.value && endDate.value < this.value) {
                        endDate.value = this.value;
                    }
                });
            }

            // Submit button loading state
            form.addEventListener('submit', function() {
                submitBtn.disabled = true;
                loadingSpinner.classList.remove('hidden');
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            });
        });
    </script>
    @endpush
@endsection