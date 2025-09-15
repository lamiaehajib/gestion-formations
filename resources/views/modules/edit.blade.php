@extends('layouts.app')

@section('content')
<div class="container">
    <div class="card">
        <div class="card-header">Edit Module</div>
        <div class="card-body">
            <form action="{{ route('modules.update', $module->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label for="formation_id" class="form-label">Formation:</label>
                    <select name="formation_id" id="formation_id" class="form-select @error('formation_id') is-invalid @enderror">
                        @foreach($formations as $formation)
                            <option value="{{ $formation->id }}" {{ $module->formation_id == $formation->id ? 'selected' : '' }}>
                                {{ $formation->title }}
                            </option>
                        @endforeach
                    </select>
                    @error('formation_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label for="title" class="form-label">Module Title:</label>
                    <input type="text" name="title" id="title" class="form-control" value="{{ old('title', $module->title) }}" required>
                </div>
                
                <div class="mb-3">
                    <label for="order" class="form-label">Order:</label>
                    <input type="number" name="order" id="order" class="form-control" value="{{ old('order', $module->order) }}" required>
                </div>

                <div class="mb-3">
                    <label for="status" class="form-label">Status:</label>
                    <select name="status" id="status" class="form-select">
                        <option value="draft" {{ $module->status == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="published" {{ $module->status == 'published' ? 'selected' : '' }}>Published</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="user_id" class="form-label">Assign Consultant:</label>
                    <select name="user_id" id="user_id" class="form-select" required>
                        @foreach($consultants as $consultant)
                            <option value="{{ $consultant->id }}" {{ $module->user_id == $consultant->id ? 'selected' : '' }}>
                                {{ $consultant->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="mb-3">
                    <label for="content" class="form-label">Content (Enter one item per line):</label>
                    <textarea name="content" id="content" class="form-control" rows="5" required>{{ old('content', implode("\n", $module->content)) }}</textarea>
                </div>
                
                <button type="submit" class="btn btn-primary">Update Module</button>
                <a href="{{ route('modules.index') }}" class="btn btn-secondary">Cancel</a>
            </form>
        </div>
    </div>
</div>
@endsection