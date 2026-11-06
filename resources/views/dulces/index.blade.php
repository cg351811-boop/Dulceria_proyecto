@extends('layouts.app')

@section('content')
<div class="mb-2 pt-2">
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver al Panel
    </a>
</div>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-box-seam text-primary me-2"></i>Administración de Inventario (Dulces)</h1>
    <a href="{{ route('dulces.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>+ Agregar Dulce
    </a>
</div>

{{-- Mensajes de estado --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

{{-- Barra de Filtros y Búsqueda (como en el boceto) --}}
<div class="card shadow-sm border-0 mb-4 bg-light">
    <div class="card-body">
        <form method="GET" action="{{ route('dulces.index') }}" class="row g-3 align-items-center">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white"><i class="bi bi-search"></i></span>
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar dulce por nombre..." value="{{ request('buscar') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="categoria_id" class="form-select">
                    <option value="">Todas las Categorías</option>
                    @foreach($categorias as $cat)
                        <option value="{{ $cat->id }}" {{ request('categoria_id') == $cat->id ? 'selected' : '' }}>
                            {{ $cat->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="bajo_stock" id="bajo_stock" value="1" {{ request('bajo_stock') ? 'checked' : '' }} onchange="this.form.submit()">
                    <label class="form-check-label fw-semibold text-danger" for="bajo_stock">
                        ⚠️ Solo Bajo Stock (≤ 5)
                    </label>
                </div>
            </div>
            <div class="col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-secondary w-100">Filtrar</button>
                @if(request('buscar') || request('categoria_id') || request('bajo_stock'))
                    <a href="{{ route('dulces.index') }}" class="btn btn-outline-secondary" title="Limpiar filtros">
                        <i class="bi bi-x-lg"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>
</div>

{{-- Tabla de Inventario --}}
<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        @if($dulces->isEmpty())
            <div class="text-center py-5">
                <div class="fs-1 text-muted mb-2">🍬</div>
                <h5 class="text-muted">No se encontraron dulces en el inventario</h5>
                <p class="text-muted small">Intenta cambiar los filtros o agrega un nuevo producto.</p>
                <a href="{{ route('dulces.create') }}" class="btn btn-primary btn-sm mt-2">
                    <i class="bi bi-plus-circle me-1"></i>Agregar Primer Dulce
                </a>
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th style="width: 80px;">Imagen</th>
                            <th>Nombre</th>
                            <th>Categoría</th>
                            <th>Stock</th>
                            <th>Precio</th>
                            <th class="text-end pe-4" style="width: 160px;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($dulces as $dulce)
                            <tr>
                                <td>
                                    @if($dulce->imagen)
                                        <img src="{{ asset('storage/' . $dulce->imagen) }}" alt="{{ $dulce->nombre }}" class="rounded shadow-sm" style="width: 50px; height: 50px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center text-muted border" style="width: 50px; height: 50px; font-size: 1.5rem;">
                                            🍬
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $dulce->nombre }}</div>
                                    @if($dulce->descripcion)
                                        <small class="text-muted text-truncate d-inline-block" style="max-width: 250px;">
                                            {{ $dulce->descripcion }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-secondary">{{ $dulce->categoria->nombre }}</span>
                                </td>
                                <td>
                                    @if($dulce->stock <= 5)
                                        <span class="badge bg-danger">⚠️ {{ $dulce->stock }} (Bajo)</span>
                                    @else
                                        <span class="badge bg-success">{{ $dulce->stock }} disp.</span>
                                    @endif
                                </td>
                                <td class="fw-bold text-success fs-6">
                                    ${{ number_format($dulce->precio, 2) }}
                                </td>
                                <td class="text-end pe-4">
                                    <a href="{{ route('dulces.edit', $dulce) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if(Auth::user() && Auth::user()->isPrincipal())
                                    <form action="{{ route('dulces.destroy', $dulce) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar este dulce?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar (Solo Usuario Principal)">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection
