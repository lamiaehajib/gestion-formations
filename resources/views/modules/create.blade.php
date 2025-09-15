@extends('layouts.app')

@section('content')
<style>
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
    .btn-secondary-custom {
        background: linear-gradient(45deg, #D32F2F, #ef4444);
        border: none;
        color: white;
        box-shadow: 0 4px 15px rgba(211, 47, 47, 0.3);
    }
    .btn-secondary-custom:hover {
        background: linear-gradient(45deg, #B71C1C, #dc2626);
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(211, 47, 47, 0.4);
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
</style>

<div class="container-fluid py-5" style="background: linear-gradient(135deg, #fdf2f8 0%, #fce7f3 50%, #fecaca 100%); min-height: 100vh;">
    <div class="container">
        <div class="card shadow-xl border-0 overflow-hidden" style="border-radius: 20px;">
            <!-- Beautiful Header -->
            <div class="gradient-bg text-white p-5">
                <div class="d-flex align-items-center">
                    <div class="me-4 p-3 bg-red bg-opacity-20 rounded-3 icon-bounce">
                        <i class="fas fa-puzzle-piece fa-2x"></i>
                    </div>
                    <div>
                        <h1 class="mb-2 fw-bold">Create New Modules</h1>
                        <p class="mb-0 opacity-90"><i class="fas fa-magic me-2"></i>Build your formation modules with style</p>
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

                    <!-- Formation Selection -->
                    <div class="mb-4 p-4 rounded-4 shadow-sm" style="background: linear-gradient(145deg, #ffffff, #fdf2f8);">
                        <label for="formation_id" class="form-label fw-bold text-dark mb-3">
                            <i class="fas fa-graduation-cap me-2" style="color: #C2185B;"></i>Choose Formation:
                        </label>
                        <select name="formation_id" id="formation_id" class="form-select form-select-lg rounded-3 shadow-sm @error('formation_id') is-invalid @enderror">
                            @foreach($formations as $formation)
                                <option value="{{ $formation->id }}">{{ $formation->title }}</option>
                            @endforeach
                        </select>
                        @error('formation_id')
                            <div class="invalid-feedback d-flex align-items-center">
                                <i class="fas fa-exclamation-triangle me-2"></i>{{ $message }}
                            </div>
                        @enderror
                    </div>

                    <div id="modules-container">
                        <!-- First Module -->
                        <div class="module-card rounded-4 p-4 mb-4 shadow-lg">
                            <div class="d-flex align-items-center mb-4">
                                <div class="me-3 p-2 rounded-3" style="background: linear-gradient(45deg, #C2185B, #D32F2F);">
                                    <i class="fas fa-cube text-white"></i>
                                </div>
                                <h5 class="mb-0 fw-bold" style="color: #C2185B;">Module 1</h5>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="modules[0][title]" class="form-label fw-semibold">
                                        <i class="fas fa-heading me-2" style="color: #D32F2F;"></i>Module Title:
                                    </label>
                                    <input type="text" name="modules[0][title]" class="form-control form-control-lg rounded-3 shadow-sm" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="modules[0][status]" class="form-label fw-semibold">
                                        <i class="fas fa-toggle-on me-2" style="color: #ef4444;"></i>Status:
                                    </label>
                                    <select name="modules[0][status]" class="form-select form-select-lg rounded-3 shadow-sm">
                                        <option value="draft"><i class="fas fa-edit"></i> Draft</option>
                                        <option value="published"><i class="fas fa-globe"></i> Published</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="mb-3">
                                <label for="modules[0][user_id]" class="form-label fw-semibold">
                                    <i class="fas fa-user-tie me-2" style="color: #C2185B;"></i>Assign Consultant:
                                </label>
                                <select name="modules[0][user_id]" class="form-select form-select-lg rounded-3 shadow-sm" required>
                                    @foreach($consultants as $consultant)
                                        <option value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            
                            <div class="mb-3">
                                <label for="modules[0][content]" class="form-label fw-semibold">
                                    <i class="fas fa-file-alt me-2" style="color: #D32F2F;"></i>Content (Enter one item per line):
                                </label>
                                <textarea name="modules[0][content]" class="form-control rounded-3 shadow-sm" rows="4" 
                                    placeholder="e.g.,
Introduction to PHP
Working with Databases
Advanced Concepts" required></textarea>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex justify-content-center gap-3 mt-5">
                        <button type="button" id="add-module" class="btn btn-secondary-custom btn-lg px-5 py-3 rounded-3 fw-bold">
                            <i class="fas fa-plus-circle me-2"></i>Add Another Module
                        </button>
                        <button type="submit" class="btn btn-primary-custom btn-lg px-5 py-3 rounded-3 fw-bold">
                            <i class="fas fa-save me-2"></i>Save All Modules
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    document.getElementById('add-module').addEventListener('click', function() {
        const container = document.getElementById('modules-container');
        const index = container.children.length;
        
        const newModule = `
            <div class="module-card rounded-4 p-4 mb-4 shadow-lg">
                <div class="d-flex align-items-center mb-4">
                    <div class="me-3 p-2 rounded-3" style="background: linear-gradient(45deg, #C2185B, #D32F2F);">
                        <i class="fas fa-cube text-white"></i>
                    </div>
                    <h5 class="mb-0 fw-bold" style="color: #C2185B;">Module ${index + 1}</h5>
                </div>
                
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="modules[${index}][title]" class="form-label fw-semibold">
                            <i class="fas fa-heading me-2" style="color: #D32F2F;"></i>Module Title:
                        </label>
                        <input type="text" name="modules[${index}][title]" class="form-control form-control-lg rounded-3 shadow-sm" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="modules[${index}][status]" class="form-label fw-semibold">
                            <i class="fas fa-toggle-on me-2" style="color: #ef4444;"></i>Status:
                        </label>
                        <select name="modules[${index}][status]" class="form-select form-select-lg rounded-3 shadow-sm">
                            <option value="draft">Draft</option>
                            <option value="published">Published</option>
                        </select>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="modules[${index}][user_id]" class="form-label fw-semibold">
                        <i class="fas fa-user-tie me-2" style="color: #C2185B;"></i>Assign Consultant:
                    </label>
                    <select name="modules[${index}][user_id]" class="form-select form-select-lg rounded-3 shadow-sm" required>
                        @foreach($consultants as $consultant)
                            <option value="{{ $consultant->id }}">{{ $consultant->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="mb-3">
                    <label for="modules[${index}][content]" class="form-label fw-semibold">
                        <i class="fas fa-file-alt me-2" style="color: #D32F2F;"></i>Content (Enter one item per line):
                    </label>
                    <textarea name="modules[${index}][content]" class="form-control rounded-3 shadow-sm" rows="4" 
                        placeholder="e.g.,
Introduction to PHP
Working with Databases
Advanced Concepts" required></textarea>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', newModule);
    });
</script>
@endsection