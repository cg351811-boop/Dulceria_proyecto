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
                <div class="card-header bg-primary text-white py-3">
                    <h5 class="mb-0 fw-bold"><i class="bi bi-person-plus me-2"></i>Crear Nuevo Usuario</h5>
                </div>
                <div class="card-body p-4">
                    {{-- Errores de validación generales --}}
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

                    <form action="{{ route('usuarios.store') }}" method="POST" id="usuarioForm" novalidate>
                        @csrf

                        <!-- Nombre Completo -->
                        <div class="mb-3">
                            <label for="name" class="form-label fw-bold">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" 
                                   class="form-control @error('name') is-invalid @enderror" 
                                   value="{{ old('name') }}" required data-only-letters="true" 
                                   placeholder="Ej: Juan Pérez">
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
                                   value="{{ old('email') }}" required 
                                   placeholder="usuario@ejemplo.com">
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Asignación de Rol de Usuario -->
                        <div class="mb-3">
                            <label for="role" class="form-label fw-bold">Rol en el Sistema <span class="text-danger">*</span></label>
                            @if(Auth::user()->isPrincipal())
                                <select name="role" id="role" class="form-select @error('role') is-invalid @enderror" required>
                                    <option value="usuario" {{ old('role', 'usuario') == 'usuario' ? 'selected' : '' }}>👤 Usuario Común (Solo edita su propio perfil)</option>
                                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>⭐ Administrador (Gestiona ventas, compras y usuarios comunes)</option>
                                </select>
                                <div class="form-text text-success">👑 Como Usuario Principal puedes otorgar el rol de Administrador.</div>
                            @else
                                <input type="hidden" name="role" value="usuario">
                                <input type="text" class="form-control bg-light" value="👤 Usuario Común" readonly>
                                <div class="form-text text-muted">⚠️ Solo el Usuario Principal tiene autorización para asignar roles de Administrador.</div>
                            @endif
                            @error('role')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Contraseña -->
                        <div class="row g-3 mb-4">
                            <div class="col-sm-6">
                                <label for="password" class="form-label fw-bold">Contraseña <span class="text-danger">*</span></label>
                                <input type="password" name="password" id="password" minlength="6" 
                                       class="form-control @error('password') is-invalid @enderror" required 
                                       placeholder="Mínimo 6 caracteres">
                                @error('password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-sm-6">
                                <label for="password_confirmation" class="form-label fw-bold">Confirmar Contraseña <span class="text-danger">*</span></label>
                                <input type="password" name="password_confirmation" id="password_confirmation" minlength="6" 
                                       class="form-control" required 
                                       placeholder="Repite la contraseña">
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary px-4 shadow-sm">
                                <i class="bi bi-save me-1"></i> Guardar Usuario
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
