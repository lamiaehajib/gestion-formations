@extends('layouts.app')

@section('title', 'reclamations')

@section('content')
<style>
/* Enhanced Reclamations Styles - Premium Design */

:root {
  --primary-red: #D32F2F;
  --secondary-pink: #C2185B;
  --accent-red: #ef4444;
  --dark-red: #B71C1C;
  --light-pink: #FCE4EC;
  --gradient-primary: linear-gradient(135deg, #D32F2F 0%, #C2185B 50%, #ef4444 100%);
  --gradient-secondary: linear-gradient(45deg, #C2185B 0%, #ef4444 100%);
  --gradient-dark: linear-gradient(135deg, #B71C1C 0%, #880E4F 100%);
  --gradient-light: linear-gradient(135deg, #FFEBEE 0%, #FCE4EC 100%);
  --shadow-primary: 0 20px 40px rgba(211, 47, 47, 0.15);
  --shadow-secondary: 0 10px 30px rgba(194, 24, 91, 0.2);
  --shadow-hover: 0 25px 50px rgba(211, 47, 47, 0.3);
  --shadow-glow: 0 0 30px rgba(239, 68, 68, 0.4);
}

/* Advanced Animations */
@keyframes fadeInUp {
  0% {
    opacity: 0;
    transform: translateY(30px);
  }
  100% {
    opacity: 1;
    transform: translateY(0);
  }
}

@keyframes scaleIn {
  0% {
    opacity: 0;
    transform: scale(0.8);
  }
  100% {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes slideInLeft {
  0% {
    opacity: 0;
    transform: translateX(-50px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes slideInRight {
  0% {
    opacity: 0;
    transform: translateX(50px);
  }
  100% {
    opacity: 1;
    transform: translateX(0);
  }
}

@keyframes pulse {
  0%, 100% {
    transform: scale(1);
    box-shadow: var(--shadow-primary);
  }
  50% {
    transform: scale(1.05);
    box-shadow: var(--shadow-hover);
  }
}

@keyframes glow {
  0%, 100% {
    box-shadow: 0 0 10px rgba(211, 47, 47, 0.3);
  }
  50% {
    box-shadow: var(--shadow-glow);
  }
}

@keyframes bounce {
  0%, 20%, 53%, 80%, 100% {
    transform: translateY(0);
  }
  40%, 43% {
    transform: translateY(-8px);
  }
  70% {
    transform: translateY(-4px);
  }
  90% {
    transform: translateY(-2px);
  }
}

@keyframes shimmer {
  0% {
    background-position: -200% 0;
  }
  100% {
    background-position: 200% 0;
  }
}

@keyframes float {
  0%, 100% {
    transform: translateY(0px);
  }
  50% {
    transform: translateY(-10px);
  }
}

/* Page Container */
.page-container {
  background: linear-gradient(135deg, #fafafa 0%, #f5f5f5 100%);
  min-height: 100vh;
  padding: 2rem 0;
}

/* Main Card */
.main-card {
  background: linear-gradient(145deg, #ffffff 0%, #fefefe 100%);
  border-radius: 24px;
  box-shadow: var(--shadow-primary);
  border: 1px solid rgba(211, 47, 47, 0.08);
  position: relative;
  overflow: hidden;
  animation: fadeInUp 0.8s ease-out;
}

.main-card::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: var(--gradient-primary);
  animation: slideInLeft 1.2s ease-out;
}

.main-card::after {
  content: '';
  position: absolute;
  top: -50%;
  left: -50%;
  width: 200%;
  height: 200%;
  background: radial-gradient(circle, rgba(211, 47, 47, 0.02) 0%, transparent 70%);
  pointer-events: none;
  animation: float 6s ease-in-out infinite;
}

/* Header Section */
.header-section {
  padding: 2rem;
  border-bottom: 1px solid rgba(211, 47, 47, 0.1);
  background: var(--gradient-light);
  animation: slideInLeft 0.8s ease-out;
}

.header-title {
  background: var(--gradient-primary);
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
  font-size: 2.5rem;
  font-weight: 800;
  letter-spacing: -0.02em;
  position: relative;
  display: inline-block;
}

.header-title::after {
  content: '';
  position: absolute;
  bottom: -8px;
  left: 0;
  width: 100%;
  height: 3px;
  background: var(--gradient-secondary);
  border-radius: 2px;
  animation: slideInLeft 1.5s ease-out;
}

/* Create Button */
.create-btn {
  background: var(--gradient-primary);
  color: white;
  padding: 1rem 2rem;
  border-radius: 16px;
  font-weight: 600;
  text-decoration: none;
  position: relative;
  overflow: hidden;
  transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: var(--shadow-secondary);
  animation: slideInRight 0.8s ease-out;
}

.create-btn::before {
  content: '';
  position: absolute;
  top: 0;
  left: -100%;
  width: 100%;
  height: 100%;
  background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
  transition: left 0.6s;
}

.create-btn:hover::before {
  left: 100%;
}

.create-btn:hover {
  transform: translateY(-4px) scale(1.05);
  box-shadow: var(--shadow-hover);
  animation: pulse 2s infinite;
}

/* Filter Section */
.filter-section {
  background: linear-gradient(135deg, #f8f9fa 0%, #f1f3f4 100%);
  border-radius: 20px;
  padding: 2rem;
  margin: 2rem;
  box-shadow: inset 0 4px 8px rgba(0, 0, 0, 0.05), var(--shadow-secondary);
  border: 1px solid rgba(194, 24, 91, 0.1);
  animation: scaleIn 0.8s ease-out 0.2s both;
}

/* Form Controls */
.form-group {
  position: relative;
}

.form-label {
  font-weight: 600;
  color: var(--primary-red);
  margin-bottom: 0.5rem;
  display: block;
  font-size: 0.9rem;
  text-transform: uppercase;
  letter-spacing: 0.05em;
}

.form-control {
  width: 100%;
  padding: 1rem;
  border: 2px solid #e5e7eb;
  border-radius: 12px;
  font-size: 1rem;
  transition: all 0.3s ease;
  background: white;
  position: relative;
}

.form-control:focus {
  border-color: var(--secondary-pink);
  box-shadow: 0 0 0 4px rgba(194, 24, 91, 0.1), var(--shadow-secondary);
  transform: translateY(-2px);
  outline: none;
}

/* Action Buttons */
.btn-primary {
  background: var(--gradient-primary);
  color: white;
  padding: 1rem 2rem;
  border: none;
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  box-shadow: var(--shadow-secondary);
  position: relative;
  overflow: hidden;
}

.btn-primary:hover {
  transform: translateY(-3px) scale(1.02);
  box-shadow: var(--shadow-hover);
  animation: glow 2s infinite;
}

.btn-secondary {
  background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
  color: var(--primary-red);
  padding: 1rem 2rem;
  border: 2px solid var(--secondary-pink);
  border-radius: 12px;
  font-weight: 600;
  cursor: pointer;
  transition: all 0.3s ease;
  text-decoration: none;
  display: inline-block;
}

.btn-secondary:hover {
  background: var(--gradient-secondary);
  color: white;
  transform: translateY(-2px);
  box-shadow: var(--shadow-secondary);
}

/* Alert Message */
.alert-warning {
  background: var(--gradient-light);
  border-left: 6px solid var(--secondary-pink);
  color: var(--primary-red);
  padding: 1.5rem;
  border-radius: 16px;
  margin: 2rem;
  box-shadow: var(--shadow-secondary);
  position: relative;
  overflow: hidden;
  animation: fadeInUp 0.8s ease-out;
}

.alert-warning::before {
  content: '';
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 3px;
  background: var(--gradient-secondary);
  animation: slideInLeft 1s ease-out;
}

/* Table Container */
.table-container {
  margin: 2rem;
  border-radius: 20px;
  overflow: hidden;
  box-shadow: var(--shadow-primary);
  animation: fadeInUp 0.8s ease-out 0.4s both;
}

/* Enhanced Table */
.enhanced-table {
  width: 100%;
  border-collapse: collapse;
  background: white;
}

.table-header {
  background: var(--gradient-primary);
  color: white;
}

.table-header th {
  padding: 1.5rem 1rem;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 0.1em;
  font-size: 0.85rem;
  position: relative;
}



/* Table Rows */
.table-row {
  transition: all 0.3s ease;
  position: relative;
  border-bottom: 1px solid rgba(211, 47, 47, 0.05);
}





.table-cell {
  padding: 1.5rem 1rem;
  font-size: 0.95rem;
  color: #374151;
}

/* Status Badges */
.badge {
  padding: 0.5rem 1rem;
  border-radius: 25px;
  font-size: 0.8rem;
  font-weight: 600;
  text-transform: uppercase;
  letter-spacing: 0.05em;
  display: inline-block;
  position: relative;
  animation: bounce 2s infinite;
}

.badge-ouverte {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.badge-en_cours {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: white;
  box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}

.badge-fermee {
  background: var(--gradient-primary);
  color: white;
  box-shadow: var(--shadow-secondary);
}

.badge-resolue {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
  box-shadow: 0 4px 15px rgba(59, 130, 246, 0.3);
}

.badge-haute {
  background: var(--gradient-primary);
  color: white;
  box-shadow: var(--shadow-secondary);
  animation: pulse 3s infinite;
}

.badge-moyenne {
  background: linear-gradient(135deg, #f59e0b, #d97706);
  color: white;
  box-shadow: 0 4px 15px rgba(245, 158, 11, 0.3);
}

.badge-basse {
  background: linear-gradient(135deg, #10b981, #059669);
  color: white;
  box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
}

.badge-category {
  background: linear-gradient(135deg, rgba(194, 24, 91, 0.1), rgba(239, 68, 68, 0.1));
  color: var(--secondary-pink);
  border: 2px solid rgba(194, 24, 91, 0.2);
}

/* Action Buttons */
.action-btn {
  width: 40px;
  height: 40px;
  border-radius: 50%;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
  margin: 0 0.25rem;
  position: relative;
  overflow: hidden;
}

.action-btn::before {
  content: '';
  position: absolute;
  top: 50%;
  left: 50%;
  width: 0;
  height: 0;
  background: rgba(255, 255, 255, 0.3);
  border-radius: 50%;
  transition: all 0.3s ease;
  transform: translate(-50%, -50%);
}

.action-btn:hover::before {
  width: 100%;
  height: 100%;
}

.action-btn:hover {
  transform: scale(1.2) rotate(10deg);
  box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
}

.btn-view {
  background: linear-gradient(135deg, #3b82f6, #2563eb);
  color: white;
}

.btn-edit {
  background: var(--gradient-secondary);
  color: white;
}

.btn-delete {
  background: var(--gradient-primary);
  color: white;
}

/* Pagination */
.pagination-container {
  padding: 2rem;
  animation: slideInRight 0.8s ease-out 0.6s both;
}

.pagination a, .pagination span {
  padding: 0.75rem 1rem;
  margin: 0 0.25rem;
  border-radius: 12px;
  transition: all 0.3s ease;
  text-decoration: none;
  font-weight: 600;
}

.pagination a:hover {
  background: var(--gradient-secondary);
  color: white;
  transform: translateY(-2px);
  box-shadow: var(--shadow-secondary);
}

/* Responsive Design */
@media (max-width: 768px) {
  .header-title {
    font-size: 2rem;
  }
  
  .create-btn, .btn-primary, .btn-secondary {
    padding: 0.75rem 1.5rem;
  }
  
  .table-container {
    margin: 1rem;
  }
  
  .table-row:hover {
    transform: none;
  }
  
  .action-btn:hover {
    transform: scale(1.1);
  }
}

/* Loading Animation */
.loading-shimmer {
  background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
  background-size: 200% 100%;
  animation: shimmer 1.5s ease-in-out infinite;
}

/* Custom Scrollbar */
.table-container::-webkit-scrollbar {
  height: 8px;
}

.table-container::-webkit-scrollbar-track {
  background: #f1f1f1;
  border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb {
  background: var(--gradient-secondary);
  border-radius: 4px;
}

.table-container::-webkit-scrollbar-thumb:hover {
  background: var(--gradient-primary);
}

span.badge.badge-en_traitement {
    color: black;
    background-color: #d98888;
}


/* Reset de base pour mobile */
@media (max-width: 768px) {
  
  /* Container principal */
  .page-container {
    padding: 1rem 0;
    overflow-x: hidden;
  }

  /* Carte principale */
  .main-card {
    margin: 0.5rem;
    border-radius: 16px;
  }

  /* Header Section - Empiler verticalement */
  .header-section {
    padding: 1.5rem 1rem;
  }

  .header-section .flex {
    flex-direction: column;
    gap: 1rem;
    align-items: stretch !important;
  }

  .header-title {
    font-size: 1.5rem;
    text-align: center;
    word-break: break-word;
    line-height: 1.3;
  }

  /* Bouton créer */
  .create-btn {
    width: 100%;
    text-align: center;
    padding: 0.875rem 1rem;
    font-size: 0.9rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
  }

  /* Section Filtres */
  .filter-section {
    padding: 1rem;
    margin: 1rem 0.5rem;
    border-radius: 16px;
  }

  .filter-section form {
    display: flex !important;
    flex-direction: column !important;
    gap: 1rem;
  }

  .filter-section .grid {
    display: flex !important;
    flex-direction: column !important;
    gap: 1rem;
  }

  .filter-section .form-group {
    width: 100%;
    margin-bottom: 0;
  }

  /* Labels et inputs */
  .form-label {
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
    display: block;
    white-space: nowrap;
  }

  .form-control {
    width: 100%;
    padding: 0.75rem;
    font-size: 0.9rem;
    border-radius: 10px;
    box-sizing: border-box;
  }

  /* Boutons de filtrage */
  .filter-section .lg\\:col-span-2 {
    width: 100%;
    display: flex !important;
    flex-direction: column !important;
    gap: 0.75rem;
  }

  .btn-primary,
  .btn-secondary {
    width: 100%;
    padding: 0.875rem 1rem;
    text-align: center;
    font-size: 0.95rem;
  }

  /* Alert */
  .alert-warning {
    margin: 1rem 0.5rem;
    padding: 1rem;
    border-radius: 12px;
  }

  .alert-warning .flex {
    flex-direction: column;
    text-align: center;
  }

  .alert-warning .text-4xl {
    margin: 0 0 1rem 0 !important;
  }

  /* Table Container - Scroll horizontal */
  .table-container {
    margin: 0.5rem;
    border-radius: 12px;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
  }

  /* Table */
  .enhanced-table {
    min-width: 800px; /* Force la largeur minimale pour scroll */
    font-size: 0.85rem;
  }

  .table-header th {
    padding: 1rem 0.5rem;
    font-size: 0.75rem;
    white-space: nowrap;
  }

  .table-cell {
    padding: 0.875rem 0.5rem;
    font-size: 0.85rem;
    vertical-align: middle;
    white-space: normal;
    word-break: break-word;
  }

  /* Badges - Plus petits */
  .badge {
    padding: 0.375rem 0.625rem;
    font-size: 0.7rem;
    white-space: nowrap;
    display: inline-block;
  }

  /* Boutons d'action - Plus compacts */
  .action-btn {
    width: 32px;
    height: 32px;
    margin: 0 0.15rem;
  }

  .action-btn i {
    font-size: 0.75rem;
  }

  /* Pagination */
  .pagination-container {
    padding: 1rem 0.5rem;
    overflow-x: auto;
  }

  .pagination {
    display: flex;
    flex-wrap: wrap;
    justify-content: center;
    gap: 0.25rem;
  }

  .pagination a,
  .pagination span {
    padding: 0.5rem 0.75rem;
    font-size: 0.85rem;
    min-width: 40px;
    text-align: center;
  }

  /* Scroll to top button */
  .fixed.bottom-8.right-8 {
    bottom: 1rem !important;
    right: 1rem !important;
    width: 48px;
    height: 48px;
  }
}

/* ============================================
   CORRECTIONS TRÈS PETITS ÉCRANS (< 400px)
   ============================================ */
@media (max-width: 400px) {
  
  .header-title {
    font-size: 1.25rem;
  }

  .create-btn {
    font-size: 0.85rem;
    padding: 0.75rem 0.875rem;
  }

  .filter-section {
    padding: 0.75rem;
    margin: 0.75rem 0.25rem;
  }

  .form-label {
    font-size: 0.8rem;
  }

  .form-control {
    padding: 0.625rem;
    font-size: 0.85rem;
  }

  .btn-primary,
  .btn-secondary {
    padding: 0.75rem 0.875rem;
    font-size: 0.875rem;
  }

  .table-container {
    margin: 0.25rem;
  }

  .enhanced-table {
    min-width: 700px;
    font-size: 0.8rem;
  }

  .table-header th {
    padding: 0.75rem 0.375rem;
    font-size: 0.7rem;
  }

  .table-cell {
    padding: 0.75rem 0.375rem;
    font-size: 0.8rem;
  }

  .badge {
    padding: 0.25rem 0.5rem;
    font-size: 0.65rem;
  }

  .action-btn {
    width: 28px;
    height: 28px;
  }
}

/* ============================================
   AMÉLIORATIONS SUPPLÉMENTAIRES
   ============================================ */

/* Empêcher le zoom automatique sur les inputs iOS */
@media (max-width: 768px) {
  input[type="text"],
  input[type="search"],
  select,
  textarea {
    font-size: 16px !important; /* iOS ne zoome pas si >= 16px */
  }
}

/* Améliorer la lisibilité du texte */
@media (max-width: 768px) {
  body {
    -webkit-text-size-adjust: 100%;
    -moz-text-size-adjust: 100%;
    text-size-adjust: 100%;
  }
}

/* Corriger l'overflow horizontal */
@media (max-width: 768px) {
  body,
  html {
    overflow-x: hidden;
    width: 100%;
  }

  * {
    max-width: 100%;
  }
}

/* Optimiser les animations pour mobile */
@media (max-width: 768px) {
  .table-row:hover {
    transform: none !important;
  }

  .action-btn:hover {
    transform: scale(1.05) !important;
  }

  .badge:hover {
    transform: scale(1.05) !important;
  }
}

/* Améliorer le contraste pour petits écrans */
@media (max-width: 768px) {
  .table-cell {
    color: #1f2937;
  }

  .form-label {
    color: #B71C1C;
  }
}

/* Fix pour le menu déroulant select */
@media (max-width: 768px) {
  select.form-control {
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%23D32F2F' d='M6 9L1 4h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right 0.75rem center;
    padding-right: 2.5rem;
  }
}

/* Améliorer le touch target (zone tactile) */
@media (max-width: 768px) {
  .action-btn,
  .btn-primary,
  .btn-secondary,
  .create-btn {
    min-height: 44px; /* Recommandation Apple/Google */
    touch-action: manipulation;
  }
}

/* Indicateur de scroll horizontal pour la table */
@media (max-width: 768px) {
  .table-container::after {
    content: '← Faites défiler →';
    display: block;
    text-align: center;
    padding: 0.5rem;
    font-size: 0.75rem;
    color: #D32F2F;
    font-weight: 600;
    background: linear-gradient(135deg, #FFEBEE 0%, #FCE4EC 100%);
    border-top: 1px solid rgba(211, 47, 47, 0.2);
  }

  .table-container::-webkit-scrollbar {
    height: 6px;
  }

  .table-container::-webkit-scrollbar-track {
    background: #f1f1f1;
  }

  .table-container::-webkit-scrollbar-thumb {
    background: #D32F2F;
    border-radius: 3px;
  }
}

/* Mode paysage mobile */
@media (max-width: 768px) and (orientation: landscape) {
  .header-section {
    padding: 1rem;
  }

  .filter-section {
    padding: 0.75rem;
  }

  .header-title {
    font-size: 1.25rem;
  }
}
</style>

<div class="page-container">
    <div class="main-card">
        <div class="header-section">
            <div class="flex justify-between items-center">
                <h1 class="header-title">Liste des Réclamations</h1>
                @can('reclamation-create')
                    <a href="{{ route('reclamations.create') }}" class="create-btn">
                        ✨ Créer une Nouvelle Réclamation
                    </a>
                @endcan
            </div>
        </div>

        <div class="filter-section">
            <form action="{{ route('reclamations.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="form-group">
                    <label for="search" class="form-label">🔍 Rechercher</label>
                    <input type="text" name="search" id="search" placeholder="Sujet ou description" value="{{ request('search') }}" class="form-control">
                </div>
                
                <div class="form-group">
                    <label for="status" class="form-label">📊 Statut</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">Tous les statuts</option>
                        @foreach(App\Models\Reclamation::STATUSES as $key => $value)
                            <option value="{{ $key }}" {{ request('status') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="category" class="form-label">📋 Catégorie</label>
                    <select name="category" id="category" class="form-control">
                        <option value="">Toutes les catégories</option>
                        @foreach(App\Models\Reclamation::CATEGORIES as $key => $value)
                            <option value="{{ $key }}" {{ request('category') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="priority" class="form-label">⚡ Priorité</label>
                    <select name="priority" id="priority" class="form-control">
                        <option value="">Toutes les priorités</option>
                        @foreach(App\Models\Reclamation::PRIORITIES as $key => $value)
                            <option value="{{ $key }}" {{ request('priority') == $key ? 'selected' : '' }}>{{ $value }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="form-group lg:col-span-2">
                    <label for="formation_id" class="form-label">🎓 Formation</label>
                    <select name="formation_id" id="formation_id" class="form-control">
                        <option value="">Toutes les formations</option>
                        @foreach($formations as $formation)
                            <option value="{{ $formation->id }}" {{ request('formation_id') == $formation->id ? 'selected' : '' }}>{{ $formation->title }}</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="lg:col-span-2 flex justify-end gap-4 items-end">
                    <button type="submit" class="btn-primary">
                        🔎 Filtrer
                    </button>
                    <a href="{{ route('reclamations.index') }}" class="btn-secondary">
                        🔄 Réinitialiser
                    </a>
                </div>
            </form>
        </div>

        @if($reclamations->isEmpty())
            <div class="alert-warning">
                <div class="flex items-center">
                    <div class="text-4xl mr-4">😔</div>
                    <div>
                        <h3 class="font-bold text-lg mb-2">Aucune réclamation trouvée</h3>
                        <p>Aucune réclamation ne correspond aux critères sélectionnés. Essayez de modifier vos filtres.</p>
                    </div>
                </div>
            </div>
        @else
            <div class="table-container">
                <div class="overflow-x-auto">
                    <table class="enhanced-table">
                        <thead class="table-header">
                            <tr>
                               
                                <th>Sujet</th>
                                <th>Catégorie</th>
                                <th>Statut</th>
                                <th>Priorité</th>
                                <th>Créée par</th>
                                <th>Formation</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($reclamations as $reclamation)
                                <tr class="table-row">
                                    
                                    <td class="table-cell font-semibold">{{ $reclamation->subject }}</td>
                                    <td class="table-cell">
                                        <span class="badge badge-category">
                                            {{ $reclamation->category ? App\Models\Reclamation::CATEGORIES[$reclamation->category] : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="badge badge-{{ $reclamation->status }}">
                                            {{ $reclamation->status ? App\Models\Reclamation::STATUSES[$reclamation->status] : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="table-cell">
                                        <span class="badge badge-{{ $reclamation->priority }}">
                                            {{ $reclamation->priority ? App\Models\Reclamation::PRIORITIES[$reclamation->priority] : 'N/A' }}
                                        </span>
                                    </td>
                                    <td class="table-cell font-medium">{{ $reclamation->user->name ?? 'N/A' }}</td>
                                    <td class="table-cell">{{ $reclamation->formation->title ?? 'N/A' }}</td>
                                    <td class="table-cell">
                                        <div class="flex items-center justify-center">
                                            @can('reclamation-list')
                                                <a href="{{ route('reclamations.show', $reclamation->id) }}" class="action-btn btn-view" title="Voir">
                                                     <i class="fas fa-eye me-2"></i>
                                                </a>
                                            @endcan
                                            
                                            @can('reclamation-edit')
                                                @if(Auth::user()->hasRole('Etudiant') && $reclamation->user_id === Auth::id() && $reclamation->status === 'ouverte')
                                                    <a href="{{ route('reclamations.edit', $reclamation->id) }}" class="action-btn btn-edit" title="Modifier">
                                                        <i class="fas fa-edit me-2"></i>
                                                    </a>
                                                @elseif(Auth::user()->hasAnyRole(['Admin', 'Super Admin']))
                                                    <a href="{{ route('reclamations.edit', $reclamation->id) }}" class="action-btn btn-edit" title="Modifier">
                                                       <i class="fas fa-edit me-2"></i>
                                                    </a>
                                                @endif
                                            @endcan
                                            
                                            @can('reclamation-delete')
                                                <form action="{{ route('reclamations.destroy', $reclamation->id) }}" method="POST" style="display: inline-block;" onsubmit="return confirm('⚠️ Êtes-vous sûr de vouloir supprimer cette réclamation ?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="action-btn btn-delete" title="Supprimer">
                                                       <i class="fas fa-trash me-2"></i>
                                                    </button>
                                                </form>
                                            @endcan
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pagination-container">
                {{ $reclamations->appends(request()->query())->links() }}
            </div>
        @endif
    </div>
</div>

<script>
// Add some interactive effects
document.addEventListener('DOMContentLoaded', function() {
    // Add loading animation to forms
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function() {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.innerHTML = '⏳ Chargement...';
                submitBtn.disabled = true;
            }
        });
    });

    // Add staggered animation to table rows
    const tableRows = document.querySelectorAll('.table-row');
    tableRows.forEach((row, index) => {
        row.style.animationDelay = `${index * 0.1}s`;
        row.classList.add('fadeInUp');
    });

    // Add hover effects to badges
    const badges = document.querySelectorAll('.badge');
    badges.forEach(badge => {
        badge.addEventListener('mouseenter', function() {
            this.style.transform = 'scale(1.1) rotate(2deg)';
        });
        
        badge.addEventListener('mouseleave', function() {
            this.style.transform = 'scale(1) rotate(0deg)';
        });
    });

    // Add dynamic background animation
    const mainCard = document.querySelector('.main-card');
    if (mainCard) {
        let mouseX = 0;
        let mouseY = 0;
        
        mainCard.addEventListener('mousemove', function(e) {
            const rect = this.getBoundingClientRect();
            mouseX = e.clientX - rect.left;
            mouseY = e.clientY - rect.top;
            
            const gradient = `radial-gradient(circle at ${mouseX}px ${mouseY}px, rgba(211, 47, 47, 0.03) 0%, transparent 50%)`;
            this.style.setProperty('--mouse-gradient', gradient);
        });
    }

    // Add smooth scroll to top functionality
    const scrollToTop = document.createElement('button');
    scrollToTop.innerHTML = '⬆️';
    scrollToTop.className = 'fixed bottom-8 right-8 w-12 h-12 bg-gradient-to-r from-red-600 to-pink-600 text-white rounded-full shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110 z-50';
    scrollToTop.style.display = 'none';
    document.body.appendChild(scrollToTop);

    window.addEventListener('scroll', function() {
        if (window.pageYOffset > 300) {
            scrollToTop.style.display = 'block';
        } else {
            scrollToTop.style.display = 'none';
        }
    });

    scrollToTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });

    // Add notification system for form submissions
    function showNotification(message, type = 'success') {
        const notification = document.createElement('div');
        notification.className = `fixed top-8 right-8 p-4 rounded-lg shadow-lg z-50 transform translate-x-full transition-transform duration-300 ${
            type === 'success' ? 'bg-green-500' : 'bg-red-500'
        } text-white`;
        notification.textContent = message;
        document.body.appendChild(notification);

        setTimeout(() => {
            notification.style.transform = 'translateX(0)';
        }, 100);

        setTimeout(() => {
            notification.style.transform = 'translateX(full)';
            setTimeout(() => {
                document.body.removeChild(notification);
            }, 300);
        }, 3000);
    }

    // Add enhanced form validation
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            this.parentElement.classList.remove('focused');
            if (this.value) {
                this.parentElement.classList.add('filled');
            } else {
                this.parentElement.classList.remove('filled');
            }
        });
    });

    // Add table row click functionality
    const tableRows = document.querySelectorAll('.table-row');
    tableRows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (!e.target.closest('.action-btn') && !e.target.closest('form')) {
                const viewLink = this.querySelector('.btn-view');
                if (viewLink) {
                    window.location.href = viewLink.href;
                }
            }
        });
        
        // Add cursor pointer style
        row.style.cursor = 'pointer';
    });

    // Add search functionality with debounce
    const searchInput = document.getElementById('search');
    if (searchInput) {
        let searchTimeout;
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(() => {
                // You can add live search functionality here
                console.log('Searching for:', this.value);
            }, 300);
        });
    }

    // Add keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // Ctrl + N for new reclamation
        if (e.ctrlKey && e.key === 'n') {
            e.preventDefault();
            const createBtn = document.querySelector('.create-btn');
            if (createBtn) {
                window.location.href = createBtn.href;
            }
        }
        
        // Escape key to clear search
        if (e.key === 'Escape') {
            const searchInput = document.getElementById('search');
            if (searchInput && document.activeElement === searchInput) {
                searchInput.value = '';
                searchInput.blur();
            }
        }
    });
});

// Add CSS animations for new elements
const additionalStyles = `
<style>
.fadeInUp {
    animation: fadeInUp 0.6s ease-out both;
}

.focused .form-label {
    color: var(--secondary-pink);
    transform: scale(0.9);
}

.filled .form-label {
    color: var(--primary-red);
}

.table-row {
    cursor: pointer;
}

.table-row:hover .table-cell {
    color: var(--primary-red);
}

@keyframes slideInFromBottom {
    0% {
        opacity: 0;
        transform: translateY(50px);
    }
    100% {
        opacity: 1;
        transform: translateY(0);
    }
}

.notification-enter {
    animation: slideInFromBottom 0.3s ease-out;
}

/* Enhanced mobile responsiveness */
@media (max-width: 640px) {
    .header-title {
        font-size: 1.5rem;
    }
    
    .create-btn {
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
    }
    
    .filter-section {
        padding: 1rem;
        margin: 1rem;
    }
    
    .form-control {
        padding: 0.75rem;
    }
    
    .table-container {
        margin: 0.5rem;
    }
    
    .table-cell {
        padding: 1rem 0.5rem;
        font-size: 0.85rem;
    }
    
    .badge {
        padding: 0.3rem 0.6rem;
        font-size: 0.7rem;
    }
    
    .action-btn {
        width: 35px;
        height: 35px;
    }
}

/* Print styles */
@media print {
    .filter-section,
    .create-btn,
    .action-btn,
    .pagination-container {
        display: none !important;
    }
    
    .main-card {
        box-shadow: none;
        border: 1px solid #ccc;
    }
    
    .table-header {
        background: #f5f5f5 !important;
        color: #000 !important;
    }
    
    .badge {
        background: #f0f0f0 !important;
        color: #000 !important;
        border: 1px solid #ccc;
    }
}

/* Dark mode support */
@media (prefers-color-scheme: dark) {
    .page-container {
        background: linear-gradient(135deg, #1a1a1a 0%, #2d2d2d 100%);
    }
    
    .main-card {
        background: linear-gradient(145deg, #2d2d2d 0%, #3a3a3a 100%);
        color: #ffffff;
    }
    
    .filter-section {
        background: linear-gradient(135deg, #3a3a3a 0%, #4a4a4a 100%);
    }
    
    .form-control {
        background: #4a4a4a;
        color: #ffffff;
        border-color: #5a5a5a;
    }
    
    .table-cell {
        color: #e5e5e5;
    }
    
    .alert-warning {
        background: linear-gradient(135deg, #4a2c2a 0%, #5a3a3a 100%);
        color: #ffcccc;
    }
}

/* High contrast mode */
@media (prefers-contrast: high) {
    .badge {
        border: 2px solid currentColor;
    }
    
    .action-btn {
        border: 2px solid currentColor;
    }
    
    .form-control:focus {
        border-width: 3px;
    }
}

/* Reduced motion preferences */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
}
</style>
`;

document.head.insertAdjacentHTML('beforeend', additionalStyles);
</script>
@endsection