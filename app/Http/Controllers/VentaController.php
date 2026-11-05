<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use App\Models\Dulce;
use App\Models\Venta;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class VentaController extends Controller
{
    /**
     * MÉTODO GET: Muestra el catálogo de Punto de Ventas y permite buscar/filtrar productos.
     */
    public function index(Request $request)
    {
        $query = Dulce::with('categoria')->where('stock', '>', 0);

        // Búsqueda por nombre usando parámetros enviados por GET
        if ($request->filled('buscar')) {
            $query->where('nombre', 'like', '%' . $request->buscar . '%');
        }

        // Filtro por categoría usando parámetro enviado por GET
        if ($request->filled('categoria_id')) {
            $query->where('categoria_id', $request->categoria_id);
        }

        $dulces = $query->orderBy('nombre')->get();
        $categorias = Categoria::orderBy('nombre')->get();

        return view('ventas.index', compact('dulces', 'categorias'));
    }

    /**
     * MÉTODO POST: Procesa y registra la venta de un dulce, descontando el stock.
     */
    public function store(Request $request)
    {
        // Validación de datos recibidos por POST
        $request->validate([
            'dulce_id' => ['required', 'exists:dulces,id'],
            'cantidad' => ['required', 'integer', 'min:1'],
        ], [
            'dulce_id.required' => 'Debes seleccionar un dulce para vender.',
            'dulce_id.exists'   => 'El dulce seleccionado no es válido.',
            'cantidad.required' => 'Debes especificar la cantidad a vender.',
            'cantidad.integer'  => 'La cantidad debe ser un número entero.',
            'cantidad.min'      => 'La cantidad mínima a vender es 1.',
        ]);

        $dulce = Dulce::findOrFail($request->dulce_id);

        // Validar que haya suficiente stock
        if ($dulce->stock < $request->cantidad) {
            return back()->with('error', "⚠️ Stock insuficiente. Solo quedan {$dulce->stock} unidades de '{$dulce->nombre}'.");
        }

        // Calcular total
        $total = $dulce->precio * $request->cantidad;

        // Registrar la venta en la base de datos
        Venta::create([
            'dulce_id'        => $dulce->id,
            'user_id'         => Auth::id(),
            'cantidad'        => $request->cantidad,
            'precio_unitario' => $dulce->precio,
            'total'           => $total,
        ]);

        // Descontar automáticamente del inventario
        $dulce->decrement('stock', $request->cantidad);

        return back()->with('success', "✅ ¡Venta registrada con éxito! Vendiste {$request->cantidad} pza(s) de {$dulce->nombre} por $" . number_format($total, 2));
    }

    /**
     * MÉTODO GET: Consulta y muestra el historial de ventas registradas.
     */
    public function historial()
    {
        $ventas = Venta::with(['dulce.categoria', 'user'])->latest()->get();
        $totalVentas = $ventas->sum('total');

        return view('ventas.historial', compact('ventas', 'totalVentas'));
    }
}
