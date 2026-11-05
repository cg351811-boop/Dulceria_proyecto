<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();

        $request->validate([
            'name'                  => ['required', 'string', 'max:255', 'regex:/^[\pL\s]+$/u'],
            'email'                 => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'password'              => ['nullable', 'string', 'min:6', 'confirmed'],
        ], [
            'name.required'         => 'El nombre es obligatorio.',
            'name.regex'            => 'El nombre solo debe contener letras, no se permiten números.',
            'email.required'        => 'El correo electrónico es obligatorio.',
            'email.email'           => 'Ingresa un correo electrónico válido.',
            'email.unique'          => 'Este correo electrónico ya está registrado por otro usuario.',
            'password.min'          => 'La contraseña debe tener al menos 6 caracteres.',
            'password.confirmed'    => 'Las contraseñas no coinciden.',
        ]);

        $user->name = trim($request->name);
        $user->email = trim($request->email);

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return Redirect::route('profile.edit')->with('success', '✅ Perfil actualizado correctamente.');
    }
}
