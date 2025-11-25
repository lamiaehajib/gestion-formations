<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tableau de Bord des Applications</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @keyframes gradient {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        
        .animate-gradient {
            background-size: 200% 200%;
            animation: gradient 8s ease infinite;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .fade-in {
            animation: fadeIn 0.5s ease-out forwards;
        }
        
        .glass-effect {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .hover-lift {
            transition: all 0.3s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 40px rgba(194, 24, 91, 0.3);
        }
        
        @keyframes pulse-slow {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }
        
        .pulse-slow {
            animation: pulse-slow 3s ease-in-out infinite;
        }

        /* Icon animations */
        svg.icon-hover {
            transition: all 0.3s ease;
        }

        button:hover svg.icon-hover {
            transform: scale(1.15) rotate(5deg);
            filter: drop-shadow(0 4px 8px rgba(0,0,0,0.3));
        }

        /* Bounce animation */
        @keyframes bounce-icon {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-3px); }
        }

        .icon-bounce {
            animation: bounce-icon 0.6s ease-in-out;
        }

        /* Spin animation */
        @keyframes spin-icon {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .icon-spin {
            animation: spin-icon 1s linear infinite;
        }

        /* Glow effect */
        @keyframes glow-icon {
            0%, 100% { filter: drop-shadow(0 0 0px rgba(244, 114, 182, 0.5)); }
            50% { filter: drop-shadow(0 0 8px rgba(244, 114, 182, 1)); }
        }

        .icon-glow {
            animation: glow-icon 2s ease-in-out infinite;
        }

        /* Pulse icon */
        @keyframes pulse-icon {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        .icon-pulse {
            animation: pulse-icon 2s ease-in-out infinite;
        }

        /* Shake animation */
        @keyframes shake-icon {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-2px); }
            75% { transform: translateX(2px); }
        }

        .icon-shake {
            animation: shake-icon 0.5s ease-in-out;
        }
    </style>
</head>
<body class="min-h-screen bg-gradient-to-br from-gray-900 via-[#C2185B] to-[#D32F2F] animate-gradient">
    
    <div class="container mx-auto px-4 max-w-7xl py-8">
        
        {{-- En-tête --}}
        <div class="glass-effect rounded-3xl p-6 mb-8 shadow-2xl fade-in">
            <div class="flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2 flex items-center gap-3">
                        <svg class="w-10 h-10 pulse-slow" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="color: #f472b6;">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"></path>
                        </svg>
                        Tableau de Bord des Applications
                    </h1>
                    <p class="text-pink-100 text-lg">Bienvenue <span class="font-semibold text-white">{{ Auth::guard('crm')->user()->name }}</span></p>
                </div>
                
                <div class="flex items-center gap-4">
                    {{-- Statistiques --}}
                    <div class="flex gap-4">
                        <div class="glass-effect px-6 py-3 rounded-2xl hover-lift text-center">
                            <div class="text-pink-200 text-sm font-medium">Applications</div>
                            <div class="text-white text-3xl font-bold bg-gradient-to-r from-pink-400 to-rose-400 bg-clip-text text-transparent">{{ $stats['total_apps'] }}</div>
                        </div>
                        <div class="glass-effect px-6 py-3 rounded-2xl hover-lift text-center">
                            <div class="text-pink-200 text-sm font-medium">Comptes</div>
                            <div class="text-white text-3xl font-bold bg-gradient-to-r from-rose-400 to-red-400 bg-clip-text text-transparent">{{ $stats['active_accounts'] }}</div>
                        </div>
                    </div>
                    
                    {{-- Bouton Déconnexion --}}
                    <form action="{{ route('crm.logout') }}" method="POST">
                        @csrf
                        <button type="submit" 
                                class="px-5 py-3 bg-gradient-to-r from-[#ef4444] to-[#D32F2F] hover:from-red-600 hover:to-red-700 text-white font-semibold rounded-2xl transition-all duration-300 flex items-center gap-2 shadow-lg hover:shadow-2xl hover:scale-105">
                            <svg class="w-5 h-5 icon-hover" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"></path>
                                <polyline points="16 17 21 12 16 7"></polyline>
                                <line x1="21" y1="12" x2="9" y2="12"></line>
                            </svg>
                            Déconnexion
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Messages --}}
        @if(session('success'))
        <div class="glass-effect border-2 border-green-400 text-green-100 px-6 py-4 rounded-2xl mb-6 fade-in shadow-lg flex items-center gap-2">
            <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            {{ session('success') }}
        </div>
        @endif

        @if(session('error'))
        <div class="glass-effect border-2 border-[#ef4444] text-red-100 px-6 py-4 rounded-2xl mb-6 fade-in shadow-lg flex items-center gap-2">
            <svg class="w-6 h-6 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            {{ session('error') }}
        </div>
        @endif

        {{-- Liste des Applications --}}
        <div class="space-y-6">
            @forelse($applications as $app)
            <div class="glass-effect rounded-3xl overflow-hidden hover-lift shadow-2xl fade-in">
                
                {{-- En-tête de l'application --}}
                <div class="bg-gradient-to-r from-[#C2185B] via-[#D32F2F] to-[#ef4444] p-6 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black opacity-10"></div>
                    <div class="relative flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="flex items-center gap-4">
                            <span class="text-5xl drop-shadow-lg">{{ $app->icon }}</span>
                            <div>
                                <h2 class="text-2xl font-bold text-white drop-shadow-md">{{ $app->name }}</h2>
                                <a href="{{ $app->url }}" target="_blank" 
                                   class="text-pink-100 hover:text-white transition-colors duration-300 inline-flex items-center gap-1 text-sm font-medium">
                                    {{ $app->url }} 
                                    <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <line x1="7" y1="17" x2="17" y2="7"></line>
                                        <polyline points="7 7 17 7 17 17"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="px-4 py-2 bg-white/20 backdrop-blur rounded-xl text-white font-semibold shadow-lg flex items-center gap-2">
                                <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                    <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"></path>
                                </svg>
                                {{ $app->vps_location }}
                            </span>
                           
                        </div>
                    </div>
                </div>

                {{-- Tableau des Comptes --}}
                <div class="p-6">
                    @if($app->accounts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b-2 border-white/20">
                                    <th class="text-left py-4 px-4 text-pink-200 font-bold text-sm uppercase tracking-wider">Rôle</th>
                                    <th class="text-left py-4 px-4 text-pink-200 font-bold text-sm uppercase tracking-wider">Nom d'utilisateur</th>
                                    <th class="text-left py-4 px-4 text-pink-200 font-bold text-sm uppercase tracking-wider">Mot de passe</th>
                                    <th class="text-center py-4 px-4 text-pink-200 font-bold text-sm uppercase tracking-wider">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($app->accounts as $account)
                                <tr class="border-b border-white/10 hover:bg-white/10 transition-all duration-300">
                                    <td class="py-4 px-4">
                                        <span class="px-4 py-2 bg-gradient-to-r from-[#C2185B] to-[#D32F2F] rounded-xl text-white text-sm font-semibold shadow-md inline-block">
                                            {{ $account->role_name }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-2">
                                            <code class="text-white font-mono text-sm bg-black/40 px-4 py-2 rounded-xl shadow-inner">
                                                {{ $account->username }}
                                            </code>
                                            <button onclick="copyText('{{ $account->username }}')" 
                                                    class="p-2 hover:bg-white/20 rounded-xl transition-all duration-300 hover:scale-110">
                                                <svg class="w-5 h-5 icon-hover" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-2">
                                            <code class="text-white font-mono text-sm bg-black/40 px-4 py-2 rounded-xl shadow-inner" 
                                                  id="pass-{{ $account->id }}">
                                                ••••••••
                                            </code>
                                            <button onclick="togglePassword({{ $account->id }})"
                                                    class="p-2 hover:bg-white/20 rounded-xl transition-all duration-300 hover:scale-110">
                                                <svg class="w-5 h-5 icon-hover" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center justify-center gap-2 flex-wrap">
                                            <button onclick="quickLogin({{ $account->id }})"
                                                    class="px-4 py-2 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl transition-all duration-300 text-sm font-semibold shadow-lg hover:shadow-2xl hover:scale-105 flex items-center gap-2">
                                                <svg class="w-4 h-4 icon-hover" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                    <path d="M13 9l3 3L8 20H4v-4l9-11z"></path>
                                                </svg>
                                                Connexion Rapide
                                            </button>
                                            
                                            
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-12">
                        <p class="text-pink-200 text-lg mb-4">Aucun compte trouvé pour cette application</p>
                        
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="glass-effect rounded-3xl p-12 text-center shadow-2xl fade-in">
                <p class="text-pink-200 text-2xl font-semibold">Aucune application trouvée</p>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Modal Connexion Rapide --}}
    <div id="loginModal" class="fixed inset-0 bg-black/70 backdrop-blur-md hidden items-center justify-center z-50">
        <div class="glass-effect rounded-3xl p-8 max-w-md w-full mx-4 shadow-2xl fade-in">
            <h3 class="text-2xl font-bold text-white mb-6 flex items-center gap-3">
                <svg class="w-7 h-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M13 9l3 3L8 20H4v-4l9-11z"></path>
                </svg>
                Informations de connexion
            </h3>
            
            <div class="space-y-5">
                <div>
                    <label class="text-pink-200 text-sm font-semibold mb-2 block">URL :</label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="modal-url" readonly
                               class="flex-1 bg-black/40 text-white px-4 py-3 rounded-xl font-mono text-sm shadow-inner">
                        <button onclick="copyField('modal-url')" 
                                class="px-4 py-3 bg-gradient-to-r from-[#C2185B] to-[#D32F2F] hover:from-pink-700 hover:to-red-700 text-white rounded-xl transition-all duration-300 shadow-lg hover:scale-105">
                            <svg class="w-5 h-5 icon-hover" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="text-pink-200 text-sm font-semibold mb-2 block">Nom d'utilisateur :</label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="modal-username" readonly
                               class="flex-1 bg-black/40 text-white px-4 py-3 rounded-xl font-mono text-sm shadow-inner">
                        <button onclick="copyField('modal-username')" 
                                class="px-4 py-3 bg-gradient-to-r from-[#C2185B] to-[#D32F2F] hover:from-pink-700 hover:to-red-700 text-white rounded-xl transition-all duration-300 shadow-lg hover:scale-105">
                            <svg class="w-5 h-5 icon-hover" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="text-pink-200 text-sm font-semibold mb-2 block">Mot de passe :</label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="modal-password" readonly
                               class="flex-1 bg-black/40 text-white px-4 py-3 rounded-xl font-mono text-sm shadow-inner">
                        <button onclick="copyField('modal-password')" 
                                class="px-4 py-3 bg-gradient-to-r from-[#C2185B] to-[#D32F2F] hover:from-pink-700 hover:to-red-700 text-white rounded-xl transition-all duration-300 shadow-lg hover:scale-105">
                            <svg class="w-5 h-5 icon-hover" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3 mt-8">
                <button onclick="openAppAndClose()" 
                        class="flex-1 px-5 py-4 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl font-bold transition-all duration-300 shadow-lg hover:shadow-2xl hover:scale-105 flex items-center justify-center gap-2">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <circle cx="12" cy="12" r="10"></circle>
                        <polyline points="12 6 12 12 16 14"></polyline>
                    </svg>
                    Ouvrir l'application
                </button>
                <button onclick="closeModal()" 
                        class="px-5 py-4 bg-white/10 hover:bg-white/20 text-white rounded-xl font-semibold transition-all duration-300">
                    Fermer
                </button>
            </div>
        </div>
    </div>

    {{-- Toast --}}
    <div id="toast" class="fixed bottom-6 right-6 px-6 py-4 rounded-2xl hidden shadow-2xl text-white font-semibold flex items-center gap-2">
    </div>

    <script>
    let currentAppUrl = '';
    let passwordCache = {};

    async function togglePassword(accountId) {
        const el = document.getElementById(`pass-${accountId}`);
        
        if (el.textContent === '••••••••') {
            if (passwordCache[accountId]) {
                el.textContent = passwordCache[accountId];
            } else {
                try {
                    const res = await fetch(`/crm/accounts/${accountId}/password`);
                    const data = await res.json();
                    passwordCache[accountId] = data.password;
                    el.textContent = data.password;
                } catch (e) {
                    showToast('Erreur', true);
                }
            }
        } else {
            el.textContent = '••••••••';
        }
    }

    async function quickLogin(accountId) {
        try {
            const res = await fetch(`/crm/accounts/${accountId}/credentials`);
            const data = await res.json();
            
            if (data.success) {
                currentAppUrl = data.url;
                document.getElementById('modal-url').value = data.url;
                document.getElementById('modal-username').value = data.username;
                document.getElementById('modal-password').value = data.password;
                
                document.getElementById('loginModal').classList.remove('hidden');
                document.getElementById('loginModal').classList.add('flex');
            }
        } catch (e) {
            showToast('Erreur', true);
        }
    }

    function openAppAndClose() {
        window.open(currentAppUrl, '_blank');
        closeModal();
    }

    function closeModal() {
        document.getElementById('loginModal').classList.add('hidden');
        document.getElementById('loginModal').classList.remove('flex');
    }

    function copyField(fieldId) {
        copyText(document.getElementById(fieldId).value);
    }

    function copyText(text) {
        navigator.clipboard.writeText(text).then(() => showToast('Copié !'));
    }

    function showToast(msg, isError = false) {
        const toast = document.getElementById('toast');
        const icon = isError ? 
            '<svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>' :
            '<svg class="w-5 h-5 flex-shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>';
        toast.innerHTML = icon + msg;
        toast.className = `fixed bottom-6 right-6 px-6 py-4 rounded-2xl shadow-2xl text-white font-semibold flex items-center gap-2 ${isError ? 'bg-gradient-to-r from-[#ef4444] to-[#D32F2F]' : 'bg-gradient-to-r from-green-500 to-emerald-600'}`;
        toast.classList.remove('hidden');
        setTimeout(() => toast.classList.add('hidden'), 2500);
    }

    document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
    </script>
</body>
</html>