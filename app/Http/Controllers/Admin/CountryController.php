<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $allCountries = config('countries');
        $activeCountries = Country::where('is_active', '=', true, 'and')->pluck('phone_prefix')->toArray();

        return view('admin.countries.index', compact('allCountries', 'activeCountries'));
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

        return redirect()->route('admin.countries.index')->with('success', 'Configuration des pays mise à jour avec succès.');
    }

    public function toggle($id)
    {
        $country = Country::findOrFail($id);
        $country->update(['is_active' => !$country->is_active]);

        return redirect()->back()->with('success', 'Statut du pays mis à jour.');
    }
}
