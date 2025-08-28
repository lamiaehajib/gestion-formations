@extends('layouts.app')

@section('title', 'Détails de la Catégorie')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h3 class="card-title">
                            <i class="{{ $category->icon ?? 'fas fa-folder' }} fa-2x mr-2"></i>
                            {{ $category->name }}
                            @if($category->is_active)
                                <span class="badge badge-success ml-2">Actif</span>
                            @else
                                <span class="badge badge-secondary ml-2">Inactif</span>
                            @endif
                        </h3>
                        <div>
                            <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Modifier
                            </a>
                            <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Retour
                            </a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Description</h5>
                            <p class="text-muted">
                                {{ $category->description ?: 'Aucune description disponible' }}
                            </p>
                        </div>
                        <div class="col-md-6">
                            <h5>Informations</h5>
                            <table class="table table-sm">
                                <tr>
                                    <td><strong>ID :</strong></td>
                                    <td>{{ $category->id }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Statut :</strong></td>
                                    <td>
                                        @if($category->is_active)
                                            <span class="badge badge-success">Actif</span>
                                        @else
                                            <span class="badge badge-secondary">Inactif</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Créée le :</strong></td>
                                    <td>{{ $category->created_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Modifiée le :</strong></td>
                                    <td>{{ $category->updated_at->format('d/m/Y à H:i') }}</td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-graduation-cap"></i>
                        Formations dans cette catégorie
                        <span class="badge badge-info ml-2">{{ $category->formations->count() }}</span>
                    </h4>
                </div>
                <div class="card-body">
                    @if($category->formations->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Titre</th>
                                        <th>Prix</th>
                                        <th>Durée</th>
                                        <th>Dates</th>
                                        <th>Statut</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($category->formations as $formation)
                                    <tr>
                                        <td>
                                            <strong>{{ $formation->title }}</strong>
                                        </td>
                                        <td>
                                            @if($formation->price)
                                                <span class="badge badge-success">{{ number_format($formation->price, 2) }} DH</span>
                                            @else
                                                <span class="badge badge-info">Gratuit</span>
                                            @endif
                                        </td>
                                        <td>
                                            <i class="fas fa-clock"></i>
                                            {{ $formation->duration_hours }}h
                                        </td>
                                        <td>
                                            @if($formation->start_date)
                                                <small>
                                                    Du {{ \Carbon\Carbon::parse($formation->start_date)->format('d/m/Y') }}
                                                    @if($formation->end_date)
                                                        <br>au {{ \Carbon\Carbon::parse($formation->end_date)->format('d/m/Y') }}
                                                    @endif
                                                </small>
                                            @else
                                                <span class="text-muted">Non planifiée</span>
                                            @endif
                                        </td>
                                        <td>
                                            @switch($formation->status)
                                                @case('published')
                                                    <span class="badge badge-success">Publié</span>
                                                    @break
                                                @case('draft')
                                                    <span class="badge badge-warning">Brouillon</span>
                                                    @break
                                                @case('completed')
                                                    <span class="badge badge-info">Terminé</span>
                                                    @break
                                                @default
                                                    <span class="badge badge-secondary">{{ $formation->status }}</span>
                                            @endswitch
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('formations.show', $formation) }}" class="btn btn-sm btn-info" title="Voir">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('formations.edit', $formation) }}" class="btn btn-sm btn-warning" title="Modifier">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-graduation-cap fa-3x mb-3"></i>
                            <p>Aucune formation dans cette catégorie</p>
                            <a href="{{ route('formations.create') }}?category={{ $category->id }}" class="btn btn-primary">
                                <i class="fas fa-plus"></i> Ajouter une formation
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-chart-bar"></i>
                        Statistiques
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row text-center">
                        <div class="col-6">
                            <div class="stat-card bg-primary text-white p-3 rounded mb-3">
                                <i class="fas fa-graduation-cap fa-2x"></i>
                                <h3 class="mt-2">{{ $stats['total_formations'] }}</h3>
                                <p class="mb-0">Total Formations</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card bg-success text-white p-3 rounded mb-3">
                                <i class="fas fa-check-circle fa-2x"></i>
                                <h3 class="mt-2">{{ $stats['published_formations'] }}</h3>
                                <p class="mb-0">Publiées</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card bg-warning text-white p-3 rounded mb-3">
                                <i class="fas fa-edit fa-2x"></i>
                                <h3 class="mt-2">{{ $stats['draft_formations'] }}</h3>
                                <p class="mb-0">Brouillons</p>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-card bg-info text-white p-3 rounded mb-3">
                                <i class="fas fa-flag-checkered fa-2x"></i>
                                <h3 class="mt-2">{{ $stats['completed_formations'] }}</h3>
                                <p class="mb-0">Terminées</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card mt-4">
                <div class="card-header">
                    <h4 class="card-title">
                        <i class="fas fa-tools"></i>
                        Actions rapides
                    </h4>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('formations.create') }}?category={{ $category->id }}" class="btn btn-primary btn-block">
                            <i class="fas fa-plus"></i> Ajouter une formation
                        </a>
                        <a href="{{ route('categories.edit', $category) }}" class="btn btn-warning btn-block">
                            <i class="fas fa-edit"></i> Modifier la catégorie
                        </a>
                        @if($category->formations->count() === 0)
                        <form method="POST" action="{{ route('categories.destroy', $category) }}" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette catégorie ?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">
                                <i class="fas fa-trash"></i> Supprimer la catégorie
                            </button>
                        </form>
                        @endif
                        <form method="POST" action="{{ route('categories.toggle-status', $category) }}">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn {{ $category->is_active ? 'btn-secondary' : 'btn-success' }} btn-block">
                                <i class="fas {{ $category->is_active ? 'fa-pause' : 'fa-play' }}"></i>
                                {{ $category->is_active ? 'Désactiver' : 'Activer' }} la catégorie
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection