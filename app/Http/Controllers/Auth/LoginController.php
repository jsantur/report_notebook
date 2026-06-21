<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Muestra el formulario de login.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Procesa el inicio de sesión.
     */
    public function login(Request $request)
    {
        $request->validate([
            'usuario' => 'required|string',
            'password' => 'required|string',
        ]);

        \Log::info('Login attempt', ['usuario' => $request->usuario]);

        // 1. Intentar con username (Recomendado)
        if (Auth::attempt(['username' => $request->usuario, 'password' => $request->password, 'activo' => true], $request->boolean('remember'))) {
            \Log::info('Login successful via username', ['user_id' => Auth::id()]);
            Auth::user()->update(['last_login_at' => now()]);
            $request->session()->regenerate();
            \Log::info('Redirecting to dashboard');
            return redirect()->intended('/dashboard');
        }
        \Log::debug('Username attempt failed');

        // 2. Intentar con email
        if (Auth::attempt(['email' => $request->usuario, 'password' => $request->password, 'activo' => true], $request->boolean('remember'))) {
            \Log::info('Login successful via email', ['user_id' => Auth::id()]);
            Auth::user()->update(['last_login_at' => now()]);
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }
        \Log::debug('Email attempt failed');

        // 3. Intentar con name (Legacy)
        if (Auth::attempt(['name' => $request->usuario, 'password' => $request->password, 'activo' => true], $request->boolean('remember'))) {
            \Log::info('Login successful via name', ['user_id' => Auth::id()]);
            Auth::user()->update(['last_login_at' => now()]);
            $request->session()->regenerate();
            return redirect()->intended('/dashboard');
        }
        \Log::debug('Name attempt failed');

        // Verificar si el usuario existe pero está suspendido
        $user = \App\Models\User::where('username', $request->usuario)
            ->orWhere('email', $request->usuario)
            ->orWhere('name', $request->usuario)
            ->first();

        if ($user && !$user->activo) {
            \Log::warning('Login failed - user suspended', ['user_id' => $user->id]);
            throw ValidationException::withMessages([
                'usuario' => 'Su cuenta ha sido suspendida. Contacte al administrador.',
            ]);
        }

        \Log::warning('Login failed - invalid credentials', ['usuario' => $request->usuario]);
        throw ValidationException::withMessages([
            'usuario' => __('auth.failed'),
        ]);
    }

    /**
     * Cierra la sesión.
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    }
}
