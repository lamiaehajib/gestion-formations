@extends('layouts.app')

@section('title', 'Modifier le Message')

@section('content')

<div class="min-h-screen bg-gradient-to-br from-blue-50 via-purple-50 to-pink-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        
        {{-- En-tête --}}
        <div class="mb-8">
            <a href="{{ route('messages.show', $message->id) }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium transition-colors mb-4">
                <i class="fas fa-arrow-left"></i>
                Retour aux détails
            </a>
            <h1 class="text-3xl font-bold text-gray-800">✏️ Modifier le Message</h1>
            <p class="text-gray-600 mt-1">Mettre à jour le message existant</p>
        </div>

        {{-- Erreurs de validation --}}
        @if ($errors->any())
            <div class="mb-6 bg-red-50 border-l-4 border-red-500 rounded-lg p-4 shadow-sm">
                <p class="text-red-800 font-medium">Veuillez corriger les erreurs suivantes :</p>
                <ul class="list-disc list-inside mt-2 text-red-700">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        
        {{-- Formulaire --}}
        <div class="bg-white rounded-2xl shadow-xl p-8">
            <form action="{{ route('messages.update', $message->id) }}" method="POST" enctype="multipart/form-data" id="messageForm">
                @csrf
                @method('PUT')
                
                {{-- Sujet --}}
                <div class="mb-6">
                    <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Sujet du Message *</label>
                    <input type="text" id="subject" name="subject" value="{{ old('subject', $message->subject) }}" required
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('subject') border-red-500 @enderror">
                    @error('subject')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- ✨ Tabs pour choisir entre Texte et Audio --}}
                <div class="mb-6">
                    <label class="block text-sm font-medium text-gray-700 mb-3">Type de Message</label>
                    <div class="flex gap-3 mb-4">
                        <button type="button" id="tabText" class="tab-btn {{ $message->message ? 'active' : '' }} px-6 py-2 rounded-lg font-medium transition-all duration-200">
                            <i class="fas fa-keyboard mr-2"></i> Message Texte
                        </button>
                        <button type="button" id="tabAudio" class="tab-btn {{ $message->audio_path && !$message->message ? 'active' : '' }} px-6 py-2 rounded-lg font-medium transition-all duration-200">
                            <i class="fas fa-microphone mr-2"></i> Message Audio
                        </button>
                    </div>

                    {{-- Contenu Tab Texte --}}
                    <div id="contentText" class="tab-content {{ $message->message ? 'active' : 'hidden' }}">
                        <textarea id="message" name="message" rows="6"
                                  class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('message') border-red-500 @enderror"
                                  placeholder="Tapez votre message ici...">{{ old('message', $message->message) }}</textarea>
                        @error('message')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ✨ Contenu Tab Audio --}}
                    <div id="contentAudio" class="tab-content {{ $message->audio_path && !$message->message ? 'active' : 'hidden' }}">
                        <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl p-6 border-2 border-dashed border-purple-300">
                            
                            {{-- Audio actuel --}}
                            @if($message->audio_path)
                                <div id="currentAudio" class="mb-4 bg-white rounded-lg p-4 border-2 border-purple-200">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-semibold text-gray-700 flex items-center gap-2">
                                            <i class="fas fa-music text-purple-500"></i>
                                            Audio Actuel
                                        </h4>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="checkbox" name="remove_audio" id="removeAudio" value="1" class="w-4 h-4 text-red-600 rounded">
                                            <span class="text-sm text-red-600 font-medium">Supprimer</span>
                                        </label>
                                    </div>
                                    <audio controls class="w-full mb-2" style="height: 40px;">
                                        <source src="{{ Storage::url($message->audio_path) }}" type="audio/webm">
                                        <source src="{{ Storage::url($message->audio_path) }}" type="audio/mpeg">
                                    </audio>
                                    @if($message->audio_duration)
                                        <p class="text-xs text-gray-500">Durée: {{ gmdate('i:s', $message->audio_duration) }}</p>
                                    @endif
                                </div>
                            @endif
                            
                            {{-- Boutons de contrôle pour nouvel enregistrement --}}
                            <div class="flex flex-wrap gap-3 mb-4">
                                <button type="button" id="btnRecord" class="btn-audio bg-red-500 hover:bg-red-600 text-white px-5 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2">
                                    <i class="fas fa-circle"></i>
                                    <span>Enregistrer Nouveau</span>
                                </button>
                                <button type="button" id="btnStop" class="btn-audio bg-gray-500 hover:bg-gray-600 text-white px-5 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2" disabled>
                                    <i class="fas fa-stop"></i>
                                    <span>Arrêter</span>
                                </button>
                                <button type="button" id="btnPlay" class="btn-audio bg-green-500 hover:bg-green-600 text-white px-5 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2" disabled>
                                    <i class="fas fa-play"></i>
                                    <span>Écouter</span>
                                </button>
                                <label for="audioUpload" class="btn-audio bg-indigo-500 hover:bg-indigo-600 text-white px-5 py-2 rounded-lg font-medium transition-all duration-200 flex items-center gap-2 cursor-pointer">
                                    <i class="fas fa-upload"></i>
                                    <span>Importer Audio</span>
                                </label>
                                <input type="file" id="audioUpload" name="audio_file" accept="audio/*" class="hidden">
                            </div>

                            {{-- Timer d'enregistrement --}}
                            <div id="recordingTimer" class="hidden mb-4">
                                <div class="bg-white rounded-lg p-4 flex items-center gap-3">
                                    <div class="w-3 h-3 bg-red-500 rounded-full animate-pulse"></div>
                                    <span class="text-lg font-mono font-bold text-gray-700" id="timerDisplay">00:00</span>
                                    <span class="text-sm text-gray-500">Enregistrement en cours...</span>
                                </div>
                            </div>

                            {{-- Visualisation du nouvel audio --}}
                            <div id="audioPreview" class="hidden">
                                <div class="bg-white rounded-lg p-4 mb-3">
                                    <audio id="audioPlayer" controls class="w-full"></audio>
                                </div>
                                <p class="text-sm text-gray-600 flex items-center gap-2">
                                    <i class="fas fa-check-circle text-green-500"></i>
                                    <span id="audioFileName">Nouveau audio prêt</span>
                                </p>
                            </div>

                            {{-- Message d'instruction --}}
                            <div id="audioInstruction" class="{{ $message->audio_path ? 'hidden' : '' }} text-center text-gray-500 py-8">
                                <i class="fas fa-microphone text-4xl mb-3 text-gray-400"></i>
                                <p>Enregistrez un nouveau message audio ou importez un fichier</p>
                            </div>
                        </div>
                        @error('audio_file')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                
                {{-- Priorité --}}
                <div class="mb-6">
                    <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">Priorité</label>
                    <select id="priority" name="priority" required
                            class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 @error('priority') border-red-500 @enderror">
                        <option value="normal" {{ old('priority', $message->priority) == 'normal' ? 'selected' : '' }}>Normale</option>
                        <option value="important" {{ old('priority', $message->priority) == 'important' ? 'selected' : '' }}>Importante</option>
                        <option value="urgent" {{ old('priority', $message->priority) == 'urgent' ? 'selected' : '' }}>Urgente</option>
                    </select>
                    @error('priority')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                {{-- Formations Ciblées --}}
                <div class="mb-8">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Formations Ciblées *</label>
                    <div class="border border-gray-300 rounded-lg p-4 h-64 overflow-y-auto bg-gray-50 @error('formation_ids') border-red-500 @enderror">
                        @forelse($formations as $formation)
                            <div class="flex items-center mb-2">
                                <input type="checkbox" id="formation_{{ $formation['id'] }}" name="formation_ids[]" value="{{ $formation['id'] }}" 
                                       class="h-4 w-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500"
                                       {{ in_array($formation['id'], old('formation_ids', $selectedFormationIds)) ? 'checked' : '' }}>
                                <label for="formation_{{ $formation['id'] }}" class="ml-3 text-sm font-medium text-gray-700 cursor-pointer flex items-center justify-between w-full">
                                    <span>{{ $formation['title'] }}</span>
                                    <span class="text-xs text-indigo-500 ml-2">({{ $formation['students_count'] }} étudiant(s))</span>
                                </label>
                            </div>
                        @empty
                            <p class="text-gray-500">Aucune formation active trouvée.</p>
                        @endforelse
                    </div>
                    @error('formation_ids')
                        <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                    @enderror
                </div>
                
                <div class="flex justify-end gap-3">
                    <a href="{{ route('messages.show', $message->id) }}" class="px-6 py-3 bg-gray-200 text-gray-700 font-semibold rounded-xl hover:bg-gray-300 transition-all duration-200">
                        Annuler
                    </a>
                    <button type="submit" class="px-6 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-700 shadow-lg transition-all duration-200 flex items-center gap-2">
                        <i class="fas fa-save w-5 h-5"></i>
                        Mettre à Jour
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
    .tab-btn {
        background: #e5e7eb;
        color: #4b5563;
    }
    .tab-btn.active {
        background: linear-gradient(to right, #6366f1, #8b5cf6);
        color: white;
        box-shadow: 0 4px 6px rgba(99, 102, 241, 0.3);
    }
    .tab-content {
        display: none !important;
    }
    .tab-content.active {
        display: block !important;
    }
    .hidden {
        display: none !important;
    }
    .btn-audio:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Gestion des Tabs
    const tabText = document.getElementById('tabText');
    const tabAudio = document.getElementById('tabAudio');
    const contentText = document.getElementById('contentText');
    const contentAudio = document.getElementById('contentAudio');
    const removeAudioCheckbox = document.getElementById('removeAudio');
    
    tabText.addEventListener('click', () => {
        tabText.classList.add('active');
        tabAudio.classList.remove('active');
        contentText.classList.remove('hidden');
        contentText.classList.add('active');
        contentAudio.classList.add('hidden');
        contentAudio.classList.remove('active');
    });
    
    tabAudio.addEventListener('click', () => {
        tabAudio.classList.add('active');
        tabText.classList.remove('active');
        contentAudio.classList.remove('hidden');
        contentAudio.classList.add('active');
        contentText.classList.add('hidden');
        contentText.classList.remove('active');
    });

    // Masquer l'audio actuel si cochée
    if (removeAudioCheckbox) {
        removeAudioCheckbox.addEventListener('change', function() {
            const currentAudio = document.getElementById('currentAudio');
            if (this.checked) {
                currentAudio.style.opacity = '0.5';
                currentAudio.style.pointerEvents = 'none';
            } else {
                currentAudio.style.opacity = '1';
                currentAudio.style.pointerEvents = 'auto';
            }
        });
    }

    // Variables pour l'enregistrement audio
    let mediaRecorder;
    let audioChunks = [];
    let audioBlob;
    let recordingInterval;
    let seconds = 0;
    
    const btnRecord = document.getElementById('btnRecord');
    const btnStop = document.getElementById('btnStop');
    const btnPlay = document.getElementById('btnPlay');
    const audioUpload = document.getElementById('audioUpload');
    const audioPlayer = document.getElementById('audioPlayer');
    const audioPreview = document.getElementById('audioPreview');
    const audioInstruction = document.getElementById('audioInstruction');
    const recordingTimer = document.getElementById('recordingTimer');
    const timerDisplay = document.getElementById('timerDisplay');
    const audioFileName = document.getElementById('audioFileName');
    
    // Démarrer l'enregistrement
    btnRecord.addEventListener('click', async () => {
        try {
            const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
            mediaRecorder = new MediaRecorder(stream);
            audioChunks = [];
            
            mediaRecorder.addEventListener('dataavailable', event => {
                audioChunks.push(event.data);
            });
            
            mediaRecorder.addEventListener('stop', () => {
                audioBlob = new Blob(audioChunks, { type: 'audio/webm' });
                const audioUrl = URL.createObjectURL(audioBlob);
                audioPlayer.src = audioUrl;
                
                const file = new File([audioBlob], 'recorded-audio.webm', { type: 'audio/webm' });
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                audioUpload.files = dataTransfer.files;
                
                showAudioPreview('Nouvel audio enregistré (' + formatTime(seconds) + ')');
            });
            
            mediaRecorder.start();
            btnRecord.disabled = true;
            btnStop.disabled = false;
            btnPlay.disabled = true;
            recordingTimer.classList.remove('hidden');
            audioInstruction.classList.add('hidden');
            
            seconds = 0;
            recordingInterval = setInterval(() => {
                seconds++;
                timerDisplay.textContent = formatTime(seconds);
            }, 1000);
            
        } catch (error) {
            alert('Erreur d\'accès au microphone: ' + error.message);
        }
    });
    
    // Arrêter l'enregistrement
    btnStop.addEventListener('click', () => {
        if (mediaRecorder && mediaRecorder.state !== 'inactive') {
            mediaRecorder.stop();
            mediaRecorder.stream.getTracks().forEach(track => track.stop());
            clearInterval(recordingInterval);
            
            btnRecord.disabled = false;
            btnStop.disabled = true;
            btnPlay.disabled = false;
            recordingTimer.classList.add('hidden');
        }
    });
    
    // Écouter l'audio
    btnPlay.addEventListener('click', () => {
        audioPlayer.play();
    });
    
    // Importer un fichier audio
    audioUpload.addEventListener('change', (e) => {
        const file = e.target.files[0];
        if (file) {
            const audioUrl = URL.createObjectURL(file);
            audioPlayer.src = audioUrl;
            showAudioPreview('Fichier importé: ' + file.name);
            btnPlay.disabled = false;
        }
    });
    
    // Afficher l'aperçu
    function showAudioPreview(fileName) {
        audioPreview.classList.remove('hidden');
        audioInstruction.classList.add('hidden');
        audioFileName.textContent = fileName;
    }
    
    // Formater le temps
    function formatTime(totalSeconds) {
        const mins = Math.floor(totalSeconds / 60).toString().padStart(2, '0');
        const secs = (totalSeconds % 60).toString().padStart(2, '0');
        return `${mins}:${secs}`;
    }
});
</script>

@endsection