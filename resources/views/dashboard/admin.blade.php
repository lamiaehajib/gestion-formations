@extends('layouts.app')

@section('title', 'Admin Dashboard')

@push('styles')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" integrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<style>
    /* Global Styles & Variables */
    :root {
        /* Brand Colors (kept as they are for accents) */
        --union-red-pink: #D32F2F; /* Closer to the logo's main red/pink */
        --union-dark-blue: #1A237E; /* From the header background - original, kept for contrast */
        --union-dark-blu: #D32F2F; /* This is #D32F2F */
        --union-light-blue: #274A78; /* A lighter shade for accents - original, kept for contrast */
        --union-accent-light: #FFCDD2; /* Very light pink/red for subtle accents */
        --union-light-ble: #C2185B; /* This is #C2185B */

        /* Dashboard Specific Colors - ADJUSTED FOR LIGHT BACKGROUND */
        --dashboard-bg: #F0F2F5; /* New very light blue/off-white background */
        --card-bg-light: white; /* Solid white for cards to stand out */
        --card-bg-transparent: rgba(255, 255, 255, 0.8); /* Slightly transparent white for glass effect, but mostly solid */
        --border-color: rgba(220, 220, 220, 0.8); /* Light gray border for cards */
        --text-color-primary: #1A202C; /* Dark text for main content */
        --text-color-secondary: #4A5568; /* Slightly lighter dark text */
        --text-color-muted: #718096; /* Muted dark text */
        --box-shadow-light: 0 4px 15px rgba(0, 0, 0, 0.08); /* Lighter shadow */
        --box-shadow-hover: 0 8px 25px rgba(0, 0, 0, 0.12); /* Slightly stronger hover shadow */

        /* NEW: Colors for content - from header card gradient */
        --content-color-start: #D32F2F;
        --content-color-end: #C2185B;
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
        background-color: var(--dashboard-bg); /* New solid light background */
        min-height: 100vh;
        display: flex;
        flex-direction: column;
        color: var(--text-color-primary); /* Default text color now dark */
    }

   .dashboard-container {
    width: 100%;
    max-width: none; /* This is the key change: removes the max-width constraint */
    margin: 0 auto; /* Keeps it centered if max-width was used, but still good practice */
    padding: 40px 80px; /* Generous padding on left/right for large screens */
    flex-grow: 1;
}

/* Responsive adjustments for padding */
@media (max-width: 1199px) {
    .dashboard-container {
        padding: 30px 60px; /* Reduced padding for medium screens */
    }
}

@media (max-width: 992px) {
    .dashboard-container {
        padding: 25px 30px; /* Further reduced padding for tablets */
    }
}

