@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center flex-wrap">
                        <div>
                            <h2 class="mb-1 text-primary fw-bold">{{ $module->title }}</h2>
                            <p class="text-muted mb-0">
                                <i class="fas fa-user-tie me-2"></i>
                                <strong>Consultant:</strong> {{ $module->user->name ?? 'Non assigné' }}
                            </p>
                        </div>
                        
                        {{-- <!-- Actions Buttons: Edit, Delete, and Back -->
                        <div class="d-flex align-items-center mt-2 mt-md-0">
                            <!-- 1. Bouton Modifier -->
                            <a href="{{ route('modules.edit', $module->id) }}" class="btn btn-warning me-2">
                                <i class="fas fa-edit me-2"></i>Modifier
                            </a>

                            <!-- 2. Bouton Supprimer (DELETE Form) -->
                            <form action="{{ route('modules.destroy', $module->id) }}" method="POST" onsubmit="return confirm('Attention ! Est-ce que vous êtes sûr de vouloir supprimer ce module ? Cette action est irréversible.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger me-3">
                                    <i class="fas fa-trash me-2"></i>Supprimer
                                </button>
                            </form> --}}

                            <!-- 3. Bouton Retour -->
                            <a href="{{ route('modules.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-arrow-left me-2"></i>Retour
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-gradient-primary text-white">
                <div class="card-body text-center">
                    <i class="fas fa-book-open fa-3x mb-3 opacity-75"></i>
                    <h3 class="fw-bold mb-1">{{ $totalCourses }}</h3>
                    <p class="mb-0 text-white-50">Total des séances</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-gradient-success text-white">
                <div class="card-body text-center">
                    <i class="fas fa-check-circle fa-3x mb-3 opacity-75"></i>
                    <h3 class="fw-bold mb-1">{{ $completedCourses }}</h3>
                    <p class="mb-0 text-white-50">Séances terminées</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
            <div class="card border-0 shadow-sm h-100 bg-gradient-warning text-white">
                <div class="card-body text-center">
                    <i class="fas fa-clock fa-3x mb-3 opacity-75"></i>
                    <h3 class="fw-bold mb-1">{{ $upcomingCourses }}</h3>
                    <p class="mb-0 text-white-50">Séances à venir</p>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 mb-3">
    <div class="card border-0 shadow-sm h-100 bg-gradient-info text-white">
        <div class="card-body text-center">
            <i class="fas fa-percent fa-3x mb-3 opacity-75"></i>
            <h3 class="fw-bold mb-1">{{ $module->progress ?? 0 }}%</h3>
            <p class="mb-0 text-white-50">Progression</p>
        </div>
    </div>
</div>
    </div>

    <!-- Module Information -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <!-- Content Section -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-list-ul text-primary me-2"></i>
                        Contenu du module
                    </h5>
                </div>
                <div class="card-body">
                    @if(is_array($module->content) && count($module->content) > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($module->content as $index => $item)
                                <li class="list-group-item border-0 py-3">
                                    <div class="d-flex align-items-start">
                                        <span class="badge bg-primary rounded-circle me-3 mt-1" style="width: 30px; height: 30px; display: flex; align-items: center; justify-content: center;">
                                            {{ $index + 1 }}
                                        </span>
                                        <span class="flex-grow-1">{{ $item }}</span>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">Aucun contenu disponible.</p>
                    @endif
                </div>
            </div>

            <!-- Courses Section -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-calendar-alt text-primary me-2"></i>
                        Liste des séances
                    </h5>
                </div>
                <div class="card-body">
                   @if($coursesList && $coursesList->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Heure</th>
                                        
                                        <th>Statut</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    {{-- 🚨 Remplacement de $module->courses par $coursesList --}}
                                    @foreach($coursesList as $course) 
                                        <tr>
                                            <td>
                                                <i class="fas fa-calendar me-2 text-muted"></i>
                                                {{ \Carbon\Carbon::parse($course->course_date)->format('d/m/Y') }}
                                            </td>
                                            <td>
                                                <i class="fas fa-clock me-2 text-muted"></i>
                                                {{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }}
                                                - 
                                                {{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}
                                            </td>
                                           
   <td>
    @php
        // ✅ Kansta3mlou direct l-attributes dyal Course model
        // course_date → Carbon Date object
        // end_time → String "HH:MM:SS"
        
        $isFinished = false;
        $isUpcomingDay = false;
        
        try {
            // 1. N-creéw l-date object
            $courseDate = \Carbon\Carbon::parse($course->course_date);
            
            // 2. N-checkiw wach nhar l-course baqi jay
            $isUpcomingDay = $courseDate->isFuture();
            
            // 3. N-checkiw wach l-course salat (b l'heure de fin)
            if ($course->end_time) {
                // Format: "2025-10-07 12:28:00"
                $courseEndDateTime = \Carbon\Carbon::parse(
                    $courseDate->format('Y-m-d') . ' ' . $course->end_time
                );
                $isFinished = $courseEndDateTime->isPast();
            }
            
        } catch (\Exception $e) {
            // Ila kayn chi error f parsing, kan-ignoréwh
            $isFinished = false;
            $isUpcomingDay = false;
        }
    @endphp

    {{-- 1. 🟢 TERMINÉE: L-heure de fin fatat --}}
    @if ($isFinished)
        <span class="badge bg-success">
            <i class="fas fa-check-circle me-1"></i> Terminée
        </span>
    
    {{-- 2. 🟠 À VENIR: Nhar l-course baqi jay --}}
    @elseif ($isUpcomingDay)
        <span class="badge bg-warning text-dark">
            <i class="fas fa-hourglass-half me-1"></i> À venir
        </span>
        
    {{-- 3. 🟡 REJOINDRE: L-course dyal lyouma w mazal ma khalatch --}}
    @else
        @if ($course->zoom_link)
            <form action="{{ route('courses.join', $course) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-sm btn-success">
                    <i class="fas fa-door-open me-1"></i> Rejoindre
                </button>
            </form>
        @else
            <span class="badge bg-secondary">
                <i class="fas fa-link-slash me-1"></i> En cours
            </span>
        @endif
    @endif
</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-5">
                            <i class="fas fa-calendar-times fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Aucune séance programmée pour ce module.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Module Details Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-info-circle text-primary me-2"></i>
                        Informations
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Statut</small>
                        @if($module->status === 'published')
                            <span class="badge bg-success">
                                <i class="fas fa-check-circle me-1"></i>Publié
                            </span>
                        @else
                            <span class="badge bg-secondary">
                                <i class="fas fa-edit me-1"></i>Brouillon
                            </span>
                        @endif
                    </div>
                    
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Durée totale</small>
                        <strong>{{ $module->duration_hours ?? 'N/A' }} heures</strong>
                    </div>
                    
                    <div class="mb-3 pb-3 border-bottom">
                        <small class="text-muted d-block mb-1">Nombre de séances</small>
                        <strong>{{ $module->number_seance ?? 'N/A' }}</strong>
                    </div>
                    
                    <div class="mb-0">
                        <small class="text-muted d-block mb-2">Progression</small>
                        <div class="progress" style="height: 25px;">
                            <div class="progress-bar bg-primary progress-bar-striped progress-bar-animated" 
                                role="progressbar" 
                                style="width: {{ $module->progress }}%;" 
                                aria-valuenow="{{ $module->progress }}" 
                                aria-valuemin="0" 
                                aria-valuemax="100">
                                {{ $module->progress }}%
                            </div>
                        </div>
                    </div>
                </div>
</div>

            {{-- <!-- Formations Linked -->
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white border-0 pt-4 pb-3">
                    <h5 class="mb-0 fw-bold">
                        <i class="fas fa-link text-primary me-2"></i>
                        Formations liées
                    </h5>
                </div>
                <div class="card-body">
                    @if($module->formations && $module->formations->count() > 0)
                        <ul class="list-group list-group-flush">
                            @foreach($module->formations as $formation)
                                <li class="list-group-item border-0 px-0 py-2">
                                    <i class="fas fa-graduation-cap text-primary me-2"></i>
                                    {{ $formation->title }}
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <p class="text-muted mb-0">Aucune formation liée.</p>
                    @endif
                </div> --}}
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }
    
    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
    
    .bg-gradient-warning {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    }
    
    .bg-gradient-info {
        background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }
    
    .card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.1) !important;
    }
    
    .table-hover tbody tr:hover {
        background-color: rgba(102, 126, 234, 0.05);
        cursor: pointer;
    }
    
    .list-group-item:hover {
        background-color: rgba(102, 126, 234, 0.03);
    }
    a.btn.btn-outline-secondary {
    background-color: #b11515 !important;
}
</style>
@endsection
