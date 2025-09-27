<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Inscription;
use App\Models\Formation;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

use Illuminate\Support\Facades\Mail; 
use App\Mail\NewPaymentNotification; 



class PaymentController extends Controller
{    
    /**
     * Display a listing of payments
     */
    public function index(Request $request)
    {
        $query = Payment::with(['inscription.user', 'inscription.formation']);

        // Check if the authenticated user is a student
        if (Auth::user() && Auth::user()->hasRole('Etudiant')) {
            $userId = Auth::id();
            $query->whereHas('inscription', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        // Filters
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('due_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('due_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('inscription', function($q) use ($search) {
                $q->whereHas('user', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                       ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('formation', function($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%");
                });
            })->orWhere('reference', 'like', "%{$search}%")
              ->orWhere('transaction_id', 'like', "%{$search}%");
        }

        $payments = $query->orderBy('created_at', 'desc')->paginate(15);

        // Statistics - pass the request to getPaymentStats to apply date filters
        $stats = $this->getPaymentStats($request);

        return view('payments.index', compact('payments', 'stats'));
    }

    /**
     * Show payment details
     */
    public function show($id)
    {
        $payment = Payment::with(['inscription.user', 'inscription.formation.consultant'])->findOrFail($id);

        // Authorization check: Only admin or the payment's associated student can view
        if (!Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']) && $payment->inscription->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action. You do not have permission to view this payment.');
        }

        return view('payments.show', compact('payment'));
    }

    /**
     * Show create payment form
     */
    public function create(Request $request)
    {
        // If the authenticated user is an admin, show all inscriptions that are not fully paid.
        if (Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin'])) {
            $inscriptions = Inscription::with(['user', 'formation'])
                ->whereRaw('total_amount > paid_amount') // Only show inscriptions not fully paid
                ->whereIn('status', ['active', 'pending', 'completed']) // Admins can see all relevant statuses
                ->get();
        } else {
            // Logic for students:
            // Fetch inscriptions for the current student that are not fully paid
            // AND are either 'active' or 'completed' (if they still have a remaining balance).
            // 'pending' inscriptions are excluded for students here, as per your request.
            $userId = Auth::id();
            $inscriptions = Inscription::with(['user', 'formation'])
                ->where('user_id', $userId) // Only current student's inscriptions
                ->whereRaw('total_amount > paid_amount') // Inscriptions not fully paid yet
                ->whereIn('status', ['active', 'completed']) // <-- CHANGED THIS LINE
                ->get();
        }

        return view('payments.create', compact('inscriptions'));
    }

    /**
     * Store a new payment
     */
   
public function store(Request $request)
    {
        $inscription = Inscription::with(['formation.category'])->findOrFail($request->inscription_id);

        // Authorization check
        if (!Auth::user() || (!Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']) && $inscription->user_id !== Auth::id())) {
            abort(403, 'Unauthorized action. You do not have permission to create this payment for this inscription.');
        }

        if (!Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']) && $inscription->status === 'pending') {
            return redirect()->back()->with('error', 'Vous ne pouvez pas effectuer un paiement pour une inscription en attente. Veuillez attendre que votre inscription soit activée.');
        }

        $validator = Validator::make($request->all(), [
            'inscription_id' => 'required|exists:inscriptions,id',
            'payment_choice' => 'required|in:full_remaining,next_installment,custom_amount',
            'amount_to_pay' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,card,cheque', // تم إضافة 'card' و 'cheque'
            'payment_description' => 'nullable|string|max:1000',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        DB::beginTransaction();
        try {
            $inscription->refresh();

            $totalAmount = $inscription->total_amount;
            $paidAmountSoFar = $inscription->paid_amount;
            $remainingAmount = $totalAmount - $paidAmountSoFar;

            $amountToPay = (float) $request->amount_to_pay;

            $epsilon = 0.01;
            
            // Validation du montant à payer selon le choix
            if ($request->payment_choice === 'full_remaining') {
                $expectedAmount = $remainingAmount;
                if (abs($amountToPay - $expectedAmount) > $epsilon) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Le montant saisi ne correspond pas au montant restant total.')->withInput();
                }
            } elseif ($request->payment_choice === 'next_installment') {
                $expectedAmount = $inscription->amount_per_installment;
                if ($remainingAmount < $expectedAmount) {
                    $expectedAmount = $remainingAmount;
                }
                if (abs($amountToPay - $expectedAmount) > $epsilon) {
                    DB::rollBack();
                    return redirect()->back()->with('error', 'Le montant saisi ne correspond pas au montant du prochain versement.')->withInput();
                }
            } elseif ($amountToPay > $remainingAmount + $epsilon) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Le montant à payer dépasse le reste dû pour cette inscription.')->withInput();
            }

            $receiptPath = null;
            if ($request->hasFile('receipt_file')) {
                $receiptPath = $request->file('receipt_file')->store('payment_receipts/' . $inscription->id, 'public');
            }

            $paymentStatus = 'pending';
            $paidDate = null;
            if (Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin'])) {
                $paymentStatus = 'paid';
                $paidDate = Carbon::now();
            }

            // ==========================================================
            // ✨ المنطق المُصحَّح لتحديد due_date ✨
            // ==========================================================
            $paymentDueDate = null;
            
            // نجلب نوع الـ formation
            $formationCategoryName = optional($inscription->formation->category)->name ?? null;

            // إذا كان نظام أقساط (chosen_installments > 1) 
            // ونوع الـ formation هو Master/Licence Professionnelle
            if ($inscription->chosen_installments > 1 && in_array($formationCategoryName, ['Master Professionnelle', 'Licence Professionnelle','LICENCE PROFESSIONNELLE RECONNU'])) {
                // نحدد تاريخ الاستحقاق هو اليوم 01 من الشهر الحالي
                $paymentDueDate = Carbon::now()->startOfMonth()->day(1);

                // إذا كان تاريخ الاستحقاق التالي في الـ inscription موجود
                // فهذا يعني أن هذا ليس أول قسط، ونستخدم التاريخ الموجود
                if ($inscription->next_installment_due_date) {
                    $paymentDueDate = $inscription->next_installment_due_date;
                }
            }
            
            // في الحالات الأخرى (دفع كامل، أو أقساط عادية)، 
            // إذا لم يتم تحديد تاريخ، نستخدم تاريخ افتراضي (مثلاً: 7 أيام من الآن)
            if (!$paymentDueDate) {
                 $paymentDueDate = Carbon::now()->addDays(7);
            }
            // ==========================================================
            
            $payment = Payment::create([
                'inscription_id' => $inscription->id,
                'amount' => $amountToPay,
                'due_date' => $paymentDueDate, // ✨ استخدام المتغير الجديد
                'paid_date' => $paidDate,
                'payment_method' => $request->payment_method,
                'status' => $paymentStatus,
                'reference' => $request->payment_description,
                'receipt_path' => $receiptPath,
                'created_by_user_id' => Auth::id(),
            ]);

            if ($payment->status === 'paid') {
                // تحديث مبلغ الانسكريبشن
                $inscription->paid_amount += $payment->amount;

                // تحديث عدد الأقساط المتبقية وتاريخ الاستحقاق التالي
                if ($inscription->remaining_installments > 0) {
                     $inscription->remaining_installments -= 1;
                }

                if ($inscription->remaining_installments > 0 && in_array($formationCategoryName, ['Master Professionnelle', 'Licence Professionnelle','LICENCE PROFESSIONNELLE RECONNU'])) {
                    // إذا كان باقي أقساط، نحدد تاريخ الاستحقاق التالي هو 01 من الشهر المقبل
                    $inscription->next_installment_due_date = Carbon::now()->addMonthNoOverflow()->day(1);
                } else {
                    // إذا سالاو الأقساط، نلغي تاريخ الاستحقاق
                    $inscription->next_installment_due_date = null;
                }
                
                $inscription->save();
                $this->createPaymentNotification($payment);
            }

            $rolesToSendEmail = ['Admin', 'Finance', 'Super Admin'];
            $admins = User::role($rolesToSendEmail)->get();
            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new NewPaymentNotification($payment));
            }

            DB::commit();

            return redirect()->route('payments.show', $payment->id)
                ->with('success', 'Votre paiement a été enregistré avec succès! Son statut est ' . ($paymentStatus === 'paid' ? 'Payé.' : 'En attente de validation.'));

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing new payment: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de l\'enregistrement de votre paiement. Veuillez réessayer.')
                ->withInput();
        }
    }



