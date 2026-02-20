@extends('layouts.app')

@section('title', 'Créer un Rattrapage')

@section('content')
<div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInDown">
    <div class="card-body p-4">
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('exams.rattrapages.index', $exam) }}"
               class="btn btn-light rounded-circle" style="width:40px;height:40px;padding:0;">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h4 class="mb-1 fw-bold" style="color:var(--primary-color);">
                    <i class="fas fa-retweet me-2"></i>Créer un Rattrapage
                </h4>
                <p class="text-muted mb-0 small">
                    <i class="fas fa-file-alt me-1"></i>Examen original : <strong>{{ $exam->title }}</strong>
                </p>
            </div>
        </div>
    </div>
</div>

<form action="{{ route('exams.rattrapages.store', $exam) }}" method="POST" id="rattrapageForm">
    @csrf
    <div class="row g-4">

        {{-- LEFT: Exam settings --}}
        <div class="col-lg-8">

            {{-- Basic Info --}}
            <div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInUp">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-danger mb-4">
                        <i class="fas fa-info-circle me-2"></i>Informations de l'Examen de Rattrapage
                    </h5>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">
                            Titre <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                               value="{{ old('title', 'Rattrapage – ' . $exam->title) }}" required>
                        @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description</label>
                        <textarea name="description" rows="3"
                                  class="form-control @error('description') is-invalid @enderror"
                                  placeholder="Instructions spécifiques au rattrapage...">{{ old('description') }}</textarea>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Durée (min) <span class="text-danger">*</span></label>
                            <input type="number" name="duration_minutes"
                                   class="form-control @error('duration_minutes') is-invalid @enderror"
                                   value="{{ old('duration_minutes', $exam->duration_minutes) }}"
                                   min="1" max="300" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Score min. (%) <span class="text-danger">*</span></label>
                            <input type="number" name="passing_score"
                                   class="form-control @error('passing_score') is-invalid @enderror"
                                   value="{{ old('passing_score', $exam->passing_score) }}"
                                   min="0" max="100" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-semibold">Tentatives max <span class="text-danger">*</span></label>
                            <select name="max_attempts"
                                    class="form-select form-control @error('max_attempts') is-invalid @enderror" required>
                                @for($i = 1; $i <= 5; $i++)
                                <option value="{{ $i }}" {{ old('max_attempts', 1) == $i ? 'selected' : '' }}>
                                    {{ $i }} tentative{{ $i > 1 ? 's' : '' }}
                                </option>
                                @endfor
                            </select>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar-check me-1 text-success"></i>Disponible dès
                            </label>
                            <input type="datetime-local" name="available_from"
                                   class="form-control"
                                   value="{{ old('available_from') }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fas fa-calendar-times me-1 text-warning"></i>Disponible jusqu'au
                            </label>
                            <input type="datetime-local" name="available_until"
                                   class="form-control"
                                   value="{{ old('available_until') }}">
                        </div>
                    </div>

                    <div class="mb-0">
                        <label class="form-label fw-semibold">Notes internes</label>
                        <textarea name="notes" rows="2" class="form-control"
                                  placeholder="Notes visibles uniquement par les admins/consultants...">{{ old('notes') }}</textarea>
                    </div>
                </div>
            </div>

            {{-- Eligible students preview --}}
            <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp" style="animation-delay:0.1s;">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-bold text-danger mb-0">
                            <i class="fas fa-users me-2"></i>Étudiants Éligibles
                        </h5>
                        <span id="eligibleCount" class="badge bg-danger rounded-pill px-3 fs-6">
                            {{ $eligibleStudents->count() }}
                        </span>
                    </div>

                    @if($eligibleStudents->isEmpty())
                    <div class="alert alert-warning mb-0">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        Aucun étudiant éligible avec les critères actuels.
                    </div>
                    @else
                    <div class="table-responsive" id="eligibleTableWrapper">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll" class="form-check-input">
                                    </th>
                                    <th>Étudiant</th>
                                    <th>Raison</th>
                                    <th>Score original</th>
                                </tr>
                            </thead>
                            <tbody id="eligibleTableBody">
                                @foreach($eligibleStudents as $eligible)
                                <tr data-user-id="{{ $eligible['user']->id }}">
                                    <td></td>{{-- checkbox added by JS --}}
                                    <td>
                                        <div class="fw-semibold">{{ $eligible['user']->name }}</div>
                                        <small class="text-muted">{{ $eligible['user']->email }}</small>
                                    </td>
                                    <td>
                                        @if($eligible['reason'] === 'absent')
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-user-slash me-1"></i>Absent
                                            </span>
                                        @else
                                            <span class="badge bg-danger">
                                                <i class="fas fa-times-circle me-1"></i>Échoué
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($eligible['original_score'] !== null)
                                            <span class="fw-bold text-danger">{{ number_format($eligible['original_score'], 1) }}%</span>
                                        @else
                                            <span class="text-muted">—</span>
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
        </div>

        {{-- RIGHT: Eligibility criteria --}}
        <div class="col-lg-4">
            <div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInUp" style="animation-delay:0.15s;">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-danger mb-4">
                        <i class="fas fa-filter me-2"></i>Critères d'Éligibilité
                    </h5>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input criteria-input" type="checkbox"
                               name="include_absent" id="includeAbsent" value="1"
                               {{ old('include_absent', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="includeAbsent">
                            <i class="fas fa-user-slash me-1 text-secondary"></i>
                            Inclure les absents
                            <br><small class="text-muted">Étudiants sans aucune tentative</small>
                        </label>
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input criteria-input" type="checkbox"
                               name="include_failed" id="includeFailed" value="1"
                               {{ old('include_failed', true) ? 'checked' : '' }}>
                        <label class="form-check-label" for="includeFailed">
                            <i class="fas fa-times-circle me-1 text-danger"></i>
                            Inclure les échoués
                            <br><small class="text-muted">Ont essayé mais le score est trop bas</small>
                        </label>
                    </div>

                    <div id="scoreThresholdWrapper" class="mb-3">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-percentage me-1 text-warning"></i>
                            Score maximum pour être éligible (%)
                        </label>
                        <input type="number" name="score_threshold" id="scoreThreshold"
                               class="form-control criteria-input"
                               value="{{ old('score_threshold', $exam->passing_score) }}"
                               min="0" max="100" step="1">
                        <small class="text-muted">
                            Les étudiants avec un score &lt; cette valeur seront éligibles.
                            Défaut = score minimum de l'examen ({{ $exam->passing_score }}%)
                        </small>
                    </div>

                    <div class="alert alert-info small mb-0">
                        <i class="fas fa-info-circle me-1"></i>
                        Les critères filtrent automatiquement les étudiants éligibles. Vous pouvez en ajouter/retirer manuellement après création.
                    </div>
                </div>
            </div>

            <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp" style="animation-delay:0.2s;">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-danger mb-3">
                        <i class="fas fa-info-circle me-2"></i>Étapes suivantes
                    </h5>
                    <ol class="small text-muted mb-0 ps-3">
                        <li class="mb-2">Créer le rattrapage (ce formulaire)</li>
                        <li class="mb-2">Ajouter les questions via l'éditeur</li>
                        <li class="mb-2">Publier l'examen de rattrapage</li>
                        <li>Les étudiants éligibles voient le rattrapage dans leur espace</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    {{-- Submit --}}
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <a href="{{ route('exams.rattrapages.index', $exam) }}"
                           class="btn btn-secondary rounded-pill px-4">
                            <i class="fas fa-times me-2"></i>Annuler
                        </a>
                        <button type="submit" class="btn btn-danger rounded-pill px-5 shadow-sm">
                            <i class="fas fa-save me-2"></i>Créer le Rattrapage
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
@endsection

@push('styles')
<style>
.form-control:focus, .form-select:focus {
    border-color: var(--primary-color);
    box-shadow: 0 0 0 0.2rem rgba(211, 47, 47, 0.15);
}
.form-check-input:checked {
    background-color: var(--primary-color);
    border-color: var(--primary-color);
}
#eligibleTableWrapper { max-height: 400px; overflow-y: auto; }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Live preview when criteria change ─────────────────────────────────
    const criteriaInputs = document.querySelectorAll('.criteria-input');
    const scoreWrapper   = document.getElementById('scoreThresholdWrapper');
    const includeFailed  = document.getElementById('includeFailed');
    const eligibleCount  = document.getElementById('eligibleCount');
    const tableBody      = document.getElementById('eligibleTableBody');

    function refreshPreview() {
        scoreWrapper.style.display = includeFailed.checked ? 'block' : 'none';

        const params = new URLSearchParams({
            include_absent  : document.getElementById('includeAbsent').checked ? '1' : '0',
            include_failed  : includeFailed.checked ? '1' : '0',
            score_threshold : document.getElementById('scoreThreshold').value,
        });

        fetch('{{ route("exams.rattrapages.preview-eligible", $exam) }}?' + params.toString(), {
            headers: { 'Accept': 'application/json' }
        })
        .then(r => r.json())
        .then(data => {
            eligibleCount.textContent = data.count;

            if (!tableBody) return;
            tableBody.innerHTML = '';

            data.students.forEach(s => {
                const reasonBadge = s.reason === 'absent'
                    ? `<span class="badge bg-secondary"><i class="fas fa-user-slash me-1"></i>Absent</span>`
                    : `<span class="badge bg-danger"><i class="fas fa-times-circle me-1"></i>Échoué</span>`;

                const scoreText = s.original_score !== null
                    ? `<span class="fw-bold text-danger">${parseFloat(s.original_score).toFixed(1)}%</span>`
                    : '<span class="text-muted">—</span>';

                tableBody.insertAdjacentHTML('beforeend', `
                    <tr data-user-id="${s.id}">
                        <td></td>
                        <td>
                            <div class="fw-semibold">${s.name}</div>
                            <small class="text-muted">${s.email}</small>
                        </td>
                        <td>${reasonBadge}</td>
                        <td>${scoreText}</td>
                    </tr>
                `);
            });

            if (data.count === 0) {
                tableBody.innerHTML = `
                    <tr><td colspan="4" class="text-center text-muted py-3">
                        <i class="fas fa-users-slash me-2"></i>Aucun étudiant éligible avec ces critères
                    </td></tr>`;
            }
        })
        .catch(() => {});
    }

    criteriaInputs.forEach(el => el.addEventListener('change', refreshPreview));
    document.getElementById('scoreThreshold')?.addEventListener('input', refreshPreview);

    // Initial toggle
    scoreWrapper.style.display = includeFailed.checked ? 'block' : 'none';

    // ── Form submit guard ──────────────────────────────────────────────────
    document.getElementById('rattrapageForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const from  = this.querySelector('[name="available_from"]').value;
        const until = this.querySelector('[name="available_until"]').value;

        if (from && until && new Date(until) <= new Date(from)) {
            Swal.fire({ icon: 'error', title: 'Erreur', text: 'La date de fin doit être après la date de début!', confirmButtonColor: '#D32F2F' });
            return;
        }

        if (parseInt(eligibleCount.textContent) === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Aucun étudiant éligible',
                text: 'Aucun étudiant ne correspond aux critères actuels. Voulez-vous continuer quand même (vous pourrez en ajouter manuellement) ?',
                showCancelButton: true,
                confirmButtonColor: '#D32F2F',
                cancelButtonText: 'Modifier les critères',
                confirmButtonText: 'Continuer',
            }).then(r => { if (r.isConfirmed) this.submit(); });
            return;
        }

        this.submit();
    });
});
</script>
@endpush