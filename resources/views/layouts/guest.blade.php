<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', __('Log in') . 'PORTAIL ETUDIANT UITS')</title>
    <link rel="shortcut icon" href="{{ asset('edmate/assets/images/logo/favicon.png') }}">
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/phosphor-icon.css') }}">
    <link rel="stylesheet" href="{{ asset('edmate/assets/css/main.css') }}">
    <link rel="shortcut icon" href="{{ asset('edmate/assets/images/logo/favicon.png') }}">
</head>
<style>
    a.inline-flex.items-center.justify-center.px-6.py-3.bg-indigo-600.border.border-transparent.rounded-lg.font-bold.text-lg.text-white.uppercase.tracking-wider.shadow-lg.hover\:bg-indigo-700.hover\:scale-105.focus\:outline-none.focus\:ring-4.focus\:ring-indigo-300.focus\:ring-offset-2.transition.ease-in-out.duration-300.transform {
    color: #c50808 !important;
}
.bg-main-50 {
    /* background-color: var(--main-50) !important; */
    background-color: white !important;
}

body{
    
}
</style>
<body>
    <div class="preloader">
        <div class="loader"></div>
    </div>
    <div class="side-overlay"></div>

    <section class="auth d-flex">
        <div class="auth-left bg-main-50 flex-center p-24">
            @yield('auth_image')
        </div>
        <div class="auth-right py-40 px-24 flex-center flex-column">
            <div class="auth-right__inner mx-auto w-100">
                <a href="{{ url('/') }}" class="auth-right__logo">
                    <img src="{{ asset('edmate/assets/images/thumbs/logou.png') }}" width="40%" alt="Logo">
                </a>
                @yield('content')
            </div>
        </div>
    </section>

    <script src="{{ asset('edmate/assets/js/jquery-3.7.1.min.js') }}"></script>
    <script src="{{ asset('edmate/assets/js/bootstrap.bundle.min.js') }}"></script>
    <script src="{{ asset('edmate/assets/js/phosphor-icon.js') }}"></script>
    <script src="{{ asset('edmate/assets/js/main.js') }}"></script>
</body>
</html>