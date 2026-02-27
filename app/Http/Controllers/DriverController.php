<?php

namespace App\Http\Controllers;

use App\Events\DriverLocationUpdated;
use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DriverController extends Controller
{
    private function formatDriverName($user)
    {
        if (!$user) return 'Chauffeur';
        if (!Auth::check() || !Auth::user()->isAdmin()) {
            return $user->short_name;
        }
        return $user->nom_complet ?? $user->name ?? 'Chauffeur';
    }

    public function index()
    {
        return view('map.index');
    }

    public function updateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        $user = Auth::user();

        // Ensure user has a driver profile
        // Assuming relationship 'driver' exists on User
        $driver = $user->driver;
        
        if (!$driver) {
             // Create one if it doesn't exist
             $driver = Driver::create(['user_id' => $user->id_user]);
        }

        $driver->update([
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'is_online' => true,
            'last_seen_at' => now(),
        ]);

        broadcast(new DriverLocationUpdated(
            $driver->id,
            $request->latitude,
            $request->longitude,
            $this->formatDriverName($user)
        ));

        return response()->json(['success' => true]);
    }

    public function getOnlineDrivers()
    {
        $drivers = Driver::where('is_online', true)
            ->where('last_seen_at', '>=', now()->subMinutes(10)) // Consider offline if no update in 10 mins
            ->with('user:id_user,nom_complet,name')
            ->get()
            ->map(function ($driver) {
                return [
                    'driverId' => $driver->id,
                    'latitude' => $driver->latitude,
                    'longitude' => $driver->longitude,
                    'driverName' => $this->formatDriverName($driver->user),
                ];
            });

        return response()->json($drivers);
    }

    public function getActiveVendors()
    {
        $vendors = \App\Models\Vendeur::where('actif', true)
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get()
            ->map(function ($vendor) {
                return [
                    'vendorId' => $vendor->id_vendeur,
                    'name' => $vendor->nom_commercial,
                    'latitude' => $vendor->latitude,
                    'longitude' => $vendor->longitude,
                    'image' => $vendor->image_principale ? asset('storage/' . $vendor->image_principale) : null,
                    'url' => route('vendor.show', ['id' => $vendor->id_vendeur, 'slug' => $vendor->slug]),
                ];
            });

        return response()->json($vendors);
    }
}
