@extends('layouts.app')

@section('content')
<div class="py-2">
    <div class="mb-2">
        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Volver al Panel
        </a>
    </div>

    <div class="d-flex justify-content-between flex-wrap align-items-center mb-4 gap-2">
        <div>
            <h2 class="h3 fw-bold mb-0 text-dark"><i class="bi bi-people me-2 text-primary"></i>Gestión de Usuarios</h2>
            <p class="text-muted small mb-0">Control de perfiles, roles y permisos de acceso al sistema.</p>
        </div>
        <a href="{{ route('usuarios.create') }}" class="btn btn-primary shadow-sm">
            <i class="bi bi-person-plus me-1"></i> Nuevo Usuario
        </a>
    </div>

    {{-- Mensajes de estado --}}
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="bi bi-shield-slash-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Tabla Responsiva de Usuarios --}}
    <div class="card border-0 shadow-sm rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Correo Electrónico</th>
                            <th>Rol / Permisos</th>
                            <th>Fecha Registro</th>
                            <th class="text-end pe-3">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usuarios as $u)
                        <tr>
                            <td class="fw-bold text-muted">#{{ $u->id }}</td>
                            <td>
                                <div class="fw-bold text-dark">{{ $u->name }}</div>
                                @if(Auth::id() === $u->id)
                                    <span class="badge bg-info text-dark" style="font-size: 0.68rem;">(Tú)</span>
                                @endif
                            </td>
                            <td>{{ $u->email }}</td>
                            <td>
                                @if($u->isPrincipal())
                                    <span class="badge bg-warning text-dark px-2 py-1 shadow-sm">
                                        👑 Usuario Principal
                                    </span>
                                @elseif($u->role === 'admin')
                                    <span class="badge bg-primary px-2 py-1 shadow-sm">
                                        ⭐ Administrador
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-2 py-1">
                                        👤 Usuario Común
                                    </span>
                                @endif
                            </td>
                            <td class="small text-muted">{{ $u->created_at->format('d/m/Y') }}</td>
                            <td class="text-end pe-3">
                                <a href="{{ route('usuarios.show', $u) }}" class="btn btn-sm btn-outline-info" title="Ver detalles">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('usuarios.edit', $u) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                    <i class="bi bi-pencil"></i>
                                </a>

                                {{-- Botón de eliminación con reglas de permisos --}}
                                @if(Auth::id() !== $u->id && !$u->isPrincipal())
                                    @if(Auth::user()->isPrincipal() || (Auth::user()->isAdmin() && $u->isComun()))
                                        <form action="{{ route('usuarios.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('¿Seguro que deseas eliminar al usuario {{ $u->name }}?')">
                                            @csrf 
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Eliminar usuario">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
