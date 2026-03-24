@extends('layouts.app')

@section('title', 'Tentatives — ' . $exam->title)

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=DM+Sans:ital,opsz,wght@0,9..40,300;0,9..40,400;0,9..40,500;0,9..40,600;0,9..40,700;1,9..40,400&family=DM+Serif+Display:ital@0;1&display=swap');

    :root {
        --pink:   #C2185B;
        --red:    #D32F2F;
        --dark:   #1a0010;
        --card-r: 20px;
    }

    .aw { font-family: 'DM Sans', sans-serif; padding: 1.5rem 0; }

    /* ═══════════════════════════ HERO ═══════════════════════════ */
    .hero {
        background: linear-gradient(135deg, var(--dark) 0%, #3d0020 40%, #6b0030 70%, var(--pink) 100%);
        border-radius: 24px;
        padding: 2rem 2.5rem;
        position: relative;
        overflow: hidden;
        margin-bottom: 2rem;
        box-shadow: 0 20px 60px rgba(194,24,91,.35);
    }
    .hero::before {
        content:''; position:absolute; top:-80px; right:-80px;
        width:320px; height:320px;
        background:radial-gradient(circle,rgba(255,255,255,.07) 0%,transparent 70%);
        border-radius:50%; pointer-events:none;
    }
    .hero .back-btn {
        width:42px; height:42px; border-radius:12px;
        background:rgba(255,255,255,.12); border:1px solid rgba(255,255,255,.2);
        color:white; display:flex; align-items:center; justify-content:center;
        text-decoration:none; transition:all .3s ease;
        backdrop-filter:blur(10px); flex-shrink:0;
    }
    .hero .back-btn:hover { background:rgba(255,255,255,.25); transform:translateX(-3px); color:white; }
    .hero .hero-title {
        font-family:'DM Serif Display',serif; font-size:1.7rem;
        color:white; margin:0 0 4px; line-height:1.2;
    }
    .hero .hero-sub { color:rgba(255,255,255,.6); font-size:.84rem; margin:0; }
    .hero .stat-num {
        font-family:'DM Serif Display',serif; font-size:2.8rem; color:white; line-height:1;
    }
    .hero .stat-lbl {
        color:rgba(255,255,255,.55); font-size:.75rem;
        letter-spacing:.5px; text-transform:uppercase; font-weight:600;
    }

    /* ═══════════════════════ FORMATION CARDS ════════════════════ */
    .formation-cards { display:grid; grid-template-columns:repeat(auto-fill,minmax(260px,1fr)); gap:14px; margin-bottom:2rem; }

    .fcard {
        background:white; border-radius:18px;
        border:2px solid transparent;
        box-shadow:0 4px 20px rgba(0,0,0,.06);
        padding:1.2rem 1.35rem;
        cursor:pointer; transition:all .25s ease;
        text-decoration:none; display:block; position:relative; overflow:hidden;
    }
    .fcard::before {
        content:''; position:absolute; inset:0;
        background:linear-gradient(135deg,rgba(194,24,91,.04),transparent);
        opacity:0; transition:opacity .25s;
    }
    .fcard:hover { border-color:rgba(194,24,91,.35); transform:translateY(-3px); box-shadow:0 12px 36px rgba(194,24,91,.14); }
    .fcard:hover::before { opacity:1; }
    .fcard.active { border-color:var(--pink); box-shadow:0 8px 32px rgba(194,24,91,.2); }
    .fcard.active::after {
        content:''; position:absolute; top:0; left:0; right:0;
        height:3px; background:linear-gradient(90deg,var(--pink),var(--red));
    }

    .fcard .fcat {
        font-size:.68rem; font-weight:700; text-transform:uppercase;
        letter-spacing:.5px; color:var(--pink); margin-bottom:5px;
    }
    .fcard .fname {
        font-weight:700; font-size:.92rem; color:#1f2937; margin-bottom:12px; line-height:1.3;
    }
    .fcard .fstats { display:flex; gap:12px; flex-wrap:wrap; }
    .fcard .fstat { text-align:center; }
    .fcard .fstat-val {
        font-family:'DM Serif Display',serif; font-size:1.35rem;
        line-height:1; color:#1f2937;
    }
    .fcard .fstat-val.red { color:#dc2626; }
    .fcard .fstat-val.green { color:#059669; }
    .fcard .fstat-lbl { font-size:.64rem; color:#9ca3af; font-weight:600; text-transform:uppercase; }

    .fcard .voir-btn {
        position:absolute; right:14px; bottom:14px;
        background:linear-gradient(135deg,var(--pink),var(--red));
        color:white; border:none; border-radius:20px;
        padding:5px 14px; font-size:.72rem; font-weight:700;
        display:inline-flex; align-items:center; gap:5px;
        transition:all .2s; cursor:pointer; text-decoration:none;
    }
    .fcard .voir-btn:hover { transform:translateY(-1px); box-shadow:0 5px 16px rgba(194,24,91,.35); color:white; }

    /* ═══════════════════════ FILTER BAR ════════════════════════ */
    .filter-bar {
        background:white; border-radius:16px;
        padding:1rem 1.25rem; margin-bottom:1.5rem;
        box-shadow:0 3px 18px rgba(0,0,0,.06);
        border:1px solid rgba(0,0,0,.05);
        display:flex; gap:10px; flex-wrap:wrap; align-items:center;
    }
    .filter-bar .fb-label {
        font-size:.72rem; font-weight:700; text-transform:uppercase;
        letter-spacing:.5px; color:#9ca3af; white-space:nowrap;
    }
    .search-wrap { position:relative; flex:1; min-width:200px; }
    .search-wrap i { position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#9ca3af; font-size:.85rem; }
    .search-input {
        width:100%; padding:8px 12px 8px 34px;
        border:1.5px solid #e5e7eb; border-radius:10px;
        font-size:.85rem; color:#1f2937; transition:border-color .2s;
        outline:none; font-family:'DM Sans',sans-serif;
    }
    .search-input:focus { border-color:var(--pink); box-shadow:0 0 0 3px rgba(194,24,91,.08); }

    .filter-chips { display:flex; gap:8px; flex-wrap:wrap; }
    .fchip {
        padding:6px 14px; border-radius:20px;
        border:1.5px solid #e5e7eb; background:white;
        font-size:.75rem; font-weight:600; color:#6b7280;
        cursor:pointer; transition:all .2s; white-space:nowrap;
        text-decoration:none; display:inline-flex; align-items:center; gap:5px;
    }
    .fchip:hover { border-color:var(--pink); color:var(--pink); }
    .fchip.active {
        background:linear-gradient(135deg,var(--pink),var(--red));
        border-color:transparent; color:white;
    }

    .threshold-wrap { display:flex; align-items:center; gap:6px; }
    .threshold-input {
        width:64px; padding:6px 8px; border:1.5px solid #e5e7eb;
        border-radius:10px; font-size:.85rem; font-weight:700;
        color:#1f2937; text-align:center; outline:none;
    }
    .threshold-input:focus { border-color:var(--pink); }
    .threshold-lbl { font-size:.75rem; color:#9ca3af; }

    /* ═══════════════════════ TABLE CARD ════════════════════════ */
    .tcard {
        background:white; border-radius:var(--card-r);
        box-shadow:0 4px 28px rgba(0,0,0,.08);
        border:1px solid rgba(0,0,0,.05); overflow:hidden;
    }
    .tcard-header {
        padding:1.1rem 1.6rem; border-bottom:1px solid #f3f4f6;
        display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:8px;
    }
    .tcard-header .hl { display:flex; align-items:center; gap:10px; }
    .hicon {
        width:36px; height:36px; border-radius:10px;
        background:linear-gradient(135deg,var(--pink),var(--red));
        display:flex; align-items:center; justify-content:center;
        color:white; font-size:.85rem; flex-shrink:0;
    }
    .tcard-header h5 {
        font-family:'DM Serif Display',serif; font-size:1.05rem; color:#1a1a2e; margin:0;
    }

    .tbl { width:100%; border-collapse:collapse; }
    .tbl thead tr { background:#fafafa; }
    .tbl thead th {
        padding:11px 14px; font-size:.7rem; font-weight:700;
        text-transform:uppercase; letter-spacing:.6px; color:#9ca3af;
        border-bottom:1px solid #f3f4f6; white-space:nowrap;
    }
    .tbl tbody tr {
        border-bottom:1px solid #f9fafb; transition:background .2s;
        animation:fadeUp .35s ease both;
    }
    .tbl tbody tr:hover { background:#fdf2f6; }
    .tbl tbody tr:last-child { border-bottom:none; }
    @keyframes fadeUp {
        from { opacity:0; transform:translateY(6px); }
        to   { opacity:1; transform:translateY(0); }
    }
    .tbl tbody tr:nth-child(1)  { animation-delay:.04s; }
    .tbl tbody tr:nth-child(2)  { animation-delay:.08s; }
    .tbl tbody tr:nth-child(3)  { animation-delay:.12s; }
    .tbl tbody tr:nth-child(4)  { animation-delay:.16s; }
    .tbl tbody tr:nth-child(n+5){ animation-delay:.20s; }
    .tbl td { padding:13px 14px; vertical-align:middle; font-size:.88rem; color:#374151; }

    /* student cell */
    .savatar {
        width:38px; height:38px; border-radius:12px;
        background:linear-gradient(135deg,rgba(194,24,91,.12),rgba(211,47,47,.12));
        display:flex; align-items:center; justify-content:center;
        color:var(--pink); font-size:.9rem; flex-shrink:0;
    }
    .sname { font-weight:600; color:#1f2937; font-size:.87rem; line-height:1.2; }
    .semail { font-size:.73rem; color:#9ca3af; }

    /* score bar */
    .score-wrap { min-width:90px; }
    .score-num {
        font-family:'DM Serif Display',serif; font-size:1.25rem; line-height:1;
    }
    .score-num.pass { color:#059669; }
    .score-num.fail { color:#dc2626; }
    .score-pts { font-size:.7rem; color:#9ca3af; font-weight:500; }
    .score-bar-bg {
        height:4px; background:#f3f4f6; border-radius:4px; margin-top:4px;
        overflow:hidden;
    }
    .score-bar-fill { height:100%; border-radius:4px; transition:width .6s ease; }
    .score-bar-fill.pass { background:linear-gradient(90deg,#059669,#34d399); }
    .score-bar-fill.pass-partial { background:linear-gradient(90deg,#d97706,#fbbf24); }
    .score-bar-fill.fail { background:linear-gradient(90deg,#dc2626,#f87171); }

    /* attempt badge */
    .att-badge {
        display:inline-flex; align-items:center; gap:4px;
        padding:3px 9px; border-radius:20px; background:#f3f4f6;
        color:#6b7280; font-size:.72rem; font-weight:700;
    }

    /* status chips */
    .chip {
        display:inline-flex; align-items:center; gap:4px;
        padding:4px 11px; border-radius:50px;
        font-size:.7rem; font-weight:700; letter-spacing:.3px; white-space:nowrap;
    }
    .chip-blue    { background:#eff6ff; color:#2563eb; }
    .chip-amber   { background:#fffbeb; color:#d97706; }
    .chip-green   { background:#f0fdf4; color:#059669; }
    .chip-red     { background:#fef2f2; color:#dc2626; }
    .chip-gray    { background:#f3f4f6; color:#6b7280; }
    .chip-absent  { background:#fdf4ff; color:#7c3aed; }

    /* result chips */
    .rchip { display:inline-flex; align-items:center; gap:4px; padding:4px 11px; border-radius:50px; font-size:.7rem; font-weight:700; }
    .rchip.pass { background:#d1fae5; color:#059669; }
    .rchip.fail { background:#fee2e2; color:#dc2626; }

    /* formation tag */
    .ftag {
        display:inline-block; padding:2px 8px; border-radius:6px;
        background:rgba(194,24,91,.08); color:var(--pink);
        font-size:.68rem; font-weight:700;
    }

    /* action btns */
    .btn-view {
        display:inline-flex; align-items:center; gap:5px;
        padding:5px 13px; border-radius:20px; font-size:.73rem; font-weight:700;
        background:linear-gradient(135deg,var(--pink),var(--red));
        color:white; border:none; text-decoration:none; transition:all .22s; cursor:pointer;
    }
    .btn-view:hover { color:white; transform:translateY(-1px); box-shadow:0 5px 18px rgba(194,24,91,.32); }
    .btn-correct {
        display:inline-flex; align-items:center; gap:5px;
        padding:5px 13px; border-radius:20px; font-size:.73rem; font-weight:700;
        background:#fffbeb; color:#d97706; border:1.5px solid #fcd34d;
        text-decoration:none; transition:all .22s; cursor:pointer;
    }
    .btn-correct:hover { background:#fef3c7; transform:translateY(-1px); box-shadow:0 4px 14px rgba(217,119,6,.2); }

    /* empty */
    .empty { padding:3.5rem 2rem; text-align:center; }
    .empty-icon {
        width:72px; height:72px; border-radius:18px;
        background:linear-gradient(135deg,rgba(194,24,91,.07),rgba(211,47,47,.07));
        display:flex; align-items:center; justify-content:center;
        margin:0 auto .75rem; font-size:1.6rem; color:var(--pink); opacity:.6;
    }
    .empty p { color:#9ca3af; font-size:.88rem; margin:0; }

    /* absent section */
    .absent-section {
        background:white; border-radius:var(--card-r);
        box-shadow:0 4px 28px rgba(0,0,0,.08);
        border:1px solid rgba(0,0,0,.05); overflow:hidden; margin-bottom:1.5rem;
    }
    .absent-header {
        padding:1rem 1.5rem; border-bottom:1px solid #f3f4f6;
        display:flex; align-items:center; gap:10px;
        background:linear-gradient(90deg,#fdf4ff,white);
    }
    .absent-icon {
        width:34px; height:34px; border-radius:10px;
        background:#ede9fe; display:flex; align-items:center; justify-content:center;
        color:#7c3aed; font-size:.85rem; flex-shrink:0;
    }
    .absent-header h5 { font-family:'DM Serif Display',serif; font-size:1rem; color:#1a1a2e; margin:0; }

    /* pagination */
    .pag-wrap { padding:.85rem 1.5rem 1.25rem; border-top:1px solid #f3f4f6; }

    /* divider */
    .sdiv { height:2px; background:linear-gradient(90deg,var(--pink),var(--red),transparent); border-radius:2px; opacity:.15; }

    /* ═══ MODAL ════════════════════════════════════════════════ */
    .grade-modal .modal-content { border-radius:20px; border:none; box-shadow:0 25px 80px rgba(0,0,0,.15); overflow:hidden; }
    .grade-modal .modal-header { background:linear-gradient(135deg,var(--pink),var(--red)); padding:1.2rem 1.5rem; border:none; }
    .grade-modal .modal-title { font-family:'DM Serif Display',serif; color:white; font-size:1.05rem; display:flex; align-items:center; gap:8px; }
    .grade-modal .btn-close { filter:invert(1) brightness(2); opacity:.8; }
    .grade-modal .modal-body { padding:1.4rem; }
    .essay-card { border-radius:14px; border:1.5px solid #e5e7eb; overflow:hidden; margin-bottom:1rem; transition:box-shadow .2s; }
    .essay-card:hover { box-shadow:0 6px 24px rgba(0,0,0,.08); }
    .essay-card-hd { padding:.8rem 1rem; background:#fafafa; border-bottom:1px solid #f3f4f6; display:flex; justify-content:space-between; align-items:center; }
    .essay-qtxt { font-weight:600; font-size:.88rem; color:#1f2937; margin:0; }
    .pts-badge { padding:3px 9px; border-radius:20px; background:linear-gradient(135deg,var(--pink),var(--red)); color:white; font-size:.7rem; font-weight:700; white-space:nowrap; }
    .essay-card-bd { padding:.95rem 1rem; }
    .ans-box { background:#f9fafb; border-radius:9px; padding:.8rem .95rem; margin-bottom:.85rem; }
    .ans-lbl { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.5px; color:#9ca3af; margin-bottom:5px; display:flex; align-items:center; gap:4px; }
    .ans-txt { font-size:.87rem; color:#374151; line-height:1.55; }
    .pts-inp-wrap label { font-size:.78rem; font-weight:700; color:#374151; margin-bottom:5px; display:block; }
    .pts-inp { border:2px solid #e5e7eb; border-radius:9px; padding:7px 12px; font-size:.88rem; font-weight:600; color:#1f2937; width:100%; transition:border-color .22s; outline:none; font-family:'DM Sans',sans-serif; }
    .pts-inp:focus { border-color:var(--pink); box-shadow:0 0 0 3px rgba(194,24,91,.08); }
    .grade-modal .modal-footer { padding:.9rem 1.4rem; border-top:1px solid #f3f4f6; gap:8px; }
    .btn-cancel { padding:7px 18px; border-radius:20px; border:1.5px solid #e5e7eb; background:white; color:#6b7280; font-size:.8rem; font-weight:600; cursor:pointer; transition:all .2s; }
    .btn-cancel:hover { background:#f9fafb; }
    .btn-save { padding:7px 18px; border-radius:20px; background:linear-gradient(135deg,#059669,#047857); color:white; border:none; font-size:.8rem; font-weight:700; cursor:pointer; transition:all .22s; display:inline-flex; align-items:center; gap:5px; }
    .btn-save:hover { transform:translateY(-1px); box-shadow:0 5px 18px rgba(5,150,105,.28); }
    .info-alert { background:#eff6ff; border:1px solid #bfdbfe; border-radius:11px; padding:9px 13px; font-size:.8rem; color:#2563eb; display:flex; align-items:center; gap:7px; margin-bottom:1rem; }
</style>
@endpush

@section('content')
<div class="aw">

{{-- ═══ HERO ═══ --}}
<div class="hero">
    <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('exams.show', $exam) }}" class="back-btn"><i class="fas fa-arrow-left"></i></a>
            <div>
                <h1 class="hero-title">{{ $exam->title }}</h1>
                <p class="hero-sub"><i class="fas fa-clipboard-list me-1" style="opacity:.7;"></i>Tentatives des étudiants · Score seuil : {{ $exam->passing_score }}%</p>
            </div>
        </div>
        <div style="text-align:right;">
            <div class="stat-num">{{ $attempts->total() + $absentStudents->count() }}</div>
            <div class="stat-lbl">Étudiants concernés</div>
        </div>
    </div>
</div>

{{-- ═══ FORMATION CARDS ═══ --}}
<div class="formation-cards">
    {{-- Carte "Toutes formations" --}}
    <a href="{{ request()->fullUrlWithQuery(['formation_id' => null, 'score_filter' => null]) }}"
       class="fcard {{ !$selectedFormationId ? 'active' : '' }}">
        <div class="fcat">Vue globale</div>
        <div class="fname">Toutes les formations</div>
        <div class="fstats">
            <div class="fstat">
                <div class="fstat-val">{{ $formationStats->sum('total_students') }}</div>
                <div class="fstat-lbl">Inscrits</div>
            </div>
            <div class="fstat">
                <div class="fstat-val">{{ $formationStats->sum('attempted') }}</div>
                <div class="fstat-lbl">Tentatives</div>
            </div>
            <div class="fstat">
                <div class="fstat-val red">{{ $formationStats->sum('absent') }}</div>
                <div class="fstat-lbl">Absents</div>
            </div>
            <div class="fstat">
                <div class="fstat-val green">{{ $formationStats->sum('passed') }}</div>
                <div class="fstat-lbl">Réussis</div>
            </div>
        </div>
    </a>

    {{-- Carte par formation --}}
    @foreach($formationStats as $fs)
    <a href="{{ request()->fullUrlWithQuery(['formation_id' => $fs['formation']->id, 'score_filter' => null, 'page' => null]) }}"
       class="fcard {{ $selectedFormationId == $fs['formation']->id ? 'active' : '' }}">
        <div class="fcat">{{ $fs['formation']->category->name ?? '—' }}</div>
        <div class="fname" style="padding-right:60px;">{{ $fs['formation']->title }}</div>
        <div class="fstats">
            <div class="fstat">
                <div class="fstat-val">{{ $fs['total_students'] }}</div>
                <div class="fstat-lbl">Inscrits</div>
            </div>
            <div class="fstat">
                <div class="fstat-val red">{{ $fs['absent'] }}</div>
                <div class="fstat-lbl">Absents</div>
            </div>
            <div class="fstat">
                <div class="fstat-val green">{{ $fs['passed'] }}</div>
                <div class="fstat-lbl">Réussis</div>
            </div>
            <div class="fstat">
                <div class="fstat-val" style="color:#6366f1;">{{ $fs['avg_score'] }}%</div>
                <div class="fstat-lbl">Moy.</div>
            </div>
        </div>
        <span class="voir-btn"><i class="fas fa-eye"></i> Voir</span>
    </a>
    @endforeach
</div>

{{-- ═══ FILTER BAR ═══ --}}
<form method="GET" action="{{ route('exams.attempts', $exam) }}" id="filterForm">
    @if($selectedFormationId)
        <input type="hidden" name="formation_id" value="{{ $selectedFormationId }}">
    @endif

    <div class="filter-bar">
        <span class="fb-label"><i class="fas fa-filter me-1"></i>Filtres</span>

        {{-- Search --}}
        <div class="search-wrap">
            <i class="fas fa-search"></i>
            <input type="text" name="search" class="search-input"
                   placeholder="Rechercher par nom ou email…"
                   value="{{ request('search') }}"
                   oninput="this.form.submit()">
        </div>

        {{-- Filter chips --}}
        <div class="filter-chips">
            <a href="{{ request()->fullUrlWithQuery(['score_filter' => null, 'page' => null]) }}"
               class="fchip {{ !request('score_filter') ? 'active' : '' }}">
                <i class="fas fa-list"></i> Tous
            </a>
            <a href="{{ request()->fullUrlWithQuery(['score_filter' => 'passed', 'page' => null]) }}"
               class="fchip {{ request('score_filter') === 'passed' ? 'active' : '' }}">
                <i class="fas fa-trophy"></i> Réussis
            </a>
            <a href="{{ request()->fullUrlWithQuery(['score_filter' => 'failed', 'page' => null]) }}"
               class="fchip {{ request('score_filter') === 'failed' ? 'active' : '' }}">
                <i class="fas fa-times-circle"></i> Échoués
            </a>
            <a href="{{ request()->fullUrlWithQuery(['score_filter' => 'absent', 'page' => null]) }}"
               class="fchip {{ request('score_filter') === 'absent' ? 'active' : '' }}"
               style="{{ request('score_filter') === 'absent' ? 'background:linear-gradient(135deg,#7c3aed,#6d28d9);border-color:transparent;color:white;' : '' }}">
                <i class="fas fa-user-slash"></i> Absents
            </a>
            <div class="threshold-wrap">
                <a href="#" onclick="applyBelowFilter(event)"
                   class="fchip {{ request('score_filter') === 'below' ? 'active' : '' }}">
                    <i class="fas fa-arrow-down"></i> En dessous de
                </a>
                <input type="number" name="score_threshold" id="scoreThreshold"
                       class="threshold-input"
                       min="0" max="100"
                       value="{{ request('score_threshold', $exam->passing_score) }}">
                <span class="threshold-lbl">%</span>
                <input type="hidden" name="score_filter" id="scoreFilterHidden"
                       value="{{ request('score_filter') }}">
            </div>
        </div>
    </div>
</form>

{{-- ═══ ABSENTS TABLE ═══ --}}
@if(request('score_filter') === 'absent' && $absentStudents->count() > 0)
<div class="absent-section">
    <div class="absent-header">
        <div class="absent-icon"><i class="fas fa-user-slash"></i></div>
        <h5>Étudiants absents — {{ $absentStudents->count() }} étudiant(s) n'ont pas encore passé l'examen</h5>
    </div>
    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Étudiant</th>
                    <th>Formation</th>
                    <th>Filière</th>
                    <th>Inscription</th>
                </tr>
            </thead>
            <tbody>
                @foreach($absentStudents as $ins)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="savatar"><i class="fas fa-user"></i></div>
                            <div>
                                <div class="sname">{{ $ins->user->name }}</div>
                                <div class="semail">{{ $ins->user->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td><span class="ftag">{{ $ins->formation->title }}</span></td>
                    <td style="font-size:.78rem;color:#6b7280;">{{ $ins->formation->category->name ?? '—' }}</td>
                    <td>
                        <span class="chip chip-gray" style="font-size:.68rem;">
                            {{ $ins->created_at->format('d/m/Y') }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

{{-- ═══ ATTEMPTS TABLE CARD ═══ --}}
@if(request('score_filter') !== 'absent' || $absentStudents->isEmpty())
<div class="tcard">
    <div class="tcard-header">
        <div class="hl">
            <div class="hicon"><i class="fas fa-users"></i></div>
            <h5>
                @if($selectedFormationId)
                    {{ $formationStats->firstWhere('formation.id', $selectedFormationId)['formation']->title ?? 'Formation' }}
                @else
                    Toutes les tentatives
                @endif
            </h5>
        </div>
        <span style="font-size:.78rem;color:#9ca3af;">{{ $attempts->total() }} résultat(s)</span>
    </div>
    <div class="sdiv"></div>

    <div style="overflow-x:auto;">
        <table class="tbl">
            <thead>
                <tr>
                    <th>Étudiant</th>
                    <th>Formation</th>
                    <th>Tentative N°</th>
                    <th>Date</th>
                    <th>Score</th>
                    <th>Statut</th>
                    <th>Résultat</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($attempts as $attempt)
                <tr>
                    {{-- Étudiant --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <div class="savatar"><i class="fas fa-user"></i></div>
                            <div>
                                <div class="sname">{{ $attempt->user->name }}</div>
                                <div class="semail">{{ $attempt->user->email }}</div>
                            </div>
                        </div>
                    </td>

                    {{-- Formation --}}
                    <td>
                        @if($attempt->inscription && $attempt->inscription->formation)
                            <span class="ftag" style="max-width:160px;display:inline-block;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;"
                                  title="{{ $attempt->inscription->formation->title }}">
                                {{ $attempt->inscription->formation->title }}
                            </span>
                        @else
                            <span style="color:#d1d5db;">—</span>
                        @endif
                    </td>

                    {{-- Attempt N° --}}
                    <td>
                        <span class="att-badge">
                            <i class="fas fa-hashtag" style="font-size:.6rem;"></i>
                            {{ $attempt->attempt_number }}
                        </span>
                    </td>

                    {{-- Date --}}
                    <td>
                        <div style="font-weight:600;font-size:.84rem;color:#1f2937;">
                            {{ $attempt->started_at->format('d/m/Y') }}
                        </div>
                        <div style="font-size:.72rem;color:#9ca3af;">
                            {{ $attempt->started_at->format('H:i') }}
                        </div>
                    </td>

                    {{-- Score --}}
                    <td>
                        @if(in_array($attempt->status, ['graded', 'submitted']))
                            @php
                                $sc = round($attempt->score, 1);
                                $cls = $attempt->passed ? 'pass' : 'fail';
                                $maxPts = $attempt->max_points ?: 1;
                                $barCls = $attempt->passed ? 'pass' : ($sc >= ($exam->passing_score * 0.7) ? 'pass-partial' : 'fail');
                            @endphp
                            <div class="score-wrap">
                                <span class="score-num {{ $cls }}">{{ $sc }}%</span>
                                <div class="score-pts">
                                    {{ number_format($attempt->total_points, 1) }} / {{ number_format($maxPts, 1) }} pts
                                </div>
                                <div class="score-bar-bg">
                                    <div class="score-bar-fill {{ $barCls }}" style="width:{{ min($sc,100) }}%"></div>
                                </div>
                            </div>
                        @else
                            <span style="color:#d1d5db;font-size:.84rem;">—</span>
                        @endif
                    </td>

                    {{-- Statut --}}
                    <td>
                        @if($attempt->status == 'in_progress')
                            <span class="chip chip-blue"><i class="fas fa-spinner fa-spin" style="font-size:.6rem;"></i> {{ $attempt->getStatusLabel() }}</span>
                        @elseif($attempt->status == 'submitted')
                            <span class="chip chip-amber"><i class="fas fa-clock" style="font-size:.6rem;"></i> {{ $attempt->getStatusLabel() }}</span>
                        @elseif($attempt->status == 'graded')
                            <span class="chip chip-green"><i class="fas fa-check" style="font-size:.6rem;"></i> {{ $attempt->getStatusLabel() }}</span>
                        @else
                            <span class="chip chip-red"><i class="fas fa-times" style="font-size:.6rem;"></i> {{ $attempt->getStatusLabel() }}</span>
                        @endif
                    </td>

                    {{-- Résultat --}}
                    <td>
                        @if(in_array($attempt->status, ['graded', 'submitted']))
                            <span class="rchip {{ $attempt->passed ? 'pass' : 'fail' }}">
                                <i class="fas {{ $attempt->passed ? 'fa-trophy' : 'fa-times-circle' }}" style="font-size:.6rem;"></i>
                                {{ $attempt->passed ? 'Réussi' : 'Échoué' }}
                            </span>
                        @else
                            <span style="color:#d1d5db;font-size:.84rem;">—</span>
                        @endif
                    </td>

                    {{-- Actions --}}
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <a href="{{ route('exams.attempt-details', $attempt) }}" class="btn-view">
                                <i class="fas fa-eye"></i> Voir
                            </a>
                            @if($attempt->status == 'submitted' && $attempt->results)
                                @php
                                    $hasEssay = collect($attempt->results)->contains(fn($r) =>
                                        ($r['feedback'] ?? '') === 'En attente de correction manuelle.'
                                    );
                                @endphp
                                @if($hasEssay)
                                <button class="btn-correct"
                                        data-bs-toggle="modal"
                                        data-bs-target="#gradeModal{{ $attempt->id }}">
                                    <i class="fas fa-pen"></i> Corriger
                                </button>
                                @endif
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8">
                        <div class="empty">
                            <div class="empty-icon"><i class="fas fa-clipboard-list"></i></div>
                            <p>Aucune tentative trouvée pour ces critères</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($attempts->hasPages())
    <div class="pag-wrap">{{ $attempts->links() }}</div>
    @endif
</div>
@endif

</div>
@endsection

{{-- ═══ GRADING MODALS ═══ --}}
@foreach($attempts as $attempt)
    @if($attempt->status == 'submitted' && $attempt->results)
        @php
            $hasEssay = collect($attempt->results)->contains(fn($r) =>
                ($r['feedback'] ?? '') === 'En attente de correction manuelle.'
            );
        @endphp
        @if($hasEssay)
        <div class="modal fade grade-modal" id="gradeModal{{ $attempt->id }}" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            <i class="fas fa-pen-nib"></i>
                            Correction — {{ $attempt->user->name }}
                        </h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('exams.attempts.grade', $attempt) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <div class="info-alert">
                                <i class="fas fa-info-circle"></i>
                                Attribuez des points aux questions de type «&nbsp;Réponse longue&nbsp;» ci-dessous.
                            </div>
                            @foreach($attempt->exam->questions as $question)
                                @if($question->type == 'essay')
                                    @php
                                        $userAnswer   = $attempt->answers[$question->id] ?? null;
                                        $currentGrade = $attempt->results[$question->id]['points_earned'] ?? 0;
                                    @endphp
                                    <div class="essay-card">
                                        <div class="essay-card-hd">
                                            <p class="essay-qtxt">{{ $question->question_text }}</p>
                                            <span class="pts-badge">{{ $question->points }} pts max</span>
                                        </div>
                                        <div class="essay-card-bd">
                                            <div class="ans-box">
                                                <div class="ans-lbl"><i class="fas fa-user-edit"></i> Réponse de l'étudiant</div>
                                                <div class="ans-txt">{{ $userAnswer ?? 'Aucune réponse' }}</div>
                                            </div>
                                            <div class="pts-inp-wrap">
                                                <label>
                                                    Points attribués <span style="color:var(--pink);">*</span>
                                                    <span style="color:#9ca3af;font-weight:400;">(max {{ $question->points }})</span>
                                                </label>
                                                <input type="number"
                                                       name="grades[{{ $question->id }}]"
                                                       class="pts-inp"
                                                       min="0" max="{{ $question->points }}"
                                                       step="0.5" value="{{ $currentGrade }}" required>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn-cancel" data-bs-dismiss="modal">Annuler</button>
                            <button type="submit" class="btn-save">
                                <i class="fas fa-check"></i> Enregistrer
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif
    @endif
@endforeach

@push('scripts')
<script>
function applyBelowFilter(e) {
    e.preventDefault();
    document.getElementById('scoreFilterHidden').value = 'below';
    document.getElementById('filterForm').submit();
}
// Auto-submit threshold on change
document.getElementById('scoreThreshold').addEventListener('change', function() {
    if (document.getElementById('scoreFilterHidden').value === 'below') {
        document.getElementById('filterForm').submit();
    }
});
</script>
@endpush