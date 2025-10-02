@extends('layouts.app')

@section('title', 'Détails du Cours : ' . $course->title)

@push('styles')
<style>
    body {
        background-color: #f0f2f5; /* Light gray background */
    }
    .course-detail-container {
        max-width: 900px;
        margin: 3rem auto;
        padding: 2.5rem;
        background-color: #ffffff;
        border-radius: 20px;
        box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
        border: 1px solid #e2e8f0;
        animation: fadeIn 0.8s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .course-header-section {
        border-bottom: 2px solid #e53e3e; /* Red accent */
        padding-bottom: 1.5rem;
        margin-bottom: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
    }
    .course-header-section h1 {
        color: #2d3748;
        font-size: 2.8rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }
    .course-header-section .formation-tag {
        background-color: #e53e3e;
        color: white;
        padding: 0.5rem 1.2rem;
        border-radius: 25px;
        font-size: 0.9rem;
        font-weight: 700;
        letter-spacing: 0.5px;
        box-shadow: 0 4px 10px rgba(229, 62, 62, 0.2);
    }
    .course-header-section .course-meta {
        font-size: 1.1rem;
        color: #718096;
        margin-top: 0.5rem;
    }
    .course-header-section .consultant-info {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-top: 1rem;
    }
    .course-header-section .consultant-info img {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e53e3e;
    }
    .course-header-section .consultant-info span {
        font-weight: 600;
        color: #4a5568;
    }

    .section-title {
        color: #2d3748;
        font-size: 1.8rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #edf2f7;
        display: flex;
        align-items: center;
        gap: 10px;
    }
    .section-title i {
        color: #e53e3e;
        font-size: 1.5rem;
    }

    .card-info {
        background-color: #f7fafc;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        border: 1px solid #ebf4f8;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
    }
    .card-info ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .card-info ul li {
        display: flex;
        align-items: center;
        margin-bottom: 0.8rem;
        color: #4a5568;
        font-size: 1rem;
    }
    .card-info ul li:last-child {
        margin-bottom: 0;
    }
    .card-info ul li i {
        color: #e53e3e;
        margin-right: 12px;
        width: 20px;
        text-align: center;
        font-size: 1.1rem;
    }
    .card-info ul li strong {
        color: #2d3748;
    }

    .description-content {
        color: #4a5568;
        line-height: 1.8;
        font-size: 1rem;
        margin-bottom: 2rem;
    }

    .documents-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .documents-list li {
        background-color: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1rem 1.5rem;
        margin-bottom: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        transition: all 0.2s ease-in-out;
    }
    .documents-list li:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }
    .documents-list li .doc-info {
        display: flex;
        align-items: center;
    }
    .documents-list li .doc-icon {
        color: #3182ce;
        font-size: 1.5rem;
        margin-right: 1rem;
    }
    .documents-list li .doc-name {
        font-weight: 600;
        color: #2d3748;
    }
    .documents-list li .doc-size {
        font-size: 0.85rem;
        color: #718096;
        margin-left: 0.5rem;
    }
    .documents-list li .btn-download {
        background-color: #3182ce;
        color: white;
        border: none;
        padding: 0.6rem 1rem;
        border-radius: 8px;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }
    .documents-list li .btn-download:hover {
        background-color: #2b6cb0;
    }

    .evaluation-card {
        background-color: #f7fafc;
        border-left: 5px solid #38a169; /* Green accent */
        border-radius: 10px;
        padding: 1.5rem;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
    }
    .evaluation-card .evaluation-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 0.8rem;
    }
    .evaluation-card .evaluation-header strong {
        color: #2d3748;
        font-size: 1.1rem;
    }
    .evaluation-card .evaluation-header .rating {
        color: #f6ad55; /* Orange for stars */
        font-size: 1.2rem;
    }
    .evaluation-card .evaluation-content {
        color: #4a5568;
        line-height: 1.6;
        font-size: 0.95rem;
    }

    .btn-custom {
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        transition: all 0.3s ease;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }
    .btn-back {
        background-color: #a0aec0;
        color: white;
    }
    .btn-back:hover {
        background-color: #718096;
        color: white;
        transform: translateY(-2px);
    }
    .btn-join-zoom {
        background: linear-gradient(135deg, #4299e1 0%, #3182ce 100%);
        color: white;
        box-shadow: 0 5px 15px rgba(66, 153, 225, 0.3);
    }
    .btn-join-zoom:hover {
        background: linear-gradient(135deg, #3182ce 0%, #2c5282 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(66, 153, 225, 0.4);
    }
    .btn-edit-course {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        color: white;
        box-shadow: 0 5px 15px rgba(56, 161, 105, 0.3);
    }
    .btn-edit-course:hover {
        background: linear-gradient(135deg, #2f855a 0%, #276749 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(56, 161, 105, 0.4);
    }
    .btn-delete-course {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
        box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
    }
    .btn-delete-course:hover {
        background: linear-gradient(135deg, #c53030 0%, #a02424 100%);
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(229, 62, 62, 0.4);
    }

    .alert-message {
        margin-top: 2rem;
        border-radius: 10px;
        padding: 1rem 1.5rem;
        font-size: 1rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
    }
    .alert-message.alert-success {
        background-color: #e6fffa;
        color: #38a169;
        border: 1px solid #9ae6b4;
    }
    .alert-message.alert-danger {
        background-color: #fff5f5;
        color: #e53e3e;
        border: 1px solid #feb2b2;
    }
    .alert-message i {
        font-size: 1.2rem;
    }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .course-detail-container {
            margin: 1.5rem auto;
            padding: 1.5rem;
        }
        .course-header-section {
            flex-direction: column;
            align-items: flex-start;
        }
        .course-header-section h1 {
            font-size: 2rem;
        }
        .course-header-section .formation-tag {
            margin-top: 1rem;
        }
        .section-title {
            font-size: 1.5rem;
        }
        .btn-group-actions {
            flex-direction: column;
            gap: 15px;
        }
        .btn-custom {
            width: 100%;
            justify-content: center;
        }
    }

    .participation-section {
    margin-top: 3rem;
    padding-top: 2rem;
    border-top: 1px solid #e2e8f0;
}

.stat-card {
    background-color: #ffffff;
    border-radius: 15px;
    padding: 1.5rem;
    border: 1px solid #ebf4f8;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    display: flex;
    align-items: center;
    gap: 15px;
}
.stat-card .icon-circle {
    width: 60px;
    height: 60px;
    border-radius: 50%;
    background-color: #e6fffa; /* Light green-blue */
    color: #38a169; /* Green icon */
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
}
.stat-card .stat-value {
    font-size: 2.5rem;
    font-weight: 800;
    color: #2d3748;
    line-height: 1;
}
.stat-card .stat-label {
    font-size: 0.9rem;
    color: #718096;
    font-weight: 600;
    margin-top: 5px;
}

.participants-list-container {
    background-color: #f7fafc;
    border-radius: 15px;
    padding: 1.5rem;
    border: 1px solid #ebf4f8;
    max-height: 400px; /* Limite la hauteur de la liste */
    overflow-y: auto; /* Permet le défilement */
}

.participants-list-container .list-title {
    color: #4a5568;
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px dashed #e2e8f0;
}

.participants-list {
    list-style: none;
    padding: 0;
    margin: 0;
}

.participants-list li {
    padding: 0.5rem 0;
    border-bottom: 1px solid #edf2f7;
    color: #4a5568;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 10px;
}

.participants-list li:last-child {
    border-bottom: none;
}
.participants-list li i {
    color: #e53e3e; /* Red accent for bullet point */
    font-size: 0.7rem;
}

@media (max-width: 768px) {
    .stat-card {
        margin-bottom: 1.5rem;
    }
}
</style>
@endpush

@section('content')
<div class="course-detail-container">

    @if (session('success'))
        <div class="alert alert-success alert-message">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger alert-message">
            <i class="fas fa-times-circle"></i> {{ session('error') }}
        </div>
    @endif

    {{-- Course Header --}}
    <div class="course-header-section">
        <div>
            <h1>{{ $course->title }}</h1>
            @if($course->formation)
                <p class="course-formation">
                    {{-- Afficher seulement la formation liée --}}
                    <span class="badge bg-secondary">{{ $course->formation->title }}</span>
                    
                    {{-- Ajouter le Module si nécessaire --}}
                    @if($course->module)
                        <span class="badge bg-info ms-2">{{ $course->module->title }}</span>
                    @endif
                </p>
            @else
                <p class="course-formation">Aucune formation associée</p>
            @endif
            <div class="consultant-info">
               
                {{-- Changed to display $course->consultant->name directly --}}
                <span>Par: {{ $course->consultant->name ?? 'N/A' }}</span>
            </div>
        </div>
        <span class="formation-tag">
            <i class="fas fa-calendar-alt me-2"></i> {{ \Carbon\Carbon::parse($course->course_date)->format('d F Y') }}
        </span>
    </div>

    {{-- Course Details --}}
    <h2 class="section-title"><i class="fas fa-info-circle"></i> Détails du Cours</h2>
    <div class="card-info">
        <ul>
            <li><i class="fas fa-clock"></i> Heure: <strong>{{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}</strong></li>
            <li><i class="fas fa-calendar-day"></i> Date: <strong>{{ \Carbon\Carbon::parse($course->course_date)->format('d/m/Y') }}</strong></li>
            @if ($course->zoom_link)
                <li><i class="fas fa-video"></i> Lien Zoom: <a href="{{ $course->zoom_link }}" target="_blank" class="text-primary-emphasis">Rejoindre la réunion</a></li>
            @else
                <li><i class="fas fa-video-slash"></i> Lien Zoom: <span class="text-muted">Non disponible</span></li>
            @endif
            @if ($course->recording_url)
                <li><i class="fas fa-record-vinyl"></i> Enregistrement: <a href="{{ $course->recording_url }}" target="_blank" class="text-primary-emphasis">Voir l'enregistrement</a></li>
            @else
                <li><i class="fas fa-compact-disc"></i> Enregistrement: <span class="text-muted">Non disponible</span></li>
            @endif
        </ul>
    </div>

    {{-- Course Description --}}
    <h2 class="section-title"><i class="fas fa-align-left"></i> Description du Cours</h2>
    <div class="description-content">
        <p> {!! nl2br(e($course->description)) !!}</p>
       
    </div>

    {{-- Documents Section --}}
    <h2 class="section-title"><i class="fas fa-file-alt"></i> Documents du Cours</h2>
    @if ($course->documents && count($course->documents) > 0)
        <ul class="documents-list">
            @foreach ($course->documents as $index => $document)
                <li>
                    <div class="doc-info">
                        <i class="doc-icon fas fa-file-pdf"></i> {{-- Default icon, you can change based on type --}}
                        <span class="doc-name">{{ $document['name'] }}</span>
                        <span class="doc-size">({{ round($document['size'] / 1024 / 1024, 2) }} MB)</span>
                    </div>
                    <a href="{{ route('courses.download-document', ['course' => $course->id, 'document_index' => $index]) }}" class="btn-download" download>
                        <i class="fas fa-download me-2"></i> Télécharger
                    </a>
                </li>
            @endforeach
        </ul>
    @else
        <div class="alert alert-info alert-message text-center">
            <i class="fas fa-info-circle"></i> Aucun document n'est disponible pour ce cours pour le moment.
        </div>
    @endif

    {{-- Evaluations Section (if you have evaluations relation setup) --}}
   

    {{-- Actions --}}
    <div class="d-flex justify-content-between align-items-center mt-5 flex-wrap btn-group-actions">
        <a href="{{ route('courses.index') }}" class="btn-custom btn-back">
            <i class="fas fa-arrow-left"></i> Retour aux Cours
        </a>

        <div class="d-flex gap-3 mt-3 mt-md-0">
            {{-- Changed "Rejoindre le Cours" to a form for POST request --}}
            @if ($course->zoom_link)
                <form action="{{ route('courses.join', $course) }}" method="POST" class="d-inline">
                    @csrf
                    <button type="submit" class="btn-custom btn-join-zoom">
                        <i class="fas fa-door-open"></i> Rejoindre le Cours
                    </button>
                </form>
            @endif

          
        </div>
    </div>
     @can('course-create')
    <div class="participation-section">
    <h2 class="section-title"><i class="fas fa-chart-bar"></i> Statistiques de participation</h2>

    {{-- Stat Card for Count --}}
    <div class="row mb-4">
        <div class="col-12">
            <div class="stat-card">
                <div class="icon-circle">
                    <i class="fas fa-users"></i>
                </div>
                <div>
                    <div class="stat-value">{{ $joinCount }}</div>
                    <div class="stat-label">Personnes ayant cliqué sur "Rejoindre"</div>
                </div>
            </div>
        </div>
    </div>

    {{-- List of Participants --}}
    <div class="participants-list-container">
        <div class="list-title">
            <i class="fas fa-list-ul me-2"></i> Liste des Participants ({{ $joinCount }} Utilisateurs)
        </div>

        @if(!empty($joinedUsers))
            <ul class="participants-list">
                @foreach($joinedUsers as $userName)
                    <li>
                        <i class="fas fa-circle"></i> {{ $userName }}
                    </li>
                @endforeach
            </ul>
        @else
            <div class="alert alert-warning alert-message text-center mb-0">
                <i class="fas fa-exclamation-circle"></i> Aucun utilisateur n'a encore cliqué sur "Rejoindre".
            </div>
        @endif
    </div>
</div>
{{-- END NEW SECTION --}}
@endcan
</div>



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
                        <i class="fas fa-trash me-2"></i>Supprimer
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmDelete(courseId) {
        document.getElementById('deleteForm').action = `/courses/${courseId}`;
        new bootstrap.Modal(document.getElementById('deleteModal')).show();
    }

    // Auto-hide alerts after 5 seconds
    document.addEventListener('DOMContentLoaded', function() {
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert-message');
            alerts.forEach(alert => {
                if (alert) {
                    alert.classList.add('fade');
                    setTimeout(() => alert.remove(), 500);
                }
            });
        }, 5000);
    });
</script>
@endpush