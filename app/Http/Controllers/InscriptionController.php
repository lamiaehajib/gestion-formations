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
        $query = Inscription::with(['user', 'formation', 'formation.category'])
            ->orderBy('created_at', 'desc');

        // *** C'est la ligne importante qui doit être modifiée ***
        // Si l'utilisateur n'est pas 'admin' ou 'finance' ou 'super admin', seules ses propres inscriptions seront affichées
        // Sinon, toutes les inscriptions seront affichées
        if (!$user->hasAnyRole(['Admin', 'Finance', 'Super Admin'])) {
            $query->where('user_id', $user->id);
        }
        // ***********************************************
        
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('formation_id')) {
            $query->where('formation_id', $request->formation_id);
        }

        // Le champ de recherche d'étudiant n'apparaît que pour les utilisateurs avec des autorisations administratives et financières
        // Cette condition a été laissée ici pour déterminer qui peut utiliser le champ de recherche pour d'autres étudiants
        if ($request->filled('search') && $user->hasAnyRole(['Admin', 'Finance', 'Super Admin'])) {
            $search = $request->search;
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }
        // Remarque : Si un étudiant ordinaire peut rechercher ses propres inscriptions, la condition de rôle doit être supprimée de l'instruction if
        // et la recherche doit être limitée à ses propres inscriptions.


        $inscriptions = $query->paginate(15);
        
        $availableStatuses = ['pending', 'active', 'completed', 'cancelled'];
        $availableFormations = Formation::all();

        $isAdminOrFinanceOrSuperAdmin = $user->hasAnyRole(['Admin', 'Finance', 'Super Admin']);

        return view('inscriptions.index', compact('inscriptions', 'isAdminOrFinanceOrSuperAdmin', 'availableStatuses', 'availableFormations'));
    }

    // ... Le reste de votre code de contrôleur reste le même que précédemment ...

 // F'InscriptionController.php

