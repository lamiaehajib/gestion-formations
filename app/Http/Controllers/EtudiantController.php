<?php

namespace App\Http\Controllers;
use App\Models\Inscription;
use App\Models\Formation;
use App\Models\User;
use App\Models\Payment;
use App\Models\Category; // Make sure Category is imported
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Log;


use Illuminate\Support\Facades\Mail; // Zid had la ligne
use App\Mail\NewInscriptionNotification; // Zid had la ligne
use Spatie\Permission\Models\Role; // Zid had la ligne

class EtudiantController extends Controller
{

       public function __construct()
    {
        // Require the 'auth' middleware for all methods in this controller.
        // This ensures only logged-in users can access these actions.
        $this->middleware('auth');

        // Apply the 'permission' middleware to the 'enrollFormation' method.
        // Only users with the 'inscription-create-own' permission can access this method.
        $this->middleware('permission:inscription-create-own')->only('enrollFormation');

        // You might want to apply permissions to other methods as well, for example:
        // ->only('enrollFormation', 'showChooseFormationForm');
        // Or
        // ->except('someOtherMethod');
    }
    // ... (rest of your methods remain unchanged before showChooseFormationForm) ...
public function showChooseFormationForm(Request $request)
{
    $categories = Category::all();
    $staticPaymentPlans = [
        'one_time' => [
            'name' => 'Paiement Complet (1 Versement)',
            'description' => 'Paiement de la totalité du montant en une seule fois.',
            'installments_options' => [1]
        ],
        'monthly' => [
            'name' => 'Paiement Mensuel',
            'description' => 'Paiement par tranches (2 ou 3 mois).',
            'installments_options' => [2, 3]
        ],
        'custom' => [
            'name' => 'Paiement Personnalisé',
            'description' => 'Un plan de paiement flexible à définir avec l\'administration.',
            'installments_options' => [],
        ],
    ];

    $fixedRegistrationFees = [
        'Licence Professionnelle' => 1600.00,
        'Master Professionnelle' => 1600.00,
    ];

    $query = Formation::where('status', 'published');
    
    if ($request->filled('category_id')) {
        $query->where('category_id', $request->category_id);
    }
    
    $formations = $query->with('category')->get();

    // ✨ FILTRER LES FORMATIONS AVEC start_date DÉPASSÉE POUR LES CATÉGORIES RESTREINTES
    $restrictedCategories = ['LICENCE PROFESSIONNELLE RECONNU', 'FORMATIONS','All in One'];
    $today = Carbon::today();
    
    $formations = $formations->filter(function($formation) use ($restrictedCategories, $today) {
        $categoryName = $formation->category->name ?? '';
        
        // Si la formation appartient à une catégorie restreinte
        if (in_array($categoryName, $restrictedCategories)) {
            $startDate = Carbon::parse($formation->start_date);
            // Garder seulement si start_date >= aujourd'hui
            return $startDate->greaterThanOrEqualTo($today);
        }
        
        // Pour les autres catégories, garder toutes les formations
        return true;
    });
    // ✨ FIN DU FILTRAGE

    $selectedCategoryId = $request->get('category_id');

    return view('etudiant.choose-formation', compact('formations', 'categories', 'staticPaymentPlans', 'selectedCategoryId', 'fixedRegistrationFees'));
}

