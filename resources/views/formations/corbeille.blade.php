@extends('layouts.app') {{-- هنا تستخدم الـ layout التقليدي ديالك --}}

@section('title', 'corbielle') {{-- تعريف الـ title للصفحة --}}

@section('content')
<h2 class="text-danger"><i class="fa fa-trash"></i> Corbeille des Formations</h2>

<p class="alert alert-info">Cette liste contient toutes les formations qui ont été supprimées. Vous pouvez les visualiser, les restaurer, ou les supprimer définitivement.</p>

<table class="table table-bordered table-striped">
    <thead class="thead-dark">
        <tr>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>Consultant</th>
            <th>Statut</th>
            <th>Date d'Effacement</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($formations as $formation)
            <tr>
                <td>{{ $formation->title }}</td>
                <td>{{ $formation->category->name ?? 'N/A' }}</td>
                <td>{{ $formation->consultant->name ?? 'N/A' }}</td>
                <td>
                    <span class="badge badge-secondary">{{ $formation->status }}</span>
                </td>
                <td>{{ $formation->deleted_at->format('Y-m-d H:i') }}</td>
                <td>
                    <a href="{{ route('formations.show', $formation->id) }}" class="btn btn-info btn-sm" title="Voir Détails" style="display:inline-block;">
                        <i class="fas fa-eye"></i> Voir
                    </a>

                    <form method="POST" action="{{ route('formations.restore', $formation->id) }}" style="display:inline-block;">
                        @csrf
                        @method('PUT')
                        <button type="submit" class="btn btn-sm btn-success" title="Restaurer">
                            <i class="fa fa-undo"></i> Restaurer
                        </button>
                    </form>

                    <form method="POST" action="{{ route('formations.forceDelete', $formation->id) }}" style="display:inline-block;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" 
                                onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette Formation DÉFINITIVEMENT ? Cette action est irréversible.');" 
                                title="Supprimer Définitivement">
                            <i class="fa fa-times"></i> Supprimer Déf.
                        </button>
                    </form>
                </td>
            </tr>
        @endforeach
        
        @if($formations->isEmpty())
            <tr>
                <td colspan="6" class="text-center text-muted">La corbeille est vide pour le moment.</td>
            </tr>
        @endif
    </tbody>
</table>
@endsection