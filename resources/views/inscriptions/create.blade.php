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
                                            <option value="{{ $formation->id }}" data-price="{{ $formation->price }}" data-available-options="{{ json_encode($formation->available_payment_options ?? [1]) }}" {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
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
        /* CSS Variables with your specified colors */
        :root {
            --primary-red: #D32F2F;
            --dark-pink: #C2185B;
            --light-red: #ef4444;
            --gradient-main: linear-gradient(135deg, var(--primary-red) 0%, var(--dark-pink) 100%);
            --gradient-button: linear-gradient(45deg, var(--primary-red), var(--light-red), var(--dark-pink));
            --text-dark: #2d3748;
            --text-medium: #4a5568;
            --text-light: #718096;
            --bg-light: #f7fafc;
            --border-light: #e2e8f0;
            --glass-bg: rgba(255, 255, 255, 0.85);
            --glass-border: rgba(255, 255, 255, 0.3);
            --shadow-soft: 0 10px 30px rgba(0, 0, 0, 0.1);
            --shadow-hover: 0 20px 60px rgba(211, 47, 47, 0.3);
        }

        /* Main Background */
        .main-bg {
            background: linear-gradient(135deg, #fce4ec, #f8bbd0, #e1bee7);
            position: relative;
            overflow: hidden;
        }

        .main-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background:
                radial-gradient(circle at 20% 80%, rgba(211, 47, 47, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 70% 30%, rgba(194, 24, 91, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 50% 50%, rgba(239, 68, 68, 0.1) 0%, transparent 50%);
            animation: float 15s ease-in-out infinite;
        }

        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            33% { transform: translateY(-10px) rotate(1deg); }
            66% { transform: translateY(5px) rotate(-1deg); }
        }

        /* Glass Card */
        .glass-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 2rem;
            box-shadow: var(--shadow-soft);
            transition: all 0.6s cubic-bezier(0.23, 1, 0.320, 1);
            position: relative;
            z-index: 1;
        }

        .glass-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--shadow-hover);
        }

        .pattern-dots {
            background-image: radial-gradient(rgba(211, 47, 47, 0.15) 1px, transparent 1px);
            background-size: 15px 15px;
            animation: pattern-move 20s linear infinite;
        }

        @keyframes pattern-move {
            0% { background-position: 0 0; }
            100% { background-position: 15px 15px; }
        }

        /* Text Gradient */
        .text-gradient {
            background: var(--gradient-main);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            animation: gradient-shift 3s ease-in-out infinite;
        }

        @keyframes gradient-shift {
            0%, 100% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
        }

        /* Animations */
        .animate-text-pop {
            animation: text-pop 1.2s cubic-bezier(0.68, -0.55, 0.265, 1.55);
        }

        @keyframes text-pop {
            0% { transform: scale(0.3) rotateX(90deg); opacity: 0; }
            50% { transform: scale(1.05) rotateX(45deg); }
            100% { transform: scale(1) rotateX(0deg); opacity: 1; }
        }

        .animate-fade-in {
            animation: fade-in 1s ease-out 0.3s both;
        }

        @keyframes fade-in {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-slide-up {
            animation: slide-up 0.8s cubic-bezier(0.25, 0.46, 0.45, 0.94) 0.2s both;
        }

        @keyframes slide-up {
            from { opacity: 0; transform: translateY(50px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-slide-right {
            animation: slide-right 0.6s ease-out 0.4s both;
        }

        @keyframes slide-right {
            from { opacity: 0; transform: translateX(-30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .animate-slide-left {
            animation: slide-left 0.6s ease-out 0.5s both;
        }

        @keyframes slide-left {
            from { opacity: 0; transform: translateX(30px); }
            to { opacity: 1; transform: translateX(0); }
        }

        .animate-fade-in-up {
            animation: fade-in-up 0.6s ease-out 0.6s both;
        }

        @keyframes fade-in-up {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-bounce-in {
            animation: bounce-in 0.8s cubic-bezier(0.68, -0.55, 0.265, 1.55) 0.7s both;
        }

        @keyframes bounce-in {
            0% { opacity: 0; transform: scale(0.3); }
            50% { transform: scale(1.05); }
            70% { transform: scale(0.9); }
            100% { opacity: 1; transform: scale(1); }
        }

        .animate-slide-down {
            animation: slide-down 0.5s ease-out both;
        }

        @keyframes slide-down {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .animate-pulse-soft {
            animation: pulse-soft 2s ease-in-out infinite;
        }

        @keyframes pulse-soft {
            0%, 100% { transform: scale(1); opacity: 1; }
            50% { transform: scale(1.1); opacity: 0.8; }
        }

        .animate-type-writer {
            overflow: hidden;
            border-right: 2px solid var(--primary-red);
            white-space: nowrap;
            animation: typing 2s steps(30, end), blink-caret 0.75s step-end infinite;
        }

        @keyframes typing {
            from { width: 0; }
            to { width: 100%; }
        }

        @keyframes blink-caret {
            from, to { border-color: transparent; }
            50% { border-color: var(--primary-red); }
        }

        /* Form Elements */
        .form-group {
            position: relative;
        }

        .form-label {
            display: flex; /* Make it a flex container */
            align-items: center; /* Align items vertically */
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 0.5rem;
            transition: color 0.3s ease;
        }

        /* New style for the icon next to the label */
        .input-icon-label {
            margin-left: 0.5rem; /* Space between text and icon */
            color: var(--primary-red); /* Color the icon */
            font-size: 1rem; /* Adjust icon size if needed */
        }

        .input-wrapper {
            position: relative;
        }

        .form-select, .form-input, .form-textarea {
            width: 100%;
            /* Increase padding to make inputs larger */
            padding: 1.25rem 1.5rem; /* Increased padding */
            border: 2px solid var(--border-light);
            border-radius: 1rem;
            font-size: 1rem;
            color: var(--text-dark);
            background: white;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        }

        .form-select:focus, .form-input:focus, .form-textarea:focus {
            outline: none;
            border-color: var(--primary-red);
            box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.1), 0 4px 20px rgba(211, 47, 47, 0.15);
            transform: translateY(-2px);
        }

        /* Remove the old .input-icon as it's no longer needed for inputs */
        /* .input-icon {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
            transition: all 0.3s ease;
        }

        .form-select:focus + .input-icon,
        .form-input:focus + .input-icon,
        .form-textarea:focus + .input-icon {
            color: var(--primary-red);
            transform: translateY(-50%) scale(1.1);
        } */

        /* File Upload */
        .file-upload-wrapper {
            position: relative;
            border: 2px dashed var(--border-light);
            border-radius: 1rem;
            background: var(--bg-light);
            transition: all 0.3s ease;
            overflow: hidden;
        }

        .file-upload-wrapper:hover {
            border-color: var(--primary-red);
            background: white;
        }

        .file-input {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            opacity: 0;
            cursor: pointer;
        }

        .file-upload-overlay {
            padding: 2rem;
            text-align: center;
            transition: all 0.3s ease;
        }

        .file-upload-wrapper:hover .file-upload-overlay {
            transform: scale(1.02);
        }

        /* Buttons */
        .btn-primary {
            background: var(--gradient-button);
            background-size: 200% 200%;
            color: white;
            padding: 1rem 2rem;
            border-radius: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            border: none;
            cursor: pointer;
            position: relative;
            overflow: hidden;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 6px 20px rgba(211, 47, 47, 0.3);
            display: inline-flex;
            align-items: center;
            animation: gradient-animation 3s ease infinite;
        }

        @keyframes gradient-animation {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .btn-primary:hover {
            transform: translateY(-3px) scale(1.02);
            box-shadow: 0 10px 30px rgba(211, 47, 47, 0.4);
        }

        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.6s;
        }

        .btn-primary:hover::before {
            left: 100%;
        }

        .btn-secondary {
            background: white;
            color: var(--text-medium);
            border: 2px solid var(--border-light);
            padding: 1rem 2rem;
            border-radius: 1rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            transition: all 0.4s cubic-bezier(0.25, 0.8, 0.25, 1);
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .btn-secondary:hover {
            background: var(--bg-light);
            border-color: var(--primary-red);
            color: var(--primary-red);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(211, 47, 47, 0.15);
        }

        /* Error Messages */
        .error-message {
            margin-top: 0.5rem;
            font-size: 0.875rem;
            color: var(--primary-red);
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        /* Loading Spinner */
        .loading-spinner {
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Responsive adjustments */
        @media (max-width: 768px) {
            .glass-card {
                margin: 1rem;
                padding: 1.5rem;
            }

            .form-select, .form-input, .form-textarea {
                padding: 1rem 1.25rem; /* Adjust padding for smaller screens if needed */
            }

            .input-icon-label {
                margin-left: 0.25rem; /* Adjust spacing for smaller screens */
                font-size: 0.9rem;
            }
        }
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

            // Add input focus animations
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

            function updateInitialPaymentAmountAndReceiptVisibility() {
                const selectedFormationOption = formationSelect.options[formationSelect.selectedIndex];
                const formationPrice = parseFloat(selectedFormationOption.dataset.price || 0);
                const selectedPaymentOption = parseInt(paymentOptionSelect.value);

                let defaultAmount = 0;
                let showReceiptField = false;

                if (selectedPaymentOption === 1) {
                    defaultAmount = formationPrice;
                    showReceiptField = true;
                } else if (selectedPaymentOption > 1) {
                    defaultAmount = parseFloat((formationPrice / selectedPaymentOption).toFixed(2));
                    showReceiptField = true;
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

            // File upload animation
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