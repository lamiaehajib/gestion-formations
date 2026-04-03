<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InfoVerificationController extends Controller
{
    public function submit(Request $request)
{
    $request->validate([
        'nom'            => 'required|string|max:100', 
        'prenom'         => 'required|string|max:100',
        'cin'            => 'required|string|max:20',
        'birth_date'     => 'required|date',
        'lieu_naissance' => 'required|string|max:100',
        'nationalite'    => 'nullable|string|max:100',
        'address'        => 'nullable|string|max:255',
        'phone'          => 'nullable|string|max:20',
    ]);

    $user = Auth::user();

    $user->update([
        'nom'             => $request->nom, 
        'prenom'          => $request->prenom, 
        'cin'             => $request->cin,
        'birth_date'      => $request->birth_date,
        'lieu_naissance'  => $request->lieu_naissance,
        'nationalite'     => $request->nationalite ?? $user->nationalite,
        'address'         => $request->address ?? $user->address,
        'phone'           => $request->phone ?? $user->phone,
        'info_verified_at'=> now(),
    ]);

    return response()->json(['success' => true]);
}

    // Page admin — liste des étudiants LP/MP
    public function adminIndex()
    {
        $students = \App\Models\User::role('Etudiant')
            ->whereHas('inscriptions', function ($q) {
                $q->where('status', 'active')
                  ->whereHas('formation.category', function ($q2) {
                      $q2->whereIn('name', [
                          'Licence Professionnelle',
                          'Master Professionnelle',
                          'LICENCE PROFESSIONNELLE RECONNU'
                      ]);
                  });
            })
            ->with(['inscriptions.formation.category'])
            ->orderBy('info_verified_at', 'asc') // les non-vérifiés en premier
            ->get();

        $stats = [
            'total'   => $students->count(),
            'done'    => $students->whereNotNull('info_verified_at')->count(),
            'pending' => $students->whereNull('info_verified_at')->count(),
        ];

        return view('admin.info-verification', compact('students', 'stats'));
    }
}