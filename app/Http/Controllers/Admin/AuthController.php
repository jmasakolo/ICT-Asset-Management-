<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Login itself now happens through the single unified form —
// App\Http\Controllers\AuthController::login tries this guard before the
// regular `web` one. Only logout is guard-specific enough to stay here.
class AuthController extends Controller
{
    public function logout(Request $request): RedirectResponse
    {
        Auth::guard('admin')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}
