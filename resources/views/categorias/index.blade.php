@extends('layouts.app')

@section('content')
<div class="mb-2 pt-2">
    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Volver al Panel
    </a>
</div>
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-tags me-2 text-primary"></i>Gestión de Categorías</h1>
    <a href="{{ route('categorias.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i>+ Nueva Categoría
    </a>
</div>

{{-- Alertas de éxito o error --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

@if($categorias->isEmpty())
    <div class="text-center py-5">
        <div class="fs-1 text-muted mb-3">🏷️</div>
        <h4 class="text-muted">Aún no hay categorías registradas</h4>
        <p class="text-muted">Comienza agregando las categorías para organizar los dulces (por ejemplo: Chocolates, Gomitas, Paletas, Caramelos).</p>
        <a href="{{ route('categorias.create') }}" class="btn btn-primary mt-2">
            <i class="bi bi-plus-circle me-1"></i>Crear Primera Categoría
        </a>
    </div>
@else
    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4 mb-4">
        @foreach($categorias as $categoria)
            <div class="col">
                <div class="card shadow-sm h-100 border-0">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <h5 class="card-title text-primary fw-bold mb-0">{{ $categoria->nombre }}</h5>
                            <span class="badge bg-info text-dark">
                                {{ $categoria->dulces_count }} {{ Str::plural('dulce', $categoria->dulces_count) }}
                            </span>
                        </div>
                        <p class="card-text text-muted small">
                            {{ $categoria->descripcion ?: 'Sin descripción registrada.' }}
                        </p>
                    </div>
                    <div class="card-footer bg-transparent border-top-0 d-flex justify-content-end gap-2 pb-3">
                        <a href="{{ route('categorias.edit', $categoria) }}" class="btn btn-sm btn-outline-warning">
                            <i class="bi bi-pencil me-1"></i>Editar
                        </a>
                        @if(Auth::user() && Auth::user()->isPrincipal())
                        <form action="{{ route('categorias.destroy', $categoria) }}" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta categoría?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar (Solo Usuario Principal)">
                                <i class="bi bi-trash me-1"></i>Eliminar
                            </button>
                        </form>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endif
@endsection
