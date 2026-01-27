<?php

namespace App\Http\Controllers;

use App\Models\FavorisClient;
use App\Models\Vendeur;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FavoriteController extends Controller
{
    /**
     * Affiche la liste des vendeurs favoris de l'utilisateur.
     */
    public function index()
    {
        $user = Auth::user();
        $favoris = $user->favoris()->with('vendeur')->get();
        
        return view('favoris.index', compact('favoris'));
    }

    /**
     * Ajoute ou supprime un vendeur des favoris.
     */
    public function toggle($vendorId)
    {
        if (!Auth::check()) {
            return response()->json([
                'error' => 'Vous devez être connecté pour ajouter des favoris.'
            ]);
        }

        $user = Auth::user();
        $vendor = Vendeur::findOrFail($vendorId);

        $favorite = FavorisClient::where('id_client', $user->id_user)
            ->where('id_vendeur', $vendorId)
            ->first();

        if ($favorite) {
            $favorite->delete();
            return response()->json([
                'success' => 'Vendeur supprimé des favoris.',
                'action' => 'removed'
            ]);
        } else {
            FavorisClient::create([
                'id_client' => $user->id_user,
                'id_vendeur' => $vendorId,
                'date_ajout' => now()
            ]);
            return response()->json([
                'success' => 'Vendeur ajouté aux favoris.',
                'action' => 'added'
            ]);
        }
    }

    /**
     * Vérifie si un vendeur est en favori.
     */
    public function check($vendorId)
    {
        if (!Auth::check()) {
            return response()->json(['is_favorite' => false]);
        }

        $isFavorite = FavorisClient::where('id_client', Auth::id())
            ->where('id_vendeur', $vendorId)
            ->exists();

        return response()->json(['is_favorite' => $isFavorite]);
    }
}
