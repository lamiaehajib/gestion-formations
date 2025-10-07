@extends('layouts.app')

@section('title', 'Gestion des Cours')

@push('styles')
<style>
    .course-header {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(229, 62, 62, 0.3);
        position: relative;
        overflow: hidden;
    }

    .course-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        transform: rotate(45deg);
        pointer-events: none;
    }

    .course-header h1 {
        color: white;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .course-header p {
        color: rgba(255,255,255,0.9);
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    .btn-new-course {
        background: linear-gradient(135deg, #fff 0%, #f7fafc 100%);
        color: #e53e3e;
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        position: relative;
        z-index: 2;
    }

    .btn-new-course:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        color: #c53030;
    }

    .filter-section {
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border-left: 5px solid #e53e3e;
    }

    .filter-title {
        color: #2d3748;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-title i {
        color: #e53e3e;
        font-size: 1.2rem;
    }

    .form-control {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control:focus {
        border-color: #e53e3e;
        box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
    }

    .btn-filter {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(229, 62, 62, 0.4);
    }

    .btn-reset {
        background: #6b7280;
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-reset:hover {
        background: #4b5563;
        transform: translateY(-2px);
    }

    .course-card {
        background: white;
        border-radius: 20px;
        padding: 0;
        margin-bottom: 2rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
    }

    .course-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .course-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #e53e3e 0%, #c53030 100%);
    }

    .course-card-header {
        padding: 1.5rem;
        border-bottom: 1px solid #f7fafc;
    }

    .course-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .course-icon i {
        color: white;
        font-size: 1.5rem;
    }

    .course-title {
        color: #2d3748;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .course-formation {
        color: #e53e3e;
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0;
    }

    .course-date-badge {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .course-card-body {
        padding: 1.5rem;
    }

    .course-description {
        color: #4a5568;
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 1.5rem;
    }

    .course-info {
        margin-bottom: 1.5rem;
    }

    .info-item {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 0.8rem;
        color: #4a5568;
        font-size: 0.9rem;
    }

    .info-item i {
        color: #e53e3e;
        width: 16px;
        text-align: center;
    }

    .course-actions {
        display: flex;
        gap: 10px;
    }

    .btn-action {
        flex: 1;
        padding: 0.6rem 1rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .btn-view {
        background: linear-gradient(135deg,rgb(206, 49, 49) 0%, #ff5a1b 100%);
        color: white;
    }

    .btn-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(49, 130, 206, 0.3);
        color: white;
    }

    .btn-edit {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        color: white;
    }

    .btn-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(56, 161, 105, 0.3);
        color: white;
    }

    .btn-delete {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
        flex: 0 0 auto;
        width: 40px;
        padding: 0.6rem;
    }

    .btn-delete:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
        color: white;
    }

    .btn-duplicate {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
    }

    .btn-duplicate:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(66, 153, 225, 0.3);
        color: white;
    }

    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        margin: 2rem 0;
    }

    .empty-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .empty-icon i {
        color: #a0aec0;
        font-size: 2rem;
    }

    .empty-title {
        color: #2d3748;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .empty-description {
        color: #4a5568;
        font-size: 1rem;
        margin-bottom: 2rem;
    }

    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }

    .modal-header {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 1.5rem;
        border-bottom: none;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.2rem;
    }

    .modal-body {
        padding: 2rem;
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: none;
    }

    .modal-form-label {
        font-weight: 600;
        color: #2d3748;
    }

    .btn-join-card {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        flex: 1;
        box-shadow: 0 5px 15px rgba(66, 153, 225, 0.3);
    }

    .btn-join-card:hover {
        background: linear-gradient(135deg, #3182ce 0%, #2c5282 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(66, 153, 225, 0.4);
        color: white;
    }

    a.btn.btn-reset {
        color: #fffefe !important;
        background-color: #e93535;
    }

    .view-toggle {
        display: inline-flex;
        gap: 10px;
        background: white;
        padding: 0.5rem;
        border-radius: 50px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }

    .btn-toggle {
        padding: 0.5rem 1.5rem;
        border: none;
        border-radius: 50px;
        font-weight: 600;
        transition: all 0.3s ease;
        background: transparent;
        color: #4a5568;
    }

    .btn-toggle.active {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
    }

    .week-navigation {
        display: flex;
        justify-content: space-between;
        align-items: center;
        background: white;
        padding: 1.5rem;
        border-radius: 15px;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .week-info {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2d3748;
    }

    .btn-week-nav {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
        border: none;
        padding: 0.6rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-block;
    }

    .planning-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin-bottom: 2rem;
    }

    .day-column {
        background: white;
        border-radius: 15px;
        overflow: hidden;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
    }

    .day-header {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
        padding: 1.5rem;
        text-align: center;
    }

    .day-header.today {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
    }

    .day-name {
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.3rem;
    }

    .day-content {
        padding: 1rem;
        min-height: 200px;
    }

    .course-item-mini {
        background: #f7fafc;
        border-left: 4px solid #e53e3e;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 1rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .course-item-mini:hover {
        background: #edf2f7;
        transform: translateX(5px);
    }

    .course-time {
        font-size: 0.85rem;
        color: #e53e3e;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .course-title-mini {
        font-size: 1rem;
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.4rem;
    }

    .empty-day {
        text-align: center;
        padding: 2rem 1rem;
        color: #a0aec0;
    }

    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .animate-fade-in {
        animation: fadeInUp 0.6s ease-out;
    }

    @media (max-width: 768px) {
        .course-header h1 {
            font-size: 2rem;
        }
        
        .course-actions {
            flex-direction: column;
        }
        
        .btn-action {
            flex: none;
        }
    }

    @media (max-width: 576px) {
        .course-actions {
            flex-direction: column;
        }
        .course-actions .btn-action,
        .course-actions .btn-join-card {
            width: 100%;
        }
    }
    /* --- Styles for Module Folder View --- */
.module-folder {
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 10px rgba(0,0,0,0.05);
    transition: all 0.3s ease;
    background-color: #ffffff;
}

.module-folder:hover {
    box-shadow: 0 6px 15px rgba(0,0,0,0.1);
}

.course-card-header {
    background: #f7fafc; /* Light background for the header */
    padding: 1rem 1.5rem;
    border-bottom: 1px solid #edf2f7;
    transition: background 0.3s ease;
}

.course-card-header:hover {
    background: #eef1f3;
}

.module-folder-icon {
    color: #f6ad55; /* Orange/Yellow for a folder look */
    font-size: 1.5rem;
}

.module-toggle-icon {
    transition: transform 0.3s ease;
    color: #4a5568;
}

.module-folder.active .module-toggle-icon {
    transform: rotate(180deg);
}

.module-content {
    padding: 0 1.5rem 1rem 1.5rem;
    /* transition: max-height 0.3s ease-out; -- Handled better by JS */
}

/* Style for individual course items inside the module folder */
.course-item-mini {
    display: block;
    text-decoration: none;
    padding: 0.75rem 1rem;
    margin: 0.5rem 0;
    border-left: 4px solid #3182ce; /* Blue stripe on the left */
    border-radius: 4px;
    background: #f7fbff;
    transition: all 0.2s ease;
    color: #2d3748;
    font-weight: 500;
}

.course-item-mini:hover {
    background: #e6f0ff;
    transform: translateX(2px);
}

.course-title-mini {
    font-size: 0.95rem;
}

.course-time {
    font-weight: 700;
    font-size: 0.9rem;
    color: #4299e1;
}
/* --- Adjustments for Module Folder Layout --- */
/* Set module card to take full width of the container */
.module-folder {
    width: 100%; /* Important for col-lg-12 */
    margin-left: auto;
    margin-right: auto;
}

.course-card-header {
    background: #e6f0ff; /* Light blue background for folder header */
    border-left: 5px solid #4299e1; /* Blue stripe */
    border-radius: 8px 8px 0 0;
}

.module-folder-icon {
    color: #4299e1; /* Blue folder icon */
}

/* --- Styles for Individual Course Item (The new detailed card) --- */
.course-item-detail {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    transition: all 0.3s ease;
}

.course-item-detail:hover {
    border-color: #a0aec0;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15) !important;
}

.course-detail-title {
    color: #2d3748;
    font-weight: 700;
    font-size: 1.05rem;
}

.course-info-row span {
    color: #4a5568;
    font-weight: 500;
}

/* Styles for the small, round action buttons */
.btn-action-mini {
    width: 35px;
    height: 35px;
    padding: 0;
    border-radius: 50%;
    border: 1px solid #cbd5e0;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.btn-action-mini:hover {
    transform: scale(1.05);
    color: white; /* Ensure text color remains white on hover */
}

.btn-view {
    background-color: #4299e1; /* Blue (View) */
    border-color: #4299e1;
}
.btn-view:hover { background-color: #3182ce; }

.btn-edit {
    background-color: #38a169; /* Green (Edit) */
    border-color: #38a169;
}
.btn-edit:hover { background-color: #2f855a; }

.btn-duplicate {
    background-color: #ff5a1b; /* Orange (Duplicate) */
    border-color: #ff5a1b;
}
.btn-duplicate:hover { background-color: #d44d18; }

.btn-delete {
    background-color: #e53e3e; /* Red (Delete) */
    border-color: #e53e3e;
}
.btn-delete:hover { background-color: #c53030; }
.course-count{
    background-color: #c53030 !important;
}
</style>
@endpush

@section('content')
    <div class="animate-fade-in">
        <div class="course-header">
            <div class="row align-items-center">
                <div class="col-md-6">
                    <h1><i class="fas fa-graduation-cap me-3"></i>
                        @if($viewMode === 'planning')
                            Mon Planning de la semaine
                        @else
                            Gestion des Cours
                        @endif
                    </h1>
                    @if($viewMode === 'planning')
                        <p>Du {{ \Carbon\Carbon::parse($weekStart)->format('d/m/Y') }} au {{ \Carbon\Carbon::parse($weekEnd)->format('d/m/Y') }}</p>
                    @else
                        <p>Gérez vos formations et cours efficacement</p>
                    @endif
                </div>
                <div class="col-md-6 text-end">
                    <div class="view-toggle me-3 d-inline-flex">
                        <a href="{{ route('courses.index', ['view_mode' => 'list'] + request()->except('view_mode', 'week_offset')) }}" 
                           class="btn-toggle {{ $viewMode === 'list' ? 'active' : '' }}">
                            <i class="fas fa-list me-2"></i>Liste
                        </a>
                        <a href="{{ route('courses.index', ['view_mode' => 'planning'] + request()->except('view_mode')) }}" 
                           class="btn-toggle {{ $viewMode === 'planning' ? 'active' : '' }}">
                            <i class="fas fa-calendar-week me-2"></i>Planning
                        </a>
                    </div>

                    @can('course-create')
                        <button type="button" class="btn-new-course" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                            <i class="fas fa-plus me-2"></i>Nouveau Cours
                        </button>
                    @endcan
                </div>
            </div>
        </div>

        @if($viewMode === 'planning')
            <div class="week-navigation">
                <div class="week-info">
                    <i class="fas fa-calendar-alt me-2"></i>
                    Semaine {{ \Carbon\Carbon::parse($weekStart)->format('W') }} - {{ \Carbon\Carbon::parse($weekStart)->format('Y') }}
                </div>
                <div>
                    <a href="{{ route('courses.index', ['view_mode' => 'planning', 'week_offset' => ($weekOffset ?? 0) - 1] + request()->except('view_mode', 'week_offset')) }}" 
                       class="btn-week-nav me-2">
                        <i class="fas fa-chevron-left"></i> Précédente
                    </a>
                    @if(($weekOffset ?? 0) != 0)
                        <a href="{{ route('courses.index', ['view_mode' => 'planning'] + request()->except('view_mode', 'week_offset')) }}" 
                           class="btn-week-nav me-2">
                            Aujourd'hui
                        </a>
                    @endif
                    <a href="{{ route('courses.index', ['view_mode' => 'planning', 'week_offset' => ($weekOffset ?? 0) + 1] + request()->except('view_mode', 'week_offset')) }}" 
                       class="btn-week-nav">
                        Suivante <i class="fas fa-chevron-right"></i>
                    </a>
                </div>
            </div>
        @endif

        <div class="filter-section">
            <form method="GET" action="{{ route('courses.index') }}">
                <input type="hidden" name="view_mode" value="{{ $viewMode }}">
                @if($viewMode === 'planning')
                    <input type="hidden" name="week_offset" value="{{ $weekOffset ?? 0 }}">
                @endif

                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label class="form-label fw-semibold">Recherche</label>
                        <input type="text" name="search" value="{{ request('search') }}" class="form-control" placeholder="Titre du cours...">
                    </div>
                    @if($viewMode === 'list')
                        <div class="col-md-3 mb-3">
                            <label class="form-label fw-semibold">Date début</label>
                            <input type="date" name="start_date" value="{{ request('start_date') }}" class="form-control">
                        </div>
                    @endif
                    <div class="col-md-3 mb-3">
                        <label class="form-label fw-semibold">Formation</label>
                        <select name="filter_formation_id" class="form-control">
                            <option value="">Toutes</option>
                            @foreach($formationsForFilter as $formation)
                                <option value="{{ $formation->id }}" {{ request('filter_formation_id') == $formation->id ? 'selected' : '' }}>
                                    {{ $formation->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 mb-3">
                        <label class="form-label"> </label>
                        <button type="submit" class="btn btn-filter w-100">
                            <i class="fas fa-search me-2"></i>Filtrer
                        </button>
                    </div>
                </div>
            </form>
        </div>

        @if($viewMode === 'planning')
            <div class="planning-grid">
                @foreach(['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi', 'Samedi', 'Dimanche'] as $index => $dayName)
                    @php
        $currentDate = \Carbon\Carbon::parse($weekStart)->addDays($index);
        $isToday = $currentDate->isToday();
        $dayCourses = $coursesByDay[$currentDate->format('Y-m-d')] ?? collect();
                    @endphp

                    <div class="day-column">
                        <div class="day-header {{ $isToday ? 'today' : '' }}">
                            <div class="day-name">{{ $dayName }}</div>
                            <div class="day-date">{{ $currentDate->format('d/m/Y') }}</div>
                        </div>

                         <div class="day-content">
                            @forelse($dayCourses as $course)
                                <div class="course-item-mini" onclick="window.location='{{ route('courses.show', $course) }}'">
                                    <div class="course-time">
                                        <i class="fas fa-clock"></i>
                                        {{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }} - 
                                        {{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}
                                    </div>
                                    <div class="course-title-mini">{{ Str::limit($course->title, 40) }}</div>
                                    
                                    {{-- 🚨 MODIFICATION ICI: On n'affiche le nom de la formation que si l'utilisateur n'est PAS un Consultant --}}
                                    @if($course->formation && !auth()->user()->hasRole('Consultant'))
                                        <small class="text-muted">
                                            <i class="fas fa-graduation-cap me-1"></i>
                                            {{ Str::limit($course->formation->title, 30) }}
                                        </small>
                                    @endif
                                </div>
                            @empty
                                <div class="empty-day">
                                    <i class="fas fa-coffee d-block mb-2" style="font-size: 2rem;"></i>
                                    Journée libre
                                </div>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
@else
    {{-- Grouping courses by Module Title --}}
    @php
        // Group the courses by module title for the folder view
        $groupedCourses = $courses->groupBy(function ($course) {
            return optional($course->module)->title ?? 'Module Non Classé';
        });
        
    @endphp

    <div class="row">
        @forelse($groupedCourses as $moduleTitle => $moduleCourses)
            <div class="col-lg-12 mb-4"> {{-- Changed to col-lg-12 for full-width folder view --}}
                {{-- Main Module Folder Card (Accordion Header) --}}
                <div class="course-card module-folder">
                    {{-- Header (Clickable to Toggle) --}}
                    <div class="course-card-header d-flex justify-content-between align-items-center"
                        onclick="toggleModuleContent(this)" style="cursor: pointer;">

                        <div class="d-flex align-items-center">
                            {{-- Folder Icon --}}
                            <div class="course-icon me-3">
                                <i class="fas fa-folder module-folder-icon"></i>
                            </div>

                            <div>
                                <h5 class="course-title" style="margin-bottom: 0;">{{ $moduleTitle }}</h5>
                                {{-- Display Formation Title if available --}}
                                
                            </div>
                        </div>

                        {{-- Total Courses and Toggle Icon --}}
                        <div class="d-flex align-items-center">
                            <span class="course-count me-3 btn btn-sm btn-outline-secondary">{{ $moduleCourses->count() }} Cours</span>
                            <i class="fas fa-chevron-down module-toggle-icon"></i>
                        </div>
                    </div>

                    {{-- Course list for the module (initially hidden) --}}
                    <div class="module-content" style="display: none; padding: 1rem;">
                        @foreach($moduleCourses as $course)
                            {{-- START: Individual Course Card (The requested style) --}}
                            <div class="course-item-detail mb-3 p-3 rounded shadow-sm d-flex justify-content-between align-items-center">

                                <div class="course-details-left">
                                    {{-- Course Title --}}
                                    <h6 class="course-detail-title mb-1">{{ $course->title }}</h6>

                                    {{-- Info Details (Date, Time, Consultant) --}}
                                    <div class="course-info-row d-flex align-items-center text-muted small">
                                        {{-- Date --}}
                                        <span class="me-3">
                                            <i class="fas fa-calendar-alt me-1 text-primary"></i>
                                            {{ \Carbon\Carbon::parse($course->course_date)->format('d/m/Y') }}
                                        </span>
                                        {{-- Time --}}
                                        <span class="me-3">
                                            <i class="fas fa-clock me-1 text-primary"></i>
                                            {{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}
                                        </span>
                                        {{-- Consultant --}}
                                        @if($course->consultant)
                                            <span>
                                                <i class="fas fa-user-tie me-1 text-primary"></i>
                                                Consultant: <strong>{{ $course->consultant->name }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                <div class="d-flex align-items-center gap-2 course-actions-buttons">
                                 @php
                $courseDate = \Carbon\Carbon::parse($course->course_date)->startOfDay();
                $today = \Carbon\Carbon::today();
                $isCourseUpcomingOrToday = $courseDate->gte($today); // True ila kan mazal aw lyoma
            @endphp

 @if($course->zoom_link && $isCourseUpcomingOrToday)
                                    <form action="{{ route('courses.join', $course) }}" method="POST" class="d-inline flex-grow-1">
                                        @csrf
                                        <button type="submit" class="btn-join-card">
                                            <i class="fas fa-door-open"></i> Rejoindre
                                        </button>
                                    </form>
                                @elseif($course->zoom_link && !$isCourseUpcomingOrToday)
                                    <span class="badge bg-secondary p-2 flex-grow-1 text-center">
                                        Terminé
                                    </span>
                                @else
                                    <span class="badge bg-warning p-2 flex-grow-1 text-center">
                                        Non lié
                                    </span>
                                @endif

                                    {{-- 1. View/Join Button --}}
                                    <a href="{{ route('courses.show', $course) }}" class="btn-action-mini btn-view" title="Voir les détails">
                                        <i class="fas fa-eye"></i>
                                    </a>

                                    {{-- 2. Edit Button (Opens Modal) --}}
                                   @can('course-edit')
    <button type="button" class="btn-action-mini btn-edit"
        data-bs-toggle="modal" data-bs-target="#editCourseModal"
        data-course-id="{{ $course->id }}"
        data-formation-id="{{ $course->formation->id ?? '' }}" {{-- Ignoré par le JS, mais gardé en cas de besoin backend --}}
        data-module-id="{{ $course->module->id ?? '' }}"
        data-consultant-id="{{ $course->consultant_id ?? '' }}"
        data-title="{{ $course->title }}"
        data-description="{{ $course->description }}"
        data-course-date="{{ \Carbon\Carbon::parse($course->course_date)->format('Y-m-d') }}"
        data-start-time="{{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }}"
        data-end-time="{{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}"
        data-zoom-link="{{ $course->zoom_link ?? '' }}"
        data-recording-url="{{ $course->recording_url ?? '' }}"
        data-documents="{{ json_encode($course->documents ?? []) }}"
        title="Modifier">
        <i class="fas fa-edit"></i>
    </button>
@endcan

                                    {{-- 3. Duplicate Button --}}
                                    @can('course-create')
                                        <form action="{{ route('courses.duplicate', $course) }}" method="POST" class="d-inline">
                                            @csrf
                                            <button type="submit" class="btn-action-mini btn-duplicate" title="Dupliquer">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </form>
                                    @endcan

                                    {{-- 4. Delete Button --}}
                                    @can('course-delete')
                                        <button onclick="confirmDelete({{ $course->id }})" class="btn-action-mini btn-delete" title="Supprimer">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    @endcan
                                </div>
                            </div>
                            {{-- END: Individual Course Card --}}
                        @endforeach
                    </div>
                </div>
            </div>
        @empty
            {{-- Your existing empty state remains here --}}
            <div class="col-12">
                <div class="empty-state">
                    {{-- ... (Empty state code) ... --}}
                </div>
            </div>
        @endforelse
    </div>

    {{-- Pagination Links --}}
    @if($courses->hasPages())
        <div class="d-flex justify-content-center mt-4">
            <div class="bg-white rounded-3 shadow-sm p-3">
                {{ $courses->links() }}
            </div>
        </div>
    @endif
@endif
        {{-- Modal Delete --}}
        <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteModalLabel">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            Confirmer la suppression
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-0">Êtes-vous sûr de vouloir supprimer ce cours ? Cette action est irréversible.</p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                        <form id="deleteForm" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="fas fa-trash"></i> Supprimer
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

       {{-- Modal Create --}}
@can('course-create')
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createCourseModalLabel">
                    <i class="fas fa-plus-circle me-2"></i>
                    Créer un Nouveau Cours
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ route('courses.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        {{-- 🔥 1. MODULE FIRST (avec search) --}}
                        <div class="col-md-12 mb-3">
                            <label for="create_module_search" class="form-label modal-form-label">
                                <i class="fas fa-search me-1"></i> Rechercher Module
                            </label>
                            <input 
                                type="text" 
                                id="create_module_search" 
                                class="form-control" 
                                placeholder="Tapez pour rechercher un module..."
                            >
                        </div>

                        <div class="col-md-12 mb-3">
                            <label for="create_module_id" class="form-label modal-form-label">Module *</label>
                            <select 
                                name="module_id" 
                                id="create_module_id"
                                class="form-control @error('module_id') is-invalid @enderror"
                                required
                            >
                                <option value="">Sélectionnez un module</option>
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}" {{ old('module_id') == $module->id ? 'selected' : '' }}>
                                        {{ $module->title }}
                                    </option>
                                @endforeach
                            </select>
                            @error('module_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                Le cours sera créé dans toutes les formations contenant ce module
                            </small>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_consultant_id" class="form-label modal-form-label">Consultant</label>
                        <select name="consultant_id" id="create_consultant_id"
                            class="form-control @error('consultant_id') is-invalid @enderror">
                            <option value="">Sélectionnez un consultant (Optionnel)</option>
                            @foreach($consultants as $consultant)
                            <option value="{{ $consultant->id }}" {{ old('consultant_id') == $consultant->id ? 'selected' : '' }}>
                                {{ $consultant->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('consultant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="create_course_title" class="form-label modal-form-label">Titre du Cours</label>
                        <input type="text" name="title" id="create_course_title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="create_course_description" class="form-label modal-form-label">Description</label>
                        <textarea name="description" id="create_course_description" class="form-control @error('description') is-invalid @enderror" rows="4" required>{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="create_course_date" class="form-label modal-form-label">Date du Cours</label>
                            <input type="date" name="course_date" id="create_course_date" class="form-control @error('course_date') is-invalid @enderror" value="{{ old('course_date') }}" required>
                            @error('course_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="create_start_time" class="form-label modal-form-label">Heure de Début</label>
                            <input type="time" name="start_time" id="create_start_time" class="form-control @error('start_time') is-invalid @enderror" value="{{ old('start_time') }}" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="create_end_time" class="form-label modal-form-label">Heure de Fin</label>
                            <input type="time" name="end_time" id="create_end_time" class="form-control @error('end_time') is-invalid @enderror" value="{{ old('end_time') }}" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="create_zoom_link" class="form-label modal-form-label">Lien Zoom /Teams (Optionnel)</label>
                        <input type="url" name="zoom_link" id="create_zoom_link" class="form-control @error('zoom_link') is-invalid @enderror" value="{{ old('zoom_link') }}" placeholder="https://zoom.us/j/123456789">
                        @error('zoom_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="create_documents" class="form-label modal-form-label">Documents (PDF, DOCX, PPTX - Max 10MB par fichier)</label>
                        <input type="file" name="documents[]" id="create_documents" class="form-control @error('documents.*') is-invalid @enderror" multiple>
                        @error('documents.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-new-course">
                        <i class="fas fa-save"></i> Créer le Cours
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan

        {{-- Modal Edit --}}
       @can('course-edit')
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editCourseModalLabel">
                    <i class="fas fa-edit me-2"></i>
                    Modifier le Cours
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editCourseForm" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="row">
                        
                        {{-- 🔥 1. MODULE SEARCH --}}
                        <div class="col-md-12 mb-3">
                            <label for="edit_module_search" class="form-label modal-form-label">
                                <i class="fas fa-search me-1"></i> Rechercher Module
                            </label>
                            <input 
                                type="text" 
                                id="edit_module_search" 
                                class="form-control" 
                                placeholder="Tapez pour rechercher un module..."
                            >
                        </div>

                        {{-- 🔥 2. MODULE SELECT (Col-md-12 comme dans la création) --}}
                        <div class="col-md-12 mb-3">
                            <label for="edit_module_id" class="form-label modal-form-label">Module *</label>
                            <select name="module_id" id="edit_module_id"
                                class="form-control @error('module_id') is-invalid @enderror" required>
                                <option value="">Sélectionnez un module</option>
                                {{-- Assurez-vous que la variable $modules est disponible ici --}}
                                @foreach($modules as $module)
                                    <option value="{{ $module->id }}">{{ $module->title }}</option>
                                @endforeach
                            </select>
                            @error('module_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                La modification du module mettra à jour le cours dans toutes les formations associées.
                            </small>
                        </div>

                        {{-- Le champ 'formation_id' a été retiré pour correspondre à la logique de création --}}
                        <input type="hidden" name="formation_id" id="edit_formation_id" value=""> 
                        {{-- NOTE: J'ai laissé un champ caché pour `formation_id` car le contrôleur Laravel 
                             pourrait toujours en avoir besoin, même si la valeur sera peut-être ignorée ou 
                             déduite par votre logique backend. Dans le JS, vous devrez définir sa valeur 
                             à partir de l'attribut data du bouton, même s'il n'est pas utilisé pour la sélection. --}}
                    </div>

                    <div class="mb-3">
                        <label for="edit_title" class="form-label modal-form-label">Titre du Cours</label>
                        <input type="text" name="title" id="edit_title"
                            class="form-control @error('title') is-invalid @enderror" required>
                        @error('title')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit_consultant_id" class="form-label modal-form-label">Consultant</label>
                        <select name="consultant_id" id="edit_consultant_id"
                            class="form-control @error('consultant_id') is-invalid @enderror">
                            <option value="">Sélectionnez un consultant (Optionnel)</option>
                            @foreach($consultants as $consultant)
                                <option value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                            @endforeach
                        </select>
                        @error('consultant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit_description" class="form-label modal-form-label">Description</label>
                        <textarea name="description" id="edit_description"
                            class="form-control @error('description') is-invalid @enderror" rows="4" required></textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="edit_course_date" class="form-label modal-form-label">Date du Cours</label>
                            <input type="date" name="course_date" id="edit_course_date"
                                class="form-control @error('course_date') is-invalid @enderror" required>
                            @error('course_date')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_start_time" class="form-label modal-form-label">Heure de Début</label>
                            <input type="time" name="start_time" id="edit_start_time"
                                class="form-control @error('start_time') is-invalid @enderror" required>
                            @error('start_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="edit_end_time" class="form-label modal-form-label">Heure de Fin</label>
                            <input type="time" name="end_time" id="edit_end_time"
                                class="form-control @error('end_time') is-invalid @enderror" required>
                            @error('end_time')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="edit_zoom_link" class="form-label modal-form-label">Lien Zoom/Teams (Optionnel)</label>
                        <input type="url" name="zoom_link" id="edit_zoom_link"
                            class="form-control @error('zoom_link') is-invalid @enderror"
                            placeholder="https://zoom.us/j/123456789">
                        @error('zoom_link')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit_recording_url" class="form-label modal-form-label">Lien d'enregistrement (Optionnel)</label>
                        <input type="url" name="recording_url" id="edit_recording_url"
                            class="form-control @error('recording_url') is-invalid @enderror"
                            placeholder="https://youtube.com/watch?v=...">
                        @error('recording_url')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="mb-3">
                        <label for="edit_documents" class="form-label modal-form-label">Documents (PDF, DOCX, PPTX - Max 10MB par fichier)</label>
                        <input type="file" name="documents[]" id="edit_documents"
                            class="form-control @error('documents.*') is-invalid @enderror" multiple>
                        @error('documents.*')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <div id="existing_documents" class="mt-2"></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                    <button type="submit" class="btn btn-edit">
                        <i class="fas fa-save"></i> Mettre à jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endcan
    </div>

@endsection

@push('scripts')
<script>
    function confirmDelete(courseId) {
        document.getElementById('deleteForm').action = `/courses/${courseId}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // 🔥 NEW FUNCTION: Load Formations by Module (opposite of old logic)
    const loadFormationsByModule = (moduleId, formationSelect, selectedFormationId = null) => {
        formationSelect.innerHTML = '<option value="">Chargement...</option>';
        
        fetch(`/courses/modules/${moduleId}/formations`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                formationSelect.innerHTML = '<option value="">Sélectionnez une formation</option>';

                if (data.formations && data.formations.length > 0) {
                    data.formations.forEach(formation => {
                        const option = document.createElement('option');
                        option.value = formation.id;
                        option.textContent = formation.title;
                        
                        // يحدد Formation الصحيح عند التعديل أو عند وجود validation error
                        if (formation.id == selectedFormationId) {
                            option.selected = true;
                        }
                        formationSelect.appendChild(option);
                    });
                } else {
                    formationSelect.innerHTML = '<option value="">Aucune formation trouvée pour ce module</option>';
                }
            })
            .catch(error => {
                console.error('Erreur lors de la récupération des formations:', error);
                formationSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            });
    };

    // --- NEW FUNCTION: Module Folder Toggle ---
    function toggleModuleContent(moduleFolderElement) {
        const content = moduleFolderElement.querySelector('.module-content');
        const icon = moduleFolderElement.querySelector('.module-toggle-icon');
        
        if (content.style.display === 'none' || content.style.display === '') {
            content.style.display = 'block';
            icon.style.transform = 'rotate(180deg)';
        } else {
            content.style.display = 'none';
            icon.style.transform = 'rotate(0deg)';
        }
    }

    document.addEventListener('DOMContentLoaded', function() {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.course-card, .module-folder').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });

        // --- GESTION DES ERREURS DE VALIDATION ---
        @if ($errors->any() && session('open_create_modal'))
            var createCourseModal = new bootstrap.Modal(document.getElementById('createCourseModal'));
            createCourseModal.show();
        @endif

        @if ($errors->any() && session('edit_course_id'))
            var courseId = {{ session('edit_course_id') }};
            var editCourseModalElement = document.getElementById('editCourseModal');
            var editModal = new bootstrap.Modal(editCourseModalElement);

            editCourseModalElement.querySelector('#edit_consultant_id').value = "{{ old('consultant_id', '') }}";
            editCourseModalElement.querySelector('#edit_title').value = "{{ old('title', '') }}";
            editCourseModalElement.querySelector('#edit_description').value = "{{ old('description', '') }}";
            editCourseModalElement.querySelector('#edit_course_date').value = "{{ old('course_date', '') }}";
            editCourseModalElement.querySelector('#edit_start_time').value = "{{ old('start_time', '') }}";
            editCourseModalElement.querySelector('#edit_end_time').value = "{{ old('end_time', '') }}";
            editCourseModalElement.querySelector('#edit_zoom_link').value = "{{ old('zoom_link', '') }}";
            editCourseModalElement.querySelector('#edit_recording_url').value = "{{ old('recording_url', '') }}";
            editModal.show();
        @endif

        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(alert => {
                if (alert) {
                    alert.classList.add('fade');
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);

        // --- 🔥 GESTION DE LA MODAL DE CRÉATION (CREATE) - SIMPLIFIED ---
        const createCourseModal = document.getElementById('createCourseModal');
        if (createCourseModal) {
            const createModuleSearch = createCourseModal.querySelector('#create_module_search');
            const createModuleSelect = createCourseModal.querySelector('#create_module_id');

            // 🔥 Search functionality for modules
            createModuleSearch.addEventListener('input', function() {
                const searchTerm = this.value.toLowerCase();
                const options = createModuleSelect.querySelectorAll('option');
                
                options.forEach(option => {
                    if (option.value === '') return; // Skip placeholder
                    const text = option.textContent.toLowerCase();
                    option.style.display = text.includes(searchTerm) ? 'block' : 'none';
                });
            });
        }

        // --- 🔥 GESTION DE LA MODAL D'ÉDITION (EDIT) - NEW LOGIC ---
       const editCourseModal = document.getElementById('editCourseModal');
if (editCourseModal) {
    const editModuleSearch = editCourseModal.querySelector('#edit_module_search');
    const editModuleSelect = editCourseModal.querySelector('#edit_module_id');
    const editFormationSelect = editCourseModal.querySelector('#edit_formation_id');
    const editConsultantSelect = editCourseModal.querySelector('#edit_consultant_id');

    // 🔥 1. Search functionality for modules (edit modal)
    if (editModuleSearch) {
        editModuleSearch.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = editModuleSelect.querySelectorAll('option');
            
            options.forEach(option => {
                if (option.value === '') return;
                const text = option.textContent.toLowerCase();
                option.style.display = text.includes(searchTerm) ? 'block' : 'none';
            });
        });
    }

    // 🔥 2. When Module changes, load Formations (optionnel selon votre logique)
    editModuleSelect.addEventListener('change', function() {
        const moduleId = this.value;
        if (moduleId && editFormationSelect) {
            loadFormationsByModule(moduleId, editFormationSelect);
        }
    });

    // 🔥 3. On modal open, load data and set formation_id
    editCourseModal.addEventListener('show.bs.modal', event => {
        const button = event.relatedTarget;
        const courseId = button.getAttribute('data-course-id');
        const moduleId = button.getAttribute('data-module-id');
        const formationId = button.getAttribute('data-formation-id');
        const consultantId = button.getAttribute('data-consultant-id');
        const title = button.getAttribute('data-title');
        const description = button.getAttribute('data-description');
        const courseDate = button.getAttribute('data-course-date');
        const startTime = button.getAttribute('data-start-time');
        const endTime = button.getAttribute('data-end-time');
        const zoomLink = button.getAttribute('data-zoom-link');
        const recordingUrl = button.getAttribute('data-recording-url');
        const documents = JSON.parse(button.getAttribute('data-documents') || '[]');

        const modalForm = editCourseModal.querySelector('#editCourseForm');
        const existingDocumentsDiv = editCourseModal.querySelector('#existing_documents');

        // Set form action
        modalForm.action = `/courses/${courseId}`;
        
        // ✅ LIGNE CRITIQUE AJOUTÉE : Set formation_id dans le champ caché
        if (editFormationSelect) {
            editFormationSelect.value = formationId || '';
        }
        
        // Set all other fields
        editCourseModal.querySelector('#edit_module_id').value = moduleId || '';
        editCourseModal.querySelector('#edit_consultant_id').value = consultantId || '';
        editCourseModal.querySelector('#edit_title').value = title || '';
        editCourseModal.querySelector('#edit_description').value = description || '';
        editCourseModal.querySelector('#edit_course_date').value = courseDate || '';
        editCourseModal.querySelector('#edit_start_time').value = startTime || '';
        editCourseModal.querySelector('#edit_end_time').value = endTime || '';
        editCourseModal.querySelector('#edit_zoom_link').value = zoomLink || '';
        editCourseModal.querySelector('#edit_recording_url').value = recordingUrl || '';

        // Handle existing documents
        existingDocumentsDiv.innerHTML = '';
        if (documents && documents.length > 0) {
            documents.forEach((doc, index) => {
                const docHtml = `
                    <div class="d-flex justify-content-between align-items-center mb-2 p-2 border rounded">
                        <a href="${doc.url || '#'}" target="_blank" class="text-primary">
                            <i class="fas fa-file-alt me-2"></i>${doc.name || 'Document'}
                        </a>
                        <button type="button" class="btn btn-sm btn-danger remove-existing-doc" 
                                data-doc-name="${doc.name}" data-course-id="${courseId}">
                            <i class="fas fa-times"></i>
                        </button>
                        <input type="hidden" name="existing_documents_to_keep[]" value="${doc.name}">
                    </div>
                `;
                existingDocumentsDiv.insertAdjacentHTML('beforeend', docHtml);
            });
            
            // Add event listeners to remove buttons
            existingDocumentsDiv.querySelectorAll('.remove-existing-doc').forEach(button => {
                button.addEventListener('click', function() {
                    const docName = this.getAttribute('data-doc-name');
                    const keepInput = this.closest('.d-flex').querySelector('input[name^="existing_documents_to_keep"]');
                    if (keepInput) {
                        keepInput.name = 'documents_to_delete[]';
                        keepInput.value = docName;
                    }
                    this.closest('.d-flex').style.textDecoration = 'line-through';
                    this.closest('.d-flex').style.opacity = '0.5';
                    this.remove();
                });
            });
        }
    });
}
    });
</script>
<script>
    function toggleModuleContent(headerElement) {
        const card = headerElement.closest('.module-folder');
        const content = card.querySelector('.module-content');
        const icon = card.querySelector('.module-toggle-icon');

        card.classList.toggle('active');

        if (content.style.display === "block" || content.style.display === "") {
            content.style.display = "none";
        } else {
            content.style.display = "block";
        }
    }
</script>
@endpush