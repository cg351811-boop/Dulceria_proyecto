@extends('layouts.app')

@section('content')
<div class="container py-2">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="mb-3">
                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Volver a Usuarios
                </a>
            </div>

            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-warning text-dark py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-pencil-square me-2"></i>Modificar Usuario: {{ $usuario->name }}</h5>
                </div>
                <div class="card-body p-4">
                    {{-- Errores de validación --}}
                    @if(session('error'))
                        <div class="alert alert-danger shadow-sm">
                            <i class="bi bi-shield-slash me-2"></i>{{ session('error') }}
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="alert alert-danger shadow-sm">
                            <strong>⚠️ Por favor corrige los siguientes errores:</strong>
                            <ul class="mb-0 mt-1 small">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <form action="{{ route('usuarios.update', $usuario) }}" method="POST" id="usuarioForm" novalidate>
                        @csrf
                        @method('PUT')

                        <!-- Nombre Completo -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name', $usuario->name) }}" required data-only-letters="true">
                            <div class="form-text">Solo se permiten letras, sin números.</div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Correo Electrónico -->
                        <div class="mb-3">
                            <label for="email" class="form-label fw-bold">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" 
                                   class="form-control @error('email') is-invalid @enderror" 
                                   value="{{ old('email', $usuario->email) }}" required>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Rol del Usuario -->
                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">Rol en el Sistema <span class="text-danger">*</span></label>
                            @if(Auth::user()->isPrincipal())
                                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="usuario" {{ old('role', $usuario->role) == 'usuario' ? 'selected' : '' }}>👤 Usuario Común (Solo edita su propio perfil)</option>
                                    <option value="admin" {{ old('role', $usuario->role) == 'admin' ? 'selected' : '' }}>⭐ Administrador (Gestiona ventas, compras y usuarios comunes)</option>
                                    <option value="principal" {{ old('role', $usuario->role) == 'principal' ? 'selected' : '' }}>👑 Usuario Principal (Control total del sistema)</option>
                                </select>
                                <div class="form-text text-success">👑 Como Usuario Principal tienes permiso para ascender o cambiar roles.</div>
                            @else
                                <input type="hidden" name="role" value="{{ $usuario->role }}">
                                <input type="text" class="form-control bg-light" value="{{ $usuario->nombre_rol }}" readonly>
                                <div class="form-text text-muted">⚠️ Solo el Usuario Principal puede cambiar o asignar roles de Administrador.</div>
                            @endif
                            @error('role')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contraseña (Opcional) -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label for="password" class="form-label fw-bold">Nueva Contraseña (Opcional)</label>
                                <input type="password" name="password" id="password" minlength="6" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       placeholder="Dejar vacío para conservar actual">
                                <div class="form-text">Mínimo 6 caracteres si se cambia.</div>
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-6">
                                <label for="password_confirmation" class="form-label fw-bold">Confirmar Nueva Contraseña</label>
                                <input type="password" name="password_confirmation" id="password_confirmation" minlength="6" 
                                       class="form-control" 
                                       placeholder="Confirmar nueva contraseña">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-warning px-4 shadow-sm fw-bold">
                                <i class="bi bi-check-lg me-1"></i> Actualizar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
