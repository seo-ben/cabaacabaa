<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LoginAttempt;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class SecurityController extends Controller
{
    /**
     * Display a listing of login attempts.
     */
    public function index(Request $request)
    {
        if (!Gate::allows('view_security')) {
            abort(403);
        }

        $query = LoginAttempt::with('user')->orderBy('attempted_at', 'desc');

        // Simple filtering
        if ($request->has('status') && $request->status !== '') {
            $query->where('status', $request->status);
        }

        if ($request->has('email') && $request->email !== '') {
            $query->where('email', 'like', '%' . $request->email . '%');
        }

        $attempts = $query->paginate(50);

        return view('admin.security.index', compact('attempts'));
    }

    /**
     * Display security details for a specific user.
     */
    public function userSecurity($id)
    {
        if (!Gate::allows('view_security')) {
            abort(403);
        }

        $user = \App\Models\User::findOrFail($id);
        $attempts = LoginAttempt::where('id_user', $id)
            ->orWhere('email', $user->email)
            ->orderBy('attempted_at', 'desc')
            ->paginate(30);

        return view('admin.security.user', compact('user', 'attempts'));
    }
}
