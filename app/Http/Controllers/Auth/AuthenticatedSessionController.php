<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();
    
        $request->session()->regenerate();
    
        $user = $request->user();
    
        // ✅ Simpan pesan selamat datang ke session
        session()->flash('welcome_message', 'Halo, selamat datang kembali, ' . $user->name);
    
        if ($user->role === 'admin') {
            return redirect()->intended('/admin');
        }
    
        if ($user->role === 'cashier') {
            return redirect()->intended('/dashboard')->with('welcome_message', 'Halo, selamat datang kembali ' . $user->name);
        }
    
        return redirect('/');
    }
    
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
