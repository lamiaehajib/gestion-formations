<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ERP Digitale | Union IT Services</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        .gradient-bg {
            background: linear-gradient(135deg, #0f0c29, #302b63, #0f3460);
            background-size: 400% 400%;
            animation: gradientShift 16s ease infinite;
        }
        @keyframes gradientShift {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
        .blob { position: absolute; border-radius: 50%; filter: blur(90px); opacity: 0.18; }
        .blob1 { width: 700px; height: 700px; background: #D32F2F; top: -15%; left: -10%; animation: floatBlob 22s infinite; }
        .blob2 { width: 900px; height: 900px; background: #cc7350ff; bottom: -25%; right: -15%; animation: floatBlob 28s infinite reverse; }
        .blob3 { width: 600px; height: 600px; background: #C2185B; top: 20%; right: 5%; animation: floatBlob 25s infinite; }
        @keyframes floatBlob {
            0%,100% { transform: translate(0,0) rotate(0deg); }
            50% { transform: translate(50px, -60px) rotate(180deg); }
        }
        .glass-card {
            background: rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(28px);
            -webkit-backdrop-filter: blur(28px);
            border: 1px solid rgba(255, 255, 255, 0.15);
            box-shadow: 0 30px 80px rgba(0, 0, 0, 0.7);
            border-radius: 2.5rem;
        }
        .input-field {
            background: rgba(255, 255, 255, 0.1);
            border: 1.5px solid rgba(255, 255, 255, 0.18);
            transition: all 0.4s ease;
        }
        .input-field:focus {
            background: rgba(255, 255, 255, 0.16);
            border-color: #D32F2F;
            box-shadow: 0 0 0 5px rgba(255, 0, 0, 0.47), 0 0 40px rgba(231, 138, 138, 0.86));
            transform: scale(1.02);
        }
        .btn-primary {
            background: linear-gradient(135deg, #D32F2F, #d81b60, #ad1457);
            background-size: 200% 200%;
            animation: btnGradient 7s ease infinite;
            transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        }
        .btn-primary:hover {
            transform: translateY(-10px) scale(1.08);
            box-shadow: 0 35px 70px rgba(255, 0, 0, 0.47);
        }
        @keyframes btnGradient { 0%{background-position:0% 50%} 50%{background-position:100% 50%} 100%{background-position:0% 50%} }
        @keyframes float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-18px)} }
        .floating { animation: float 5s ease-in-out infinite; }
    </style>
</head>
<body class="min-h-screen gradient-bg flex items-center justify-center p-6 relative overflow-hidden">

    <div class="fixed inset-0 pointer-events-none">
        <div class="blob blob1"></div>
        <div class="blob blob2"></div>
        <div class="blob blob3"></div>
    </div>

    <!-- Form kbir bzzaf w mzyan -->
    <div class="w-full max-w-2xl relative z-10">
        <div class="text-center mb-12">
            <img src="{{ asset('edmate/assets/images/thumbs/Asset.png') }}" 
                 class="w-40 h-40 mx-auto mb-8 object-contain drop-shadow-2xl floating" alt="Logo">
            <h1 class="text-6xl md:text-7xl font-black text-white tracking-tight">
                ERP <span class="text-transparent bg-clip-text bg-gradient-to-r from-red-500 via-red-500 to-purple-700">Digitale</span>
            </h1>
            <p class="text-red-300 text-2xl font-bold mt-3">Plateforme Digitale by Union IT Services</p>
            <p class="text-gray-300 text-lg mt-2">Connectez-vous pour accéder à votre espace</p>
        </div>

        <div class="glass-card p-12 md:p-16">
            @if($errors->any())
                <div class="bg-red-900/40 border border-red-500/60 rounded-2xl p-5 mb-8 backdrop-blur">
                    <div class="flex items-center gap-4 text-red-200 font-bold">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <span>{{ $errors->first() }}</span>
                    </div>
                </div>
            @endif

            <form action="{{ route('crm.login.submit') }}" method="POST" class="space-y-9">
                @csrf
                <div class="relative group">
                    <input type="email" name="email" value="{{ old('email') }}" required autofocus placeholder="votre@email.com"
                           class="w-full input-field text-white rounded-3xl pl-16 pr-6 py-6 text-xl placeholder-gray-400 focus:outline-none">
                    <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-red-400 transition">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    </div>
                </div>

                <div class="relative group">
                    <input type="password" name="password" id="password" required placeholder="••••••••••"
                           class="w-full input-field text-white rounded-3xl pl-16 pr-20 py-6 text-xl placeholder-gray-400 focus:outline-none">
                    <div class="absolute left-6 top-1/2 -translate-y-1/2 text-gray-300 group-focus-within:text-red-400 transition">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                        </svg>
                    </div>
                    <button type="button" onclick="togglePassword()" class="absolute right-6 top-1/2 -translate-y-1/2 text-gray-300 hover:text-red-400 transition">
                        <svg id="eyeIcon" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                    </button>
                </div>

                <button type="submit" id="loginBtn" class="w-full py-7 btn-primary text-white font-black text-2xl rounded-3xl shadow-2xl relative overflow-hidden">
                    <span class="relative z-10 flex items-center justify-center gap-4">
                        SE CONNECTER
                        <svg class="w-9 h-9" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </span>
                </button>
            </form>
        </div>

        <p class="text-center text-gray-400 text-base mt-12 font-light">
            © <span id="year"></span> UITS — Tous droits réservés
        </p>
    </div>

    <script>
        document.getElementById('year').textContent = new Date().getFullYear();

        function togglePassword() {
            const input = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21"/>`;
            } else {
                input.type = 'password';
                icon.innerHTML = `<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>`;
            }
        }

        // Particules 3afya
        document.getElementById('loginBtn').addEventListener('click', function(e) {
            for(let i = 0; i < 35; i++) {
                const p = document.createElement('div');
                p.classList.add('particle');
                const size = Math.random() * 10 + 5;
                p.style.cssText = `
                    position:fixed; background:#D32F2F; border-radius:50%; pointer-events:none;
                    width:${size}px; height:${size}px;
                    left:${e.clientX}px; top:${e.clientY}px;
                    --x: ${(Math.random()-0.5)*400}px;
                    --y: ${(Math.random()-0.5)*400}px;
                    animation: explode 1.2s ease-out forwards; opacity:0.8;
                `;
                document.body.appendChild(p);
                setTimeout(() => p.remove(), 1200);
            }
        });
        const style = document.createElement('style');
        style.innerHTML = `@keyframes explode { to { transform: translate(var(--x), var(--y)) scale(0); opacity:0; } }`;
        document.head.appendChild(style);
    </script>
</body>
</html>