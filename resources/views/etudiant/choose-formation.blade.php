@extends('layouts.inscri')

@section('title', __('S\'inscrire à une Formation'))

@section('content')
    <style>
        /* Modern CSS Variables */
        :root {
            --primary-gradient: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --success-gradient: linear-gradient(135deg,rgb(107, 46, 46) 0%,rgb(240, 120, 87) 100%);
            --warning-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --card-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            --hover-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            --border-radius: 16px;
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Page Background */
        .page-bg {
           
            min-height: 100vh;
            position: relative;
        }

        .page-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%);
            pointer-events: none;
        }

        /* Main Container */
        .main-container {
            position: relative;
            z-index: 1;
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            animation: slideUp 0.6s ease-out;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Alert Animations */
        .alert-animated {
            animation: bounceIn 0.6s ease-out;
            border: none;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            position: relative;
            overflow: hidden;
        }

        .alert-animated::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            animation: shimmer 2s infinite;
        }

        @keyframes bounceIn {
            0% {
                opacity: 0;
                transform: scale(0.3);
            }
            50% {
                opacity: 1;
                transform: scale(1.05);
            }
            70% {
                transform: scale(0.9);
            }
            100% {
                opacity: 1;
                transform: scale(1);
            }
        }

        @keyframes shimmer {
            0% {
                left: -100%;
            }
            100% {
                left: 100%;
            }
        }

        .alert-success {
            background: var(--success-gradient);
            color: white;
        }

        .alert-danger {
            background: var(--secondary-gradient);
            color: white;
        }

        /* Title Styling */
        .page-title {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            position: relative;
            animation: titleGlow 2s ease-in-out infinite alternate;
        }

       

        /* Modern Select Dropdown */
        .select-modern {
            position: relative;
            overflow: hidden;
            border: 2px solid transparent;
            border-radius: var(--border-radius);
            background: linear-gradient(white, white) padding-box,
                        var(--primary-gradient) border-box;
            transition: var(--transition);
        }

        .select-modern:focus-within {
            transform: translateY(-2px);
            box-shadow: var(--hover-shadow);
        }

        .select-modern select {
            background: transparent;
            border: none;
            padding: 15px 20px;
            font-size: 16px;
            width: 100%;
            cursor: pointer;
            appearance: none;
        }

        .select-modern::after {
            content: '▼';
            position: absolute;
            right: 20px;
            top: 50%;
            transform: translateY(-50%);
            color: #667eea;
            font-size: 12px;
            pointer-events: none;
            transition: var(--transition);
        }

        .select-modern:hover::after {
            transform: translateY(-50%) rotate(180deg);
        }

        /* Formation Cards */
        .formation-card {
            background: white;
            border-radius: var(--border-radius);
            box-shadow: var(--card-shadow);
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            height: 100%;
        }

        .formation-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: var(--primary-gradient);
            transform: scaleX(0);
            transition: var(--transition);
        }

        .formation-card:hover {
            transform: translateY(-8px);
            box-shadow: var(--hover-shadow);
        }

        .formation-card:hover::before {
            transform: scaleX(1);
        }

        .formation-card .card-body {
            padding: 24px;
        }

        .formation-card .card-title {
            color: #1a202c;
            font-weight: 700;
            margin-bottom: 12px;
            font-size: 1.25rem;
        }

        .formation-card .card-text {
            color: #718096;
            line-height: 1.6;
            margin-bottom: 16px;
        }

        .formation-info {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .formation-info li {
            display: flex;
            align-items: center;
            margin-bottom: 8px;
            color: #4a5568;
            font-size: 14px;
        }

        .formation-info li i {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: var(--primary-gradient);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            font-size: 12px;
        }

        /* Modern Buttons */
        .btn-modern {
            background: var(--primary-gradient);
            border: none;
            border-radius: 50px;
            color: white;
            font-weight: 600;
            padding: 12px 24px;
            transition: var(--transition);
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            font-size: 14px;
        }

        .btn-modern::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: var(--transition);
        }

        .btn-modern:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 20px rgba(102, 126, 234, 0.4);
        }

        .btn-modern:hover::before {
            left: 100%;
        }

        .btn-success {
            background: var(--success-gradient);
        }

        .btn-danger {
            background: var(--secondary-gradient);
        }

        .btn-secondary {
            background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);
        }

        /* Modal Enhancements */
        .modal-content {
            border-radius: var(--border-radius);
            border: none;
            box-shadow: var(--hover-shadow);
            animation: modalSlideIn 0.3s ease-out;
        }

        @keyframes modalSlideIn {
            from {
                opacity: 0;
                transform: scale(0.9) translateY(-20px);
            }
            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-bottom: none;
            border-radius: var(--border-radius) var(--border-radius) 0 0;
        }

        .modal-title {
            font-weight: 700;
        }

        .btn-close {
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            opacity: 1;
            color: white;
        }

        /* Form Inputs */
        .form-control-modern {
            border: 2px solid #e2e8f0;
            border-radius: var(--border-radius);
            padding: 15px 20px;
            font-size: 16px;
            transition: var(--transition);
            background: white;
        }

        .form-control-modern:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
            transform: translateY(-2px);
        }

        .form-label-modern {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 8px;
            display: block;
        }

        /* Price Display */
        .price-display {
            background: var(--warning-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            font-weight: 800;
            font-size: 1.5rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        /* Loading Animation */
        .loading-spinner {
            display: inline-block;
            width: 20px;
            height: 20px;
            border: 3px solid rgba(255, 255, 255, 0.3);
            border-radius: 50%;
            border-top-color: white;
            animation: spin 1s ease-in-out infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .main-container {
                margin: 10px;
                border-radius: 12px;
            }
            
            .formation-card {
                margin-bottom: 20px;
            }
            
            .page-title {
                font-size: 2rem;
            }
        }

        /* Hover Effects */
        .hover-lift {
            transition: var(--transition);
        }

        .hover-lift:hover {
            transform: translateY(-2px);
        }

        /* Custom Scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb {
            background: var(--primary-gradient);
            border-radius: 10px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-gradient);
        }
    </style>

   <div class="page-bg py-5">
        <div class="container">
            <div class="main-container">
                <div class="p-4 p-md-5">
                    {{-- Success/Error Messages --}}
                    @if (session('success'))
                        <div class="alert alert-success alert-animated mb-4" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger alert-animated mb-4" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>@endif
                    @if ($errors->any())
                        <div class="alert alert-danger alert-animated mb-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    {{-- Page Title --}}
                    <div class="text-center mb-5">
                        <h1 class="page-title display-4 mb-3">
                            <i class="fas fa-graduation-cap me-3"></i>
                            {{ __('Inscription à une Formation') }}
                        </h1>
                        <p class="lead text-muted">{{ __('Sélectionnez une catégorie, puis choisissez votre formation et mode de paiement.') }}</p>
                    </div>

                    {{-- Category Filter --}}
                    <div class="mb-5">
                        <label for="category_filter" class="form-label-modern">
                            <i class="fas fa-filter me-2"></i>
                            {{ __('Filtrer par Catégorie') }}
                        </label>
                        <div class="select-modern">
                            <select id="category_filter" class="form-control">
                                <option value="">{{ __('Toutes les catégories') }}</option>
                                @foreach ($categories as $category)
                                    <option value="{{ $category->id }}" {{ $selectedCategoryId == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    {{-- Formations Grid --}}
                    <div class="mb-5">
                        <h2 class="h3 mb-4 text-dark">
                            <i class="fas fa-books me-2"></i>
                            {{ __('Formations Disponibles') }}
                        </h2>
                        @if ($formations->isEmpty())
                            <div class="text-center py-5">
                                <i class="fas fa-book-open display-1 text-muted mb-3"></i>
                                <p class="lead text-muted">{{ __('Aucune formation disponible pour le moment dans cette catégorie.') }}</p>
                            </div>
                        @else
                            <div class="row g-4">
                                @foreach ($formations as $formation)
                                    <div class="col-md-6 col-lg-4">
                                        <div class="formation-card">
                                            <div class="card-body">
                                                <h3 class="card-title">{{ $formation->title }}</h3>
                                                <p class="card-text">{{ Str::limit($formation->description, 100) }}</p>
                                                <ul class="formation-info">
                                                    <li>
                                                        <i class="fas fa-tag"></i>
                                                        {{ __('Catégorie') }}: {{ $formation->category->name ?? 'N/A' }}
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-money-bill-wave"></i>
                                                        {{ __('Prix') }}: {{ number_format($formation->price, 2) }} DH
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-clock"></i>
                                                        {{-- Display duration with its unit --}}
                                                        {{ __('Durée') }}: {{ $formation->duration_hours }} {{ $formation->duration_unit }}
                                                    </li>
                                                    <li>
                                                        <i class="fas fa-users"></i>
                                                        {{ __('Capacité') }}: {{ $formation->capacity }}
                                                    </li>
                                                </ul>
                                                <button type="button" class="btn btn-modern w-100 mt-3 enroll-btn"
                                                        data-bs-toggle="modal" data-bs-target="#inscriptionModal"
                                                        data-formation-id="{{ $formation->id }}"
                                                        data-formation-title="{{ $formation->title }}"
                                                        data-formation-price="{{ $formation->price }}"
                                                        data-available-options="{{ json_encode($formation->available_payment_options ?? [1]) }}"
                                                        data-category-name="{{ $formation->category->name ?? '' }}"> {{-- ADD THIS LINE --}}
                                                    <i class="fas fa-rocket me-2"></i>
                                                    {{ __('S\'inscrire maintenant') }}
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Modal --}}
    <div class="modal fade" id="inscriptionModal" tabindex="-1" aria-labelledby="inscriptionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="inscriptionModalLabel">
                        <i class="fas fa-user-plus me-2"></i>
                        {{ __('Détails de l\'inscription pour') }} <span id="modalFormationTitle"></span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="{{ route('etudiant.enroll_formation') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-body">
                        <input type="hidden" name="formation_id" id="modalFormationId">
                        <input type="hidden" name="total_amount_display" id="modalFormationPrice"> {{-- Renamed to be clearer --}}
                        <input type="hidden" name="category_name" id="modalCategoryName"> {{-- ADD THIS LINE --}}

                        {{-- Price Display --}}
                        <div class="text-center mb-4 p-3 bg-light rounded">
                            <label class="form-label-modern mb-2">
                                <i class="fas fa-tag me-2"></i>
                                {{ __('Prix de la Formation :') }}
                            </label>
                            <div class="price-display" id="displayFormationPrice"></div>
                            <small class="text-muted">DH</small>
                        </div>

                        {{-- Payment Options --}}
                        <div class="mb-4">
                            <label for="selected_payment_option" class="form-label-modern">
                                <i class="fas fa-credit-card me-2"></i>
                                {{ __('Modalité de Paiement') }}
                            </label>
                            <div class="select-modern">
                                <select id="selected_payment_option" name="selected_payment_option" class="form-control" required>
                                    <option value="">{{ __('Sélectionner une modalité') }}</option>
                                </select>
                            </div>
                            @error('selected_payment_option')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Initial Payment Amount --}}
                        
<div class="mb-4">
    <label for="initial_paid_amount" class="form-label-modern">
        <i class="fas fa-calculator me-2"></i>
        {{ __('Montant Initial Payé :') }}
    </label>
    <input type="number" step="0.01" min="0" name="initial_paid_amount" 
           id="initial_paid_amount" class="form-control-modern" value="0" required>
    <input type="hidden" name="min_initial_paid_amount" id="min_initial_paid_amount" value="0">
    <small class="text-muted">
        <i class="fas fa-info-circle me-1"></i>
        {{ __('Veuillez payer au moins le montant minimum requis.') }}
        <span id="fixedFeeNote" class="text-primary fw-bold" style="display: none;"> (Minimum requis : 1600 DH)</span>
    </small>
    @error('initial_paid_amount')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
</div>

                        {{-- Payment Method --}}
                        <div class="mb-4">
                            <label for="payment_method" class="form-label-modern">
                                <i class="fas fa-wallet me-2"></i>
                                {{ __('Méthode de Paiement') }}
                            </label>
                            <div class="select-modern">
                                <select id="payment_method" name="payment_method" class="form-control" required>
                                    <option value="">{{ __('Sélectionnez une méthode') }}</option>
                                    <option value="cash">
                                        <i class="fas fa-money-bill-wave me-2"></i>
                                        {{ __('Cash') }}
                                    </option>
                                    <option value="bank_transfer">
                                        <i class="fas fa-exchange-alt me-2"></i>
                                        {{ __('Virement Bancaire') }}
                                    </option>
                                </select>
                            </div>
                            @error('payment_method')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Proof of Payment --}}
                        <div class="mb-4">
                            <label for="proof_of_payment" class="form-label-modern">
                                <i class="fas fa-upload me-2"></i>
                                {{ __('Preuve de Paiement (Capture d\'écran ou Reçu)') }}
                            </label>
                            <input type="file" id="proof_of_payment" name="proof_of_payment" 
                                   class="form-control-modern" accept="image/*,.pdf" required>
                            @error('proof_of_payment')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Notes --}}
                        <div class="mb-4">
                            <label for="notes" class="form-label-modern">
                                <i class="fas fa-sticky-note me-2"></i>
                                {{ __('Notes (Optionnel)') }}
                            </label>
                            <textarea id="notes" name="notes" class="form-control-modern" rows="3" 
                                    placeholder="{{ __('Ajoutez des notes supplémentaires...') }}">{{ old('notes') }}</textarea>
                            @error('notes')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">
                            <i class="fas fa-times me-2"></i>
                            {{ __('Annuler') }}
                        </button>
                        <button type="submit" class="btn btn-success btn-modern">
                            <i class="fas fa-check me-2"></i>
                            {{ __('Confirmer l\'inscription') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')

    {{-- Enhanced JavaScript --}}
    <script src="{{ asset('edmate/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('edmate/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            // Access fixed registration fees passed from controller
            const fixedRegistrationFees = @json($fixedRegistrationFees); // Make sure this is correctly passed from controller

            // Global variable to store current formation's category name
            let currentFormationCategoryName = '';

            // Add loading animation to buttons
            function showButtonLoading(button) {
                const originalText = button.html();
                button.html('<span class="loading-spinner me-2"></span>Chargement...');
                button.prop('disabled', true);
                
                // No setTimeout here, as the action usually leads to page reload or modal close
                // The spinner will disappear on its own after navigation or AJAX completion.
            }

            // Category filter with animation
            $('#category_filter').on('change', function() {
                const button = $(this).closest('.select-modern');
                showButtonLoading(button);
                
                var categoryId = $(this).val();
                var currentUrl = new URL(window.location.href);
                if (categoryId) {
                    currentUrl.searchParams.set('category_id', categoryId);
                } else {
                    currentUrl.searchParams.delete('category_id');
                }
                
                // This will cause a full page reload, so the spinner will naturally disappear.
                window.location.href = currentUrl.toString();
            });

            // Enhanced enrollment button handler (for each formation card)
            $('.enroll-btn').on('click', function() {
                const button = $(this);
                // No need to show loading here if modal opens immediately.
                // Loading is typically for form submission.
                
                var formationId = $(this).data('formation-id');
                var formationTitle = $(this).data('formation-title');
                var formationPrice = parseFloat($(this).data('formation-price'));
                var availableOptions = $(this).data('available-options');
                currentFormationCategoryName = $(this).data('category-name'); // Store category name

                // Set modal values
                $('#modalFormationId').val(formationId);
                $('#modalFormationTitle').text(formationTitle);
                $('#modalFormationPrice').val(formationPrice); // Store actual price in hidden input
                $('#displayFormationPrice').text(formationPrice.toFixed(2)); // Display formatted price
                $('#modalCategoryName').val(currentFormationCategoryName); // Set category name in hidden input

                // Reset form
                $('#inscriptionModal form')[0].reset();
                $('#inscriptionModal .invalid-feedback').removeClass('d-block').text(''); // Clear previous errors
                $('#initial_paid_amount_display').removeClass('border-danger'); // Clear error border

                // Update payment options dropdown
                var paymentOptionSelect = $('#selected_payment_option');
                paymentOptionSelect.empty().append('<option value="">{{ __('Sélectionner une modalité') }}</option>');

                if (availableOptions && availableOptions.length > 0) {
                    availableOptions.sort((a, b) => a - b);
                    $.each(availableOptions, function(index, option) {
                        let optionText = '';
                        let icon = '';
                        if (option === 1) {
                            optionText = 'Paiement Complet (1 Versement)';
                            icon = '💰';
                        } else if (option === 2) {
                            optionText = '2 Versements';
                            icon = '💳';
                        } else if (option === 3) {
                            optionText = '3 Versements';
                            icon = '📅';
                        } else {
                            optionText = `${option} Versements`;
                            icon = '📊';
                        }
                        paymentOptionSelect.append($('<option>', {
                            value: option,
                            text: `${icon} ${optionText}`
                        }));
                    });
                } else {
                    // Fallback if no payment options are available
                    paymentOptionSelect.append($('<option>', {
                        value: "1",
                        text: "💰 Paiement Complet (1 Versement) - Par défaut"
                    }));
                }

                // Trigger updateInitialPaymentAmount after options are loaded
                // This handles cases where initial option is auto-selected or 'old' value is restored
                paymentOptionSelect.trigger('change'); 
            });

            // Update initial payment amount dynamically when payment option changes
            $('#selected_payment_option').on('change', function() {
                updateInitialPaymentAmount();
                
                // Add visual feedback
                $('#initial_paid_amount_display').addClass('border-success').removeClass('border-danger');
                setTimeout(() => {
                    $('#initial_paid_amount_display').removeClass('border-success');
                }, 1000);
            });

            function updateInitialPaymentAmount() {
    const formationPrice = parseFloat($('#modalFormationPrice').val());
    const selectedInstallments = parseInt($('#selected_payment_option').val());
    const initialPaidAmountInput = $('#initial_paid_amount');
    const minInitialPaidAmountInput = $('#min_initial_paid_amount');
    const fixedFeeNote = $('#fixedFeeNote');

    let calculatedAmount = 0;
    let isFixedFeeApplied = false;

    if (fixedRegistrationFees[currentFormationCategoryName]) {
        let fixedFee = fixedRegistrationFees[currentFormationCategoryName];
        calculatedAmount = fixedFee;
        isFixedFeeApplied = true;

        if (calculatedAmount > formationPrice) {
            calculatedAmount = formationPrice;
        }
    } else if (!isNaN(formationPrice) && !isNaN(selectedInstallments) && selectedInstallments > 0) {
        calculatedAmount = parseFloat((formationPrice / selectedInstallments).toFixed(2));
    }

    // Set the value of the editable input only if it's currently 0 or empty
    // This prevents overwriting a value the student has entered
    if (initialPaidAmountInput.val() === '0' || initialPaidAmountInput.val() === '') {
        initialPaidAmountInput.val(calculatedAmount.toFixed(2));
    }
    
    // Set the hidden input for validation
    minInitialPaidAmountInput.val(calculatedAmount.toFixed(2));

    if (isFixedFeeApplied && calculatedAmount < formationPrice) {
        fixedFeeNote.show();
    } else {
        fixedFeeNote.hide();
    }

    initialPaidAmountInput.css('transform', 'scale(1.05)');
    setTimeout(() => {
        initialPaidAmountInput.css('transform', 'scale(1)');
    }, 200);
}

            // Form submission with loading
            $('#inscriptionModal form').on('submit', function() {
                const submitBtn = $(this).find('button[type="submit"]');
                showButtonLoading(submitBtn);
            });

            // Handle validation errors on page load
            @if ($errors->any() && old('formation_id'))
                var inscriptionModal = new bootstrap.Modal(document.getElementById('inscriptionModal'));
                inscriptionModal.show();

                // Restore old values from the clicked button's data attributes if validation fails
                // Assuming we can retrieve the original button data if a validation error occurs on submit
                // This is a more complex scenario. For simplicity, we'll try to find the formation by ID.
                var oldFormationId = "{{ old('formation_id') }}";
                if (oldFormationId) {
                    // Find the formation data from the original list (assuming it's still available on the page)
                    const originalCardButton = $(`button.enroll-btn[data-formation-id="${oldFormationId}"]`);
                    if (originalCardButton.length) {
                        var oldFormationTitle = originalCardButton.data('formation-title');
                        var oldFormationPrice = parseFloat(originalCardButton.data('formation-price'));
                        var oldAvailableOptions = originalCardButton.data('available-options');
                        currentFormationCategoryName = originalCardButton.data('category-name'); // Restore category name

                        $('#modalFormationId').val(oldFormationId);
                        $('#modalFormationTitle').text(oldFormationTitle);
                        $('#modalFormationPrice').val(oldFormationPrice);
                        $('#displayFormationPrice').text(oldFormationPrice.toFixed(2));
                        $('#modalCategoryName').val(currentFormationCategoryName); // Restore category name in hidden input

                        var paymentOptionSelect = $('#selected_payment_option');
                        paymentOptionSelect.empty().append('<option value="">{{ __('Sélectionner une modalité') }}</option>');
                        if (oldAvailableOptions && oldAvailableOptions.length > 0) {
                            oldAvailableOptions.sort((a, b) => a - b);
                            $.each(oldAvailableOptions, function(index, option) {
                                let optionText = '';
                                let icon = '';
                                if (option === 1) {
                                    optionText = 'Paiement Complet (1 Versement)';
                                    icon = '💰';
                                } else if (option === 2) {
                                    optionText = '2 Versements';
                                    icon = '💳';
                                } else if (option === 3) {
                                    optionText = '3 Versements';
                                    icon = '📅';
                                } else {
                                    optionText = `${option} Versements`;
                                    icon = '📊';
                                }
                                paymentOptionSelect.append($('<option>', {
                                    value: option,
                                    text: `${icon} ${optionText}`,
                                    selected: (option == "{{ old('selected_payment_option') }}")
                                }));
                            });
                        }
                        $('#selected_payment_option').val("{{ old('selected_payment_option') }}");
                        updateInitialPaymentAmount(); // Recalculate with old selected option

                        $('#payment_method').val("{{ old('payment_method') }}");
                        $('#proof_of_payment').prop('required', true); // Ensure required is set if it was required before
                        $('#notes').val("{{ old('notes') }}");

                        // Animate error fields (add shake animation)
                        $('.invalid-feedback').each(function() {
                            $(this).closest('.mb-4').find('.form-control-modern, .select-modern').addClass('border-danger shake-animation');
                            setTimeout(() => {
                                $(this).closest('.mb-4').find('.form-control-modern, .select-modern').removeClass('shake-animation');
                            }, 500);
                        });
                    }
                }
            @endif

            // Add smooth scrolling to page
            $('html').css('scroll-behavior', 'smooth');

            // Add hover effects to cards
            $('.formation-card').hover(
                function() {
                    $(this).find('.card-title').css('color', '#667eea');
                },
                function() {
                    $(this).find('.card-title').css('color', '#1a202c');
                }
            );

            // Add ripple effect to buttons
            $('.btn-modern').on('click', function(e) {
                const button = $(this);
                const ripple = $('<span class="ripple"></span>');
                
                const rect = this.getBoundingClientRect();
                const size = Math.max(rect.width, rect.height);
                const x = e.clientX - rect.left - size / 2;
                const y = e.clientY - rect.top - size / 2;
                
                ripple.css({
                    position: 'absolute',
                    width: size,
                    height: size,
                    left: x,
                    top: y,
                    background: 'rgba(255, 255, 255, 0.5)',
                    borderRadius: '50%',
                    transform: 'scale(0)',
                    animation: 'ripple 0.6s linear',
                    pointerEvents: 'none'
                });
                
                button.append(ripple);
                
                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });

            // Add loading overlay for better UX
            function showLoadingOverlay() {
                const overlay = $(`
                    <div class="loading-overlay" style="
                        position: fixed;
                        top: 0;
                        left: 0;
                        width: 100%;
                        height: 100%;
                        background: rgba(231, 58, 61, 0.45);
                        z-index: 9999;
                        display: flex;
                        align-items: center;
                        justify-content: center;
                        flex-direction: column;
                    ">
                        <div class="loading-spinner" style="
                            width: 60px;
                            height: 60px;
                            border: 6px solid rgba(255, 255, 255, 0.3);
                            border-top: 6px solid white;
                            border-radius: 50%;
                            animation: spin 1s linear infinite;
                        "></div>
                        <p style="color: white; margin-top: 20px; font-size: 18px; font-weight: 600;">
                            Chargement en cours...
                        </p>
                    </div>
                `);
                $('body').append(overlay);
                
                // Removed the setTimeout to fadeOut, as page navigation will handle removal
                // setTimeout(() => {
                //     overlay.fadeOut(500, () => overlay.remove());
                // }, 2000); 
            }

            // Show loading on form submission
            $('#inscriptionModal form').on('submit', function() {
                showLoadingOverlay();
            });
            // Also show overlay for category filter change (which causes full page reload)
            $('#category_filter').on('change', function() {
                showLoadingOverlay();
            });


            // Add floating action button for quick help
            const fabButton = $(`
                <div class="fab-button" style="
                    position: fixed;
                    bottom: 30px;
                    right: 30px;
                    width: 60px;
                    height: 60px;
                    background: var(--primary-gradient);
                    border-radius: 50%;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: white;
                    font-size: 24px;
                    cursor: pointer;
                    box-shadow: var(--hover-shadow);
                    transition: var(--transition);
                    z-index: 1000;
                " title="Aide">
                    <i class="fas fa-question"></i>
                </div>
            `);
            
            fabButton.hover(
                function() {
                    $(this).css('transform', 'scale(1.1)');
                },
                function() {
                    $(this).css('transform', 'scale(1)');
                }
            );
            
            fabButton.on('click', function() {
                alert('💡 Astuce: Sélectionnez d\'abord une catégorie pour filtrer les formations, puis cliquez sur "S\'inscrire maintenant" pour la formation qui vous intéresse!');
            });
            
            $('body').append(fabButton);

            // Add progress indicator
            const progressBar = $(`
                <div class="progress-bar" style="
                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 0%;
                    height: 4px;
                    background: var(--primary-gradient);
                    z-index: 9999;
                    transition: width 0.3s ease;
                "></div>
            `);
            $('body').append(progressBar);

            // Update progress on scroll
            $(window).on('scroll', function() {
                const scrollTop = $(this).scrollTop();
                const docHeight = $(document).height();
                const winHeight = $(this).height();
                const scrollPercent = (scrollTop / (docHeight - winHeight)) * 100;
                progressBar.css('width', scrollPercent + '%');
            });

            // Add entrance animations for cards
            function animateCards() {
                $('.formation-card').each(function(index) {
                    const card = $(this);
                    setTimeout(() => {
                        card.css({
                            opacity: '0',
                            transform: 'translateY(30px)'
                        }).animate({
                            opacity: '1'
                        }, 500).css({
                            transform: 'translateY(0)',
                            transition: 'transform 0.5s ease'
                        });
                    }, index * 100);
                });
            }
            animateCards(); // Trigger animations on page load

            // Add search functionality
            const searchInput = $(`
                <div class="search-container mb-4">
                    <div class="position-relative">
                        <input type="text" id="formation-search" class="form-control-modern ps-5" 
                               placeholder=" Rechercher une formation...">
                        <i class="fas fa-search position-absolute" style="
                            left: 20px;
                            top: 50%;
                            transform: translateY(-50%);
                            color: #667eea;
                        "></i>
                    </div>
                </div>
            `);
            
            $('.mb-5 h2').after(searchInput);

            // Live search functionality
            $('#formation-search').on('input', function() {
                const searchTerm = $(this).val().toLowerCase();
                $('.formation-card').each(function() {
                    const card = $(this);
                    const title = card.find('.card-title').text().toLowerCase();
                    const description = card.find('.card-text').text().toLowerCase();
                    
                    if (title.includes(searchTerm) || description.includes(searchTerm)) {
                        card.parent().show().css('animation', 'fadeIn 0.3s ease');
                    } else {
                        card.parent().hide();
                    }
                });
            });

            // Add fade in animation CSS
            $('<style>').text(`
                @keyframes fadeIn {
                    from { opacity: 0; transform: translateY(20px); }
                    to { opacity: 1; transform: translateY(0); }
                }
            `).appendTo('head');

        });
    </script>

    @endpush
@endsection