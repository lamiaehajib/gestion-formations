<div class="mb-3">
    <label>Nom complet</label>
    <input type="text" name="name" value="{{ old('name', $user->name ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label>Email</label>
    <input type="email" name="email" value="{{ old('email', $user->email ?? '') }}" class="form-control" required>
</div>

<div class="mb-3">
    <label>Mot de passe</label>
    <input type="password" name="password" class="form-control" {{ isset($user) ? '' : 'required' }}>
</div>

<div class="mb-3">
    <label>Confirmer le mot de passe</label>
    <input type="password" name="password_confirmation" class="form-control" {{ isset($user) ? '' : 'required' }}>
</div>

<div class="mb-3">
    <label>Téléphone</label>
    <input type="text" name="phone" value="{{ old('phone', $user->phone ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label>Adresse</label>
    <input type="text" name="address" value="{{ old('address', $user->address ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label>Date de naissance</label>
    <input type="date" name="birth_date" value="{{ old('birth_date', $user->birth_date ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label>CIN</label>
    <input type="text" name="cin" value="{{ old('cin', $user->cin ?? '') }}" class="form-control">
</div>

<div class="mb-3">
    <label>Statut</label>
    <select name="status" class="form-control" required>
        <option value="active" {{ (old('status', $user->status ?? '') == 'active') ? 'selected' : '' }}>Actif</option>
        <option value="inactive" {{ (old('status', $user->status ?? '') == 'inactive') ? 'selected' : '' }}>Inactif</option>
    </select>
</div>

<div class="mb-3">
    <label>Avatar</label>
    <input type="file" name="avatar" class="form-control">
    @if(isset($user) && $user->avatar)
        <img src="{{ asset('storage/' . $user->avatar) }}" width="100" class="mt-2">
    @endif
</div>

<div class="mb-3">
    <label>Rôles</label>
    <select name="roles[]" class="form-control" multiple required>
        @foreach($roles as $role)
            <option value="{{ $role }}"
                {{ (isset($userRoles) && in_array($role, $userRoles)) ? 'selected' : '' }}>
                {{ $role }}
            </option>
        @endforeach
    </select>
</div>
