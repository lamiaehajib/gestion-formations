@extends('layouts.app')

@section('title', 'Gestion des Rappels de Paiement')

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #D32F2F 0%, #C2185B 100%); border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-white">
                    <h2 class="mb-2 fw-bold">
                        <i class="fas fa-bell me-2"></i>Rappels de Paiement
                    </h2>
                    <p class="mb-0 opacity-90">
                        Gérez les rappels pour les étudiants ayant des paiements en attente
                    </p>
                </div>
                <div class="text-white text-end">
                    <div class="fs-1 fw-bold">{{ $students->count() }}</div>
                    <small class="opacity-90">Étudiants concernés</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px;">
        <div class="card-body">
            <form method="GET" action="{{ route('payment-reminders.index') }}" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label fw-bold">
                        <i class="fas fa-search text-primary me-2"></i>Rechercher
                    </label>
                    <input type="text" 
                           name="search" 
                           class="form-control" 
                           placeholder="Nom ou email de l'étudiant..."
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">
                        <i class="fas fa-filter text-primary me-2"></i>Catégorie
                    </label>
                    <select name="category" class="form-select">
                        <option value="">Toutes les catégories</option>
                        @foreach($targetCategories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>Filtrer
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Formulaire d'envoi des rappels -->
    <form method="POST" action="{{ route('payment-reminders.send') }}" id="reminderForm">
        @csrf
        
        <!-- Barre d'actions -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 15px; background-color: #f8f9fa;">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-4">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label fw-bold" for="selectAll">
                                Sélectionner tous (<span id="selectedCount">0</span>)
                            </label>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-bold mb-1">
                            <i class="fas fa-calendar-alt text-danger me-2"></i>Date d'échéance
                        </label>
                        <input type="date" 
                               name="expiry_date" 
                               class="form-control" 
                               value="2025-11-05"
                               min="{{ date('Y-m-d') }}"
                               required>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" 
                                class="btn text-white w-100" 
                                style="background: linear-gradient(135deg, #D32F2F, #C2185B); margin-top: 28px;"
                                id="sendBtn"
                                disabled>
                            <i class="fas fa-paper-plane me-2"></i>Envoyer Rappels
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Liste des étudiants -->
        <div class="row">
            @forelse($students as $student)
                <div class="col-xl-6 col-lg-6 mb-4">
                    <div class="card border-0 shadow-sm h-100 student-card" style="border-radius: 15px; transition: all 0.3s ease;">
                        <div class="card-body p-4">
                            <!-- Header avec checkbox -->
                            <div class="d-flex align-items-start mb-3">
                                <div class="form-check me-3">
                                    <input class="form-check-input student-checkbox" 
                                           type="checkbox" 
                                           name="student_ids[]" 
                                           value="{{ $student->id }}"
                                           id="student_{{ $student->id }}">
                                </div>
                                <div class="flex-grow-1">
                                    <h5 class="mb-1 fw-bold text-dark">
                                        <i class="fas fa-user-graduate text-primary me-2"></i>
                                        {{ $student->name }}
                                    </h5>
                                    <p class="text-muted mb-0 small">
                                        <i class="fas fa-envelope me-1"></i>{{ $student->email }}
                                    </p>
                                </div>
                                
                                <!-- Badge de statut rappel -->
                                @if($student->last_reminder && $student->last_reminder->is_active)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Rappel actif
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-clock me-1"></i>Aucun rappel
                                    </span>
                                @endif
                            </div>

                            <!-- Statistiques -->
                            <div class="row g-3 mb-3">
                                <div class="col-6">
                                    <div class="p-3 rounded" style="background-color: #fef2f2;">
                                        <div class="text-danger fw-bold fs-4">
                                            {{ number_format($student->total_remaining, 2) }} MAD
                                        </div>
                                        <small class="text-muted">Montant restant total</small>
                                    </div>
                                </div>
                                <div class="col-6">
                                    <div class="p-3 rounded" style="background-color: #eff6ff;">
                                        <div class="text-primary fw-bold fs-4">
                                            {{ $student->inscriptions_count }}
                                        </div>
                                        <small class="text-muted">Formation(s) active(s)</small>
                                    </div>
                                </div>
                            </div>

                            <!-- Liste des inscriptions -->
                            <div class="inscriptions-list">
                                <small class="text-muted fw-bold d-block mb-2">
                                    <i class="fas fa-list me-1"></i>Formations concernées:
                                </small>
                                @foreach($student->inscriptions as $inscription)
                                    <div class="mb-2 p-2 rounded" style="background-color: #f8f9fa;">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <small class="fw-bold text-dark d-block">
                                                    {{ $inscription->formation->title }}
                                                </small>
                                                <small class="text-muted">
                                                    <span class="badge bg-info me-1">
                                                        {{ $inscription->formation->category->name }}
                                                    </span>
                                                    Reste: <strong class="text-danger">{{ number_format($inscription->remaining_amount, 2) }} MAD</strong>
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <!-- Info dernier rappel -->
                            @if($student->last_reminder)
                                <div class="mt-3 pt-3 border-top">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle text-primary me-1"></i>
                                        Dernier rappel: <strong>{{ $student->last_reminder->sent_at->format('d/m/Y à H:i') }}</strong>
                                        @if($student->last_reminder->sentBy)
                                            par {{ $student->last_reminder->sentBy->name }}
                                        @endif
                                    </small>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm" style="border-radius: 15px;">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-inbox fa-4x text-muted mb-3"></i>
                            <h4 class="text-muted">Aucun étudiant trouvé</h4>
                            <p class="text-muted">
                                Aucun étudiant n'a d'inscription avec paiement en attente pour ces catégories.
                            </p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </form>
</div>

<style>
    .student-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
    }

    .form-check-input:checked {
        background-color: #D32F2F;
        border-color: #D32F2F;
    }

    .student-checkbox {
        cursor: pointer;
        width: 20px;
        height: 20px;
    }
</style>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAllCheckbox = document.getElementById('selectAll');
        const studentCheckboxes = document.querySelectorAll('.student-checkbox');
        const selectedCountSpan = document.getElementById('selectedCount');
        const sendBtn = document.getElementById('sendBtn');
        const reminderForm = document.getElementById('reminderForm');

        // Fonction pour mettre à jour le compteur et le bouton
        function updateSelectedCount() {
            const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
            selectedCountSpan.textContent = checkedCount;
            sendBtn.disabled = checkedCount === 0;
        }

        // Select All
        selectAllCheckbox.addEventListener('change', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        // Individual checkboxes
        studentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                // Update "select all" state
                const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                updateSelectedCount();
            });
        });

        // Confirmation avant envoi
        reminderForm.addEventListener('submit', function(e) {
            const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
            if (!confirm(`Êtes-vous sûr de vouloir envoyer un rappel à ${checkedCount} étudiant(s) ?`)) {
                e.preventDefault();
            }
        });

        // Initial count
        updateSelectedCount();
    });
</script>
@endsection