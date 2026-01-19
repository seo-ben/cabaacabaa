@extends('layouts.vendor')

@section('title', 'Ajouter un membre')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="flex items-center gap-4 mb-6">
        <a href="{{ route('vendeur.slug.team.index', ['vendor_slug' => $vendor->slug]) }}" class="p-2 hover:bg-gray-100 rounded-lg transition text-gray-600">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <h1 class="text-2xl font-bold text-gray-900">Ajouter un membre</h1>
    </div>

    <form action="{{ route('vendeur.slug.team.store', ['vendor_slug' => $vendor->slug]) }}" method="POST" class="bg-white rounded-xl shadow-sm border border-gray-100 p-6 space-y-6">
        @csrf
        
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet</label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition" placeholder="Ex: Jean Dupont">
                @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email professionnel</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition" placeholder="Ex: employe@boutique.com">
                <p class="text-xs text-gray-500 mt-1">Cet email servira d'identifiant de connexion.</p>
                @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mot de passe provisoire</label>
                <input type="password" name="password" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition" placeholder="********">
                @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Rôle / Titre</label>
                <input type="text" name="role_name" value="{{ old('role_name', 'Employé') }}" required
                       class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-[#ff4d00] focus:border-[#ff4d00] transition" placeholder="Ex: Gérant, Livreur, Vendeur...">
                @error('role_name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="border-t border-gray-100 pt-6">
            <h3 class="font-bold text-gray-900 mb-4">Permissions</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @php
                    $perms = [
                        'manage_orders' => 'Gérer les commandes',
                        'manage_products' => 'Gérer les produits',
                        'view_finance' => 'Voir les finances',
                        'manage_settings' => 'Gérer les paramètres',
                        'view_analytics' => 'Voir les statistiques'
                    ];
                @endphp

                @foreach($perms as $key => $label)
                    <label class="flex items-center gap-3 p-3 border border-gray-200 rounded-lg hover:bg-gray-50 cursor-pointer transition">
                        <input type="checkbox" name="permissions[]" value="{{ $key }}" 
                               class="w-5 h-5 text-[#ff4d00] border-gray-300 rounded focus:ring-[#ff4d00]"
                               {{ is_array(old('permissions')) && in_array($key, old('permissions')) ? 'checked' : '' }}>
                        <span class="text-sm font-medium text-gray-700">{{ $label }}</span>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
            <a href="{{ route('vendeur.slug.team.index', ['vendor_slug' => $vendor->slug]) }}" class="px-6 py-2.5 text-gray-600 font-medium hover:bg-gray-100 rounded-lg transition">Annuler</a>
            <button type="submit" class="px-6 py-2.5 bg-[#ff4d00] text-white font-medium rounded-lg hover:bg-[#e64500] transition shadow-lg shadow-orange-200">
                Ajouter le membre
            </button>
        </div>
    </form>
</div>
@endsection
