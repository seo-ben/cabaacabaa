@extends('layouts.admin')

@section('title', 'Sécurité Utilisateur - ' . $user->name)

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.security.index') }}" class="p-2 hover:bg-gray-100 rounded-xl transition text-gray-500">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
        </a>
        <div>
            <h1 class="text-3xl font-black text-gray-900">Détails de Sécurité</h1>
            <p class="text-gray-500 font-medium">Historique pour <strong>{{ $user->name }}</strong> ({{ $user->email }})</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- User Security Status Card -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-4">État du Compte</h2>
                <div class="space-y-4">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Statut</span>
                        <span class="px-2 py-0.5 rounded-full font-bold {{ $user->status === 'actif' ? 'bg-green-100 text-green-700' : ($user->status === 'suspendu' ? 'bg-red-100 text-red-700' : 'bg-gray-100 text-gray-700') }}">
                            {{ ucfirst($user->status) }}
                        </span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Dernière IP</span>
                        <span class="font-mono font-medium">{{ $user->derniere_ip ?? 'N/A' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Dernière connexion</span>
                        <span class="font-medium">{{ $user->date_derniere_connexion ? $user->date_derniere_connexion->format('d/m/Y H:i') : 'Jamais' }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Échecs de connexion</span>
                        <span class="font-bold {{ $user->login_attempts > 3 ? 'text-red-600' : 'text-gray-900' }}">{{ $user->login_attempts }}</span>
                    </div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-gray-500">Compte vérifié</span>
                        <span class="font-bold {{ $user->is_verified ? 'text-blue-600' : 'text-gray-400' }}">{{ $user->is_verified ? 'Oui' : 'Non' }}</span>
                    </div>
                </div>
                
                @if($user->isLocked())
                    <div class="mt-6 p-4 bg-red-50 border border-red-100 rounded-2xl text-red-700 text-sm">
                        <div class="font-bold flex items-center gap-2 mb-1">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                            </svg>
                            Compte Verrouillé
                        </div>
                        Verrouillé jusqu'au {{ $user->locked_until->format('d/m H:i') }}
                    </div>
                @endif
            </div>

            <!-- Risk Profile -->
            <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Profil de Risque</h2>
                <div class="relative pt-1">
                    <div class="flex mb-2 items-center justify-between">
                        <div>
                            <span class="text-xs font-semibold inline-block py-1 px-2 uppercase rounded-full {{ $user->risk_score > 50 ? 'text-red-600 bg-red-200' : ($user->risk_score > 20 ? 'text-orange-600 bg-orange-200' : 'text-green-600 bg-green-200') }}">
                                Score de Risque
                            </span>
                        </div>
                        <div class="text-right">
                            <span class="text-xs font-semibold inline-block text-gray-600">
                                {{ $user->risk_score }}/100
                            </span>
                        </div>
                    </div>
                    <div class="overflow-hidden h-2 mb-4 text-xs flex rounded bg-gray-100">
                        <div style="width:{{ $user->risk_score }}%" class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center {{ $user->risk_score > 50 ? 'bg-red-500' : ($user->risk_score > 20 ? 'bg-orange-500' : 'bg-green-500') }}"></div>
                    </div>
                </div>

                @if($user->suspicious_flags)
                    <div class="mt-4 space-y-2">
                        <div class="text-xs font-bold text-gray-500 uppercase tracking-wider">Indicateurs suspects</div>
                        <div class="flex flex-wrap gap-2">
                            @foreach($user->suspicious_flags as $flag)
                                <span class="px-2 py-1 bg-red-50 text-red-600 text-xs font-bold rounded-lg border border-red-100">{{ $flag }}</span>
                            @endforeach
                        </div>
                    </div>
                @else
                    <p class="text-sm text-green-600 font-medium mt-2">Aucun indicateur suspect détecté.</p>
                @endif
            </div>
        </div>

        <!-- Logs History -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-100 flex items-center justify-between">
                    <h2 class="text-lg font-bold text-gray-900">Historique des Connexions</h2>
                    <span class="text-xs text-gray-500 font-medium">30 derniers records</span>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-100">
                            <tr>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Date</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Adresse IP</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Résultat</th>
                                <th class="px-6 py-4 text-xs font-semibold text-gray-500 uppercase tracking-wider">Détails</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @forelse($attempts as $log)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        {{ $log->attempted_at->format('d/m/Y H:i') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm font-mono text-gray-600">
                                        {{ $log->ip_address }}
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($log->status === 'success')
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-green-100 text-green-700">Succès</span>
                                        @else
                                            <span class="px-2 py-0.5 rounded-full text-xs font-bold bg-red-100 text-red-700">Échec</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-xs text-gray-400">
                                        {{ $log->failure_reason ?? '-' }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-12 text-center text-gray-500">Aucun historique disponible.</td>
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
    </div>
</div>
@endsection
