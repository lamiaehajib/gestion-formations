<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', config('app.name', 'Portail Étudiant UITS'))</title>

    <link rel="shortcut icon" href="{{ asset('edmate/assets/images/logo/favicon.png') }}">

    {{-- REQUIRED FOR AJAX REQUESTS --}}
    <meta name="csrf-token" content="{{ csrf_token() }}">

    {{-- BASE CSS FILES FROM YOUR TEMPLATE --}}
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/file-upload.css') }}">
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/plyr.css') }}">
    <link rel="stylesheet" href="https://cdn.datatables.net/2.0.8/css/dataTables.dataTables.min.css">
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/full-calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/jquery-ui.css') }}">
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/editor-quill.css') }}">
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/apexcharts.css') }}">
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/calendar.css') }}">
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/jquery-jvectormap-2.0.5.css') }}">
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/main.css') }}">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    {{-- Font Awesome --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />

    {{-- Animate.css --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    {{-- Google Fonts --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="{{ asset('edmate/assets/images/logo/favicon.png') }}">

    {{-- Tailwind CSS via CDN (Only for development, compile for production!) --}}
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'custom-blue': '#1da1f2',
                    }
                }
            }
        }
    </script>
    {{-- END Tailwind CSS CDN --}}

    {{-- GLOBAL CSS (FOR ALL PAGES) --}}
    <style>
        
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

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg,rgb(234, 102, 102) 0%, #764ba2 100%);
            min-height: 100vh;
            line-height: 1.6;
            /* Add transition for body margin-left so content shifts smoothly */
            transition: margin-left 0.3s ease;
            color: black !Important;
            overflow-x: hidden; /* Prevent horizontal scroll when sidebar is off-screen */
        }

        /* Specific styles for theme toggle in dark mode. */
        body.dark-theme {
            filter: invert(1) hue-rotate(180deg);
        }
        body.dark-theme img { /* Images inside body should not be inverted */
            filter: invert(1) hue-rotate(180deg);
        }
        /* This targets the header specifically when body has dark-theme */
        body.dark-theme #mainHeader { /* Ensure this selector is correct if header needs to invert */
            filter: invert(1) hue-rotate(180deg);
        }

        button.btn.btn-action.btn-delete.tooltip-custom {
            color: green !important;
        }
        a.btn.btn-action.btn-edit.tooltip-custom {
            color: #df9424 !important;
        }

        /* Custom Scrollbar Styling */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(45deg, #D32F2F, #C2185B); /* Red to Magenta */
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(45deg, #C2185B, #D32F2F);
        }
        /* END Custom Scrollbar Styling */

        .fa-2x {
            font-size: 2em !important;
        }
        i.fa-solid.fa-file-alt.fa-2x.mr-3.text-\[\#D32F2F\] {
            font-size: 27px !important;
        }
        i.fa-solid.fa-calendar-alt.fa-2x.mr-3.text-\[\#C2185B\] {
            font-size: 27px !important;
        }
        .bg-black {
            --bs-bg-opacity: 1;
            background-color: rgb(225 46 46) !important;
        }

        /* Styles for the global loading overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(229, 62, 62, 0.9); /* Red overlay background */
            z-index: 9999;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-direction: column;
            transition: opacity 0.3s ease-in-out; /* For smooth fade */
            opacity: 0; /* Initially hidden */
            visibility: hidden; /* Initially hidden */
        }

        .loading-overlay.active {
            opacity: 1;
            visibility: visible;
        }

        .loading-spinner-global { /* Renamed to avoid conflict with other spinners */
            width: 60px;
            height: 60px;
            border: 6px solid rgba(255, 255, 255, 0.3);
            border-top: 6px solid #e53e3e; /* Red spinner */
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to {
                transform: rotate(360deg);
            }
        }

        /* Styles for Scroll Progress Bar */
        .scroll-progress-bar {
            position: fixed;
            top: 0;
            left: 0;
            width: 0%; /* Initial width */
            height: 4px; /* Thickness of the bar */
            background: linear-gradient(90deg, #E53E3E, #C53030); /* Red gradient, or use var(--primary-color) if defined */
            z-index: 10000; /* Ensure it's on top of everything */
            transition: width 0.1s linear; /* Smooth transition for width change */
        }

        
        .dashboard-main-wrapper {
            margin-left: 280px; /* Default expanded sidebar width for desktop */
            transition: margin-left 0.3s ease; /* Smooth transition */
            position: relative;
        }

        body.sidebar-collapsed .dashboard-main-wrapper {
            margin-left: 80px; /* New collapsed sidebar width for desktop */
        }

        /* Mobile specific styles for the sidebar and main wrapper */
        @media (max-width: 1199.98px) { /* Screens smaller than XL (desktop) */
            .dashboard-main-wrapper {
                margin-left: 0; /* No left margin on mobile, sidebar is off-screen */
            }

            .sidebar {
                left: -280px; /* Hide sidebar completely off-screen */
                box-shadow: none; /* Remove shadow when off-screen */
                transition: left 0.3s ease; /* Smooth slide in/out */
            }

            body.mobile-sidebar-open .sidebar {
                left: 0; /* Slide sidebar into view */
                box-shadow: 0 10px 30px rgba(211, 47, 47, 0.1); /* Add shadow when open */
            }

            /* Overlay for mobile sidebar  */
            .side-overlay {
                display: none; /* Hidden by default */
                position: fixed;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                background: rgba(0, 0, 0, 0.5); /* Semi-transparent black */
                z-index: 999; /* Below sidebar, above content */
                transition: opacity 0.3s ease;
                opacity: 0;
                visibility: hidden;
            }

            body.mobile-sidebar-open .side-overlay {
                display: block;
                opacity: 1;
                visibility: visible;
            }
        }

        .form-control {
            width: 100%;
            padding: 1rem;
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            font-size: 1rem;
            transition: all 0.3s ease;
            background: white;
            position: relative;
            color: black !important;
        }

        .form-control-filter {
            border-radius: 10px;
            border: 2px solid #e2e8f0;
            padding: 0.75rem 1rem;
            font-size: 0.95rem;
            transition: all 0.3s ease;
            color: black !important;
        }

        .input-styled, .select-styled {
            width: 100%;
            padding: 16px 20px 16px 50px;
            font-size: 1rem;
            color: var(--text-primary) !important;
            background: var(--glass-bg);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 2px solid var(--glass-border);
            border-radius: var(--radius-lg);
            transition: var(--transition);
            appearance: none;
            box-shadow: var(--shadow-sm);
        }

        .select-inline {
            padding: 6px 25px 6px 10px;
            font-size: 0.85rem;
            border-radius: 6px;
            min-width: 100px;
            color: black !important;
        }

        select#chosen_installments {
            color: black !important;
        }

        select.form-select.modern-select {
            color: black !important;
        }

        .form-select-modern {
            border-radius: 15px;
            border: 2px solid var(--payment-card-border);
            padding: 12px 18px;
            font-weight: 500;
            transition: all 0.3s ease;
            background-color: #ffffff;
            width: 100%;
            color: #000 !important;
        }
        .form-control-modern.bg-gray-100 {
            background-color: #f3f4f6;
            cursor: not-allowed;
            color: #000 !important;
        }

        button.btn.btn-new-course {
            background-color: red !important;
            color: black !important;
            border: 1px #e33939 solid;
        }

        select#status {
            color: black !important;
        }
        .form-select {
            --bs-form-select-bg-img: url(data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e);
            display: block;
            width: 100%;
            padding: .375rem 2.25rem .375rem .75rem;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.5;
            color: var(--bs-body-color) !important;
            background-color: var(--bs-body-bg);
            background-image: var(--bs-form-select-bg-img), var(--bs-form-select-bg-icon, none);
            background-repeat: no-repeat;
            background-position: right .75rem center;
            background-size: 16px 12px;
            border: var(--bs-border-width) solid var(--bs-border-color);
            border-radius: var(--bs-border-radius);
            transition: border-color .15s ease-in-out, box-shadow .15s ease-in-out;
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
        }

        option {
    color: black !important;
}

