<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sidebar Animée</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/phosphor-icons/2.0.2/phosphor.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
            min-height: 100vh;
            transition: margin-left 0.3s ease;
            overflow-x: hidden;
        }

        .sidebar {
            width: 280px;
            background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            box-shadow: 0 10px 30px rgba(211, 47, 47, 0.1);
            z-index: 1000;
            display: flex;
            flex-direction: column;
            border-right: 1px solid rgba(211, 47, 47, 0.1);
            overflow: hidden;
            transition: width 0.3s ease, padding 0.3s ease, left 0.3s ease, box-shadow 0.3s ease;
        }

        /* Desktop: Collapsed sidebar styles */
        body.sidebar-collapsed .sidebar {
            width: 80px;
            padding: 0;
            align-items: center;
        }

        body.sidebar-collapsed .sidebar__logo {
            padding: 6px 0;
        }

        body.sidebar-collapsed .sidebar__logo img {
            width: 50px;
            object-fit: contain;
            margin: 0 auto;
        }
        body.sidebar-collapsed .sidebar__logo::before {
            display: none;
        }

        body.sidebar-collapsed .sidebar-menu {
            padding: 0;
        }

        body.sidebar-collapsed .sidebar-menu__item {
            transform: translateX(0);
            opacity: 1;
            animation: none;
        }

        body.sidebar-collapsed .sidebar-menu__link {
            padding: 16px 0;
            justify-content: center;
            flex-direction: column;
            text-align: center;
        }

        body.sidebar-collapsed .sidebar-menu__link .icon {
            margin-right: 0;
            margin-bottom: 5px;
            font-size: 24px;
        }

        body.sidebar-collapsed .sidebar-menu__link .text {
            display: none;
            opacity: 0;
            transition: opacity 0.1s ease;
            font-size: 11px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        /* Show text on hover for collapsed sidebar items */
        body.sidebar-collapsed .sidebar-menu__link:hover .text {
            display: block;
            opacity: 1;
            position: absolute;
            left: 90px;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            z-index: 10;
            top: 50%;
            transform: translateY(-50%);
            white-space: nowrap;
        }

        body.sidebar-collapsed .sidebar-menu__link:hover {
            transform: none;
            box-shadow: none;
        }

        body.sidebar-collapsed .sidebar-close-btn {
            display: none;
        }

        body.sidebar-collapsed .section-title {
            display: none;
        }

        body.sidebar-collapsed .certificate-banner {
            display: none;
            opacity: 0;
        }

        .sidebar-close-btn {
            position: absolute;
            left: 10px;
            top: 10px;
            background: transparent;
            border: 1px solid #e0e0e0;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #666;
            cursor: pointer;
            transition: all 0.3s ease;
            z-index: 10;
        }

        .sidebar-close-btn:hover {
            background: linear-gradient(135deg, #D32F2F, #C2185B);
            color: white;
            border-color: #D32F2F;
            transform: rotate(90deg);
        }

       .sidebar__logo {
            padding: 6px 0 0px 0;
            text-align: center;
            background: #ffffff;
            border-bottom: 3px solid rgba(211, 47, 47, 0.2);
            position: relative;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 60px;
            flex-shrink: 0;
        }

        .sidebar__logo::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s ease;
        }

        .sidebar__logo:hover::before {
            left: 100%;
        }

        .sidebar__logo img {
            width: 70%;
            filter: none;
            transition: all 0.3s ease;
            padding: 0 10px;
            box-sizing: border-box;
            max-height: 100%;
        }

        .sidebar__logo:hover img {
            transform: scale(1.1) rotate(5deg);
        }

        .sidebar-menu-wrapper {
            padding-top: 20px;
            flex-grow: 1;
            overflow-y: auto;
            overflow-x: hidden;
            position: relative;
            /* Set a max height to ensure scrollbar appears */
            max-height: calc(100vh - 80px);
        }

        .sidebar-menu {
            list-style: none;
            padding: 0 20px;
        }

        .sidebar-menu__item {
            margin-bottom: 8px;
            transform: translateX(-20px);
            opacity: 0;
            animation: slideIn 0.5s ease forwards;
        }

        /* Animation delays for staggered effect */
        .sidebar-menu__item:nth-child(1) { animation-delay: 0.1s; }
        .sidebar-menu__item:nth-child(2) { animation-delay: 0.2s; }
        .sidebar-menu__item:nth-child(3) { animation-delay: 0.3s; }
        .sidebar-menu__item:nth-child(4) { animation-delay: 0.4s; }
        .sidebar-menu__item:nth-child(5) { animation-delay: 0.5s; }
        .sidebar-menu__item:nth-child(6) { animation-delay: 0.6s; }
        .sidebar-menu__item:nth-child(7) { animation-delay: 0.7s; }
        .sidebar-menu__item:nth-child(8) { animation-delay: 0.8s; }
        .sidebar-menu__item:nth-child(9) { animation-delay: 0.9s; }
        .sidebar-menu__item:nth-child(10) { animation-delay: 1.0s; }
        .sidebar-menu__item:nth-child(11) { animation-delay: 1.1s; }
        .sidebar-menu__item:nth-child(12) { animation-delay: 1.2s; }
        .sidebar-menu__item:nth-child(13) { animation-delay: 1.3s; }
        .sidebar-menu__item:nth-child(14) { animation-delay: 1.4s; }
        .sidebar-menu__item:nth-child(15) { animation-delay: 1.5s; }

        @keyframes slideIn {
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .sidebar-menu__link {
            display: flex;
            align-items: center;
            padding: 16px 20px;
            border-radius: 15px;
            text-decoration: none;
            color: #555;
            font-size: 1.05em;
            font-weight: 500;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            position: relative;
            overflow: hidden;
        }

        .sidebar-menu__link::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
            transition: left 0.5s ease;
            border-radius: 15px;
            z-index: 0;
        }

        .sidebar-menu__link:hover::before {
            left: 0;
        }

        .sidebar-menu__link:hover {
            color: white;
            transform: translateX(10px) scale(1.02);
            box-shadow: 0 8px 25px rgba(211, 47, 47, 0.3);
        }

        .sidebar-menu__link .icon {
            margin-right: 15px;
            font-size: 22px;
            color: #888;
            transition: all 0.3s ease;
            position: relative;
            z-index: 1;
        }

        .sidebar-menu__link:hover .icon {
            color: white;
            transform: scale(1.2) rotate(10deg);
        }

        .sidebar-menu__link .text {
            position: relative;
            z-index: 1;
            white-space: nowrap;
        }

        .section-title {
            display: block;
            padding: 15px 20px 10px 20px;
            font-size: 0.75em;
            color: #D32F2F;
            margin-top: 15px;
            letter-spacing: 0.5px;
            text-transform: uppercase;
            font-weight: 600;
            position: relative;
        }

        .section-title::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 20px;
            right: 20px;
            height: 2px;
            background: linear-gradient(90deg, #D32F2F, #C2185B, #ef4444);
            border-radius: 1px;
        }

        .logout-link {
            color: #D32F2F !important;
        }

        .logout-link .icon {
            color: #D32F2F !important;
        }

        .logout-link:hover {
            background: linear-gradient(135deg, #D32F2F, #C2185B);
            color: white !important;
        }

        .logout-link:hover .icon {
            color: white !important;
        }

        .certificate-banner {
            background: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
            padding: 25px;
            border-radius: 20px;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin: 20px;
            box-shadow: 0 10px 30px rgba(211, 47, 47, 0.3);
            animation: pulse 2s infinite alternate;
            transition: opacity 0.3s ease;
            flex-shrink: 0;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            100% { transform: scale(1.02); }
        }

        .certificate-banner::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
            animation: rotate 4s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .certificate-icon {
            background: rgba(255, 255, 255, 0.2);
            width: 80px;
            height: 80px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            color: white;
            margin: 0 auto 15px;
            position: relative;
            z-index: 2;
            backdrop-filter: blur(10px);
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .certificate-title {
            font-size: 1.1em;
            margin-bottom: 8px;
            color: white;
            font-weight: 600;
            position: relative;
            z-index: 2;
        }

        .certificate-text {
            font-size: 0.85em;
            color: rgba(255, 255, 255, 0.9);
            line-height: 1.5;
            margin-bottom: 20px;
            position: relative;
            z-index: 2;
        }

        .subscribe-btn {
            display: inline-block;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 12px 28px;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 500;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
            border: 2px solid rgba(255, 255, 255, 0.3);
            position: relative;
            z-index: 2;
            backdrop-filter: blur(10px);
        }

        .subscribe-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-3px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
        }

        /* Custom scrollbar styles - Fixed the typo in class name */
        .sidebar-menu-wrapper::-webkit-scrollbar {
            width: 8px;
        }

        .sidebar-menu-wrapper::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }

        .sidebar-menu-wrapper::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #D32F2F, #C2185B);
            border-radius: 4px;
        }

        .sidebar-menu-wrapper::-webkit-scrollbar-thumb:hover {
            background: linear-gradient(135deg, #C2185B, #ef4444);
        }

        /* Active link style */
        .sidebar-menu__link.active {
            background: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
            color: white;
            transform: translateX(10px) scale(1.02);
            box-shadow: 0 8px 25px rgba(211, 47, 47, 0.3);
        }

        .sidebar-menu__link.active .icon {
            color: white;
            transform: scale(1.2);
        }

        /* Mobile specific adjustments for sidebar */
        @media (max-width: 1199.98px) {
            .sidebar {
                width: 280px;
                left: -280px;
                box-shadow: none;
                z-index: 1050;
            }

            body.mobile-sidebar-open .sidebar {
                left: 0;
                box-shadow: 0 10px 30px rgba(211, 47, 47, 0.1);
            }

            body.mobile-sidebar-open .sidebar .sidebar-menu__link .text,
            body.mobile-sidebar-open .sidebar .section-title,
            body.mobile-sidebar-open .sidebar .certificate-banner,
            body.mobile-sidebar-open .sidebar .sidebar-close-btn {
                display: block;
                opacity: 1;
                animation: none;
                transform: translateX(0);
            }

            .sidebar-menu__link:hover .text {
                position: static;
                transform: none;
                background: transparent;
                color: inherit;
                padding: 0;
                border-radius: 0;
                z-index: auto;
                white-space: normal;
                overflow: visible;
                text-overflow: clip;
            }
            .sidebar-menu__link:hover {
                transform: none;
                box-shadow: none;
            }
        }


    </style>
</head>
<body>
    <aside class="sidebar">
        <button type="button" class="sidebar-close-btn" aria-label="Fermer la barre latérale">
            <i class="ph ph-x"></i>
        </button>

        <a href="{{ route('dashboard') }}" class="sidebar__logo">
            <img src="{{ asset('edmate/assets/images/thumbs/logou.png') }}" alt="UNION IT SERVICES Logo" />
        </a>

        <div class="sidebar-menu-wrapper">
            <div class="p-20 pt-10" style="padding: 20px 0 10px 0;">
                <ul class="sidebar-menu">
                    <li class="sidebar-menu__item">
                        <a href="{{ route('dashboard') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-grid-four"></i></span>
                            <span class="text">Tableau de bord</span>
                        </a>
                    </li>
                    {{-- @can('inscription-create-own')
                    <li class="sidebar-menu__item">
                        <a href="{{ route('etudiant.choose_formation') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-file-plus"></i></span>
                            <span class="text">Je m'inscris</span>
                        </a>
                    </li>
                    @endcan
@can('message-view-own')
                     <li class="sidebar-menu__item">
    <a href="{{ route('message.index') }}" class="sidebar-menu__link">
        <span class="icon"><i class="ph ph-chat-circle"></i></span>
        <span class="text">Mes Messages</span>
    </a>
</li>
@endcan --}}
@can('message-list-all')
<li class="sidebar-menu__item">
                        <a href="{{ route('messages.index') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-chat-circle"></i></span>
                            <span class="text">les Messages</span>
                        </a>
                    </li>
                    @endcan
                    @can('user-list')
                    <li class="sidebar-menu__item">
                        <a href="{{ route('users.index') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-users"></i></span>
                            <span class="text">Utilisateurs</span>
                        </a>
                    </li>
                    @endcan

                    @can('category-list')
                    <li class="sidebar-menu__item">
                        <a href="{{ route('categories.index') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-folder-open"></i></span>
                            <span class="text">Catégories</span>
                        </a>
                    </li>
                    @endcan
                    @can('formation-list')
                    <li class="sidebar-menu__item">
                        <a href="{{ route('formations.index') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-chalkboard-simple"></i></span>
                            <span class="text">Formations</span>
                        </a>
                    </li>
                    @endcan
                   @if (Auth::check() && (Auth::user()->hasRole('Etudiant') || Auth::user()->can('inscription-list')))
    <li class="sidebar-menu__item">
        <a href="{{ route('inscriptions.index') }}" class="sidebar-menu__link">
            <span class="icon"><i class="ph ph-list-checks"></i></span>
            <span class="text">
                @if (Auth::user()->hasRole('Etudiant'))
                    Mes Formations
                @else
                    Inscriptions
                @endif
            </span>
        </a>
    </li>
@endif
                      @can('payment-list')
                    <li class="sidebar-menu__item">
                        <a href="{{ route('payments.index') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-wallet"></i></span>
                            <span class="text">Paiements</span>
                        </a>
                    </li>
                    @endcan

                    <li class="sidebar-menu__item">
                        <a href="{{ route('modules.index') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-squares-four"></i></span>
                            <span class="text">modules</span>
                        </a>
                    </li>

                    <li class="sidebar-menu__item">
                        <a href="{{ route('courses.index') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-book"></i></span>
                            <span class="text">Séances</span>
                        </a>
                    </li>

                    <li class="sidebar-menu__item">
                        <a href="{{ route('course_reschedules.index') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-book"></i></span>
                            <span class="text">Séances Reportées</span>
                        </a>
                    </li>

                    <li class="sidebar-menu__item">
                        <a href="{{ route('reclamations.index') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-question"></i></span>
                            <span class="text">Réclamations</span>
                        </a>
                    </li>

                     

                   

                    @can('promotions')
                    <li class="sidebar-menu__item">
                        <a href="{{ route('promotions.index') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-percent"></i></span>
                            <span class="text">Suivi annuel</span>
                        </a>
                    </li>
                    @endcan

                    @can('role-list')
                     <li class="sidebar-menu__item">
                        <a href="{{ route('roles.index') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-shield"></i></span>
                            <span class="text">roles</span>
                        </a>
                    </li>
                    @endcan

                     @can('role-list')
                     <li class="sidebar-menu__item">
                        <a href="{{ route('download.backup') }}" class="sidebar-menu__link">
                            <span class="icon"><i class="ph ph-shield"></i></span>
                            <span class="text">backups</span>
                        </a>
                    </li>
                    @endcan

                   

                    <li class="sidebar-menu__item">
                        <span class="section-title">Paramètres</span>
                    </li>
                   
                    <li class="sidebar-menu__item">
                        <a href="{{ route('logout') }}" class="sidebar-menu__link logout-link" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                            <span class="icon"><i class="ph ph-sign-out"></i></span>
                            <span class="text">Déconnexion</span>
                        </a>
                        <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </li>
                </ul>
            </div>

          <div class="certificate-banner">
    <div class="certificate-icon">
        <i class="ph ph-certificate"></i>
    </div>
    <h5 class="certificate-title">Découvrez nos exemples de diplômes et d'attestations</h5>
    <p class="certificate-text">Explorez des exemples d'attestations de formation et de diplômes pour mieux vous projeter dans votre parcours.</p>
    <a href="{{ route(name: 'exemples') }}" class="subscribe-btn">Découvrez</a>
</div>
        </div>
    </aside>

    <script>

        document.addEventListener('DOMContentLoaded', function() {
            const sidebarCloseBtn = document.querySelector('.sidebar-close-btn');
            if (sidebarCloseBtn) {
                sidebarCloseBtn.addEventListener('click', function() {
                    document.body.classList.remove('mobile-sidebar-open');
                    this.style.transform = 'rotate(180deg) scale(0.8)';
                    setTimeout(() => {
                        this.style.transform = 'rotate(0deg) scale(1)';
                    }, 300);
                });
            }

            document.querySelectorAll('.sidebar-menu__link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    document.querySelectorAll('.sidebar-menu__link').forEach(l => l.classList.remove('active'));
                    this.classList.add('active');
                    this.style.transform = 'translateX(10px) scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = 'translateX(10px) scale(1.02)';
                    }, 100);
                    if (window.innerWidth < 1200) {
                        document.body.classList.remove('mobile-sidebar-open');
                    }
                });
            });

            document.querySelector('.sidebar__logo img').addEventListener('click', function() {
                this.style.transform = 'scale(1.2) rotate(360deg)';
                setTimeout(() => {
                    this.style.transform = 'scale(1) rotate(0deg)';
                }, 500);
            });

            let particleInterval;
            function toggleParticleEffect() {
                if (!document.body.classList.contains('sidebar-collapsed') && window.innerWidth >= 1200) {
                    if (!particleInterval) {
                        particleInterval = setInterval(createParticle, 2000);
                    }
                } else {
                    clearInterval(particleInterval);
                    particleInterval = null;
                }
            }

            toggleParticleEffect();
            window.addEventListener('resize', toggleParticleEffect);

            function createParticle() {
                const banner = document.querySelector('.certificate-banner');
                if (!banner || banner.style.display === 'none' || banner.style.opacity === '0') return;
                const particle = document.createElement('div');
                particle.style.position = 'absolute';
                particle.style.width = '4px';
                particle.style.height = '4px';
                particle.style.background = 'rgba(255, 255, 255, 0.8)';
                particle.style.borderRadius = '50%';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.top = '100%';
                particle.style.pointerEvents = 'none';
                particle.style.animation = 'float 3s ease-out forwards';

                banner.appendChild(particle);

                setTimeout(() => {
                    particle.remove();
                }, 3000);
            }

            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.style.opacity = '1';
                        entry.target.style.transform = 'translateX(0)';
                    }
                });
            }, observerOptions);

            
            });

            document.querySelectorAll('.sidebar-menu__link, .subscribe-btn').forEach(button => {
                button.addEventListener('click', function(e) {
                    const ripple = document.createElement('span');
                    const rect = this.getBoundingClientRect();
                    const size = Math.max(rect.width, rect.height);
                    const x = e.clientX - rect.left - size / 2;
                    const y = e.clientY - rect.top - size / 2;

                    ripple.style.cssText = `
                        position: absolute;
                        width: ${size}px;
                        height: ${size}px;
                        left: ${x}px;
                        top: ${y}px;
                        background: rgba(255, 255, 255, 0.3);
                        border-radius: 50%;
                        transform: scale(0);
                        animation: ripple 0.6s ease-out;
                        pointer-events: none;
                    `;

                    this.appendChild(ripple);

                    setTimeout(() => {
                        ripple.remove();
                    }, 600);
                });
            });
        });

        // Add CSS animation for float effect
        const style = document.createElement('style');
        style.textContent = `
            @keyframes float {
                0% {
                    transform: translateY(0) scale(0);
                    opacity: 1;
                }
                50% {
                    transform: translateY(-50px) scale(1);
                    opacity: 0.8;
                }
                100% {
                    transform: translateY(-100px) scale(0);
                    opacity: 0;
                }
            }
            
            @keyframes ripple {
                0% {
                    transform: scale(0);
                    opacity: 1;
                }
                100% {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>