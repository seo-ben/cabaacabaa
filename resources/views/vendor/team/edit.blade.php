@extends('layouts.vendor')

@section('title', 'Modifier le membre - ' . $staff->user->name)

@section('content')
<div class="max-w-2xl mx-auto pb-20">
    <div class="flex items-center gap-6 mb-10">
        <a href="{{ route('vendeur.slug.team.index', ['vendor_slug' => $vendor->slug]) }}" 
           class="w-12 h-12 bg-white dark:bg-gray-900 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-800 flex items-center justify-center text-gray-400 hover:text-[#ff4d00] hover:border-orange-100 transition-all active:scale-90">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
        </a>
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Modifier le collaborateur</h1>
            <p class="text-gray-500 dark:text-gray-400 font-medium mt-1">Mettez à jour les informations et permissions de {{ $staff->user->name }}.</p>
        </div>
    </div>

    <form action="{{ route('vendeur.slug.team.update', ['vendor_slug' => $vendor->slug, 'id' => $staff->id]) }}" method="POST" 
          class="bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-800 p-8 md:p-10 space-y-8">
        @csrf
        @method('PUT')
        
        <div class="space-y-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Nom complet</label>
                    <input type="text" name="name" value="{{ old('name', $staff->user->name) }}" required
                           class="w-full bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl px-5 py-4 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-[#ff4d00] transition-all" placeholder="Ex: Jean Dupont">
                    @error('name')<p class="mt-2 text-xs font-bold text-red-500 ml-1">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Rôle / Titre</label>
                    <input type="text" name="role_name" value="{{ old('role_name', $staff->role_name) }}" required
                           class="w-full bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl px-5 py-4 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-[#ff4d00] transition-all" placeholder="Ex: Gérant, Vendeur...">
                    @error('role_name')<p class="mt-2 text-xs font-bold text-red-500 ml-1">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-gray-400 mb-2 ml-1">Email professionnel</label>
                <input type="email" name="email" value="{{ old('email', $staff->user->email) }}" required
                       class="w-full bg-gray-50 dark:bg-gray-800/50 border-none rounded-2xl px-5 py-4 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-[#ff4d00] transition-all" placeholder="Ex: employe@boutique.com">
                <p class="text-[10px] text-gray-400 font-bold mt-2 ml-1">Identifiant de connexion permanent.</p>
                @error('email')<p class="mt-2 text-xs font-bold text-red-500 ml-1">{{ $message }}</p>@enderror
            </div>

            <div class="p-6 bg-orange-50 dark:bg-orange-900/10 rounded-3xl border border-orange-100/50 dark:border-orange-900/20">
                <label class="block text-[10px] font-black uppercase tracking-widest text-orange-600 dark:text-orange-400 mb-2 ml-1">Nouveau mot de passe (Optionnel)</label>
                <input type="password" name="password"
                       class="w-full bg-white dark:bg-gray-900 border-none rounded-2xl px-5 py-4 text-sm font-bold text-gray-900 dark:text-white focus:ring-2 focus:ring-[#ff4d00] transition-all shadow-sm" placeholder="Laissez vide pour ne pas modifier">
                <p class="text-[10px] text-orange-400 font-bold mt-2 ml-1">Ne remplissez ceci que si le membre a oublié son mot de passe.</p>
                @error('password')<p class="mt-2 text-xs font-bold text-red-500 ml-1">{{ $message }}</p>@enderror
            </div>
        </div>

        <div class="pt-4">
            <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-gray-400 mb-6 ml-1">Permissions d'accès</h3>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @php
                    $perms = [
                        'manage_orders' => ['label' => 'Commandes', 'desc' => 'Voir, accepter et livrer'],
                        'manage_products' => ['label' => 'Catalogue', 'desc' => 'Gérer les plats et stocks'],
                        'view_finance' => ['label' => 'Finances', 'desc' => 'Voir le solde et historique'],
                        'manage_settings' => ['label' => 'Paramètres', 'desc' => 'Horaires et infos boutique'],
                        'view_analytics' => ['label' => 'Analyses', 'desc' => 'Statistiques et rapports']
                    ];
                    $currentPerms = $staff->permissions ?? [];
                @endphp

                @foreach($perms as $key => $info)
                    <label class="flex items-start gap-4 p-5 bg-gray-50 dark:bg-gray-800/50 rounded-2xl hover:bg-white dark:hover:bg-gray-800 border border-transparent hover:border-orange-100 dark:hover:border-orange-900/30 cursor-pointer transition-all group">
                        <div class="relative flex items-center">
                            <input type="checkbox" name="permissions[]" value="{{ $key }}" 
                                   class="peer w-6 h-6 text-[#ff4d00] border-gray-300 dark:border-gray-700 rounded-lg focus:ring-[#ff4d00] transition-all"
                                   {{ (is_array(old('permissions')) && in_array($key, old('permissions'))) || in_array($key, $currentPerms) ? 'checked' : '' }}>
                        </div>
                        <div class="min-w-0">
                            <span class="block text-sm font-black text-gray-900 dark:text-white group-hover:text-[#ff4d00]">{{ $info['label'] }}</span>
                            <span class="block text-[10px] font-bold text-gray-400 mt-0.5">{{ $info['desc'] }}</span>
                        </div>
                    </label>
                @endforeach
            </div>
        </div>

        <div class="flex flex-col-reverse md:flex-row justify-end gap-4 pt-8 border-t border-gray-50 dark:border-gray-800">
            <a href="{{ route('vendeur.slug.team.index', ['vendor_slug' => $vendor->slug]) }}" 
               class="px-8 py-4 text-gray-400 font-black text-xs uppercase tracking-widest hover:text-gray-600 transition-all text-center">Annuler</a>
            <button type="submit" class="px-10 py-4 bg-[#ff4d00] text-white font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-[#e64500] transition shadow-xl shadow-orange-500/20 active:scale-95">
                Enregistrer les modifications
            </button>
        </div>
    </form>

    {{-- Info Card --}}
    <div class="mt-8 p-8 bg-blue-50 dark:bg-blue-900/10 rounded-[2rem] border border-blue-100 dark:border-blue-900/20 flex gap-6 items-center">
        <div class="w-14 h-14 bg-white dark:bg-gray-900 rounded-2xl flex items-center justify-center shadow-sm shrink-0 border border-blue-100 dark:border-blue-900/20">
            <svg class="w-7 h-7 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        </div>
        <p class="text-xs font-bold text-blue-700 dark:text-blue-400 leading-relaxed">
            Note : Les modifications prendront effet dès la prochaine connexion de l'utilisateur. Si vous changez le mot de passe, l'utilisateur devra se reconnecter avec ses nouveaux identifiants.
        </p>
    </div>
</div>
@endsection
