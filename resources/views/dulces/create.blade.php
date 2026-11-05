@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-plus-circle text-primary me-2"></i>Agregar Dulce</h1>
    <a href="{{ route('dulces.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i>Volver al Inventario
    </a>
</div>

<div class="row justify-content-center">
    <div class="col-lg-8 col-md-10">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="card-title mb-0">🍬 Registro de Nuevo Dulce</h5>
            </div>
            <div class="card-body p-4">
                {{-- Alertas si hay errores del servidor --}}
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

                <form action="{{ route('dulces.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    <div class="row g-3">
                        <!-- Nombre del Dulce -->
                        <div class="col-md-7">
                            <label for="nombre" class="form-label fw-bold">Nombre del Dulce <span class="text-danger">*</span></label>
                            <input type="text" 
                                   name="nombre" 
                                   id="nombre" 
                                   class="form-control @error('nombre') is-invalid @enderror" 
                                   value="{{ old('nombre') }}" 
                                   required 
                                   placeholder="Ej: Mazapán De la Rosa, Paleta Payaso, Kranky"
                                   autofocus>
                            @error('nombre')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Categoría -->
                        <div class="col-md-5">
                            <label for="categoria_id" class="form-label fw-bold">Categoría <span class="text-danger">*</span></label>
                            <select name="categoria_id" 
                                    id="categoria_id" 
                                    class="form-select @error('categoria_id') is-invalid @enderror" 
                                    required>
                                <option value="">-- Selecciona una categoría --</option>
                                @foreach($categorias as $cat)
                                    <option value="{{ $cat->id }}" {{ old('categoria_id') == $cat->id ? 'selected' : '' }}>
                                        {{ $cat->nombre }}
                                    </option>
                                @endforeach
                            </select>
                            @error('categoria_id')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Precio -->
                        <div class="col-md-6">
                            <label for="precio" class="form-label fw-bold">Precio Unitario ($) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">$</span>
                                <input type="number" 
                                       name="precio" 
                                       id="precio" 
                                       step="0.01" 
                                       min="0.01" 
                                       class="form-control @error('precio') is-invalid @enderror" 
                                       value="{{ old('precio') }}" 
                                       required 
                                       placeholder="0.00">
                            </div>
                            <div class="form-text">Debe ser mayor a 0.</div>
                            @error('precio')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Stock Inicial -->
                        <div class="col-md-6">
                            <label for="stock" class="form-label fw-bold">Stock Inicial (Piezas) <span class="text-danger">*</span></label>
                            <input type="number" 
                                   name="stock" 
                                   id="stock" 
                                   step="1" 
                                   min="0" 
                                   class="form-control @error('stock') is-invalid @enderror" 
                                   value="{{ old('stock', 0) }}" 
                                   required 
                                   placeholder="0">
                            <div class="form-text">Cantidad disponible en inventario (mínimo 0).</div>
                            @error('stock')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Descripción -->
                        <div class="col-12">
                            <label for="descripcion" class="form-label fw-bold">Descripción (Opcional)</label>
                            <textarea name="descripcion" 
                                      id="descripcion" 
                                      rows="3" 
                                      class="form-control @error('descripcion') is-invalid @enderror" 
                                      placeholder="Presentación, sabor, peso o detalles del dulce...">{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Imagen del Dulce -->
                        <div class="col-12">
                            <label for="imagen" class="form-label fw-bold">Imagen del Producto (Opcional)</label>
                            <input type="file" 
                                   name="imagen" 
                                   id="imagen" 
                                   accept="image/*"
                                   class="form-control @error('imagen') is-invalid @enderror">
                            <div class="form-text">Formatos permitidos: JPG, PNG, WEBP (máx. 2MB).</div>
                            @error('imagen')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Botones de Acción -->
                    <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                        <a href="{{ route('dulces.index') }}" class="btn btn-secondary">Cancelar</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-save me-1"></i>Guardar Dulce
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
