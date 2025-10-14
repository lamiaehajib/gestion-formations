@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row mb-4">
        <div class="col-md-8">
            <h2>Statistiques de mes documentations</h2>
        </div>
        <div class="col-md-4 text-end">
            <a href="{{ route('consultant.documentations.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="card text-white bg-warning mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">En attente</h5>
                            <h2 class="mb-0">{{ $stats['pending'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-clock fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Approuvées</h5>
                            <h2 class="mb-0">{{ $stats['approved'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-check-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-danger mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Rejetées</h5>
                            <h2 class="mb-0">{{ $stats['rejected'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-times-circle fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="card-title">Total</h5>
                            <h2 class="mb-0">{{ $stats['total'] }}</h2>
                        </div>
                        <div>
                            <i class="fas fa-file-alt fa-3x opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h5>Taux de succès</h5>
                </div>
                <div class="card-body">
                    @php
                        $total = $stats['total'];
                        $approvedPercent = $total > 0 ? round(($stats['approved'] / $total) * 100, 1) : 0;
                        $rejectedPercent = $total > 0 ? round(($stats['rejected'] / $total) * 100, 1) : 0;
                        $pendingPercent = $total > 0 ? round(($stats['pending'] / $total) * 100, 1) : 0;
                    @endphp

                    @if($total > 0)
                        <div class="progress mb-3" style="height: 30px;">
                            <div class="progress-bar bg-success" 
                                 role="progressbar" 
                                 style="width: {{ $approvedPercent }}%"
                                 aria-valuenow="{{ $approvedPercent }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $approvedPercent }}%
                            </div>
                            <div class="progress-bar bg-danger" 
                                 role="progressbar" 
                                 style="width: {{ $rejectedPercent }}%"
                                 aria-valuenow="{{ $rejectedPercent }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $rejectedPercent }}%
                            </div>
                            <div class="progress-bar bg-warning" 
                                 role="progressbar" 
                                 style="width: {{ $pendingPercent }}%"
                                 aria-valuenow="{{ $pendingPercent }}" 
                                 aria-valuemin="0" 
                                 aria-valuemax="100">
                                {{ $pendingPercent }}%
                            </div>
                        </div>

                        <div class="row text-center">
                            <div class="col-md-4">
                                <span class="badge bg-success">Approuvées: {{ $approvedPercent }}%</span>
                            </div>
                            <div class="col-md-4">
                                <span class="badge bg-danger">Rejetées: {{ $rejectedPercent }}%</span>
                            </div>
                            <div class="col-md-4">
                                <span class="badge bg-warning">En attente: {{ $pendingPercent }}%</span>
                            </div>
                        </div>
                    @else
                        <p class="text-center text-muted">Aucune documentation soumise pour le moment</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection