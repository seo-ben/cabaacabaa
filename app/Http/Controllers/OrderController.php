<?php

namespace App\Http\Controllers;

use App\Models\Commande;
use App\Models\LigneCommande;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    protected $qosic_url;
    protected $qosic_login;
    protected $qosic_password;
    protected $qosic_clientid;

    public function __construct()
    {
        $this->qosic_url = \App\Models\AppSetting::get('qosic_url', env('QOSPAY_REQUEST_URL', 'https://api.qosic.net/QosicBridge/user/requestpayment'));
        $this->qosic_login = \App\Models\AppSetting::get('qosic_login', env('QOSPAY_LOGIN'));
        $this->qosic_password = \App\Models\AppSetting::get('qosic_password', env('QOSPAY_PASSWORD'));
        $this->qosic_clientid = \App\Models\AppSetting::get('qosic_client_id', env('QOSPAY_CLIENT_ID'));
    }

    /**
     * Display a listing of the orders for the authenticated user.
     */
    public function index()
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('info', 'Connectez-vous pour voir vos commandes.');
        }

        $commandes = Commande::with(['vendeur'])
            ->where('id_client', Auth::id())
            ->orderBy('id_commande', 'desc')
            ->paginate(10);

        return view('orders.index', compact('commandes'));
    }

    /**
     * Display the checkout page.
     */
    public function checkout()
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Votre panier est vide.');
        }

        $total = 0;
        $vendeur = null;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
            if (!$vendeur) {
                $vendeur = \App\Models\Vendeur::find($item['id_vendeur']);
            }
        }

        return view('cart.checkout', compact('cart', 'total', 'vendeur'));
    }

    /**
     * Calculate delivery fee based on distance.
     */
    public function calculateDeliveryFee(Request $request)
    {
        $vendeur = \App\Models\Vendeur::findOrFail($request->vendeur_id);

        $lat1 = $vendeur->latitude;
        $lon1 = $vendeur->longitude;
        $lat2 = $request->lat;
        $lon2 = $request->lng;

        if (!$lat1 || !$lon1 || !$lat2 || !$lon2) {
            return response()->json([
                'distance' => 0,
                'fee' => 500,
                'estimated_time' => 20,
                'status' => 'coord_missing'
            ]);
        }

        // Haversine formula
        $earthRadius = 6371; // km
        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);
        $a = sin($dLat / 2) * sin($dLat / 2) +
            cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
            sin($dLon / 2) * sin($dLon / 2);
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
        $distanceKm = $earthRadius * $c;

        // Business rules for delivery
        $baseFee = 300;
        $perKmRate = 150;

        // Final calculation
        $calculatedFee = $baseFee + ($distanceKm * $perKmRate);

        // Round to nearest 50 FCFA
        $fee = round($calculatedFee / 50) * 50;

        // Minimum fee from settings
        $minFee = (int) \App\Models\AppSetting::get('default_delivery_fee', 500);
        if ($fee < $minFee)
            $fee = $minFee;

        // Max distance check (e.g., 25km)
        $maxDistance = 25;
        $outOfRange = $distanceKm > $maxDistance;

        // Estimated delivery time (15 mins prep + 3 mins per km)
        $estTime = 15 + ceil($distanceKm * 3);

        return response()->json([
            'distance' => round($distanceKm, 2),
            'fee' => (int) $fee,
            'estimated_time' => $estTime,
            'out_of_range' => $outOfRange,
            'max_distance' => $maxDistance
        ]);
    }

    /**
     * Process the order.
     */
    public function processCheckout(Request $request)
    {
        $cart = session()->get('cart', []);
        if (empty($cart)) {
            return redirect()->route('home')->with('error', 'Votre panier est vide.');
        }

        $rules = [
            'nom_complet' => 'required|string|max:200',
            'phone' => 'required|string|max:20',
            'type_recuperation' => 'required|in:emporter,sur_place,livraison',
            // ============================================================================
            // PAIEMENT EN LIGNE - TEMPORAIREMENT DÉSACTIVÉ
            // ============================================================================
            // TODO: Réactiver 'mobile_money' quand Tmoney, Flooz seront opérationnels
            // Ancienne validation: 'mode_paiement' => 'required|in:espece,mobile_money',
            // ============================================================================
            'mode_paiement' => 'required|in:espece', // Seul espèces accepté pour le moment
            'notes' => 'nullable|string',
        ];

        if ($request->type_recuperation == 'livraison') {
            $rules['adresse_livraison'] = 'required|string|max:255';
            $rules['lat'] = 'required';
            $rules['lng'] = 'required';
        }

        $request->validate($rules);

        // Format phone number for QOSPAY (228XXXXXXXX)
        $rawPhone = preg_replace('/[^0-9]/', '', $request->phone);
        if (strlen($rawPhone) == 8) {
            $formattedPhone = '228' . $rawPhone;
        } elseif (strlen($rawPhone) == 11 && str_starts_with($rawPhone, '228')) {
            $formattedPhone = $rawPhone;
        } else {
            $formattedPhone = $rawPhone; // Keep as is if unknown format
        }

        $totalPlats = 0;
        $id_vendeur = null;
        foreach ($cart as $item) {
            $totalPlats += $item['price'] * $item['quantity'];
            $id_vendeur = $item['id_vendeur'];
        }

        $fraisLivraison = 0;
        $distance = 0;
        if ($request->type_recuperation == 'livraison') {
            $deliveryInfo = $this->calculateDeliveryFee(new Request([
                'vendeur_id' => $id_vendeur,
                'lat' => $request->lat,
                'lng' => $request->lng
            ]))->getData();
            $fraisLivraison = $deliveryInfo->fee;
            $distance = $deliveryInfo->distance;
        }

        $totalOrder = $totalPlats + $fraisLivraison;

        try {
            DB::beginTransaction();

            $commande = new Commande();
            $commande->numero_commande = 'CMD-' . strtoupper(Str::random(8));
            $commande->id_client = Auth::id();
            $commande->nom_complet_client = $request->nom_complet;
            $commande->telephone_client = $request->phone;
            $commande->id_vendeur = $id_vendeur;
            $commande->statut = 'en_attente';
            $commande->type_recuperation = $request->type_recuperation;
            $commande->adresse_livraison = $request->adresse_livraison;
            $commande->latitude_livraison = $request->lat;
            $commande->longitude_livraison = $request->lng;
            $commande->distance_livraison = $distance;
            $commande->frais_livraison = $fraisLivraison;
            $commande->mode_paiement_prevu = $request->mode_paiement;
            $commande->montant_plats = $totalPlats;
            $commande->montant_total = $totalOrder;
            $commande->instructions_speciales = $request->notes;
            $commande->save();

            foreach ($cart as $id => $item) {
                $ligne = new LigneCommande();
                $ligne->id_commande = $commande->id_commande;
                $ligne->id_plat = $item['id']; // Use the actual product ID, not the cart key
                $ligne->nom_plat_snapshot = $item['name'];
                $ligne->quantite = $item['quantity'];
                $ligne->prix_unitaire = $item['price'];
                $ligne->sous_total = $item['price'] * $item['quantity'];
                $ligne->options = $item['options'] ?? null; // Save the selected options
                $ligne->save();
            }

            DB::commit();

            // Notify vendor in real-time
            event(new \App\Events\OrderPlaced($commande));

            // Notify Admins
            $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'])->get();
            foreach ($admins as $admin) {
                \App\Models\Notification::create([
                    'id_utilisateur' => $admin->id_user,
                    'type_notification' => 'nouvelle_commande',
                    'titre' => 'Nouvelle commande reçue',
                    'message' => "La commande {$commande->numero_commande} d'un montant de {$commande->montant_total} FCFA a été passée.",
                    'id_commande' => $commande->id_commande,
                    'lue' => false,
                    'date_creation' => now(),
                ]);
            }

            // ============================================================================
            // PAIEMENT EN LIGNE MOBILE MONEY - TEMPORAIREMENT DÉSACTIVÉ
            // ============================================================================
            // TODO: Réactiver quand l'intégration QOSPAY (Tmoney, Flooz) sera prête
            // ============================================================================
            
            /*
            if ($request->mode_paiement == 'mobile_money') {
                try {
                    // Call QOSPAY API
                    $response = Http::withBasicAuth($this->qosic_login, $this->qosic_password)
                        ->post($this->qosic_url, [
                            'msisdn' => $formattedPhone,
                            'amount' => (int) $totalOrder,
                            'firstname' => $request->nom_complet,
                            'lastname' => '-',
                            'transref' => $commande->numero_commande,
                            'clientid' => $this->qosic_clientid,
                        ]);

                    if ($response->successful()) {
                        // For push payments, we just tell the user to check their phone
                        session()->forget('cart');
                        return redirect()->route('order.confirmation', $commande->id_commande)
                            ->with('success', 'Demande de paiement envoyée ! Veuillez confirmer sur votre téléphone.');
                    } else {
                        return redirect()->back()->with('error', 'Erreur QOSPAY : ' . ($response->json('message') ?? 'Échec de la connexion'));
                    }
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Erreur technique QOSPAY : ' . $e->getMessage());
                }
            }
            */

            session()->forget('cart');
            return redirect()->route('order.confirmation', $commande->id_commande)->with('success', 'Votre commande a été passée avec succès !');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Erreur : ' . $e->getMessage());
        }
    }

    /**
     * Handle QOSPAY callback / Webhook.
     */
    public function callback(Request $request)
    {
        // QosPay often sends transref and status
        $transref = $request->transref;
        $status = $request->status; // 'SUCCESSFUL', 'FAILED', etc.

        $commande = Commande::where('numero_commande', $transref)->first();

        if ($commande && $status === 'SUCCESSFUL') {
            $commande->paiement_effectue = true;
            $commande->statut = 'confirmee';
            $commande->save();
            return response()->json(['status' => 'OK']);
        }

        return response()->json(['status' => 'ERROR'], 400);
    }

    /**
     * Cancel an order.
     */
    public function cancel($id)
    {
        $commande = Commande::findOrFail($id);

        // Security check
        if (Auth::id() != $commande->id_client) {
            abort(403);
        }

        // Only allow cancellation if order is in 'en_attente'
        if ($commande->statut !== 'en_attente') {
            return redirect()->back()->with('error', 'Cette commande ne peut plus être annulée.');
        }

        $commande->statut = 'annulee';
        $commande->date_annulation = now();
        $commande->raison_annulation = 'Annulée par le client';
        $commande->save();

        return redirect()->back()->with('success', 'Votre commande a été annulée.');
    }

    /**
     * Reorder items from a previous order.
     */
    public function reorder($id)
    {
        $commande = Commande::with('lignes')->findOrFail($id);

        // Security check
        if (Auth::id() != $commande->id_client) {
            abort(403);
        }

        $cart = session()->get('cart', []);

        foreach ($commande->lignes as $ligne) {
            // Check if product still exists
            $plat = \App\Models\Plat::with('vendeur')->find($ligne->id_plat);
            if ($plat) {
                // Determine image
                $image = $plat->image_principale;
                if (!$image && $plat->images_galerie && count($plat->images_galerie) > 0) {
                    $image = $plat->images_galerie[0];
                }

                $cart[$plat->id_plat] = [
                    "id" => $plat->id_plat,
                    "name" => $plat->nom_plat,
                    "quantity" => $ligne->quantite,
                    "price" => $plat->en_promotion ? $plat->prix_promotion : $plat->prix,
                    "image" => $plat->image_principale,
                    "id_vendeur" => $plat->id_vendeur,
                    "vendor_name" => $plat->vendeur->nom_commercial ?? 'Restaurant'
                ];
            }
        }

        session()->put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Articles ajoutés au panier !');
    }

    /**
     * Display order confirmation.
     */
    public function confirmation($id)
    {
        $commande = Commande::with(['vendeur', 'lignes', 'avis'])->findOrFail($id);

        if (Auth::check()) {
            if (Auth::id() != $commande->id_client && (!Auth::user()->vendeur || Auth::user()->vendeur->id_vendeur != $commande->id_vendeur)) {
                abort(403);
            }
        }
        
        // Store in session to allow chat for guests/new orders
        session()->put('viewed_order_' . $commande->id_commande, true);

        return view('cart.confirmation', compact('commande'));
    }

    /**
     * Show tracking search page.
     */
    public function trackOrder(Request $request)
    {
        $code = $request->get('code');
        $commande = null;

        if ($code) {
            $commande = Commande::with(['vendeur', 'lignes', 'avis'])
                ->where('numero_commande', 'LIKE', '%' . $code)
                ->first();

            if ($commande) {
                // Store in session to allow chat for guests
                session()->put('viewed_order_' . $commande->id_commande, true);
            } else {
                return redirect()->back()->with('error', 'Commande introuvable avec ce code.');
            }
        }

        return view('orders.track', compact('commande', 'code'));
    }

    /**
     * API to check order status for real-time updates.
     */
    public function checkStatus($code)
    {
        $commande = Commande::where('numero_commande', $code)->first();

        if (!$commande) {
            return response()->json(['error' => 'Commade non trouvée'], 404);
        }

        return response()->json([
            'statut' => $commande->statut,
            'statut_label' => ucfirst(str_replace('_', ' ', $commande->statut)),
            'numero' => $commande->numero_commande
        ]);
    }
}
