@extends('layouts.app')

@section('title', 'Historique de Paiements - ' . $user->name)

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

    body {
        font-family: 'Inter', sans-serif;
    }

    .glass-effect {
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .floating-animation {
        animation: floating 3s ease-in-out infinite;
    }
    
    @keyframes floating {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    .pulse-glow {
        animation: pulse-glow 2s ease-in-out infinite alternate;
    }
    
    @keyframes pulse-glow {
        from { box-shadow: 0 0 20px rgba(239, 68, 68, 0.3); }
        to { box-shadow: 0 0 30px rgba(239, 68, 68, 0.6), 0 0 40px rgba(239, 68, 68, 0.4); }
    }

    .shimmer {
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.4), transparent);
        background-size: 200% 100%;
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { background-position: -200% 0; }
        100% { background-position: 200% 0; }
    }
    
    .morphing-bg {
        background: linear-gradient(-45deg, #fdf2f8, #ffffff, #fef2f2, #fff1f2);
        background-size: 400% 400%;
        animation: gradient-shift 15s ease infinite;
    }
    
    @keyframes gradient-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }

    .stat-card {
        border-radius: 12px;
        padding: 24px;
        color: white;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        margin-bottom: 12px;
    }

    .clean-table {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }

    .table-header {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-bottom: 2px solid #e2e8f0;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen morphing-bg">
    <!-- Header Section -->
    <div class="glass-effect shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center">
               <a href="{{ route('promotions.show', $promotion) }}" class="mr-4 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
  <i class="fa-solid fa-arrow-left"></i>
</a>
                <div>
                    <h1 class="text-3xl font-bold gradient-text flex items-center">
                        <i class="fa-solid fa-file-invoice-dollar text-2xl text-red-600 mr-3"></i>
                        Historique de Paiements
                    </h1>
                    <p class="mt-2 text-gray-600">
                        {{ $user->name }} - {{ $promotion->formation->title }} ({{ $promotion->year }})
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                <div class="stat-icon">
                    <i class="fa-solid fa-money-bill-transfer text-xl"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90 mb-1">Montant Total</p>
                    <p class="text-2xl font-bold">{{ number_format($inscription->total_amount, 0) }} MAD</p>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669);">
                <div class="stat-icon">
                    <i class="fa-solid fa-check-to-slot text-xl"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90 mb-1">Montant Payé</p>
                    <p class="text-2xl font-bold">{{ number_format($inscription->paid_amount, 0) }} MAD</p>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <div class="stat-icon">
                    <i class="fa-solid fa-hand-holding-dollar text-xl"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90 mb-1">Reste à Payer</p>
                    <p class="text-2xl font-bold">{{ number_format($inscription->remaining_amount, 0) }} MAD</p>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <div class="stat-icon">
                    <i class="fa-solid fa-credit-card text-xl"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90 mb-1">Type de Paiement</p>
                    <p class="text-lg font-bold">{{ $inscription->payment_type }}</p>
                </div>
            </div>
        </div>

        <div class="clean-table">
            <div class="table-header p-6">
                <h3 class="section-title mb-0">
                    <i class="fa-solid fa-receipt title-icon"></i>
                    Historique des Paiements ({{ $inscription->payments->count() }})
                </h3>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Montant
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Date de Paiement
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Méthode
                            </th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Référence
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($inscription->payments as $payment)
                        <tr class="table-row">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ number_format($payment->amount, 0) }} MAD
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-600">
                                {{ $payment->paid_date ? $payment->paid_date->format('d/m/Y') : 'N/A' }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-600 capitalize">
                                {{ $payment->payment_method }}
                            </td>
                            <td class="px-6 py-4 text-sm font-medium text-gray-600">
                                {{ $payment->reference ?? 'N/A' }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="empty-state">
                                <div class="empty-icon">
                                    <i class="fa-solid fa-receipt text-3xl text-gray-400"></i>
                                </div>
                                <p class="text-lg font-semibold text-gray-600">Aucun paiement enregistré</p>
                                <p class="text-sm text-gray-500">Aucun paiement n'a été effectué pour cette inscription.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://kit.fontawesome.com/a117b2b918.js" crossorigin="anonymous"></script>
@endsection
