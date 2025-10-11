@extends('layouts.app')

@section('title', 'Gestion des Messages')

@section('content')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Segoe+UI:wght@400;500;600;700&display=swap');
    
    * {
        font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
    }

    @keyframes slideInMessage {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }

    @keyframes bounce {
        0%, 100% { transform: scale(1); }
        50% { transform: scale(1.05); }
    }

    @keyframes fadeIn {
        from { opacity: 0; }
        to { opacity: 1; }
    }

    @keyframes slideUp {
        from { transform: translateY(50px); opacity: 0; }
        to { transform: translateY(0); opacity: 1; }
    }

    body {
        background-color: #0a0a0a;
        background-image: 
            radial-gradient(circle at 20% 50%, rgba(194, 24, 91, 0.1) 0%, transparent 50%),
            radial-gradient(circle at 80% 80%, rgba(211, 47, 47, 0.1) 0%, transparent 50%);
    }

    .admin-wrapper {
        max-width: 1800px;
        margin: 0 auto;
        padding: 20px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 20px;
        margin-bottom: 24px;
    }

    .stat-card {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        border-radius: 16px;
        padding: 24px;
        border: 1px solid #3a3a3a;
        animation: slideInMessage 0.5s ease-out;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .stat-card.primary::before { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
    .stat-card.success::before { background: linear-gradient(135deg, #10b981, #059669); }
    .stat-card.warning::before { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .stat-card.info::before { background: linear-gradient(135deg, #3b82f6, #2563eb); }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        margin-bottom: 16px;
    }

    .stat-card.primary .stat-icon { background: rgba(99, 102, 241, 0.1); color: #6366f1; }
    .stat-card.success .stat-icon { background: rgba(16, 185, 129, 0.1); color: #10b981; }
    .stat-card.warning .stat-icon { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
    .stat-card.info .stat-icon { background: rgba(59, 130, 246, 0.1); color: #3b82f6; }

    .stat-label {
        color: #8696a0;
        font-size: 13px;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 8px;
    }

    .stat-value {
        color: #e9e9e9;
        font-size: 32px;
        font-weight: 700;
    }

    .messages-wrapper {
        display: flex;
        gap: 0;
        height: calc(100vh - 280px);
        min-height: 600px;
    }

    .messages-list-panel {
        background: #111;
        border-radius: 20px 0 0 20px;
        overflow: hidden;
        box-shadow: 0 20px 60px rgba(194, 24, 91, 0.3);
        flex: 0 0 450px;
        display: flex;
        flex-direction: column;
    }

    .detail-panel {
        background: #0d0d0d;
        border-radius: 0 20px 20px 0;
        overflow: hidden;
        flex: 1;
        border-left: 1px solid #2a2a2a;
        display: flex;
        flex-direction: column;
    }

    .panel-header {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        padding: 20px 24px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        flex-shrink: 0;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 16px;
        flex: 1;
    }

    .header-avatar {
        width: 56px;
        height: 56px;
        background: rgba(255,255,255,0.2);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 24px;
        color: white;
        backdrop-filter: blur(10px);
    }

    .header-info h1 {
        color: white;
        font-size: 20px;
        font-weight: 700;
        margin-bottom: 2px;
    }

    .header-info p {
        color: rgba(255,255,255,0.8);
        font-size: 14px;
    }

    .new-message-btn {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        border: 2px solid rgba(255,255,255,0.3);
        cursor: pointer;
    }

    .new-message-btn:hover {
        background: rgba(255,255,255,0.3);
        transform: scale(1.05);
        color: white;
    }

    .messages-list {
        background: #0d0d0d;
        overflow-y: auto;
        flex: 1;
    }

    .message-item {
        background: #1a1a1a;
        border-bottom: 1px solid #2a2a2a;
        padding: 16px 20px;
        cursor: pointer;
        transition: all 0.3s ease;
        animation: slideInMessage 0.4s ease-out;
        display: flex;
        align-items: center;
        gap: 16px;
    }

    .message-item:hover {
        background: #252525;
    }

    .message-item.active {
        background: #2a2a2a;
        border-left: 4px solid #C2185B;
    }

    .message-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 20px;
        font-weight: 600;
        color: white;
        flex-shrink: 0;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
    }

    .message-content {
        flex: 1;
        min-width: 0;
    }

    .message-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 4px;
    }

    .message-subject {
        font-weight: 600;
        font-size: 15px;
        color: #e9e9e9;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .message-time {
        font-size: 12px;
        color: #8696a0;
        white-space: nowrap;
    }

    .message-meta {
        display: flex;
        align-items: center;
        gap: 12px;
        font-size: 13px;
        color: #8696a0;
    }

    .priority-badge {
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 11px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .priority-urgent {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        animation: bounce 1.5s infinite;
    }

    .priority-important {
        background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%);
        color: white;
    }

    .priority-normal {
        background: rgba(59, 130, 246, 0.2);
        color: #3b82f6;
    }

    .message-stats {
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .stat-badge {
        display: flex;
        align-items: center;
        gap: 4px;
        padding: 4px 8px;
        background: rgba(99, 102, 241, 0.1);
        border-radius: 8px;
        font-size: 12px;
        color: #6366f1;
    }

    .detail-content {
        flex: 1;
        overflow-y: auto;
        padding: 24px;
    }

    .detail-header {
        background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        border: 1px solid #3a3a3a;
    }

    .detail-title {
        color: #e9e9e9;
        font-size: 24px;
        font-weight: 700;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 12px;
        flex-wrap: wrap;
    }

    .audio-section {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
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
    }

    audio {
        width: 100%;
        height: 48px;
        border-radius: 8px;
    }

    .message-text-section {
        background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
        border-radius: 16px;
        padding: 24px;
        margin-bottom: 24px;
        border-left: 4px solid #C2185B;
    }

    .section-title {
        color: #8696a0;
        font-size: 13px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 12px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .message-text {
        color: #e9e9e9;
        line-height: 1.6;
        font-size: 15px;
        word-wrap: break-word;
    }

    .recipients-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 16px;
        margin-bottom: 24px;
    }

    .recipient-card {
        background: linear-gradient(135deg, #1f1f1f 0%, #2a2a2a 100%);
        border-radius: 12px;
        padding: 16px;
        border: 1px solid #3a3a3a;
    }

    .recipient-header {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 12px;
    }

    .recipient-avatar {
        width: 48px;
        height: 48px;
        border-radius: 50%;
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        font-weight: 700;
        font-size: 18px;
    }

    .recipient-info h4 {
        color: #e9e9e9;
        font-size: 15px;
        font-weight: 600;
        margin-bottom: 2px;
    }

    .recipient-info p {
        color: #8696a0;
        font-size: 13px;
    }

    .recipient-status {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px solid #3a3a3a;
    }

    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }

    .status-badge.read {
        background: rgba(16, 185, 129, 0.1);
        color: #10b981;
    }

    .status-badge.unread {
        background: rgba(245, 158, 11, 0.1);
        color: #f59e0b;
    }

    .formations-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
        gap: 12px;
        margin-bottom: 24px;
    }

    .formation-badge {
        background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
        color: white;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .formation-count {
        background: rgba(255,255,255,0.2);
        padding: 4px 8px;
        border-radius: 6px;
        font-size: 12px;
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
    }

    .action-buttons {
        display: flex;
        gap: 12px;
        margin-top: 24px;
        flex-wrap: wrap;
    }

    .action-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 12px 20px;
        border-radius: 12px;
        font-weight: 600;
        transition: all 0.3s ease;
        text-decoration: none;
        border: none;
        cursor: pointer;
    }

    .action-btn.primary {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        color: white;
    }

    .action-btn.danger {
        background: linear-gradient(135deg, #ef4444, #dc2626);
        color: white;
    }

    .action-btn:hover {
        transform: scale(1.05);
        box-shadow: 0 8px 24px rgba(99, 102, 241, 0.4);
    }

    /* Modal Styles */
    .modal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(5px);
        z-index: 9998;
        display: none;
        animation: fadeIn 0.3s ease;
    }

    .modal-overlay.active {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }

    .modal-container {
        background: linear-gradient(135deg, #1a1a1a 0%, #2a2a2a 100%);
        border-radius: 24px;
        max-width: 900px;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: 0 25px 50px rgba(194, 24, 91, 0.5);
        border: 1px solid #3a3a3a;
        animation: slideUp 0.3s ease;
    }

    .modal-header {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        padding: 24px;
        border-radius: 24px 24px 0 0;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .modal-header h2 {
        color: white;
        font-size: 24px;
        font-weight: 700;
        margin: 0;
    }

    .modal-close {
        background: rgba(255,255,255,0.2);
        border: none;
        color: white;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.3s ease;
    }

    .modal-close:hover {
        background: rgba(255,255,255,0.3);
        transform: rotate(90deg);
    }

    .modal-body {
        padding: 24px;
    }

    .form-group {
        margin-bottom: 20px;
    }

    .form-label {
        display: block;
        color: #8696a0;
        font-size: 14px;
        font-weight: 600;
        margin-bottom: 8px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-input,
    .form-textarea,
    .form-select {
        width: 100%;
        background: #1a1a1a;
        border: 1px solid #3a3a3a;
        border-radius: 12px;
        padding: 12px 16px;
        color: #e9e9e9;
        font-size: 15px;
        transition: all 0.3s ease;
    }

    .form-input:focus,
    .form-textarea:focus,
    .form-select:focus {
        outline: none;
        border-color: #C2185B;
        box-shadow: 0 0 0 3px rgba(194, 24, 91, 0.1);
    }

    .form-textarea {
        resize: vertical;
        min-height: 120px;
    }

    .tab-buttons {
        display: flex;
        gap: 12px;
        margin-bottom: 16px;
    }

    .tab-btn {
        flex: 1;
        padding: 12px;
        border-radius: 12px;
        background: #1a1a1a;
        border: 2px solid #3a3a3a;
        color: #8696a0;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
    }

    .tab-btn.active {
        background: linear-gradient(135deg, #C2185B 0%, #D32F2F 100%);
        border-color: transparent;
        color: white;
    }

    .tab-content {
        display: none;
    }

    .tab-content.active {
        display: block;
    }

    .audio-controls {
        display: flex;
        flex-wrap: wrap;
        gap: 12px;
        margin-bottom: 16px;
    }

    .btn-audio {
        padding: 10px 16px;
        border-radius: 10px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
        font-size: 14px;
    }

    .btn-audio:hover:not(:disabled) {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.3);
    }

    .btn-audio:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    .btn-record { background: linear-gradient(135deg, #ef4444, #dc2626); color: white; }
    .btn-stop { background: linear-gradient(135deg, #6b7280, #4b5563); color: white; }
    .btn-play { background: linear-gradient(135deg, #10b981, #059669); color: white; }
    .btn-upload { background: linear-gradient(135deg, #6366f1, #8b5cf6); color: white; }

    .formations-list {
        background: #1a1a1a;
        border: 1px solid #3a3a3a;
        border-radius: 12px;
        padding: 16px;
        max-height: 300px;
        overflow-y: auto;
    }

    .formation-item {
        display: flex;
        align-items: center;
        padding: 10px;
        border-radius: 8px;
        margin-bottom: 8px;
        transition: all 0.3s ease;
    }

    .formation-item:hover {
        background: #252525;
    }

    .formation-checkbox {
        width: 18px;
        height: 18px;
        margin-right: 12px;
        cursor: pointer;
    }

    .formation-label {
        flex: 1;
        color: #e9e9e9;
        font-size: 14px;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .formation-count-badge {
        background: rgba(99, 102, 241, 0.2);
        color: #6366f1;
        padding: 4px 10px;
        border-radius: 8px;
        font-size: 12px;
        font-weight: 600;
    }

    .modal-footer {
        padding: 20px 24px;
        border-top: 1px solid #3a3a3a;
        display: flex;
        justify-content: flex-end;
        gap: 12px;
    }

    .btn-submit {
        background: linear-gradient(135deg, #10b981, #059669);
        color: white;
        padding: 12px 24px;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .btn-submit:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.4);
    }

    .btn-cancel {
        background: #2a2a2a;
        color: #e9e9e9;
        padding: 12px 24px;
        border-radius: 12px;
        border: none;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn-cancel:hover {
        background: #3a3a3a;
    }

    .recording-indicator {
        display: none;
        background: #1a1a1a;
        border-radius: 12px;
        padding: 16px;
        margin-bottom: 16px;
        border: 2px solid #ef4444;
    }

    .recording-indicator.active {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .recording-dot {
        width: 12px;
        height: 12px;
        background: #ef4444;
        border-radius: 50%;
        animation: pulse 1s infinite;
    }

    @keyframes pulse {
        0%, 100% { opacity: 1; transform: scale(1); }
        50% { opacity: 0.5; transform: scale(1.1); }
    }

    .recording-time {
        font-family: monospace;
        font-size: 20px;
        font-weight: 700;
        color: #e9e9e9;
    }

    .audio-preview {
        display: none;
        background: #1a1a1a;
        border-radius: 12px;
        padding: 16px;
        margin-top: 16px;
        border: 2px solid #10b981;
    }

    .audio-preview.active {
        display: block;
    }

    .messages-list::-webkit-scrollbar,
    .detail-content::-webkit-scrollbar,
    .modal-container::-webkit-scrollbar,
    .formations-list::-webkit-scrollbar {
        width: 6px;
    }

    .messages-list::-webkit-scrollbar-track,
    .detail-content::-webkit-scrollbar-track,
    .modal-container::-webkit-scrollbar-track,
    .formations-list::-webkit-scrollbar-track {
        background: #0d0d0d;
    }

    .messages-list::-webkit-scrollbar-thumb,
    .detail-content::-webkit-scrollbar-thumb,
    .modal-container::-webkit-scrollbar-thumb,
    .formations-list::-webkit-scrollbar-thumb {
        background: #3a3a3a;
        border-radius: 3px;
    }

    .messages-list::-webkit-scrollbar-thumb:hover,
    .detail-content::-webkit-scrollbar-thumb:hover,
    .modal-container::-webkit-scrollbar-thumb:hover,
    .formations-list::-webkit-scrollbar-thumb:hover {
        background: #4a4a4a;
    }

    .error-message {
        background: rgba(239, 68, 68, 0.1);
        border: 1px solid #ef4444;
        color: #ef4444;
        padding: 12px;
        border-radius: 8px;
        margin-top: 8px;
        font-size: 13px;
    }
</style>

<div class="admin-wrapper">
    <div class="stats-grid">
        <div class="stat-card primary">
            <div class="stat-icon"><i class="fas fa-envelope-open-text"></i></div>
            <div class="stat-label">Messages Envoyés</div>
            <div class="stat-value">{{ $totalMessages ?? 0 }}</div>
        </div>
        <div class="stat-card success">
            <div class="stat-icon"><i class="fas fa-calendar-day"></i></div>
            <div class="stat-label">Aujourd'hui</div>
            <div class="stat-value">{{ $sentToday ?? 0 }}</div>
        </div>
        <div class="stat-card warning">
            <div class="stat-icon"><i class="fas fa-exclamation-triangle"></i></div>
            <div class="stat-label">Messages Urgents</div>
            <div class="stat-value">{{ $urgentMessages ?? 0 }}</div>
        </div>
        <div class="stat-card info">
            <div class="stat-icon"><i class="fas fa-user-graduate"></i></div>
            <div class="stat-label">Étudiants Touchés</div>
            <div class="stat-value">{{ $totalRecipients ?? 0 }}</div>
        </div>
    </div>

    <div class="messages-wrapper">
        <div class="messages-list-panel">
            <div class="panel-header">
                <div class="header-left">
                    <div class="header-avatar"><i class="fas fa-comment-dots"></i></div>
                    <div class="header-info">
                        <h1>Messages</h1>
                        <p>{{ $messages->total() }} conversations</p>
                    </div>
                </div>
                <button onclick="openCreateModal()" class="new-message-btn">
                    <i class="fas fa-plus"></i> Nouveau
                </button>
            </div>

            <div class="messages-list">
                @forelse($messages as $index => $msg)
                    <div class="message-item" data-message-id="{{ $msg->id }}" style="animation-delay: {{ $index * 0.05 }}s;">
                        <div class="message-avatar">{{ strtoupper(substr($msg->sender->name ?? 'A', 0, 1)) }}</div>
                        <div class="message-content">
                            <div class="message-header">
                                <div class="message-subject">{{ $msg->subject }}</div>
                                <div class="message-time">{{ $msg->created_at->format('H:i') }}</div>
                            </div>
                            <div class="message-meta">
                                <span class="priority-badge priority-{{ $msg->priority }}">
                                    @if($msg->priority === 'urgent')<i class="fas fa-bolt"></i>
                                    @elseif($msg->priority === 'important')<i class="fas fa-star"></i>@endif
                                    {{ $msg->priority }}
                                </span>
                                <div class="message-stats">
                                    <span class="stat-badge"><i class="fas fa-users"></i> {{ $msg->recipient_records_count }}</span>
                                    @if($msg->audio_path)
                                        <span class="stat-badge" style="background: rgba(194, 24, 91, 0.1); color: #C2185B;">
                                            <i class="fas fa-microphone"></i>
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="empty-detail">
                        <i class="fas fa-inbox"></i>
                        <h3>Aucun message</h3>
                        <p>Commencez par créer un nouveau message</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="detail-panel" id="detailPanel">
            <div class="empty-detail">
                <i class="fas fa-mouse-pointer"></i>
                <h3>Sélectionnez un message</h3>
                <p>Cliquez sur un message pour voir tous les détails</p>
            </div>
        </div>
    </div>
</div>

{{-- Modal Create Message --}}
<div class="modal-overlay" id="createModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-plus-circle"></i> Nouveau Message</h2>
            <button onclick="closeCreateModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="createForm" action="{{ route('messages.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Sujet du Message *</label>
                    <input type="text" name="subject" class="form-input" placeholder="Ex: Nouvelle formation disponible" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Type de Message</label>
                    <div class="tab-buttons">
                        <button type="button" class="tab-btn active" onclick="switchTab('text', 'create')">
                            <i class="fas fa-keyboard"></i> Message Texte
                        </button>
                        <button type="button" class="tab-btn" onclick="switchTab('audio', 'create')">
                            <i class="fas fa-microphone"></i> Message Audio
                        </button>
                    </div>

                    <div id="textTabCreate" class="tab-content active">
                        <textarea name="message" class="form-textarea" placeholder="Votre message..."></textarea>
                    </div>

                    <div id="audioTabCreate" class="tab-content">
                        <div class="audio-controls">
                            <button type="button" class="btn-audio btn-record" onclick="startRecording('create')">
                                <i class="fas fa-circle"></i> Enregistrer
                            </button>
                            <button type="button" class="btn-audio btn-stop" onclick="stopRecording('create')" disabled>
                                <i class="fas fa-stop"></i> Arrêter
                            </button>
                            <button type="button" class="btn-audio btn-play" onclick="playAudio('create')" disabled>
                                <i class="fas fa-play"></i> Écouter
                            </button>
                            <label class="btn-audio btn-upload">
                                <i class="fas fa-upload"></i> Importer
                                <input type="file" name="audio_file" accept="audio/*" onchange="handleAudioUpload(event, 'create')" style="display:none">
                            </label>
                        </div>

                        <div id="recordingIndicatorCreate" class="recording-indicator">
                            <div class="recording-dot"></div>
                            <span class="recording-time" id="timerCreate">00:00</span>
                            <span style="color: #8696a0;">Enregistrement...</span>
                        </div>

                        <div id="audioPreviewCreate" class="audio-preview">
                            <audio id="audioPlayerCreate" controls style="width: 100%;"></audio>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Priorité</label>
                    <select name="priority" class="form-select" required>
                        <option value="normal">Normale</option>
                        <option value="important">Importante</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Formations Ciblées *</label>
                    <div class="formations-list">
                        @php
                            $formations = \App\Models\Formation::where('status', 'published')->get();
                        @endphp
                        @foreach($formations as $formation)
                            <div class="formation-item">
                                <input type="checkbox" name="formation_ids[]" value="{{ $formation->id }}" class="formation-checkbox" id="formation_create_{{ $formation->id }}">
                                <label for="formation_create_{{ $formation->id }}" class="formation-label">
                                    <span>{{ $formation->title }}</span>
                                    <span class="formation-count-badge">{{ $formation->active_students_count }} étudiants</span>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeCreateModal()" class="btn-cancel">Annuler</button>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-paper-plane"></i> Envoyer
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Edit Message --}}
<div class="modal-overlay" id="editModal">
    <div class="modal-container">
        <div class="modal-header">
            <h2><i class="fas fa-edit"></i> Modifier le Message</h2>
            <button onclick="closeEditModal()" class="modal-close">
                <i class="fas fa-times"></i>
            </button>
        </div>
        <form id="editForm" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="modal-body">
                <div class="form-group">
                    <label class="form-label">Sujet du Message *</label>
                    <input type="text" name="subject" id="editSubject" class="form-input" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Type de Message</label>
                    <div class="tab-buttons">
                        <button type="button" class="tab-btn active" onclick="switchTab('text', 'edit')">
                            <i class="fas fa-keyboard"></i> Message Texte
                        </button>
                        <button type="button" class="tab-btn" onclick="switchTab('audio', 'edit')">
                            <i class="fas fa-microphone"></i> Message Audio
                        </button>
                    </div>

                    <div id="textTabEdit" class="tab-content active">
                        <textarea name="message" id="editMessage" class="form-textarea"></textarea>
                    </div>

                    <div id="audioTabEdit" class="tab-content">
                        <div id="currentAudioEdit" style="display:none; margin-bottom: 16px; padding: 16px; background: #1a1a1a; border-radius: 12px; border: 2px solid #6366f1;">
                            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 12px;">
                                <span style="color: #e9e9e9; font-weight: 600;">
                                    <i class="fas fa-music" style="color: #6366f1;"></i> Audio Actuel
                                </span>
                                <label style="display: flex; align-items: center; gap: 8px; cursor: pointer;">
                                    <input type="checkbox" name="remove_audio" value="1">
                                    <span style="color: #ef4444; font-size: 14px;">Supprimer</span>
                                </label>
                            </div>
                            <audio id="currentAudioPlayer" controls style="width: 100%;"></audio>
                        </div>

                        <div class="audio-controls">
                            <button type="button" class="btn-audio btn-record" onclick="startRecording('edit')">
                                <i class="fas fa-circle"></i> Enregistrer
                            </button>
                            <button type="button" class="btn-audio btn-stop" onclick="stopRecording('edit')" disabled>
                                <i class="fas fa-stop"></i> Arrêter
                            </button>
                            <button type="button" class="btn-audio btn-play" onclick="playAudio('edit')" disabled>
                                <i class="fas fa-play"></i> Écouter
                            </button>
                            <label class="btn-audio btn-upload">
                                <i class="fas fa-upload"></i> Importer
                                <input type="file" name="audio_file" accept="audio/*" onchange="handleAudioUpload(event, 'edit')" style="display:none">
                            </label>
                        </div>

                        <div id="recordingIndicatorEdit" class="recording-indicator">
                            <div class="recording-dot"></div>
                            <span class="recording-time" id="timerEdit">00:00</span>
                            <span style="color: #8696a0;">Enregistrement...</span>
                        </div>

                        <div id="audioPreviewEdit" class="audio-preview">
                            <audio id="audioPlayerEdit" controls style="width: 100%;"></audio>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Priorité</label>
                    <select name="priority" id="editPriority" class="form-select" required>
                        <option value="normal">Normale</option>
                        <option value="important">Importante</option>
                        <option value="urgent">Urgente</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Formations Ciblées *</label>
                    <div class="formations-list" id="editFormationsList"></div>
                </div>
            </div>

            <div class="modal-footer">
                <button type="button" onclick="closeEditModal()" class="btn-cancel">Annuler</button>
                <button type="submit" class="btn-submit">
                    <i class="fas fa-save"></i> Mettre à jour
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Variables globales
let mediaRecorders = {create: null, edit: null};
let audioChunks = {create: [], edit: []};
let recordingIntervals = {create: null, edit: null};
let recordingSeconds = {create: 0, edit: 0};

document.addEventListener('DOMContentLoaded', function() {
    const messageItems = document.querySelectorAll('.message-item');
    const detailPanel = document.getElementById('detailPanel');
    
    if (messageItems.length > 0) {
        messageItems[0].classList.add('active');
        loadMessageDetail(messageItems[0].getAttribute('data-message-id'));
    }
    
    messageItems.forEach(item => {
        item.addEventListener('click', function() {
            messageItems.forEach(mi => mi.classList.remove('active'));
            this.classList.add('active');
            loadMessageDetail(this.getAttribute('data-message-id'));
        });
    });
});

function loadMessageDetail(messageId) {
    const detailPanel = document.getElementById('detailPanel');
    detailPanel.innerHTML = `
        <div class="empty-detail">
            <i class="fas fa-spinner fa-spin" style="color: #C2185B;"></i>
            <h3>Chargement...</h3>
        </div>
    `;
    
    fetch(`/messages/${messageId}/details`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        let html = '<div class="detail-content">';
        html += buildDetailHeader(data.subject, data.priority, data.stats, messageId);
        
        if (data.audio_path) {
            html += buildAudioSection(data.audio_path, data.audio_duration);
        }
        
        if (data.message) {
            html += buildMessageTextSection(data.message);
        }
        
        html += buildStatsGrid(data.stats);
        
        if (data.stats.total > 0) {
            html += buildProgressBar(data.stats);
        }
        
        html += buildFormationsSection(data.formations);
        html += buildRecipientsSection(data.recipients);
        html += '</div>';
        
        detailPanel.innerHTML = html;
    })
    .catch(error => {
        console.error('Erreur:', error);
        detailPanel.innerHTML = '<div class="empty-detail"><i class="fas fa-exclamation-triangle"></i><h3>Erreur</h3><p>Impossible de charger le message</p></div>';
    });
}

function buildDetailHeader(subject, priority, stats, messageId) {
    const priorityBadges = {
        urgent: '<span class="priority-badge priority-urgent"><i class="fas fa-bolt"></i> URGENT</span>',
        important: '<span class="priority-badge priority-important"><i class="fas fa-star"></i> IMPORTANT</span>',
        normal: '<span class="priority-badge priority-normal">NORMAL</span>'
    };
    
    return `
        <div class="detail-header">
            <div class="detail-title">${subject} ${priorityBadges[priority] || ''}</div>
            <div class="action-buttons">
                <button onclick="openEditModal(${messageId})" class="action-btn primary">
                    <i class="fas fa-edit"></i> Modifier
                </button>
                <form action="/messages/${messageId}" method="POST" style="display: inline;" onsubmit="return confirm('Êtes-vous sûr ?')">
                    <input type="hidden" name="_token" value="${document.querySelector('meta[name=csrf-token]')?.content}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="action-btn danger">
                        <i class="fas fa-trash"></i> Supprimer
                    </button>
                </form>
            </div>
        </div>
    `;
}

function buildAudioSection(audioPath, audioDuration) {
    const durationText = audioDuration ? `Durée: ${Math.floor(audioDuration / 60)}:${(audioDuration % 60).toString().padStart(2, '0')} min` : 'Cliquez pour écouter';
    
    return `
        <div class="audio-section">
            <div class="audio-header">
                <div class="audio-icon"><i class="fas fa-headphones"></i></div>
                <div class="audio-info">
                    <h3>Message Audio</h3>
                    <p>🎵 ${durationText}</p>
                </div>
            </div>
            <div class="audio-player-container">
                <audio controls style="width: 100%; height: 45px;">
                    <source src="/storage/${audioPath}" type="audio/webm">
                    <source src="/storage/${audioPath}" type="audio/mpeg">
                    <source src="/storage/${audioPath}" type="audio/wav">
                    <source src="/storage/${audioPath}" type="audio/ogg">
                </audio>
            </div>
        </div>
    `;
}

function buildMessageTextSection(messageText) {
    return `
        <div class="message-text-section">
            <div class="section-title"><i class="fas fa-align-left"></i> Message Texte</div>
            <div class="message-text">${messageText}</div>
        </div>
    `;
}

function buildStatsGrid(stats) {
    return `
        <div class="recipients-grid" style="grid-template-columns: repeat(4, 1fr);">
            <div class="stat-card primary" style="margin: 0;">
                <div class="stat-icon"><i class="fas fa-users"></i></div>
                <div class="stat-label">Total Destinataires</div>
                <div class="stat-value">${stats.total}</div>
            </div>
            <div class="stat-card success" style="margin: 0;">
                <div class="stat-icon"><i class="fas fa-check-circle"></i></div>
                <div class="stat-label">Ont Lu</div>
                <div class="stat-value">${stats.read}</div>
            </div>
            <div class="stat-card warning" style="margin: 0;">
                <div class="stat-icon"><i class="fas fa-exclamation-circle"></i></div>
                <div class="stat-label">Non Lu</div>
                <div class="stat-value">${stats.unread}</div>
            </div>
            <div class="stat-card info" style="margin: 0;">
                <div class="stat-icon"><i class="fas fa-chart-bar"></i></div>
                <div class="stat-label">Taux de Lecture</div>
                <div class="stat-value">${stats.percentage}%</div>
            </div>
        </div>
    `;
}

function buildProgressBar(stats) {
    return `
        <div class="message-text-section">
            <div class="section-title"><i class="fas fa-chart-line"></i> Progression de la Lecture</div>
            <div style="background: #1a1a1a; border-radius: 12px; padding: 8px; margin-top: 12px;">
                <div style="background: linear-gradient(90deg, #10b981, #059669); height: 32px; border-radius: 8px; width: ${stats.percentage}%; display: flex; align-items: center; justify-content: center; color: white; font-weight: 700; font-size: 14px; transition: width 0.5s ease;">
                    ${stats.percentage}%
                </div>
            </div>
            <div style="display: flex; justify-content: space-between; margin-top: 12px; color: #8696a0; font-size: 13px;">
                <span>${stats.read} l'ont lu</span>
                <span>${stats.unread} ne l'ont pas lu</span>
            </div>
        </div>
    `;
}

function buildFormationsSection(formations) {
    if (formations.length === 0) return '';
    
    let html = `
        <div class="message-text-section">
            <div class="section-title"><i class="fas fa-book-open"></i> Formations Ciblées (${formations.length})</div>
            <div class="formations-grid" style="margin-top: 16px;">
    `;
    
    formations.forEach(formation => {
        html += `
            <div class="formation-badge">
                <span>${formation.title}</span>
                <span class="formation-count">${formation.count}</span>
            </div>
        `;
    });
    
    html += '</div></div>';
    return html;
}

function buildRecipientsSection(recipients) {
    if (recipients.length === 0) return '';
    
    let html = `
        <div class="message-text-section">
            <div class="section-title"><i class="fas fa-users-cog"></i> Destinataires (${recipients.length})</div>
            <div class="recipients-grid" style="margin-top: 16px;">
    `;
    
    recipients.forEach(recipient => {
        const initial = recipient.name.charAt(0).toUpperCase();
        const statusClass = recipient.isRead ? 'read' : 'unread';
        const statusText = recipient.isRead ? 'Lu' : 'Non lu';
        const statusIcon = recipient.isRead ? 'check-circle' : 'exclamation-circle';
        
        html += `
            <div class="recipient-card">
                <div class="recipient-header">
                    <div class="recipient-avatar">${initial}</div>
                    <div class="recipient-info">
                        <h4>${recipient.name}</h4>
                        <p>${recipient.email}</p>
                    </div>
                </div>
                <div class="recipient-status">
                    <span class="status-badge ${statusClass}">
                        <i class="fas fa-${statusIcon}"></i>
                        ${statusText}
                    </span>
                    ${recipient.isRead && recipient.readAt ? `<span style="color: #8696a0; font-size: 12px;">${recipient.readAt}</span>` : ''}
                </div>
            </div>
        `;
    });
    
    html += '</div></div>';
    return html;
}

// Modal Functions
function openCreateModal() {
    document.getElementById('createModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeCreateModal() {
    document.getElementById('createModal').classList.remove('active');
    document.body.style.overflow = 'auto';
    document.getElementById('createForm').reset();
}

function openEditModal(messageId) {
    fetch(`/messages/${messageId}/details`, {
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        document.getElementById('editSubject').value = data.subject;
        document.getElementById('editMessage').value = data.message || '';
        document.getElementById('editPriority').value = data.priority;
        document.getElementById('editForm').action = `/messages/${messageId}`;
        
        // Audio actuel
        if (data.audio_path) {
            document.getElementById('currentAudioEdit').style.display = 'block';
            document.getElementById('currentAudioPlayer').src = '/storage/' + data.audio_path;
        } else {
            document.getElementById('currentAudioEdit').style.display = 'none';
        }
        
        // Formations
        fetch('/api/formations')
            .then(res => res.json())
            .then(formations => {
                let html = '';
                formations.forEach(formation => {
                    const checked = data.formations.some(f => f.title === formation.title) ? 'checked' : '';
                    html += `
                        <div class="formation-item">
                            <input type="checkbox" name="formation_ids[]" value="${formation.id}" class="formation-checkbox" id="formation_edit_${formation.id}" ${checked}>
                            <label for="formation_edit_${formation.id}" class="formation-label">
                                <span>${formation.title}</span>
                                <span class="formation-count-badge">${formation.students_count} étudiants</span>
                            </label>
                        </div>
                    `;
                });
                document.getElementById('editFormationsList').innerHTML = html;
            });
        
        document.getElementById('editModal').classList.add('active');
        document.body.style.overflow = 'hidden';
    });
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
    document.body.style.overflow = 'auto';
}

// Tab Switching
function switchTab(type, modal) {
    const tabs = document.querySelectorAll(`#${modal}Modal .tab-btn`);
    const contents = document.querySelectorAll(`#${modal}Modal .tab-content`);
    
    tabs.forEach(tab => tab.classList.remove('active'));
    contents.forEach(content => content.classList.remove('active'));
    
    event.target.closest('.tab-btn').classList.add('active');
    document.getElementById(`${type}Tab${modal.charAt(0).toUpperCase() + modal.slice(1)}`).classList.add('active');
}

// Audio Recording Functions
async function startRecording(modal) {
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
        mediaRecorders[modal] = new MediaRecorder(stream);
        audioChunks[modal] = [];
        
        mediaRecorders[modal].addEventListener('dataavailable', event => {
            audioChunks[modal].push(event.data);
        });
        
        mediaRecorders[modal].addEventListener('stop', () => {
            const audioBlob = new Blob(audioChunks[modal], { type: 'audio/webm' });
            const audioUrl = URL.createObjectURL(audioBlob);
            document.getElementById(`audioPlayer${modal.charAt(0).toUpperCase() + modal.slice(1)}`).src = audioUrl;
            
            const file = new File([audioBlob], 'recorded-audio.webm', { type: 'audio/webm' });
            const dataTransfer = new DataTransfer();
            dataTransfer.items.add(file);
            document.querySelector(`#${modal}Modal input[name="audio_file"]`).files = dataTransfer.files;
            
            document.getElementById(`audioPreview${modal.charAt(0).toUpperCase() + modal.slice(1)}`).classList.add('active');
        });
        
        mediaRecorders[modal].start();
        document.querySelector(`#${modal}Modal .btn-record`).disabled = true;
        document.querySelector(`#${modal}Modal .btn-stop`).disabled = false;
        document.getElementById(`recordingIndicator${modal.charAt(0).toUpperCase() + modal.slice(1)}`).classList.add('active');
        
        recordingSeconds[modal] = 0;
        recordingIntervals[modal] = setInterval(() => {
            recordingSeconds[modal]++;
            const mins = Math.floor(recordingSeconds[modal] / 60).toString().padStart(2, '0');
            const secs = (recordingSeconds[modal] % 60).toString().padStart(2, '0');
            document.getElementById(`timer${modal.charAt(0).toUpperCase() + modal.slice(1)}`).textContent = `${mins}:${secs}`;
        }, 1000);
        
    } catch (error) {
        alert('Erreur d\'accès au microphone: ' + error.message);
    }
}

function stopRecording(modal) {
    if (mediaRecorders[modal] && mediaRecorders[modal].state !== 'inactive') {
        mediaRecorders[modal].stop();
        mediaRecorders[modal].stream.getTracks().forEach(track => track.stop());
        clearInterval(recordingIntervals[modal]);
        
        document.querySelector(`#${modal}Modal .btn-record`).disabled = false;
        document.querySelector(`#${modal}Modal .btn-stop`).disabled = true;
        document.querySelector(`#${modal}Modal .btn-play`).disabled = false;
        document.getElementById(`recordingIndicator${modal.charAt(0).toUpperCase() + modal.slice(1)}`).classList.remove('active');
    }
}

function playAudio(modal) {
    document.getElementById(`audioPlayer${modal.charAt(0).toUpperCase() + modal.slice(1)}`).play();
}

function handleAudioUpload(event, modal) {
    const file = event.target.files[0];
    if (file) {
        const audioUrl = URL.createObjectURL(file);
        document.getElementById(`audioPlayer${modal.charAt(0).toUpperCase() + modal.slice(1)}`).src = audioUrl;
        document.getElementById(`audioPreview${modal.charAt(0).toUpperCase() + modal.slice(1)}`).classList.add('active');
        document.querySelector(`#${modal}Modal .btn-play`).disabled = false;
    }
}
</script>

@endsection