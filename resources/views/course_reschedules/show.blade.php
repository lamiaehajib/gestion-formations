@extends('layouts.app')

@section('title', 'Course Reschedule Details')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<style>
    /* Consistent Color Variables from Index Page */
    :root {
        --primary-red: #D32F2F;
        --secondary-pink: #C2185B;
        --accent-red: #ef4444;
        --gradient-primary: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
        --gradient-secondary: linear-gradient(135deg, #ef4444 0%, #D32F2F 100%);
        --gradient-light: linear-gradient(135deg, rgba(211,47,47,0.1) 0%, rgba(194,24,91,0.1) 100%);
        --shadow-red: rgba(211, 47, 47, 0.3);
        --shadow-pink: rgba(194, 24, 91, 0.3);
    }

    body {
        background: linear-gradient(135deg, #f8f9ff 0%, #fff0f5 100%); /* Consistent background */
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        color: #333; /* Default text color */
    }

    .card-modern {
        border: none;
        border-radius: 20px; /* Consistent border-radius */
        box-shadow: 0 15px 35px rgba(0,0,0,0.08); /* Consistent shadow */
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        backdrop-filter: blur(10px);
        background: rgba(255,255,255,0.95);
    }

    /* Gradient header (for modal or specific card headers) */
    .gradient-header {
        background: var(--gradient-primary); /* Use primary gradient */
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 25px 30px;
        position: relative;
        overflow: hidden;
    }

    /* Header ::before animation remains the same */
    .gradient-header::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.1'%3E%3Ccircle cx='7' cy='7' r='7'/%3E%3Ccircle cx='53' cy='53' r='7'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
        animation: float 20s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-20px) rotate(180deg); }
    }

    .btn-modern {
        border-radius: 30px; /* Consistent button radius */
        padding: 12px 30px;
        font-weight: 600;
        font-size: 14px; /* Consistent font size */
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        border: none;
        position: relative;
        overflow: hidden;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Primary action button (Edit) */
    .btn-primary-modern {
        background: var(--gradient-secondary); /* Use the secondary gradient for primary action */
        color: white;
        box-shadow: 0 8px 25px var(--shadow-red); /* Consistent shadow for primary buttons */
    }

    .btn-primary-modern{
        transform: translateY(-3px) scale(1.05); /* Consistent hover effect */
        box-shadow: 0 15px 35px var(--shadow-red);
        color: white;
    }

    .btn-primary-modern::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
        transition: left 0.5s;
    }

    .btn-primary-modern:hover::before {
        left: 100%;
    }

    /* Secondary action button (Back to List) */
    .btn-secondary-modern {
        background-color: #6c757d; /* Standard Bootstrap secondary color for consistency */
        color: white;
        box-shadow: 0 5px 15px rgba(108, 117, 125, 0.2); /* Subtle shadow for secondary */
    }

    .btn-secondary-modern:hover {
        background-color: #5a6268; /* Darker on hover */
        color: white;
        transform: translateY(-2px); /* Subtle lift on hover */
    }

    /* Outline danger for "Delete" button */
    .btn-outline-danger.btn-modern {
        border: 2px solid var(--primary-red); /* Use primary red for outline */
        color: var(--primary-red);
        background-color: transparent;
        box-shadow: none;
    }

    .btn-outline-danger.btn-modern{
        background: var(--primary-red); /* Fill with color on hover */
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px var(--shadow-red);
    }


    .detail-item {
        margin-bottom: 20px; /* More space between items */
        padding-bottom: 15px; /* Padding for the dashed border */
        border-bottom: 1px dashed rgba(211,47,47,0.15); /* Themed dashed border */
    }
    .detail-item:last-child {
        border-bottom: none;
        margin-bottom: 0;
        padding-bottom: 0;
    }
    .detail-item strong {
        display: block;
        margin-bottom: 8px; /* More space between label and value */
        color: var(--secondary-pink); /* Use secondary pink for labels */
        font-size: 1.05rem; /* Slightly larger label font */
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 700;
    }
    .detail-item span {
        font-size: 1.2rem; /* Larger value text */
        color: #555; /* General text color for values */
        font-weight: 600;
    }

    /* Badge styles (copied from index for consistency) */
    .badge-date { /* Specific class for date badges */
        border-radius: 20px;
        padding: 10px 18px;
        font-weight: 600;
        font-size: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        position: relative;
        overflow: hidden;
    }
    .badge-date::before { /* Shimmer effect for badges */
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }
    @keyframes shimmer {
        0% { left: -100%; }
        100% { left: 100%; }
    }
    .badge-original {
        background: var(--gradient-secondary) !important; /* Red gradient for original */
        box-shadow: 0 4px 15px var(--shadow-red);
        color: white;
    }
    .badge-new {
        background: linear-gradient(135deg, #4CAF50 0%, #8BC34A 100%) !important; /* Green gradient for new */
        box-shadow: 0 4px 15px rgba(76, 175, 80, 0.3);
        color: white;
    }

    /* Alert styles (for delete modal) */
    .alert-warning {
        background: linear-gradient(135deg, #fff3cd 0%, #ffeaa7 100%);
        border: 2px solid #ffc107;
        border-radius: 15px;
        color: #856404;
    }

    /* Modal styles (consistent with index page) */
    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    }
    .modal-header {
        background: var(--gradient-primary);
        color: white;
        border-radius: 20px 20px 0 0;
        border: none;
        padding: 20px;
        font-weight: 700;
    }
    .modal-header .btn-close {
        filter: invert(1) grayscale(100%) brightness(200%); /* White close button */
    }
    .modal-body {
        padding: 30px;
    }
    .modal-footer {
        padding: 20px 30px;
        border-top: 1px solid rgba(0,0,0,0.05);
    }
    .modal-title {
        color: white;
        font-weight: 700;
        display: flex;
        align-items: center;
    }
    .modal-title i {
        margin-right: 10px;
    }


    /* Page header specific to this "Show" page */
    .py-4 {
        padding-top: 2.5rem !important;
        padding-bottom: 2.5rem !important;
    }

    .mb-4 {
        margin-bottom: 2.5rem !important;
    }

    h2 {
        font-size: 2rem;
        color: var(--primary-red); /* Main heading color from index theme */
    }

    .text-muted {
        color: #888 !important;
    }

    .fas.fa-info-circle { /* Icon in page header */
        color: var(--primary-red) !important; /* Icon color consistent with primary theme */
    }

    /* Keyframes for button ripple (if not already global) */
    @keyframes ripple {
        to {
            transform: scale(4);
            opacity: 0;
        }
    }

    /* Basic entrance animation for the card */
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .card-modern {
        animation: fadeIn 0.6s ease-out forwards;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="fw-bold mb-1">
                        <i class="fas fa-info-circle me-3"></i>
                        Reschedule Details
                    </h2>
                    <p class="text-muted mb-0">Detailed information about the course reschedule</p>
                </div>
                <div>
                    <a href="{{ route('course_reschedules.index') }}" class="btn btn-secondary-modern btn-modern me-2">
                        <i class="fas fa-arrow-left me-2"></i>Back to List
                    </a>
                    @can('course-edit')
                        @if(Auth::user()->can('course-manage-all') || $reschedule->consultant_id == Auth::id())
                        <a href="{{ route('course_reschedules.edit', $reschedule->id) }}" class="btn btn-primary-modern btn-modern">
                            <i class="fas fa-edit me-2"></i>Edit Reschedule
                        </a>
                        @endif
                    @endcan
                </div>
            </div>
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-header gradient-header">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-file-alt me-2"></i>Reschedule for: {{ $reschedule->course->title }}
            </h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="detail-item">
                        <strong>Course Title:</strong>
                        <span>{{ $reschedule->course->title }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Course ID:</strong>
                        <span>#{{ $reschedule->course->id }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Original Date & Time:</strong>
                        <span class="badge badge-original badge-date">
                            <i class="fas fa-calendar-times me-1"></i>
                            {{ 
            \Carbon\Carbon::parse($reschedule->course->course_date)
                ->setTimeFromTimeString($reschedule->course->start_time)
                ->format('d/m/Y H:i') 
        }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <strong>New Date & Time:</strong>
                        <span class="badge badge-new badge-date">
                            <i class="fas fa-calendar-check me-1"></i>
                            {{ \Carbon\Carbon::parse($reschedule->new_date)->format('d/m/Y H:i') }}
                        </span>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="detail-item">
                        <strong>Rescheduled By Consultant:</strong>
                        <span>{{ $reschedule->consultant->name }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Consultant Email:</strong>
                        <span>{{ $reschedule->consultant->email }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Reason for Reschedule:</strong>
                        <span>{{ $reschedule->reason ?: 'No reason provided.' }}</span>
                    </div>
                    <div class="detail-item">
                        <strong>Rescheduled On:</strong>
                        <span>
                            {{ $reschedule->created_at->format('d/m/Y H:i') }}
                            ({{ $reschedule->created_at->diffForHumans() }})
                        </span>
                    </div>
                    <div class="detail-item">
                        <strong>Last Updated:</strong>
                        <span>
                            {{ $reschedule->updated_at->format('d/m/Y H:i') }}
                            ({{ $reschedule->updated_at->diffForHumans() }})
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer d-flex justify-content-end">
            @can('course-delete')
                @if(Auth::user()->can('course-manage-all') || $reschedule->consultant_id == Auth::id())
                <button type="button" class="btn btn-outline-danger btn-modern" 
                        onclick="confirmDelete({{ $reschedule->id }})">
                    <i class="fas fa-trash-alt me-2"></i>Delete Reschedule
                </button>
                @endif
            @endcan
        </div>
    </div>
</div>

{{-- Delete Confirmation Modal (Consistent with Index Page) --}}
<div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title fw-bold" id="deleteModalLabel">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirm Deletion
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p class="mb-3">Are you sure you want to delete this reschedule record?</p>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong>Warning:</strong> This action cannot be undone!
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-modern" data-bs-dismiss="modal">
                    <i class="fas fa-times me-2"></i>Cancel
                </button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-modern">
                        <i class="fas fa-trash me-2"></i>Delete
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Initialize tooltips (if any are present)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });

    // Add ripple effect to buttons
    document.querySelectorAll('.btn-modern').forEach(btn => {
        btn.addEventListener('click', function(e) {
            const ripple = document.createElement('span');
            const rect = this.getBoundingClientRect();
            const size = Math.max(rect.width, rect.height);
            const x = e.clientX - rect.left - size / 2;
            const y = e.clientY - rect.top - size / 2;
            
            ripple.style.cssText = `
                width: ${size}px;
                height: ${size}px;
                left: ${x}px;
                top: ${y}px;
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.5);
                transform: scale(0);
                animation: ripple 0.6s linear;
                pointer-events: none;
                z-index: 1; /* Ensure ripple is above button content but below text */
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });

    // Add basic entrance animation for the card
    const card = document.querySelector('.card-modern');
    if (card) {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.animation = 'fadeIn 0.6s ease-out forwards';
    }
});

function confirmDelete(rescheduleId) {
    const form = document.getElementById('deleteForm');
    form.action = `/course-reschedules/${rescheduleId}`; // Ensure this matches your route
    
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    deleteModal.show();

    // Add shake animation to modal
    const modalDialog = document.querySelector('#deleteModal .modal-dialog');
    modalDialog.style.animation = 'shake 0.5s ease-in-out';
    setTimeout(() => {
        modalDialog.style.animation = ''; // Remove animation after it plays
    }, 500);
}

// Add custom keyframe animations if not already defined globally in a master CSS file
const style = document.createElement('style');
style.textContent = `
    @keyframes shake {
        0%, 100% { transform: translateX(0); }
        10%, 30%, 50%, 70%, 90% { transform: translateX(-5px); }
        20%, 40%, 60%, 80% { transform: translateX(5px); }
    }
`;
document.head.appendChild(style);
</script>
@endpush