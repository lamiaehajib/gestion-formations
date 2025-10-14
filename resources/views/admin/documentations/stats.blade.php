@extends('layouts.app')

@section('content')
<div class="container">
    <h1>Statistiques Globales des Documentations</h1>
    
    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <h5 class="card-title">En Attente</h5>
                    <h2>{{ $stats['pending'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <h5 class="card-title">Approuvées</h5>
                    <h2>{{ $stats['approved'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger">
                <div class="card-body">
                    <h5 class="card-title">Rejetées</h5>
                    <h2>{{ $stats['rejected'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <h5 class="card-title">Total</h5>
                    <h2>{{ $stats['total'] }}</h2>
                </div>
            </div>
        </div>
    </div>
    
    <a href="{{ route('documentations.adminIndex') }}" class="btn btn-secondary mt-3">Retour à la Liste</a>
</div>
@endsection