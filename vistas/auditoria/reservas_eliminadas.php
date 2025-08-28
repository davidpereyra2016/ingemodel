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
    <div class="row mt-5 mb-5 flex-row align-items-center justify-content-between">
        <div class="col-md-8">
            <h2><i class="fas fa-trash-restore me-2"></i>Reservas Eliminadas</h2>
            <p class="text-muted">Respaldo de reservas eliminadas físicamente del sistema</p>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2">
            <a href="index.php?controlador=auditoria&accion=historial" class="btn btn-info">
                <i class="fas fa-history me-1"></i>
                Historial Auditoría
            </a>
            <a href="index.php?controlador=reservas&accion=listar" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Volver
            </a>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="card-title mb-0"><i class="fas fa-filter me-2"></i>Filtros de Búsqueda</h5>
        </div>
        <div class="card-body">
            <form method="GET" action="index.php">
                <input type="hidden" name="controlador" value="auditoria">
                <input type="hidden" name="accion" value="reservasEliminadas">
                
                <div class="row">
                    <div class="col-md-4">
                        <label for="eliminado_por" class="form-label">Eliminado Por</label>
                        <select name="eliminado_por" id="eliminado_por" class="form-select">
                            <option value="">Todos los administradores</option>
                            <?php foreach ($administradores as $admin): ?>
                                <option value="<?php echo $admin['id']; ?>" 
                                    <?php echo (isset($_GET['eliminado_por']) && $_GET['eliminado_por'] == $admin['id']) ? 'selected' : ''; ?>>
                                    <?php echo $admin['nombre'] . ' ' . $admin['apellido']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="fecha_desde" class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" 
                               value="<?php echo isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : ''; ?>">
                    </div>
                    
                    <div class="col-md-3">
                        <label for="fecha_hasta" class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" 
                               value="<?php echo isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : ''; ?>">
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                        <a href="index.php?controlador=auditoria&accion=reservasEliminadas" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de reservas eliminadas -->
    <div class="card">
        <div class="card-header bg-light pt-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title text-danger">
                    <i class="fas fa-database me-2"></i>Reservas Eliminadas (<?php echo count($reservas_eliminadas); ?>)
                </h5>
                <a href="index.php?controlador=auditoria&accion=generarReporte&tipo=eliminadas<?php 
                    echo isset($_GET['fecha_desde']) ? '&fecha_desde=' . $_GET['fecha_desde'] : '';
                    echo isset($_GET['fecha_hasta']) ? '&fecha_hasta=' . $_GET['fecha_hasta'] : '';
                ?>" class="btn btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i>Exportar PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($reservas_eliminadas)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No se encontraron reservas eliminadas con los filtros aplicados.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID Original</th>
                                <th>Usuario</th>
                                <th>Evento</th>
                                <th>Estado</th>
                                <th>Monto</th>
                                <th>Fecha Eliminación</th>
                                <th>Eliminado Por</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($reservas_eliminadas as $reserva): ?>
                                <tr>
                                    <td>
                                        <strong>#<?php echo $reserva['reserva_id_original']; ?></strong>
                                        <br><small class="text-muted"><?php echo $reserva['codigo_unico']; ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo $reserva['nombre_usuario'] . ' ' . $reserva['apellido_usuario']; ?></strong>
                                        <br><small class="text-muted"><?php echo $reserva['email_usuario']; ?></small>
                                        <br><small class="text-info">Mat: <?php echo $reserva['matricula_usuario']; ?></small>
                                    </td>
                                    <td>
                                        <strong><?php echo $reserva['tipo_uso']; ?></strong>
                                        <br><small class="text-muted">
                                            <?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?>
                                            <?php echo substr($reserva['hora_inicio'], 0, 5) . ' - ' . substr($reserva['hora_fin'], 0, 5); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <?php
                                        $badge_class = '';
                                        switch($reserva['estado']) {
                                            case 'pendiente':
                                                $badge_class = 'bg-warning';
                                                break;
                                            case 'aprobada':
                                                $badge_class = 'bg-success';
                                                break;
                                            case 'rechazada':
                                                $badge_class = 'bg-danger';
                                                break;
                                            case 'cancelada':
                                                $badge_class = 'bg-secondary';
                                                break;
                                            case 'baja':
                                                $badge_class = 'bg-dark';
                                                break;
                                            default:
                                                $badge_class = 'bg-light text-dark';
                                        }
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($reserva['estado']); ?></span>
                                    </td>
                                    <td>
                                        <strong>$<?php echo number_format($reserva['monto'], 2); ?></strong>
                                        <?php if ($reserva['monto_anticipo'] || $reserva['monto_saldo']): ?>
                                            <br><small class="text-muted">
                                                A: $<?php echo number_format($reserva['monto_anticipo'] ?: 0, 2); ?> | 
                                                S: $<?php echo number_format($reserva['monto_saldo'] ?: 0, 2); ?>
                                            </small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo date('d/m/Y', strtotime($reserva['fecha_eliminacion'])); ?><br>
                                            <?php echo date('H:i:s', strtotime($reserva['fecha_eliminacion'])); ?>
                                        </small>
                                    </td>
                                    <td>
                                        <strong><?php echo $reserva['eliminado_por_nombre'] . ' ' . $reserva['eliminado_por_apellido']; ?></strong>
                                        <br><small class="text-muted">ID: <?php echo $reserva['eliminado_por_usuario_id']; ?></small>
                                    </td>
                                    <td>
                                        <a href="index.php?controlador=auditoria&accion=verReservaEliminada&id=<?php echo $reserva['id']; ?>" 
                                           class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye me-1"></i>Ver Detalles
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

<style>
.table th {
    font-size: 0.9rem;
    font-weight: 600;
}

.table td {
    font-size: 0.85rem;
    vertical-align: middle;
}

.badge {
    font-size: 0.7rem;
}

.alert {
    border-left: 4px solid;
}

.alert-info {
    border-left-color: #17a2b8;
}
</style>
