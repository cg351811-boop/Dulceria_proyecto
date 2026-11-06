@extends('layouts.app')

@section('content')
<div class="mb-2 pt-2">
    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver a Punto de Ventas
    </a>
</div>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-clock-history text-primary me-2"></i>Historial de Ventas Registradas</h1>
    <a href="{{ route('ventas.index') }}" class="btn btn-outline-success">
        <i class="bi bi-cart-check me-1"></i>Ir a Punto de Ventas
    </a>
</div>

{{-- Tarjeta de resumen total recaudado --}}
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 text-uppercase small fw-bold">Total Recaudado (Ventas)</div>
                        <div class="h2 fw-bold mb-0">${{ number_format($totalVentas, 2) }}</div>
                    </div>
                    <div class="fs-1">💰</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card shadow-sm border-0 bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-white-50 text-uppercase small fw-bold">Transacciones Realizadas</div>
                        <div class="h2 fw-bold mb-0">{{ $ventas->count() }}</div>
                    </div>
                    <div class="fs-1">🧾</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="card-title mb-0">Detalle de Ventas (Consultadas vía Método GET)</h5>
        <span class="badge bg-info text-dark">MÉTODO GET</span>
    </div>
    <div class="card-body p-0">
        @if($ventas->isEmpty())
            <div class="text-center py-5">
                <div class="fs-1 text-muted mb-2">🧾</div>
                <h5 class="text-muted">Aún no se han registrado ventas</h5>
                <p class="text-muted small">Realiza una venta desde el Punto de Ventas para verla registrada aquí.</p>
                <a href="{{ route('ventas.index') }}" class="btn btn-primary btn-sm mt-2">
                    Ir a Vender
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th># Folio</th>
                            <th>Fecha y Hora</th>
                            <th>Dulce</th>
                            <th>Categoría</th>
                            <th>Vendedor</th>
                            <th class="text-center">Cantidad</th>
                            <th class="text-end">Precio Unit.</th>
                            <th class="text-end pe-4">Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($ventas as $venta)
                            <tr>
                                <td class="fw-bold text-muted">#{{ str_pad($venta->id, 5, '0', STR_PAD_LEFT) }}</td>
                                <td>{{ $venta->created_at->format('d/m/Y h:i A') }}</td>
                                <td class="fw-bold">{{ $venta->dulce->nombre }}</td>
                                <td><span class="badge bg-secondary">{{ $venta->dulce->categoria->nombre }}</span></td>
                                <td><small class="text-muted">👤 {{ $venta->user->name }}</small></td>
                                <td class="text-center fw-bold">{{ $venta->cantidad }} pza(s)</td>
                                <td class="text-end">${{ number_format($venta->precio_unitario, 2) }}</td>
                                <td class="text-end pe-4 fw-bold text-success">${{ number_format($venta->total, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
