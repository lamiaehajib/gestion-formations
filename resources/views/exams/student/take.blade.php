@extends('layouts.app')

@section('title', 'Passer l\'Examen')

@push('styles')
<style>
    /* ══════════════════════════════════════════════════════════
       EXAM MODE
    ══════════════════════════════════════════════════════════ */
    body.exam-active-mode {
        overflow: hidden;
        background: #f0f2f5 !important;
    }
    body.exam-active-mode nav,
    body.exam-active-mode header,
    body.exam-active-mode .navbar,
    body.exam-active-mode .sidebar,
    body.exam-active-mode aside,
    body.exam-active-mode footer,
    body.exam-active-mode .topbar,
    body.exam-active-mode [class*="navbar"],
    body.exam-active-mode [class*="sidebar"],
    body.exam-active-mode [class*="top-bar"],
    body.exam-active-mode [class*="header"]:not(#examHeader) {
        display: none !important;
    }
    body.exam-active-mode #examWrapper {
        position: fixed !important;
        top: 0 !important; left: 0 !important;
        right: 0 !important; bottom: 0 !important;
        z-index: 9999 !important;
        background: #f0f2f5 !important;
        overflow-y: auto !important;
        padding: 1rem !important;
    }

    .swal2-container { z-index: 999999999 !important; }

    #fsOverlay {
        display: none;
        position: fixed; inset: 0;
        background: #C62828;
        z-index: 99999999;
        flex-direction: column;
        align-items: center; justify-content: center;
        color: white; text-align: center;
    }
    #fsOverlay.show { display: flex; }
    #securityWarningModal { z-index: 99999 !important; }

    .no-select { -webkit-user-select: none; -moz-user-select: none; user-select: none; }
    img { pointer-events: none; -webkit-user-drag: none; }

    .list-group-item:hover {
        background-color: rgba(211,47,47,.05);
        border-color: #D32F2F !important;
    }
    .question-nav-btn { transition: all .25s; }
    .question-nav-btn:hover {
        background: rgba(211,47,47,.1);
        border-color: #D32F2F;
        color: #D32F2F !important;
    }
    #reenterFsBtn:hover {
        background: white !important;
        color: #b71c1c !important;
        transform: translateY(-3px);
        box-shadow: 0 8px 25px rgba(255,255,255,0.3);
    }
    .question-navigator { display: block !important; }
    .question-nav-btn {
        display: flex !important;
        visibility: visible !important;
        opacity: 1 !important;
    }
    .question-nav-btn.answered .answer-indicator { color: #28a745 !important; }

    /* ══════════════════════════════════════════════════════════
       CUSTOM CURSOR
    ══════════════════════════════════════════════════════════ */
    #examCursor {
        width: 18px;
        height: 18px;
        background: #D32F2F;
        border: 2.5px solid #fff;
        border-radius: 50%;
        position: fixed;
        pointer-events: none;
        z-index: 2147483647;
        transform: translate(-50%, -50%);
        display: none;
        box-shadow: 0 0 0 2px rgba(211,47,47,0.4), 0 2px 8px rgba(0,0,0,0.5);
    }

    /* ══════════════════════════════════════════════════════════
       CURSOR FIX - swal-open class يغلب على cursor:none
    ══════════════════════════════════════════════════════════ */
    html.swal-open,
    html.swal-open body,
    html.swal-open body * {
        cursor: auto !important;
    }
    html.swal-open .swal2-confirm,
    html.swal-open .swal2-cancel,
    html.swal-open .swal2-deny {
        cursor: pointer !important;
    }
</style>
@endpush

@section('content')

{{-- Custom Cursor --}}
<div id="examCursor"></div>

{{-- SECURITY MODAL --}}
<div class="modal fade" id="securityWarningModal"
     data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-shield-alt me-2"></i>Règles de l'Examen
                </h5>
            </div>
            <div class="modal-body p-4">
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention !</strong> Cet examen est surveillé
                </div>
                <h6 class="fw-bold mb-3">Restrictions appliquées :</h6>
                <ul class="mb-3">
                    <li class="mb-2">🔒 <strong>Mode plein écran obligatoire</strong></li>
                    <li class="mb-2">🚫 <strong>Copier-coller désactivé</strong></li>
                    <li class="mb-2">👁️ <strong>Changements d'onglet détectés</strong></li>
                    <li class="mb-2">⏰ <strong>Temps limité</strong> - Le chronomètre ne s'arrête pas</li>
                    <li class="mb-2">📵 <strong>F12/Console bloqués</strong></li>
                    <li class="mb-2">🖱️ <strong>Curseur verrouillé</strong> - La souris reste dans cette fenêtre</li>
                </ul>
                <div class="alert alert-danger mb-0">
                    <i class="fas fa-ban me-2"></i>
                    <strong>Avertissement :</strong> Toute tentative de triche sera enregistrée.
                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-danger btn-lg w-100 rounded-pill" id="acceptSecurityBtn">
                    <i class="fas fa-check-circle me-2"></i>J'ai compris, commencer l'examen
                </button>
            </div>
        </div>
    </div>
