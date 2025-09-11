@extends('layouts.app')

@section('title', 'Gestion du suivi annuel')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-pink-50 via-white to-red-50">
    <!-- Header Section -->
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col md:flex-row md:items-center md:justify-between">
                <div>
                    <h1 class="text-3xl font-bold text-gray-900 flex items-center">
                        <i class="fa-solid fa-graduation-cap text-[#D32F2F] text-2xl mr-3"></i>
                        Gestion du suivi annuel
                    </h1>
                    <p class="mt-2 text-gray-600">Gérez le suivi annuel et suivez les paiements des étudiants</p>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 mt-4 md:mt-0">
                    <button onclick="openBulkCreateModal()" class="bg-gradient-to-r from-[#C2185B] to-[#D32F2F] hover:from-[#D32F2F] hover:to-[#C2185B] text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg flex items-center">
                        <i class="fa-solid fa-layer-group text-white mr-2"></i>
                        Création en Lot
                    </button>
                    <a href="{{ route('promotions.create') }}" class="bg-gradient-to-r from-pink-600 to-red-600 hover:from-red-600 hover:to-pink-600 text-white px-6 py-2 rounded-lg font-medium transition-all duration-200 transform hover:scale-105 shadow-lg flex items-center">
                        <i class="fa-solid fa-plus text-white mr-2"></i>
                        Nouvelle Promotion
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-check text-green-600 mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        @if(session('error') || $errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg shadow-sm">
                <div class="flex items-center">
                    <i class="fa-solid fa-circle-exclamation text-red-600 mr-2"></i>
                    {{ session('error') ?: $errors->first() }}
                </div>
            </div>
        @endif

        <!-- Filters and Search -->
        <div class="bg-white rounded-lg shadow-xl border border-gray-200 p-6 mb-8">
            <div class="flex flex-col lg:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" id="search" placeholder="Rechercher par nom, formation ou année..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ef4444] focus:border-transparent transition-all duration-200">
                </div>
                <div class="flex flex-col sm:flex-row gap-3">
                    <select id="yearFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ef4444] focus:border-transparent">
                        <option value="">Toutes les années</option>
                        @for($year = date('Y') + 2; $year >= 2020; $year--)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endfor
                    </select>
                    <select id="categoryFilter" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ef4444] focus:border-transparent">
                        <option value="">Toutes les catégories</option>
                        <option value="licence">Licence</option>
                        <option value="master">Master</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Promotions Grid -->
        @if($promotions->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6 mb-8">
                @foreach($promotions as $promotion)
                    <div class="bg-white rounded-xl shadow-md hover:shadow-xl transition-all duration-300 border border-gray-100 overflow-hidden group">
                        <!-- Card Header -->
                        <div class="bg-gradient-to-r from-pink-600 to-[#D32F2F] text-white p-6">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold mb-1">{{ $promotion->name }}</h3>
                                    <p class="text-white text-sm opacity-90">{{ $promotion->formation->title }}</p>
                                </div>
                                <div class="text-right">
                                    <div class="text-2xl font-bold">{{ $promotion->year }}</div>
                                    <div class="text-xs text-white opacity-80 uppercase tracking-wide">
                                        {{ $promotion->formation->category->name }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Card Body -->
                        <div class="p-6">
                            @php
                                $studentsCount = $promotion->users->count();
                                $totalRevenue = $promotion->users->sum(function($user) use ($promotion) {
                                    $inscription = $user->inscriptions->where('formation_id', $promotion->formation_id)->first();
                                    return $inscription ? $inscription->total_amount : 0;
                                });
                                $totalPaid = $promotion->users->sum(function($user) use ($promotion) {
                                    $inscription = $user->inscriptions->where('formation_id', $promotion->formation_id)->first();
                                    return $inscription ? $inscription->paid_amount : 0;
                                });
                                $completionRate = $totalRevenue > 0 ? round(($totalPaid / $totalRevenue) * 100, 1) : 0;
                            @endphp

                            <!-- Statistics -->
                            <div class="grid grid-cols-2 gap-4 mb-6">
                                <div class="text-center p-3 bg-gray-50 rounded-lg border border-gray-200">
                                    <i class="fa-solid fa-users text-2xl text-gray-800 mb-2"></i>
                                    <div class="text-lg font-bold text-gray-900">{{ $studentsCount }}</div>
                                    <div class="text-xs text-gray-600 uppercase tracking-wide">Étudiants</div>
                                </div>
                                <div class="text-center p-3 bg-green-50 rounded-lg border border-green-200">
                                    <i class="fa-solid fa-percent text-2xl text-green-600 mb-2"></i>
                                    <div class="text-lg font-bold text-green-600">{{ $completionRate }}%</div>
                                    <div class="text-xs text-gray-600 uppercase tracking-wide">Payé</div>
                                </div>
                            </div>

                            <!-- Progress Bar -->
                            <div class="mb-4">
                                <div class="flex justify-between text-sm text-gray-600 mb-1">
                                    <span>Progression des paiements</span>
                                    <span>{{ number_format($totalPaid, 0) }} / {{ number_format($totalRevenue, 0) }} MAD</span>
                                </div>
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-gradient-to-r from-green-400 to-green-600 h-2 rounded-full transition-all duration-500" 
                                         style="width: {{ $completionRate }}%"></div>
                                </div>
                            </div>

                            @if($promotion->description)
                                <p class="text-gray-600 text-sm mb-4">{{ Str::limit($promotion->description, 100) }}</p>
                            @endif

                            <!-- Action Buttons -->
                            <div class="flex gap-2 pt-4 border-t border-gray-100">
                                <a href="{{ route('promotions.show', $promotion) }}" 
                                   class="flex-1 bg-[#ef4444] hover:bg-red-700 text-white text-center py-2 px-4 rounded-lg text-sm font-medium transition-colors duration-200">
                                    <i class="fa-solid fa-eye mr-2"></i>
                                    Détails
                                </a>
                                <button onclick="deletePromotion({{ $promotion->id }})" 
                                        class="bg-gray-200 hover:bg-gray-300 text-red-600 py-2 px-3 rounded-lg text-sm transition-colors duration-200">
                                    <i class="fa-solid fa-trash-can"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                {{ $promotions->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-12 text-center">
                <i class="fa-solid fa-box-open text-gray-400 text-5xl mb-4"></i>
                <h3 class="text-lg font-medium text-gray-900 mb-2">Aucune promotion trouvée</h3>
                <p class="text-gray-600 mb-6">Commencez par créer votre première promotion.</p>
                <a href="{{ route('promotions.create') }}" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium transition-colors duration-200">
                    <i class="fa-solid fa-plus mr-2"></i>
                    Créer une promotion
                </a>
            </div>
        @endif
    </div>
</div>

<!-- Bulk Create Modal -->
<div id="bulkCreateModal" class="fixed inset-0 bg-black bg-opacity-50 hidden items-center justify-center z-50">
    <div class="bg-white rounded-lg p-6 w-full max-w-md mx-4">
        <h3 class="text-lg font-semibold mb-4">Création en Lot</h3>
        <form id="bulkCreateForm" method="POST" action="{{ route('promotions.bulk-create') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-sm font-medium text-gray-700 mb-2">Année</label>
                <select name="year" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ef4444]">
                    @for($year = date('Y'); $year <= date('Y') + 2; $year++)
                        <option value="{{ $year }}">{{ $year }}</option>
                    @endfor
                </select>
            </div>
            <div class="mb-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">Formations</label>
                <div class="max-h-40 overflow-y-auto border border-gray-300 rounded-lg p-2">
                    <div id="formationsList">
                        <!-- Will be populated by JavaScript -->
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <button type="button" onclick="closeBulkCreateModal()" class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-800 py-2 px-4 rounded-lg">
                    Annuler
                </button>
                <button type="submit" class="flex-1 bg-[#D32F2F] hover:bg-[#C2185B] text-white py-2 px-4 rounded-lg">
                    Créer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Search and filter functionality
document.getElementById('search').addEventListener('input', filterPromotions);
document.getElementById('yearFilter').addEventListener('change', filterPromotions);
document.getElementById('categoryFilter').addEventListener('change', filterPromotions);

function filterPromotions() {
    const search = document.getElementById('search').value.toLowerCase();
    const year = document.getElementById('yearFilter').value;
    const category = document.getElementById('categoryFilter').value.toLowerCase();
    
    const cards = document.querySelectorAll('[class*="grid-cols-1"] > div');
    
    cards.forEach(card => {
        const text = card.textContent.toLowerCase();
        const matchesSearch = search === '' || text.includes(search);
        const matchesYear = year === '' || text.includes(year);
        const matchesCategory = category === '' || text.includes(category);
        
        card.style.display = matchesSearch && matchesYear && matchesCategory ? 'block' : 'none';
    });
}

// Bulk create modal
function openBulkCreateModal() {
    document.getElementById('bulkCreateModal').classList.remove('hidden');
    document.getElementById('bulkCreateModal').classList.add('flex');
    loadEligibleFormations();
}

function closeBulkCreateModal() {
    document.getElementById('bulkCreateModal').classList.add('hidden');
    document.getElementById('bulkCreateModal').classList.remove('flex');
}

async function loadEligibleFormations() {
    try {
        const response = await fetch('/api/eligible-formations');
        const formations = await response.json();
        
        const container = document.getElementById('formationsList');
        container.innerHTML = '';
        
        formations.forEach(formation => {
            const div = document.createElement('div');
            div.className = 'flex items-center p-2 hover:bg-gray-50 rounded';
            div.innerHTML = `
                <input type="checkbox" name="formation_ids[]" value="${formation.id}" class="mr-2 text-[#D32F2F] focus:ring-[#D32F2F]">
                <div class="flex-1">
                    <div class="font-medium">${formation.title}</div>
                    <div class="text-sm text-gray-600">${formation.category} - ${formation.students_count} étudiants</div>
                </div>
            `;
            container.appendChild(div);
        });
    } catch (error) {
        console.error('Error loading formations:', error);
    }
}

function deletePromotion(id) {
    if (confirm('Êtes-vous sûr de vouloir supprimer cette promotion ?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = `/promotions/${id}`;
        form.innerHTML = `
            @csrf
            @method('DELETE')
        `;
        document.body.appendChild(form);
        form.submit();
    }
}

// Close modal when clicking outside
document.getElementById('bulkCreateModal').addEventListener('click', function(e) {
    if (e.target === this) {
        closeBulkCreateModal();
    }
});
</script>
<script src="https://kit.fontawesome.com/a117b2b918.js" crossorigin="anonymous"></script>
@endpush
