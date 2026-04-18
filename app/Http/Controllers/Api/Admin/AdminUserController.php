<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        // Show all admins and super admins
        $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'], 'and', false)->paginate(10);
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    public function create()
    {
        $permissions = \App\Models\Permission::all()->groupBy('group');
        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $admin = \App\Models\User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => \Illuminate\Support\Facades\Hash::make($validated['password']),
            'role' => 'admin',
            'email_verified_at' => now(),
            'status' => 'actif',
        ]);

        if (isset($validated['permissions'])) {
            $admin->permissions()->sync($validated['permissions']);
        }

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }

    public function edit($id)
    {
        $admin = \App\Models\User::whereIn('role', ['admin', 'super_admin'], 'and', false)->findOrFail($id);

        // Prevent editing Super Admin if not Super Admin
        if ($admin->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
        }

        $permissions = \App\Models\Permission::all()->groupBy('group');

        return response()->json(["info" => "Point de terminaison API généré... Il faut y injecter vos variables de vue."]);
    }

    public function update(Request $request, $id)
    {
        $admin = \App\Models\User::whereIn('role', ['admin', 'super_admin'], 'and', false)->findOrFail($id);

        if ($admin->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $id . ',id_user',
            'password' => 'nullable|string|min:8|confirmed',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
        ]);

        $data = [
            'name' => $validated['name'],
            'email' => $validated['email'],
        ];

        if ($request->filled('password')) {
            $data['password'] = \Illuminate\Support\Facades\Hash::make($validated['password']);
        }

        $admin->update($data);

        // Sync permissions
        if (isset($validated['permissions'])) {
            $admin->permissions()->sync($validated['permissions']);
        } else {
            $admin->permissions()->detach();
        }

        return response()->json(["info" => "Action terminée (Ancien redirect).", "status" => "success"]);
    }

    public function destroy($id)
    {
        $admin = \App\Models\User::whereIn('role', ['admin', 'super_admin'], 'and', false)->findOrFail($id);

        if ($admin->isSuperAdmin()) {
            return response()->json(["info" => "Action terminée (Ancien back).", "status" => "success"]);
        }

        if ($admin->id_user === auth()->id()) {
            return response()->json(["info" => "Action terminée (Ancien back).", "status" => "success"]);
        }

        $admin->delete();
        return response()->json(["info" => "Action terminée (Ancien back).", "status" => "success"]);
    }
}
