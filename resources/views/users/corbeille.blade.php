@extends('layouts.app')
@section('title', 'Gestion des corbeille')

@section('content')

<style>
    .table-striped>tbody>tr:nth-of-type(odd)>* {
    --bs-table-color-type: var(--bs-table-striped-color);
    --bs-table-bg-type: var(--bs-table-striped-bg);
    color: black !important;
}
span.badge.badge-secondary {
    color: black !important;
}
</style>
<h2 class="text-danger"><i class="fa fa-trash"></i> Corbeille des Utilisateurs</h2>

<p class="alert alert-info">Cette liste contient tous les comptes d'utilisateurs qui ont été supprimés.</p>

<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>Nom</th>
            <th>Email</th>
            <th>Rôles</th>
            <th>Date d'Effacement</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>
                    @foreach ($user->getRoleNames() as $role)
                        <span class="badge badge-secondary">{{ $role }}</span>
                    @endforeach
                </td>
                <td>{{ $user->deleted_at->format('Y-m-d H:i') }}</td>
                <td>
                   

                    <form method="POST" action="{{ route('users.restore', $user->id) }}" style="display:inline-block;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-sm btn-success" title="Restaurer">
                            <i class="fa fa-undo"></i> Restaurer
                        </button>
                    </form>

                    <form method="POST" action="{{ route('users.forceDelete', $user->id) }}" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cet Utilisateur DÉFINITIVEMENT ? Cette action est irréversible.');" 
                                title="Supprimer Définitivement">
                            <i class="fa fa-times"></i> Supprimer Déf.
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        
        @if($users->isEmpty())
            <tr>
                <td colspan="5" class="text-center text-muted">La corbeille est vide pour le moment.</td>
            </tr>
        @endif
    </tbody>
</table>
@endsection