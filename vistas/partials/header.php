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
            <li><a class="dropdown-item" href="?controlador=reservas&accion=calendario"><i class="bi bi-calendar-event me-2"></i> Salón</a></li>
            <li><a class="dropdown-item" href="?controlador=reservas&accion=listar" disabled> <i class="bi bi-calendar-event-fill me-2"></i>Cancha</a></li>
          </ul>
        </li>
        
        <?php if (!$esIngeniero): ?>
        <li class="nav-item dropdown text-uppercase">
          <a class="nav-link dropdown-toggle text-uppercase <?php echo $controlador == 'reservas' && ($accion == 'listar' || $accion == 'listar&tipo=2') ? 'active' : ''; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="true">
            Gestión
          </a>
          <ul class="dropdown-menu" data-bs-popper="static">
            <li><a class="dropdown-item" href="?controlador=reservas&accion=listar&tipo=1"><i class="bi bi-calendar2-event me-2"></i> Mis Reservas</a></li>
            <li><a class="dropdown-item" href="?controlador=reservas&accion=listar&tipo=2"> <i class="bi bi-calendar-range-fill me-2"></i> Gestionar Reservas</a></li>
          </ul>
        </li>
        <?php else: ?>
          <li class="nav-item">
            <a class="nav-link text-uppercase <?php echo $controlador == 'reservas' && $accion == 'listar' ? 'active' : ''; ?>"
              aria-current="page" href="?controlador=reservas&accion=listar"><i class="bi bi-calendar2-event me-2"></i>Mis Reservas</a>
          </li>         
        <?php endif; ?>
        <!-- <li class="nav-item py-2 py-lg-1 col-12 col-lg-auto">
          <div class="vr d-none d-lg-flex h-100 mx-lg-2 text-white"></div>
          <hr class="d-lg-none my-2 text-white-50">
        </li> -->

        <?php if (!$esIngeniero): ?>
          <li class="nav-item dropdown text-uppercase">
            <a class="nav-link dropdown-toggle <?php echo $controlador == 'configuracion' ? 'active' : ''; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="true">
              <i class="bi bi-gear-fill"></i>
              Configuración
            </a>
            <ul class="dropdown-menu" data-bs-popper="static">
              <li>
                <a class="dropdown-item" href="?controlador=usuarios&accion=listar">
                  <i class="bi bi-person-lines-fill"></i>
                  <span class="ms-2">Gestión de Usuarios</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="?controlador=configuracion&accion=listarDocumentos">
                  <i class="bi bi-file-earmark-pdf"></i>
                  <span class="ms-2">Gestión de Documentos</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="?controlador=configuracion&accion=listarAranceles">
                  <i class="bi bi-cash-coin"></i>
                  <span class="ms-2">Gestión de Aranceles</span>
                </a>
              </li>
              <li>
                <a class="dropdown-item" href="?controlador=reportes&accion=listar">
                  <i class="bi bi-cash-coin"></i>
                  <span class="ms-2">Gestión de Reportes</span>
                </a>
              </li>
            <?php endif; ?>
          </ul>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link text-uppercase dropdown-toggle <?php echo $controlador == 'usuarios' && $accion == 'logout' ? 'active' : ''; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="true">
            <i class="fas fa-user-circle me-1"></i>
            <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?>
          </a>
          <ul class="dropdown-menu" data-bs-popper="static">
            <li>
              <a class="dropdown-item" href="?controlador=usuarios&accion=logout">
                <i class="bi bi-box-arrow-right"></i>
                <span class="ms-2">Cerrar Sesión</span>
              </a>
            </li>
          </ul>
        </li>

        <!-- Notificaciones dropdown -->
        <?php if (!$esIngeniero): ?>
          <li class="nav-item dropdown">
            <a
              class="nav-link dropdown-toggle <?php echo $controlador == 'notificaciones' && $accion == 'listar' ? 'active' : ''; ?>"
              role="button" data-bs-toggle="dropdown" aria-expanded="true" id="notificaciones-header-link">
              <i class="fas fa-bell"></i>
              <span class="ms-2 text-uppercase">Notificaciones</span>
            </a>
            <ul class="dropdown-menu pb-0" data-bs-popper="static" id="notificaciones-header">
              <li>
                <a class="dropdown-item border-bottom" href="">
                  <i class="bi bi-x-circle-fill"></i>
                  No hay notificaciones
                </a>
              </li>
              <li>
                <a class="dropdown-item border-bottom bg-success-2 text-white" style="border-radius: 0 0 6px 6px;" href="?controlador=notificaciones&accion=listar">
                  <i class="bi bi-bell-fill"></i>
                  <span class="ms-2 ">Mostrar Todas</span>
                </a>
              </li>
            </ul>
          </li>
        <?php endif; ?>
        <!-- Fin Notificaciones dropdown -->

      </ul>
    </div>
  </div>
</nav>