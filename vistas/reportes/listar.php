<?php
// Mostrar mensajes de éxito o error si existen
if (isset($_SESSION['mensaje'])) {
    echo '<div class="container mt-4 alert alert-success">' . $_SESSION['mensaje'] . '</div>';
    unset($_SESSION['mensaje']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="container mt-4 alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

// Nombres de meses en español
$nombresMeses = [
    1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
    5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
    9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
];
?>

<div class="container">
    <div class="row mt-5 mb-4 flex-row align-items-center justify-content-between">
        <div class="col-md-8">
            <h2>Gestión de Reportes</h2>
            <p class="text-muted">Visualiza y genera reportes de las reservas del salón</p>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2 header-page-responsive">
            <a href="index.php?controlador=reportes&accion=reporteSemanal" class="btn btn-outline-primary">
                <i class="fas fa-calendar-week me-1"></i> Reporte Semanal
            </a>
            <a href="index.php?controlador=reportes&accion=reporteMensual" class="btn btn-outline-success">
                <i class="fas fa-calendar-alt me-1"></i> Reporte Mensual
            </a>
        </div>
    </div>

    <!-- Filtros de búsqueda -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Filtros de búsqueda</h5>
            <form action="index.php" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="controlador" value="reportes">
                <input type="hidden" name="accion" value="listar">
                
                <div class="col-md-3">
                    <label for="mes" class="form-label">Mes</label>
                    <select name="mes" id="mes" class="form-select">
                        <?php foreach ($nombresMeses as $num => $nombre): ?>
                            <option value="<?php echo $num; ?>" <?php echo $mesSeleccionado == $num ? 'selected' : ''; ?>>
                                <?php echo $nombre; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="anio" class="form-label">Año</label>
                    <select name="anio" id="anio" class="form-select">
                        <?php foreach ($anosDisponibles as $anio): ?>
                            <option value="<?php echo $anio; ?>" <?php echo $anioSeleccionado == $anio ? 'selected' : ''; ?>>
                                <?php echo $anio; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Filtrar
                    </button>
                </div>
                
                <div class="col-md-3">
                    <a href="index.php?controlador=reportes&accion=imprimirReporte&tipo=mensual&mes=<?php echo $mesSeleccionado; ?>&anio=<?php echo $anioSeleccionado; ?>" class="btn btn-success w-100" target="_blank">
                        <i class="fas fa-print me-1"></i> Imprimir Reporte
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen estadístico -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success-2 text-white">
                    <h5 class="card-title mb-0">Resumen del mes de <?php echo $nombresMeses[intval($mesSeleccionado)]; ?> de <?php echo $anioSeleccionado; ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Total Reservas</h6>
                                    <h1 class="display-4"><?php echo $estadisticas['total_reservas']; ?></h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Aprobadas</h6>
                                    <h1 class="display-4 text-success"><?php echo $estadisticas['aprobadas']; ?></h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Pendientes</h6>
                                    <h1 class="display-4 text-warning"><?php echo $estadisticas['pendientes']; ?></h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Rechazadas</h6>
                                    <h1 class="display-4 text-danger"><?php echo $estadisticas['rechazadas']; ?></h1>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-3">
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body text-center">
                                    <h6 class="text-muted">Ingresos Totales</h6>
                                    <h1 class="display-5 text-primary">$<?php echo number_format($estadisticas['ingreso_total'], 2, ',', '.'); ?></h1>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-muted mb-3">Tipos de Uso Más Frecuentes</h6>
                                    <div class="table-responsive">
                                        <table class="table table-sm">
                                            <thead>
                                                <tr>
                                                    <th>Tipo de Uso</th>
                                                    <th class="text-center">Cantidad</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($tiposUsoPopulares as $tipo): ?>
                                                <tr>
                                                    <td><?php echo $tipo['tipo_uso']; ?></td>
                                                    <td class="text-center"><?php echo $tipo['cantidad']; ?></td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Listado de Reservas -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success-2 text-white">
            <h5 class="card-title mb-0">Listado de Reservas</h5>
        </div>
        <div class="card-body">
            <?php if (empty($reservas)): ?>
                <div class="alert alert-info">
                    No hay reservas para el período seleccionado.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="myTable">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Ingeniero</th>
                                <th>Matrícula</th>
                                <th>Fecha</th>
                                <th>Horario</th>
                                <th>Tipo de Uso</th>
                                <th>Estado</th>
                                <th>Monto</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas as $reserva): ?>
                                <tr>
                                    <td><?php echo $reserva['id']; ?></td>
                                    <td><?php echo $reserva['nombre'] . ' ' . $reserva['apellido']; ?></td>
                                    <td><?php echo $reserva['matricula']; ?></td>
                                    <td><?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?></td>
                                    <td><?php echo substr($reserva['hora_inicio'], 0, 5) . ' - ' . substr($reserva['hora_fin'], 0, 5); ?></td>
                                    <td><?php echo $reserva['tipo_uso']; ?></td>
                                    <td>
                                        <?php
                                        switch ($reserva['estado']) {
                                            case 'pendiente':
                                                echo '<span class="badge rounded-pill text-bg-warning">Pendiente</span>';
                                                break;
                                            case 'aprobada':
                                                echo '<span class="badge rounded-pill text-bg-success">Aprobada</span>';
                                                break;
                                            case 'rechazada':
                                                echo '<span class="badge rounded-pill text-bg-danger">Rechazada</span>';
                                                break;
                                            case 'cancelada':
                                                echo '<span class="badge rounded-pill text-bg-secondary">Cancelada</span>';
                                                break;
                                        }
                                        ?>
                                    </td>
                                    <td>$<?php echo number_format($reserva['monto'], 2, ',', '.'); ?></td>
                                    <td>
                                        <a href="index.php?controlador=reservas&accion=ver&id=<?php echo $reserva['id']; ?>" class="btn btn-sm btn-success">
                                            <i class="fas fa-eye me-1"></i> Ver
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
