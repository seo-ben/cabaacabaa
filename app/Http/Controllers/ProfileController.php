<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit()
    {
        return view('profile.edit', [
            'user' => Auth::user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
            'telephone' => ['nullable', 'string', 'max:20'],
            'photo_profil' => ['nullable', 'image', 'max:2048', 'mimes:jpg,jpeg,png,gif'], // 2MB Max
        ]);

        if ($request->hasFile('photo_profil')) {
            // Delete old photo if exists in public storage
            if ($user->photo_profil) {
                Storage::disk('public')->delete($user->photo_profil);
            }
            $path = $request->file('photo_profil')->store('avatars', 'public');
            $user->photo_profil = $path;
            $user->save();
            
            // If it was just a photo update, we can return early or continue
            if (!$request->has('name') && !$request->has('email')) {
                return redirect()->route('profile.edit')->with('success', 'Photo de profil mise à jour.');
            }
        }

        if ($request->has('name')) $user->name = $validated['name'];
        if ($request->has('email')) $user->email = $validated['email'];
        if ($request->has('telephone')) $user->telephone = $validated['telephone'];
        $user->save();

        return redirect()->route('profile.edit')->with('success', 'Profil mis à jour avec succès.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ]);

        $request->user()->update([
            'password' => Hash::make($validated['password']),
        ]);

        return back()->with('success', 'Mot de passe modifié avec succès.');
    }
}