@media (min-width: 1200px) {
    .col-xl-3 {
        flex: 0 0 auto;
        width: 48% !important;
    }
}

.table-striped>tbody>tr:nth-of-type(odd)>* {
    --bs-table-color-type: var(--bs-table-striped-color);
    --bs-table-bg-type: var(--bs-table-striped-bg);
    color: black;
}
    </style>

    
    @stack('styles')

</head>
<body class="light">
    {{-- Globa more scrol tes ss Bar  HTML --}}
    <div id="scrollProgressBar" class="scroll-progress-bar"></div>

    {{-- Global Loading Overlay Element --}}
    <div class="loading-overlay" id="globalLoadingOverlay">
        <div class="loading-spinner-global"></div>
        <p style="color: white; margin-top: 20px; font-size: 18px; font-weight: 600;">
            Chargement en cours...
        </p>
    </div>

    <div class="preloader">
        <div class="loader"></div>
    </div>

    <div class="side-overlay" id="mobileSidebarOverlay"></div>

@auth
    @include('components.notification-banner')
@endauth

@auth
    @include('components.payment-reminder-modal')
@endauth
@auth
    @include('components.satisfaction-survey-modal')
@endauth
    
            @include('layouts.sidebar')
     

    <div class="dashboard-main-wrapper">
      
       
                @include('layouts.header')
        

        <div class="dashboard-body">
            <div class="container-fluid">
                @yield('content')
            </div>
        </div>

        @include('layouts.footer')
    </div>

   
    <div id="scrollToTopBtn">
        <i class="fas fa-arrow-up"></i>
    </div>
{{-- BASE JS FILES FROM YOUR TEMPLATE (jQuery and Bootstrap first) --}}
<script src="{{ asset('edmate/assets/js/jquery-3.7.1.min.js') }}"></script>

