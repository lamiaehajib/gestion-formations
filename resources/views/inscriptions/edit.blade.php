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
                                    <select name="user_id" id="user_id" class="form-select bg-gray-100 cursor-not-allowed" disabled>
                                        <option value="{{ $inscription->user->id }}" selected>{{ $inscription->user->name }} ({{ $inscription->user->email }})</option>
                                    </select>
                                    {{-- Champ caché pour envoyer la valeur réellement --}}
                                    <input type="hidden" name="user_id" value="{{ $inscription->user->id }}">
                                </div>
                            </div>

                            {{-- Champ Formation (Lecture seule) --}}
                            <div class="form-group animate-slide-left">
                                <label for="formation_id" class="form-label label-with-icon">
                                    Formation
                                    <i class="fas fa-book-reader input-icon-label"></i>
                                </label>
                                <div class="input-wrapper">
                                    <select name="formation_id" id="formation_id" class="form-select bg-gray-100 cursor-not-allowed" disabled>
                                        <option value="{{ $inscription->formation->id }}"
                                                data-price="{{ $inscription->formation->price }}"
                                                data-available-options="{{ json_encode($inscription->formation->available_payment_options ?? [1]) }}"
                                                selected>
                                            {{ $inscription->formation->title }} ({{ number_format($inscription->formation->price, 2) }} DH)
                                        </option>
                                    </select>
                                    {{-- Champ caché pour envoyer la valeur réellement --}}
                                    <input type="hidden" name="formation_id" value="{{ $inscription->formation->id }}">
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
                                                {{-- Option to remove existing document (requires JS and Controller logic) --}}
                                                {{-- <button type="button" class="text-red-500 hover:text-red-700 hidden group-hover:inline-block ml-2 text-xs" title="Supprimer ce document" data-doc-path="{{ $docPath }}">
                                                    <i class="fas fa-trash-alt"></i>
                                                </button> --}}
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
        /* CSS Variables with your specified colors */
        :root {
            --primary-red: #D32F2F;
            --dark-pink: #C2185B;
            --light-red: #ef4444;
            --primary-green: #388E3C; /* Added for payment section */
            --light-green: #81C784; /* Added for payment section */
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

        /* Adjust icon color for green section */
        .section-card .text-green-700 + .form-group .input-icon-label,
        .section-card .text-green-500 + .animate-type-writer + .form-group .input-icon-label {
            color: var(--primary-green);
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

        /* Specific styles for disabled inputs */
        .form-select.bg-gray-100, .form-input.bg-gray-100 {
            background-color: #f3f4f6; /* A slightly darker gray for disabled inputs */
            cursor: not-allowed;
            opacity: 0.8;
        }

        /* File Upload */
        .file-upload-wrapper {
            position: relative;
            border: 2px dashed var(--border-light);
            border-radius: 1rem;
            background: var(--bg-light);
            transition: all 0.3s ease;
            overflow: hidden;
            min-height: 80px; /* Ensure a minimum height for the drop zone */
            display: flex;
            align-items: center;
            justify-content: center;
            text-align: center;
            padding: 1rem;
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
            z-index: 10; /* Make sure it's clickable */
        }

        .file-upload-overlay {
            padding: 1rem;
            text-align: center;
            transition: all 0.3s ease;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 100%;
            height: 100%;
        }

        .file-upload-wrapper:hover .file-upload-overlay {
            transform: scale(1.02);
        }

        /* Toggle Switch for Access Restricted */
        .form-check {
            min-height: 1.25rem; /* Ensure enough height for the switch */
            padding-left: 2.5em; /* Space for the custom switch */
            position: relative; /* For custom positioning */
            display: flex;
            align-items: center;
            height: 100%; /* Fill the parent height */
        }

        .form-check-input {
            float: left;
            margin-left: -2.5em; /* Offset for switch */
            width: 2em; /* Width of the switch itself */
            height: 1em; /* Height of the switch itself */
            vertical-align: top;
            background-color: #bcc1c7; /* Default gray for switch */
            border: 1px solid rgba(0, 0, 0, 0.25);
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            border-radius: 0.5em; /* Rounded corners */
            transition: background-color 0.25s ease-in-out, border-color 0.25s ease-in-out, box-shadow 0.25s ease-in-out;
            margin-right: 0.75rem; /* Space between switch and label text */
            flex-shrink: 0; /* Prevent shrinking */
        }

        .form-check-input:checked {
            background-color: var(--primary-red); /* Red when checked */
            border-color: var(--primary-red);
        }

        .form-check-input:focus {
            outline: 0;
            box-shadow: 0 0 0 0.25rem rgba(211, 47, 47, 0.25);
        }

        .form-check-input:after {
            content: "";
            display: block;
            width: calc(1em - 2px);
            height: calc(1em - 2px);
            background-color: #fff;
            border-radius: 0.5em;
            transition: transform 0.25s ease-in-out;
            transform: translateX(0);
        }

        .form-check-input:checked:after {
            transform: translateX(100%);
        }

        .form-check-label {
            font-size: 1rem;
            color: var(--text-dark);
            cursor: pointer;
            padding-left: 0.5rem; /* space between input and text */
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

        /* Animated Alerts */
        .animated-alert {
            animation: slide-in-top 0.5s ease-out;
        }

        .alert-success {
            background-color: #d4edda;
            border-color: #c3e6cb;
            color: #155724;
        }

        .alert-error {
            background-color: #f8d7da;
            border-color: #f5c6cb;
            color: #721c24;
        }

        @keyframes slide-in-top {
            from { transform: translateY(-20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Add fade-out animation for alerts */
        .fade-out {
            opacity: 0;
            transition: opacity 0.5s ease-out;
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
            const formationSelect = document.getElementById('formation_id'); // This is disabled, but still useful for data.
            const chosenInstallmentsSelect = document.getElementById('chosen_installments');
            const inscriptionForm = document.getElementById('inscription-form');
            const submitBtn = document.getElementById('submit-btn');

            // Add input focus animations
            const inputs = document.querySelectorAll('.form-select, .form-input, .form-textarea');
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    // Only apply transform if the input is not disabled
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
                // Fetch data from the hidden formation_id input (or the disabled select if it has data-attributes)
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
                    // Fallback: If no options are available from data-attribute, add default 1 installment
                    const defaultOption = document.createElement('option');
                    defaultOption.value = "1";
                    defaultOption.textContent = "Paiement Complet (1 Versement)";
                    chosenInstallmentsSelect.appendChild(defaultOption);
                    if (1 == currentChosen) {
                        defaultOption.selected = true;
                    }
                }
            }

            // Initial call on page load to populate installment options
            // since formation_id is disabled, its value won't change, so this runs once.
            if (formationSelect.value) {
                updateChosenInstallmentsOptions();
            }

            // Handle form submission loading state
            inscriptionForm.addEventListener('submit', function() {
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
                    alert.classList.add('fade-out'); // Add a class for fade out animation
                    setTimeout(() => alert.remove(), 500); // Remove after animation
                });
            }, 5000);

            // File upload animation for new_receipt_file and documents
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
                            if (fileNames.length > 50) { // Truncate long file name list for display
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