    /**
     * Show edit payment form
     */
   public function edit($id)
{
    // تأكد من تحميل علاقة 'inscription' و 'creator'
    $payment = Payment::with(['inscription', 'creator'])->findOrFail($id);

    // Authorization check: Only Admin, Finance, Super Admin or the payment's associated student can edit
    if (!Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']) && $payment->inscription->user_id !== Auth::id()) {
        abort(403, 'Unauthorized action. You do not have permission to edit this payment.');
    }

    $currentUserIsAdmin = Auth::user() && Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']);
    $paymentCreatedByAdmin = $payment->creator && $payment->creator->hasRole('Admin');

    return view('payments.edit', compact('payment', 'currentUserIsAdmin', 'paymentCreatedByAdmin'));
}

    /**
     * Update payment
     */
    public function update(Request $request, $id)
    {
        // Find the payment and its related inscription and creator
        $payment = Payment::with('inscription')->findOrFail($id);
        $inscription = $payment->inscription;
        
        // Check for authorization: Admin, Finance, Super Admin or the student who owns the inscription
        if (!Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']) && $inscription->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action. You do not have permission to update this payment.');
        }
        
        // Define validation rules
        $rules = [
            'amount_to_pay' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,cheque,credit_card', // تأكد من إضافة credit_card هنا
            'payment_description' => 'nullable|string|max:1000',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            
            // Admins, Finance, and Super Admins can change the status, students cannot
            'status' => (Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']) ? 'required|in:pending,paid,late' : 'nullable'),

            // ✨ إضافة قاعدة التحقق من due_date هنا ✨
            'due_date' => (Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']) ? 'nullable|date' : 'nullable'),
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            // Recalculate remaining amount for validation
            $totalPaidExceptThis = $inscription->paid_amount - $payment->amount;
            $remainingAmount = $inscription->total_amount - $totalPaidExceptThis;

            $amountToPay = (float) $request->amount_to_pay;

            // Validation to prevent overpaying, similar to the store function
            $epsilon = 0.01;
            if ($amountToPay > $remainingAmount + $epsilon) {
                DB::rollBack();
                return redirect()->back()->with('error', 'Le montant à payer dépasse le reste dû pour cette inscription.')->withInput();
            }

            // Handle receipt file update
            $receiptPath = $payment->receipt_path;
            if ($request->hasFile('receipt_file')) {
                // Delete old receipt if it exists
                if ($receiptPath) {
                    Storage::disk('public')->delete($receiptPath);
                }
                $receiptPath = $request->file('receipt_file')->store('payment_receipts/' . $inscription->id, 'public');
            }

            // Set payment status based on user role
            $paymentStatus = $payment->status;
            $paidDate = $payment->paid_date;
            $oldStatus = $payment->status; // Keep old status for later logic
            
            if (Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']) && $request->has('status')) {
                $paymentStatus = $request->status;
                if ($paymentStatus === 'paid' && !$paidDate) {
                    $paidDate = now();
                } elseif ($paymentStatus !== 'paid') {
                    $paidDate = null;
                }
            }
            
            // Update the payment
            $payment->update([
                'amount' => $amountToPay,
                'payment_method' => $request->payment_method,
                'reference' => $request->payment_description,
                'status' => $paymentStatus,
                'paid_date' => $paidDate,
                'receipt_path' => $receiptPath,
                
                // ✨ تحديث due_date فقط إذا كان المستخدم أدمن ✨
                'due_date' => Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin'])
                                ? $request->due_date
                                : $payment->due_date,
            ]);
            
            // Update the inscription's paid amount based on the new payment status and amount
            $inscription->paid_amount = $totalPaidExceptThis + $payment->amount;
            
            // تحديث remaining_installments بناءً على التغيير في status
            if ($oldStatus !== 'paid' && $payment->status === 'paid' && $inscription->remaining_installments > 0) {
                 $inscription->remaining_installments -= 1;
            } elseif ($oldStatus === 'paid' && $payment->status !== 'paid' && $inscription->remaining_installments < $inscription->chosen_installments) {
                 $inscription->remaining_installments += 1;
            }
            
            $inscription->save();

            DB::commit();

            return redirect()->route('payments.show', $payment->id)
                ->with('success', 'Paiement mis à jour avec succès.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating payment: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la mise à jour du paiement. Veuillez réessayer.')
                ->withInput();
        }
    }


   

    public function destroy($id)
    {
        // Authorization check: Only admin can delete payments
        if (!Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin'])) {
            abort(403, 'Unauthorized action. You do not have permission to delete payments.');
        }

        DB::beginTransaction();
        try {
            $payment = Payment::with('inscription')->findOrFail($id);

            // If the payment was marked as 'paid', deduct its amount from the inscription's paid_amount
            if ($payment->status === 'paid') {
                $inscription = $payment->inscription;
                if ($inscription) {
                    $inscription->paid_amount -= $payment->amount;
                    // No change to remaining_installments here, as deleting a payment means it was never made.
                    $inscription->save();
                } else {
                    // Log an error if inscription is somehow missing, though `with('inscription')` should prevent this.
                    Log::error("Attempted to delete a paid payment (ID: {$id}) but its associated inscription was not found.");
                }
            }

            // Delete associated receipt file if it exists
            if ($payment->receipt_path && Storage::disk('public')->exists($payment->receipt_path)) {
                Storage::disk('public')->delete($payment->receipt_path);
            }

            $payment->delete();

            DB::commit();

            return redirect()->route('payments.index')
                ->with('success', 'Paiement supprimé avec succès.');

        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            DB::rollBack();
            Log::error('Payment not found for deletion. ID: ' . $id . '. Error: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Paiement introuvable.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error deleting payment: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
            return redirect()->back()
                ->with('error', 'Une erreur est survenue lors de la suppression du paiement. Veuillez réessayer.');
        }
    }


    /**
     * Update payment status via Ajax
     */
     public function updateStatus(Request $request, $id)
{
    try {
        $payment = Payment::with('inscription')->findOrFail($id);

        if (!Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin'])) {
            \Log::warning("Unauthorized attempt to update payment status. User ID: " . (Auth::id() ?? 'Guest'));
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,paid,late',
            'paid_date' => 'nullable|date',
            'late_fee' => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed for updateStatus. Payment ID: ' . $id . ', Errors: ' . json_encode($validator->errors()->toArray()));
            return response()->json(['errors' => $validator->errors(), 'message' => 'Validation failed.'], 422);
        }

        DB::beginTransaction();

        $inscription = $payment->inscription;
        $oldStatus = $payment->status;
        
        // 1. Mettre à jour le statut du paiement
        $payment->status = $request->status;
        $payment->paid_date = ($request->status === 'paid') ? ($request->paid_date ?? now()) : null;
        $payment->save();

        // --------------------------------------------------------------------------------
        // LOGIQUE CLÉ: RECALCULER PAID_AMOUNT ET GÉRER LES STATUTS
        // --------------------------------------------------------------------------------

        // 2. Recalculer le montant total payé (le FIX contre le doublage)
        // La source de vérité est la somme de TOUS les paiements 'paid'
        $totalPaidVerified = $inscription->payments()->where('status', 'paid')->sum('amount');
        
        // Mettre à jour paid_amount de l'inscription avec la somme vérifiée
        $inscription->paid_amount = $totalPaidVerified;
        
        // 3. Gérer la déduction/réintégration des acomptes (remaining_installments)
        if ($oldStatus !== 'paid' && $payment->status === 'paid') {
            // Un paiement vient d'être validé -> Déduire un acompte
            if ($inscription->remaining_installments > 0) {
                 $inscription->remaining_installments -= 1;
            }
        } elseif ($oldStatus === 'paid' && $payment->status !== 'paid') {
            // Un paiement est annulé -> Rajouter un acompte
             if ($inscription->remaining_installments < $inscription->chosen_installments) {
                 $inscription->remaining_installments += 1;
             }
        }

        // 4. Mettre à jour le statut de l'inscription (Active / Completed)
        $epsilon = 0.01;
        if ($inscription->paid_amount >= $inscription->total_amount - $epsilon) {
            $inscription->status = 'completed';
            $inscription->remaining_installments = 0;
            $inscription->next_installment_due_date = null;
        } elseif ($inscription->status === 'pending' && $payment->status === 'paid' && $totalPaidVerified > 0) {
            // Activation de l'inscription si elle était en attente et que la première preuve est validée
            $inscription->status = 'active'; 
        }

        // 5. Mise à jour de la date d'échéance suivante
        if ($inscription->status === 'active' && $inscription->remaining_installments > 0) {
            // Si l'inscription est active et qu'il reste des acomptes, définir le prochain
            $inscription->next_installment_due_date = Carbon::now()->addMonth()->day(5);
        } else {
             // S'il n'y a plus d'acomptes (completed), on efface la date
            $inscription->next_installment_due_date = null;
        }

        $inscription->save();
        
        // --------------------------------------------------------------------------------
        // FIN DE LA LOGIQUE DE CORRECTION
        // --------------------------------------------------------------------------------

        // Logique de notification (à garder telle quelle)
        if ($payment->status === 'paid') {
             $this->createPaymentNotification($payment);
        }
       
        DB::commit();

        return response()->json([
            'success' => true,
            'message' => 'Payment status updated successfully',
            'new_status' => $payment->status,
            'updated_paid_amount' => number_format($inscription->paid_amount, 2),
            'updated_remaining_amount' => number_format($inscription->total_amount - $inscription->paid_amount, 2),
            'inscription_status' => $inscription->status
        ]);

    } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
        \Log::error('Payment not found. ID: ' . $id . '. Error: ' . $e->getMessage());
        DB::rollBack();
        return response()->json(['success' => false, 'message' => 'Payment not found.'], 404);
    } catch (\Exception $e) {
        DB::rollBack();
        \Log::error('Error updating payment status (General Exception): ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        return response()->json(['success' => false, 'message' => 'An error occurred while updating payment status. Please try again.'], 500);
    }
}

    /**
     * Get payments by inscription
     */
    public function byInscription($inscriptionId)
    {
        $payments = Payment::where('inscription_id', $inscriptionId)
            ->orderBy('due_date', 'asc')
            ->get();

        // Optional: Add authorization here if only admin or the specific student can view
        $inscription = Inscription::findOrFail($inscriptionId);
        if (!Auth::user()->hasRole('Admin') && $inscription->user_id !== Auth::id()) {
             abort(403, 'Unauthorized action.');
        }

        return response()->json($payments);
    }

    /**
     * Bulk update payments
     */
    public function bulkUpdate(Request $request)
    {
        // Authorization check: Only admin can perform bulk updates
        if (!Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin'])) {
            return response()->json(['success' => false, 'message' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'payment_ids' => 'required|array',
            'payment_ids.*' => 'exists:payments,id',
            'action' => 'required|in:mark_paid,mark_late,delete',
            'paid_date' => 'nullable|date'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        try {
            DB::beginTransaction();

            $payments = Payment::whereIn('id', $request->payment_ids)->get();

            foreach ($payments as $payment) {
                switch ($request->action) {
                    case 'mark_paid':
                        if ($payment->status !== 'paid') {
                            $payment->status = 'paid';
                            $payment->paid_date = $request->paid_date ?? now();
                            $payment->save();

                            // Update inscription
                            $inscription = $payment->inscription;
                            $inscription->paid_amount += $payment->amount;
                            $inscription->save();
                        }
                        break;

                    case 'mark_late':
                        $payment->status = 'late';
                        $payment->save();
                        break;

                    case 'delete':
                        if ($payment->status === 'paid') {
                            $inscription = $payment->inscription;
                            $inscription->paid_amount -= $payment->amount;
                            $inscription->save();
                        }
                        $payment->delete();
                        break;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Bulk update completed successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error in bulk update: ' . $e->getMessage()
            ], 500);
        }
    }

    // Private helper methods

    private function getPaymentStats($request = null)
    {
        $dateFrom = $request && $request->filled('date_from') ? $request->date_from : now()->startOfMonth();
        $dateTo = $request && $request->filled('date_to') ? $request->date_to : now()->endOfMonth();

        $baseQuery = Payment::query();

        // Apply student specific filter to stats as well
        if (Auth::user() && Auth::user()->hasRole('Etudiant')) {
            $userId = Auth::id();
            $baseQuery->whereHas('inscription', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        return [
            'total_payments' => (clone $baseQuery)->whereBetween('created_at', [$dateFrom, $dateTo])->count(),
            'total_amount' => (clone $baseQuery)->whereBetween('created_at', [$dateFrom, $dateTo])->sum('amount'),
            'paid_amount' => (clone $baseQuery)->where('status', 'paid')
                ->whereBetween('paid_date', [$dateFrom, $dateTo])->sum('amount'), // Use paid_date for paid amount
            'pending_amount' => (clone $baseQuery)->where('status', 'pending')
                ->whereBetween('due_date', [$dateFrom, $dateTo])->sum('amount'), // Use due_date for pending amount
            'late_amount' => (clone $baseQuery)->where('status', 'late')
                ->whereBetween('due_date', [$dateFrom, $dateTo])->sum('amount'), // Use due_date for late amount
            'payment_methods' => (clone $baseQuery)->whereBetween('created_at', [$dateFrom, $dateTo])
                ->groupBy('payment_method')
                ->selectRaw('payment_method, count(*) as count, sum(amount) as total')
                ->get(),
            'monthly_revenue' => (clone $baseQuery)->where('status', 'paid')
                ->whereBetween('paid_date', [$dateFrom, $dateTo])
                ->groupByRaw('MONTH(paid_date)')
                ->selectRaw('MONTH(paid_date) as month, sum(amount) as total')
                ->get(),
            'status_breakdown' => (clone $baseQuery)->whereBetween('created_at', [$dateFrom, $dateTo])
                ->groupBy('status')
                ->selectRaw('status, count(*) as count, sum(amount) as total')
                ->get()
        ];
    }

    private function createPaymentNotification($payment)
    {
        $inscription = $payment->inscription;
        $user = $inscription->user;

        $title = '';
        $message = '';
        $type = 'payment';

        switch ($payment->status) {
            case 'paid':
                $title = 'Payment Confirmed';
                $message = "Your payment of {$payment->amount} MAD for {$inscription->formation->title} has been confirmed.";
                $type = 'success';
                break;
            case 'late':
                $title = 'Late Payment Notice';
                $message = "Your payment of {$payment->amount} MAD for {$inscription->formation->title} is overdue.";
                $type = 'warning';
                break;
        }

        if ($title && $message) {
            \App\Models\Notification::create([
                'user_id' => $user->id,
                'title' => $title,
                'message' => $message,
                'type' => $type,
                'data' => json_encode([
                    'payment_id' => $payment->id,
                    'inscription_id' => $inscription->id,
                    'amount' => $payment->amount
                ]),
                'is_read' => false
            ]);
        }
    }

   
  public function getOverdueStudents(Request $request)
{
    // Ensure only admins can access this
   if (!Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin'])) {
        return response()->json(['message' => 'Unauthorized.'], 403);
    }

    $overduePayments = Payment::with(['inscription.user', 'inscription.formation'])
        ->where('status', 'pending')
        ->where('due_date', '<', Carbon::now()->startOfDay()) // Ayi paiement fat l'waqt dyalo
        ->orderBy('due_date', 'asc')
        ->get();

    // The rest of the code is the same...
    $studentsWithOverduePayments = [];
    foreach ($overduePayments as $payment) {
        $inscriptionId = $payment->inscription->id;
        if (!isset($studentsWithOverduePayments[$inscriptionId])) {
            $studentsWithOverduePayments[$inscriptionId] = [
                'inscription_id' => $inscriptionId,
                'user_id' => $payment->inscription->user->id,
                'student_name' => $payment->inscription->user->name,
                'student_email' => $payment->inscription->user->email,
                'formation_title' => $payment->inscription->formation->title,
                'overdue_payments' => []
            ];
        }
        $studentsWithOverduePayments[$inscriptionId]['overdue_payments'][] = [
            'payment_id' => $payment->id,
            'amount' => $payment->amount,
            'due_date' => $payment->due_date->format('d/m/Y'),
            'status' => $payment->status
        ];
    }

    return response()->json(array_values($studentsWithOverduePayments));
}

    /**
     * Export payments to CSV.
     * This function applies the same filters as the index method.
     */
    public function export(Request $request)
    {
        $query = Payment::with(['inscription.user', 'inscription.formation']);

        // Apply student-specific filter
        if (Auth::user() && Auth::user()->hasRole('Etudiant')) {
            $userId = Auth::id();
            $query->whereHas('inscription', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            });
        }

        // Apply filters from the request (same as index method)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('due_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('due_date', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('inscription', function($q) use ($search) {
                $q->whereHas('user', function($q2) use ($search) {
                    $q2->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })->orWhereHas('formation', function($q2) use ($search) {
                    $q2->where('title', 'like', "%{$search}%");
                });
            })->orWhere('reference', 'like', "%{$search}%")
              ->orWhere('transaction_id', 'like', "%{$search}%");
        }

        $payments = $query->orderBy('created_at', 'desc')->get(); // Get all filtered payments

        $filename = 'payments_' . date('Y-m-d_H-i-s') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8', // Specify UTF-8 charset
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($payments) {
            $file = fopen('php://output', 'w');

            // Add UTF-8 BOM for proper Excel encoding
            fwrite($file, "\xEF\xBB\xBF");

            // CSV Headers
            fputcsv($file, [
                'ID Paiement',
                'ID Inscription',
                'Nom Etudiant',
                'Email Etudiant',
                'Formation',
                'Montant',
                'Date Echéance',
                'Date Paiement',
                'Méthode Paiement',
                'Statut',
                'Référence',
                'ID Transaction',
                'Frais de Retard',
                'Créé le'
            ]);

            foreach ($payments as $payment) {
                // Ensure values are formatted correctly for CSV
                $studentName = $payment->inscription->user->name ?? '';
                $studentEmail = $payment->inscription->user->email ?? '';
                $formationTitle = $payment->inscription->formation->title ?? '';

                fputcsv($file, [
                    (string) $payment->id,
                    (string) $payment->inscription->id,
                    $studentName,
                    $studentEmail,
                    $formationTitle,
                    (string) $payment->amount,
                    $payment->due_date ? $payment->due_date->format('Y-m-d') : '',
                    $payment->paid_date ? $payment->paid_date->format('Y-m-d H:i:s') : '',
                    (string) $payment->payment_method,
                    (string) $payment->status,
                    (string) $payment->reference,
                    (string) $payment->transaction_id,
                    (string) $payment->late_fee,
                    $payment->created_at ? $payment->created_at->format('Y-m-d H:i:s') : ''
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function corbeille()
{
    // Kanst3amlo onlyTrashed() bach njebdo GHI les paiements li mamsou7in
    // KanLoadéw relations 'inscription' w 'creator'
    $payments = Payment::onlyTrashed()
                  ->with(['inscription', 'creator']) 
                  ->orderBy('deleted_at', 'desc')
                  ->get();

    return view('payments.corbeille', compact('payments'));
}

// N°2. Restauration d'un Paiement (I3ada l'Hayat)
public function restore($id)
{
    // Kanjebdo l-paiement b ID men l'Corbeille (withTrashed) w kan3ayto 3la restore()
    $payment = Payment::withTrashed()->findOrFail($id);
    $payment->restore();

    return redirect()->route('payments.corbeille')->with('success', 'Paiement restauré avec succès!');
}

// N°3. Suppression Définitive (Mass7 Nnéha'i)
public function forceDelete($id)
{
    // Kanjebdo l-paiement b ID men l'Corbeille (withTrashed) w kan3ayto 3la forceDelete()
    $payment = Payment::withTrashed()->findOrFail($id);
    
    // ⚠️ Mola7aḍa: Ila 3endek des fichiers flouked (b7al `receipt_path`), khass tmass7hom hna.
    
    $payment->forceDelete(); // Hadchi kaymassah men la base de données b neha'i!

    return redirect()->route('payments.corbeille')->with('success', 'Paiement supprimé définitivement!');
}
}