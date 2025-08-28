@extends('layouts.app')

@section('title', 'Gestion des Paiements')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="page-header min-height-300 border-radius-xl mt-4 position-relative overflow-hidden"
                 style="background: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);">
                <div class="animated-bg"></div>
                <span class="mask bg-gradient-primary opacity-4"></span>
                <div class="container position-relative">
                    <div class="row">
                        <div class="col-lg-7 text-center mx-auto">
                            <h1 class="text-white mb-2 mt-5 animate-title"><strong>Gestion des Paiements</strong></h1>
                            <p class="text-white opacity-9 mb-0 animate-subtitle"><strong>Surveillez et gérez tous les paiements efficacement</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card stats-card hover-lift">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold text-gradient-red"><strong>Total des Paiements</strong></p>
                                <h5 class="font-weight-bolder mb-0 counter" data-target="{{ $stats['total_payments'] ?? 0 }}">
                                    <strong>{{ $stats['total_payments'] ?? 0 }}</strong>
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape text-center border-radius-md pulse-icon stat-icon-total">
                                <i class="fas fa-wallet large-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card stats-card hover-lift">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold text-gradient-red"><strong>Montant Total</strong></p>
                                <h5 class="font-weight-bolder mb-0">
                                    <span class="counter" data-target="{{ $stats['total_amount'] ?? 0 }}"><strong>{{ $stats['total_amount'] ?? 0 }}</strong></span> MAD
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape text-center border-radius-md pulse-icon stat-icon-amount">
                                <i class="fas fa-coins large-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6 mb-xl-0 mb-4">
            <div class="card stats-card hover-lift">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold text-gradient-red"><strong>Montant Payé</strong></p>
                                <h5 class="font-weight-bolder mb-0 text-success">
                                    <span class="counter" data-target="{{ $stats['paid_amount'] ?? 0 }}"><strong>{{ $stats['paid_amount'] ?? 0 }}</strong></span> MAD
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape text-center border-radius-md pulse-icon stat-icon-paid">
                                <i class="fas fa-check-circle large-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-sm-6">
            <div class="card stats-card hover-lift">
                <div class="card-body p-3">
                    <div class="row">
                        <div class="col-8">
                            <div class="numbers">
                                <p class="text-sm mb-0 text-capitalize font-weight-bold text-gradient-red"><strong>Montant En Attente</strong></p>
                                <h5 class="font-weight-bolder mb-0 text-warning">
                                    <span class="counter" data-target="{{ $stats['pending_amount'] ?? 0 }}"><strong>{{ $stats['pending_amount'] ?? 0 }}</strong></span> MAD
                                </h5>
                            </div>
                        </div>
                        <div class="col-4 text-end">
                            <div class="icon icon-shape text-center border-radius-md pulse-icon stat-icon-pending">
                                <i class="fas fa-hourglass-half large-icon"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row my-4">

        <div class="col-lg-8 col-md-6 mb-md-0 mb-4">
            <div class="card glassmorphism-card">
                <div class="card-header pb-0 gradient-border-bottom">
                    <div class="row">
                        <div class="col-lg-6 col-7">
                            <h6 class="text-gradient-red"><strong>Filtres de Paiements</strong></h6>
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <form method="GET" action="{{ route('payments.index') }}" class="px-3">
                        <div class="row">
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-gradient-red"><strong>Statut</strong></label>
                                <select name="status" class="form-select modern-select">
                                    <option value="">Tous les Statuts</option>
                                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>En Attente</option>
                                    <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Payé</option>
                                    <option value="late" {{ request('status') == 'late' ? 'selected' : '' }}>En Retard</option>
                                </select>
                            </div>
                            <div class="col-md-3 mb-3">
                                <label class="form-label text-gradient-red"><strong>Mode de Paiement</strong></label>
                                <select name="payment_method" class="form-select modern-select">
                                    <option value="">Toutes les Méthodes</option>
                                    <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Espèces</option>
                                    <option value="credit_card" {{ request('payment_method') == 'credit_card' ? 'selected' : '' }}>Carte de Crédit</option>
                                    <option value="transfer" {{ request('payment_method') == 'transfer' ? 'selected' : '' }}>Virement</option>
                                    <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Chèque</option>
                                    <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Virement Bancaire</option>
                                </select>
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label text-gradient-red"><strong>Date Début</strong></label>
                                <input type="date" name="date_from" class="form-control modern-input" value="{{ request('date_from') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label text-gradient-red"><strong>Date Fin</strong></label>
                                <input type="date" name="date_to" class="form-control modern-input" value="{{ request('date_to') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <label class="form-label">&nbsp;</label>
                                <button type="submit" class="btn btn-gradient-red w-100 hover-scale">
                                    <i class="fas fa-search me-1"></i> <strong>Filtrer</strong>
                                </button>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-10 mb-3">
                                <input type="text" name="search" class="form-control modern-input"
                                             placeholder="Rechercher par  formation, référence..."
                                             value="{{ request('search') }}">
                            </div>
                            <div class="col-md-2 mb-3">
                                <a href="{{ route('payments.index') }}" class="btn btn-outline-red w-100 hover-scale">
                                    <i class="fas fa-times me-1"></i> <strong>Effacer</strong>
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4 col-md-6">
            <div class="card h-100 glassmorphism-card">
                <div class="card-header pb-0 d-flex align-items-center gradient-border-bottom">
                    <div class="w-100">
                        <h6 class="text-gradient-red"><strong>Actions Rapides</strong></h6>
                    </div>
                </div>
                <div class="card-body d-flex flex-column justify-content-center">
                    <a href="{{ route('payments.create') }}" class="btn btn-gradient-red mb-3 hover-scale">
                        <i class="fas fa-plus me-2"></i> <strong>Ajouter un Nouveau Paiement</strong>
                    </a>
                    @if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']))
                    <button type="button" class="btn btn-gradient-pink mb-3 hover-scale" onclick="markLatePayments()">
                        <i class="fas fa-clock me-2"></i> <strong>Marquer les Paiements en Retard</strong>
                    </button>
                    <button type="button" class="btn btn-gradient-pink mb-3 hover-scale" onclick="exportPayments()">
                        <i class="fas fa-download me-2"></i> <strong>Exporter les Données</strong>
                    </button>
                    @endif
                </div>
            </div>
        </div>
    </div>
     

    <div class="row">
        <div class="col-12">
            <div class="card mb-4 glassmorphism-card">
                <div class="card-header pb-0 gradient-border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="text-gradient-red"><strong>Liste des Paiements</strong></h6>
                       @if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']))
                        <div class="bulk-actions" style="display: none;">
                            <button type="button" class="btn btn-sm btn-gradient-success hover-scale" onclick="bulkMarkPaid()">
                                <i class="fas fa-check me-1"></i> <strong>Marquer comme Payé</strong>
                            </button>
                            <button type="button" class="btn btn-sm btn-gradient-warning hover-scale" onclick="bulkMarkLate()">
                                <i class="fas fa-clock me-1"></i> <strong>Marquer en Retard</strong>
                            </button>
                            <button type="button" class="btn btn-sm btn-gradient-danger hover-scale" onclick="bulkDelete()">
                                <i class="fas fa-trash me-1"></i> <strong>Supprimer</strong>
                            </button>
                        </div>
                        @endif
                    </div>
                </div>
                <div class="card-body px-0 pt-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0 modern-table">
                            <thead class="gradient-header">
                                <tr>
                                   @if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']))
                                    <th class="text-center text-uppercase text-red text-xxs font-weight-bolder">
                                        <input type="checkbox" id="select-all" class="modern-checkbox">
                                    </th>
                                    @endif
                                    <th class="text-uppercase text-red text-xxs font-weight-bolder"><strong>
                                        @if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin'])) Étudiant @else Votre Paiement @endif
                                    </strong></th>
                                    <th class="text-uppercase text-red text-xxs font-weight-bolder ps-2"><strong>Formation</strong></th>
                                    <th class="text-center text-uppercase text-red text-xxs font-weight-bolder"><strong>Montant</strong></th>
                                    <th class="text-center text-uppercase text-red text-xxs font-weight-bolder"><strong>Date d'Échéance</strong></th>
                                    <th class="text-center text-uppercase text-red text-xxs font-weight-bolder"><strong>Statut</strong></th>
                                    <th class="text-center text-uppercase text-red text-xxs font-weight-bolder"><strong>Méthode</strong></th>
                                    <th class="text-red"><strong>Actions</strong></th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($payments as $payment)
                                <tr class="table-row-hover">
                                    @if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']))
                                    <td class="text-center">
                                        <input type="checkbox" class="payment-checkbox modern-checkbox" value="{{ $payment->id }}">
                                    </td>
                                    @endif
                                    <td>
                                        <div class="d-flex px-2 py-1 align-items-center">
                                            <div>
                                                {{-- Ensure correct asset path for avatars. Assuming 'storage/' prefix is needed --}}
                                                @if($payment->inscription->user->avatar)
                                                    <img src="{{ Storage::url($payment->inscription->user->avatar) }}"
                                                         class="avatar avatar-sm me-3 border-radius-lg hover-zoom" alt="user avatar">
                                                @else
                                                    <div class="avatar avatar-sm me-3 border-radius-lg initials-avatar hover-zoom">
                                                         <span class="initials">{{ mb_strtoupper(substr($payment->inscription->user->name, 0, 2)) }}</span>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm text-gradient-red"><strong>{{ $payment->inscription->user->name }}</strong></h6>
                                                <p class="text-xs text-secondary mb-0"><strong>{{ $payment->inscription->user->email }}</strong></p>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm text-gradient-red"><strong>{{ $payment->inscription->formation->title }}</strong></h6>
                                            <p class="text-xs text-secondary mb-0"><strong>{{ $payment->reference ?? 'N/A' }}</strong></p>
                                        </div>
                                    </td>
                                    <td class="align-middle text-center text-sm">
                                        <span class="text-xs font-weight-bold text-gradient-red"><strong>{{ number_format($payment->amount, 2) }} MAD</strong></span>
                                        @if($payment->late_fee > 0)
                                            <br><small class="text-danger pulse-text"><strong>+{{ number_format($payment->late_fee, 2) }} MAD (Frais de Retard)</strong></small>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            <strong>{{ \Carbon\Carbon::parse($payment->due_date)->format('d M Y') }}</strong>
                                        </span>
                                        @if($payment->paid_date)
                                            <br><small class="text-success"><strong>Payé: {{ \Carbon\Carbon::parse($payment->paid_date)->format('d M Y') }}</strong></small>
                                        @endif
                                    </td>
                                    <td class="align-middle text-center text-sm text-red">
                                        <span class="badge badge-sm modern-badge
                                            @if($payment->status == 'paid') bg-gradient-success
                                            @elseif($payment->status == 'late') bg-gradient-danger
                                            @else bg-gradient-warning @endif">
                                            <strong>
                                            @if($payment->status == 'paid') Payé
                                            @elseif($payment->status == 'late') En Retard
                                            @else En Attente @endif
                                            </strong>
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <span class="text-secondary text-xs font-weight-bold">
                                            <strong>
                                            @if($payment->payment_method == 'cash') Espèces
                                            @elseif($payment->payment_method == 'credit_card') Carte de Crédit
                                            @elseif($payment->payment_method == 'transfer') Virement
                                            @else {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }} @endif
                                            </strong>
                                        </span>
                                    </td>
                                    <td class="align-middle">
                                        <div class="dropup">
                                            <button class="btn btn-link text-secondary mb-0 hover-scale" type="button"
                                                     data-bs-toggle="dropdown"
                                                     data-bs-placement="top-start"
                                                     aria-expanded="false">
                                                <i class="fa fa-ellipsis-v text-xs"></i>
                                            </button>
                                            <ul class="dropup-menu modern-dropdown" style="z-index: 100;">
                                                <li><a class="dropdown-item" href="{{ route('payments.show', $payment->id) }}">
                                                    <i class="fas fa-eye me-2"></i> <strong>Voir les Détails</strong>
                                                </a></li>
                                                <li><a class="dropdown-item" href="{{ route('payments.edit', $payment->id) }}">
                                                    <i class="fas fa-edit me-2"></i> <strong>Modifier</strong>
                                                </a></li>
                                               @if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']))
                                                    @if($payment->status !== 'paid')
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="quickStatusUpdate(event, {{ $payment->id }}, 'paid');">
                                                            <i class="fas fa-check me-2"></i> <strong>Marquer comme Payé</strong>
                                                        </a>
                                                    </li>
                                                    @endif
                                                    @if($payment->status !== 'late')
                                                    <li>
                                                        <a class="dropdown-item" href="#" onclick="quickStatusUpdate(event, {{ $payment->id }}, 'late');">
                                                            <i class="fas fa-clock me-2"></i> <strong>Marquer en Retard</strong>
                                                        </a>
                                                    </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-danger" href="#" onclick="deletePayment(event, {{ $payment->id }});">
                                                            <i class="fas fa-trash me-2"></i> <strong>Supprimer</strong>
                                                        </a>
                                                    </li>
                                                @endif
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="{{ Auth::user()->hasRole('Admin') ? '9' : '8' }}" class="text-center py-4">
                                        <div class="d-flex flex-column align-items-center empty-state">
                                            <i class="fas fa-receipt fa-3x text-gradient-red mb-3 pulse-icon"></i>
                                            <h6 class="text-gradient-red"><strong>Aucun paiement trouvé</strong></h6>
                                            <p class="text-sm text-secondary"><strong>Essayez d'ajuster vos filtres ou d'ajouter un nouveau paiement</strong></p>
                                        </div>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                @if($payments->hasPages())
                <div class="card-footer gradient-border-top">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <span class="text-sm text-gradient-red">
                                <strong>Affichage de {{ $payments->firstItem() }} à {{ $payments->lastItem() }} sur {{ $payments->total() }} résultats</strong>
                            </span>
                        </div>
                        <div>
                            {{ $payments->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- MODAL FOR OVERDUE PAYMENTS --}}
{{-- This modal should ONLY exist once and be conditional --}}
@if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']))
<div class="modal fade" id="overdueStudentsModal" tabindex="-1" role="dialog" aria-labelledby="overdueStudentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content glassmorphism-card">
            <div class="modal-header gradient-border-bottom">
                <h5 class="modal-title text-gradient-red" id="overdueStudentsModalLabel"><strong>Paiements en Retard : Étudiants Concernés</strong></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p class="text-secondary">Sélectionnez les paiements à marquer comme "En Retard".</p>
                <div class="table-responsive">
                    <table class="table align-items-center mb-0 modern-table">
                        <thead class="gradient-header">
                            <tr>
                                <th class="text-center text-uppercase text-red text-xxs font-weight-bolder">
                                    <input type="checkbox" id="selectAllOverdue" class="modern-checkbox">
                                </th>
                                <th class="text-uppercase text-red text-xxs font-weight-bolder">Étudiant</th>
                                <th class="text-uppercase text-red text-xxs font-weight-bolder">Formation</th>
                                <th class="text-center text-uppercase text-red text-xxs font-weight-bolder">Montant Dû</th>
                                <th class="text-center text-uppercase text-red text-xxs font-weight-bolder">Date d'Échéance</th>
                            </tr>
                        </thead>
                        <tbody id="overdueStudentsTableBody">
                            </tbody>
                    </table>
                </div>
                <div id="noOverduePaymentsMessage" class="text-center py-4" style="display: none;">
                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                    <h6 class="text-success"><strong>Aucun paiement en retard trouvé pour le moment.</strong></h6>
                    <p class="text-sm text-secondary"><strong>Tous les paiements sont à jour ou déjà marqués comme tels.</strong></p>
                </div>
            </div>
            <div class="modal-footer gradient-border-top">
                <button type="button" class="btn btn-outline-secondary " data-bs-dismiss="modal">Fermer</button>
                <button type="button" class="btn btn-gradient-danger hover-scale" id="markSelectedAsLateBtn" disabled>
                    <i class="fas fa-exclamation-triangle me-2"></i> Marquer les sélectionnés en retard
                </button>
            </div>
        </div>
    </div>
