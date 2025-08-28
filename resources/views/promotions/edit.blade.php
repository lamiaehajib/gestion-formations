@extends('layouts.app')

@section('title', 'Modifier la Promotion - ' . $promotion->name)

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    body {
        font-family: 'Inter', sans-serif;
    }

    .glass-effect {
        backdrop-filter: blur(20px);
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    
    .floating-animation {
        animation: floating 3s ease-in-out infinite;
    }
    
    @keyframes floating {
        0%, 100% { transform: translateY(0px); }
        50% { transform: translateY(-10px); }
    }
    
    .pulse-glow {
        animation: pulse-glow 2s ease-in-out infinite alternate;
    }
    
    @keyframes pulse-glow {
        from { box-shadow: 0 0 20px rgba(239, 68, 68, 0.3); }
        to { box-shadow: 0 0 30px rgba(239, 68, 68, 0.6), 0 0 40px rgba(239, 68, 68, 0.4); }
    }
    
    .morphing-bg {
        background: linear-gradient(-45deg, #fdf2f8, #ffffff, #fef2f2, #fff1f2);
        background-size: 400% 400%;
        animation: gradient-shift 15s ease infinite;
    }
    
    @keyframes gradient-shift {
        0% { background-position: 0% 50%; }
        50% { background-position: 100% 50%; }
        100% { background-position: 0% 50%; }
    }
    
    .gradient-text {
        background: linear-gradient(135deg, #ec4899, #dc2626);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    
    .button-magnetic {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    
    .button-magnetic:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    
    .input-focus:focus {
        border-color: #D32F2F;
        box-shadow: 0 0 0 3px rgba(211, 47, 47, 0.2);
    }
    
    .alert-error {
        background: #fee2e2;
        border: 1px solid #fca5a5;
        color: #991b1b;
    }
    
    .card-details {
        background: linear-gradient(135deg, #fef2f2 0%, #ffffff 100%);
    }
</style>
@endpush

@section('content')
<div class="min-h-screen morphing-bg">
    <!-- Header Section -->
    <div class="glass-effect shadow-lg border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center">
                <a href="{{ route('promotions.show', $promotion) }}" class="mr-4 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <i class="fa-solid fa-arrow-left text-gray-600"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold gradient-text flex items-center">
                        <i class="fa-solid fa-pen-to-square text-2xl text-purple-600 mr-3"></i>
                        Modifier la Promotion
                    </h1>
                    <p class="mt-2 text-gray-600">{{ $promotion->name }} - {{ $promotion->year }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Error Messages -->
        @if($errors->any())
            <div class="mb-6 alert-error text-red-800 px-6 py-4 rounded-xl shadow-lg modal-enter">
                <div class="flex items-center">
                    <div class="bg-red-100 p-2 rounded-full mr-4">
                        <i class="fa-solid fa-exclamation-circle text-red-600 text-lg"></i>
                    </div>
                    <div>
                        <span class="font-bold">Des erreurs ont été trouvées :</span>
                        <ul class="list-disc list-inside mt-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form Card -->
        <div class="glass-effect rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-[#D32F2F] to-[#C2185B] text-white p-6">
                <h2 class="text-xl font-semibold">Modifier les Informations de la Promotion</h2>
                <p class="text-white mt-1 opacity-90">Modifiez les détails de la promotion et cliquez sur 'Enregistrer'</p>
            </div>

            <form method="POST" action="{{ route('promotions.update', $promotion) }}" class="p-8 space-y-6">
                @csrf
                @method('PUT')

                <!-- Promotion Name -->
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                        Nom de la Promotion *
                    </label>
                    <input type="text" name="name" id="name" value="{{ old('name', $promotion->name) }}" required
                           class="input-focus w-full px-4 py-3 border-2 border-gray-300 rounded-lg transition-all duration-200 font-semibold text-gray-800">
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-2">
                        Description (Optionnelle)
                    </label>
                    <textarea name="description" id="description" rows="4" 
                              class="input-focus w-full px-4 py-3 border-2 border-gray-300 rounded-lg transition-all duration-200 resize-none font-medium text-gray-800">{{ old('description', $promotion->description) }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Maximum 1000 caractères</p>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('promotions.show', $promotion) }}" 
                       class="button-magnetic flex-1 bg-gradient-to-r from-gray-300 to-gray-400 hover:from-gray-400 hover:to-gray-500 text-gray-800 text-center py-3 px-6 rounded-xl font-bold shadow-lg">
                        <i class="fa-solid fa-ban mr-2"></i>
                        Annuler
                    </a>
                    <button type="submit" 
                            class="button-magnetic flex-1 bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white text-center py-3 px-6 rounded-xl font-bold shadow-lg">
                        <i class="fa-solid fa-save mr-2"></i>
                        Enregistrer
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://kit.fontawesome.com/a117b2b918.js" crossorigin="anonymous"></script>
<script>
// Live character count for description
document.addEventListener('DOMContentLoaded', function() {
    const descriptionInput = document.getElementById('description');
    const maxLength = 1000;
    const helpText = descriptionInput.parentNode.querySelector('.text-gray-500');

    descriptionInput.addEventListener('input', function() {
        const currentLength = this.value.length;
        const remaining = maxLength - currentLength;
        
        helpText.textContent = `${remaining} caractères restants`;
        if (remaining < 0) {
            helpText.className = 'mt-1 text-sm text-red-500 font-bold';
        } else if (remaining < 100) {
            helpText.className = 'mt-1 text-sm text-orange-500 font-bold';
        } else {
            helpText.className = 'mt-1 text-sm text-gray-500';
        }
    });

    // Initial check on page load
    descriptionInput.dispatchEvent(new Event('input'));
});

// Form submission loading state
document.querySelector('form').addEventListener('submit', function(e) {
    const submitButton = this.querySelector('button[type="submit"]');
    if (submitButton) {
        submitButton.disabled = true;
        submitButton.innerHTML = `
            <i class="fa-solid fa-spinner fa-spin mr-2"></i>
            Enregistrement...
        `;
        submitButton.classList.add('opacity-75');
    }
});
</script>
@endsection
