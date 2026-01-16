@props(['horaires' => []])

<div class="bg-white border rounded p-4">
    <h4 class="font-semibold mb-2">Horaires</h4>
    <ul class="text-sm text-gray-700 space-y-1">
        @foreach($horaires as $h)
            <li class="flex items-center justify-between">
                <div class="font-medium">{{ $h['jour'] ?? $h['jour_semaine'] ?? 'Jour' }}</div>
                <div class="text-gray-600">
                    @if(!empty($h['ferme']) && $h['ferme'])
                        Fermé
                    @else
                        {{ $h['heure_ouverture'] ?? '—' }} — {{ $h['heure_fermeture'] ?? '—' }}
                    @endif
                </div>
            </li>
        @endforeach
    </ul>
</div>