{{-- ⚠️ BOOTSTRAP - GARDE SEULEMENT UNE VERSION --}}
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Other main JS files --}}
<script src="{{ asset('edmate/assets/js/phosphor-icon.js') }}"></script>
<script src="{{ asset('edmate/assets/js/file-upload.js') }}"></script>
<script src="{{ asset('edmate/assets/js/plyr.js') }}"></script>
<script src="https://cdn.datatables.net/2.0.8/js/dataTables.min.js"></script>
<script src="{{ asset('edmate/assets/js/full-calendar.js') }}"></script>
<script src="{{ asset('edmate/assets/js/jquery-ui.js') }}"></script>
<script src="{{ asset('edmate/assets/js/editor-quill.js') }}"></script>
<script src="{{ asset('edmate/assets/js/apexcharts.min.js') }}"></script>
<script src="{{ asset('edmate/assets/js/calendar.js') }}"></script>
<script src="{{ asset('edmate/assets/js/jquery-jvectormap-2.0.5.min.js') }}"></script>
<script src="{{ asset('edmate/assets/js/jquery-jvectormap-world-mill-en.js') }}"></script>
<script src="{{ asset('edmate/assets/js/main.js') }}"></script>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

{{-- DEBUG SCRIPT --}}
<script>
    console.log('🔍 Checking Bootstrap...');
    console.log('Bootstrap version:', typeof bootstrap !== 'undefined' ? bootstrap : 'NOT LOADED');
    console.log('jQuery version:', typeof $ !== 'undefined' ? $.fn.jquery : 'NOT LOADED');
</script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Global JavaScript for Loading Overlay, Scroll-to-Top, and Progress Bar --}}
    



  <!-- REPLACE THIS SECTION IN app.blade.php around line 250 -->
<!-- From: "Global JavaScript for Loading Overlay, Scroll-to-Top, and Progress Bar" -->

<script>
    // ✅ DEFINE THESE FUNCTIONS FIRST (they were commented out before)
    function showGlobalLoadingOverlay() {
        const overlay = document.getElementById('globalLoadingOverlay');
        if (overlay) {
            overlay.classList.add('active');
        }
    }

    function hideGlobalLoadingOverlay() {
        const overlay = document.getElementById('globalLoadingOverlay');
        if (overlay) {
            overlay.classList.remove('active');
        }
    }

    // Show loading overlay on all form submissions
    document.addEventListener('submit', function(e) {
        // Don't show loading for modals or AJAX forms
        if (!e.target.closest('.modal')) {
            showGlobalLoadingOverlay();
        }
    }, true);

    // Show loading overlay on all anchor tag clicks that lead to a new page
    document.addEventListener('click', function(e) {
        const link = e.target.closest('a[href]');
        if (link && link.hostname === window.location.hostname && 
            !link.getAttribute('href').startsWith('#') && 
            !link.hasAttribute('download') &&
            !link.closest('.modal') &&
            !link.closest('[data-bs-toggle="modal"]')) {
            showGlobalLoadingOverlay();
        }
    }, true);

    // Hide loading overlay when the page finishes loading
    window.addEventListener('load', function() {
        hideGlobalLoadingOverlay();
    });

    // Hide loading overlay on browser back/forward
    window.addEventListener('pageshow', function(event) {
        if (event.persisted) {
            hideGlobalLoadingOverlay();
        }
    });

    // ✅ Scroll-to-Top Button
    document.addEventListener('DOMContentLoaded', function() {
        const scrollToTopBtn = document.getElementById('scrollToTopBtn');

        window.addEventListener('scroll', function() {
            if (window.scrollY > 300) {
                scrollToTopBtn?.classList.add('show');
            } else {
                scrollToTopBtn?.classList.remove('show');
            }
        });

        scrollToTopBtn?.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    });

    // ✅ Scroll Progress Bar
    document.addEventListener('DOMContentLoaded', function() {
        const progressBar = document.getElementById('scrollProgressBar');

        window.addEventListener('scroll', function() {
            const scrollTop = window.scrollY;
            const docHeight = document.documentElement.scrollHeight;
            const winHeight = window.innerHeight;
            const scrollPercent = (scrollTop / (docHeight - winHeight)) * 100;
            if (progressBar) {
                progressBar.style.width = Math.min(scrollPercent, 100) + '%';
            }
        });
    });

    // ✅ Mobile Sidebar Toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileToggleBtn = document.querySelector('.toggle-btn.d-xl-none');
        const mobileOverlay = document.getElementById('mobileSidebarOverlay');
        const sidebarCloseBtn = document.querySelector('.sidebar-close-btn');

        mobileToggleBtn?.addEventListener('click', function() {
            document.body.classList.add('mobile-sidebar-open');
        });

        mobileOverlay?.addEventListener('click', function() {
            document.body.classList.remove('mobile-sidebar-open');
        });

        sidebarCloseBtn?.addEventListener('click', function() {
            document.body.classList.remove('mobile-sidebar-open');
        });
    });
</script>

