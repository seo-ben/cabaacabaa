@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-display font-bold text-slate-900 mb-4">Conditions d'Utilisation</h1>
            <p class="text-slate-500 font-medium tracking-wide border-y border-slate-100 py-3 inline-block">Dernière mise à jour : {{ date('d F Y') }}</p>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-100 prose prose-slate max-w-none">
            <div class="space-y-12">
                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-sm">01</span>
                        Acceptation des Conditions
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        En accédant et en utilisant la plateforme {{ config('app.name') }}, vous acceptez d'être lié par les présentes Conditions d'Utilisation. Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser nos services.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-sm">02</span>
                        Description du Service
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        {{ config('app.name') }} est une place de marché en ligne qui permet aux utilisateurs de commander de la nourriture auprès de vendeurs locaux indépendants. Nous agissons en tant qu'intermédiaire facilitant la transaction entre vous et les vendeurs.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-sm">03</span>
                        Comptes Utilisateurs
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Pour utiliser certains services, vous devez créer un compte. Vous êtes responsable de la confidentialité de votre mot de passe et de toutes les activités effectuées sous votre compte. Vous vous engagez à fournir des informations exactes et à jour.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-sm">04</span>
                        Commandes et Paiements
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Les commandes passées sur la plateforme sont définitives une fois acceptées par le vendeur. Les paiements peuvent être effectués en ligne ou à la livraison selon les options disponibles. Les prix incluent les taxes applicables mais peuvent exclure les frais de livraison.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-sm">05</span>
                        Responsabilités
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Bien que nous fassions de notre mieux pour assurer un service de qualité, {{ config('app.name') }} ne peut être tenu responsable de la qualité intrinsèque des plats préparés par les vendeurs, des délais de livraison imprévus ou des allergies non signalées par l'utilisateur.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 bg-orange-100 text-orange-600 rounded-lg flex items-center justify-center text-sm">06</span>
                        Modifications des conditions
                    </h2>
                    <p class="text-slate-600 leading-relaxed">
                        Nous nous réservons le droit de modifier ces conditions à tout moment. Les modifications entreront en vigueur dès leur publication sur cette page. Il est de votre responsabilité de consulter régulièrement ces conditions.
                    </p>
                </section>
            </div>
            
            <div class="mt-12 pt-8 border-t border-slate-100 text-center">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 text-orange-600 font-bold hover:text-orange-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Retour à l'accueil
                </a>
            </div>
        </div>
    </div>
</div>
@endsection
