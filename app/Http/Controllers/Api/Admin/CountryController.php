<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $allCountries = config('countries');
        $activeCountries = Country::where('is_active', '=', true, 'and')->pluck('phone_prefix')->toArray();

        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    /**
     * Mettre à jour la sélection des pays actifs.
     */
    public function updateSelection(Request $request)
    {
        $selectedPrefixes = $request->input('countries', []);
        $allCountries = config('countries');

        // Désactiver tous les pays non sélectionnés ou absents de la base
        Country::query()->update(['is_active' => false]);

        foreach ($allCountries as $countryData) {
            if (in_array($countryData['phone_prefix'], $selectedPrefixes)) {
                Country::updateOrCreate(
                    ['phone_prefix' => $countryData['phone_prefix']],
                    [
                        'name' => $countryData['name'],
                        'flag_icon' => $countryData['flag_icon'],
                        'is_active' => true
                    ]
                );
            }
        }

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }

    public function toggle($id)
    {
        $country = Country::findOrFail($id);
        $country->update(['is_active' => !$country->is_active]);

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }
}
