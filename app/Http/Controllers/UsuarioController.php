<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UsuarioController extends Controller
{
    /**
     * Valida que el usuario tenga privilegios para gestionar usuarios (Admin o Principal).
     * Los usuarios comunes solo pueden editar su propio perfil.
     */
    private function autorizarGestionUsuarios()
    {
        $currentUser = Auth::user();
        if (! $currentUser || $currentUser->isComun()) {
            abort(redirect()->route('profile.edit')->with(
                'error',
                '⛔ Acceso restringido: Como usuario común solo tienes permiso para modificar tu propia información de perfil.'
            ));
        }
    }

    /**
     * Mostrar todos los usuarios.
     */
    public function index()
    {
        $this->autorizarGestionUsuarios();
        $usuarios = User::orderBy('id', 'asc')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    /**
     * Mostrar formulario de creación.
     */
    public function create()
    {
        $this->autorizarGestionUsuarios();
        return view('usuarios.create');
    }

    /**
     * Guardar nuevo usuario.
     */
    public function store(Request $request)
    {
        $this->autorizarGestionUsuarios();

        // Validaciones estrictas con mensajes detallados
        $request->validate([
            'name'     => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', 'in:usuario,admin,principal'],
        ], [
            'name.required'      => 'El nombre del usuario es obligatorio.',
            'name.regex'         => 'El nombre solo debe contener letras, no se permiten números ni caracteres especiales.',
            'name.max'           => 'El nombre no puede tener más de 255 caracteres.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'Ingresa un formato de correo electrónico válido.',
            'email.unique'       => 'Este correo electrónico ya está registrado en el sistema.',
            'password.required'  => 'La contraseña es obligatoria.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role.required'      => 'Debes asignar un rol al usuario.',
            'role.in'            => 'El rol seleccionado no es válido.',
        ]);

        // REGLA: Solo el Usuario Principal puede otorgar rol de 'admin' o 'principal'
        if (in_array($request->role, ['admin', 'principal']) && ! Auth::user()->isPrincipal()) {
            return back()->withInput()->with(
                'error',
                '⛔ Acceso denegado: Solo el Usuario Principal tiene autorización para asignar el rol de Administrador.'
            );
        }

        User::create([
            'name'     => trim($request->name),
            'email'    => trim($request->email),
            'password' => Hash::make($request->password),
            'role'     => $request->role,
        ]);

        return redirect()->route('usuarios.index')
            ->with('success', '✅ Usuario creado y registrado correctamente.');
    }

    /**
     * Mostrar detalles de un usuario.
     */
    public function show(User $usuario)
    {
        $this->autorizarGestionUsuarios();
        return view('usuarios.show', compact('usuario'));
    }

    /**
     * Mostrar formulario de edición.
     */
    public function edit(User $usuario)
    {
        $this->autorizarGestionUsuarios();
        return view('usuarios.edit', compact('usuario'));
    }

    /**
     * Actualizar usuario.
     */
    public function update(Request $request, User $usuario)
    {
        $this->autorizarGestionUsuarios();

        $request->validate([
            'name'     => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email'    => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $usuario->id],
            'password' => ['nullable', 'string', 'min:6', 'confirmed'],
            'role'     => ['required', 'in:usuario,admin,principal'],
        ], [
            'name.required'      => 'El nombre del usuario es obligatorio.',
            'name.regex'         => 'El nombre solo debe contener letras, no se permiten números ni caracteres especiales.',
            'name.max'           => 'El nombre no puede tener más de 255 caracteres.',
            'email.required'     => 'El correo electrónico es obligatorio.',
            'email.email'        => 'Ingresa un formato de correo electrónico válido.',
            'email.unique'       => 'Este correo electrónico ya está en uso por otro usuario.',
            'password.min'       => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
            'role.required'      => 'Debes asignar un rol al usuario.',
            'role.in'            => 'El rol seleccionado no es válido.',
        ]);

        // REGLA: Solo el Usuario Principal puede otorgar o cambiar el rol de Administrador
        if ($usuario->role !== $request->role && ! Auth::user()->isPrincipal()) {
            return back()->withInput()->with(
                'error',
                '⛔ Acceso denegado: Solo el Usuario Principal tiene autorización para cambiar o asignar roles de Administrador.'
            );
        }

        $datos = [
            'name'  => trim($request->name),
            'email' => trim($request->email),
            'role'  => Auth::user()->isPrincipal() ? $request->role : $usuario->role,
        ];
        
        if ($request->filled('password')) {
            $datos['password'] = Hash::make($request->password);
        }

        $usuario->update($datos);

        return redirect()->route('usuarios.index')
            ->with('success', '✅ Usuario actualizado correctamente.');
    }

    /**
     * Eliminar usuario.
     * El Administrador puede eliminar usuarios comunes.
     * Solo el Usuario Principal puede gestionar administradores.
     */
    public function destroy(User $usuario)
    {
        $this->autorizarGestionUsuarios();
        $currentUser = Auth::user();

        // No permitir auto-eliminación
        if ($currentUser->id === $usuario->id) {
            return redirect()->route('usuarios.index')
                ->with('error', '⚠️ No puedes eliminar tu propia cuenta.');
        }

        // Nadie puede eliminar al Usuario Principal
        if ($usuario->isPrincipal()) {
            return redirect()->route('usuarios.index')
                ->with('error', '⛔ No está permitido eliminar la cuenta del Usuario Principal del sistema.');
        }

        // Si el usuario autenticado es Administrador (no Principal), solo puede eliminar usuarios comunes
        if ($currentUser->role === 'admin' && ! $usuario->isComun()) {
            return redirect()->route('usuarios.index')
                ->with('error', '⛔ Como administrador solo puedes eliminar usuarios comunes, no a otros administradores.');
        }

        $usuario->delete();

        return redirect()->route('usuarios.index')
            ->with('success', '🗑️ Usuario eliminado correctamente.');
    }
}
