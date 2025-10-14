@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Documentations pour le Module: {{ $module->name }}</h1>
    
    <table class="table table-striped">
        <thead>
            <tr>
                <th>Description</th>
                <th>Statut</th>
                <th>Vérifié par</th>
                <th>Date de Soumission</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($documentations as $documentation)
            <tr>
                <td>{{ Str::limit($documentation->description, 50) }}</td>
                <td>
                    <span class="badge badge-{{ $documentation->status == 'approved' ? 'success' : ($documentation->status == 'rejected' ? 'danger' : 'warning') }}">
                        {{ ucfirst($documentation->status) }}
                    </span>
                </td>
                <td>{{ $documentation->verifiedBy->name ?? 'N/A' }}</td>
                <td>{{ $documentation->created_at->format('d/m/Y') }}</td>
                <td>
                    <a href="{{ route('documentations.show', $documentation->id) }}" class="btn btn-sm btn-primary">Voir</a>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    
    <a href="{{ route('documentations.index') }}" class="btn btn-secondary">Retour à la Liste</a>
</div>
@endsection