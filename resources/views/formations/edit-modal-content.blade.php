{{-- This file is loaded dynamically into the editFormationModal --}}
<div class="container-fluid">
    <div class="text-center mb-5 animate-slide-down">
        <h3 class="modal-main-title">Modifiez les détails de la formation</h3>
        <p class="modal-subtitle">Mettez à jour les informations de votre formation pour qu'elles restent précises.</p>
    </div>

    {{-- The form action dynamically targets the update route for the specific formation --}}
    {{-- IMPORTANT: Add enctype="multipart/form-data" for file uploads --}}
    <form action="{{ route('formations.update', $formation->id) }}" method="POST" class="row g-4 new-form-grid" id="formation-edit-form" enctype="multipart/form-data">
        @csrf
        @method('PATCH') {{-- Use PATCH method for updates --}}

        <div class="col-lg-6 col-md-12 animate-fade-in-up delay-1">
            <div class="section-card new-section-card h-100">
                <h4 class="new-section-title primary-border-bottom d-flex align-items-center mb-4">
                    <i class="fas fa-info-circle me-2 section-icon primary-icon"></i> Informations Générales
                </h4>
                <div class="mb-3">
                    <label for="edit_title" class="new-form-label">
                        <i class="fas fa-book-open me-2"></i> Titre de la Formation <span class="text-danger">*</span>
                    </label>
                    <div class="input-group-with-icon">
                        <input type="text" name="title" id="edit_title" class="form-control new-form-control"
                               value="{{ old('title', $formation->title) }}" placeholder="Ex: Développement Web avec Laravel" required>
                    </div>
                    @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="edit_category_id" class="new-form-label">
                        <i class="fas fa-tags me-2"></i> Catégorie <span class="text-danger">*</span>
                    </label>
                    <div class="input-group-with-icon">
                        <select id="edit_category_id" name="category_id" class="form-control new-form-control" required>
                            <option value="">Sélectionner une catégorie</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ old('category_id', $formation->category_id) == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('category_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="mb-3">
                    <label for="edit_consultant_id" class="new-form-label">
                        <i class="fas fa-user-tie me-2"></i> Consultant <span class="text-danger">*</span>
                    </label>
                    <div class="input-group-with-icon">
                        <select id="edit_consultant_id" name="consultant_id" class="form-control new-form-control" required>
                            <option value="">Sélectionner un consultant</option>
                            @foreach($consultants as $consultant)
                                <option value="{{ $consultant->id }}" {{ old('consultant_id', $formation->consultant_id) == $consultant->id ? 'selected' : '' }}>
                                    {{ $consultant->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('consultant_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
                <div class="mb-0">
                    <label for="edit_status" class="new-form-label">
                        <i class="fas fa-clipboard-check me-2"></i> Statut <span class="text-danger">*</span>
                    </label>
                    <div class="input-group-with-icon">
                        <select id="edit_status" name="status" class="form-control new-form-control" required>
                            <option value="draft" {{ old('status', $formation->status) == 'draft' ? 'selected' : '' }}>Brouillon</option>
                            <option value="published" {{ old('status', $formation->status) == 'published' ? 'selected' : '' }}>Publiée</option>
                            <option value="completed" {{ old('status', $formation->status) == 'completed' ? 'selected' : '' }}>Terminée</option>
                        </select>
                    </div>
                    @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="col-lg-6 col-md-12 animate-fade-in-up delay-2">
            <div class="section-card new-section-card h-100">
                <h4 class="new-section-title success-border-bottom d-flex align-items-center mb-4">
                    <i class="fas fa-calendar-alt me-2 section-icon success-icon"></i> Détails et Calendrier
                </h4>
                <div class="row g-3">
                    <div class="col-md-6 mb-3">
                        <label for="edit_price" class="new-form-label">
                            <i class="fas fa-money-bill-wave me-2"></i> Prix (MAD) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group-with-icon">
                            <input type="number" name="price" id="edit_price" step="0.01" min="0" class="form-control new-form-control"
                                   value="{{ old('price', $formation->price) }}" placeholder="0.00" required>
                        </div>
                        @error('price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_capacity" class="new-form-label">
                            <i class="fas fa-users me-2"></i> Capacité (Nb. Places) <span class="text-danger">*</span>
                        </label>
                        <div class="input-group-with-icon">
                            <input type="number" name="capacity" id="edit_capacity" min="1" class="form-control new-form-control"
                                   value="{{ old('capacity', $formation->capacity) }}" required>
                        </div>
                        @error('capacity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    
                    {{-- DURATION HOURS AND UNIT WITH COMBINED STYLE --}}
                    <div class="col-md-6 mb-3">
                        <label for="edit_duration_hours" class="new-form-label">
                            <i class="fas fa-clock me-2"></i> Durée <span class="text-danger">*</span>
                        </label>
                        <div class="input-group input-group-modern">
                            <span class="input-group-text modern-addon">
                                <i class="fas fa-calendar-alt"></i>
                            </span>
                            <input type="number" name="duration_hours" id="edit_duration_hours" min="1" 
                                   class="form-control new-form-control modern-input-no-border"
                                   value="{{ old('duration_hours', $formation->duration_hours) }}" required>
                            
                            <select class="form-select modern-select-no-border" id="edit_duration_unit" name="duration_unit" required>
                                @foreach($durationUnits as $unit)
                                    <option value="{{ $unit }}" {{ old('duration_unit', $formation->duration_unit ?? '') == $unit ? 'selected' : '' }}>
                                        {{ ucfirst($unit) }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        @error('duration_hours')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @error('duration_unit')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    {{-- END DURATION HOURS AND UNIT --}}

                    <div class="col-md-6 mb-3">
                        <label for="edit_start_date" class="new-form-label">
                            <i class="fas fa-calendar-alt me-2"></i> Date Début <span class="text-danger">*</span>
                        </label>
                        <div class="input-group-with-icon">
                            <input type="date" name="start_date" id="edit_start_date" class="form-control new-form-control"
                                   value="{{ old('start_date', \Carbon\Carbon::parse($formation->start_date)->format('Y-m-d')) }}" required>
                        </div>
                        @error('start_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="edit_end_date" class="new-form-label">
                            <i class="fas fa-calendar-check me-2"></i> Date Fin <span class="text-danger">*</span>
                        </label>
                        <div class="input-group-with-icon">
                            <input type="date" name="end_date" id="edit_end_date" class="form-control new-form-control"
                                   value="{{ old('end_date', \Carbon\Carbon::parse($formation->end_date)->format('Y-m-d')) }}" required>
                        </div>
                        @error('end_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                    <div class="col-12 mb-0">
                        <label class="new-form-label">
                            <i class="fas fa-wallet me-2"></i> Options de Paiement Disponibles <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex flex-wrap gap-3 mt-2">
                            @php
                                $paymentOptions = [1 => 'Paiement Complet (1 Versement)', 2 => '2 Versements', 3 => '3 Versements', 4 => '4 Versements', 6 => '6 Versements', 10 => '10 Versements', 12 => '12 Versements'];
                                $selectedOptions = old('available_payment_options', $formation->available_payment_options ?? []);
                            @endphp
                            @foreach($paymentOptions as $value => $label)
                                <div class="form-check new-form-check-inline">
                                    <input type="checkbox" name="available_payment_options[]" id="edit_payment_option_{{ $value }}" value="{{ $value }}"
                                           class="form-check-input new-form-check-input" @checked(in_array($value, $selectedOptions))>
                                    <label class="form-check-label new-form-check-label" for="edit_payment_option_{{ $value }}">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @error('available_payment_options')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                        @error('available_payment_options.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 animate-fade-in-up delay-3">
            <div class="section-card new-section-card">
                <h4 class="new-section-title info-border-bottom d-flex align-items-center mb-4">
                    <i class="fas fa-file-lines me-2 section-icon info-icon"></i> Description de la Formation <span class="text-danger">*</span>
                </h4>
                <div class="mb-0">
                    <label for="edit_description" class="new-form-label d-none">Description</label>
                    <textarea id="edit_description" name="description" rows="6" class="form-control new-form-control"
                                  placeholder="Décrivez le contenu détaillé de la formation, les objectifs d'apprentissage, et le public cible..." required>{{ old('description', $formation->description) }}</textarea>
                </div>
                @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="col-lg-6 col-md-12 animate-fade-in-up delay-4">
            <div class="section-card new-section-card h-100">
                <h4 class="new-section-title danger-border-bottom d-flex align-items-center mb-4">
                    <i class="fas fa-graduation-cap me-2 section-icon danger-icon"></i> Prérequis (Optionnel)
                </h4>
                <div id="edit-prerequisites-container" class="space-y-3">
                    @php
                        $prerequisites = old('prerequisites', $formation->prerequisites ?? []);
                        // Ensure there's at least one empty field if no prerequisites or only empty ones
                        if (empty($prerequisites) || (count($prerequisites) === 1 && empty($prerequisites[0]))) {
                            $prerequisites = ['']; 
                        }
                    @endphp
                    @foreach($prerequisites as $prerequisite)
                        <div class="dynamic-field-item new-dynamic-field animate-item-enter">
                            <i class="fas fa-check-circle dynamic-field-icon"></i>
                            <input type="text" name="prerequisites[]" class="form-control new-form-control"
                                   value="{{ $prerequisite }}" placeholder="Ex: Avoir des connaissances en HTML">
                            <button type="button" class="remove-dynamic-btn">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    @endforeach
                </div>
                <button type="button" id="edit-add-prerequisite-btn" class="btn new-btn-outline-secondary mt-3 d-flex align-items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Ajouter un prérequis
                </button>
                @error('prerequisites')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="col-lg-6 col-md-12 animate-fade-in-up delay-5">
            <div class="section-card new-section-card h-100">
                <h4 class="new-section-title secondary-border-bottom d-flex align-items-center mb-4">
                    <i class="fas fa-folder-open me-2 section-icon secondary-icon"></i> Documents Requis (Optionnel)
                </h4>
                <div id="edit-documents-container" class="space-y-3">
                    @php
                        // Prioritize old input in case of validation failure
                        $documents_required = old('documents_required', $formation->documents_required ?? []);
                    @endphp

                    {{-- Display existing documents as clickable links with hidden inputs to retain their data --}}
                    @if(is_array($documents_required) && count($documents_required) > 0)
                        @foreach($documents_required as $index => $document)
                            @if(is_array($document) && isset($document['name']) && isset($document['path']))
                                <div class="dynamic-field-item new-dynamic-field animate-item-enter existing-document-item">
                                    <i class="fas fa-file-check dynamic-field-icon"></i>
                                    <a href="{{ asset('storage/' . $document['path']) }}" target="_blank" class="form-control new-form-control text-primary hover:underline d-flex align-items-center" style="cursor: pointer;">
                                        {{ $document['name'] }}
                                        <i class="fas fa-external-link-alt ms-2 text-sm"></i>
                                    </a>
                                    {{-- Hidden inputs to send existing document data back to the server --}}
                                    <input type="hidden" name="existing_documents_names[]" value="{{ $document['name'] }}">
                                    <input type="hidden" name="existing_documents_paths[]" value="{{ $document['path'] }}">
                                    <button type="button" class="remove-dynamic-btn" data-document-path="{{ $document['path'] }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @elseif(is_string($document))
                                {{-- Fallback for old string-only entries if validation failed and old() contains strings --}}
                                <div class="dynamic-field-item new-dynamic-field animate-item-enter">
                                    <i class="fas fa-file-check dynamic-field-icon"></i>
                                    <input type="text" name="existing_documents_names[]" class="form-control new-form-control" value="{{ $document }}" readonly>
                                    <input type="hidden" name="existing_documents_paths[]" value=""> {{-- No path for string-only old data --}}
                                    <button type="button" class="remove-dynamic-btn">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            @endif
                        @endforeach
                    @endif

                    {{-- Always provide one empty file input for new uploads --}}
                    <div class="dynamic-field-item new-dynamic-field animate-item-enter">
                        <i class="fas fa-file-upload dynamic-field-icon"></i>
                        <input type="file" name="documents_files[]" class="form-control new-form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                        <button type="button" class="remove-dynamic-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
                <button type="button" id="edit-add-document-btn" class="btn new-btn-outline-primary mt-3 d-flex align-items-center gap-2">
                    <i class="fas fa-plus-circle"></i> Ajouter un document
                </button>
                @error('documents_files')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                @error('documents_files.*')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
            </div>
        </div>

        <div class="col-12 d-flex justify-content-end gap-3 mt-5 animate-fade-in-up delay-6">
            <button type="button" class="btn new-btn-cancel" data-bs-dismiss="modal">
                <i class="fas fa-times-circle me-2"></i> Annuler
            </button>
            <button type="submit" id="edit-submit-btn" class="btn new-btn-submit d-flex align-items-center">
                <i class="fas fa-save me-2"></i> Enregistrer les Modifications
                <span class="spinner-border spinner-border-sm ms-2 hidden" role="status" aria-hidden="true"></span>
            </button>
        </div>
    </form>
</div>


<script> 
    if (typeof initializeModalForm === 'function') {
        const editForm = document.getElementById('formation-edit-form');
        if (editForm) {
            initializeModalForm(editForm);

            // Re-initialize dynamic prerequisites and documents fields with existing data
            const editPrerequisitesContainer = document.getElementById('edit-prerequisites-container');
            const editDocumentsContainer = document.getElementById('edit-documents-container');

            // Re-attach remove listeners for dynamically loaded content
            if (editPrerequisitesContainer) {
                editPrerequisitesContainer.addEventListener('click', function(event) {
                    if (event.target.closest('.remove-dynamic-btn')) {
                        event.target.closest('.dynamic-field-item').remove();
                    }
                });
            }
            if (editDocumentsContainer) {
                editDocumentsContainer.addEventListener('click', function(event) {
                    if (event.target.closest('.remove-dynamic-btn')) {
                        event.target.closest('.dynamic-field-item').remove();
                    }
                });
            }
        }
    }
</script>
