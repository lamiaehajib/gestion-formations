@extends('layouts.app')

@section('title', 'Gestion des Paiements')

@section('content')
<h2 class="text-danger"><i class="fa fa-trash"></i> Corbeille des Paiements</h2>

<p class="alert alert-info">Cette liste contient tous les paiements qui ont été supprimés. Vous pouvez les visualiser, les restaurer, ou les supprimer définitivement.</p>

<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>Montant</th>
            <th>Inscription</th>
            <th>Créé par</th>
            <th>Méthode</th>
            <th>Date d'Effacement</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($payments as $payment)
            <tr>
                <td>{{ number_format($payment->amount, 2) }} DH</td>
                <td>{{ $payment->inscription->user->name ?? 'N/A' }}</td>
                <td>{{ $payment->creator->name ?? 'N/A' }}</td>
                <td>{{ $payment->payment_method }}</td>
                <td>{{ $payment->deleted_at->format('Y-m-d H:i') }}</td>
                <td>
                  

                    <form method="POST" action="{{ route('payments.restore', $payment->id) }}" style="display:inline-block;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-sm btn-success" title="Restaurer">
                            <i class="fa fa-undo"></i> Restaurer
                        </button>
                    </form>

                    <form method="POST" action="{{ route('payments.forceDelete', $payment->id) }}" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce Paiement DÉFINITIVEMENT ? Cette action est irréversible.');" 
                                title="Supprimer Définitivement">
                            <i class="fa fa-times"></i> Supprimer Déf.
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        
        @if($payments->isEmpty())
            <tr>
                <td colspan="6" class="text-center text-muted">La corbeille est vide pour le moment.</td>
            </tr>
        @endif
    </tbody>
</table>
@endsection