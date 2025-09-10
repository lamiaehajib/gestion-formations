@extends('layouts.app')

@section('title', 'Modifier l\'Inscription - ' . $inscription->user->name)

@section('content')

        <div >
            <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
                <div class="glass-card overflow-hidden shadow-2xl sm:rounded-lg p-8 animated-form relative">
                    <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-pink-50 opacity-50 rounded-lg -z-10 pattern-dots"></div>

                    <h3 class="text-3xl font-extrabold text-gray-800 mb-8 text-center animate-text-pop">
                        <span class="text-gradient">Modifier l'Inscription</span>
                    </h3>
                    <p class="text-center text-gray-600 mb-8 animate-fade-in">
                        Mettez à jour les informations de cette inscription pour <span class="font-semibold text-red-700">{{ $inscription->user->name }}</span>.
                    </p>

                    {{-- Success/Error Messages --}}
                    @if (session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4 animated-alert alert-success" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 animated-alert alert-error" role="alert">
                            <span class="block sm:inline">{{ session('error') }}</span>
                        </div>
                    @endif
                    @if ($errors->any())
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-4 animated-alert alert-error" role="alert">
                            <ul class="mt-3 list-disc list-inside text-sm">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('inscriptions.update', $inscription) }}" method="POST" class="space-y-8" id="inscription-form" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <div class="section-card border-2 border-red-200 rounded-lg p-6 bg-white transition-all duration-300 hover:shadow-lg animate-slide-up">
                            <h4 class="text-xl font-bold text-red-700 mb-5 flex items-center">
                                <i class="fas fa-clipboard-list mr-3 text-red-500 animate-pulse-soft"></i>
                                <span class="animate-type-writer">Détails de l'Inscription</span>
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                                {{-- Champ Étudiant (Lecture seule) --}}
                                <div class="form-group animate-slide-right">
                                    <label for="user_id" class="form-label label-with-icon">
                                        Étudiant
                                        <i class="fas fa-user input-icon-label"></i>
                                    </label>
                                    <div class="input-wrapper">
                                        <select name="user_id" id="user_id" class="form-select">
                                            {{-- هنا زيد الخيارات ديال جميع الطلاب اللي بغيتي المستخدم يقدّر يختارهم --}}
                                            @foreach($users as $user)
                                                <option value="{{ $user->id }}" {{ ($user->id == old('user_id', $inscription->user->id)) ? 'selected' : '' }}>
                                                    {{ $user->name }} ({{ $user->email }})
                                                </option>
                                            @endforeach
                                        </select>
                                        {{-- حيد هاد الكود ديال الـ input:hidden حيت ما بقاش ضروري --}}
                                        {{-- <input type="hidden" name="user_id" value="{{ $inscription->user->id }}"> --}}
                                    </div>
                                </div>

                                {{-- Champ Formation (Lecture seule) --}}
                                <div class="form-group animate-slide-left">
                                    <label for="formation_id" class="form-label label-with-icon">
                                        Formation
                                        <i class="fas fa-book-reader input-icon-label"></i>
                                    </label>
                                    <div class="input-wrapper">
                                       <select name="formation_id" id="formation_id" class="form-select">
            {{-- هنا زيد الخيارات ديال جميع التكوينات اللي بغيتي المستخدم يقدّر يختارهم --}}
            @foreach($formations as $formation)
                <option value="{{ $formation->id }}"
                        data-price="{{ $formation->price }}"
                        data-category="{{ $formation->category->name ?? '' }}"
                        data-available-options="{{ json_encode($formation->available_payment_options ?? [1]) }}"
                        {{ ($formation->id == old('formation_id', $inscription->formation->id)) ? 'selected' : '' }}>
                    {{ $formation->title }} ({{ number_format($formation->price, 2) }} DH)
                </option>
            @endforeach
        </select>
       
                                    </div>
                                </div>

                                {{-- Champ Statut de l'Inscription --}}
                                <div class="form-group animate-fade-in-up">
                                    <label for="status" class="form-label label-with-icon">
                                        Statut de l'Inscription <span class="text-red-500">*</span>
                                        <i class="fas fa-info-circle input-icon-label"></i>
                                    </label>
                                    <div class="input-wrapper">
                                        <select name="status" id="status" class="form-select" required>
                                            <option value="pending" {{ old('status', $inscription->status) == 'pending' ? 'selected' : '' }}>En attente</option>
                                            <option value="active" {{ old('status', $inscription->status) == 'active' ? 'selected' : '' }}>Active</option>
                                            <option value="completed" {{ old('status', $inscription->status) == 'completed' ? 'selected' : '' }}>Terminée</option>
                                            <option value="cancelled" {{ old('status', $inscription->status) == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                                        </select>
                                    </div>
                                    @error('status')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Champ Modalité de Paiement --}}
                                <div class="form-group animate-fade-in-up">
                                    <label for="chosen_installments" class="form-label label-with-icon">
                                        Modalité de Paiement <span class="text-red-500">*</span>
                                        <i class="fas fa-credit-card input-icon-label"></i>
                                    </label>
                                    <div class="input-wrapper">
                                        <select name="chosen_installments" id="chosen_installments" class="form-select" required>
                                            {{-- Options will be populated by JS, or manually if no JS fallback --}}
                                            {{-- Initial option for current value --}}
                                            <option value="{{ $inscription->chosen_installments }}" selected>
                                                @if($inscription->chosen_installments == 1)
                                                    Paiement Complet (1 Versement)
                                                @else
                                                    {{ $inscription->chosen_installments }} Versements
                                                @endif
                                            </option>
                                            @php
    $allPaymentOptions = [1, 2, 3, 4, 6, 10, 12]; // All possible options
                                            @endphp
                                            @foreach($allPaymentOptions as $option)
                                                @if($option != $inscription->chosen_installments)
                                                    <option value="{{ $option }}" {{ old('chosen_installments') == $option ? 'selected' : '' }}>
                                                        @if($option == 1)
                                                            Paiement Complet (1 Versement)
                                                        @else
                                                            {{ $option }} Versements
                                                        @endif
                                                    </option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('chosen_installments')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Champ Montant Total --}}
                                <div class="form-group animate-bounce-in">
                                    <label for="total_amount" class="form-label label-with-icon">
                                        Montant Total <span class="text-red-500">*</span>
                                        <i class="fas fa-dollar-sign input-icon-label"></i>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="number" step="0.01" min="0" name="total_amount" id="total_amount" class="form-input" value="{{ old('total_amount', $inscription->total_amount) }}" required>
                                    </div>
                                    @error('total_amount')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Champ Montant Payé --}}
                                <div class="form-group animate-bounce-in">
                                    <label for="paid_amount" class="form-label label-with-icon">
                                        Montant Total Payé <span class="text-red-500">*</span>
                                        <i class="fas fa-money-check-alt input-icon-label"></i>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="number" step="0.01" min="0" name="paid_amount" id="paid_amount" class="form-input" value="{{ old('paid_amount', $inscription->paid_amount) }}" required>
                                    </div>
                                    @error('paid_amount')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                <div class="md:col-span-2 form-group animate-bounce-in" id="payment-details-summary" style="display: none;">
                                    <div class="bg-gray-100 border-l-4 border-red-500 text-gray-700 p-4 rounded-lg shadow-inner">
                                        <h5 class="font-bold text-lg mb-2">Détails du Paiement</h5>
                                        <p id="first_payment_details" class="text-md font-semibold text-red-600 mb-2"></p>
                                        <p id="remaining_payments_details" class="text-sm"></p>
                                    </div>
                                </div>
                                {{-- Champ Remaining Installments (Lecture seule) --}}
                                <div class="form-group animate-fade-in-up">
                                    <label for="remaining_installments_display" class="form-label label-with-icon">
                                        Acomptes Restants
                                        <i class="fas fa-calendar-alt input-icon-label"></i>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" id="remaining_installments_display" class="form-input bg-gray-100 cursor-not-allowed" value="{{ $inscription->remaining_installments }}" readonly>
                                    </div>
                                </div>

                                {{-- Champ Access Restricted --}}
                                <div class="form-group animate-fade-in-up">
                                    <label for="access_restricted" class="form-label label-with-icon">
                                        Restriction d'accès aux cours
                                        <i class="fas fa-ban input-icon-label"></i>
                                    </label>
                                    <div class="input-wrapper">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="access_restricted" name="access_restricted" value="1"
                                                    @checked(old('access_restricted', $inscription->access_restricted))>
                                            <label class="form-check-label" for="access_restricted">
                                                @if(old('access_restricted', $inscription->access_restricted))
                                                    Accès actuellement restreint.
                                                @else
                                                    Accès non restreint.
                                                @endif
                                            </label>
                                        </div>
                                    </div>
                                    @error('access_restricted')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Champ Next Installment Due Date --}}
                                <div class="form-group animate-fade-in-up">
                                    <label for="next_installment_due_date" class="form-label label-with-icon">
                                        Prochaine date d'échéance de l'acompte
                                        <i class="fas fa-calendar-day input-icon-label"></i>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="date" name="next_installment_due_date" id="next_installment_due_date"
                                                class="form-input" value="{{ old('next_installment_due_date', $inscription->next_installment_due_date ? $inscription->next_installment_due_date->format('Y-m-d') : '') }}">
                                    </div>
                                    @error('next_installment_due_date')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- START of NEW CODE --}}
                                <div class="form-group animate-fade-in-up">
                                    <label for="inscri_par" class="form-label label-with-icon">
                                        Inscrit par (Optionnel)
                                        <i class="fas fa-signature input-icon-label"></i>
                                    </label>
                                    <div class="input-wrapper">
                                        <input type="text" name="inscri_par" id="inscri_par" class="form-input" value="{{ old('inscri_par', $inscription->inscri_par) }}" placeholder="Nom de l'administrateur qui a effectué l'inscription">
                                    </div>
                                    @error('inscri_par')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>
                                {{-- END of NEW CODE --}}

                                {{-- Champ Notes --}}
                                <div class="md:col-span-2 form-group animate-fade-in">
                                    <label for="notes" class="form-label label-with-icon">
                                        Notes (Optionnel)
                                        <i class="fas fa-sticky-note input-icon-label"></i>
                                    </label>
                                    <div class="input-wrapper">
                                        <textarea name="notes" id="notes" rows="3" class="form-textarea" placeholder="Ajouter des notes sur l'inscription...">{{ old('notes', $inscription->notes) }}</textarea>
                                    </div>
                                    @error('notes')
                                        <p class="error-message">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Section Documents --}}
                                <div class="md:col-span-2 border-t-2 border-red-200 pt-6 mt-6 section-card animate-slide-up">
                                    <h4 class="text-xl font-bold text-red-700 mb-4 flex items-center">
                                        <i class="fas fa-file-alt mr-2 text-red-500 animate-pulse-soft"></i> Gestion des Documents
                                    </h4>
                                    <label class="form-label">Documents d'Inscription Actuels</label>
                                    @if(!empty($inscription->documents) && is_array($inscription->documents))
                                        <ul class="list-disc list-inside text-sm text-gray-700 space-y-1 mb-4">
                                            @foreach($inscription->documents as $docPath)
                                                <li class="flex items-center justify-between group">
                                                    <a href="{{ Storage::url($docPath) }}" target="_blank" class="text-red-600 hover:underline flex items-center">
                                                        <i class="fas fa-file-alt mr-2 text-gray-500"></i> {{ basename($docPath) }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    @else
                                        <p class="text-sm text-gray-500 mb-4">Aucun document actuel n'est enregistré pour cette inscription.</p>
                                    @endif

                                    <div class="form-group animate-slide-down">
                                        <label for="documents" class="form-label label-with-icon mt-4">
                                            Ajouter de Nouveaux Documents
                                            <i class="fas fa-cloud-upload-alt input-icon-label"></i>
                                        </label>
                                        <div class="file-upload-wrapper">
                                            <input type="file" name="documents[]" id="documents" multiple class="file-input" accept="image/*,application/pdf">
                                            <div class="file-upload-overlay">
                                                <i class="fas fa-cloud-upload-alt text-4xl text-red-400 mb-2"></i>
                                                <p class="text-sm text-gray-600">Glissez vos fichiers ici ou cliquez pour sélectionner</p>
                                            </div>
                                            <p class="mt-1 text-xs text-gray-500">Formats acceptés: PDF, JPG, JPEG, PNG (Max 2MB par fichier)</p>
                                        </div>
                                        @error('documents')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                        @error('documents.*')
                                            <p class="error-message">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                            </div>
                        </div>

                        <div class="mt-8 flex items-center justify-end gap-x-6 animate-fade-in-up">
                            <a href="{{ route('inscriptions.show', $inscription) }}" class="btn-secondary group">
                                <i class="fas fa-times-circle mr-2"></i> Annuler
                            </a>
                            <button type="submit" id="submit-btn" class="btn-primary group">
                                <i class="fas fa-save mr-2"></i> Enregistrer les Modifications
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
            /* CSS is unchanged */
        </style>
        @endpush

        @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const formationSelect = document.getElementById('formation_id'); // This is disabled, but still useful for data.
                const chosenInstallmentsSelect = document.getElementById('chosen_installments');
                const totalAmountInput = document.getElementById('total_amount'); // Le champ total_amount existe déjà
                const paidAmountInput = document.getElementById('paid_amount');
                const inscriptionForm = document.getElementById('inscription-form');
                const submitBtn = document.getElementById('submit-btn');

                // Add input focus animations
                const inputs = document.querySelectorAll('.form-select, .form-input, .form-textarea');
                inputs.forEach(input => {
                    input.addEventListener('focus', function() {
                        if (!this.disabled) {
                            this.closest('.input-wrapper').style.transform = 'scale(1.02)';
                        }
                    });

                    input.addEventListener('blur', function() {
                        if (!this.disabled) {
                            this.closest('.input-wrapper').style.transform = 'scale(1)';
                        }
                    });
                });


                function updateChosenInstallmentsOptions() {
                    const selectedFormationId = formationSelect.value;
                    const selectedFormationOption = formationSelect.options[formationSelect.selectedIndex];
                    const availableOptionsJson = selectedFormationOption.dataset.availableOptions;
                    let availableOptions = [];

                    try {
                        availableOptions = JSON.parse(availableOptionsJson || '[]');
                        if (!Array.isArray(availableOptions)) {
                            availableOptions = [];
                        }
                    } catch (e) {
                        console.error("Error parsing available payment options:", e);
                        availableOptions = [];
                    }

                    chosenInstallmentsSelect.innerHTML = ''; // Clear existing options
                    const currentChosen = "{{ old('chosen_installments', $inscription->chosen_installments) }}";

                    if (availableOptions.length > 0) {
                        availableOptions.forEach(option => {
                            const optionText = (option === 1) ?
                                'Paiement Complet (1 Versement)' :
                                `${option} Versements`;
                            const newOption = document.createElement('option');
                            newOption.value = option;
                            newOption.textContent = optionText;
                            if (option == currentChosen) { // Compare number value
                                newOption.selected = true;
                            }
                            chosenInstallmentsSelect.appendChild(newOption);
                        });
                    } else {
                        const defaultOption = document.createElement('option');
                        defaultOption.value = "1";
                        defaultOption.textContent = "Paiement Complet (1 Versement)";
                        chosenInstallmentsSelect.appendChild(defaultOption);
                        if (1 == currentChosen) {
                            defaultOption.selected = true;
                        }
                    }
                }

                if (formationSelect.value) {
                    updateChosenInstallmentsOptions();
                }

                inscriptionForm.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    const loadingSpinner = submitBtn.querySelector('.loading-spinner');
                    if (loadingSpinner) {
                        loadingSpinner.classList.remove('hidden');
                    }
                    submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
                });

                setTimeout(function() {
                    const alerts = document.querySelectorAll('.animated-alert');
                    alerts.forEach(function(alert) {
                        alert.classList.add('fade-out');
                        setTimeout(() => alert.remove(), 500);
                    });
                }, 5000);

                const fileInputs = document.querySelectorAll('.file-input');
                fileInputs.forEach(fileInput => {
                    const fileUploadWrapper = fileInput.closest('.file-upload-wrapper');

                    if (fileUploadWrapper) {
                        fileUploadWrapper.addEventListener('dragover', function(e) {
                            e.preventDefault();
                            this.style.transform = 'scale(1.02)';
                            this.style.borderColor = 'var(--primary-red)';
                        });

                        fileUploadWrapper.addEventListener('dragleave', function(e) {
                            e.preventDefault();
                            this.style.transform = 'scale(1)';
                            this.style.borderColor = 'var(--border-light)';
                        });

                        fileInput.addEventListener('change', function() {
                            const overlay = fileUploadWrapper.querySelector('.file-upload-overlay');
                            if (this.files && this.files.length > 0) {
                                let fileNames = Array.from(this.files).map(file => file.name).join(', ');
                                if (fileNames.length > 50) {
                                    fileNames = fileNames.substring(0, 47) + '...';
                                }
                                overlay.innerHTML = `
                                    <i class="fas fa-check-circle text-4xl text-green-500 mb-2"></i>
                                    <p class="text-sm text-green-600">Fichier(s) sélectionné(s): ${fileNames}</p>
                                `;
                            } else {
                                overlay.innerHTML = `
                                    <i class="fas fa-cloud-upload-alt text-4xl text-red-400 mb-2"></i>
                                    <p class="text-sm text-gray-600">Glissez votre fichier ici ou cliquez pour sélectionner</p>
                                `;
                            }
                        });
                    }
                });
            });
        </script>
        @endpush
@endsection