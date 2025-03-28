<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Empresarial</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- FullCalendar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.css" rel="stylesheet">
    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        main {
            flex: 1;
            /* Hace que el contenido principal ocupe el espacio disponible */
        }
        footer {
            text-align: center;
            padding: 15px;
            background-color: #f8f9fa;
            border-top: 2px solid #dee2e6;
            font-weight: bold;
            font-size: 14px;
            /* Se ajusta bien en móviles */
        }
        .hero {
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://source.unsplash.com/random/1920x1080/?event-hall');
            background-size: cover;
            color: white;
            padding: 100px 0;
            margin-bottom: 30px;
            text-align: center;
        }
        .calendar-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
            padding: 20px;
            margin-bottom: 30px;
        }

        @media (min-width: 768px) {
            footer {
                font-size: 16px;
                /* Aumenta el tamaño del texto en pantallas más grandes */
                padding: 20px;
            }
        }
    </style>
</head>

<body>
    <?php if (isset($_SESSION['id_usuario'])): ?>
        <?php
        $rol = $_SESSION['rol']; // Obtener el rol del usuario
        $esIngeniero = ($rol === 'ingeniero');
        ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <div class="container">
                <a class="navbar-brand" href="?controlador=paginas&accion=inicio">
                    <i class="fas fa-building me-2"></i>Colegio de Ingenieros
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link" href="?controlador=paginas&accion=inicio">
                                <i class="fas fa-home me-1"></i>Inicio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?controlador=reservas&accion=calendario">
                                <i class="fas fa-calendar-alt me-1"></i>Calendario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="?controlador=reservas&accion=listar">
                                <i class="fas fa-ticket-alt me-1"></i>Mis Reservas
                            </a>
                        </li>
                        
                        <?php if (!$esIngeniero): ?>
                            <li class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-cogs me-1"></i>Administración
                                </a>
                                <ul class="dropdown-menu" aria-labelledby="navbarDropdownAdmin">
                                    <li>
                                        <a class="dropdown-item" href="?controlador=usuarios&accion=listar">
                                            <i class="fas fa-users me-1"></i>Gestión de Usuarios
                                        </a>
                                    </li>
                                    <!-- <li>
                                        <a class="dropdown-item" href="?controlador=reservas&accion=administrar">
                                            <i class="fas fa-clipboard-list me-1"></i>Gestión de Reservas
                                        </a>
                                    </li> -->
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>
                    <div class="navbar-nav">
                        <span class="nav-item nav-link text-light">
                            <i class="fas fa-user-circle me-1"></i>
                            Bienvenido, <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?>
                        </span>
                        <a class="nav-link" href="?controlador=usuarios&accion=logout">
                            <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                        </a>
                    </div>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <main class="container">
        <div class="row">
            <div class="col-12">
                <?php include_once("ruteador.php"); ?>
            </div>
        </div>
    </main>
    <footer class="footer mt-auto py-3 bg-light">
        <div class="container">
            <span class="text-muted">&copy; <?php echo date('Y'); ?>. Todos los derechos reservados. Empresa SoftForm</span>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle JS (incluye Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/main.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.0/locales/es.js"></script>
    <script src="utils/js/fullcalendar.js"></script>
    <!-- <script src="utils/js/datatables.js"></script> -->

    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2({
                theme: "classic"
            });

            $('#myTable').DataTable({
                language: {
                    url: 'https://cdn.datatables.net/plug-ins/1.10.21/i18n/Spanish.json'
                },
                columnDefs: [{
                    targets: '_all',
                    defaultContent: '-'
                }], // Placeholder para todas las columnas
                responsive: true, // Para tablas adaptables
                order: [
                    [0, 'desc']
                ] // Ordenar por la primera columna (ID)
            });
        });
    </script>

</body>

</html>