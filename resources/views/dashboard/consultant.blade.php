@extends('layouts.app') {{-- Assurez-vous d'avoir un fichier de mise en page principal --}}

@section('title', 'Tableau de Bord Consultant')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    /* Global Styles & Variables */
    :root {
        /* Primary Brand Colors (Using the same colors from your student dashboard style) */
        --primary-blue: #D32F2F; /* A vibrant, modern red */
        --primary-dark-blue: #C2185B; /* A darker shade for depth */
        --primary-light-blue: #ffd6d65e; /* Very light red for subtle backgrounds */
        --accent-green: #28B463; /* Success/positive actions */
        --accent-orange: #F5B041; /* Warning/pending states */
        --accent-red: #E74C3C; /* Danger/alerts */
        --accent-purple: #8E44AD; /* A new accent color for variety */
        --union-dark-blu: #D32F2F;
        --union-light-ble: #C2185B;

        /* Neutral Colors */
        --background-light: #F8F9FA; /* Very light background for overall dashboard */
        --card-background: #FFFFFF; /* Pure white for cards */
        --border-light: #E0E6F0; /* Light border for separation */
        --text-dark: #2C3E50; /* Primary dark text */
        --text-medium: #62748A; /* Secondary text, lighter */
        --text-light: #9BB0C7; /* Muted text */
        --shadow-subtle: rgba(0, 0, 0, 0.03); /* Lighter shadow for table rows */

        /* Shadows */
        --shadow-sm: 0 2px 8px rgba(0, 0, 0, 0.05);
        --shadow-md: 0 5px 20px rgba(0, 0, 0, 0.08);
        --shadow-lg: 0 10px 30px rgba(0, 0, 0, 0.12);
    }

    body {
        font-family: 'Inter', sans-serif;
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        line-height: 1.6;
    }

    .dashboard-body {
        background-color: var(--background-light);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        color: var(--text-dark);
    }

    .dashboard-container {
        width: 100%;
        max-width: 1600px;
        margin: 0 auto;
        padding: 40px;
        flex-grow: 1;
    }

    @media (max-width: 1199px) {
        .dashboard-container { padding: 30px; }
    }
    @media (max-width: 992px) {
        .dashboard-container { padding: 25px; }
    }
    @media (max-width: 768px) {
        .dashboard-container { padding: 20px; }
    }

    /* Card Base Style */
    .card-base {
        background: var(--card-background);
        border-radius: 16px;
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid var(--border-light);
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
    }

    .card-base:hover {
        transform: translateY(-5px);
        box-shadow: var(--shadow-md);
    }

    /* Header Card */
    .header-card {
        padding: 45px 30px;
        text-align: center;
        background: linear-gradient(135deg, var(--union-dark-blu) 0%, var(--union-light-ble) 100%);
        color: white;
        position: relative;
        overflow: hidden;
        border-radius: 20px;
    }

    .header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('https://www.transparenttextures.com/patterns/connected-dots.png') repeat;
        opacity: 0.1;
        pointer-events: none;
    }

    .header-card h1 {
        color: white;
        font-size: 3.5rem;
        font-weight: 800;
        margin-bottom: 15px;
        text-shadow: 0 5px 15px rgba(0,0,0,0.4);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }

    .header-card h1 .fa-chalkboard-teacher {
        font-size: 0.9em;
    }

    .header-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.4rem;
        margin-bottom: 35px;
        font-weight: 400;
    }

    .date-filter {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .date-filter input, .date-filter select {
        background: rgba(255, 255, 255, 0.15);
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 12px 18px;
        color: white;
        font-size: 16px;
        outline: none;
        transition: all 0.3s ease;
        -webkit-appearance: none;
        -moz-appearance: none;
        appearance: none;
    }

    .date-filter select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ffffff' width='18px' height='18px'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3Cpath d='M0 0h24v24H0z' fill='none'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        padding-right: 40px;
    }

    .date-filter input:focus, .date-filter select:focus {
        border-color: rgba(255, 255, 255, 0.6);
        background-color: rgba(255, 255, 255, 0.25);
        box-shadow: 0 0 0 3px rgba(255, 255, 255, 0.2);
    }

    .date-filter input::placeholder {
        color: rgba(255, 255, 255, 0.7);
    }

    .filter-btn {
        background: white;
        border: none;
        border-radius: 12px;
        padding: 12px 25px;
        color: var(--primary-blue);
        cursor: pointer;
        font-weight: 700;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-btn:hover {
        transform: translateY(-3px);
        box-shadow: 0 12px 25px rgba(0, 0, 0, 0.25);
        color: var(--primary-dark-blue);
    }

    .filter-btn .fa-solid {
        color: var(--primary-blue);
        transition: color 0.3s ease;
    }
    .filter-btn:hover .fa-solid {
        color: var(--primary-dark-blue);
    }

    .sr-only {
        position: absolute;
        width: 1px;
        height: 1px;
        padding: 0;
        margin: -1px;
        overflow: hidden;
        clip: rect(0, 0, 0, 0);
        white-space: nowrap;
        border-width: 0;
    }

    /* Stats Grid (Overview Cards) */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
        gap: 25px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--card-background);
        border-radius: 16px;
        padding: 25px;
        box-shadow: var(--shadow-sm);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid var(--border-light);
        color: var(--text-dark);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 6px;
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-dark-blue));
        border-radius: 16px 16px 0 0;
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--shadow-lg);
    }

    .stat-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 15px;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px;
        color: white;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        background: linear-gradient(45deg, var(--primary-blue), var(--primary-dark-blue));
    }
    .stat-card:nth-child(2) .stat-icon { background: linear-gradient(45deg, var(--accent-green), #2ECC71); }
    .stat-card:nth-child(3) .stat-icon { background: linear-gradient(45deg, var(--accent-purple), #9B59B6); }
    .stat-card:nth-child(4) .stat-icon { background: linear-gradient(45deg, var(--accent-orange), #E67E22); }

    .stat-info {
        text-align: right;
    }

    .stat-number {
        font-size: 3rem;
        font-weight: 800;
        background: linear-gradient(45deg, var(--primary-blue), var(--primary-dark-blue));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        color: var(--primary-blue);
        margin-bottom: 5px;
        min-width: 90px;
        white-space: nowrap;
    }
    .stat-card:nth-child(2) .stat-number { background: linear-gradient(45deg, var(--accent-green), #2ECC71); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .stat-card:nth-child(3) .stat-number { background: linear-gradient(45deg, var(--accent-purple), #9B59B6); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .stat-card:nth-child(4) .stat-number { background: linear-gradient(45deg, var(--accent-orange), #E67E22); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }

    .stat-label {
        color: var(--text-medium);
        font-size: 15px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px;
    }

    /* General Table Reset for New Styles */
    .list-card .table {
        margin-top: 0;
        width: 100%;
        border-collapse: collapse;
        border-spacing: 0;
        font-size: 15px;
    }

    .list-card thead th,
    .list-card tbody td {
        vertical-align: middle;
        text-align: left;
        color: var(--text-dark);
        font-size: 15px;
    }

    /* Table Style "Minimalist Dark Header" */
    .table-dark-header {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--border-light);
        box-shadow: var(--shadow-sm);
    }
    .table-dark-header thead th {
        background-color: var(--primary-dark-blue);
        color: white;
        font-weight: 600;
        padding: 20px 25px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    }
    .table-dark-header tbody tr {
        background-color: var(--card-background);
        border-bottom: 1px solid var(--border-light);
    }
    .table-dark-header tbody tr:last-child {
        border-bottom: none;
    }
    .table-dark-header tbody tr:hover {
        background-color: var(--background-light);
        transform: none;
        box-shadow: none;
    }
    .table-dark-header tbody td {
        padding: 18px 25px;
        border: none;
    }
    .table-dark-header .badge {
        font-size: 0.75rem;
        padding: 6px 12px;
    }

    /* Table Style "Clean Lines with Subtle Depth" */
    .table-subtle-depth {
        border-collapse: separate;
        border-spacing: 0;
        border: 1px solid var(--border-light);
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05);
    }
    .table-subtle-depth thead th {
        background-color: var(--background-light);
        color: var(--text-dark);
        font-weight: 700;
        padding: 15px 20px;
        border-bottom: 2px solid var(--border-light);
        text-transform: uppercase;
        letter-spacing: 0.7px;
    }
    .table-subtle-depth thead th:first-child { border-top-left-radius: 12px; }
    .table-subtle-depth thead th:last-child { border-top-right-radius: 12px; }

    .table-subtle-depth tbody tr {
        background-color: var(--card-background);
        border-bottom: 1px solid var(--border-light);
    }
    .table-subtle-depth tbody tr:last-child {
        border-bottom: none;
    }
    .table-subtle-depth tbody tr:hover {
        background-color: rgba(211, 47, 47, 0.03); /* Subtle red tint on hover */
        transform: translateY(-2px);
        box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    }
    .table-subtle-depth tbody td {
        padding: 14px 20px;
        border: none;
    }

    /* Badges */
    .badge {
        padding: 7px 14px;
        border-radius: 25px;
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-width: 90px;
        justify-content: center;
        transition: all 0.2s ease;
    }
    .badge.bg-primary { background-color: var(--primary-blue) !important; color: white !important; }
    .badge.bg-success { background-color: var(--accent-green) !important; color: white !important; }
    .badge.bg-warning { background-color: var(--accent-orange) !important; color: white !important; }
    .badge.bg-info { background-color: #274A78 !important; color: white !important; } /* Re-using a nice blue for info */
    .badge.bg-secondary { background-color: var(--text-light) !important; color: white !important; }

    /* Chart Containers */
    .chart-container {
        position: relative;
        height: 320px;
        width: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .chart-card canvas {
        width: 100% !important;
        height: 320px !important;
        max-height: 320px;
        min-height: 250px;
    }

    .chart-title {
        font-size: 1.6rem;
        font-weight: 700;
        color: var(--text-dark);
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 12px;
        text-align: center;
    }

    .chart-title .fa-solid {
        margin-right: 12px;
        color: var(--primary-blue);
        font-size: 1.1em;
    }

    .chart-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 70px;
        height: 5px;
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-dark-blue));
        border-radius: 3px;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 40px;
        color: var(--text-medium);
        background: var(--primary-light-blue);
        border-radius: 16px;
        margin-top: 20px;
        border: 2px dashed rgba(211, 47, 47, 0.3); /* Dotted border with primary color */
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .empty-state .icon {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.7;
        color: var(--primary-blue);
    }
    .empty-state p {
        margin-bottom: 25px;
        font-size: 1.15rem;
        color: var(--text-dark);
        font-weight: 500;
    }
    .empty-state .btn {
        padding: 12px 30px;
        border-radius: 30px;
        font-weight: 600;
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-dark-blue));
        border: none;
    }

    /* Smaller empty state for payment sub-sections */
    .empty-state.empty-state-small {
        padding: 25px;
        font-size: 0.95rem;
        border-radius: 12px;
        margin-top: 15px;
        background: rgba(211, 47, 47, 0.05);
        border: 1px solid rgba(211, 47, 47, 0.15);
    }
    .empty-state.empty-state-small .icon {
        font-size: 3rem;
        margin-bottom: 15px;
    }
    .empty-state.empty-state-small p {
        margin-bottom: 15px;
        font-size: 1rem;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        text-decoration: none;
        cursor: pointer;
        padding: 10px 20px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-primary {
        background: linear-gradient(135deg, var(--primary-blue), var(--primary-dark-blue));
        border: none;
        color: white;
        box-shadow: 0 5px 15px rgba(211, 47, 47, 0.25);
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-dark-blue), var(--primary-blue));
        box-shadow: 0 8px 20px rgba(211, 47, 47, 0.4);
        transform: translateY(-2px);
    }

    .btn-outline-primary {
        border: 2px solid var(--primary-blue);
        color: var(--primary-blue);
        background: transparent;
        border-radius: 12px;
    }
    .btn-outline-primary:hover {
        background-color: var(--primary-blue);
        color: white;
        box-shadow: 0 3px 10px rgba(211, 47, 47, 0.2);
    }

    /* Specific Button Styles (consistent with badge colors) */
    .btn-info {
        background-color: #274A78; /* A darker, professional blue */
        border-color: #274A78;
        color: white;
    }
    .btn-info:hover {
        background-color: #1a3556;
        border-color: #1a3556;
    }

    .btn-success {
        background-color: var(--accent-green);
        border-color: var(--accent-green);
        color: white;
    }
    .btn-success:hover {
        background-color: #2ECC71;
        border-color: #2ECC71;
    }

    /* Small button size for actions */
    .btn-sm {
        padding: 6px 12px;
        font-size: 0.85rem;
        border-radius: 8px;
        gap: 6px;
    }

    /* Row layout adjustments for alignment */
    .row {
        display: flex;
        flex-wrap: wrap;
        margin-left: -15px;
        margin-right: -15px;
    }
    .row > [class*="col-"] {
        padding-left: 15px;
        padding-right: 15px;
        margin-bottom: 30px;
        display: flex;
        flex-direction: column;
    }

    /* Responsive Adjustments */
    @media (min-width: 1200px) {
        .stats-grid { grid-template-columns: repeat(4, 1fr); }
    }
    @media (max-width: 1199px) {
        .stats-grid { grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); }
    }
    @media (max-width: 992px) {
        .header-card h1 { font-size: 2.8rem; }
        .header-subtitle { font-size: 1.2rem; margin-bottom: 25px; }
        .stats-grid { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
        .row > .col-lg-8,
        .row > .col-lg-4,
        .row > .col-lg-6 { flex: 1 1 100%; max-width: 100%; }
        .chart-card, .list-card { flex: 1 1 100%; }
        .chart-card canvas { height: 280px !important; max-height: 280px; }
    }
    @media (max-width: 768px) {
        .dashboard-container { padding: 15px; }
        .header-card { padding: 25px; }
        .header-card h1 { font-size: 2.2rem; gap: 10px; }
        .header-subtitle { font-size: 1.05rem; margin-bottom: 20px; }
        .date-filter { flex-direction: column; align-items: stretch; }
        .date-filter input, .date-filter select, .filter-btn { width: 100%; margin-bottom: 10px; }
        .stat-card, .chart-card, .card-base { padding: 20px; border-radius: 12px; }
        .stat-number { font-size: 2.5rem; }
        .stat-icon { width: 55px; height: 55px; font-size: 24px; }
        .list-card thead th, .list-card tbody td { padding: 12px 10px; font-size: 14px; }
        .badge { padding: 6px 12px; font-size: 0.75rem; min-width: 70px; }
        .chart-title { font-size: 1.4rem; margin-bottom: 20px; padding-bottom: 8px; }
        .chart-title .fa-solid { margin-right: 8px; font-size: 1em; }
        .chart-title::after { width: 50px; height: 3px; }
        .empty-state { padding: 30px; border-radius: 12px; }
        .empty-state .icon { font-size: 3rem; margin-bottom: 15px; }
        .empty-state p { font-size: 1rem; margin-bottom: 20px; }
    }
    @media (max-width: 480px) {
        .header-card h1 { font-size: 1.8rem; flex-direction: column; gap: 5px; }
        .header-subtitle { font-size: 0.9rem; }
        .stats-grid { grid-template-columns: 1fr; }
        .stat-card { text-align: center; }
        .stat-content { flex-direction: column; align-items: center; }
        .stat-icon { margin-bottom: 10px; }
        .stat-info { text-align: center; }
        .list-card tbody td { display: block; width: 100%; text-align: left; padding: 8px 5px; }
        .list-card tbody td:last-child { text-align: left; }
        .list-card tbody tr:hover { padding-left: 5px; }
        .btn { padding: 8px 15px; font-size: 0.9rem; }
        .empty-state.empty-state-small { padding: 15px; }
    }

    .btn-action {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 8px 16px;
    border-radius: 5px;
    text-decoration: none;
    font-weight: 500;
    font-size: 14px;
    transition: all 0.3s ease;
    cursor: pointer;
    border: 1px solid transparent; /* Pour éviter le "saut" au hover */
}

.btn-view {
    background-color: #007bff; /* Couleur primaire */
    color: #fff;
    border-color: #007bff;
}

.btn-view:hover {
    background-color: #0056b3; /* Un bleu plus foncé au survol */
    box-shadow: 0 4px 12px rgba(0, 123, 255, 0.2);
    transform: translateY(-2px); /* Un petit effet de "levage" */
}

.btn-action i {
    margin-right: 8px; /* Espace entre l'icône et le texte */
}
</style>
@endpush

@section('content')
<div class="dashboard-body">
    <div class="dashboard-container">
        {{-- Header Section (matches student dashboard) --}}
        <div class="card-base header-card mb-4">
            <h1><i class="fa-solid fa-chalkboard-teacher"></i> Tableau de Bord Consultant</h1>
            <p class="header-subtitle">Bienvenue, {{ Auth::user()->name }} ! Gérez vos formations, vos cours et vos étudiants.</p>
            <form action="{{ route('dashboard') }}" method="GET" class="date-filter">
                <label for="selected_month" class="sr-only">Filtrer par mois :</label>
                <select name="selected_month" id="selected_month">
                    <option value="">Tous les mois</option>
                    @foreach($months as $num => $name)
                        <option value="{{ $num }}" {{ (int)$selectedMonth === $num ? 'selected' : '' }}>
                            {{ $name }}
                        </option>
                    @endforeach
                </select>
                <label for="selected_year" class="sr-only">Année :</label>
                <input type="number" name="selected_year" id="selected_year" value="{{ $selectedYear ?? \Carbon\Carbon::now()->year }}" min="2020" max="{{ \Carbon\Carbon::now()->addYears(5)->year }}">
                <button type="submit" class="filter-btn"><i class="fa-solid fa-filter"></i> Filtrer</button>
                @if($selectedMonth || request()->filled('selected_year') && request()->selected_year != \Carbon\Carbon::now()->year)
                    <a href="{{ route('dashboard') }}" class="filter-btn" style="background: var(--text-light); color: white; box-shadow: none;">
                        <i class="fa-solid fa-rotate-right"></i> Réinitialiser
                    </a>
                @endif
            </form>
        </div>

        {{-- Stats Cards Section (matches student dashboard) --}}
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(45deg, var(--accent-green), #2ECC71);"><i class="fas fa-users"></i></div>
                    <div class="stat-info">
                        <div class="stat-number" style="background: linear-gradient(45deg, var(--accent-green), #2ECC71); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $totalStudents }}</div>
                        <div class="stat-label">Total Étudiants</div>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(45deg, var(--primary-blue), var(--primary-dark-blue));"><i class="fas fa-book-open"></i></div>
                    <div class="stat-info">
                        <div class="stat-number" style="background: linear-gradient(45deg, var(--primary-blue), var(--primary-dark-blue)); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $totalCourses }}</div>
                        <div class="stat-label">Total Cours</div>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(45deg, var(--accent-purple), #9B59B6);"><i class="fas fa-chalkboard"></i></div>
                    <div class="stat-info">
                        <div class="stat-number" style="background: linear-gradient(45deg, var(--accent-purple), #9B59B6); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $myFormations->count() }}</div>
                        <div class="stat-label">Mes Formations</div>
                    </div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon" style="background: linear-gradient(45deg, var(--accent-orange), #E67E22);"><i class="fas fa-calendar-day"></i></div>
                    <div class="stat-info">
                        <div class="stat-number" style="background: linear-gradient(45deg, var(--accent-orange), #E67E22); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $coursesToday->count() }}</div>
                        <div class="stat-label">Cours Aujourd'hui</div>
                    </div>
                </div>
            </div>
        </div>


        {{-- Module Progress Overview Cards --}}
<div class="stats-grid" style="margin-top: 40px;">
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-icon" style="background: linear-gradient(45deg, #28B463, #2ECC71);"><i class="fas fa-tasks"></i></div>
            <div class="stat-info">
                <div class="stat-number" style="background: linear-gradient(45deg, #28B463, #2ECC71); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $totalModules }}</div>
                <div class="stat-label">Total Modules</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-icon" style="background: linear-gradient(45deg, #28a745, #20c997);"><i class="fas fa-check-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number" style="background: linear-gradient(45deg, #28a745, #20c997); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $totalCompletedModules }}</div>
                <div class="stat-label">Modules Terminés</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-icon" style="background: linear-gradient(45deg, #17a2b8, #20c997);"><i class="fas fa-play-circle"></i></div>
            <div class="stat-info">
                <div class="stat-number" style="background: linear-gradient(45deg, #17a2b8, #20c997); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $totalInProgressModules }}</div>
                <div class="stat-label">En Cours</div>
            </div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-content">
            <div class="stat-icon" style="background: linear-gradient(45deg, #ffc107, #fd7e14);"><i class="fas fa-hourglass-start"></i></div>
            <div class="stat-info">
                <div class="stat-number" style="background: linear-gradient(45deg, #ffc107, #fd7e14); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">{{ $totalNotStartedModules }}</div>
                <div class="stat-label">Pas Commencés</div>
            </div>
        </div>
    </div>
</div>

{{-- Global Module Progress Chart --}}
<div class="row" style="margin-top: 30px;">
    <div class="col-lg-8 mb-4">
        <div class="card-base">
            <h3 class="chart-title"><i class="fa-solid fa-chart-line"></i> Progression Globale des Modules</h3>
            @if($totalModules == 0)
                <div class="empty-state">
                    <div class="icon"><i class="fa-solid fa-tasks"></i></div>
                    <p>Aucun module assigné pour le moment.</p>
                </div>
            @else
                <div class="chart-container">
                    <canvas id="globalModulesProgressChart"></canvas>
                </div>
            @endif
        </div>
    </div>

    {{-- Module Progress Statistics --}}
    <div class="col-lg-4 mb-4">
        <div class="card-base">
            <h3 class="chart-title"><i class="fa-solid fa-chart-pie"></i> Répartition des Modules</h3>
            @if($totalModules == 0)
                <div class="empty-state empty-state-small">
                    <div class="icon"><i class="fa-solid fa-chart-pie"></i></div>
                    <p>Aucune donnée disponible.</p>
                </div>
            @else
                <div class="chart-container">
                    <canvas id="moduleStatsChart"></canvas>
                </div>
                
                {{-- Progress Statistics Details --}}
                <div style="margin-top: 25px;">
                    <div class="progress-stat-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border-light);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; background-color: #28a745; border-radius: 50%;"></div>
                            <span style="font-weight: 600; color: var(--text-dark);">Terminés</span>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700; color: #28a745;">{{ $moduleProgressStats['completed']['percentage'] }}%</div>
                            <div style="font-size: 0.85rem; color: var(--text-medium);">{{ $moduleProgressStats['completed']['count'] }} modules</div>
                        </div>
                    </div>
                    
                    <div class="progress-stat-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0; border-bottom: 1px solid var(--border-light);">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; background-color: #17a2b8; border-radius: 50%;"></div>
                            <span style="font-weight: 600; color: var(--text-dark);">En Cours</span>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700; color: #17a2b8;">{{ $moduleProgressStats['in_progress']['percentage'] }}%</div>
                            <div style="font-size: 0.85rem; color: var(--text-medium);">{{ $moduleProgressStats['in_progress']['count'] }} modules</div>
                        </div>
                    </div>
                    
                    <div class="progress-stat-item" style="display: flex; justify-content: space-between; align-items: center; padding: 12px 0;">
                        <div style="display: flex; align-items: center; gap: 10px;">
                            <div style="width: 12px; height: 12px; background-color: #dc3545; border-radius: 50%;"></div>
                            <span style="font-weight: 600; color: var(--text-dark);">Non Commencés</span>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700; color: #dc3545;">{{ $moduleProgressStats['not_started']['percentage'] }}%</div>
                            <div style="font-size: 0.85rem; color: var(--text-medium);">{{ $moduleProgressStats['not_started']['count'] }} modules</div>
                        </div>
                    </div>
                </div>

                {{-- Overall Progress Bar --}}
                <div style="margin-top: 25px; padding-top: 20px; border-top: 2px solid var(--border-light);">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-weight: 700; color: var(--text-dark);">Progression Moyenne</span>
                        <span style="font-weight: 800; font-size: 1.4rem; color: var(--primary-blue);">{{ $overallAverageProgress }}%</span>
                    </div>
                    <div style="width: 100%; height: 12px; background-color: var(--background-light); border-radius: 6px; overflow: hidden;">
                        <div style="height: 100%; background: linear-gradient(90deg, var(--primary-blue), var(--primary-dark-blue)); width: {{ $overallAverageProgress }}%; transition: width 0.8s ease;"></div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Modules by Formation --}}
@if($modulesByFormation->isNotEmpty())
<div class="row">
    <div class="col-12">
        <div class="card-base">
            <h3 class="chart-title"><i class="fa-solid fa-layer-group"></i> Modules par Formation</h3>
            
            <div style="display: grid; gap: 30px;">
                @foreach($modulesByFormation as $formationId => $formationData)
                    <div style="background: var(--background-light); border-radius: 16px; padding: 25px; border: 1px solid var(--border-light);">
                        {{-- Formation Header --}}
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                            <div>
                                <h4 style="color: var(--text-dark); font-weight: 700; font-size: 1.3rem; margin: 0; margin-bottom: 5px;">
                                    <i class="fas fa-graduation-cap" style="color: var(--primary-blue); margin-right: 10px;"></i>
                                    {{ $formationData['formation']->title }}
                                </h4>
                                <div style="display: flex; gap: 20px; flex-wrap: wrap;">
                                    <span style="color: var(--text-medium); font-size: 0.9rem;">
                                        <i class="fas fa-tasks" style="margin-right: 5px;"></i>
                                        {{ $formationData['total_modules'] }} modules
                                    </span>
                                    <span style="color: var(--accent-green); font-size: 0.9rem; font-weight: 600;">
                                        <i class="fas fa-check-circle" style="margin-right: 5px;"></i>
                                        {{ $formationData['completed_modules'] }} terminés
                                    </span>
                                    <span style="color: var(--accent-orange); font-size: 0.9rem; font-weight: 600;">
                                        <i class="fas fa-play-circle" style="margin-right: 5px;"></i>
                                        {{ $formationData['in_progress_modules'] }} en cours
                                    </span>
                                </div>
                            </div>
                            <div style="text-align: right;">
                                <div style="font-size: 2.2rem; font-weight: 800; color: var(--primary-blue); margin-bottom: 5px;">
                                    {{ $formationData['average_progress'] }}%
                                </div>
                                <div style="color: var(--text-medium); font-size: 0.85rem; text-transform: uppercase; letter-spacing: 0.5px; font-weight: 600;">
                                    Progression Moyenne
                                </div>
                            </div>
                        </div>

                        {{-- Progress Bar for Formation --}}
                        <div style="width: 100%; height: 8px; background-color: white; border-radius: 4px; overflow: hidden; margin-bottom: 25px; box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);">
                            <div style="height: 100%; background: linear-gradient(90deg, var(--primary-blue), var(--primary-dark-blue)); width: {{ $formationData['average_progress'] }}%; transition: width 1s ease;"></div>
                        </div>

                        {{-- Modules List --}}
                        @if($formationData['modules']->isNotEmpty())
                            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 15px;">
                                @foreach($formationData['modules'] as $module)
                                    <div style="background: white; border-radius: 12px; padding: 20px; border: 1px solid var(--border-light); box-shadow: var(--shadow-sm); transition: all 0.3s ease;" onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='var(--shadow-md)'" onmouseout="this.style.transform='translateY(0px)'; this.style.boxShadow='var(--shadow-sm)'">
                                        <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 12px;">
                                            <h5 style="color: var(--text-dark); font-weight: 600; font-size: 1rem; margin: 0; line-height: 1.4; flex: 1;">
                                                {{ $module->title }}
                                            </h5>
                                            <div style="margin-left: 15px;">
                                                @if($module->progress == 100)
                                                    <div style="width: 24px; height: 24px; background-color: #28a745; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-check" style="color: white; font-size: 12px;"></i>
                                                    </div>
                                                @elseif($module->progress >= 1)
                                                    <div style="width: 24px; height: 24px; background-color: #17a2b8; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-play" style="color: white; font-size: 10px;"></i>
                                                    </div>
                                                @else
                                                    <div style="width: 24px; height: 24px; background-color: #dc3545; border-radius: 50%; display: flex; align-items: center; justify-content: center;">
                                                        <i class="fas fa-pause" style="color: white; font-size: 10px;"></i>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                        
                                        {{-- Module Progress Bar --}}
                                        <div style="margin-bottom: 8px;">
                                            <div style="width: 100%; height: 6px; background-color: var(--background-light); border-radius: 3px; overflow: hidden;">
                                                <div style="height: 100%; 
                                                    background-color: {{ $module->progress >= 80 ? '#28a745' : ($module->progress >= 60 ? '#17a2b8' : ($module->progress >= 40 ? '#ffc107' : ($module->progress >= 20 ? '#fd7e14' : '#dc3545'))) }}; 
                                                    width: {{ $module->progress }}%; 
                                                    transition: width 0.8s ease;"></div>
                                            </div>
                                        </div>
                                        
                                        <div style="display: flex; justify-content: space-between; align-items: center;">
                                            <span style="color: var(--text-medium); font-size: 0.85rem;">
                                                Ordre: #{{ $module->order }}
                                            </span>
                                            <span style="font-weight: 700; color: {{ $module->progress >= 80 ? '#28a745' : ($module->progress >= 60 ? '#17a2b8' : ($module->progress >= 40 ? '#ffc107' : ($module->progress >= 20 ? '#fd7e14' : '#dc3545'))) }};">
                                                {{ $module->progress }}%
                                            </span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif

{{-- Recent Module Updates --}}
@if($recentModuleUpdates->isNotEmpty())
<div class="row">
    <div class="col-12">
        <div class="card-base">
            <h3 class="chart-title"><i class="fa-solid fa-clock"></i> Modules Récemment Mis à Jour</h3>
            <div class="table-responsive">
                <table class="table table-subtle-depth">
                    <thead>
                        <tr>
                            <th>Module</th>
                            <th>Formation</th>
                            <th>Progression</th>
                            <th>Nouveaux Cours</th>
                            <th>Dernière Mise à Jour</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentModuleUpdates as $module)
                            <tr>
                                <td><strong>{{ $module->title }}</strong></td>
                                <td><span class="badge bg-secondary">{{ $module->formation->title }}</span></td>
                                <td>
                                    <div style="display: flex; align-items: center; gap: 10px;">
                                        <div style="width: 60px; height: 6px; background-color: var(--background-light); border-radius: 3px; overflow: hidden;">
                                            <div style="height: 100%; background-color: {{ $module->progress >= 80 ? '#28a745' : ($module->progress >= 60 ? '#17a2b8' : ($module->progress >= 40 ? '#ffc107' : ($module->progress >= 20 ? '#fd7e14' : '#dc3545'))) }}; width: {{ $module->progress }}%;"></div>
                                        </div>
                                        <span style="font-weight: 600; color: {{ $module->progress >= 80 ? '#28a745' : ($module->progress >= 60 ? '#17a2b8' : ($module->progress >= 40 ? '#ffc107' : ($module->progress >= 20 ? '#fd7e14' : '#dc3545'))) }};">{{ $module->progress }}%</span>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <i class="fas fa-plus"></i>
                                        {{ $module->courses->count() }} nouveau(x) cours
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-warning">
                                        {{ Carbon\Carbon::parse($module->updated_at)->diffForHumans() }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endif
        
        <div class="row">
            {{-- Courses for Today List (using table-subtle-depth style) --}}
            <div class="col-lg-6 mb-4">
                <div class="card-base">
                    <h3 class="chart-title"><i class="fa-solid fa-calendar-check"></i> Mes Cours Aujourd'hui</h3>
                    @if($coursesToday->isEmpty())
                        <div class="empty-state">
                            <div class="icon"><i class="fa-solid fa-calendar-check"></i></div>
                            <p>Aucun cours prévu pour aujourd'hui.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-subtle-depth">
                                <thead>
                                    <tr>
                                         <th>Cours</th>
                                        <th>Formation</th>
                                        <th>Heure</th>
                                        <th>Consultant</th>
                                        <th>Lien Zoom</th>
                                    </tr>
                                </thead>
                                <tbody>
                                   @foreach($coursesToday as $course)
                                    <tr>
                                        <td><strong>{{ $course->title }}</strong></td>
                                     @if($course->formation)
    <td class="course-formation">
        {{-- عرض اسم Formation الواحدة --}}
        <span class="badge bg-secondary">
            {{ $course->formation->title }}
        </span>
        
        {{-- (اختياري): يمكنك إضافة اسم Module هنا إذا كنت بحاجة إليه في الجدول --}}
        @if($course->module)
            <span class="badge bg-info ms-1">{{ $course->module->title }}</span>
        @endif
    </td>
@else
    <td class="course-formation">
        -
    </td>
@endif
                                        <td>
                                            <span class="badge bg-secondary">{{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }}</span>
                                            -
                                            <span class="badge bg-secondary">{{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}</span>
                                        </td>
                                        <td>{{ $course->consultant->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($course->zoom_link)
                                                <a href="{{ $course->zoom_link }}" target="_blank" class="btn btn-sm btn-success">
                                                    <i class="fas fa-video"></i> Rejoindre
                                                </a>
                                            @else
                                                <span class="badge bg-secondary"><i class="fas fa-link-slash"></i> Indisponible</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
            
            {{-- Upcoming Courses List (using table-subtle-depth style) --}}
            <div class="col-lg-6 mb-4">
                <div class="card-base">
                    <h3 class="chart-title"><i class="fa-solid fa-calendar-alt"></i> Mes Prochains Cours</h3>
                    @if($upcomingCourses->isEmpty())
                        <div class="empty-state">
                            <div class="icon"><i class="fa-solid fa-calendar-plus"></i></div>
                            <p>Aucun cours à venir pour le moment.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-subtle-depth">
                                <thead>
                                    <tr>
                                        <th>Formation</th>
                                        <th>Date</th>
                                        <th>Heure</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingCourses as $course)
                                        <tr>
                                            <td><strong>{{ $course->formation->title }}</strong></td>
                                            <td><span class="badge bg-warning">{{ Carbon\Carbon::parse($course->course_date)->format('d/m/Y') }}</span></td>
                                            <td><span class="badge bg-info">{{ Carbon\Carbon::parse($course->start_time)->format('H:i') }}</span></td>
                                            <td>
                                                <a href="{{ route('courses.show', $course) }}" class="btn-action btn-view">
                                    <i class="fas fa-eye"></i>
                                    <span>Voir</span>
                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Enrollments by Formation Chart --}}
            <div class="col-lg-6 mb-4">
                <div class="card-base">
                    <h3 class="chart-title"><i class="fa-solid fa-chart-pie"></i> Étudiants par Formation</h3>
                    @if($enrollmentsByFormation->isEmpty())
                        <div class="empty-state">
                            <div class="icon"><i class="fa-solid fa-chart-bar"></i></div>
                            <p>Aucune donnée d'inscription pour le moment.</p>
                        </div>
                    @else
                        <div class="chart-container">
                            <canvas id="enrollmentsByFormationChart"></canvas>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Recent Course Reschedules List (using table-subtle-depth style) --}}
            <div class="col-lg-6 mb-4">
                <div class="card-base">
                    <h3 class="chart-title"><i class="fa-solid fa-sync-alt"></i> Reprogrammations Récentes</h3>
                    @if($recentReschedules->isEmpty())
                        <div class="empty-state">
                            <div class="icon"><i class="fa-solid fa-history"></i></div>
                            <p>Aucune reprogrammation récente.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            <table class="table table-subtle-depth">
                                <thead>
                                    <tr>
                                        <th>Cours</th>
                                        <th>Ancienne Date</th>
                                        <th>Nouvelle Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentReschedules as $reschedule)
                                        <tr>
                                            <td><strong>{{ $reschedule->course->title }}</strong></td>
                                            <td><span class="badge bg-danger">{{ Carbon\Carbon::parse($reschedule->old_date)->format('d/m/Y') }}</span></td>
                                            <td><span class="badge bg-success">{{ Carbon\Carbon::parse($reschedule->new_date)->format('d/m/Y') }}</span></td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Chart.js Global Configuration
        Chart.defaults.font.family = 'Inter, sans-serif';
        Chart.defaults.color = 'var(--text-medium)';
        Chart.defaults.borderColor = 'var(--border-light)';

        // New color palette based on the image provided
        const chartColors = [
            '#C2185B', // A deep pink/burgundy color
            '#D32F2F', // A vibrant red
            '#F48FB1', // A light, muted pink
            '#EF5350', // A lighter red/coral
        ];

        // Enrollments by Formation Chart
        const enrollmentsByFormationCtx = document.getElementById('enrollmentsByFormationChart');
        const enrollmentsData = @json($enrollmentsByFormation);

        if (enrollmentsByFormationCtx && enrollmentsData.length > 0) {
            // This is the corrected line. We now map 'item.formation.title'
            const labels = enrollmentsData.map(item => item.formation.title);
            const data = enrollmentsData.map(item => item.total_students);

            new Chart(enrollmentsByFormationCtx, {
                type: 'doughnut',
                data: {
                    labels: labels,
                    datasets: [{
                        label: 'Total Étudiants',
                        data: data,
                        // Use the new color palette
                        backgroundColor: chartColors,
                        borderColor: 'white',
                        borderWidth: 3,
                        hoverOffset: 10,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20,
                                font: { size: 13, weight: '600' },
                                color: 'var(--text-dark)'
                            }
                        },
                        tooltip: {
                            backgroundColor: "var(--text-dark)",
                            titleColor: "#fff",
                            bodyColor: "#fff",
                            borderColor: 'var(--border-light)',
                            borderWidth: 1,
                            cornerRadius: 8,
                            padding: 15,
                        },
                    },
                    cutout: '70%',
                }
            });
        }
    });


    const globalModulesCtx = document.getElementById('globalModulesProgressChart');
const globalModulesData = @json($globalModulesChart ?? []);

if (globalModulesCtx && globalModulesData.labels && globalModulesData.labels.length > 0) {
    new Chart(globalModulesCtx, {
        type: 'bar',
        data: {
            labels: globalModulesData.labels,
            datasets: [{
                label: 'Progression (%)',
                data: globalModulesData.data,
                backgroundColor: globalModulesData.backgroundColor,
                borderColor: globalModulesData.backgroundColor.map(color => color.replace('0.8', '1')),
                borderWidth: 2,
                borderRadius: 8,
                borderSkipped: false,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    backgroundColor: "var(--text-dark)",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    borderColor: 'var(--border-light)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 15,
                    callbacks: {
                        label: function(context) {
                            return `Progression: ${context.parsed.y}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    max: 100,
                    ticks: {
                        callback: function(value) {
                            return value + '%';
                        }
                    },
                    grid: {
                        color: 'var(--border-light)'
                    }
                },
                x: {
                    ticks: {
                        maxRotation: 45,
                        font: {
                            size: 11
                        }
                    },
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
}

// Module Statistics Chart (Doughnut)
const moduleStatsCtx = document.getElementById('moduleStatsChart');
const moduleStatsData = @json($moduleProgressStats ?? []);

if (moduleStatsCtx && moduleStatsData.completed) {
    new Chart(moduleStatsCtx, {
        type: 'doughnut',
        data: {
            labels: ['Terminés', 'En Cours', 'Non Commencés'],
            datasets: [{
                data: [
                    moduleStatsData.completed.count,
                    moduleStatsData.in_progress.count,
                    moduleStatsData.not_started.count
                ],
                backgroundColor: ['#28a745', '#17a2b8', '#dc3545'],
                borderColor: 'white',
                borderWidth: 3,
                hoverOffset: 8,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false // We'll show custom legend below
                },
                tooltip: {
                    backgroundColor: "var(--text-dark)",
                    titleColor: "#fff",
                    bodyColor: "#fff",
                    borderColor: 'var(--border-light)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    padding: 15,
                    callbacks: {
                        label: function(context) {
                            const label = context.label || '';
                            const value = context.parsed;
                            const total = context.dataset.data.reduce((a, b) => a + b, 0);
                            const percentage = ((value / total) * 100).toFixed(1);
                            return `${label}: ${value} modules (${percentage}%)`;
                        }
                    }
                }
            },
            cutout: '60%',
        }
    });
}
</script>
@endpush