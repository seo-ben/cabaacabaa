@extends('layouts.admin')

@section('title', 'Créer une Catégorie')

@section('content')
<div class="max-w-3xl mx-auto space-y-10">
    <div class="flex items-center justify-between">
        <a href="{{ route('admin.categories.index') }}" class="group flex items-center gap-3 text-gray-400 hover:text-red-600 transition-colors">
            <div class="w-10 h-10 bg-white border border-gray-100 rounded-xl flex items-center justify-center group-hover:bg-red-50 group-hover:border-red-100 transition-all">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
            </div>
            <span class="text-xs font-black uppercase tracking-widest">Toutes les catégories</span>
        </a>
    </div>

    <div class="bg-white rounded-[2.5rem] border border-gray-100 shadow-sm overflow-hidden">
        <div class="px-10 py-8 border-b border-gray-50 bg-gray-50/20">
            <h1 class="text-2xl font-black text-gray-900 tracking-tight">Nouvelle Catégorie</h1>
            <p class="text-[10px] font-black uppercase text-gray-400 tracking-widest mt-1">Définissez une spécialité globale</p>
        </div>

        <form action="{{ route('admin.categories.store') }}" method="POST" enctype="multipart/form-data" class="p-10 space-y-8">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Nom de la Catégorie</label>
                    <input type="text" name="nom_categorie" required placeholder="ex: Africain, Fast Food..."
                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Icône / Emoji</label>
                    <input type="text" name="icone" placeholder="ex: 🍕, 🍲, 🥐"
                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                </div>
            </div>

            <div class="space-y-2">
                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Description (Optionnel)</label>
                <textarea name="description" rows="3" placeholder="Brève description de ce type de cuisine..."
                    class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-[2rem] font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all resize-none"></textarea>
            </div>

            <!-- Image Upload Section -->
            <div class="space-y-4">
                <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Image de la catégorie</label>
                <div id="image-upload-zone" class="relative border-2 border-dashed border-gray-200 rounded-[2rem] p-8 text-center hover:border-red-400 transition-all cursor-pointer bg-gray-50/50">
                    <input type="file" name="image" id="image-input" accept="image/*" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                    <div id="upload-placeholder" class="space-y-4">
                        <div class="w-16 h-16 bg-red-50 rounded-2xl flex items-center justify-center mx-auto">
                            <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-sm font-bold text-gray-600">Cliquez ou glissez une image ici</p>
                            <p class="text-xs text-gray-400 mt-1">PNG, JPG, WEBP jusqu'à 2MB</p>
                        </div>
                    </div>
                    <div id="image-preview" class="hidden">
                        <img id="preview-img" src="" alt="Preview" class="max-h-48 mx-auto rounded-2xl shadow-lg">
                        <p id="preview-name" class="text-sm font-bold text-gray-600 mt-4"></p>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Ordre d'affichage</label>
                    <input type="number" name="ordre_affichage" value="0"
                        class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all">
                </div>
                <div class="space-y-2">
                    <label class="block text-[11px] font-black text-gray-400 uppercase tracking-widest ml-1">Disponibilité</label>
                    <select name="actif" required class="w-full px-6 py-4 bg-gray-50 border-2 border-transparent rounded-2xl font-bold text-gray-900 focus:bg-white focus:border-red-500 outline-none transition-all appearance-none cursor-pointer">
                        <option value="1">Disponible (Visible)</option>
                        <option value="0">Désactivé (Masqué)</option>
                    </select>
                </div>
            </div>

            <div class="flex gap-4 pt-6 border-t border-gray-50">
                <button type="submit" class="flex-1 px-10 py-5 bg-red-600 text-white rounded-[2rem] font-black uppercase text-[11px] tracking-widest shadow-xl shadow-red-200 hover:bg-black hover:shadow-none transition-all active:scale-95 flex items-center justify-center gap-3">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                    Créer la Catégorie
                </button>
                <a href="{{ route('admin.categories.index') }}" class="px-10 py-5 bg-gray-100 text-gray-600 rounded-[2rem] font-black uppercase text-[11px] tracking-widest hover:bg-gray-200 transition-all flex items-center justify-center">
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('image-input');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    const previewName = document.getElementById('preview-name');

    imageInput.addEventListener('change', function(e) {
        if (e.target.files && e.target.files[0]) {
            const file = e.target.files[0];
            const reader = new FileReader();
            
            reader.onload = function(e) {
                previewImg.src = e.target.result;
                previewName.textContent = file.name;
                uploadPlaceholder.classList.add('hidden');
                imagePreview.classList.remove('hidden');
            };
            
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection
