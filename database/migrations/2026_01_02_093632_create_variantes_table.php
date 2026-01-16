<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up()
    {
        Schema::create('variantes', function (Blueprint $table) {
            $table->id('id_variante');
            $table->unsignedBigInteger('id_groupe');
            $table->string('nom'); // Ex: Grande, Moyenne, Ketchup
            $table->decimal('prix_supplement', 10, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_groupe')->references('id_groupe')->on('groupe_variantes')->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::dropIfExists('variantes');
    }
};

// C'est une excellente question. D'après ce que j'ai vu du code (architecture, design premium, structure multi-vendeurs), vous avez déjà une base très solide pour un MVP (Minimum Viable Product).

// Cependant, pour passer d'un "bon site" à une plateforme complète et performante type UberEats ou Glovo, voici les 4 piliers majeurs qu'il faudrait envisager d'ajouter ou de renforcer :

// 1. Gestion Avancée des Plats (Variantes & Suppléments)
// C'est souvent le point bloquant dans la restauration. Actuellement, un plat semble être un produit simple.

// Le Besoin : Un client veut commander une pizza mais choisir la taille (Moyenne/Grande) ou ajouter un supplément (Fromage +200 FCFA).
// L'Ajout : Système d'attributs sur les plats (ex: "Cuisson", "Taille", "Sauces") qui modifient le prix final.
// 2. Le Maillon Logistique : Interface "Livreurs"
// Actuellement, on gère la commande du Client au Vendeur. Mais qui livre ?

// Le Besoin : Une fois la commande "Prête", elle doit être assignée à un livreur.
// L'Ajout :
// Un rôle "Livreur" dans la base de données.
// Une interface mobile simplifiée pour eux (Accepter une course, Voir l'adresse, Valider la livraison).
// Assignation automatique ou manuelle des commandes aux livreurs par zone.
// 3. L'Expérience Temps Réel (WebSockets)
// J'ai vu que nous utilisons du setInterval (polling) toutes les 30 secondes pour vérifier les notifications.

// Le Problème : Cela consomme des ressources serveur inutilement et ce n'est pas "instantané".
// L'Ajout : Utiliser Laravel Reverb ou Pusher.
// Le vendeur reçoit un "GONG" sonore immédiat quand une commande arrive.
// Le client voit le statut changer ("En cuisine" -> "En route") sans recharger la page.
// 4. Marketing Vendeur (Autonomie)
// Pour que votre plateforme vive toute seule, il faut donner des outils aux vendeurs.

// Le Besoin : Un vendeur veut booster ses ventes un mardi soir calme.
// L'Ajout : Permettre aux vendeurs de créer leurs propres Coupons ou Promotions Flash (ex: "-20% sur tout le menu ce soir") directement depuis leur tableau de bord.


// pour l'option 1 je suis tout a fait d'accord on va implémenté cela , si au ajouter d'un produit si la boutique peut ajouter avec les prix qui l'accompagene  les variant qui peut etre ajouter , je sais pas si tu me comprend pas trop pour que cleint peut ajouter des variante et suplément a sa commande , pour les livreur on va les integer mais on ne va pas les activé , on va l"activé le mement ou on a acess a un api de localisation , pour 3. L'Expérience Temps Réel (WebSockets) on va utiliser pusher ; on va integerer aussi le 4. Marketing Vendeur (Autonomie) pour etre en avant 



// Immediate Next Steps (for your information):

// To make this fully functional end-to-end, we would need to:

// Update the Edit Form: Replicate this logic in 
// edit.blade.php
//  (pre-filling existing data) so vendors can modify options later.
// Frontend Display: Update the generic explore-plats page (and potentially a detailed product view) to actually show these options to the client and let them select them before adding to the cart.
// Cart & Order Logic: Update the Cart logic to store the selected options and the Order logic to save them.
// Shall we tackle the Client-side display (Step 2) next so you can see the result?