</div>
@endif

<style>
/* Animations et styles personnalisés */
@keyframes gradient-shift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-10px); }
}

@keyframes pulse-glow {
    0%, 100% { box-shadow: 0 0 10px rgba(211, 47, 47, 0.3); }
    50% { box-shadow: 0 0 20px rgba(211, 47, 47, 0.6); }
}

.animated-bg {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(45deg, #D32F2F, #C2185B, #ef4444, #D32F2F);
    background-size: 400% 400%;
    animation: gradient-shift 8s ease infinite;
    opacity: 0.1;
}

.animate-title {
    animation: float 3s ease-in-out infinite;
}

.animate-subtitle {
    animation: float 3s ease-in-out infinite;
    animation-delay: 0.5s;
}

.text-gradient-red {
    background: linear-gradient(45deg, #D32F2F, #C2185B);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.stats-card {
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(211, 47, 47, 0.1);
    transition: all 0.3s ease;
    padding: 1rem; /* Increase padding for larger cards */
}

.hover-lift:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(211, 47, 47, 0.2);
}

.glassmorphism-card {
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(211, 47, 47, 0.1);
    box-shadow: 0 8px 32px rgba(211, 47, 47, 0.1);
}

.gradient-border-bottom {
    border-bottom: 2px solid;
    border-image: linear-gradient(45deg, #D32F2F, #C2185B) 1;
}

.gradient-border-top {
    border-top: 2px solid;
    border-image: linear-gradient(45deg, #D32F2F, #C2185B) 1;
}

.pulse-icon {
    animation: pulse-glow 2s ease-in-out infinite;
}

.btn-gradient-red {
    background: linear-gradient(45deg, #D32F2F, #C2185B);
    border: none;
    color: white;
    transition: all 0.3s ease;
}

.btn-gradient-red:hover {
    background: linear-gradient(45deg, #C2185B, #ef4444);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(211, 47, 47, 0.4);
}

.btn-gradient-pink {
    background: linear-gradient(45deg, #C2185B, #ef4444);
    border: none;
    color: white;
    transition: all 0.3s ease;
}

.btn-gradient-pink:hover {
    background: linear-gradient(45deg, #ef4444, #D32F2F);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(194, 24, 91, 0.4);
}

.btn-gradient-red-outline {
    background: transparent;
    border: 2px solid #D32F2F;
    color: #D32F2F;
    transition: all 0.3s ease;
}

.btn-gradient-red-outline:hover {
    background: linear-gradient(45deg, #D32F2F, #C2185B);
    color: white;
    transform: translateY(-2px);
}

.btn-outline-red {
    border-color: #D32F2F;
    color: #D32F2F;
    transition: all 0.3s ease;
}

.btn-outline-red:hover {
    background: #D32F2F;
    color: white;
    transform: translateY(-2px);
}

.hover-scale:hover {
    transform: scale(1.05);
}

.modern-select,
.modern-input {
    border: 2px solid rgba(211, 47, 47, 0.2);
    border-radius: 12px;
    transition: all 0.3s ease;
}

.modern-select:focus,
.modern-input:focus {
    border-color: #D32F2F;
    box-shadow: 0 0 0 0.2rem rgba(211, 47, 47, 0.25);
}

.modern-table {
    border-radius: 12px;
    overflow: hidden;
}

.gradient-header {
    background: linear-gradient(45deg, #D32F2F, #C2185B);
}

.table-row-hover:hover {
    background: rgba(211, 47, 47, 0.05);
    transform: translateX(5px);
    transition: all 0.3s ease;
}

.modern-badge {
    border-radius: 20px;
    padding: 8px 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modern-checkbox {
    width: 20px;
    height: 20px;
    accent-color: #D32F2F;
}

.modern-dropdown {
    border: none;
    border-radius: 12px;
    box-shadow: 0 10px 30px rgba(211, 47, 47, 0.2);
    background: rgba(255, 255, 255, 0.98);
    backdrop-filter: blur(10px);
}

.modern-dropdown .dropdown-item:hover {
    background: rgba(211, 47, 47, 0.1);
    color: #D32F2F;
}

.hover-zoom:hover {
    transform: scale(1.1);
    transition: all 0.3s ease;
}

.empty-state {
    animation: float 3s ease-in-out infinite;
}

.pulse-text {
    animation: pulse-glow 2s ease-in-out infinite;
    font-weight: bold;
}

.counter {
    display: inline-block;
    transition: all 0.3s ease;
    font-weight: bold !important;
    font-size: 2.2rem; /* Make the counter numbers larger */
}

/* Added for general text boldness where 'font-weight-bold' isn't enough or for dynamic content */
.font-weight-bolder {
    font-weight: 900 !important; /* Makes it even bolder than 'bold' */
}

/* Styles for larger and modern colored icons in stat cards */
.icon.icon-shape {
    width: 70px; /* Larger icon container */
    height: 70px; /* Larger icon container */
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 2.5rem; /* Large icon size */
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2); /* More pronounced shadow */
    transition: all 0.3s ease;
}

.large-icon {
    font-size: 2.5rem; /* Explicitly make the icon large */
    color: #fff; /* Ensure icons are white inside their colored background */
    opacity: 1; /* Ensure full opacity */
}

/* Modern gradient colors for each stat icon */
.stat-icon-total {
    background: linear-gradient(135deg, #6a11cb 0%, #2575fc 100%); /* Purple to Blue */
}

.stat-icon-amount {
    background: linear-gradient(135deg, #ff6b6b 0%, #ffa500 100%); /* Red-ish to Orange */
}

.stat-icon-paid {
    background: linear-gradient(135deg, #28b485 0%, #20c997 100%); /* Green to lighter Green */
}

.stat-icon-pending {
    background: linear-gradient(135deg, #fd7e14 0%, #ffc107 100%); /* Orange to Yellow */
}

/* Styles for Initial Avatar */
.initials-avatar {
    width: 48px; /* Same size as avatar-sm */
    height: 48px; /* Same size as avatar-sm */
    border-radius: 0.75rem; /* border-radius-lg equivalent */
    background: linear-gradient(45deg, #D32F2F, #C2185B); /* A nice gradient background */
    display: flex;
    justify-content: center;
    align-items: center;
    color: white;
    font-weight: bold;
    font-size: 0.9rem; /* Adjust font size as needed */
    text-transform: uppercase;
    flex-shrink: 0; /* Prevent it from shrinking */
}

.initials {
    line-height: 1; /* Adjust line height to center text vertically */
}


/* Responsive adjustments */
@media (max-width: 768px) {
    .stats-card {
        margin-bottom: 1rem;
    }

    .hover-lift:hover {
        transform: none;
    }

    .table-row-hover:hover {
        transform: none;
    }

    .icon.icon-shape {
        width: 60px; /* Adjust size for smaller screens */
        height: 60px;
        font-size: 2rem;
    }

    .large-icon {
        font-size: 2rem;
    }

    .initials-avatar {
        width: 40px; /* Adjust size for smaller screens */
        height: 40px;
        font-size: 0.8rem;
    }
}

@media (max-width: 576px) {
    .page-header {
        min-height: 200px !important;
    }

    .animate-title {
        font-size: 1.5rem !important;
    }

    .table-responsive {
        border-radius: 8px;
    }
    span.badge.badge-sm.modern-badge.bg-gradient-warning {
    color: #d32a2a !important;
}
}
.btn-outline-red {
    border-color: #D32F2F;
    background-color: #D32F2F !important;
    transition: all 0.3s ease;
}
.modern-badge {
    border-radius: 20px;
    padding: 8px 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #ea2020;
}
button.btn.btn-outline-secondary {
    background-color: #ab2f2f ! IMPORTANT;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Animation des compteurs
    const counters = document.querySelectorAll('.counter');
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-target'));
        const increment = target / 100;
        let current = 0;

        const timer = setInterval(() => {
            current += increment;
            if (current >= target) {
                counter.textContent = target.toLocaleString();
                clearInterval(timer);
            } else {
                counter.textContent = Math.floor(current).toLocaleString();
            }
        }, 20);
    });

    // Fonctionnalité de sélection globale (Applies to the main payments table checkboxes)
   @if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']))
        const selectAllCheckbox = document.getElementById('select-all');
        const paymentCheckboxes = document.querySelectorAll('.payment-checkbox');
        const bulkActions = document.querySelector('.bulk-actions');

        if (selectAllCheckbox) { // Check if element exists before attaching listener
            selectAllCheckbox.addEventListener('change', function() {
                paymentCheckboxes.forEach(checkbox => {
                    checkbox.checked = this.checked;
                });
                toggleBulkActions();
            });
        }

        paymentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', toggleBulkActions);
        });

        function toggleBulkActions() {
            const checkedBoxes = document.querySelectorAll('.payment-checkbox:checked');
            if (bulkActions) { // Ensure bulkActions element exists
                if (checkedBoxes.length > 0) {
                    bulkActions.style.display = 'block';
                    bulkActions.style.animation = 'float 1s ease-in-out';
                } else {
                    bulkActions.style.display = 'none';
                }
            }
        }
    @endif

    // Animations avancées pour les interactions (existing logic)
    const buttons = document.querySelectorAll('.btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-2px) scale(1.02)';
        });

        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    const cards = document.querySelectorAll('.card');
    cards.forEach((card, index) => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';

        setTimeout(() => {
            card.style.transition = 'all 0.6s ease';
            card.style.opacity = '1';
            card.style.transform = 'translateY(0)';
        }, index * 100);
    });

    const tableRows = document.querySelectorAll('.table-row-hover');
    tableRows.forEach((row, index) => {
        row.style.opacity = '0';
        row.style.transform = 'translateX(-20px)';

        setTimeout(() => {
            row.style.transition = 'all 0.4s ease';
            row.style.opacity = '1';
            row.style.transform = 'translateX(0)';
        }, 500 + (index * 50));
    });

    // Gestion des thèmes sombre/clair (bonus)
    function toggleTheme() {
        const body = document.body;
        body.classList.toggle('dark-theme');
        localStorage.setItem('theme', body.classList.contains('dark-theme') ? 'dark' : 'light');
    }

    const savedTheme = localStorage.getItem('theme');
    if (savedTheme === 'dark') {
        document.body.classList.add('dark-theme');
    }
});


// === MODIFIED FUNCTIONS ===

// Système de notifications (ensure this is accessible globally or defined within DOMContentLoaded)
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas ${getNotificationIcon(type)} me-2"></i>
            <span>${message}</span>
            <button class="notification-close" onclick="closeNotification(this)">
                <i class="fas fa-times"></i>
            </button>
        </div>
    `;

    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('show');
    }, 100);

    setTimeout(() => {
        closeNotification(notification.querySelector('.notification-close'));
    }, 5000);
}

function getNotificationIcon(type) {
    switch(type) {
        case 'success': return 'fa-check-circle';
        case 'error': return 'fa-exclamation-circle';
        case 'warning': return 'fa-exclamation-triangle';
        default: return 'fa-info-circle';
    }
}

function closeNotification(button) {
    const notification = button.closest('.notification');
    notification.classList.remove('show');
    setTimeout(() => {
        notification.remove();
    }, 300);
}

// Quick status update function (available to Admin in dropdown menu)
function quickStatusUpdate(event, paymentId, status) {
    event.preventDefault();

    const statusText = status === 'paid' ? 'payé' : 'en retard';
    if (!confirm(`Êtes-vous sûr de vouloir marquer ce paiement comme ${statusText}?`)) {
        return;
    }

    const url = `/payments/${paymentId}/status`;
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    let data = { status: status };
    if (status === 'paid') {
        data.paid_date = new Date().toISOString().split('T')[0];
    }

    fetch(url, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken,
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                console.error('Server error response:', errorData);
                throw new Error(errorData.message || (errorData.errors ? JSON.stringify(errorData.errors) : 'Unknown server error.'));
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            showNotification('Statut mis à jour avec succès!', 'success');
            setTimeout(() => location.reload(), 1000);
        } else {
            showNotification('Erreur: ' + (data.message || 'Unknown error from server'), 'error');
        }
    })
    .catch(error => {
        console.error('Erreur Fetch API:', error);
        showNotification('Erreur lors de la mise à jour du statut: ' + error.message, 'error');
    });
}

// All Admin-specific JavaScript, including modal functionality and bulk actions.
@if(Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']))

// Function to open the modal and fetch overdue students
function markLatePayments() {
    // IMPORTANT: Get references to modal elements. These will only exist if the user is Admin.
    const tableBody = document.getElementById('overdueStudentsTableBody');
    const markBtn = document.getElementById('markSelectedAsLateBtn');
    const selectAllCb = document.getElementById('selectAllOverdue');
    const noMsg = document.getElementById('noOverduePaymentsMessage');


    if (!tableBody || !markBtn || !selectAllCb || !noMsg) {
        console.error("Error: One or more modal elements not found. This function should only be callable by Admin.");
        showNotification('Erreur interne: Les éléments de la fenêtre modale sont introuvables. Contactez l\'administrateur.', 'error');
        return; // Stop execution if elements are missing
    }

    // Show a loading indicator or clear previous content
    tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-3"><i class="fas fa-spinner fa-spin me-2"></i> Chargement des paiements en retard...</td></tr>';
    markBtn.disabled = true;
    selectAllCb.checked = false;
    noMsg.style.display = 'none';

    fetch('/payments/overdue-students', { // This route should be defined in web.php
        method: 'GET',
        headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => {
                console.error('Server error response:', errorData);
                throw new Error(errorData.message || (errorData.errors ? JSON.stringify(errorData.errors) : 'Unknown server error.'));
            });
        }
        return response.json();
    })
    .then(data => {
        tableBody.innerHTML = ''; // Clear loading message
        if (data.length > 0) {
            data.forEach(student => {
                student.overdue_payments.forEach(payment => {
                    const row = `
                        <tr class="table-row-hover">
                            <td class="text-center">
                                <input type="checkbox" class="overdue-payment-checkbox modern-checkbox" value="${payment.payment_id}">
                            </td>
                            <td>
                                <div class="d-flex px-2 py-1 align-items-center">
                                    <div class="avatar avatar-sm me-3 border-radius-lg initials-avatar hover-zoom">
                                        <span class="initials">${student.student_name.substring(0, 2).toUpperCase()}</span>
                                    </div>
                                    <div class="d-flex flex-column justify-content-center">
                                        <h6 class="mb-0 text-sm text-gradient-red"><strong>${student.student_name}</strong></h6>
                                        <p class="text-xs text-secondary mb-0"><strong>${student.student_email}</strong></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <h6 class="mb-0 text-sm text-gradient-red"><strong>${student.formation_title}</strong></h6>
                            </td>
                            <td class="align-middle text-center text-sm">
                                <span class="text-xs font-weight-bold text-gradient-red"><strong>${payment.amount} MAD</strong></span>
                            </td>
                            <td class="align-middle text-center">
                                <span class="text-secondary text-xs font-weight-bold">
                                    <strong>${payment.due_date}</strong>
                                </span>
                            </td>
                        </tr>
                    `;
                    tableBody.insertAdjacentHTML('beforeend', row);
                });
            });

            // Attach event listeners for newly rendered checkboxes
            document.querySelectorAll('.overdue-payment-checkbox').forEach(cb => {
                cb.addEventListener('change', toggleMarkSelectedButton);
            });
            selectAllCb.addEventListener('change', toggleSelectAllOverdue); // The select all checkbox in modal
            toggleMarkSelectedButton(); // Initial check for button state

        } else {
            noMsg.style.display = 'block';
        }

        const overdueModal = new bootstrap.Modal(document.getElementById('overdueStudentsModal'));
        overdueModal.show();
    })
    .catch(error => {
        console.error('Error fetching overdue students:', error);
        showNotification('Erreur lors du chargement des paiements en retard: ' + error.message, 'error');
        if (tableBody) { // Check before setting innerHTML in catch block too
            tableBody.innerHTML = '<tr><td colspan="5" class="text-center py-3 text-danger">Erreur lors du chargement.</td></tr>';
        }
    });
}

function toggleSelectAllOverdue() {
    const isChecked = this.checked;
    document.querySelectorAll('.overdue-payment-checkbox').forEach(checkbox => {
        checkbox.checked = isChecked;
    });
    toggleMarkSelectedButton();
}

function toggleMarkSelectedButton() {
    const checkedBoxes = document.querySelectorAll('.overdue-payment-checkbox:checked');
    const markBtn = document.getElementById('markSelectedAsLateBtn');
    if (markBtn) { // Ensure markBtn exists
        markBtn.disabled = checkedBoxes.length === 0;
    }
}

// Function to mark selected payments as late from the modal

document.getElementById('markSelectedAsLateBtn').addEventListener('click', function() {
    const checkedBoxes = document.querySelectorAll('.overdue-payment-checkbox:checked');
    const paymentIds = Array.from(checkedBoxes).map(cb => cb.value);

    if (paymentIds.length === 0) {
        showNotification('Veuillez sélectionner au moins un paiement à marquer comme en retard.', 'warning');
        return;
    }

    if (confirm(`Êtes-vous sûr de vouloir marquer ${paymentIds.length} paiements comme "En Retard"?`)) {
        // Use the existing bulkUpdate function
        bulkUpdate(paymentIds, 'mark_late');
        // Close the modal after initiating the bulk update
        const overdueModal = bootstrap.Modal.getInstance(document.getElementById('overdueStudentsModal'));
        if (overdueModal) {
            overdueModal.hide();
        }
    }
});

// Bulk update functions (only for Admin) - these were already here
function bulkMarkPaid() {
    const checkedBoxes = document.querySelectorAll('.payment-checkbox:checked');
    const paymentIds = Array.from(checkedBoxes).map(cb => cb.value);

    if (paymentIds.length === 0) {
        showNotification('Veuillez sélectionner au moins un paiement.', 'warning');
        return;
    }

    if (confirm(`Marquer ${paymentIds.length} paiements comme payés?`)) {
        bulkUpdate(paymentIds, 'mark_paid');
    }
}

function bulkMarkLate() {
    const checkedBoxes = document.querySelectorAll('.payment-checkbox:checked');
    const paymentIds = Array.from(checkedBoxes).map(cb => cb.value);

    if (paymentIds.length === 0) {
        showNotification('Veuillez sélectionner au moins un paiement.', 'warning');
        return;
    }

    if (confirm(`Marquer ${paymentIds.length} paiements comme en retard?`)) {
        bulkUpdate(paymentIds, 'mark_late');
    }
}

function bulkDelete() {
    const checkedBoxes = document.querySelectorAll('.payment-checkbox:checked');
    const paymentIds = Array.from(checkedBoxes).map(cb => cb.value);

    if (paymentIds.length === 0) {
        showNotification('Veuillez sélectionner au moins un paiement.', 'warning');
        return;
    }

    if (confirm(`Supprimer ${paymentIds.length} paiements? Cette action ne peut pas être annulée.`)) {
        bulkUpdate(paymentIds, 'delete');
    }
}

function bulkUpdate(paymentIds, action) {
    fetch('/payments/bulk-update', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            payment_ids: paymentIds,
            action: action,
            paid_date: action === 'mark_paid' ? new Date().toISOString().split('T')[0] : null
        })
    })
    .then(response => response.json())
    .then(data => {
        showNotification(data.message, data.success ? 'success' : 'error');
        if (data.success) {
            setTimeout(() => location.reload(), 1000);
        }
    })
    .catch(error => {
        console.error('Erreur:', error);
        showNotification('Erreur lors de la mise à jour en masse', 'error');
    });
}

// Delete payment function (available to Admin in dropdown menu)
function deletePayment(event, paymentId) {
    event.preventDefault();

    if (confirm('Êtes-u sure de vouloir supprimer ce paiement? Cette action ne peut pas être annulée.')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/payments/${paymentId}`;
        form.innerHTML = `
            <input type="hidden" name="_method" value="DELETE">
            <input type="hidden" name="_token" value="${document.querySelector('meta[name="csrf-token"]').getAttribute('content')}">
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

@endif 

// exportPayments (still global as it might be useful for all, or move it inside Admin if only admins export)
function exportPayments() {
    const params = new URLSearchParams(window.location.search);
    params.set('export', 'true');
    showNotification('Exportation en cours...', 'info');
    window.location.href = '?' + params.toString();
}

</script>

<style>
/* Your existing CSS remains unchanged here */
.notification {
    position: fixed;
    top: 20px;
    right: 20px;
    background: rgba(255, 255, 255, 0.95);
    backdrop-filter: blur(10px);
    border-radius: 12px;
    padding: 0;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    border: 1px solid rgba(211, 47, 47, 0.2);
    z-index: 9999;
    transform: translateX(100%);
    transition: all 0.3s ease;
    min-width: 300px;
}

.notification.show {
    transform: translateX(0);
}

.notification-content {
    padding: 16px 20px;
    display: flex;
    align-items: center;
    color: #333;
}

.notification-success {
    border-left: 4px solid #28a745;
}

.notification-error {
    border-left: 4px solid #dc3545;
}

.notification-warning {
    border-left: 4px solid #ffc107;
}

.notification-info {
    border-left: 4px solid #17a2b8;
}

.notification-close {
    background: none;
    border: none;
    margin-left: auto;
    cursor: pointer;
    color: #666;
    font-size: 12px;
    padding: 4px;
    border-radius: 4px;
    transition: all 0.2s ease;
}

.notification-close:hover {
    background: rgba(211, 47, 47, 0.1);
    color: #D32F2F;
}

/* Styles pour le thème sombre */
.dark-theme {
    background: #1a1a1a;
    color: #e0e0e0;
}

.dark-theme .glassmorphism-card {
    background: rgba(30, 30, 30, 0.95);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(211, 47, 47, 0.3);
}

.dark-theme .stats-card {
    background: rgba(30, 30, 30, 0.95);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(211, 47, 47, 0.3);
}

.dark-theme .modern-table {
    background: rgba(30, 30, 30, 0.9);
}

.dark-theme .table-row-hover:hover {
    background: rgba(211, 47, 47, 0.1);
}

.dark-theme .notification {
    background: rgba(30, 30, 30, 0.95);
    color: #e0e0e0;
}

.dark-theme .modern-dropdown {
    background: rgba(30, 30, 30, 0.95);
}

/* Animations supplémentaires */
@keyframes slideInUp {
    from {
        transform: translateY(30px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@keyframes slideInLeft {
    from {
        transform: translateX(-30px);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

@keyframes bounceIn {
    0% {
        transform: scale(0.3);
        opacity: 0;
    }
    50% {
        transform: scale(1.05);
    }
    70% {
        transform: scale(0.9);
    }
    100% {
        transform: scale(1);
        opacity: 1;
    }
}

.animate-slide-up {
    animation: slideInUp 0.6s ease forwards;
}

.animate-slide-left {
    animation: slideInLeft 0.6s ease forwards;
}

.animate-bounce-in {
    animation: bounceIn 0.8s ease forwards;
}

/* Effet de vague sur les boutons */
.btn-wave {
    position: relative;
    overflow: hidden;
}

.btn-wave::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 255, 255, 0.3);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.3s, height 0.3s;
}

.btn-wave:hover::before {
    width: 300px;
    height: 300px;
}

/* Scrollbar personnalisée */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(211, 47, 47, 0.1);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb {
    background: linear-gradient(45deg, #D32F2F, #C2185B);
    border-radius: 10px;
}

::-webkit-scrollbar-thumb:hover {
    background: linear-gradient(45deg, #C2185B, #ef4444);
}

/* Responsive design amélioré */
@media (max-width: 992px) {
    .notification {
        right: 10px;
        left: 10px;
        min-width: auto;
    }

    .stats-card {
        margin-bottom: 20px;
    }
}

@media (max-width: 576px) {
    .page-header {
        min-height: 200px !important;
    }

    .animate-title {
        font-size: 1.5rem !important;
    }

    .table-responsive {
        border-radius: 8px;
    }
    span.badge.badge-sm.modern-badge.bg-gradient-warning {
    color: #d32a2a !important;
}
}
.btn-outline-red {
    border-color: #D32F2F;
    background-color: #D32F2F !important;
    transition: all 0.3s ease;
}
.modern-badge {
    border-radius: 20px;
    padding: 8px 16px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #ea2020;
}
button.btn.btn-outline-secondary {
    background-color: #ab2f2f ! IMPORTANT;
}
img.avatar.avatar-sm.me-3.border-radius-lg.hover-zoom {
    width: 56px;
    border: 1px red solid;
    border-radius: 10px;
    background-color: var(--bs-form-invalid-color);
}
</style>
@endsection