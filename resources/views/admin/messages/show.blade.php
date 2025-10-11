@extends('layouts.app')

@section('title', 'Détails du Message')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 via-indigo-50 to-purple-50 py-8">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="mb-8">
            <a href="{{ route('messages.index') }}" class="inline-flex items-center gap-2 text-blue-600 hover:text-blue-700 font-medium transition-colors mb-4">
                <i class="fas fa-arrow-left w-5 h-5"></i>
                Retour à la liste
            </a>
        </div>

        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden border border-gray-100 mb-8">
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-8 text-white">
                <div class="flex items-start justify-between">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-4">
                            @if($message->priority === 'urgent')
                                <span class="px-4 py-2 bg-red-500 text-white text-sm font-bold rounded-full flex items-center gap-2 animate-pulse shadow-lg">
                                    <i class="fas fa-exclamation-triangle w-4 h-4"></i>
                                    URGENT
                                </span>
                            @elseif($message->priority === 'important')
                                <span class="px-4 py-2 bg-yellow-500 text-white text-sm font-bold rounded-full flex items-center gap-2 shadow-lg">
                                    <i class="fas fa-star w-4 h-4"></i>
                                    Important
                                </span>
                            @else
                                <span class="px-4 py-2 bg-green-500 text-white text-sm font-bold rounded-full shadow-lg">Normal</span>
                            @endif

                            @if($message->status === 'sent')
                                <span class="px-4 py-2 bg-white/20 backdrop-blur-sm text-white text-sm font-semibold rounded-full">Envoyé</span>
                            @endif

                            {{-- ✨ Badge Audio si présent --}}
                            @if($message->audio_path)
                                <span class="px-4 py-2 bg-purple-500 text-white text-sm font-bold rounded-full flex items-center gap-2 shadow-lg">
                                    <i class="fas fa-microphone w-4 h-4"></i>
                                    Message Audio
                                </span>
                            @endif
                        </div>

                        <h1 class="text-3xl font-bold mb-4">{{ $message->subject }}</h1>

                        <div class="flex flex-wrap items-center gap-6 text-sm text-white/90">
                            <div class="flex items-center gap-2">
                                <i class="fas fa-user w-5 h-5"></i>
                                <span>{{ $message->sender->name ?? 'Inconnu' }}</span>
                            </div>

                            <div class="flex items-center gap-2">
                                <i class="far fa-calendar-alt w-5 h-5"></i>
                                <span>{{ $message->created_at->format('Y-m-d H:i') }}</span>
                            </div>

                            @if($message->sent_at)
                                <div class="flex items-center gap-2">
                                    <i class="fas fa-paper-plane w-5 h-5"></i>
                                    <span>Envoyé {{ $message->sent_at->diffForHumans() }}</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <form action="{{ route('messages.destroy', $message->id) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer ce message ?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="p-3 bg-red-500 hover:bg-red-600 rounded-xl transition-colors shadow-lg" title="Supprimer le message">
                            <i class="fas fa-trash-alt w-6 h-6 text-white"></i>
                        </button>
                    </form>
                </div>
            </div>

            <div class="p-8">
                {{-- ✨ NOUVEAU: Affichage du message audio si présent --}}
                @if($message->audio_path)
                    <div class="mb-6">
                        <div class="bg-gradient-to-br from-purple-50 to-blue-50 rounded-xl p-6 border-2 border-purple-200">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-14 h-14 bg-purple-500 rounded-full flex items-center justify-center shadow-lg">
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
                            
                            <div class="bg-white rounded-lg p-4 shadow-sm">
                                <audio controls class="w-full" style="height: 45px;">
                                    <source src="{{ Storage::url($message->audio_path) }}" type="audio/webm">
                                    <source src="{{ Storage::url($message->audio_path) }}" type="audio/mpeg">
                                    <source src="{{ Storage::url($message->audio_path) }}" type="audio/wav">
                                    <source src="{{ Storage::url($message->audio_path) }}" type="audio/ogg">
                                    Votre navigateur ne supporte pas la lecture audio.
                                </audio>
                            </div>
                            
                            <div class="mt-3 text-xs text-gray-500 flex items-center gap-2">
                                <i class="fas fa-info-circle"></i>
                                <span>Message vocal envoyé aux étudiants</span>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Message texte --}}
                @if($message->message)
                    <div class="prose max-w-none">
                        <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                            <h3 class="font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <i class="fas fa-align-left text-indigo-500"></i>
                                Message Texte
                            </h3>
                            <p class="text-gray-800 leading-relaxed whitespace-pre-wrap">{{ $message->message }}</p>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-indigo-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Total des Destinataires</p>
                        <p class="text-3xl font-bold text-gray-800 mt-2">{{ $message->recipients_count }}</p>
                    </div>
                    <div class="w-14 h-14 bg-indigo-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-users w-7 h-7 text-indigo-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-green-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Ont lu le message</p>
                        <p class="text-3xl font-bold text-green-600 mt-2">{{ $message->read_count }}</p>
                    </div>
                    <div class="w-14 h-14 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-check-circle w-7 h-7 text-green-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-orange-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Ne l'ont pas lu</p>
                        <p class="text-3xl font-bold text-orange-600 mt-2">{{ $message->unread_count }}</p>
                    </div>
                    <div class="w-14 h-14 bg-orange-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-exclamation-circle w-7 h-7 text-orange-600"></i>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-lg p-6 border-l-4 border-purple-500">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600 text-sm font-medium">Taux de Lecture</p>
                        <p class="text-3xl font-bold text-purple-600 mt-2">{{ $message->read_percentage }}%</p>
                    </div>
                    <div class="w-14 h-14 bg-purple-100 rounded-full flex items-center justify-center">
                        <i class="fas fa-chart-bar w-7 h-7 text-purple-600"></i>
                    </div>
                </div>
            </div>
        </div>

        @if($message->recipients_count > 0)
            <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-800 mb-4">Progression de la Lecture</h3>
                <div class="relative">
                    <div class="w-full bg-gray-200 rounded-full h-6 overflow-hidden">
                        <div class="bg-gradient-to-r from-green-500 to-emerald-500 h-6 rounded-full transition-all duration-500 flex items-center justify-center text-white text-sm font-bold" style="width: {{ $message->read_percentage }}%">
                            {{ $message->read_percentage }}%
                        </div>
                    </div>
                    <div class="flex justify-between mt-2 text-sm text-gray-600">
                        <span>{{ $message->read_count }} l'ont lu</span>
                        <span>{{ $message->unread_count }} ne l'ont pas lu</span>
                    </div>
                </div>
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-lg p-6 mb-8">
            <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                <i class="fas fa-book-open w-6 h-6 text-indigo-600"></i>
                Formations Ciblées ({{ $message->formations->count() }})
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                @foreach($message->formations as $formation)
                    <div class="p-4 bg-gradient-to-br from-blue-50 to-indigo-50 rounded-lg border border-indigo-200">
                        <h4 class="font-semibold text-gray-800 mb-1">{{ $formation->title }}</h4>
                        <p class="text-sm text-gray-600">{{ $formation->active_students_count }} étudiant(s)</p>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-lg overflow-hidden">
            <div class="p-6 border-b border-gray-200">
                <h3 class="text-lg font-bold text-gray-800 flex items-center gap-2">
                    <i class="fas fa-users-cog w-6 h-6 text-indigo-600"></i>
                    Destinataires ({{ $message->recipientRecords->count() }})
                </h3>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Étudiant</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">E-mail</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Formation</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Statut</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">Date de Lecture</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @forelse($message->recipientRecords as $recipient)
                            <tr class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-full flex items-center justify-center text-white font-bold">
                                            {{ substr($recipient->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-800">{{ $recipient->user->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $recipient->user->email }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="text-sm text-gray-700">{{ $recipient->inscription->formation->title ?? 'Non spécifié' }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    @if($recipient->is_read)
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-green-100 text-green-700 text-xs font-semibold rounded-full">
                                            <i class="fas fa-circle-check w-3 h-3"></i>
                                            Lu
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 px-3 py-1 bg-orange-100 text-orange-700 text-xs font-semibold rounded-full">
                                            <i class="fas fa-circle-xmark w-3 h-3"></i>
                                            Non lu
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if($recipient->read_at)
                                        <span class="flex items-center gap-1">
                                            <i class="far fa-clock w-4 h-4 text-gray-400"></i>
                                            {{ $recipient->read_at->diffForHumans() }}
                                        </span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="far fa-envelope-open w-16 h-16 mx-auto text-gray-300 mb-4"></i>
                                    <p class="text-lg font-medium">Aucun destinataire</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection