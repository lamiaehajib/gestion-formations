@extends('layouts.app')

@section('title', $message->subject)

@section('content')
<div class="container mx-auto p-4">
    <div class="max-w-4xl mx-auto bg-white shadow-xl rounded-xl p-8">

        {{-- Bouton de retour --}}
        <a href="{{ route('message.index') }}" class="inline-flex items-center text-indigo-600 hover:text-indigo-800 transition duration-150 mb-6">
            <i class="fas fa-arrow-left mr-2"></i>
            Retour à la boîte de réception
        </a>

        <div class="border-b pb-4 mb-4">
            <div class="flex items-center gap-3 mb-2">
                <h1 class="text-3xl font-bold text-gray-900">{{ $message->subject }}</h1>

                {{-- Affichage de la priorité --}}
                @if($message->priority === 'urgent')
                    <span class="px-3 py-1 bg-red-100 text-red-700 text-sm font-bold rounded-full animate-pulse">URGENT</span>
                @elseif($message->priority === 'important')
                    <span class="px-3 py-1 bg-yellow-100 text-yellow-700 text-sm font-bold rounded-full">Important</span>
                @endif
            </div>

            <div class="text-gray-600 text-sm flex items-center gap-4 flex-wrap">
                <p>
                    <span class="font-semibold">De:</span> {{ $message->sender->name ?? 'Administrateur' }}
                </p>
                <span class="text-gray-300">•</span>
                <p>
                    <span class="font-semibold">Envoyé le:</span> {{ $message->created_at->format('d/m/Y H:i') }}
                </p>
                <span class="text-gray-300">•</span>
                <p>
                    <span class="font-semibold">Statut:</span> 
                    <span class="{{ $receipt->is_read ? 'text-green-600' : 'text-blue-600' }}">
                        {{ $receipt->is_read ? 'Lu' : 'Non Lu (maintenant marqué comme lu)' }}
                    </span>
                </p>
            </div>
        </div>

        {{-- ✨ NOUVEAU: Affichage du message audio si présent --}}
        @if($message->audio_path)
            <div class="mb-6">
                <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl p-6 border-2 border-purple-200 shadow-lg">
                    <div class="flex items-center gap-3 mb-4">
                        <div class="w-14 h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full flex items-center justify-center shadow-lg">
                            <i class="fas fa-headphones text-white text-xl"></i>
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-800 text-lg">Message Audio</h3>
                            <p class="text-sm text-gray-600">
                                @if($message->audio_duration)
                                    Durée: {{ gmdate('i:s', $message->audio_duration) }} min
                                @else
                                    Cliquez pour écouter
                                @endif
                            </p>
                        </div>
                    </div>
                    
                    {{-- Lecteur audio avec design amélioré --}}
                    <div class="bg-white rounded-xl p-4 shadow-md border border-purple-100">
                        <audio id="audioPlayer" controls class="w-full" style="height: 45px;">
                            <source src="{{ Storage::url($message->audio_path) }}" type="audio/webm">
                            <source src="{{ Storage::url($message->audio_path) }}" type="audio/mpeg">
                            <source src="{{ Storage::url($message->audio_path) }}" type="audio/wav">
                            <source src="{{ Storage::url($message->audio_path) }}" type="audio/ogg">
                            Votre navigateur ne supporte pas la lecture audio.
                        </audio>
                    </div>
                    
                    <div class="mt-4 flex items-center justify-between text-xs">
                        <span class="text-gray-500 flex items-center gap-2">
                            <i class="fas fa-info-circle"></i>
                            Message vocal de votre formateur
                        </span>
                        <span id="playCount" class="text-purple-600 font-semibold flex items-center gap-1">
                            <i class="fas fa-play-circle"></i>
                            <span>Pas encore écouté</span>
                        </span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Corps du message texte (si présent) --}}
        @if($message->message)
            <div class="mb-6">
                <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                    <i class="fas fa-align-left text-indigo-500"></i>
                    Message
                </h3>
                <div class="prose max-w-none text-gray-800 bg-gray-50 rounded-lg p-4">
                    {!! nl2br(e($message->message)) !!}
                </div>
            </div>
        @endif

        {{-- Afficher uniquement la formation de l'étudiant --}}
        @if($studentFormation)
            <div class="mt-8 pt-4 border-t">
                <p class="font-semibold text-gray-700 mb-2">Concerne votre formation:</p>
                <div class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-100 text-indigo-700 rounded-lg">
                    <i class="fas fa-graduation-cap"></i>
                    <span class="font-medium">{{ $studentFormation->title }}</span>
                </div>
            </div>
        @endif
    </div>
</div>

<style>
    /* Style personnalisé pour le lecteur audio */
    audio {
        height: 45px;
        border-radius: 8px;
    }
    audio::-webkit-media-controls-panel {
        background-color: #f3f4f6;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const audioPlayer = document.getElementById('audioPlayer');
    
    if (audioPlayer) {
        // Marquer le message comme vraiment lu après avoir écouté l'audio
        audioPlayer.addEventListener('play', function() {
            console.log('Message audio en cours de lecture');
        });
        
        // Optionnel: Empêcher le téléchargement
        audioPlayer.addEventListener('contextmenu', function(e) {
            e.preventDefault();
            return false;
        });
    }
});
</script>

@endsection