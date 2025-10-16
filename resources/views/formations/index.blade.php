@extends('layouts.app')

@section('title', 'Gestion des Formations')

@push('styles')
<style>
    body {
        background-color: #f0f2f5; /* Light gray background */
        font-family: 'Inter', sans-serif; /* Modern font */
    }

    /* === Header Section (Matching Courses Page) === */
    .section-header { /* General header style, consistent with courses */
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%); /* Red gradient */
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 10px 30px rgba(229, 62, 62, 0.3);
        position: relative;
        overflow: hidden;
    }

    .section-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        transform: rotate(45deg);
        pointer-events: none;
    }

    .section-header h1 {
        color: white;
        font-size: 2.5rem;
        font-weight: 800;
        margin-bottom: 0.5rem;
        text-shadow: 2px 2px 4px rgba(0,0,0,0.1);
    }

    .section-header p {
        color: rgba(255,255,255,0.9);
        font-size: 1.1rem;
        margin-bottom: 0;
    }

    .btn-header-action { /* Button style for header, consistent with courses */
        background: linear-gradient(135deg, #fff 0%, #f7fafc 100%);
        color: #e53e3e; /* Red text on light button */
        border: none;
        padding: 0.75rem 2rem;
        border-radius: 50px;
        font-weight: 700;
        text-decoration: none;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        position: relative;
        z-index: 2;
    }

    .btn-header-action:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.2);
        color: #c53030;
    }

    /* === Filter & Search Section (Matching Courses Page) === */
    .filter-section { /* Consistent with courses */
        background: white;
        border-radius: 15px;
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: 0 5px 20px rgba(0,0,0,0.08);
        border-left: 5px solid #e53e3e; /* Red accent */
    }

    .filter-title { /* Consistent with courses */
        color: #2d3748;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 1.5rem;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .filter-title i { /* Consistent with courses */
        color: #e53e3e;
        font-size: 1.2rem;
    }

    .form-control-filter { /* Input/select styles for filter, consistent with courses */
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }

    .form-control-filter:focus {
        border-color: #e53e3e;
        box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
    }

    .btn-filter-submit { /* Submit button for filter, consistent with courses */
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
        box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
    }

    .btn-filter-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(229, 62, 62, 0.4);
    }

    .btn-filter-reset { /* Reset button for filter, consistent with courses */
        background: #6b7280;
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-filter-reset:hover {
        background: #4b5563;
        transform: translateY(-2px);
    }

    /* === Formation Cards (Adapted to Course Card Style) === */
    .formation-card-item { /* Main card container */
        background: white;
        border-radius: 20px;
        padding: 0;
        margin-bottom: 2rem;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
        overflow: hidden;
        position: relative;
        display: flex; /* Flex container for body content */
        flex-direction: column;
        height: 100%; /* Ensure cards are same height */
    }

    .formation-card-item::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 5px;
        background: linear-gradient(90deg, #e53e3e 0%, #c53030 100%); /* Red accent top border */
    }

    .formation-card-item:hover {
        transform: translateY(-8px);
        box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    }

    .card-item-header { /* Header of the card */
        padding: 1.5rem;
        border-bottom: 1px solid #f7fafc;
        display: flex;
        align-items: flex-start; /* Align icon and text to top */
        justify-content: space-between; /* Space between content and badge */
        gap: 1rem;
    }

    .card-item-icon { /* Icon within card header */
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        border-radius: 15px;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-right: 1rem;
        flex-shrink: 0;
    }

    .card-item-icon i {
        color: white;
        font-size: 1.5rem;
    }

    .card-item-title { /* Title within card */
        color: #2d3748;
        font-size: 1.3rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        line-height: 1.2;
    }

    .card-item-category { /* Category within card */
        color: #e53e3e; /* Red for category */
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0;
    }

    .card-item-status-badge { /* Status badge */
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%); /* Default published green */
        color: white;
        padding: 0.4rem 1rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
        flex-shrink: 0; /* Don't let it shrink */
    }
    .card-item-status-draft { background: linear-gradient(135deg, #ecc94b 0%, #d69e2e 100%); color: #333;} /* Yellow */
    .card-item-status-completed { background: linear-gradient(135deg, #a0aec0 0%, #718096 100%); } /* Grey */


    .card-item-body { /* Main body of the card */
        padding: 1.5rem;
        flex-grow: 1; /* Allows content to push actions to bottom */
        display: flex;
        flex-direction: column;
    }

    .card-item-description { /* Description text */
        color: #4a5568;
        font-size: 0.95rem;
        line-height: 1.5;
        margin-bottom: 1.5rem;
    }

    .card-item-stats { /* Stats section */
        display: flex;
        justify-content: space-around;
        padding: 1rem 0;
        border-top: 1px dashed #e2e8f0;
        border-bottom: 1px dashed #e2e8f0;
        margin-bottom: 1.5rem;
        flex-wrap: wrap; /* Allow stats to wrap if too many */
        gap: 0.5rem; /* Small gap for wrapped items */
    }

    .stat-item-detail {
        text-align: center;
        flex: 1;
        min-width: 80px; /* Ensure minimum width for each stat */
    }
    .stat-value {
        font-size: 1.1rem;
        font-weight: 700;
        color: #2d3748;
        margin-bottom: 0.25rem;
    }
    .stat-label {
        font-size: 0.8rem;
        color: #718096;
        white-space: nowrap;
    }
    .stat-icon {
        color: #e53e3e; /* Red for stat icons */
        margin-right: 5px;
    }
    .stat-value.price .stat-icon { color: #38a169; } /* Green for price icon */
    .stat-value.duration .stat-icon { color: #3182ce; } /* Blue for duration icon */
    .stat-value.capacity .stat-icon { color: #f6ad55; } /* Orange for capacity icon */


    .card-item-info { /* Dates and Consultant info */
        margin-bottom: 1.5rem;
    }

    .info-item-line {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 0.8rem;
        color: #4a5568;
        font-size: 0.9rem;
    }

    .info-item-line i {
        color: #e53e3e; /* Red for info icons */
        width: 16px;
        text-align: center;
    }

    .card-item-actions { /* Action buttons container */
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
        margin-top: auto; /* Pushes actions to the bottom */
        padding-top: 1.5rem; /* Space from content above */
        border-top: 1px dashed #e2e8f0;
    }

    .btn-card-action { /* General button style on card */
        flex: 1;
        padding: 0.6rem 1rem;
        border: none;
        border-radius: 10px;
        font-weight: 600;
        font-size: 0.9rem;
        text-decoration: none;
        text-align: center;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        white-space: nowrap;
    }

    .btn-card-view {
        background: linear-gradient(135deg, #3182ce 0%, #2c5282 100%);
        color: white;
    }

    .btn-card-view:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(49, 130, 206, 0.3);
        color: white;
    }

    .btn-card-edit {
        background: linear-gradient(135deg, #38a169 0%, #2f855a 100%);
        color: white;
    }

    .btn-card-edit:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(56, 161, 105, 0.3);
        color: white;
    }

    .btn-card-dropdown-toggle { /* Style for the three-dots dropdown button */
        background: linear-gradient(135deg, #a0aec0 0%, #718096 100%);
        color: white;
        flex: 0 0 auto;
        width: 40px;
        padding: 0.6rem;
    }
    .btn-card-dropdown-toggle:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(160, 174, 192, 0.3);
        color: white;
    }
    .dropdown-menu-card-actions {
        border-radius: 10px;
        box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        border: none;
        overflow: hidden;
    }
    .dropdown-item-card-action {
        padding: 0.75rem 1.25rem;
        font-size: 0.95rem;
        transition: all 0.2s ease;
    }
    .dropdown-item-card-action:hover {
        background-color: #f7fafc;
        color: #2d3748;
    }
    .dropdown-item-card-action.text-danger:hover {
        background-color: #fff5f5;
        color: #e53e3e;
    }

    /* === Empty State (Consistent with Courses Page) === */
    .empty-state-card {
        text-align: center;
        padding: 4rem 2rem;
        background: white;
        border-radius: 20px;
        box-shadow: 0 8px 30px rgba(0,0,0,0.1);
        margin: 2rem auto;
        max-width: 600px;
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .empty-state-icon i {
        color: #a0aec0;
        font-size: 2rem;
    }

    .empty-state-title {
        color: #2d3748;
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .empty-state-description {
        color: #4a5568;
        font-size: 1rem;
        margin-bottom: 2rem;
    }

    /* === Modal Styles (Consistent with Courses Page) === */
    .modal-content {
        border-radius: 20px;
        border: none;
        box-shadow: 0 20px 50px rgba(0,0,0,0.3);
    }

    .modal-header {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%); /* Red gradient */
        color: white;
        border-radius: 20px 20px 0 0;
        padding: 1.5rem;
        border-bottom: none;
    }

    .modal-title {
        font-weight: 700;
        font-size: 1.2rem;
    }

    .modal-body {
        padding: 2rem;
    }

    .modal-footer {
        padding: 1.5rem;
        border-top: none;
    }

    .modal-form-label-new { /* Renamed for clarity in modal forms */
        font-weight: 600;
        color: #2d3748;
        margin-bottom: 0.5rem;
        display: block;
    }
    /* Input/select styles within modals */
    .form-control, .form-select, .form-check-input {
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        padding: 0.75rem 1rem;
        font-size: 0.95rem;
        transition: all 0.3s ease;
    }
    .form-control:focus, .form-select:focus, .form-check-input:focus {
        border-color: #e53e3e;
        box-shadow: 0 0 0 3px rgba(229, 62, 62, 0.1);
    }
    .form-group-custom .form-control { /* For inputs with internal icons */
        padding-left: 2.5rem; /* Adjust for icon padding */
    }
    .form-group-custom .form-control-icon { /* Position for icons inside inputs */
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #a0aec0;
    }
    .dynamic-field-item { /* For prerequisite/document fields */
        background-color: #f7fafc;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        margin-bottom: 10px;
        align-items: center;
        gap: 10px;
        display: flex;
    }
    .dynamic-field-item input.form-control {
        border: none;
        background-color: transparent;
        box-shadow: none;
        padding: 0;
        padding-left: 0.5rem; /* Minimal padding for content */
    }
    .dynamic-field-item input.form-control:focus {
        box-shadow: none;
        border-color: transparent;
    }
    .dynamic-field-item .remove-btn {
        background: none;
        border: none;
        color: #e53e3e;
        font-size: 1.1rem;
        opacity: 0.8;
        transition: opacity 0.2s;
    }
    .dynamic-field-item .remove-btn:hover {
        opacity: 1;
    }

    /* Modal specific buttons */
    .btn-modal-primary {
        background: linear-gradient(135deg, #e53e3e 0%, #c53030 100%);
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-modal-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(229, 62, 62, 0.3);
    }
    .btn-modal-secondary {
        background: #6b7280;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s ease;
    }
    .btn-modal-secondary:hover {
        background: #4b5563;
        transform: translateY(-2px);
    }

    /* General Animations (consistent with courses) */
    .animate-fade-in { animation: fadeInUp 0.6s ease-out; }
    @keyframes fadeInUp { from { opacity: 0; transform: translateY(30px); } to { opacity: 1; transform: translateY(0); } }

    /* Specific animation for form elements, if used */
    .animated-form {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeInSlideUp 0.8s ease-out forwards;
    }
    @keyframes fadeInSlideUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-text-pop { animation: textPop 1s ease-out 0.5s forwards; opacity: 0; transform: scale(0.8); }
    @keyframes textPop { 0% { opacity: 0; transform: scale(0.8); } 50% { opacity: 1; transform: scale(1.05); } 100% { opacity: 1; transform: scale(1); } }
    .animate-icon-fade { animation: iconFade 0.8s ease-out 0.8s forwards; opacity: 0; }
    @keyframes iconFade { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }
    .animate-item-enter { animation: itemEnter 0.5s ease-out forwards; opacity: 0; transform: translateX(-10px); }
    @keyframes itemEnter { from { opacity: 0; transform: translateX(-10px); } to { opacity: 1; transform: translateX(0); } }
    .animate-item-exit { animation: itemExit 0.5s ease-out forwards; opacity: 0; transform: translateX(10px); }
    @keyframes itemExit { from { opacity: 1; transform: translateX(0); } to { opacity: 0; transform: translateX(10px); } }

    /* Responsive adjustments */
    @media (max-width: 768px) {
        .section-header h1 { font-size: 2rem; }
        .btn-header-action { width: 100%; justify-content: center; margin-top: 1rem; } /* Stack buttons */
        .filter-title { font-size: 1.2rem; }
        .btn-filter-submit, .btn-filter-reset { width: 100%; justify-content: center; } /* Stack filter buttons */
        .card-item-actions { flex-direction: column; gap: 10px; }
        .btn-card-action, .btn-card-dropdown-toggle { width: 100%; justify-content: center; }
        .card-item-header { flex-direction: column; align-items: flex-start; }
        .card-item-status-badge { margin-top: 1rem; margin-left: 0; }
    }

  /* ============ NEW MODAL DESIGN STYLES ============ */

:root {
        --primary-color: #C2185B;
        --secondary-color: #D32F2F;
        --accent-color: #D32F2F;
        --success-color: #10b981;
        --warning-color: #f59e0b;
        --danger-color: #ef4444;
        --dark-color: #1f2937;
        --light-color: #f8fafc;
        --border-radius: 12px;
        --box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

.new-modal-design {
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 25px 50px rgba(0, 0, 0, 0.2);
    background-color: var(--gray-50);
}

.new-modal-header {
    /* --- CHANGE MADE HERE --- */
    background: linear-gradient(90deg,rgb(255, 255, 255) 0%,rgb(223, 29, 29) 100%); /* Changed from red to blue gradient */
    color: var(--white);
    padding: 1.8rem 2.5rem;
    border-bottom: none;
    position: relative;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.new-modal-header::before {
    content: '';
    position: absolute;
    top: -30%;
    left: -30%;
    width: 160%;
    height: 160%;
    background: rgba(255, 255, 255, 0.08);
    border-radius: 50%;
    transform: rotate(30deg);
    pointer-events: none;
}

.new-modal-header .modal-title {
    font-weight: 700;
    font-size: 1.6rem;
    display: flex;
    align-items: center;
    text-shadow: 0 2px 5px rgba(0,0,0,0.2);
}

.new-modal-header .modal-title i {
    font-size: 1.8rem;
    margin-right: 15px;
}

.new-btn-close {
    background: none;
    border: none;
    font-size: 1.5rem;
    color: rgba(255, 255, 255, 0.8);
    transition: all 0.3s ease;
    text-shadow: none;
    opacity: 1;
}

.new-btn-close:hover {
    color: var(--white);
    transform: rotate(90deg);
    opacity: 1;
}

.new-modal-body {
    padding: 3rem 2.5rem;
    background-color: white;
}

.modal-main-title {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--gray-900);
    margin-bottom: 0.75rem;
    letter-spacing: -0.05em;
}

.modal-subtitle {
    font-size: 1.1rem;
    color: var(--gray-600);
    max-width: 600px;
    margin: 0 auto;
    line-height: 1.6;
}

.new-form-grid {
    margin-top: 3rem;
}

.new-section-card {
    background: var(--white);
    border-radius: 12px;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
    padding: 2.5rem;
    border: 1px solid var(--gray-200);
    transition: all 0.3s ease;
}

.new-section-card:hover {
    box-shadow: 0 12px 35px rgba(0, 0, 0, 0.12);
    transform: translateY(-3px);
}

.new-section-title {
    font-weight: 700;
    font-size: 1.25rem;
    color: var(--gray-800);
    padding-bottom: 1rem;
    margin-bottom: 1.5rem;
    border-bottom: 2px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 10px;
}

.new-section-title.primary-border-bottom { border-bottom-color: var(--primary-color); }
.new-section-title.success-border-bottom { border-bottom-color: var(--success-color); }
.new-section-title.danger-border-bottom { border-bottom-color: var(--danger-color); }
.new-section-title.info-border-bottom { border-bottom-color: var(--info-color); }
.new-section-title.secondary-border-bottom { border-bottom-color: var(--secondary-color); }

.section-icon {
    font-size: 1.4rem;
    transition: transform 0.3s ease;
}
.new-section-card:hover .section-icon {
    transform: scale(1.1);
}

.section-icon.primary-icon { color: var(--primary-color); }
.section-icon.success-icon { color: var(--success-color); }
.section-icon.danger-icon { color: var(--danger-color); }
.section-icon.info-icon { color: var(--info-color); }
.section-icon.secondary-icon { color: var(--secondary-color); }


.new-form-label {
    font-weight: 600;
    color: var(--gray-700);
    margin-bottom: 0.6rem;
    display: block;
    font-size: 0.95rem;
}

.input-group-with-icon {
    position: relative;
    display: flex;
    align-items: center;
}

.input-group-with-icon .input-icon {
    position: absolute;
    left: 1rem;
    color: var(--gray-400);
    font-size: 1rem;
    z-index: 2;
    transition: color 0.3s ease;
}

.new-form-control {
    border-radius: 8px;
    border: 1px solid var(--gray-300);
    padding: 0.85rem 1rem 0.85rem 3rem; /* Adjust padding for icon */
    font-size: 0.95rem;
    background-color: var(--white);
    color: var(--gray-800);
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.03);
}

.new-form-control:focus {
    border-color: var(--accent-blue);
    box-shadow: 0 0 0 3px rgba(49, 130, 206, 0.15);
    outline: none;
    background-color: var(--white);
}

.new-form-control:focus + .input-icon {
    color: var(--accent-blue);
}

.new-form-control::placeholder {
    color: var(--gray-400);
    opacity: 0.9;
}

textarea.new-form-control {
    padding-left: 1.25rem; /* Textarea doesn't typically have an icon inside, so adjust for normal padding */
}


.new-form-check-inline {
    margin-right: 1.5rem; /* Space between checkboxes */
}

.new-form-check-input {
    width: 1.25rem;
    height: 1.25rem;
    margin-top: 0.25rem;
    border: 2px solid var(--gray-400);
    border-radius: 6px; /* Slightly rounded squares */
    transition: all 0.2s ease;
    cursor: pointer;
}

.new-form-check-input:checked {
    background-color: var(--accent-blue);
    border-color: var(--accent-blue);
    transform: scale(1.05);
}

.new-form-check-label {
    font-weight: 500;
    color: var(--gray-700);
    cursor: pointer;
    font-size: 0.9rem;
    user-select: none; /* Prevent text selection on label click */
}


/* Dynamic Fields (Prerequisites, Documents) */
.new-dynamic-field {
    background-color: var(--gray-100);
    border: 1px solid var(--gray-200);
    border-radius: 8px;
    padding: 0.75rem 1rem;
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
    gap: 10px;
    transition: all 0.3s ease;
}

.new-dynamic-field:hover {
    background-color: var(--gray-200);
    border-color: var(--gray-300);
}

.dynamic-field-icon {
    color: var(--gray-500);
    font-size: 1rem;
    flex-shrink: 0;
}

.new-dynamic-field .new-form-control {
    border: none;
    background-color: transparent;
    box-shadow: none;
    padding: 0.25rem 0.5rem;
    flex-grow: 1;
    font-size: 0.9rem;
}

.new-dynamic-field .new-form-control:focus {
    border-color: transparent;
    box-shadow: none;
}

.remove-dynamic-btn {
    background: none;
    border: none;
    color: var(--danger-color);
    font-size: 1rem;
    opacity: 0.7;
    transition: all 0.2s ease;
    padding: 0.3rem;
    border-radius: 5px;
}

.remove-dynamic-btn:hover {
    opacity: 1;
    background-color: var(--primary-light);
    transform: scale(1.1);
}

/* New Outline Buttons for adding dynamic fields */
.new-btn-outline-secondary {
    background: var(--white);
    border: 1px solid var(--secondary-color);
    color: var(--secondary-color);
    padding: 0.6rem 1.25rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.new-btn-outline-secondary:hover {
    background: var(--secondary-color);
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

.new-btn-outline-primary {
    background: red;
    border: 1px solid var(--primary-color); /* This is still your original red */
    color: var(--primary-color); /* This is still your original red */
    padding: 0.6rem 1.25rem;
    border-radius: 8px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.new-btn-outline-primary:hover {
    background: blue; /* This is still your original red */
    color: var(--white);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}


/* Modal Action Buttons (Footer-like) */
.new-btn-cancel {
    background: gray;
    color: var(--gray-800);
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.new-btn-cancel:hover {
    background: red;
    color: var(--gray-900);
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(0,0,0,0.15);
}

.new-btn-submit {
    background: linear-gradient(135deg, red 0%, #2c5282 100%);
    color: var(--white);
    border: none;
    padding: 0.8rem 2rem;
    border-radius: 10px;
    font-weight: 600;
    transition: all 0.3s ease;
    box-shadow: 0 4px 15px rgba(49, 130, 206, 0.3);
}

.new-btn-submit:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(49, 130, 206, 0.4);
}

.new-btn-submit .spinner-border {
    color: rgba(255, 255, 255, 0.8);
}


/* Animations */
@keyframes animatePop {
    0% { opacity: 0; transform: scale(0.8); }
    70% { opacity: 1; transform: scale(1.05); }
    100% { opacity: 1; transform: scale(1); }
}
.animate-pop { animation: animatePop 0.6s ease-out forwards; }

@keyframes animateTextFocus {
    0% { opacity: 0; filter: blur(4px); }
    100% { opacity: 1; filter: blur(0); }
}
.animate-text-focus { animation: animateTextFocus 0.8s ease-out 0.2s forwards; opacity: 0; }

@keyframes animateSlideDown {
    0% { opacity: 0; transform: translateY(-30px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-slide-down { animation: animateSlideDown 0.7s ease-out 0.1s forwards; opacity: 0; }

@keyframes animateFadeInUp {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}
.animate-fade-in-up { animation: animateFadeInUp 0.7s ease-out forwards; opacity: 0; }
.animate-fade-in-up.delay-1 { animation-delay: 0.3s; }
.animate-fade-in-up.delay-2 { animation-delay: 0.4s; }
.animate-fade-in-up.delay-3 { animation-delay: 0.5s; }
.animate-fade-in-up.delay-4 { animation-delay: 0.6s; }
.animate-fade-in-up.delay-5 { animation-delay: 0.7s; }
.animate-fade-in-up.delay-6 { animation-delay: 0.8s; }


/* Responsive Adjustments for New Modal */
@media (max-width: 991px) { /* For tablets and smaller desktops */
    .modal-dialog.modal-xl {
        max-width: 95%; /* Make it wider on smaller screens to use available space */
    }
    .new-modal-body {
        padding: 2rem 1.5rem;
    }
    .new-section-card {
        padding: 2rem;
    }
    .new-form-grid .col-lg-6 {
        margin-bottom: 1.5rem; /* Add spacing when columns stack */
    }
    .new-form-grid .col-lg-6:last-of-type {
        margin-bottom: 0;
    }
    .modal-main-title {
        font-size: 1.8rem;
    }
    .modal-subtitle {
        font-size: 1rem;
    }
}

@media (max-width: 767px) { /* For mobile phones */
    .new-modal-header {
        padding: 1.2rem 1.5rem;
    }
    .new-modal-header .modal-title {
        font-size: 1.3rem;
    }
    .new-modal-body {
        padding: 1.5rem 1rem;
    }
    .new-section-card {
        padding: 1.5rem;
    }
    .new-section-title {
        font-size: 1.1rem;
        margin-bottom: 1rem;
        padding-bottom: 0.75rem;
    }
    .new-form-label {
        font-size: 0.85rem;
        margin-bottom: 0.4rem;
    }
    .new-form-control {
        padding: 0.6rem 0.75rem 0.6rem 2.5rem;
        font-size: 0.85rem;
    }
    .input-group-with-icon .input-icon {
        left: 0.75rem;
        font-size: 0.9rem;
    }
    .new-btn-cancel, .new-btn-submit {
        padding: 0.6rem 1.2rem;
        font-size: 0.9rem;
        width: 100%; /* Make buttons full width */
        margin-top: 1rem !important; /* Force margin when stacked */
    }
    .new-dynamic-field {
        flex-direction: column; /* Stack dynamic field elements */
        align-items: flex-start;
        padding: 0.6rem 0.8rem;
    }
    .new-dynamic-field .new-form-control {
        width: 100%;
        padding-left: 0.25rem;
    }
    .dynamic-field-icon {
        margin-bottom: 0.5rem;
    }
    .remove-dynamic-btn {
        position: absolute;
        right: 0.5rem;
        top: 0.5rem;
    }
    .new-form-check-inline {
        margin-right: 0.75rem;
    }
    .new-form-check-label {
        font-size: 0.8rem;
    }
}button.btn.new-btn-cancel {
    color: black !important;
}

button.btn-card-dropdown-toggle.dropdown-toggle {
    border-radius: 8px;
}
.btn-card-view {
    background: linear-gradient(135deg, #3d93e4 0%, #4b9bff 100%);
    color: white;
}
/* Add to your existing <style> block in the Blade file or your main CSS */

.input-group-modern {
    display: flex; /* Ensures flexible layout within the group */
    border: 1px solid rgba(211, 47, 47, 0.2); /* Unified border for the entire group */
    border-radius: 12px; /* Matches your modern-input/select styling */
    overflow: hidden; /* Important to hide internal borders and ensure rounded corners */
    transition: all 0.3s ease; /* Smooth transition for focus */
}

.input-group-modern:focus-within { /* Style for when any element inside the group is focused */
    border-color: #D32F2F; /* Focus color for the whole group */
    box-shadow: 0 0 0 0.2rem rgba(211, 47, 47, 0.25);
}

.modern-addon {
    background-color: #f8f9fa; /* Light background for the icon area */
    border: none; /* Remove default border */
    padding: 0.5rem 0.75rem; /* Match input padding */
    display: flex;
    align-items: center;
    color: #6c757d; /* Icon color */
    border-right: 1px solid rgba(211, 47, 47, 0.1); /* Separator line */
}

.modern-input-no-border {
    border: none !important; /* Remove all borders */
    box-shadow: none !important; /* Remove focus shadow */
    padding-left: 0.75rem; /* Ensure consistent padding if border was removed */
    flex-grow: 1; /* Allow it to take available space */
}

.modern-select-no-border {
    border: none !important; /* Remove all borders */
    box-shadow: none !important; /* Remove focus shadow */
    background-color: transparent; /* Make background transparent if needed */
    padding-right: 0.75rem; /* Ensure consistent padding */
    width: auto; /* Allow select to shrink to content */
    min-width: 80px; /* Minimum width for the select */
    appearance: none; /* Remove default select arrow if desired */
    /* Add custom arrow styling if appearance: none is used, e.g., with background-image */
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M2 5l6 6 6-6'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    background-size: 16px 12px;
}
.modern-select-no-border:focus {
    outline: 0; /* Remove focus outline */
}

/* Ensure the form-control and form-select overrides apply */
.input-group > .form-control:not(:first-child):not(.is-invalid):not(.is-valid),
.input-group > .form-select:not(:last-child):not(.is-invalid):not(.is-valid) {
    border-top-left-radius: 0;
    border-bottom-left-radius: 0;
    border-top-right-radius: 0;
    border-bottom-right-radius: 0;
}

/* Adjustments for potential Bootstrap 5 specifics if it adds borders around select in input groups */
.input-group .form-select {
    border-left: 1px solid rgba(211, 47, 47, 0.1) !important; /* Separator for select */
    border-radius: 0 12px 12px 0 !important; /* Apply right border radius */
}

.input-group .form-control:not(.modern-input-no-border) {
    border: 1px solid rgba(211, 47, 47, 0.2); /* Reapply default borders for other controls */
}

/* Adjust for responsiveness if needed */
@media (max-width: 768px) {
    .input-group-modern {
        flex-wrap: wrap; /* Allow wrapping on small screens if necessary */
    }
    .modern-input-no-border, .modern-select-no-border {
        flex-basis: 100%; /* Take full width on wrap */
    }
}


/* Example of how your custom CSS might be adjusted */

.form-control-filter {
    /* Ensure it behaves like a standard form control within the grid */
    display: block; /* This is typically correct for form inputs */
    width: 100%;    /* This is also typically correct */
    padding: .375rem .75rem;
    font-size: 1rem;
    line-height: 1.5;
    color: #495057;
    background-color: #fff;
    background-clip: padding-box;
    border: 1px solid #ced4da;
    border-radius: .25rem;
    transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
    -webkit-appearance: none; /* For consistent styling across browsers */
    -moz-appearance: none;
    appearance: none;
    /* Ensure box model is inclusive of padding/border */
    box-sizing: border-box; /* VERY IMPORTANT for width calculations */
}

/* Ensure buttons within the d-flex container behave correctly */
.btn-filter-submit,
.btn-filter-reset {
    /* If you added specific widths that conflict, remove them or adjust */
    /* For flex-grow-1, height consistency is key */
    height: calc(1.5em + .75rem + 2px); /* Match height of form-control-filter if needed */
    display: flex; /* Make them flex containers for internal alignment */
    align-items: center; /* Vertically center icon and text */
    justify-content: center; /* Horizontally center icon and text */
    text-align: center; /* Fallback for older browsers */
    white-space: nowrap; /* Prevent text wrapping inside buttons */
    /* Add existing styles like background, color, border, padding, border-radius */
    padding: .375rem .75rem; /* Ensure consistent padding */
    border-radius: .25rem;
    cursor: pointer;
    font-size: 1rem;
}

/* Ensure the parent .d-flex for the buttons works as expected */
.col-md-2.d-flex {
    /* No specific changes usually needed here, just ensure no conflicting display properties */
}
</style>
@endpush

@section('content')
<div class="animate-fade-in">
    <div class="section-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1><i class="fas fa-graduation-cap me-3"></i>Gestion des Formations</h1>
                <p>Gérez vos formations professionnelles efficacement</p>
            </div>
            <div class="col-md-4 text-end d-flex flex-column flex-md-row justify-content-md-end align-items-md-center gap-2">
                {{-- Only show "Nouvelle Formation" button if user can create formations --}}
              
                    <button type="button" class="btn-header-action" data-bs-toggle="modal" data-bs-target="#createFormationModal">
                        <i class="fas fa-plus me-2"></i>Nouvelle Formation
                    </button>
                 <a href="{{ route('formations.corbeille') }}" class="btn btn-danger">
    <i class="fa fa-trash"></i> Corbeille
</a>
             
                    <a href="{{ route('formations.export-csv') }}"
       class="btn-header-action" style="background: white; color: #38a169; border: 1px solid #38a169;">
        <i class="fas fa-download me-2"></i>Exporter
    </a>
                    
               
            </div>
        </div>
    </div>

   <div class="filter-section">
    <div class="filter-title">
        <i class="fas fa-filter"></i>
        Filtres de recherche
    </div>

    <form method="GET" action="{{ route('formations.index') }}">
        {{-- Use row and g-3 for gap between columns --}}
        <div class="row g-3 align-items-end"> {{-- Added align-items-end for vertical alignment of inputs and buttons --}}
            <div class="col-md-4 col-sm-12"> {{-- Added col-sm-12 to make it full width on small screens --}}
                <label for="search" class="form-label fw-semibold">Rechercher</label>
                <div class="position-relative">
                    <input type="text" class="form-control-filter ps-4" id="search" name="search"
                           value="{{ request('search') }}" placeholder="Titre ou description...">
                    <i class="fas fa-search position-absolute" style="left: 12px; top: 50%; transform: translateY(-50%); color: #a0aec0;"></i>
                </div>
            </div>

            <div class="col-md-3 col-sm-12"> {{-- Added col-sm-12 --}}
                <label for="category_id" class="form-label fw-semibold">Catégorie</label>
                <select class="form-control-filter" id="category_id" name="category_id">
                    <option value="">Toutes les catégories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}"
                                {{ request('category_id') == $category->id ? 'selected' : '' }}>
                            {{ $category->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-md-3 col-sm-12"> {{-- Added col-sm-12 --}}
                <label for="status" class="form-label fw-semibold">Statut</label>
                <select class="form-control-filter" id="status" name="status">
                    <option value="">Tous les statuts</option>
                    <option value="draft" {{ request('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                    <option value="published" {{ request('status') == 'published' ? 'selected' : '' }}>Publié</option>
                    <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                </select>
            </div>

            <div class="col-md-2 col-sm-12 d-flex align-items-end"> {{-- Added col-sm-12 --}}
                <button type="submit" class="btn-filter-submit flex-grow-1 me-2">
                    <i class="fas fa-search me-2"></i>l
                </button>
                <a href="{{ route('formations.index') }}" class="btn-filter-reset flex-grow-1">
                    <i class="fas fa-undo me-2"></i>
                </a>
            </div>
        </div>
    </form>
</div>

    {{-- Info bar and view toggle --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <span class="text-muted fw-semibold">
            <i class="fas fa-info-circle me-1"></i>
            {{ $formations->total() }} formation(s) trouvée(s)
            @if(request()->hasAny(['search', 'category_id', 'status']))
                <span class="badge bg-info ms-2">Filtré</span>
            @endif
        </span>

        <div class="btn-group" role="group">
    <input type="radio" class="btn-check" name="view" id="grid-view" autocomplete="off">
    <label class="btn btn-outline-secondary view-toggle-btn" for="grid-view" title="Affichage Grille">
        <i class="fas fa-th-large"></i> </label>

    <input type="radio" class="btn-check" name="view" id="list-view" autocomplete="off">
    <label class="btn btn-outline-secondary view-toggle-btn" for="list-view" title="Affichage Liste">
        <i class="fas fa-list"></i> </label>
</div>
    </div>


    <div class="row" id="formations-grid">
        @forelse($formations as $formation)
          <div class="col-lg-6 col-md-6 col-12 mb-4 formation-card-col">
    <div class="formation-card-item h-100">
                    <div class="card-item-header">
                        <div class="d-flex align-items-center">
                            <div class="card-item-icon">
                                <i class="fas fa-book"></i> {{-- Consistent icon with courses --}}
                            </div>
                            <div>
                                <h5 class="card-item-title text-truncate" title="{{ $formation->title }}">{{ Str::limit($formation->title, 25) }}</h5>
                                @if($formation->category)
                                    <p class="card-item-category">{{ $formation->category->name }}</p>
                                @endif
                            </div>
                        </div>
                        <span class="card-item-status-badge
                                @if($formation->status === 'published') bg-success
                                @elseif($formation->status === 'draft') card-item-status-draft
                                @else card-item-status-completed @endif">
                            {{ ucfirst($formation->status) }}
                        </span>
                    </div>

                    <div class="card-item-body">
                        <p class="card-item-description">{{ Str::limit(strip_tags($formation->description), 100) }}</p>

                        <div class="card-item-stats">
                            <div class="stat-item-detail">
                                <div class="stat-value price"><i class="fas fa-money-bill-wave stat-icon"></i>{{ $formation->price }}</div>
                                <div class="stat-label">Prix (DH)</div>
                            </div>
                            <div class="stat-item-detail">
                                <div class="stat-value duration"><i class="fas fa-clock stat-icon"></i>{{ $formation->duration_hours }} {{ $formation->duration_unit}}</div>
                                <div class="stat-label">Durée</div>
                            </div>
                            <div class="stat-item-detail">
                                <div class="stat-value capacity"><i class="fas fa-users stat-icon"></i>
                                    {{ $formation->capacity - $formation->inscriptions->whereIn('status', ['active', 'pending'])->count() }} / {{ $formation->capacity }}
                                </div>
                                <div class="stat-label">Places</div>
                            </div>
                        </div>
                        
                        <div class="card-item-info">
                            <div class="info-item-line">
                                <i class="fas fa-calendar-alt"></i>
                                <span>Début: <strong>{{ \Carbon\Carbon::parse($formation->start_date)->format('d/m/Y') }}</strong></span>
                            </div>
                            <div class="info-item-line">
                                <i class="fas fa-calendar-check"></i>
                                <span>Fin: <strong>{{ \Carbon\Carbon::parse($formation->end_date)->format('d/m/Y') }}</strong></span>
                            </div>
                            @if($formation->consultant)
                                <div class="info-item-line">
                                    <i class="fas fa-user-tie"></i>
                                    <span>Consultant: <strong>{{ $formation->consultant->name }}</strong></span>
                                </div>
                            @endif
                        </div>
                        
                        <div class="card-item-actions">
                            {{-- View Details button --}}
                           @can('formation-view')
                                <a href="{{ route('formations.show', $formation) }}" class="btn-card-action btn-card-view">
                                    <i class="fas fa-eye"></i> 
                                </a>
                                @endcan
                                @can('formation-edit')
                           
                                <div class="btn-group flex-grow-1"> {{-- Changed from flex-grow-1 to flex-grow-0 for dropdown --}}
                                    
                                        <button type="button" class="btn-card-action btn-card-edit"
                                                data-bs-toggle="modal" data-bs-target="#editFormationModal"
                                                data-formation-id="{{ $formation->id }}">
                                            <i class="fas fa-edit"></i> 
                                        </button>
                                  <a href="{{ route('formations.evaluations', $formation->id) }}" 
   class="btn btn-sm btn-warning" 
   title="Voir les évaluations">
    <i class="fas fa-star"></i> Évaluations
</a>
                                    
                                    <button type="button"
        class="btn-card-dropdown-toggle dropdown-toggle" data-bs-toggle="dropdown"
        aria-expanded="false">
    <i class="fas fa-ellipsis-h"></i> <span class="visually-hidden">Plus d'options</span> </button>
                                    <ul class="dropdown-menu dropdown-menu-card-actions">
                                        
                                            <li>
                                                <form method="POST" action="{{ route('formations.duplicate', $formation) }}" class="d-inline">
                                                    @csrf
                                                    <button type="submit" class="dropdown-item-card-action">
                                                        <i class="fas fa-copy me-2"></i> Dupliquer
                                                    </button>
                                                </form>
                                            </li>
                                        
                                            <li>
                                                <form method="POST" action="{{ route('formations.toggleStatus', $formation) }}" class="d-inline">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="dropdown-item-card-action">
                                                        <i class="fas fa-toggle-on me-2"></i>
                                                        {{ $formation->status === 'published' ? 'Dépublier' : 'Publier' }}
                                                    </button>
                                                </form>
                                            </li>
                                        
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <form method="POST" action="{{ route('formations.destroy', $formation) }}"
                                                      class="d-inline delete-form">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="dropdown-item-card-action text-danger">
                                                        <i class="fas fa-trash me-2"></i> Supprimer
                                                    </button>
                                                </form>
                                            </li>
                                        
                                    </ul>
                                </div>
                                @endcan
                           
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="empty-state-card">
                    <div class="empty-state-icon">
                        <i class="fas fa-book-open"></i> {{-- Consistent icon with courses empty state --}}
                    </div>
                    <h4 class="empty-state-title">Aucune formation trouvée</h4>
                   
                 
                </div>
            </div>
        @endforelse
    </div>

    @if($formations->hasPages())
    <div class="d-flex justify-content-center mt-5">
        <div class="bg-white rounded-3 shadow-sm p-3">
            {{ $formations->appends(request()->query())->links() }}
        </div>
    </div>
    @endif
</div>

{{-- Create Formation Modal --}}
{{-- Create Formation Modal --}}
<div class="modal fade" id="createFormationModal" tabindex="-1" aria-labelledby="createFormationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content new-modal-design">
            <div class="modal-header new-modal-header">
                <h5 class="modal-title" id="createFormationModalLabel">
                    <i class="fas fa-plus-circle me-3 animate-pop"></i>
                    <span class="animate-text-focus">Ajouter une Nouvelle Formation</span>
                </h5>
                <button type="button" class="btn-close new-btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body new-modal-body">
                <div class="container-fluid">
                    <div class="text-center mb-5 animate-slide-down">
                        <h3 class="modal-main-title">Créez votre prochaine formation facilement</h3>
                        <p class="modal-subtitle">Remplissez les champs ci-dessous pour publier un nouveau cours passionnant.</p>
                    </div>

                    {{-- IMPORTANT: Add enctype="multipart/form-data" for file uploads --}}
                    <form action="{{ route('formations.store') }}" method="POST" class="row g-4 new-form-grid" id="formation-create-form" enctype="multipart/form-data">
                        @csrf

                        <div class="col-lg-6 col-md-12 animate-fade-in-up delay-1">
                            <div class="section-card new-section-card h-100">
                                <h4 class="new-section-title primary-border-bottom d-flex align-items-center mb-4">
                                    <i class="fas fa-info-circle me-2 section-icon primary-icon"></i> Informations générales
                                </h4>
                                <div class="mb-3">
                                    <label for="create_title" class="new-form-label">
                                        <i class="fas fa-book-open me-2"></i> Titre de la formation <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group-with-icon">
                                        <input type="text" name="title" id="create_title" class="form-control new-form-control" value="{{ old('title') }}" placeholder="Exemple : Développement Web avec Laravel" required>
                                    </div>
                                    @error('title')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label for="create_category_id" class="new-form-label">
                                        <i class="fas fa-tags me-2"></i> Catégorie <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group-with-icon">
                                        <select id="create_category_id" name="category_id" class="form-control new-form-control" required>
                                            <option value="">Sélectionnez une catégorie</option>
                                            @foreach($categories as $category)
                                                <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>{{ $category->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('category_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-3">
                                    <label for="create_consultant_id" class="new-form-label">
                                        <i class="fas fa-user-tie me-2"></i> Consultant <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group-with-icon">
                                        <select id="create_consultant_id" name="consultant_id" class="form-control new-form-control" required>
                                            <option value="">Sélectionnez un consultant</option>
                                            @foreach($consultants as $consultant)
                                                <option value="{{ $consultant->id }}" {{ old('consultant_id') == $consultant->id ? 'selected' : '' }}>{{ $consultant->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @error('consultant_id')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                                <div class="mb-0">
                                    <label for="create_status" class="new-form-label">
                                        <i class="fas fa-clipboard-check me-2"></i> Statut <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group-with-icon">
                                        <select id="create_status" name="status" class="form-control new-form-control" required>
                                            <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Brouillon</option>
                                            <option value="published" {{ old('status') == 'published' ? 'selected' : '' }}>Publié</option>
                                            <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Terminé</option>
                                        </select>
                                    </div>
                                    @error('status')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12 animate-fade-in-up delay-2">
                            <div class="section-card new-section-card h-100">
                                <h4 class="new-section-title success-border-bottom d-flex align-items-center mb-4">
                                    <i class="fas fa-calendar-alt me-2 section-icon success-icon"></i> Détails et calendrier
                                </h4>
                                <div class="row g-3">
                                    <div class="col-md-6 mb-3">
                                        <label for="create_price" class="new-form-label">
                                            <i class="fas fa-money-bill-wave me-2"></i> Prix (MAD) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group-with-icon">
                                            <input type="number" name="price" id="create_price" step="0.01" min="0" class="form-control new-form-control" value="{{ old('price') }}" placeholder="0.00" required>
                                        </div>
                                        @error('price')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="create_capacity" class="new-form-label">
                                            <i class="fas fa-users me-2"></i> Capacité (nombre de places) <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group-with-icon">
                                            <input type="number" name="capacity" id="create_capacity" min="1" class="form-control new-form-control" value="{{ old('capacity') }}" required>
                                        </div>
                                        @error('capacity')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="create_duration_hours" class="new-form-label">
                                            <i class="fas fa-clock me-2"></i> Durée <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group input-group-modern">
                                            <span class="input-group-text modern-addon">
                                                <i class="fas fa-calendar-alt"></i>
                                            </span>
                                            <input type="number" name="duration_hours" id="create_duration_hours" min="1" 
                                                 class="form-control new-form-control modern-input-no-border"
                                                 value="{{ old('duration_hours') }}" required>
                                            
                                            <select class="form-select modern-select-no-border" id="duration_unit" name="duration_unit" required>
                                                @foreach($durationUnits as $unit)
                                                    <option value="{{ $unit }}" {{ old('duration_unit') == $unit ? 'selected' : '' }}>
                                                        {{ ucfirst($unit) }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        @error('duration_hours')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                        @error('duration_unit')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="create_start_date" class="new-form-label">
                                            <i class="fas fa-calendar-alt me-2"></i> Date de début <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group-with-icon">
                                            <input type="date" name="start_date" id="create_start_date" class="form-control new-form-control" value="{{ old('start_date') }}" required>
                                        </div>
                                        @error('start_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="create_end_date" class="new-form-label">
                                            <i class="fas fa-calendar-check me-2"></i> Date de fin <span class="text-danger">*</span>
                                        </label>
                                        <div class="input-group-with-icon">
                                            <input type="date" name="end_date" id="create_end_date" class="form-control new-form-control" value="{{ old('end_date') }}" required>
                                        </div>
                                        @error('end_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                    </div>
                                    <div class="col-12 mb-0">
                                        <label class="new-form-label">
                                            <i class="fas fa-wallet me-2"></i> Options de paiement disponibles <span class="text-danger">*</span>
                                        </label>
                                        <div class="d-flex flex-wrap gap-3 mt-2">
                                            @php
                                                $paymentOptions = [1 => 'Paiement intégral (une seule tranche)', 2 => '2 tranches', 3 => '3 tranches', 4 => '4 tranches', 6 => '6 tranches', 10 => '10 tranches', 12 => '12 tranches'];
                                                $selectedOptions = old('available_payment_options', [1]);
                                            @endphp
                                            @foreach($paymentOptions as $value => $label)
                                                <div class="form-check new-form-check-inline">
                                                    <input type="checkbox" name="available_payment_options[]" id="create_payment_option_{{ $value }}" value="{{ $value }}"
                                                        class="form-check-input new-form-check-input" @checked(in_array($value, $selectedOptions))
                                                    >
                                                    <label class="form-check-label new-form-check-label" for="create_payment_option_{{ $value }}">
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
                                    <i class="fas fa-file-lines me-2 section-icon info-icon"></i> Description de la formation <span class="text-danger">*</span>
                                </h4>
                                <div class="mb-0">
                                    <label for="create_description" class="new-form-label d-none">Description</label>
                                    <textarea id="create_description" name="description" rows="6" class="form-control new-form-control" placeholder="Décrivez le contenu détaillé de la formation, les objectifs d'apprentissage et le public cible..." required>{{ old('description') }}</textarea>
                                </div>
                                @error('description')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12 animate-fade-in-up delay-4">
                            <div class="section-card new-section-card h-100">
                                <h4 class="new-section-title danger-border-bottom d-flex align-items-center mb-4">
                                    <i class="fas fa-graduation-cap me-2 section-icon danger-icon"></i> Prérequis (facultatif)
                                </h4>
                                <div id="create-prerequisites-container" class="space-y-3">
                                    @if(old('prerequisites'))
                                        @foreach(old('prerequisites') as $prerequisite)
                                            <div class="dynamic-field-item new-dynamic-field animate-item-enter">
                                                <i class="fas fa-check-circle dynamic-field-icon"></i>
                                                <input type="text" name="prerequisites[]" class="form-control new-form-control" value="{{ $prerequisite }}" placeholder="Exemple : Avoir des connaissances en HTML">
                                                <button type="button" class="remove-dynamic-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        @endforeach
                                    @else
                                        <div class="dynamic-field-item new-dynamic-field animate-item-enter">
                                            <i class="fas fa-check-circle dynamic-field-icon"></i>
                                            <input type="text" name="prerequisites[]" class="form-control new-form-control" placeholder="Exemple : Avoir des connaissances en HTML">
                                            <button type="button" class="remove-dynamic-btn">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                                <button type="button" id="create-add-prerequisite-btn" class="btn new-btn-outline-secondary mt-3 d-flex align-items-center gap-2">
                                    <i class="fas fa-plus-circle"></i> Ajouter un prérequis
                                </button>
                                @error('prerequisites')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-lg-6 col-md-12 animate-fade-in-up delay-5">
                            <div class="section-card new-section-card h-100">
                                <h4 class="new-section-title secondary-border-bottom d-flex align-items-center mb-4">
                                    <i class="fas fa-folder-open me-2 section-icon secondary-icon"></i> Documents requis (facultatif)
                                </h4>
                                <div id="create-documents-container" class="space-y-3">
                                    {{-- For create form, we only provide file inputs. old() values for files cannot be pre-filled. --}}
                                    {{-- If validation fails, Laravel will re-render the form and show error messages. --}}
                                    @if(old('documents_files')) {{-- If there were old files due to validation error, add empty inputs --}}
                                        @foreach(old('documents_files') as $key => $file)
                                            <div class="dynamic-field-item new-dynamic-field animate-item-enter">
                                                <i class="fas fa-file-upload dynamic-field-icon"></i>
                                                <input type="file" name="documents_files[]" class="form-control new-form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                                <button type="button" class="remove-dynamic-btn">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                                @error('documents_files.' . $key)<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                                            </div>
                                        @endforeach
                                    @endif
                                    {{-- Always provide at least one empty file input for new uploads --}}
                                    <div class="dynamic-field-item new-dynamic-field animate-item-enter">
                                        <i class="fas fa-file-upload dynamic-field-icon"></i>
                                        <input type="file" name="documents_files[]" class="form-control new-form-control" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png">
                                        <button type="button" class="remove-dynamic-btn">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                                <button type="button" id="create-add-document-btn" class="btn new-btn-outline-primary mt-3 d-flex align-items-center gap-2">
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
                            <button type="submit" id="create-submit-btn" class="btn new-btn-submit d-flex align-items-center">
                                <i class="fas fa-save me-2"></i> Enregistrer la formation
                                <span class="spinner-border spinner-border-sm ms-2 hidden" role="status" aria-hidden="true"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
{{-- Edit Formation Modal --}}
<div class="modal fade" id="editFormationModal" tabindex="-1" aria-labelledby="editFormationModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editFormationModalLabel">
                    <i class="fas fa-edit me-2"></i>Modifier la Formation
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="editFormationModalBody">
                <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete confirmation (already existing)
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Êtes-vous sûr ?',
                text: 'Vous ne pourrez pas annuler cette action !',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Oui, supprimer !',
                cancelButtonText: 'Annuler'
            }).then((result) => {
                if (result.isConfirmed) {
                    this.submit();
                }
            });
        });
    });

    // View toggle (list/grid)
    const gridView = document.getElementById('grid-view');
    const listView = document.getElementById('list-view');
    const formationsGrid = document.getElementById('formations-grid');
    const formationCardCols = document.querySelectorAll('.formation-card-col'); // Select the column wrapper

    // Function to apply view mode dynamically
    const applyViewMode = (mode) => {
        if (mode === 'list') {
            formationsGrid.classList.remove('row');
            formationCardCols.forEach(col => {
                col.classList.remove('col-xl-3', 'col-lg-4', 'col-md-6', 'mb-4');
                col.classList.add('col-12', 'mb-3');
                const card = col.querySelector('.formation-card-item');
                if (card) {
                    card.style.flexDirection = 'row';
                    card.style.alignItems = 'center'; /* Center content vertically in list mode */
                    card.style.padding = '1.5rem'; /* Adjust padding for list mode */

                    // Hide elements not suitable for list view or adjust their display
                    const headerContent = card.querySelector('.card-item-header > div');
                    if(headerContent) headerContent.style.flexGrow = '1'; /* Allow title/category to grow */

                    const statusBadge = card.querySelector('.card-item-status-badge');
                    if(statusBadge) statusBadge.style.marginRight = '1.5rem'; /* Add space */

                    const cardItemIcon = card.querySelector('.card-item-icon');
                    if(cardItemIcon) cardItemIcon.style.marginRight = '1.5rem'; /* More space */

                    const cardDescription = card.querySelector('.card-item-description');
                    if (cardDescription) {
                        cardDescription.style.flexGrow = '1';
                        cardDescription.style.maxWidth = '30%'; /* Constrain description width */
                        cardDescription.style.marginBottom = '0';
                    }
                    const cardStats = card.querySelector('.card-item-stats');
                    if (cardStats) {
                        cardStats.style.border = 'none';
                        cardStats.style.padding = '0';
                        cardStats.style.marginBottom = '0';
                        cardStats.style.flexDirection = 'column'; /* Stack stats vertically */
                        cardStats.style.alignItems = 'flex-start';
                        cardStats.style.justifyContent = 'flex-start';
                        cardStats.style.gap = '0.5rem';
                        cardStats.style.minWidth = '120px'; /* Give stats some space */
                    }
                    const cardInfo = card.querySelector('.card-item-info');
                    if(cardInfo) cardInfo.style.flexGrow = '1';
                    
                    const cardActions = card.querySelector('.card-item-actions');
                    if (cardActions) {
                        cardActions.style.borderTop = 'none';
                        cardActions.style.paddingTop = '0';
                        cardActions.style.justifyContent = 'flex-end'; /* Push actions to the right */
                        cardActions.style.flexDirection = 'row'; /* Keep actions in a row */
                        cardActions.style.flexWrap = 'nowrap';
                        cardActions.style.minWidth = '150px'; /* Ensure actions have space */
                    }
                     // Hide header icon/title for list mode as they're now in the content
                     const headerIcon = card.querySelector('.card-item-icon');
                     if(headerIcon) headerIcon.style.display = 'none';
                     const headerTitle = card.querySelector('.card-item-title');
                     if(headerTitle) headerTitle.style.display = 'none';
                }
            });
        } else { // Grid view
            formationsGrid.classList.add('row');
            formationCardCols.forEach(col => {
                col.classList.remove('col-12', 'mb-3');
                col.classList.add('col-xl-3', 'col-lg-4', 'col-md-6', 'mb-4');
                const card = col.querySelector('.formation-card-item');
                if (card) {
                    card.style.flexDirection = 'column';
                    card.style.alignItems = 'stretch'; /* Reset alignment */
                    card.style.padding = '0'; /* Reset padding */

                    const headerContent = card.querySelector('.card-item-header > div');
                    if(headerContent) headerContent.style.flexGrow = 'initial';

                    const statusBadge = card.querySelector('.card-item-status-badge');
                    if(statusBadge) statusBadge.style.marginRight = '0';

                    const cardItemIcon = card.querySelector('.card-item-icon');
                    if(cardItemIcon) cardItemIcon.style.marginRight = '1rem';

                    const cardDescription = card.querySelector('.card-item-description');
                    if (cardDescription) {
                        cardDescription.style.flexGrow = 'initial';
                        cardDescription.style.maxWidth = 'none';
                        cardDescription.style.marginBottom = '1.5rem';
                    }
                    const cardStats = card.querySelector('.card-item-stats');
                    if (cardStats) {
                        cardStats.style.borderTop = '1px dashed #e2e8f0';
                        cardStats.style.borderBottom = '1px dashed #e2e8f0';
                        cardStats.style.padding = '1rem 0';
                        cardStats.style.marginBottom = '1.5rem';
                        cardStats.style.flexDirection = 'row';
                        cardStats.style.alignItems = 'stretch';
                        cardStats.style.justifyContent = 'space-around';
                        cardStats.style.gap = '0.5rem';
                        cardStats.style.minWidth = 'initial';
                    }
                    const cardInfo = card.querySelector('.card-item-info');
                    if(cardInfo) cardInfo.style.flexGrow = 'initial';

                    const cardActions = card.querySelector('.card-item-actions');
                    if (cardActions) {
                        cardActions.style.borderTop = '1px dashed #e2e8f0';
                        cardActions.style.paddingTop = '1.5rem';
                        cardActions.style.justifyContent = 'flex-start';
                        cardActions.style.flexDirection = 'row';
                        cardActions.style.flexWrap = 'wrap';
                        cardActions.style.minWidth = 'initial';
                    }

                    // Show header icon/title again
                    const headerIcon = card.querySelector('.card-item-icon');
                    if(headerIcon) headerIcon.style.display = 'flex';
                    const headerTitle = card.querySelector('.card-item-title');
                    if(headerTitle) headerTitle.style.display = 'block';
                }
            });
        }
    };

    // Apply saved view mode or default to grid on load
    const savedView = localStorage.getItem('formations_view_mode');
    if (savedView) {
        if (savedView === 'list') {
            if (listView) listView.checked = true;
            applyViewMode('list');
        } else {
            if (gridView) gridView.checked = true;
            applyViewMode('grid');
        }
    } else {
        if (gridView) gridView.checked = true;
        applyViewMode('grid');
    }

    if (listView) {
        listView.addEventListener('change', function() {
            if (this.checked) {
                applyViewMode('list');
                localStorage.setItem('formations_view_mode', 'list');
            }
        });
    }
    
    if (gridView) {
        gridView.addEventListener('change', function() {
            if (this.checked) {
                applyViewMode('grid');
                localStorage.setItem('formations_view_mode', 'grid');
            }
        });
    }

    // --- Modal Specific JavaScript ---
  // ... (Your existing JavaScript code) ...

// --- Modal Specific JavaScript ---
window.initializeModalForm = function(formElement) { // Khas tkoun global haka
    const prerequisitesContainer = formElement.querySelector('[id$="-prerequisites-container"]');
    const documentsContainer = formElement.querySelector('[id$="-documents-container"]');

    // ... (rest of addInputFieldHandler and removeInputFieldHandler functions) ...

    // Date validation for start_date and end_date
    const startDateInput = formElement.querySelector('[id$="_start_date"]');
    const endDateInput = formElement.querySelector('[id$="_end_date"]');
    
    if (startDateInput && endDateInput) {
        // Remove previous listeners before adding new ones (very important for dynamically loaded content)
        startDateInput.removeEventListener('change', updateEndDateMin);
        startDateInput.addEventListener('change', updateEndDateMin);

        // Initial check when the form is loaded
        updateEndDateMin.call(startDateInput); // Call it once to set initial min for end date

        function updateEndDateMin() {
            endDateInput.min = this.value;
            if (endDateInput.value && endDateInput.value < this.value) {
                endDateInput.value = this.value;
            }
        }
    }

    // Form submission loading state and client-side validation
    formElement.removeEventListener('submit', handleFormSubmission);
    formElement.addEventListener('submit', handleFormSubmission);

    function handleFormSubmission(e) {
        const submitBtn = e.submitter;
        const loadingSpinner = submitBtn ? submitBtn.querySelector('.spinner-border') : null;

        if (submitBtn) {
            submitBtn.disabled = true;
            if (loadingSpinner) loadingSpinner.classList.remove('hidden');
            submitBtn.classList.add('opacity-75', 'cursor-not-allowed');
        }

        const titleInput = formElement.querySelector('[id$="_title"]');
        const descriptionInput = formElement.querySelector('[id$="_description"]');
        let isValid = true;

        if (titleInput && titleInput.value.trim().length < 5) {
            Swal.fire({ icon: 'error', title: 'Erreur de Validation', text: 'Le titre doit contenir au moins 5 caractères.', confirmButtonText: 'OK' });
            isValid = false;
        } else if (descriptionInput && descriptionInput.value.trim().length < 20) {
            Swal.fire({ icon: 'error', title: 'Erreur de Validation', text: 'La description doit contenir au moins 20 caractères.', confirmButtonText: 'OK' });
            isValid = false;
        }

        if (!isValid) {
            e.preventDefault();
            if (submitBtn) { submitBtn.disabled = false; if (loadingSpinner) loadingSpinner.classList.add('hidden'); submitBtn.classList.remove('opacity-75', 'cursor-not-allowed'); }
            return;
        }

        // --- HADA HOWA L PART LI GHANDIR FIH TAGHYIR L'ASASI ---
        if (formElement.id === 'formation-edit-form' && formElement.querySelector('[id$="_capacity"]')) {
            e.preventDefault();

            // Khasna nakhdo l'ID men input hidden wella men l form action li kayna f blade template
            // L'ID rah jaya men l form.action f 'edit-modal-content.blade.php'
            const currentFormationId = formElement.action.split('/').slice(-1)[0]; // 👈 Changed slice(-2, -1) to slice(-1)
            // Ghadi nzid console.log bach n'ta'akkad men l'ID
            console.log('Formation ID li kaytsifet f l\'request:', currentFormationId); 

            const newCapacity = parseInt(formElement.querySelector('[id$="_capacity"]').value);

            // Fetch request needs to be aware of the exact URL structure
           fetch(`/formations/${currentFormationId}/inscriptions-count`)// 👈 Make sure this URL matches your api.php route
                .then(response => {
                    // console.log('Response Status:', response.status); // Zid hadi
                    if (!response.ok) {
                        // console.error('Network response for inscriptions count was not ok:', response.statusText); // Zid hadi
                        return response.json().then(err => { throw new Error(err.message || 'Network response was not ok'); }).catch(() => { throw new Error('Network response was not ok'); });
                    }
                    return response.json();
                })
                .then(data => {
                    // console.log('Inscriptions count data:', data); // Zid hadi
                    const currentInscriptions = data.active_inscriptions;

                    if (newCapacity < currentInscriptions) {
                        Swal.fire({
                            title: 'Attention !',
                            text: `Vous tentez de réduire la capacité à ${newCapacity} places alors qu'il y a déjà ${currentInscriptions} inscriptions actives. Continuer?`,
                            icon: 'warning',
                            showCancelButton: true, confirmButtonText: 'Oui, réduire la capacité', cancelButtonText: 'Annuler'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                submitBtn.disabled = false;
                                if (loadingSpinner) loadingSpinner.classList.add('hidden');
                                submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                                formElement.submit(); // Proceed with actual form submission
                            } else {
                                if (submitBtn) { submitBtn.disabled = false; if (loadingSpinner) loadingSpinner.classList.add('hidden'); submitBtn.classList.remove('opacity-75', 'cursor-not-allowed'); }
                            }
                        });
                    } else {
                        // If capacity is fine, proceed immediately
                        submitBtn.disabled = false;
                        if (loadingSpinner) loadingSpinner.classList.add('hidden');
                        submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
                        formElement.submit(); // Proceed with actual form submission
                    }
                })
                .catch(error => {
                    console.error('Error fetching inscriptions count:', error);
                    Swal.fire({ icon: 'error', title: 'Erreur', text: 'Impossible de vérifier le nombre d\'inscriptions. Veuillez réessayer.' + (error.message ? ` Détails: ${error.message}` : ''), confirmButtonText: 'OK' }); // Zid details error
                    if (submitBtn) { submitBtn.disabled = false; if (loadingSpinner) loadingSpinner.classList.add('hidden'); submitBtn.classList.remove('opacity-75', 'cursor-not-allowed'); }
                });
        } else {
            // For create form, or if edit form and no capacity warning needed, submit normally
            if (loadingSpinner) loadingSpinner.classList.add('hidden');
            submitBtn.disabled = false;
            submitBtn.classList.remove('opacity-75', 'cursor-not-allowed');
            formElement.submit();
        }
    }
}

// ... (rest of your DOMContentLoaded listener and other scripts) ...

    // Initialize the Create Form when the modal is shown
    const createFormationModalElement = document.getElementById('createFormationModal');
    if (createFormationModalElement) {
        createFormationModalElement.addEventListener('shown.bs.modal', function () {
            const createForm = document.getElementById('formation-create-form');
            if (createForm) {
                initializeModalForm(createForm);
            }
        });
        createFormationModalElement.addEventListener('hidden.bs.modal', function () {
            const createForm = document.getElementById('formation-create-form');
            if (createForm) {
                createForm.reset();
                createForm.querySelectorAll('.text-danger').forEach(el => el.remove());
                createForm.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
                
                // Clear and re-add one empty prerequisite field
                const createPrerequisitesContainer = document.getElementById('create-prerequisites-container');
                if (createPrerequisitesContainer) {
                    createPrerequisitesContainer.innerHTML = ''; // Clear all
                    addInputFieldHandler(createPrerequisitesContainer, 'prerequisites[]', 'fas fa-check-circle', 'Ex: Avoir des connaissances en HTML');
                }

                // Clear and re-add one empty document file input
                const createDocumentsContainer = document.getElementById('create-documents-container');
                if (createDocumentsContainer) {
                    createDocumentsContainer.innerHTML = ''; // Clear all
                    addInputFieldHandler(createDocumentsContainer, 'documents_files[]', 'fas fa-file-upload', '', true);
                }
            }
        });
    }

    // Initial load for create modal with errors (if page reloads with validation errors)
    @if ($errors->any() && session('open_create_formation_modal'))
        const createFormOnLoad = document.getElementById('formation-create-form');
        if (createFormOnLoad) {
            initializeModalForm(createFormOnLoad);
            const modalInstance = new bootstrap.Modal(document.getElementById('createFormationModal'));
            modalInstance.show();

            // Re-populate dynamic fields if old data exists
            const oldPrerequisites = {!! json_encode(old('prerequisites', [])) !!};
            const createPrerequisitesContainer = document.getElementById('create-prerequisites-container');
            if (createPrerequisitesContainer && oldPrerequisites.length > 0) {
                createPrerequisitesContainer.innerHTML = ''; // Clear initial empty field
                oldPrerequisites.forEach(prerequisite => {
                    const newItem = document.createElement('div');
                    newItem.classList.add('dynamic-field-item', 'new-dynamic-field', 'animate-item-enter');
                    newItem.innerHTML = `
                        <i class="fas fa-check-circle dynamic-field-icon"></i>
                        <input type="text" name="prerequisites[]" class="form-control new-form-control" value="${prerequisite}" placeholder="Ex: Avoir des connaissances en HTML">
                        <button type="button" class="remove-dynamic-btn">
                            <i class="fas fa-trash"></i>
                        </button>
                    `;
                    createPrerequisitesContainer.appendChild(newItem);
                });
            }
            // For documents_files, old() won't repopulate file inputs, but you can add empty ones
            // if there were errors on file fields. The current logic adds one by default.
        }
    @endif


    // Handle Edit Formation Modal (dynamic loading)
    const editFormationModalElement = document.getElementById('editFormationModal');
    const editFormationModalBody = document.getElementById('editFormationModalBody');
    if (editFormationModalElement) {
        editFormationModalElement.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const formationId = button.getAttribute('data-formation-id');

            editFormationModalBody.innerHTML = `
                <div class="d-flex justify-content-center align-items-center" style="min-height: 200px;">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
            `;

            fetch(`/formations/${formationId}/edit-modal`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(html => {
                    editFormationModalBody.innerHTML = html;
                    const editForm = editFormationModalBody.querySelector('#formation-edit-form');
                    if (editForm) {
                        initializeModalForm(editForm);
                    }
                })
                .catch(error => {
                    console.error('Error loading edit form:', error);
                    editFormationModalBody.innerHTML = `<p class="text-danger">Erreur lors du chargement du formulaire. Veuillez réessayer.</p>`;
                });
        });
    }
    // Initial load for edit modal with errors
    @if ($errors->any() && session('open_edit_formation_modal') && session('edit_formation_id'))
        const editFormationId = {{ session('edit_formation_id') }};
        fetch(`/formations/${editFormationId}/edit-modal?errors=1`) // Pass a flag for errors
            .then(response => response.text())
            .then(html => {
                editFormationModalBody.innerHTML = html;
                const editFormOnLoad = editFormationModalBody.querySelector('#formation-edit-form');
                if (editFormOnLoad) {
                    initializeModalForm(editFormOnLoad);
                    // Manually re-populate if old data is not handled by AJAX-loaded form
                    // This is for error cases when Laravel's old() values need to populate the dynamically loaded form
                    editFormOnLoad.querySelector('#edit_title').value = "{{ old('title', '') }}";
                    editFormOnLoad.querySelector('#edit_category_id').value = "{{ old('category_id', '') }}";
                    editFormOnLoad.querySelector('#edit_description').value = "{{ old('description', '') }}";
                    editFormOnLoad.querySelector('#edit_consultant_id').value = "{{ old('consultant_id', '') }}";
                    editFormOnLoad.querySelector('#edit_status').value = "{{ old('status', '') }}";
                    editFormOnLoad.querySelector('#edit_price').value = "{{ old('price', '') }}";
                    editFormOnLoad.querySelector('#edit_duration_hours').value = "{{ old('duration_hours', '') }}";
                    editFormOnLoad.querySelector('#edit_duration_minutes').value = "{{ old('duration_minutes', '') }}";
                    editFormOnLoad.querySelector('#edit_capacity').value = "{{ old('capacity', '') }}";
                    editFormOnLoad.querySelector('#edit_start_date').value = "{{ old('start_date', '') }}";
                    editFormOnLoad.querySelector('#edit_end_date').value = "{{ old('end_date', '') }}";
                    
                    // For payment options, prerequisites, documents_required, you'd need more complex JS re-population
                    // based on old() values. For now, they'll start fresh.
                }
                const modalInstance = new bootstrap.Modal(document.getElementById('editFormationModal'));
                modalInstance.show();
            });
    @endif

    // Initial animation for cards on page load
    const observerOptions = { threshold: 0.1, rootMargin: '0px 0px -50px 0px' };
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => { if (entry.isIntersecting) { entry.target.style.opacity = '1'; entry.target.style.transform = 'translateY(0)'; } });
    }, observerOptions);

    document.querySelectorAll('.formation-card-item').forEach(card => {
        card.style.opacity = '0';
        card.style.transform = 'translateY(20px)';
        card.style.transition = 'all 0.6s ease';
        observer.observe(card);
    });

    // Auto-hide alerts after 5 seconds
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => { if (alert) { alert.classList.add('fade'); setTimeout(() => alert.remove(), 500); } });
    }, 5000);

});
</script>
@endpush
@endsection

