@extends('layouts.app')

@section('title', 'Effectuer un Nouveau Paiement')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    /* Consistent Color Variables from Reschedule Page */
    :root {
        --primary-red: #D32F2F;
        --secondary-pink: #C2185B;
        --accent-red: #ef4444;
        --gradient-primary: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
        --gradient-secondary: linear-gradient(135deg, #ef4444 0%, #D32F2F 100%);
        --gradient-light: linear-gradient(135deg, rgba(211,47,47,0.1) 0%, rgba(194,24,91,0.1) 100%);
        --shadow-red: rgba(211, 47, 47, 0.3);
        --shadow-pink: rgba(194, 24, 91, 0.3);

        /* Specific colors for Payment form - ADJUSTED TO YOUR RED/PINK THEME */
        --payment-primary: #D32F2F; /* Primary Red */
        --payment-secondary: #C2185B; /* Secondary Pink */
        --payment-accent: #ef4444; /* Accent Red */
        --payment-gradient-bg: linear-gradient(135deg, rgba(240, 98, 146, 0.1) 0%, rgba(229, 115, 115, 0.1) 100%); /* Light pink/red gradient for background */
        --payment-card-border: #FFCDD2; /* Lighter red for borders */
        --payment-info-bg: #F8E1E1; /* Very light red for info sections */
        --payment-options-bg: #FCE4EC; /* Very light pink for options section */
    }

    body {
        /* Use a consistent background with the new payment gradient for a distinct but harmonious look */
        background: var(--payment-gradient-bg);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #333; /* Default text color */
    }

    .card-modern {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        background: rgba(255,255,255,0.95);
    }

    /* Gradient header for the payment form */
    .gradient-header {
        background: linear-gradient(135deg, var(--payment-primary) 0%, var(--payment-secondary) 100%); /* Red to Pink gradient */
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 25px 30px;
        position: relative;
        overflow: hidden;
    }

    .gradient-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='7' cy='7' r='7'/%3E%3Ccircle cx='53' cy='53' r='7'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .btn-modern {
        border-radius: 30px;
        padding: 12px 30px;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: inline-flex; /* To align icon and text */
        align-items: center;
        justify-content: center;
    }

    /* Primary action button (Confirmer le Paiement) */
    .btn-primary-modern {
        background: linear-gradient(135deg, var(--payment-primary) 0%, var(--payment-secondary) 100%); /* Use red-pink gradient */
        color: white;
        box-shadow: 0 8px 25px var(--shadow-red); /* Shadow based on primary red */
    }

    .btn-primary-modern:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 15px 35px var(--shadow-red);
        color: white;
    }

    .btn-primary-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary-modern:hover::before {
        left: 100%;
    }

    /* Secondary action button (Annuler) */
    .btn-secondary-modern {
        background-color: #6c757d; /* Standard Bootstrap secondary color for consistency */
        color: white;
        box-shadow: 0 5px 15px rgba(108, 117, 125, 0.2); /* Subtle shadow for secondary */
    }

    .btn-secondary-modern:hover {
        background-color: #5a6268; /* Darker on hover */
        color: white;
        transform: translateY(-2px);
    }

    /* Form labels and controls */
    .form-label-modern {
        font-weight: 700;
        color: #4a4a4a;
        margin-bottom: 8px;
        display: flex; /* Make labels flex containers */
        align-items: center; /* Vertically align items */
    }

    .form-label-modern i {
        margin-right: 8px; /* Space between icon and text */
        color: var(--payment-primary); /* Icon color */
    }

    .form-control-modern, .form-textarea-modern, .form-input-file-modern {
        border-radius: 15px;
        border: 2px solid var(--payment-card-border); /* Lighter red border for inputs */
        padding: 12px 18px;
        font-weight: 500;
        transition: all 0.3s ease;
        background-color: #ffffff;
        width: 100%; /* Ensure full width */
    }

    .form-select-modern {
        border-radius: 15px;
        border: 2px solid var(--payment-card-border); /* Lighter red border for selects */
        padding: 12px 18px;
        font-weight: 500;
        transition: all 0.3s ease;
        background-color: #ffffff;
        width: 100%; /* Ensure full width */
        color: #000; /* Set text color to black for select options */
    }

    .form-control-modern:focus, .form-select-modern:focus, .form-textarea-modern:focus, .form-input-file-modern:focus {
        border-color: var(--payment-primary); /* Focus color from payment theme */
        box-shadow: 0 0 20px rgba(211, 47, 47, 0.2); /* Focus shadow based on payment-primary */
        outline: none; /* Remove default outline */
    }

    .form-control-modern.bg-gray-100 {
        background-color: #f3f4f6; /* Tailwind gray-100 */
        cursor: not-allowed;
    }

    .form-text-modern {
        font-size: 0.85em;
        color: #777;
        margin-top: 5px;
    }

    /* Alert styles (adjusting existing ones to fit theme) */
    .alert-success-modern {
        background: linear-gradient(135deg, #e6ffe6 0%, #d4ffdb 100%); /* Light green gradient */
        border: 1px solid #4CAF50; /* Green border */
        border-radius: 15px;
        color: #1B5E20; /* Dark green text */
        padding: 15px 20px;
        animation: fadeIn 0.5s ease-out;
    }

    .alert-danger-modern {
        background: linear-gradient(135deg, #fde7e7 0%, #fcdede 100%); /* Lighter red gradient */
        border: 1px solid var(--payment-accent); /* Accent red border */
        border-radius: 15px;
        color: var(--payment-primary); /* Primary red text */
        padding: 15px 20px;
        animation: fadeIn 0.5s ease-out;
    }

    /* Specific styles for payment info and options sections */
    .section-card {
        border: 2px solid var(--payment-card-border); /* Consistent with input borders */
        border-radius: 15px;
        padding: 24px;
        background-color: #ffffff;
        transition: all 0.3s ease;
    }
    .section-card:hover {
        box-shadow: 0 8px 25px rgba(211, 47, 47, 0.1); /* Shadow based on payment-primary */
    }

    #inscription-info {
        background-color: var(--payment-info-bg); /* Very light red for info sections */
        border-radius: 10px;
        padding: 16px;
        border: 1px solid #EF9A9A; /* Slightly darker red border */
    }

    #payment-options-section {
        background-color: var(--payment-options-bg); /* Very light pink for options section */
        border-radius: 10px;
        padding: 16px;
        border: 1px solid #F48FB1; /* Slightly darker pink border */
    }

    /* Radio button styling */
    .form-radio-modern {
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
        display: inline-block;
        vertical-align: middle;
        height: 1.25rem; /* Slightly larger */
        width: 1.25rem;
        border-radius: 50%;
        border-width: 2px; /* Thicker border */
        border-color: #9CA3AF; /* Tailwind gray-400 */
        background-color: #fff;
        transition: background-color 0.2s ease-in-out, border-color 0.2s ease-in-out;
    }
    .form-radio-modern:checked {
        background-color: var(--payment-primary); /* Consistent with payment theme */
        border-color: var(--payment-primary);
        background-image: url("data:image/svg+xml,%3csvg viewBox='0 0 16 16' fill='white' xmlns='http://www.w3.org/2000/svg'%3e%3cpath d='M8 15A7 7 0 108 1a7 7 0 000 14zm0 1A8 8 0 118 0a8 8 0 010 16z'/%3e%3cpath d='M8 4a4 4 0 100 8 4 4 0 000-8z'/%3e%3c/svg%3e");
        background-size: 100% 100%;
        background-position: center;
        background-repeat: no-repeat;
    }
    .form-radio-modern:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.45); /* Focus shadow based on payment-primary */
    }

    /* Animations */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animated-form {
        animation: fadeIn 0.6s ease-out forwards;
    }
    .animated-alert {
        animation: fadeIn 0.5s ease-out forwards;
    }
    .fade-out {
        animation: fadeOut 0.5s forwards;
    }
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; transform: translateY(-10px); }
    }
    .animate-text-pop {
        animation: textPop 0.8s ease-out forwards;
    }
    @keyframes textPop {
        0% { transform: scale(0.8); opacity: 0; }
        70% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); }
    }
    .animate-icon-fade {
        animation: iconFade 1.5s infinite alternate;
    }
    @keyframes iconFade {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
    /* Spinner for loading state */
    .loading-spinner {
        display: inline-block;
    }
    .fa-spin {
        animation: fa-spin 1s infinite linear;
    }
    @keyframes fa-spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }
</style>
@endpush

@section('content')

    <div class="py-12 min-h-screen">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="card-modern p-8 animated-form">
                <div class="gradient-header mb-8">
                    <h3 class="text-3xl font-extrabold text-white text-center animate-text-pop">
                        <i class="fas fa-file-invoice-dollar mr-3"></i> Effectuer un Nouveau Paiement
                    </h3>
                    <p class="text-center text-white text-opacity-80 mt-2">
                        Sélectionnez votre inscription et enregistrez votre paiement.
                    </p>
                </div>

                {{-- Messages de succès/erreur --}}
                @if (session('success'))
                    <div class="alert alert-success-modern mb-4 animated-alert" role="alert">
                        <span class="block sm:inline">{{ session('success') }}</span>
                    </div>
                @endif
                @if (session('error'))
                    <div class="alert alert-danger-modern mb-4 animated-alert" role="alert">
                        <span class="block sm:inline">{{ session('error') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="alert alert-danger-modern mb-4 animated-alert" role="alert">
                        <ul class="mt-3 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('payments.store') }}" method="POST" class="space-y-8" id="paymentForm" enctype="multipart/form-data">
                    @csrf

                    <div class="section-card">
                        <h4 class="text-xl font-bold text-gray-700 mb-5 flex items-center">
                            <i class="fas fa-money-check-alt mr-3 text-red-500 animate-icon-fade"></i> Détails du Paiement
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">
                            
                            {{-- Champ Sélectionner une Inscription --}}
                            <div class="md:col-span-2">
                                <label for="inscription_id" class="form-label-modern block">
                                    <i class="fas fa-clipboard-list"></i> Sélectionner une Inscription <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select class="form-select-modern" id="inscription_id" name="inscription_id" required>
                                        <option value="">-- Choisir une Inscription --</option>
                                        @foreach ($inscriptions as $inscription)
                                            @php
                                                $remainingAmount = $inscription->total_amount - $inscription->paid_amount;
                                                $displayRemaining = number_format($remainingAmount, 2);
                                                $paymentType = $inscription->payment_type;
                                            @endphp
                                            <option value="{{ $inscription->id }}"
                                                data-total-amount="{{ $inscription->total_amount }}"
                                                data-paid-amount="{{ $inscription->paid_amount }}"
                                                data-amount-per-installment="{{ $inscription->amount_per_installment }}"
                                                data-chosen-installments="{{ $inscription->chosen_installments }}"
                                                data-remaining-amount="{{ $remainingAmount }}"
                                                {{ old('inscription_id') == $inscription->id ? 'selected' : '' }}>
                                                {{ $inscription->formation->title }} ({{ $inscription->user->name }}) - Reste: {{ $displayRemaining }} DH {{ $paymentType ? '(' . $paymentType . ')' : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('inscription_id')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Informations sur l'Inscription sélectionnée --}}
                            <div id="inscription-info" class="md:col-span-2 hidden">
                                <h5 class="text-lg font-bold text-gray-800 mb-2">Informations de l'Inscription</h5>
                                <p class="text-sm text-gray-700 mb-1">Formation: <span class="font-semibold text-gray-900" id="info-formation-title"></span></p>
                                <p class="text-sm text-gray-700 mb-1">Prix Total: <span class="font-semibold text-gray-900" id="info-total-amount"></span> DH</p>
                                <p class="text-sm text-gray-700 mb-1">Déjà Payé: <span class="font-semibold text-gray-900" id="info-paid-amount"></span> DH</p>
                                <p class="text-sm text-gray-700 mb-1">Reste à Payer: <span class="font-bold text-red-700" id="info-remaining-amount-display"></span> DH</p>
                                <p class="text-sm text-gray-700 mb-1">Modalité de paiement: <span class="font-semibold text-gray-900" id="info-payment-type"></span></p>
                            </div>

                            {{-- Options de Paiement --}}
                            <div id="payment-options-section" class="md:col-span-2 hidden">
                                <label for="payment_choice" class="form-label-modern block">
                                    <i class="fas fa-hand-holding-dollar"></i> Comment voulez-vous payer ? <span class="text-red-500">*</span>
                                </label>
                                <div class="space-y-2" id="payment-choices-radios">
                                    {{-- Radio buttons will be inserted here by JavaScript --}}
                                </div>
                                @error('payment_choice')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror

                                {{-- New: Dropdown for choosing number of installments --}}
                                <div id="custom-installments-selection" class="mt-4 hidden">
                                    <label for="num_installments_to_pay" class="form-label-modern block">
                                        <i class="fas fa-list-ol"></i> Nombre d'acomptes à payer <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select class="form-select-modern" id="num_installments_to_pay">
                                            <option value="">-- Choisir le nombre d'acomptes --</option>
                                            {{-- Options populated by JS --}}
                                        </select>
                                    </div>
                                </div>
                            </div>

                            {{-- Champ Montant du paiement --}}
                            <div id="amount-field" class="md:col-span-1 hidden">
                                <label for="amount_to_pay" class="form-label-modern block">
                                    <i class="fas fa-dollar-sign"></i> Montant à Payer <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" step="0.01" min="0.01" class="form-control-modern bg-gray-100 cursor-not-allowed" id="amount_to_pay" name="amount_to_pay" value="{{ old('amount_to_pay') }}" required readonly>
                                </div>
                                <small class="form-text-modern" id="payment-hint"></small>
                                @error('amount_to_pay')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Champ Date de paiement (Automatique) --}}
                            <div class="md:col-span-1">
                                <label for="paid_date" class="form-label-modern block">
                                    <i class="fas fa-calendar-alt"></i> Date du Paiement <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="date" class="form-control-modern bg-gray-100 cursor-not-allowed" id="paid_date" name="paid_date" value="{{ \Carbon\Carbon::now()->format('Y-m-d') }}" required readonly>
                                </div>
                            </div>

                            {{-- Champ Méthode de Paiement --}}
                            <div class="md:col-span-1">
                                <label for="payment_method" class="form-label-modern block">
                                    <i class="fas fa-wallet"></i> Méthode de Paiement <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select class="form-select-modern" id="payment_method" name="payment_method" required>
                                        <option value="">-- Choisir une méthode --</option>
                                        <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                                        <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Virement bancaire</option>
                                    </select>
                                </div>
                                @error('payment_method')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Champ Description du paiement --}}
                            <div class="md:col-span-2">
                                <label for="payment_description" class="form-label-modern block">
                                    <i class="fas fa-comment-alt"></i> Description du Paiement (Optionnel)
                                </label>
                                <div class="relative">
                                    <textarea name="payment_description" id="payment_description" rows="2" class="form-textarea-modern" placeholder="Ex: Paiement du 2ème versement, Acompte...">{{ old('payment_description') }}</textarea>
                                </div>
                                @error('payment_description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Champ Télécharger le Reçu --}}
                            <div class="md:col-span-2">
                                <label for="receipt_file" class="form-label-modern block">
                                    <i class="fas fa-receipt"></i> Télécharger le Reçu (Optionnel)
                                </label>
                                <div class="relative">
                                    <input type="file" name="receipt_file" id="receipt_file" class="form-input-file-modern" accept="image/*,application/pdf">
                                    <p class="mt-1 text-xs text-gray-500">Formats acceptés: PDF, JPG, JPEG, PNG (Max 5MB)</p>
                                </div>
                                @error('receipt_file')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                        </div>
                    </div>

                    {{-- Boutons d'action --}}
                    <div class="mt-8 flex items-center justify-end gap-x-6">
                        <a href="{{ route('payments.index') }}" class="btn-modern btn-secondary-modern group">
                            <i class="fas fa-times-circle mr-2 group-hover:scale-110 transition-transform duration-200"></i> Annuler
                        </a>
                        <button type="submit" id="submit-btn" class="btn-modern btn-primary-modern group">
                            <i class="fas fa-check-circle mr-2 group-hover:scale-110 transition-transform duration-200"></i> Confirmer le Paiement
                            <span class="loading-spinner ml-2 hidden">
                                <i class="fas fa-circle-notch fa-spin"></i>
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const inscriptionSelect = document.getElementById('inscription_id');
        const inscriptionInfoSection = document.getElementById('inscription-info');
        const paymentOptionsSection = document.getElementById('payment-options-section');
        const paymentChoicesRadios = document.getElementById('payment-choices-radios');
        const customInstallmentsSelection = document.getElementById('custom-installments-selection');
        const numInstallmentsToPaySelect = document.getElementById('num_installments_to_pay');
        const amountToPayInput = document.getElementById('amount_to_pay');
        const paymentHint = document.getElementById('payment-hint');
        const submitBtn = document.getElementById('submit-btn');
        const paymentForm = document.getElementById('paymentForm');

        let currentInscriptionData = {};
        const epsilon = 0.01; // Small value for floating point comparisons

        function updateInscriptionDetails() {
            const selectedOption = inscriptionSelect.options[inscriptionSelect.selectedIndex];
            
            if (selectedOption.value) {
                inscriptionInfoSection.classList.remove('hidden');
                paymentOptionsSection.classList.remove('hidden');
                
                currentInscriptionData = {
                    totalAmount: parseFloat(selectedOption.dataset.totalAmount),
                    paidAmount: parseFloat(selectedOption.dataset.paidAmount),
                    amountPerInstallment: parseFloat(selectedOption.dataset.amountPerInstallment),
                    chosenInstallments: parseInt(selectedOption.dataset.chosenInstallments),
                    remainingAmount: parseFloat(selectedOption.dataset.remainingAmount),
                    formationTitle: selectedOption.textContent.split('(')[0].trim()
                };

                // Populate inscription info display
                document.getElementById('info-formation-title').textContent = currentInscriptionData.formationTitle;
                document.getElementById('info-total-amount').textContent = currentInscriptionData.totalAmount.toFixed(2);
                document.getElementById('info-paid-amount').textContent = currentInscriptionData.paidAmount.toFixed(2);
                document.getElementById('info-remaining-amount-display').textContent = currentInscriptionData.remainingAmount.toFixed(2);
                
                const paymentTypeText = currentInscriptionData.chosenInstallments === 1 
                                                ? 'Paiement Complet' 
                                                : `En ${currentInscriptionData.chosenInstallments} Versements`;
                document.getElementById('info-payment-type').textContent = paymentTypeText;

                // Disable payment options if remaining amount is zero or negligible
                if (currentInscriptionData.remainingAmount <= epsilon) {
                    paymentOptionsSection.classList.add('hidden');
                    amountToPayInput.value = '0.00';
                    amountToPayInput.readOnly = true;
                    amountToPayInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                    paymentHint.textContent = 'Cette inscription est déjà entièrement payée.';
                    submitBtn.disabled = true; // Button disabled if fully paid
                    document.getElementById('amount-field').classList.remove('hidden'); // Ensure amount field is visible to show 0.00
                } else {
                    // Reset amount field and hint
                    amountToPayInput.value = '';
                    paymentHint.textContent = '';
                    customInstallmentsSelection.classList.add('hidden'); // Hide on new inscription select
                    amountToPayInput.readOnly = true;
                    amountToPayInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                    document.getElementById('amount-field').classList.remove('hidden'); // Ensure amount field is visible

                    // Generate payment options and let generatePaymentOptions/updateAmountField handle button state
                    generatePaymentOptions(); 
                }
                
            } else {
                // No inscription selected
                inscriptionInfoSection.classList.add('hidden');
                paymentOptionsSection.classList.add('hidden');
                paymentChoicesRadios.innerHTML = ''; // Clear radios
                customInstallmentsSelection.classList.add('hidden'); // Hide on no inscription
                numInstallmentsToPaySelect.innerHTML = ''; // Clear custom options
                amountToPayInput.value = '';
                amountToPayInput.readOnly = true;
                amountToPayInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                paymentHint.textContent = '';
                submitBtn.disabled = true; // Button disabled if no inscription selected
                document.getElementById('amount-field').classList.add('hidden'); // Hide amount field
            }
        }

        function generatePaymentOptions() {
            paymentChoicesRadios.innerHTML = '';
            const remaining = currentInscriptionData.remainingAmount;
            const installmentAmount = currentInscriptionData.amountPerInstallment;
            const currentPaidInstallments = Math.floor(currentInscriptionData.paidAmount / installmentAmount + epsilon);
            const totalInstallments = currentInscriptionData.chosenInstallments;

            // Option: Payer le reste total (Always show if remaining > 0)
            if (remaining > epsilon) { 
                addRadioButton('full_remaining', `Payer le reste total (${remaining.toFixed(2)} DH)`, remaining.toFixed(2));
            }

            // Option: Payer le prochain versement (if applicable and not full payment)
            if (currentInscriptionData.chosenInstallments > 1 && remaining > epsilon) {
                let nextInstallmentValue = installmentAmount;
                // If this is the last installment and remaining is less than a full installment,
                // set nextInstallmentValue to remaining.
                if ((currentPaidInstallments + 1 === totalInstallments) && remaining < installmentAmount + epsilon) {
                    nextInstallmentValue = remaining;
                }
                // Only show if not effectively the same as "full_remaining"
                if (Math.abs(nextInstallmentValue - remaining) > epsilon) {
                    addRadioButton('next_installment', `Payer le prochain versement (${nextInstallmentValue.toFixed(2)} DH)`, nextInstallmentValue.toFixed(2));
                }
            }
            
            // Option: Payer un montant personnalisé (which will reveal a dropdown for installments count)
            const remainingInstallmentsCount = totalInstallments - currentPaidInstallments;
            // Show custom installments option only if there are at least two installments remaining to choose from,
            // and the remaining amount is more than one installment.
            if (currentInscriptionData.chosenInstallments > 1 && remainingInstallmentsCount > 1 && remaining > installmentAmount + epsilon) {
                addRadioButton('custom_installments_count', 'Payer un nombre d\'acomptes personnalisé', ''); // No amount needed here
            }

            paymentChoicesRadios.querySelectorAll('input[name="payment_choice"]').forEach(radio => {
                radio.removeEventListener('change', updateAmountField); // Remove old listener to prevent duplicates
                radio.addEventListener('change', updateAmountField);
            });

            // Automatically select the first available option if there are options,
            // and trigger the amount update.
            const firstRadio = paymentChoicesRadios.querySelector('input[type="radio"]');
            if (firstRadio) {
                firstRadio.checked = true;
                updateAmountField(); 
            } else {
                amountToPayInput.value = '';
                paymentHint.textContent = '';
                submitBtn.disabled = true; // Button disabled if no payment options are generated
            }
        }

        function addRadioButton(value, text, amount) {
            const label = document.createElement('label');
            label.className = 'inline-flex items-center cursor-pointer mb-2'; // Added cursor-pointer and margin-bottom
            label.innerHTML = `
                <input type="radio" class="form-radio-modern" name="payment_choice" value="${value}" data-amount="${amount}">
                <span class="ml-2 text-gray-700">${text}</span>
            `;
            paymentChoicesRadios.appendChild(label);
        }

        function updateAmountField() {
            const selectedPaymentChoice = document.querySelector('input[name="payment_choice"]:checked');
            customInstallmentsSelection.classList.add('hidden'); 
            numInstallmentsToPaySelect.innerHTML = '<option value="">-- Choisir le nombre d\'acomptes --</option>'; // Clear options

            if (selectedPaymentChoice) {
                const choiceValue = selectedPaymentChoice.value;
                const remaining = currentInscriptionData.remainingAmount;
                const installmentAmount = currentInscriptionData.amountPerInstallment;

                if (choiceValue === 'full_remaining' || choiceValue === 'next_installment') {
                    const amount = parseFloat(selectedPaymentChoice.dataset.amount);
                    amountToPayInput.value = amount.toFixed(2);
                    amountToPayInput.readOnly = true;
                    amountToPayInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                    amountToPayInput.classList.remove('bg-white', 'cursor-text'); // Ensure correct classes
                    paymentHint.textContent = selectedPaymentChoice.nextElementSibling.textContent + " sera appliqué.";
                    submitBtn.disabled = false; // Enable button here
                } else if (choiceValue === 'custom_installments_count') {
                    amountToPayInput.value = '';
                    amountToPayInput.readOnly = true;
                    amountToPayInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                    amountToPayInput.classList.remove('bg-white', 'cursor-text'); // Ensure correct classes
                    
                    customInstallmentsSelection.classList.remove('hidden');
                    populateCustomInstallmentsDropdown();
                    
                    paymentHint.textContent = `Veuillez choisir le nombre d'acomptes à payer.`;
                    submitBtn.disabled = true; // Disable until a custom amount is chosen
                }
            } else {
                amountToPayInput.value = '';
                amountToPayInput.readOnly = true;
                amountToPayInput.classList.add('bg-gray-100', 'cursor-not-allowed');
                amountToPayInput.classList.remove('bg-white', 'cursor-text'); // Ensure correct classes
                paymentHint.textContent = '';
                submitBtn.disabled = true; // Disable if no choice is selected
            }
        }

        function populateCustomInstallmentsDropdown() {
            numInstallmentsToPaySelect.innerHTML = '<option value="">-- Choisir le nombre d\'acomptes --</option>';
            const remaining = currentInscriptionData.remainingAmount;
            const installmentAmount = currentInscriptionData.amountPerInstallment;
            const currentPaidAmount = currentInscriptionData.paidAmount;
            const totalCourseAmount = currentInscriptionData.totalAmount;
            
            // Calculate how many full installments are remaining
            // We use Math.floor with epsilon to handle floating point inaccuracies
            const paidInstallmentsCount = Math.floor(currentPaidAmount / installmentAmount + epsilon);
            const totalInstallmentsCount = currentInscriptionData.chosenInstallments;
            const maxInstallmentsToPay = totalInstallmentsCount - paidInstallmentsCount;

            for (let i = 1; i <= maxInstallmentsToPay; i++) {
                let calculatedAmount = 0;
                let tempPaid = currentPaidAmount;
                let tempRemaining = remaining;

                // Calculate the exact amount for 'i' installments, considering the last installment might be less
                for (let j = 0; j < i; j++) {
                    const amountForThisInstallment = Math.min(installmentAmount, tempRemaining);
                    calculatedAmount += amountForThisInstallment;
                    tempPaid += amountForThisInstallment;
                    tempRemaining = totalCourseAmount - tempPaid;
                    if (tempRemaining <= epsilon && j < i - 1) { // If fully paid before reaching 'i' installments, break
                           calculatedAmount = remaining; // Cap at total remaining
                           break;
                    }
                }
                
                // Ensure the calculated amount does not exceed the total remaining amount
                calculatedAmount = Math.min(calculatedAmount, remaining);

                if (calculatedAmount > epsilon) { // Only add if the calculated amount is positive
                    const optionText = `${i} acompte(s) (${calculatedAmount.toFixed(2)} DH)`;
                    const newOption = document.createElement('option');
                    newOption.value = calculatedAmount.toFixed(2);
                    newOption.textContent = optionText;
                    numInstallmentsToPaySelect.appendChild(newOption);
                }
            }

            // Remove old listener before adding new one
            numInstallmentsToPaySelect.removeEventListener('change', handleCustomInstallmentChange);
            numInstallmentsToPaySelect.addEventListener('change', handleCustomInstallmentChange);
        }

        function handleCustomInstallmentChange() {
            const selectedAmount = parseFloat(numInstallmentsToPaySelect.value);
            if (!isNaN(selectedAmount) && selectedAmount > epsilon) { // Use epsilon for comparison
                amountToPayInput.value = selectedAmount.toFixed(2);
                paymentHint.textContent = `Vous allez payer ${numInstallmentsToPaySelect.options[numInstallmentsToPaySelect.selectedIndex].textContent}.`;
                submitBtn.disabled = false; // Enable button when a custom amount is chosen
            } else {
                amountToPayInput.value = '';
                paymentHint.textContent = '';
                submitBtn.disabled = true; // Disable if no custom amount is chosen or amount is zero
            }
        }

        // Event Listeners
        inscriptionSelect.addEventListener('change', updateInscriptionDetails);
        
        // Initial setup if an inscription is pre-selected (e.g., via old() value)
        if (inscriptionSelect.value) {
            updateInscriptionDetails();
        } else {
            // Ensure button is disabled if no inscription is initially selected
            submitBtn.disabled = true;
            // Hide amount field initially if no inscription is selected
            document.getElementById('amount-field').classList.add('hidden');
        }

        // Handle form submission loading state and validation
        paymentForm.addEventListener('submit', function(event) {
            const amount = parseFloat(amountToPayInput.value);
            const remaining = currentInscriptionData.remainingAmount;
            
            if (isNaN(amount) || amount <= epsilon) { // Check if amount is non-positive or not a number
                alert('Veuillez entrer un montant valide supérieur à zéro.');
                event.preventDefault();
                return false;
            }
            if (amount > remaining + epsilon) { // Allow for small floating point differences
                alert(`Le montant saisi (${amount.toFixed(2)} DH) dépasse le reste à payer (${remaining.toFixed(2)} DH).`);
                event.preventDefault();
                return false;
            }
            
            submitBtn.disabled = true;
            const loadingSpinner = submitBtn.querySelector('.loading-spinner');
            if (loadingSpinner) {
                loadingSpinner.classList.remove('hidden');
            }
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.animated-alert');
            alerts.forEach(function(alert) {
                alert.classList.add('fade-out');
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);

        // Add ripple effect to buttons
        document.querySelectorAll('.btn-modern').forEach(btn => {
            btn.addEventListener('click', function(e) {
                // Prevent ripple if the button is disabled
                if (this.disabled) {
                    return;
                }

                const ripple = document.createElement('span');
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.style.cssText = `
                    width: ${size}px;
                    height: ${size}px;
                    left: ${x}px;
                    top: ${y}px;
                    position: absolute;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.5);
                    transform: scale(0);
                    animation: ripple 0.6s linear;
                    pointer-events: none;
                    z-index: 1; 
                `;
                
                this.style.position = 'relative';
                this.style.overflow = 'hidden';
                this.appendChild(ripple);
                
                setTimeout(() => ripple.remove(), 600);
            });
        });
    });
</script>
@endpush