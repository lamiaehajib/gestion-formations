<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Mail;
use App\Mail\EtudiantRegisteredMail;
class RegisteredUserController extends Controller
{
    /**
     * Display the default registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Display the student registration view.
     */
    public function createEtudiantRegistrationForm(): View
    {
        return view('auth.register-etudiant');
    }

    /**
     * Handle an incoming registration request. (Default for general users)
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => 'Etudiant', // تم تصحيح 'etudiant' إلى 'Etudiant' (لعمود الـ ENUM)
            'status' => 'inactive', // Set to inactive by default for admin validation
        ]);

        // =========================================================
        // هذا السطر يضمن تعيين دور Spatie بشكل صحيح
        $user->assignRole('Etudiant'); // تم التأكد من أن 'Etudiant' بحرف E كبير
        // =========================================================

        event(new Registered($user));

        

        Auth::login($user);

        return redirect(RouteServiceProvider::HOME);
    }

    /**
     * Handle an incoming student registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
   public function storeEtudiantRegistration(Request $request): RedirectResponse
{
    $request->validate([
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
        'cin' => ['required', 'string', 'max:255', 'unique:'.User::class],
        'phone' => ['required', 'string', 'max:20'],
        'avatar' => ['nullable', 'image', 'max:2048'],
        'password' => ['required', 'confirmed', Rules\Password::defaults()],
        
        // 🆕 Ajout de la validation des documents
        'documents' => ['nullable', 'array'],
        'documents.*.name' => ['nullable', 'string', 'max:255'],
        // 'max:10240' correspond à 10 Mo
        'documents.*.file' => ['nullable', 'file', 'mimes:pdf,doc,docx,jpg,png', 'max:10240'], 
    ]);

    $avatarPath = null;
    if ($request->hasFile('avatar')) {
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
    }

    // 🆕 Logique pour traiter et stocker les documents
    $documentsData = [];
    if ($request->has('documents')) {
        foreach ($request->input('documents') as $index => $document) {
            // Assurez-vous qu'il y a un fichier dans le tableau de documents
            if ($request->hasFile("documents.{$index}.file")) {
                $file = $request->file("documents.{$index}.file");

                if ($file->isValid()) {
                    // Stocker le fichier dans le dossier 'documents'
                    $path = $file->store('documents', 'public'); 

                    // Utiliser le nom du document fourni ou le nom de fichier original
                    $docName = $document['name'] ?? pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

                    $documentsData[] = [
                        'name' => $docName,
                        'path' => $path,
                        'type' => $file->getClientOriginalExtension(),
                    ];
                }
            }
        }
    }

    $user = User::create([
        'name' => $request->name,
        'email' => $request->email,
        'cin' => $request->cin,
        'phone' => $request->phone,
        'avatar' => $avatarPath,
        // 🆕 Stocker les données des documents dans la colonne 'documents' (Doit être de type JSON/TEXT dans la BDD)
        'documents' => $documentsData, 
        'password' => Hash::make($request->password),
        'role' => 'Etudiant',
        'status' => 'active', 
    ]);

    $user->assignRole('Etudiant');

    event(new Registered($user));
    Mail::to($user->email)->send(new EtudiantRegisteredMail($user));
    Auth::login($user);

    return redirect()->route('etudiant.choose_formation')->with('success', 'Votre compte a été créé avec succès. Un email de confirmation vous a été envoyé. Veuillez choisir une formation.');
}
}