@extends('layouts.app') {{-- Assurez-vous d'avoir un fichier de mise en page principal --}}

@section('title', 'Mon Tableau de Bord Étudiant')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    /* Global Styles & Variables */
    :root {
        /* Primary Brand Colors (Refined for a modern, inviting feel) */
        --primary-blue: #D32F2F; /* A vibrant, modern blue */
        --primary-dark-blue: #C2185B; /* A darker shade for depth */
        --primary-light-blue: #ffd6d65e; /* Very light blue for subtle accents/backgrounds */
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
        max-width: 1600px; /* Increased max-width for more spacious feel */
        margin: 0 auto;
        padding: 40px;
        flex-grow: 1;
    }

    @media (max-width: 1199px) {
        .dashboard-container {
            padding: 30px;
        }
    }

    @media (max-width: 992px) {
        .dashboard-container {
            padding: 25px;
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 20px;
        }
    }

    /* Card Base Style */
    .card-base {
        background: var(--card-background);
        border-radius: 16px; /* Slightly less rounded for modern look */
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
        padding: 45px 30px; /* More vertical padding */
        text-align: center;
        background: linear-gradient(135deg, var(--union-dark-blu) 0%, var(--union-light-ble) 100%); /* Blue gradient */
        color: white;
        position: relative;
        overflow: hidden;
        border-radius: 20px; /* Keep header card slightly more rounded */
    }

    .header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('https://www.transparenttextures.com/patterns/connected-dots.png') repeat; /* Changed texture */
        opacity: 0.1; /* Slightly more visible texture */
        pointer-events: none;
    }

    .header-card h1 {
        color: white;
        font-size: 3.5rem; /* Larger font size */
        font-weight: 800;
        margin-bottom: 15px;
        text-shadow: 0 5px 15px rgba(0,0,0,0.4); /* Stronger shadow */
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 15px;
    }

    .header-card h1 .fa-graduation-cap {
        font-size: 0.9em; /* Adjust icon size relative to text */
    }

    .header-subtitle {
        color: rgba(255, 255, 255, 0.9);
        font-size: 1.4rem;
        margin-bottom: 35px; /* More space below subtitle */
        font-weight: 400;
    }

    .date-filter {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .date-filter input, .date-filter select {
        background: rgba(255, 255, 255, 0.15); /* Lighter background for inputs */
        border: 1px solid rgba(255, 255, 255, 0.3);
        border-radius: 12px;
        padding: 12px 18px; /* Slightly reduced padding */
        color: white;
        font-size: 16px;
        outline: none;
        transition: border-color 0.3s ease, background-color 0.3s ease, box-shadow 0.3s ease;
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
        background: white; /* White button on gradient header */
        border: none;
        border-radius: 12px;
        padding: 12px 25px;
        color: var(--primary-blue); /* Text color from primary blue */
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
        grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); /* Slightly larger min-width for stats */
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
        height: 6px; /* Thicker top border */
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-dark-blue));
        border-radius: 16px 16px 0 0; /* Apply border-radius to the top border */
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
        width: 60px; /* Larger icons */
        height: 60px;
        border-radius: 18px; /* Slightly less rounded */
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 26px; /* Larger font size for icon */
        color: white;
        box-shadow: 0 6px 15px rgba(0, 0, 0, 0.15);
        background: linear-gradient(45deg, var(--primary-blue), var(--primary-dark-blue));
    }
    /* Specific stat icon colors */
    .stat-card:nth-child(2) .stat-icon { background: linear-gradient(45deg, var(--accent-orange), #E67E22); }
    .stat-card:nth-child(3) .stat-icon { background: linear-gradient(45deg, var(--accent-green), #2ECC71); }
    .stat-card:nth-child(4) .stat-icon { background: linear-gradient(45deg, var(--accent-red), #C0392B); }


    .stat-info {
        text-align: right;
    }

    .stat-number {
        font-size: 3rem; /* Larger number */
        font-weight: 800;
        background: linear-gradient(45deg, var(--primary-blue), var(--primary-dark-blue)); /* Consistent gradient */
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        color: var(--primary-blue); /* Fallback */
        margin-bottom: 5px;
        min-width: 90px; /* Adjusted min-width */
        white-space: nowrap;
    }
    /* Specific stat number colors */
    .stat-card:nth-child(2) .stat-number { background: linear-gradient(45deg, var(--accent-orange), #E67E22); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .stat-card:nth-child(3) .stat-number { background: linear-gradient(45deg, var(--accent-green), #2ECC71); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
    .stat-card:nth-child(4) .stat-number { background: linear-gradient(45deg, var(--accent-red), #C0392B); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }


    .stat-label {
        color: var(--text-medium);
        font-size: 15px; /* Slightly larger font size */
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px; /* Increased letter spacing */
    }

    /* General Table Reset for New Styles */
    .list-card .table {
        margin-top: 0;
        width: 100%;
        border-collapse: collapse; /* Ensure consistent collapsing */
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

    /* --- NEW TABLE STYLES --- */

    /* 1. Tableau Style "Glassmorphism / Frosted Glass" */
    .table-glass {
        border-radius: 16px;
        background: rgba(255, 255, 255, 0.15); /* Semi-transparent background */
        backdrop-filter: blur(10px); /* Frosted glass effect */
        border: 1px solid rgba(255, 255, 255, 0.3); /* Lighter border */
        box-shadow: var(--shadow-sm); /* Subtle shadow */
        overflow: hidden; /* Ensures blur and radius apply correctly */
    }
    .table-glass thead th {
        background: #f1a4a460; /* Light blue transparent header */
        color: var(--primary-dark-blue);
        font-weight: 700;
        padding: 18px 25px;
        text-transform: uppercase;
        letter-spacing: 1px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.4); /* Frosted separator */
    }
    .table-glass tbody tr {
        background: rgba(255, 255, 255, 0.05); /* Even lighter transparent rows */
        transition: background-color 0.3s ease;
    }
    .table-glass tbody tr:hover {
        background: rgba(255, 255, 255, 0.15); /* More transparent on hover */
        box-shadow: none; /* No extra shadow on rows */
        transform: none; /* No transform */
    }
    .table-glass tbody td {
        padding: 15px 25px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.2); /* Soft separator */
        color: var(--text-dark); /* Keep text readable */
    }
    .table-glass tbody tr:last-child td {
        border-bottom: none;
    }
    .table-glass .progress {
        height: 8px; /* Very thin progress bar */
        background-color: rgba(255, 255, 255, 0.3);
        border-radius: 4px;
        box-shadow: none;
    }
    .table-glass .progress-bar {
        font-size: 0.7em;
        height: 100%;
        border-radius: 4px;
        background: var(--primary-blue); /* Solid blue for progress */
    }


    /* 2. Tableau Style "Minimalist Dark Header" */
    .table-dark-header {
        border-radius: 16px;
        overflow: hidden;
        border: 1px solid var(--border-light);
        box-shadow: var(--shadow-sm);
    }
    .table-dark-header thead th {
        background-color: var(--primary-dark-blue); /* Dark header background */
        color: white; /* White text on dark header */
        font-weight: 600;
        padding: 20px 25px;
        text-transform: uppercase;
        letter-spacing: 0.8px;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1); /* Subtle white border for separation */
    }
    .table-dark-header tbody tr {
        background-color: var(--card-background); /* White rows */
        border-bottom: 1px solid var(--border-light); /* Light separator */
    }
    .table-dark-header tbody tr:last-child {
        border-bottom: none;
    }
    .table-dark-header tbody tr:hover {
        background-color: var(--background-light); /* Very subtle light grey on hover */
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
    .table-dark-header .progress {
        height: 10px;
        background-color: var(--border-light);
        border-radius: 5px;
        box-shadow: none;
    }
    .table-dark-header .progress-bar {
        font-size: 0.7em;
        background: var(--primary-blue);
        border-radius: 5px;
    }

    /* 3. Tableau Style "Clean Lines with Subtle Depth" */
    .table-subtle-depth {
        border-collapse: separate; /* Allows for outer border-radius and spacing */
        border-spacing: 0; /* Remove default spacing */
        border: 1px solid var(--border-light); /* Main border */
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.05); /* Soft overall shadow */
    }
    .table-subtle-depth thead th {
        background-color: var(--background-light); /* Lighter header background */
        color: var(--text-dark); /* Darker text for headers */
        font-weight: 700;
        padding: 15px 20px;
        border-bottom: 2px solid var(--border-light); /* A more prominent header separator */
        text-transform: uppercase;
        letter-spacing: 0.7px;
    }
    .table-subtle-depth thead th:first-child { border-top-left-radius: 12px; }
    .table-subtle-depth thead th:last-child { border-top-right-radius: 12px; }

    .table-subtle-depth tbody tr {
        background-color: var(--card-background);
        border-bottom: 1px solid var(--border-light); /* Gentle row separator */
    }
    .table-subtle-depth tbody tr:last-child {
        border-bottom: none;
    }
    .table-subtle-depth tbody tr:hover {
        background-color: rgba(74, 118, 250, 0.03); /* Extremely subtle blue tint on hover */
        transform: translateY(-2px); /* Slight lift */
        box-shadow: 0 2px 10px rgba(0,0,0,0.08); /* A bit more pronounced shadow on hover */
    }
    .table-subtle-depth tbody td {
        padding: 14px 20px;
        border: none; /* No internal vertical borders */
    }
    .table-subtle-depth .progress {
        height: 16px; /* A bit more visible progress bar */
        background-color: var(--border-light);
        border-radius: 8px;
        box-shadow: inset 0 1px 2px rgba(0,0,0,0.08);
    }
    .table-subtle-depth .progress-bar {
        font-size: 0.8rem;
        border-radius: 8px;
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-dark-blue));
    }


    /* Badges */
    .badge {
        padding: 7px 14px; /* Slightly larger padding */
        border-radius: 25px; /* More rounded, pill shape */
        font-size: 0.8rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.6px;
        display: inline-flex;
        align-items: center;
        gap: 6px;
        min-width: 90px; /* Ensure consistent width for badges */
        justify-content: center;
        transition: all 0.2s ease;
    }

    /* Custom badge colors */
    .badge.bg-success { background-color: var(--accent-green) !important; color: white !important; }
    .badge.bg-warning { background-color: var(--accent-orange) !important; color: white !important; }
    .badge.bg-danger { background-color: var(--accent-red) !important; color: white !important; }
    .badge.bg-info { background-color: var(--primary-blue) !important; color: white !important; } /* Uses primary blue for info */
    .badge.bg-secondary { background-color: var(--text-light) !important; color: white !important; }

    /* Progress Bar (General, overridden by table-specific styles) */
    .progress {
        height: 20px;
        background-color: var(--primary-light-blue);
        border-radius: 10px;
        overflow: hidden;
        min-width: 120px;
        box-shadow: inset 0 1px 3px rgba(0,0,0,0.1);
    }

    .progress-bar {
        height: 100%;
        color: white;
        font-size: 0.85rem;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: width 0.4s ease;
        background: linear-gradient(90deg, var(--primary-blue), var(--primary-dark-blue));
    }
    .progress-bar.bg-success {
        background: var(--accent-green) !important;
    }

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
        border: 2px dashed rgba(74, 118, 250, 0.3);
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
         border: #C2185B !important;
    }

    /* Smaller empty state for payment sub-sections */
    .empty-state.empty-state-small {
        padding: 25px;
        font-size: 0.95rem;
        border-radius: 12px;
        margin-top: 15px;
        background: rgba(214, 224, 255, 0.5);
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
        box-shadow: 0 5px 15px rgba(74, 118, 250, 0.25);
    }
    .btn-primary:hover {
        background: linear-gradient(135deg, var(--primary-dark-blue), var(--primary-blue));
        box-shadow: 0 8px 20px rgba(74, 118, 250, 0.4);
        transform: translateY(-2px);
    }

    .btn-outline-primary {
       border: #C2185B !important;
        color: #fff;
         background: linear-gradient(90deg, var(--primary-blue), var(--primary-dark-blue));
          border-radius: 12px;
    }
    .btn-outline-primary:hover {
        background-color: var(--primary-blue);
        color: white;
        box-shadow: 0 3px 10px rgba(74, 118, 250, 0.2);
    }

    /* Specific Button Styles (consistent with badge colors) */
    .btn-info {
        background-color: var(--primary-blue);
        border-color: var(--primary-blue);
        color: white;
    }
    .btn-info:hover {
        background-color: var(--primary-dark-blue);
        border-color: var(--primary-dark-blue);
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
        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    @media (max-width: 1199px) {
        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
        }
    }

    @media (max-width: 992px) {
        .header-card h1 { font-size: 2.8rem; }
        .header-subtitle { font-size: 1.2rem; margin-bottom: 25px; }
        .stats-grid { grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); }
        .row > .col-lg-8,
        .row > .col-lg-4,
        .row > .col-lg-6 {
            flex: 1 1 100%;
            max-width: 100%;
        }
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
        .list-card tbody td {
            display: block;
            width: 100%;
            text-align: left;
            padding: 8px 5px;
        }
        .list-card tbody td:last-child {
            text-align: left;
        }
        .list-card tbody tr:hover { padding-left: 5px; }
        .btn { padding: 8px 15px; font-size: 0.9rem; }
        .empty-state.empty-state-small { padding: 15px; }
    }

    /* Define RGB values for primary blue for rgba usage in glassmorphism */
    :root {
        --primary-blue-rgb: 74, 118, 250; /* RGB values for #f069699a */
    }
    a.btn.btn-outline-primary {
    border-radius: 20px;
}
</style>
@endpush

@section('content')
<div class="dashboard-body">
    <div class="dashboard-container">
        <div class="card-base header-card">
            <h1><i class="fa-solid fa-graduation-cap"></i> Mon Tableau de Bord Étudiant</h1>
            <p class="header-subtitle">Bienvenue ! Gérez vos cours, paiements et progrès en toute simplicité.</p>

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

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                    <div class="stat-info">
                        <div class="stat-number">{{ $activeInscriptions->count() }}</div>
                        <div class="stat-label">Inscriptions Actives</div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon"><i class="fas fa-hourglass-half"></i></div>
                    <div class="stat-info">
                        <div class="stat-number">{{ $pendingInscriptions->count() }}</div>
                        <div class="stat-label">Inscriptions en Attente</div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon"><i class="fas fa-money-bill-wave"></i></div>
                    <div class="stat-info">
                        <div class="stat-number">{{ number_format($totalPaid, 2) }}</div>
                        <div class="stat-label">Montant Payé (MAD)</div>
                    </div>
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-content">
                    <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
                    <div class="stat-info">
                        <div class="stat-number">{{ number_format($totalOutstanding, 2) }}</div>
                        <div class="stat-label">Montant Dû (MAD)</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card-base">
                    <h3 class="chart-title"><i class="fa-solid fa-calendar-day"></i> Mes Cours Aujourd'hui ({{ \Carbon\Carbon::today()->format('d/m/Y') }})</h3>
                    @if($coursesToday->isEmpty())
                        <div class="empty-state">
                            <div class="icon"><i class="fa-solid fa-calendar-check"></i></div>
                            <p>Vous n'avez aucun cours prévu pour aujourd'hui.</p>
                            <a href="{{ route('courses.index', ['start_date' => \Carbon\Carbon::today()->toDateString()]) }}" class="btn btn-outline-primary">Voir tous mes cours</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            {{-- Applying the "Minimalist Dark Header" style --}}
                            <table class="table table-dark-header">
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
                                       @if($course->formations->isNotEmpty())
                                                            <td class="course-formation">
                                                                @foreach($course->formations as $formation)
                                                                    <span class="badge bg-secondary">{{ $formation->title }}</span>
                                                                @endforeach
                                                            </td>
                                                        @else
                                                            -
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
                        <div class="text-center mt-3">
                            <a href="{{ route('courses.index') }}" class="btn btn-outline-primary">Voir tous mes cours</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card-base chart-card">
                    <h3 class="chart-title"><i class="fa-solid fa-chart-pie"></i> Statut des Paiements</h3>
                    @if(array_sum($paymentChartData) > 0)
                        <div class="chart-container">
                            <canvas id="paymentStatusChart"></canvas>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="icon"><i class="fa-solid fa-coins"></i></div>
                            <p>Aucune donnée de paiement disponible pour le graphique.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-8 mb-4">
                <div class="card-base">
                    <h3 class="chart-title"><i class="fa-solid fa-calendar-refresh"></i> Dernières Reprogrammations de mes Cours</h3>
                    @if($recentCourseReschedules->isEmpty())
                        <div class="empty-state">
                            <div class="icon"><i class="fa-solid fa-check-circle"></i></div>
                            <p>Aucune reprogrammation de cours récente vous concernant.</p>
                            <a href="{{ route('course_reschedules.index') }}" class="btn btn-outline-primary">Voir toutes les reprogrammations</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            {{-- Applying the "Clean Lines with Subtle Depth" style --}}
                            <table class="table table-subtle-depth">
                                <thead>
                                    <tr>
                                        <th>Cours</th>
                                        <th>Date Originale</th>
                                        <th>Nouvelle Date</th>
                                        <th>Raison</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentCourseReschedules as $reschedule)
                                    <tr>
                                        <td><strong>{{ $reschedule->course->title ?? 'N/A' }}</strong></td>
                                        <td><span class="badge bg-danger">{{ \Carbon\Carbon::parse($reschedule->original_date)->format('d/m/Y') }}</span></td>
                                        <td><span class="badge bg-success">{{ \Carbon\Carbon::parse($reschedule->new_date)->format('d/m/Y') }}</span></td>
                                        <td>{{ Str::limit($reschedule->reason, 40) }}</td>
                                        <td>
                                            <a href="{{ route('course_reschedules.show', $reschedule->id) }}" class="btn btn-info btn-sm" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('course_reschedules.index') }}" class="btn btn-outline-primary">Voir toutes les reprogrammations</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-4 mb-4">
                <div class="card-base chart-card">
                    <h3 class="chart-title"><i class="fa-solid fa-chart-line"></i> Statut des Inscriptions</h3>
                    @if(array_sum($inscriptionChartData) > 0)
                        <div class="chart-container">
                            <canvas id="inscriptionStatusChart"></canvas>
                        </div>
                    @else
                        <div class="empty-state">
                            <div class="icon"><i class="fa-solid fa-address-card"></i></div>
                            <p>Aucune donnée d'inscription disponible pour le graphique.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-6 mb-4">
                <div class="card-base">
                    <h3 class="chart-title"><i class="fa-solid fa-file-invoice"></i> Mes Inscriptions</h3>
                    @if($inscriptions->isEmpty())
                        <div class="empty-state">
                            <div class="icon"><i class="fa-solid fa-file-circle-plus"></i></div>
                            <p>Vous n'avez aucune inscription pour le moment.</p>
                            <a href="{{ route('formations.index') }}" class="btn btn-primary">Découvrir nos formations</a>
                        </div>
                    @else
                        <div class="table-responsive">
                            {{-- Applying the "Table Glass" style for Mes Inscriptions --}}
                            <table class="table table-glass">
                                <thead>
                                    <tr>
                                        <th>Formation</th>
                                        <th>Statut</th>
                                        <th>Payé</th>
                                        <th>Dû</th>                                        
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($inscriptions as $inscription)
                                    <tr>
                                        <td><strong>{{ $inscription->formation->title ?? 'N/A' }}</strong></td>
                                        <td>
                                            <span class="badge {{
                                                $inscription->status == 'active' ? 'bg-success' :
                                                ($inscription->status == 'pending' ? 'bg-warning' :
                                                ($inscription->status == 'completed' ? 'bg-info' : 'bg-secondary'))
                                            }}">
                                                {{ ucfirst($inscription->status) }}
                                            </span>
                                        </td>
                                        <td>{{ number_format($inscription->paid_amount, 2) }} MAD</td>
                                        <td>{{ number_format($inscription->total_amount - $inscription->paid_amount, 2) }} MAD</td>
                                        
                                        <td>
                                            <a href="{{ route('inscriptions.show', $inscription->id) }}" class="btn btn-info btn-sm" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="text-center mt-3">
                            <a href="{{ route('inscriptions.index') }}" class="btn btn-outline-primary">Voir toutes mes inscriptions</a>
                        </div>
                    @endif
                </div>
            </div>

            <div class="col-lg-6 mb-4">
                <div class="card-base">
                    <h3 class="chart-title"><i class="fa-solid fa-money-check-dollar"></i> Mes Paiements</h3>
                    <h5 class="mb-3" style="color: var(--text-medium); font-weight: 600;">Prochains paiements</h5>
                    @if($upcomingPayments->isEmpty())
                        <div class="empty-state empty-state-small">
                            <div class="icon"><i class="fa-solid fa-calendar-check"></i></div>
                            <p>Aucun paiement à venir.</p>
                        </div>
                    @else
                        <div class="table-responsive mb-4">
                            {{-- Applying the "Table Glass" style for Upcoming Payments --}}
                            <table class="table table-glass">
                                <thead>
                                    <tr>
                                        <th>Formation</th>
                                        <th>Montant</th>
                                        <th>Échéance</th>
                                        <th>Statut</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($upcomingPayments as $payment)
                                    <tr>
                                        <td><strong>{{ $payment->inscription->formation->title ?? 'N/A' }}</strong></td>
                                        <td>{{ number_format($payment->amount, 2) }} MAD</td>
                                        <td><span class="badge bg-warning">{{ \Carbon\Carbon::parse($payment->due_date)->format('d/m/Y') }}</span></td>
                                        <td><span class="badge bg-warning">{{ ucfirst($payment->status) }}</span></td>
                                        <td>
                                            <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-info btn-sm" title="Voir le paiement">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    <h5 class="mt-4 mb-3" style="color: var(--text-medium); font-weight: 600;">Paiements Récents</h5>
                    @if($recentPayments->isEmpty())
                        <div class="empty-state empty-state-small">
                            <div class="icon"><i class="fa-solid fa-receipt"></i></div>
                            <p>Aucun paiement récent.</p>
                        </div>
                    @else
                        <div class="table-responsive">
                            {{-- Applying the "Table Glass" style for Recent Payments --}}
                            <table class="table table-glass">
                                <thead>
                                    <tr>
                                        <th>Formation</th>
                                        <th>Montant</th>
                                        <th>Statut</th>
                                        <th>Date</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentPayments as $payment)
                                    <tr>
                                        <td><strong>{{ $payment->inscription->formation->title ?? 'N/A' }}</strong></td>
                                        <td>{{ number_format($payment->amount, 2) }} MAD</td>
                                        <td>
                                            <span class="badge {{
                                                $payment->status == 'paid' ? 'bg-success' :
                                                ($payment->status == 'pending' ? 'bg-warning' : 'bg-danger')
                                            }}">
                                                {{ ucfirst($payment->status) }}
                                            </span>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($payment->paid_date ?? $payment->created_at)->format('d/m/Y') }}</td>
                                        <td>
                                            <a href="{{ route('payments.show', $payment->id) }}" class="btn btn-info btn-sm" title="Voir les détails">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    <div class="text-center mt-3">
                        <a href="{{ route('payments.index') }}" class="btn btn-outline-primary">Voir tous mes paiements</a>
                    </div>
                </div>
            </div>
        </div>

        

           
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
{{-- Ensure you replace 'your-font-awesome-kit.js' with your actual Font Awesome kit URL if you're using a kit. --}}
{{-- For demonstration, I'm using the CDN link from your admin dashboard style for consistency. --}}
<script src="https://kit.fontawesome.com/your-font-awesome-kit.js" crossorigin="anonymous"></script>

<script>
    // Chart.js Global Configuration
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.color = 'var(--text-medium)'; /* Use CSS variable for consistency */
    Chart.defaults.borderColor = 'var(--border-light)';

    // Helper function for creating gradients in charts
    function createGradient(ctx, chartArea, colorStops) {
        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
        colorStops.forEach(stop => gradient.addColorStop(stop.offset, stop.color));
        return gradient;
    }

    // Chart for Payment Status
    const paymentStatusCtx = document.getElementById('paymentStatusChart');
    if (paymentStatusCtx) {
        new Chart(paymentStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($paymentChartLabels),
                datasets: [{
                    data: @json($paymentChartData),
                    // Restored original colors for Payment Status Chart
                    backgroundColor: [
                        '#10b981', '#f59e0b', '#D32F2F', '#718096' 
                    ],
                    borderColor: 'white',
                    borderWidth: 3,
                    hoverBorderWidth: 5,
                    hoverOffset: 10,
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    tooltip: {
                        backgroundColor: "var(--text-dark)", /* Darker tooltip */
                        titleColor: "#fff",
                        bodyColor: "#fff",
                        borderColor: 'var(--primary-light-blue)',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: true,
                        cornerRadius: 8,
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed) {
                                    label += new Intl.NumberFormat('fr-MA', { style: 'currency', currency: 'MAD' }).format(context.parsed);
                                }
                                return label;
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 25,
                            usePointStyle: true,
                            font: { size: 13, weight: '600' },
                            color: 'var(--text-dark)'
                        }
                    },
                },
                cutout: '70%',
            },
        });
    }

    // Chart for Inscription Status
    const inscriptionStatusCtx = document.getElementById('inscriptionStatusChart');
    if (inscriptionStatusCtx) {
        new Chart(inscriptionStatusCtx, {
            type: 'doughnut',
            data: {
                labels: @json($inscriptionChartLabels),
                datasets: [{
                    data: @json($inscriptionChartData),
                    // Restored original colors for Inscription Status Chart
                    backgroundColor: [
                        '#274A78', '#f59e0b', '#10b981', '#718096'
                    ],
                    borderColor: 'white',
                    borderWidth: 3,
                    hoverBorderWidth: 5,
                    hoverOffset: 10,
                }],
            },
            options: {
                maintainAspectRatio: false,
                responsive: true,
                plugins: {
                    tooltip: {
                        backgroundColor: "var(--text-dark)", /* Darker tooltip */
                        titleColor: "#fff",
                        bodyColor: "#fff",
                        borderColor: 'var(--primary-light-blue)',
                        borderWidth: 1,
                        xPadding: 15,
                        yPadding: 15,
                        displayColors: true,
                        cornerRadius: 8,
                        caretPadding: 10,
                        callbacks: {
                            label: function(context) {
                                let label = context.label || '';
                                if (label) {
                                    label += ': ';
                                }
                                if (context.parsed !== null) {
                                    label += context.parsed;
                                }
                                return label + ' inscriptions';
                            }
                        }
                    },
                    legend: {
                        display: true,
                        position: 'bottom',
                        labels: {
                            padding: 25,
                            usePointStyle: true,
                            font: { size: 13, weight: '600' },
                            color: 'var(--text-dark)'
                        }
                    },
                },
                cutout: '70%',
            },
        });
    }
</script>
@endpush