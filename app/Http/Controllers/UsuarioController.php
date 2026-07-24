<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    public function index()
{
    $usuarios = User::all();
    
    // Ahora solo contamos a los administradores que estén ACTIVOS
    $totalAdmins = User::where('rol', 'admin')
                       ->where('estado', 'activo')
                       ->count();

    return view('usuarios.index', compact('usuarios', 'totalAdmins'));
}

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'max:100'],
            'apellido_paterno' => ['required', 'max:100'],
            'apellido_materno' => ['nullable', 'max:100'],
            'usuario_login' => ['required', 'max:50', 'unique:users,usuario_login'],
            'email' => [
                'required',
                'email',
                'unique:users,email',
                function ($attribute, $value, $fail) {
                    $typos = ['gamil.com', 'gamil.co', 'hotmial.com', 'outlok.com', 'hotmial.es'];
                    foreach ($typos as $typo) {
                        if (str_ends_with(strtolower($value), $typo)) {
                            $fail('Parece que escribiste mal el dominio del correo.');
                        }
                    }
                }
            ],
            'password' => ['required', 'size:8', 'regex:/^(?=.*[a-zA-Z])(?=.*[A-Z])(?=.*\d).{8}$/'],
            'rol' => ['required', Rule::in(['admin', 'empleado'])],
        ], [
            'password.size' => 'La contraseña debe tener exactamente 8 caracteres.',
            'password.regex' => 'La contraseña debe tener al menos una mayúscula, letras y números.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with('modal', 'nuevo');
        }

        if ($request->rol === 'admin') {
            // CORRECCIÓN: Contar solo administradores activos al registrar uno nuevo
            $totalAdmins = User::where('rol', 'admin')
                               ->where('estado', 'activo')
                               ->count();

            if ($totalAdmins >= 3) {
                return back()
                    ->withErrors(['rol' => 'Solo se permiten 3 administradores.'])
                    ->withInput()
                    ->with('modal', 'nuevo');
            }
        }

        User::create([
            'nombre' => $request->nombre,
            'apellido_paterno' => $request->apellido_paterno,
            'apellido_materno' => $request->apellido_materno,
            'usuario_login' => $request->usuario_login,
            'email' => $request->email,
            'password' => $request->password,
            'rol' => $request->rol,
            'estado' => 'activo',
        ]);

        return redirect('/usuarios')->with('success', 'Usuario ' . $request->usuario_login . ' registrado correctamente.');
    }

    public function update(Request $request, $id)
    {
        $usuario = User::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'nombre' => ['required', 'max:100'],
            'apellido_paterno' => ['required', 'max:100'],
            'apellido_materno' => ['nullable', 'max:100'],
            'usuario_login' => ['required', 'max:50', Rule::unique('users', 'usuario_login')->ignore($usuario->id)],
            'email' => [
                'required',
                'email',
                Rule::unique('users', 'email')->ignore($usuario->id),
                function ($attribute, $value, $fail) {
                    $typos = ['gamil.com', 'gamil.co', 'hotmial.com', 'outlok.com', 'hotmial.es'];
                    foreach ($typos as $typo) {
                        if (str_ends_with(strtolower($value), $typo)) {
                            $fail('Parece que escribiste mal el dominio del correo.');
                        }
                    }
                }
            ],
            'password' => ['nullable', 'size:8', 'regex:/^(?=.*[a-zA-Z])(?=.*[A-Z])(?=.*\d).{8}$/'],
            'rol' => ['required', Rule::in(['admin', 'empleado'])],
        ], [
            'password.size' => 'La contraseña debe tener exactamente 8 caracteres.',
            'password.regex' => 'La contraseña debe tener al menos una mayúscula, letras y números.',
        ]);

        if ($validator->fails()) {
            return back()
                ->withErrors($validator)
                ->withInput()
                ->with([
                    'modal' => 'editar',
                    'usuario_id' => $id,
                ]);
        }

        if ($request->rol === 'admin' && $usuario->rol !== 'admin') {
            // CORRECCIÓN: Contar solo administradores que estén activos al cambiar el rol en edición
            $totalAdmins = User::where('rol', 'admin')
                               ->where('estado', 'activo')
                               ->count();
            if ($totalAdmins >= 3) {
                return back()
                    ->withErrors(['rol' => 'Solo se permiten 3 administradores.'])
                    ->withInput()
                    ->with([
                        'modal' => 'editar',
                        'usuario_id' => $id,
                    ]);
            }
        }

        $usuario->nombre = $request->nombre;
        $usuario->apellido_paterno = $request->apellido_paterno;
        $usuario->apellido_materno = $request->apellido_materno;
        $usuario->usuario_login = $request->usuario_login;
        $usuario->email = $request->email;
        $usuario->rol = $request->rol;

        if ($request->filled('password')) {
            $usuario->password = $request->password;
        }

        $usuario->save();

        return redirect('/usuarios')->with('success', 'Usuario actualizado correctamente.');
    }

    public function cambiarEstado($id)
{
    // 1. Buscamos al usuario en la base de datos, si no existe arroja un error 404
    $usuario = User::findOrFail($id);

    // 2. SEGURIDAD: Evitar que el administrador que está logueado se desactive a sí mismo
    if (auth()->id() == $usuario->id) {
        return back()->with('error', 'No puedes desactivar tu propia cuenta.');
    }

    // 3. SEGURIDAD: Evitar desactivar al usuario principal del seeder (ID 1)
    if ($usuario->id == 1) {
        return back()->with('error', 'Por seguridad, el usuario administrador principal del sistema no se puede desactivar.');
    }

    // 4. INTERRUPTOR (Switch): Si está activo pasa a inactivo, y viceversa
    if ($usuario->estado === 'activo') {
        $usuario->estado = 'inactivo';
    } else {
        $usuario->estado = 'activo';
    }

    // 5. Guardamos el cambio en la base de datos
    $usuario->save();

    // 6. Creamos un mensaje dinámico personalizado con el nombre de usuario
    $mensaje = $usuario->estado === 'activo' 
        ? 'Usuario ' . $usuario->usuario_login . ' activado correctamente.' 
        : 'Usuario ' . $usuario->usuario_login . ' ha sido suspendido.';
    
    // 6. Redireccionamos atrás con el mensaje de éxito
    return back()->with('success', $mensaje);
}
}