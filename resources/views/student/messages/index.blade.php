@extends('layouts.app')

@section('title', 'Mes Messages')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap');
    
    * {
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    @keyframes slideInMessage {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    @keyframes slideInDetail {
        from {
            opacity: 0;
            transform: translateX(20px);
        }
        to {
            opacity: 1;
            transform: translateX(0);
        }
    }

    @keyframes bounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes wave {
        0%, 100% { transform: rotate(0deg); }
        25% { transform: rotate(10deg); }
        75% { transform: rotate(-10deg); }
    }

    body {
        background-color: #0a0a0a;
        background-image: 
            radial-gradient(circle at 20% 50%, rgba(194, 24, 91, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(211, 47, 47, 0.1) 0%, transparent 50%);
    }

    .messages-wrapper {
        display: flex;
        gap: 0;
        max-width: 1600px;
        margin: 0 auto;
        height: calc(100vh - 80px);
    }

    .whatsapp-container {
        background: #111;
        border-radius: 20px 0 0 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(194, 24, 91, 0.3);
        flex: 0 0 420px;
        display: flex;
        flex-direction: column;
    }

    .detail-panel {
        background: #0d0d0d;
        border-radius: 0 20px 20px 0;
        overflow: hidden;
        flex: 1;
        border-left: 1px solid #2a2a2a;
        display: none;
        flex-direction: column;
        animation: slideInDetail 0.3s ease-out;
    }

    .detail-panel.active {
        display: flex;
    }

    .whatsapp-header {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        flex-shrink: 0;
    }

    .chat-list {
        background: #0d0d0d;
        overflow-y: auto;
        flex: 1;
    }

    .chat-item {
        background: #1a1a1a;
        border-bottom: 1px solid #2a2a2a;
        padding: 16px 20px;
        display: flex;
        gap: 16px;
        cursor: pointer;
        transition: all 0.3s ease;
        animation: slideInMessage 0.4s ease-out;
    }

    .chat-item:hover {
        background: #252525;
    }

    .chat-item.active {
        background: #2a2a2a;
        border-left: 4px solid #C2185B;
    }

    .chat-item.unread {
        background: #1f1f1f;
    }

    .avatar {
        width: 56px;
        height: 56px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        font-size: 24px;
        font-weight: 600;
        color: white;
        position: relative;
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .avatar.unread {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        animation: bounce 2s infinite;
    }

    .avatar.read {
        background: linear-gradient(135deg, #4a4a4a 0%, #2a2a2a 100%);
    }

    .online-dot {
        position: absolute;
        bottom: 2px;
        right: 2px;
        width: 14px;
        height: 14px;
        background: #25D366;
        border: 3px solid #111;
        border-radius: 50%;
        animation: pulse 2s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; }
        50% { opacity: 0.5; }
    }

    .chat-content {
        flex: 1;
        min-width: 0;
    }

    .chat-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    .sender-name {
        font-weight: 600;
        font-size: 17px;
        color: #e9e9e9;
    }

    .chat-item.unread .sender-name {
        color: #fff;
    }

    .time {
        font-size: 13px;
        color: #8696a0;
    }

    .chat-item.unread .time {
        color: #C2185B;
        font-weight: 600;
    }

    .message-preview {
        color: #8696a0;
        font-size: 15px;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .chat-item.unread .message-preview {
        color: #d1d7db;
        font-weight: 500;
    }

    .badge {
        min-width: 24px;
        height: 24px;
        background: #C2185B;
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 12px;
        font-weight: 700;
        padding: 0 8px;
        box-shadow: 0 2px 8px rgba(194, 24, 91, 0.4);
    }

    .urgent-badge {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        padding: 4px 12px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        animation: bounce 1.5s infinite;
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.5);
    }

    .message-view {
        background: #0d0d0d;
        overflow-y: auto;
        flex: 1;
    }

    .message-header {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        padding: 16px 20px;
        display: flex;
        align-items: center;
        gap: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.3);
        flex-shrink: 0;
    }

    .message-body {
        padding: 24px;
    }

    .message-bubble {
        background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
        border-radius: 12px;
        padding: 16px 20px;
        margin-bottom: 16px;
        border-left: 4px solid #C2185B;
        animation: slideInMessage 0.5s ease-out;
        box-shadow: 0 4px 16px rgba(0,0,0,0.3);
    }

    .message-bubble.from-sender {
        background: linear-gradient(135deg, #2a2a2a 0%, #1f1f1f 100%);
        border-left: 4px solid #D32F2F;
    }

    .message-text {
        color: #e9e9e9;
        line-height: 1.6;
        font-size: 15px;
        word-wrap: break-word;
    }

    .message-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-top: 8px;
        font-size: 13px;
        color: #8696a0;
    }

    .audio-message {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        animation: slideInMessage 0.6s ease-out;
        box-shadow: 0 8px 24px rgba(194, 24, 91, 0.4);
    }

    .audio-header {
        display: flex;
        align-items: center;
        gap: 16px;
        margin-bottom: 20px;
    }

    .audio-icon {
        width: 64px;
        height: 64px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 28px;
        color: white;
        backdrop-filter: blur(10px);
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
        animation: wave 3s infinite;
    }

    .audio-info h3 {
        color: white;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 4px;
    }

    .audio-info p {
        color: rgba(255,255,255,0.8);
        font-size: 14px;
    }

    .audio-player-container {
        background: rgba(255,255,255,0.95);
        border-radius: 12px;
        padding: 16px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.2);
    }

    audio {
        width: 100%;
        height: 48px;
        border-radius: 8px;
    }

    audio::-webkit-media-controls-panel {
        background-color: #f5f5f5;
    }

    .audio-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: 16px;
    }

    .audio-label {
        color: rgba(255,255,255,0.9);
        font-size: 13px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .play-count {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        padding: 8px 16px;
        border-radius: 20px;
        color: white;
        font-size: 13px;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .formation-card {
        background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
        border-radius: 16px;
        padding: 20px;
        border: 2px solid #C2185B;
        animation: slideInMessage 0.7s ease-out;
        box-shadow: 0 4px 16px rgba(194, 24, 91, 0.2);
    }

    .formation-card h4 {
        color: #8696a0;
        font-size: 14px;
        margin-bottom: 12px;
        font-weight: 500;
    }

    .formation-info {
        display: flex;
        align-items: center;
        gap: 12px;
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        padding: 16px 20px;
        border-radius: 12px;
        color: white;
        box-shadow: 0 4px 12px rgba(194, 24, 91, 0.3);
    }

    .formation-icon {
        font-size: 24px;
    }

    .formation-title {
        font-weight: 700;
        font-size: 16px;
    }

    .empty-state {
        text-align: center;
        padding: 80px 20px;
        color: #8696a0;
    }

    .empty-icon {
        font-size: 80px;
        margin-bottom: 20px;
        color: #3a3a3a;
    }

    .empty-state h3 {
        color: #e9e9e9;
        font-size: 24px;
        margin-bottom: 8px;
    }

    .empty-state p {
        font-size: 16px;
    }

    .checkmark {
        color: #53bdeb;
        font-size: 16px;
        margin-right: 4px;
    }

    .checkmark.read {
        color: #53bdeb;
    }

    .empty-detail {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        height: 100%;
        color: #8696a0;
        padding: 40px;
        text-align: center;
        background: linear-gradient(135deg, #0d0d0d 0%, #1a1a1a 100%);
    }

    .empty-detail i {
        font-size: 100px;
        color: #2a2a2a;
        margin-bottom: 24px;
        animation: float 3s ease-in-out infinite;
    }

    @keyframes float {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }

    .empty-detail h3 {
        color: #e9e9e9;
        font-size: 24px;
        margin-bottom: 8px;
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .empty-detail p {
        color: #8696a0;
        font-size: 16px;
        max-width: 400px;
        margin: 0 auto;
        line-height: 1.6;
    }

    .welcome-badge {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: rgba(194, 24, 91, 0.1);
        border: 1px solid rgba(194, 24, 91, 0.3);
        padding: 12px 24px;
        border-radius: 24px;
        margin-top: 24px;
        color: #C2185B;
        font-size: 14px;
        font-weight: 600;
    }

    .welcome-badge i {
        font-size: 20px !important;
        color: #C2185B;
        margin: 0 !important;
        animation: pulse 2s infinite;
    }

    /* Scrollbar */
    .chat-list::-webkit-scrollbar,
    .message-view::-webkit-scrollbar {
        width: 6px;
    }

    .chat-list::-webkit-scrollbar-track,
    .message-view::-webkit-scrollbar-track {
        background: #0d0d0d;
    }

    .chat-list::-webkit-scrollbar-thumb,
    .message-view::-webkit-scrollbar-thumb {
        background: #3a3a3a;
        border-radius: 3px;
    }

    .chat-list::-webkit-scrollbar-thumb:hover,
    .message-view::-webkit-scrollbar-thumb:hover {
        background: #4a4a4a;
    }

    @media (max-width: 768px) {
        .messages-wrapper {
            flex-direction: column;
            height: auto;
        }
        
        .whatsapp-container {
            flex: 1;
            border-radius: 20px 20px 0 0;
        }
        
        .detail-panel {
            border-radius: 0 0 20px 20px;
            border-left: none;
            border-top: 1px solid #2a2a2a;
        }
    }
     .chat-list {
    width: 418px !important;
     }
</style>

<div class="min-h-screen py-6 px-4">
    <div class="messages-wrapper">
        {{-- Liste des messages --}}
        <div class="whatsapp-container">
            <div class="whatsapp-header">
                <div class="avatar" style="background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);">
                    <i class="fas fa-graduation-cap"></i>
                </div>
                <div style="flex: 1;">
                    <h1 style="color: white; font-size: 20px; font-weight: 700; margin-bottom: 2px;">
                        Messages Formation
                    </h1>
                    <p style="color: rgba(255,255,255,0.8); font-size: 14px;">
                        {{ $messages->total() }} conversations
                    </p>
                </div>
            </div>

            @if(session('success'))
                <div style="background: #25D366; color: white; padding: 12px 20px; display: flex; align-items: center; gap: 12px; animation: slideInMessage 0.5s ease-out;">
                    <i class="fas fa-check-circle" style="font-size: 20px;"></i>
                    <span style="font-weight: 500;">{{ session('success') }}</span>
                </div>
            @endif

            <div class="chat-list">
                @forelse($messages as $index => $msg)
                    @php
                        $receipt = $msg->recipientRecords()->where('user_id', Auth::id())->first();
                        $isUnread = $receipt && !$receipt->is_read;
                        $initials = strtoupper(substr($msg->sender->name ?? 'A', 0, 1));
                    @endphp
                    
                    <div class="chat-item {{ $isUnread ? 'unread' : '' }}" 
                         data-message-id="{{ $msg->id }}"
                         style="animation-delay: {{ $index * 0.05 }}s;">
                        
                        <div class="avatar {{ $isUnread ? 'unread' : 'read' }}">
                            {{ $initials }}
                            @if($isUnread)
                                <div class="online-dot"></div>
                            @endif
                        </div>

                        <div class="chat-content">
                            <div class="chat-header">
                                <div class="sender-name">
                                    {{ $msg->sender->name ?? 'Administrateur' }}
                                </div>
                                <div class="time">
                                    {{ $msg->created_at->format('H:i') }}
                                </div>
                            </div>

                            <div style="display: flex; align-items: center; gap: 8px;">
                                <div class="message-preview" style="flex: 1;">
                                    @if(!$isUnread)
                                        <i class="fas fa-check-double checkmark read"></i>
                                    @endif
                                    
                                    @if($msg->audio_path)
                                        <i class="fas fa-microphone" style="color: #C2185B;"></i>
                                    @endif
                                    
                                    <span>{{ $msg->subject }}</span>
                                </div>

                                @if($msg->priority === 'urgent')
                                    <div class="urgent-badge">
                                        <i class="fas fa-bolt"></i> URGENT
                                    </div>
                                @elseif($msg->priority === 'important')
                                    <div class="badge" style="background: #D32F2F;">
                                        <i class="fas fa-star"></i>
                                    </div>
                                @elseif($isUnread)
                                    <div class="badge">1</div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <div class="empty-icon">
                            <i class="fas fa-comments"></i>
                        </div>
                        <h3>Aucun message</h3>
                        <p>Vos conversations apparaîtront ici</p>
                    </div>
                @endforelse
            </div>

            @if($messages->hasPages())
                <div style="padding: 16px 20px; background: #111; border-top: 1px solid #2a2a2a;">
                    {{ $messages->links() }}
                </div>
            @endif
        </div>

        {{-- Panneau de détail --}}
        <div class="detail-panel" id="detailPanel">
            <div class="empty-detail">
                <i class="fas fa-envelope-open-text"></i>
                <h3>Bienvenue dans vos messages</h3>
                <p>Sélectionnez une conversation à gauche pour voir son contenu complet avec tous les détails</p>
                <div class="welcome-badge">
                    <i class="fas fa-arrow-left"></i>
                    <span>Choisissez un message</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatItems = document.querySelectorAll('.chat-item');
    const detailPanel = document.getElementById('detailPanel');
    let currentAudio = null;
    
    // ✨ AUTO-LOAD: Charger automatiquement le premier message non lu (ou le premier message)
    if (chatItems.length > 0) {
        // Chercher le premier message non lu
        const firstUnread = document.querySelector('.chat-item.unread');
        const firstMessage = firstUnread || chatItems[0];
        
        if (firstMessage) {
            firstMessage.classList.add('active');
            const messageId = firstMessage.getAttribute('data-message-id');
            loadMessageDetail(messageId);
        }
    }
    
    chatItems.forEach(item => {
        item.addEventListener('click', function() {
            const messageId = this.getAttribute('data-message-id');
            
            // Marquer comme actif
            chatItems.forEach(ci => ci.classList.remove('active'));
            this.classList.add('active');
            
            // Charger le détail
            loadMessageDetail(messageId);
        });
    });
    
    function loadMessageDetail(messageId) {
        detailPanel.classList.add('active');
        detailPanel.innerHTML = '<div style="padding: 40px; text-align: center; color: #8696a0;"><i class="fas fa-spinner fa-spin" style="font-size: 40px;"></i><br><br>Chargement...</div>';
        
        console.log('Loading message:', messageId);
        
        fetch(`/message/${messageId}/content`)
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response headers:', response.headers);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Data received:', data);
                detailPanel.innerHTML = buildMessageDetail(data);
                initializeAudioPlayer();
            })
            .catch(error => {
                console.error('Erreur complète:', error);
                detailPanel.innerHTML = `
                    <div class="empty-detail">
                        <i class="fas fa-exclamation-triangle"></i>
                        <h3>Erreur</h3>
                        <p>Impossible de charger le message</p>
                        <small style="color: #555; margin-top: 10px;">${error.message}</small>
                    </div>`;
            });
    }
    
    function buildMessageDetail(data) {
        let html = '<div class="message-view"><div class="message-header">';
        
        // Header
        html += `
            <div class="avatar" style="background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);">
                ${data.sender_initial}
                <div class="online-dot"></div>
            </div>
            <div style="flex: 1;">
                <div style="color: white; font-size: 18px; font-weight: 600;">
                    ${data.sender_name}
                </div>
                <div style="color: rgba(255,255,255,0.8); font-size: 13px;">
                    ${data.created_at}
                </div>
            </div>`;
        
        if (data.priority === 'urgent') {
            html += '<div class="urgent-badge"><i class="fas fa-bolt"></i> URGENT</div>';
        }
        
        html += '</div><div class="message-body">';
        
        // Audio Message
        if (data.audio_path) {
            html += `
                <div class="audio-message">
                    <div class="audio-header">
                        <div class="audio-icon">
                            <i class="fas fa-headphones"></i>
                        </div>
                        <div class="audio-info">
                            <h3>Message Vocal</h3>
                            <p>🎵 ${data.audio_duration ? 'Durée: ' + data.audio_duration : 'Appuyez pour écouter'}</p>
                        </div>
                    </div>
                    <div class="audio-player-container">
                        <audio id="audioPlayer" controls>
                            <source src="${data.audio_url}" type="audio/webm">
                            <source src="${data.audio_url}" type="audio/mpeg">
                            <source src="${data.audio_url}" type="audio/wav">
                            <source src="${data.audio_url}" type="audio/ogg">
                            Votre navigateur ne supporte pas la lecture audio.
                        </audio>
                    </div>
                    <div class="audio-footer">
                        <div class="audio-label">
                            <i class="fas fa-microphone"></i>
                            <span>Message de votre formateur</span>
                        </div>
                        <div class="play-count" id="playCount">
                            <i class="fas fa-play-circle"></i>
                            <span>Non écouté</span>
                        </div>
                    </div>
                </div>`;
        }
        
        // Text Message
        if (data.message) {
            html += `
                <div class="message-bubble from-sender">
                    <div class="message-text">${data.message}</div>
                    <div class="message-meta">
                        <span>${data.time}</span>
                        <i class="fas fa-check-double checkmark read"></i>
                    </div>
                </div>`;
        }
        
        // Subject
        html += `
            <div class="message-bubble">
                <div style="color: #8696a0; font-size: 13px; margin-bottom: 8px; font-weight: 600;">
                    📌 SUJET
                </div>
                <div class="message-text" style="font-weight: 600; font-size: 17px;">
                    ${data.subject}
                </div>
            </div>`;
        
        // Formation
        if (data.formation) {
            html += `
                <div class="formation-card">
                    <h4>
                        <i class="fas fa-link"></i> Formation concernée
                    </h4>
                    <div class="formation-info">
                        <div class="formation-icon">
                            <i class="fas fa-book-open"></i>
                        </div>
                        <div class="formation-title">
                            ${data.formation}
                        </div>
                    </div>
                </div>`;
        }
        
        html += '</div></div>';
        return html;
    }
    
    function initializeAudioPlayer() {
        const audioPlayer = detailPanel.querySelector('audio');
        
        if (audioPlayer) {
            // Arrêter l'audio précédent si existe
            if (currentAudio && currentAudio !== audioPlayer) {
                currentAudio.pause();
            }
            currentAudio = audioPlayer;
            
            let playCount = 0;
            const playCountElement = detailPanel.querySelector('#playCount span, .play-count span');
            
            audioPlayer.addEventListener('play', function() {
                playCount++;
                if (playCountElement) {
                    if (playCount === 1) {
                        playCountElement.textContent = '1ère écoute 🎧';
                    } else {
                        playCountElement.textContent = `${playCount}x écouté ✓`;
                    }
                }
            });
            
            audioPlayer.addEventListener('contextmenu', function(e) {
                e.preventDefault();
                return false;
            });
        }
    }
});
</script>

@endsection