public function create(Request $request)
{
    $users = Auth::user()->hasAnyRole(['Admin', 'Finance', 'Super Admin']) ? User::role('etudiant')->get() : collect();
    // Zid with('category') bach t'charger l'category m3a kol formation
    $formations = Formation::where('status', 'published')->with('category')->get(); 
    return view('inscriptions.create', compact('users', 'formations'));
}

     public function store(Request $request)
    {
        $user = Auth::user();
        $isAdminOrFinanceOrSuperAdmin = $user->hasAnyRole(['Admin', 'Finance', 'Super Admin']);

        $rules = [
            'formation_id' => 'required|exists:formations,id',
            'selected_payment_option' => 'required|integer|min:1|max:12',
            'notes' => 'nullable|string|max:1000'
        ];

        if ($isAdminOrFinanceOrSuperAdmin) {
            $rules['user_id'] = 'required|exists:users,id';
            $rules['status'] = 'required|in:pending,active,completed,cancelled';
            $rules['paid_amount'] = 'required|numeric|min:0';

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

        $formation = Formation::findOrFail($request->formation_id);
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
            $totalAmount = $formation->price;

            // Nouvelle logique pour gérer la catégorie "Professionnelle"
            $amountToDivide = $totalAmount;
            if ($formation->category && in_array($formation->category->name, ['Master Professionnelle', 'Licence Professionnelle'])) {
                $amountToDivide = $totalAmount - 1600;
            }

            $inscriptionStatus = $isAdminOrFinanceOrSuperAdmin ? $request->status : 'pending';
            $initialPaidAmount = $isAdminOrFinanceOrSuperAdmin ? $request->paid_amount : 0;
            $receiptPathForInitialPayment = null;

            if ($isAdminOrFinanceOrSuperAdmin && $initialPaidAmount > 0 && $request->hasFile('initial_receipt_file')) {
                $initialReceiptFile = $request->file('initial_receipt_file');
                $receiptPathForInitialPayment = $initialReceiptFile->store('payment_receipts/' . $userToEnroll->id, 'public');
            }

            // Calculer le montant par acompte en utilisant le montant à diviser
            $amountPerInstallment = ($chosenInstallments > 0) ? round($amountToDivide / $chosenInstallments, 2) : $amountToDivide;

            $inscription = Inscription::create([
                'user_id' => $userToEnroll->id,
                'formation_id' => $request->formation_id,
                'status' => $inscriptionStatus,
                'inscription_date' => now(),
                'total_amount' => $totalAmount,
                'paid_amount' => $initialPaidAmount,
                'chosen_installments' => $chosenInstallments,
                'amount_per_installment' => $amountPerInstallment,
                'remaining_installments' => ($amountPerInstallment > 0) ? ceil(($totalAmount - $initialPaidAmount) / $amountPerInstallment) : 0,
                'notes' => $request->notes,
                'documents' => [],
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
        // Si l'utilisateur n'est pas 'admin' ou 'finance' ou 'super admin' et qu'il n'est pas le propriétaire de l'inscription, l'accès est refusé
        if (!$user->hasAnyRole(['Admin', 'Finance', 'Super Admin']) && $inscription->user_id !== $user->id) {
            abort(403, 'Action non autorisée.');
        }

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
        $validator = Validator::make($request->all(), [
            'status' => 'required|in:pending,active,completed,cancelled',
            'chosen_installments' => 'required|integer|min:1',
            'total_amount' => 'required|numeric|min:0',
            'paid_amount' => 'required|numeric|min:0', 
            'documents' => 'nullable|array', 
            'documents.*' => 'file|mimes:pdf,jpg,jpeg,png|max:8048',
            'notes' => 'nullable|string|max:1000',
            'access_restricted' => 'boolean', // <--- Ceci est correct pour la validation
            'next_installment_due_date' => 'nullable|date', // <--- Ceci est correct pour la validation
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        DB::beginTransaction();
        try {
            $oldPaidAmount = $inscription->paid_amount;
            $oldStatus = $inscription->status; 
            $oldAccessRestricted = $inscription->access_restricted; // Conserver l'ancien état

            $newPaidAmount = $request->paid_amount;
            $chosenInstallments = $request->chosen_installments;
            $totalAmount = $request->total_amount;
            // $amountPerInstallment = ($chosenInstallments > 0) ? round($totalAmount / $chosenInstallments, 2) : $totalAmount; // Nous ne l'utilisons plus ici

            $inscription->fill([
                'status' => $request->status,
                'total_amount' => $totalAmount,
                'paid_amount' => $newPaidAmount, // Mise à jour du montant payé
                'chosen_installments' => $chosenInstallments,
                // Nous ne mettons pas 'amount_per_installment' ici car il ne change pas lors de la mise à jour
                // Et nous ne mettons pas 'remaining_installments' ici pour éviter un recalcul incorrect
                'notes' => $request->notes,
                'access_restricted' => $request->boolean('access_restricted'), // <--- Mettre à jour access_restricted directement
                'next_installment_due_date' => $request->next_installment_due_date, // <--- Mettre à jour next_installment_due_date directement
            ]);

            $uploadedDocumentPaths = $inscription->documents ?? [];
            if ($request->hasFile('documents')) {
                foreach ($request->file('documents') as $documentFile) {
                    $path = $documentFile->store('inscription_documents/' . $inscription->id, 'public');
                    $uploadedDocumentPaths[] = $path;
                }
            }
            $inscription->documents = $uploadedDocumentPaths;

            $inscription->save(); // Enregistrer les modifications de base

            // S'il y a une différence positive dans le montant payé (nouveau paiement via le formulaire de modification)
            if ($newPaidAmount > $oldPaidAmount) {
                $newPaymentAmount = $newPaidAmount - $oldPaidAmount;
                // Ajouter ce nouveau paiement comme un enregistrement distinct
                // Cette fonction doit mettre à jour correctement paid_amount dans Inscription
                // et doit modifier remaining_installments dans Inscription (diminuer de un)
                $inscription->addPayment($newPaymentAmount, 'cash', 'Paiement manuel via édition'); 
            }

            // Si l'état de restriction d'accès est passé de restreint à non restreint (l'administrateur a levé la restriction)
            if ($oldAccessRestricted && !$inscription->access_restricted) {
                Log::info("L'administrateur (" . (Auth::id() ?? 'N/A') . ") a levé la restriction d'accès pour l'inscription ID : {$inscription->id}.");
                // Mettre à jour la prochaine date d'échéance au 5ème jour du mois suivant si la restriction est levée
                if (!$inscription->next_installment_due_date || $inscription->next_installment_due_date->lt(Carbon::today()->day(5))) {
                   $inscription->next_installment_due_date = Carbon::today()->addMonth()->day(5);
                   $inscription->save(); // Enregistrer la nouvelle modification de la date d'échéance
                }
            }
            // Si access_restricted est passé de false à true, pas besoin de logique spéciale ici
            // car la tâche planifiée se chargera de restreindre l'accès et de mettre à jour next_installment_due_date si nécessaire

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

   
}