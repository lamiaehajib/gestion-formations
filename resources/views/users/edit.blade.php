@extends('layouts.app')

@section('title', 'Modifier l\'utilisateur')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-red-50 via-white to-pink-50 py-8">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="mb-8 animate__animated animate__fadeInDown">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-4xl font-extrabold text-gray-900 mb-2">
                        <i class="fa-solid fa-user-pen text-red-700 mr-3"></i> Modifier l'utilisateur
                    </h1>
                    <p class="text-gray-600 text-lg">Mettez à jour les informations de cet utilisateur.</p>
                </div>
                <a href="{{ route('users.index') }}"
                   class="inline-flex items-center px-6 py-3 bg-white hover:bg-gray-100 text-gray-800 font-semibold rounded-full shadow-md transition-all duration-300 transform hover:scale-105 border border-gray-200">
                    <i class="fa-solid fa-arrow-left-long"></i>
                    Retour à la liste
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-xl relative mb-6 animate__animated animate__fadeInUp" role="alert">
                <strong class="font-bold">Succès!</strong>
                <span class="block sm:inline">{{ session('success') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none';">
                    <i class="fa-solid fa-xmark"></i>
                </span>
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-xl relative mb-6 animate__animated animate__fadeInUp" role="alert">
                <strong class="font-bold">Erreur!</strong>
                <span class="block sm:inline">{{ session('error') }}</span>
                <span class="absolute top-0 bottom-0 right-0 px-4 py-3 cursor-pointer" onclick="this.parentElement.style.display='none';">
                    <i class="fa-solid fa-xmark"></i>
                </span>
            </div>
        @endif

        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 overflow-hidden animate__animated animate__fadeInUp">
            <div class="p-8 lg:p-10">
                <form action="{{ route('users.update', $user->id) }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    @method('PATCH')
                    
                    <div class="flex flex-col items-center justify-center mb-8">
                        <div class="relative group">
                            <div class="w-36 h-36 rounded-full bg-gradient-to-br from-red-500 to-pink-600 flex items-center justify-center text-white text-5xl font-bold shadow-lg ring-4 ring-pink-200 group-hover:ring-pink-400 transition-all duration-300" id="avatar-preview">
                                @if($user->avatar)
                                    <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar de l'utilisateur" class="w-full h-full object-cover rounded-full">
                                @else
                                    <i class="fa-solid fa-camera"></i>
                                @endif
                            </div>
                            <label for="avatar" class="absolute bottom-0 right-0 bg-white rounded-full p-3 shadow-lg cursor-pointer hover:bg-gray-100 transition-colors duration-200 transform group-hover:scale-110">
                                <i class="fa-solid fa-camera"></i>
                            </label>
                            <input type="file" id="avatar" name="avatar" accept="image/*" class="hidden" onchange="previewAvatar(this)">
                        </div>
                        <p class="mt-3 text-sm text-gray-500">Modifier la photo de profil (Optionnel)</p>
                        @error('avatar')
                            <p class="mt-2 text-sm text-red-600 animate__animated animate__shakeX">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="bg-gray-50 rounded-2xl p-6 lg:p-8 border border-gray-100 shadow-sm animate__animated animate__fadeInRight" style="animation-delay: 0.2s;">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fa-solid fa-circle-info text-red-700 mr-3 text-2xl"></i> Informations personnelles
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                            <div>
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">Nom complet <span class="text-red-500">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-red-200 focus:border-red-500 transition-all duration-300 text-gray-800 placeholder-gray-400 @error('name') border-red-500 ring-red-100 @enderror"
                                        placeholder="Saisissez le nom complet">
                                @error('name')
                                    <p class="mt-2 text-sm text-red-600 animate__animated animate__shakeX">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label for="phone" class="block text-sm font-semibold text-gray-700 mb-2">Téléphone (Optionnel)</label>
                                <input type="tel" id="phone" name="phone" value="{{ old('phone', $user->phone) }}" 
                                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-red-200 focus:border-red-500 transition-all duration-300 text-gray-800 placeholder-gray-400 @error('phone') border-red-500 ring-red-100 @enderror"
                                        placeholder="+212 6XX XXX XXX">
                                @error('phone')
                                    <p class="mt-2 text-sm text-red-600 animate__animated animate__shakeX">{{ $message }}</p>
                                @enderror
                            </div>

                            <div class="md:col-span-2">
                                <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">Email <span class="text-red-500">*</span></label>
                                <input type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required
                                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-red-200 focus:border-red-500 transition-all duration-300 text-gray-800 placeholder-gray-400 @error('email') border-red-500 ring-red-100 @enderror"
                                        placeholder="exemple@domaine.com">
                                @error('email')
                                    <p class="mt-2 text-sm text-red-600 animate__animated animate__shakeX">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    {{-- Bloc pour les documents existants et nouveaux --}}
                    <div class="bg-gray-50 rounded-2xl p-6 lg:p-8 border border-gray-100 shadow-sm animate__animated animate__fadeInRight" style="animation-delay: 0.6s;">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fa-solid fa-file-arrow-up text-red-700 mr-3 text-2xl"></i> Documents (Facultatif)
                        </h3>

                        <div id="documents-container" class="space-y-4">
                            {{-- Afficher les documents existants --}}
                            @if($user->documents)
                                @foreach($user->documents as $index => $document)
                                    <div class="document-item bg-white p-4 rounded-xl border border-gray-200 flex items-center justify-between">
                                        <div class="flex items-center w-full md:w-auto">
                                            @php
                                                $icon = 'fa-file-alt';
                                                if (isset($document['type'])) {
                                                    if ($document['type'] == 'pdf') $icon = 'fa-file-pdf text-red-600';
                                                    else if (in_array($document['type'], ['jpg', 'jpeg', 'png', 'gif'])) $icon = 'fa-file-image text-blue-600';
                                                    else if (in_array($document['type'], ['doc', 'docx'])) $icon = 'fa-file-word text-blue-800';
                                                }
                                            @endphp
                                            <i class="fa-solid {{ $icon }} text-2xl mr-4"></i>
                                            <div class="flex-grow">
                                                <label class="block text-sm font-semibold text-gray-700 mb-1">Nom du document</label>
                                                <input type="text" name="documents[{{ $index }}][name]" value="{{ $document['name'] }}"
                                                    class="w-full px-2 py-1 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-200 focus:border-red-500 transition-all"
                                                    placeholder="Ex: Baccalauréat">
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . $document['path']) }}" target="_blank" class="text-gray-500 hover:text-red-700 ml-4">
                                            <i class="fa-solid fa-eye text-lg"></i>
                                        </a>
                                        <button type="button" onclick="removeDocument(this, '{{ $document['path'] }}')" class="ml-2 p-2 text-red-600 hover:text-red-800 transition-colors">
                                            <i class="fa-solid fa-xmark text-lg"></i>
                                        </button>
                                        <input type="hidden" name="documents[{{ $index }}][id]" value="{{ $document['path'] }}">
                                    </div>
                                @endforeach
                            @endif
                        </div>
                        
                        {{-- New hidden input for removed documents (no '[]' in the name) --}}
                        <input type="hidden" name="removed_documents_paths" id="removed-documents-input">

                        <button type="button" onclick="addDocument()" class="mt-4 inline-flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white font-semibold rounded-full transition-colors duration-200">
                            <i class="fa-solid fa-plus mr-2"></i> Ajouter un autre document
                        </button>
                    </div>


                    <div class="bg-gray-50 rounded-2xl p-6 lg:p-8 border border-gray-100 shadow-sm animate__animated animate__fadeInRight" style="animation-delay: 0.8s;">
                        <h3 class="text-xl font-bold text-gray-900 mb-6 flex items-center">
                            <i class="fa-solid fa-users-gear text-red-700 mr-3 text-2xl"></i> Rôles et statut
                        </h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 lg:gap-8">
                            <div>
                                <label for="status" class="block text-sm font-semibold text-gray-700 mb-2">Statut <span class="text-red-500">*</span></label>
                                <select id="status" name="status" required
                                        class="w-full px-5 py-3 border border-gray-300 rounded-xl focus:ring-4 focus:ring-red-200 focus:border-red-500 transition-all duration-300 text-gray-800 @error('status') border-red-500 ring-red-100 @enderror">
                                    <option value="active" {{ old('status', $user->status) == 'active' ? 'selected' : '' }}>Actif</option>
                                    <option value="inactive" {{ old('status', $user->status) == 'inactive' ? 'selected' : '' }}>Inactif</option>
                                    <option value="suspended" {{ old('status', $user->status) == 'suspended' ? 'selected' : '' }}>Suspendu</option>
                                </select>
                                @error('status')
                                    <p class="mt-2 text-sm text-red-600 animate__animated animate__shakeX">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Rôle <span class="text-red-500">*</span></label>
                                <div class="space-y-3 max-h-48 overflow-y-auto pr-2 custom-scrollbar">
                                    @foreach($roles as $role)
                                        <label class="flex items-center p-4 bg-white rounded-xl border border-gray-200 shadow-sm hover:bg-red-50 hover:border-red-300 cursor-pointer transition-all duration-200 transform hover:scale-[1.01]">
                                            <input type="radio" name="role" value="{{ $role->name }}" 
                                                    class="w-10 h-10 text-red-600 border-gray-300 focus:ring-red-500 form-radio transition-colors duration-200"
                                                    {{ old('role', $user->getRoleNames()->first()) == $role->name ? 'checked' : '' }} required>
                                            <span class="ml-4 text-lg font-medium text-gray-800 flex items-center">
                                                @if($role->name == 'admin')
                                                    <i class="fa-solid fa-shield-halved text-red-600 mr-2 text-3xl"></i> Admin
                                                @elseif($role->name == 'editor')
                                                    <i class="fa-solid fa-pen-to-square text-red-500 mr-2 text-3xl"></i> Editeur
                                                @else
                                                    <i class="fa-solid fa-user text-gray-500 mr-2 text-3xl"></i> {{ ucfirst($role->name) }}
                                                @endif
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('role')
                                    <p class="mt-2 text-sm text-red-600 animate__animated animate__shakeX">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col sm:flex-row items-center justify-end space-y-4 sm:space-y-0 sm:space-x-4 pt-8 border-t border-gray-200">
                        <a href="{{ route('users.index') }}" 
                           class="w-full sm:w-auto px-8 py-3 bg-gray-200 hover:bg-gray-300 text-gray-800 font-semibold rounded-full transition-all duration-300 transform hover:scale-105 shadow-md text-center">
                            Annuler
                        </a>
                        <button type="submit" 
                                class="w-full sm:w-auto px-8 py-3 bg-gradient-to-r from-red-600 to-pink-700 hover:from-red-700 hover:to-pink-800 text-white font-semibold rounded-full transition-all duration-300 transform hover:scale-105 shadow-lg">
                            <i class="fa-solid fa-save mr-2"></i> Mettre à jour l'utilisateur
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 8px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 10px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
    <script>
        function previewAvatar(input) {
            const previewContainer = document.getElementById('avatar-preview');
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewContainer.innerHTML = `<img src="${e.target.result}" alt="Avatar" class="w-full h-full object-cover rounded-full">`;
                }
                reader.readAsDataURL(input.files[0]);
            } else {
                previewContainer.innerHTML = `<i class="fa-solid fa-camera-retro text-7xl opacity-80"></i>`;
            }
        }
        
        // Initialisation de l'index des documents et du set des documents à supprimer
        let documentIndex = {{ count($user->documents ?? []) }};
        const removedDocuments = new Set();
        
        function addDocument() {
            const container = document.getElementById('documents-container');
            const newItem = document.createElement('div');
            newItem.classList.add('document-item', 'bg-white', 'p-4', 'rounded-xl', 'border', 'border-gray-200');
            
            // Le HTML pour les nouveaux champs de documents
            newItem.innerHTML = `
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Nom du document</label>
                        <input type="text" name="documents[${documentIndex}][name]" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-200 focus:border-red-500 transition-all"
                                placeholder="Ex: Diplôme de Licence">
                    </div>
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Fichier</label>
                        <div class="flex items-center">
                            <input type="file" name="documents[${documentIndex}][file]"
                                   class="w-full px-4 py-2 border border-gray-300 rounded-xl focus:ring-2 focus:ring-red-200 focus:border-red-500 transition-all">
                            <button type="button" onclick="removeDocument(this, '')" class="ml-2 p-2 text-red-600 hover:text-red-800 transition-colors">
                                <i class="fa-solid fa-xmark"></i>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            container.appendChild(newItem);
            documentIndex++;
        }

        function removeDocument(button, path) {
            const item = button.closest('.document-item');
            if (path) {
                removedDocuments.add(path);
                // Mettre à jour le champ hidden pour la suppression
                const removedInput = document.getElementById('removed-documents-input');
                removedInput.value = JSON.stringify(Array.from(removedDocuments));
            }
            if (item) {
                item.remove();
            }
        }
    </script>
@endpush
@endsection