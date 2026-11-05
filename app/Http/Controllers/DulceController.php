<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Dulce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class DulceController extends Controller
{
    /**
     * Display a listing of candies / inventory.
     */
    public function index(Request $request)
    {
        $query = Dulce::with('categoria');

        // Filtro por búsqueda de nombre
        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        // Filtro por categoría
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        // Filtro por bajo stock (<= 5)
        if ($request->boolean('bajo_stock')) {
            $query->where('stock', '<=', 5);
        }

        $dulces = $query->latest()->get();
        $categorias = Categoria::orderBy('nombre')->get();

        return view('dulces.index', compact('dulces', 'categorias'));
    }

    /**
     * Show the form for creating a new candy.
     */
    public function create()
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('dulces.create', compact('categorias'));
    }

    /**
     * Store a newly created candy in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'precio'       => ['required', 'numeric', 'min:0.01'],
            'stock'        => ['required', 'integer', 'min:0'],
            'descripcion'  => ['nullable', 'string', 'max:1000'],
            'imagen'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'nombre.required'       => 'El nombre del dulce es obligatorio.',
            'nombre.max'            => 'El nombre no puede tener más de 150 caracteres.',
            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'categoria_id.exists'   => 'La categoría seleccionada no es válida.',
            'precio.required'       => 'El precio del dulce es obligatorio.',
            'precio.numeric'        => 'El precio debe ser un número válido.',
            'precio.min'            => 'El precio debe ser mayor a 0.',
            'stock.required'        => 'El stock inicial es obligatorio.',
            'stock.integer'         => 'El stock debe ser un número entero.',
            'stock.min'             => 'El stock no puede ser un número negativo.',
            'descripcion.max'       => 'La descripción no puede tener más de 1000 caracteres.',
            'imagen.image'          => 'El archivo debe ser una imagen válida.',
            'imagen.mimes'          => 'La imagen debe ser de formato JPG, JPEG, PNG o WEBP.',
            'imagen.max'            => 'La imagen no debe pesar más de 2MB.',
        ]);

        $datos = [
            'nombre'       => trim($request->nombre),
            'categoria_id' => $request->categoria_id,
            'precio'       => $request->precio,
            'stock'        => $request->stock,
            'descripcion'  => $request->descripcion,
        ];

        if ($request->hasFile('imagen')) {
            $datos['imagen'] = $request->file('imagen')->store('dulces', 'public');
        }

        Dulce::create($datos);

        return redirect()->route('dulces.index')
            ->with('success', '🍬 Dulce agregado correctamente al inventario.');
    }

    /**
     * Show the form for editing the specified candy.
     */
    public function edit(Dulce $dulce)
    {
        $categorias = Categoria::orderBy('nombre')->get();
        return view('dulces.edit', compact('dulce', 'categorias'));
    }

    /**
     * Update the specified candy in storage.
     */
    public function update(Request $request, Dulce $dulce)
    {
        $request->validate([
            'nombre'       => ['required', 'string', 'max:150'],
            'categoria_id' => ['required', 'exists:categorias,id'],
            'precio'       => ['required', 'numeric', 'min:0.01'],
            'stock'        => ['required', 'integer', 'min:0'],
            'descripcion'  => ['nullable', 'string', 'max:1000'],
            'imagen'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp', 'max:2048'],
        ], [
            'nombre.required'       => 'El nombre del dulce es obligatorio.',
            'nombre.max'            => 'El nombre no puede tener más de 150 caracteres.',
            'categoria_id.required' => 'Debes seleccionar una categoría.',
            'categoria_id.exists'   => 'La categoría seleccionada no es válida.',
            'precio.required'       => 'El precio del dulce es obligatorio.',
            'precio.numeric'        => 'El precio debe ser un número válido.',
            'precio.min'            => 'El precio debe ser mayor a 0.',
            'stock.required'        => 'El stock es obligatorio.',
            'stock.integer'         => 'El stock debe ser un número entero.',
            'stock.min'             => 'El stock no puede ser un número negativo.',
            'descripcion.max'       => 'La descripción no puede tener más de 1000 caracteres.',
            'imagen.image'          => 'El archivo debe ser una imagen válida.',
            'imagen.mimes'          => 'La imagen debe ser de formato JPG, JPEG, PNG o WEBP.',
            'imagen.max'            => 'La imagen no debe pesar más de 2MB.',
        ]);

        $datos = [
            'nombre'       => trim($request->nombre),
            'categoria_id' => $request->categoria_id,
            'precio'       => $request->precio,
            'stock'        => $request->stock,
            'descripcion'  => $request->descripcion,
        ];

        if ($request->hasFile('imagen')) {
            if ($dulce->imagen && Storage::disk('public')->exists($dulce->imagen)) {
                Storage::disk('public')->delete($dulce->imagen);
            }
            $datos['imagen'] = $request->file('imagen')->store('dulces', 'public');
        }

        $dulce->update($datos);

        return redirect()->route('dulces.index')
            ->with('success', '✅ Dulce modificado correctamente.');
    }

    /**
     * Remove the specified candy from storage.
     */
    public function destroy(Dulce $dulce)
    {
        // Solo el Usuario Principal puede eliminar dulces y sus imágenes
        if (! Auth::user() || ! Auth::user()->isPrincipal()) {
            return redirect()->route('dulces.index')
                ->with('error', '⛔ Acceso denegado: Solo el Usuario Principal tiene permisos para eliminar dulces y sus imágenes.');
        }

        if ($dulce->imagen && Storage::disk('public')->exists($dulce->imagen)) {
            Storage::disk('public')->delete($dulce->imagen);
        }

        $dulce->delete();

        return redirect()->route('dulces.index')
            ->with('success', '🗑️ Dulce e imagen eliminados del inventario.');
    }
}