@media (max-width: 768px) {
    .dashboard-container {
        padding: 20px; /* Smallest padding for mobile */
    }
}

    /* Card Base Style */
    .card-base {
        background: var(--card-bg-light); /* Solid white background for all cards */
        border-radius: 20px;
        padding: 30px;
        margin-bottom: 30px;
        border: 1px solid var(--border-color); /* Lighter border */
        box-shadow: var(--box-shadow-light); /* Lighter shadow */
        transition: all 0.3s ease;
    }

    .card-base:hover {
        transform: translateY(-5px);
        box-shadow: var(--box-shadow-hover); /* Slightly stronger hover shadow */
    }

    /* Header Card */
    .header-card {
        padding: 40px;
        text-align: center;
        background: linear-gradient(135deg, var(--union-dark-blu), var(--union-light-ble)); /* Keep dark blue gradient for header card background */
        color: white; /* Text in header card remains white */
        position: relative;
        overflow: hidden;
    }

    .header-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: url('https://www.transparenttextures.com/patterns/cubes.png') repeat; /* Subtle texture */
        opacity: 0.05;
        pointer-events: none;
    }

    .header-card h1 {
        color: white; /* Explicitly white */
        font-size: 3.2rem; /* Slightly larger */
        font-weight: 800;
        margin-bottom: 15px;
        text-shadow: 0 4px 10px rgba(0,0,0,0.5); /* Stronger shadow for heading */
    }

    .header-subtitle {
        color: rgba(255, 255, 255, 0.95); /* Slightly less muted white for readability */
        font-size: 1.35rem; /* Slightly larger */
        margin-bottom: 30px;
        font-weight: 400;
    }

    .date-filter {
        display: flex;
        gap: 15px;
        justify-content: center;
        flex-wrap: wrap;
    }

    .date-filter input, .date-filter select {
        background: rgba(255, 255, 255, 0.2); /* Still transparent white for inputs */
        border: 1px solid rgba(255, 255, 255, 0.4); /* Stronger border */
        border-radius: 12px;
        padding: 14px 20px;
        color: white; /* Text in inputs remains white */
        font-size: 16px;
        outline: none;
        transition: border-color 0.3s ease, background-color 0.3s ease;
        -webkit-appearance: none; /* For custom select arrow */
        -moz-appearance: none;
        appearance: none;
    }

    .date-filter select {
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='%23ffffff' width='18px' height='18px'%3E%3Cpath d='M7 10l5 5 5-5z'/%3E%3Cpath d='M0 0h24v24H0z' fill='none'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 15px center;
        padding-right: 40px; /* Space for arrow */
    }

    .date-filter input:focus, .date-filter select:focus {
        border-color: var(--union-accent-light); /* Light red/pink border on focus */
        background-color: rgba(255, 255, 255, 0.3);
    }

    .date-filter input::placeholder {
        color: rgba(255, 255, 255, 0.8); /* Brighter placeholder text */
    }

    .filter-btn {
        background: linear-gradient(135deg, var(--union-red-pink), #FF6F6F); /* Change filter button to red/pink gradient */
        border: none;
        border-radius: 12px;
        padding: 14px 30px;
        color: white;
        cursor: pointer;
        font-weight: 700;
        transition: all 0.3s ease;
        box-shadow: 0 8px 20px rgba(211, 47, 47, 0.3); /* Shadow adjusted for red */
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .filter-btn:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 35px rgba(211, 47, 47, 0.5);
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

    /* Stats Grid */
    .stats-grid {
        display: grid;
        /* Revert to auto-fit with a slightly adjusted minmax for better balance */
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Adjusted min-width for flexibility */
        gap: 25px;
        margin-bottom: 30px;
    }

    .stat-card {
        background: var(--card-bg-light); /* Solid white */
        border-radius: 20px;
        padding: 30px;
        box-shadow: var(--box-shadow-light);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
        border: 1px solid var(--border-color); /* Light border */
        color: var(--text-color-primary); /* Dark text */
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, var(--content-color-start), var(--content-color-end)); /* Red/Pink gradient for top border */
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: var(--box-shadow-hover);
    }

    .stat-icon {
        width: 65px;
        height: 65px;
        border-radius: 18px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        margin-bottom: 20px;
        color: white; /* Still white icons on colored backgrounds */
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
        /* Applying the gradient to the icon background */
        background: linear-gradient(45deg, var(--content-color-start), var(--content-color-end));
    }

    .stat-number {
        font-size: 3.2rem; /* Slightly larger */
        font-weight: 800;
        /* Apply a gradient to the text for numbers */
        background: linear-gradient(45deg, var(--content-color-start), var(--content-color-end));
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        color: var(--content-color-start); /* Fallback for browsers that don't support text-fill-color */
        margin-bottom: 8px;
        /* Add min-width to prevent severe squeezing of the numbers */
        min-width: 100px; /* Adjust as needed */
        white-space: nowrap; /* Prevent number from breaking line */
    }

    .stat-label {
        color: var(--text-color-secondary); /* Darker gray */
        font-size: 16px; /* Slightly larger */
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.8px; /* Slightly tighter */
    }

    .stat-detail {
        margin-top: 15px; /* More space */
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px; /* More space for icon */
    }
    .stat-detail.positive { color: #10b981; } /* Keep green for positive */
    .stat-detail.info { color: #274A78; } /* Using union-light-blue for info */
    .stat-detail.warning { color: #f59e0b; } /* Keep orange for warning */
    .stat-detail.danger { color: #D32F2F; } /* Using union-red-pink for danger */
    .stat-detail i {
        font-size: 16px;
    }


    /* Quick Actions */
    .quick-actions-container {
        margin-bottom: 30px;
    }

    .quick-actions-wrapper {
        background: var(--card-bg-light); /* Solid white for quick actions */
        border-radius: 20px;
        padding: 25px;
        display: flex;
        gap: 20px; /* More space between buttons */
        flex-wrap: wrap;
        box-shadow: var(--box-shadow-light);
        align-items: center;
        border: 1px solid var(--border-color);
    }

    .quick-actions-label {
        color: var(--text-color-primary); /* Dark text */
        font-weight: 700;
        margin-right: 10px;
        font-size: 1.2rem; /* Slightly larger */
    }

    .quick-action-btn {
        padding: 12px 22px; /* Slightly more padding */
        border-radius: 30px; /* More rounded */
        text-decoration: none;
        font-size: 15px; /* Slightly larger */
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        display: flex;
        align-items: center;
        gap: 8px;
        color: white; /* White text on buttons */
        /* Apply a gradient to the button background */
        background: linear-gradient(135deg, var(--content-color-start), var(--content-color-end));
    }

    /* Quick Action Buttons - using brand colors */
    .quick-action-btn:nth-of-type(1) { background: linear-gradient(135deg, var(--union-dark-blue), var(--union-light-blue)); } /* Original blue gradient */
    .quick-action-btn:nth-of-type(2) { background: linear-gradient(135deg, var(--content-color-start), var(--content-color-end)); } /* Red/Pink gradient */
    .quick-action-btn:nth-of-type(3) { background: linear-gradient(135deg, #f59e0b, #d97706); } /* Original orange gradient */

    .quick-action-btn:hover {
        transform: translateY(-3px); /* Slightly more pronounced hover */
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2); /* Stronger shadow on hover */
    }

    /* Chart Grid & Card */
    .charts-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); /* Adjusted minmax for charts */
        gap: 25px;
        margin-bottom: 30px;
    }

    .chart-card {
        background: var(--card-bg-light); /* Solid white */
        border-radius: 20px;
        padding: 30px;
        box-shadow: var(--box-shadow-light);
        border: 1px solid var(--border-color);
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .chart-title {
        font-size: 1.5rem; /* Slightly larger */
        font-weight: 700;
        color: var(--text-color-primary); /* Dark text */
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 10px;
    }

    .chart-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px; /* Slightly wider underline */
        height: 4px;
        background: linear-gradient(90deg, var(--content-color-start), var(--content-color-end)); /* Red/Pink gradient for title underline */
        border-radius: 2px;
    }

    /* Crucial: Explicitly size the canvas within its parent */
    .chart-card canvas {
        width: 100% !important;
        height: 320px !important; /* Slightly taller charts */
        max-height: 320px;
        min-height: 200px;
    }

    /* Lists Grid & Card */
    .lists-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        gap: 25px;
    }

    .list-card {
        background: var(--card-bg-light); /* Solid white */
        border-radius: 20px;
        padding: 30px;
        box-shadow: var(--box-shadow-light);
        border: 1px solid var(--border-color);
    }

    .list-title {
        font-size: 1.5rem; /* Slightly larger */
        font-weight: 700;
        color: var(--text-color-primary); /* Dark text */
        margin-bottom: 30px;
        position: relative;
        padding-bottom: 10px;
    }

    .list-title::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px; /* Slightly wider underline */
        height: 4px;
        background: linear-gradient(90deg, var(--content-color-start), var(--content-color-end)); /* Red/Pink gradient for title underline */
        border-radius: 2px;
    }

    .list-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 10px; /* More padding */
        border-bottom: 1px solid rgba(0, 0, 0, 0.08); /* Lighter border for light background */
        transition: all 0.3s ease;
        color: var(--text-color-secondary); /* Darker gray for general list item text */
        font-size: 15px;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .list-item:hover {
        background: rgba(0, 0, 0, 0.04); /* Slightly more visible hover background */
        padding-left: 20px; /* More pronounced slide effect */
        border-radius: 12px;
    }

    .list-item strong {
        color: var(--text-color-primary); /* Dark text */
        font-size: 16px;
    }

    .list-item div[style*="color: #6b7280"] { /* Targeting the inline style for muted text */
        color: var(--text-color-muted); /* Muted dark text */
    }

    .status-badge {
        padding: 8px 16px; /* More padding */
        border-radius: 25px;
        font-size: 13px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.8px; /* Slightly tighter */
        min-width: 90px; /* Ensures consistent width for badges */
        text-align: center;
        color: white; /* White text on badges */
    }

    /* Status Badges - adjust backgrounds to the desired colors */
    .status-active { background: var(--content-color-end); } /* Use a solid color from the gradient */
    .status-pending { background: #f59e0b; } /* Keep original orange for distinction */
    .status-completed { background: var(--union-dark-blue); } /* Keep original blue for distinction */
    .status-overdue { background: var(--content-color-start); } /* Use a solid color from the gradient */
    .status-ouverte { background: var(--content-color-start); } /* Use a solid color from the gradient */
    .status-fermee { background: var(--union-dark-blue); } /* Use original blue for consistency with completed */

    .progress-bar {
        width: 100%;
        height: 10px;
        background: rgba(0, 0, 0, 0.08); /* Slightly darker background for light theme */
        border-radius: 5px;
        overflow: hidden;
        margin: 10px 0;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--content-color-start), var(--content-color-end)); /* Red to pink gradient for progress */
        transition: width 0.4s ease-out;
    }

    .metric-highlight {
        background: linear-gradient(135deg, var(--union-accent-light), var(--union-red-pink)); /* Use red/pink accents */
        color: var(--union-dark-blue); /* Dark blue text on light red background */
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 700;
        font-size: 14px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .empty-state {
        text-align: center;
        padding: 50px; /* More padding */
        color: var(--text-color-muted); /* Muted dark text */
        background: rgba(0, 0, 0, 0.03); /* Slightly more visible light background */
        border-radius: 15px;
        margin-top: 20px;
        border: 1px dashed rgba(0, 0, 0, 0.15); /* Darker dashed border */
    }

    .empty-state .icon {
        font-size: 4rem; /* Larger icon */
        margin-bottom: 20px;
        opacity: 0.7; /* Slightly higher opacity for icons */
        color: var(--text-color-muted); /* Ensure icon color matches muted text */
    }

    /* Responsive Adjustments */
    @media (min-width: 1200px) {
        /* Force 4 columns only when screen is wide enough AND not zoomed in too much */
        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
        }
        .charts-grid {
            grid-template-columns: 1fr 1fr;
        }
    }

    @media (max-width: 1199px) {
        .dashboard-container {
            padding: 30px 60px; /* Adjusted horizontal padding */
        }
        .charts-grid {
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
        }
        .chart-card canvas {
            height: 280px !important;
            max-height: 280px;
        }
    }

    @media (max-width: 992px) {
        .dashboard-container {
            padding: 25px 30px;
        }
        .header-card h1 {
            font-size: 2.8rem;
        }
        .header-subtitle {
            font-size: 1.2rem;
        }
        /* Allow auto-fit with a minimum width for tablets and smaller */
        .stats-grid {
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); /* Adjusted minmax for smaller screens */
        }
        .charts-grid, .lists-grid {
            grid-template-columns: 1fr; /* Stack on smaller screens */
            gap: 25px;
        }
        .chart-card canvas {
            height: 250px !important;
            max-height: 250px;
        }
    }

    @media (max-width: 768px) {
        .dashboard-container {
            padding: 20px;
        }

        .header-card {
            padding: 30px;
        }

        .header-card h1 {
            font-size: 2.2rem;
        }

        .header-subtitle {
            font-size: 1rem;
            margin-bottom: 20px;
        }

        .date-filter {
            flex-direction: column;
            align-items: stretch;
        }

        .date-filter input, .date-filter select, .filter-btn {
            width: 100%;
            margin-bottom: 10px;
        }

        .stat-card, .chart-card, .list-card {
            padding: 25px;
        }

        .stat-number {
            font-size: 2.8rem;
        }
        .stat-icon {
            width: 60px;
            height: 60px;
            font-size: 26px;
        }
    }

    @media (max-width: 480px) {
        .header-card h1 {
            font-size: 1.8rem;
        }
        .stat-number {
            font-size: 2.2rem;
        }
        .stat-icon {
            width: 50px;
            height: 50px;
            font-size: 22px;
        }
        .quick-action-btn {
            width: 100%;
            justify-content: center;
        }
        .list-item {
            flex-direction: column;
            align-items: flex-start;
            gap: 5px;
            padding: 15px;
        }
        .list-item strong {
            margin-bottom: 5px;
        }
        .status-badge {
            margin-top: 10px;
            align-self: flex-end; /* Align badge to the end when stacked */
        }
    }