<script>
    // ✅ Notifications & Profile Dropdown
    document.addEventListener('DOMContentLoaded', function() {
        // Close notification dropdown
        const closeNotificationDropdown = document.querySelector('.close-dropdown');
        if (closeNotificationDropdown) {
            closeNotificationDropdown.addEventListener('click', function() {
                const dropdown = this.closest('.dropdown');
                const toggleBtn = dropdown?.querySelector('[data-bs-toggle="dropdown"]');
                if (toggleBtn && typeof bootstrap !== 'undefined') {
                    bootstrap.Dropdown.getInstance(toggleBtn)?.hide();
                }
            });
        }
        
        // Mark all notifications as read
        const markAllAsReadButton = document.getElementById('markAllAsRead');
        if (markAllAsReadButton) {
            markAllAsReadButton.addEventListener('click', function(e) {
                e.preventDefault();
                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
                
                fetch('{{ route("admin.notifications.markAllAsRead") }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.querySelectorAll('.notification-badge').forEach(b => b.remove());
                        document.querySelectorAll('.fw-bold').forEach(el => el.classList.remove('fw-bold'));
                    }
                })
                .catch(error => console.error('Error:', error));
            });
        }
        
        // Profile Dropdown Toggle
        const profileToggle = document.getElementById('profileDropdownToggle');
        const profileMenu = document.getElementById('profileDropdownMenu');

        if (profileToggle && profileMenu) {
            profileToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                profileMenu.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!profileToggle.contains(e.target) && !profileMenu.contains(e.target)) {
                    profileMenu.classList.remove('show');
                }
            });
        }

        // Sidebar Toggle Buttons
        document.getElementById('sidebarToggle')?.addEventListener('click', function() {
            document.body.classList.toggle('sidebar-collapsed');
        });

        document.getElementById('mobileSidebarToggle')?.addEventListener('click', function() {
            document.body.classList.toggle('mobile-sidebar-open');
        });
    });
</script>

<script>
    // ✅ Icon Interactions (Search, Theme, Fullscreen)
    function handleIconClick(action) {
        const iconElement = event.currentTarget;
        iconElement.style.transform = 'translateY(0) scale(0.95)';
        
        setTimeout(() => {
            iconElement.style.transform = 'translateY(-2px) scale(1)';
        }, 150);

        switch(action) {
            case 'search':
                console.log('🔍 Search activated');
                break;
            case 'theme':
                toggleTheme();
                break;
            case 'fullscreen':
                toggleFullscreen();
                break;
            default:
                console.log(`Action: ${action}`);
        }
    }

    let isDarkTheme = false;
    function toggleTheme() {
        const themeIcon = document.querySelector('.theme-toggle i');
        const body = document.body;
        
        if (!isDarkTheme) {
            themeIcon.className = 'fas fa-sun';
            body.style.filter = 'invert(1) hue-rotate(180deg)';
            isDarkTheme = true;
        } else {
            themeIcon.className = 'fas fa-moon';
            body.style.filter = 'none';
            isDarkTheme = false;
        }
    }

    function toggleFullscreen() {
        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen().catch(err => {
                console.warn('Fullscreen request denied:', err);
            });
        } else {
            document.exitFullscreen();
        }
    }