</div>

{{-- FULLSCREEN EXIT OVERLAY --}}
<div id="fsOverlay">
    <i class="fas fa-expand-arrows-alt fa-5x mb-4" style="opacity:.8;"></i>
    <h2 class="fw-bold mb-3">Mode plein écran requis !</h2>
    <p class="mb-4 fs-5" style="opacity:.8;">Vous devez rester en mode plein écran pendant l'examen.</p>
    <button class="btn btn-outline-light btn-lg rounded-pill px-5 fw-bold" id="reenterFsBtn">
        <i class="fas fa-expand me-2"></i>Revenir en plein écran
    </button>
</div>

{{-- EXAM WRAPPER --}}
<div id="examWrapper">

    <div id="examHeader" class="card border-0 rounded-4 shadow-sm mb-4 position-sticky top-0"
         style="z-index:100; background:white;">
        <div class="card-body p-3">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <h5 class="mb-0 fw-bold text-danger">
                        <i class="fas fa-clipboard-check me-2"></i>{{ $exam->title }}
                    </h5>
                </div>
                <div class="col-md-4 text-center">
                    <div id="timerDisplay" class="fs-3 fw-bold text-danger">
                        <i class="fas fa-clock me-2"></i><span id="timerText">--:--</span>
                    </div>
                    <small class="text-muted">Temps Restant</small>
                </div>
                <div class="col-md-4 text-end">
                    <span class="badge bg-secondary me-2">
                        Question <span id="currentQuestionNumber">1</span> / {{ $exam->questions->count() }}
                    </span>
                    <button type="button" class="btn btn-danger rounded-pill px-4" id="submitExamBtn">
                        <i class="fas fa-paper-plane me-2"></i>Soumettre l'Examen
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-9">
            <div class="card border-0 rounded-4 shadow-sm">
                <div class="card-body p-4 no-select">

                    @foreach($exam->questions as $index => $question)
                    <div class="question-container"
                         data-question-id="{{ $question->id }}"
                         data-question-index="{{ $index }}"
                         data-question-type="{{ $question->type }}"
                         style="display:{{ $index === 0 ? 'block' : 'none' }};">

                        <div class="d-flex justify-content-between align-items-start mb-4">
                            <div>
                                <span class="badge bg-danger rounded-pill mb-2">Question {{ $index + 1 }}</span>
                                <span class="badge bg-success rounded-pill mb-2">
                                    {{ $question->points }} {{ $question->points == 1 ? 'point' : 'points' }}
                                </span>
                                <span class="badge bg-secondary rounded-pill mb-2">{{ $question->getTypeLabel() }}</span>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h5 class="fw-semibold">{!! nl2br(e($question->question_text)) !!}</h5>
                        </div>

                        @if($question->question_image)
                        <div class="mb-4">
                            <img src="{{ asset('storage/' . $question->question_image) }}"
                                 class="img-fluid rounded shadow-sm no-select"
                                 style="max-height:400px; pointer-events:none;"
                                 alt="Question Image" ondragstart="return false;">
                        </div>
                        @endif

                        <div class="answer-area mb-4">

                            @if($question->type == 'qcm')
                                <div class="list-group">
                                    @foreach($question->formatted_options as $option)
                                    <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3 border rounded-3 mb-2">
                                        <input type="radio" name="question_{{ $question->id }}"
                                               value="{{ $option['text'] }}"
                                               class="form-check-input flex-shrink-0"
                                               data-question-id="{{ $question->id }}">
                                        <span class="flex-grow-1">{{ $option['text'] }}</span>
                                    </label>
                                    @endforeach
                                </div>

                            @elseif($question->type == 'checkbox')
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle me-2"></i>Plusieurs réponses peuvent être correctes
                                </div>
                                <div class="list-group">
                                    @foreach($question->formatted_options as $option)
                                    <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3 border rounded-3 mb-2">
                                        <input type="checkbox" name="question_{{ $question->id }}[]"
                                               value="{{ $option['text'] }}"
                                               class="form-check-input flex-shrink-0"
                                               data-question-id="{{ $question->id }}">
                                        <span class="flex-grow-1">{{ $option['text'] }}</span>
                                    </label>
                                    @endforeach
                                </div>

                            @elseif($question->type == 'true_false')
                                <div class="list-group">
                                    <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3 border rounded-3 mb-2">
                                        <input type="radio" name="question_{{ $question->id }}"
                                               value="true" class="form-check-input flex-shrink-0"
                                               data-question-id="{{ $question->id }}">
                                        <span class="flex-grow-1 fw-semibold">
                                            <i class="fas fa-check-circle text-success me-2"></i>Vrai
                                        </span>
                                    </label>
                                    <label class="list-group-item list-group-item-action d-flex align-items-center gap-3 p-3 border rounded-3 mb-2">
                                        <input type="radio" name="question_{{ $question->id }}"
                                               value="false" class="form-check-input flex-shrink-0"
                                               data-question-id="{{ $question->id }}">
                                        <span class="flex-grow-1 fw-semibold">
                                            <i class="fas fa-times-circle text-danger me-2"></i>Faux
                                        </span>
                                    </label>
                                </div>

                            @elseif($question->type == 'text')
                                <input type="text" name="question_{{ $question->id }}"
                                       class="form-control form-control-lg"
                                       placeholder="Entrez votre réponse..."
                                       data-question-id="{{ $question->id }}" autocomplete="off">

                            @elseif($question->type == 'numeric')
                                @php $numericData = $question->getNumericData(); @endphp
                                <div class="mb-3">
                                    <div class="input-group input-group-lg">
                                        <input type="number" name="question_{{ $question->id }}"
                                               class="form-control" placeholder="Entrez un nombre..."
                                               step="any" data-question-id="{{ $question->id }}"
                                               autocomplete="off">
                                        @if($numericData && $numericData['unit'])
                                            <span class="input-group-text">{{ $numericData['unit'] }}</span>
                                        @endif
                                    </div>
                                    @if($numericData && $numericData['tolerance'] > 0)
                                        <small class="text-muted mt-2 d-block">
                                            <i class="fas fa-info-circle me-1"></i>
                                            Marge d'erreur de ±{{ $numericData['tolerance'] }} acceptée
                                        </small>
                                    @endif
                                </div>

                            @elseif($question->type == 'fill_blanks')
                                @php
                                    $blanksData = $question->getBlanksData();
                                    $blankCount = $blanksData['blank_count'];
                                @endphp
                                <div class="alert alert-info mb-3">
                                    <i class="fas fa-info-circle me-2"></i>
                                    Remplissez les {{ $blankCount }} blanc(s) ci-dessous
                                </div>
                                <div class="blanks-container" data-question-id="{{ $question->id }}">
                                    @for($i = 0; $i < $blankCount; $i++)
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold">
                                            <i class="fas fa-pencil-alt me-1 text-danger"></i>Blanc {{ $i + 1 }}
                                        </label>
                                        <input type="text"
                                               name="question_{{ $question->id }}_blank_{{ $i }}"
                                               class="form-control blank-input"
                                               placeholder="Votre réponse pour le blanc {{ $i + 1 }}"
                                               data-blank-index="{{ $i }}"
                                               data-question-id="{{ $question->id }}"
                                               autocomplete="off">
                                    </div>
                                    @endfor
                                </div>

                            @elseif($question->type == 'matching')
                                @php
                                    $matchingData = $question->getMatchingData();
                                    $leftItems  = $matchingData['left_items']  ?? [];
                                    $rightItems = $matchingData['right_items'] ?? [];
                                @endphp
                                @if(!empty($leftItems) && !empty($rightItems))
                                    <div class="alert alert-info mb-3">
                                        <i class="fas fa-info-circle me-2"></i>
                                        Associez chaque élément de gauche avec celui de droite
                                    </div>
                                    <div class="matching-container" data-question-id="{{ $question->id }}">
                                        @foreach($leftItems as $li => $leftItem)
                                        <div class="row mb-3 align-items-center">
                                            <div class="col-md-5">
                                                <div class="p-3 bg-light rounded-3 border">
                                                    <strong>{{ $leftItem }}</strong>
                                                </div>
                                            </div>
                                            <div class="col-md-2 text-center">
                                                <i class="fas fa-arrows-alt-h text-danger"></i>
                                            </div>
                                            <div class="col-md-5">
                                                <select class="form-select matching-select"
                                                        name="question_{{ $question->id }}_match_{{ $li }}"
                                                        data-left-item="{{ $leftItem }}"
                                                        data-question-id="{{ $question->id }}">
                                                    <option value="">-- Sélectionnez --</option>
                                                    @foreach($rightItems as $rightItem)
                                                        <option value="{{ $rightItem }}">{{ $rightItem }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="alert alert-warning">
                                        <i class="fas fa-exclamation-triangle me-2"></i>
                                        Erreur: Données de correspondance manquantes
                                    </div>
                                @endif

                            @elseif($question->type == 'ordering')
    @php
        $orderingData  = $question->getOrderingData();
        $shuffledItems = $orderingData['shuffled_items'] ?? [];
    @endphp
    @if(!empty($shuffledItems))
        <div class="alert alert-info mb-3">
            <i class="fas fa-info-circle me-2"></i>
            Cliquez sur un élément pour le <strong>sélectionner</strong>, puis cliquez sur un autre pour les <strong>échanger</strong>
        </div>
        <div class="ordering-container"
             id="ordering_{{ $question->id }}"
             data-question-id="{{ $question->id }}">
            @foreach($shuffledItems as $si => $item)
            <div class="ordering-item p-3 mb-2 bg-light border rounded-3"
                 style="cursor:pointer; transition: background 0.15s, border-color 0.15s;"
                 data-item="{{ $item }}">
                <div class="d-flex align-items-center gap-3">
                    <i class="fas fa-arrows-alt-v text-muted"></i>
                    <span class="order-number badge bg-danger rounded-circle me-2"
                          style="width:30px;height:30px;line-height:20px;">{{ $si+1 }}</span>
                    <span class="flex-grow-1">{{ $item }}</span>
                </div>
            </div>
            @endforeach
        </div>
        <small class="text-muted">
            <i class="fas fa-mouse-pointer me-1"></i>
            Cliquez pour sélectionner (bleu), puis cliquez sur la position cible pour échanger
        </small>
    @else
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i>
            Erreur: Données de tri manquantes
        </div>
    @endif

                            @elseif($question->type == 'essay')
                                <textarea name="question_{{ $question->id }}" rows="8"
                                          class="form-control"
                                          placeholder="Rédigez votre réponse ici..."
                                          data-question-id="{{ $question->id }}"></textarea>
                                <small class="text-muted mt-2 d-block">
                                    <i class="fas fa-info-circle me-1"></i>
                                    Cette question nécessitera une correction manuelle
                                </small>
                            @endif

                        </div>

                        <div class="d-flex justify-content-between pt-4 border-top">
                            <button type="button"
                                    class="btn btn-secondary rounded-pill px-4 prev-question-btn"
                                    style="{{ $index === 0 ? 'visibility:hidden;' : '' }}">
                                <i class="fas fa-arrow-left me-2"></i>Précédent
                            </button>
                            @if($index < $exam->questions->count() - 1)
                                <button type="button" class="btn btn-danger rounded-pill px-4 next-question-btn">
                                    Suivant<i class="fas fa-arrow-right ms-2"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-success rounded-pill px-4" id="finalSubmitBtn">
                                    <i class="fas fa-check-circle me-2"></i>Terminer & Soumettre
                                </button>
                            @endif
                        </div>

                    </div>
                    @endforeach

                </div>
            </div>
        </div>

        <div class="col-lg-3">
            <div class="card border-0 rounded-4 shadow-sm position-sticky" style="top:100px;">
                <div class="card-body p-3">
                    <h6 class="fw-bold mb-3 text-danger">
                        <i class="fas fa-list-ol me-2"></i>Navigation des Questions
                    </h6>
                    <div class="question-navigator">
                        @foreach($exam->questions as $index => $question)
                        <button type="button"
                                class="btn btn-secondary btn-sm w-100 mb-2 text-start question-nav-btn"
                                data-question-index="{{ $index }}"
                                data-question-id="{{ $question->id }}">
                            <i class="fas fa-circle me-2 answer-indicator" style="font-size:.6rem;color:#ccc;"></i>
                            Question {{ $index + 1 }}
                        </button>
                        @endforeach
                    </div>
                    <div class="mt-4 pt-3 border-top small">
                        <div class="d-flex align-items-center gap-2 mb-2">
                            <i class="fas fa-circle text-success" style="font-size:.6rem;"></i><span>Répondue</span>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fas fa-circle text-muted" style="font-size:.6rem;"></i><span>Non répondue</span>
                        </div>
                    </div>
                    <div class="alert alert-info mt-3 mb-0 small">
                        <i class="fas fa-save me-1"></i>Vos réponses sont sauvegardées automatiquement
                    </div>
                    <div id="tabSwitchWarning" class="alert alert-warning mt-3 mb-0 small" style="display:none;">
                        <i class="fas fa-exclamation-triangle me-1"></i>
                        Changements d'onglet détectés: <span id="tabSwitchCount">0</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<form id="submitExamForm" action="{{ route('exams.submit', $attempt) }}" method="POST" style="display:none;">
    @csrf
</form>

@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    const attemptId      = {{ $attempt->id }};
    const remainingTime  = {{ $attempt->getRemainingTime() }};
    const totalQuestions = {{ $exam->questions->count() }};
    let currentQuestionIndex = 0;
    let tabSwitchCount       = 0;
    let isExamStarted        = false;

    const csrfToken = () => document.querySelector('meta[name="csrf-token"]').content;

    // ════════════════════════════════════════════════════════════════════════
    // CUSTOM CURSOR
    // ════════════════════════════════════════════════════════════════════════
    const examCursor = document.getElementById('examCursor');
    let cx = window.innerWidth  / 2;
    let cy = window.innerHeight / 2;
    let useFreeMouse = false;

    // Style tag ديناميكي للـ cursor:none
    const cursorHideStyle = document.createElement('style');
    cursorHideStyle.id = 'cursorHideStyle';
    cursorHideStyle.textContent = 'body, body * { cursor: none !important; }';

    function hideCursorForExam() {
        document.documentElement.classList.remove('swal-open');
        if (!document.getElementById('cursorHideStyle')) {
            document.head.appendChild(cursorHideStyle);
        }
    }

    function showRealCursor() {
        const s = document.getElementById('cursorHideStyle');
        if (s) s.remove();
        document.documentElement.classList.add('swal-open');
    }

    document.addEventListener('mousemove', function (e) {
        if (!isExamStarted) return;

        if (!useFreeMouse && document.pointerLockElement) {
            cx += e.movementX;
            cy += e.movementY;
            cx = Math.max(1, Math.min(window.innerWidth  - 1, cx));
            cy = Math.max(1, Math.min(window.innerHeight - 1, cy));
        } else {
            cx = e.clientX;
            cy = e.clientY;
        }

        examCursor.style.left = cx + 'px';
        examCursor.style.top  = cy + 'px';
    });

    // ════════════════════════════════════════════════════════════════════════
    // CLICK FORWARDING مع Pointer Lock
    // ════════════════════════════════════════════════════════════════════════
    document.addEventListener('mousedown', function (e) {
        if (!isExamStarted || !document.pointerLockElement) return;

        examCursor.style.display = 'none';
        const el = document.elementFromPoint(cx, cy);
        examCursor.style.display = 'block';

        if (!el) return;

        // ══ SELECT FIX: khrej mn pointer lock bach dropdown ixdem ══
        const selectEl = el.closest('select') || (el.tagName === 'SELECT' ? el : null);
        if (selectEl) {
            // khrej mn pointer lock w walli cursor 3adi
            document.exitPointerLock();
            useFreeMouse = true;
            examCursor.style.display = 'none';
            showRealCursor();

            setTimeout(() => {
                selectEl.focus();
                selectEl.click();

                // b3d ma ybdl user l-valeur → rj3 l pointer lock
                selectEl.addEventListener('change', function restoreLock() {
                    selectEl.removeEventListener('change', restoreLock);
                    setTimeout(() => {
                        hideCursorForExam();
                        useFreeMouse = false;
                        activatePointerLock().then(() => {
                            examCursor.style.left = cx + 'px';
                            examCursor.style.top  = cy + 'px';
                            examCursor.style.display = 'block';
                        }).catch(() => {
                            examCursor.style.left = cx + 'px';
                            examCursor.style.top  = cy + 'px';
                            examCursor.style.display = 'block';
                        });
                    }, 300);
                }, { once: true });

                // Fallback: ila ma badlch walu b3d 8 tawani, rj3 pointer lock
                setTimeout(() => {
                    if (!document.pointerLockElement && useFreeMouse) {
                        hideCursorForExam();
                        useFreeMouse = false;
                        activatePointerLock().then(() => {
                            examCursor.style.left = cx + 'px';
                            examCursor.style.top  = cy + 'px';
                            examCursor.style.display = 'block';
                        });
                    }
                }, 8000);
            }, 50);
            return;
        }

        // ══ INPUT / TEXTAREA ══
        if (el.tagName === 'INPUT' || el.tagName === 'TEXTAREA') {
            el.focus();
        }

        const clickTarget = el.closest('label') || el.closest('button') || el;

        if (clickTarget !== document.documentElement && clickTarget !== document.body) {
            const syntheticClick = new MouseEvent('click', {
                bubbles: true,
                cancelable: true,
                view: window,
                clientX: cx,
                clientY: cy
            });
            clickTarget.dispatchEvent(syntheticClick);
        }
    });

    // ════════════════════════════════════════════════════════════════════════
    // 1. SECURITY MODAL
    // ════════════════════════════════════════════════════════════════════════
    const securityModal = new bootstrap.Modal(document.getElementById('securityWarningModal'));
    securityModal.show();

    document.getElementById('acceptSecurityBtn').addEventListener('click', async function () {
        document.body.classList.add('exam-active-mode');
        try { await document.documentElement.requestFullscreen(); }
        catch (e) { console.warn('Fullscreen:', e); }
        await activatePointerLock();
        examCursor.style.display = 'block';
        examCursor.style.left = cx + 'px';
        examCursor.style.top  = cy + 'px';
        hideCursorForExam();
        securityModal.hide();
        isExamStarted = true;
    });

    // ════════════════════════════════════════════════════════════════════════
    // POINTER LOCK
    // ════════════════════════════════════════════════════════════════════════
    async function activatePointerLock() {
        try {
            await document.documentElement.requestPointerLock({ unadjustedMovement: true });
        } catch (e1) {
            try { await document.documentElement.requestPointerLock(); }
            catch (e2) { console.warn('Pointer lock not available:', e2); }
        }
    }

    // ════════════════════════════════════════════════════════════════════════
    // 2. FULLSCREEN MONITOR
    // ════════════════════════════════════════════════════════════════════════
    const fsOverlay    = document.getElementById('fsOverlay');
    const reenterFsBtn = document.getElementById('reenterFsBtn');

    function isInFullscreen() {
        return !!(document.fullscreenElement || document.webkitFullscreenElement ||
                  document.mozFullScreenElement || document.msFullscreenElement);
    }

    ['fullscreenchange','webkitfullscreenchange','mozfullscreenchange','MSFullscreenChange']
        .forEach(ev => document.addEventListener(ev, function () {
            if (!isExamStarted) return;
            if (!isInFullscreen()) {
                fsOverlay.classList.add('show');
                recordActivity('fullscreen_exit');
            } else {
                fsOverlay.classList.remove('show');
            }
        }));

    reenterFsBtn.addEventListener('click', async function () {
        try { await document.documentElement.requestFullscreen(); } catch(e) {}
        fsOverlay.classList.remove('show');
        setTimeout(() => activatePointerLock(), 300);
    });

    // ════════════════════════════════════════════════════════════════════════
    // 2.5. POINTER LOCK MONITOR
    // ════════════════════════════════════════════════════════════════════════
    document.addEventListener('pointerlockchange', function () {
        if (!isExamStarted) return;
        if (!document.pointerLockElement) {
            recordActivity('pointer_lock_exit');
            // ma nrj3ouch pointer lock ila kayn select mftouh aw swal
            if (isInFullscreen() && !fsOverlay.classList.contains('show')
                && !document.documentElement.classList.contains('swal-open')
                && !useFreeMouse) {
                setTimeout(async () => {
                    if (isExamStarted && !document.pointerLockElement
                        && !document.documentElement.classList.contains('swal-open')
                        && !useFreeMouse) {
                        await activatePointerLock();
                    }
                }, 300);
            }
        }
    });

    document.addEventListener('pointerlockerror', () => console.warn('[Security] Pointer lock error'));

    // ════════════════════════════════════════════════════════════════════════
    // 3. COPY / PASTE / RIGHT-CLICK
    // ════════════════════════════════════════════════════════════════════════
    document.addEventListener('copy', e => {
        e.preventDefault(); toast('Copie désactivée!'); recordActivity('copy_attempt');
    });
    document.addEventListener('cut', e => e.preventDefault());
    document.addEventListener('paste', e => {
        const t = e.target.tagName, tp = e.target.type;
        if (t === 'TEXTAREA' || (t === 'INPUT' && tp === 'text')) return;
        e.preventDefault();
    });
    document.addEventListener('contextmenu', e => {
        e.preventDefault(); toast('Clic droit désactivé!'); recordActivity('right_click');
    });

    // ════════════════════════════════════════════════════════════════════════
    // 4. TAB / WINDOW SWITCH
    // ════════════════════════════════════════════════════════════════════════
    document.addEventListener('visibilitychange', () => {
        if (document.hidden && isExamStarted) {
            tabSwitchCount++;
            document.getElementById('tabSwitchCount').textContent = tabSwitchCount;
            document.getElementById('tabSwitchWarning').style.display = 'block';
            toast(`Changement d'onglet détecté! (${tabSwitchCount})`);
            recordActivity('tab_switch');
        }
    });
    window.addEventListener('blur', () => { if (isExamStarted) recordActivity('window_blur'); });

    // ════════════════════════════════════════════════════════════════════════
    // 5. BLOCK DEVTOOLS
    // ════════════════════════════════════════════════════════════════════════
    document.addEventListener('keydown', function (e) {
        if (e.key === 'ArrowRight' && currentQuestionIndex < totalQuestions - 1) {
            showQuestion(currentQuestionIndex + 1); return;
        }
        if (e.key === 'ArrowLeft' && currentQuestionIndex > 0) {
            showQuestion(currentQuestionIndex - 1); return;
        }
        if (e.keyCode === 123) {
            e.preventDefault(); toast('F12 bloqué!'); recordActivity('f12_attempt'); return;
        }
        if (e.ctrlKey && e.shiftKey && [73,67,74].includes(e.keyCode)) {
            e.preventDefault(); recordActivity('devtools_attempt'); return;
        }
        if (e.ctrlKey && e.keyCode === 85) {
            e.preventDefault(); recordActivity('source_attempt'); return;
        }
    });

    let dtOpen = false;
    setInterval(() => {
        const open = (window.outerWidth - window.innerWidth > 160) ||
                     (window.outerHeight - window.innerHeight > 160);
        if (open && !dtOpen && isExamStarted) {
            recordActivity('devtools_detected'); toast('Outils détectés!', 'error');
        }
        dtOpen = open;
    }, 500);

    // ════════════════════════════════════════════════════════════════════════
    // 6. HELPERS
    // ════════════════════════════════════════════════════════════════════════
    function toast(msg, type = 'warning') {
        Swal.mixin({
            toast: true, position: 'top-end',
            showConfirmButton: false, timer: 3000, timerProgressBar: true
        }).fire({ icon: type, title: msg });
    }

    function recordActivity(type) {
        fetch('/api/exam-security-log', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken() },
            body: JSON.stringify({ attempt_id: attemptId, activity_type: type, tab_switch_count: tabSwitchCount })
        }).catch(err => console.error('[Security] FAILED:', err));
    }

    // ════════════════════════════════════════════════════════════════════════
    // 7. TIMER
    // ════════════════════════════════════════════════════════════════════════
    let timeLeft = remainingTime;
    const timerText    = document.getElementById('timerText');
    const timerDisplay = document.getElementById('timerDisplay');

    function updateTimer() {
        if (timeLeft <= 0) { clearInterval(timerInterval); autoSubmit(); return; }
        const h = Math.floor(timeLeft / 3600);
        const m = Math.floor((timeLeft % 3600) / 60);
        const s = timeLeft % 60;
        timerText.textContent = `${String(h).padStart(2,'0')}:${String(m).padStart(2,'0')}:${String(s).padStart(2,'0')}`;
        timerDisplay.classList.remove('warning','danger');
        if      (timeLeft < 300) timerDisplay.classList.add('danger');
        else if (timeLeft < 600) timerDisplay.classList.add('warning');
        timeLeft--;
    }
    updateTimer();
    const timerInterval = setInterval(updateTimer, 1000);

    function autoSubmit() {
        showRealCursor();
        examCursor.style.display = 'none';
        if (document.pointerLockElement) document.exitPointerLock();
        Swal.fire({
            title:'Temps Écoulé!', text:'Soumission automatique en cours...',
            icon:'warning', timer:3000, timerProgressBar:true, showConfirmButton:false
        }).then(() => document.getElementById('submitExamForm').submit());
    }

    // ════════════════════════════════════════════════════════════════════════
    // 8. QUESTION NAVIGATION
    // ════════════════════════════════════════════════════════════════════════
    function showQuestion(index) {
        document.querySelectorAll('.question-container').forEach(q => q.style.display = 'none');
        document.querySelector(`.question-container[data-question-index="${index}"]`).style.display = 'block';
        document.querySelectorAll('.question-nav-btn').forEach(b => b.classList.remove('active'));
        document.querySelector(`.question-nav-btn[data-question-index="${index}"]`).classList.add('active');
        currentQuestionIndex = index;
        document.getElementById('currentQuestionNumber').textContent = index + 1;
        document.getElementById('examWrapper').scrollTo({ top: 0, behavior: 'smooth' });
    }

    document.querySelectorAll('.next-question-btn').forEach(b =>
        b.addEventListener('click', () => {
            if (currentQuestionIndex < totalQuestions - 1) showQuestion(currentQuestionIndex + 1);
        })
    );
    document.querySelectorAll('.prev-question-btn').forEach(b =>
        b.addEventListener('click', () => {
            if (currentQuestionIndex > 0) showQuestion(currentQuestionIndex - 1);
        })
    );
    document.querySelectorAll('.question-nav-btn').forEach(b =>
        b.addEventListener('click', function() { showQuestion(parseInt(this.dataset.questionIndex)); })
    );

    // ════════════════════════════════════════════════════════════════════════
    // 9. SAVE ANSWERS
    // ════════════════════════════════════════════════════════════════════════
    function saveAnswer(questionId, answer) {
        fetch('{{ route("exams.save-answer", $attempt) }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken(),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ question_id: questionId, answer })
        })
        .then(r => r.json())
        .then(d => {
            if (d.success) {
                const btn = document.querySelector(`.question-nav-btn[data-question-id="${questionId}"]`);
                if (btn) btn.classList.add('answered');
            }
        })
        .catch(err => console.error('Save answer error:', err));
    }

    document.querySelectorAll('input[type="radio"],input[type="checkbox"],input[type="text"],input[type="number"],textarea')
        .forEach(inp => inp.addEventListener('change', function () {
            const qid = this.dataset.questionId;
            if (!qid) return;
            const answer = this.type === 'checkbox'
                ? Array.from(document.querySelectorAll(`input[name="question_${qid}[]"]:checked`)).map(c => c.value)
                : this.value;
            saveAnswer(qid, answer);
        }));

    document.querySelectorAll('.blank-input').forEach(inp =>
        inp.addEventListener('change', function () {
            const qid = this.dataset.questionId;
            const answers = Array.from(
                this.closest('.blanks-container').querySelectorAll('.blank-input')
            ).map(b => b.value);
            saveAnswer(qid, answers);
        })
    );

    document.querySelectorAll('.matching-select').forEach(sel =>
        sel.addEventListener('change', function () {
            const qid = this.dataset.questionId;
            const pairs = [];
            this.closest('.matching-container').querySelectorAll('.matching-select').forEach(s => {
                if (s.value) pairs.push({ left: s.dataset.leftItem, right: s.value });
            });
            saveAnswer(qid, pairs);
        })
    );

    // ════════════════════════════════════════════════════════════════════════
    // ORDERING - Click to Swap
    // ════════════════════════════════════════════════════════════════════════
    document.querySelectorAll('.ordering-container').forEach(container => {
        const qid = container.dataset.questionId;
        let selectedItem = null;

        container.querySelectorAll('.ordering-item').forEach(item => {
            item.addEventListener('click', function () {
                if (!selectedItem) {
                    selectedItem = this;
                    this.style.background = '#cfe2ff';
                    this.style.borderColor = '#0d6efd';
                    this.style.boxShadow = '0 0 0 3px rgba(13,110,253,0.25)';
                    return;
                }

                if (selectedItem === this) {
                    selectedItem.style.background = '';
                    selectedItem.style.borderColor = '';
                    selectedItem.style.boxShadow = '';
                    selectedItem = null;
                    return;
                }

                const tmpItem = selectedItem.dataset.item;
                const tmpText = selectedItem.querySelector('.flex-grow-1').textContent;
                selectedItem.dataset.item = this.dataset.item;
                selectedItem.querySelector('.flex-grow-1').textContent = this.querySelector('.flex-grow-1').textContent;
                this.dataset.item = tmpItem;
                this.querySelector('.flex-grow-1').textContent = tmpText;

                selectedItem.style.background = '';
                selectedItem.style.borderColor = '';
                selectedItem.style.boxShadow = '';
                selectedItem = null;

                container.querySelectorAll('.ordering-item').forEach((it, i) => {
                    const b = it.querySelector('.order-number');
                    if (b) b.textContent = i + 1;
                });

                saveAnswer(qid, [...container.querySelectorAll('.ordering-item')].map(i => i.dataset.item));
            });
        });
    });

    // ════════════════════════════════════════════════════════════════════════
    // 10. SUBMIT
    // ════════════════════════════════════════════════════════════════════════
    function submitExam() {
        useFreeMouse = true;
        examCursor.style.display = 'none';
        showRealCursor();

        const doShowSwal = () => {
            examCursor.style.display = 'none';
            showRealCursor();

            Swal.fire({
                title: 'Soumettre l\'Examen?',
                html: `<p>Êtes-vous sûr de vouloir soumettre?${
                    tabSwitchCount > 0 ? `<br><br>⚠️ ${tabSwitchCount} changement(s) d'onglet détecté(s)` : ''
                }</p>
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Attention:</strong> Cette action est irréversible!
                </div>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#D32F2F',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Oui, soumettre!',
                cancelButtonText: 'Annuler',
                allowOutsideClick: false,
                didOpen: () => {
                    examCursor.style.display = 'none';
                    showRealCursor();
                },
                willClose: () => {
                    examCursor.style.display = 'none';
                }
            }).then(r => {
                if (r.isConfirmed) {
                    clearInterval(timerInterval);
                    isExamStarted = false;
                    examCursor.style.display = 'none';
                    document.documentElement.classList.remove('swal-open');
                    const ov = document.getElementById('globalLoadingOverlay');
                    if (ov) ov.classList.add('active');
                    document.getElementById('submitExamForm').submit();
                } else {
                    document.documentElement.classList.remove('swal-open');
                    setTimeout(() => {
                        hideCursorForExam();
                        useFreeMouse = false;
                        activatePointerLock().then(() => {
                            examCursor.style.left = cx + 'px';
                            examCursor.style.top  = cy + 'px';
                            examCursor.style.display = 'block';
                        }).catch(() => {
                            examCursor.style.left = cx + 'px';
                            examCursor.style.top  = cy + 'px';
                            examCursor.style.display = 'block';
                        });
                    }, 150);
                }
            });
        };

        if (document.pointerLockElement) {
            document.exitPointerLock();
            setTimeout(doShowSwal, 200);
        } else {
            doShowSwal();
        }
    }

    document.getElementById('submitExamBtn').addEventListener('click', submitExam);
    document.getElementById('finalSubmitBtn')?.addEventListener('click', submitExam);

    window.addEventListener('beforeunload', e => {
        if (isExamStarted) { e.preventDefault(); e.returnValue = ''; }
    });

});
</script>
@endpush