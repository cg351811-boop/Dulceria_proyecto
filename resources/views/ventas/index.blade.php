@extends('layouts.app')

@section('content')
<div class="mb-2 pt-2">
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver al Panel
    </a>
</div>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-cart-check text-success me-2"></i>Punto de Ventas (Dulcería)</h1>
    <div>
        <a href="{{ route('ventas.historial') }}" class="btn btn-outline-primary">
            <i class="bi bi-clock-history me-1"></i>Ver Historial de Ventas (GET)
        </a>
    </div>
</div>

{{-- Alertas de éxito o error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- FORMULARIO CON MÉTODO GET: Búsqueda y Filtrado de Dulces --}}
<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body">
        <div class="d-flex align-items-center mb-2">
            <span class="badge bg-primary me-2">MÉTODO GET</span>
            <small class="text-muted fw-bold">Buscar y consultar productos mediante URL</small>
        </div>
        <form method="GET" action="{{ route('ventas.index') }}" class="row g-2 align-items-center">
            <div class="col-md-5">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar dulce (ej: Chocolate, Gomitas, Paleta)..." value="{{ request('buscar') }}">
                </div>
            </div>
            <div class="col-md-4">
                <select name="categoria_id" class="form-select">
                    <option value="">Todas las Categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>Buscar (GET)
                </button>
                @if(request('buscar') || request('categoria_id'))
                    <a href="{{ route('ventas.index') }}" class="btn btn-outline-secondary" title="Limpiar búsqueda">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Catálogo de Dulces Disponibles para Vender --}}
<div class="mb-3 d-flex justify-content-between align-items-center">
    <h5 class="fw-bold mb-0">🍬 Dulces Disponibles para Venta ({{ $dulces->count() }})</h5>
    <span class="badge bg-success">MÉTODO POST para procesar compras</span>
</div>

@if($dulces->isEmpty())
    <div class="text-center py-5 bg-white rounded shadow-sm">
        <div class="fs-1 text-muted mb-2">🍭</div>
        <h5 class="text-muted">No hay dulces con stock disponible para la venta</h5>
        <p class="text-muted small">Verifica tu inventario o ingresa nuevas unidades.</p>
        <a href="{{ route('dulces.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i>Agregar Stock de Dulces
        </a>
    </div>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
        @foreach($dulces as $dulce)
            <div class="col">
                <div class="card h-100 shadow-sm border-0 position-relative">
                    <div class="card-body">
                        <div class="d-flex align-items-center gap-3 mb-3">
                            @if($dulce->imagen)
                                <img src="{{ asset('storage/' . $dulce->imagen) }}" alt="{{ $dulce->nombre }}" class="rounded shadow-sm" style="width: 70px; height: 70px; object-fit: cover;">
                            @else
                                <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border" style="width: 70px; height: 70px; font-size: 2rem;">
                                    🍬
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <span class="badge bg-secondary mb-1">{{ $dulce->categoria->nombre }}</span>
                                <h5 class="card-title fw-bold mb-1">{{ $dulce->nombre }}</h5>
                                <div class="fs-5 text-success fw-bold">${{ number_format($dulce->precio, 2) }}</div>
                            </div>
                        </div>

                        @if($dulce->descripcion)
                            <p class="card-text text-muted small mb-3">
                                {{ $dulce->descripcion }}
                            </p>
                        @endif

                        <div class="d-flex justify-content-between align-items-center text-muted small mb-3">
                            <span>Disponibles:</span>
                            <span class="badge {{ $dulce->stock <= 5 ? 'bg-danger' : 'bg-primary' }}">
                                {{ $dulce->stock }} piezas
                            </span>
                        </div>

                        {{-- FORMULARIO CON MÉTODO POST: Registrar Venta --}}
                        <form action="{{ route('ventas.store') }}" method="POST" class="mt-2 pt-2 border-top">
                            @csrf
                            <input type="hidden" name="dulce_id" value="{{ $dulce->id }}">
                            <div class="input-group">
                                <span class="input-group-text small">Cant.</span>
                                <input type="number" 
                                       name="cantidad" 
                                       class="form-control" 
                                       value="1" 
                                       min="1" 
                                       max="{{ $dulce->stock }}" 
                                       required>
                                <button type="submit" class="btn btn-success px-3 fw-bold">
                                    <i class="bi bi-cart-plus me-1"></i>Vender (POST)
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
