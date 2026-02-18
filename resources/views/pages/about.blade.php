@extends('layouts.app')

@section('title', 'À propos de ' . config('app.name'))

@section('content')
<div class="bg-gray-50 dark:bg-gray-950 py-12 md:py-24">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h1 class="text-3xl md:text-5xl font-black text-gray-900 dark:text-white mb-6">Notre Mission : Connecter le Local</h1>
            <p class="text-xl text-gray-600 dark:text-gray-400 max-w-2xl mx-auto">
                {{ config('app.name') }} transforme la façon dont vous découvrez et commandez auprès des commerces de votre ville.
            </p>
        </div>

        <div class="grid md:grid-cols-2 gap-12 items-center mb-24">
            <div class="rounded-[2.5rem] overflow-hidden shadow-2xl transform rotate-1">
                <img src="{{ asset('assets/cabaacabaa_logo/5e67d812-c344-4c34-a0ac-b60a19080bde.png') }}" class="w-full h-full object-cover">
            </div>
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Plus qu'une application de livraison</h2>
                <div class="space-y-6 text-gray-600 dark:text-gray-400 leading-relaxed">
                    <p>
                        Née de la volonté de soutenir l'économie locale, {{ config('app.name') }} offre une vitrine digitale puissante aux commerçants de quartier tout en apportant un confort inégalé aux consommateurs.
                    </p>
                    <p>
                        Que ce soit pour un repas rapide, vos courses de la semaine ou un cadeau de dernière minute, notre technologie vous connecte instantanément aux meilleurs produits de votre ville.
                    </p>
                    <div class="flex gap-4 pt-4">
                        <div class="text-center">
                            <span class="block text-3xl font-black text-red-600">100%</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Local</span>
                        </div>
                        <div class="w-px bg-gray-200 dark:bg-slate-700"></div>
                        <div class="text-center">
                            <span class="block text-3xl font-black text-red-600">24/7</span>
                            <span class="text-xs font-bold uppercase tracking-wider text-gray-500">Support</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
