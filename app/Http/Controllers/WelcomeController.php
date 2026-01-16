<?php

namespace App\Http\Controllers;

use App\Models\Vendeur;
use App\Models\Plat;
use Illuminate\Http\Request;

class WelcomeController extends Controller
{
    public function index()
    {
        // Fetch a few featured sellers and plats if models exist
        $vendeurs = [];
        $plats = [];

        try {
            $vendeurs = Vendeur::where('actif', 1)->orderBy('date_inscription', 'desc')->take(6)->get();
        } catch (\Throwable $e) {
            // models/migration might not exist yet on minimal installs
        }

        try {
            $plats = Plat::where('disponible', 1)->orderBy('date_creation', 'desc')->take(8)->get();
        } catch (\Throwable $e) {
        }

        return view('welcome', compact('vendeurs','plats'));
    }
}
