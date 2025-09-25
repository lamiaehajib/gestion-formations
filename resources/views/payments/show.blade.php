@extends('layouts.app')

@section('title', 'Détails du Paiement')

@push('styles')
<style>
    :root {
        --primary-red: #D32F2F;
        --accent-pink: #C2185B;
        --secondary-red: #ef4444;
        --gradient-primary: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
        --gradient-secondary: linear-gradient(135deg, #ef4444 0%, #D32F2F 100%);
        --shadow-primary: 0 8px 32px rgba(211, 47, 47, 0.3);
        --shadow-hover: 0 12px 40px rgba(211, 47, 47, 0.4);
    }
    .animated-card {
        animation: slideInUp 0.6s cubic-bezier(0.4, 0, 0.2, 1);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        box-shadow: var(--shadow-primary);
        backdrop-filter: blur(10px);
        border-radius: 20px;
        overflow: hidden;
    }
    .animated-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-hover);
    }
    .gradient-header {
        background: var(--gradient-primary);
        position: relative;
        overflow: hidden;
    }
    .gradient-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        animation: shimmer 3s infinite;
    }
    .gradient-secondary {
        background: var(--gradient-secondary);
    }
    @keyframes slideInUp {
        from { opacity: 0; transform: translateY(30px); }
        to { opacity: 1; transform: translateY(0); }
    }
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }
    .status-badge {
        padding: 8px 16px;
        border-radius: 25px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        transition: all 0.3s ease;
    }
    .status-badge:hover {
        transform: scale(1.1);
    }
    .status-paid { background: linear-gradient(135deg, #4caf50, #8bc34a); }
    .status-pending { background: linear-gradient(135deg, #ff9800, #ffc107); }
    .status-late { background: linear-gradient(135deg, #ef4444, #D32F2F); }
    .animated-btn {
        background: var(--gradient-primary);
        border: none;
        border-radius: 25px;
        padding: 10px 20px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 1px;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .animated-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(211, 47, 47, 0.4);
    }
    .animated-btn::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }
    .animated-btn:hover::before {
        left: 100%;
    }
    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }
    .custom-list-item {
        border: none;
        padding: 15px 20px;
        margin-bottom: 8px;
        border-radius: 12px;
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .custom-list-item:hover {
        transform: translateX(10px);
        box-shadow: 0 4px 20px rgba(211, 47, 47, 0.1);
        background: linear-gradient(135deg, #fff 0%, #ffebee 100%);
    }
</style>
@endpush

@section('content')
    <div class="container-fluid py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">

                {{-- Main Payment Details Card --}}
                <div class="card animated-card mb-4">
                    <div class="card-header gradient-header text-white d-flex justify-content-between align-items-center p-4">
                        <h3 class="card-title mb-0">
                            <i class="fas fa-money-check-alt fa-2x me-2 floating-icon"></i>
                            Détails du Paiement
                        </h3>
                        @php
    $statusClass = [
        'paid' => 'status-badge status-paid',
        'pending' => 'status-badge status-pending',
        'late' => 'status-badge status-late',
    ][$payment->status] ?? 'status-badge bg-secondary';
                        @endphp
                        <span class="{{ $statusClass }} fs-6">{{ ucfirst($payment->status) }}</span>
                    </div>
                    <div class="card-body glass-card p-4">
                        @if (session('success'))
                            <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle me-2"></i> {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <div class="row">
                            {{-- Payment Information --}}
                            <div class="col-md-6 mb-4">
                                <h5 class="mb-3" style="color: var(--primary-red); font-weight: 700;">
                                    <i class="fas fa-file-invoice-dollar me-2"></i> Informations du Paiement
                                </h5>
                                <ul class="list-group list-group-flush">
                                    <li class="custom-list-item"><strong>Référence:</strong> {{ $payment->reference ?? 'N/A' }}</li>
                                    <li class="custom-list-item"><strong>Montant:</strong> <span class="badge bg-success">{{ number_format($payment->amount, 2) }} DH</span></li>
                                    <li class="custom-list-item"><strong>Méthode:</strong> {{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</li>
                                <li class="custom-list-item">
                                    <strong>Date d'échéance:</strong>
                                    {{ $payment->due_date ? (is_string($payment->due_date) ? \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') : $payment->due_date->format('d/m/Y')) : 'N/A' }}
                                </li>
                                    @if($payment->paid_date)
                                    <li class="custom-list-item"><strong>Date de Paiement:</strong> {{ \Carbon\Carbon::parse($payment->paid_date)->format('d/m/Y') }}</li>
                                    @endif
                                    @if($payment->late_fee > 0)
                                    <li class="custom-list-item"><strong>Frais de Retard:</strong> <span class="badge bg-danger">{{ number_format($payment->late_fee, 2) }} DH</span></li>
                                    @endif
                                    @if($payment->transaction_id)
                                    <li class="custom-list-item"><strong>ID de Transaction:</strong> {{ $payment->transaction_id }}</li>
                                    @endif
                                    @if($payment->receipt_path)
                                    <li class="custom-list-item">
                                        <strong>Reçu:</strong>
                                        <a href="{{ Storage::url($payment->receipt_path) }}" target="_blank" class="text-info hover:text-dark">
                                            <i class="fas fa-file-pdf me-1"></i> Voir le reçu
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>

                            {{-- Associated Inscription & User Details --}}
                            <div class="col-md-6 mb-4">
                                <h5 class="mb-3" style="color: var(--accent-pink); font-weight: 700;">
                                    <i class="fas fa-user-graduate me-2"></i> Détails de l'Inscription
                                </h5>
                                <ul class="list-group list-group-flush">
                                    <li class="custom-list-item"><strong>Étudiant:</strong> {{ $payment->inscription->user->name }}</li>
                                    <li class="custom-list-item"><strong>Formation:</strong> {{ $payment->inscription->formation->title }}</li>
                                    <li class="custom-list-item"><strong>Montant payé:</strong> <span class="badge bg-success">{{ number_format($payment->inscription->paid_amount, 2) }} DH</span></li>
                                    <li class="custom-list-item"><strong>Reste à payer:</strong> <span class="badge bg-danger">{{ number_format($payment->inscription->total_amount - $payment->inscription->paid_amount, 2) }} DH</span></li>
                                </ul>
                            </div>
                        </div>

                        {{-- Action Buttons --}}
                        <div class="d-flex justify-content-end gap-3 mt-4 pt-4 border-top">
                            <a href="{{ route('payments.index') }}" class="btn btn-outline-secondary animated-btn text-white">
                                <i class="fas fa-arrow-left me-2"></i> Retour
                            </a>
                            @can('payment-edit')
                                <a href="{{ route('payments.edit', $payment->id) }}" class="btn btn-warning animated-btn">
                                    <i class="fas fa-edit me-2"></i> Modifier
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const alerts = document.querySelectorAll('.alert-custom');
    alerts.forEach(alert => {
        setTimeout(() => {
            new bootstrap.Alert(alert).close();
        }, 5000);
    });
});
</script>
@endpush