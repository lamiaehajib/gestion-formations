@extends('layouts.app')

@section('title', 'Détails du Rattrapage')

@section('content')
{{-- Header --}}
<div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInDown">
    <div class="card-body p-4">
        <div class="d-flex align-items-center justify-content-between flex-wrap gap-3">
            <div class="d-flex align-items-center gap-3">
                <a href="{{ route('exams.rattrapages.index', $exam) }}"
                   class="btn btn-red rounded-circle" style="width:40px;height:40px;padding:0; background-color: red;">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <div>
                    <h4 class="mb-1 fw-bold" style="color:var(--primary-color);">
                        <i class="fas fa-retweet me-2"></i>{{ $rattrapageExam->title }}
                    </h4>
                    <p class="text-muted mb-0 small">
                        <i class="fas fa-link me-1"></i>Rattrapage de : <strong>{{ $exam->title }}</strong>
                    </p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('exams.edit', $rattrapageExam) }}"
                   class="btn btn-warning rounded-pill px-3">
                    <i class="fas fa-question-circle me-1"></i>Gérer les Questions
                </a>
                @if($rattrapageExam->status === 'draft')
                <form action="{{ route('exams.update', $rattrapageExam) }}" method="POST">
                    @csrf @method('PUT')
                    @foreach(['module_id','title','description','duration_minutes','passing_score','max_attempts','shuffle_questions','show_results_immediately','show_correct_answers','available_from','available_until'] as $field)
                    <input type="hidden" name="{{ $field }}" value="{{ $rattrapageExam->$field }}">
                    @endforeach
                    <input type="hidden" name="status" value="published">
                    <button type="submit" class="btn btn-success rounded-pill px-3">
                        <i class="fas fa-check-circle me-1"></i>Publier
                    </button>
                </form>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Stats row --}}
