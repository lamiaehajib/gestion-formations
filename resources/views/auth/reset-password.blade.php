@extends('layouts.guest')

{{-- Ajout des styles pour correspondre à la page de connexion --}}
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
    .form-control {
        padding: 0.6875rem 1.25rem 0.6875rem 2.5rem; /* Ajusté pour correspondre à l'icône */
    }
    .position-relative {
        position: relative;
    }
    .position-absolute {
        position: absolute;
    }
    .top-50 {
        top: 50%;
    }
    .translate-middle-y {
        transform: translateY(-50%);
    }
    .ms-16 {
        margin-left: 1rem;
    }
    .btn-red {
        background-color: #D32F2F;
        border-color: #D32F2F;
        color: white;
        transition: all 0.3s ease;
    }
    .btn-red:hover {
        background-color: #C2185B;
        border-color: #C2185B;
    }
    .rounded-pill {
        border-radius: 50rem;
    }
    .w-100 {
        width: 100%;
    }
</style>

@section('title', __('Réinitialiser le mot de passe') . ' - Portail Étudiant UITS')

@section('auth_image')
    <img src="{{ asset('edmate/assets/images/thumbs/Untitledf.jpg') }}" class="h-full w-full rounded-lg object-cover" alt="Auth Image">
@endsection

@section('content')
    {{-- Titre stylisé --}}
    <h2 class="welcome-title text-center">{{ __('Réinitialiser le mot de passe') }}</h2>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf

        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        {{-- Champ Email --}}
        <div class="mb-24">
            <label for="email" class="form-label mb-8 h6">{{ __('Adresse e-mail') }}</label>
            <div class="position-relative">
                <input type="email" class="form-control py-11 ps-40 @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username" placeholder="{{ __('Entrez votre e-mail') }}">
                {{-- Icône pour l'email --}}
                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-user"></i></span>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Champ Nouveau mot de passe --}}
        <div class="mb-24">
            <label for="password" class="form-label mb-8 h6">{{ __('Nouveau mot de passe') }}</label>
            <div class="position-relative">
                <input type="password" class="form-control py-11 ps-40 @error('password') is-invalid @enderror" id="password" name="password" required autocomplete="new-password" placeholder="{{ __('Entrez votre nouveau mot de passe') }}">
                {{-- Icône pour le mot de passe --}}
                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-lock"></i></span>
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        {{-- Champ Confirmation du mot de passe --}}
        <div class="mb-24">
            <label for="password_confirmation" class="form-label mb-8 h6">{{ __('Confirmer le mot de passe') }}</label>
            <div class="position-relative">
                <input id="password_confirmation" type="password" class="form-control py-11 ps-40 @error('password_confirmation') is-invalid @enderror" name="password_confirmation" required autocomplete="new-password" placeholder="{{ __('Confirmez votre nouveau mot de passe') }}">
                {{-- Icône pour la confirmation --}}
                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-lock-key"></i></span>
                @error('password_confirmation')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="flex items-center justify-end mt-4">
            <button type="submit" class="btn btn-red rounded-pill w-100">
                {{ __('Réinitialiser le mot de passe') }}
            </button>
        </div>
    </form>
@endsection