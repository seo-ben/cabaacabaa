<?php

namespace App\Http\Controllers\Vendor;

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

        return view('vendor.staff.login', compact('vendor', 'token'));
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
            return back()->withErrors(['token' => 'Lien d\'accès invalide ou expiré.']);
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
            return back()->withErrors(['email' => 'Email incorrect.']);
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
            return redirect()->route('vendeur.slug.dashboard', ['vendor_slug' => $vendor->slug]);
        }

        LoginAttempt::create([
            'id_user' => $staff->id_user,
            'email' => $validated['email'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'failed',
            'failure_reason' => 'Mot de passe incorrect (Staff Login)',
        ]);

        return back()->withErrors(['password' => 'Mot de passe incorrect.']);
    }
}
