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
                    <input type="text"
                        class="form-control py-11 ps-40 @error('name') is-invalid @enderror" id="name" name="name"
                        value="{{ old('name') }}" required autofocus autocomplete="name"
                        placeholder="{{ __('Entrez votre nom complet') }}">
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i
                            class="ph ph-user"></i></span>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="flex-fill">
                <label for="email" class="form-label mb-8 h6">{{ __('Adresse E-mail') }}</label>
                <div class="position-relative">
                    <input type="email"
                        class="form-control py-11 ps-40 @error('email') is-invalid @enderror" id="email" name="email"
                        value="{{ old('email') }}" required autocomplete="username"
                        placeholder="{{ __('Entrez votre e-mail') }}">
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i
                            class="ph ph-envelope"></i></span>
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
                    <input type="text"
                        class="form-control py-11 ps-40 @error('cin') is-invalid @enderror" id="cin" name="cin"
                        value="{{ old('cin') }}" required placeholder="{{ __('Entrez votre numéro CIN') }}">
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i
                            class="ph ph-identification-badge"></i></span>
                    @error('cin')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="flex-fill">
                <label for="phone" class="form-label mb-8 h6">{{ __('Numéro de Téléphone') }}</label>
                <div class="position-relative">
                    <input type="text"
                        class="form-control py-11 ps-40 @error('phone') is-invalid @enderror" id="phone" name="phone"
                        value="{{ old('phone') }}" required placeholder="{{ __('Entrez votre numéro de téléphone') }}">
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i
                            class="ph ph-phone"></i></span>
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
                <input id="avatar" class="form-control py-11 @error('avatar') is-invalid @enderror"
                    type="file" name="avatar" />
                @error('avatar')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
        </div>

        <div class="mb-4 p-4 border border-gray-200 rounded-xl shadow-sm">
            <h3 class="text-xl font-bold text-gray-900 mb-4 flex items-center">
                <i class="ph ph-cloud-arrow-up text-red-600 me-2 text-2xl"></i>
                {{ __('Documents (Facultatif)') }}
            </h3>

            <div id="documents-container" class="space-y-3">
                {{-- Le premier champ de document (Modèle de base) --}}
                <div
                    class="document-item bg-white p-4 rounded-xl border border-gray-100 transition-all duration-300">
                    <div class="d-flex flex-column flex-md-row gap-3 align-items-center">
                        <div class="flex-fill w-100">
                            <label class="form-label mb-1 h6 text-sm text-gray-600">{{ __('Nom du document') }}</label>
                            <input type="text" name="documents[0][name]"                        
                                class="form-control py-8 px-3"                                    
                                placeholder="{{ __('Ex: Baccalauréat') }}">
                        </div>
                        <div class="flex-fill w-100">
                            <label class="form-label mb-1 h6 text-sm text-gray-600">{{ __('Fichier') }}</label>
                            <div class="d-flex align-items-center">
                                <input type="file" name="documents[0][file]"                
                                    class="form-control py-11">
                                <button type="button" onclick="removeDocument(this)"
                                    class="btn btn-sm btn-danger ms-2 "
                                    style="width: 40px; height: 40px; min-width: 40px; display: flex; align-items: center; justify-content: center; padding: 0;"
                                    title="Supprimer ce document">
                                    <i class="ph ph-x text-white text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" onclick="addDocument()"
                class="btn btn-red rounded-pill mt-4 d-flex align-items-center justify-content-center">
                <i class="ph ph-plus me-1"></i> {{ __('Ajouter un autre document') }}
            </button>
        </div>
        {{-- Pair 3: Mot de Passe & Confirmer le Mot de Passe --}}
        <div class="d-flex gap-3 mb-4">
            <div class="flex-fill">
                <label for="password" class="form-label mb-8 h6">{{ __('Mot de Passe') }}</label>
                <div class="position-relative">
                    <input type="password"
                        class="form-control py-11 ps-40 @error('password') is-invalid @enderror" id="password"
                        name="password" required autocomplete="new-password"
                        placeholder="{{ __('Entrez votre mot de passe') }}">
                    <span
                        class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash"
                        id="toggle-password-register"></span>
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i
                            class="ph ph-lock"></i></span>
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            <div class="flex-fill">
                <label for="password_confirmation"
                    class="form-label mb-8 h6">{{ __('Confirmer le Mot de Passe') }}</label>
                <div class="position-relative">
                    <input type="password"
                        class="form-control py-11 ps-40 @error('password_confirmation') is-invalid @enderror"
                        id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                        placeholder="{{ __('Confirmez votre mot de passe') }}">
                    <span
                        class="toggle-password position-absolute top-50 inset-inline-end-0 me-16 translate-middle-y ph ph-eye-slash"
                        id="toggle-password-confirm"></span>
                    <span class="position-absolute top-50 translate-middle-y ms-16 text-gray-600 d-flex"><i
                            class="ph ph-lock-key"></i></span>
                    @error('password_confirmation')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>
        </div>

        <div class="mb-32 flex-between flex-wrap gap-8">
            <a href="{{ route('login') }}"
                class="text-main-600 hover-text-decoration-underline text-15 fw-medium">
                {{ __('Déjà enregistré ? Connectez-vous') }}
            </a>
        </div>

        <button type="submit" class="btn btn-red rounded-pill w-100">{{ __('S\'inscrire') }}</button>
    </form>

    <style>
        /* CSS existing for mobile */
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

            .document-item .d-flex {
                flex-direction: column !important;
            }
        }

        /* 🎨 CSS Ajouté pour le style du bouton 'Choisir un Fichier' bhal tsawira 2 */
        .is-file-upload {
            position: relative;
            z-index: 1;
            opacity: 0;
            height: 100%;
            cursor: pointer;
        }

        /* Had l-pseudo-element kaytcréé le bouton li kayban */
        .is-file-upload:before {
            content: 'Choisir un fichier';
            position: absolute;
            top: 0;
            left: 0;
            z-index: 2;
            background-color: #e9ecef;
            color: #495057;
            border: 1px solid #ced4da;
            border-radius: 0.75rem;
            padding: 0.75rem 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 130px;
            height: 100%;
        }

        /* Had l-pseudo-element kaytcréé le texte "Aucun fichier choisi" */
        .is-file-upload:after {
            content: attr(data-filename) 'Aucun fichier choisi';
            position: absolute;
            top: 0;
            left: 130px;
            right: 0;
            bottom: 0;
            z-index: 1;
            padding: 0.75rem 1rem;
            border: 1px solid #ced4da;
            border-left: none;
            border-radius: 0 0.75rem 0.75rem 0;
            background-color: #fff;
            color: #6c757d;
            display: flex;
            align-items: center;
            overflow: hidden;
            white-space: nowrap;
            text-overflow: ellipsis;
        }
    </style>


    <script>
        // ⭐️ Had l-fonction dyal jQuery 3ala9tha m3a l-bouton dyal "Ajouter un autre document" 
        // L-code dyal Phosphor Icons (`ph ph-x`) machi howa l-Font Awesome (`fa-solid fa-xmark`)

        // 📝 Script pour les documents multiples
        let documentIndex = 1;

        // Fonction dyal la mise à jour dyal ism l-fichier (m7taja f l-HTML w l-JS)
        function updateFileName(input) {
            const fileName = input.files[0] ? input.files[0].name : '';
            input.setAttribute('data-filename', fileName + ' ');
        }
        
        function addDocument() {
            const container = document.getElementById('documents-container');
            const newItem = document.createElement('div');
            
            // Had l-classes darori bach yban hadak l'style zwin
            newItem.classList.add('document-item', 'bg-white', 'p-4', 'rounded-xl', 'border', 'border-gray-100', 'mt-3', 'transition-all', 'duration-300');

            // L'HTML khasso ykoun s7i7 bach l-bouton ykoun kheddam
            newItem.innerHTML = `
                <div class="d-flex flex-column flex-md-row gap-3 align-items-center">
                    <div class="flex-fill w-100">
                        <label class="form-label mb-1 h6 text-sm text-gray-600">{{ __('Nom du document') }}</label>
                        <input type="text" name="documents[${documentIndex}][name]" 
                                class="form-control py-8 px-3"
                                placeholder="{{ __('Ex: autre document') }}">
                    </div>
                    <div class="flex-fill w-100">
                        <label class="form-label mb-1 h6 text-sm text-gray-600">{{ __('Fichier') }}</label>
                        <div class="d-flex align-items-center">
                            <input type="file" name="documents[${documentIndex}][file]"
                                    class="form-control py-11">
                            <button type="button" onclick="removeDocument(this)" class="btn btn-sm btn-danger ms-2 text-white" style="width: 40px; height: 40px; min-width: 40px; display: flex; align-items: center; justify-content: center; padding: 0;" title="Supprimer ce document">
                                <i class="ph ph-x text-white text-lg"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            
            // ⭐️ Had l-ligne hiya l'mohim: katzid l-element f'la page
            container.appendChild(newItem);
            
            // ⭐️ Katcréé l'event listener dyal changement dyal l'fichier melli kaytzad l'élément jdid
            newItem.querySelector('.is-file-upload').addEventListener('change', function() {
                updateFileName(this);
            });
            
            // Mzn bach yzid f l'index
            documentIndex++;
        }

        function removeDocument(button) {
            const item = button.closest('.document-item');
            if (item) {
                // Animation de suppression (facultatif)
                item.style.opacity = 0;
                item.style.height = 0;
                item.style.padding = 0;
                item.style.margin = 0;
                setTimeout(() => {
                    item.remove();
                }, 300);
            }
        }
        
        $(document).ready(function () {
            // Fonction لتبديل رؤية كلمة المرور
            function togglePasswordVisibility(inputId, toggleId) {
                const input = $('#' + inputId);
                const toggle = $('#' + toggleId);
                toggle.click(function () {
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
            
            // Tkhdem updateFileName 3la les inputs li kano deja f l-HTML
            document.querySelectorAll('.is-file-upload').forEach(input => {
                input.addEventListener('change', function() {
                    updateFileName(this);
                });
            });
        });
    </script>
@endsection