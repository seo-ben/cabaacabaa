<?php

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;

class CouponController extends Controller
{
    public function apply(Request $request)
    {
        $code = $request->input('code');
        $coupon = Coupon::where('code', $code)->first();

        if (!$coupon) {
            return response()->json(['success' => false, 'error' => 'Code promo invalide.']);
        }

        if (!$coupon->isValid()) {
            return response()->json(['success' => false, 'error' => 'Ce code promo a expiré ou a atteint sa limite.']);
        }

        $cart = session()->get('cart', []);

        if (empty($cart)) {
            return response()->json(['success' => false, 'error' => 'Votre panier est vide.']);
        }

        // Check if coupon belongs to the vendor
        if ($coupon->id_vendeur) {
            $firstItem = reset($cart);
            if ($firstItem['id_vendeur'] != $coupon->id_vendeur) {
                return response()->json(['success' => false, 'error' => 'Ce coupon n\'est pas valable pour ce restaurant.']);
            }
        }

        $total = 0;
        foreach ($cart as $item) {
            $total += $item['price'] * $item['quantity'];
        }

        if ($total < $coupon->montant_minimal_achat) {
            return response()->json([
                'success' => false,
                'error' => 'Le montant minimum pour ce coupon est de ' . number_format($coupon->montant_minimal_achat, 0, ',', ' ') . ' FCFA'
            ]);
        }

        // Simuler ou appliquer réellement au panier
        session()->put('coupon', [
            'code' => $coupon->code,
            'type' => $coupon->type,
            'valeur' => $coupon->valeur,
            'id_coupon' => $coupon->id_coupon
        ]);

        $discount = $coupon->type === 'percentage'
            ? ($total * ($coupon->valeur / 100))
            : $coupon->valeur;

        return response()->json([
            'success' => true,
            'message' => 'Coupon appliqué avec succès !',
            'discount' => $discount,
            'new_total' => $total - $discount
        ]);
    }
}
