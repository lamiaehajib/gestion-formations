{{-- resources/views/components/payment-reminder-modal.blade.php --}}

@php
    use Carbon\Carbon;
    
    $user = Auth::user();
    $showPaymentReminder = false;
    
    // Initialisation des variables pour éviter 'Undefined variable'
    $dueDate = null; 
    $daysRemaining = null; 
    $inscriptions = collect(); 
    $remindersGrouped = []; // Pour stocker les données pertinentes

    
    if ($user && $user->hasRole('Etudiant')) {
        // 1. Récupérer TOUS les rappels actifs pour cet étudiant
        $activeReminders = \App\Models\PaymentReminder::where('user_id', $user->id)
            ->where('is_active', true)
            ->where('expiry_date', '>=', Carbon::today())
            ->with(['formation', 'formation.category'])
            ->get();
        
        if ($activeReminders->isNotEmpty()) {
            
            foreach ($activeReminders as $reminder) {
                // 2. Vérifier l'inscription pour cette formation et le montant restant
                $inscription = \App\Models\Inscription::where('user_id', $user->id)
                    ->where('formation_id', $reminder->formation_id)
                    ->whereIn('status', ['active', 'pending'])
                    ->whereRaw('(total_amount - paid_amount) > 0.01') // S'assurer qu'il reste à payer
                    ->with(['formation', 'formation.category'])
                    ->first();
                
                if ($inscription) {
                    $remindersGrouped[] = [
                        'reminder' => $reminder,
                        'inscription' => $inscription,
                        'daysRemaining' => Carbon::today()->diffInDays($reminder->expiry_date, false)
                    ];
                }
            }
            
            if (!empty($remindersGrouped)) {
                $showPaymentReminder = true;
                
                // 3. Déterminer la plus proche échéance pour l'affichage principal
                $closestReminder = collect($remindersGrouped)->sortBy('daysRemaining')->first();
                
                if ($closestReminder) {
                    // La date d'échéance est l'expiry_date du rappel le plus proche
                    $dueDate = $closestReminder['reminder']->expiry_date; 
                    $daysRemaining = $closestReminder['daysRemaining'];
                }

                // 4. Extraire la liste des inscriptions pour la boucle d'affichage
                $inscriptions = collect($remindersGrouped)->pluck('inscription'); 
            }
        }
    }
@endphp

