@extends('layouts.inscri')
@section('title', __('Inscription en Attente') . ' - Portail Étudiant UITS')
@section('content')
<style>
/* Modern animated background with floating particles */
.page-bg {
    min-height: 100vh;
    display: flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #D32F2F 0%, #C2185B 25%,rgb(142, 24, 24) 50%,rgb(254, 87, 87) 75%,rgb(161, 93, 120) 100%);
    background-size: 400% 400%;
    animation: gradientShift 15s ease infinite;
    position: relative;
    overflow: hidden;
}

@keyframes gradientShift {
    0% { background-position: 0% 50%; }
    50% { background-position: 100% 50%; }
    100% { background-position: 0% 50%; }
}

/* Floating particles animation */
.page-bg::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="25" cy="25" r="2" fill="rgba(255,255,255,0.1)"/><circle cx="75" cy="75" r="1.5" fill="rgba(255,255,255,0.15)"/><circle cx="50" cy="10" r="1" fill="rgba(255,255,255,0.2)"/><circle cx="10" cy="90" r="2.5" fill="rgba(255,255,255,0.08)"/></svg>') repeat;
    animation: float 20s linear infinite;
}

@keyframes float {
    0% { transform: translateY(0px) rotate(0deg); }
    100% { transform: translateY(-100vh) rotate(360deg); }
}

/* Glassmorphism container */
.main-container {
    background: rgba(255, 255, 255, 0.15);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.2);
    border-radius: 24px;
    box-shadow: 
        0 25px 50px -12px rgba(0, 0, 0, 0.25),
        0 0 0 1px rgba(255, 255, 255, 0.1);
    padding: 50px;
    text-align: center;
    animation: slideInScale 0.8s cubic-bezier(0.34, 1.56, 0.64, 1);
    max-width: 650px;
    width: 90%;
    position: relative;
    z-index: 10;
}

@keyframes slideInScale {
    0% { 
        opacity: 0; 
        transform: translateY(50px) scale(0.8); 
    }
    100% { 
        opacity: 1; 
        transform: translateY(0) scale(1); 
    }
}

/* Animated title with gradient */
.page-title {
    background: linear-gradient(135deg, #fff 0%, #ffe8e8 50%, #ffeaea 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
    font-weight: 900;
    font-size: 2.5rem;
    margin-bottom: 30px;
    animation: titleGlow 2s ease-in-out infinite alternate;
    text-shadow: 0 0 30px rgba(255, 255, 255, 0.3);
}

@keyframes titleGlow {
    0% { text-shadow: 0 0 20px rgba(255, 255, 255, 0.3); }
    100% { text-shadow: 0 0 40px rgba(255, 255, 255, 0.6); }
}

/* Modern rotating icon with multiple animations */
.icon-container {
    position: relative;
    display: inline-block;
    margin-bottom: 30px;
}

.icon-large {
    font-size: 6rem;
    color:rgb(248, 248, 248);
    animation: 
        iconFloat 3s ease-in-out infinite,
        iconGlow 2s ease-in-out infinite alternate;
    filter: drop-shadow(0 0 20px rgba(211, 47, 47, 0.6));
    position: relative;
    z-index: 2;
}

@keyframes iconFloat {
    0%, 100% { transform: translateY(0px) rotate(0deg); }
    25% { transform: translateY(-10px) rotate(90deg); }
    50% { transform: translateY(0px) rotate(180deg); }
    75% { transform: translateY(-5px) rotate(270deg); }
}

@keyframes iconGlow {
    0% { 
        color:rgb(255, 255, 255);
        filter: drop-shadow(0 0 20px rgba(211, 47, 47, 0.6));
    }
    100% { 
        color:rgb(255, 255, 255);
        filter: drop-shadow(0 0 40px rgba(239, 68, 68, 0.8));
    }
}

/* Animated background circle for icon */
.icon-container::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 150px;
    height: 150px;
    background: radial-gradient(circle, rgba(211, 47, 47, 0.1) 0%, transparent 70%);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    animation: pulse 2s ease-in-out infinite;
    z-index: 1;
}

@keyframes pulse {
    0% { transform: translate(-50%, -50%) scale(0.8); opacity: 0.3; }
    50% { transform: translate(-50%, -50%) scale(1.2); opacity: 0.1; }
    100% { transform: translate(-50%, -50%) scale(0.8); opacity: 0.3; }
}

/* Modern text styling */
.content-text {
    color: rgba(255, 255, 255, 0.9);
    font-size: 1.1rem;
    line-height: 1.6;
    margin-bottom: 20px;
    animation: fadeInUp 1s ease-out 0.3s both;
}

