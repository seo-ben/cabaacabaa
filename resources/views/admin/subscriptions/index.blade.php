@extends('layouts.admin')

@section('title', 'Gestion des Abonnements')

@section('content')
<div class="space-y-6">
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-xl font-black text-gray-900 tracking-tight leading-none uppercase">Plans d'Abonnement</h1>
            <p class="text-[9px] font-black text-gray-400 uppercase tracking-widest mt-1.5 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                Configuration des tarifs et limites
            </p>
        </div>
        <a href="{{ route('admin.subscriptions.create') }}" class="px-4 py-2 bg-gray-900 text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-gray-800 transition-all">
            Nouveau Plan
        </a>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-100 text-emerald-600 px-4 py-3 rounded-xl text-[10px] font-bold uppercase tracking-widest">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @foreach($plans as $plan)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden flex flex-col">
            <div class="p-6 bg-gray-50/50 border-b border-gray-100">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-[9px] font-black text-orange-500 uppercase tracking-widest">{{ $plan->name }}</span>
                    @if($plan->is_boosted)
                        <span class="px-2 py-0.5 bg-orange-100 text-orange-600 text-[8px] font-black uppercase rounded">Boosté</span>
                    @endif
                </div>
                <div class="flex items-baseline gap-1">
                    <span class="text-2xl font-black text-gray-900">{{ number_format($plan->price, 0, ',', ' ') }}</span>
                    <span class="text-[10px] font-bold text-gray-400 uppercase">FCFA / mois</span>
                </div>
            </div>
            
            <div class="p-6 flex-1 space-y-4 text-[10px]">
                <div class="flex items-center justify-between py-1 border-b border-gray-50">
                    <span class="font-bold text-gray-400 uppercase">Limite Produits</span>
                    <span class="font-black text-gray-900">{{ $plan->product_limit }}</span>
                </div>
                <div class="flex items-center justify-between py-1 border-b border-gray-50">
                    <span class="font-bold text-gray-400 uppercase">Limite Staff</span>
                    <span class="font-black text-gray-900">{{ $plan->staff_limit }}</span>
                </div>
                <div class="flex items-center justify-between py-1 border-b border-gray-50">
                    <span class="font-bold text-gray-400 uppercase">Coupons Actifs</span>
                    <span class="font-black text-gray-900">{{ $plan->coupon_limit }}</span>
                </div>
                <div class="flex items-center justify-between py-1 border-b border-gray-50">
                    <span class="font-bold text-gray-400 uppercase">Galerie Photos</span>
                    <span class="font-black {{ $plan->has_gallery ? 'text-emerald-500' : 'text-gray-300' }}">
                        {{ $plan->has_gallery ? 'OUI' : 'NON' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-1 border-b border-gray-50">
                    <span class="font-bold text-gray-400 uppercase">Réseaux Sociaux</span>
                    <span class="font-black {{ $plan->has_socials ? 'text-emerald-500' : 'text-gray-300' }}">
                        {{ $plan->has_socials ? 'OUI' : 'NON' }}
                    </span>
                </div>
                <div class="flex items-center justify-between py-1 border-b border-gray-50">
                    <span class="font-bold text-gray-400 uppercase">Recrutement Livreur</span>
                    <span class="font-black {{ $plan->can_recruit_drivers ? 'text-emerald-500' : 'text-gray-300' }}">
                        {{ $plan->can_recruit_drivers ? 'OUI' : 'NON' }}
                    </span>
                </div>
            </div>

            <div class="p-4 bg-gray-50 flex gap-2">
                <a href="{{ route('admin.subscriptions.edit', $plan->id) }}" class="flex-1 text-center py-2 bg-white border border-gray-200 text-[9px] font-black uppercase tracking-widest text-gray-600 rounded-xl hover:bg-gray-100 transition-all">
                    Modifier
                </a>
                <form action="{{ route('admin.subscriptions.destroy', $plan->id) }}" method="POST" onsubmit="return confirm('Supprimer ce plan ?')" class="flex-shrink-0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="w-10 h-10 flex items-center justify-center bg-white border border-gray-200 text-red-400 rounded-xl hover:bg-red-50 hover:border-red-100 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-4v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                    </button>
                </form>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
