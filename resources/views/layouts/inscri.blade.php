<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name', 'Portail Étudiant UITS'))</title>

    <!-- Favicon -->
    <link rel="shortcut icon" href="{{ asset('edmate/assets/images/logo/favicon.png') }}">
    
    <!-- BASE CSS FILES FROM YOUR TEMPLATE -->
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

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" xintegrity="sha512-SnH5WK+bZxgPHs44uWIX+LLJAJ9/2PkPKZ5QiAj6Ta86w+fsb2TkcmfRyVX3pBnMFcV7oQPJkl9QevSCWr3W6A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700;800;900&family=Nunito+Sans:wght@400;600;700&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS -->
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
    
    <style>
        /* This is the general CSS from your app.blade.php */
        :root {
            --primary-color: #C2185B;
            --secondary-color: #D32F2F;
            /* ... (more variables) ... */
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg,rgb(234, 102, 102) 0%, #764ba2 100%);
            min-height: 100vh;
            line-height: 1.6;
            color: black !important;
            overflow-x: hidden;
        }
        /* ... (more styles from your app.blade.php) ... */
    </style>
    @stack('styles')
</head>
<body class="light">

    <!-- هنا غيكون المحتوى ديال الصفحة فقط -->
    @yield('content')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const searchableItems = document.querySelectorAll('.list-card'); // Use the class of your cards

        searchInput.addEventListener('input', function() {
            const query = searchInput.value.toLowerCase();

            searchableItems.forEach(item => {
                const itemText = item.textContent.toLowerCase();

                if (itemText.includes(query)) {
                    item.style.display = 'block'; // Or whatever the default display style is
                } else {
                    item.style.display = 'none';
                }
            });
        });
    });
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // --- Notification Dropdown (Bootstrap's native dropdown) ---
        const closeNotificationDropdown = document.querySelector('.close-dropdown');
        if (closeNotificationDropdown) {
            closeNotificationDropdown.addEventListener('click', function() {
                const notificationDropdownElement = closeNotificationDropdown.closest('.dropdown');
                if (notificationDropdownElement) {
                    const bsCollapse = new bootstrap.Dropdown(notificationDropdownElement.querySelector('[data-bs-toggle="dropdown"]'));
                    bsCollapse.hide();
                }
            });
        }
        
        // Handle marking notifications as read
        const notificationDropdownToggle = document.getElementById('notificationDropdownToggle');
        if (notificationDropdownToggle) {
            notificationDropdownToggle.addEventListener('click', function() {
                const unreadCountElement = this.querySelector('.notification-badge');
                if (unreadCountElement) {
                    unreadCountElement.remove();
                }
            });
        }
        
        // Handle "Mark All as Read" button
        const markAllAsReadButton = document.getElementById('markAllAsRead');
        if (markAllAsReadButton) {
            markAllAsReadButton.addEventListener('click', function(e) {
                e.preventDefault();
                // Assurez-vous d'avoir la bonne route définie pour marquer toutes les notifications comme lues
                // Cette route doit être protégée par un middleware
                fetch('{{ route('admin.notifications.markAllAsRead') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mettre à jour l'interface utilisateur pour toutes les notifications
                        document.querySelectorAll('.notification-badge').forEach(badge => badge.remove());
                        document.querySelectorAll('.fw-bold').forEach(el => el.classList.remove('fw-bold'));
                        console.log('All notifications marked as read.');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                });
            });
        }
        
        // --- Profile Dropdown (Custom implementation, not Bootstrap's native) ---
        const profileDropdownToggle = document.getElementById('profileDropdownToggle');
        const profileDropdownMenu = document.getElementById('profileDropdownMenu');

        if (profileDropdownToggle && profileDropdownMenu) {
            profileDropdownToggle.addEventListener('click', function(e) {
                e.preventDefault();
                e.stopPropagation();
                profileDropdownMenu.classList.toggle('show');
            });

            document.addEventListener('click', function(e) {
                if (!profileDropdownToggle.contains(e.target) && !profileDropdownMenu.contains(e.target)) {
                    profileDropdownMenu.classList.remove('show');
                }
            });

            profileDropdownMenu.addEventListener('click', function(e) {
                e.stopPropagation();
            });
        }

        // --- Desktop Sidebar Toggle Button in Header ---
        const sidebarToggleButton = document.getElementById('sidebarToggle');
        if (sidebarToggleButton) {
            sidebarToggleButton.addEventListener('click', function() {
                document.body.classList.toggle('sidebar-collapsed');
            });
        }

        // --- Mobile Sidebar Toggle Button in Header ---
        const mobileSidebarToggle = document.getElementById('mobileSidebarToggle');
        if (mobileSidebarToggle) {
            mobileSidebarToggle.addEventListener('click', function() {
                document.body.classList.toggle('mobile-sidebar-open');
            });
        }

        // Apply saved sidebar state on load
        // if (localStorage.getItem('sidebarState') === 'collapsed') {
        //      document.body.classList.add('sidebar-collapsed');
        // }
    });

    // --- General Icon Interactions (Search, Theme, Fullscreen, etc.) ---
    function handleIconClick(action) {
        const iconElement = event.currentTarget;

        iconElement.style.transform = 'translateY(0) scale(0.95)';
        setTimeout(() => {
            iconElement.style.transform = 'translateY(-2px) scale(1)';
        }, 150);

        switch(action) {
            case 'search':
                console.log('Recherche activée');
                break;
            case 'theme':
                toggleTheme();
                break;
            case 'fullscreen':
                toggleFullscreen();
                break;
            case 'notifications':
                console.log('Notifications ouvertes');
                const notificationBadge = iconElement.querySelector('.notification-badge');
                if (notificationBadge) {
                    notificationBadge.style.animation = 'none';
                    setTimeout(() => {
                        notificationBadge.textContent = '0';
                        notificationBadge.style.opacity = '0.5';
                    }, 500);
                }
                break;
            case 'messages':
                console.log('Messages ouverts');
                break;
            case 'settings':
                console.log('Paramètres ouverts');
                break;
            case 'logout':
                console.log('Déconnexion...');
                alert('Vous avez été déconnecté.');
                break;
            case 'apps':
                console.log('Menu applications ouvert');
                break;
            default:
                console.log(`Action: ${action}`);
        }
    }

    // --- Theme Toggle functionality ---
    let isDarkTheme = false;
    function toggleTheme() {
        const themeIcon = document.querySelector('.theme-toggle i');
        const themeTooltip = themeIcon.parentElement.querySelector('.tooltip');
        const body = document.body;
        const header = document.querySelector('.header');

        if (!isDarkTheme) {
            themeIcon.className = 'fas fa-sun';
            if (themeTooltip) themeTooltip.textContent = 'Mode clair';
            body.style.filter = 'invert(1) hue-rotate(180deg)';
            header.style.filter = 'invert(1) hue-rotate(180deg)';
            isDarkTheme = true;
        } else {
            themeIcon.className = 'fas fa-moon';
            if (themeTooltip) themeTooltip.textContent = 'Mode sombre';
            body.style.filter = 'none';
            header.style.filter = 'none';
            isDarkTheme = false;
        }
    }

    // --- Fullscreen Toggle functionality ---
    function toggleFullscreen() {
        const fullscreenIcon = document.querySelector('.nav-icon [class*="fa-expand"], .nav-icon [class*="fa-compress"]');
        const fullscreenTooltip = fullscreenIcon.parentElement.querySelector('.tooltip');

        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            fullscreenIcon.className = 'fas fa-compress';
            if (fullscreenTooltip) fullscreenTooltip.textContent = 'Quitter plein écran';
        } else {
            document.exitFullscreen();
            fullscreenIcon.className = 'fas fa-expand';
            if (fullscreenTooltip) fullscreenTooltip.textContent = 'Plein écran';
        }
    }
</script>
    <!-- JavaScript -->
    <script src="{{ asset('edmate/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('edmate/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('edmate/assets/js/phosphor-icon.js') }}"></script>
    <script src="{{ asset('edmate/assets/js/main.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    @stack('scripts')
</body>
</html>
