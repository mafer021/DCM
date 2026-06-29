<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | MOSTRAR LOGIN
    |--------------------------------------------------------------------------
    */

    public function showLogin()
    {
        return view('auth.login');
    }

    /*
    |--------------------------------------------------------------------------
    | INICIAR SESIÓN
    |--------------------------------------------------------------------------
    */

    public function login(Request $request)
    {
        // VALIDAR CAMPOS
        $request->validate([
            'usuario_login' => 'required',
            'password' => 'required'
        ]);

        // 1. COMPROBAR PRIMERO SI EL USUARIO ESTÁ INACTIVO
        // Buscamos al usuario en la base de datos por su login
        $usuario = \App\Models\User::where('usuario_login', $request->usuario_login)->first();

        // Si existe pero su estado es inactivo, lo frenamos aquí mismo con un mensaje claro
        if ($usuario && $usuario->estado === 'inactivo') {
            return back()->withErrors([
                'usuario_login' => 'Tu cuenta se encuentra inactiva. Contacta al administrador.'
            ])->onlyInput('usuario_login');
        }

        // 2. INTENTAR LOGIN NORMAL (Solo entran los activos)
        $credenciales = [
            'usuario_login' => $request->usuario_login,
            'password' => $request->password,
            'estado' => 'activo' // Doble capa de seguridad
        ];

        // RECORDAR SESIÓN
        $remember = $request->has('remember');

        // VALIDAR ACCESO (Aquí quitamos el .make())
        if(Auth::attempt($credenciales, $remember))
        {
            $request->session()->regenerate();

            return redirect('/dashboard');
        }
        // SI FALLA POR CREDENCIALES INCORRECTAS
        return back()->withErrors([
            'usuario_login' => 'Usuario o contraseña incorrectos'
        ])->onlyInput('usuario_login');
    }
    /*
    |--------------------------------------------------------------------------
    | CERRAR SESIÓN
    |--------------------------------------------------------------------------
    */

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}