@extends('layouts.admin')

@section('title', 'Modifier le Plan')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <div class="flex items-center gap-4">
        <a href="{{ route('admin.subscriptions.index') }}" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-100 rounded-xl text-gray-400 hover:text-gray-900 transition-all">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"/></svg>
        </a>
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none uppercase">Modifier {{ $plan->name }}</h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5">Ajustement des paramètres du plan</p>
        </div>
    </div>

    <form action="{{ route('admin.subscriptions.update', $plan->id) }}" method="POST" class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        @csrf
        @method('PUT')
        
        <div class="p-8 space-y-6">
            <div class="grid grid-cols-2 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Nom du Plan</label>
                    <input type="text" name="name" value="{{ old('name', $plan->name) }}" class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-[11px] font-bold focus:ring-2 focus:ring-orange-500/20" required>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Prix (FCFA)</label>
                    <input type="number" name="price" value="{{ old('price', $plan->price) }}" class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-[11px] font-bold focus:ring-2 focus:ring-orange-500/20" required>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-6">
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Limite Produits</label>
                    <input type="number" name="product_limit" value="{{ old('product_limit', $plan->product_limit) }}" class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-[11px] font-bold focus:ring-2 focus:ring-orange-500/20" required>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Limite Staff</label>
                    <input type="number" name="staff_limit" value="{{ old('staff_limit', $plan->staff_limit) }}" class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-[11px] font-bold focus:ring-2 focus:ring-orange-500/20" required>
                </div>
                <div class="space-y-2">
                    <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Coupons Max</label>
                    <input type="number" name="coupon_limit" value="{{ old('coupon_limit', $plan->coupon_limit) }}" class="w-full px-4 py-3 bg-gray-50 border-none rounded-2xl text-[11px] font-bold focus:ring-2 focus:ring-orange-500/20" required>
                </div>
            </div>

            <div class="space-y-4 pt-4">
                <label class="text-[10px] font-black text-gray-400 uppercase tracking-widest">Fonctionnalités & Privilèges</label>
                
                <div class="space-y-3">
                    <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl cursor-pointer group hover:bg-gray-100 transition-all">
                        <input type="checkbox" name="has_gallery" value="1" {{ old('has_gallery', $plan->has_gallery) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-gray-200 text-orange-600 focus:ring-orange-500/20">
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-gray-900 uppercase">Galerie Photos</p>
                            <p class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter">Permettre au vendeur d'ajouter plusieurs photos par produit</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl cursor-pointer group hover:bg-gray-100 transition-all">
                        <input type="checkbox" name="has_socials" value="1" {{ old('has_socials', $plan->has_socials) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-gray-200 text-orange-600 focus:ring-orange-500/20">
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-gray-900 uppercase">Réseaux Sociaux</p>
                            <p class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter">Afficher les liens Facebook / Instagram sur la page boutique</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 bg-gray-50 rounded-2xl cursor-pointer group hover:bg-gray-100 transition-all">
                        <input type="checkbox" name="is_boosted" value="1" {{ old('is_boosted', $plan->is_boosted) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-gray-200 text-orange-600 focus:ring-orange-500/20">
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-gray-900 uppercase">Boost Permanent</p>
                            <p class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter">Le vendeur apparaît toujours en tête de liste (Premium)</p>
                        </div>
                    </label>

                    <label class="flex items-center gap-3 p-4 bg-gray-100 rounded-2xl cursor-pointer group hover:bg-emerald-50 transition-all">
                        <input type="checkbox" name="can_recruit_drivers" value="1" {{ old('can_recruit_drivers', $plan->can_recruit_drivers) ? 'checked' : '' }} class="w-5 h-5 rounded-lg border-gray-200 text-emerald-600 focus:ring-emerald-500/20">
                        <div class="flex-1">
                            <p class="text-[10px] font-black text-gray-900 uppercase">Recrutement Livreur</p>
                            <p class="text-[8px] text-gray-400 font-bold uppercase tracking-tighter text-emerald-600/60">Permettre au vendeur de recruter des livreurs pour sa boutique (15k+)</p>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="p-6 bg-gray-50 border-t border-gray-100 flex justify-end">
            <button type="submit" class="px-8 py-3 bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest rounded-2xl hover:bg-gray-800 shadow-lg shadow-gray-200 transition-all">
                Enregistrer les modifications
            </button>
        </div>
    </form>
</div>
@endsection
