@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-white to-slate-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-12">
            <h1 class="text-4xl font-display font-bold text-slate-900 mb-4">Politique de Confidentialité</h1>
            <p class="text-slate-500 font-medium tracking-wide border-y border-slate-100 py-3 inline-block">Dernière mise à jour : {{ date('d F Y') }}</p>
        </div>

        <!-- Content -->
        <div class="bg-white rounded-3xl p-8 sm:p-12 shadow-sm border border-slate-100 prose prose-slate max-w-none">
            <div class="space-y-12">
                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">Collecte des informations</h2>
                    <p class="text-slate-600 leading-relaxed">
                        Nous recueillons des informations lorsque vous vous inscrivez sur notre site, lorsque vous vous connectez à votre compte et/ou lorsque vous passez une commande. Les informations recueillies incluent votre nom, votre adresse e-mail, votre numéro de téléphone et votre adresse de livraison.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">Utilisation des informations</h2>
                    <p class="text-slate-600 leading-relaxed">
                        Toutes les informations que nous recueillons auprès de vous peuvent être utilisées pour :
                    </p>
                    <ul class="text-slate-600 list-disc list-inside mt-4 space-y-2">
                        <li>Personnaliser votre expérience et répondre à vos besoins individuels</li>
                        <li>Fournir un contenu publicitaire personnalisé</li>
                        <li>Améliorer notre site Web</li>
                        <li>Améliorer le service client et vos besoins de prise en charge</li>
                        <li>Vous contacter par e-mail</li>
                        <li>Administrer un concours, une promotion, ou un enquête</li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">Confidentialité du commerce en ligne</h2>
                    <p class="text-slate-600 leading-relaxed">
                        Nous sommes les seuls propriétaires des informations recueillies sur ce site. Vos informations personnelles ne seront pas vendues, échangées, transférées, ou données à une autre société pour n'importe quelle raison, sans votre consentement, en dehors de ce qui est nécessaire pour répondre à une demande et / ou une transaction, comme par exemple pour expédier une commande.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">Divulgation à des tiers</h2>
                    <p class="text-slate-600 leading-relaxed">
                        Nous ne vendons, n'échangeons et ne transférons pas vos informations personnelles identifiables à des tiers. Cela ne comprend pas les tierce parties de confiance qui nous aident à exploiter notre site Web ou à mener nos affaires, tant que ces parties conviennent de garder ces informations confidentielles.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">Protection des informations</h2>
                    <p class="text-slate-600 leading-relaxed">
                        Nous mettons en œuvre une variété de mesures de sécurité pour préserver la sécurité de vos informations personnelles. Nous utilisons un cryptage à la pointe de la technologie pour protéger les informations sensibles transmises en ligne. Nous protégeons également vos informations hors ligne.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-bold text-slate-900 mb-4">Consentement</h2>
                    <p class="text-slate-600 leading-relaxed">
                        En utilisant notre site, vous consentez à notre politique de confidentialité.
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
