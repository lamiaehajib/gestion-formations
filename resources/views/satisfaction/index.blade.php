@extends('layouts.app')

@section('title', 'Mes Évaluations')

@section('content')
<div class="container">
    <div class="page-header">
        <h1>Évaluations de formations</h1>
        <p class="subtitle">Donnez votre avis sur les formations que vous avez terminées</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            {{ session('error') }}
        </div>
    @endif

    @if($inscriptions->isEmpty())
        <div class="empty-state">
            <svg width="64" height="64" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                <polyline points="14 2 14 8 20 8"></polyline>
                <line x1="16" y1="13" x2="8" y2="13"></line>
                <line x1="16" y1="17" x2="8" y2="17"></line>
                <polyline points="10 9 9 9 8 9"></polyline>
            </svg>
            <h3>Aucune évaluation en attente</h3>
            <p>Vous avez évalué toutes vos formations terminées. Merci pour votre participation !</p>
        </div>
    @else
        <div class="formations-grid">
            @foreach($inscriptions as $inscription)
                <div class="formation-card">
                    <div class="card-header">
                        <h3>{{ $inscription->formation->name ?? 'Formation' }}</h3>
                        <span class="badge badge-pending">À évaluer</span>
                    </div>
                    
                    <div class="card-body">
                        <p class="formation-description">
                            {{ Str::limit($inscription->formation->description ?? '', 100) }}
                        </p>
                        
                        <div class="formation-meta">
                            <span class="meta-item">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                    <rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect>
                                    <line x1="16" y1="2" x2="16" y2="6"></line>
                                    <line x1="8" y1="2" x2="8" y2="6"></line>
                                    <line x1="3" y1="10" x2="21" y2="10"></line>
                                </svg>
                                Terminée le {{ $inscription->completed_at ? $inscription->completed_at->format('d/m/Y') : 'N/A' }}
                            </span>
                        </div>
                    </div>
                    
                    <div class="card-footer">
                        <a href="{{ route('satisfaction.create', $inscription->id) }}" class="btn-evaluate">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon>
                            </svg>
                            Évaluer maintenant
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<style>
.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 40px 20px;
}

.page-header {
    margin-bottom: 40px;
}

.page-header h1 {
    margin: 0 0 8px 0;
    font-size: 32px;
    color: #1f2937;
}

.subtitle {
    margin: 0;
    color: #6b7280;
    font-size: 16px;
}

.alert {
    padding: 16px;
    border-radius: 8px;
    margin-bottom: 24px;
}

.alert-success {
    background: #d1fae5;
    color: #065f46;
    border: 1px solid #6ee7b7;
}

.alert-error {
    background: #fee2e2;
    color: #991b1b;
    border: 1px solid #fca5a5;
}

.empty-state {
    text-align: center;
    padding: 80px 20px;
}

.empty-state svg {
    color: #9ca3af;
    margin-bottom: 16px;
}

.empty-state h3 {
    margin: 0 0 8px 0;
    font-size: 20px;
    color: #374151;
}

.empty-state p {
    margin: 0;
    color: #6b7280;
}

.formations-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 24px;
}

.formation-card {
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.2s;
}

.formation-card:hover {
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    transform: translateY(-2px);
}

.card-header {
    padding: 20px;
    border-bottom: 1px solid #e5e7eb;
    display: flex;
    justify-content: space-between;
    align-items: start;
    gap: 12px;
}

.card-header h3 {
    margin: 0;
    font-size: 18px;
    color: #1f2937;
    flex: 1;
}

.badge {
    padding: 4px 12px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 500;
    white-space: nowrap;
}

.badge-pending {
    background: #fef3c7;
    color: #92400e;
}

.card-body {
    padding: 20px;
}

.formation-description {
    margin: 0 0 16px 0;
    color: #6b7280;
    line-height: 1.6;
}

.formation-meta {
    display: flex;
    flex-wrap: wrap;
    gap: 16px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 14px;
    color: #6b7280;
}

.meta-item svg {
    color: #9ca3af;
}

.card-footer {
    padding: 20px;
    border-top: 1px solid #e5e7eb;
}

.btn-evaluate {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    width: 100%;
    justify-content: center;
    padding: 12px 24px;
    background: #3b82f6;
    color: white;
    border-radius: 8px;
    text-decoration: none;
    font-weight: 500;
    transition: background 0.2s;
}

.btn-evaluate:hover {
    background: #2563eb;
}
</style>
@endsection