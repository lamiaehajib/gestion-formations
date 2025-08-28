@extends('layouts.guest')

@section('title', __('Mot de passe oublié ?') . ' - UITS')

@section('auth_image')
    <img src="{{ asset('edmate/assets/images/thumbs/Untitledf.jpg') }}" class="h-full w-full rounded-lg object-cover" alt="Auth Image">
@endsection

@section('content')
    <h2 class="mb-6 text-center text-3xl font-bold text-gray-800">
        {{ __('Mot de passe oublié ?') }}
    </h2>

    <div class="mb-4 text-sm text-gray-600">
        {{ __('Vous avez oublié votre mot de passe ? Pas de problème. Indiquez-nous simplement votre adresse e-mail et nous vous enverrons un lien de réinitialisation de mot de passe qui vous permettra d’en choisir un nouveau.') }}
    </div>

    @if (session('status'))
        <div class="alert alert-success mb-24" role="alert">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('password.email') }}">
        @csrf

        <div class="mb-24">
            <label for="email" class="form-label mb-8 h6">{{ __('Email') }}</label>
            <div class="position-relative">
                <input type="email" class="form-control py-11 ps-40" id="email" name="email" value="{{ old('email') }}" required autofocus />
                <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i class="ph ph-user"></i></span>
                @error('email')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <button type="submit" class="btn btn-red rounded-pill w-100">{{ __('Envoyer le lien de réinitialisation') }}</button>
    </form>
@endsection