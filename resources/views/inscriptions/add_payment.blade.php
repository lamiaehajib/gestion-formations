@extends('layouts.app') {{-- Assurez-vous que ceci pointe vers votre layout principal --}}

@section('title', 'Ajouter un Paiement')

@section('content')

<div class="main-bg min-h-screen py-10 flex items-center justify-center">
    <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 w-full">
        <div class="glass-card overflow-hidden shadow-2xl sm:rounded-lg p-8 animated-form relative">
            {{-- Background pattern --}}
            <div class="absolute inset-0 bg-gradient-to-br from-red-50 to-pink-50 opacity-50 rounded-lg -z-10 pattern-dots"></div>

            {{-- Title Section --}}
            <h3 class="text-3xl font-extrabold text-gray-800 mb-8 text-center animate-text-pop">
                <span class="text-gradient">Ajouter un Paiement</span>
            </h3>
            <p class="text-center text-gray-600 mb-8 animate-fade-in">
                Veuillez remplir les détails du paiement pour l'inscription sélectionnée.
            </p>

            {{-- Inscription Details Section --}}
            <div class="section-card border-2 border-red-200 rounded-lg p-6 bg-white transition-all duration-300 hover:shadow-lg animate-slide-up mb-8">
                <h4 class="text-xl font-bold text-red-700 mb-5 flex items-center">
                    <i class="fas fa-info-circle mr-3 text-red-500 animate-pulse-soft"></i>
                    <span class="animate-type-writer">Détails de l'Inscription</span>
                </h4>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4 text-gray-700">
                    <div>
                        <p class="font-semibold">Formation:</p>
                        <p>{{ $inscription->formation->title }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Étudiant:</p>
                        <p>{{ $inscription->user->name }}</p>
                    </div>
                    <div>
                        <p class="font-semibold">Montant Total:</p>
                        <span class="text-blue-600 font-bold">{{ number_format($inscription->total_amount, 2) }} MAD</span>
                    </div>
                    <div>
                        <p class="font-semibold">Montant Payé:</p>
                        <span class="text-green-600 font-bold">{{ number_format($inscription->paid_amount, 2) }} MAD</span>
                    </div>
                    <div class="md:col-span-2">
                        <p class="font-semibold">Montant Restant à Payer:</p>
                        <span class="text-red-600 font-bold">{{ number_format($inscription->total_amount - $inscription->paid_amount, 2) }} MAD</span>
                    </div>
                </div>
            </div>

            {{-- Error & Session Messages --}}
            @if ($errors->any())
                <div class="alert bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 animate-fade-in" role="alert">
                    <strong class="font-bold">Oups!</strong>
                    <span class="block sm:inline">Il y a eu des problèmes avec votre soumission.</span>
                    <ul class="mt-3 list-disc list-inside text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none'">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.15a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.15 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </span>
                </div>
            @endif

            @if (session('error'))
                <div class="alert bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative mb-6 animate-fade-in" role="alert">
                    <strong class="font-bold">Erreur!</strong>
                    <span class="block sm:inline">{{ session('error') }}</span>
                    <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none'">
                        <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"><title>Close</title><path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.15a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.15 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/></svg>
                    </span>
                </div>
            @endif

            {{-- Payment Form --}}
            <form action="{{ route('inscriptions.addPayment', $inscription) }}" method="POST" enctype="multipart/form-data" class="space-y-8" id="add-payment-form">
                @csrf

                <div class="form-group animate-slide-right">
                    <label for="amount" class="form-label label-with-icon">
                        Montant du paiement <span class="text-red-500">*</span>
                        <i class="fas fa-coins input-icon-label"></i>
                    </label>
                    <div class="input-wrapper">
                        <input type="number" step="0.01" name="amount" id="amount" class="form-input"
                            value="{{ old('amount', number_format($inscription->total_amount - $inscription->paid_amount, 2, '.', '')) }}"
                            required min="0.01" max="{{ number_format($inscription->total_amount - $inscription->paid_amount, 2, '.', '') }}">
                    </div>
                    <small class="text-sm text-gray-500 mt-1 block">Le montant maximum que vous pouvez payer est {{ number_format($inscription->total_amount - $inscription->paid_amount, 2) }} MAD.</small>
                    @error('amount')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group animate-slide-left">
                    <label for="payment_method" class="form-label label-with-icon">
                        Méthode de paiement <span class="text-red-500">*</span>
                        <i class="fas fa-credit-card input-icon-label"></i>
                    </label>
                    <div class="input-wrapper">
                        <select name="payment_method" id="payment_method" class="form-select" required>
                            <option value="">Sélectionner une méthode</option>
                            <option value="cash" {{ old('payment_method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                            <option value="bank_transfer" {{ old('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Virement bancaire</option>
                            <option value="card" {{ old('payment_method') == 'card' ? 'selected' : '' }}>Carte bancaire</option>
                            <option value="cheque" {{ old('payment_method') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                        </select>
                    </div>
                    @error('payment_method')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group animate-fade-in-up">
                    <label for="payment_notes" class="form-label label-with-icon">
                        Notes de paiement (Optionnel)
                        <i class="fas fa-sticky-note input-icon-label"></i>
                    </label>
                    <div class="input-wrapper">
                        <textarea name="payment_notes" id="payment_notes" rows="3" class="form-textarea" placeholder="Ajoutez des notes supplémentaires ici...">{{ old('payment_notes') }}</textarea>
                    </div>
                    @error('payment_notes')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="form-group animate-bounce-in">
                    <label for="receipt_file" class="form-label label-with-icon">
                        Fichier de reçu (Optionnel, PDF/Image)
                        <i class="fas fa-receipt input-icon-label"></i>
                    </label>
                    <div class="file-upload-wrapper">
                        <input type="file" name="receipt_file" id="receipt_file" class="file-input" accept="image/*,application/pdf">
                        <div class="file-upload-overlay">
                            <i class="fas fa-cloud-upload-alt text-4xl text-red-400 mb-2"></i>
                            <p class="text-sm text-gray-600">Glissez votre fichier ici ou cliquez pour sélectionner</p>
                        </div>
                        <p class="mt-1 text-xs text-gray-500">Formats acceptés: PDF, JPG, JPEG, PNG (Max 2MB).</p>
                    </div>
                    @error('receipt_file')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mt-8 flex items-center justify-end gap-x-6 animate-fade-in-up">
                    <a href="{{ route('inscriptions.show', $inscription) }}" class="btn-secondary group">
                        <i class="fas fa-times-circle mr-2"></i> Annuler
                    </a>
                    <button type="submit" id="submit-payment-btn" class="btn-primary group">
                        <i class="fas fa-save mr-2"></i> Enregistrer le Paiement
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
    /* CSS Variables for colors */
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
        display: flex;
        align-items: center;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--text-dark);
        margin-bottom: 0.5rem;
        transition: color 0.3s ease;
    }

    .input-icon-label {
        margin-left: 0.5rem;
        color: var(--primary-red);
        font-size: 1rem;
    }

    .input-wrapper {
        position: relative;
    }

    .form-select, .form-input, .form-textarea {
        width: 100%;
        padding: 1.25rem 1.5rem;
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
            padding: 1rem 1.25rem;
        }

        .input-icon-label {
            margin-left: 0.25rem;
            font-size: 0.9rem;
        }
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const amountInput = document.getElementById('amount');
        const paymentForm = document.getElementById('add-payment-form');
        const submitBtn = document.getElementById('submit-payment-btn');
        const receiptFileInput = document.getElementById('receipt_file');
        const fileUploadWrapper = document.querySelector('.file-upload-wrapper');

        // Add input focus animations (similar to the example)
        const inputs = document.querySelectorAll('.form-select, .form-input, .form-textarea');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                // For direct form-inputs, we can apply transform on the input itself or its wrapper if needed
                // For simplicity, this example directly targets the input for a slight visual feedback.
                this.style.transform = 'translateY(-2px)';
                this.style.boxShadow = '0 0 0 4px rgba(211, 47, 47, 0.1), 0 4px 20px rgba(211, 47, 47, 0.15)';
            });

            input.addEventListener('blur', function() {
                this.style.transform = 'translateY(0)';
                this.style.boxShadow = '0 2px 10px rgba(0, 0, 0, 0.05)';
            });
        });

        // Submit button loading state
        paymentForm.addEventListener('submit', function() {
            submitBtn.disabled = true;
            const loadingSpinner = submitBtn.querySelector('.loading-spinner');
            if (loadingSpinner) {
                loadingSpinner.classList.remove('hidden');
            }
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        });

        // File upload animation logic
        if (receiptFileInput && fileUploadWrapper) {
            fileUploadWrapper.addEventListener('dragover', function(e) {
                e.preventDefault();
                this.style.borderColor = 'var(--primary-red)';
                this.style.backgroundColor = 'rgba(255, 255, 255, 0.95)'; // Lighter background on drag
            });

            fileUploadWrapper.addEventListener('dragleave', function(e) {
                e.preventDefault();
                this.style.borderColor = 'var(--border-light)';
                this.style.backgroundColor = 'var(--bg-light)';
            });

            receiptFileInput.addEventListener('change', function() {
                const overlay = fileUploadWrapper.querySelector('.file-upload-overlay');
                if (this.files && this.files[0]) {
                    const fileName = this.files[0].name;
                    overlay.innerHTML = `
                        <i class="fas fa-check-circle text-4xl text-green-500 mb-2"></i>
                        <p class="text-sm text-green-600">Fichier sélectionné: ${fileName}</p>
                    `;
                    fileUploadWrapper.style.borderColor = 'rgb(34, 197, 94)'; /* Tailwind green-500 equivalent */
                } else {
                    overlay.innerHTML = `
                        <i class="fas fa-cloud-upload-alt text-4xl text-red-400 mb-2"></i>
                        <p class="text-sm text-gray-600">Glissez votre fichier ici ou cliquez pour sélectionner</p>
                    `;
                    fileUploadWrapper.style.borderColor = 'var(--border-light)';
                }
            });
        }
    });
</script>
@endpush

@endsection