</style>
@endpush

@section('content')
<div class="dashboard-body">
    <div class="dashboard-container">
        @php
            use Carbon\Carbon;
        @endphp
        <div class="card-base header-card">
            <h1><i class="fa-solid fa-gauge-high"></i> Tableau de Bord Administrateur</h1>
            <p class="header-subtitle">Aperçu complet des performances de votre plateforme</p>

            <form method="GET" action="{{ route('dashboard') }}" class="date-filter">
                <label for="month-select" class="sr-only">Select Month</label>
                <select name="selected_month" id="month-select">
                    <option value="">Tous les mois</option>
                    @foreach($months as $monthNumber => $monthName)
                        <option value="{{ $monthNumber }}" {{ $selectedMonth == $monthNumber ? 'selected' : '' }}>
                            {{ $monthName }}
                        </option>
                    @endforeach
                </select>
                <label for="year-input" class="sr-only">Select Year</label>
                <input type="number" name="selected_year" id="year-input" value="{{ $selectedYear ?? Carbon::now()->year }}" min="2000" max="{{ Carbon::now()->year + 5 }}">

                <button type="submit" class="filter-btn"><i class="fa-solid fa-filter"></i> Appliquer le filtre</button>
            </form>
        </div>

        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon icon-users"><i class="fa-solid fa-users"></i></div>
                <div class="stat-number">{{ number_format($totalUsers) }}</div>
                <div class="stat-label">Utilisateurs Totaux</div>
                <div class="stat-detail positive">
                    <i class="fa-solid fa-arrow-up"></i> +{{ $newRegistrationsLast30Days }} nouveaux ce mois-ci
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-formations"><i class="fa-solid fa-book-open"></i></div>
                <div class="stat-number">{{ number_format($totalFormations) }}</div>
                <div class="stat-label">Formations Totales</div>
                <div class="stat-detail info">
                    <i class="fa-solid fa-star"></i> {{ number_format($averageFormationRating ?? 0, 1) }}/5 évaluation moyenne
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-inscriptions"><i class="fa-solid fa-user-graduate"></i></div>
                <div class="stat-number">{{ number_format($totalInscriptions) }}</div>
                <div class="stat-label">Inscriptions Totales</div>
                <div class="stat-detail warning">
                    <i class="fa-solid fa-clipboard-check"></i> {{ number_format($completionRate ?? 0, 1) }}% taux d'achèvement
                </div>
            </div>

            <div class="stat-card">
                <div class="stat-icon icon-revenue"><i class="fa-solid fa-wallet"></i></div>
                <div class="stat-number">{{ number_format($totalRevenue, 2) }} DH</div>
                <div class="stat-label">Revenus Totaux</div>
                <div class="stat-detail danger">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ number_format($outstandingAmount, 2) }} DH en suspens
                </div>
            </div>
        </div>

        <div class="quick-actions-container">
            <div class="quick-actions-wrapper">
                <span class="quick-actions-label">Actions rapides :</span>
                <a href="{{ route('formations.index') }}" class="quick-action-btn"><i class="fa-solid fa-plus-circle"></i> Ajouter une Formation</a>
                <a href="{{ route('users.index') }}" class="quick-action-btn"><i class="fa-solid fa-users-gear"></i> Gérer les Utilisateurs</a>
                <a href="{{ route('payments.index') }}" class="quick-action-btn"><i class="fa-solid fa-money-check-dollar"></i> Voir les Paiements</a>
            </div>
        </div>

        <div class="charts-grid">
            <div class="card-base chart-card">
                <h3 class="chart-title"><i class="fa-solid fa-chart-pie"></i> Utilisateurs par Rôle</h3>
                <canvas id="usersByRoleChart"></canvas>
            </div>

            <div class="card-base chart-card">
                <h3 class="chart-title"><i class="fa-solid fa-chart-line"></i> Tendance des Revenus Mensuels</h3>
                <canvas id="revenueTrendChart"></canvas>
            </div>

            <div class="card-base chart-card">
                <h3 class="chart-title"><i class="fa-solid fa-chart-bar"></i> Méthodes de Paiement</h3>
                <canvas id="paymentMethodsChart"></canvas>
            </div>

            <div class="card-base chart-card">
                <h3 class="chart-title"><i class="fa-solid fa-circle-nodes"></i> Statut des Formations</h3>
                <canvas id="formationStatusChart"></canvas>
            </div>
        </div>

        <div class="lists-grid">
            <div class="card-base list-card">
                <h3 class="list-title"><i class="fa-solid fa-fire"></i> Meilleures Formations par Inscription</h3>
                @if($topFormationsByEnrollment->count() > 0)
                    @foreach($topFormationsByEnrollment as $formation)
                    <div class="list-item">
                        <div>
                            <strong>{{ $formation->title }}</strong>
                            <div>{{ $formation->inscriptions_count }} étudiants inscrits</div>
                        </div>
                        <div style="min-width: 100px;">
                            <div class="progress-bar">
                                <div class="progress-fill" style="width: {{ $topFormationsByEnrollment->max('inscriptions_count') > 0 ? ($formation->inscriptions_count / $topFormationsByEnrollment->max('inscriptions_count')) * 100 : 0 }}%"></div>
                            </div>
                            <div style="text-align: center; font-size: 12px;">{{ $formation->inscriptions_count }}</div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="icon"><i class="fa-solid fa-book-reader"></i></div>
                        <p>Aucune formation trouvée</p>
                    </div>
                @endif
            </div>

            <div class="card-base list-card">
                <h3 class="list-title"><i class="fa-solid fa-hourglass-end"></i> Paiements en Retard</h3>
                @if($overduePaymentsList->count() > 0)
                    @foreach($overduePaymentsList as $payment)
                    <div class="list-item">
                        <div>
                            <strong>{{ $payment->inscription->user->name }}</strong>
                            <div>{{ $payment->inscription->formation->name }}</div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-weight: 700; color: #dc2626; font-size: 16px;">{{ number_format($payment->amount, 2) }} DH</div>
                            <div style="font-size: 12px; color: #dc2626;">Dû le : {{ \Carbon\Carbon::parse($payment->due_date)->format('d M, Y') }}</div>
                        </div>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="icon"><i class="fa-solid fa-check-circle"></i></div>
                        <p>Tous les paiements sont à jour !</p>
                    </div>
                @endif
            </div>

            <div class="card-base list-card">
                <h3 class="list-title"><i class="fa-solid fa-calendar-alt"></i> Formations à Venir</h3>
                @if($upcomingFormations->count() > 0)
                    @foreach($upcomingFormations as $formation)
                    <div class="list-item">
                        <div>
                            <strong>{{ $formation->title }}</strong>
                            <div>Début : {{ \Carbon\Carbon::parse($formation->start_date)->format('d M, Y') }}</div>
                        </div>
                        <span class="status-badge status-pending">Bientôt</span>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="icon"><i class="fa-solid fa-calendar-check"></i></div>
                        <p>Aucune formation à venir</p>
                    </div>
                @endif
            </div>

            <div class="card-base list-card">
                <h3 class="list-title"><i class="fa-solid fa-comment-dots"></i> Réclamations Récentes</h3>
                @if($recentReclamations->count() > 0)
                    @foreach($recentReclamations as $reclamation)
                    <div class="list-item">
                        <div>
                            <strong>{{ $reclamation->user->name ?? 'N/A' }}</strong>
                            <div>{{ Str::limit($reclamation->subject ?? 'Sans objet', 40) }}</div>
                            <div style="font-size: 12px;">{{ $reclamation->created_at->format('d M, Y H:i') }}</div>
                        </div>
                        <span class="status-badge status-{{ $reclamation->status }}">{{ ucfirst($reclamation->status) }}</span>
                    </div>
                    @endforeach
                @else
                    <div class="empty-state">
                        <div class="icon"><i class="fa-solid fa-face-smile"></i></div>
                        <p>Aucune réclamation récente</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.9.1/chart.min.js"></script>
