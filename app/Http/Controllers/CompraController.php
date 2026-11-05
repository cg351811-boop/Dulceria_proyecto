<?php

namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\Dulce;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CompraController extends Controller
{
    /**
     * Muestra el formulario para registrar compras (abastecimiento) y calcular el total en tiempo real.
     */
    public function index(Request $request)
    {
        $dulces = Dulce::with('categoria')->orderBy('nombre')->get();
        $comprasRecientes = Compra::with(['dulce.categoria', 'user'])->latest()->take(8)->get();
        $totalHistorico = Compra::sum('total');

        return view('compras.index', compact('dulces', 'comprasRecientes', 'totalHistorico'));
    }

    /**
     * Procesa y guarda la compra calculando el total y sumando el stock al inventario.
     */
    public function store(Request $request)
    {
        $request->validate([
            'dulce_id'        => ['required', 'exists:dulces,id'],
            'cantidad'        => ['required', 'integer', 'min:1'],
            'precio_unitario' => ['required', 'numeric', 'min:0.01'],
            'proveedor'       => ['nullable', 'string', 'max:150'],
        ], [
            'dulce_id.required'        => 'Debes seleccionar el dulce a comprar.',
            'dulce_id.exists'          => 'El dulce seleccionado no es válido.',
            'cantidad.required'        => 'La cantidad es obligatoria.',
            'cantidad.integer'         => 'La cantidad debe ser un número entero.',
            'cantidad.min'             => 'La cantidad mínima a comprar es 1.',
            'precio_unitario.required' => 'El costo unitario es obligatorio.',
            'precio_unitario.numeric'  => 'El costo unitario debe ser un valor numérico.',
            'precio_unitario.min'      => 'El costo unitario debe ser mayor a 0.',
            'proveedor.max'            => 'El nombre del proveedor no debe superar 150 caracteres.',
        ]);

        $dulce = Dulce::findOrFail($request->dulce_id);

        // Cálculo exacto del total en el backend
        $cantidad = (int) $request->cantidad;
        $precioUnitario = (float) $request->precio_unitario;
        $total = $cantidad * $precioUnitario;

        // Registrar la compra
        Compra::create([
            'dulce_id'        => $dulce->id,
            'user_id'         => Auth::id(),
            'proveedor'       => trim($request->proveedor) ?: 'Proveedor General',
            'cantidad'        => $cantidad,
            'precio_unitario' => $precioUnitario,
            'total'           => $total,
        ]);

        // Aumentar automáticamente el stock del dulce en el inventario
        $dulce->increment('stock', $cantidad);

        return redirect()->route('compras.index')->with(
            'success',
            "✅ ¡Compra registrada con éxito! Se añadieron {$cantidad} unidades al dulce '{$dulce->nombre}'. Total calculado: $" . number_format($total, 2)
        );
    }

    /**
     * Muestra el historial completo de compras realizadas.
     */
    public function historial(Request $request)
    {
        $query = Compra::with(['dulce.categoria', 'user']);

        if ($request->filled('buscar')) {
            $buscar = $request->buscar;
            $query->whereHas('dulce', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%");
            })->orWhere('proveedor', 'like', "%{$buscar}%");
        }

        $compras = $query->latest()->get();
        $totalGeneral = $compras->sum('total');
        $totalUnidades = $compras->sum('cantidad');

        return view('compras.historial', compact('compras', 'totalGeneral', 'totalUnidades'));
    }
}
