@extends('layouts.app')

@section('content')
<div class="py-3">
    <!-- Encabezado con Botón de Regreso -->
    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-2">
        <div>
            <a href="{{ route('compras.index') }}" class="btn btn-outline-secondary btn-sm mb-2">
                <i class="bi bi-arrow-left"></i> Volver a Registro de Compras
            </a>
            <h1 class="h3 fw-bold text-dark mb-0">📊 Historial Completo de Compras</h1>
            <p class="text-muted small mb-0">Consulta todas las compras realizadas a proveedores y el total acumulado invertido.</p>
        </div>
        <div>
            <a href="{{ route('compras.index') }}" class="btn btn-primary shadow-sm">
                <i class="bi bi-plus-circle me-1"></i> Nueva Compra
            </a>
        </div>
    </div>

    <!-- Tarjetas de Resumen Numérico -->
    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-primary text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-bold">Total Invertido</div>
                            <div class="fs-3 fw-bold">${{ number_format($totalGeneral, 2) }}</div>
                        </div>
                        <i class="bi bi-cash-stack fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-6 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-success text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-bold">Unidades Adquiridas</div>
                            <div class="fs-3 fw-bold">{{ number_format($totalUnidades) }} pzas</div>
                        </div>
                        <i class="bi bi-boxes fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-12 col-md-4">
            <div class="card border-0 shadow-sm rounded-3 bg-dark text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="text-white-50 small text-uppercase fw-bold">Compras Registradas</div>
                            <div class="fs-3 fw-bold">{{ $compras->count() }} operaciones</div>
                        </div>
                        <i class="bi bi-receipt-cutoff fs-1 text-white-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtro de Búsqueda -->
    <div class="card border-0 shadow-sm rounded-3 mb-4">
        <div class="card-body p-3">
            <form action="{{ route('compras.historial') }}" method="GET" class="row g-2 align-items-center">
                <div class="col-md-9 col-sm-8">
                    <div class="input-group">
                        <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                        <input type="text" name="buscar" class="form-control" 
                               placeholder="Buscar por dulce o proveedor..." 
                               value="{{ request('buscar') }}">
                    </div>
                </div>
                <div class="col-md-3 col-sm-4 d-flex gap-2">
                    <button type="submit" class="btn btn-primary flex-grow-1">
                        Filtrar
                    </button>
                    @if(request('buscar'))
                        <a href="{{ route('compras.historial') }}" class="btn btn-outline-secondary" title="Limpiar filtro">
                            <i class="bi bi-x-lg"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla Responsiva de Compras -->
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-header bg-white py-3">
            <h5 class="card-title mb-0 fw-semibold text-dark">
                <i class="bi bi-table me-2 text-primary"></i> Listado de Transacciones
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th># Folio</th>
                            <th>Dulce</th>
                            <th>Proveedor</th>
                            <th>Cantidad</th>
                            <th>Costo Unitario</th>
                            <th>Total Pagado</th>
                            <th>Registrado Por</th>
                            <th>Fecha y Hora</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($compras as $compra)
                            <tr>
                                <td class="fw-bold text-muted">#COMP-{{ str_pad($compra->id, 4, '0', STR_PAD_LEFT) }}</td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $compra->dulce->nombre ?? 'Dulce Eliminado' }}</div>
                                    <small class="text-muted">{{ $compra->dulce->categoria->nombre ?? 'Sin categoría' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-truck me-1"></i> {{ $compra->proveedor ?: 'Proveedor General' }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-success fs-6">+{{ $compra->cantidad }} pzas</span>
                                </td>
                                <td class="text-muted">
                                    ${{ number_format($compra->precio_unitario, 2) }}
                                </td>
                                <td class="fw-bold text-primary fs-6">
                                    ${{ number_format($compra->total, 2) }}
                                </td>
                                <td class="small">
                                    👤 {{ $compra->user->name ?? 'Sistema' }}
                                </td>
                                <td class="small text-muted">
                                    {{ $compra->created_at->format('d/m/Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    <i class="bi bi-inbox fs-1 d-block mb-2 text-muted"></i>
                                    No se encontraron registros de compras.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
