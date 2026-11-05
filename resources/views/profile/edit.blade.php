@extends('layouts.app')

@section('content')
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2"><i class="bi bi-person-gear text-primary me-2"></i>Configuración del Sistema - Perfil de Usuario</h1>
</div>

{{-- Mensaje de éxito --}}
@if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="row justify-content-center">
    <div class="col-lg-7 col-md-9">
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-dark text-white py-3">
                <div class="d-flex align-items-center">
                    <div class="fs-3 me-3">👤</div>
                    <div>
                        <h5 class="card-title mb-0">Datos del Perfil</h5>
                        <small class="text-white-50">Actualiza tu información personal y credenciales de acceso</small>
                    </div>
                </div>
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

                <form action="{{ route('profile.update') }}" method="POST" novalidate>
                    @csrf
                    @method('PATCH')

                    <!-- Nombre Completo -->
                    <div class="mb-3">
                        <label for="name" class="form-label fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                        <input type="text" 
                               name="name" 
                               id="name" 
                               class="form-control @error('name') is-invalid @enderror" 
                               value="{{ old('name', $user->name) }}" 
                               required 
                               data-only-letters="true"
                               placeholder="Ingresa tu nombre completo">
                        <div class="form-text">Solo se permiten letras (sin números).</div>
                        @error('name')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Correo Electrónico -->
                    <div class="mb-3">
                        <label for="email" class="form-label fw-bold">Correo Electrónico <span class="text-danger">*</span></label>
                        <input type="email" 
                               name="email" 
                               id="email" 
                               class="form-control @error('email') is-invalid @enderror" 
                               value="{{ old('email', $user->email) }}" 
                               required 
                               placeholder="correo@ejemplo.com">
                        @error('email')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <hr class="my-4">
                    <h6 class="fw-bold text-muted mb-3"><i class="bi bi-shield-lock me-1"></i>Cambiar Contraseña (Opcional)</h6>
                    <p class="text-muted small">Deja estos campos vacíos si deseas conservar tu contraseña actual.</p>

                    <!-- Nueva Contraseña -->
                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold">Nueva Contraseña</label>
                        <input type="password" 
                               name="password" 
                               id="password" 
                               minlength="6"
                               class="form-control @error('password') is-invalid @enderror" 
                               placeholder="Mínimo 6 caracteres (dejar en blanco para no cambiar)">
                        <div class="form-text">Si deseas cambiarla, debe tener al menos 6 caracteres.</div>
                        @error('password')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div class="mb-4">
                        <label for="password_confirmation" class="form-label fw-bold">Confirmar Nueva Contraseña</label>
                        <input type="password" 
                               name="password_confirmation" 
                               id="password_confirmation" 
                               minlength="6"
                               class="form-control" 
                               placeholder="Vuelve a escribir la nueva contraseña">
                    </div>

                    <!-- Botón Guardar -->
                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <a href="{{ route('dashboard') }}" class="btn btn-secondary">Volver al Panel</a>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="bi bi-check-circle me-1"></i>Guardar Cambios
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
