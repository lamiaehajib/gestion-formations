{{-- resources/views/users/_users_table_rows.blade.php --}}
@foreach($users as $user)
    <tr>
        <td class="text-center">
            @if($user->avatar)
                <img src="{{ asset('storage/' . $user->avatar) }}" 
                     alt="Avatar de {{ $user->name }}" 
                     class="rounded-circle shadow-sm" 
                     width="45" 
                     height="45"
                     style="object-fit: cover;">
            @else
                <div class="bg-primary-gradient rounded-circle d-flex align-items-center justify-content-center text-white fw-bold shadow-sm" 
                     style="width: 45px; height: 45px; font-size: 1.2em;">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>
            @endif
        </td>
        <td><strong class="text-dark">{{ $user->name }}</strong></td>
        <td>{{ $user->email }}</td>
        <td>{{ $user->phone ?? '-' }}</td>
        <td>
            @if($user->getRoleNames()->count() > 0)
                @foreach($user->getRoleNames() as $role)
                    <span class="badge bg-info animate__animated animate__fadeIn">{{ $role }}</span>
                @endforeach
            @else
                <span class="badge bg-secondary">Aucun rôle</span>
            @endif
        </td>
        <td>
            <label class="switch">
                <input type="checkbox" class="status-toggle" data-user-id="{{ $user->id }}" {{ $user->status === 'active' ? 'checked' : '' }}>
                <span class="slider"></span>
            </label>
            <span class="badge status-badge {{ $user->status === 'active' ? 'bg-success' : 'bg-danger' }} ms-2">
                {{ $user->status === 'active' ? 'Actif' : 'Inactif' }}
            </span>
        </td>
        <td>{{ $user->created_at->format('d/m/Y H:i') }}</td>
        <td class="text-center">
            <div class="btn-group" role="group">
                <a href="{{ route('users.show', $user->id) }}" 
                   class="btn btn-sm btn-info shadow-sm" 
                   title="Voir">
                    <i class="fas fa-eye"></i>
                </a>
                <a href="{{ route('users.edit', $user->id) }}" 
                   class="btn btn-sm btn-warning shadow-sm" 
                   title="Modifier">
                    <i class="fas fa-edit"></i>
                </a>
                <form action="{{ route('users.destroy', $user->id) }}" 
                      method="POST" 
                      style="display:inline-block;"
                      onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ? Cette action est irréversible.')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="btn btn-sm btn-danger shadow-sm" 
                            title="Supprimer">
                        <i class="fas fa-trash"></i>
                    </button>
                </form>
            </div>
        </td>
    </tr>
@endforeach