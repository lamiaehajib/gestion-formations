<div class="user-card animate__animated animate__fadeInUp" data-user-id="{{ $user->id }}">
    <div class="user-card-header">
        <div class="user-card-avatar user-avatar">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" alt="Avatar de {{ $user->name }}" class="avatar-img">
            @else
                <div class="avatar-placeholder">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
            @endif
            <div class="status-indicator status-{{ $user->status }}"></div>
        </div>
        <div class="user-card-info">
            <h5>{{ $user->name }}</h5>
            <p>ID: {{ $user->id }}</p>
        </div>
    </div>
    <div class="user-card-details">
        <div class="user-card-detail"><span><i class="fas fa-envelope me-2 text-muted"></i>Email:</span><span>{{ $user->email }}</span></div>
        <div class="user-card-detail"><span><i class="fas fa-phone me-2 text-muted"></i>Téléphone:</span><span>{{ $user->phone ?? '-' }}</span></div>
        <div class="user-card-detail">
            <span><i class="fas fa-user-tag me-2 text-muted"></i>Rôle:</span>
            <span>
                @if($user->getRoleNames()->count() > 0)
                    @foreach($user->getRoleNames() as $role)
                        <span class="badge badge-role">{{ $role }}</span>
                    @endforeach
                @else
                    <span class="badge badge-secondary">Aucun rôle</span>
                @endif
            </span>
        </div>
        <div class="user-card-detail">
            <span><i class="fas fa-power-off me-2 text-muted"></i>Statut:</span>
            <div class="form-check form-switch d-flex align-items-center justify-content-center">
                <input class="form-check-input status-toggle-switch" type="checkbox" id="gridStatusSwitch-{{ $user->id }}"
                       data-user-id="{{ $user->id }}" {{ $user->status === 'active' ? 'checked' : '' }}>
                <label class="form-check-label ms-2 status-label {{ $user->status }}" for="gridStatusSwitch-{{ $user->id }}">{{ $user->status === 'active' ? 'Actif' : 'Inactif' }}</label>
            </div>
        </div>
        <div class="user-card-detail"><span><i class="fas fa-calendar-alt me-2 text-muted"></i>Créé le:</span><span>{{ $user->created_at->format('d/m/Y H:i') }}</span></div>
    </div>
    <div class="user-card-actions">
        <a href="{{ route('users.show', $user->id) }}" class="btn btn-action btn-view tooltip-custom" data-tooltip="Voir" title="Voir"><i class="fas fa-eye"></i></a>
        <a href="{{ route('users.edit', $user->id) }}" class="btn btn-action btn-edit tooltip-custom" data-tooltip="Modifier" title="Modifier"><i class="fas fa-edit"></i></a>
        <button class="btn btn-action btn-delete tooltip-custom" data-tooltip="Supprimer" onclick="deleteUser({{ $user->id }})" title="Supprimer"><i class="fas fa-trash"></i></button>
    </div>
</div>