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

                            <div class="form-group animate-fade-in-up md:col-span-2">
                                <label for="total_amount_override" class="form-label label-with-icon text-green-700">
                                    Montant Total de l'Inscription (après réduction)
                                    <i class="fas fa-percent input-icon-label text-green-500"></i>
                                </label>
                                <div class="input-wrapper">
                                    <input type="number" step="0.01" min="0" name="total_amount_override" id="total_amount_override" class="form-input text-green-700 font-bold" value="" placeholder="Prix total normal de la formation">
                                </div>
                                @error('total_amount_override')
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
            const totalAmountOverrideInput = document.getElementById('total_amount_override');
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
                let formationPrice = parseFloat(selectedFormationOption.dataset.price || 0);
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
                
                // Mettre à jour le prix total affiché dans le champ de réduction
                if (totalAmountOverrideInput) {
                    totalAmountOverrideInput.value = formationPrice.toFixed(2);
                }

                updateInitialPaymentAmountAndReceiptVisibility();
            }

            function updateInitialPaymentAmountAndReceiptVisibility() {
                const selectedFormationOption = formationSelect.options[formationSelect.selectedIndex];
                let formationPrice = parseFloat(selectedFormationOption.dataset.price || 0);
                const formationCategory = selectedFormationOption.dataset.category; // Assurez-vous que cette ligne est présente
                const selectedPaymentOption = parseInt(paymentOptionSelect.value);
                const initialPaidAmount = parseFloat(initialPaidAmountInput.value) || 0;
                
                // Utiliser le prix de la formation modifié si l'administrateur a saisi une valeur
                const finalTotalAmount = parseFloat(totalAmountOverrideInput.value) || formationPrice;

                let showReceiptField = true;

                // --- NEW LOGIC: Calculate remaining amount for installments ---
                let fixedFee = 0;
                if (formationCategory === 'Master Professionnelle' || formationCategory === 'Licence Professionnelle') {
                    fixedFee = 1600;
                }
                
                // Total amount to be divided (e.g., 18000 DH for professional courses)
                const totalInstallmentAmount = finalTotalAmount - fixedFee;

                // Amount paid that exceeds the fixed fee
                const extraPaidAmount = Math.max(0, initialPaidAmount - fixedFee);
                
                // Remaining balance to be paid in installments (18000 - extraPaidAmount)
                const remainingAmountToPayForInstallments = totalInstallmentAmount - extraPaidAmount;
                
                // Calculate the amount of each installment from the remaining balance
                const amountPerInstallment = (remainingAmountToPayForInstallments > 0 && selectedPaymentOption > 0) 
                                            ? (remainingAmountToPayForInstallments / selectedPaymentOption).toFixed(2)
                                            : '0.00';
                
                // --- END OF NEW LOGIC ---

                // Update the summary section
                paymentDetailsSummary.style.display = 'block';
                
                let firstPaymentDetailsText = `<strong>Montant Initial Payé:</strong> <span class="text-red-600">${initialPaidAmount.toFixed(2)} DH</span>`;
                if (fixedFee > 0) {
                    firstPaymentDetailsText += ` (inclut les frais fixes de ${fixedFee.toFixed(2)} DH)`;
                }
                firstPaymentDetails.innerHTML = firstPaymentDetailsText;
                
                if (remainingAmountToPayForInstallments > 0) {
                    remainingPaymentsDetails.textContent = `Le montant restant (${remainingAmountToPayForInstallments.toFixed(2)} DH) sera divisé en ${selectedPaymentOption} versements de ${amountPerInstallment} DH chacun.`;
                } else {
                    remainingPaymentsDetails.textContent = `Le paiement complet de la formation a été effectué.`;
                }

                if (showReceiptField) {
                    initialReceiptUploadSection.classList.remove('hidden');
                    initialReceiptFileInput.required = true;
                } else {
                    initialReceiptUploadSection.classList.add('hidden');
                    initialReceiptFileInput.required = false;
                    initialReceiptFileInput.value = '';
                }
            }

            // Add an event listener to the initial paid amount input
            initialPaidAmountInput.addEventListener('input', updateInitialPaymentAmountAndReceiptVisibility);
            
            // Add an event listener to the total amount override input
            totalAmountOverrideInput.addEventListener('input', updateInitialPaymentAmountAndReceiptVisibility);

            // Also call the function on change of formation and payment option
            formationSelect.addEventListener('change', updatePaymentOptionsAndAmount);
            paymentOptionSelect.addEventListener('change', updateInitialPaymentAmountAndReceiptVisibility);

            if (formationSelect.value) {
                updatePaymentOptionsAndAmount();
            }

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