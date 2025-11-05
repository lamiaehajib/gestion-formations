@extends('layouts.app')

@section('title', 'Envoyer Rappels - ' . $formation->title)

@section('content')
<div class="container-fluid py-4">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="{{ route('payment-reminders.index') }}">
                    <i class="fas fa-bell me-1"></i>Rappels de Paiement
                </a>
            </li>
            <li class="breadcrumb-item active">{{ $formation->title }}</li>
        </ol>
    </nav>

    <!-- Header Formation -->
    <div class="card border-0 shadow-lg mb-4" style="background: linear-gradient(135deg, #D32F2F 0%, #C2185B 100%); border-radius: 20px;">
        <div class="card-body p-4">
            <div class="d-flex justify-content-between align-items-center">
                <div class="text-white">
                    <h2 class="mb-2 fw-bold">
                        <i class="fas fa-graduation-cap me-2"></i>{{ $formation->title }}
                    </h2>
                    <p class="mb-0 opacity-90">
                        <span class="badge bg-white text-dark me-2">{{ $formation->category->name ?? 'N/A' }}</span>
                        Prix: {{ number_format($formation->price, 2) }} MAD
                    </p>
                </div>
                <div class="text-white text-end">
                    <div class="fs-1 fw-bold">{{ $students->count() }}</div>
                    <small class="opacity-90">Étudiants avec paiements en attente</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulaire d'envoi -->
    <form method="POST" action="{{ route('payment-reminders.send') }}" id="reminderForm">
        @csrf
        <input type="hidden" name="formation_id" value="{{ $formation->id }}">
        
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
                               value="{{ date('Y-m-d', strtotime('+7 days')) }}"
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
                            <!-- Header -->
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
                                    <p class="text-muted mb-0 small">
                                        <i class="fas fa-phone me-1"></i>{{ $student->phone }}
                                    </p>
                                </div>
                                
                                @if($student->has_active_reminder)
                                    <span class="badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>Rappel actif
                                    </span>
                                @else
                                    <span class="badge bg-secondary">
                                        <i class="fas fa-clock me-1"></i>Aucun rappel
                                    </span>
                                @endif
                            </div>

                            <!-- Info inscription -->
                            @if($student->inscription)
                                <div class="p-3 rounded mb-3" style="background-color: #fef2f2;">
                                    <div class="row">
                                        <div class="col-6">
                                            <small class="text-muted d-block">Montant total</small>
                                            <strong class="text-dark">{{ number_format($student->inscription->total_amount, 2) }} MAD</strong>
                                        </div>
                                        <div class="col-6 text-end">
                                            <small class="text-muted d-block">Reste à payer</small>
                                            <strong class="text-danger fs-5">{{ number_format($student->remaining_amount, 2) }} MAD</strong>
                                        </div>
                                    </div>
                                    
                                    <!-- Barre de progression -->
                                    @php
                                        $progress = ($student->inscription->paid_amount );
                                    @endphp
                                    <div class="mt-2">
                                        <div class="d-flex justify-content-between mb-1">
                                            <small class="text-muted">Payé</small>
                                            <small class="fw-bold">{{ number_format($progress) }}MAD</small>
                                        </div>
                                        <div class="progress" style="height: 6px;">
                                            <div class="progress-bar" 
                                                 style="width: {{ $progress }}%; background: linear-gradient(90deg, #D32F2F, #C2185B);">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center py-5">
                            <i class="fas fa-check-circle fa-4x text-success mb-3"></i>
                            <h4 class="text-muted">Aucun étudiant en attente</h4>
                            <p class="text-muted">
                                Tous les étudiants de cette formation sont à jour dans leurs paiements!
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

        function updateSelectedCount() {
            const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
            selectedCountSpan.textContent = checkedCount;
            sendBtn.disabled = checkedCount === 0;
        }

        selectAllCheckbox.addEventListener('change', function() {
            studentCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateSelectedCount();
        });

        studentCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', function() {
                const allChecked = Array.from(studentCheckboxes).every(cb => cb.checked);
                selectAllCheckbox.checked = allChecked;
                updateSelectedCount();
            });
        });

        reminderForm.addEventListener('submit', function(e) {
            const checkedCount = document.querySelectorAll('.student-checkbox:checked').length;
            if (!confirm(`Êtes-vous sûr de vouloir envoyer un rappel à ${checkedCount} étudiant(s) pour cette formation ?`)) {
                e.preventDefault();
            }
        });

        updateSelectedCount();
    });
</script>
@endsection