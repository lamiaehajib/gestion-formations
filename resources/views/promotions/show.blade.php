@extends('layouts.app')

@section('title', 'Détails de la Promotion - ' . $promotion->name)

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');
    
    body {
        font-family: 'Inter', sans-serif;
        background-color: #f8fafc;
    }
    
    /* Clean Card Styles */
    .clean-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .clean-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.12);
        transform: translateY(-2px);
    }
    
    /* Header Styling */
    .header-section {
        background: white;
        border-bottom: 1px solid #e2e8f0;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }
    
    /* Statistics Cards */
    .stat-card {
        border-radius: 12px;
        padding: 20px;
        color: white;
        position: relative;
        overflow: hidden;
        transition: transform 0.3s ease;
    }
    
    .stat-card:hover {
        transform: translateY(-3px);
    }
    
    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 80px;
        height: 80px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 50%;
        transform: translate(30px, -30px);
    }
    
    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.2);
        margin-bottom: 12px;
    }
    
    /* Progress Bar */
    .progress-container {
        background: #e2e8f0;
        border-radius: 20px;
        height: 12px;
        overflow: hidden;
        position: relative;
    }
    
    .progress-bar {
        background: linear-gradient(90deg, #10b981, #059669);
        height: 100%;
        border-radius: 20px;
        position: relative;
        transition: width 1.5s ease-out;
    }
    
    .progress-bar::after {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
        animation: shimmer 2s infinite;
    }
    
    @keyframes shimmer {
        0% { transform: translateX(-100%); }
        100% { transform: translateX(100%); }
    }
    
    /* Clean Table */
    .clean-table {
        background: white;
        border-radius: 16px;
        overflow: hidden;
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.08);
        border: 1px solid #e2e8f0;
    }
    
    .table-header {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-bottom: 2px solid #e2e8f0;
    }
    
    .table-row {
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }
    
    .table-row:hover {
        background: #f8fafc;
        transform: translateX(2px);
    }
    
    .table-row:last-child {
        border-bottom: none;
    }
    
    /* Avatar Styling */
    .student-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        border: 2px solid #e2e8f0;
        transition: all 0.3s ease;
    }
    
    .student-avatar:hover {
        border-color: #ef4444;
        transform: scale(1.05);
    }
    
    /* Status Badges */
    .status-badge {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 12px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    
    .status-paid {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }
    
    .status-partial {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #fde68a;
    }
    
    .status-unpaid {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #fecaca;
    }
    
    /* Button Styles */
    .btn-modern {
        padding: 10px 20px;
        border-radius: 10px;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.3s ease;
        border: none;
        cursor: pointer;
    }
    
    .btn-primary {
        background: #ef4444;
        color: white;
    }
    
    .btn-primary:hover {
        background: #dc2626;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3);
    }
    
    .btn-success {
        background: #10b981;
        color: white;
    }
    
    .btn-success:hover {
        background: #059669;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
    }
    
    .btn-purple {
        background: #8b5cf6;
        color: white;
    }
    
    .btn-purple:hover {
        background: #7c3aed;
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.3);
    }
    
    .btn-details {
        background: #ef4444;
        color: white;
        padding: 8px 16px;
        font-size: 13px;
    }
    
    .btn-details:hover {
        background: #dc2626;
        color: white;
        text-decoration: none;
    }
    
    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #64748b;
    }
    
    .empty-icon {
        width: 64px;
        height: 64px;
        background: #f1f5f9;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 16px;
    }
    
    /* Responsive */
    @media (max-width: 768px) {
        .stat-card {
            margin-bottom: 16px;
        }
        
        .btn-modern {
            width: 100%;
            justify-content: center;
            margin-bottom: 8px;
        }
        
        .table-responsive {
            overflow-x: auto;
        }
    }
    
    /* Success Alert */
    .alert-success {
        background: #dcfce7;
        border: 1px solid #bbf7d0;
        color: #166534;
        padding: 16px;
        border-radius: 12px;
        margin-bottom: 24px;
    }
    
    /* Title with icon */
    .section-title {
        font-size: 20px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 20px;
    }
    
    .title-icon {
        width: 24px;
        height: 24px;
        color: #ef4444;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen" style="background-color: #f8fafc;">
    <!-- Header Section -->
    <div class="header-section">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                <div class="flex items-center">
                    <a href="{{ route('promotions.index') }}" class="mr-4 p-2 rounded-lg hover:bg-gray-100 transition-colors duration-200">
                        <i class="fas fa-arrow-left"></i>
                    </a>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900 flex items-center">
                            <i class="fas fa-graduation-cap text-red-500 mr-3"></i>
                            {{ $promotion->name }}
                        </h1>
                        <p class="mt-1 text-gray-600">{{ $promotion->formation->title }} - {{ $promotion->year }}</p>
                        <div class="mt-2">
                            <span class="bg-red-500 text-white text-xs font-semibold px-3 py-1 rounded-full uppercase">
                                {{ $promotion->formation->category->name }}
                            </span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row gap-3 mt-4 lg:mt-0">
                    <a href="{{ route('promotions.generateReport', ['promotion' => $promotion->id, 'format' => 'pdf']) }}"
                       class="btn-modern btn-primary">
                        <i class="fas fa-file-pdf"></i>
                        Exporter en PDF
                    </a>
                    <a href="{{ route('promotions.generateReport', ['promotion' => $promotion->id, 'format' => 'excel']) }}"
                       class="btn-modern btn-success">
                        <i class="fas fa-file-excel"></i>
                        Exporter en Excel
                    </a>
                   <a href="{{ route('promotions.edit', $promotion) }}" class="button-magnetic bg-gradient-to-r from-purple-600 to-purple-700 hover:from-purple-700 hover:to-purple-800 text-white px-6 py-2 rounded-lg font-medium shadow-md flex items-center">
    <i class="fa-solid fa-pen-to-square mr-2"></i>
    Modifier
</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @if(session('success'))
            <div class="alert-success">
                <div class="flex items-center">
                    <i class="fas fa-check-circle text-green-600 mr-2"></i>
                    {{ session('success') }}
                </div>
            </div>
        @endif

        <!-- Statistics Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="stat-card" style="background: linear-gradient(135deg, #ef4444, #dc2626);">
                <div class="stat-icon">
                    <i class="fas fa-users text-xl"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90 mb-1">Total Étudiants</p>
                    <p class="text-2xl font-bold">{{ $statistics['total_students'] }}</p>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #10b981, #059669);">
                <div class="stat-icon">
                    <i class="fas fa-dollar-sign text-xl"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90 mb-1">Revenus Totaux</p>
                    <p class="text-2xl font-bold">{{ number_format($statistics['total_revenue'], 0) }}</p>
                    <p class="text-xs opacity-80">MAD</p>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #8b5cf6, #7c3aed);">
                <div class="stat-icon">
                    <i class="fas fa-credit-card text-xl"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90 mb-1">Montant Payé</p>
                    <p class="text-2xl font-bold">{{ number_format($statistics['total_paid'], 0) }}</p>
                    <p class="text-xs opacity-80">MAD</p>
                </div>
            </div>

            <div class="stat-card" style="background: linear-gradient(135deg, #f59e0b, #d97706);">
                <div class="stat-icon">
                    <i class="fas fa-chart-line text-xl"></i>
                </div>
                <div>
                    <p class="text-sm opacity-90 mb-1">Taux de Completion</p>
                    <p class="text-2xl font-bold">{{ $statistics['completion_percentage'] }}%</p>
                </div>
            </div>
        </div>

        <!-- Progress Section -->
        <div class="clean-card p-6 mb-8">
            <h3 class="section-title">
                <i class="fas fa-chart-line title-icon"></i>
                Progression des Paiements
            </h3>
            <div class="flex justify-between text-sm font-medium mb-4">
                <span class="text-green-600">{{ number_format($statistics['total_paid'], 0) }} MAD payés</span>
                <span class="text-red-600">{{ number_format($statistics['total_remaining'], 0) }} MAD restants</span>
            </div>
            <div class="progress-container">
                <div class="progress-bar" style="width: {{ $statistics['completion_percentage'] }}%"></div>
            </div>
            <div class="flex justify-between text-xs text-gray-500 mt-2">
                <span>0%</span>
                <span>50%</span>
                <span>100%</span>
            </div>
        </div>

        <!-- Students Table -->
        <div class="clean-table">
            <div class="table-header p-6">
                <h3 class="section-title mb-0">
                    <i class="fas fa-users title-icon"></i>
                    Liste des Étudiants ({{ $statistics['total_students'] }})
                </h3>
            </div>
            
            <div class="table-responsive">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Étudiant
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                État de Paiement
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Montant Payé
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Reste à Payer
                            </th>
                            <th class="px-6 py-4 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($studentsData as $student)
                            <tr class="table-row">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <img src="{{ Storage::url($student->inscription->user->avatar) }}"
                                                         class="avatar avatar-sm me-3 border-radius-lg hover-zoom" alt="user avatar">
                                        <div class="ml-4">
                                            <div class="text-sm font-semibold text-gray-900">{{ $student['user']->name }}</div>
                                            <div class="text-sm text-gray-500">{{ $student['user']->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusClass = [
                                            'Payé' => 'status-paid',
                                            'Partiellement payé' => 'status-partial',
                                            'Non payé' => 'status-unpaid',
                                        ][$student['payment_status']] ?? 'status-unpaid';
                                    @endphp
                                    <span class="status-badge {{ $statusClass }}">
                                        {{ $student['payment_status'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    {{ number_format($student['paid_amount'], 0) }} MAD
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                                    {{ number_format($student['remaining_amount'], 0) }} MAD
                                </td>
                                <td class="px-6 py-4">
                                    <a href="{{ route('promotions.studentPaymentHistory', ['promotion' => $promotion->id, 'user' => $student['user']->id]) }}" 
                                       class="btn-modern btn-details">
                                        <i class="fas fa-arrow-right"></i>
                                        Voir Détails
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="empty-state">
                                    <div class="empty-icon">
                                        <i class="fas fa-users text-xl text-gray-400"></i>
                                    </div>
                                    <p class="text-lg font-medium text-gray-600">Aucun étudiant associé</p>
                                    <p class="text-sm text-gray-500">Cette promotion ne contient aucun étudiant pour le moment.</p>
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

@section('scripts')
<script src="https://kit.fontawesome.com/a117b2b918.js" crossorigin="anonymous"></script>
<script>
function openEditModal() {
    // Add your modal logic here
    console.log('Opening edit modal');
}

// Progress bar animation
document.addEventListener('DOMContentLoaded', function() {
    const progressBar = document.querySelector('.progress-bar');
    if (progressBar) {
        const targetWidth = progressBar.style.width;
        progressBar.style.width = '0%';
        setTimeout(() => {
            progressBar.style.width = targetWidth;
        }, 500);
    }
});
</script>
@endsection