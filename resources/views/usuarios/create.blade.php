@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <h4 class="mb-0">Crear Nuevo Usuario</h4>
                </div>
                <div class="card-body">
                    {{-- Errores de validación --}}
                    @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                                <p class="mb-1">{{ $error }}</p>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('usuarios.store') }}" method="POST" id="usuarioForm" novalidate>
                        @csrf
                        <div class="mb-3">
                            <label for="name" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" required data-only-letters="true">
                            <div class="form-text">Solo se permiten letras, no números.</div>
                            @error('name')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Correo Electrónico <span class="text-danger">*</span></label>
                            <input type="email" name="email" id="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email') }}" required>
                            @error('email')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password" id="password" minlength="6" class="form-control @error('password') is-invalid @enderror" required>
                            <div class="form-text">Mínimo 6 caracteres.</div>
                            @error('password')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmar Contraseña <span class="text-danger">*</span></label>
                            <input type="password" name="password_confirmation" id="password_confirmation" minlength="6" class="form-control" required>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary">Guardar Usuario</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

{{-- Validación JavaScript --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('usuarioForm');
    const nameInput = document.getElementById('name');
    const emailInput = document.getElementById('email');
    const passInput = document.getElementById('password');
    const passConfInput = document.getElementById('password_confirmation');

    form.addEventListener('submit', function (e) {
        let valido = true;

        if (!nameInput.value.trim()) {
            nameInput.classList.add('is-invalid');
            valido = false;
        }

        if (!emailInput.value.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
            emailInput.classList.add('is-invalid');
            valido = false;
        }

        if (!passInput.value || passInput.value.length < 6) {
            passInput.classList.add('is-invalid');
            valido = false;
        }

        if (passInput.value !== passConfInput.value) {
            passConfInput.classList.add('is-invalid');
            valido = false;
        }

        if (!valido) e.preventDefault();
    });
});
</script>