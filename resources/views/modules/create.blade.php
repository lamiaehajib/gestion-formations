@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
<style>
    /* Your existing CSS styles go here */
    .gradient-bg {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 50%, #ef4444 100%);
    }
    .form-control:focus, .form-select:focus {
        border-color: #C2185B;
        box-shadow: 0 0 0 0.2rem rgba(194, 24, 91, 0.15);
    }
    .btn-primary-custom {
        background: linear-gradient(45deg, #C2185B, #D32F2F);
        border: none;
        box-shadow: 0 4px 15px rgba(194, 24, 91, 0.3);
    }
    .btn-primary-custom:hover {
        background: linear-gradient(45deg, #A91749, #B71C1C);
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(194, 24, 91, 0.4);
    }
    .module-card {
        background: linear-gradient(145deg, #ffffff 0%, #fdf2f8 100%);
        border: 2px solid transparent;
        background-clip: padding-box;
        position: relative;
    }
    .module-card::before {
        content: '';
        position: absolute;
        inset: 0;
        padding: 2px;
        background: linear-gradient(145deg, #C2185B, #D32F2F, #ef4444);
        border-radius: inherit;
        mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
        mask-composite: exclude;
        z-index: -1;
    }
    .icon-bounce {
        animation: bounce 2s infinite;
    }
    @keyframes bounce {
        0%, 20%, 50%, 80%, 100% { transform: translateY(0); }
        40% { transform: translateY(-10px); }
        60% { transform: translateY(-5px); }
    }
    .form-label, .fw-semibold {
        font-weight: 600;
        color: #555;
    }
</style>

<div class="container-fluid py-5" style="background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 50%, #fecaca 100%); min-height: 100vh;">
    <div class="container">
        <div class="card shadow-xl border-0 overflow-hidden" style="border-radius: 20px;">
            <div class="gradient-bg text-white p-5">
                <div class="d-flex align-items-center">
                    <div class="me-4 p-3 bg-red bg-opacity-20 rounded-3 icon-bounce">
                        <i class="fas fa-puzzle-piece fa-2x"></i>
                    </div>
                    <div>
                        <h1 class="mb-2 fw-bold">Create New Module</h1>
                        <p class="mb-0 opacity-90"><i class="fas fa-magic me-2"></i>Design a standalone, reusable module.</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-5">
                @if (session('success'))
                    <div class="alert alert-success border-0 rounded-4 shadow-sm mb-4" style="background: linear-gradient(45deg, #d4edda, #c3e6cb);">
                        <div class="d-flex align-items-center">
                            <i class="fas fa-check-circle me-3 text-success fa-lg"></i>
                            {{ session('success') }}
                        </div>
                    </div>
                @endif
                
                <form action="{{ route('modules.store') }}" method="POST">
                    @csrf

                    <div class="module-card rounded-4 p-4 mb-4 shadow-lg">
                        <div class="d-flex align-items-center mb-4">
                            <div class="me-3 p-2 rounded-3" style="background: linear-gradient(45deg, #C2185B, #D32F2F);">
                                <i class="fas fa-cube text-white"></i>
                            </div>
                            <h5 class="mb-0 fw-bold" style="color: #C2185B;">Module Details</h5>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="title" class="form-label fw-semibold">
                                    <i class="fas fa-heading me-2" style="color: #D32F2F;"></i>Module Title:
                                </label>
                                <input type="text" name="title" id="title" class="form-control form-control-lg rounded-3 shadow-sm @error('title') is-invalid @enderror" value="{{ old('title') }}" required>
                                @error('title')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="duration_hours" class="form-label fw-semibold">
                                    <i class="fas fa-clock me-2" style="color: #C2185B;"></i>Duration (in hours):
                                </label>
                                <input type="number" name="duration_hours" id="duration_hours" class="form-control form-control-lg rounded-3 shadow-sm @error('duration_hours') is-invalid @enderror" value="{{ old('duration_hours') }}" min="0">
                                @error('duration_hours')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="number_seance" class="form-label fw-semibold">
                                    <i class="fas fa-calendar-alt me-2" style="color: #C2185B;"></i>Number of Sessions:
                                </label>
                                <input type="number" name="number_seance" id="number_seance" class="form-control form-control-lg rounded-3 shadow-sm @error('number_seance') is-invalid @enderror" value="{{ old('number_seance') }}" min="1">
                                @error('number_seance')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="status" class="form-label fw-semibold">
                                    <i class="fas fa-toggle-on me-2" style="color: #ef4444;"></i>Status:
                                </label>
                                <select name="status" id="status" class="form-select form-select-lg rounded-3 shadow-sm @error('status') is-invalid @enderror">
                                    <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                                    <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Published</option>
                                </select>
                                @error('status')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="user_id" class="form-label fw-semibold">
                                <i class="fas fa-user-tie me-2" style="color: #C2185B;"></i>Assign Consultant:
                            </label>
                            <select name="user_id" id="user_id" class="form-select form-select-lg rounded-3 shadow-sm @error('user_id') is-invalid @enderror" required>
                                @foreach($consultants as $consultant)
                                    <option value="{{ $consultant->id }}" {{ old('user_id') == $consultant->id ? 'selected' : '' }}>{{ $consultant->name }}</option>
                                @endforeach
                            </select>
                            @error('user_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        {{-- L'HAQL JDEED LLI BAGHI: Multi-Select dial les Formations --}}
                       <div class="mb-4 p-3 bg-red-50 rounded-3" style="border: 1px dashed #D32F2F;">
    <label class="form-label fw-bold fs-5 text-danger">
        <i class="fas fa-graduation-cap me-2 text-danger"></i>Select Formations:
    </label>
    
    <div class="row">
        @foreach($formations as $formation)
            <div class="col-md-6 mb-2">
                <div class="form-check">
                    <input 
                        class="form-check-input @error('formation_ids') is-invalid @enderror" 
                        type="checkbox" 
                        name="formation_ids[]" 
                        value="{{ $formation->id }}" 
                        id="formation_{{ $formation->id }}"
                        {{ in_array($formation->id, old('formation_ids', [])) ? 'checked' : '' }}
                    >
                    <label class="form-check-label" for="formation_{{ $formation->id }}">
                        {{ $formation->title }}
                    </label>
                </div>
            </div>
        @endforeach
    </div>
    
    @error('formation_ids')
        <div class="invalid-feedback d-block">{{ $message }}</div>
    @enderror
    
    <small class="text-muted mt-2 d-block">
        <i class="fas fa-info-circle me-1"></i>You can select multiple formations.
    </small>
</div>
                        {{-- END L'HAQL JDEED --}}

                        <div class="mb-3">
                            <label for="content" class="form-label fw-semibold">
                                <i class="fas fa-file-alt me-2" style="color: #D32F2F;"></i>Content (Enter one item per line):
                            </label>
                            <textarea name="content" id="content" class="form-control rounded-3 shadow-sm @error('content') is-invalid @enderror" rows="4" 
                                placeholder="e.g.,
Introduction to PHP
Working with Databases
Advanced Concepts" required>{{ old('content') }}</textarea>
                            @error('content')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-center mt-5">
                        <button type="submit" class="btn btn-primary-custom btn-lg px-5 py-3 rounded-3 fw-bold">
                            <i class="fas fa-save me-2"></i>Save Module
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
