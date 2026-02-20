<div class="modal fade" id="addQuestionModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header bg-danger text-white rounded-top-4">
                <h5 class="modal-title fw-bold">
                    <i class="fas fa-plus-circle me-2"></i>
                    Ajouter une Question
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>

            <form id="addQuestionForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-body p-4">
                    <!-- Question Type -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-list-ul me-1 text-danger"></i>
                            Type de Question <span class="text-danger">*</span>
                        </label>
                        <select name="type" id="questionType" class="form-select form-control" required>
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

                    <!-- Question Text -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-question me-1 text-danger"></i>
                            Question <span class="text-danger">*</span>
                        </label>
                        <textarea name="question_text" id="questionTextArea" rows="3" 
                                  class="form-control" 
                                  placeholder="Écrivez votre question ici..."
                                  required></textarea>
                        <small class="text-muted" id="fillBlanksHelp" style="display: none;">
                            <i class="fas fa-info-circle me-1"></i>
                            Utilisez <strong>[___]</strong> pour marquer les blancs. Exemple: "La capitale du Maroc est [___]."
                        </small>
                    </div>

                    <!-- Question Image -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-image me-1 text-danger"></i>
                            Image (optionnel)
                        </label>
                        <input type="file" name="question_image" 
                               class="form-control" 
                               accept="image/*">
                        <small class="text-muted">Maximum 2MB</small>
                    </div>

                    <!-- Points -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-star me-1 text-danger"></i>
                            Points <span class="text-danger">*</span>
                        </label>
                        <input type="number" name="points" 
                               class="form-control" 
                               value="1" 
                               min="0.5" 
                               max="100" 
                               step="0.5" 
                               required>
                    </div>

                    <!-- Options (for QCM/Checkbox) -->
                    <div id="optionsContainer" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold mb-0">
                                <i class="fas fa-list-check me-1 text-danger"></i>
                                Options de Réponse
                            </label>
                            <button type="button" class="btn btn-sm btn-danger rounded-pill" id="addOptionBtn">
                                <i class="fas fa-plus me-1"></i>Ajouter une option
                            </button>
                        </div>
                        <div id="optionsList"></div>
                    </div>

                    <!-- Fill Blanks Container -->
                    <div id="fillBlanksContainer" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold mb-0">
                                <i class="fas fa-pen me-1 text-danger"></i>
                                Réponses des Blancs
                            </label>
                            <button type="button" class="btn btn-sm btn-info rounded-pill" id="detectBlanksBtn">
                                <i class="fas fa-sync me-1"></i>Détecter les blancs
                            </button>
                        </div>
                        <div id="blanksList">
                            <div class="alert alert-info">
                                <i class="fas fa-lightbulb me-2"></i>
                                Écrivez votre question avec [___] puis cliquez sur "Détecter les blancs"
                            </div>
                        </div>
                    </div>

                    <!-- Matching Container -->
                    <div id="matchingContainer" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold mb-0">
                                <i class="fas fa-link me-1 text-danger"></i>
                                Paires à associer
                            </label>
                            <button type="button" class="btn btn-sm btn-primary rounded-pill" id="addPairBtn">
                                <i class="fas fa-plus me-1"></i>Ajouter une paire
                            </button>
                        </div>
                        <div class="row mb-2">
                            <div class="col-5 text-center fw-bold">Colonne Gauche</div>
                            <div class="col-5 text-center fw-bold">Colonne Droite</div>
                            <div class="col-2"></div>
                        </div>
                        <div id="pairsList"></div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Les étudiants devront associer les éléments de gauche avec ceux de droite
                        </small>
                    </div>

                    <!-- Ordering Container -->
                    <div id="orderingContainer" style="display: none;">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <label class="form-label fw-semibold mb-0">
                                <i class="fas fa-sort me-1 text-danger"></i>
                                Éléments à ordonner
                            </label>
                            <button type="button" class="btn btn-sm btn-success rounded-pill" id="addOrderItemBtn">
                                <i class="fas fa-plus me-1"></i>Ajouter un élément
                            </button>
                        </div>
                        <div id="orderItemsList"></div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            Ajoutez les éléments dans le bon ordre. Les étudiants devront les réorganiser.
                        </small>
                    </div>

                    <!-- Numeric Container -->
                    <div id="numericContainer" style="display: none;">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-hashtag me-1 text-danger"></i>
                                    Valeur correcte <span class="text-danger">*</span>
                                </label>
                                <input type="number" id="numericValue" 
                                       class="form-control" 
                                       step="any"
                                       placeholder="ex: 3.14">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label class="form-label fw-semibold">
                                    <i class="fas fa-plus-minus me-1 text-warning"></i>
                                    Tolérance (optionnel)
                                </label>
                                <input type="number" id="numericTolerance" 
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
                                <input type="text" id="numericUnit" 
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
                    <div id="correctAnswerContainer" style="display: none;">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-check-circle me-1 text-danger"></i>
                            Réponse Correcte <span class="text-danger">*</span>
                        </label>
                        
                        <select name="correct_answer_select" id="trueFalseSelect" class="form-select form-control" style="display: none;">
                            <option value="">-- Sélectionnez --</option>
                            <option value="true">Vrai</option>
                            <option value="false">Faux</option>
                        </select>

                        <input type="text" name="correct_answer_text" id="textInput" 
                               class="form-control" 
                               placeholder="Entrez la réponse correcte..."
                               style="display: none;">
                    </div>

                    <!-- Explanation -->
                    <div class="mb-4">
                        <label class="form-label fw-semibold">
                            <i class="fas fa-lightbulb me-1 text-danger"></i>
                            Explication (optionnel)
                        </label>
                        <textarea name="explanation" rows="2" 
                                  class="form-control" 
                                  placeholder="Explication de la réponse correcte..."></textarea>
                    </div>
                </div>

                <div class="modal-footer border-0 p-4">
                    <button type="button" class="btn btn-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Annuler
                    </button>
                    <button type="submit" class="btn btn-danger rounded-pill px-4">
                        <i class="fas fa-save me-2"></i>Ajouter la Question
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const addQuestionForm = document.getElementById('addQuestionForm');
        const questionTypeSelect = document.getElementById('questionType');
        const questionTextArea = document.getElementById('questionTextArea');
        
        // Containers
        const optionsContainer = document.getElementById('optionsContainer');
        const fillBlanksContainer = document.getElementById('fillBlanksContainer');
        const matchingContainer = document.getElementById('matchingContainer');
        const orderingContainer = document.getElementById('orderingContainer');
        const numericContainer = document.getElementById('numericContainer');
        const correctAnswerContainer = document.getElementById('correctAnswerContainer');
        
        // Lists
        const optionsList = document.getElementById('optionsList');
        const blanksList = document.getElementById('blanksList');
        const pairsList = document.getElementById('pairsList');
        const orderItemsList = document.getElementById('orderItemsList');
        
        // Others
        const fillBlanksHelp = document.getElementById('fillBlanksHelp');
        const trueFalseSelect = document.getElementById('trueFalseSelect');
        const textInput = document.getElementById('textInput');

        let optionCounter = 0;
        let pairCounter = 0;
        let orderItemCounter = 0;

        // Handle question type change
        questionTypeSelect.addEventListener('change', function() {
            const type = this.value;
            hideAllContainers();
            resetAllLists();

            switch(type) {
                case 'qcm':
                case 'checkbox':
                    optionsContainer.style.display = 'block';
                    addOption();
                    addOption();
                    break;
                
                case 'fill_blanks':
                    fillBlanksContainer.style.display = 'block';
                    fillBlanksHelp.style.display = 'block';
                    break;
                
                case 'matching':
                    matchingContainer.style.display = 'block';
                    addPair();
                    addPair();
                    break;
                
                case 'ordering':
                    orderingContainer.style.display = 'block';
                    addOrderItem();
                    addOrderItem();
                    addOrderItem();
                    break;
                
                case 'numeric':
                    numericContainer.style.display = 'block';
                    break;
                
                case 'true_false':
                    correctAnswerContainer.style.display = 'block';
                    trueFalseSelect.style.display = 'block';
                    trueFalseSelect.required = true;
                    break;
                
                case 'text':
                    correctAnswerContainer.style.display = 'block';
                    textInput.style.display = 'block';
                    textInput.required = true;
                    break;
            }
        });

        function hideAllContainers() {
            optionsContainer.style.display = 'none';
            fillBlanksContainer.style.display = 'none';
            matchingContainer.style.display = 'none';
            orderingContainer.style.display = 'none';
            numericContainer.style.display = 'none';
            correctAnswerContainer.style.display = 'none';
            fillBlanksHelp.style.display = 'none';
            trueFalseSelect.style.display = 'none';
            textInput.style.display = 'none';
            trueFalseSelect.required = false;
            textInput.required = false;
        }

        function resetAllLists() {
            optionsList.innerHTML = '';
            blanksList.innerHTML = '<div class="alert alert-info"><i class="fas fa-lightbulb me-2"></i>Écrivez votre question avec [___] puis cliquez sur "Détecter les blancs"</div>';
            pairsList.innerHTML = '';
            orderItemsList.innerHTML = '';
            optionCounter = 0;
            pairCounter = 0;
            orderItemCounter = 0;
        }

        // ========== OPTIONS (QCM/Checkbox) ==========
        function addOption() {
            optionCounter++;
            const div = document.createElement('div');
            div.className = 'input-group mb-3';
            div.innerHTML = `
                <div class="input-group-text">
                    <input class="form-check-input mt-0 option-checkbox" type="checkbox">
                </div>
                <input type="text" class="form-control option-text" placeholder="Option ${optionCounter}" required>
                <button type="button" class="btn btn-danger remove-btn">
                    <i class="fas fa-times"></i>
                </button>
            `;
            optionsList.appendChild(div);
        }

        document.getElementById('addOptionBtn').addEventListener('click', () => {
            if (optionCounter < 10) addOption();
        });

        optionsList.addEventListener('click', (e) => {
            if (e.target.closest('.remove-btn') && optionsList.children.length > 2) {
                e.target.closest('.input-group').remove();
            }
        });

        // ========== FILL BLANKS ==========
        document.getElementById('detectBlanksBtn').addEventListener('click', function() {
            const questionText = questionTextArea.value;
            const blanksCount = (questionText.match(/\[___\]/g) || []).length;

            if (blanksCount === 0) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Aucun blanc détecté',
                    text: 'Ajoutez [___] dans votre question',
                    confirmButtonColor: '#D32F2F'
                });
                return;
            }

            blanksList.innerHTML = '';
            for (let i = 1; i <= blanksCount; i++) {
                const div = document.createElement('div');
                div.className = 'mb-3';
                div.innerHTML = `
                    <label class="form-label fw-semibold">
                        <i class="fas fa-pencil-alt me-1 text-info"></i>
                        Blanc ${i}
                    </label>
                    <input type="text" class="form-control blank-answer" 
                           placeholder="Réponse pour le blanc ${i}" required>
                `;
                blanksList.appendChild(div);
            }

            Swal.fire({
                icon: 'success',
                title: `${blanksCount} blanc(s) détecté(s)!`,
                timer: 2000,
                confirmButtonColor: '#17a2b8'
            });
        });

        // ========== MATCHING ==========
        function addPair() {
            pairCounter++;
            const div = document.createElement('div');
            div.className = 'row mb-3 pair-item';
            div.innerHTML = `
                <div class="col-5">
                    <input type="text" class="form-control pair-left" 
                           placeholder="Élément ${pairCounter} (Gauche)" required>
                </div>
                <div class="col-5">
                    <input type="text" class="form-control pair-right" 
                           placeholder="Correspondance ${pairCounter} (Droite)" required>
                </div>
                <div class="col-2">
                    <button type="button" class="btn btn-danger w-100 remove-pair-btn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            `;
            pairsList.appendChild(div);
        }

        document.getElementById('addPairBtn').addEventListener('click', () => {
            if (pairCounter < 10) addPair();
        });

        pairsList.addEventListener('click', (e) => {
            if (e.target.closest('.remove-pair-btn') && pairsList.children.length > 2) {
                e.target.closest('.pair-item').remove();
            }
        });

        // ========== ORDERING ==========
        function addOrderItem() {
            orderItemCounter++;
            const div = document.createElement('div');
            div.className = 'input-group mb-3 order-item';
            div.innerHTML = `
                <span class="input-group-text">
                    <i class="fas fa-grip-vertical"></i> ${orderItemCounter}
                </span>
                <input type="text" class="form-control order-text" 
                       placeholder="Élément ${orderItemCounter}" required>
                <button type="button" class="btn btn-danger remove-order-btn">
                    <i class="fas fa-times"></i>
                </button>
            `;
            orderItemsList.appendChild(div);
        }

        document.getElementById('addOrderItemBtn').addEventListener('click', () => {
            if (orderItemCounter < 15) addOrderItem();
        });

        orderItemsList.addEventListener('click', (e) => {
            if (e.target.closest('.remove-order-btn') && orderItemsList.children.length > 2) {
                e.target.closest('.order-item').remove();
                updateOrderNumbers();
            }
        });

        function updateOrderNumbers() {
            const items = orderItemsList.querySelectorAll('.order-item');
            items.forEach((item, index) => {
                item.querySelector('.input-group-text').innerHTML = `<i class="fas fa-grip-vertical"></i> ${index + 1}`;
            });
            orderItemCounter = items.length;
        }

        // ========== FORM SUBMISSION ==========
        addQuestionForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const formData = new FormData(this);
            const type = formData.get('type');

            // Build data based on type
            if (type === 'qcm' || type === 'checkbox') {
                const options = buildOptions();
                if (!options) return;
                formData.append('options', JSON.stringify(options));
            }
            else if (type === 'fill_blanks') {
                const blanks = buildBlanks();
                if (!blanks) return;
                formData.append('blanks', JSON.stringify(blanks));
            }
            else if (type === 'matching') {
                const pairs = buildPairs();
                if (!pairs) return;
                formData.append('matching_pairs', JSON.stringify(pairs));
            }
            else if (type === 'ordering') {
                const items = buildOrderItems();
                if (!items) return;
                formData.append('order_items', JSON.stringify(items));
            }
            else if (type === 'numeric') {
                const numericData = buildNumericData();
                if (!numericData) return;
                formData.append('numeric_data', JSON.stringify(numericData));
            }
            else if (type === 'true_false') {
                if (!trueFalseSelect.value) {
                    showError('Veuillez sélectionner la réponse correcte!');
                    return;
                }
                formData.set('correct_answer', trueFalseSelect.value);
            }
            else if (type === 'text') {
                if (!textInput.value.trim()) {
                    showError('Veuillez entrer la réponse correcte!');
                    return;
                }
                formData.set('correct_answer', textInput.value);
            }

            submitQuestion(formData);
        });

        function buildOptions() {
            const texts = Array.from(optionsList.querySelectorAll('.option-text'));
            const checks = Array.from(optionsList.querySelectorAll('.option-checkbox'));
            
            if (texts.length < 2) {
                showError('Minimum 2 options requises!');
                return null;
            }

            const checkedCount = checks.filter(c => c.checked).length;
            if (checkedCount === 0) {
                showError('Cochez au moins une bonne réponse!');
                return null;
            }

            const type = questionTypeSelect.value;
            if (type === 'qcm' && checkedCount > 1) {
                showError('QCM: une seule bonne réponse!');
                return null;
            }

            return texts.map((t, i) => ({
                text: t.value.trim(),
                is_correct: checks[i].checked
            }));
        }

        function buildBlanks() {
            const inputs = blanksList.querySelectorAll('.blank-answer');
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

        function buildPairs() {
            const items = pairsList.querySelectorAll('.pair-item');
            if (items.length < 2) {
                showError('Minimum 2 paires requises!');
                return null;
            }

            const pairs = [];
            for (let item of items) {
                const left = item.querySelector('.pair-left').value.trim();
                const right = item.querySelector('.pair-right').value.trim();
                if (!left || !right) {
                    showError('Toutes les paires doivent être complètes!');
                    return null;
                }
                pairs.push({ left, right });
            }

            return pairs;
        }

        function buildOrderItems() {
            const inputs = orderItemsList.querySelectorAll('.order-text');
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

        function buildNumericData() {
            const value = document.getElementById('numericValue').value;
            if (!value) {
                showError('Entrez la valeur correcte!');
                return null;
            }

            return {
                value: parseFloat(value),
                tolerance: parseFloat(document.getElementById('numericTolerance').value) || 0,
                unit: document.getElementById('numericUnit').value.trim()
            };
        }

        function showError(message) {
            Swal.fire({
                icon: 'warning',
                title: 'Attention',
                text: message,
                confirmButtonColor: '#D32F2F'
            });
        }

        function submitQuestion(formData) {
            if (typeof globalLoadingOverlay !== 'undefined') {
                document.getElementById('globalLoadingOverlay').classList.add('active');
            }

            fetch('{{ route("exams.questions.add", $exam) }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
            .then(response => response.ok ? response.json() : response.json().then(err => Promise.reject(err)))
            .then(data => {
                if (typeof globalLoadingOverlay !== 'undefined') {
                    document.getElementById('globalLoadingOverlay').classList.remove('active');
                }
                
                if (data.success) {
                    bootstrap.Modal.getInstance(document.getElementById('addQuestionModal')).hide();
                    Swal.fire({
                        icon: 'success',
                        title: 'Succès!',
                        text: data.message,
                        confirmButtonColor: '#D32F2F'
                    }).then(() => location.reload());
                }
            })
            .catch(error => {
                if (typeof globalLoadingOverlay !== 'undefined') {
                    document.getElementById('globalLoadingOverlay').classList.remove('active');
                }
                
                const message = error.errors ? Object.values(error.errors)[0][0] : error.message || 'Erreur';
                Swal.fire({
                    icon: 'error',
                    title: 'Erreur',
                    text: message,
                    confirmButtonColor: '#D32F2F'
                });
            });
        }

        // Reset on modal close
        document.getElementById('addQuestionModal').addEventListener('hidden.bs.modal', function() {
            addQuestionForm.reset();
            questionTypeSelect.value = '';
            hideAllContainers();
            resetAllLists();
        });
    });
</script>
@endpush