<div class="row g-3 mb-4">
    @php
        $totalStudents   = $students->count();
        $attempted       = $students->filter(fn($s) => $s->attempt)->count();
        $passed          = $students->filter(fn($s) => $s->attempt && $s->attempt->passed)->count();
        $questionCount   = $rattrapageExam->questions->count();
    @endphp
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-4 shadow-hover text-center p-3">
            <div class="fw-bold fs-4 text-danger">{{ $totalStudents }}</div>
            <small class="text-muted">Étudiants éligibles</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-4 shadow-hover text-center p-3">
            <div class="fw-bold fs-4 text-info">{{ $attempted }}</div>
            <small class="text-muted">Ont passé</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-4 shadow-hover text-center p-3">
            <div class="fw-bold fs-4 text-success">{{ $passed }}</div>
            <small class="text-muted">Réussi</small>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card border-0 rounded-4 shadow-hover text-center p-3">
            <div class="fw-bold fs-4 text-warning">{{ $questionCount }}</div>
            <small class="text-muted">Questions</small>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Students list --}}
    <div class="col-lg-8">
        <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-bold text-danger mb-0">
                        <i class="fas fa-users me-2"></i>Liste des Étudiants
                    </h5>
                    {{-- Add student manually --}}
                    <button type="button" class="btn btn-sm btn-danger rounded-pill px-3"
                            data-bs-toggle="modal" data-bs-target="#addStudentModal">
                        <i class="fas fa-user-plus me-1"></i>Ajouter
                    </button>
                </div>

                @if($students->isEmpty())
                <div class="text-center text-muted py-4">
                    <i class="fas fa-users-slash fa-2x mb-2"></i>
                    <p>Aucun étudiant dans ce rattrapage.</p>
                </div>
                @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Étudiant</th>
                                <th>Éligibilité</th>
                                <th>Score original</th>
                                <th>Statut rattrapage</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $student->user->name }}</div>
                                    <small class="text-muted">{{ $student->user->email }}</small>
                                </td>
                                <td>
                                    <span class="badge {{ $student->getReasonBadgeClass() }}">
                                        {{ $student->getReasonLabel() }}
                                    </span>
                                </td>
                                <td>
                                    @if($student->original_score !== null)
                                        <span class="fw-bold text-danger">{{ number_format($student->original_score, 1) }}%</span>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    @if($student->attempt)
                                        @php
                                            $a = $student->attempt;
                                            $badgeCl = match($a->status) {
                                                'graded'      => $a->passed ? 'bg-success' : 'bg-danger',
                                                'submitted'   => 'bg-warning text-dark',
                                                'in_progress' => 'bg-info text-dark',
                                                'timed_out'   => 'bg-secondary',
                                                default       => 'bg-secondary',
                                            };
                                            $label = match($a->status) {
                                                'graded'      => $a->passed ? 'Réussi (' . number_format($a->score,1) . '%)' : 'Échoué (' . number_format($a->score,1) . '%)',
                                                'submitted'   => 'Soumis',
                                                'in_progress' => 'En cours',
                                                'timed_out'   => 'Temps écoulé',
                                                default       => $a->status,
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeCl }}">{{ $label }}</span>
                                    @else
                                        <span class="badge bg-light text-muted border">Non démarré</span>
                                    @endif
                                </td>
                                <td>
                                    <button type="button"
                                            class="btn btn-sm btn-danger rounded-circle remove-student-btn"
                                            data-user-id="{{ $student->user_id }}"
                                            data-user-name="{{ $student->user->name }}"
                                            title="Retirer">
                                        <i class="fas fa-times"></i>
                                    </button>
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

    {{-- Right: exam info --}}
    <div class="col-lg-4">
        <div class="card border-0 rounded-4 shadow-hover mb-4 animate__animated animate__fadeInUp">
            <div class="card-body p-4">
                <h6 class="fw-bold text-danger mb-3">
                    <i class="fas fa-cog me-2"></i>Paramètres du rattrapage
                </h6>
                <dl class="row small mb-0">
                    <dt class="col-6 text-muted">Durée</dt>
                    <dd class="col-6">{{ $rattrapageExam->duration_minutes }} min</dd>

                    <dt class="col-6 text-muted">Score min.</dt>
                    <dd class="col-6">{{ $rattrapageExam->passing_score }}%</dd>

                    <dt class="col-6 text-muted">Tentatives</dt>
                    <dd class="col-6">{{ $rattrapageExam->max_attempts }}</dd>

                    <dt class="col-6 text-muted">Statut</dt>
                    <dd class="col-6">
                        @php
                            $sc = match($rattrapageExam->status) {
                                'published' => 'badge bg-success',
                                'draft'     => 'badge bg-secondary',
                                default     => 'badge bg-dark',
                            };
                            $sl = match($rattrapageExam->status) {
                                'published' => 'Publié',
                                'draft'     => 'Brouillon',
                                default     => $rattrapageExam->status,
                            };
                        @endphp
                        <span class="{{ $sc }}">{{ $sl }}</span>
                    </dd>

                    @if($rattrapageExam->available_from)
                    <dt class="col-6 text-muted">Dès</dt>
                    <dd class="col-6">{{ $rattrapageExam->available_from->format('d/m/Y H:i') }}</dd>
                    @endif

                    @if($rattrapageExam->available_until)
                    <dt class="col-6 text-muted">Jusqu'au</dt>
                    <dd class="col-6">{{ $rattrapageExam->available_until->format('d/m/Y H:i') }}</dd>
                    @endif
                </dl>
            </div>
        </div>

        <div class="card border-0 rounded-4 shadow-hover animate__animated animate__fadeInUp">
            <div class="card-body p-4">
                <h6 class="fw-bold text-danger mb-3">
                    <i class="fas fa-filter me-2"></i>Critères utilisés
                </h6>
                <div class="d-flex flex-column gap-2 small">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $rattrapage->include_absent ? 'bg-secondary' : 'bg-light text-muted border' }}">
                            <i class="fas fa-user-slash me-1"></i>Absents
                        </span>
                        {{ $rattrapage->include_absent ? 'Inclus' : 'Exclus' }}
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge {{ $rattrapage->include_failed ? 'bg-danger' : 'bg-light text-muted border' }}">
                            <i class="fas fa-times-circle me-1"></i>Échoués
                        </span>
                        {{ $rattrapage->include_failed ? 'Inclus (< ' . $rattrapage->score_threshold . '%)' : 'Exclus' }}
                    </div>
                </div>
                @if($rattrapage->notes)
                <hr class="my-3">
                <p class="text-muted small mb-0">
                    <i class="fas fa-sticky-note me-1"></i>{{ $rattrapage->notes }}
                </p>
                @endif
            </div>
        </div>
    </div>
</div>

