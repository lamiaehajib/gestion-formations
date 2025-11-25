@extends('layouts.app')

@section('title', 'Ajouter un compte - ' . $application->name)

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-900 via-purple-900 to-slate-900 py-8">
    <div class="container mx-auto px-4 max-w-2xl">
        
        {{-- Retour --}}
        <a href="{{ route('crm.dashboard') }}" 
           class="inline-flex items-center gap-2 text-purple-200 hover:text-white mb-6 transition">
            ← Retour au tableau de bord
        </a>

        {{-- Formulaire --}}
        <div class="bg-white/10 backdrop-blur-lg rounded-2xl border border-white/20 overflow-hidden">
            
            {{-- En-tête --}}
            <div class="bg-gradient-to-r from-purple-600 to-blue-600 p-6">
                <div class="flex items-center gap-4">
                    <span class="text-4xl">{{ $application->icon }}</span>
                    <div>
                        <h1 class="text-2xl font-bold text-white">Ajouter un compte</h1>
                        <p class="text-purple-100">{{ $application->name }}</p>
                    </div>
                </div>
            </div>

            {{-- Corps du formulaire --}}
            <form action="{{ route('crm.accounts.store', $application->id) }}" method="POST" class="p-6 space-y-6">
                @csrf

                {{-- Sélection du rôle --}}
                <div>
                    <label class="block text-purple-200 text-sm font-medium mb-2">
                        Rôle <span class="text-red-400">*</span>
                    </label>
                    <select name="role_name" required
                            class="w-full bg-white/10 border border-white/20 text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <option value="">-- Sélectionnez un rôle --</option>
                        @foreach($availableRoles as $role)
                        <option value="{{ $role }}" {{ old('role_name') == $role ? 'selected' : '' }}>
                            {{ $role }}
                        </option>
                        @endforeach
                    </select>
                    @error('role_name')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Nom d'utilisateur --}}
                <div>
                    <label class="block text-purple-200 text-sm font-medium mb-2">
                        Nom d'utilisateur <span class="text-red-400">*</span>
                    </label>
                    <input type="text" name="username" value="{{ old('username') }}" required
                           placeholder="ex: admin@application.ma"
                           class="w-full bg-white/10 border border-white/20 text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-white/50">
                    @error('username')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mot de passe --}}
                <div>
                    <label class="block text-purple-200 text-sm font-medium mb-2">
                        Mot de passe <span class="text-red-400">*</span>
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                               placeholder="Mot de passe du compte"
                               class="w-full bg-white/10 border border-white/20 text-white rounded-xl px-4 py-3 pr-12 focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-white/50">
                        <button type="button" onclick="togglePasswordVisibility()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-white/70 hover:text-white">
                            👁️
                        </button>
                    </div>
                    @error('password')
                    <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Notes --}}
                <div>
                    <label class="block text-purple-200 text-sm font-medium mb-2">
                        Notes (optionnel)
                    </label>
                    <textarea name="notes" rows="3"
                              placeholder="Informations supplémentaires..."
                              class="w-full bg-white/10 border border-white/20 text-white rounded-xl px-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500 placeholder-white/50 resize-none">{{ old('notes') }}</textarea>
                </div>

                {{-- Boutons --}}
                <div class="flex gap-4 pt-4">
                    <button type="submit"
                            class="flex-1 px-6 py-3 bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700 text-white rounded-xl font-semibold transition">
                        ✅ Enregistrer le compte
                    </button>
                    <a href="{{ route('crm.dashboard') }}"
                       class="px-6 py-3 bg-slate-600 hover:bg-slate-700 text-white rounded-xl transition text-center">
                        Annuler
                    </a>
                </div>
            </form>
        </div>

    </div>
</div>

<script>
function togglePasswordVisibility() {
    const input = document.getElementById('password');
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
@endsection