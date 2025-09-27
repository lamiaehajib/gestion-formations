<div class="empty-state">
    <div class="empty-icon">
        <i class="fas fa-users"></i>
    </div>
    <h4>Aucun utilisateur trouvé</h4>
    <p>Aucun utilisateur ne correspond à vos critères de recherche dans cette catégorie.</p>
    <div class="empty-actions mt-3">
        <button type="button" class="btn btn-outline-primary" onclick="resetFilters()">
            <i class="fas fa-refresh me-2"></i>
            Réinitialiser les filtres
        </button>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus me-2"></i>
            Ajouter un utilisateur
        </a>
    </div>
</div>