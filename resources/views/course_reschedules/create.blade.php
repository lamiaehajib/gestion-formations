@extends('layouts.app')

@section('title', 'Create Course Reschedule')

@push('styles')
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css" rel="stylesheet">
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

    
    .btn-primary-modern {
        background: var(--gradient-secondary); /* Use the secondary gradient for primary action */
        color: white;
        box-shadow: 0 8px 25px var(--shadow-red); /* Consistent shadow for primary buttons */
    }

    .btn-primary-modern:hover {
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

    /* Outline secondary for "Clear Form" */
    .btn-outline-secondary.btn-modern {
        border: 2px solid var(--secondary-pink); /* Use a primary theme color for outline */
        color: var(--secondary-pink);
        background-color: transparent;
        box-shadow: none; /* No initial shadow for outline */
    }

    .btn-outline-secondary.btn-modern{
        background: var(--secondary-pink); /* Fill with color on hover */
        color: white;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px var(--shadow-pink); /* Add shadow on hover */
    }

    .form-label {
        font-weight: 700;
        color: #4a4a4a; /* Darker text for labels */
        margin-bottom: 8px;
    }

    .form-control-modern, .form-select-modern {
        border-radius: 15px; /* Consistent with index page inputs */
        border: 2px solid rgba(211,47,47,0.1); /* Consistent border */
        padding: 12px 18px; /* Consistent padding */
        font-weight: 500;
        transition: all 0.3s ease;
        background-color: #ffffff;
    }

    .form-control-modern:focus, .form-select-modern:focus {
        border-color: var(--primary-red); /* Consistent focus color */
        box-shadow: 0 0 20px rgba(211,47,47,0.2); /* Consistent focus shadow */
    }

    .form-text {
        font-size: 0.85em;
        color: #777;
        margin-top: 5px;
    }

    /* Alert styles (adjusting existing ones to fit theme) */
    .alert-danger {
        background: linear-gradient(135deg, #fde7e7 0%, #fcdede 100%); /* Lighter, themed background */
        border: 1px solid #e57373; /* Slightly darker red border */
        border-radius: 15px; /* Consistent with other rounded elements */
        color: #c62828; /* Stronger red text */
        padding: 15px 20px;
    }

    .alert-info-modern {
        background: linear-gradient(135deg, #e0f2f7 0%, #d4eaf0 100%); /* Light blue gradient */
        color: #005662;
        border-left: 5px solid #00acc1;
        border-radius: 15px;
        padding: 15px 20px;
    }

    /* Page header specific to this "Create" page */
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

    .fas.fa-calendar-plus {
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
    a.btn.btn-secondary-modern.btn-modern {
    color: black !important;
    background-color: #db3f3f;
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
                        <i class="fas fa-calendar-plus me-3"></i>
                        Create New Course Reschedule
                    </h2>
                    <p class="text-muted mb-0">Fill in the details to reschedule a course</p>
                </div>
                {{-- Using btn-secondary-modern for the "Back to List" button --}}
                <a href="{{ route('course_reschedules.index') }}" class="btn btn-secondary-modern btn-modern">
                    <i class="fas fa-arrow-left me-2"></i>Back to List
                </a>
            </div>
        </div>
    </div>

    <div class="card card-modern">
        <div class="card-header gradient-header">
            <h5 class="mb-0 fw-bold">Reschedule Details</h5>
        </div>
        <div class="card-body">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Whoops!</strong> There were some problems with your input.
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <form action="{{ route('course_reschedules.store') }}" method="POST">
                @csrf

                {{-- This section only appears if the user has 'course-manage-all' permission (e.g., admin) --}}
                @can('course-manage-all') 
                <div class="row mb-3">
                    <div class="col-md-12">
                        <label for="consultant_id" class="form-label fw-bold">Select Consultant <span class="text-danger">*</span></label>
                        <select name="consultant_id" id="consultant_id" class="form-select form-select-modern @error('consultant_id') is-invalid @enderror" required>
                            <option value="">-- Select a Consultant --</option>
                            @foreach($consultants as $consultant)
                                <option value="{{ $consultant->id }}" {{ old('consultant_id', $selectedConsultantId ?? '') == $consultant->id ? 'selected' : '' }}>
                                    {{ $consultant->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('consultant_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Select the consultant whose course you want to reschedule.</small>
                    </div>
                </div>
                @else {{-- This section appears for consultants or students (where the consultant is implicitly the current user) --}}
                    <input type="hidden" name="consultant_id" value="{{ Auth::id() }}">
                @endcan

                {{-- Course Selection --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="course_id" class="form-label fw-bold">Select Course <span class="text-danger">*</span></label>
                        <select name="course_id" id="course_id" class="form-select form-select-modern @error('course_id') is-invalid @enderror" required>
                            <option value="">-- Select a Course --</option>
                            {{-- Courses will be dynamically loaded by JavaScript for admins, or pre-loaded for others --}}
                            @foreach($courses as $course)
                                <option 
                                    value="{{ $course->id }}" 
                                    data-original-date="{{ \Carbon\Carbon::parse($course->course_date)->format('Y-m-d') }}"
                                    data-start-time="{{ \Carbon\Carbon::parse($course->start_time)->format('H:i') }}"
                                    data-end-time="{{ \Carbon\Carbon::parse($course->end_time)->format('H:i') }}"
                                    {{ old('course_id', $selectedCourse ? $selectedCourse->id : '') == $course->id ? 'selected' : '' }}
                                >
                                    {{ $course->title }} ({{ \Carbon\Carbon::parse($course->course_date)->format('d/m/Y') }})
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="original_date_display" class="form-label fw-bold">Original Course Date & Time</label>
                        <input type="text" id="original_date_display" class="form-control form-control-modern" readonly disabled 
                               value="{{ $selectedCourse ? \Carbon\Carbon::parse($selectedCourse->course_date)->format('d/m/Y') . ' ' . \Carbon\Carbon::parse($selectedCourse->start_time)->format('H:i') . '-' . \Carbon\Carbon::parse($selectedCourse->end_time)->format('H:i') : 'Select a course' }}">
                        <small class="form-text text-muted">This is the current scheduled date and time for the selected course.</small>
                    </div>
                </div>

                {{-- New Date Field --}}
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="new_date" class="form-label fw-bold">New Date <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="new_date" id="new_date" class="form-control form-control-modern @error('new_date') is-invalid @enderror" 
                               value="{{ old('new_date') }}" required>
                        @error('new_date')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="form-text text-muted">Select the new date and time for the course.</small>
                    </div>
                </div>

                {{-- Reason Field --}}
                <div class="mb-4">
                    <label for="reason" class="form-label fw-bold">Reason for Rescheduling</label>
                    <textarea name="reason" id="reason" rows="4" class="form-control form-control-modern @error('reason') is-invalid @enderror" 
                                 placeholder="Provide a brief reason for the reschedule (optional)">{{ old('reason') }}</textarea>
                    @error('reason')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                {{-- Action Buttons --}}
                <div class="d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary-modern btn-modern">
                        <i class="fas fa-save me-2"></i>Reschedule Course
                    </button>
                    <button type="reset" class="btn btn-outline-secondary btn-modern">
                        <i class="fas fa-times me-2"></i>Clear Form
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ... b9a l'code dyal les tooltips hna ...

    // Flatpickr initialization for new_date field
    flatpickr("#new_date", {
        dateFormat: "Y-m-d H:i",
        enableTime: true,
        altInput: true,
        altFormat: "F j, Y H:i",
        minDate: "tomorrow", // <-- Hada howa l'changement l'mohim!
        allowInput: true,
        theme: 'material_red',
        locale: {
            firstDayOfWeek: 1
        },
    });

    const courseSelect = document.getElementById('course_id');
    const originalDateDisplay = document.getElementById('original_date_display');
    const consultantSelect = document.getElementById('consultant_id');

    // Function to update the display of the original course date
    function updateOriginalDateDisplay() {
        const selectedOption = courseSelect.options[courseSelect.selectedIndex];
        if (selectedOption && selectedOption.value) {
            const originalDate = selectedOption.dataset.originalDate;
            const startTime = selectedOption.dataset.startTime;
            const endTime = selectedOption.dataset.endTime;
            
            const formattedOriginalDate = new Date(originalDate + 'T' + startTime).toLocaleDateString('en-GB', {
                day: '2-digit', month: '2-digit', year: 'numeric'
            });

            originalDateDisplay.value = `${formattedOriginalDate} ${startTime}-${endTime}`;
        } else {
            originalDateDisplay.value = 'Select a course';
        }
    }

    updateOriginalDateDisplay();
    courseSelect.addEventListener('change', updateOriginalDateDisplay);

    if (consultantSelect) {
        consultantSelect.addEventListener('change', function() {
            const selectedConsultantId = this.value;
            courseSelect.innerHTML = '<option value="">-- Select a Course --</option>';
            originalDateDisplay.value = 'Select a course';

            if (selectedConsultantId) {
                courseSelect.innerHTML = '<option value="">Loading...</option>';
                courseSelect.disabled = true;

                fetch('/course-reschedules/get-courses-by-consultant?consultant_id=' + selectedConsultantId)
                    .then(response => response.json())
                    .then(courses => {
                        courseSelect.innerHTML = '<option value="">-- Select a Course --</option>';
                        courseSelect.disabled = false;

                        if (courses.length === 0) {
                            courseSelect.innerHTML = '<option value="">No courses found</option>';
                            return;
                        }

                        courses.forEach(course => {
                            const option = document.createElement('option');
                            option.value = course.id;
                            option.textContent = course.title + ' (' + new Date(course.course_date).toLocaleDateString() + ')';
                            option.dataset.originalDate = new Date(course.course_date).toISOString().split('T')[0];
                            option.dataset.startTime = course.start_time;
                            option.dataset.endTime = course.end_time;
                            courseSelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.log('Error:', error);
                        courseSelect.innerHTML = '<option value="">Error loading courses</option>';
                        courseSelect.disabled = false;
                    });
            } else {
                courseSelect.disabled = false;
            }
        });
    }

    // Le code li kayvalidi la soumission dyal l'form bach yban l'message
    document.querySelector('form').addEventListener('submit', function(event) {
        const courseSelect = document.getElementById('course_id');
        const selectedOption = courseSelect.options[courseSelect.selectedIndex];
        
        // La date actuelle
        const today = new Date();
        today.setHours(0, 0, 0, 0); // Reset time for comparison

        if (selectedOption && selectedOption.value) {
            const originalDate = new Date(selectedOption.dataset.originalDate);
            originalDate.setHours(0, 0, 0, 0); // Reset time for comparison

            if (originalDate.getTime() === today.getTime()) {
                const errorMessage = document.getElementById('todayCourseError');
                errorMessage.classList.remove('d-none');
                event.preventDefault();
            }
        }
    });

    // Add ripple effect to buttons
    document.querySelectorAll('.btn-modern').forEach(btn => {
        btn.addEventListener('click', function(e) {
            // ... le code dyal ripple ...
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
                z-index: 1;
            `;
            
            this.style.position = 'relative';
            this.style.overflow = 'hidden';
            this.appendChild(ripple);
            
            setTimeout(() => ripple.remove(), 600);
        });
    });
});
</script>
@endpush