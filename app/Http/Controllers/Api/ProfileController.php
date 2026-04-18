<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

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
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id_user, 'id_user')],
            'telephone' => ['nullable', 'string', 'max:20'],
            'photo_profil' => ['nullable', 'image', 'max:2048'], // 2MB Max
        ]);

        if ($request->hasFile('photo_profil')) {
            // Delete old photo if exists
            if ($user->photo_profil) {
                Storage::delete($user->photo_profil);
            }
            $path = $request->file('photo_profil')->store('avatars', 'public');
            $user->photo_profil = $path;
        }

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->telephone = $validated['telephone'];
        $user->save();

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
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

        return response()->json(["info" => "Action terminée (Ancien back).", "status" => "success"]);
    }
}