</script>
<script>
    // ============================================
    // 📱 MOBILE SIDEBAR TOGGLE - VERSION FINALE
    // ============================================
    document.addEventListener('DOMContentLoaded', function() {
        console.log('🔧 Initializing mobile sidebar...');
        
        // Get all required elements
        const mobileToggleBtn = document.querySelector('.toggle-btn.d-xl-none');
        const desktopToggleBtn = document.querySelector('#sidebarToggle');
        const mobileOverlay = document.getElementById('mobileSidebarOverlay');
        const sidebarCloseBtn = document.querySelector('.sidebar-close-btn');
        const sidebar = document.querySelector('.sidebar');
        const body = document.body;

        // Debug: Check if elements exist
        console.log('📋 Element Check:');
        console.log('  - Mobile Toggle Button:', mobileToggleBtn ? '✅' : '❌');
        console.log('  - Desktop Toggle Button:', desktopToggleBtn ? '✅' : '❌');
        console.log('  - Overlay:', mobileOverlay ? '✅' : '❌');
        console.log('  - Close Button:', sidebarCloseBtn ? '✅' : '❌');
        console.log('  - Sidebar:', sidebar ? '✅' : '❌');

        // Function to check if we're on mobile
        function isMobile() {
            return window.innerWidth < 1200;
        }

        // Function to open mobile sidebar
        function openMobileSidebar() {
            if (!isMobile()) return;
            console.log('📱 Opening mobile sidebar...');
            body.classList.add('mobile-sidebar-open');
            if (mobileOverlay) mobileOverlay.style.display = 'block';
            setTimeout(() => {
                if (mobileOverlay) {
                    mobileOverlay.style.opacity = '1';
                    mobileOverlay.style.visibility = 'visible';
                }
            }, 10);
        }

        // Function to close mobile sidebar
        function closeMobileSidebar() {
            console.log('📱 Closing mobile sidebar...');
            body.classList.remove('mobile-sidebar-open');
            if (mobileOverlay) {
                mobileOverlay.style.opacity = '0';
                mobileOverlay.style.visibility = 'hidden';
                setTimeout(() => {
                    mobileOverlay.style.display = 'none';
                }, 300);
            }
        }

        // Function to toggle desktop sidebar collapse
        function toggleDesktopSidebar() {
            if (isMobile()) return;
            console.log('💻 Toggling desktop sidebar...');
            body.classList.toggle('sidebar-collapsed');
        }

        // Mobile toggle button click
        if (mobileToggleBtn) {
            mobileToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('🔘 Mobile toggle clicked');
                openMobileSidebar();
            });
        } else {
            console.warn('⚠️ Mobile toggle button not found!');
        }

        // Desktop toggle button click
        if (desktopToggleBtn) {
            desktopToggleBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('🔘 Desktop toggle clicked');
                toggleDesktopSidebar();
            });
        }

        // Overlay click to close (mobile only)
        if (mobileOverlay) {
            mobileOverlay.addEventListener('click', function(e) {
                if (isMobile()) {
                    console.log('🔘 Overlay clicked');
                    closeMobileSidebar();
                }
            });
        } else {
            console.warn('⚠️ Mobile overlay not found!');
        }

        // Close button click
        if (sidebarCloseBtn) {
            sidebarCloseBtn.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('🔘 Close button clicked');
                closeMobileSidebar();
            });
        } else {
            console.warn('⚠️ Close button not found!');
        }

        // Close sidebar when clicking on menu items (mobile only)
        const menuLinks = document.querySelectorAll('.sidebar-menu__link');
        menuLinks.forEach(link => {
            link.addEventListener('click', function(e) {
                if (isMobile() && body.classList.contains('mobile-sidebar-open')) {
                    console.log('🔘 Menu link clicked on mobile');
                    setTimeout(() => closeMobileSidebar(), 200);
                }
            });
        });

        // Close sidebar on ESC key (mobile only)
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape' && isMobile() && body.classList.contains('mobile-sidebar-open')) {
                console.log('⌨️ ESC key pressed');
                closeMobileSidebar();
            }
        });

        // Handle window resize
        let resizeTimer;
        window.addEventListener('resize', function() {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(() => {
                console.log('📐 Window resized:', window.innerWidth);
                // Close mobile sidebar if switching to desktop
                if (!isMobile() && body.classList.contains('mobile-sidebar-open')) {
                    closeMobileSidebar();
                }
                // Remove desktop collapse if switching to mobile
                if (isMobile() && body.classList.contains('sidebar-collapsed')) {
                    body.classList.remove('sidebar-collapsed');
                }
            }, 250);
        });

        // Prevent body scroll when sidebar is open on mobile
        const preventScroll = (e) => {
            if (isMobile() && body.classList.contains('mobile-sidebar-open')) {
                if (!sidebar.contains(e.target)) {
                    e.preventDefault();
                }
            }
        };

        document.addEventListener('touchmove', preventScroll, { passive: false });

        console.log('✅ Mobile sidebar initialized successfully');
    });
</script>

    {{-- Pushed scripts from individual Blade views --}}
    @stack('scripts')
</body>
</html>
