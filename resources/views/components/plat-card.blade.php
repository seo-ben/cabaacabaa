@props([
    'name' => 'Plat',
    'description' => '',
    'price' => '0.00',
    'devise' => 'XOF',
    'image' => null,
    'tags' => [],
    'time' => null,
    'available' => true,
    'onPromotion' => false,
    'promoPrice' => null,
])

<article {{ $attributes->merge(['class' => 'bg-white border rounded-lg overflow-hidden shadow-sm']) }}>
    <div class="flex">
        <div class="w-32 h-32 bg-gray-100 flex-shrink-0">
            @if($image)
                <img src="{{ $image }}" alt="{{ $name }}" class="w-full h-full object-cover">
            @else
                <div class="w-full h-full flex items-center justify-center text-gray-400">No image</div>
            @endif
        </div>
        <div class="p-4 flex-1">
            <div class="flex items-start justify-between">
                <div>
                    <h3 class="text-lg font-semibold">{{ $name }}</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $description }}</p>
                </div>
                <div class="text-right">
                    @if($onPromotion && $promoPrice)
                        <div class="text-sm text-gray-500 line-through">{{ number_format($price,2) }} {{ $devise }}</div>
                        <div class="text-lg font-bold text-red-600">{{ number_format($promoPrice,2) }} {{ $devise }}</div>
                    @else
                        <div class="text-lg font-bold">{{ number_format($price,2) }} {{ $devise }}</div>
                    @endif
                </div>
            </div>

            <div class="mt-3 flex items-center justify-between text-sm text-gray-500">
                <div class="flex items-center gap-2">
                    @foreach($tags as $tag)
                        <span class="px-2 py-0.5 bg-gray-100 rounded text-xs">{{ $tag }}</span>
                    @endforeach
                </div>
                <div>
                    @if($time)
                        <span class="mr-3">⏱ {{ $time }} min</span>
                    @endif
                    <span class="{{ $available ? 'text-green-600' : 'text-red-600' }} font-medium">{{ $available ? 'Disponible' : 'Indisponible' }}</span>
                </div>
            </div>
        </div>
    </div>
</article>
