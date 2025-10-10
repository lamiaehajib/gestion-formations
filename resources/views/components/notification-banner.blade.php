{{-- resources/views/components/notification-banner.blade.php (Version B'lbyad w Icons zwinin) --}}
<div id="notification-banner" class="bg-white text-red-600 shadow-2xl relative overflow-hidden border-b-4 border-red-500" style="display: none;">
    <div class="container mx-auto px-4 py-3 flex items-center justify-between">
        
        {{-- Icône animée b'<i> --}}
        <div class="flex-shrink-0">
            {{-- Ghnst3mlo chi class icon dyal chi library bhal Font Awesome, matalan "fa-bell" --}}
            <i class="fa fa-bell fa-2x animate-pulse text-red-500" aria-hidden="true"></i> 
        </div>

        {{-- Messages défilants --}}
        <div class="flex-1 mx-4 overflow-hidden">
            <div id="banner-messages" class="whitespace-nowrap animate-marquee">
                <span class="inline-block px-4 font-semibold">Chargement des notifications...</span>
            </div>
        </div>

        {{-- Bouton fermer b'<i> --}}
        <button onclick="closeBanner()" class="flex-shrink-0 text-red-600 hover:text-red-800 hover:bg-red-50 rounded-full p-2 transition">
            {{-- Ghnst3mlo chi class icon dyal chi library bhal Font Awesome, matalan "fa-times" --}}
            <i class="fa fa-times fa-lg" aria-hidden="true"></i>
        </button>
    </div>

    {{-- Barre de progression --}}
    <div class="absolute bottom-0 left-0 h-1 bg-red-500" id="progress-bar"></div>
</div>

<style>
/* Force red color for all links in banner - Override any other styles */
#notification-banner a {
    color: #dc2626 !important; /* Red color */
    text-decoration: none !important;
}

#notification-banner a:hover {
    color: #991b1b !important; /* Darker red on hover */
    text-decoration: underline !important;
}

/* Force red color for all text in banner */
#notification-banner,
#notification-banner * {
    color: #dc2626 !important;
}

/* Exceptions for icons that need specific red shades */
#notification-banner .text-red-500 {
    color: #ef4444 !important;
}

#notification-banner .text-red-400 {
    color: #f87171 !important;
}

/* CSS bach ykhdmo les animations */
@keyframes marquee {
    0% { transform: translateX(100%); }
    100% { transform: translateX(-100%); }
}

.animate-marquee {
    display: inline-block;
    animation: marquee 30s linear infinite;
}

.animate-marquee:hover {
    animation-play-state: paused;
}

.animate-pulse {
    animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: .5; }
}

#progress-bar {
    animation: progress 30s linear infinite;
}

@keyframes progress {
    0% { width: 0%; }
    100% { width: 100%; }
}
</style>

<script>
let notificationInterval;
let currentNotifications = [];

// Charger les notifications au démarrage
document.addEventListener('DOMContentLoaded', function() {
    // Check if banner was closed recently
    const closedTime = localStorage.getItem('bannerClosed');
    if (!closedTime || (Date.now() - closedTime > 600000)) { // 10 min
        loadNotifications();
    }
    
    // Recharger toutes les 2 minutes
    notificationInterval = setInterval(loadNotifications, 120000);
});

async function loadNotifications() {
    try {
        const response = await fetch('/api/notification-banner/recent');
        const data = await response.json();
        
        if (data.success && data.notifications.length > 0) {
            currentNotifications = data.notifications;
            displayNotifications();
            showBanner();
        } else {
            hideBanner();
        }
    } catch (error) {
        console.error('Erreur chargement notifications:', error);
    }
}

function displayNotifications() {
    const container = document.getElementById('banner-messages');
    
    if (currentNotifications.length === 0) {
        container.innerHTML = '<span class="inline-block px-4 font-semibold">Aucune notification récente</span>';
        return;
    }
    
    // Créer le HTML des notifications
    const messages = currentNotifications.map(notif => {
        // Ghnst3mlo l'icon li jay f data b'<i> w nzidoha chi style dyal taille (text-xl)
        const iconHtml = notif.icon ? `<i class="${notif.icon} text-xl text-red-500" aria-hidden="true"></i>` : ''; 
        
        return `
            <a href="${notif.link}" 
                class="inline-flex items-center px-6 space-x-2">
                ${iconHtml}
                <span class="font-semibold">${notif.message}</span>
                
            </a>
            <span class="inline-block px-4 text-red-400">•</span>
        `;
    }).join('');
    
    // Dupliquer pour effet continu
    container.innerHTML = messages + messages;
}

function formatRelativeTime(dateString) {
    const date = new Date(dateString);
    const now = new Date();
    const diffMs = now - date;
    const diffMins = Math.floor(diffMs / 60000);
    const diffHours = Math.floor(diffMs / 3600000);
    const diffDays = Math.floor(diffMs / 86400000);
    
    if (diffMins < 1) return 'À l\'instant';
    if (diffMins < 60) return `Il y a ${diffMins} min`;
    if (diffHours < 24) return `Il y a ${diffHours}h`;
    return `Il y a ${diffDays}j`;
}

function showBanner() {
    document.getElementById('notification-banner').style.display = 'block';
}

function hideBanner() {
    document.getElementById('notification-banner').style.display = 'none';
}

function closeBanner() {
    hideBanner();
    // Sauvegarder dans localStorage pour ne pas ré-afficher dans cette session
    localStorage.setItem('bannerClosed', Date.now());
    clearInterval(notificationInterval);
}

// Réafficher le bandeau après 10 minutes si fermé
setInterval(() => {
    const closedTime = localStorage.getItem('bannerClosed');
    // 600000 milliseconds = 10 minutes
    if (closedTime && Date.now() - closedTime > 600000) { 
        localStorage.removeItem('bannerClosed');
        loadNotifications();
    }
}, 60000); // Check every minute
</script>