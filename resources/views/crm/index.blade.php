<!DOCTYPE html>
<html lang="fr" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plateforme Digitale ERP by Union IT Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700;800;900&display=swap');
        
        /* COULEURS ET POLICES */
        :root {
            --color-pink: #C2185B;
            --color-red-dark: #D32F2F;
            --color-red-light: #ef4444;
            --color-text-dark: #1a1a2e;
            --color-bg-light: #f4f5f7;
        }

        * {
            font-family: 'Inter', sans-serif;
        }
        
        body {
            /* Fond légèrement texturé */
            background: var(--color-bg-light);
            min-height: 100vh;
        }
        
        /* HEADER FIXE ET ÉLÉGANT */
        #app-fixed-header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 50; 
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15); /* Ombre plus prononcée */
            background: linear-gradient(135deg, var(--color-red-light) 0%, var(--color-red-dark) 50%, var(--color-pink) 100%);
        }

        
        /* Animations */
        @keyframes slideUp {
            from { opacity: 0; transform: translateY(30px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .animate-slide-up {
            animation: slideUp 0.6s ease-out forwards;
        }
        
        /* Cartes Modernes (Applications & Stats) */
        .card-modern {
            background: #ffffff;
            border: 1px solid #e0e0e5; 
            border-radius: 20px; /* Légèrement plus arrondi */
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08); 
            transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        .card-modern:hover {
            border-color: rgba(211, 47, 47, 0.3); 
            transform: translateY(-4px); /* Effet 3D léger */
            box-shadow: 0 25px 60px rgba(211, 47, 47, 0.15); 
        }
        
        /* Ajustements de Texte */
        h1, h2, code, .text-white-dark-bg, .text-white\/80-dark-bg {
            color: var(--color-text-white ) !important; 
        }

        /* Exceptions pour le texte dans l'en-tête coloré */
        #app-fixed-header h1, #app-fixed-header p, #app-fixed-header svg {
            color: white !important;
        }

        .gradient-text {
            background: linear-gradient(135deg, var(--color-pink) 0%, var(--color-red-dark) 50%, var(--color-red-light) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
        /* Bouton Principal (Connexion Rapide) */
        .btn-primary {
            background: linear-gradient(135deg, var(--color-red-dark) 0%, var(--color-pink) 100%);
            box-shadow: 0 10px 30px rgba(211, 47, 47, 0.3);
            transition: all 0.3s ease;
            color: white !important; 
            border-radius: 12px; /* Uniformiser le border-radius */
        }

        .btn-primary:hover {
            box-shadow: 0 15px 40px rgba(211, 47, 47, 0.4); 
            transform: translateY(-2px);
        }

        /* Cartes de Statistiques */
        .stat-card {
            background: rgba(var(--color-red-dark), 0.03); /* Utiliser une légère teinte de la couleur principale */
            border: 1px solid rgba(var(--color-red-dark), 0.1); 
            border-radius: 16px;
        }

        /* Lignes de Tableau */
        .table-row {
            transition: all 0.3s ease;
        }
        
        .table-row:hover {
            background: rgba(239, 68, 68, 0.03); 
            transform: scale(1.005); /* Effet très subtil */
        }
        
        /* Badge (Rôle) */
        .badge-modern {
            background: linear-gradient(135deg, var(--color-red-dark) 0%, var(--color-pink) 100%);
            box-shadow: 0 4px 15px rgba(211, 47, 47, 0.2); 
            padding: 8px 16px;
            border-radius: 10px;
        }

        /* Conteneur d'Application (En-tête Coloré) */
        .app-header-gradient {
            background: linear-gradient(135deg, var(--color-red-light) 0%, var(--color-red-dark) 50%, var(--color-pink) 100%);
            position: relative;
            overflow: hidden;
            color: white !important;
            border-radius: 20px 20px 0 0; /* Uniquement en haut */
            padding: 30px; /* Plus d'espace */
        }
        
        @keyframes shimmer {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100%); }
        }

        .app-header-gradient::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent 30%, rgba(255,255,255,0.15) 50%, transparent 70%);
            animation: shimmer 4s ease-in-out infinite;
        }
        
        /* Champs de saisie */
        .input-modern {
            background: #f0f0f5; 
            border: 1px solid #cccccc;
            color: var(--color-text-dark); 
            transition: all 0.3s ease;
        }
        
        .input-modern:focus {
            background: #ffffff;
            border-color: rgba(211, 47, 47, 0.5); 
            box-shadow: 0 0 20px rgba(211, 47, 47, 0.1); 
        }

        /* Modal */
        .modal-backdrop {
            backdrop-filter: blur(8px); /* Légèrement moins flou */
            background: rgba(0, 0, 0, 0.4); 
        }
        
        /* Ajuster les couleurs spécifiques */
        .text-red-400 { color: var(--color-red-light); }
        .text-gray-700 { color: #374151; }

        /* Marge pour le contenu (Header plus grand) */
        .content-container {
            padding-top: 120px !important; 
        }

        /* NOUVEAU STYLE LOGO */
        .logo-container .relative.group img {
            width: auto; 
            height: 80px;
            
        }

        @media (min-width: 768px) {
            .logo-container .relative.group img {
                width: 65px; 
                height: 65px;
                min-width: 65px;
                min-height: 65px;
                
            }
        }
        
        @media (min-width: 1024px) {
            .logo-container .relative.group img {
                width: auto; 
                height: 80px;
                min-width: 80px;
                min-height: 80px;
                        margin-right: 100px;
            }
        }
        
        /* Logo dans le header */
        .header-logo {
            transform: scale(1.1) !important; /* Agrandir le logo de manière visible */
        }
        
    </style>

</head>
<body>

<header id="app-fixed-header" class="app-header-gradient px-4 py-4 md:px-8 shadow-2xl">
    <div class="max-w-7xl mx-auto flex items-center justify-between">

        <div class="flex items-center gap-5">
            <div class="logo-container">
                <div class="relative group">
                    
                    <div class="absolute -inset-2  rounded-3xl blur-xl opacity-70 group-hover:opacity-100 transition duration-700"></div>
            
                    <img src="{{ asset('edmate/assets/images/thumbs/Asset.png') }}" 
                        
                        alt="Logo Union IT Services">
                </div>
            </div>

            <div class="hidden sm:block">
                <h1 class="text-2xl md:text-4xl font-black text-white tracking-tight">
                    Plateforme <span class="text-white bg-clip-text">Digitale ERP</span>
                </h1>
                <p class="text-red-100 text-sm md:text-base font-medium tracking-wider opacity-90">
                    Union IT Services © {{ date('Y') }}
                </p>
            </div>
        </div>

        <div class="flex items-center gap-6">

            <div class="hidden lg:flex items-center gap-3 text-white">
                <div class="w-12 h-12 bg-white/20 backdrop-blur-md rounded-full flex items-center justify-center border border-white/30">
                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-sm opacity-80">Bienvenue,</p>
                    <p class="font-bold text-lg">{{ Auth::guard('crm')->user()->name }}</p>
                </div>
            </div>

            <form action="{{ route('crm.logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" 
                        class="group relative overflow-hidden bg-white/15 hover:bg-white/30 backdrop-blur-md border border-white/30 px-6 py-3 rounded-xl text-white font-bold text-sm md:text-base transition-all duration-500 hover:scale-105 hover:shadow-2xl flex items-center gap-3">

                    <span class="absolute inset-0 bg-gradient-to-r from-transparent via-white/30 to-transparent -skew-x-12 translate-x-[-200%] group-hover:translate-x-[200%] transition-transform duration-1000"></span>

                    <svg class="w-5 h-5 md:w-6 md:h-6 group-hover:translate-x-1 transition-transform duration-300" 
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                        <path d="M17 16l4-4m0 0l-4-4m4 4H7"></path>
                        <path d="M13 19H5a2 2 0 01-2-2V7a2 2 0 012-2h8"></path>
                    </svg>

                    <span class="relative z-10 hidden sm:inline">Déconnexion</span>
                    <span class="relative z-10 inline sm:hidden">Quitter</span>
                </button>
            </form>
        </div>
    </div>
</header>

    <div class="content-container container mx-auto px-4 max-w-7xl py-8">
        
        <div class="card-modern rounded-3xl p-6 md:p-8 mb-8 animate-slide-up">
             <div class="flex flex-col lg:flex-row items-center justify-between gap-6">
                 <div>
                     <h1 class="text-3xl md:text-4xl font-extrabold text-white-dark-bg mb-1">
                         Tableau de Bord <span class="gradient-text">Applications</span>
                     </h1>
                     <p class="text-gray-500 text-base">Gérez vos accès rapidement et efficacement.</p>
                 </div>
                 
                 <div class="flex items-center gap-4 flex-shrink-0">
                     <div class="flex gap-4">
                         <div class="stat-card px-5 py-3 rounded-2xl text-center shadow-lg">
                             <div class="text-gray-500 text-xs font-semibold mb-1 uppercase">APPLICATIONS</div>
                             <div class="text-3xl font-black gradient-text">{{ $stats['total_apps'] }}</div>
                         </div>
                         <div class="stat-card px-5 py-3 rounded-2xl text-center shadow-lg">
                             <div class="text-gray-500 text-xs font-semibold mb-1 uppercase">COMPTES</div>
                             <div class="text-3xl font-black gradient-text">{{ $stats['active_accounts'] }}</div>
                         </div>
                     </div>
                 </div>
             </div>
        </div>
        @if(session('success'))
        <div class="card-modern border-l-4 border-green-500 bg-green-50 text-green-700 px-6 py-4 rounded-2xl mb-6 animate-slide-up flex items-center gap-3">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <polyline points="20 6 9 17 4 12"></polyline>
            </svg>
            <span class="font-medium">{{ session('success') }}</span>
        </div>
        @endif
        
        @if(session('error'))
        <div class="card-modern border-l-4 border-red-500 bg-red-50 text-red-600 px-6 py-4 rounded-2xl mb-6 animate-slide-up flex items-center gap-3">
            <svg class="w-6 h-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                <circle cx="12" cy="12" r="10"></circle>
                <line x1="12" y1="8" x2="12" y2="12"></line>
                <line x1="12" y1="16" x2="12.01" y2="16"></line>
            </svg>
            <span class="font-medium">{{ session('error') }}</span>
        </div>
        @endif
        <div class="space-y-6">
            @forelse($applications as $app)
            <div class="card-modern rounded-3xl overflow-hidden animate-slide-up">
                <div class="app-header-gradient p-6 md:p-8">
                    <div class="relative z-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4">
                        <div class="flex items-center gap-5">
                            <div class="text-5xl md:text-6xl bg-white/15 p-4 rounded-xl backdrop-blur-sm shadow-xl">{{ $app->icon }}</div>
                            <div>
                                <h2 class="text-2xl md:text-3xl font-extrabold text-white mb-1 tracking-tight">{{ $app->name }}</h2>
                                <p class="text-red-100 text-sm md:text-base mb-2 opacity-90">{{ $app->description }}</p>
                                <a href="{{ $app->url }}" target="_blank" class="text-white/80 hover:text-white transition-colors inline-flex items-center gap-1 text-xs font-medium border-b border-white/50 hover:border-white">
                                    {{ $app->url }}
                                    <svg class="w-3 h-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <line x1="7" y1="17" x2="17" y2="7"></line>
                                        <polyline points="7 7 17 7 17 17"></polyline>
                                    </svg>
                                </a>
                            </div>
                        </div>
                        <div class="px-4 py-2 bg-white/20 backdrop-blur-sm rounded-full text-white font-bold text-sm flex items-center gap-2 flex-shrink-0">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor">
                                <circle cx="12" cy="12" r="10"></circle>
                            </svg>
                            {{ $app->vps_location }}
                        </div>
                    </div>
                </div>

                <div class="p-6 md:p-8">
                    @if($app->accounts->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="border-b border-gray-200">
                                    <th class="text-left py-4 px-4 text-gray-700 font-extrabold text-xs uppercase tracking-widest">Rôle</th>
                                    <th class="text-left py-4 px-4 text-gray-700 font-extrabold text-xs uppercase tracking-widest">Utilisateur</th>
                                    <th class="text-left py-4 px-4 text-gray-700 font-extrabold text-xs uppercase tracking-widest">Mot de passe</th>
                                    <th class="text-center py-4 px-4 text-gray-700 font-extrabold text-xs uppercase tracking-widest">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($app->accounts as $account)
                                <tr class="table-row border-b border-gray-100">
                                    <td class="py-4 px-4">
                                        <span class="badge-modern px-4 py-2 text-white text-sm font-semibold inline-block">
                                            {{ $account->role_name }}
                                        </span>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <code class="font-mono text-sm input-modern px-4 py-2 rounded-lg text-gray-800">{{ $account->username }}</code>
                                            <button onclick="copyText('{{ $account->username }}')" class="p-2 hover:bg-red-500/20 rounded-full transition-all" title="Copier l'utilisateur">
                                                <svg class="w-5 h-5 text-gray-400 hover:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                                    <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4">
                                        <div class="flex items-center gap-3">
                                            <code class="font-mono text-sm input-modern px-4 py-2 rounded-lg text-gray-800" id="pass-{{ $account->id }}">••••••••</code>
                                            <button onclick="togglePassword({{ $account->id }})" class="p-2 hover:bg-red-500/20 rounded-full transition-all" title="Afficher/Masquer le mot de passe">
                                                <svg class="w-5 h-5 text-gray-400 hover:text-red-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                    <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                                    <circle cx="12" cy="12" r="3"></circle>
                                                </svg>
                                            </button>
                                        </div>
                                    </td>
                                    <td class="py-4 px-4 text-center">
                                        <button onclick="quickLogin({{ $account->id }})" class="btn-primary px-5 py-2.5 text-white text-sm font-bold inline-flex items-center gap-2 shadow-lg">
                                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                                                <polyline points="10 17 15 12 10 7"></polyline>
                                                <line x1="15" y1="12" x2="3" y2="12"></line>
                                            </svg>
                                            Connexion Rapide
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-12 text-gray-400">Aucun compte disponible pour cette application.</div>
                    @endif
                </div>
            </div>
            @empty
            <div class="card-modern rounded-3xl p-12 text-center animate-slide-up">
                <p class="text-gray-400 text-xl">Aucune application trouvée. Contactez votre administrateur.</p>
            </div>
            @endforelse
        </div>
        </div>

    <div id="loginModal" class="fixed inset-0 modal-backdrop hidden items-center justify-center z-50">
        <div class="card-modern rounded-3xl p-8 max-w-md w-full mx-4 animate-slide-up">
            <h3 class="text-2xl font-bold text-white-dark-bg mb-6 flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-red-600 to-red-800 flex items-center justify-center shadow-lg">
                    <svg class="w-6 h-6 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"></path>
                        <polyline points="10 17 15 12 10 7"></polyline>
                        <line x1="15" y1="12" x2="3" y2="12"></line>
                    </svg>
                </div>
                Informations de connexion
            </h3>
            
            <div class="space-y-5">
                <div>
                    <label class="text-gray-500 text-sm font-semibold mb-2 block">URL</label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="modal-url" readonly class="flex-1 input-modern text-white-dark-bg px-4 py-3 rounded-xl font-mono text-sm">
                        <button onclick="copyField('modal-url')" class="btn-primary px-4 py-3 rounded-xl flex-shrink-0" title="Copier l'URL">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="text-gray-500 text-sm font-semibold mb-2 block">Nom d'utilisateur</label>
                    <div class="flex items-center gap-2">
                        <input type="text" id="modal-username" readonly class="flex-1 input-modern text-white-dark-bg px-4 py-3 rounded-xl font-mono text-sm">
                        <button onclick="copyField('modal-username')" class="btn-primary px-4 py-3 rounded-xl flex-shrink-0" title="Copier l'utilisateur">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                        </button>
                    </div>
                </div>
                
                <div>
                    <label class="text-gray-500 text-sm font-semibold mb-2 block">Mot de passe</label>
                    <div class="flex items-center gap-2">
                        <input type="password" id="modal-password" readonly class="flex-1 input-modern text-white-dark-bg px-4 py-3 rounded-xl font-mono text-sm">
                        
                        <button type="button" onclick="toggleModalPassword()" class="p-3 hover:bg-red-500/20 rounded-xl transition-all flex-shrink-0" title="Afficher/Masquer">
                            <svg id="eye-open" class="w-5 h-5 text-gray-500 hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path>
                                <circle cx="12" cy="12" r="3"></circle>
                            </svg>
                            <svg id="eye-closed" class="w-5 h-5 text-gray-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"></path>
                                <path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"></path>
                                <path d="M1 1l22 22"></path>
                            </svg>
                        </button>

                        <button onclick="copyField('modal-password')" class="btn-primary px-4 py-3 rounded-xl flex-shrink-0" title="Copier le mot de passe">
                            <svg class="w-5 h-5 text-white" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path>
                                <rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect>
                            </svg>
                        </button>
                    </div>
                </div>
            </div>
            
            <div class="flex gap-3 mt-8">
                <button onclick="openAppAndClose()" class="flex-1 btn-primary px-5 py-4 text-white font-bold flex items-center justify-center gap-2 shadow-xl">
                    <svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="7" y1="17" x2="17" y2="7"></line>
                        <polyline points="7 7 17 7 17 17"></polyline>
                    </svg>
                    Ouvrir l'application
                </button>
                <button onclick="closeModal()" class="px-5 py-4 hover:bg-gray-100 text-gray-700 rounded-xl font-semibold transition-all border border-gray-200">
                    Fermer
                </button>
            </div>
        </div>
    </div>
    <div id="toast" class="fixed bottom-6 right-6 px-6 py-4 rounded-2xl hidden shadow-2xl text-white font-semibold flex items-center gap-2 z-50"></div>
    <script>
        let currentAppUrl = '';
        let passwordCache = {};

        // Récupère et affiche/cache le mot de passe dans la liste des comptes
        async function togglePassword(accountId) {
            const el = document.getElementById(`pass-${accountId}`);
            
            if (el.textContent === '••••••••') {
                if (passwordCache[accountId]) {
                    el.textContent = passwordCache[accountId];
                } else {
                    el.textContent = '...'; // Chargement
                    try {
                        // Ceci simule une requête sécurisée pour obtenir le mot de passe
                        const res = await fetch(`/erp/accounts/${accountId}/password`);
                        const data = await res.json();
                        passwordCache[accountId] = data.password;
                        el.textContent = data.password;
                    } catch (e) {
                        el.textContent = 'Erreur';
                        showToast('Erreur lors du chargement du mot de passe', true);
                    }
                }
            } else {
                el.textContent = '••••••••';
            }
        }

        // Affiche la modal de connexion rapide avec les informations du compte
        async function quickLogin(accountId) {
            try {
                // Ceci simule une requête pour obtenir les credentials du compte
                const res = await fetch(`/erp/accounts/${accountId}/credentials`);
                if (!res.ok) throw new Error('Erreur réseau');
                const data = await res.json();
                
                if (data.success) {
                    currentAppUrl = data.url;
                    document.getElementById('modal-url').value = data.url;
                    document.getElementById('modal-username').value = data.username;
                    
                    const passInput = document.getElementById('modal-password');
                    passInput.value = data.password;
                    passInput.type = 'password';
                    
                    document.getElementById('eye-open').classList.add('hidden');
                    document.getElementById('eye-closed').classList.remove('hidden');

                    document.getElementById('loginModal').classList.remove('hidden');
                    document.getElementById('loginModal').classList.add('flex');
                } else {
                    showToast(data.message || 'Erreur lors de la récupération des informations.', true);
                }
            } catch (e) {
                console.error(e);
                showToast('Erreur de connexion rapide', true);
            }
        }

        // Bascule entre texte et mot de passe pour le champ dans la modal
        function toggleModalPassword() {
            const input = document.getElementById('modal-password');
            const eyeOpen = document.getElementById('eye-open');
            const eyeClosed = document.getElementById('eye-closed');

            if (input.type === 'password') {
                input.type = 'text';
                eyeOpen.classList.remove('hidden');
                eyeClosed.classList.add('hidden');
            } else {
                input.type = 'password';
                eyeOpen.classList.add('hidden');
                eyeClosed.classList.remove('hidden');
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
            navigator.clipboard.writeText(text).then(() => showToast('Copié avec succès !'));
        }

        function showToast(msg, isError = false) {
            const toast = document.getElementById('toast');
            const icon = isError ?
                '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>' :
                '<svg class="w-5 h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"></polyline></svg>';
            toast.innerHTML = icon + msg;
            toast.className = `fixed bottom-6 right-6 px-6 py-4 rounded-2xl shadow-2xl text-white font-semibold flex items-center gap-2 z-50 ${isError ? 'bg-gradient-to-r from-red-dark to-red-light' : 'bg-gradient-to-r from-green-500 to-green-600'}`;
            toast.classList.remove('hidden');
            setTimeout(() => toast.classList.add('hidden'), 3000); // 3 secondes
        }

        document.addEventListener('keydown', (e) => { if (e.key === 'Escape') closeModal(); });
    </script>
</body>
</html>