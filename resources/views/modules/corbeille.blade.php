@extends('layouts.app')

@section('content')

<h2 class="text-danger"><i class="fa fa-trash"></i> Corbeille des Modules</h2>

<p class="alert alert-info">Cette liste contient tous les modules qui ont été supprimés.</p>

<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>Titre</th>
            <th>Formation</th>
            <th>Consultant</th>
            <th>Date d'Effacement</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($modules as $module)
            <tr>
                <td>{{ $module->title }}</td>
                <td>{{ $module->formation->title ?? 'N/A' }}</td>
                <td>{{ $module->user->name ?? 'N/A' }}</td>
                <td>{{ $module->deleted_at->format('Y-m-d H:i') }}</td>
                <td>
                   

                    <form method="POST" action="{{ route('modules.restore', $module->id) }}" style="display:inline-block;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-sm btn-success" title="Restaurer">
                            <i class="fa fa-undo"></i> Restaurer
                        </button>
                    </form>

                    <form method="POST" action="{{ route('modules.forceDelete', $module->id) }}" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer ce Module DÉFINITIVEMENT ? Cette action est irréversible.');" 
                                title="Supprimer Définitivement">
                            <i class="fa fa-times"></i> Supprimer Déf.
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        
        @if($modules->isEmpty())
            <tr>
                <td colspan="5" class="text-center text-muted">La corbeille est vide pour le moment.</td>
            </tr>
        @endif
    </tbody>
</table>
@endsection