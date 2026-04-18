@extends('layouts.admin')

@section('title', 'Journal de Sécurité')

@section('content')
<div class="space-y-4">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none uppercase">Journal de Sécurité</h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-rose-500 animate-pulse"></span>
                Surveillance & Intégrité
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-1.5 rounded-2xl border border-gray-100 shadow-sm">
        <form action="{{ route('admin.security.index') }}" method="GET" class="flex flex-col lg:flex-row lg:items-center gap-1">
            <div class="flex-1 px-3 py-1">
                <div class="relative group">
                    <input type="text" name="email" value="{{ request('email') }}" placeholder="Recherche par email..." 
                           class="w-full pl-8 pr-4 py-2 bg-transparent border-none focus:ring-0 text-xs font-bold text-gray-900 placeholder:text-gray-300 placeholder:uppercase">
                    <svg class="absolute left-0 top-2 w-4 h-4 text-gray-300 group-focus-within:text-rose-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                </div>
            </div>

            <div class="h-6 w-px bg-gray-50 hidden lg:block mx-1"></div>

            <select name="status" class="bg-gray-50 border-none rounded-xl text-[10px] font-bold text-gray-600 focus:ring-0 mx-2 px-3 py-2">
                <option value="">Tous les statuts</option>
                <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Succès</option>
                <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Échecs</option>
            </select>

            <div class="flex items-center gap-1.5 p-1.5 ml-auto">
                <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white rounded-xl font-black text-[9px] uppercase tracking-widest hover:bg-black transition-all shadow-lg">Appliquer</button>
                @if(request('email') || request('status'))
                    <a href="{{ route('admin.security.index') }}" class="p-2.5 bg-gray-50 text-gray-400 hover:text-red-600 rounded-xl transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"/></svg>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden text-[10px]">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="pl-4 pr-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Date & Heure</th>
                        <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Cible / Utilisateur</th>
                        <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Origine IP</th>
                        <th class="px-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Résultat</th>
                        <th class="pr-4 pl-3 py-3 font-black text-gray-400 uppercase tracking-widest leading-none">Agent</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($attempts as $log)
                        <tr class="group hover:bg-gray-50/50 transition-all duration-200">
                            <td class="pl-4 pr-3 py-2.5">
                                <p class="font-black text-gray-900 leading-none">{{ $log->attempted_at->format('d/m/Y') }}</p>
                                <p class="text-[7px] text-gray-300 font-bold uppercase mt-0.5">{{ $log->attempted_at->format('H:i:s') }}</p>
                            </td>
                            <td class="px-3 py-2.5">
                                <div class="flex items-center gap-2">
                                    <div class="min-w-0">
                                        @if($log->user)
                                            <p class="font-black text-gray-900 truncate leading-none mb-0.5">{{ $log->user->name }}</p>
                                        @endif
                                        <p class="text-[8px] text-gray-400 font-bold uppercase truncate">{{ $log->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-2.5">
                                <span class="font-bold text-gray-600 font-mono">{{ $log->ip_address }}</span>
                                @if($log->location)
                                    <p class="text-[7px] text-gray-300 font-bold uppercase mt-0.5">{{ $log->location }}</p>
                                @endif
                            </td>
                            <td class="px-3 py-2.5">
                                @if($log->status === 'success')
                                    <span class="font-black text-emerald-500 tracking-tighter uppercase">AUTHENTIFIÉ</span>
                                @else
                                    <div class="flex flex-col">
                                        <span class="font-black text-rose-500 tracking-tighter uppercase">ÉCHEC</span>
                                        <span class="text-[7px] text-rose-300 font-bold uppercase truncate max-w-[100px]">{{ $log->failure_reason }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="pr-4 pl-3 py-2.5 max-w-[150px]">
                                <p class="text-[7px] text-gray-300 font-bold uppercase truncate" title="{{ $log->user_agent }}">{{ $log->user_agent }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-300 font-bold uppercase text-[10px]">Aucun enregistrement trouvé</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="px-4 py-3 bg-gray-50/50 border-t border-gray-100 flex justify-center text-[8px] font-bold text-gray-400 uppercase tracking-widest">
            {{ $attempts->links() }}
        </div>
    </div>
</div>
@endsection