    /**
     * Handle a student's request to enroll in a formation.
     * This will create an inscription with 'pending' status and the initial payment record.
     */
   public function enrollFormation(Request $request)
{
    $user = Auth::user();

    try {
        $request->validate([
            'formation_id' => 'required|exists:formations,id',
            'selected_payment_option' => 'required|integer|min:1',
            'initial_paid_amount' => 'required|numeric|min:0.01', 
            'proof_of_payment' => 'required|file|mimes:jpg,jpeg,png,pdf|max:9048',
            'payment_method' => 'required|string|in:cash,bank_transfer',
            'notes' => 'nullable|string',
        ]);

        $formation = Formation::with('category')->findOrFail($request->formation_id);
        
        // ✨ NOUVELLE VÉRIFICATION: Bloquer l'inscription si start_date est dépassée pour certaines catégories
        $restrictedCategories = ['LICENCE PROFESSIONNELLE RECONNU', 'FORMATIONS','All in One'];
        $categoryName = $formation->category->name ?? '';
        
        if (in_array($categoryName, $restrictedCategories)) {
            $today = Carbon::today();
            $startDate = Carbon::parse($formation->start_date);
            
            if ($startDate->lessThan($today)) {
                return redirect()->back()->with('error', 'Les inscriptions pour cette formation sont closes. La date de début est déjà passée. 📅❌')->withInput();
            }
        }
        // ✨ FIN DE LA VÉRIFICATION

        $existingActiveInscription = Inscription::where('user_id', $user->id)
            ->where('formation_id', $request->formation_id)
            ->whereIn('status', ['pending', 'active', 'suspended'])
            ->first();
                        
        if ($existingActiveInscription) {
            return redirect()->back()->with('error', 'Vous avez déjà une inscription en cours ou en attente pour cette formation. Vous ne pouvez pas vous réinscrire. 🚫')->withInput();
        }
        
        $chosenInstallments = (int) $request->input('selected_payment_option');
        $initialPaidAmount = (float) $request->input('initial_paid_amount');
    
        $fixedRegistrationFees = [
            'Licence Professionnelle' => 1600.00,
            'Master Professionnelle' => 1600.00,
        ];
    
        $amountPerInstallment = 0;
        $remainingInstallmentsCount = $chosenInstallments; 
    
        $isProfessional = array_key_exists($categoryName, $fixedRegistrationFees);

        $amountToDivide = $formation->price;
        $initialFee = 0;

        if ($isProfessional) {
            $initialFee = $fixedRegistrationFees[$categoryName];
            $amountToDivide = $formation->price - $initialFee;
        }

        $standardInstallmentAmount = ($chosenInstallments > 0) ? round($amountToDivide / $chosenInstallments, 2) : 0;
        
        if ($initialPaidAmount < $initialFee) {
            return redirect()->back()->with('error', 'Le montant initial payé doit être au moins de ' . number_format($initialFee, 2) . ' DH (frais d\'inscription).')->withInput();
        }

        $paidTowardsInstallments = $initialPaidAmount - $initialFee;
        $coveredInstallments = floor($paidTowardsInstallments / $standardInstallmentAmount);
        $remainingInstallmentBalance = $amountToDivide - ($coveredInstallments * $standardInstallmentAmount);
        $remainingInstallmentsCount = max(0, $chosenInstallments - $coveredInstallments);
        $amountPerInstallment = $standardInstallmentAmount;

        DB::beginTransaction();
    
        $receiptPath = null;
        if ($request->hasFile('proof_of_payment')) {
            $receiptPath = $request->file('proof_of_payment')->store('payment_receipts/' . $user->id, 'public');
        }
    
        $nextInstallmentDueDate = null;
        if ($remainingInstallmentsCount > 0) {
            $today = Carbon::today();
            $targetDay = 5;
            if ($today->day < $targetDay) {
                $nextInstallmentDueDate = $today->day($targetDay);
            } else {
                $nextInstallmentDueDate = $today->addMonth()->day($targetDay);
            }
        }
        
        $inscription = Inscription::create([
            'user_id' => $user->id,
            'formation_id' => $request->formation_id,
            'status' => 'pending',
            'inscription_date' => now(),
            'total_amount' => $formation->price,
            'paid_amount' => 0.00,
            'chosen_installments' => $chosenInstallments,
            'amount_per_installment' => $amountPerInstallment,
            'remaining_installments' =>  $chosenInstallments,
            'notes' => $request->notes,
            'documents' => null, 
            'access_restricted' => false,
            'next_installment_due_date' => $nextInstallmentDueDate,
        ]);
    
        Payment::create([
            'inscription_id' => $inscription->id,
            'amount' => $initialPaidAmount,
            'due_date' => now(),
            'paid_date' => now(),
            'payment_method' => $request->payment_method,
            'status' => 'pending',
            'reference' => 'Paiement initial pour ' . $formation->title,
            'transaction_id' => null,
            'receipt_path' => $receiptPath,
            'created_by_user_id' => Auth::id(),
        ]);

        $recipients = User::role(['Admin', 'Finance', 'Super Admin'])->get();
        $inscription->load('formation', 'payments');
        foreach ($recipients as $recipient) {
            Mail::to($recipient->email)->send(new NewInscriptionNotification($inscription, $user));
        }
        
        DB::commit();
        return redirect()->route('etudiant.inscription.pending', ['inscription_id' => $inscription->id])
                        ->with('success', 'Votre demande d\'inscription a été soumise avec succès et est en attente de validation par l\'administrateur.');
    } catch (ValidationException $e) {
        DB::rollBack();
        return redirect()->back()->withErrors($e->errors())->withInput();
    } catch (\Exception $e) {
        DB::rollBack();
        Log::error('Error during student enrollment: ' . $e->getMessage() . ' at ' . $e->getFile() . ':' . $e->getLine());
        return redirect()->back()->with('error', 'Une erreur est survenue lors de votre demande d\'inscription. Veuillez réessayer.')->withInput();
    }
}

    // ... (rest of your EtudiantController methods) ...

    /**
     * Display the inscription pending page.
     */
 public function showInscriptionPending(int $inscription_id)
{
    $user = Auth::user();

    $inscription = Inscription::with('formation')
                                ->where('id', $inscription_id)
                                ->where('user_id', $user->id)
                                ->first();

    if (!$inscription) {
        return redirect()->route('dashboard')->with('error', 'Inscription introuvable ou accès non autorisé. 🚧');
    }

    $formationTitle = $inscription->formation->title ?? 'Formation inconnue';

    // ✨ هنا التعديل: نمرر حالة المستخدم (user status) إلى الـview ✨
    return view('etudiant.inscription-pending', [
        'formationTitle' => $formationTitle,
        'status' => $inscription->status, // نمرر حالة الـinscription الحالية
       
    ]);
}
}