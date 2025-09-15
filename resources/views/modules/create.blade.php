@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Create New Modules</div>
        <div class="card-body">
            @if (session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('modules.store') }}" method="POST">
                @csrf

                <div class="mb-3">
                    <label for="formation_id" class="form-label">Choose Formation:</label>
                    <select name="formation_id" id="formation_id" class="form-select @error('formation_id') is-invalid @enderror">
                        @foreach($formations as $formation)
                            <option value="{{ $formation->id }}">{{ $formation->title }}</option>
                        @endforeach
                    </select>
                    @error('formation_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div id="modules-container">
                    <div class="module-group p-4 border rounded mb-3 bg-light">
                        <h5 class="mb-3">Module 1</h5>
                        <div class="mb-3">
                            <label for="modules[0][title]" class="form-label">Module Title:</label>
                            <input type="text" name="modules[0][title]" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label for="modules[0][status]" class="form-label">Status:</label>
                            <select name="modules[0][status]" class="form-select">
                                <option value="draft">Draft</option>
                                <option value="published">Published</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modules[0][user_id]" class="form-label">Assign Consultant:</label>
                            <select name="modules[0][user_id]" class="form-select" required>
                                @foreach($consultants as $consultant)
                                    <option value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="modules[0][content]" class="form-label">Content (Enter one item per line):</label>
                            <textarea name="modules[0][content]" class="form-control" rows="3" placeholder='e.g.,
Introduction to PHP
Working with Databases' required></textarea>
                        </div>
                    </div>
                </div>
                
                <button type="button" id="add-module" class="btn btn-secondary me-2">Add Another Module</button>
                <button type="submit" class="btn btn-primary">Save All Modules</button>
            </form>
        </div>
    </div>
</div>

<script>
    document.getElementById('add-module').addEventListener('click', function() {
        const container = document.getElementById('modules-container');
        const index = container.children.length;
        
        const newModule = `
            <div class="module-group p-4 border rounded mb-3 bg-light">
                <h5 class="mb-3">Module ${index + 1}</h5>
                <div class="mb-3">
                    <label for="modules[${index}][title]" class="form-label">Module Title:</label>
                    <input type="text" name="modules[${index}][title]" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="modules[${index}][status]" class="form-label">Status:</label>
                    <select name="modules[${index}][status]" class="form-select">
                        <option value="draft">Draft</option>
                        <option value="published">Published</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="modules[${index}][user_id]" class="form-label">Assign Consultant:</label>
                    <select name="modules[${index}][user_id]" class="form-select" required>
                        @foreach($consultants as $consultant)
                            <option value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label for="modules[${index}][content]" class="form-label">Content (Enter one item per line):</label>
                    <textarea name="modules[${index}][content]" class="form-control" rows="3" placeholder='e.g.,
Introduction to PHP
Working with Databases' required></textarea>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newModule);
    });
</script>
@endsection