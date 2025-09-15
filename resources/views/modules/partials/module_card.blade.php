<div class="col" id="module-card-{{ $module->id }}">
    <div class="card border-primary h-100">
        <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
            <span class="module-title">Module {{ $module->order }}: {{ $module->title }}</span>
            <div>
                <button class="btn btn-sm btn-light edit-btn" data-bs-toggle="modal" data-bs-target="#editModuleModal"
                        data-id="{{ $module->id }}"
                        data-consultant-id="{{ $module->user_id }}">
                    <i class="fas fa-edit"></i> Edit
                </button>
                <button class="btn btn-sm btn-danger delete-btn" data-id="{{ $module->id }}">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
        <div class="card-body">
            <p><strong>Assigned Consultant:</strong> {{ $module->user->name ?? 'N/A' }}</p>
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