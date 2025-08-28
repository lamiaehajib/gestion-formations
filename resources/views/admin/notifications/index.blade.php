@extends('layouts.app') {{-- Remplacez 'layouts.app' par votre layout principal --}}

@section('content')
<div class="container-fluid">
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Toutes les Notifications</h6>
        </div>
        <div class="card-body">
            @forelse ($notifications as $notification)
            <a href="#" class="text-decoration-none text-dark d-block mb-3">
                <div class="d-flex align-items-start gap-12 p-3 border rounded @if(!$notification->is_read) bg-light @endif">
                    {{-- Icône basée sur le type de notification --}}
                    <div class="w-48 h-48 rounded-circle bg-gray-100 flex-center">
                        @if($notification->type == 'payment')
                            <i class="fas fa-money-bill-wave text-26 text-success"></i>
                        @elseif($notification->type == 'reclamation')
                            <i class="fas fa-exclamation-triangle text-26 text-danger"></i>
                        @else
                            <i class="fas fa-info-circle text-26 text-primary"></i>
                        @endif
                    </div>
                    <div class="d-flex flex-column">
                        <h6 class="text-15 mb-0 text-dark @if(!$notification->is_read) fw-bold @endif">{{ $notification->title }}</h6>
                        <span class="text-gray-500 text-13 mt-8">{{ $notification->message }}</span>
                        <small class="text-muted mt-1">{{ $notification->created_at->diffForHumans() }}</small>
                    </div>
                </div>
            </a>
            @empty
            <p class="text-center text-muted">Aucune notification à afficher.</p>
            @endforelse

            <div class="d-flex justify-content-center mt-4">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection