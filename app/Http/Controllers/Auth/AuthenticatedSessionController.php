<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): RedirectResponse
    {
        return redirect('/?auth=login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        $role = auth()->user()->role?->name;

        return match($role) {
            'Super Admin' => redirect()->intended(route('staf.dashboard')),
            'Manager'     => redirect()->intended(route('staf.dashboard')),
            'Admin'       => redirect()->intended(route('staf.dashboard')),
            'Design'      => redirect()->intended(route('staf.dashboard')),
            'Produksi'    => redirect()->intended(route('staf.dashboard')),
            default       => redirect()->intended(route('beranda')),
        };
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect()->route('beranda');
    }
}
