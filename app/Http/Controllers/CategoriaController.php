<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    /**
     * Display a listing of categories.
     */
    public function index()
    {
        $categorias = Categoria::withCount('dulces')->latest()->get();
        return view('categorias.index', compact('categorias'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('categorias.create');
    }

    /**
     * Store a newly created category in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => ['required', 'string', 'max:100', 'unique:categorias,nombre', 'regex:/^[\pL\s]+$/u'],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.regex'    => 'El nombre solo debe contener letras, no se permiten números.',
            'nombre.unique'   => 'Esta categoría ya existe en el sistema.',
            'nombre.max'      => 'El nombre no puede tener más de 100 caracteres.',
            'descripcion.max' => 'La descripción no puede tener más de 500 caracteres.',
        ]);

        Categoria::create([
            'nombre'      => trim($request->nombre),
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('categorias.index')
            ->with('success', '✅ Categoría agregada correctamente.');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Categoria $categoria)
    {
        return view('categorias.edit', compact('categoria'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre'      => ['required', 'string', 'max:100', 'unique:categorias,nombre,' . $categoria->id, 'regex:/^[\pL\s]+$/u'],
            'descripcion' => ['nullable', 'string', 'max:500'],
        ], [
            'nombre.required' => 'El nombre de la categoría es obligatorio.',
            'nombre.regex'    => 'El nombre solo debe contener letras, no se permiten números.',
            'nombre.unique'   => 'Esta categoría ya existe en el sistema.',
            'nombre.max'      => 'El nombre no puede tener más de 100 caracteres.',
            'descripcion.max' => 'La descripción no puede tener más de 500 caracteres.',
        ]);

        $categoria->update([
            'nombre'      => trim($request->nombre),
            'descripcion' => $request->descripcion,
        ]);

        return redirect()->route('categorias.index')
            ->with('success', '✅ Categoría actualizada correctamente.');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Categoria $categoria)
    {
        $categoria->delete();

        return redirect()->route('categorias.index')
            ->with('success', '🗑️ Categoría eliminada correctamente.');
    }
}
