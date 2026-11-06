<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🍬 Dulcería - Panel de Administración</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    
    <style>
        :root {
            --sb-sidebar-width: 250px;
        }
        body {
            background-color: #f4f6f9;
            overflow-x: hidden;
            font-family: system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }
        .sb-topnav {
            height: 56px;
            z-index: 1040;
        }
        .sb-sidenav {
            width: var(--sb-sidebar-width);
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;
            padding-top: 56px;
            background-color: #1e2229;
            z-index: 1035;
            transition: transform 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
            overflow-y: auto;
            border-right: 1px solid rgba(255, 255, 255, 0.05);
        }
        .sb-sidenav .sb-sidenav-menu {
            padding: 0.5rem 0 2rem;
        }
        .sb-sidenav .sb-sidenav-menu .nav-link {
            color: rgba(255, 255, 255, 0.72);
            padding: 0.65rem 1.15rem;
            display: flex;
            align-items: center;
            border-radius: 8px;
            margin: 2px 10px;
            font-size: 0.92rem;
            font-weight: 500;
            transition: all 0.2s ease;
        }
        .sb-sidenav .sb-sidenav-menu .nav-link:hover {
            color: #fff;
            background-color: rgba(255, 255, 255, 0.08);
            transform: translateX(3px);
        }
        .sb-sidenav .sb-sidenav-menu .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
            box-shadow: 0 4px 8px rgba(13, 110, 253, 0.25);
        }
        .sb-sidenav .sb-sidenav-menu-heading {
            color: rgba(255, 255, 255, 0.35);
            padding: 1.1rem 1.25rem 0.35rem;
            font-size: 0.72rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            font-weight: 700;
        }
        #layoutSidenav_content {
            padding-top: 56px;
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
        }

        /* Fondo oscurecido para pantallas móviles */
        .sidenav-backdrop {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.55);
            z-index: 1030;
            backdrop-filter: blur(2px);
        }

        /* Dispositivos de escritorio (>= 992px) */
        @media (min-width: 992px) {
            .sb-sidenav {
                transform: translateX(0);
            }
            #layoutSidenav_content {
                margin-left: var(--sb-sidebar-width);
            }
            body.sb-sidenav-toggled .sb-sidenav {
                transform: translateX(-100%);
            }
            body.sb-sidenav-toggled #layoutSidenav_content {
                margin-left: 0;
            }
        }

        /* Teléfonos y tabletas (< 992px) */
        @media (max-width: 991.98px) {
            .sb-sidenav {
                transform: translateX(-100%);
                box-shadow: 6px 0 20px rgba(0, 0, 0, 0.35);
            }
            #layoutSidenav_content {
                margin-left: 0 !important;
            }
            body.sb-sidenav-toggled .sb-sidenav {
                transform: translateX(0);
            }
            body.sb-sidenav-toggled .sidenav-backdrop {
                display: block;
            }
            .navbar-brand {
                font-size: 1rem;
            }
            .container-fluid {
                padding-left: 1rem !important;
                padding-right: 1rem !important;
            }
        }

        /* Estilo general para botones de regreso */
        .btn-regresar {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            transition: transform 0.15s ease;
        }
        .btn-regresar:hover {
            transform: translateX(-2px);
        }
    </style>