<script>
    // Chart.js Global Configuration
    Chart.defaults.font.family = 'Inter, sans-serif';
    Chart.defaults.color = 'rgba(0, 0, 0, 0.7)'; /* Muted dark for axis labels */
    Chart.defaults.borderColor = 'rgba(0, 0, 0, 0.1)'; /* Lighter grid lines on light background */

    // Helper function for creating gradients in charts
    function createGradient(ctx, chartArea, colorStops) {
        const gradient = ctx.createLinearGradient(0, chartArea.bottom, 0, chartArea.top);
        colorStops.forEach(stop => gradient.addColorStop(stop.offset, stop.color));
        return gradient;
    }

    // Users by Role Chart
    const usersByRoleCtx = document.getElementById('usersByRoleChart').getContext('2d');
    new Chart(usersByRoleCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($usersByRole->keys()) !!},
            datasets: [{
                data: {!! json_encode($usersByRole->values()) !!},
                backgroundColor: [
                    '#D32F2F', '#C2185B', '#FF6F6F', '#E57373', '#FFCDD2', '#F8BBD0' /* Shades of red/pink */
                ],
                borderColor: 'white', /* White border for contrast on light cards */
                borderWidth: 3,
                hoverBorderWidth: 5,
                hoverOffset: 10,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '70%',
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 25,
                        usePointStyle: true,
                        font: { size: 13, weight: '600' },
                        color: 'var(--text-color-primary)' /* Dark text for legend */
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255,255,255,0.3)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    boxPadding: 5
                }
            }
        }
    });

    // Revenue Trend Chart
    const revenueTrendCtx = document.getElementById('revenueTrendChart').getContext('2d');
    new Chart(revenueTrendCtx, {
        type: 'line',
        data: {
            labels: {!! json_encode($monthlyRevenueTrend->pluck('month')) !!},
            datasets: [{
                label: 'Revenus (DH)',
                data: {!! json_encode($monthlyRevenueTrend->pluck('total_amount')) !!},
                borderColor: 'var(--content-color-start)', /* Use the red color for the line */
                backgroundColor: (context) => {
                    const chart = context.chart;
                    const { ctx, chartArea } = chart;
                    if (!chartArea) {
                        return;
                    }
                    return createGradient(ctx, chartArea, [
                        { offset: 0, color: 'rgba(211, 47, 47, 0.6)' }, /* Matching gradient for fill */
                        { offset: 1, color: 'rgba(211, 47, 47, 0)' }
                    ]);
                },
                fill: true,
                tension: 0.4,
                borderWidth: 4,
                pointBackgroundColor: 'var(--content-color-end)', /* Use the pink color for points */
                pointBorderColor: 'white', /* White point border on light background */
                pointBorderWidth: 2,
                pointRadius: 6,
                pointHoverRadius: 9,
                pointHitRadius: 15
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255,255,255,0.3)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Revenus : ' + context.raw.toLocaleString() + ' DH';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)', /* Darker grid lines */
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' DH';
                        },
                        color: 'var(--text-color-secondary)' /* Darker tick labels */
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        color: 'var(--text-color-secondary)' /* Darker tick labels */
                    }
                }
            }
        }
    });

    // Payment Methods Chart
    const paymentMethodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
    new Chart(paymentMethodsCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($paymentsByMethod->pluck('payment_method')) !!},
            datasets: [{
                label: 'Montant (DH)',
                data: {!! json_encode($paymentsByMethod->pluck('total')) !!},
                backgroundColor: [
                    '#D32F2F', '#C2185B', '#FF6F6F', '#E57373', '#FFCDD2' /* Shades of red/pink */
                ],
                borderRadius: 10,
                borderSkipped: false,
                hoverBackgroundColor: (context) => {
                    const colors = [
                        '#A22020', '#9A134A', '#CC5F5F', '#B55E5E', '#D9BDC1'
                    ];
                    return colors[context.dataIndex % colors.length];
                }
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { display: false },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255,255,255,0.3)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    callbacks: {
                        label: function(context) {
                            return 'Montant : ' + context.raw.toLocaleString() + ' DH';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: 'rgba(0, 0, 0, 0.1)', /* Darker grid lines */
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return value.toLocaleString() + ' DH';
                        },
                        color: 'var(--text-color-secondary)' /* Darker tick labels */
                    }
                },
                x: {
                    grid: { display: false },
                    ticks: {
                        color: 'var(--text-color-secondary)' /* Darker tick labels */
                    }
                }
            }
        }
    });

    // Formation Status Chart
    const formationStatusCtx = document.getElementById('formationStatusChart').getContext('2d');
    new Chart(formationStatusCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode($formationsByStatus->keys()) !!},
            datasets: [{
                data: {!! json_encode($formationsByStatus->values()) !!},
                backgroundColor: [
                    '#D32F2F', '#C2185B', '#FF6F6F', '#E57373', '#FFCDD2' /* Shades of red/pink */
                ],
                borderColor: 'white', /* White border for contrast on light cards */
                borderWidth: 3,
                hoverBorderWidth: 5,
                hoverOffset: 10
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 25,
                        usePointStyle: true,
                        font: { size: 13, weight: '600' },
                        color: 'var(--text-color-primary)' /* Dark text for legend */
                    }
                },
                tooltip: {
                    backgroundColor: 'rgba(0,0,0,0.8)',
                    titleColor: '#fff',
                    bodyColor: '#fff',
                    borderColor: 'rgba(255,255,255,0.3)',
                    borderWidth: 1,
                    cornerRadius: 8,
                    displayColors: true,
                    boxPadding: 5
                }
            }
        }
    });
</script>
@endpush