.formation-title {
    color:rgb(254, 254, 254);
    font-weight: 700;
    text-shadow: 0 0 10px rgba(239, 68, 68, 0.3);
}

@keyframes fadeInUp {
    0% { opacity: 0; transform: translateY(20px); }
    100% { opacity: 1; transform: translateY(0); }
}

/* Modern button with hover effects */
.btn-return {
    background: linear-gradient(135deg, #D32F2F 0%, #C2185B 100%);
    border: none;
    border-radius: 50px;
    color: white;
    font-weight: 700;
    padding: 16px 32px;
    font-size: 16px;
    text-transform: uppercase;
    letter-spacing: 1px;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    margin-top: 30px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 10px 30px rgba(211, 47, 47, 0.3);
    animation: fadeInUp 1s ease-out 0.6s both;
}

.btn-return::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-return:hover::before {
    left: 100%;
}

.btn-return:hover {
    transform: translateY(-3px) scale(1.05);
    box-shadow: 0 20px 40px rgba(211, 47, 47, 0.5);
    color: white;
}

.btn-return:active {
    transform: translateY(-1px) scale(1.02);
}

/* Progress dots animation */
.progress-dots {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin: 30px 0;
    animation: fadeInUp 1s ease-out 0.4s both;
}

.dot {
    width: 12px;
    height: 12px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.3);
    animation: dotPulse 1.5s ease-in-out infinite;
}

.dot:nth-child(1) { animation-delay: 0s; }
.dot:nth-child(2) { animation-delay: 0.5s; }
.dot:nth-child(3) { animation-delay: 1s; }

@keyframes dotPulse {
    0%, 100% { 
        background: rgba(255, 255, 255, 0.3);
        transform: scale(1);
    }
    50% { 
        background: rgba(255, 255, 255, 0.8);
        transform: scale(1.3);
    }
}

/* Mobile responsiveness */
@media (max-width: 768px) {
    .main-container {
        padding: 30px 20px;
        margin: 20px;
    }
    
    .page-title {
        font-size: 2rem;
    }
    
    .icon-large {
        font-size: 4rem;
    }
    
    .content-text {
        font-size: 1rem;
    }
}

/* Additional decorative elements */
.decorative-element {
    position: absolute;
    width: 60px;
    height: 60px;
    border: 2px solid rgba(255, 255, 255, 0.1);
    border-radius: 50%;
    animation: rotate 20s linear infinite;
}

.decorative-element:nth-child(1) {
    top: 10%;
    left: 10%;
    animation-delay: -5s;
}

.decorative-element:nth-child(2) {
    top: 20%;
    right: 15%;
    animation-delay: -10s;
}

.decorative-element:nth-child(3) {
    bottom: 15%;
    left: 20%;
    animation-delay: -15s;
}

@keyframes rotate {
    0% { transform: rotate(0deg) scale(1); }
    50% { transform: rotate(180deg) scale(1.2); }
    100% { transform: rotate(360deg) scale(1); }
}
</style>

<div class="page-bg">
    <!-- Decorative elements -->
    <div class="decorative-element"></div>
    <div class="decorative-element"></div>
    <div class="decorative-element"></div>
    
    <div class="main-container">
        <div class="icon-container">
            <i class="fas fa-clock icon-large"></i>
        </div>
        
        <h1 class="page-title">{{ __('Inscription en Attente') }}</h1>
        
        <div class="progress-dots">
            <div class="dot"></div>
            <div class="dot"></div>
            <div class="dot"></div>
        </div>
        
        <p class="content-text">
            {{ __('Merci pour votre inscription à la formation :') }}
            <span class="formation-title">{{ $formationTitle ?? 'N/A' }}</span>
        </p>
        
        <p class="content-text">
            {{ __('Votre demande est actuellement en attente de validation par un administrateur.') }}
            {{ __('Une fois votre inscription approuvée, vous recevrez une notification et aurez accès complet à la formation.') }}
        </p>
        
        {{-- ✨ هنا هو التعديل: نضيف رسالة توضيحية للـEtudiant ✨ --}}
        <p class="content-text">
            {{ __('Une fois que votre inscription sera acceptée, vous recevrez une notification par e-mail.') }}
        </p>

        {{-- نتحقق مما إذا كانت الحالة 'active' قبل عرض الزر --}}
        @if ($status && $status === 'active')
        <p class="content-text">
            {{ __('Veuillez consulter votre tableau de bord où vous trouverez toutes vos inscriptions, qu\'elles soient actives ou en attente de validation.') }}
        </p>
        
        <a href="{{ route('inscriptions.index') }}" class="btn btn-return">
            <i class="fas fa-tachometer-alt me-2"></i>
            {{ __('Aller à Mes Inscriptions') }}
        </a>
        @endif
    </div>
</div>
@endsection
