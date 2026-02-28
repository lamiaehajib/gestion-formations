@extends('layouts.app')

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Space+Mono:wght@400;700&family=Sora:wght@300;400;600;700&display=swap');

    :root {
        --rouge:    #D32F2F;
        --rouge-fonce: #B71C1C;
        --rouge-clair: rgba(211,47,47,0.08);
        --noir:     #0f0f0f;
        --surface:  #f7f7f7;
        --card:     #ffffff;
        --bordure:  #e8e8e8;
        --texte:    #1a1a1a;
        --texte-soft: #6b6b6b;
        --vert:     #2e7d32;
        --orange:   #e65100;
        --bleu:     #1565c0;
    }

    body { font-family: 'Sora', sans-serif; background: var(--surface); color: var(--texte); }

    /* ── PAGE HEADER ── */
    .sl-hero {
        background: var(--noir);
        color: white;
        padding: 2.5rem 2rem 2rem;
        border-radius: 0 0 28px 28px;
        margin-bottom: 2rem;
        position: relative;
        overflow: hidden;
    }
    .sl-hero::before {
        content: '';
        position: absolute;
        inset: 0;
        background: repeating-linear-gradient(
            -45deg,
            transparent,
            transparent 18px,
            rgba(211,47,47,0.04) 18px,
            rgba(211,47,47,0.04) 19px
        );
    }
    .sl-hero .badge-exam {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        background: var(--rouge);
        color: white;
        font-family: 'Space Mono', monospace;
        font-size: .7rem;
        letter-spacing: .12em;
        text-transform: uppercase;
        padding: .3rem .75rem;
        border-radius: 100px;
        margin-bottom: .9rem;
    }
    .sl-hero h1 {
        font-size: 1.75rem;
        font-weight: 700;
        margin: 0 0 .3rem;
        line-height: 1.2;
    }
    .sl-hero p { color: rgba(255,255,255,.55); font-size: .9rem; margin: 0; }
    .sl-hero .student-chip {
        display: inline-flex;
        align-items: center;
        gap: .5rem;
        background: rgba(255,255,255,.1);
        border: 1px solid rgba(255,255,255,.15);
        padding: .35rem .9rem;
        border-radius: 100px;
        font-size: .85rem;
        margin-top: .9rem;
    }

    /* ── STATS CARDS ── */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 2rem;
    }
    .stat-card {
        background: var(--card);
        border: 1px solid var(--bordure);
        border-radius: 16px;
        padding: 1.2rem 1.4rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: box-shadow .2s, transform .2s;
    }
    .stat-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,.07); transform: translateY(-2px); }
    .stat-card .stat-icon {
        width: 46px; height: 46px;
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        font-size: 1.15rem;
        flex-shrink: 0;
    }
    .stat-card .stat-label {
        font-size: .72rem;
        color: var(--texte-soft);
        font-weight: 600;
        letter-spacing: .06em;
        text-transform: uppercase;
        margin-bottom: .15rem;
    }
    .stat-card .stat-value {
        font-family: 'Space Mono', monospace;
        font-size: 1.5rem;
        font-weight: 700;
        line-height: 1;
    }

    /* colors per activity */
    .ic-tab_switch       { background: #fff3e0; color: #e65100; }
    .ic-fullscreen_exit  { background: #fce4ec; color: #c62828; }
    .ic-right_click      { background: #ede7f6; color: #4527a0; }
    .ic-copy_attempt     { background: #e8f5e9; color: #1b5e20; }
    .ic-devtools         { background: #e3f2fd; color: #0d47a1; }
    .ic-window_blur      { background: #f3e5f5; color: #6a1b9a; }
    .ic-pointer_lock_exit{ background: #fff8e1; color: #f57f17; }
    .ic-other            { background: #f5f5f5; color: #424242; }

    /* ── TABLE SECTION ── */
    .table-card {
        background: var(--card);
        border: 1px solid var(--bordure);
        border-radius: 20px;
        overflow: hidden;
        box-shadow: 0 2px 16px rgba(0,0,0,.04);
    }
    .table-card-header {
        padding: 1.2rem 1.5rem;
        border-bottom: 1px solid var(--bordure);
        display: flex;
        align-items: center;
        gap: .75rem;
    }
    .table-card-header h5 {
        font-size: .95rem;
        font-weight: 700;
        margin: 0;
        flex: 1;
    }
    .table-card-header .count-badge {
        background: var(--rouge-clair);
        color: var(--rouge);
        font-family: 'Space Mono', monospace;
        font-size: .72rem;
        font-weight: 700;
        padding: .25rem .65rem;
        border-radius: 100px;
    }

    .sl-table { width: 100%; border-collapse: collapse; }
    .sl-table thead th {
        background: #fafafa;
        padding: .85rem 1.2rem;
        font-size: .7rem;
        font-weight: 700;
        letter-spacing: .1em;
        text-transform: uppercase;
        color: var(--texte-soft);
        border-bottom: 1px solid var(--bordure);
        white-space: nowrap;
    }
    .sl-table tbody tr {
        border-bottom: 1px solid var(--bordure);
        transition: background .15s;
    }
    .sl-table tbody tr:last-child { border-bottom: none; }
    .sl-table tbody tr:hover { background: #fafafa; }
    .sl-table td {
        padding: .85rem 1.2rem;
        font-size: .85rem;
        vertical-align: middle;
    }

    /* timestamp */
    .ts-cell {
        font-family: 'Space Mono', monospace;
        font-size: .75rem;
        color: var(--texte-soft);
        white-space: nowrap;
    }

    /* activity badge */
    .activity-pill {
        display: inline-flex;
        align-items: center;
        gap: .4rem;
        padding: .3rem .75rem;
        border-radius: 100px;
        font-size: .75rem;
        font-weight: 600;
    }
    .activity-pill i { font-size: .65rem; }

    .ap-tab_switch        { background: #fff3e0; color: #e65100; }
    .ap-fullscreen_exit   { background: #fce4ec; color: #c62828; }
    .ap-right_click       { background: #ede7f6; color: #4527a0; }
    .ap-copy_attempt      { background: #e8f5e9; color: #1b5e20; }
    .ap-devtools_attempt  { background: #e3f2fd; color: #0d47a1; }
    .ap-devtools_detected { background: #e3f2fd; color: #0d47a1; }
    .ap-window_blur       { background: #f3e5f5; color: #6a1b9a; }
    .ap-pointer_lock_exit { background: #fff8e1; color: #f57f17; }
    .ap-f12_attempt       { background: #fce4ec; color: #ad1457; }
    .ap-source_attempt    { background: #f1f8e9; color: #33691e; }

    /* tab switch count */
    .switch-count {
        font-family: 'Space Mono', monospace;
        font-size: .8rem;
        font-weight: 700;
    }
    .switch-count.danger { color: var(--rouge); }
    .switch-count.warn   { color: var(--orange); }
    .switch-count.safe   { color: var(--vert); }

    /* IP */
    .ip-cell {
        font-family: 'Space Mono', monospace;
        font-size: .75rem;
        background: #f5f5f5;
        padding: .2rem .55rem;
        border-radius: 6px;
        color: var(--texte-soft);
    }

    /* empty state */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        color: var(--texte-soft);
    }
    .empty-state i { font-size: 3rem; opacity: .2; margin-bottom: 1rem; display: block; }
</style>
@endpush

@section('content')
<div class="container-xl py-4">

    {{-- ── HERO ── --}}
    <div class="sl-hero">
        <div class="badge-exam">
            <i class="fas fa-shield-alt"></i> Rapport de Sécurité
        </div>
        <h1>{{ $attempt->exam->title }}</h1>
        <p>Surveillance et logs des activités suspectes détectées pendant l'examen</p>
        <div class="student-chip">
            <i class="fas fa-user-graduate" style="color:rgba(255,255,255,.5);font-size:.8rem;"></i>
            {{ $attempt->user->name }}
        </div>
    </div>

    {{-- ── STATS ── --}}
    @php
        $activityConfig = [
            'tab_switch'        => ['icon' => 'fas fa-exchange-alt',   'label' => 'Changements d\'onglet'],
            'fullscreen_exit'   => ['icon' => 'fas fa-compress-arrows-alt', 'label' => 'Sorties plein écran'],
            'right_click'       => ['icon' => 'fas fa-mouse-pointer',  'label' => 'Clics droits'],
            'copy_attempt'      => ['icon' => 'fas fa-copy',           'label' => 'Tentatives de copie'],
            'devtools_attempt'  => ['icon' => 'fas fa-terminal',       'label' => 'Accès DevTools'],
            'devtools_detected' => ['icon' => 'fas fa-bug',            'label' => 'DevTools détectés'],
            'window_blur'       => ['icon' => 'fas fa-window-minimize','label' => 'Pertes de focus'],
            'pointer_lock_exit' => ['icon' => 'fas fa-lock-open',      'label' => 'Sorties Pointer Lock'],
            'f12_attempt'       => ['icon' => 'fas fa-keyboard',       'label' => 'Tentatives F12'],
            'source_attempt'    => ['icon' => 'fas fa-code',           'label' => 'Tentatives source'],
        ];
    @endphp

    <div class="stat-grid">
        @foreach($suspiciousActivities as $activity => $count)
            @if($count > 0)
            @php $cfg = $activityConfig[$activity] ?? ['icon' => 'fas fa-exclamation-circle', 'label' => str_replace('_', ' ', $activity)]; @endphp
            <div class="stat-card">
                <div class="stat-icon ic-{{ Str::before($activity, '_detected') == 'devtools' ? 'devtools' : $activity }}">
                    <i class="{{ $cfg['icon'] }}"></i>
                </div>
                <div>
                    <div class="stat-label">{{ $cfg['label'] }}</div>
                    <div class="stat-value" style="color:{{ $count >= 5 ? '#D32F2F' : ($count >= 2 ? '#e65100' : '#1a1a1a') }};">
                        {{ $count }}
                    </div>
                </div>
            </div>
            @endif
        @endforeach
    </div>

    {{-- ── TABLE ── --}}
    <div class="table-card">
        <div class="table-card-header">
            <i class="fas fa-list-ul" style="color:var(--rouge);"></i>
            <h5>Journal complet des activités</h5>
            <span class="count-badge">{{ $logs->count() }} événements</span>
        </div>

        @if($logs->isEmpty())
            <div class="empty-state">
                <i class="fas fa-shield-check"></i>
                <p class="fw-semibold mb-1">Aucune activité suspecte détectée</p>
                <small>Cet étudiant a passé l'examen sans déclencher d'alerte</small>
            </div>
        @else
        <div style="overflow-x:auto;">
            <table class="sl-table">
                <thead>
                    <tr>
                        <th><i class="fas fa-clock me-1"></i>Horodatage</th>
                        <th><i class="fas fa-tag me-1"></i>Type d'activité</th>
                        <th><i class="fas fa-exchange-alt me-1"></i>Changements d'onglet</th>
                        <th><i class="fas fa-network-wired me-1"></i>Adresse IP</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($logs as $log)
                    @php
                        $type = $log->activity_type;
                        $cfg  = $activityConfig[$type] ?? ['icon' => 'fas fa-exclamation-circle', 'label' => str_replace('_', ' ', ucfirst($type))];
                        $switchClass = $log->tab_switch_count >= 5 ? 'danger' : ($log->tab_switch_count >= 2 ? 'warn' : 'safe');
                    @endphp
                    <tr>
                        <td class="ts-cell">
                            {{ \Carbon\Carbon::parse($log->activity_timestamp)->format('d/m/Y') }}
                            <span style="opacity:.5;">·</span>
                            {{ \Carbon\Carbon::parse($log->activity_timestamp)->format('H:i:s') }}
                        </td>
                        <td>
                            <span class="activity-pill ap-{{ $type }}">
                                <i class="{{ $cfg['icon'] }}"></i>
                                {{ $cfg['label'] }}
                            </span>
                        </td>
                        <td>
                            <span class="switch-count {{ $switchClass }}">
                                {{ $log->tab_switch_count }}
                                @if($log->tab_switch_count >= 5)
                                    <i class="fas fa-exclamation-circle ms-1" style="font-size:.7rem;"></i>
                                @endif
                            </span>
                        </td>
                        <td><span class="ip-cell">{{ $log->ip_address }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection