<nav class="navbar navbar-expand-lg navbar-dark bg-success-2" style="z-index: 9;">
  <div class="container flex justify-content-between">

    <a class="navbar-brand logo-header" href="?controlador=paginas&accion=inicio">
      <img src="assets/img/logo-2.png" alt="Logo" width="50" height="50" class="d-inline-block align-text-top me-2">
      <span class="text-light">Colegio Público de Ingenieros de Formosa</span>
    </a>

    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse text-uppercase ms-auto" id="navbarNavDropdown" style="flex-grow: 0;">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link <?php echo $accion == 'inicio' ? 'active' : ''; ?>"
            aria-current="page" href="?controlador=paginas&accion=inicio">Inicio</a>
        </li>
        <li class="nav-item dropdown">
          <a class="nav-link dropdown-toggle <?php echo $accion == 'calendario' || $accion == 'futbol' ? 'active' : ''; ?>" role="button" data-bs-toggle="dropdown" aria-expanded="true">
            Reservas
          </a>
          <ul class="dropdown-menu" data-bs-popper="static">
            <li><a class="dropdown-item" href="?controlador=reservas&accion=calendario">Salón</a></li>
            <li><a class="dropdown-item" href="?controlador=reservas&accion=listar">Cancha</a></li>
          </ul>
        </li>

        <li class="nav-item">
          <a class="nav-link <?php echo $controlador == 'reservas' && $accion == 'listar' ? 'active' : ''; ?>" href="?controlador=reservas&accion=listar">
            Mis Reservas
          </a>
        </li>

        <li class="nav-item py-2 py-lg-1 col-12 col-lg-auto">
          <div class="vr d-none d-lg-flex h-100 mx-lg-2 text-white"></div>
          <hr class="d-lg-none my-2 text-white-50">
        </li>

        <li class="nav-item dropdown">
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
      </ul>
    </div>
  </div>
</nav>