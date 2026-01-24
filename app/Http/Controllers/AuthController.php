<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Vendeur;
use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    public function showRegister(Request $request)
    {
        if ($request->has('ref')) {
            session(['referred_by_code' => $request->query('ref')]);
        }
        $countries = \App\Models\Country::where('is_active', '=', true, 'and')->get();
        return view('auth.register', compact('countries'));
    }

    public function register(Request $request)
    {
        $data = $request->only(['name', 'email', 'password', 'password_confirmation', 'telephone', 'phone_prefix']);

        $validator = Validator::make($data, [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
            'telephone' => 'required|string|max:20',
            'phone_prefix' => 'required|string|max:10',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $fullPhone = $data['phone_prefix'] . ' ' . preg_replace('/[^0-9]/', '', $data['telephone']);

        if (User::withTrashed()->where('telephone', $fullPhone)->exists()) {
            return redirect()->back()->withErrors(['telephone' => 'Ce numéro de téléphone est déjà utilisé.'])->withInput();
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
            'telephone' => $fullPhone,
            'role' => 'client',
            'referral_code' => $referralCode,
            'referred_by' => $referredBy,
            'referral_balance' => 0,
            'derniere_ip' => $request->ip(),
            'date_derniere_connexion' => now(),
            'status' => 'actif',
        ]);

        // Notify Admins of new registration
        $admins = User::whereIn('role', ['admin', 'super_admin'], 'and', false)->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'id_utilisateur' => $admin->id_user,
                'type_notification' => 'nouvel_utilisateur',
                'titre' => 'Nouvel utilisateur inscrit',
                'message' => "Un nouvel utilisateur, {$user->name}, vient de s'inscrire.",
                'lue' => false,
                'date_creation' => now(),
            ]);
        }

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
        $login = $request->input('login');
        $password = $request->input('password');

        // SECURITY FIX: Rate Limiting to prevent brute force
        $throttleKey = Str::lower($login) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);
            return back()->withErrors(['login' => "Trop de tentatives de connexion. Veuillez réessayer dans {$seconds} secondes."])->withInput();
        }

        // Determine if login is email or phone
        $field = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'telephone';

        $credentials = [
            $field => $login,
            'password' => $password
        ];

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            RateLimiter::clear($throttleKey);
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

            // Prevent redirecting to API routes (like background notification polling)
            $intendedUrl = session('url.intended');
            if ($intendedUrl && (str_contains($intendedUrl, '/api/') || str_contains($intendedUrl, '/notifications/'))) {
                session()->forget('url.intended');
                return redirect('/');
            }

            return redirect()->intended('/');
        }

        // Log failed attempt
        RateLimiter::hit($throttleKey);
        $user = User::where($field, '=', $login, 'and')->first();

        LoginAttempt::create([
            'id_user' => $user ? $user->id_user : null,
            'email' => $field === 'email' ? $login : ($user->email ?? $login),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'status' => 'failed',
            'failure_reason' => 'Identifiants invalides',
        ]);

        return back()->withErrors(['login' => 'Identifiants invalides'])->withInput();
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
        $countries = \App\Models\Country::where('is_active', '=', true, 'and')->get();
        return view('auth.vendor_apply', compact('categories', 'countries'));
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
            'telephone_commercial' => 'nullable|string|max:20',
            'phone_prefix' => 'nullable|string|max:10',
            'registre_commerce' => 'required|string|max:100',
            'adresse_complete' => 'required|string|max:500',
            'document_identite' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
            'justificatif_domicile' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:5120',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $fullPhone = null;
        if ($request->telephone_commercial) {
            $fullPhone = ($request->phone_prefix ?? '') . ' ' . $request->telephone_commercial;
        }

        $data = [
            'id_user' => $user->id_user,
            'nom_commercial' => $request->nom_commercial,
            'id_category_vendeur' => $request->id_category_vendeur,
            'telephone_commercial' => $fullPhone,
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

        $vendeur = Vendeur::updateOrCreate(
            ['id_user' => $user->id_user],
            $data
        );

        // Notify Admins of new application
        $admins = User::whereIn('role', ['admin', 'super_admin'], 'and', false)->get();
        foreach ($admins as $admin) {
            \App\Models\Notification::create([
                'id_utilisateur' => $admin->id_user,
                'type_notification' => 'demande_vendeur',
                'titre' => 'Nouvelle demande de vendeur',
                'message' => "L'utilisateur {$user->name} a déposé une demande pour la boutique \"{$vendeur->nom_commercial}\".",
                'id_vendeur' => $vendeur->id_vendeur,
                'lue' => false,
                'date_creation' => now(),
            ]);
        }

        return redirect()->route('home')->with('success', 'Votre demande pour devenir vendeur a été envoyée avec succès. Elle est en cours de révision par nos administrateurs.');
    }
}
