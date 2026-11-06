@extends('layouts.app')

@section('content')
<div class="py-3">
    <!-- Encabezado con Botón de Regreso -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver al Panel
            </a>
            <h1 class="h3 fw-bold text-dark mb-0">📦 Registro de Compras (Abastecimiento)</h1>
            <p class="text-muted small mb-0">Ingresa compras a proveedores, calcula el total en tiempo real y aumenta el stock automáticamente.</p>
        </div>
        <div>
            <a href="{{ route('compras.historial') }}" class="btn btn-info text-white shadow-sm">
                <i class="bi bi-journal-text me-1"></i> Ver Historial de Compras
            </a>
        </div>
    </div>

    {{-- Alertas de Éxito o Error --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i> <strong>Por favor corrige los siguientes errores:</strong>
            <ul class="mb-0 mt-1 small">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row g-4">
        <!-- Formulario de Compra con Cálculo de Total -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-3">
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-cart-plus me-2"></i> Formulario de Nueva Compra
                    </h5>
                </div>
                <div class="card-body p-4">
                    <form action="{{ route('compras.store') }}" method="POST" id="formCompra">
                        @csrf

                        <!-- Selección del Dulce -->
                        <div class="mb-3">
                            <label for="dulce_id" class="form-label fw-bold">Dulce a reabastecer <span class="text-danger">*</span></label>
                            <select name="dulce_id" id="dulce_id" class="form-select form-select-lg @error('dulce_id') is-invalid @enderror" required>
                                <option value="" disabled selected>-- Selecciona un dulce --</option>
                                @foreach($dulces as $dulce)
                                    <option value="{{ $dulce->id }}" 
                                            data-precio="{{ $dulce->precio }}" 
                                            data-stock="{{ $dulce->stock }}"
                                            {{ old('dulce_id') == $dulce->id ? 'selected' : '' }}>
                                        🍬 {{ $dulce->nombre }} (Stock actual: {{ $dulce->stock }} pzas | Cat: {{ $dulce->categoria->nombre ?? 'Sin categoría' }})
                                    </option>
                                @endforeach
                            </select>
                            @error('dulce_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <div id="stockBadge" class="form-text text-muted mt-1"></div>
                        </div>

                        <!-- Proveedor -->
                        <div class="mb-3">
                            <label for="proveedor" class="form-label fw-bold">Proveedor / Distribuidor (Opcional)</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-truck"></i></span>
                                <input type="text" name="proveedor" id="proveedor" class="form-control" 
                                       placeholder="Ej. Distribuidora Dulcilandia S.A." 
                                       value="{{ old('proveedor') }}">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Cantidad -->
                            <div class="col-sm-6">
                                <label for="cantidad" class="form-label fw-bold">Cantidad de Piezas <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-123"></i></span>
                                    <input type="number" name="cantidad" id="cantidad" min="1" step="1" 
                                           class="form-control form-control-lg @error('cantidad') is-invalid @enderror" 
                                           placeholder="Ej. 50" value="{{ old('cantidad', 1) }}" required>
                                </div>
                                @error('cantidad')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Costo Unitario -->
                            <div class="col-sm-6">
                                <label for="precio_unitario" class="form-label fw-bold">Costo Unitario ($ MXN) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text">$</span>
                                    <input type="number" name="precio_unitario" id="precio_unitario" min="0.01" step="0.01" 
                                           class="form-control form-control-lg @error('precio_unitario') is-invalid @enderror" 
                                           placeholder="0.00" value="{{ old('precio_unitario') }}" required>
                                </div>
                                @error('precio_unitario')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Tarjeta de Cálculo del Total en Tiempo Real -->
                        <div class="card bg-light border-primary border-opacity-50 p-3 mb-4 rounded-3">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Cantidad a comprar:</span>
                                <strong id="lblCantidad" class="fs-6">1 pza(s)</strong>
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="text-muted">Costo unitario:</span>
                                <strong id="lblCostoUnitario" class="fs-6">$0.00</strong>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold fs-5 text-dark">TOTAL CALCULADO:</span>
                                <span id="lblTotal" class="fw-bold fs-3 text-primary">$0.00</span>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary btn-lg flex-grow-1 shadow-sm">
                                <i class="bi bi-check2-circle me-1"></i> Registrar Compra y Sumar Stock
                            </button>
                            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-lg">
                                Cancelar
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Panel Lateral: Resumen de Compras Recientes -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-3 mb-3">
                <div class="card-header bg-dark text-white py-3 d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0 fw-semibold">
                        <i class="bi bi-clock-history me-1"></i> Últimas Compras
                    </h5>
                    <span class="badge bg-primary">Total: ${{ number_format($totalHistorico, 2) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Dulce</th>
                                    <th>Cant.</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($comprasRecientes as $compra)
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-dark">{{ $compra->dulce->nombre ?? 'Eliminado' }}</span>
                                            <div class="text-muted small text-truncate" style="max-width: 140px;">{{ $compra->proveedor }}</div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success">+{{ $compra->cantidad }}</span>
                                        </td>
                                        <td class="fw-bold text-primary">
                                            ${{ number_format($compra->total, 2) }}
                                        </td>
                                        <td class="small text-muted">
                                            {{ $compra->created_at->format('d/m H:i') }}
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-4 text-muted">
                                            <i class="bi bi-inbox fs-2 d-block mb-1"></i>
                                            No se han registrado compras aún.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="card-footer bg-white text-center py-2">
                    <a href="{{ route('compras.historial') }}" class="text-decoration-none small fw-bold">
                        Ver todas las compras registradas &rarr;
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Script para cálculo dinámico del total en tiempo real --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectDulce = document.getElementById('dulce_id');
    const inputCantidad = document.getElementById('cantidad');
    const inputPrecio = document.getElementById('precio_unitario');
    const lblCantidad = document.getElementById('lblCantidad');
    const lblCostoUnitario = document.getElementById('lblCostoUnitario');
    const lblTotal = document.getElementById('lblTotal');
    const stockBadge = document.getElementById('stockBadge');

    function formatearDinero(valor) {
        return '$' + parseFloat(valor || 0).toLocaleString('es-MX', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });
    }

    function calcularTotal() {
        const cantidad = parseInt(inputCantidad.value) || 0;
        const precio = parseFloat(inputPrecio.value) || 0;
        const total = (cantidad > 0 && precio > 0) ? (cantidad * precio) : 0;

        lblCantidad.textContent = cantidad + ' pza(s)';
        lblCostoUnitario.textContent = formatearDinero(precio);
        lblTotal.textContent = formatearDinero(total);
    }

    // Al seleccionar un dulce, mostrar su stock actual y sugerir costo si está vacío
    selectDulce.addEventListener('change', function () {
        const selected = selectDulce.options[selectDulce.selectedIndex];
        if (selected && selected.dataset.stock !== undefined) {
            stockBadge.innerHTML = `<span class="badge bg-secondary">Stock actual en almacén: ${selected.dataset.stock} unidades</span>`;
            if (!inputPrecio.value || parseFloat(inputPrecio.value) <= 0) {
                // Sugerir el 70% del precio de venta como costo estimado si no se ha escrito
                const precioVenta = parseFloat(selected.dataset.precio || 0);
                if (precioVenta > 0) {
                    inputPrecio.value = (precioVenta * 0.7).toFixed(2);
                }
            }
        } else {
            stockBadge.textContent = '';
        }
        calcularTotal();
    });

    inputCantidad.addEventListener('input', calcularTotal);
    inputPrecio.addEventListener('input', calcularTotal);

    // Ejecutar al cargar la página si ya hay valores seleccionados
    calcularTotal();
});
</script>
@endsection
