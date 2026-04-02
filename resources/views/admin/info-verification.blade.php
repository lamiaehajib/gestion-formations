@extends('layouts.app') {{-- Aw smiya dyal layout dyalk --}}

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                <div class="card-header bg-white border-0 py-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-0 font-weight-bold">Suivi de Vérification des Informations</h5>
                            <p class="text-muted small mb-0">Liste des étudiants en Licence et Master Professionnelle</p>
                        </div>
                        <div class="d-flex gap-3">
                            <div class="text-center px-3 border-end">
                                <span class="d-block h5 mb-0 text-primary">{{ $stats['total'] }}</span>
                                <small class="text-muted">Total</small>
                            </div>
                            <div class="text-center px-3 border-end">
                                <span class="d-block h5 mb-0 text-success">{{ $stats['done'] }}</span>
                                <small class="text-muted">Vérifiés</small>
                            </div>
                            <div class="text-center px-3">
                                <span class="d-block h5 mb-0 text-danger">{{ $stats['pending'] }}</span>
                                <small class="text-muted">En attente</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card-body px-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="bg-light text-muted">
                                <tr>
                                    <th class="ps-4" style="font-size: 12px; text-transform: uppercase;">Étudiant</th>
                                    <th style="font-size: 12px; text-transform: uppercase;">CIN</th>
                                    <th style="font-size: 12px; text-transform: uppercase;">Formation / Catégorie</th>
                                    <th style="font-size: 12px; text-transform: uppercase;">Statut</th>
                                    <th style="font-size: 12px; text-transform: uppercase;">Détails Manquants</th>
                                    <th style="font-size: 12px; text-transform: uppercase;">Date Vérif.</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm me-3 bg-soft-primary text-primary rounded-circle d-flex align-items-center justify-content-center" style="width: 35px; height: 35px; background: #eef2ff;">
                                                {{ substr($student->nom ?? $student->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="font-weight-bold mb-0" style="font-size: 14px;">
                                                    {{ $student->nom }} {{ $student->prenom }}
                                                </div>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ $student->cin ?? '---' }}</span></td>
                                    <td>
                                        @foreach($student->inscriptions as $ins)
                                            <div class="small fw-bold">{{ $ins->formation->name }}</div>
                                            <div class="badge bg-info-subtle text-info style="font-size: 10px;">{{ $ins->formation->category->name }}</div>
                                        @endforeach
                                    </td>
                                    <td>
                                        @if($student->info_verified_at)
                                            <span class="badge bg-success-subtle text-success border border-success">
                                                <i class="fas fa-check-circle me-1"></i> Vérifié
                                            </span>
                                        @else
                                            <span class="badge bg-warning-subtle text-warning border border-warning">
                                                <i class="fas fa-clock me-1"></i> En attente
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if(!$student->info_verified_at)
                                            @php
                                                $missing = [];
                                                if(empty($student->nom)) $missing[] = 'Nom';
                                                if(empty($student->prenom)) $missing[] = 'Prénom';
                                                if(empty($student->cin)) $missing[] = 'CIN';
                                                if(empty($student->birth_date)) $missing[] = 'Date Naiss.';
                                                if(empty($student->lieu_naissance)) $missing[] = 'Lieu Naiss.';
                                            @endphp
                                            @foreach($missing as $m)
                                                <span class="badge bg-danger" style="font-size: 10px;">{{ $m }}</span>
                                            @endforeach
                                        @else
                                            <span class="text-muted small">Aucun</span>
                                        @endif
                                    </td>
                                    <td class="text-muted small">
                                        {{ $student->info_verified_at ? $student->info_verified_at->format('d/m/Y H:i') : '---' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-success-subtle { background-color: #d1e7dd; color: #0f5132; }
    .bg-warning-subtle { background-color: #fff3cd; color: #664d03; }
    .bg-info-subtle { background-color: #cff4fc; color: #055160; }
</style>
@endsection