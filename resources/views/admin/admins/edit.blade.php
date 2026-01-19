@extends('layouts.admin')

@section('title', 'Modifier Administrateur')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('admin.admins.index') }}" class="p-2 hover:bg-gray-100 rounded-lg transition text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Modifier {{ $admin->name }}</h1>
    </div>

    <form action="{{ route('admin.admins.update', $admin->id_user) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
        @csrf
        @method('PUT')

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                <input type="text" name="name" value="{{ old('name', $admin->name) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $admin->email) }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nouveau mot de passe (optionnel)</label>
                    <input type="password" name="password"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                    @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Confirmer nouveau mot de passe</label>
                    <input type="password" name="password_confirmation"
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-red-500 transition">
                </div>
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6">
            <h3 class="font-bold text-gray-900 mb-4">Permissions</h3>
            
            @if($admin->id_user === 1)
                <div class="p-4 bg-purple-50 text-purple-700 rounded-lg mb-4 text-sm font-medium">
                    Le Super Admin a automatiquement toutes les permissions.
                </div>
            @endif

            <div class="space-y-6">
                @foreach($permissions as $group => $perms)
                    <div>
                        <h4 class="text-sm font-semibold text-gray-500 uppercase tracking-wider mb-2">{{ $group }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                            @foreach($perms as $permission)
                                <label class="flex items-center gap-2 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                                    <input type="checkbox" name="permissions[]" value="{{ $permission->id }}" 
                                           class="w-5 h-5 text-red-600 border-gray-300 rounded focus:ring-red-500"
                                           {{ (is_array(old('permissions')) && in_array($permission->id, old('permissions'))) || ($admin->permissions->contains($permission->id)) ? 'checked' : '' }}>
                                    <span class="text-sm text-gray-700">{{ $permission->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('admin.admins.index') }}" class="px-6 py-2.5 text-gray-600 font-medium hover:bg-gray-100 rounded-lg transition">Annuler</a>
            <button type="submit" class="px-6 py-2.5 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition shadow-lg shadow-red-200">
                Mettre à jour
            </button>
        </div>
    </form>
</div>
@endsection
