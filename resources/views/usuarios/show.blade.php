@extends('layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Detalles del Usuario</h2>
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
            Volver a la Lista
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <ul class="list-group list-group-flush">
                <li class="list-group-item">
                    <strong>ID:</strong>
                    {{ $usuario->id }}
                </li>
                <li class="list-group-item">
                    <strong>Nombre:</strong>
                    {{ $usuario->name }}
                </li>
                <li class="list-group-item">
                    <strong>Correo Electrónico:</strong>
                    {{ $usuario->email }}
                </li>
                <li class="list-group-item">
                    <strong>Fecha de creación:</strong>
                    {{ $usuario->created_at->format('d/m/Y H:i:s') }}
                </li>
                <li class="list-group-item">
                    <strong>Última actualización:</strong>
                    {{ $usuario->updated_at->format('d/m/Y H:i:s') }}
                </li>
            </ul>
        </div>
        <div class="card-footer">
            <a href="{{ route('usuarios.edit', $usuario) }}" class="btn btn-warning">Editar</a>
            <form action="{{ route('usuarios.destroy', $usuario) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Eliminar este usuario?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn btn-danger">Eliminar</button>
            </form>
        </div>
    </div>
</div>
@endsection