@if($showPaymentReminder)
<div class="modal fade" id="paymentReminderModal" tabindex="-1" aria-labelledby="paymentReminderModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 20px; border: 3px solid #D32F2F; overflow: hidden;">
            <div class="modal-header text-white" style="background: linear-gradient(135deg, #D32F2F 0%, #C2185B 100%); border: none; padding: 25px 30px;">
                <div class="d-flex align-items-center w-100">
                    <i class="fas fa-exclamation-triangle fa-2x me-3 animate__animated animate__wobble animate__infinite" style="animation-duration: 2s;"></i>
                    <div class="flex-grow-1">
                        <h4 class="modal-title fw-bold mb-1" id="paymentReminderModalLabel">
                            <i class="fas fa-bell me-2"></i>Rappel Important de Paiement
                        </h4>
                        <p class="mb-0 opacity-90" style="font-size: 0.9rem;">
                            Échéance : **{{ $dueDate ? $dueDate->format('d/m/Y') : 'Non définie' }}**
                        </p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body" style="padding: 30px;">
                <div class="alert alert-warning d-flex align-items-center mb-4" role="alert" style="border-left: 5px solid #f59e0b; background-color: #fffbeb;">
                    <i class="fas fa-clock fa-2x me-3 text-warning"></i>
                    <div>
                        <strong class="d-block mb-1">⏰ Temps restant :</strong>
                        @if($daysRemaining !== null)
                            @if($daysRemaining > 0)
                                <span class="fs-5 fw-bold text-danger">{{ $daysRemaining }} jour(s) restant(s)</span>
                            @else
                                <span class="fs-5 fw-bold text-danger">Échéance aujourd'hui !</span>
                            @endif
                        @else
                            <span class="fs-5 fw-bold text-danger">Date d'échéance non spécifiée.</span>
                        @endif
                    </div>
                </div>

                <div class="mb-4">
                    <p class="lead mb-3" style="color: #1f2937;">
                        <i class="fas fa-user-graduate text-primary me-2"></i>
                        Cher(e) **{{ $user->name }}**,
                    </p>
                    <p style="color: #4b5563; line-height: 1.8;">
                        Nous vous rappelons que le paiement de vos mensualités pour vos formations doit être effectué 
                        <strong class="text-danger">avant le {{ $dueDate ? $dueDate->format('d/m/Y') : 'Non définie' }}</strong>. Veuillez régulariser votre situation 
                        dans les plus brefs délais pour éviter toute interruption de votre accès aux cours.
                    </p>
                </div>

                <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #fef2f2 0%, #fff 100%);">
                    <div class="card-header bg-transparent border-bottom" style="padding: 15px 20px;">
                        <h5 class="mb-0 text-danger">
                            <i class="fas fa-file-invoice-dollar me-2"></i>
                            Formations concernées ({{ $inscriptions->count() }})
                        </h5>
                    </div>
                    <div class="card-body" style="padding: 20px; max-height: 300px; overflow-y: auto;">
                        @foreach($inscriptions as $inscription)
                            @php
                                $remainingAmount = $inscription->total_amount - $inscription->paid_amount;
                                $paymentProgress = ($inscription->total_amount > 0) ? ($inscription->paid_amount / $inscription->total_amount) * 100 : 0;
                                $remainingAmount = max(0, $remainingAmount); // S'assurer qu'il n'est pas négatif
                            @endphp
                            <div class="mb-3 pb-3 @if(!$loop->last) border-bottom @endif">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-2 fw-bold text-dark">
                                            <i class="fas fa-graduation-cap text-primary me-2"></i>
                                            {{ $inscription->formation->title }}
                                        </h6>
                                        <div class="small text-muted mb-2">
                                            @if($inscription->formation->category)
                                                <span class="badge bg-info me-2">
                                                    {{ $inscription->formation->category->name }}
                                                </span>
                                            @endif
                                            <span class="badge bg-secondary">
                                                Statut: {{ ucfirst($inscription->status) }}
                                            </span>
                                        </div>
                                        
                                        <div class="mt-2">
                                            <div class="d-flex justify-content-between align-items-center mb-1">
                                                <small class="text-muted">Progression du paiement</small>
                                                <small class="fw-bold">{{ number_format($paymentProgress, 1) }}%</small>
                                            </div>
                                            <div class="progress" style="height: 8px; border-radius: 10px;">
                                                <div class="progress-bar bg-gradient" 
                                                    style="width: {{ $paymentProgress }}%; background: linear-gradient(90deg, #D32F2F, #C2185B);"
                                                    role="progressbar"></div>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <div class="text-end ms-3">
                                        <div class="mb-1">
                                            <small class="text-muted d-block">Montant total</small>
                                            <strong style="color: #1f2937;">{{ number_format($inscription->total_amount, 2) }} MAD</strong>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Reste à payer</small>
                                            <strong class="text-danger fs-5">{{ number_format($remainingAmount, 2) }} MAD</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <div class="mt-4 p-3 rounded" style="background-color: #f3f4f6;">
                    <p class="mb-2 fw-bold" style="color: #1f2937;">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Besoin d'aide ?
                    </p>
                    <p class="mb-0 small" style="color: #4b5563;">
                        Pour toute question concernant vos paiements, veuillez contacter le service financier 
                        ou consulter votre espace étudiant pour plus de détails sur vos échéances.
                    </p>
                </div>
            </div>

            <div class="modal-footer" style="background-color: #f9fafb; border-top: 1px solid #e5e7eb; padding: 20px 30px;">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal" id="remindLaterBtn">
                    <i class="fas fa-clock me-2"></i>Me le rappeler plus tard
                </button>
                <a href="{{ route('inscriptions.index') }}" class="btn text-white" style="background: linear-gradient(135deg, #D32F2F, #C2185B);">
                    <i class="fas fa-money-check-alt me-2"></i>Voir mes inscriptions
                </a>
            </div>
        </div>
    </div>
</div>

<style>
    /* Animation pour le modal */
    #paymentReminderModal .modal-content {
        animation: slideInDown 0.5s ease-out;
    }

    @keyframes slideInDown {
        from {
            opacity: 0;
            transform: translateY(-50px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Scrollbar personnalisée pour la liste */
    #paymentReminderModal .card-body::-webkit-scrollbar {
        width: 6px;
    }

    #paymentReminderModal .card-body::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    #paymentReminderModal .card-body::-webkit-scrollbar-thumb {
        background: linear-gradient(45deg, #D32F2F, #C2185B);
        border-radius: 10px;
    }

    /* Effet hover sur les boutons */
    #paymentReminderModal .modal-footer .btn {
        transition: all 0.3s ease;
    }

    #paymentReminderModal .modal-footer .btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.2);
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modalElement = document.getElementById('paymentReminderModal');
        const remindLaterBtn = document.getElementById('remindLaterBtn');
        
        // Assurez-vous que l'objet user est disponible pour la clé localStorage
        @if($user) 
            const userId = '{{ $user->id }}';
        @else 
            const userId = 'guest';
        @endif
        
        if (modalElement) {
            // Vérifier si l'utilisateur a déjà fermé le modal aujourd'hui
            const lastDismissed = localStorage.getItem('paymentReminderDismissed_' + userId);
            const today = new Date().toDateString();
            
            if (lastDismissed !== today) {
                // Afficher le modal après un court délai
                setTimeout(() => {
                    const modal = new bootstrap.Modal(modalElement);
                    modal.show();
                }, 1500);
            }
            
            // Enregistrer la fermeture du modal
            if (remindLaterBtn) {
                remindLaterBtn.addEventListener('click', function() {
                    localStorage.setItem('paymentReminderDismissed_' + userId, today);
                });
            }
            
            // Enregistrer également lors de la fermeture par le X
            modalElement.addEventListener('hidden.bs.modal', function() {
                localStorage.setItem('paymentReminderDismissed_' + userId, today);
            });
        }
    });
</script>
@endif