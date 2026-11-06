@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-2 pb-2 mb-3 border-bottom gap-2">
    <h1 class="h2 mb-0"><i class="bi bi-speedometer2 text-primary me-2"></i>Panel de Control (Dulcería)</h1>
    <div class="btn-toolbar gap-2 flex-wrap">
        <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-success shadow-sm">
            <i class="bi bi-cart-check me-1"></i> Punto de Venta
        </a>
        <a href="{{ route('compras.index') }}" class="btn btn-sm btn-warning text-dark shadow-sm">
            <i class="bi bi-box-seam me-1"></i> Registrar Compra
        </a>
        <a href="{{ route('dulces.create') }}" class="btn btn-sm btn-primary shadow-sm">
            <i class="bi bi-plus-circle me-1"></i>+ Dulce
        </a>
        <a href="{{ route('categorias.create') }}" class="btn btn-sm btn-outline-primary shadow-sm">
            <i class="bi bi-tags me-1"></i>+ Categoría
        </a>
    </div>
</div>

{{-- Tarjetas Resumen --}}
<div class="row g-3 mb-4">
    <!-- Total Dulces -->
    <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 border-start border-primary border-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-primary text-uppercase mb-1">Total Dulces</div>
                        <div class="h3 mb-0 fw-bold">{{ \App\Models\Dulce::count() }}</div>
                    </div>
                    <div class="fs-1 text-primary">🍬</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <a href="{{ route('dulces.index') }}" class="small text-primary text-decoration-none">Ver inventario &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Categorías -->
    <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 border-start border-info border-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-info text-uppercase mb-1">Categorías</div>
                        <div class="h3 mb-0 fw-bold">{{ \App\Models\Categoria::count() }}</div>
                    </div>
                    <div class="fs-1 text-info">🏷️</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <a href="{{ route('categorias.index') }}" class="small text-info text-decoration-none">Ver categorías &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Bajo Stock -->
    <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 border-start border-danger border-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-danger text-uppercase mb-1">Bajo Stock (≤ 5)</div>
                        <div class="h3 mb-0 fw-bold text-danger">{{ \App\Models\Dulce::where('stock', '<=', 5)->count() }}</div>
                    </div>
                    <div class="fs-1 text-danger">⚠️</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <a href="{{ route('dulces.index', ['bajo_stock' => 1]) }}" class="small text-danger text-decoration-none">Ver agotándose &rarr;</a>
            </div>
        </div>
    </div>

    <!-- Usuarios -->
    <div class="col-xl-3 col-sm-6">
        <div class="card shadow-sm border-0 border-start border-success border-4 h-100">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <div class="text-xs fw-bold text-success text-uppercase mb-1">Usuarios</div>
                        <div class="h3 mb-0 fw-bold">{{ \App\Models\User::count() }}</div>
                    </div>
                    <div class="fs-1 text-success">👥</div>
                </div>
            </div>
            <div class="card-footer bg-transparent border-top-0 pt-0">
                <a href="{{ route('usuarios.index') }}" class="small text-success text-decoration-none">Gestionar usuarios &rarr;</a>
            </div>
        </div>
    </div>
</div>

{{-- Menú de Dulce Rápido (como en el boceto) --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-header bg-dark text-white d-flex justify-content-between align-items-center py-3">
        <h5 class="card-title mb-0">🍭 Menú de Dulces Recientes</h5>
        <a href="{{ route('dulces.index') }}" class="btn btn-sm btn-outline-light">Ver Todo el Inventario</a>
    </div>
    <div class="card-body">
        @php
            $dulcesRecientes = \App\Models\Dulce::with('categoria')->latest()->take(6)->get();
        @endphp

        @if($dulcesRecientes->isEmpty())
            <div class="text-center py-4">
                <p class="text-muted mb-2">Aún no hay dulces registrados en el sistema.</p>
                <a href="{{ route('dulces.create') }}" class="btn btn-primary btn-sm">
                    <i class="bi bi-plus-circle me-1"></i>Agregar Primer Dulce
                </a>
            </div>
        @else
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-3">
                @foreach($dulcesRecientes as $dulce)
                    <div class="col">
                        <div class="card h-100 border shadow-sm">
                            <div class="card-body d-flex align-items-center gap-3">
                                @if($dulce->imagen)
                                    <img src="{{ asset('storage/' . $dulce->imagen) }}" alt="{{ $dulce->nombre }}" class="rounded" style="width: 60px; height: 60px; object-fit: cover;">
                                @else
                                    <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border" style="width: 60px; height: 60px; font-size: 1.8rem;">
                                        🍬
                                    </div>
                                @endif
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">{{ $dulce->nombre }}</h6>
                                    <small class="text-muted d-block">{{ $dulce->categoria->nombre }}</small>
                                    <div class="d-flex justify-content-between align-items-center mt-2">
                                        <span class="fw-bold text-success fs-6">${{ number_format($dulce->precio, 2) }}</span>
                                        <span class="badge {{ $dulce->stock <= 5 ? 'bg-danger' : 'bg-secondary' }}">Stock: {{ $dulce->stock }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer bg-transparent border-top-0 d-flex justify-content-end gap-1 pt-0 pb-2">
                                <a href="{{ route('dulces.edit', $dulce) }}" class="btn btn-sm btn-outline-warning">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
