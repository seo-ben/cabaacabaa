<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\VendorStaff;
use App\Models\Vendeur;
use App\Models\LoginAttempt;
use App\Models\User;

class StaffAuthController extends Controller
{
    /**
     * Show staff login form
     */
    public function showLogin(Request $request, $vendor_slug)
    {
        $vendor = Vendeur::where('slug', '=', $vendor_slug, 'and')->firstOrFail();
        $token = $request->query('token');

        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    /**
     * Handle staff login with token
     */
    public function login(Request $request, $vendor_slug)
    {
        $vendor = Vendeur::where('slug', '=', $vendor_slug, 'and')->firstOrFail();

        $validated = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'token' => 'required|string',
        ]);

        // Find staff member by token and vendor
        $staff = VendorStaff::where('access_token', '=', $validated['token'], 'and')
            ->where('id_vendeur', '=', $vendor->id_vendeur, 'and')
            ->with('user')
            ->first();

        if (!$staff) {
            LoginAttempt::create([
                'email' => $validated['email'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'failed',
                'failure_reason' => 'Token invalide ou boutique incorrecte',
            ]);
            return response()->json(["info" => "Action terminée (Ancien back).", "status" => "success"]);
        }

        // Verify email matches
        if ($staff->user->email !== $validated['email']) {
            LoginAttempt::create([
                'id_user' => $staff->id_user,
                'email' => $validated['email'],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'failed',
                'failure_reason' => 'Email ne correspond pas au token',
            ]);
            return response()->json(["info" => "Action terminée (Ancien back).", "status" => "success"]);
        }

        // Attempt authentication
        if (Auth::attempt(['email' => $validated['email'], 'password' => $validated['password']])) {
            $request->session()->regenerate();

            $user = Auth::user();
            $user->update([
                'date_derniere_connexion' => now(),
                'derniere_ip' => $request->ip(),
                'login_attempts' => 0,
            ]);

            LoginAttempt::create([
                'id_user' => $user->id_user,
                'email' => $user->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success',
            ]);

            // Redirect to vendor dashboard
            return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
        }

        LoginAttempt::create([
            'id_user' => $staff->id_user,
            'email' => $validated['email'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'failed',
            'failure_reason' => 'Mot de passe incorrect (Staff Login)',
        ]);

        return response()->json(["info" => "Action terminée (Ancien back).", "status" => "success"]);
    }
}