</head>
<body class="sb-nav-fixed">
    <!-- Barra Superior -->
    <nav class="sb-topnav navbar navbar-expand navbar-dark bg-dark px-2 px-sm-3">
        <!-- Botón Hamburguesa para desplegar/ocultar el menú -->
        <button class="btn btn-link text-white me-2 p-1" id="sidebarToggle" type="button" aria-label="Alternar menú lateral">
            <i class="bi bi-list fs-2"></i>
        </button>

        <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('dashboard') }}">
            🍬 <span class="d-none d-sm-inline ms-2">Dulcería</span>
        </a>
        
        <ul class="navbar-nav ms-auto align-items-center">
            @auth
                {{-- INDICADOR DE SESIONES ACTIVAS --}}
                <li class="nav-item d-none d-md-flex align-items-center me-2">
                    @if(isset($cantidadSesiones))
                        @if($cantidadSesiones == 1)
                            <span class="badge bg-success">1 sesión activa</span>
                        @elseif($cantidadSesiones > 1)
                            <span class="badge bg-warning text-dark">⚠️ {{ $cantidadSesiones }} sesiones activas</span>
                        @endif
                    @endif
                </li>

                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white d-flex align-items-center py-1" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle fs-5 me-1 text-info"></i>
                        <span class="d-none d-sm-inline">{{ Auth::user()->name }}</span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow">
                        <li class="px-3 py-2 border-bottom">
                            <small class="text-muted">Conectado como:</small><br>
                            <strong class="text-dark">{{ Auth::user()->name }}</strong><br>
                            <small class="text-muted text-break">{{ Auth::user()->email }}</small><br>
                            <span class="badge bg-dark mt-1">{{ Auth::user()->nombre_rol }}</span>
                        </li>
                        <li>
                            <a class="dropdown-item py-2" href="{{ route('profile.edit') }}">
                                <i class="bi bi-person-gear me-2 text-primary"></i> Mi Perfil
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item py-2 text-danger">
                                    <i class="bi bi-box-arrow-right me-2"></i> Cerrar Sesión
                                </button>
                            </form>
                        </li>
                    </ul>
                </li>
            @endauth
        </ul>
    </nav>

    <!-- Contenedor Principal con Menú Desplegable -->
    <div id="layoutSidenav">
        <!-- Fondo oscuro táctil para móviles -->
        <div class="sidenav-backdrop" id="sidenavBackdrop"></div>

        <div id="layoutSidenav_nav">
            <!-- Barra Lateral Desplegable -->
            <nav class="sb-sidenav accordion sb-sidenav-dark" id="sidenavAccordion">
                <div class="sb-sidenav-menu">
                    <div class="nav">
                        <div class="sb-sidenav-menu-heading">Principal</div>
                        <a class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                            <i class="bi bi-speedometer2 me-2 fs-5"></i> Panel de Control
                        </a>
                        
                        <div class="sb-sidenav-menu-heading">Dulcería & Catálogo</div>
                        <a class="nav-link {{ request()->routeIs('categorias.*') ? 'active' : '' }}" href="{{ route('categorias.index') }}">
                            <i class="bi bi-tags me-2 fs-5"></i> Categorías
                        </a>
                        <a class="nav-link {{ request()->routeIs('dulces.*') ? 'active' : '' }}" href="{{ route('dulces.index') }}">
                            <i class="bi bi-bag-heart me-2 fs-5"></i> Dulces (Inventario)
                        </a>

                        <div class="sb-sidenav-menu-heading">Transacciones</div>
                        <a class="nav-link {{ request()->routeIs('ventas.index') ? 'active' : '' }}" href="{{ route('ventas.index') }}">
                            <i class="bi bi-cart-check me-2 fs-5 text-success"></i> Punto de Venta
                        </a>
                        <a class="nav-link {{ request()->routeIs('ventas.historial') ? 'active' : '' }}" href="{{ route('ventas.historial') }}">
                            <i class="bi bi-receipt me-2 fs-5 text-success"></i> Historial de Ventas
                        </a>
                        <a class="nav-link {{ request()->routeIs('compras.index') ? 'active' : '' }}" href="{{ route('compras.index') }}">
                            <i class="bi bi-box-seam me-2 fs-5 text-warning"></i> Registro de Compras
                        </a>
                        <a class="nav-link {{ request()->routeIs('compras.historial') ? 'active' : '' }}" href="{{ route('compras.historial') }}">
                            <i class="bi bi-journal-text me-2 fs-5 text-warning"></i> Historial de Compras
                        </a>

                        <div class="sb-sidenav-menu-heading">Administración</div>
                        @if(Auth::user()->isAdmin())
                        <a class="nav-link {{ request()->routeIs('usuarios.*') ? 'active' : '' }}" href="{{ route('usuarios.index') }}">
                            <i class="bi bi-people me-2 fs-5"></i> Usuarios
                        </a>
                        @endif
                        <a class="nav-link {{ request()->routeIs('profile.*') ? 'active' : '' }}" href="{{ route('profile.edit') }}">
                            <i class="bi bi-person-gear me-2 fs-5"></i> Mi Perfil
                        </a>
                    </div>
                </div>
            </nav>
        </div>

        <!-- Contenido de la Página -->
        <div id="layoutSidenav_content">
            <main>
                <div class="container-fluid px-3 px-md-4 py-3">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ Control del Menú Desplegable (Hamburguesa) para Móvil y Escritorio -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidenavBackdrop = document.getElementById('sidenavBackdrop');

        if (sidebarToggle) {
            sidebarToggle.addEventListener('click', function (e) {
                e.preventDefault();
                document.body.classList.toggle('sb-sidenav-toggled');
            });
        }

        if (sidenavBackdrop) {
            sidenavBackdrop.addEventListener('click', function () {
                document.body.classList.remove('sb-sidenav-toggled');
            });
        }

        // Cerrar menú al hacer clic en un enlace en versión móvil
        document.querySelectorAll('.sb-sidenav .nav-link').forEach(function(link) {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    document.body.classList.remove('sb-sidenav-toggled');
                }
            });
        });
    });
    </script>

    <!-- ✅ Detección de inactividad (5 minutos) y sesión simultánea -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const TIEMPO_INACTIVIDAD_MS = 5 * 60 * 1000; // 5 minutos
        let ultimaActividad = Date.now();
        let sesionCerrada = false;

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

        // Verificación periódica en servidor
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

    <!-- ✅ Validación automática instantánea en formularios -->
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        function validarCampo(input) {
            if (input.type === 'hidden' || input.type === 'submit' || input.type === 'button') return true;

            const valor = input.value.trim();
            let esValido = true;
            let mensaje = '';

            if (input.hasAttribute('required') && valor === '') {
                esValido = false;
                mensaje = '⚠️ Este campo es obligatorio y no puede estar vacío.';
            } else if (input.dataset.onlyLetters === 'true' && valor !== '') {
                const regexSoloLetras = /^[\p{L}\s]+$/u;
                if (!regexSoloLetras.test(valor)) {
                    esValido = false;
                    mensaje = '⚠️ Solo se permiten letras (no se permiten números ni símbolos).';
                }
            } else if (input.type === 'email' && valor !== '') {
                const regexEmail = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                if (!regexEmail.test(valor)) {
                    esValido = false;
                    mensaje = '⚠️ Ingresa un correo electrónico válido.';
                }
            } else if (input.type === 'number' && input.hasAttribute('min') && valor !== '') {
                const minVal = parseFloat(input.getAttribute('min'));
                if (parseFloat(valor) < minVal) {
                    esValido = false;
                    mensaje = '⚠️ El valor no puede ser menor a ' + minVal + '.';
                }
            } else if (input.type === 'password' && input.hasAttribute('minlength') && valor !== '') {
                const minLen = parseInt(input.getAttribute('minlength'));
                if (valor.length < minLen) {
                    esValido = false;
                    mensaje = '⚠️ Debe tener al menos ' + minLen + ' caracteres.';
                }
            }

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
