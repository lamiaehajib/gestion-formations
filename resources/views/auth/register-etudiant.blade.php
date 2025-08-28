@extends('layouts.guest')

@section('title', __('Register as Student'))
@section('auth_image')
    <img src="{{ asset('edmate/assets/images/thumbs/Untitled-15.jpg') }}" width="100%" height="100%" alt="Auth Image">
@endsection
@section('content')

    {{-- عرض رسالة النجاح في حالة وجودها --}}
    @if (session('success'))
        <div class="alert alert-success text-center mb-8" role="alert">
            {{ session('success') }}
        </div>
    @endif

    <h2 class="mb-8 text-3xl font-bold text-gray-900">{{ __("Créez votre compte étudiant") }}</h2>
    <p class="text-gray-600 text-base mb-8">{{ __("Pour commencer votre parcours de formation.") }}</p>

    <form method="POST" action="{{ route('register.etudiant') }}" enctype="multipart/form-data">
        @csrf

        {{-- Pair 1: Nom Complet & Adresse E-mail --}}
        <div class="d-flex gap-3 mb-4">
            <div class="flex-fill">
                <label for="name" class="form-label mb-8 h6">{{ __('Nom Complet') }}</label>
                <div class="position-relative">
                    <input type="text" class="form-control py-11 ps-40 @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="{{ __('Entrez votre nom complet') }}">
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-user"></i></span>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="flex-fill">
                <label for="email" class="form-label mb-8 h6">{{ __('Adresse E-mail') }}</label>
                <div class="position-relative">
                    <input type="email" class="form-control py-11 ps-40 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="{{ __('Entrez votre e-mail') }}">
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-envelope"></i></span>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Pair 2: Numéro CIN & Numéro de Téléphone --}}
        <div class="d-flex gap-3 mb-4">
            <div class="flex-fill">
                <label for="cin" class="form-label mb-8 h6">{{ __('Numéro CIN') }}</label>
                <div class="position-relative">
                    <input type="text" class="form-control py-11 ps-40 @error('cin') is-invalid @enderror" id="cin" name="cin" value="{{ old('cin') }}" required placeholder="{{ __('Entrez votre numéro CIN') }}">
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-identification-badge"></i></span>
                    @error('cin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="flex-fill">
                <label for="phone" class="form-label mb-8 h6">{{ __('Numéro de Téléphone') }}</label>
                <div class="position-relative">
                    <input type="text" class="form-control py-11 ps-40 @error('phone') is-invalid @enderror" id="phone" name="phone" value="{{ old('phone') }}" required placeholder="{{ __('Entrez votre numéro de téléphone') }}">
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-phone"></i></span>
                    @error('phone')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Single field: Photo de Profil --}}
        <div class="mt-4 mb-24">
            <label for="avatar" class="form-label mb-8 h6">{{ __('Photo de Profil (Optionnel)') }}</label>
            <div class="position-relative">
                <input id="avatar" class="form-control py-11 @error('avatar') is-invalid @enderror" type="file" name="avatar" />
                @error('avatar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Pair 3: Mot de Passe & Confirmer le Mot de Passe --}}
        <div class="d-flex gap-3 mb-4">
            <div class="flex-fill">
                <label for="password" class="form-label mb-8 h6">{{ __('Mot de Passe') }}</label>
                <div class="position-relative">
                    <input type="password" class="form-control py-11 ps-40 @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password" placeholder="{{ __('Entrez votre mot de passe') }}">
                    <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash" id="toggle-password-register"></span>
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-lock"></i></span>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
            
            <div class="flex-fill">
                <label for="password_confirmation" class="form-label mb-8 h6">{{ __('Confirmer le Mot de Passe') }}</label>
                <div class="position-relative">
                    <input type="password" class="form-control py-11 ps-40 @error('password_confirmation') is-invalid @enderror" id="password_confirmation" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirmez votre mot de passe') }}">
                    <span class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash" id="toggle-password-confirm"></span>
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-lock-key"></i></span>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-32 flex-between flex-wrap gap-8">
            <a href="{{ route('login') }}" class="text-main-600 hover-text-decoration-underline text-15 fw-medium">
                {{ __('Déjà enregistré ? Connectez-vous') }}
            </a>
        </div>

        <button type="submit" class="btn btn-red rounded-pill w-100">{{ __('S\'inscrire') }}</button>
    </form>

    <style>
        /* للتأكد من أن الحقول تبدو جيدة على الشاشات الصغيرة */
        @media (max-width: 768px) {
            .d-flex {
                flex-direction: column !important;
            }
            .gap-3 {
                gap: 0 !important;
            }
            .flex-fill {
                margin-bottom: 1rem;
            }
        }
    </style>

    <script>
        $(document).ready(function() {
            // Fonction لتبديل رؤية كلمة المرور
            function togglePasswordVisibility(inputId, toggleId) {
                const input = $('#' + inputId);
                const toggle = $('#' + toggleId);
                toggle.click(function() {
                    if (input.attr('type') === 'password') {
                        input.attr('type', 'text');
                        $(this).removeClass('ph-eye-slash').addClass('ph-eye');
                    } else {
                        input.attr('type', 'password');
                        $(this).removeClass('ph-eye').addClass('ph-eye-slash');
                    }
                });
            }
            togglePasswordVisibility('password', 'toggle-password-register');
            togglePasswordVisibility('password_confirmation', 'toggle-password-confirm');
        });
    </script>
@endsection