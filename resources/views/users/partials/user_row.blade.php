<tr class="table-row" data-user-id="{{ $user->id }}">
    <td>
        <div class="form-check">
            <input class="form-check-input user-checkbox" type="checkbox" value="{{ $user->id }}">
        </div>
    </td>
    <td>
        <div class="user-avatar">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}"
                     alt="Avatar de {{ $user->name }}"
                     class="avatar-img">
            @else
                <div class="avatar-placeholder">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
            <div class="status-indicator status-{{ $user->status }}"></div>
        </div>
    </td>
    <td>
        <div class="user-info">
            <span class="user-name">{{ $user->name }}</span>
            <small class="user-id">ID: {{ $user->id }}</small>
        </div>
    </td>
    <td>
        <span class="user-email">{{ $user->email }}</span>
    </td>
    <td>
        <span class="user-phone">{{ $user->phone ?? '-' }}</span>
    </td>
    <td>
        @if($user->getRoleNames()->count() > 0)
            @foreach($user->getRoleNames() as $role)
                <span class="badge badge-role">{{ $role }}</span>
            @endforeach
        @else
            <span class="badge badge-secondary">Aucun rôle</span>
        @endif
    </td>
    <td>
        <div class="form-check form-switch d-flex align-items-center justify-content-center">
            <input class="form-check-input status-toggle-switch" type="checkbox" id="statusSwitch-{{ $user->id }}"
                   data-user-id="{{ $user->id }}" {{ $user->status === 'active' ? 'checked' : '' }}>
            <label class="form-check-label ms-2 status-label {{ $user->status }}" for="statusSwitch-{{ $user->id }}">
                {{ $user->status === 'active' ? 'Actif' : 'Inactif' }}
            </label>
        </div>
    </td>
    <td>
        <span class="date-text">{{ $user->created_at->format('d/m/Y') }}</span>
        <small class="date-time">{{ $user->created_at->format('H:i') }}</small>
    </td>
    <td>
        <div class="action-buttons">
            <a href="{{ route('users.show', $user->id) }}"
               class="btn btn-action btn-view tooltip-custom"
               data-tooltip="Voir"
               title="Voir">
                <i class="fas fa-eye"></i>
            </a>
            <a href="{{ route('users.edit', $user->id) }}"
               class="btn btn-action btn-edit tooltip-custom"
               data-tooltip="Modifier"
               title="Modifier">
                <i class="fas fa-edit"></i>
            </a>
            <button class="btn btn-action btn-delete tooltip-custom"
                    data-tooltip="Supprimer"
                    onclick="deleteUser({{ $user->id }})"
                    title="Supprimer">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    </td>
</tr>