<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Proyecto - Panel de Administración</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sb-sidebar-width: 225px;
        }
        body {
            background-color: #f8f9fa;
        }
        .sb-topnav {
            height: 56px;
            z-index: 1030;
        }
        .sb-sidenav {
            width: var(--sb-sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 56px;
            background-color: #212529;
        }
        .sb-sidenav .sb-sidenav-menu {
            padding: 0;
        }
        .sb-sidenav .sb-sidenav-menu .nav-link {
            color: rgba(255,255,255,0.75);
            padding: 0.75rem 1.5rem;
        }
        .sb-sidenav .sb-sidenav-menu .nav-link:hover {
            color: #fff;
        }
        .sb-sidenav .sb-sidenav-menu-heading {
            color: rgba(255,255,255,0.4);
            padding: 1rem 1.5rem 0.5rem;
            font-size: 0.75rem;
            text-transform: uppercase;
        }
        #layoutSidenav_content {
            margin-left: var(--sb-sidebar-width);
            padding-top: 56px;
            min-height: 100vh;
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <!-- Barra Superior -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark">
        <a class="navbar-brand ps-3" href="{{ route('dashboard') }}"> Mi Proyecto</a>
        
        <ul class="navbar-nav ms-auto me-3">
            @auth
                {{-- INDICADOR DE SESIONES ACTIVAS --}}
                <li class="nav-item d-flex align-items-center me-2">
                    @if(isset($cantidadSesiones))
                        @if($cantidadSesiones == 1)
                            <span class="badge bg-success"> 1 sesión activa</span>
                        @elseif($cantidadSesiones > 1)
                            <span class="badge bg-warning text-dark">⚠️ {{ $cantidadSesiones }} sesiones activas</span>
                        @endif
                    @endif
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        👤 {{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li class="px-3 py-2 border-bottom">
                            <small class="text-muted">Correo:</small><br>
                            <strong>{{ Auth::user()->email }}</strong>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                👤 Mi Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    🚪 Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth
        </ul>
    </nav>

    <!-- Contenedor Principal -->
    <div id="layoutSidenav">
        <div id="layoutSidenav_nav">
            <!-- Barra Lateral -->
            <nav class="sb-sidenav accordion sb-sidenav-dark">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Principal</div>
                        <a class="nav-link" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2"></i> Panel
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Dulcería</div>
                        <a class="nav-link" href="{{ route('categorias.index') }}">
                            <i class="bi bi-tags me-2"></i> Categorías
                        </a>
                        <a class="nav-link" href="{{ route('dulces.index') }}">
                            <i class="bi bi-bag-heart me-2"></i> Dulces (Inventario)
                        </a>

                        <div class="sb-sidenav-menu-heading">Administración</div>
                        <a class="nav-link" href="{{ route('usuarios.index') }}">
                            <i class="bi bi-people me-2"></i> Usuarios
                        </a>
                        <a class="nav-link" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person-gear me-2"></i> Mi Perfil
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Contenido de la Página -->
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-4">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ Detección de inactividad (5 minutos) y sesión simultánea -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const TIEMPO_INACTIVIDAD_MS = 5 * 60 * 1000; // 5 minutos = 300,000 ms
        let ultimaActividad = Date.now();
        let sesionCerrada = false;

        // Restablecer el contador de inactividad con cualquier acción del usuario
        function registrarActividad() {
            ultimaActividad = Date.now();
        }

        ['mousemove', 'mousedown', 'keydown', 'scroll', 'touchstart', 'click'].forEach(function(evento) {
            window.addEventListener(evento, registrarActividad, { passive: true });
        });

        // Verificación local de inactividad cada segundo
        setInterval(function() {
            if (sesionCerrada) return;

            const tiempoTranscurrido = Date.now() - ultimaActividad;
            if (tiempoTranscurrido >= TIEMPO_INACTIVIDAD_MS) {
                sesionCerrada = true;
                window.location.replace('{{ route('login') }}?inactividad=1');
            }
        }, 1000);

        // Verificación periódica en servidor (sesión en otro dispositivo o expiración)
        let verificando = false;
        function verificarSesionEnServidor() {
            if (sesionCerrada || verificando) return;
            verificando = true;

            fetch('{{ route('session.check') }}', {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            })
            .then(function(respuesta) {
                if (respuesta.status === 401) {
                    sesionCerrada = true;
                    return respuesta.json().then(function(datos) {
                        if (datos && datos.status === 'session_timeout') {
                            window.location.replace('{{ route('login') }}?inactividad=1');
                        } else {
                            window.location.replace('{{ route('login') }}?sesion_cerrada=1');
                        }
                    }).catch(function() {
                        window.location.replace('{{ route('login') }}?sesion_cerrada=1');
                    });
                } else if (respuesta.redirected || (respuesta.url && respuesta.url.includes('/login'))) {
                    sesionCerrada = true;
                    window.location.replace('{{ route('login') }}?inactividad=1');
                } else {
                    verificando = false;
                    setTimeout(verificarSesionEnServidor, 3000);
                }
            })
            .catch(function() {
                verificando = false;
                setTimeout(verificarSesionEnServidor, 5000);
            });
        }

        setTimeout(verificarSesionEnServidor, 3000);
    });
    </script>

    <!-- ✅ Validación automática instantánea en formularios al no tener campos llenos o con errores -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        function validarCampo(input) {
            if (input.type === 'hidden' || input.type === 'submit' || input.type === 'button') return true;

            const valor = input.value.trim();
            let esValido = true;
            let mensaje = '';

            // 1. Campo obligatorio vacío
            if (input.hasAttribute('required') && valor === '') {
                esValido = false;
                mensaje = '⚠️ Este campo es obligatorio y no puede estar vacío.';
            }
            // 2. Campo que solo permite letras
            else if (input.dataset.onlyLetters === 'true' && valor !== '') {
                const regexSoloLetras = /^[\p{L}\s]+$/u;
                if (!regexSoloLetras.test(valor)) {
                    esValido = false;
                    mensaje = '⚠️ Solo se permiten letras (no se permiten números ni símbolos).';
                }
            }
            // 3. Email
            else if (input.type === 'email' && valor !== '') {
                const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!regexEmail.test(valor)) {
                    esValido = false;
                    mensaje = '⚠️ Ingresa un correo electrónico válido.';
                }
            }
            // 4. Mínimo numérico (precio, stock)
            else if (input.type === 'number' && input.hasAttribute('min') && valor !== '') {
                const minVal = parseFloat(input.getAttribute('min'));
                if (parseFloat(valor) < minVal) {
                    esValido = false;
                    mensaje = '⚠️ El valor no puede ser menor a ' + minVal + '.';
                }
            }
            // 5. Contraseña mínima
            else if (input.type === 'password' && input.hasAttribute('minlength') && valor !== '') {
                const minLen = parseInt(input.getAttribute('minlength'));
                if (valor.length < minLen) {
                    esValido = false;
                    mensaje = '⚠️ Debe tener al menos ' + minLen + ' caracteres.';
                }
            }

            // Buscar o crear contenedor de feedback
            let feedback = input.parentNode.querySelector('.auto-invalid-feedback');
            if (!feedback) {
                feedback = document.createElement('div');
                feedback.className = 'auto-invalid-feedback text-danger small mt-1';
                input.parentNode.appendChild(feedback);
            }

            if (!esValido) {
                input.classList.add('is-invalid');
                input.classList.remove('is-valid');
                feedback.textContent = mensaje;
                feedback.style.display = 'block';
            } else if (valor !== '') {
                input.classList.remove('is-invalid');
                input.classList.add('is-valid');
                feedback.style.display = 'none';
            } else {
                input.classList.remove('is-invalid');
                input.classList.remove('is-valid');
                feedback.style.display = 'none';
            }

            return esValido;
        }

        // Aplicar a todos los formularios del sistema
        document.querySelectorAll('form').forEach(function(form) {
            const inputs = form.querySelectorAll('input, select, textarea');

            inputs.forEach(function(input) {
                input.addEventListener('input', function() { validarCampo(input); });
                input.addEventListener('change', function() { validarCampo(input); });
                input.addEventListener('blur', function() { validarCampo(input); });
            });

            form.addEventListener('submit', function(e) {
                let formValido = true;
                let primerInvalido = null;

                inputs.forEach(function(input) {
                    const valido = validarCampo(input);
                    if (!valido) {
                        formValido = false;
                        if (!primerInvalido) primerInvalido = input;
                    }
                });

                if (!formValido) {
                    e.preventDefault();
                    e.stopPropagation();
                    if (primerInvalido) {
                        primerInvalido.focus();
                    }
                }
            });
        });
    });
    </script>
</body>
</html>