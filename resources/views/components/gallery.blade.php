@props(['images' => []])

<div class="grid grid-cols-3 gap-2">
    @foreach($images as $idx => $img)
        <button type="button" class="focus:outline-none" onclick="openGallery({{ $idx }})">
            <div class="w-full h-24 bg-gray-100 overflow-hidden rounded">
                <img src="{{ $img }}" class="w-full h-full object-cover" alt="gallery-{{ $idx }}">
            </div>
        </button>
    @endforeach

    <!-- Modal -->
    <div id="gallery-modal" class="fixed inset-0 bg-black bg-opacity-60 hidden items-center justify-center z-50">
        <div class="max-w-3xl w-full p-4">
            <div class="bg-white rounded overflow-hidden">
                <div class="p-2 flex justify-end">
                    <button onclick="closeGallery()" class="text-gray-600 px-3 py-1">Fermer</button>
                </div>
                <div class="p-4">
                    <img id="gallery-current" src="" class="w-full h-96 object-contain" alt="current">
                </div>
            </div>
        </div>
    </div>

    <script>
        const galleryImages = @json(array_values($images));
        function openGallery(i){
            const modal = document.getElementById('gallery-modal');
            const img = document.getElementById('gallery-current');
            img.src = galleryImages[i] || '';
            modal.classList.remove('hidden');
            modal.classList.add('flex');
        }
        function closeGallery(){
            const modal = document.getElementById('gallery-modal');
            modal.classList.remove('flex');
            modal.classList.add('hidden');
        }
    </script>
</div>
