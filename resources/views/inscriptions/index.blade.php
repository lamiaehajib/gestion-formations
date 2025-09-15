@extends('layouts.app')

@section('title', 'Gestion des Inscriptions')

@section('content')

<div>
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        {{-- En-tête avec Breadcrumbs et boutons --}}
        <div class="mb-8 flex flex-col sm:flex-row justify-between items-center animated-header">
            {{-- Breadcrumbs --}}
            <nav aria-label="breadcrumb" class="mb-4 sm:mb-0">
                <ol class="flex items-center space-x-2 text-sm text-gray-500">
                    <li class="flex items-center text-gray-700 font-semibold">
                        <div class="icon-container mr-2">
                            <i class="fas fa-user-graduate floating-icon"></i>
                        </div>
                        Inscriptions
                    </li>
                </ol>
            </nav>
            
            {{-- Titre et actions --}}
            <div class="flex flex-col sm:flex-row items-center space-y-3 sm:space-y-0 sm:space-x-4 w-full sm:w-auto justify-between sm:justify-start">
                <h1 class="text-4xl font-extrabold bg-gradient-to-r from-[#D32F2F] via-[#C2185B] to-[#ef4444] bg-clip-text text-transparent animate-text-glow">
                    Explorer les Inscriptions
                </h1>
                
                <div class="flex space-x-3">
                    @can('inscription-create')
                    <a href="{{ route('inscriptions.create') }}" class="btn-new-inscription group">
                        <div class="icon-wrapper">
                            <i class="fas fa-plus-circle group-hover:rotate-90 transition-all duration-300"></i>
                        </div>
                        Nouvelle Inscription
                    </a>
                    @endcan
                    @can('inscription-create')
                    <a href="{{ route('inscriptions.export', request()->all()) }}" class="btn-export-csv group">
                        <div class="icon-wrapper">
                            <i class="fas fa-download group-hover:animate-bounce"></i>
                        </div>
                        Exporter CSV
                    </a>
                    @endcan
                </div>
            </div>
        </div>

        {{-- Filtres et Recherche --}}
        <div class="interactive-card p-6 mb-8 animated-section">
            <form action="{{ route('inscriptions.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6 items-end">
                
                {{-- Champ de recherche étudiant (visible uniquement pour Admin/Finance/Super Admin) --}}
                @if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']))
                <div class="input-group">
                    <label for="search" class="input-label">Chercher un Étudiant</label>
                    <div class="input-container">
                        <input type="text" name="search" id="search" class="input-styled" placeholder="Nom ou email de l'étudiant" value="{{ request('search') }}">
                        <div class="input-icon">
                            <i class="fas fa-search pulse-icon"></i>
                        </div>
                    </div>
                </div>
                @endif

                <div class="input-group">
                    <label for="status" class="input-label">Filtrer par Statut</label>
                    <div class="select-container">
                        <select name="status" id="status" class="select-styled">
                            <option value="">Tous les statuts</option>
                            @foreach($availableStatuses as $statusOption)
                                <option value="{{ $statusOption }}" {{ request('status') == $statusOption ? 'selected' : '' }}>{{ ucfirst($statusOption) }}</option>
                            @endforeach
                        </select>
                        <div class="select-icon">
                            <i class="fas fa-chevron-down spin-on-hover"></i>
                        </div>
                    </div>
                </div>

                <div class="input-group">
                    <label for="formation_id" class="input-label">Filtrer par Formation</label>
                    <div class="select-container">
                        <select name="formation_id" id="formation_id" class="select-styled">
                            <option value="">Toutes les formations</option>
                            @foreach($availableFormations as $formation)
                                <option value="{{ $formation->id }}" {{ request('formation_id') == $formation->id ? 'selected' : '' }}>{{ $formation->title }}</option>
                            @endforeach
                        </select>
                        <div class="select-icon">
                            <i class="fas fa-book-open floating-icon"></i>
                        </div>
                    </div>
                </div>

                <div class="col-span-full md:col-span-1 lg:col-span-1 flex justify-end gap-2">
                    <button type="submit" class="btn-filter group">
                        <div class="icon-wrapper">
                            <i class="fas fa-filter group-hover:rotate-12 transition-all duration-300"></i>
                        </div>
                        
                    </button>
                    <a href="{{ route('inscriptions.index') }}" class="btn-reset group">
                        <div class="icon-wrapper">
                            <i class="fas fa-redo group-hover:rotate-180 transition-all duration-500"></i>
                        </div>
                        
                    </a>
                </div>
            </form>
        </div>

        {{-- Messages de succès/erreur --}}
        @if (session('success'))
            <div class="alert-success animated-alert">
                <div class="alert-icon">
                    <i class="fas fa-check-circle pulse-success"></i>
                </div>
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif
        @if (session('error'))
            <div class="alert-error animated-alert">
                <div class="alert-icon">
                    <i class="fas fa-times-circle shake-error"></i>
                </div>
                <span class="block sm:inline">{{ session('error') }}</span>
            </div>
        @endif

        {{-- Cartes d'Inscription --}}
        <div class="inscription-grid animated-section">
            @if($inscriptions->count() > 0)
                @foreach($inscriptions as $inscription)
                <div class="inscription-card" data-id="{{ $inscription->id }}">
                    <div class="card-header">
                        <div class="user-avatar">
                            <i class="fas fa-user-circle"></i>
                        </div>
                        <div class="user-details">
                            <h3 class="user-name">{{ $inscription->user->name ?? 'N/A' }}</h3>
                            <p class="user-email">{{ $inscription->user->email ?? 'N/A' }}</p>
                        </div>
                        @php
                            $statusConfig = [
                                'pending' => ['class' => 'status-badge-pending', 'icon' => 'fas fa-clock'],
                                'active' => ['class' => 'status-badge-active', 'icon' => 'fas fa-check-circle'],
                                'completed' => ['class' => 'status-badge-completed', 'icon' => 'fas fa-trophy'],
                                'cancelled' => ['class' => 'status-badge-cancelled', 'icon' => 'fas fa-times-circle'],
                            ][$inscription->status] ?? ['class' => 'status-badge-default', 'icon' => 'fas fa-question-circle'];
                        @endphp
                        <div class="status-badge {{ $statusConfig['class'] }}">
                            <i class="{{ $statusConfig['icon'] }}"></i>
                            {{ ucfirst($inscription->status) }}
                        </div>
                    </div>
                    
                    <div class="card-body">
                        <div class="info-item">
                            <i class="fas fa-graduation-cap info-icon"></i>
                            <span class="info-label">Formation:</span>
                            <span class="info-value">{{ $inscription->formation->title ?? 'N/A' }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-calendar-alt info-icon"></i>
                            <span class="info-label">Date:</span>
                            <span class="info-value">{{ $inscription->inscription_date->format('d/m/Y') }}</span>
                        </div>
                        <div class="info-item">
                            <i class="fas fa-money-bill-wave info-icon"></i>
                            <span class="info-label">Paiement:</span>
                            <span class="info-value">{{ number_format($inscription->paid_amount, 2) }} DH</span>
                            <span class="info-sub-value">(Reste: {{ number_format($inscription->total_amount - $inscription->paid_amount, 2) }} DH)</span>
                        </div>
                    </div>

                    <div class="card-actions-collapsed">
                        <button class="expand-card-btn btn-action-icon" title="Voir plus de détails">
                            <i class="fas fa-chevron-down"></i>
                        </button>
                    </div>

                    {{-- Détails et Actions cachés (expandable) --}}
                    <div class="card-expanded-details hidden">
                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fas fa-info-circle"></i> Détails d'Inscription</h4>
                            <ul>
                                <li><strong>ID Inscription:</strong> {{ $inscription->id }}</li>
                                <li><strong>Statut:</strong> 
                                    @if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']))
                                            <div class="status-select-wrapper inline-block ml-2">
                                                <select class="status-select select-inline" data-inscription-id="{{ $inscription->id }}">
                                                    <option value="pending" {{ $inscription->status == 'pending' ? 'selected' : '' }}>En attente</option>
                                                    <option value="active" {{ $inscription->status == 'active' ? 'selected' : '' }}>Active</option>
                                                    <option value="completed" {{ $inscription->status == 'completed' ? 'selected' : '' }}>Terminée</option>
                                                    <option value="cancelled" {{ $inscription->status == 'cancelled' ? 'selected' : '' }}>Annulée</option>
                                                </select>
                                            </div>
                                    @else
                                        <span class="status-badge {{ $statusConfig['class'] }} ml-2">{{ ucfirst($inscription->status) }}</span>
                                    @endif
                                </li>
                                <li><strong>Versements:</strong> {{ $inscription->chosen_installments }}</li>
                                <li><strong>Montant Total:</strong> {{ number_format($inscription->total_amount, 2) }} DH</li>
                                <li><strong>Montant Restant:</strong> {{ number_format($inscription->total_amount - $inscription->paid_amount, 2) }} DH</li>
                                <li><strong>inscrit par:</strong> {{ $inscription->inscrit_par }}</li>
                            </ul>
                        </div>

                        <div class="detail-section">
                            <h4 class="detail-title"><i class="fas fa-receipt"></i> Reçus de Paiement</h4>
                            @if($inscription->payments->count() > 0)
                                <ul>
                                    @foreach($inscription->payments->sortBy('created_at') as $payment)
                                        <li>
                                            Paiement du {{ $payment->created_at->format('d/m/Y') }}: {{ number_format($payment->amount, 2) }} DH
                                            @if($payment->receipt_path)
                                                <a href="{{ Storage::url($payment->receipt_path) }}" target="_blank" class="text-blue-500 hover:text-blue-700 ml-2" title="Voir reçu"><i class="fas fa-file-download"></i></a>
                                            @else
                                                <span class="text-gray-400 ml-2">(Pas de reçu)</span>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <p class="text-gray-500">Aucun paiement enregistré pour cette inscription.</p>
                            @endif
                        </div>

                        <div class="card-full-actions">
                            @can('inscription-create')
                            <a href="{{ route('inscriptions.showAddPaymentForm', $inscription) }}" class="btn-action-full btn-add-payment">
                                <i class="fas fa-money-check-alt mr-2"></i> Ajouter Paiement
                            </a>
                            @endcan
                            <a href="{{ route('inscriptions.show', $inscription) }}" class="btn-action-full btn-view-details">
                                <i class="fas fa-info-circle mr-2"></i> Voir les détails
                            </a>
                            @if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']))
                            <a href="{{ route('inscriptions.edit', $inscription) }}" class="btn-action-full btn-edit">
                                <i class="fas fa-edit mr-2"></i> Modifier
                            </a>
                            {{-- ICI LE FORMULAIRE DE SUPPRESSION QUI SERA INTERCEPTE PAR LE MODAL --}}
                            <form action="{{ route('inscriptions.destroy', $inscription) }}" method="POST">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-action-full btn-delete">
                                    <i class="fas fa-trash-alt mr-2"></i> Supprimer
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
                @endforeach
            @else
                <div class="empty-state col-span-full">
                    <div class="empty-icon">
                        <i class="fas fa-inbox floating-icon"></i>
                    </div>
                    <h3>Aucune inscription trouvée</h3>
                    <p>Il n'y a pas d'inscriptions correspondant à vos critères de recherche ou de filtre.</p>
                    <a href="{{ route('inscriptions.index') }}" class="btn-reset mt-6 inline-flex items-center gap-2">
                        <i class="fas fa-redo"></i> Réinitialiser les filtres
                    </a>
                </div>
            @endif
        </div>

        <div class="pagination-wrapper">
            {{ $inscriptions->links() }}
        </div>

        {{-- Résumé des statistiques --}}
        @if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']))
        <div class="stats-grid">
            <div class="stat-card stat-card-total">
                <div class="stat-icon">
                    <i class="fas fa-users floating-icon"></i>
                </div>
                <div class="stat-content">
                    <h5 class="stat-number">{{ $inscriptions->total() }}</h5>
                    <p class="stat-label">Total Inscriptions</p>
                </div>
                <div class="stat-glow stat-glow-red"></div>
            </div>
            <div class="stat-card stat-card-active">
                <div class="stat-icon">
                    <i class="fas fa-check-circle pulse-icon"></i>
                </div>
                <div class="stat-content">
                    <h5 class="stat-number">{{ $inscriptions->where('status', 'active')->count() }}</h5>
                    <p class="stat-label">Actives</p>
                </div>
                <div class="stat-glow stat-glow-green"></div>
            </div>
            <div class="stat-card stat-card-pending">
                <div class="stat-icon">
                    <i class="fas fa-clock rotating-slow"></i>
                </div>
                <div class="stat-content">
                    <h5 class="stat-number">{{ $inscriptions->where('status', 'pending')->count() }}</h5>
                    <p class="stat-label">En Attente</p>
                </div>
                <div class="stat-glow stat-glow-yellow"></div>
            </div>
            <div class="stat-card stat-card-completed">
                <div class="stat-icon">
                    <i class="fas fa-trophy bounce-icon"></i>
                </div>
                <div class="stat-content">
                    <h5 class="stat-number">{{ $inscriptions->where('status', 'completed')->count() }}</h5>
                    <p class="stat-label">Terminées</p>
                </div>
                <div class="stat-glow stat-glow-red-new"></div> {{-- Changed to red-new --}}
            </div>
        </div>
        @endif

    </div>
   
</div>
 @if($isAdminOrFinanceOrSuperAdmin)
    <div class="stats-card mt-8 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
        @foreach($inscriptionCountsByAgent as $agent => $count)
            <div class="bg-white p-4 rounded-lg shadow-md text-center border-l-4 border-red-500">
                <p class="text-sm text-gray-500 font-semibold">{{ $agent }}</p>
                <p class="text-2xl font-bold text-gray-800 mt-1">{{ $count }}</p>
                <p class="text-xs text-gray-400">Inscriptions</p>
            </div>
        @endforeach
    </div>
@endif
{{-- MODAL DE CONFIRMATION UNIVERSEL --}}
<div id="confirmation-modal" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden animate-fade-in" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-gray-900 bg-opacity-75 transition-opacity animate-fade-in-bg" aria-hidden="true"></div>

    <div class="relative bg-white dark:bg-gray-800 rounded-xl shadow-2xl transform transition-all sm:max-w-lg sm:w-full p-6 space-y-6 animate-scale-in border border-gray-200 dark:border-gray-700 glassmorphism-modal">
        <div class="flex flex-col items-center justify-center text-center">
            <div id="modal-icon-container" class="mx-auto flex-shrink-0 flex items-center justify-center h-20 w-20 rounded-full bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-3xl transition-all duration-300 ease-in-out transform scale-0 opacity-0 animate-icon-bounce">
                <i id="modal-icon" class="fas fa-exclamation-triangle"></i>
            </div>
            <h3 class="mt-5 text-2xl leading-8 font-extrabold text-gray-900 dark:text-white" id="modal-title">
                Confirmer l'action
            </h3>
            <div class="mt-2">
                <p id="modal-message" class="text-lg text-gray-500 dark:text-gray-400">
                    Êtes-vous sûr de vouloir effectuer cette action ? Cette opération pourrait être irréversible.
                </p>
            </div>
        </div>
        <div class="mt-5 sm:mt-6 sm:flex sm:flex-row-reverse sm:gap-4">
            <button type="button" id="confirm-button" class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-6 py-3 bg-red-600 text-lg font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-4 focus:ring-red-500 focus:ring-opacity-50 transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105 active:scale-95 btn-modal-action">
                Confirmer
            </button>
            <button type="button" id="cancel-button" class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-6 py-3 bg-white text-lg font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-4 focus:ring-indigo-500 focus:ring-opacity-50 transition-all duration-300 ease-in-out transform hover:-translate-y-1 hover:scale-105 active:scale-95 sm:mt-0 btn-modal-cancel">
                Annuler
            </button>
        </div>
    </div>
</div>

@push('styles')
<style>
   
    :root {
        --primary-color: #D32F2F; /* Red-700 */
        --secondary-color: #C2185B; /* Pink-700 */
        --text-dark: #1f2937; /* Gray-900 */
        --text-medium: #4b5563; /* Gray-700 */
        --text-light: #6b7280; /* Gray-500 */
        
        --card-bg: rgba(255, 255, 255, 0.8);
        --card-border: rgba(255, 255, 255, 0.5);
        --card-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        
        --border-radius-lg: 1.25rem; /* 20px */
        --border-radius-md: 0.75rem; /* 12px */
        --transition-ease: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* Base Layout & Background */
    .bg-gradient-to-br {
        background: linear-gradient(to bottom right, #ffe0e0, #ffe0e0, #f0f0ff); /* Light red/pink to light blue/purple */
    }

    /* Animated Header */
    .animated-header, .animated-section, .animated-alert {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInSlideUp 0.8s ease-out forwards;
    }

    .animated-header { animation-delay: 0.1s; }
    .animated-section:nth-of-type(1) { animation-delay: 0.3s; } /* Filters */
    .animated-section:nth-of-type(2) { animation-delay: 0.5s; } /* Inscription Cards */
    .animated-alert { animation-delay: 0.7s; }

    @keyframes fadeInSlideUp {
        to { opacity: 1; transform: translateY(0); }
    }

    /* Text Glow */
    .animate-text-glow {
        animation: textGlow 2s ease-in-out infinite alternate;
    }
    @keyframes textGlow {
        from { text-shadow: 0 0 10px rgba(241, 69, 69, 0.4); }
        to { text-shadow: 0 0 20px rgba(241, 95, 95, 0.6), 0 0 30px rgba(194, 24, 91, 0.4); } /* Adjusted for new pink */
    }

    /* General Buttons */
    .btn-base {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 24px;
        font-weight: 600;
        border-radius: var(--border-radius-md);
        transition: var(--transition-ease);
        text-decoration: none;
        border: none;
        cursor: pointer;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-size: 0.875rem;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    }

    .btn-new-inscription {
        background: linear-gradient(to right, #D32F2F, #C2185B); /* New colors */
        color: white;
    }
    .btn-new-inscription:hover {
        box-shadow: 0 8px 25px rgba(211, 47, 47, 0.4); /* Red-focused shadow */
        transform: translateY(-1px) scale(1.02);
    }

    .btn-export-csv {
        @apply bg-white text-gray-700 border border-gray-300;
    }
    .btn-export-csv:hover {
        @apply bg-gray-50 shadow-md transform -translate-y-1 scale-102;
    }

    .btn-filter {
        @apply bg-blue-500 text-white;
    }
    .btn-filter:hover {
        @apply bg-blue-600 shadow-md transform -translate-y-1;
    }

    .btn-reset {
        @apply bg-gray-200 text-gray-700;
    }
    .btn-reset:hover {
        @apply bg-gray-300 shadow-md transform -translate-y-1;
    }

    /* Icon Wrappers & Animations */
    .icon-wrapper {
        display: inline-flex; align-items: center; justify-content: center;
        width: 20px; height: 20px;
        transition: var(--transition-ease);
    }
    .icon-container {
        display: inline-flex; align-items: center; justify-content: center;
        width: 32px; height: 32px; border-radius: 8px;
        background: linear-gradient(135deg, #ef4444 0%, #D32F2F 100%); /* Adjusted for new colors */
        color: white;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3); /* Adjusted for new colors */
    }

    /* Specific Icon Animations */
    .floating-icon { animation: float 3s ease-in-out infinite; }
    @keyframes float { 0%, 100% { transform: translateY(0px); } 50% { transform: translateY(-6px); } }

    .pulse-icon { animation: pulse 2s ease-in-out infinite; }
    @keyframes pulse { 0%, 100% { transform: scale(1); opacity: 1; } 50% { transform: scale(1.1); opacity: 0.8; } }

    .spin-on-hover { transition: var(--transition-ease); }
    .select-container:hover .spin-on-hover { transform: rotate(180deg); }

    /* Input & Select Styling */
    .interactive-card {
        background: var(--card-bg);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border-radius: var(--border-radius-lg);
        border: 1px solid var(--card-border);
        box-shadow: var(--card-shadow);
        transition: var(--transition-ease);
        position: relative;
        overflow: hidden;
    }
    .input-group { position: relative; }
    .input-label {
        display: block; font-size: 0.875rem; font-weight: 600;
        color: var(--text-medium); margin-bottom: 6px;
        text-transform: uppercase; letter-spacing: 0.5px;
    }
    .input-container, .select-container {
        position: relative; display: flex; align-items: center;
    }
    .input-styled, .select-styled {
        width: 100%; padding: 12px 16px 12px 44px;
        font-size: 1rem; color: var(--text-dark);
        background: rgba(255, 255, 255, 0.9);
        border: 2px solid rgba(200, 200, 200, 0.3);
        border-radius: var(--border-radius-md);
        transition: var(--transition-ease);
        appearance: none;
    }
    .input-styled:focus, .select-styled:focus {
        outline: none; border-color: var(--primary-color);
        box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.1); /* Adjusted for new colors */
        background: rgba(255, 255, 255, 1);
    }
    .input-icon, .select-icon {
        position: absolute; left: 14px; color: var(--text-light);
        z-index: 2; transition: var(--transition-ease);
    }
    .input-container:focus-within .input-icon,
    .select-container:focus-within .select-icon {
        color: var(--primary-color); transform: scale(1.1);
    }

    /* Alert Messages */
    .alert-success, .alert-error {
        display: flex; align-items: center; gap: 12px;
        padding: 16px 20px; margin-bottom: 20px;
        border-radius: var(--border-radius-md); font-weight: 500;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        backdrop-filter: blur(8px);
    }
    .alert-success { background: rgba(110, 231, 183, 0.15); border: 1px solid rgba(52, 211, 153, 0.3); color: #065f46; }
    .alert-error { background: rgba(252, 165, 165, 0.15); border: 1px solid rgba(239, 68, 68, 0.3); color: #991b1b; }
    .alert-icon { font-size: 1.5rem; opacity: 0.8; }
    .pulse-success { animation: pulseSuccess 1.5s infinite; }
    @keyframes pulseSuccess { 0% { transform: scale(1); } 50% { transform: scale(1.1); } 100% { transform: scale(1); } }
    .shake-error { animation: shakeError 0.5s cubic-bezier(.36,.07,.19,.97) both; transform: translate3d(0, 0, 0); backface-visibility: hidden; perspective: 1000px; }
    @keyframes shakeError { 10%, 90% { transform: translate3d(-1px, 0, 0); } 20%, 80% { transform: translate3d(2px, 0, 0); } 30%, 50%, 70% { transform: translate3d(-4px, 0, 0); } 40%, 60% { transform: translate3d(4px, 0, 0); } }

    /* Inscription Card Grid */
    .inscription-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
        gap: 24px;
        margin-top: 32px;
    }

j    .inscription-card {
        background: var(--card-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: var(--border-radius-lg);
        border: 1px solid var(--card-border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        transition: var(--transition-ease);
        overflow: hidden;
        display: flex;
        flex-direction: column;
        cursor: pointer; /* Indicates interactivity */
        position: relative;
    }

    .inscription-card:hover {
        transform: translateY(-8px) scale(1.01);
        box-shadow: 0 12px 40px rgba(0, 0, 0, 0.15);
    }

    .card-header {
        display: flex;
        align-items: center;
        padding: 20px;
        background: rgba(255, 255, 255, 0.9);
        border-bottom: 1px solid rgba(255, 255, 255, 0.4);
    }
    .user-avatar {
        font-size: 2.5rem;
        color: var(--primary-color);
        margin-right: 16px;
    }
    .user-details {
        flex-grow: 1;
    }
    .user-name {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 2px;
    }
    .user-email {
        font-size: 0.9rem;
        color: var(--text-light);
    }

    /* Status Badges */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 9999px; /* Pill shape */
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: capitalize;
        white-space: nowrap;
        animation: pulseStatus 2s infinite ease-in-out;
    }

    @keyframes pulseStatus {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.8; }
    }

    .status-badge-pending { background-color: #fcd34d; color: #b45309; } /* Amber-400 */
    .status-badge-active { background-color: #6ee7b7; color: #065f46; } /* Emerald-300 */
    .status-badge-completed { background-color: #ef4444; color: white; } /* Changed to ef4444 */
    .status-badge-cancelled { background-color: #fda4af; color: #9f1239; } /* Rose-300 */

    .card-body {
        padding: 20px;
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        gap: 12px;
    }
    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        font-size: 0.95rem;
        color: var(--text-medium);
    }
    .info-icon {
        color: var(--primary-color);
        font-size: 1.1em;
        width: 20px; /* fixed width for alignment */
        text-align: center;
    }
    .info-label {
        font-weight: 600;
    }
    .info-value {
        color: var(--text-dark);
        font-weight: 500;
    }
    .info-sub-value {
        font-size: 0.85rem;
        color: var(--text-light);
        margin-left: auto;
    }

    .card-actions-collapsed {
        display: flex;
        justify-content: center;
        padding: 10px;
        border-top: 1px solid rgba(255, 255, 255, 0.4);
        background: rgba(255, 255, 255, 0.9);
    }
    .expand-card-btn {
        background: none;
        border: none;
        color: var(--primary-color);
        font-size: 1.5rem;
        cursor: pointer;
        transition: transform 0.3s ease-in-out;
        padding: 8px;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .inscription-card.expanded .expand-card-btn {
        transform: rotate(180deg);
    }
    .expand-card-btn:hover {
        background-color: rgba(211, 47, 47, 0.1); /* Adjusted for new color */
    }

    /* Expanded Details */
    .card-expanded-details {
        padding: 0 20px 20px;
        background: rgba(255, 255, 255, 0.85);
        border-top: 1px dashed rgba(255, 255, 255, 0.5);
        display: none; /* Controlled by JS */
        opacity: 0;
        max-height: 0;
        transition: max-height 0.5s ease-out, opacity 0.5s ease-out, padding 0.5s ease-out;
        overflow: hidden;
    }

    .inscription-card.expanded .card-expanded-details {
        display: block; /* Show with JS */
        opacity: 1;
        max-height: 500px; /* Sufficient height for content */
        padding: 20px;
    }

    .detail-section {
        margin-bottom: 20px;
    }
    .detail-section:last-of-type {
        margin-bottom: 0;
    }
    .detail-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 10px;
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .detail-title i {
        color: var(--secondary-color);
    }
    .detail-section ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .detail-section ul li {
        font-size: 0.9rem;
        color: var(--text-medium);
        margin-bottom: 6px;
        display: flex;
        align-items: center;
    }
    .detail-section ul li strong {
        color: var(--text-dark);
        margin-right: 5px;
        flex-shrink: 0;
    }
    .select-inline {
        padding: 6px 25px 6px 10px;
        font-size: 0.85rem;
        border-radius: 6px;
        min-width: 100px;
    }
    .select-inline + .select-icon {
        font-size: 0.8rem;
        right: 8px;
        left: unset;
    }

    .card-full-actions {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
        gap: 10px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px dashed rgba(255, 255, 255, 0.5);
    }
    .btn-action-full {
        @apply flex items-center justify-center p-3 font-semibold rounded-lg transition-all duration-300 ease-in-out text-sm;
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .btn-add-payment { @apply bg-blue-500 text-white hover:bg-blue-600; }
    .btn-view-details { background-color: #C2185B; color: white; } /* Changed to Pink-700 */
    .btn-edit { @apply bg-green-500 text-white hover:bg-green-600; }
    .btn-delete { @apply bg-red-500 text-white hover:bg-red-600; }

    /* Empty State for Cards */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        background: rgba(255, 255, 255, 0.2);
        border-radius: var(--border-radius-lg);
        border: 1px solid var(--card-border);
        box-shadow: var(--card-shadow);
        color: var(--text-medium);
        margin-top: 40px;
    }
    .empty-state .empty-icon {
        font-size: 4rem; color: #a0aec0; margin-bottom: 20px;
        animation: float 4s ease-in-out infinite alternate;
    }
    .empty-state h3 { font-size: 1.875rem; font-weight: 700; color: var(--text-dark); margin-bottom: 10px; }
    .empty-state p { font-size: 1.125rem; max-width: 500px; margin: 0 auto; }

    /* Pagination */
    .pagination-wrapper {
        margin-top: 24px;
        display: flex;
        justify-content: center;
    }
    .pagination-wrapper nav { display: flex; gap: 8px; }
    .pagination-wrapper .pagination-link {
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 40px; height: 40px; padding: 0 10px;
        border-radius: var(--border-radius-md);
        background: rgba(255, 255, 255, 0.6);
        border: 1px solid rgba(200, 200, 200, 0.3);
        color: var(--text-dark); font-weight: 600;
        transition: var(--transition-ease); text-decoration: none;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
    .pagination-wrapper .pagination-link:hover:not(.active) {
        background: rgba(255, 255, 255, 0.8); border-color: var(--primary-color);
        transform: translateY(-2px);
    }
    .pagination-wrapper .pagination-link.active {
        background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
        color: white; border-color: var(--primary-color);
        cursor: default; box-shadow: 0 4px 15px rgba(211, 47, 47, 0.4); /* Adjusted for new colors */
    }
    .pagination-wrapper .pagination-link svg { width: 20px; height: 20px; }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 24px;
        margin-top: 40px;
    }
    @media (min-width: 640px) { .stats-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (min-width: 1024px) { .stats-grid { grid-template-columns: repeat(4, 1fr); } }

    .stat-card {
        background: var(--card-bg);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-radius: var(--border-radius-lg);
        border: 1px solid var(--card-border);
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 24px;
        display: flex;
        align-items: center;
        gap: 20px;
        position: relative;
        overflow: hidden;
        transition: var(--transition-ease);
    }
    .stat-card:hover { transform: translateY(-6px) scale(1.02); box-shadow: 0 10px 30px rgba(0, 0, 0, 0.12); }
    .stat-card .stat-icon {
        font-size: 2.5rem; width: 60px; height: 60px; border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        color: white; background: linear-gradient(45deg, #ef4444, #D32F2F); /* Adjusted for new colors */
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4); /* Adjusted for new colors */
    }
    .stat-card-total .stat-icon { background: linear-gradient(45deg, #ef4444, #dc2626); } /* Red */
    .stat-card-active .stat-icon { background: linear-gradient(45deg, #34d399, #10b981); } /* Green */
    .stat-card-pending .stat-icon { background: linear-gradient(45deg, #fbbf24, #f59e0b); } /* Amber */
    .stat-card-completed .stat-icon { background: linear-gradient(45deg, #C2185B, #ef4444); } /* Changed to C2185B and ef4444 */

    .stat-card .stat-content { flex-grow: 1; }
    .stat-card .stat-number {
        font-size: 2.5rem; font-weight: 800; color: var(--text-dark);
        line-height: 1; margin-bottom: 4px;
    }
    .stat-card .stat-label {
        font-size: 0.9rem; color: var(--text-medium);
        text-transform: uppercase; letter-spacing: 0.05em; font-weight: 600;
    }
    .stat-glow {
        position: absolute; bottom: -20px; right: -20px;
        width: 100px; height: 100px; border-radius: 50%;
        filter: blur(40px); opacity: 0.6; z-index: 0;
    }
    .stat-glow-red { background: radial-gradient(circle, #f87171, transparent 60%); }
    .stat-glow-green { background: radial-gradient(circle, #34d399, transparent 60%); }
    .stat-glow-yellow { background: radial-gradient(circle, #fbbf24, transparent 60%); }
    .stat-glow-red-new { background: radial-gradient(circle, #D32F2F, transparent 60%); } /* New stat-glow for completed */

    /* Responsive adjustments for smaller screens */
    @media (max-width: 768px) {
        .inscription-grid {
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        }
        .card-header { padding: 15px; }
        .user-name { font-size: 1.1rem; }
        .user-email { font-size: 0.8rem; }
        .status-badge { font-size: 0.7rem; padding: 4px 10px; }
        .card-body { padding: 15px; gap: 8px; }
        .info-item { font-size: 0.9rem; }
        .detail-section ul li { font-size: 0.85rem; }
        .btn-action-full { padding: 10px 15px; font-size: 0.8rem; }
        .stats-grid { grid-template-columns: repeat(1, 1fr); }
    }

    @media (max-width: 640px) {
        .flex-col.sm:flex-row { flex-direction: column; align-items: flex-start; }
        .flex.space-x-3 { flex-direction: column; gap: 12px; width: 100%; margin-top: 1rem; }
        .btn-base { width: 100%; justify-content: center; padding: 10px 20px; font-size: 0.8rem; }
        .glass-card form { grid-template-columns: 1fr; }
        .col-span-full.md:col-span-1.lg:col-span-1 { justify-content: stretch; flex-direction: column; gap: 12px; }
        .col-span-full.md:col-span-1.lg:col-span-1 button,
        .col-span-full.md:col-span-1.lg:col-span-1 a { width: 100%; justify-content: center; }
    }


    /* Variables CSS Modernes et Avancées */
:root {
    /* Gradients Ultra Modernes */
    --primary-gradient: linear-gradient(135deg, #D32F2F 0%, #C2185B 100%);
    --secondary-gradient: linear-gradient(135deg, #ef4444 0%, #C2185B 100%); /* Adjusted to new colors */
    --accent-gradient: linear-gradient(135deg, #ef4444 0%, #D32F2F 100%); /* Adjusted to new colors */
    --success-gradient: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
    --warning-gradient: linear-gradient(135deg, #fa709a 0%, #fee140 100%);
    --danger-gradient: linear-gradient(135deg, #ff9a9e 0%, #fecfef 100%);
    --red-gradient: linear-gradient(135deg, #D32F2F 0%, #ef4444 100%); /* New red gradient */
    --ocean-gradient: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%); /* Adjusted for new colors */
    
    /* Glassmorphism */
    --glass-bg: rgba(255, 255, 255, 0.15);
    --glass-bg-strong: rgba(255, 255, 255, 0.25);
    --glass-border: rgba(255, 255, 255, 0.2);
    --glass-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    --glass-shadow-hover: 0 15px 40px 0 rgba(31, 38, 135, 0.5);
    
    /* Texte */
    --text-primary: #1a202c;
    --text-secondary: #2d3748;
    --text-light: #4a5568;
    --text-muted: #718096;
    --text-white: #ffffff;
    
    /* Espacements */
    --space-xs: 0.25rem;
    --space-sm: 0.5rem;
    --space-md: 1rem;
    --space-lg: 1.5rem;
    --space-xl: 2rem;
    --space-2xl: 3rem;
    
    /* Border Radius */
    --radius-sm: 6px;
    --radius-md: 12px;
    --radius-lg: 16px;
    --radius-xl: 24px;
    --radius-2xl: 32px;
    --radius-full: 9999px;
    
    /* Ombres */
    --shadow-sm: 0 2px 8px rgba(0,0,0,0.08);
    --shadow-md: 0 4px 12px rgba(0,0,0,0.1);
    --shadow-lg: 0 8px 25px rgba(0,0,0,0.15);
    --shadow-xl: 0 20px 40px rgba(0,0,0,0.2);
    --shadow-2xl: 0 25px 50px rgba(0,0,0,0.25);
    
    /* Transitions */
    --transition-fast: all 0.15s cubic-bezier(0.4, 0, 0.2, 1);
    --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-slow: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    --transition-bounce: all 0.3s cubic-bezier(0.68, -0.55, 0.265, 1.55);
}

/* Background Ultra Moderne */
.bg-gradient-to-br {
    background: var(--ocean-gradient);
    position: relative;
    overflow: hidden;
}

.bg-gradient-to-br::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: 
        radial-gradient(circle at 20% 50%, rgba(211, 47, 47, 0.3) 0%, transparent 50%), /* Adjusted */
        radial-gradient(circle at 80% 20%, rgba(194, 24, 91, 0.3) 0%, transparent 50%), /* Adjusted */
        radial-gradient(circle at 40% 80%, rgba(239, 68, 68, 0.3) 0%, transparent 50%); /* Adjusted */
    animation: backgroundShift 20s ease-in-out infinite;
    z-index: 0;
}

@keyframes backgroundShift {
    0%, 100% { opacity: 1; transform: translateX(0) translateY(0); }
    33% { opacity: 0.8; transform: translateX(-10px) translateY(10px); }
    66% { opacity: 0.9; transform: translateX(10px) translateY(-5px); }
}

/* Container Principal */
.max-w-7xl {
    position: relative;
    z-index: 1;
}

/* Animations d'Entrée Ultra Smooth */
.animated-header {
    opacity: 0;
    transform: translateY(-30px) scale(0.95);
    animation: fadeInScaleUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
    animation-delay: 0.1s;
}

.animated-section {
    opacity: 0;
    transform: translateY(40px);
    animation: fadeInSlideUp 0.8s cubic-bezier(0.4, 0, 0.2, 1) forwards;
}

.animated-section:nth-of-type(1) { animation-delay: 0.2s; }
.animated-section:nth-of-type(2) { animation-delay: 0.4s; }
.animated-section:nth-of-type(3) { animation-delay: 0.6s; }

.animated-alert {
    opacity: 0;
    transform: scale(0.8) translateY(20px);
    animation: popIn 0.6s cubic-bezier(0.68, -0.55, 0.265, 1.55) forwards;
    animation-delay: 0.8s;
}

@keyframes fadeInScaleUp {
    to {
        opacity: 1;
        transform: translateY(0) scale(1);
    }
}

@keyframes fadeInSlideUp {
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

@keyframes popIn {
    to {
        opacity: 1;
        transform: scale(1) translateY(0);
    }
}

/* Titre avec Effet Holographique */
.animate-text-glow {
    background: linear-gradient(45deg, #D32F2F, #C2185B, #ef4444); /* Adjusted to new colors */
    background-size: 400% 400%;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    animation: holographicGlow 3s ease-in-out infinite;
    text-shadow: 0 0 30px rgba(211, 47, 47, 0.5); /* Adjusted for new colors */
    position: relative;
}

.animate-text-glow::after {
    content: attr(data-text);
    position: absolute;
    top: 0;
    left: 0;
    z-index: -1;
    background: linear-gradient(45deg, #D32F2F, #C2185B, rgb(239, 133, 133), #ef4444); /* Adjusted for new colors */
    background-size: 400% 400%;
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    filter: blur(3px);
    opacity: 0.7;
    animation: holographicGlow 3s ease-in-out infinite reverse;
}

@keyframes holographicGlow {
    0%, 100% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
}

/* Boutons Ultra Modernes */
.btn-new-inscription {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 14px 28px;
    border-radius: var(--radius-lg);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    transition: var(--transition);
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
}

.btn-new-inscription::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: var(--transition);
}

.btn-new-inscription:hover {
    transform: translateY(-3px) scale(1.02);
    box-shadow: var(--shadow-2xl);
}

.btn-new-inscription:hover::before {
    left: 100%;
}

.btn-new-inscription:active {
    transform: translateY(-1px) scale(1.01);
}

.btn-export-csv {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    color: var(--text-primary);
    padding: 14px 28px;
    border-radius: var(--radius-lg);
    font-weight: 600;
    transition: var(--transition);
    display: inline-flex;
    align-items: center;
    gap: 10px;
    text-decoration: none;
    box-shadow: var(--shadow-md);
}

.btn-export-csv:hover {
    background: var(--glass-bg-strong);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

.btn-filter {
    background: var(--accent-gradient);
    color: white;
    border: none;
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-weight: 600;
    transition: var(--transition);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    box-shadow: var(--shadow-md);
}

.btn-filter:hover {
    transform: translateY(-2px) scale(1.05);
    box-shadow: var(--shadow-lg);
}

.btn-reset {
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 1px solid var(--glass-border);
    color: var(--text-secondary);
    padding: 12px 24px;
    border-radius: var(--radius-md);
    font-weight: 600;
    transition: var(--transition);
    cursor: pointer;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    text-decoration: none;
    box-shadow: var(--shadow-md);
}

.btn-reset:hover {
    background: var(--glass-bg-strong);
    transform: translateY(-2px);
    box-shadow: var(--shadow-lg);
}

/* Carte Interactive Glassmorphism */
.interactive-card {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    box-shadow: var(--glass-shadow);
    padding: var(--space-xl);
    transition: var(--transition);
    position: relative;
    overflow: hidden;
}

.interactive-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 1px;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.6), transparent);
    animation: shimmer 3s ease-in-out infinite;
}

@keyframes shimmer {
    0%, 100% { opacity: 0; }
    50% { opacity: 1; }
}

.interactive-card:hover {
    background: var(--glass-bg-strong);
    box-shadow: var(--glass-shadow-hover);
    transform: translateY(-2px);
}

/* Inputs et Selects Ultra Modernes */
.input-group {
    position: relative;
    margin-bottom: var(--space-lg);
}

.input-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: var(--space-sm);
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.input-container, .select-container {
    position: relative;
    display: flex;
    align-items: center;
}

.input-styled, .select-styled {
    width: 100%;
    padding: 16px 20px 16px 50px;
    font-size: 1rem;
    color: var(--text-primary);
    background: var(--glass-bg);
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    border: 2px solid var(--glass-border);
    border-radius: var(--radius-lg);
    transition: var(--transition);
    appearance: none;
    box-shadow: var(--shadow-sm);
}

.input-styled:focus, .select-styled:focus {
    outline: none;
    border-color: #D32F2F;
    background: var(--glass-bg-strong);
    box-shadow: 0 0 0 4px rgba(211, 47, 47, 0.1), var(--shadow-md); /* Adjusted */
    transform: translateY(-1px);
}

.input-icon, .select-icon {
    position: absolute;
    left: 18px;
    color: var(--text-light);
    font-size: 1.1rem;
    z-index: 2;
    transition: var(--transition);
}

.input-container:focus-within .input-icon,
.select-container:focus-within .select-icon {
    color: #D32F2F;
    transform: scale(1.1);
}

/* Animations d'Icônes Avancées */
.floating-icon {
    animation: float 3s ease-in-out infinite;
}

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    33% { transform: translateY(-6px) rotate(2deg); }
    66% { transform: translateY(3px) rotate(-1deg); }
}

.pulse-icon {
    animation: pulse 2s ease-in-out infinite;
}

@keyframes pulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.1); opacity: 0.8; }
}

.rotating-slow {
    animation: rotateSlow 4s linear infinite;
}

@keyframes rotateSlow {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.bounce-icon {
    animation: bounce 2s ease-in-out infinite;
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Cartes d'Inscription Ultra Modernes */
.inscription-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: var(--space-xl);
    margin-top: var(--space-2xl);
}

.inscription-card {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    border-radius: var(--radius-xl);
    box-shadow: var(--glass-shadow);
    overflow: hidden;
    transition: var(--transition-slow);
    position: relative;
    cursor: pointer;
    transform-style: preserve-3d;
}

.inscription-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, transparent 50%, rgba(255,255,255,0.05) 100%);
    opacity: 0;
    transition: var(--transition);
    z-index: 1;
}

.inscription-card:hover {
    transform: translateY(-8px) rotateX(5deg) rotateY(5deg);
    box-shadow: var(--shadow-2xl);
}

.inscription-card:hover::before {
    opacity: 1;
}

.card-header {
    display: flex;
    align-items: center;
    padding: var(--space-xl);
    background: var(--glass-bg-strong);
    border-bottom: 1px solid var(--glass-border);
    position: relative;
    z-index: 2;
}

.user-avatar {
    font-size: 3rem;
    color: #D32F2F;
    margin-right: var(--space-lg);
    text-shadow: 0 0 20px rgba(211, 47, 47, 0.3); /* Adjusted */
    animation: pulse 3s ease-in-out infinite;
}

.user-details {
    flex-grow: 1;
}

.user-name {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--space-xs);
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.user-email {
    font-size: 0.9rem;
    color: var(--text-light);
    opacity: 0.8;
}

/* Status Badges Ultra Modernes */
.status-badge {
    display: inline-flex;
    align-items: center;
    gap: var(--space-sm);
    padding: 8px 16px;
    border-radius: var(--radius-full);
    font-size: 0.75rem;
    font-weight: 600;
    text-transform: capitalize;
    white-space: nowrap;
    position: relative;
    overflow: hidden;
    backdrop-filter: blur(10px);
    -webkit-backdrop-filter: blur(10px);
    box-shadow: var(--shadow-sm);
}

.status-badge::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
    transition: var(--transition);
}

.status-badge:hover::before {
    left: 100%;
}

.status-badge-pending {
    background: linear-gradient(135deg, #fbbf24, #f59e0b);
    color: white;
    animation: pulsePending 2s ease-in-out infinite;
}

@keyframes pulsePending {
    0%, 100% { box-shadow: 0 0 10px rgba(251, 191, 36, 0.5); }
    50% { box-shadow: 0 0 20px rgba(251, 191, 36, 0.8); }
}

.status-badge-active {
    background: var(--success-gradient);
    color: white;
    animation: pulseActive 2s ease-in-out infinite;
}

@keyframes pulseActive {
    0%, 100% { box-shadow: 0 0 10px rgba(67, 233, 123, 0.5); }
    50% { box-shadow: 0 0 20px rgba(67, 233, 123, 0.8); }
}

.status-badge-completed {
    background: var(--red-gradient); /* Changed to new red gradient */
    color: white;
    animation: pulseCompleted 2s ease-in-out infinite;
}

@keyframes pulseCompleted {
    0%, 100% { box-shadow: 0 0 10px rgba(211, 47, 47, 0.5); } /* Adjusted */
    50% { box-shadow: 0 0 20px rgba(211, 47, 47, 0.8); } /* Adjusted */
}

.status-badge-cancelled {
    background: var(--danger-gradient);
    color: white;
    animation: pulseCancelled 2s ease-in-out infinite;
}

@keyframes pulseCancelled {
    0%, 100% { box-shadow: 0 0 10px rgba(255, 154, 158, 0.5); }
    50% { box-shadow: 0 0 20px rgba(255, 154, 158, 0.8); }
}

.card-body {
    padding: var(--space-xl);
    flex-grow: 1;
    display: flex;
    flex-direction: column;
    gap: var(--space-md);
    position: relative;
    z-index: 2;
}

.info-item {
    display: flex;
    align-items: center;
    gap: var(--space-md);
    font-size: 0.95rem;
    color: var(--text-secondary);
    padding: var(--space-sm);
    border-radius: var(--radius-md);
    transition: var(--transition);
    background: rgba(255, 255, 255, 0.05);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.info-item:hover {
    background: rgba(255, 255, 255, 0.1);
    transform: translateX(5px);
}

.info-icon {
    color: #D32F2F;
    font-size: 1.2em;
    width: 24px;
    text-align: center;
    animation: float 4s ease-in-out infinite;
}

.info-label {
    font-weight: 600;
    min-width: 80px;
}

.info-value {
    color: var(--text-primary);
    font-weight: 600;
    flex-grow: 1;
}

.info-sub-value {
    font-size: 0.85rem;
    color: var(--text-light);
    font-style: italic;
}

/* Bouton d'Expansion Ultra Moderne */
.card-actions-collapsed {
    display: flex;
    justify-content: center;
    padding: var(--space-lg);
    border-top: 1px solid var(--glass-border);
    background: var(--glass-bg-strong);
    position: relative;
    z-index: 2;
}

.expand-card-btn {
    background: var(--primary-gradient);
    border: none;
    color: white;
    font-size: 1.5rem;
    cursor: pointer;
    transition: var(--transition-bounce);
    padding: 12px;
    border-radius: var(--radius-full);
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: var(--shadow-md);
    position: relative;
    overflow: hidden;
}

.expand-card-btn::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    transition: var(--transition);
    transform: translate(-50%, -50%);
}

.expand-card-btn:hover {
    transform: scale(1.1) rotate(180deg);
    box-shadow: var(--shadow-lg);
}

.expand-card-btn:hover::before {
    width: 100%;
    height: 100%;
}

.inscription-card.expanded .expand-card-btn {
    transform: rotate(180deg);
}

/* Détails Expandables */
.card-expanded-details {
    padding: 0;
    background: var(--glass-bg-strong);
    border-top: 1px solid var(--glass-border);
    opacity: 0;
    max-height: 0;
    overflow: hidden;
    transition: all 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    z-index: 2;
}

.inscription-card.expanded .card-expanded-details {
    opacity: 1;
    max-height: 600px;
    padding: var(--space-xl);
}

.detail-section {
    margin-bottom: var(--space-xl);
    padding: var(--space-lg);
    background: rgba(255, 255, 255, 0.05);
    border-radius: var(--radius-md);
    border: 1px solid rgba(255, 255, 255, 0.1);
}

.detail-title {
    font-size: 1.1rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--space-lg);
    display: flex;
    align-items: center;
    gap: var(--space-sm);
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.detail-title i {
    background: var(--secondary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

/* Actions de Cartes */
.card-full-actions {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(140px, 1fr));
    gap: var(--space-md);
    margin-top: var(--space-xl);
    padding-top: var(--space-xl);
    border-top: 1px solid rgba(255, 255, 255, 0.2);
}

.btn-action-full {
    display: flex;
    align-items: center;
    justify-content: center;
    padding: var(--space-md);
    font-weight: 600;
    border-radius: var(--radius-md);
    transition: var(--transition);
    text-decoration: none;
    font-size: 0.875rem;
    border: none;
    cursor: pointer;
    gap: var(--space-sm);
    box-shadow: var(--shadow-sm);
    position: relative;
    overflow: hidden;
}

.btn-action-full::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: var(--transition);
}

.btn-action-full:hover::before {
    left: 100%;
}

.btn-add-payment {
    background: var(--accent-gradient);
    color: white;
}

.btn-view-details {
    background: var(--primary-gradient);
    color: white;
}

.btn-edit {
    background: var(--success-gradient);
    color: white;
}

.btn-delete {
    background: var(--danger-gradient);
    color: white;
}

.btn-action-full:hover {
    transform: translateY(-2px) scale(1.02);
    box-shadow: var(--shadow-lg);
}

/* État Vide Ultra Moderne */
.empty-state {
    text-align: center;
    padding: var(--space-2xl);
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: var(--radius-xl);
    border: 1px solid var(--glass-border);
    box-shadow: var(--glass-shadow);
    color: var(--text-secondary);
    margin-top: var(--space-2xl);
    position: relative;
    overflow: hidden;
}

.empty-state::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: conic-gradient(from 0deg, transparent, rgba(211, 47, 47, 0.1), transparent); /* Adjusted */
    animation: rotate 10s linear infinite;
    z-index: 0;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.empty-state > * {
    position: relative;
    z-index: 1;
}

.empty-icon {
    font-size: 4rem;
    color: #D32F2F;
    margin-bottom: var(--space-xl);
    animation: float 4s ease-in-out infinite;
    text-shadow: 0 0 30px rgba(211, 47, 47, 0.3); /* Adjusted */
}

.empty-state h3 {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: var(--space-md);
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
}

.empty-state p {
    font-size: 1.125rem;
    max-width: 500px;
    margin: 0 auto var(--space-xl);
    opacity: 0.8;
}

/* Statistiques Ultra Modernes */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: var(--space-xl);
    margin-top: var(--space-2xl);
}

.stat-card {
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border-radius: var(--radius-xl);
    border: 1px solid var(--glass-border);
    box-shadow: var(--glass-shadow);
    padding: var(--space-xl);
}

/* Styles pour le Modal */
.glassmorphism-modal {
    background: rgba(255, 255, 255, 0.1);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.18);
    box-shadow: 0 8px 32px 0 rgba(31, 38, 135, 0.37);
    border-radius: 1.25rem; /* 20px */
    animation: fadeInScaleIn 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

/* Animations pour le modal */
@keyframes animate-fade-in {
    from { opacity: 0; }
    to { opacity: 1; }
}

@keyframes animate-fade-in-bg {
    from { opacity: 0; }
    to { opacity: 0.75; }
}

@keyframes animate-scale-in {
    from { transform: scale(0.9); opacity: 0; }
    to { transform: scale(1); opacity: 1; }
}

@keyframes animate-icon-bounce {
    0%, 20%, 50%, 80%, 100% { transform: translateY(0) scale(1); opacity: 1;}
    40% { transform: translateY(-30px) scale(1.1); opacity: 1; }
    60% { transform: translateY(-15px) scale(1.05); opacity: 1; }
}

/* Initial state for hidden elements */
.hidden {
    display: none;
}
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Card Expansion Toggle
        document.querySelectorAll('.inscription-card').forEach(card => {
            const expandButton = card.querySelector('.expand-card-btn');
            const expandedDetails = card.querySelector('.card-expanded-details');

            if (expandButton && expandedDetails) {
                // Prevent click on inner action buttons from toggling the card
                expandedDetails.addEventListener('click', function(event) {
                    if (event.target.closest('.btn-action-full') || event.target.closest('.status-select-wrapper')) {
                        event.stopPropagation();
                    }
                });

                expandButton.addEventListener('click', function(event) {
                    event.stopPropagation(); // Stop click from bubbling to card click
                    card.classList.toggle('expanded');
                    if (card.classList.contains('expanded')) {
                        expandedDetails.style.maxHeight = expandedDetails.scrollHeight + "px"; // Set max-height for smooth transition
                        expandedDetails.style.display = 'block';
                        setTimeout(() => { // Small delay to allow display change before transition
                            expandedDetails.style.opacity = 1;
                        }, 10);
                    } else {
                        expandedDetails.style.opacity = 0;
                        expandedDetails.style.maxHeight = '0';
                        setTimeout(() => {
                            expandedDetails.style.display = 'none';
                        }, 500); // Match transition duration
                    }
                });

                // Optional: Allow clicking anywhere on the card to expand/collapse (excluding buttons)
                card.addEventListener('click', function(event) {
                    if (!event.target.closest('.btn-action-full') && !event.target.closest('.status-select-wrapper') && !event.target.closest('.expand-card-btn')) {
                        card.classList.toggle('expanded');
                        if (card.classList.contains('expanded')) {
                            expandedDetails.style.maxHeight = expandedDetails.scrollHeight + "px";
                            expandedDetails.style.display = 'block';
                            setTimeout(() => {
                                expandedDetails.style.opacity = 1;
                            }, 10);
                        } else {
                            expandedDetails.style.opacity = 0;
                            expandedDetails.style.maxHeight = '0';
                            setTimeout(() => {
                                expandedDetails.style.display = 'none';
                            }, 500);
                        }
                    }
                });
            }
        });

        // Handle direct status change for Admins/Finance
        document.querySelectorAll('.status-select').forEach(selectElement => {
            selectElement.addEventListener('change', function() {
                const inscriptionId = this.dataset.inscriptionId;
                const newStatus = this.value;

                fetch(`/inscriptions/${inscriptionId}/update-status`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ status: newStatus })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Update status badge on the card directly without full reload
                        const card = selectElement.closest('.inscription-card');
                        const statusBadge = card.querySelector('.card-header .status-badge');
                        if (statusBadge) {
                            // Remove existing status classes
                            statusBadge.classList.remove(
                                'status-badge-pending', 'status-badge-active',
                                'status-badge-completed', 'status-badge-cancelled',
                                'status-badge-default'
                            );
                            // Add new status class and text
                            let newClass, newIconClass;
                            switch (newStatus) {
                                case 'pending': newClass = 'status-badge-pending'; newIconClass = 'fas fa-clock'; break;
                                case 'active': newClass = 'status-badge-active'; newIconClass = 'fas fa-check-circle'; break;
                                case 'completed': newClass = 'status-badge-completed'; newIconClass = 'fas fa-trophy'; break;
                                case 'cancelled': newClass = 'status-badge-cancelled'; newIconClass = 'fas fa-times-circle'; break;
                                default: newClass = 'status-badge-default'; newIconClass = 'fas fa-question-circle'; break;
                            }
                            statusBadge.classList.add(newClass);
                            statusBadge.innerHTML = `<i class="${newIconClass}"></i> ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}`;
                        }
                        console.log('Status updated successfully:', data.message);
                        // Optional: Show a temporary success message
                        // alert('Statut mis à jour avec succès !');
                    } else {
                        console.error('Failed to update status:', data.message);
                        alert('Erreur lors de la mise à jour du statut: ' + data.message);
                        // Revert select if update fails
                        this.value = this.dataset.currentStatus;
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue lors de la communication avec le serveur.');
                    // Revert select if error occurs
                    this.value = this.dataset.currentStatus;
                });
            });
        });

        // Store current status on load for potential revert
        document.querySelectorAll('.status-select').forEach(selectElement => {
            selectElement.dataset.currentStatus = selectElement.value;
        });


        // Logique du MODAL DE CONFIRMATION
        const modal = document.getElementById('confirmation-modal');
        const modalTitle = document.getElementById('modal-title');
        const modalMessage = document.getElementById('modal-message');
        const modalIcon = document.getElementById('modal-icon');
        const modalIconContainer = document.getElementById('modal-icon-container');
        const confirmButton = document.getElementById('confirm-button');
        const cancelButton = document.getElementById('cancel-button');

        let currentForm = null; // Pour stocker le formulaire à soumettre

        function openModal(title, message, iconClass, buttonText, form) {
            modalTitle.textContent = title;
            modalMessage.innerHTML = message; // Use innerHTML for rich content
            modalIcon.className = iconClass; // Set the icon class
            modalIconContainer.classList.remove('scale-0', 'opacity-0'); // Show icon and animate
            confirmButton.textContent = buttonText;
            currentForm = form; // Store the form

            // Set button colors based on action (e.g., red for delete, blue for generic confirm)
            if (iconClass.includes('fa-trash-alt')) {
                confirmButton.classList.remove('bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-500');
                confirmButton.classList.add('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
            } else {
                confirmButton.classList.remove('bg-red-600', 'hover:bg-red-700', 'focus:ring-red-500');
                confirmButton.classList.add('bg-blue-600', 'hover:bg-blue-700', 'focus:ring-blue-500');
            }

            modal.classList.remove('hidden');
            // Re-add animation classes to trigger them each time
            modal.classList.remove('animate-fade-in'); // Reset animation
            modal.querySelector('.glassmorphism-modal').classList.remove('animate-scale-in'); // Reset animation
            modalIconContainer.classList.remove('animate-icon-bounce'); // Reset animation

            setTimeout(() => {
                modal.classList.add('animate-fade-in');
                modal.querySelector('.glassmorphism-modal').classList.add('animate-scale-in');
                modalIconContainer.classList.add('animate-icon-bounce'); // Re-trigger icon animation
            }, 10);
        }

        function closeModal() {
            modal.classList.remove('animate-fade-in');
            modal.querySelector('.glassmorphism-modal').classList.remove('animate-scale-in');
            modalIconContainer.classList.remove('animate-icon-bounce');
            modalIconContainer.classList.add('scale-0', 'opacity-0'); // Hide icon
            setTimeout(() => {
                modal.classList.add('hidden');
                currentForm = null; // Clear the stored form
            }, 300); // Allow animation to finish
        }

        // Handle confirmation
        confirmButton.addEventListener('click', function() {
            if (currentForm) {
                currentForm.submit(); // Submit the stored form
            }
            closeModal();
        });

        // Handle cancellation
        cancelButton.addEventListener('click', closeModal);

        // Close modal when clicking outside (on the overlay)
        modal.addEventListener('click', function(event) {
            if (event.target === modal) {
                closeModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape' && !modal.classList.contains('hidden')) {
                closeModal();
            }
        });

        // Intercept delete form submissions
        document.querySelectorAll('form[method="POST"][action*="/inscriptions/"][action*="/delete"]').forEach(form => {
            form.addEventListener('submit', function(event) {
                event.preventDefault(); // Prevent default form submission

                const inscriptionId = this.action.split('/').slice(-1)[0]; // Extract ID from action URL
                const card = this.closest('.inscription-card');
                const userName = card ? card.querySelector('.user-name').textContent : 'cet(te) étudiant(e)';

                openModal(
                    'Confirmer la Suppression',
                    `Voulez-vous vraiment supprimer l'inscription de <span class="font-semibold text-red-600 dark:text-red-400">${userName}</span> (ID: ${inscriptionId}) ? Cette action est **irréversible** !`,
                    'fas fa-trash-alt', // Icon for delete
                    'Supprimer Définitivement',
                    this // Pass the form element
                );
            });
        });


    });
</script>
@endpush
@endsection