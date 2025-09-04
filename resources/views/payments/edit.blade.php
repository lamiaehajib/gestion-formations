@extends('layouts.app')

@section('title', 'Modifier le Paiement')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    /*
    This CSS is copied from your create.blade.php to ensure a consistent look and feel.
    It includes styles for modern cards, buttons, form inputs, and animations.
    */
    :root {
        --primary-red: #D32F2F;
        --secondary-pink: #C2185B;
        --accent-red: #ef4444;
        --gradient-primary: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
        --gradient-secondary: linear-gradient(135deg, #ef4444 0%, #D32F2F 100%);
        --gradient-light: linear-gradient(135deg, rgba(211,47,47,0.1) 0%, rgba(194,24,91,0.1) 100%);
        --shadow-red: rgba(211, 47, 47, 0.3);
        --shadow-pink: rgba(194, 24, 91, 0.3);
        --payment-primary: #D32F2F;
        --payment-secondary: #C2185B;
        --payment-accent: #ef4444;
        --payment-gradient-bg: linear-gradient(135deg, rgba(240, 98, 146, 0.1) 0%, rgba(229, 115, 115, 0.1) 100%);
        --payment-card-border: #FFCDD2;
        --payment-info-bg: #F8E1E1;
        --payment-options-bg: #FCE4EC;
    }

    body {
        background: var(--payment-gradient-bg);
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #333;
    }

    .card-modern {
        border: none;
        border-radius: 20px;
        box-shadow: 0 15px 35px rgba(0,0,0,0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        background: rgba(255,255,255,0.95);
    }

    .gradient-header {
        background: linear-gradient(135deg, var(--payment-primary) 0%, var(--payment-secondary) 100%);
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
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .btn-gradient {
        background: linear-gradient(135deg, var(--payment-primary) 0%, var(--payment-secondary) 100%);
        color: white;
        box-shadow: 0 8px 25px var(--shadow-red);
    }

    .btn-gradient:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 15px 35px var(--shadow-red);
        color: white;
    }

    .btn-gradient::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-gradient:hover::before {
        left: 100%;
    }

    .btn-secondary-outline {
        background-color: transparent;
        border: 2px solid #6c757d;
        color: #6c757d;
        box-shadow: 0 5px 15px rgba(108, 117, 125, 0.1);
    }

    .btn-secondary-outline:hover {
        background-color: #6c757d;
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(108, 117, 125, 0.2);
    }

    .form-label-modern {
        font-weight: 700;
        color: #4a4a4a;
        margin-bottom: 8px;
        display: flex;
        align-items: center;
    }

    .form-label-modern i {
        margin-right: 8px;
        color: var(--payment-primary);
    }

    .form-input, .form-textarea, .form-select, .form-input-file {
        border-radius: 15px;
        border: 2px solid var(--payment-card-border);
        padding: 12px 18px;
        font-weight: 500;
        transition: all 0.3s ease;
        background-color: #ffffff;
        width: 100%;
        color: #000;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus, .form-input-file:focus {
        border-color: var(--payment-primary);
        box-shadow: 0 0 20px rgba(211, 47, 47, 0.2);
        outline: none;
    }

    .form-input.bg-gray-100, .form-select.bg-gray-100 {
        background-color: #f3f4f6;
        cursor: not-allowed !important;
    }

    .form-text-modern {
        font-size: 0.85em;
        color: #777;
        margin-top: 5px;
    }

    .alert-success-modern {
        background: linear-gradient(135deg, #e6ffe6 0%, #d4ffdb 100%);
        border: 1px solid #4CAF50;
        border-radius: 15px;
        color: #1B5E20;
        padding: 15px 20px;
        animation: fadeIn 0.5s ease-out;
    }

    .alert-danger-modern {
        background: linear-gradient(135deg, #fde7e7 0%, #fcdede 100%);
        border: 1px solid var(--payment-accent);
        border-radius: 15px;
        color: var(--payment-primary);
        padding: 15px 20px;
        animation: fadeIn 0.5s ease-out;
    }

    .section-card {
        border: 2px solid var(--payment-card-border);
        border-radius: 15px;
        padding: 24px;
        background-color: #ffffff;
        transition: all 0.3s ease;
    }
    .section-card:hover {
        box-shadow: 0 8px 25px rgba(211, 47, 47, 0.1);
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animated-form { animation: fadeIn 0.6s ease-out forwards; }
    .animated-alert { animation: fadeIn 0.5s ease-out forwards; }
    .fade-out { animation: fadeOut 0.5s forwards; }
    @keyframes fadeOut {
        from { opacity: 1; }
        to { opacity: 0; transform: translateY(-10px); }
    }
    .animate-text-pop { animation: textPop 0.8s ease-out forwards; }
    @keyframes textPop {
        0% { transform: scale(0.8); opacity: 0; }
        70% { transform: scale(1.05); opacity: 1; }
        100% { transform: scale(1); }
    }
    .animate-icon-fade { animation: iconFade 1.5s infinite alternate; }
    @keyframes iconFade {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.6; }
    }
    .loading-spinner { display: inline-block; }
    .fa-spin { animation: fa-spin 1s infinite linear; }
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
                        <i class="fas fa-file-invoice-dollar mr-3"></i> Modifier le Paiement
                    </h3>
                    <p class="text-center text-white text-opacity-80 mt-2">
                        Mettez à jour les détails du paiement.
                    </p>
                </div>

                {{-- Success/Error Messages --}}
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

                <form action="{{ route('payments.update', $payment->id) }}" method="POST" class="space-y-8" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="section-card">
                        <h4 class="text-xl font-bold text-gray-700 mb-5 flex items-center">
                            <i class="fas fa-file-invoice mr-3 text-red-500 animate-icon-fade"></i> Détails du Paiement
                        </h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-6">

                            {{-- Inscription field (read-only for edit) --}}
                            <div class="md:col-span-2">
                                <label for="inscription_display" class="form-label-modern block">
                                    <i class="fas fa-clipboard-list"></i> Inscription
                                </label>
                                <input type="text" id="inscription_display" value="{{ $payment->inscription->formation->title }} ({{ $payment->inscription->user->name }})" class="form-input bg-gray-100 cursor-not-allowed" readonly>
                                <input type="hidden" name="inscription_id" value="{{ $payment->inscription->id }}">
                            </div>

                            {{-- Montant à Payer field --}}
                            <div>
                                <label for="amount_to_pay" class="form-label-modern block">
                                    <i class="fas fa-dollar-sign"></i> Montant à Payer <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="number" step="0.01" min="0.01" class="form-input {{ ($currentUserIsAdmin) ? '' : 'bg-gray-100 cursor-not-allowed' }}" id="amount_to_pay" name="amount_to_pay" value="{{ old('amount_to_pay', $payment->amount) }}" required {{ ($currentUserIsAdmin) ? '' : 'readonly' }}>
                                </div>
                                <small class="form-text-modern" id="payment-hint"></small>
                                @error('amount_to_pay')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Date de paiement field --}}
                            <div>
                                <label for="paid_date" class="form-label-modern block">
                                    <i class="fas fa-calendar-alt"></i> Date de paiement <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="date" class="form-input {{ ($currentUserIsAdmin) ? '' : 'bg-gray-100 cursor-not-allowed' }}" id="paid_date" name="paid_date" value="{{ old('paid_date', $payment->paid_date ? \Carbon\Carbon::parse($payment->paid_date)->format('Y-m-d') : '') }}" required {{ ($currentUserIsAdmin) ? '' : 'readonly' }}>
                                </div>
                                @error('paid_date')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Méthode de Paiement field --}}
                            <div class="md:col-span-1">
                                <label for="payment_method" class="form-label-modern block">
                                    <i class="fas fa-wallet"></i> Méthode de Paiement <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <select class="form-select {{ ($currentUserIsAdmin) ? '' : 'bg-gray-100 cursor-not-allowed' }}" id="payment_method" name="payment_method" required {{ ($currentUserIsAdmin) ? '' : 'disabled' }}>
                                        <option value="">-- Choisir une méthode --</option>
                                        <option value="cash" {{ old('payment_method', $payment->payment_method) == 'cash' ? 'selected' : '' }}>Espèces</option>
                                        <option value="bank_transfer" {{ old('payment_method', $payment->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Virement bancaire</option>
                                        <option value="credit_card" {{ old('payment_method', $payment->payment_method) == 'credit_card' ? 'selected' : '' }}>Carte de crédit</option>
                                        <option value="cheque" {{ old('payment_method', $payment->payment_method) == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                    </select>
                                </div>
                                @error('payment_method')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Status field (for admins only) --}}
                            @if ($currentUserIsAdmin)
                                <div>
                                    <label for="status" class="form-label-modern block">
                                        <i class="fas fa-info-circle"></i> Statut <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <select class="form-select" id="status" name="status" required>
                                            <option value="pending" {{ old('status', $payment->status) == 'pending' ? 'selected' : '' }}>En attente</option>
                                            <option value="paid" {{ old('status', $payment->status) == 'paid' ? 'selected' : '' }}>Payé</option>
                                            <option value="late" {{ old('status', $payment->status) == 'late' ? 'selected' : '' }}>En retard</option>
                                        </select>
                                    </div>
                                    @error('status')
                                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            @else
                                <input type="hidden" name="status" value="{{ $payment->status }}">
                            @endif

                            {{-- Description du paiement --}}
                            <div class="md:col-span-2">
                                <label for="payment_description" class="form-label-modern block">
                                    <i class="fas fa-comment-alt"></i> Description du Paiement (Optionnel)
                                </label>
                                <div class="relative">
                                    <textarea name="payment_description" id="payment_description" rows="2" class="form-textarea">{{ old('payment_description', $payment->reference) }}</textarea>
                                </div>
                                @error('payment_description')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            {{-- Receipt File (View/Replace based on access) --}}
                            <div class="md:col-span-2">
                                <label class="form-label-modern block">
                                    <i class="fas fa-file-invoice"></i> Reçu du paiement
                                </label>
                                @if ($payment->receipt_path)
                                    <p class="mb-2">
                                        <a href="{{ Storage::url($payment->receipt_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 flex items-center">
                                            <i class="fas fa-file-download mr-2"></i> Voir le reçu actuel
                                        </a>
                                    </p>
                                @else
                                    <p class="text-gray-500">Aucun reçu téléchargé.</p>
                                @endif

                                @if (!$paymentCreatedByAdmin)
                                    <label for="receipt_file" class="form-label-modern block mt-3">
                                        <i class="fas fa-upload"></i> Remplacer le reçu (Optionnel)
                                    </label>
                                    <div class="relative">
                                        <input type="file" name="receipt_file" id="receipt_file" class="form-input-file" accept="image/*,application/pdf">
                                        <p class="mt-1 text-xs text-gray-500">Formats acceptés: PDF, JPG, JPEG, PNG (Max 5MB)</p>
                                    </div>
                                @endif
                                @error('receipt_file')
                                    <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Action Buttons --}}
                    <div class="mt-8 flex items-center justify-end gap-x-6">
                        <a href="{{ route('payments.index') }}" class="btn-modern btn-secondary-outline group">
                            <i class="fas fa-times-circle mr-2 group-hover:scale-110 transition-transform duration-200"></i> Annuler
                        </a>
                        <button type="submit" class="btn-modern btn-gradient group">
                            <i class="fas fa-save mr-2 group-hover:scale-110 transition-transform duration-200"></i> Enregistrer les modifications
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
        const paymentForm = document.querySelector('form');
        const submitBtn = paymentForm.querySelector('button[type="submit"]');
        const amountToPayInput = document.getElementById('amount_to_pay');
        const paymentHint = document.getElementById('payment-hint');

        // Initial setup for the payment hint
        const totalAmount = parseFloat({{ $payment->inscription->total_amount }});
        const paidAmount = parseFloat({{ $payment->inscription->paid_amount }});
        const thisPaymentAmount = parseFloat({{ $payment->amount }});
        const remainingAmount = totalAmount - (paidAmount - thisPaymentAmount);
        paymentHint.textContent = `Le reste à payer pour cette inscription est de ${remainingAmount.toFixed(2)} DH.`;

        // Validation for the amount field
        paymentForm.addEventListener('submit', function(event) {
            const currentAmount = parseFloat(amountToPayInput.value);
            // Floating point tolerance check
            const epsilon = 0.01;
            if (currentAmount > remainingAmount + epsilon) {
                alert(`Le montant saisi (${currentAmount.toFixed(2)} DH) dépasse le reste dû (${remainingAmount.toFixed(2)} DH) pour cette inscription.`);
                event.preventDefault();
                return;
            }

            // Disable button to prevent multiple submissions
            submitBtn.disabled = true;
            const loadingSpinner = submitBtn.querySelector('.loading-spinner');
            if (loadingSpinner) {
                loadingSpinner.classList.remove('hidden');
            }
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        });

        // Auto-hide alerts
        setTimeout(function() {
            const alerts = document.querySelectorAll('.animated-alert');
            alerts.forEach(function(alert) {
                alert.classList.add('fade-out');
                setTimeout(() => alert.remove(), 500);
            });
        }, 5000);
    });
</script>
@endpush
