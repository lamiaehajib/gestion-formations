@extends('layouts.app')

@section('title', 'Créer une Promotion')

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
                <a href="{{ route('promotions.index') }}" class="mr-4 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                    <i class="fa-solid fa-arrow-left"></i>
                </a>
                <div>
                    <h1 class="text-3xl font-bold gradient-text flex items-center">
                        <i class="fa-solid fa-plus-circle text-2xl text-red-600 mr-3"></i>
                        Créer une Nouvelle Promotion
                    </h1>
                    <p class="mt-2 text-gray-600">Remplissez les détails pour créer une promotion</p>
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
                <h2 class="text-xl font-semibold">Informations de la Promotion</h2>
                <p class="text-white mt-1 opacity-90">Remplissez les détails pour créer une nouvelle promotion</p>
            </div>

            <form method="POST" action="{{ route('promotions.store') }}" class="p-8 space-y-6">
                @csrf

                <!-- Formation Selection -->
                <div class="card-details rounded-xl p-6 border border-gray-200 shadow-md">
                    <label for="formation_id" class="block text-sm font-bold text-gray-700 mb-3 flex items-center">
                        <i class="fa-solid fa-book-open text-[#D32F2F] text-lg mr-2"></i>
                        Formation *
                    </label>
                    <select name="formation_id" id="formation_id" required 
                            class="input-focus w-full px-4 py-3 border-2 border-gray-300 rounded-lg transition-all duration-200 font-semibold text-gray-800">
                        <option value="">Sélectionnez une formation...</option>
                        @foreach($formations as $formation)
                            <option value="{{ $formation->id }}" 
                                    data-category="{{ $formation->category->name }}"
                                    data-students="{{ $formation->inscriptions->count() }}"
                                    data-price="{{ $formation->price }}"
                                    {{ old('formation_id') == $formation->id ? 'selected' : '' }}>
                                {{ $formation->title }} ({{ ucfirst($formation->category->name) }})
                            </option>
                        @endforeach
                    </select>
                    <div id="formation-info" class="mt-4 p-4 bg-white rounded-xl border border-gray-200 shadow-sm hidden">
                        <!-- Formation details will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Basic Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Promotion Name -->
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-2">
                            Nom de la Promotion *
                        </label>
                        <input type="text" name="name" id="name" value="{{ old('name') }}" required
                               placeholder="Ex: Licence Informatique 2025"
                               class="input-focus w-full px-4 py-3 border-2 border-gray-300 rounded-lg transition-all duration-200 font-semibold text-gray-800">
                    </div>

                    <!-- Year -->
                    <div>
                        <label for="year" class="block text-sm font-bold text-gray-700 mb-2">
                            Année *
                        </label>
                        <select name="year" id="year" required 
                                class="input-focus w-full px-4 py-3 border-2 border-gray-300 rounded-lg transition-all duration-200 font-semibold text-gray-800">
                            @foreach($availableYears as $year)
                                <option value="{{ $year }}" {{ old('year', date('Y')) == $year ? 'selected' : '' }}>
                                    {{ $year }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Description -->
                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-2">
                        Description (Optionnelle)
                    </label>
                    <textarea name="description" id="description" rows="4" 
                              placeholder="Décrivez cette promotion..."
                              class="input-focus w-full px-4 py-3 border-2 border-gray-300 rounded-lg transition-all duration-200 resize-none font-medium text-gray-800">{{ old('description') }}</textarea>
                    <p class="mt-1 text-sm text-gray-500">Maximum 1000 caractères</p>
                </div>

                <!-- Preview Section -->
                <div id="preview-section" class="card-details rounded-xl p-6 border border-gray-200 shadow-md hidden">
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center">
                        <i class="fa-solid fa-clipboard-check text-green-600 text-lg mr-2"></i>
                        Aperçu de la Promotion
                    </h3>
                    <div id="preview-content">
                        <!-- Preview content will be populated by JavaScript -->
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex flex-col sm:flex-row gap-4 pt-6 border-t border-gray-200">
                    <a href="{{ route('promotions.index') }}" 
                       class="button-magnetic flex-1 bg-gradient-to-r from-gray-300 to-gray-400 hover:from-gray-400 hover:to-gray-500 text-gray-800 text-center py-3 px-6 rounded-xl font-bold shadow-lg">
                        <i class="fa-solid fa-ban mr-2"></i>
                        Annuler
                    </a>
                    <button type="submit" 
                            class="button-magnetic flex-1 bg-gradient-to-r from-[#D32F2F] to-[#C2185B] hover:from-[#C2185B] hover:to-[#D32F2F] text-white text-center py-3 px-6 rounded-xl font-bold shadow-lg">
                        <i class="fa-solid fa-plus mr-2"></i>
                        Créer la Promotion
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
// Auto-generate promotion name based on formation and year
document.getElementById('formation_id').addEventListener('change', updateFormationInfo);
document.getElementById('year').addEventListener('change', updatePromotionName);

function updateFormationInfo() {
    const select = document.getElementById('formation_id');
    const selectedOption = select.options[select.selectedIndex];
    const infoDiv = document.getElementById('formation-info');
    
    if (selectedOption.value) {
        const category = selectedOption.getAttribute('data-category');
        const students = selectedOption.getAttribute('data-students');
        const price = selectedOption.getAttribute('data-price');
        
        infoDiv.innerHTML = `
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-tag text-[#D32F2F] mr-2"></i>
                    <span class="font-bold text-gray-700">Catégorie:</span>
                    <span class="ml-1 capitalize font-medium text-gray-900">${category}</span>
                </div>
                <div class="flex items-center">
                    <i class="fa-solid fa-users text-green-600 mr-2"></i>
                    <span class="font-bold text-gray-700">Étudiants:</span>
                    <span class="ml-1 font-medium text-gray-900">${students}</span>
                </div>
                <div class="flex items-center">
                    <i class="fa-solid fa-money-bill-wave text-purple-600 mr-2"></i>
                    <span class="font-bold text-gray-700">Prix:</span>
                    <span class="ml-1 font-medium text-gray-900">${parseFloat(price).toLocaleString()} MAD</span>
                </div>
            </div>
        `;
        infoDiv.classList.remove('hidden');
        
        updatePromotionName();
        updatePreview();
    } else {
        infoDiv.classList.add('hidden');
        document.getElementById('preview-section').classList.add('hidden');
    }
}

function updatePromotionName() {
    const formationSelect = document.getElementById('formation_id');
    const yearSelect = document.getElementById('year');
    const nameInput = document.getElementById('name');
    
    if (formationSelect.value && yearSelect.value) {
        const formationText = formationSelect.options[formationSelect.selectedIndex].text;
        const formationTitle = formationText.split(' (')[0]; // Remove category part
        const year = yearSelect.value;
        
        if (!nameInput.value || nameInput.value.includes('Promotion')) {
            nameInput.value = `${formationTitle} - Promotion ${year}`;
        }
        
        updatePreview();
    }
}

function updatePreview() {
    const formationSelect = document.getElementById('formation_id');
    const yearSelect = document.getElementById('year');
    const nameInput = document.getElementById('name');
    const descriptionInput = document.getElementById('description');
    const previewSection = document.getElementById('preview-section');
    const previewContent = document.getElementById('preview-content');
    
    if (formationSelect.value && yearSelect.value && nameInput.value) {
        const selectedOption = formationSelect.options[formationSelect.selectedIndex];
        const formationTitle = selectedOption.text.split(' (')[0];
        const category = selectedOption.getAttribute('data-category');
        const students = selectedOption.getAttribute('data-students');
        const price = selectedOption.getAttribute('data-price');
        
        const totalRevenue = parseFloat(price) * parseInt(students);
        
        previewContent.innerHTML = `
            <div class="bg-white rounded-lg p-4 border border-gray-200 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <h4 class="font-bold text-lg text-gray-900">${nameInput.value}</h4>
                        <p class="text-gray-600 text-sm">${formationTitle}</p>
                    </div>
                    <div class="text-right">
                        <div class="text-2xl font-black text-[#D32F2F]">${yearSelect.value}</div>
                        <div class="text-xs text-gray-500 uppercase">${category}</div>
                    </div>
                </div>
                
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <i class="fa-solid fa-users text-2xl text-blue-600 mb-1"></i>
                        <div class="text-xl font-bold text-gray-900">${students}</div>
                        <div class="text-sm text-gray-600">Étudiants</div>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-3 text-center">
                        <i class="fa-solid fa-sack-dollar text-2xl text-green-600 mb-1"></i>
                        <div class="text-xl font-bold text-green-600">${totalRevenue.toLocaleString()}</div>
                        <div class="text-sm text-gray-600">Revenus (MAD)</div>
                    </div>
                </div>
                
                ${descriptionInput.value ? `
                    <div class="bg-gray-50 rounded-lg p-3">
                        <p class="text-gray-700 text-sm">${descriptionInput.value}</p>
                    </div>
                ` : ''}
            </div>
        `;
        
        previewSection.classList.remove('hidden');
    } else {
        previewSection.classList.add('hidden');
    }
}

// Add event listeners for real-time preview updates
document.getElementById('name').addEventListener('input', updatePreview);
document.getElementById('description').addEventListener('input', updatePreview);

// Character count for description
const descriptionInput = document.getElementById('description');
const maxLength = 1000;

descriptionInput.addEventListener('input', function() {
    const currentLength = this.value.length;
    const remaining = maxLength - currentLength;
    
    let helpText = this.parentNode.querySelector('.text-gray-500');
    if (remaining < 100) {
        helpText.textContent = `${remaining} caractères restants`;
        helpText.className = remaining < 0 ? 'mt-1 text-sm text-red-500 font-bold' : 'mt-1 text-sm text-orange-500 font-bold';
    } else {
        helpText.textContent = 'Maximum 1000 caractères';
        helpText.className = 'mt-1 text-sm text-gray-500';
    }
});

// Form validation
document.querySelector('form').addEventListener('submit', function(e) {
    const nameInput = document.getElementById('name');
    const formationSelect = document.getElementById('formation_id');
    
    if (!nameInput.value.trim()) {
        e.preventDefault();
        alert('Veuillez saisir un nom pour la promotion.');
        nameInput.focus();
        return;
    }
    
    if (!formationSelect.value) {
        e.preventDefault();
        alert('Veuillez sélectionner une formation.');
        formationSelect.focus();
        return;
    }
    
    // Show loading state
    const submitButton = this.querySelector('button[type="submit"]');
    const originalText = submitButton.innerHTML;
    submitButton.disabled = true;
    submitButton.innerHTML = `
        <i class="fa-solid fa-spinner fa-spin mr-2"></i>
        Création en cours...
    `;
});
</script>
@endsection
