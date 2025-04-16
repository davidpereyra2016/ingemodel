<?php

include_once("controladores/controlador_notificaciones.php");
$objControladorNotificaciones = new ControladorNotificaciones();
$notificaciones = $objControladorNotificaciones->listaPreviaNotificaciones();

// Mostrar los ultimos 5 mensajes de notificaciones
$notificaciones = array_slice($notificaciones, -5, 5, true);

// Contar la cantidad de notificaciones no leídas
$countNotificaciones = 0;
foreach ($notificaciones as $notificacion) {
  if ($notificacion['leido'] == 0) {
    $countNotificaciones++;
  }
}
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-success-2" style="z-index: 9;">
  <div class="container flex justify-content-between">

    <a class="navbar-brand logo-header" href="?controlador=paginas&accion=inicio">
      <img src="assets/img/logo-2.png" alt="Logo" width="50" height="50" class="d-inline-block align-text-top me-2">
      <span class="text-light">Colegio Público de Ingenieros de Formosa</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>


    <div class="collapse navbar-collapse ms-auto" id="navbarNavDropdown" style="flex-grow: 0;">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link text-uppercase <?php echo $accion == 'inicio' ? 'active' : ''; ?>"
            aria-current="page" href="?controlador=paginas&accion=inicio">Inicio</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link text-uppercase dropdown-toggle <?php echo $accion == 'calendario' || $accion == 'futbol' ? 'active' : ''; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="true">
            Reservas
          </a>
          <ul class="dropdown-menu text-uppercase" data-bs-popper="static">
            <li><a class="dropdown-item" href="?controlador=reservas&accion=calendario">Salón</a></li>
            <li><a class="dropdown-item" href="?controlador=reservas&accion=listar">Cancha</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link text-uppercase <?php echo $controlador == 'reservas' && $accion == 'listar' ? 'active' : ''; ?>" href="?controlador=reservas&accion=listar">
            Mis Reservas
          </a>
        </li>

        <li class="nav-item py-2 py-lg-1 col-12 col-lg-auto">
          <div class="vr d-none d-lg-flex h-100 mx-lg-2 text-white"></div>
          <hr class="d-lg-none my-2 text-white-50">
        </li>

        <li class="nav-item dropdown text-uppercase">
          <a class="nav-link dropdown-toggle <?php echo $controlador == 'usuarios' && $accion == 'listar' ? 'active' : ''; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="true">
            <i class="fas fa-user-circle me-1"></i>
            <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?>
          </a>
          <ul class="dropdown-menu" data-bs-popper="static">
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

        <!-- Notificaciones dropdown -->
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo $controlador == 'notificaciones' && $accion == 'listar' ? 'active' : ''; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="true">
            <i class="fas fa-bell"></i>
            <span class="ms-2 text-uppercase">Notificaciones</span>
            <?php if ($countNotificaciones > 0): ?>
              <span class="badge bg-danger rounded-pill"><?= $countNotificaciones ?></span>
            <?php endif; ?>
          </a>
          <ul class="dropdown-menu pb-0" data-bs-popper="static">
            <?php if ($notificaciones): ?>
              <?php foreach ($notificaciones as $notificacion): ?>
                <li>
                  <a class="dropdown-item border-bottom d-flex align-items-center gap-2" href="?controlador=reservas&accion=ver&id=<?= $notificacion['id_reserva']; ?>">
                    <i class="bi bi-eye"></i>
                    <span class="ms-2"><?php
                                        // Transformar la fecha a un formato legible ej: 18 de abril
                                        $fecha = new DateTime($notificacion['fecha']);
                                        // setlocale(LC_TIME, 'es_ES.UTF-8', 'Spanish_Spain.1252', 'Spanish_Spain', 'Spanish');
                                        $fecha->setTimezone(new DateTimeZone('America/Argentina/Buenos_Aires'));
                                        // Establecer la localización a español
                                        setlocale(LC_TIME, 'es_ES.UTF-8');
                                        // echo $fecha->format('d \d\e F'); // Formato: 18 de abril

                                        // concatenar el mensaje y fecha
                                        echo $notificacion['mensaje'] . ' <br> ' . $fecha->format('d \d\e F');
                                        ?>
                    </span>
                    <span class="badge <?php echo $notificacion['leido'] == 0 ? 'bg-success' : 'bg-secondary'; ?> rounded-pill">
                      <?= $notificacion['leido'] == 0 ? 'Nuevo' : 'Leído' ?>
                    </span>
                  </a>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li>
                <a class="dropdown-item border-bottom" href="">
                  <i class="bi bi-x-circle-fill"></i>
                  No hay notificaciones
                </a>
              </li>
            <?php endif; ?>
            <li>
              <a class="dropdown-item border-bottom bg-success-2 text-white" style="border-radius: 0 0 6px 6px;" href="?controlador=notificaciones&accion=listar">
                <i class="bi bi-bell-fill"></i>
                <span class="ms-2 ">Mostrar Todas</span>
              </a>
            </li>
          </ul>
        </li>
        <!-- Fin Notificaciones dropdown -->

      </ul>
    </div>
  </div>
</nav>