<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password as PasswordRule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetController extends Controller
{
    public function showLinkRequestForm(): View
    {
        return view('auth.forgot-password');
    }

    public function sendResetLinkEmail(Request $request): RedirectResponse
    {
        $request->validate(['email' => ['required', 'email']]);

        $email = Str::lower($request->input('email'));

        // Same dual-account-type pattern as login: try the admin broker
        // first, then the regular user broker. The response is identical
        // either way so this endpoint can't be used to enumerate accounts.
        $status = Password::broker('admins')->sendResetLink(['email' => $email]);

        if ($status !== Password::RESET_LINK_SENT) {
            Password::broker('users')->sendResetLink(['email' => $email]);
        }

        return back()->with('status', 'If an account exists for that email, a password reset link has been sent.');
    }

    public function showResetForm(Request $request, string $token): View
    {
        return view('auth.reset-password', [
            'token' => $token,
            'email' => $request->query('email'),
        ]);
    }

    public function reset(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', PasswordRule::defaults()],
        ]);

        $credentials['email'] = Str::lower($credentials['email']);

        $resetter = function ($user, string $password): void {
            $user->forceFill(['password' => Hash::make($password)])->save();
        };

        $status = Password::broker('admins')->reset($credentials, $resetter);

        if ($status !== Password::PASSWORD_RESET) {
            $status = Password::broker('users')->reset($credentials, $resetter);
        }

        if ($status === Password::PASSWORD_RESET) {
            return redirect()->route('login')->with('status', 'Your password has been reset. You can now log in.');
        }

        throw ValidationException::withMessages([
            'email' => [__($status)],
        ]);
    }
}
