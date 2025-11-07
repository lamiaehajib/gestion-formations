
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Styled Navbar with Dropdowns</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.0.3/dist/assets/initial-dot-shadow.css">
    <link rel="stylesheet" href="https://unpkg.com/@phosphor-icons/web@2.0.3/dist/phosphor.css">

    <style>
        /* Define your custom colors and utility classes first */
        :root {
            --main-50: #e3f2fd;
            --main-100: #bbdefb;
            --main-600: #1e88e5;
            --primary-600: #1976d2;
            --gray-500: #6c757d;
            --gray-100: #f8f9fa;
            --gray-200: #e9ecef;
            --gray-300: #dee2e6;
            --text-primary-600: #1976d2;
            --red-alert: #ff4757;
            --green-online: #2ecc71;
            --dark-background-start: #D32F2F;
            --dark-background-end: #C2185B;
            --light-text: white;
            --light-bg-alpha: rgba(255, 255, 255, 0.1);
            --light-border-alpha: rgba(255, 255, 255, 0.15);
            --light-hover-bg-alpha: rgba(255, 255, 255, 0.2);
            --light-hover-border-alpha: rgba(255, 255, 255, 0.3);
            --shadow-primary: rgba(0, 0, 0, 0.15);
            --shadow-secondary: rgba(0, 0, 0, 0.2);
            --tooltip-bg: rgba(0, 0, 0, 0.8);
            --dropdown-border: #eee;
            --dropdown-header-bg: #f9f9f9;
            --dropdown-item-hover-bg: #f0f2f5;
            --dropdown-item-hover-color: #D32F2F;
            --logout-bg: #fef0f0;
            --logout-color: #e74c3c;
            --logout-hover-bg: #fcdbdc;
            --logout-hover-color: #c0392b;
        }

        /* Utility classes from the original HTML's inline styles/assumed framework */
        .flex-between {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .flex-align {
            display: flex;
            align-items: center;
        }
        .flex-center {
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .gap-16 { gap: 16px; }
        .gap-12 { gap: 12px; }
        .gap-4 { gap: 4px; }
        .text-26 { font-size: 26px; }
        .text-xl { font-size: 20px; }
        .text-sm { font-size: 14px; }
        .text-15 { font-size: 15px; }
        .text-13 { font-size: 13px; }
        .text-gray-500 { color: var(--gray-500); }
        .text-gray-100 { color: var(--gray-100); }
        .text-gray-200 { color: var(--gray-200); }
        .text-gray-300 { color: var(--gray-300); }
        .text-white { color: var(--light-text); }
        .text-dark { color: #212529; }
        .text-muted { color: var(--bs-text-muted); }
        .text-danger { color: #dc3545; }
        .text-primary { color: #007bff; }
        .bg-main-50 { background-color: var(--main-50); }
        .bg-main-100 { background-color: var(--main-100); }
        .bg-main-600 { background-color: var(--main-600); }
        .bg-white { background-color: var(--light-text); }
        .bg-gray-100 { background-color: var(--gray-100); }
        .hover-bg-main-100:hover { background-color: var(--main-100); }
        .hover-text-primary-600:hover { color: var(--primary-600); }
        .hover-scale-1:hover { transform: scale(1.1); }
        .hover-text-decoration-underline:hover { text-decoration: underline; }
        .w-350 { max-width: 350px; width: 100%; }
        .w-40 { width: 40px; }
        .h-40 { height: 40px; }
        .w-48 { width: 48px; }
        .h-48 { height: 48px; }
        .ps-40 { padding-left: 40px; }
        .py-8 { padding-top: 8px; padding-bottom: 8px; }
        .px-24 { padding-left: 24px; padding-right: 24px; }
        .p-0 { padding: 0; }
        .p-24 { padding: 24px; }
        .py-13 { padding-top: 13px; padding-bottom: 13px; }
        .px-8 { padding-left: 8px; padding-right: 8px; }
        .py-2 { padding-top: 2px; padding-bottom: 2px; }
        .max-h-270 { max-height: 270px; }
        .border-transparent { border-color: transparent !important; }
        .focus-border-main-600:focus { border-color: var(--main-600) !important; }
        .rounded-pill { border-radius: 50rem !important; }
        .rounded-circle { border-radius: 50% !important; }
        .rounded-6 { border-radius: 6px !important; }
        .rounded-12 { border-radius: 12px !important; }
        .placeholder-15::placeholder { font-size: 15px; }
        .fw-semibold { font-weight: 600; }
        .fw-medium { font-weight: 500; }
        .fw-bold { font-weight: 700; }
        .mb-0 { margin-bottom: 0 !important; }
        .mb-24 { margin-bottom: 24px !important; }
        .mt-8 { margin-top: 8px !important; }
        .me-3 { margin-right: 1rem !important; }
        .ms-1 { margin-left: 0.25rem !important; }
        .d-xl-none { display: none !important; }
        @media (max-width: 1199.98px) {
            .d-xl-none { display: flex !important; }
        }
        .d-sm-block { display: none !important; }
        @media (min-width: 576px) {
            .d-sm-block { display: block !important; }
        }
        .pointer-event-none { pointer-events: none; }
        .object-fit-cover { object-fit: cover; }
        .overflow-hidden { overflow: hidden; }
        .overflow-y-auto { overflow-y: auto; }
        .scroll-sm::-webkit-scrollbar {
            width: 8px;
        }
        .scroll-sm::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        .scroll-sm::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }
        .scroll-sm::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        .transition-2 { transition: all 0.2s ease-in-out; }
        .box-shadow-custom { box-shadow: 0 0.5rem 1rem var(--shadow-primary) !important; }
        .text-line-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Header Specific Styles */
        .header {
            background: linear-gradient(135deg, var(--dark-background-start) 0%, var(--dark-background-end) 100%);
            padding: 0 30px;
            height: 62px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            box-shadow: 0 4px 20px var(--shadow-primary);
            position: sticky;
            top: 0;
            z-index: 1000;
        }

        /* Grouping for left side of the header */
        .header-left {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        /* Support Contact Styles */
        .support-contact {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-left: 20px;
            padding-left: 20px;
            border-left: 1px solid rgba(255, 255, 255, 0.2);
        }

        .support-item {
            display: flex;
            align-items: center;
            gap: 8px;
            color: var(--light-text);
            text-decoration: none;
            font-size: 13px;
            padding: 6px 12px;
            background: var(--light-bg-alpha);
            border: 1px solid var(--light-border-alpha);
            border-radius: 8px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
            white-space: nowrap;
        }

        .support-item:hover {
            background: var(--light-hover-bg-alpha);
            border-color: var(--light-hover-border-alpha);
            transform: translateY(-2px);
            box-shadow: 0 4px 15px var(--shadow-secondary);
            color: var(--light-text);
        }

        .support-item i {
            font-size: 14px;
            animation: ring 3s infinite;
        }

        @keyframes ring {
            0%, 100% { transform: rotate(0deg); }
            10%, 30% { transform: rotate(-10deg); }
            20%, 40% { transform: rotate(10deg); }
        }

        .support-label {
            font-weight: 600;
            margin-right: 5px;
        }

        /* Menu toggle button */
        .toggle-btn, .menu-dots-toggle {
            width: 50px;
            height: 50px;
            background: var(--light-bg-alpha);
            border: 1px solid var(--light-border-alpha);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light-text);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 18px;
            backdrop-filter: blur(10px);
        }

        .toggle-btn:hover, .menu-dots-toggle:hover {
            background: var(--light-hover-bg-alpha);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--shadow-secondary);
            border-color: var(--light-hover-border-alpha);
        }

        /* Grouping for the center part (title and search) */
        .header-center {
            display: flex;
            align-items: center;
            justify-content: center;
            flex-grow: 1;
            gap: 15px;
        }

        /* Grouping for the right side (icons) */
        .header-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logo-text {
            font-size: 24px;
            font-weight: 700;
            letter-spacing: -0.5px;
            color: var(--light-text);
            white-space: nowrap;
            flex-shrink: 0;
        }
        
        .nav-icons {
            display: flex;
            align-items: center;
            gap: 8px;
            flex-shrink: 0;
        }

        .nav-icon {
            width: 50px;
            height: 50px;
            background: var(--light-bg-alpha);
            border: 1px solid var(--light-border-alpha);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light-text);
            cursor: pointer;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            font-size: 18px;
            position: relative;
            backdrop-filter: blur(10px);
        }

        .nav-icon:hover {
            background: var(--light-hover-bg-alpha);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px var(--shadow-secondary);
            border-color: var(--light-hover-border-alpha);
        }

        .nav-icon:active {
            transform: translateY(0);
            transition: all 0.1s;
        }

        .user-avatar {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            overflow: hidden;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.2);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light-text);
            font-weight: bold;
            font-size: 20px;
            background-color: #C2185B;
        }

        .user-avatar:hover {
            transform: scale(1.05);
            border-color: rgba(255, 255, 255, 0.4);
            box-shadow: 0 8px 25px var(--shadow-secondary);
        }

        .user-avatar img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            display: block;
        }

        /* Notification badge */
        .notification-badge {
            position: absolute;
            top: -3px;
            right: -3px;
            width: 18px;
            height: 18px;
            background: linear-gradient(45deg, var(--red-alert), #ff3838);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 10px;
            font-weight: bold;
            color: var(--light-text);
            border: 2px solid var(--dark-background-start);
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .tooltip {
            position: absolute;
            bottom: -35px;
            left: 50%;
            transform: translateX(-50%);
            background: var(--tooltip-bg);
            color: var(--light-text);
            padding: 6px 10px;
            border-radius: 6px;
            font-size: 12px;
            white-space: nowrap;
            opacity: 0;
            visibility: hidden;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .tooltip::before {
            content: '';
            position: absolute;
            top: -4px;
            left: 50%;
            transform: translateX(-50%);
            width: 0;
            height: 0;
            border-left: 4px solid transparent;
            border-right: 4px solid transparent;
            border-bottom: 4px solid var(--tooltip-bg);
        }

        .nav-icon:hover .tooltip {
            opacity: 1;
            visibility: visible;
            bottom: -40px;
        }

        .dropdown-menu.profile-custom-dropdown {
            background-color: var(--light-text);
            border-radius: 12px;
            box-shadow: 0 8px 30px var(--shadow-primary);
            width: 220px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: opacity 0.3s ease, transform 0.3s ease, visibility 0.3s;
            z-index: 999;
            overflow: hidden;
            position: absolute;
            right: 20px;
            top: 60px;
        }

        .dropdown-menu.profile-custom-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }
        
        .dropdown-header {
            display: flex;
            align-items: center;
            padding: 15px;
            border-bottom: 1px solid var(--dropdown-border);
            background-color: var(--dropdown-header-bg);
        }
        
        .dropdown-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            border: 2px solid #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--light-text);
            font-weight: bold;
            font-size: 18px;
            background-color: #D32F2F;
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: bold;
            color: #333;
            font-size: 15px;
        }

        .user-role {
            font-size: 13px;
            color: #777;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: #555;
            text-decoration: none;
            transition: background-color 0.2s ease, color 0.2s ease;
            font-size: 15px;
        }

        .dropdown-item i {
            margin-right: 12px;
            color: #888;
            font-size: 16px;
        }

        .dropdown-item:hover {
            background-color: var(--dropdown-item-hover-bg);
            color: var(--dropdown-item-hover-color);
        }

        .dropdown-item:hover i {
            color: var(--dropdown-item-hover-color);
        }

        .dropdown-item.logout {
            background-color: var(--logout-bg);
            color: var(--logout-color);
            border-top: 1px solid var(--dropdown-border);
            margin-top: 5px;
        }

        .dropdown-item.logout i {
            color: var(--logout-color);
        }

        .dropdown-item.logout:hover {
            background-color: var(--logout-hover-bg);
            color: var(--logout-hover-color);
        }
        
        /* Responsive Design Overrides */
        @media (max-width: 1199.98px) {
            .header {
                padding: 0 15px;
            }
            .header-center {
                display: none;
            }
            .support-contact {
                display: none;
            }
            .header-left {
                gap: 8px;
            }
            .header-right {
                gap: 4px;
            }
            .nav-icon {
                width: 45px;
                height: 45px;
                font-size: 16px;
            }
            .user-avatar {
                width: 45px;
                height: 45px;
            }
            .logo-text {
                font-size: 20px;
            }
            .dropdown-menu.profile-custom-dropdown {
                top: 50px;
                right: 15px;
            }
        }

        @media (max-width: 991.98px) {
            .support-contact {
                display: none;
            }
        }

        .messages-dropdown {
            position: absolute;
            right: 20px;
            top: 70px;
            background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
            border-radius: 16px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
            width: 420px;
            max-height: 600px;
            opacity: 0;
            visibility: hidden;
            transform: translateY(-10px);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 9999;
            overflow: hidden;
            border: 1px solid #3a3a3a;
        }

        .messages-dropdown.show {
            opacity: 1;
            visibility: visible;
            transform: translateY(0);
        }

        .messages-dropdown-header {
            background: linear-gradient(135deg, #D32F2F 0%, #C2185B 100%);
            padding: 20px;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .messages-dropdown-title {
            color: white;
            font-size: 18px;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .messages-count-badge {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 700;
            backdrop-filter: blur(10px);
        }

        .messages-dropdown-body {
            max-height: 450px;
            overflow-y: auto;
            padding: 8px;
        }

        .messages-dropdown-body::-webkit-scrollbar {
            width: 6px;
        }

        .messages-dropdown-body::-webkit-scrollbar-track {
            background: #1a1a1a;
        }

        .messages-dropdown-body::-webkit-scrollbar-thumb {
            background: #3a3a3a;
            border-radius: 3px;
        }

        .messages-dropdown-body::-webkit-scrollbar-thumb:hover {
            background: #4a4a4a;
        }

        .message-dropdown-item {
            display: flex;
            gap: 12px;
            padding: 12px;
            border-radius: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            margin-bottom: 4px;
            background: rgba(255, 255, 255, 0.02);
            border: 1px solid transparent;
        }

        .message-dropdown-item:hover {
            background: rgba(194, 24, 91, 0.1);
            border-color: rgba(194, 24, 91, 0.3);
            transform: translateX(4px);
        }

        .message-dropdown-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            background: linear-gradient(135deg, #6366f1, #8b5cf6);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 700;
            font-size: 18px;
            flex-shrink: 0;
        }

        .message-dropdown-content {
            flex: 1;
            min-width: 0;
        }

        .message-dropdown-header-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 6px;
        }

        .message-dropdown-subject {
            color: #e9e9e9;
            font-weight: 600;
            font-size: 14px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            flex: 1;
            margin-right: 8px;
        }

        .message-dropdown-time {
            color: #8696a0;
            font-size: 12px;
            white-space: nowrap;
        }

        .message-dropdown-meta {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            flex-wrap: wrap;
        }

        .priority-tag {
            padding: 2px 8px;
            border-radius: 8px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            display: inline-flex;
            align-items: center;
            gap: 4px;
        }

        .priority-tag.priority-urgent {
            background: linear-gradient(135deg, #ef4444, #dc2626);
            color: white;
        }

        .priority-tag.priority-important {
            background: linear-gradient(135deg, #f59e0b, #d97706);
            color: white;
        }

        .priority-tag.priority-normal {
            background: rgba(59, 130, 246, 0.2);
            color: #3b82f6;
        }

        .message-dropdown-recipients {
            color: #8696a0;
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
        }

        .audio-indicator {
            color: #C2185B;
            font-size: 12px;
            display: flex;
            align-items: center;
        }

        .message-dropdown-preview {
            color: #8696a0;
            font-size: 13px;
            margin: 0;
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .empty-messages {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 40px 20px;
            text-align: center;
        }

        .empty-messages i {
            font-size: 60px;
            color: #3a3a3a;
            margin-bottom: 16px;
        }

        .empty-messages p {
            color: #8696a0;
            font-size: 15px;
            margin: 0;
        }

        .messages-dropdown-footer {
            background: rgba(194, 24, 91, 0.1);
            padding: 12px;
            border-top: 1px solid #3a3a3a;
        }

        .view-all-messages {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            color: #C2185B;
            font-weight: 600;
            font-size: 14px;
            text-decoration: none;
            transition: all 0.3s ease;
            padding: 8px;
            border-radius: 8px;
        }

        .view-all-messages:hover {
            background: rgba(194, 24, 91, 0.1);
            color: #D32F2F;
        }

        .view-all-messages i {
            transition: transform 0.3s ease;
        }

        .view-all-messages:hover i {
            transform: translateX(4px);
        }

        @media (max-width: 576px) {
            .messages-dropdown {
                width: calc(100vw - 40px);
                right: 20px;
                left: 20px;
            }
        }

        @keyframes slideInMessage {
            from {
                opacity: 0;
                transform: translateX(-20px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .message-dropdown-item {
            animation: slideInMessage 0.3s ease-out;
        }

        .message-dropdown-item:nth-child(1) { animation-delay: 0.05s; }
        .message-dropdown-item:nth-child(2) { animation-delay: 0.1s; }
        .message-dropdown-item:nth-child(3) { animation-delay: 0.15s; }
        .message-dropdown-item:nth-child(4) { animation-delay: 0.2s; }
        .message-dropdown-item:nth-child(5) { animation-delay: 0.25s; }
    </style>
</head>
<body>

<div class="top-navbar header">
    <div class="header-left">
        <button type="button" class="toggle-btn d-xl-none text-26 text-white" aria-label="Toggle mobile menu" id="mobileSidebarToggle">
            <i class="ph ph-list"></i>
        </button>
        <button type="button" class="menu-dots-toggle d-none d-xl-flex" id="sidebarToggle" aria-label="Toggle sidebar collapse">
            <i class="ph ph-dots-three-outline-vertical"></i>
        </button>
        
        <!-- Support Contact Numbers -->
        <div class="support-contact d-none d-lg-flex">
            <a href="tel:+212658112727" class="support-item">
                <i class="fas fa-phone-alt"></i>
                <span class="support-label">Support:</span>
                <span>+212 658-112727</span>
            </a>
            <a href="tel:+212658113939" class="support-item">
                <i class="fas fa-phone-alt"></i>
                <span>+212 658-113939</span>
            </a>
        </div>
    </div>
   

    <div class="header-center d-none d-lg-flex">
        <div class="logo-text">@yield('title', 'Gestion des Utilisateurs')</div>
        <form class="w-350">
            <div class="position-relative">
                <button type="submit" class="input-icon text-xl flex-center text-gray-100 pointer-event-none">
                    <i class="ph ph-magnifying-glass"></i>
                </button>
                <input type="text" id="searchInput" class="form-control ps-40 h-40 border-transparent focus-border-main-600 bg-main-50 rounded-pill placeholder-15" placeholder="Rechercher...">
            </div>
        </form>
    </div>

    <div class="header-right">
        <div class="nav-icons">
           


       {{-- Messages Dropdown Menu - VERSION CORRIGÉE --}}
<div class="nav-icon" id="messagesDropdownToggle" role="button" aria-haspopup="true" aria-expanded="false" aria-label="Messages">
    <span class="position-relative">
        <i class="fas fa-envelope"></i>
        @php
            // Si l'utilisateur est étudiant, filtrer par ses inscriptions
            if (auth()->user()->hasRole('Etudiant')) {
                // Récupérer les IDs des formations où l'étudiant est inscrit (actif ou terminé)
                $userFormationIds = \App\Models\Inscription::where('user_id', auth()->id())
                    ->whereIn('status', ['active', 'completed', 'pending'])
                    ->pluck('formation_id')
                    ->toArray();
                
                // Compter les messages d'aujourd'hui pour ces formations uniquement
                $todayMessages = \App\Models\FormationMessage::whereDate('created_at', today())
                    ->whereHas('formations', function($query) use ($userFormationIds) {
                        $query->whereIn('formations.id', $userFormationIds);
                    })
                    ->count();
            } else {
                // Pour admin/formateur, afficher tous les messages
                $todayMessages = \App\Models\FormationMessage::whereDate('created_at', today())->count();
            }
        @endphp
        @if($todayMessages > 0)
            <span class="notification-badge">{{ $todayMessages }}</span>
        @endif
    </span>
    <div class="tooltip">Messages ({{ $todayMessages }})</div>
</div>

{{-- Messages Dropdown Menu --}}
<div class="dropdown-menu messages-dropdown" id="messagesDropdownMenu">
    <div class="messages-dropdown-header">
        <div class="flex-between w-100">
            <h5 class="messages-dropdown-title">
                <i class="fas fa-envelope-open-text"></i>
                Messages d'aujourd'hui
            </h5>
            <span class="messages-count-badge">{{ $todayMessages }}</span>
        </div>
    </div>

    <div class="messages-dropdown-body">
        @php
            // Construction de la requête selon le rôle
            $messagesQuery = \App\Models\FormationMessage::with(['sender', 'formations'])
                ->whereDate('created_at', today());
            
            // Si étudiant, filtrer par ses formations
            if (auth()->user()->hasRole('Etudiant')) {
                $userFormationIds = \App\Models\Inscription::where('user_id', auth()->id())
                    ->whereIn('status', ['active', 'completed', 'pending'])
                    ->pluck('formation_id')
                    ->toArray();
                
                $messagesQuery->whereHas('formations', function($query) use ($userFormationIds) {
                    $query->whereIn('formations.id', $userFormationIds);
                });
            }
            
            $messages = $messagesQuery
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
        @endphp

        @forelse($messages as $message)
            <a href="{{ auth()->user()->hasRole('Etudiant') ? route('message.showa', $message->id) : route('messages.show', $message->id) }}" 
               class="message-dropdown-item">
                <div class="message-dropdown-avatar">
                    @php
                        $senderName = $message->sender->name ?? 'Admin';
                        $initial = strtoupper(substr($senderName, 0, 1));
                    @endphp
                    {{ $initial }}
                </div>
                <div class="message-dropdown-content">
                    <div class="message-dropdown-header-row">
                        <span class="message-dropdown-subject">{{ $message->subject }}</span>
                        <span class="message-dropdown-time">{{ $message->created_at->format('H:i') }}</span>
                    </div>
                    <div class="message-dropdown-meta">
                        <span class="priority-tag priority-{{ $message->priority }}">
                            @if($message->priority === 'urgent')
                                <i class="fas fa-bolt"></i>
                            @elseif($message->priority === 'important')
                                <i class="fas fa-star"></i>
                            @endif
                            {{ ucfirst($message->priority) }}
                        </span>
                        
                        @if(auth()->user()->hasRole('Etudiant'))
                            {{-- Pour l'étudiant, afficher ses formations concernées --}}
                            @php
                                $studentFormations = $message->formations
                                    ->whereIn('id', $userFormationIds ?? [])
                                    ->pluck('title')
                                    ->take(2);
                            @endphp
                            @if($studentFormations->isNotEmpty())
                                <span class="message-dropdown-recipients">
                                    <i class="fas fa-graduation-cap"></i>
                                    {{ $studentFormations->join(', ') }}
                                </span>
                            @endif
                        @else
                            {{-- Pour admin, afficher le nombre de destinataires --}}
                            <span class="message-dropdown-recipients">
                                <i class="fas fa-users"></i>
                                {{ $message->recipients_count }} destinataires
                            </span>
                        @endif
                        
                        @if($message->audio_path)
                            <span class="audio-indicator">
                                <i class="fas fa-microphone"></i>
                            </span>
                        @endif
                    </div>
                    @if($message->message)
                        <p class="message-dropdown-preview">{{ Str::limit($message->message, 60) }}</p>
                    @endif
                </div>
            </a>
        @empty
            <div class="empty-messages">
                <i class="fas fa-inbox"></i>
                <p>Aucun message aujourd'hui</p>
            </div>
        @endforelse
    </div>

    @if($todayMessages > 0)
        <div class="messages-dropdown-footer">
            <a href="{{ auth()->user()->hasRole('Etudiant') ? route('message.index') : route('messages.index') }}" 
               class="view-all-messages">
                Voir tous les messages
                <i class="fas fa-arrow-right"></i>
            </a>
        </div>
    @endif
</div>
           
            <div class="nav-icon theme-toggle" onclick="handleIconClick('theme')" role="button" aria-label="Mode sombre">
                <i class="fas fa-moon"></i>
                <div class="tooltip">Mode sombre</div>
            </div>

            <div class="nav-icon" onclick="handleIconClick('fullscreen')" role="button" aria-label="Plein écran">
                <i class="fas fa-expand"></i>
                <div class="tooltip">Plein écran</div>
            </div>

            {{-- Profile Dropdown Toggle --}}
            <div class="user-avatar" id="profileDropdownToggle" role="button" aria-haspopup="true" aria-expanded="false" aria-label="Profil utilisateur">
                @auth
                    @php
                        $userName = Auth::user()->name;
                        $userAvatar = Auth::user()->avatar;
                        $nameParts = explode(' ', $userName);
                        $initials = (count($nameParts) > 1) ? strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1)) : strtoupper(substr($userName, 0, 2));
                    @endphp
                    @if ($userAvatar)
                        <img src="{{ asset('storage/' . $userAvatar) }}" alt="Avatar utilisateur">
                    @else
                        <span>{{ $initials }}</span>
                    @endif
                @else
                    <span>GU</span>
                @endauth
            </div>

            {{-- Profile Dropdown Menu --}}
            <div class="dropdown-menu profile-custom-dropdown" id="profileDropdownMenu">
                <div class="dropdown-header">
                    <div class="dropdown-avatar">
                        @auth
                            @if ($userAvatar)
                                <img src="{{ asset('storage/' . $userAvatar) }}" alt="User Avatar" class="dropdown-avatar-img">
                            @else
                                <span>{{ $initials }}</span>
                            @endif
                        @else
                            <span>GU</span>
                        @endauth
                    </div>
                    <div class="user-info">
                        <span class="user-name">@auth {{ Auth::user()->name }} @else Guest User @endauth</span>
                        <span class="user-role">@auth {{ Auth::user()->email }} @else guest@example.com @endauth</span>
                    </div>
                </div>
                <a href="{{ route('profile.edit') }}" class="dropdown-item">
                    <i class="fas fa-user"></i> Profile
                </a>
                <a href="{{ route('logout') }}" class="dropdown-item logout" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                    <i class="ph ph-sign-out"></i> Déconnexion
                </a>
                <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                    @csrf
                </form>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript pour gérer le dropdown des messages
document.addEventListener('DOMContentLoaded', function() {
    const messagesToggle = document.getElementById('messagesDropdownToggle');
    const messagesDropdown = document.getElementById('messagesDropdownMenu');
    const profileToggle = document.getElementById('profileDropdownToggle');
    const profileDropdown = document.getElementById('profileDropdownMenu');

    // Toggle Messages Dropdown
    if (messagesToggle && messagesDropdown) {
        messagesToggle.addEventListener('click', function(e) {
            e.stopPropagation();
            messagesDropdown.classList.toggle('show');
            
            // Fermer le dropdown profile si ouvert
            if (profileDropdown) {
                profileDropdown.classList.remove('show');
            }
        });
    }

    // Fermer les dropdowns en cliquant ailleurs
    document.addEventListener('click', function(e) {
        if (messagesDropdown && !messagesDropdown.contains(e.target) && !messagesToggle.contains(e.target)) {
            messagesDropdown.classList.remove('show');
        }
        if (profileDropdown && !profileDropdown.contains(e.target) && !profileToggle.contains(e.target)) {
            profileDropdown.classList.remove('show');
        }
    });

    // Empêcher la fermeture en cliquant à l'intérieur du dropdown
    if (messagesDropdown) {
        messagesDropdown.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
</script>

</body>
</html>