@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-plus-circle text-primary me-2"></i>Agregar Categoría</h1>
    <a href="{{ route('categorias.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver a Categorías
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-6 col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title mb-0">Nueva Categoría de Dulces</h5>
            </div>
            <div class="card-body p-4">
                {{-- Alerta general si hay errores del servidor --}}
                @if($errors->any())
                    <div class="alert alert-danger mb-4">
                        <strong class="d-block mb-1">Por favor corrige los siguientes errores:</strong>
                        <ul class="mb-0 ps-3">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('categorias.store') }}" method="POST" novalidate>
                    @csrf

                    <!-- Nombre de la Categoría -->
                    <div class="mb-3">
                        <label for="nombre" class="form-label fw-bold">Nombre de la Categoría <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="nombre" 
                               id="nombre" 
                               class="form-control @error('nombre') is-invalid @enderror" 
                               value="{{ old('nombre') }}" 
                               required 
                               data-only-letters="true"
                               placeholder="Ej: Chocolates, Gomitas, Paletas"
                               autofocus>
                        <div class="form-text">Solo se permiten letras (sin números).</div>
                        @error('nombre')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Descripción -->
                    <div class="mb-4">
                        <label for="descripcion" class="form-label fw-bold">Descripción (Opcional)</label>
                        <textarea name="descripcion" 
                                  id="descripcion" 
                                  rows="3" 
                                  class="form-control @error('descripcion') is-invalid @enderror" 
                                  placeholder="Detalle o descripción de la categoría...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('categorias.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>Guardar Categoría
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
