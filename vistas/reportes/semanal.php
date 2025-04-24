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
?>

<div class="container">
    <div class="row mt-5 mb-4 flex-row align-items-center justify-content-between">
        <div class="col-md-8">
            <h2>Reporte Semanal de Reservas</h2>
            <p class="text-muted">Semana del <?php echo $inicioSemana->format('d/m/Y'); ?> al <?php echo $finSemana->format('d/m/Y'); ?></p>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2 header-page-responsive">
            <a href="index.php?controlador=reportes&accion=listar" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Volver
            </a>
            <a href="index.php?controlador=reportes&accion=imprimirReporte&tipo=semanal&fecha=<?php echo $fechaActual->format('Y-m-d'); ?>" class="btn btn-success" target="_blank">
                <i class="fas fa-print me-1"></i> Imprimir Reporte
            </a>
        </div>
    </div>

    <!-- Filtro por semana -->
    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <h5 class="card-title mb-3">Seleccionar semana</h5>
            <form action="index.php" method="GET" class="row g-3 align-items-end">
                <input type="hidden" name="controlador" value="reportes">
                <input type="hidden" name="accion" value="reporteSemanal">
                
                <div class="col-md-4">
                    <label for="fecha" class="form-label">Seleccione una fecha</label>
                    <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $fechaActual->format('Y-m-d'); ?>">
                </div>
                
                <div class="col-md-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-calendar-week me-1"></i> Ver semana
                    </button>
                </div>
                
                <div class="col-md-4 text-end">
                    <div class="btn-group">
                        <a href="index.php?controlador=reportes&accion=reporteSemanal&fecha=<?php echo (clone $inicioSemana)->modify('-7 days')->format('Y-m-d'); ?>" class="btn btn-outline-secondary">
                            <i class="fas fa-angle-left"></i> Semana anterior
                        </a>
                        <a href="index.php?controlador=reportes&accion=reporteSemanal&fecha=<?php echo (clone $inicioSemana)->modify('+7 days')->format('Y-m-d'); ?>" class="btn btn-outline-secondary">
                            Semana siguiente <i class="fas fa-angle-right"></i>
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Resumen de la semana -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success-2 text-white">
            <h5 class="card-title mb-0">Resumen semanal</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Total Reservas</h6>
                            <h1 class="display-4"><?php echo count($reservasSemana); ?></h1>
                        </div>
                    </div>
                </div>
                
                <?php
                // Calcular estadísticas
                $aprobadas = 0;
                $pendientes = 0;
                $rechazadas = 0;
                $canceladas = 0;
                $montoTotal = 0;
                
                foreach ($reservasSemana as $reserva) {
                    switch ($reserva['estado']) {
                        case 'aprobada':
                            $aprobadas++;
                            break;
                        case 'pendiente':
                            $pendientes++;
                            break;
                        case 'rechazada':
                            $rechazadas++;
                            break;
                        case 'cancelada':
                            $canceladas++;
                            break;
                    }
                    $montoTotal += $reserva['monto'];
                }
                ?>
                
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Aprobadas</h6>
                            <h1 class="display-4 text-success"><?php echo $aprobadas; ?></h1>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Pendientes</h6>
                            <h1 class="display-4 text-warning"><?php echo $pendientes; ?></h1>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3 mb-3">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Rechazadas</h6>
                            <h1 class="display-4 text-danger"><?php echo $rechazadas; ?></h1>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body text-center">
                            <h6 class="text-muted">Ingresos Totales de la Semana</h6>
                            <h1 class="display-5 text-primary">$<?php echo number_format($montoTotal, 2, ',', '.'); ?></h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vista por días de la semana -->
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-success-2 text-white">
            <h5 class="card-title mb-0">Reservas por día</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php
                // Organizar reservas por día
                $reservasPorDia = [];
                $diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                
                // Inicializar array de días
                for ($i = 0; $i < 7; $i++) {
                    $dia = clone $inicioSemana;
                    $dia->modify('+' . $i . ' days');
                    $reservasPorDia[$dia->format('Y-m-d')] = [
                        'fecha' => $dia->format('d/m/Y'),
                        'dia' => $diasSemana[$i],
                        'reservas' => []
                    ];
                }
                
                // Agregar reservas a sus días correspondientes
                foreach ($reservasSemana as $reserva) {
                    $fechaReserva = $reserva['fecha_evento'];
                    if (isset($reservasPorDia[$fechaReserva])) {
                        $reservasPorDia[$fechaReserva]['reservas'][] = $reserva;
                    }
                }
                
                // Mostrar días con sus reservas
                foreach ($reservasPorDia as $fecha => $info):
                ?>
                    <div class="col-md-12 mb-4">
                        <div class="card">
                            <div class="card-header <?php echo !empty($info['reservas']) ? 'bg-light' : ''; ?>">
                                <h5 class="mb-0">
                                    <?php echo $info['dia']; ?> - <?php echo $info['fecha']; ?>
                                    <span class="badge bg-primary ms-2"><?php echo count($info['reservas']); ?> reservas</span>
                                </h5>
                            </div>
                            
                            <?php if (empty($info['reservas'])): ?>
                                <div class="card-body">
                                    <p class="text-muted">No hay reservas para este día.</p>
                                </div>
                            <?php else: ?>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <thead>
                                                <tr>
                                                    <th>ID</th>
                                                    <th>Ingeniero</th>
                                                    <th>Horario</th>
                                                    <th>Tipo de Uso</th>
                                                    <th>Estado</th>
                                                    <th>Monto</th>
                                                    <th>Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($info['reservas'] as $reserva): ?>
                                                    <tr>
                                                        <td><?php echo $reserva['id']; ?></td>
                                                        <td><?php echo $reserva['nombre'] . ' ' . $reserva['apellido']; ?></td>
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
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
