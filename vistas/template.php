<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Empresarial</title>
    <link rel="icon" href="assets/img/logo.png" type="image/png" sizes="16x16">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.css" rel="stylesheet">
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <!-- FullCalendar CSS -->
    <!-- <link href='https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css' rel='stylesheet'> -->
    <link href='https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css' rel='stylesheet'>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link href="assets/css/template.css" rel="stylesheet">
</head>

<body>
    <?php if (isset($_SESSION['id_usuario'])): ?>
        <?php
        $rol = $_SESSION['rol']; // Obtener el rol del usuario
        $esIngeniero = ($rol === 'ingeniero');
        ?>
        <nav class="navbar navbar-expand-lg navbar-dark bg-success-2 text-light shadow-sm position-sticky" style="z-index: 9;">
            <div class="container flex justify-content-between">
                <a class="navbar-brand logo-header" href="?controlador=paginas&accion=inicio">
                    <img src="assets/img/logo-2.png" alt="Logo" width="50" height="50" class="d-inline-block align-text-top me-2">
                    <span class="text-light">Colegio Público de Ingenieros de Formosa</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="text-uppercase " id="navbarNav">
                    <ul class="navbar-nav me-auto">
                        <li class="nav-item">
                            <a class="nav-link nav-link-theme" href="?controlador=paginas&accion=inicio">
                                Inicio
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-theme" href="?controlador=reservas&accion=calendario">
                                Calendario
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-theme" href="?controlador=reservas&accion=listar">
                                Mis Reservas
                            </a>
                        </li>
                        <li class="nav-item py-2 py-lg-1 col-12 col-lg-auto">
                            <div class="vr d-none d-lg-flex h-100 mx-lg-2 text-white"></div>
                            <hr class="d-lg-none my-2 text-white-50">
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link nav-link-theme dropdown-toggle" href="#" id="navbarDropdownAdmin" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-user-circle me-1"></i>
                                <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?>
                            </a>
                            <ul class="dropdown-menu" aria-labelledby="navbarDropdownAdmin">
                                <?php if (!$esIngeniero): ?>
                                    <li>
                                        <a class="dropdown-item" href="?controlador=usuarios&accion=listar">
                                            <i class="bi bi-person-lines-fill"></i>
                                            <span class="ms-2">Gestión de Usuarios</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <a class="dropdown-item" href="?controlador=usuarios&accion=logout">
                                        <i class="bi bi-box-arrow-right"></i>
                                        <span class="ms-2">Cerrar Sesión</span>
                                    </a>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>
        </nav>
    <?php endif; ?>

    <main class="container-fluid mb-5" style="padding-inline: 0px;">
        <div class="col-12">
            <?php include_once("ruteador.php"); ?>
        </div>
    </main>
    <footer class="footer mt-auto py-3 bg-success">
        <div class="container">
            <span class="text-white">&copy; <?php echo date('Y'); ?>. Todos los derechos reservados. Empresa SoftForm</span>
        </div>
    </footer>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Bootstrap Bundle JS (incluye Popper) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- DataTables -->
    <script src="https://cdn.datatables.net/v/dt/dt-2.2.2/datatables.min.js"></script>
    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- FullCalendar JS -->
    <script src='https://cdn.jsdelivr.net/npm/fullcalendar/index.global.min.js'></script>
    <script src='./utils/js/fullcalendar.js'></script>

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