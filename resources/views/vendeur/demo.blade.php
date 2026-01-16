@extends('layouts.app')

@section('content')
    <div class="space-y-6">
        <div class="bg-white p-6 rounded shadow">
            <h2 class="text-2xl font-semibold">Démonstration page vendeur</h2>
            <p class="text-gray-600">Exemple d'utilisation des composants : galerie, horaires et carte de plat.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <x-gallery :images="['/images/sample1.jpg','/images/sample2.jpg','/images/sample3.jpg']" />

                <x-horaires :horaires="[
                    ['jour' => 'Lundi','heure_ouverture'=>'08:00','heure_fermeture'=>'18:00'],
                    ['jour' => 'Mardi','ferme' => true],
                    ['jour' => 'Dimanche','heure_ouverture'=>'10:00','heure_fermeture'=>'15:00']
                ]" />
            </div>

            <div class="space-y-4">
                <x-pl... 
            </div>
        </div>

        <div class="bg-white p-6 rounded shadow">
            <h3 class="text-lg font-medium">Menu</h3>
            <div class="mt-4 grid grid-cols-1 gap-3">
                <x-plat-card name="Poulet Yassa" description="Délicieux poulet mariné" :price="1500" :tags="['Populaire','Épicé']" :time="25" image="/images/sample1.jpg" />
                <x-plat-card name="Salade fraîche" description="Légère et croquante" :price="800" :tags="['Vegan']" :time="10" available="false" image="/images/sample2.jpg" />
                <x-plat-card name="Bissap glacé" description="Boisson rafraîchissante" :price="300" :tags="['Boisson']" :time="5" image="/images/sample3.jpg" onPromotion="true" :promoPrice="200" />
            </div>
        </div>
    </div>
@endsection
