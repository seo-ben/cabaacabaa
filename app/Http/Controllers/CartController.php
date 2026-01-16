<?php

namespace App\Http\Controllers;

use App\Models\Plat;
use Illuminate\Http\Request;

class CartController extends Controller
{
    /**
     * Afficher le contenu du panier.
     */
    public function index()
    {
        $cart = session()->get('cart', []);
        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        return view('cart.index', compact('cart', 'total'));
    }

    /**
     * Ajouter un plat au panier.
     */
    public function add(Request $request, $id)
    {
        $plat = Plat::with(['vendeur', 'groupesVariantes.variantes'])->findOrFail($id);
        $cart = session()->get('cart', []);

        // If we are editing/replacing an item
        if ($request->has('replace_key') && isset($cart[$request->replace_key])) {
            unset($cart[$request->replace_key]);
        }

        // Si le panier n'est pas vide, on vérifie que le plat appartient au même vendeur
        // (Règle métier classique pour la livraison: un seul restaurant par commande)
        if (!empty($cart)) {
            $firstItem = reset($cart);
            if ($firstItem['id_vendeur'] != $plat->id_vendeur) {
                return response()->json([
                    'error' => 'Vous ne pouvez commander que chez un seul restaurant à la fois. Voulez-vous vider votre panier actuel ?',
                    'can_clear' => true
                ], 400);
            }
        }

        // Process variant options if provided
        $options = [];
        $optionsPrice = 0;

        if ($request->has('options') && is_array($request->options)) {
            foreach ($request->options as $groupId => $selection) {
                $groupe = $plat->groupesVariantes->firstWhere('id_groupe', $groupId);
                if (!$groupe)
                    continue;

                $selectedVariants = [];

                // Format can be: {variantId: quantity} or [variantId1, variantId2] or variantId
                if (is_array($selection)) {
                    // Check if it's an associative array (variantId => quantity)
                    $isAssoc = array_keys($selection) !== range(0, count($selection) - 1);
                    
                    if ($isAssoc) {
                        foreach ($selection as $variantId => $quantity) {
                            $variant = $groupe->variantes->firstWhere('id_variante', $variantId);
                            if ($variant && $quantity > 0) {
                                $selectedVariants[] = [
                                    'id' => $variant->id_variante,
                                    'nom' => $variant->nom,
                                    'prix' => $variant->prix_supplement,
                                    'quantite' => intval($quantity)
                                ];
                                $optionsPrice += floatval($variant->prix_supplement) * intval($quantity);
                            }
                        }
                    } else {
                        foreach ($selection as $variantId) {
                            $variant = $groupe->variantes->firstWhere('id_variante', $variantId);
                            if ($variant) {
                                $selectedVariants[] = [
                                    'id' => $variant->id_variante,
                                    'nom' => $variant->nom,
                                    'prix' => $variant->prix_supplement,
                                    'quantite' => 1
                                ];
                                $optionsPrice += floatval($variant->prix_supplement);
                            }
                        }
                    }
                } else {
                    $variant = $groupe->variantes->firstWhere('id_variante', $selection);
                    if ($variant) {
                        $selectedVariants[] = [
                            'id' => $variant->id_variante,
                            'nom' => $variant->nom,
                            'prix' => $variant->prix_supplement,
                            'quantite' => 1
                        ];
                        $optionsPrice += floatval($variant->prix_supplement);
                    }
                }

                if (!empty($selectedVariants)) {
                    $options[] = [
                        'groupe' => $groupe->nom,
                        'variantes' => $selectedVariants
                    ];
                }
            }
        }

        // Create unique cart key based on product ID and options
        $cartKey = $id;
        if (!empty($options)) {
            $cartKey = $id . '_' . md5(json_encode($options));
        }

        // Calculate final price (base + options)
        $basePrice = $plat->en_promotion ? $plat->prix_promotion : $plat->prix;
        $finalPrice = $basePrice + $optionsPrice;

        // If exact same item with same options exists, increment quantity
        if (isset($cart[$cartKey])) {
            $cart[$cartKey]['quantity']++;
        } else {
            // Add new item to cart
            $cart[$cartKey] = [
                "id" => $plat->id_plat,
                "name" => $plat->nom_plat,
                "quantity" => 1,
                "price" => $finalPrice,
                "base_price" => $basePrice,
                "options_price" => $optionsPrice,
                "image" => $plat->image_principale,
                "id_vendeur" => $plat->id_vendeur,
                "vendor_name" => $plat->vendeur->nom_commercial,
                "options" => $options
            ];
        }

        session()->put('cart', $cart);

        if ($request->ajax()) {
            return response()->json([
                'success' => 'Plat ajouté au panier !',
                'cart_count' => count($cart)
            ]);
        }

        return redirect()->back()->with('success', 'Plat ajouté au panier !');
    }

    /**
     * Mettre à jour la quantité d'un plat dans le panier.
     */
    public function update(Request $request)
    {
        if ($request->id && $request->quantity) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                $cart[$request->id]["quantity"] = intval($request->quantity);
                session()->put('cart', $cart);

                $itemTotal = $cart[$request->id]["price"] * $cart[$request->id]["quantity"];
                $total = 0;
                foreach ($cart as $item) {
                    $total += $item['price'] * $item['quantity'];
                }

                return response()->json([
                    'success' => 'Panier mis à jour !',
                    'item_total' => number_format($itemTotal, 0, ',', ' '),
                    'cart_total' => number_format($total, 0, ',', ' '),
                    'cart_count' => count($cart)
                ]);
            }
        }
        return response()->json(['error' => 'Erreur lors de la mise à jour'], 400);
    }

    /**
     * Supprimer un plat du panier.
     */
    public function remove(Request $request)
    {
        if ($request->id) {
            $cart = session()->get('cart');
            if (isset($cart[$request->id])) {
                unset($cart[$request->id]);
                session()->put('cart', $cart);

                $total = 0;
                foreach ($cart as $item) {
                    $total += $item['price'] * $item['quantity'];
                }

                return response()->json([
                    'success' => 'Plat retiré !',
                    'cart_total' => number_format($total, 0, ',', ' '),
                    'cart_count' => count($cart)
                ]);
            }
        }
        return response()->json(['error' => 'Erreur lors de la suppression'], 400);
    }

    /**
     * Vider le panier.
     */
    public function clear()
    {
        session()->forget('cart');
        return redirect()->back()->with('success', 'Panier vidé !');
    }
}
