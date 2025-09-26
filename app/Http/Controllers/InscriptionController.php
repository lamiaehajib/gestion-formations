<?php

namespace App\Http\Controllers;

use App\Models\Inscription;
use App\Models\Formation;
use App\Models\User;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\Support\Facades\Log; // Assurez-vous d'avoir cette importation

use Illuminate\Support\Facades\Mail; // Zid had la ligne
use App\Mail\InscriptionActivatedNotification;

class InscriptionController extends Controller
{
    // Ajoutez le constructeur pour appliquer le middleware d'autorisations
    public function __construct()
    {
        // Ces autorisations doivent être définies dans Spatie Seeder et attribuées aux rôles appropriés
        $this->middleware('permission:inscription-list', ['only' => ['index', 'show', 'export']]);
        $this->middleware('permission:inscription-create', ['only' => ['create', 'store']]);
        $this->middleware('permission:inscription-edit', ['only' => ['edit', 'update', 'updateStatus', 'showAddPaymentForm', 'addPayment']]); // Ajout de addPaymentForm et addPayment ici
        $this->middleware('permission:inscription-delete', ['only' => ['destroy', 'bulkAction']]);
    }

    public function index(Request $request)
{
    $user = Auth::user();
    $isAdminOrFinanceOrSuperAdmin = $user->hasAnyRole(['Admin', 'Finance', 'Super Admin']);
    
    // Initialiser la requête principale des inscriptions
    $query = Inscription::with(['user', 'formation', 'formation.category'])
        ->orderBy('created_at', 'desc');

    // Limiter les résultats si l'utilisateur n'est pas un admin
    if (!$isAdminOrFinanceOrSuperAdmin) {
        $query->where('user_id', $user->id);
    }
    
    // Filtres
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    if ($request->filled('formation_id')) {
        $query->where('formation_id', $request->formation_id);
    }

    // Recherche d'étudiant
    if ($request->filled('search') && $isAdminOrFinanceOrSuperAdmin) {
        $search = $request->search;
        $query->whereHas('user', function ($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    // ✨ Le nouveau code pour calculer le nombre d'inscriptions par agent
    $inscriptionCountsByAgent = [];
    if ($isAdminOrFinanceOrSuperAdmin) {
        // Définir la liste des agents
        $agents = ['Sara BELKASSEH', 'Ghizlane LAFKIR', 'Lamiae HAJIB', 'Abdellatif LEZHARI', 'Khalid Katkout'];

        // Compter le nombre d'inscriptions pour chaque agent
        foreach ($agents as $agent) {
            $count = Inscription::where('inscrit_par', $agent)->count();
            $inscriptionCountsByAgent[$agent] = $count;
        }
        
        // Ou en utilisant une requête groupée pour plus d'efficacité
        // $counts = Inscription::select('inscrit_par', DB::raw('count(*) as total'))
        //                       ->whereIn('inscrit_par', $agents)
        //                       ->groupBy('inscrit_par')
        //                       ->pluck('total', 'inscrit_par')
        //                       ->toArray();
        // $inscriptionCountsByAgent = array_merge(array_fill_keys($agents, 0), $counts);
    }
    // ***********************************************

    $inscriptions = $query->paginate(15);
    
    $availableStatuses = ['pending', 'active', 'completed', 'cancelled'];
    $availableFormations = Formation::all();

    // Passer les données à la vue
    return view('inscriptions.index', compact('inscriptions', 'isAdminOrFinanceOrSuperAdmin', 'availableStatuses', 'availableFormations', 'inscriptionCountsByAgent'));
}

    // ... Le reste de votre code de contrôleur reste le même que précédemment ...

 // F'InscriptionController.php

public function create(Request $request)
{
    $users = Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']) ? User::role('etudiant')->get() : collect();
    // This line will now fetch ALL formations
    $formations = Formation::with('category')->get(); 
    return view('inscriptions.create', compact('users', 'formations'));
}

 // InscriptionController.php
// InscriptionController.php
// F'InscriptionController.php

public function store(Request $request)
{
    $user = Auth::user();
    $isAdminOrFinanceOrSuperAdmin = $user->hasAnyRole(['Admin', 'Finance', 'Super Admin']);

    $rules = [
        'formation_id' => 'required|exists:formations,id',
        'selected_payment_option' => 'required|integer|min:1|max:12',
        'notes' => 'nullable|string|max:1000'
    ];
    
    // Ajout d'une règle pour le prix modifiable
    if ($isAdminOrFinanceOrSuperAdmin) {
        $rules['user_id'] = 'required|exists:users,id';
        $rules['status'] = 'required|in:pending,active,completed,cancelled';
        $rules['paid_amount'] = 'required|numeric|min:0';
        $rules['total_amount_override'] = 'nullable|numeric|min:0';
        
        // ✨ Le nouveau champ inscrit_par avec la validation enum
        $rules['inscrit_par'] = 'nullable|in:Sara BELKASSEH,Ghizlane LAFKIR,Lamiae HAJIB,Abdellatif LEZHARI,Khalid Katkout'; 

        if ($request->paid_amount > 0) {
            $rules['initial_receipt_file'] = 'required|file|mimes:pdf,jpg,jpeg,png|max:2048';
        } else {
            $rules['initial_receipt_file'] = 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048';
        }
    } else {
        $rules['documents'] = 'nullable|array';
        $rules['documents.*'] = 'file|mimes:pdf,jpg,jpeg,png|max:2048';
    }

    $validator = Validator::make($request->all(), $rules);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    // هنا يتم جلب Formation. من الأفضل أن نطلب العلاقة 'category' مباشرة 
    // إذا كنتِ تستخدمين optional() في المنطق، يمكن أن تستمري كما كنتِ، لكن هذا أفضل للمردودية:
    $formation = Formation::with('category')->findOrFail($request->formation_id); 
    $userToEnroll = $isAdminOrFinanceOrSuperAdmin ? User::findOrFail($request->user_id) : $user;

    $existingInscription = Inscription::where('user_id', $userToEnroll->id)
        ->where('formation_id', $formation->id)
        ->whereIn('status', ['pending', 'active'])
        ->first();

    if ($existingInscription) {
        return redirect()->back()->with('error', 'Cet utilisateur a déjà une inscription active ou en attente pour cette formation.')->withInput();
    }

    if (!in_array($request->selected_payment_option, $formation->available_payment_options ?? [])) {
        return redirect()->back()->with('error', 'Option de paiement sélectionnée non valide pour cette formation.')->withInput();
    }

    DB::beginTransaction();
    try {
        $chosenInstallments = $request->selected_payment_option;
        
        $totalAmount = $isAdminOrFinanceOrSuperAdmin && $request->filled('total_amount_override') 
                      ? (float) $request->total_amount_override 
                      : $formation->price;
        
        $initialPaidAmount = $isAdminOrFinanceOrSuperAdmin ? $request->paid_amount : 0;
        $receiptPathForInitialPayment = null;
        
        $fixedFee = 0;
        if ($formation->category && in_array($formation->category->name, ['Master Professionnelle', 'Licence Professionnelle','LICENCE PROFESSIONNELLE RECONNU'])) {
            $fixedFee = 1600;
        }

        $totalInstallmentAmount = ($fixedFee > 0) ? ($totalAmount - $fixedFee) : $totalAmount;
        
        $remainingAmountToPayForInstallments = $totalInstallmentAmount - max(0, $initialPaidAmount - $fixedFee);

        $numberOfRemainingInstallments = $chosenInstallments; 

        $amountPerInstallment = ($remainingAmountToPayForInstallments > 0 && $numberOfRemainingInstallments > 0)
                                     ? round($remainingAmountToPayForInstallments / $numberOfRemainingInstallments, 2)
                                     : 0;
        
        $inscriptionStatus = $isAdminOrFinanceOrSuperAdmin ? $request->status : 'pending';

        if ($isAdminOrFinanceOrSuperAdmin && $initialPaidAmount > 0 && $request->hasFile('initial_receipt_file')) {
            $initialReceiptFile = $request->file('initial_receipt_file');
            $receiptPathForInitialPayment = $initialReceiptFile->store('payment_receipts/' . $userToEnroll->id, 'public');
        }

        // ✨ Nouvelle logique pour le champ 'inscrit_par'
        $inscritPar = null;
        if ($isAdminOrFinanceOrSuperAdmin) {
            $inscritPar = $request->inscrit_par;
        }
        
        // ==========================================================
        // ✨ المنطق الصحيح لتحديد 'next_installment_due_date'
        // ==========================================================
        $nextInstallmentDueDate = null;
        $epsilon = 0.01;

        // التحقق واش الدفع ماشي كامل (يعني كاين أقساط)
        // وواش باقي شي حاجة يتخلص فيها
        if ($chosenInstallments > 1 && $remainingAmountToPayForInstallments > $epsilon) {
            $formationCategoryName = optional($formation->category)->name ?? null; 

            if (in_array($formationCategoryName, ['Master Professionnelle', 'Licence Professionnelle','LICENCE PROFESSIONNELLE RECONNU'])) {
                // تاريخ الإستحقاق هو اليوم 01 من الشهر المقبل
                $nextInstallmentDueDate = Carbon::now()->addMonthNoOverflow()->day(1)->toDateString();
            }
        }
        // ==========================================================

        $inscription = Inscription::create([
            'user_id' => $userToEnroll->id,
            'formation_id' => $request->formation_id,
            'status' => $inscriptionStatus,
            'inscription_date' => now(),
            'total_amount' => $totalAmount,
            'paid_amount' => $initialPaidAmount,
            'chosen_installments' => $chosenInstallments,
            'amount_per_installment' => $amountPerInstallment,
            'remaining_installments' => $numberOfRemainingInstallments,
            'notes' => $request->notes,
            'documents' => [],
            'inscrit_par' => $inscritPar, 
            'next_installment_due_date' => $nextInstallmentDueDate, // ✨ إضافة التاريخ هنا
        ]);

        if (!$isAdminOrFinanceOrSuperAdmin && $request->hasFile('documents')) {
            $documentPaths = [];
            foreach ($request->file('documents') as $documentFile) {
                $path = $documentFile->store('inscription_documents/' . $inscription->id, 'public');
                $documentPaths[] = $path;
            }
            $inscription->documents = $documentPaths;
            $inscription->save();
        }

        if ($isAdminOrFinanceOrSuperAdmin && $initialPaidAmount > 0) {
            Payment::create([
                'inscription_id' => $inscription->id,
                'amount' => $initialPaidAmount,
                'paid_date' => now(),
                'payment_method' => 'cash',
                'status' => 'paid',
                'reference' => 'Paiement initial manuel',
                'receipt_path' => $receiptPathForInitialPayment,
            ]);
        }

        DB::commit();

        return redirect()->route('inscriptions.show', $inscription)
            ->with('success', $isAdminOrFinanceOrSuperAdmin ? 'Inscription créée avec succès par l\'administrateur.' : 'Votre inscription a été créée avec succès ! Veuillez procéder au paiement.');

    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Erreur lors de la création de l\'inscription : ' . $e->getMessage() . ' dans ' . $e->getFile() . ' à la ligne ' . $e->getLine());
        return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la création de l\'inscription. Veuillez réessayer.')->withInput();
    }
}

public function show(Inscription $inscription)
{
    $user = Auth::user();

    if (!$user->hasAnyRole(['Admin', 'Finance', 'Super Admin']) && $inscription->user_id !== $user->id) {
        abort(403, 'Action non autorisée.');
    }

    // Correct: Only load the 'user' relationship.
    // The 'documents' attribute is automatically available due to the cast.
    $inscription->load(['user', 'formation', 'formation.category', 'payments']);
    
    $remainingAmount = $inscription->remaining_amount;
    $nextPaymentAmount = ($inscription->amount_per_installment > 0) ? $inscription->amount_per_installment : $remainingAmount;

    return view('inscriptions.show', compact('inscription', 'remainingAmount', 'nextPaymentAmount'));
}

    public function edit(Inscription $inscription)
    {
        // Le middleware Spatie se chargera de la vérification de l'autorisation (inscription-edit)
        // Pas besoin de vérification manuelle ici : if (!Auth::user()->hasAnyRole(['admin', 'finance'])) { abort(403, 'Action non autorisée.'); }

        $formations = Formation::all();
        // Seuls les administrateurs/finances/super administrateurs peuvent voir la liste des étudiants
        $users = Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']) ? User::role('etudiant')->get() : collect([$inscription->user]);


        return view('inscriptions.edit', compact('inscription', 'formations', 'users'));
    }



public function update(Request $request, Inscription $inscription)
{
    // ✨ Étape 1: Mettre à jour la règle de validation
    $validator = Validator::make($request->all(), [
        'formation_id' => 'required|exists:formations,id', 
        'status' => 'required|in:pending,active,completed,cancelled',
        'chosen_installments' => 'required|integer|min:1',
        'total_amount' => 'required|numeric|min:0',
        'paid_amount' => 'required|numeric|min:0', 
        'documents' => 'nullable|array', 
        'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:8048',
        'notes' => 'nullable|string|max:1000',
        'access_restricted' => 'boolean',
        'next_installment_due_date' => 'nullable|date',
        // Remplacer la règle pour 'inscri_par' par la nouvelle
        'inscrit_par' => 'nullable|in:Sara BELKASSEH,Ghizlane LAFKIR,Lamiae HAJIB,Abdellatif LEZHARI,Khalid Katkout',
    ]);

    if ($validator->fails()) {
        return redirect()->back()->withErrors($validator)->withInput();
    }

    DB::beginTransaction();
    try {
        $oldPaidAmount = $inscription->paid_amount;
        
        $newTotalAmount = (float) $request->total_amount;
        $newPaidAmount = (float) $request->paid_amount;
        $chosenInstallments = (int) $request->chosen_installments;
        
        // La logique de calcul reste la même
        $remainingBalance = $newTotalAmount - $newPaidAmount;
        $newAmountPerInstallment = ($remainingBalance > 0 && $chosenInstallments > 0)
                                    ? round($remainingBalance / $chosenInstallments, 2)
                                    : 0;
        
        $newRemainingInstallments = $chosenInstallments;

        if (abs($newTotalAmount - $newPaidAmount) < 0.01) {
            $newRemainingInstallments = 0;
        }

        // ✨ Étape 2: Remplacer le champ 'inscri_par' par 'inscrit_par' lors de la mise à jour
        $inscription->fill([
            'formation_id' => $request->formation_id, 
            'status' => $request->status,
            'total_amount' => $newTotalAmount,
            'paid_amount' => $newPaidAmount,
            'chosen_installments' => $chosenInstallments,
            'amount_per_installment' => $newAmountPerInstallment,
            'remaining_installments' => $newRemainingInstallments,
            'notes' => $request->notes,
            'access_restricted' => $request->boolean('access_restricted'), 
            'next_installment_due_date' => $request->next_installment_due_date,
            'inscrit_par' => $request->inscrit_par, // Le champ a été mis à jour ici
        ]);
        
        if ($newPaidAmount > $oldPaidAmount) {
            $newPaymentAmount = $newPaidAmount - $oldPaidAmount;
            $inscription->addPayment($newPaymentAmount, 'cash', 'Paiement manuel via édition');
        }

        $inscription->save();

        DB::commit();
        return redirect()->route('inscriptions.show', $inscription)->with('success', 'Inscription mise à jour avec succès.');
    } catch (\Exception $e) {
        DB::rollback();
        Log::error('Erreur lors de la mise à jour de l\'inscription : ' . $e->getMessage() . ' dans ' . $e->getFile() . ' à la ligne ' . $e->getLine());
        return redirect()->back()->with('error', 'Une erreur s\'est produite lors de la mise à jour de l\'inscription. Veuillez réessayer.')->withInput();
    }
}

    public function destroy(Inscription $inscription)
    {
        // Le middleware Spatie se chargera de la vérification de l'autorisation (inscription-delete)
        // Pas besoin de vérification manuelle ici : if (!Auth::user()->hasAnyRole(['admin', 'finance'])) { abort(403, 'Action non autorisée.'); }

        // Tout utilisateur ayant la permission inscription-delete peut supprimer n'importe quelle inscription,
        // mais une logique peut être ajoutée pour empêcher la suppression de certaines inscriptions (par exemple, si elles sont actives)
        if ($inscription->status === 'active') {
            return redirect()->back()->with('error', 'Impossible de supprimer une inscription active.')->withInput();
        }

        // Supprimer les documents liés à l'inscription avant de la supprimer
        if ($inscription->documents) {
            foreach ($inscription->documents as $documentPath) {
                if (Storage::disk('public')->exists($documentPath)) {
                    Storage::disk('public')->delete($documentPath);
                }
            }
        }
        // Supprimer les reçus liés aux paiements (s'il existe une relation One-to-Many entre Inscription et Payment)
        foreach ($inscription->payments as $payment) {
            if ($payment->receipt_path && Storage::disk('public')->exists($payment->receipt_path)) {
                Storage::disk('public')->delete($payment->receipt_path);
            }
        }

        $inscription->delete();
        return redirect()->route('inscriptions.index')->with('success', 'Inscription supprimée avec succès.');
    }

     public function updateStatus(Request $request, Inscription $inscription)
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,active,completed,cancelled'
        ]);

        if ($validator->fails()) {
            Log::error('Échec de la validation du statut d\'inscription : ' . json_encode($validator->errors()));
            return response()->json(['error' => $validator->errors()->first()], 422);
        }

        DB::beginTransaction();
        try {
            $oldStatus = $inscription->status; // Kanakhdou l'statut l'qdim qbel ma nbedlouh
            $inscription->status = $request->status;

            if ($request->status === 'completed') {
                $epsilon = 0.01;
                if (abs($inscription->paid_amount - $inscription->total_amount) > $epsilon) {
                    $inscription->paid_amount = $inscription->total_amount;
                    $inscription->remaining_installments = 0;
                }
            }

            $inscription->save();

            // =========================================================================
            // NOUVELLE LOGIQUE POUR L'ENVOI D'EMAIL A L'ÉTUDIANT
            // =========================================================================
            // Kanseftou email ghir ila tbadal l'statut men chi 7aja okhra l'active
            if ($oldStatus !== 'active' && $request->status === 'active') {
                $user = $inscription->user;
                // Kanseftou email l'l'étudiant
                Mail::to($user->email)->send(new InscriptionActivatedNotification($inscription));
                Log::info("Notification d'activation d'inscription envoyée à l'étudiant " . $user->email);
            }
            // =========================================================================

            DB::commit();
            return response()->json(['success' => 'Statut mis à jour avec succès.']);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de la mise à jour du statut de l\'inscription : ' . $e->getMessage() . ' à ' . $e->getFile() . ':' . $e->getLine());
            return response()->json(['error' => 'Échec de la mise à jour du statut.'], 500);
        }
    }

    // Ajout de fonctions pour afficher le formulaire d'ajout de paiement et ajouter le paiement
    public function showAddPaymentForm(Inscription $inscription)
    {
        // Le middleware Spatie se chargera de la vérification de l'autorisation (inscription-edit)
        return view('inscriptions.add_payment', compact('inscription'));
    }

    public function addPayment(Request $request, Inscription $inscription)
    {
        // Le middleware Spatie se chargera de la vérification de l'autorisation (inscription-edit)
        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01|max:' . $inscription->remaining_amount,
            'payment_method' => 'required|string|in:cash,bank_transfer,card,cheque', // Ou les méthodes de paiement disponibles
            'payment_notes' => 'nullable|string|max:500',
            'receipt_file' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:9048',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $receiptPath = null;
            if ($request->hasFile('receipt_file')) {
                $receiptPath = $request->file('receipt_file')->store('payment_receipts/' . $inscription->id, 'public');
            }

            // Assurez-vous que le modèle Inscription a la fonction addPayment()
            $inscription->addPayment(
                $request->amount,
                $request->payment_method,
                $request->payment_notes,
                $receiptPath
            );

            DB::commit();
            return redirect()->route('inscriptions.show', $inscription)->with('success', 'Paiement ajouté avec succès.');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de l\'ajout du paiement à l\'inscription : ' . $e->getMessage() . ' dans ' . $e->getFile() . ' à la ligne ' . $e->getLine());
           return view('inscriptions.add_payment', compact('inscription'));
        }
    }
    
    // Fonction pour les actions groupées (Bulk Actions)
    public function bulkAction(Request $request)
    {
        // Le middleware Spatie se chargera de la vérification de l'autorisation (inscription-delete ou inscription-edit)
        $request->validate([
            'action' => 'required|in:activate,complete,cancel,delete',
            'inscriptions' => 'required|array',
            'inscriptions.*' => 'exists:inscriptions,id'
        ]);

        $inscriptions = Inscription::whereIn('id', $request->inscriptions);
        $user = Auth::user();
        
        // Empêcher la suppression des inscriptions actives en cas de suppression groupée (cette logique peut être modifiée selon les besoins)
        if ($request->action === 'delete') {
            $activeInscriptionsCount = (clone $inscriptions)->where('status', 'active')->count();
            if ($activeInscriptionsCount > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de supprimer les inscriptions actives en action groupée.'
                ], 403);
            }
        }

        DB::beginTransaction();
        try {
            switch ($request->action) {
                case 'activate':
                    $inscriptions->update(['status' => 'active']);


                     foreach ($inscriptions->get() as $inscription) {
                    $user = $inscription->user;
                    if ($user) {
                        $user->has_active_inscription = true;
                        $user->save();
                    }
                }

                
                    $message = 'Inscriptions sélectionnées activées avec succès !';
                    break;
                case 'complete':
                    // La logique d'achèvement doit définir paid_amount à total_amount
                    foreach ($inscriptions->get() as $inscription) {
                        $inscription->status = 'completed';
                        $inscription->paid_amount = $inscription->total_amount;
                        $inscription->remaining_installments = 0;
                        $inscription->save();
                    }
                    $message = 'Inscriptions sélectionnées marquées comme "terminées" avec succès !';
                    break;
                case 'cancel':
                    $inscriptions->update(['status' => 'cancelled']);
                    $message = 'Inscriptions sélectionnées annulées avec succès !';
                    break;
                case 'delete':
                    foreach ($inscriptions->get() as $inscription) {
                        // Supprimer les documents et reçus avant de supprimer l'inscription
                        if ($inscription->documents) {
                            foreach ($inscription->documents as $documentPath) {
                                if (Storage::disk('public')->exists($documentPath)) {
                                    Storage::disk('public')->delete($documentPath);
                                }
                            }
                        }
                        foreach ($inscription->payments as $payment) {
                            if ($payment->receipt_path && Storage::disk('public')->exists($payment->receipt_path)) {
                                Storage::disk('public')->delete($payment->receipt_path);
                            }
                        }
                        $inscription->delete();
                    }
                    $message = 'Inscriptions sélectionnées supprimées avec succès !';
                    break;
                default:
                    $message = 'Action invalide.';
                    break;
            }
            DB::commit();
            return response()->json(['success' => true, 'message' => $message]);
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Erreur lors de l\'action groupée sur les inscriptions : ' . $e->getMessage() . ' dans ' . $e->getFile() . ' à la ligne ' . $e->getLine());
            return response()->json(['success' => false, 'message' => 'Une erreur s\'est produite lors de l\'exécution de l\'action groupée. Veuillez réessayer.'], 500);
        }
    }


    public function export()
    {
        // The permission 'inscription-list' is already checked by the middleware in the constructor.
        // If you want a specific permission for export (e.g., 'inscription-export'),
        // you should add it to the __construct middleware and check it here instead.
        // Example: if (!Auth::user()->can('inscription-export')) { abort(403, 'Unauthorized action.'); }

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="inscriptions_' . date('Ymd_His') . '.csv"',
        ];

        $callback = function() {
            // Adjust the query based on roles, similar to your index method,
            // so that regular users only export their own inscriptions if that's the desired behavior.
            $user = Auth::user();
            $query = Inscription::with(['user', 'formation', 'payments']);

            if (!$user->hasAnyRole(['Admin', 'Finance', 'Super Admin'])) {
                $query->where('user_id', $user->id);
            }
            
            $inscriptions = $query->orderBy('created_at', 'desc')->get();
            $file = fopen('php://output', 'w');

            // Add CSV headers
            fputcsv($file, [
                'ID Inscription',
                'Nom Utilisateur',
                'Email Utilisateur',
                'ID Formation',
                'Titre Formation',
                'Statut Inscription',
                'Date Inscription',
                'Montant Total',
                'Montant Payé',
                'Montant Restant',
                'Option Paiement (acomptes)',
                'Montant par acompte',
                'Acomptes Restants',
                'Notes',
                'Documents Liés',
                'Date Prochain Acompte',
                'Accès Restreint',
                'Nombre de Paiements',
                'Montant Total des Paiements Enregistrés',
            ]);

            foreach ($inscriptions as $inscription) {
                // Calculate remaining amount based on database values
                $remainingAmount = $inscription->total_amount - $inscription->paid_amount;
                
                // Get payment details
                $paymentsCount = $inscription->payments->count();
                $paymentsTotalAmount = $inscription->payments->sum('amount');

                fputcsv($file, [
                    $inscription->id,
                    $inscription->user->name ?? 'N/A',
                    $inscription->user->email ?? 'N/A',
                    $inscription->formation->id ?? 'N/A',
                    $inscription->formation->title ?? 'N/A',
                    $inscription->status,
                    $inscription->inscription_date ? $inscription->inscription_date->format('Y-m-d H:i:s') : 'N/A',
                    $inscription->total_amount,
                    $inscription->paid_amount,
                    number_format($remainingAmount, 2), // Format remaining amount
                    $inscription->chosen_installments,
                    number_format($inscription->amount_per_installment, 2),
                    $inscription->remaining_installments,
                    $inscription->notes,
                    !empty($inscription->documents) ? implode(', ', $inscription->documents) : 'Aucun', // Join document paths
                    $inscription->next_installment_due_date ? $inscription->next_installment_due_date->format('Y-m-d') : 'N/A',
                    $inscription->access_restricted ? 'Oui' : 'Non',
                    $paymentsCount,
                    number_format($paymentsTotalAmount, 2),
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    // In InscriptionController.php

public function detailsJson(Inscription $inscription)
{
    // Check authorization if necessary
    // For example, only allow users who own the inscription or have finance/admin roles
    // if (Auth::user()->cannot('view', $inscription) && !Auth::user()->hasAnyRole(['Admin', 'finance', 'super admin'])) {
    //     abort(403, 'Unauthorized action.');
    // }

    // Load relationships (user, formation, payments)
    $inscription->load(['user', 'formation', 'payments']);

    // Format payments data, especially receipt_path for public URL
    $payments = $inscription->payments->map(function ($payment) {
        return [
            'amount' => number_format($payment->amount, 2, '.', ''), // Ensure consistent number format
            'created_at' => $payment->created_at->format('d/m/Y'),
            'receipt_path' => $payment->receipt_path ? Storage::url($payment->receipt_path) : null, // Generate public URL
        ];
    });

    return response()->json([
        'success' => true,
        'inscription' => [
            'id' => $inscription->id,
            'status' => $inscription->status,
            'chosen_installments' => $inscription->chosen_installments,
            'total_amount' => number_format($inscription->total_amount, 2, '.', ''),
            'paid_amount' => number_format($inscription->paid_amount, 2, '.', ''),
            // Pass the raw date for JS to format, or a specific format if you prefer
            'inscription_date' => $inscription->inscription_date->format('Y-m-d H:i:s'), // ISO format is good for JS Date object
            'user' => [
                'name' => $inscription->user->name ?? 'N/A',
                'email' => $inscription->user->email ?? 'N/A',
            ],
            'formation' => [
                'title' => $inscription->formation->title ?? 'N/A',
            ],
            'payments' => $payments,
        ]
    ]);
}
public function corbeille()
{
    // Kanst3amlo onlyTrashed() bach njebdo GHI les Inscriptions li mamsou7in
    // KanLoadéw relations 'user' w 'formation'
    $inscriptions = Inscription::onlyTrashed()
                  ->with(['user', 'formation']) 
                  ->orderBy('deleted_at', 'desc')
                  ->get();

    return view('inscriptions.corbeille', compact('inscriptions'));
}

// N°2. Restauration d'une Inscription (I3ada l'Hayat)
public function restore($id)
{
    // Kanjebdo l-Inscription b ID men l'Corbeille (withTrashed) w kan3ayto 3la restore()
    $inscription = Inscription::withTrashed()->findOrFail($id);
    $inscription->restore();

    return redirect()->route('inscriptions.corbeille')->with('success', 'Inscription restaurée avec succès!');
}

// N°3. Suppression Définitive (Mass7 Nnéha'i)
public function forceDelete($id)
{
    // Kanjebdo l-Inscription b ID men l'Corbeille (withTrashed) w kan3ayto 3la forceDelete()
    $inscription = Inscription::withTrashed()->findOrFail($id);
    
    // ⚠️ Mola7aḍa: Ila 3endek des fichiers flouked (b7al `documents`), khass tmass7hom hna.
    
    $inscription->forceDelete(); // Hadchi kaymassah men la base de données b neha'i!

    return redirect()->route('inscriptions.corbeille')->with('success', 'Inscription supprimée définitivement!');
}
   
}