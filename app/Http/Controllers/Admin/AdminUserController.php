<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        // Show all admins and super admins
        $admins = \App\Models\User::whereIn('role', ['admin', 'super_admin'], 'and', false)->paginate(10);
        return view('admin.admins.index', compact('admins'));
    }

    public function create()
    {
        $permissions = \App\Models\Permission::all()->groupBy('group');
        return view('admin.admins.create', compact('permissions'));
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

        return redirect()->route('admin.admins.index')->with('success', 'Administrateur créé avec succès.');
    }

    public function edit($id)
    {
        $admin = \App\Models\User::whereIn('role', ['admin', 'super_admin'], 'and', false)->findOrFail($id);

        // Prevent editing Super Admin if not Super Admin
        if ($admin->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.admins.index')->with('error', 'Vous ne pouvez pas modifier un Super Admin.');
        }

        $permissions = \App\Models\Permission::all()->groupBy('group');

        return view('admin.admins.edit', compact('admin', 'permissions'));
    }

    public function update(Request $request, $id)
    {
        $admin = \App\Models\User::whereIn('role', ['admin', 'super_admin'], 'and', false)->findOrFail($id);

        if ($admin->isSuperAdmin() && !auth()->user()->isSuperAdmin()) {
            return redirect()->route('admin.admins.index')->with('error', 'Vous ne pouvez pas modifier un Super Admin.');
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

        return redirect()->route('admin.admins.index')->with('success', 'Administrateur mis à jour avec succès.');
    }

    public function destroy($id)
    {
        $admin = \App\Models\User::whereIn('role', ['admin', 'super_admin'], 'and', false)->findOrFail($id);

        if ($admin->isSuperAdmin()) {
            return back()->with('error', 'Impossible de supprimer un Super Admin.');
        }

        if ($admin->id_user === auth()->id()) {
            return back()->with('error', 'Vous ne pouvez pas vous supprimer vous-même.');
        }

        $admin->delete();
        return back()->with('success', 'Administrateur supprimé avec succès.');
    }
}
