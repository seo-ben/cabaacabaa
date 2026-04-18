<?php

namespace App\Http\Controllers\Api\Vendor;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class TeamController extends Controller
{
    public function index(Request $request, $vendor_slug)
    {
        $vendor = $request->get('current_vendor');
        if (!$vendor)
            abort(404);

        $staffMembers = $vendor->staff()->with('user')->get();
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    public function create(Request $request, $vendor_slug)
    {
        $vendor = $request->get('current_vendor');
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    public function store(Request $request, $vendor_slug)
    {
        $vendor = $request->get('current_vendor');

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // Enforce unique email for simplicity
            'password' => 'required|string|min:8',
            'permissions' => 'nullable|array',
            'role_name' => 'required|string|max:50',
        ]);

        // Create User
        $user = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => 'client', // Role is client, but permissions come from VendorStaff
            'email_verified_at' => now(), // Auto verify for staff?
            'status' => 'actif',
        ]);

        // Generate unique access token
        $accessToken = bin2hex(random_bytes(32));

        // Create Staff Link
        $staff = \App\Models\VendorStaff::create([
            'id_vendeur' => $vendor->id_vendeur,
            'id_user' => $user->id_user,
            'role_name' => $validated['role_name'],
            'permissions' => $request->permissions ?? [], // Array of permissions
            'access_token' => $accessToken,
        ]);

        // Generate unique login URL
        $loginUrl = route('vendor.staff.login', ['vendor_slug' => $vendor->slug, 'token' => $accessToken]);

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }

    public function edit(Request $request, $vendor_slug, $id)
    {
        $vendor = $request->get('current_vendor');
        $staff = \App\Models\VendorStaff::where('id_vendeur', $vendor->id_vendeur)
            ->where('id', $id)
            ->with('user')
            ->firstOrFail();

        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    public function update(Request $request, $vendor_slug, $id)
    {
        $vendor = $request->get('current_vendor');
        $staff = \App\Models\VendorStaff::where('id_vendeur', $vendor->id_vendeur)
            ->where('id', $id)
            ->with('user')
            ->firstOrFail();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $staff->id_user . ',id_user',
            'password' => 'nullable|string|min:8',
            'permissions' => 'nullable|array',
            'role_name' => 'required|string|max:50',
        ]);

        // Update User
        $userUpdate = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if (!empty($validated['password'])) {
            $userUpdate['password'] = Hash::make($validated['password']);
        }

        $staff->user->update($userUpdate);

        // Update Staff Link
        $staff->update([
            'role_name' => $validated['role_name'],
            'permissions' => $request->permissions ?? [],
        ]);

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }

    public function destroy(Request $request, $vendor_slug, $id)
    {
        $vendor = $request->get('current_vendor');

        $staff = \App\Models\VendorStaff::where('id_vendeur', $vendor->id_vendeur)
            ->where('id', $id)
            ->firstOrFail();

        // Prevent deleting oneself if somehow possible (though staff listing usually viewed by owner)
        if ($staff->id_user === auth()->id()) {
            return response()->json(["info" => "Action terminée (Ancien back).", "status" => "success"]);
        }

        // Optionally delete the user account? No, just remove access.
        $staff->delete();

        return response()->json(["info" => "Action terminée (Ancien back).", "status" => "success"]);
    }
}
