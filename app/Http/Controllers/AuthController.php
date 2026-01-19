<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendeur;
use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function showRegister(Request $request)
    {
        if ($request->has('ref')) {
            session(['referred_by_code' => $request->query('ref')]);
        }
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $data = $request->only(['name', 'email', 'password', 'password_confirmation', 'telephone']);

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'telephone' => 'nullable|string|max:20',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Logic for referral
        $referredBy = null;
        $referredByCode = session('referred_by_code');
        if ($referredByCode) {
            $referrer = User::where('referral_code', '=', $referredByCode, 'and')->first();
            if ($referrer) {
                $referredBy = $referrer->id_user;
            }
        }

        // Generate a unique referral code for the new user
        $referralCode = strtoupper(\Illuminate\Support\Str::random(8));
        while (User::where('referral_code', '=', $referralCode, 'and')->exists()) {
            $referralCode = strtoupper(\Illuminate\Support\Str::random(8));
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'telephone' => $data['telephone'] ?? null,
            'role' => 'client',
            'referral_code' => $referralCode,
            'referred_by' => $referredBy,
            'referral_balance' => 0,
            'derniere_ip' => $request->ip(),
            'date_derniere_connexion' => now(),
            'status' => 'actif',
        ]);

        // Log registration as a successful login attempt
        LoginAttempt::create([
            'id_user' => $user->id_user,
            'email' => $user->email,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'success',
        ]);

        if ($referredBy) {
            session()->forget('referred_by_code');
        }

        Auth::login($user);

        return redirect('/');
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only(['email', 'password']);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            $user = Auth::user();

            // Update safety metadata
            $user->update([
                'date_derniere_connexion' => now(),
                'derniere_ip' => $request->ip(),
                'login_attempts' => 0, // Reset counter
            ]);

            // Log successful attempt
            LoginAttempt::create([
                'id_user' => $user->id_user,
                'email' => $user->email,
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'status' => 'success',
            ]);

            // Role-based redirection
            if (($user->role ?? null) === 'admin' || ($user->role ?? null) === 'super_admin') {
                return redirect()->route('admin.dashboard');
            }

            return redirect()->intended('/');
        }

        // Log failed attempt
        $user = User::where('email', '=', $credentials['email'], 'and')->first();

        if ($user) {
            $user->increment('login_attempts');
            $user->increment('failed_logins_count'); // If we have this field, if not it just won't work but won't crash usually if we handle it
            // Actually let's just stick to what we have in User model
        }

        LoginAttempt::create([
            'id_user' => $user ? $user->id_user : null,
            'email' => $credentials['email'],
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'failed',
            'failure_reason' => 'Identifiants invalides',
        ]);

        return back()->withErrors(['email' => 'Identifiants invalides'])->withInput();
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/');
    }

    public function dashboard()
    {
        $user = Auth::user();

        // Redirect based on role
        if ($user->role === 'admin' || $user->role === 'super_admin') {
            return redirect()->route('admin.dashboard');
        }

        if ($user->isVendor()) {
            // This named route 'vendeur.dashboard' already handles the slug redirect in web.php
            return redirect()->route('vendeur.dashboard');
        }

        return view('dashboard', compact('user'));
    }

    /**
     * Afficher le formulaire pour devenir vendeur.
     */
    public function showApply()
    {
        $categories = \App\Models\VendorCategory::where('is_active', '=', true, 'and')->get();
        return view('auth.vendor_apply', compact('categories'));
    }

    /**
     * Soumettre une demande pour devenir vendeur.
     */
    public function apply(Request $request)
    {
        $user = Auth::user();

        if ($user->role === 'vendeur') {
            return redirect()->route('home')->with('info', 'Vous êtes déjà un vendeur.');
        }

        $validator = Validator::make($request->all(), [
            'nom_commercial' => 'required|string|max:150',
            'id_category_vendeur' => 'required|exists:vendor_categories,id_category_vendeur',
            'telephone_commercial' => 'required|string|max:20',
            'registre_commerce' => 'required|string|max:100',
            'adresse_complete' => 'required|string|max:500',
            'document_identite' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'justificatif_domicile' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $data = [
            'id_user' => $user->id_user,
            'nom_commercial' => $request->nom_commercial,
            'id_category_vendeur' => $request->id_category_vendeur,
            'telephone_commercial' => $request->telephone_commercial,
            'registre_commerce' => $request->registre_commerce,
            'adresse_complete' => $request->adresse_complete,
            'statut_verification' => 'en_cours',
            'actif' => false,
        ];

        if ($request->hasFile('document_identite')) {
            $data['document_identite'] = $request->file('document_identite')->store('verification_docs', 'private');
        }

        if ($request->hasFile('justificatif_domicile')) {
            $data['justificatif_domicile'] = $request->file('justificatif_domicile')->store('verification_docs', 'private');
        }

        Vendeur::updateOrCreate(
            ['id_user' => $user->id_user],
            $data
        );

        return redirect()->route('home')->with('success', 'Votre demande pour devenir vendeur a été envoyée avec succès. Elle est en cours de révision par nos administrateurs.');
    }
}
