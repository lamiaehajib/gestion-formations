
@extends('layouts.app')

@section('title', 'Plan de Paiement')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-purple-50 via-white to-blue-50">
    <!-- Header Section -->
    <div class="bg-white shadow-lg border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="py-6">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="bg-gradient-to-r from-purple-500 to-indigo-600 p-3 rounded-xl shadow-lg">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-3xl font-bold text-gray-900">Plan de Paiement</h1>
                            <p class="text-gray-600 mt-1">Générer un plan de paiement pour l'inscription</p>
                        </div>
                    </div>

                    <a href="{{ route('inscriptions.show', $inscription->id) }}" class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded-xl font-semibold transition-all duration-200 flex items-center space-x-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                        </svg>
                        <span>Retour à l'inscription</span>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Inscription Info -->
        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-gray-700 to-gray-800 px-8 py-6">
                <h2 class="text-2xl font-bold text-white">Informations de l'Inscription</h2>
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-4">
                    <div>
                        <p class="text-gray-300 text-sm">Étudiant</p>
                        <p class="text-white font-semibold">{{ $inscription->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-gray-300 text-sm">Formation</p>
                        <p class="text-white font-semibold">{{ $inscription->formation->title }}</p>
                    </div>
                    <div>
                        <p class="text-gray-300 text-sm">Montant Total</p>
                        <p class="text-white font-semibold">{{ number_format($inscription->total_amount, 2) }} MAD</p>
                    </div>
                    <div>
                        <p class="text-gray-300 text-sm">Montant Payé</p>
                        <p class="text-white font-semibold">{{ number_format($inscription->paid_amount, 2) }} MAD</p>
                    </div>
                </div>
            </div>
            
            <div class="p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm text-gray-600">Montant Restant</p>
                        <p class="text-3xl font-bold text-red-600">{{ number_format($inscription->total_amount - $inscription->paid_amount, 2) }} MAD</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm text-gray-600">Pourcentage Payé</p>
                        <div class="flex items-center space-x-2">
                            <div class="w-32 bg-gray-200 rounded-full h-3">
                                <div class="bg-green-500 h-3 rounded-full" style="width: {{ $inscription->total_amount > 0 ? ($inscription->paid_amount / $inscription->total_amount) * 100 : 0 }}%"></div>
                            </div>
                            <span class="text-lg font-semibold text-green-600">
                                {{ $inscription->total_amount > 0 ? round(($inscription->paid_amount / $inscription->total_amount) * 100, 1) : 0 }}%
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Payment Plan Form -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-purple-500 to-indigo-600 px-8 py-6">
                    <h2 class="text-2xl font-bold text-white">Générer le Plan</h2>
                    <p class="text-purple-100 mt-1">Configurez le plan de paiement</p>
                </div>

                <form action="{{ route('payments.generate-plan', $inscription->id) }}" method="POST" class="p-8" id="paymentPlanForm">
                    @csrf

                    <!-- Payment Plan Type -->
                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-3">
                            Type de Plan <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3">
                            <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-all">
                                <input type="radio" name="payment_plan" value="one_time" class="mr-3 text-purple-600" checked>
                                <div>
                                    <p class="font-semibold text-gray-900">Paiement Unique</p>
                                    <p class="text-sm text-gray-600">Un seul paiement pour le montant total</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-all">
                                <input type="radio" name="payment_plan" value="monthly" class="mr-3 text-purple-600">
                                <div>
                                    <p class="font-semibold text-gray-900">Paiements Mensuels</p>
                                    <p class="text-sm text-gray-600">Diviser en paiements mensuels égaux</p>
                                </div>
                            </label>
                            <label class="flex items-center p-4 border border-gray-200 rounded-xl hover:bg-gray-50 cursor-pointer transition-all">
                                <input type="radio" name="payment_plan" value="custom" class="mr-3 text-purple-600">
                                <div>
                                    <p class="font-semibold text-gray-900">Plan Personnalisé</p>
                                    <p class="text-sm text-gray-600">Nombre d'échéances personnalisé</p>
                                </div>
                            </label>
                        </div>
                        @error('payment_plan')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Installments (hidden by default) -->
                    <div id="installmentsDiv" class="mb-6 hidden">
                        <label for="installments" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nombre d'échéances <span class="text-red-500">*</span>
                        </label>
                        <select name="installments" id="installments"
                                class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <option value="">Sélectionner</option>
                            @for($i = 2; $i <= 12; $i++)
                                <option value="{{ $i }}">{{ $i }} échéances</option>
                            @endfor
                        </select>
                        @error('installments')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Down Payment -->
                    <div class="mb-6">
                        <label for="down_payment" class="block text-sm font-semibold text-gray-700 mb-2">
                            Acompte (optionnel)
                        </label>
                        <div class="relative">
                            <input type="number" name="down_payment" id="down_payment" step="0.01" min="0" 
                                   max="{{ $inscription->total_amount - $inscription->paid_amount }}"
                                   value="{{ old('down_payment') }}"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200">
                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                <span class="text-gray-500 font-medium">MAD</span>
                            </div>
                        </div>
                        <p class="text-sm text-gray-600 mt-1">Maximum: {{ number_format($inscription->total_amount - $inscription->paid_amount, 2) }} MAD</p>
                        @error('down_payment')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Submit Button -->
                    <button type="submit" 
                            class="w-full bg-gradient-to-r from-purple-500 to-indigo-600 hover:from-purple-600 hover:to-indigo-700 text-white px-8 py-4 rounded-xl font-semibold transition-all duration-200 shadow-lg hover:shadow-xl">
                        Générer le Plan de Paiement
                    </button>
                </form>
            </div>

            <!-- Preview Section -->
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                <div class="bg-gradient-to-r from-green-500 to-emerald-600 px-8 py-6">
                    <h2 class="text-2xl font-bold text-white">Aperçu du Plan</h2>
                    <p class="text-green-100 mt-1">Simulation des paiements</p>
                </div>

                <div class="p-8">
                    <div id="paymentPreview">
                        <div class="text-center text-gray-500 py-8">
                            <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            <p>Sélectionnez un type de plan pour voir l'aperçu</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Existing Payments -->
        @if($inscription->payments->count() > 0)
        <div class="mt-8 bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-600 to-gray-700 px-8 py-6">
                <h2 class="text-2xl font-bold text-white">Paiements Existants</h2>
                <p class="text-gray-200 mt-1">{{ $inscription->payments->count() }} paiement(s) enregistré(s)</p>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Date d'échéance</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Montant</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Méthode</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Statut</th>
                            <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Date de paiement</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($inscription->payments->sortBy('due_date') as $payment)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                {{ number_format($payment->amount, 2) }} MAD
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                @if($payment->payment_method === 'cash') Espèces
                                @elseif($payment->payment_method === 'credit_card') Carte de crédit
                                @else Virement bancaire @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex px-3 py-1 rounded-full text-xs font-semibold
                                    @if($payment->status === 'paid') bg-green-100 text-green-800
                                    @elseif($payment->status === 'late') bg-red-100 text-red-800
                                    @else bg-yellow-100 text-yellow-800 @endif">
                                    @if($payment->status === 'paid') Payé
                                    @elseif($payment->status === 'late') En retard
                                    @else En attente @endif
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-600">
                                {{ $payment->paid_date ? \Carbon\Carbon::parse($payment->paid_date)->format('d/m/Y') : '-' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentPlanRadios = document.querySelectorAll('input[name="payment_plan"]');
    const installmentsDiv = document.getElementById('installmentsDiv');
    const installmentsSelect = document.getElementById('installments');
    const downPaymentInput = document.getElementById('down_payment');
    const previewDiv = document.getElementById('paymentPreview');
    
    const remainingAmount = {{ $inscription->total_amount - $inscription->paid_amount }};
    
    // Handle payment plan type change
    paymentPlanRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            if (this.value === 'monthly' || this.value === 'custom') {
                installmentsDiv.classList.remove('hidden');
                installmentsSelect.required = true;
            } else {
                installmentsDiv.classList.add('hidden');
                installmentsSelect.required = false;
                installmentsSelect.value = '';
            }
            updatePreview();
        });
    });
    
    // Handle installments change
    installmentsSelect.addEventListener('change', updatePreview);
    downPaymentInput.addEventListener('input', updatePreview);
    
    function updatePreview() {
        const selectedPlan = document.querySelector('input[name="payment_plan"]:checked').value;
        const installments = parseInt(installmentsSelect.value) || 0;
        const downPayment = parseFloat(downPaymentInput.value) || 0;
        const remainingAfterDown = remainingAmount - downPayment;
        
        let previewHTML = '';
        
        if (selectedPlan === 'one_time') {
            previewHTML = generateOneTimePreview(downPayment, remainingAfterDown);
        } else if ((selectedPlan === 'monthly' || selectedPlan === 'custom') && installments > 0) {
            previewHTML = generateInstallmentsPreview(downPayment, remainingAfterDown, installments);
        } else {
            previewHTML = `
                <div class="text-center text-gray-500 py-8">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    <p>Complétez la configuration pour voir l'aperçu</p>
                </div>
            `;
        }
        
        previewDiv.innerHTML = previewHTML;
    }
    
    function generateOneTimePreview(downPayment, remaining) {
        let html = '<div class="space-y-4">';
        
        if (downPayment > 0) {
            html += `
                <div class="flex justify-between items-center p-4 bg-blue-50 rounded-xl border border-blue-200">
                    <div>
                        <p class="font-semibold text-blue-900">Acompte</p>
                        <p class="text-sm text-blue-700">Dû aujourd'hui</p>
                    </div>
                    <p class="text-xl font-bold text-blue-900">${downPayment.toFixed(2)} MAD</p>
                </div>
            `;
        }
        
        if (remaining > 0) {
            const dueDate = new Date();
            dueDate.setDate(dueDate.getDate() + 7);
            
            html += `
                <div class="flex justify-between items-center p-4 bg-green-50 rounded-xl border border-green-200">
                    <div>
                        <p class="font-semibold text-green-900">Paiement Final</p>
                        <p class="text-sm text-green-700">Dû le ${dueDate.toLocaleDateString('fr-FR')}</p>
                    </div>
                    <p class="text-xl font-bold text-green-900">${remaining.toFixed(2)} MAD</p>
                </div>
            `;
        }
        
        html += `
            <div class="mt-6 p-4 bg-gray-50 rounded-xl">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-900">Total:</span>
                    <span class="text-2xl font-bold text-gray-900">${remainingAmount.toFixed(2)} MAD</span>
                </div>
            </div>
        `;
        
        return html + '</div>';
    }
    
    function generateInstallmentsPreview(downPayment, remaining, installments) {
        let html = '<div class="space-y-3">';
        
        if (downPayment > 0) {
            html += `
                <div class="flex justify-between items-center p-3 bg-blue-50 rounded-lg border border-blue-200">
                    <div>
                        <p class="font-semibold text-blue-900 text-sm">Acompte</p>
                        <p class="text-xs text-blue-700">Aujourd'hui</p>
                    </div>
                    <p class="font-bold text-blue-900">${downPayment.toFixed(2)} MAD</p>
                </div>
            `;
        }
        
        if (remaining > 0) {
            const installmentAmount = remaining / installments;
            
            for (let i = 1; i <= installments; i++) {
                const dueDate = new Date();
                dueDate.setMonth(dueDate.getMonth() + i);
                
                html += `
                    <div class="flex justify-between items-center p-3 bg-green-50 rounded-lg border border-green-200">
                        <div>
                            <p class="font-semibold text-green-900 text-sm">Échéance ${i}</p>
                            <p class="text-xs text-green-700">${dueDate.toLocaleDateString('fr-FR')}</p>
                        </div>
                        <p class="font-bold text-green-900">${installmentAmount.toFixed(2)} MAD</p>
                    </div>
                `;
            }
        }
        
        html += `
            <div class="mt-4 p-4 bg-gray-50 rounded-xl">
                <div class="flex justify-between items-center">
                    <span class="font-semibold text-gray-900">Total:</span>
                    <span class="text-xl font-bold text-gray-900">${remainingAmount.toFixed(2)} MAD</span>
                </div>
            </div>
        `;
        
        return html + '</div>';
    }
    
    // Initial preview
    updatePreview();
});
</script>
@endsection
