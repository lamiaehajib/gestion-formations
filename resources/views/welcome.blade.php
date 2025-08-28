@extends('layouts.guest')

<style>
     .welcome-title {
        background: linear-gradient(135deg, #D32F2F 0%, #D32F2F 50%, #D32F2F 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
        font-size: 2.5rem;
        font-weight: 800;
        text-align: center;
        margin-bottom: 1rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .welcome-subtitle {
        color: #6b7280;
        text-align: center;
        font-size: 1.1rem;
        margin-bottom: 2rem;
        font-weight: 500;
    }

    .register-btn {
        background: linear-gradient(135deg, #D32F2F 0%, #D32F2F 50%, #C2185B 100%);
        border: none;
        border-radius: 15px;
        padding: 1rem 2rem;
        color: white;
        font-weight: 700;
        font-size: 1.1rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        box-shadow: 0 10px 30px rgba(220, 38, 127, 0.3);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        margin-bottom: 2rem;
    }

    .register-btn:hover {
        transform: translateY(-3px) scale(1.02);
        box-shadow: 0 15px 40px rgba(220, 38, 127, 0.4);
        color: white;
        text-decoration: none;
    }

    .register-btn:before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        transition: left 0.5s;
    }

    .register-btn:hover:before {
        left: 100%;
    }

    </style>
@section('title', __('Se connecter') . ' UITS')

@section('auth_image')
    <img src="{{ asset('edmate/assets/images/thumbs/log.jpg') }}" width="100%" height="100%" alt="Auth Image">
@endsection

@section('content')
   <h2 class="welcome-title">{{ __('Bienvenue ! 👋') }}</h2>
    <p class="welcome-subtitle">{{ __('Veuillez vous connecter à votre compte et commencer l`aventure') }}</p>


    @if (session('status'))
        <div class="alert alert-success mb-24" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <div class="text-center">
        <a href="{{ route('register.etudiant') }}" class="register-btn">
            <i class="ph ph-user-plus mr-2"></i>
            {{ __("S'inscrire en tant qu'étudiant") }}
        </a>
    </div>


    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="mb-24">
            <label for="email" class="form-label mb-8 h6">{{ __('Adresse e-mail') }}</label>
            <div class="position-relative">
                <input type="email" class="form-control py-11 ps-40 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" placeholder="{{ __('Entrez votre e-mail') }}" required autofocus autocomplete="username">
                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-user"></i></span>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-24">
            <label for="password" class="form-label mb-8 h6">{{ __('Mot de passe') }}</label>
            <div class="position-relative">
                <input type="password" class="form-control py-11 ps-40 @error('password') is-invalid @enderror" id="password" name="password" placeholder="{{ __('Entrez votre mot de passe') }}" required autocomplete="current-password">
                <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash" id="#password"></span>
                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-lock"></i></span>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-32 flex-between flex-wrap gap-8">
            <div class="form-check mb-0 flex-shrink-0">
                <input class="form-check-input flex-shrink-0 rounded-4" type="checkbox" id="remember_me" name="remember">
                <label class="form-check-label text-15 flex-grow-1" for="remember_me">{{ __('Se souvenir de moi') }}</label>
            </div>
            @if (Route::has('password.request'))
                <a href="{{ route('password.request') }}" class="text-main-600 hover-text-decoration-underline text-15 fw-medium">{{ __('Mot de passe oublié ?') }}</a>
            @endif
        </div>

        <button type="submit" class="btn btn-red rounded-pill w-100">{{ __('Se connecter') }}</button>
    </form>

    <script>
        $(document).ready(function() {
            $('.toggle-password').click(function() {
                const input = $('#password');
                if (input.attr('type') === 'password') {
                    input.attr('type', 'text');
                    $(this).removeClass('ph-eye-slash').addClass('ph-eye');
                } else {
                    input.attr('type', 'password');
                    $(this).removeClass('ph-eye').addClass('ph-eye-slash');
                }
            });
        });
    </script>
@endsection