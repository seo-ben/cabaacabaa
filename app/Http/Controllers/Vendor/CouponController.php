<?php

namespace App\Http\Controllers\Vendor;

use App\Http\Controllers\Controller;
use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CouponController extends Controller
{
    public function index(Request $request, $vendor_slug)
    {
        $vendeur = $request->get('current_vendor') ?? Auth::user()->vendeur;
        $coupons = Coupon::where('id_vendeur', $vendeur->id_vendeur)
            ->orderBy('id_coupon', 'desc')
            ->get();

        return view('vendeur.coupons.index', compact('coupons'));
    }

    public function store(Request $request, $vendor_slug)
    {
        $vendeur = $request->get('current_vendor') ?? Auth::user()->vendeur;

        $request->validate([
            'code' => 'required|string|max:20|unique:coupons,code',
            'type' => 'required|in:percentage,fixed',
            'valeur' => 'required|numeric|min:0',
            'montant_minimal_achat' => 'required|numeric|min:0',
            'limite_utilisation' => 'nullable|integer|min:1',
            'expire_at' => 'nullable|date|after:today',
        ]);

        Coupon::create([
            'id_vendeur' => $vendeur->id_vendeur,
            'code' => strtoupper($request->code),
            'type' => $request->type,
            'valeur' => $request->valeur,
            'montant_minimal_achat' => $request->montant_minimal_achat,
            'limite_utilisation' => $request->limite_utilisation,
            'expire_at' => $request->expire_at,
            'actif' => true,
        ]);

        return redirect()->back()->with('success', 'Coupon créé avec succès !');
    }

    public function toggle(Request $request, $vendor_slug, Coupon $coupon)
    {
        $vendeur = $request->get('current_vendor') ?? Auth::user()->vendeur;
        $this->authorizeOwner($coupon, $vendeur);

        $coupon->update(['actif' => !$coupon->actif]);

        return redirect()->back()->with('success', 'Statut du coupon mis à jour !');
    }

    public function destroy(Request $request, $vendor_slug, Coupon $coupon)
    {
        $vendeur = $request->get('current_vendor') ?? Auth::user()->vendeur;
        $this->authorizeOwner($coupon, $vendeur);

        $coupon->delete();

        return redirect()->back()->with('success', 'Coupon supprimé !');
    }

    private function authorizeOwner(Coupon $coupon, $vendeur)
    {
        if (!$vendeur || $coupon->id_vendeur !== $vendeur->id_vendeur) {
            abort(403, 'Action non autorisée.');
        }
    }
}
