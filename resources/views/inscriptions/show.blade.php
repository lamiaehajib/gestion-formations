{{-- resources/views/inscriptions/show.blade.php --}}
@extends('layouts.app')

@section('title', 'Détails de l\'inscription')

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
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }

    @keyframes pulse {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes fadeInLeft {
        from {
            opacity: 0;
            transform: translateX(-30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes fadeInRight {
        from {
            opacity: 0;
            transform: translateX(30px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    .fade-in-left {
        animation: fadeInLeft 0.8s ease-out;
    }

    .fade-in-right {
        animation: fadeInRight 0.8s ease-out;
    }

    .pulse-animation {
        animation: pulse 2s infinite;
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

    .status-pending { background: linear-gradient(135deg, #ff9800, #ffc107); }
    .status-active { background: linear-gradient(135deg, #4caf50, #8bc34a); }
    .status-completed { background: linear-gradient(135deg, #2196f3, #03a9f4); }
    .status-cancelled { background: var(--gradient-primary); }

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

    .animated-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(211, 47, 47, 0.4);
    }

    .payment-table {
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(0,0,0,0.1);
    }

    .payment-table thead {
        background: var(--gradient-primary);
        color: white;
    }

    .payment-table tbody tr {
        transition: all 0.3s ease;
    }

    .payment-table tbody tr:hover {
        background: linear-gradient(135deg, #ffebee 0%, #fce4ec 100%);
        transform: scale(1.02);
    }

    .floating-icon {
        display: inline-block;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .amount-badge {
        background: var(--gradient-primary);
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: bold;
        box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3);
        animation: pulse 2s infinite;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(20px);
        border: 1px solid rgba(255, 255, 255, 0.2);
        box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    }

    .stagger-animation {
        animation-delay: calc(var(--i) * 0.1s);
    }

    .document-item {
        transition: all 0.3s ease;
        border-radius: 12px;
        margin-bottom: 10px;
        padding: 15px;
        background: linear-gradient(135deg, #fff 0%, #f8f9fa 100%);
    }

    .document-item:hover {
        background: var(--gradient-primary);
        color: white;
        transform: translateX(10px) scale(1.02);
    }

    .quick-action-btn {
        background: var(--gradient-primary);
        border: none;
        border-radius: 15px;
        padding: 15px 25px;
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .quick-action-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 30px rgba(211, 47, 47, 0.4);
    }

    .notes-card {
        background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%);
        color: white;
    }

    .alert-custom {
        border: none;
        border-radius: 15px;
        padding: 20px;
        animation: slideInDown 0.5s ease-out;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            {{-- Header Card --}}
            <div class="card animated-card mb-4 fade-in-left">
                <div class="card-header gradient-header text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">
                        <i class="fas fa-file-invoice fa-2x me-2 floating-icon"></i>
                        Détails de l'inscription #{{ $inscription->id }}
                    </h3>
                    <div class="d-flex align-items-center">
                        @php
                            $statusConfig = [
                                'pending' => ['class' => 'status-badge status-pending', 'text' => 'En attente'],
                                'active' => ['class' => 'status-badge status-active', 'text' => 'Active'],
                                'completed' => ['class' => 'status-badge status-completed', 'text' => 'Terminée'],
                                'cancelled' => ['class' => 'status-badge status-cancelled', 'text' => 'Annulée']
                            ];
                            $currentStatus = $statusConfig[$inscription->status] ?? ['class' => 'status-badge bg-secondary', 'text' => $inscription->status];
                        @endphp
                        <span class="{{ $currentStatus['class'] }} me-3 fs-6">{{ $currentStatus['text'] }}</span>

                        @can('update', $inscription)
                            <a href="{{ route('inscriptions.edit', $inscription) }}" class="btn animated-btn text-white me-2">
                                <i class="fas fa-edit me-1"></i> Modifier
                            </a>
                        @endcan
                        <a href="{{ route('inscriptions.index') }}" class="btn btn-outline-light animated-btn">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body glass-card">
                    @if(session('success'))
                        <div class="alert alert-success alert-custom alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            <strong>Succès!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            <strong>Erreur!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <div class="row">
                        <div class="col-md-6">
                            <h5 class="mb-3" style="color: var(--primary-red); font-weight: 700;">
                                <i class="fas fa-user-graduate me-2"></i>Informations Étudiant
                            </h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="custom-list-item stagger-animation" style="--i: 1">
                                    <strong style="color: var(--primary-red);">Nom :</strong> {{ $inscription->user->name }}
                                </li>
                                <li class="custom-list-item stagger-animation" style="--i: 2">
                                    <strong style="color: var(--primary-red);">Email :</strong> {{ $inscription->user->email }}
                                </li>
                                @if($inscription->user->phone)
                                <li class="custom-list-item stagger-animation" style="--i: 3">
                                    <strong style="color: var(--primary-red);">Téléphone :</strong> {{ $inscription->user->phone }}
                                </li>
                                @endif
                                
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5 class="mb-3" style="color: var(--accent-pink); font-weight: 700;">
                                <i class="fas fa-clipboard-list me-2"></i>Détails de l'inscription
                            </h5>
                            <ul class="list-group list-group-flush mb-3">
                                <li class="custom-list-item stagger-animation" style="--i: 1">
                                    <strong style="color: var(--accent-pink);">Date d'inscription :</strong> {{ $inscription->inscription_date->format('d/m/Y à H:i') }}
                                </li>
                                <li class="custom-list-item stagger-animation" style="--i: 2">
                                    <strong style="color: var(--accent-pink);">Plan de paiement :</strong>
                                    @php
                                        $paymentPlans = [
                                            'one_time' => 'Paiement unique',
                                            'monthly' => 'Mensuel',
                                            'custom' => 'Personnalisé'
                                        ];
                                    @endphp
                                    {{ $paymentPlans[$inscription->payment_plan] ?? $inscription->payment_plan }}
                                </li>
                                <li class="custom-list-item stagger-animation" style="--i: 3">
                                    <strong style="color: var(--accent-pink);">Montant total :</strong> 
                                    <span class="amount-badge">{{ number_format($inscription->total_amount, 2) }} DH</span>
                                </li>
                                <li class="custom-list-item stagger-animation" style="--i: 4">
                                    <strong style="color: var(--accent-pink);">Montant payé :</strong> 
                                    <span class="badge bg-success pulse-animation">{{ number_format($inscription->paid_amount, 2) }} DH</span>
                                </li>
                                <li class="custom-list-item stagger-animation" style="--i: 5">
                                    <strong style="color: var(--accent-pink);">Reste à payer :</strong> 
                                    <span class="badge" style="background: var(--gradient-secondary); color: white;">
                                        {{ number_format($inscription->total_amount - $inscription->paid_amount, 2) }} DH
                                    </span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Formation Details Card --}}
            <div class="card animated-card mb-4 fade-in-left" style="animation-delay: 0.2s;">
                <div class="card-header gradient-secondary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-graduation-cap me-2 floating-icon"></i>
                        Détails de la formation
                    </h4>
                </div>
                <div class="card-body glass-card">
                    <h5 class="card-title" style="color: var(--primary-red); font-weight: 700;">{{ $inscription->formation->title }}</h5>
                    <p class="card-text" style="color: #666; line-height: 1.6;">{{ $inscription->formation->description }}</p>
                    <ul class="list-group list-group-flush mt-3">
                        <li class="custom-list-item">
                            <strong style="color: var(--primary-red);">Catégorie :</strong> {{ $inscription->formation->category->name ?? 'N/A' }}
                        </li>
                        <li class="custom-list-item">
                            <strong style="color: var(--primary-red);">Prix :</strong> 
                            <span class="amount-badge">{{ number_format($inscription->formation->price, 2) }} DH</span>
                        </li>
                       
                        @if($inscription->formation->start_date)
                        <li class="custom-list-item">
                            <strong style="color: var(--primary-red);">Date de début :</strong> {{ $inscription->formation->start_date->format('d/m/Y') }}
                        </li>
                        @endif
                        @if($inscription->formation->end_date)
                        <li class="custom-list-item">
                            <strong style="color: var(--primary-red);">Date de fin :</strong> {{ $inscription->formation->end_date->format('d/m/Y') }}
                        </li>
                        @endif
                    </ul>
                </div>
            </div>

            {{-- Payment Schedule Card --}}
            <div class="card animated-card mb-4 fade-in-left" style="animation-delay: 0.4s;">
                <div class="card-header gradient-header text-white d-flex justify-content-between align-items-center">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-calendar-alt me-2 floating-icon"></i>
                        Calendrier de Paiement
                    </h4>
                    @can('update', $inscription)
                        <a href="{{ route('payments.create', ['inscription_id' => $inscription->id]) }}" class="btn animated-btn text-white">
                            <i class="fas fa-plus me-1"></i> Ajouter un paiement
                        </a>
                    @endcan
                </div>
                <div class="card-body glass-card">
                    @if($inscription->payments->isNotEmpty())
                        <div class="table-responsive">
                            <table class="table payment-table mb-0">
                                <thead>
                                    <tr>
                                        <th>Date d'échéance</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Méthode</th>
                                        <th>Date de paiement</th>
                                        @can('update', $inscription)
                                        <th class="text-center">Actions</th>
                                        @endcan
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inscription->payments->sortBy('due_date') as $payment)
                                        <tr>
                                            <td>{{ $payment->due_date ? $payment->due_date->format('d/m/Y') : 'N/A' }}</td>
                                            <td>
                                                <span class="amount-badge">{{ number_format($payment->amount, 2) }} DH</span>
                                            </td>
                                            <td>
                                                @php
                                                    $paymentStatusClass = [
                                                        'pending' => 'status-badge status-pending',
                                                        'paid' => 'status-badge status-active',
                                                        'overdue' => 'status-badge status-cancelled',
                                                        'refunded' => 'status-badge bg-secondary',
                                                        'failed' => 'status-badge status-cancelled',
                                                    ][$payment->status] ?? 'status-badge bg-secondary';
                                                @endphp
                                                <span class="{{ $paymentStatusClass }}">{{ ucfirst($payment->status) }}</span>
                                            </td>
                                            <td>{{ ucfirst(str_replace('_', ' ', $payment->payment_method)) }}</td>
                                            <td>{{ $payment->paid_date ? $payment->paid_date->format('d/m/Y') : 'N/A' }}</td>
                                            @can('update', $inscription)
                                            <td class="text-center">
                                                <div class="btn-group" role="group">
                                                    @if($payment->status === 'pending' || $payment->status === 'overdue')
                                                        <form action="{{ route('payments.markAsPaid', $payment) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('PATCH')
                                                            <button type="submit" class="btn btn-sm animated-btn" title="Marquer comme payé" style="background: var(--gradient-primary);">
                                                                <i class="fas fa-check"></i>
                                                            </button>
                                                        </form>
                                                    @endif
                                                    <a href="{{ route('payments.edit', $payment) }}" class="btn btn-sm btn-warning animated-btn" title="Modifier le paiement">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                    <form action="{{ route('payments.destroy', $payment) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce paiement ?');" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm animated-btn" title="Supprimer le paiement" style="background: var(--gradient-primary);">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                            @endcan
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <p class="text-muted">Aucun calendrier de paiement défini pour cette inscription.</p>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            {{-- Documents Card --}}
            <div class="card animated-card mb-4 fade-in-right">
                <div class="card-header gradient-secondary text-white">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-paperclip me-2 floating-icon"></i>
                        Documents joints
                    </h4>
                </div>
                <div class="card-body glass-card">
                    @if($payment->receipt_path)
                                                <a href="{{ Storage::url($payment->receipt_path) }}" target="_blank" class="text-blue-500 hover:text-blue-700 ml-2" title="Voir reçu"><i class="fas fa-file-download"></i> Voir reçu</a>
                                            @else
                                                <span class="text-gray-400 ml-2">(Pas de reçu)</span>
                                            @endif
                </div>
            </div>

            {{-- Notes Card --}}
            <div class="card animated-card mb-4 fade-in-right notes-card" style="animation-delay: 0.2s;">
                <div class="card-header text-white" style="background: var(--gradient-primary);">
                    <h4 class="card-title mb-0">
                        <i class="fas fa-comment-dots me-2 floating-icon"></i>
                        Notes d'inscription
                    </h4>
                </div>
                <div class="card-body">
                    @if($inscription->notes)
                        <p class="card-text text-white">{{ $inscription->notes }}</p>
                    @else
                 
                        <p class="text-light opacity-75">Aucune note pour cette inscription.</p>
                    @endif
                        <strong class="card-text text-white">inscrit par : {{ $inscription->inscrit_par }}</strong> 
                </div>
            </div>

             <div class="card animated-card mb-4 fade-in-right">
        <div class="card-header gradient-secondary text-white">
            <h4 class="card-title mb-0">
                <i class="fas fa-paperclip me-2 floating-icon"></i>
                Documents joints du etudiant
            </h4>
        </div>
        <div class="card-body glass-card">
            @if($inscription->user->documents)
                <ul class="list-unstyled">
                    @foreach($inscription->user->documents as $document)
                        <li class="document-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-file me-2"></i> {{ $document['name'] }}
                            </span>
                            <a href="{{ Storage::url($document['path']) }}" target="_blank" class="text-white btn btn-sm animated-btn" title="Voir le document">
                                <i class="fas fa-eye"></i>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-muted">Aucun document joint pour ce client.</p>
            @endif
        </div>

        
    </div>
            
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    
    const listItems = document.querySelectorAll('.stagger-animation');
    listItems.forEach((item, index) => {
        item.style.animationDelay = `${index * 0.1}s`;
    });

        const buttons = document.querySelectorAll('.animated-btn');
    buttons.forEach(button => {
        button.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px) scale(1.05)';
        });
        
        button.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0) scale(1)';
        });
    });

    // Add click animation to cards
    const cards = document.querySelectorAll('.animated-card');
    cards.forEach(card => {
        card.addEventListener('click', function(e) {
            if (!e.target.closest('button') && !e.target.closest('a')) {
                this.style.transform = 'scale(0.98)';
                setTimeout(() => {
                    this.style.transform = 'scale(1)';
                }, 150);
            }
        });
    });
});
</script>
@endpush
@endsection