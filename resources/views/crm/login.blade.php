<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - Connexion</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        * {
            font-family: 'Inter', sans-serif;
        }
        
        .gradient-bg {
            background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
            position: relative;
            overflow: hidden;
        }
        
        .gradient-bg::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(211, 47, 47, 0.1) 0%, transparent 70%);
            animation: pulse 15s ease-in-out infinite;
        }
        
        @keyframes pulse {
            0%, 100% { transform: translate(0, 0) scale(1); }
            50% { transform: translate(-10%, -10%) scale(1.1); }
        }
        
        .glass-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
        
        .input-field {
            background: rgba(255, 255, 255, 0.08);
            border: 1px solid rgba(255, 255, 255, 0.15);
            transition: all 0.3s ease;
        }
        
        .input-field:focus {
            background: rgba(255, 255, 255, 0.12);
            border-color: #D32F2F;
            box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.2);
            outline: none;
        }
        
        .btn-primary {
            background: linear-gradient(135deg, #D32F2F 0%, #C2185B 100%);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .btn-primary::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }
        
        .btn-primary:hover::before {
            left: 100%;
        }
        
        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 20px 35px -10px rgba(211, 47, 47, 0.5);
        }
        
        .floating {
            animation: float 3s ease-in-out infinite;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px); }
            50% { transform: translateY(-10px); }
        }
        
        .icon-glow {
            filter: drop-shadow(0 0 10px rgba(211, 47, 47, 0.5));
        }
        
        .error-box {
            background: linear-gradient(135deg, rgba(239, 68, 68, 0.2) 0%, rgba(220, 38, 38, 0.2) 100%);
            border: 1px solid rgba(239, 68, 68, 0.4);
            animation: shake 0.5s ease-in-out;
        }
        
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-10px); }
            75% { transform: translateX(10px); }
        }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-4">
    
    <!-- Particles décoratifs -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 left-10 w-72 h-72 bg-pink-600 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse"></div>
        <div class="absolute bottom-20 right-10 w-72 h-72 bg-red-600 rounded-full mix-blend-multiply filter blur-3xl opacity-10 animate-pulse" style="animation-delay: 2s;"></div>
    </div>

    <div class="w-full max-w-md relative z-10">
        
        <!-- Logo et titre -->
        <div class="text-center mb-10">
            <div class="inline-block floating">
                <img src="{{ asset('edmate/assets/images/thumbs/Asset.png') }}" 
                     class="w-32 h-32 mx-auto mb-6 icon-glow object-contain" 
                     alt="Logo">
            </div>
            <h1 class="text-4xl font-bold text-white mb-3 tracking-tight">
                CRM <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 to-pink-600">Applications</span>
            </h1>
            <p class="text-gray-300 text-sm font-light">
                Connectez-vous pour accéder à votre espace
            </p>
        </div>

        <!-- Carte principale -->
        <div class="glass-card rounded-3xl p-8 shadow-2xl">
            
            <!-- Message d'erreur -->
            @if($errors->any())
            <div class="error-box rounded-2xl px-5 py-4 mb-6">
                <div class="flex items-center gap-3">
                    <svg class="w-6 h-6 text-red-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-red-200 font-medium">{{ $errors->first() }}</span>
                </div>
            </div>
            @endif

            <form action="{{ route('crm.login.submit') }}" method="POST" class="space-y-6">
                @csrf

                <!-- Email -->
                <div>
                    <label class="block text-gray-200 text-sm font-semibold mb-2.5">
                        Adresse email
                    </label>
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-red-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <input type="email" 
                               name="email" 
                               value="{{ old('email') }}"
                               required 
                               autofocus
                               placeholder="votre@email.com"
                               class="w-full input-field text-white rounded-xl pl-12 pr-4 py-3.5 placeholder-gray-500">
                    </div>
                </div>

                <!-- Mot de passe -->
                <div>
                    <label class="block text-gray-200 text-sm font-semibold mb-2.5">
                        Mot de passe
                    </label>
                    <div class="relative group">
                        <div class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400 group-focus-within:text-red-400 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                        </div>
                        <input type="password" 
                               name="password" 
                               id="password"
                               required
                               placeholder="••••••••"
                               class="w-full input-field text-white rounded-xl pl-12 pr-12 py-3.5 placeholder-gray-500">
                        <button type="button" 
                                onclick="togglePassword()"
                                class="absolute right-4 top-1/2 -translate-y-1/2 text-gray-400 hover:text-red-400 transition-colors">
                            <svg id="eyeIcon" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- Se souvenir de moi -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2.5 cursor-pointer group">
                        <input type="checkbox" 
                               name="remember" 
                               class="w-4 h-4 rounded border-gray-600 bg-white/10 text-red-600 focus:ring-red-500 focus:ring-offset-0 cursor-pointer">
                        <span class="text-gray-300 text-sm group-hover:text-white transition-colors">
                            Se souvenir de moi
                        </span>
                    </label>
                </div>

                <!-- Bouton de connexion -->
                <button type="submit"
                        class="w-full py-4 btn-primary text-white font-bold text-lg rounded-xl shadow-lg relative overflow-hidden">
                    <span class="relative z-10 flex items-center justify-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                        Se connecter
                    </span>
                </button>
            </form>
        </div>

        <!-- Footer -->
        <p class="text-center text-gray-400 text-sm mt-8 font-light">
            © <span id="year"></span> UITS - Tous droits réservés
        </p>
    </div>

    <script>
    // Année dynamique
    document.getElementById('year').textContent = new Date().getFullYear();
    
    // Toggle password
    function togglePassword() {
        const input = document.getElementById('password');
        const icon = document.getElementById('eyeIcon');
        
        if (input.type === 'password') {
            input.type = 'text';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>';
        } else {
            input.type = 'password';
            icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>';
        }
    }
    </script>
</body>
</html>