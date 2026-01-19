@extends('layouts.admin')

@section('title', 'Gestion des Administrateurs')

@section('content')
<div class="flex justify-between items-center mb-6">
    <h1 class="text-2xl font-bold text-gray-900">Administrateurs</h1>
    <a href="{{ route('admin.admins.create') }}" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
        Ajouter un administrateur
    </a>
</div>

<div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden">
    <table class="w-full text-left">
        <thead class="bg-gray-50 border-b border-gray-100">
            <tr>
                <th class="px-6 py-4 font-semibold text-gray-700">Nom</th>
                <th class="px-6 py-4 font-semibold text-gray-700">Email</th>
                <th class="px-6 py-4 font-semibold text-gray-700">Rôle</th>
                <th class="px-6 py-4 font-semibold text-gray-700 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($admins as $admin)
            <tr class="hover:bg-gray-50 transition">
                <td class="px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center text-gray-600 font-bold">
                            {{ substr($admin->name, 0, 1) }}
                        </div>
                        <span class="font-medium text-gray-900">{{ $admin->name }}</span>
                    </div>
                </td>
                <td class="px-6 py-4 text-gray-600">{{ $admin->email }}</td>
                <td class="px-6 py-4">
                    @if($admin->id_user === 1)
                        <span class="px-2.5 py-1 bg-purple-100 text-purple-700 rounded-full text-xs font-bold">Super Admin</span>
                    @else
                        <span class="px-2.5 py-1 bg-gray-100 text-gray-700 rounded-full text-xs font-bold">Admin</span>
                    @endif
                </td>
                <td class="px-6 py-4 text-right">
                    @if($admin->id_user !== 1 || auth()->id() === 1)
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.admins.edit', $admin->id_user) }}" class="p-2 text-gray-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            </a>
                            
                            @if($admin->id_user !== auth()->id())
                                <form action="{{ route('admin.admins.destroy', $admin->id_user) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet administrateur ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                    @else
                        <span class="text-xs text-gray-400 italic">Protégé</span>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="px-6 py-8 text-center text-gray-500">Aucun administrateur trouvé.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
    <div class="px-6 py-4 border-t border-gray-100">
        {{ $admins->links() }}
    </div>
</div>
@endsection
