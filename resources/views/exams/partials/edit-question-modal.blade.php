<div class="modal fade" id="editQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-primary text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-edit me-2"></i>
                    Modifier la Question
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="editQuestionForm" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <input type="hidden" name="question_id" id="editQuestionId">

                <div class="modal-body p-4">
                    <!-- Question Type - MAINTENANT MODIFIABLE -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-list-ul me-1 text-primary"></i>
                            Type de Question <span class="text-danger">*</span>
                        </label>
                        <select name="type" id="editQuestionType" class="form-select form-control" required>
                            <option value="">-- Sélectionnez un type --</option>
                            <option value="qcm">QCM (Choix unique)</option>
                            <option value="checkbox">Choix multiples</option>
                            <option value="true_false">Vrai/Faux</option>
                            <option value="text">Réponse courte</option>
                            <option value="numeric">Réponse numérique</option>
                            <option value="fill_blanks">Remplir les blancs</option>
                            <option value="matching">Correspondance (Matching)</option>
                            <option value="ordering">Tri/Ordre (Ordering)</option>
                            <option value="essay">Réponse longue (Essay)</option>
                        </select>
                    </div>

                    <!-- Fill Blanks Help -->
                    <div id="editFillBlanksHelp" class="mb-3" style="display: none;">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Utilisez <strong>[___]</strong> pour marquer les blancs. Exemple: "La capitale du Maroc est [___]."
                        </small>
                    </div>

                    <!-- Question Text -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-question me-1 text-primary"></i>
                            Question <span class="text-danger">*</span>
                        </label>
                        <textarea name="question_text" id="editQuestionText" rows="3" 
                                  class="form-control" 
                                  required></textarea>
                    </div>

                    <!-- Current Image Display -->
                    <div id="currentImageContainer" class="mb-3" style="display: none;">
                        <label class="form-label fw-semibold">Image Actuelle</label>
                        <div class="border rounded-3 p-3">
                            <img id="currentImage" src="" class="img-fluid rounded" style="max-height: 200px;">
                        </div>
                    </div>

                    <!-- Question Image -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-image me-1 text-primary"></i>
                            Nouvelle Image (optionnel)
                        </label>
                        <input type="file" name="question_image" 
                               class="form-control" 
                               accept="image/*">
                        <small class="text-muted">Maximum 2MB. Laissez vide pour garder l'image actuelle.</small>
                    </div>

                    <!-- Points -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-star me-1 text-primary"></i>
                            Points <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="points" id="editPoints" 
                               class="form-control" 
                               min="0.5" 
                               max="100" 
                               step="0.5" 
                               required>
                    </div>

                    <!-- Options (for QCM/Checkbox) -->
                    <div id="editOptionsContainer" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold mb-0">
                                <i class="fas fa-list-check me-1 text-primary"></i>
                                Options de Réponse
                            </label>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill" id="editAddOptionBtn">
                                <i class="fas fa-plus me-1"></i>Ajouter une option
                            </button>
                        </div>
                        <div id="editOptionsList">
                            <!-- Options will be populated here -->
                        </div>
                    </div>

                    <!-- Fill Blanks Container -->
                    <div id="editFillBlanksContainer" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold mb-0">
                                <i class="fas fa-pen me-1 text-primary"></i>
                                Réponses des Blancs
                            </label>
                            <button type="button" class="btn btn-sm btn-info rounded-pill" id="editDetectBlanksBtn">
                                <i class="fas fa-sync me-1"></i>Détecter les blancs
                            </button>
                        </div>
                        <div id="editBlanksList">
                            <div class="alert alert-info">
                                <i class="fas fa-lightbulb me-2"></i>
                                Écrivez votre question avec [___] puis cliquez sur "Détecter les blancs"
                            </div>
                        </div>
                    </div>

                    <!-- Matching Container -->
                    <div id="editMatchingContainer" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold mb-0">
                                <i class="fas fa-link me-1 text-primary"></i>
                                Paires à associer
                            </label>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill" id="editAddPairBtn">
                                <i class="fas fa-plus me-1"></i>Ajouter une paire
                            </button>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-center fw-bold">Colonne Gauche</div>
                            <div class="col-5 text-center fw-bold">Colonne Droite</div>
                            <div class="col-2"></div>
                        </div>
                        <div id="editPairsList"></div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Les étudiants devront associer les éléments de gauche avec ceux de droite
                        </small>
                    </div>

                    <!-- Ordering Container -->
                    <div id="editOrderingContainer" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold mb-0">
                                <i class="fas fa-sort me-1 text-primary"></i>
                                Éléments à ordonner
                            </label>
                            <button type="button" class="btn btn-sm btn-success rounded-pill" id="editAddOrderItemBtn">
                                <i class="fas fa-plus me-1"></i>Ajouter un élément
                            </button>
                        </div>
                        <div id="editOrderItemsList"></div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Ajoutez les éléments dans le bon ordre. Les étudiants devront les réorganiser.
                        </small>
                    </div>

                    <!-- Numeric Container -->
                    <div id="editNumericContainer" style="display: none;">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-hashtag me-1 text-primary"></i>
                                    Valeur correcte <span class="text-danger">*</span>
                                </label>
                                <input type="number" id="editNumericValue" 
                                       class="form-control" 
                                       step="any"
                                       placeholder="ex: 3.14">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-plus-minus me-1 text-warning"></i>
                                    Tolérance (optionnel)
                                </label>
                                <input type="number" id="editNumericTolerance" 
                                       class="form-control" 
                                       step="any"
                                       placeholder="ex: 0.01"
                                       value="0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-ruler me-1 text-info"></i>
                                    Unité (optionnel)
                                </label>
                                <input type="text" id="editNumericUnit" 
                                       class="form-control" 
                                       placeholder="ex: kg, m, °C">
                            </div>
                        </div>
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle me-2"></i>
                            La tolérance permet d'accepter une marge d'erreur. Par exemple, avec valeur=10 et tolérance=0.5, 
                            les réponses entre 9.5 et 10.5 seront acceptées.
                        </div>
                    </div>

                    <!-- Correct Answer (for True/False and Text) -->
                    <div id="editCorrectAnswerContainer" style="display: none;">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-check-circle me-1 text-primary"></i>
                            Réponse Correcte <span class="text-danger">*</span>
                        </label>
                        
                        <!-- True/False Select -->
                        <select name="correct_answer_select" id="editTrueFalseSelect" class="form-select form-control" style="display: none;">
                            <option value="">-- Sélectionnez --</option>
                            <option value="true">Vrai</option>
                            <option value="false">Faux</option>
                        </select>

                        <!-- Text Input -->
                        <input type="text" name="correct_answer_text" id="editTextInput" 
                               class="form-control" 
                               placeholder="Entrez la réponse correcte..."
                               style="display: none;">
                    </div>

                    <!-- Explanation -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-lightbulb me-1 text-primary"></i>
                            Explication (optionnel)
                        </label>
                        <textarea name="explanation" id="editExplanation" rows="2" 
                                  class="form-control"></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-save me-2"></i>Enregistrer les Modifications
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const editQuestionForm = document.getElementById('editQuestionForm');
    const editQuestionTypeSelect = document.getElementById('editQuestionType');
    const editQuestionText = document.getElementById('editQuestionText');
    
    // Containers
    const editOptionsContainer = document.getElementById('editOptionsContainer');
    const editFillBlanksContainer = document.getElementById('editFillBlanksContainer');
    const editMatchingContainer = document.getElementById('editMatchingContainer');
    const editOrderingContainer = document.getElementById('editOrderingContainer');
    const editNumericContainer = document.getElementById('editNumericContainer');
    const editCorrectAnswerContainer = document.getElementById('editCorrectAnswerContainer');
    
    // Lists
    const editOptionsList = document.getElementById('editOptionsList');
    const editBlanksList = document.getElementById('editBlanksList');
    const editPairsList = document.getElementById('editPairsList');
    const editOrderItemsList = document.getElementById('editOrderItemsList');
    
    // Others
    const editFillBlanksHelp = document.getElementById('editFillBlanksHelp');
    const editTrueFalseSelect = document.getElementById('editTrueFalseSelect');
    const editTextInput = document.getElementById('editTextInput');

    let editOptionCounter = 0;
    let editPairCounter = 0;
    let editOrderItemCounter = 0;
    let currentQuestionData = null;

    // Handle type change
    editQuestionTypeSelect.addEventListener('change', function() {
        const type = this.value;
        hideAllEditContainers();
        
        // Only reset if changing to different type
        if (!currentQuestionData || type !== currentQuestionData.type) {
            resetAllEditLists();
        }

        switch(type) {
            case 'qcm':
            case 'checkbox':
                editOptionsContainer.style.display = 'block';
                if (!currentQuestionData || type !== currentQuestionData.type) {
                    addEditOption();
                    addEditOption();
                }
                break;
            
            case 'fill_blanks':
                editFillBlanksContainer.style.display = 'block';
                editFillBlanksHelp.style.display = 'block';
                break;
            
            case 'matching':
                editMatchingContainer.style.display = 'block';
                if (!currentQuestionData || type !== currentQuestionData.type) {
                    addEditPair();
                    addEditPair();
                }
                break;
            
            case 'ordering':
                editOrderingContainer.style.display = 'block';
                if (!currentQuestionData || type !== currentQuestionData.type) {
                    addEditOrderItem();
                    addEditOrderItem();
                }
                break;
            
            case 'numeric':
                editNumericContainer.style.display = 'block';
                break;
            
            case 'true_false':
                editCorrectAnswerContainer.style.display = 'block';
                editTrueFalseSelect.style.display = 'block';
                editTrueFalseSelect.required = true;
                break;
            
            case 'text':
                editCorrectAnswerContainer.style.display = 'block';
                editTextInput.style.display = 'block';
                editTextInput.required = true;
                break;
        }
    });

    function hideAllEditContainers() {
        editOptionsContainer.style.display = 'none';
        editFillBlanksContainer.style.display = 'none';
        editMatchingContainer.style.display = 'none';
        editOrderingContainer.style.display = 'none';
        editNumericContainer.style.display = 'none';
        editCorrectAnswerContainer.style.display = 'none';
        editFillBlanksHelp.style.display = 'none';
        editTrueFalseSelect.style.display = 'none';
        editTextInput.style.display = 'none';
        editTrueFalseSelect.required = false;
        editTextInput.required = false;
    }

    function resetAllEditLists() {
        editOptionsList.innerHTML = '';
        editBlanksList.innerHTML = '<div class="alert alert-info"><i class="fas fa-lightbulb me-2"></i>Écrivez votre question avec [___] puis cliquez sur "Détecter les blancs"</div>';
        editPairsList.innerHTML = '';
        editOrderItemsList.innerHTML = '';
        editOptionCounter = 0;
        editPairCounter = 0;
        editOrderItemCounter = 0;
    }

    // ========== OPTIONS (QCM/Checkbox) ==========
    function addEditOption(text = '', isCorrect = false) {
        editOptionCounter++;
        const div = document.createElement('div');
        div.className = 'input-group mb-3';
        div.innerHTML = `
            <div class="input-group-text">
                <input class="form-check-input mt-0 edit-option-checkbox" type="checkbox" ${isCorrect ? 'checked' : ''}>
            </div>
            <input type="text" class="form-control edit-option-text" value="${text}" placeholder="Option ${editOptionCounter}" required>
            <button type="button" class="btn btn-danger edit-remove-option-btn">
                <i class="fas fa-times"></i>
            </button>
        `;
        editOptionsList.appendChild(div);
    }

    document.getElementById('editAddOptionBtn').addEventListener('click', () => {
        if (editOptionCounter < 10) addEditOption();
    });

    editOptionsList.addEventListener('click', (e) => {
        if (e.target.closest('.edit-remove-option-btn') && editOptionsList.children.length > 2) {
            e.target.closest('.input-group').remove();
        }
    });

    // ========== FILL BLANKS ==========
    document.getElementById('editDetectBlanksBtn').addEventListener('click', function() {
        const questionText = editQuestionText.value;
        const blanksCount = (questionText.match(/\[___\]/g) || []).length;

        if (blanksCount === 0) {
            Swal.fire({
                icon: 'warning',
                title: 'Aucun blanc détecté',
                text: 'Ajoutez [___] dans votre question',
                confirmButtonColor: '#0d6efd'
            });
            return;
        }

        editBlanksList.innerHTML = '';
        for (let i = 1; i <= blanksCount; i++) {
            const div = document.createElement('div');
            div.className = 'mb-3';
            div.innerHTML = `
                <label class="form-label fw-semibold">
                    <i class="fas fa-pencil-alt me-1 text-info"></i>
                    Blanc ${i}
                </label>
                <input type="text" class="form-control edit-blank-answer" 
                       placeholder="Réponse pour le blanc ${i}" required>
            `;
            editBlanksList.appendChild(div);
        }

        Swal.fire({
            icon: 'success',
            title: `${blanksCount} blanc(s) détecté(s)!`,
            timer: 2000,
            confirmButtonColor: '#17a2b8'
        });
    });

    // ========== MATCHING ==========
    function addEditPair(left = '', right = '') {
        editPairCounter++;
        const div = document.createElement('div');
        div.className = 'row mb-3 edit-pair-item';
        div.innerHTML = `
            <div class="col-5">
                <input type="text" class="form-control edit-pair-left" 
                       value="${left}" placeholder="Élément ${editPairCounter} (Gauche)" required>
            </div>
            <div class="col-5">
                <input type="text" class="form-control edit-pair-right" 
                       value="${right}" placeholder="Correspondance ${editPairCounter} (Droite)" required>
            </div>
            <div class="col-2">
                <button type="button" class="btn btn-danger w-100 edit-remove-pair-btn">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        `;
        editPairsList.appendChild(div);
    }

    document.getElementById('editAddPairBtn').addEventListener('click', () => {
        if (editPairCounter < 10) addEditPair();
    });

    editPairsList.addEventListener('click', (e) => {
        if (e.target.closest('.edit-remove-pair-btn') && editPairsList.children.length > 2) {
            e.target.closest('.edit-pair-item').remove();
        }
    });

    // ========== ORDERING ==========
    function addEditOrderItem(text = '') {
        editOrderItemCounter++;
        const div = document.createElement('div');
        div.className = 'input-group mb-3 edit-order-item';
        div.innerHTML = `
            <span class="input-group-text">
                <i class="fas fa-grip-vertical"></i> ${editOrderItemCounter}
            </span>
            <input type="text" class="form-control edit-order-text" 
                   value="${text}" placeholder="Élément ${editOrderItemCounter}" required>
            <button type="button" class="btn btn-danger edit-remove-order-btn">
                <i class="fas fa-times"></i>
            </button>
        `;
        editOrderItemsList.appendChild(div);
    }

    document.getElementById('editAddOrderItemBtn').addEventListener('click', () => {
        if (editOrderItemCounter < 15) addEditOrderItem();
    });

    editOrderItemsList.addEventListener('click', (e) => {
        if (e.target.closest('.edit-remove-order-btn') && editOrderItemsList.children.length > 2) {
            e.target.closest('.edit-order-item').remove();
            updateEditOrderNumbers();
        }
    });

    function updateEditOrderNumbers() {
        const items = editOrderItemsList.querySelectorAll('.edit-order-item');
        items.forEach((item, index) => {
            item.querySelector('.input-group-text').innerHTML = `<i class="fas fa-grip-vertical"></i> ${index + 1}`;
        });
        editOrderItemCounter = items.length;
    }

    // Handle edit button click
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-question-btn')) {
            const btn = e.target.closest('.edit-question-btn');
            const questionId = btn.dataset.questionId;
            
            document.getElementById('globalLoadingOverlay').classList.add('active');

            fetch(`/exam-questions/${questionId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('globalLoadingOverlay').classList.remove('active');
                    currentQuestionData = data;
                    populateEditForm(data);
                    
                    const modal = new bootstrap.Modal(document.getElementById('editQuestionModal'));
                    modal.show();
                })
                .catch(error => {
                    document.getElementById('globalLoadingOverlay').classList.remove('active');
                    console.error('Error:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Erreur',
                        text: 'Impossible de charger les données de la question',
                        confirmButtonColor: '#0d6efd'
                    });
                });
        }
    });

    function populateEditForm(question) {
        document.getElementById('editQuestionId').value = question.id;
        editQuestionTypeSelect.value = question.type;
        editQuestionText.value = question.question_text;
        document.getElementById('editPoints').value = question.points;
        document.getElementById('editExplanation').value = question.explanation || '';

        // Handle image
        const currentImageContainer = document.getElementById('currentImageContainer');
        const currentImage = document.getElementById('currentImage');
        if (question.question_image) {
            currentImage.src = `/storage/${question.question_image}`;
            currentImageContainer.style.display = 'block';
        } else {
            currentImageContainer.style.display = 'none';
        }

        // Reset
        hideAllEditContainers();
        editOptionsList.innerHTML = '';
        editBlanksList.innerHTML = '<div class="alert alert-info"><i class="fas fa-lightbulb me-2"></i>Écrivez votre question avec [___] puis cliquez sur "Détecter les blancs"</div>';
        editPairsList.innerHTML = '';
        editOrderItemsList.innerHTML = '';
        editOptionCounter = 0;
        editPairCounter = 0;
        editOrderItemCounter = 0;

        // Populate based on type
        if (question.type === 'qcm' || question.type === 'checkbox') {
            editOptionsContainer.style.display = 'block';
            if (question.options && question.options.length > 0) {
                question.options.forEach(opt => {
                    addEditOption(opt.text, opt.is_correct);
                });
            }
        }
        else if (question.type === 'fill_blanks') {
            editFillBlanksContainer.style.display = 'block';
            editFillBlanksHelp.style.display = 'block';
            
            if (question.correct_answer && question.correct_answer.blanks) {
                editBlanksList.innerHTML = '';
                question.correct_answer.blanks.forEach((answer, i) => {
                    const div = document.createElement('div');
                    div.className = 'mb-3';
                    div.innerHTML = `
                        <label class="form-label fw-semibold">
                            <i class="fas fa-pencil-alt me-1 text-info"></i>
                            Blanc ${i + 1}
                        </label>
                        <input type="text" class="form-control edit-blank-answer" 
                               value="${answer}" placeholder="Réponse pour le blanc ${i + 1}" required>
                    `;
                    editBlanksList.appendChild(div);
                });
            }
        }
        else if (question.type === 'matching') {
            editMatchingContainer.style.display = 'block';
            
            if (question.correct_answer && question.correct_answer.pairs) {
                question.correct_answer.pairs.forEach(pair => {
                    addEditPair(pair.left, pair.right);
                });
            }
        }
        else if (question.type === 'ordering') {
            editOrderingContainer.style.display = 'block';
            
            if (question.correct_answer && question.correct_answer.items) {
                question.correct_answer.items.forEach(item => {
                    addEditOrderItem(item);
                });
            }
        }
        else if (question.type === 'numeric') {
            editNumericContainer.style.display = 'block';
            
            if (question.correct_answer) {
                const data = typeof question.correct_answer === 'object' ? question.correct_answer : {value: question.correct_answer};
                document.getElementById('editNumericValue').value = data.value || '';
                document.getElementById('editNumericTolerance').value = data.tolerance || 0;
                document.getElementById('editNumericUnit').value = data.unit || '';
            }
        }
        else if (question.type === 'true_false') {
            editCorrectAnswerContainer.style.display = 'block';
            editTrueFalseSelect.style.display = 'block';
            editTrueFalseSelect.value = question.correct_answer;
            editTrueFalseSelect.required = true;
        }
        else if (question.type === 'text') {
            editCorrectAnswerContainer.style.display = 'block';
            editTextInput.style.display = 'block';
            editTextInput.value = question.correct_answer;
            editTextInput.required = true;
        }
    }

    // Form submission
    editQuestionForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(this);
        const type = formData.get('type');
        const questionId = formData.get('question_id');

        // Build data based on type
        if (type === 'qcm' || type === 'checkbox') {
            const options = buildEditOptions();
            if (!options) return;
            formData.append('options', JSON.stringify(options));
        }
        else if (type === 'fill_blanks') {
            const blanks = buildEditBlanks();
            if (!blanks) return;
            formData.append('blanks', JSON.stringify(blanks));
        }
        else if (type === 'matching') {
            const pairs = buildEditPairs();
            if (!pairs) return;
            formData.append('matching_pairs', JSON.stringify(pairs));
        }
        else if (type === 'ordering') {
            const items = buildEditOrderItems();
            if (!items) return;
            formData.append('order_items', JSON.stringify(items));
        }
        else if (type === 'numeric') {
            const numericData = buildEditNumericData();
            if (!numericData) return;
            formData.append('numeric_data', JSON.stringify(numericData));
        }
        else if (type === 'true_false') {
            if (!editTrueFalseSelect.value) {
                showError('Veuillez sélectionner la réponse correcte!');
                return;
            }
            formData.set('correct_answer', editTrueFalseSelect.value);
        }
        else if (type === 'text') {
            if (!editTextInput.value.trim()) {
                showError('Veuillez entrer la réponse correcte!');
                return;
            }
            formData.set('correct_answer', editTextInput.value);
        }

        submitEdit(formData, questionId);
    });

    function buildEditOptions() {
        const texts = Array.from(editOptionsList.querySelectorAll('.edit-option-text'));
        const checks = Array.from(editOptionsList.querySelectorAll('.edit-option-checkbox'));
        
        if (texts.length < 2) {
            showError('Minimum 2 options requises!');
            return null;
        }

        const checkedCount = checks.filter(c => c.checked).length;
        if (checkedCount === 0) {
            showError('Cochez au moins une bonne réponse!');
            return null;
        }

        const type = editQuestionTypeSelect.value;
        if (type === 'qcm' && checkedCount > 1) {
            showError('QCM: une seule bonne réponse!');
            return null;
        }

        return texts.map((t, i) => ({
            text: t.value.trim(),
            is_correct: checks[i].checked
        }));
    }

    function buildEditBlanks() {
        const inputs = editBlanksList.querySelectorAll('.edit-blank-answer');
        if (inputs.length === 0) {
            showError('Détectez les blancs d\'abord!');
            return null;
        }

        const blanks = Array.from(inputs).map(i => i.value.trim());
        if (blanks.some(b => !b)) {
            showError('Tous les blancs doivent avoir une réponse!');
            return null;
        }

        return blanks;
    }

    function buildEditPairs() {
        const items = editPairsList.querySelectorAll('.edit-pair-item');
        if (items.length < 2) {
            showError('Minimum 2 paires requises!');
            return null;
        }

        const pairs = [];
        for (let item of items) {
            const left = item.querySelector('.edit-pair-left').value.trim();
            const right = item.querySelector('.edit-pair-right').value.trim();
            if (!left || !right) {
                showError('Toutes les paires doivent être complètes!');
                return null;
            }
            pairs.push({ left, right });
        }

        return pairs;
    }

    function buildEditOrderItems() {
        const inputs = editOrderItemsList.querySelectorAll('.edit-order-text');
        if (inputs.length < 2) {
            showError('Minimum 2 éléments requis!');
            return null;
        }

        const items = Array.from(inputs).map(i => i.value.trim());
        if (items.some(i => !i)) {
            showError('Tous les éléments doivent être remplis!');
            return null;
        }

        return items;
    }

    function buildEditNumericData() {
        const value = document.getElementById('editNumericValue').value;
        if (!value) {
            showError('Entrez la valeur correcte!');
            return null;
        }

        return {
            value: parseFloat(value),
            tolerance: parseFloat(document.getElementById('editNumericTolerance').value) || 0,
            unit: document.getElementById('editNumericUnit').value.trim()
        };
    }

    function showError(message) {
        Swal.fire({
            icon: 'warning',
            title: 'Attention',
            text: message,
            confirmButtonColor: '#0d6efd'
        });
    }

   function submitEdit(formData, questionId) {
    // DEBUG: Log what we're sending
    console.log('=== EDIT SUBMISSION DEBUG ===');
    console.log('Question ID:', questionId);
    console.log('Type:', formData.get('type'));
    
    // Log all form data
    for (let pair of formData.entries()) {
        console.log(pair[0] + ': ' + pair[1]);
    }
    console.log('=== END DEBUG ===');
    
    document.getElementById('globalLoadingOverlay').classList.add('active');

    fetch(`/exam-questions/${questionId}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-HTTP-Method-Override': 'PUT',
            'Accept': 'application/json'
        },
        body: formData
    })
    .then(response => {
        // DEBUG: Log response status
        console.log('Response status:', response.status);
        return response.json();
    })
    .then(data => {
        document.getElementById('globalLoadingOverlay').classList.remove('active');
        
        // DEBUG: Log response data
        console.log('Response data:', data);
        
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('editQuestionModal')).hide();
            Swal.fire({
                icon: 'success',
                title: 'Succès!',
                text: data.message,
                confirmButtonColor: '#0d6efd'
            }).then(() => location.reload());
        } else {
            // Show validation errors if any
            if (data.errors) {
                console.error('Validation errors:', data.errors);
                const errorMsg = Object.values(data.errors).flat().join('\n');
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur de validation',
                    text: errorMsg,
                    confirmButtonColor: '#0d6efd'
                });
            }
        }
    })
    .catch(error => {
        document.getElementById('globalLoadingOverlay').classList.remove('active');
        console.error('Fetch error:', error);
        Swal.fire({
            icon: 'error',
            title: 'Erreur',
            text: 'Impossible de modifier la question',
            confirmButtonColor: '#0d6efd'
        });
    });
}

    // Reset on modal close
    document.getElementById('editQuestionModal').addEventListener('hidden.bs.modal', function() {
        currentQuestionData = null;
        editQuestionForm.reset();
        hideAllEditContainers();
        resetAllEditLists();
    });
});
</script>
@endpush