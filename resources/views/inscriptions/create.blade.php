@extends('layouts.app')

@section('title', 'Créer une Inscription Manuelle')

@section('content')

    <div >
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="glass-card overflow-hidden shadow-2xl sm:rounded-lg p-8 animated-form relative">
                <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-pink-50 opacity-50 rounded-lg -z-10 pattern-dots"></div>

                <h3 class="text-3xl font-extrabold text-gray-800 mb-8 text-center animate-text-pop">
                    <span class="text-gradient">Créer une Nouvelle Inscription</span>
                </h3>
                <p class="text-center text-gray-600 mb-8 animate-fade-in">
                    Remplissez les détails pour enregistrer manuellement une inscription.
                </p>

                <form action="{{ route('inscriptions.store') }}" method="POST" class="space-y-8" id="inscription-form" enctype="multipart/form-data">
                    @csrf

                    <div class="section-card border-2 border-red-200 rounded-lg p-6 bg-white transition-all duration-300 hover:shadow-lg animate-slide-up">
                        <h4 class="text-xl font-bold text-red-700 mb-5 flex items-center">
                            <i class="fas fa-user-plus mr-3 text-red-500 animate-pulse-soft"></i>
                            <span class="animate-type-writer">Détails de l'Inscription</span>
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                            <div class="form-group animate-slide-right">
                                <label for="user_id" class="form-label label-with-icon">
                                    Sélectionner un Étudiant <span class="text-red-500">*</span>
                                    <i class="fas fa-user input-icon-label"></i>
                                </label>
                                <div class="input-wrapper">
                                    <select name="user_id" id="user_id" class="form-select" required>
                                        <option value="">Choisir un étudiant</option>
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }} ({{ $user->email }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('user_id')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group animate-slide-left">
                                <label for="formation_id" class="form-label label-with-icon">
                                    Sélectionner une Formation <span class="text-red-500">*</span>
                                    <i class="fas fa-book-reader input-icon-label"></i>
                                </label>
                                <div class="input-wrapper">
                                    <select name="formation_id" id="formation_id" class="form-select" required>
                                        <option value="">Choisir une formation</option>
                                        @foreach($formations as $formation)
                                            <option value="{{ $formation->id }}"
                                                    data-price="{{ $formation->price }}"
                                                    data-category="{{ $formation->category->name ?? '' }}"
                                                    data-available-options="{{ json_encode($formation->available_payment_options ?? [1]) }}"
                                                    {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                                                {{ $formation->title }} ({{ number_format($formation->price, 2) }} DH)
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('formation_id')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group animate-fade-in-up">
                                <label for="status" class="form-label label-with-icon">
                                    Statut de l'Inscription <span class="text-red-500">*</span>
                                    <i class="fas fa-list-alt input-icon-label"></i>
                                </label>
                                <div class="input-wrapper">
                                    <select name="status" id="status" class="form-select" required>
                                        <option value="pending" {{ old('status') == 'pending' ? 'selected' : '' }}>En attente</option>
                                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Terminée</option>
                                        <option value="cancelled" {{ old('status') == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                                    </select>
                                </div>
                                @error('status')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="form-group animate-fade-in-up">
                                <label for="selected_payment_option" class="form-label label-with-icon">
                                    Modalité de Paiement <span class="text-red-500">*</span>
                                    <i class="fas fa-credit-card input-icon-label"></i>
                                </label>
                                <div class="input-wrapper">
                                    <select name="selected_payment_option" id="selected_payment_option" class="form-select" required>
                                        <option value="">Sélectionner après avoir choisi la formation</option>
                                    </select>
                                </div>
                                @error('selected_payment_option')
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
                            
                            <div class="form-group animate-bounce-in">
                                <label for="initial_paid_amount" class="form-label label-with-icon">
                                    Montant Initial Payé <span class="text-red-500">*</span>
                                    <i class="fas fa-dollar-sign input-icon-label"></i>
                                </label>
                                <div class="input-wrapper">
                                    <input type="number" step="0.01" min="0" name="paid_amount" id="initial_paid_amount" class="form-input" value="{{ old('paid_amount', 0) }}" required>
                                </div>
                                @error('paid_amount')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 hidden animate-slide-down" id="initial_receipt_upload_section">
                                <label for="initial_receipt_file" class="form-label label-with-icon">
                                    Justificatif de Paiement Initial (Reçu/Screenshot) <span class="text-red-500">*</span>
                                    <i class="fas fa-receipt input-icon-label"></i>
                                </label>
                                <div class="file-upload-wrapper">
                                    <input type="file" name="initial_receipt_file" id="initial_receipt_file" class="file-input" accept="image/*,application/pdf">
                                    <div class="file-upload-overlay">
                                        <i class="fas fa-cloud-upload-alt text-4xl text-red-400 mb-2"></i>
                                        <p class="text-sm text-gray-600">Glissez votre fichier ici ou cliquez pour sélectionner</p>
                                    </div>
                                    <p class="mt-1 text-xs text-gray-500">Formats acceptés: PDF, JPG, JPEG, PNG (Max 2MB)</p>
                                </div>
                                @error('initial_receipt_file')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2 form-group animate-fade-in">
                                <label for="notes" class="form-label label-with-icon">
                                    Notes (Optionnel)
                                    <i class="fas fa-sticky-note input-icon-label"></i>
                                </label>
                                <div class="input-wrapper">
                                    <textarea name="notes" id="notes" rows="3" class="form-textarea" placeholder="Ajouter des notes sur l'inscription...">{{ old('notes') }}</textarea>
                                </div>
                                @error('notes')
                                    <p class="error-message">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    <div class="mt-8 flex items-center justify-end gap-x-6 animate-fade-in-up">
                        <a href="{{ route('inscriptions.index') }}" class="btn-secondary group">
                            <i class="fas fa-times-circle mr-2"></i> Annuler
                        </a>
                        <button type="submit" id="submit-btn" class="btn-primary group">
                            <i class="fas fa-save mr-2"></i> Enregistrer Inscription
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
            const formationSelect = document.getElementById('formation_id');
            const paymentOptionSelect = document.getElementById('selected_payment_option');
            const initialPaidAmountInput = document.getElementById('initial_paid_amount');
            const initialReceiptUploadSection = document.getElementById('initial_receipt_upload_section');
            const initialReceiptFileInput = document.getElementById('initial_receipt_file');
            const inscriptionForm = document.getElementById('inscription-form');
            const submitBtn = document.getElementById('submit-btn');
            const paymentDetailsSummary = document.getElementById('payment-details-summary');
            const firstPaymentDetails = document.getElementById('first_payment_details');
            const remainingPaymentsDetails = document.getElementById('remaining_payments_details');

            // Add input focus animations (unchanged)
            const inputs = document.querySelectorAll('.form-select, .form-input, .form-textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.parentElement.style.transform = 'scale(1.02)';
                });

                input.addEventListener('blur', function() {
                    this.parentElement.style.transform = 'scale(1)';
                });
            });

            function updatePaymentOptionsAndAmount() {
                const selectedFormationOption = formationSelect.options[formationSelect.selectedIndex];
                const formationPrice = parseFloat(selectedFormationOption.dataset.price || 0);
                const formationCategory = selectedFormationOption.dataset.category;
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

                paymentOptionSelect.innerHTML = '<option value="">Sélectionner une modalité</option>';

                if (availableOptions.length > 0) {
                    availableOptions.forEach(option => {
                        const optionText = (option === 1) ?
                            'Paiement Complet (1 Versement)' :
                            `${option} Versements`;
                        const newOption = document.createElement('option');
                        newOption.value = option;
                        newOption.textContent = optionText;
                        paymentOptionSelect.appendChild(newOption);
                    });

                    let defaultSelectedOption = availableOptions[0];
                    if (formationPrice >= 1000 && availableOptions.includes(2)) {
                        defaultSelectedOption = 2;
                    }
                    paymentOptionSelect.value = defaultSelectedOption;
                } else {
                    const defaultOption = document.createElement('option');
                    defaultOption.value = "1";
                    defaultOption.textContent = "Paiement Complet (1 Versement) - Par défaut";
                    paymentOptionSelect.appendChild(defaultOption);
                    paymentOptionSelect.value = "1";
                }

                updateInitialPaymentAmountAndReceiptVisibility();
            }

            // La fonction complète avec le nouveau calcul

            function updateInitialPaymentAmountAndReceiptVisibility() {
                const selectedFormationOption = formationSelect.options[formationSelect.selectedIndex];
                const formationPrice = parseFloat(selectedFormationOption.dataset.price || 0);
                const formationCategory = selectedFormationOption.dataset.category;
                const selectedPaymentOption = parseInt(paymentOptionSelect.value);

                let defaultAmount = 0;
                let showReceiptField = false;
                let amountToDivide = formationPrice;

                // Masquer le résumé du paiement initialement
                paymentDetailsSummary.style.display = 'none';

                const isProfessional = (formationCategory === 'Master Professionnelle' || formationCategory === 'Licence Professionnelle');
                
                // If it's a professional formation and 10 installments are selected
                if (isProfessional && selectedPaymentOption === 10) {
                    const initialFee = 1600;
                    amountToDivide = formationPrice - initialFee; // 19600 - 1600 = 18000
                    defaultAmount = initialFee;
                    showReceiptField = true;
                    
                    // NOUVEAU CALCUL ICI : on divise par 10 versements au lieu de 9
                    const monthlyPayment = (amountToDivide / selectedPaymentOption).toFixed(2); // 18000 / 10 = 1800
                    const remainingInstallments = selectedPaymentOption;

                    paymentDetailsSummary.style.display = 'block';
                    firstPaymentDetails.innerHTML = `<strong>Premier Versement:</strong> <span class="text-red-600">${initialFee.toFixed(2)} DH</span>`;
                    remainingPaymentsDetails.textContent = `Le montant restant (${amountToDivide.toFixed(2)} DH) sera divisé en ${remainingInstallments} versements, chaque versement d'un montant de ${monthlyPayment} DH par mois.`;

                } else if (selectedPaymentOption > 1) {
                    // Standard calculation for other payment options
                    defaultAmount = (amountToDivide / selectedPaymentOption).toFixed(2);
                    showReceiptField = true;

                    paymentDetailsSummary.style.display = 'block';
                    firstPaymentDetails.innerHTML = `<strong>Montant de chaque versement:</strong> <span class="text-red-600">${defaultAmount} DH</span>`;
                    remainingPaymentsDetails.textContent = `Le montant total à diviser: ${amountToDivide.toFixed(2)} DH.`;

                } else if (selectedPaymentOption === 1) {
                    // Full payment calculation
                    defaultAmount = formationPrice;
                    showReceiptField = true;
                    
                    paymentDetailsSummary.style.display = 'block';
                    firstPaymentDetails.textContent = `Paiement complet: ${formationPrice.toFixed(2)} DH`;
                    remainingPaymentsDetails.textContent = '';
                }

                initialPaidAmountInput.value = defaultAmount;

                if (showReceiptField) {
                    initialReceiptUploadSection.classList.remove('hidden');
                    initialReceiptFileInput.required = true;
                } else {
                    initialReceiptUploadSection.classList.add('hidden');
                    initialReceiptFileInput.required = false;
                    initialReceiptFileInput.value = '';
                }
            }

            if (formationSelect.value) {
                updatePaymentOptionsAndAmount();
            }

            formationSelect.addEventListener('change', updatePaymentOptionsAndAmount);
            paymentOptionSelect.addEventListener('change', updateInitialPaymentAmountAndReceiptVisibility);

            inscriptionForm.addEventListener('submit', function() {
                submitBtn.disabled = true;
                const loadingSpinner = submitBtn.querySelector('.loading-spinner');
                if (loadingSpinner) {
                    loadingSpinner.classList.remove('hidden');
                }
                submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
            });

            // File upload animation (unchanged)
            const fileInput = document.getElementById('initial_receipt_file');
            const fileUploadWrapper = document.querySelector('.file-upload-wrapper');
            if (fileInput && fileUploadWrapper) {
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
                    if (this.files && this.files[0]) {
                        const fileName = this.files[0].name;
                        const overlay = document.querySelector('.file-upload-overlay');
                        overlay.innerHTML = `
                            <i class="fas fa-check-circle text-4xl text-green-500 mb-2"></i>
                            <p class="text-sm text-green-600">Fichier sélectionné: ${fileName}</p>
                        `;
                    }
                });
            }
        });
    </script>
    @endpush
@endsection