{{-- Add Student Modal --}}
<div class="modal fade" id="addStudentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-user-plus me-2"></i>Ajouter un Étudiant Manuellement
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Email ou nom de l'étudiant</label>
                    <input type="text" id="studentSearchInput" class="form-control"
                           placeholder="Tapez pour rechercher...">
                    <div id="studentSearchResults" class="list-group mt-2" style="max-height:200px;overflow-y:auto;"></div>
                    <input type="hidden" id="selectedStudentId">
                </div>
                <div id="selectedStudentInfo" class="alert alert-success d-none">
                    <i class="fas fa-user-check me-2"></i>
                    <span id="selectedStudentName"></span>
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Annuler</button>
                <button type="button" id="confirmAddStudent" class="btn btn-danger rounded-pill px-4" disabled>
                    <i class="fas fa-user-plus me-1"></i>Ajouter
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Remove student ─────────────────────────────────────────────────────
    document.querySelectorAll('.remove-student-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId   = this.dataset.userId;
            const userName = this.dataset.userName;

            Swal.fire({
                title: `Retirer ${userName} ?`,
                text: 'L\'étudiant ne pourra plus accéder à ce rattrapage.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#D32F2F',
                cancelButtonText: 'Annuler',
                confirmButtonText: 'Retirer',
            }).then(result => {
                if (!result.isConfirmed) return;

                fetch(`{{ route("exams.rattrapages.students.remove", [$exam, $rattrapage, ':uid']) }}`.replace(':uid', userId), {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json',
                    }
                })
                .then(r => r.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({ icon: 'success', title: 'Retiré!', timer: 1500, showConfirmButton: false })
                            .then(() => location.reload());
                    }
                });
            });
        });
    });

    // ── Add student search ─────────────────────────────────────────────────
    let searchTimeout;
    const searchInput   = document.getElementById('studentSearchInput');
    const searchResults = document.getElementById('studentSearchResults');
    const selectedId    = document.getElementById('selectedStudentId');
    const selectedInfo  = document.getElementById('selectedStudentInfo');
    const selectedName  = document.getElementById('selectedStudentName');
    const confirmBtn    = document.getElementById('confirmAddStudent');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const q = this.value.trim();
        if (q.length < 2) { searchResults.innerHTML = ''; return; }

        searchTimeout = setTimeout(() => {
            fetch(`/api/users/search?q=${encodeURIComponent(q)}&role=Etudiant`, {
                headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            })
            .then(r => r.json())
            .then(data => {
                searchResults.innerHTML = '';
                (data.users || []).forEach(user => {
                    const item = document.createElement('button');
                    item.type = 'button';
                    item.className = 'list-group-item list-group-item-action';
                    item.innerHTML = `<strong>${user.name}</strong> <small class="text-muted">${user.email}</small>`;
                    item.addEventListener('click', () => {
                        selectedId.value    = user.id;
                        selectedName.textContent = user.name;
                        selectedInfo.classList.remove('d-none');
                        confirmBtn.disabled = false;
                        searchResults.innerHTML = '';
                        searchInput.value = user.name;
                    });
                    searchResults.appendChild(item);
                });
            });
        }, 300);
    });

    confirmBtn.addEventListener('click', function() {
        const userId = selectedId.value;
        if (!userId) return;

        this.disabled = true;
        this.innerHTML = '<i class="fas fa-spinner fa-spin me-1"></i>Ajout...';

        fetch('{{ route("exams.rattrapages.students.add", [$exam, $rattrapage]) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json',
            },
            body: JSON.stringify({ user_id: userId })
        })
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                bootstrap.Modal.getInstance(document.getElementById('addStudentModal')).hide();
                Swal.fire({ icon: 'success', title: 'Ajouté!', timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'error', title: 'Erreur', text: data.message, confirmButtonColor: '#D32F2F' });
                confirmBtn.disabled = false;
                confirmBtn.innerHTML = '<i class="fas fa-user-plus me-1"></i>Ajouter';
            }
        });
    });

    // Reset modal on close
    document.getElementById('addStudentModal').addEventListener('hidden.bs.modal', function() {
        searchInput.value = '';
        selectedId.value  = '';
        searchResults.innerHTML = '';
        selectedInfo.classList.add('d-none');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-user-plus me-1"></i>Ajouter';
    });
});
</script>
@endpush