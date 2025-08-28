@extends('layouts.app')

@section('content')
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
        
    

        /* Hide default file input */
        input[type="file"] {
            display: none;
        }

        /* Custom gradient backgrounds */
        .gradient-bg {
            background: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
        }

        .gradient-border {
            background: linear-gradient(135deg, #D32F2F, #C2185B, #ef4444);
            padding: 2px;
            border-radius: 16px;
        }

        .gradient-text {
            background: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        /* Custom button styles */
        .btn-gradient {
            background: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
            position: relative;
            overflow: hidden;
            transform: translateY(0);
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .btn-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }

        .btn-gradient:hover::before {
            left: 100%;
        }

        .btn-gradient:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(211, 47, 47, 0.3);
        }

        /* Input focus styles */
        .form-input:focus {
            border-color: #D32F2F;
            box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.1);
            transform: translateY(-1px);
        }

        /* Avatar styles */
        .avatar-container {
            position: relative;
            display: inline-block;
        }

        .avatar-container::before {
            content: '';
            position: absolute;
            inset: -4px;
            border-radius: 50%;
            background: linear-gradient(135deg, #D32F2F, #C2185B, #ef4444);
            z-index: -1;
            animation: rotate 3s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .avatar-button {
            background: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
            transition: all 0.3s ease;
        }

        .avatar-button:hover {
            transform: scale(1.1);
            box-shadow: 0 8px 25px rgba(211, 47, 47, 0.4);
        }

        /* Card styles */
        .main-card {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        /* Form section headers */
        .section-header {
            position: relative;
            text-align: center;
            margin-bottom: 2rem;
        }

        .section-header::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            width: 60px;
            height: 3px;
            background: linear-gradient(135deg, #D32F2F, #C2185B, #ef4444);
            border-radius: 2px;
        }

        /* Floating labels effect */
        .floating-label {
            position: relative;
        }

        .floating-label input,
        .floating-label textarea {
            border: 2px solid #e5e7eb;
            border-radius: 12px;
            padding: 16px 16px 16px 16px;
            background: #ffffff;
            transition: all 0.3s ease;
        }

        .floating-label label {
            position: absolute;
            top: 16px;
            left: 16px;
            color: #6b7280;
            transition: all 0.3s ease;
            pointer-events: none;
            font-weight: 500;
        }

        .floating-label input:focus + label,
        .floating-label input:not(:placeholder-shown) + label,
        .floating-label textarea:focus + label,
        .floating-label textarea:not(:placeholder-shown) + label {
            top: -8px;
            left: 12px;
            font-size: 12px;
            color: #D32F2F;
            background: white;
            padding: 0 4px;
        }

        /* Icon styles */
        .icon-camera {
            width: 20px;
            height: 20px;
            filter: drop-shadow(0 2px 4px rgba(0,0,0,0.1));
        }

        /* Separator line */
        .separator {
            height: 2px;
            background: linear-gradient(90deg, transparent, #D32F2F, #C2185B, #ef4444, transparent);
            margin: 3rem 0;
            border-radius: 1px;
        }

        /* Success/Error message styles */
        .error-message {
            color: #ef4444;
            font-size: 12px;
            margin-top: 4px;
            font-weight: 500;
        }

        /* Responsive improvements */
        @media (max-width: 768px) {
            .main-card {
                margin: 1rem;
                padding: 1.5rem;
            }
            
            .grid-cols-2-responsive {
                grid-template-columns: 1fr;
            }
        }

        /* Animations */
        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fadeInUp {
            animation: fadeInUp 0.6s ease-out;
        }

        /* Custom scrollbar */
        ::-webkit-scrollbar {
            width: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #D32F2F, #C2185B, #ef4444);
            border-radius: 4px;
        }
    </style>

    <div class="min-h-screen flex items-center justify-center py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl w-full main-card p-8 rounded-2xl shadow-2xl space-y-8 animate-fadeInUp">
            
            <!-- Profile Header -->
            <div class="section-header">
                <h2 class="text-4xl font-bold gradient-text mb-2">
                    Modifier le Profil
                </h2>
                <p class="text-gray-600 font-medium">
                    Gérer les informations de votre profil et votre avatar
                </p>
            </div>

            <!-- Profile Information Form -->
            <form class="space-y-8" action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('patch')

                <!-- Avatar Section -->
                <div class="flex flex-col items-center space-y-6">
                    <div class="avatar-container">
                        <img id="avatar-preview" 
                             class="w-32 h-32 rounded-full object-cover shadow-2xl transition-all duration-300"
                             src="{{ $user->avatar ? Storage::url($user->avatar) : 'https://placehold.co/128x128/D32F2F/ffffff?text=Avatar' }}"
                             alt="User Avatar">
                        <label for="avatar-upload" 
                               class="absolute bottom-0 right-0 avatar-button text-white p-3 rounded-full cursor-pointer shadow-lg">
                            <svg class="icon-camera" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <input id="avatar-upload" type="file" name="avatar" accept="image/*" onchange="previewAvatar(event)">
                        </label>
                    </div>
                    <span class="gradient-text text-lg font-semibold">Modifier l'Avatar</span>
                </div>

                <!-- Profile Information Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="floating-label">
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" 
                               placeholder=" " class="form-input w-full">
                        <label for="name">Nom Complet</label>
                        @error('name')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="floating-label">
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" 
                               placeholder=" " class="form-input w-full">
                        <label for="email">Adresse Email</label>
                        @error('email')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="floating-label">
                        <input type="text" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" 
                               placeholder=" " class="form-input w-full">
                        <label for="phone">Téléphone</label>
                        @error('phone')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="floating-label">
                        <input type="date" name="birth_date" id="birth_date" 
                               value="{{ old('birth_date', $user->birth_date ? \Carbon\Carbon::parse($user->birth_date)->format('Y-m-d') : '') }}" 
                               placeholder=" " class="form-input w-full">
                        <label for="birth_date">Date de Naissance</label>
                        @error('birth_date')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="md:col-span-2 floating-label">
                        <textarea name="address" id="address" rows="3" placeholder=" " 
                                  class="form-input w-full resize-none">{{ old('address', $user->address) }}</textarea>
                        <label for="address">Adresse</label>
                        @error('address')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="floating-label">
                        <input type="text" name="cin" id="cin" value="{{ old('cin', $user->cin) }}" 
                               placeholder=" " class="form-input w-full">
                        <label for="cin">CIN</label>
                        @error('cin')
                            <p class="error-message">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    <div class="floating-label">
                        <input type="text" name="status" id="status" value="{{ $user->status }}" 
                               readonly placeholder=" " 
                               class="form-input w-full bg-gray-50 cursor-not-allowed opacity-60">
                        <label for="status">Statut</label>
                    </div>
                </div>

                <!-- Save Profile Button -->
                <div class="flex justify-end">
                    <button type="submit" class="btn-gradient px-8 py-4 text-white font-semibold rounded-xl shadow-lg">
                        <span class="relative z-10">Enregistrer les Modifications du Profil</span>
                    </button>
                </div>
            </form>

            <!-- Separator -->
            <div class="separator"></div>

            <!-- Password Update Section -->
            <div class="section-header">
                <h3 class="text-3xl font-bold gradient-text mb-2">
                    Changer le Mot de Passe
                </h3>
                <p class="text-gray-600 font-medium">
                    Assurez-vous que votre compte utilise un mot de passe long et aléatoire pour rester sécurisé
                </p>
            </div>

            <form class="space-y-6" action="{{ route('password.update') }}" method="POST">
                @csrf
                @method('put')

                <div class="floating-label">
                    <input type="password" name="current_password" id="current_password" 
                           placeholder=" " class="form-input w-full">
                    <label for="current_password">Mot de Passe Actuel</label>
                    @error('current_password', 'updatePassword')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="floating-label">
                    <input type="password" name="password" id="password" 
                           placeholder=" " class="form-input w-full">
                    <label for="password">Nouveau Mot de Passe</label>
                    @error('password', 'updatePassword')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <div class="floating-label">
                    <input type="password" name="password_confirmation" id="password_confirmation" 
                           placeholder=" " class="form-input w-full">
                    <label for="password_confirmation">Confirmer le Nouveau Mot de Passe</label>
                    @error('password_confirmation', 'updatePassword')
                        <p class="error-message">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Save Password Button -->
                <div class="flex justify-end">
                    <button type="submit" class="btn-gradient px-8 py-4 text-white font-semibold rounded-xl shadow-lg">
                        <span class="relative z-10">Enregistrer le Nouveau Mot de Passe</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        /**
         * Previews the selected image file as the user's avatar.
         * @param {Event} event The change event from the file input.
         */
        function previewAvatar(event) {
            const reader = new FileReader();
            const file = event.target.files[0];
            
            if (file) {
                reader.onload = function(e) {
                    const output = document.getElementById('avatar-preview');
                    output.src = e.target.result;
                    
                    // Add a subtle animation when image changes
                    output.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        output.style.transform = 'scale(1)';
                    }, 150);
                };
                reader.readAsDataURL(file);
            }
        }

        // Add smooth transitions for form inputs
        document.addEventListener('DOMContentLoaded', function() {
            const inputs = document.querySelectorAll('.form-input');
            
            inputs.forEach(input => {
                input.addEventListener('focus', function() {
                    this.style.transform = 'translateY(-2px)';
                });
                
                input.addEventListener('blur', function() {
                    this.style.transform = 'translateY(0)';
                });
            });
        });
    </script>
@endsection