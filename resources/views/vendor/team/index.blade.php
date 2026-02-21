@extends('layouts.vendor')

@section('title', 'Gestion de l\'équipe')

@section('content')
<div class="max-w-4xl mx-auto pb-20">
    <div class="flex flex-col md:flex-row md:items-center justify-between mb-10 gap-6">
        <div>
            <h1 class="text-3xl font-black text-gray-900 dark:text-white tracking-tight">Membres de l'équipe</h1>
            <p class="text-gray-500 dark:text-gray-400 font-medium mt-1">Gérez les accès à votre boutique pour vos collaborateurs.</p>
        </div>
        <a href="{{ route('vendeur.slug.team.create', ['vendor_slug' => $vendor->slug]) }}" 
           class="bg-[#ff4d00] hover:bg-[#e64500] text-white px-6 py-4 rounded-2xl transition shadow-xl shadow-orange-500/20 flex items-center justify-center gap-3 text-xs font-black uppercase tracking-widest active:scale-95">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            Ajouter un membre
        </a>
    </div>

    @if(session('success'))
        <div class="mb-8 bg-green-50 dark:bg-green-900/20 border border-green-100 dark:border-green-900/30 text-green-700 dark:text-green-400 px-6 py-4 rounded-2xl flex items-center gap-4 animate-in fade-in slide-in-from-top-4 duration-500">
            <div class="w-10 h-10 bg-green-100 dark:bg-green-900/40 rounded-xl flex items-center justify-center shrink-0">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>
            </div>
            <p class="text-sm font-bold">{{ session('success') }}</p>
        </div>
    @endif

    @if($staffMembers->isEmpty())
        <div class="bg-white dark:bg-gray-900 rounded-[2.5rem] shadow-sm border border-gray-100 dark:border-gray-800 p-12 text-center">
            <div class="w-20 h-20 bg-orange-50 dark:bg-orange-900/20 text-[#ff4d00] rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-xl shadow-orange-500/10">
                <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
            </div>
            <h3 class="text-2xl font-black text-gray-900 dark:text-white mb-3">Aucun membre dans l'équipe</h3>
            <p class="text-gray-400 font-medium mb-8 max-w-sm mx-auto">Ajoutez des membres pour vous aider à gérer vos commandes et vos articles en toute simplicité.</p>
            <a href="{{ route('vendeur.slug.team.create', ['vendor_slug' => $vendor->slug]) }}" 
               class="inline-flex items-center gap-2 px-8 py-4 bg-[#ff4d00] text-white rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-orange-500/20 active:scale-95 transition-all">
                Ajouter le premier membre
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
            </a>
        </div>
    @else
        {{-- Desktop Table View --}}
        <div class="hidden md:block bg-white dark:bg-gray-900 rounded-[2rem] shadow-sm border border-gray-100 dark:border-gray-800 overflow-hidden">
            <table class="w-full text-left">
                <thead class="bg-gray-50/50 dark:bg-gray-800/50 border-b border-gray-100 dark:border-gray-800">
                    <tr>
                        <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Membre</th>
                        <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Rôle</th>
                        <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest">Accès Staff</th>
                        <th class="px-6 py-5 text-xs font-black text-gray-400 uppercase tracking-widest text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 dark:divide-gray-800">
                    @foreach($staffMembers as $staff)
                        <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/30 transition-colors group">
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-2xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400 font-display font-black text-lg border border-orange-100 dark:border-orange-900/30 group-hover:scale-110 transition-transform">
                                        {{ mb_substr($staff->user->name, 0, 1) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-black text-gray-900 dark:text-white truncate">{{ $staff->user->name }}</div>
                                        <div class="text-xs font-bold text-gray-400 truncate">{{ $staff->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                <div class="space-y-1.5">
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 border border-blue-100 dark:border-blue-900/30">
                                        {{ $staff->role_name }}
                                    </span>
                                    @if(!empty($staff->permissions))
                                        <div class="flex flex-wrap gap-1 mt-1">
                                            @foreach($staff->permissions as $perm)
                                                <span class="px-1.5 py-0.5 rounded text-[8px] font-bold bg-gray-100 dark:bg-gray-800 text-gray-500 uppercase tracking-tighter">
                                                    {{ $perm }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-5">
                                @if($staff->access_token)
                                    @php
                                        $loginUrl = route('vendor.staff.login', ['vendor_slug' => $vendor->slug, 'token' => $staff->access_token]);
                                    @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="relative group/link max-w-[240px]">
                                            <input type="text" value="{{ $loginUrl }}" readonly 
                                                   class="text-[10px] bg-gray-50 dark:bg-gray-800 border-none rounded-xl px-4 py-3 w-full truncate font-bold text-gray-400 pr-10 outline-none"
                                                   id="link-{{ $staff->id }}">
                                            <button onclick="copyLink('link-{{ $staff->id }}')" 
                                                    class="absolute right-2 top-1/2 -translate-y-1/2 p-1.5 hover:bg-white dark:hover:bg-gray-700 rounded-lg transition-all text-orange-500 shadow-sm"
                                                    title="Copier le lien unique">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <span class="text-[10px] font-black text-gray-400 uppercase italic">Acces désactivé</span>
                                @endif
                            </td>
                            <td class="px-6 py-5 text-right">
                                <form action="{{ route('vendeur.slug.team.destroy', ['vendor_slug' => $vendor->slug, 'id' => $staff->id]) }}" method="POST" class="inline-block" onsubmit="return confirm('Êtes-vous sûr de vouloir révoquer cet accès ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-3 text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-2xl transition-all" title="Révoquer l'accès">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{-- Mobile Card View --}}
        <div class="md:hidden space-y-4">
            @foreach($staffMembers as $staff)
                <div class="bg-white dark:bg-gray-900 rounded-[2rem] p-6 border border-gray-100 dark:border-gray-800 shadow-sm space-y-5">
                    <div class="flex items-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-orange-50 dark:bg-orange-900/20 flex items-center justify-center text-orange-600 dark:text-orange-400 font-display font-black text-xl border border-orange-100 dark:border-orange-900/30">
                            {{ mb_substr($staff->user->name, 0, 1) }}
                        </div>
                        <div class="min-w-0">
                            <h4 class="font-black text-gray-900 dark:text-white truncate">{{ $staff->user->name }}</h4>
                            <p class="text-xs font-bold text-gray-400 truncate">{{ $staff->user->email }}</p>
                            <span class="inline-block mt-2 px-2.5 py-0.5 rounded-lg text-[9px] font-black uppercase tracking-widest bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                                {{ $staff->role_name }}
                            </span>
                        </div>
                    </div>

                    @if($staff->access_token)
                        @php
                            $loginUrl = route('vendor.staff.login', ['vendor_slug' => $vendor->slug, 'token' => $staff->access_token]);
                        @endphp
                        <div class="space-y-2">
                            <p class="text-[10px] font-black text-gray-400 uppercase tracking-widest ml-1">Lien de Connexion Unique</p>
                            <div class="flex items-center gap-2">
                                <input type="text" value="{{ $loginUrl }}" readonly 
                                       class="flex-1 text-[10px] bg-gray-50 dark:bg-gray-800 border-none rounded-xl px-4 py-4 font-bold text-gray-400 truncate outline-none"
                                       id="link-mobile-{{ $staff->id }}">
                                <button onclick="copyLink('link-mobile-{{ $staff->id }}')" 
                                        class="p-4 bg-orange-500 text-white rounded-xl shadow-lg shadow-orange-500/30 active:scale-95 transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    @endif

                    <div class="pt-4 border-t border-gray-50 dark:border-gray-800 flex justify-end">
                        <form action="{{ route('vendeur.slug.team.destroy', ['vendor_slug' => $vendor->slug, 'id' => $staff->id]) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 rounded-xl text-[10px] font-black uppercase tracking-widest">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                Révoquer l'accès
                            </button>
                        </form>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

<script>
function copyLink(inputId) {
    const input = document.getElementById(inputId);
    input.select();
    input.setSelectionRange(0, 99999); // For mobile
    
    navigator.clipboard.writeText(input.value).then(() => {
        // Show temporary success message
        const button = input.nextElementSibling || input.parentElement.querySelector('button');
        const originalHTML = button.innerHTML;
        button.innerHTML = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/></svg>';
        
        setTimeout(() => {
            button.innerHTML = originalHTML;
        }, 2000);
    });
}
</script>
@endsection
