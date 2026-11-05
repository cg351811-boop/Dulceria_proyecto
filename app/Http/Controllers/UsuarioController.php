<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    // Mostrar todos los usuarios
    public function index()
    {
        $usuarios = User::latest()->get();
        return view('usuarios.index', compact('usuarios'));
    }

    // Mostrar formulario de creación
    public function create()
    {
        return view('usuarios.create');
    }

    // Guardar nuevo usuario
    public function store(Request $request)
    {
        // Validaciones
        $request->validate([
            'name'     => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email'    => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ], [
            'name.required'     => 'El nombre es obligatorio',
            'name.regex'        => 'El nombre solo debe contener letras, no se permiten números',
            'email.required'    => 'El correo es obligatorio',
            'email.email'       => 'Ingresa un correo válido',
            'email.unique'      => 'Este correo ya está registrado',
            'password.required'  => 'La contraseña es obligatoria',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        // Crear usuario
        User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', '✅ Usuario creado correctamente');
    }

    // Mostrar un usuario
    public function show(User $usuario)
    {
        return view('usuarios.show', compact('usuario'));
    }

    // Mostrar formulario de edición
    public function edit(User $usuario)
    {
        return view('usuarios.edit', compact('usuario'));
    }

    // Actualizar usuario
    public function update(Request $request, User $usuario)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email'    => 'required|email|unique:users,email,' . $usuario->id,
            'password' => 'nullable|string|min:6|confirmed',
        ], [
            'name.required'     => 'El nombre es obligatorio',
            'name.regex'        => 'El nombre solo debe contener letras, no se permiten números',
            'email.required'    => 'El correo es obligatorio',
            'email.email'       => 'Ingresa un correo válido',
            'email.unique'      => 'Este correo ya está en uso',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres',
            'password.confirmed' => 'Las contraseñas no coinciden',
        ]);

        $datos = $request->only(['name', 'email']);
        
        if ($request->password) {
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')
            ->with('success', '✅ Usuario actualizado correctamente');
    }

    // Eliminar usuario
    public function destroy(User $usuario)
    {
        $usuario->delete();
        return redirect()->route('usuarios.index')
            ->with('success', '✅ Usuario eliminado correctamente');
    }
}