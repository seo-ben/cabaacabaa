<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class PasswordResetController extends Controller
{
    /**
     * Show the forgot password form.
     */
    public function showForgotForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Send reset link to user's email.
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'L\'adresse email est requise.',
            'email.email'    => 'Veuillez entrer une adresse email valide.',
        ]);

        $user = User::where('email', $request->email)->first();

        // Always return success to prevent email enumeration
        if (!$user) {
            return back()->with('success', 'Si cet email existe dans notre système, vous recevrez un lien de réinitialisation sous peu.');
        }

        // Delete old tokens for this email
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        // Generate a secure token
        $token = Str::random(64);

        // Store hashed token
        DB::table('password_reset_tokens')->insert([
            'email'      => $request->email,
            'token'      => Hash::make($token),
            'created_at' => Carbon::now(),
        ]);

        // Build reset URL
        $resetUrl = url('/reset-password?token=' . $token . '&email=' . urlencode($request->email));

        // Send email
        try {
            Mail::send('emails.password-reset', [
                'user'     => $user,
                'resetUrl' => $resetUrl,
            ], function ($mail) use ($user) {
                $mail->to($user->email, $user->name)
                     ->subject('Réinitialisation de votre mot de passe — ' . config('app.name'));
            });
        } catch (\Exception $e) {
            // Log the error but don't expose it to user
            \Log::error('Password reset email failed: ' . $e->getMessage());
            return back()->withErrors(['email' => 'Impossible d\'envoyer l\'email. Veuillez réessayer plus tard.']);
        }

        return back()->with('success', 'Un lien de réinitialisation a été envoyé à ' . $request->email . '. Vérifiez aussi vos spams.');
    }

    /**
     * Show the password reset form.
     */
    public function showResetForm(Request $request)
    {
        $token = $request->query('token');
        $email = $request->query('email');

        if (!$token || !$email) {
            abort(404);
        }

        return view('auth.reset-password', compact('token', 'email'));
    }

    /**
     * Reset the password.
     */
    public function resetPassword(Request $request)
    {
        $request->validate([
            'token'                 => ['required'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'confirmed', 'min:8'],
            'password_confirmation' => ['required'],
        ], [
            'password.min'              => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed'        => 'Les deux mots de passe ne correspondent pas.',
            'password_confirmation.required' => 'Veuillez confirmer votre mot de passe.',
        ]);

        // Find the token record
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$record) {
            return back()->withErrors(['token' => 'Ce lien de réinitialisation est invalide ou a expiré.']);
        }

        // Check token expiry (60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            DB::table('password_reset_tokens')->where('email', $request->email)->delete();
            return back()->withErrors(['token' => 'Ce lien a expiré. Veuillez faire une nouvelle demande.']);
        }

        // Verify token
        if (!Hash::check($request->token, $record->token)) {
            return back()->withErrors(['token' => 'Ce lien de réinitialisation est invalide.']);
        }

        // Find user and update password
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withErrors(['email' => 'Utilisateur introuvable.']);
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Delete the token
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();

        return redirect()->route('login')->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }
}
