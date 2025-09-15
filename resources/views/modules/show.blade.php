@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">
            Formation: {{ $formation->title }}
        </div>
        <div class="card-body">
            <p><strong>Description:</strong> {{ $formation->description }}</p>
            <p><strong>Duration:</strong> {{ $formation->duration_hours }} hours</p>

            <hr>
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Modules for this Formation ({{ $formation->modules->count() }})</h4>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createModuleModal">
                    <i class="fas fa-plus"></i> Add New Module
                </button>
            </div>

            <div id="alert-container"></div>

            @if ($formation->modules->isEmpty())
                <div class="alert alert-info">No modules have been added to this formation yet.</div>
            @else
                <div class="row row-cols-1 g-4" id="modules-list">
                    @foreach($formation->modules->sortBy('order') as $module)
                        <div class="col" id="module-card-{{ $module->id }}">
                            <div class="card border-primary h-100">
                                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                    <span class="module-title">Module {{ $module->order }}: {{ $module->title }}</span>
                                    <div>
                                        <button class="btn btn-sm btn-light edit-btn" data-bs-toggle="modal" data-bs-target="#editModuleModal" data-id="{{ $module->id }}">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                        <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $module->id }}">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <p><strong>Assigned Consultant:</strong> <span class="module-consultant">{{ $module->user->name ?? 'N/A' }}</span></p>
                                    <p><strong>Status:</strong> <span class="badge module-status bg-{{ $module->status == 'published' ? 'success' : 'warning' }}" data-status="{{ $module->status }}">{{ $module->status }}</span></p>

                                    <div class="mb-3">
                                        <strong>Content:</strong>
                                        <ul class="list-group list-group-flush mt-2 module-content">
                                            @forelse($module->content as $item)
                                                <li class="list-group-item">{{ $item }}</li>
                                            @empty
                                                <li class="list-group-item text-muted">No content available.</li>
                                            @endforelse
                                        </ul>
                                    </div>

                                    <hr>
                                    <strong>Module Progress:</strong>
                                    <div class="progress mt-2" style="height: 25px;">
                                        <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: {{ $module->progress }}%;" aria-valuenow="{{ $module->progress }}" aria-valuemin="0" aria-valuemax="100">{{ $module->progress }}%</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif

            <div class="mt-4">
                <a href="{{ route('modules.index') }}" class="btn btn-secondary">Back to Formations List</a>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="createModuleModal" tabindex="-1" aria-labelledby="createModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createModuleModalLabel">Add New Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createModuleForm">
                <div class="modal-body">
                    <input type="hidden" name="formation_id" value="{{ $formation->id }}">
                    <div class="mb-3">
                        <label for="create-title" class="form-label">Module Title</label>
                        <input type="text" class="form-control" id="create-title" required>
                    </div>
                    <div class="mb-3">
                        <label for="create-status" class="form-label">Status</label>
                        <select class="form-select" id="create-status">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="create-content" class="form-label">Content</label>
                        <textarea class="form-control" id="create-content" rows="3" placeholder="Enter one item per line" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="create-user" class="form-label">Assign Consultant</label>
                        <select class="form-select" id="create-user" required></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Module</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="editModuleModal" tabindex="-1" aria-labelledby="editModuleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModuleModalLabel">Edit Module</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="editModuleForm">
                <div class="modal-body">
                    <input type="hidden" id="edit-module-id">
                    <div class="mb-3">
                        <label for="edit-title" class="form-label">Module Title</label>
                        <input type="text" class="form-control" id="edit-title" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-order" class="form-label">Order</label>
                        <input type="number" class="form-control" id="edit-order" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit-status" class="form-label">Status</label>
                        <select class="form-select" id="edit-status">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="edit-content" class="form-label">Content</label>
                        <textarea class="form-control" id="edit-content" rows="3" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="edit-user" class="form-label">Assign Consultant</label>
                        <select class="form-select" id="edit-user" required></select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save changes</button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const modulesList = document.getElementById('modules-list');
        const alertContainer = document.getElementById('alert-container');
        const editModuleModal = new bootstrap.Modal(document.getElementById('editModuleModal'));
        const createModuleModal = new bootstrap.Modal(document.getElementById('createModuleModal'));
        const editModuleForm = document.getElementById('editModuleForm');
        const createModuleForm = document.getElementById('createModuleForm');
        // Kanjibo les consultants men l'backend w kan7awlohom l'JavaScript object
        const consultants = @json($consultants); 

        // Function bach t3ammar select dyal les consultants
        function populateConsultantsSelect(selectElement, selectedUserId = null) {
            selectElement.innerHTML = '';
            consultants.forEach(consultant => {
                const option = document.createElement('option');
                option.value = consultant.id;
                option.textContent = consultant.name;
                if (consultant.id === selectedUserId) {
                    option.selected = true;
                }
                selectElement.appendChild(option);
            });
        }

        // Listener to handle Edit and Delete button clicks
        modulesList.addEventListener('click', function(e) {
            // Delete a module
            if (e.target.closest('.delete-btn')) {
                e.preventDefault();
                if (confirm('Are you sure you want to delete this module?')) {
                    const moduleId = e.target.closest('.delete-btn').dataset.id;
                    deleteModule(moduleId);
                }
            }

            // Open the edit modal
            if (e.target.closest('.edit-btn')) {
                const moduleId = e.target.closest('.edit-btn').dataset.id;
                fetchModuleData(moduleId);
            }
        });

        // Listener to open the create modal
        const createBtn = document.querySelector('[data-bs-target="#createModuleModal"]');
        if(createBtn) {
            createBtn.addEventListener('click', function() {
                populateConsultantsSelect(document.getElementById('create-user'));
            });
        }


        // Function to fetch module data via AJAX and populate the modal
        function fetchModuleData(moduleId) {
            const url = `/modules/${moduleId}/get-data`;
            axios.get(url)
                .then(response => {
                    const { module, consultants } = response.data;
                    
                    document.getElementById('edit-module-id').value = module.id;
                    document.getElementById('edit-title').value = module.title;
                    document.getElementById('edit-order').value = module.order;
                    document.getElementById('edit-status').value = module.status;
                    document.getElementById('edit-content').value = module.content.join('\n');
                    
                    populateConsultantsSelect(document.getElementById('edit-user'), module.user_id);

                    editModuleModal.show();
                })
                .catch(error => {
                    console.error('Error fetching module data:', error);
                    showAlert('Failed to load module data. Please try again.', 'danger');
                });
        }
        
        // Handle form submission for creating a new module
        createModuleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = {
                formation_id: '{{ $formation->id }}', // Kanstkhdem ID dyal l'formation l'actuelle
                modules: [
                    {
                        title: document.getElementById('create-title').value,
                        status: document.getElementById('create-status').value,
                        content: document.getElementById('create-content').value,
                        user_id: document.getElementById('create-user').value
                    }
                ]
            };
            
            axios.post('{{ route('modules.store') }}', formData)
                .then(response => {
                    createModuleModal.hide();
                    location.reload(); // Hadchi ghadi y3awd ychargi l'page bach yban l'module l'jdid
                    showAlert('Module created successfully!', 'success');
                })
                .catch(error => {
                    console.error('Create error:', error);
                    showAlert('Failed to create module.', 'danger');
                });
        });


        // Handle form submission for editing
        editModuleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const moduleId = document.getElementById('edit-module-id').value;
            const url = `/modules/${moduleId}`;
            
            const formData = {
                title: document.getElementById('edit-title').value,
                order: document.getElementById('edit-order').value,
                status: document.getElementById('edit-status').value,
                content: document.getElementById('edit-content').value,
                user_id: document.getElementById('edit-user').value
            };
            
            axios.put(url, formData)
                .then(response => {
                    const updatedModules = response.data.modules;
                    updateModulesList(updatedModules);
                    editModuleModal.hide();
                    showAlert('Module updated successfully!', 'success');
                })
                .catch(error => {
                    console.error('Update error:', error);
                    
                    // Kan7awlou nbyenou les erreurs li jaw men backend
                    if (error.response && error.response.data.errors) {
                        const errors = error.response.data.errors;
                        let errorMessage = 'Failed to update module:<br>';
                        for (const key in errors) {
                            errorMessage += `- ${errors[key][0]}<br>`;
                        }
                        showAlert(errorMessage, 'danger');
                    } else {
                        showAlert('An unknown error occurred. Please try again.', 'danger');
                    }
                });
        });

        // Function to update the modules list on the page
        function updateModulesList(modules) {
            const modulesList = document.getElementById('modules-list');
            modulesList.innerHTML = '';

            modules.forEach(module => {
                const moduleHtml = `
                    <div class="col" id="module-card-${module.id}">
                        <div class="card border-primary h-100">
                            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                                <span class="module-title">Module ${module.order}: ${module.title}</span>
                                <div>
                                    <button class="btn btn-sm btn-light edit-btn" data-bs-toggle="modal" data-bs-target="#editModuleModal" data-id="${module.id}">
                                        <i class="fas fa-edit"></i> Edit
                                    </button>
                                    <button class="btn btn-sm btn-danger delete-btn" data-id="${module.id}">
                                        <i class="fas fa-trash"></i> Delete
                                    </button>
                                </div>
                            </div>
                            <div class="card-body">
                                <p><strong>Assigned Consultant:</strong> <span class="module-consultant">${module.user.name ?? 'N/A'}</span></p>
                                <p><strong>Status:</strong> <span class="badge module-status bg-${module.status === 'published' ? 'success' : 'warning'}" data-status="${module.status}">${module.status}</span></p>
                                
                                <div class="mb-3">
                                    <strong>Content:</strong>
                                    <ul class="list-group list-group-flush mt-2 module-content">
                                        ${module.content.map(item => `<li class="list-group-item">${item}</li>`).join('')}
                                    </ul>
                                </div>
                                
                                <hr>
                                <strong>Module Progress:</strong>
                                <div class="progress mt-2" style="height: 25px;">
                                    <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: ${module.progress}%;" aria-valuenow="${module.progress}" aria-valuemin="0" aria-valuemax="100">${module.progress}%</div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                modulesList.insertAdjacentHTML('beforeend', moduleHtml);
            });
        }

        // Function to handle module deletion
        function deleteModule(moduleId) {
            const url = `/modules/${moduleId}`;
            axios.delete(url)
                .then(response => {
                    document.getElementById('module-card-' + moduleId).remove();
                    showAlert('Module deleted successfully!', 'success');
                })
                .catch(error => {
                    console.error('Deletion error:', error);
                    showAlert('Failed to delete module.', 'danger');
                });
        }

        // Show a temporary alert message
        function showAlert(message, type) {
            const alertHtml = `<div class="alert alert-${type} alert-dismissible fade show" role="alert">${message}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
            alertContainer.innerHTML = alertHtml;
        }
    });
</script>
@endsection