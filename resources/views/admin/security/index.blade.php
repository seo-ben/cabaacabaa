@extends('layouts.admin')

@section('title', 'Journal de Sécurité')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-gray-900">Journal de Sécurité</h1>
            <p class="text-gray-500 font-medium">Suivi des tentatives de connexion et activités suspectes.</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
        <form action="{{ route('admin.security.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Email</label>
                <input type="text" name="email" value="{{ request('email') }}" 
                       class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00]" 
                       placeholder="Rechercher par email...">
            </div>
            <div>
                <label class="block text-sm font-bold text-gray-700 mb-1">Statut</label>
                <select name="status" class="w-full px-4 py-2 border border-gray-200 rounded-xl focus:ring-2 focus:ring-[#ff4d00]">
                    <option value="">Tous les statuts</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Succès uniquement</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Échecs uniquement</option>
                </select>
            </div>
            <div class="md:col-span-2 flex items-end gap-2">
                <button type="submit" class="bg-gray-900 text-white font-bold px-6 py-2 rounded-xl hover:bg-black transition">
                    Filtrer
                </button>
                <a href="{{ route('admin.security.index') }}" class="bg-gray-100 text-gray-600 font-bold px-6 py-2 rounded-xl hover:bg-gray-200 transition">
                    Réinitialiser
                </a>
            </div>
        </form>
    </div>

    <!-- Logs Table -->
    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-gray-50 border-b border-gray-100">
                    <tr>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date & Heure</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Utilisateur / Email</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Adresse IP</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Résultat</th>
                        <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Appareil / Navigateur</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($attempts as $log)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 text-sm text-gray-600 font-medium">
                                {{ $log->attempted_at->format('d/m/Y H:i:s') }}
                            </td>
                            <td class="px-6 py-4">
                                @if($log->user)
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-xs font-bold text-gray-500">
                                            {{ substr($log->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900">{{ $log->user->name }}</div>
                                            <div class="text-xs text-gray-500">{{ $log->email }}</div>
                                        </div>
                                    </div>
                                @else
                                    <div class="text-sm font-bold text-gray-400 italic">Inconnu</div>
                                    <div class="text-xs text-gray-500">{{ $log->email }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <span class="text-sm font-mono text-gray-600">{{ $log->ip_address }}</span>
                                @if($log->location)
                                    <div class="text-xs text-gray-400">{{ $log->location }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($log->status === 'success')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Succès
                                    </span>
                                @else
                                    <div class="flex flex-col gap-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 w-fit">
                                            Échec
                                        </span>
                                        <span class="text-xs text-red-500 font-medium">{{ $log->failure_reason }}</span>
                                    </div>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <div class="max-w-xs truncate text-xs text-gray-400" title="{{ $log->user_agent }}">
                                    {{ $log->user_agent }}
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-gray-500 font-medium">
                                Aucun log trouvé.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($attempts->hasPages())
            <div class="px-6 py-4 bg-gray-50 border-t border-gray-100">
                {{ $attempts->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
