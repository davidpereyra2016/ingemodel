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
            <h2><i class="fas fa-history me-2"></i>Historial de Auditoría</h2>
            <p class="text-muted">Registro completo de acciones realizadas en el sistema</p>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2">
            <a href="index.php?controlador=auditoria&accion=reservasEliminadas" class="btn btn-warning">
                <i class="fas fa-trash-restore me-1"></i>
                Reservas Eliminadas
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
                <input type="hidden" name="accion" value="historial">
                
                <div class="row">
                    <div class="col-md-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <select name="usuario" id="usuario" class="form-select">
                            <option value="">Todos los usuarios</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?php echo $usuario['id']; ?>" 
                                    <?php echo (isset($_GET['usuario']) && $_GET['usuario'] == $usuario['id']) ? 'selected' : ''; ?>>
                                    <?php echo $usuario['nombre'] . ' ' . $usuario['apellido'] . ' (' . $usuario['rol'] . ')'; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-3">
                        <label for="filtro_accion" class="form-label">Acción</label>
                        <select name="filtro_accion" id="filtro_accion" class="form-select">
                            <option value="">Todas las acciones</option>
                            <option value="creación" <?php echo (isset($_GET['filtro_accion']) && $_GET['filtro_accion'] == 'creación') ? 'selected' : ''; ?>>Creación</option>
                            <option value="baja" <?php echo (isset($_GET['filtro_accion']) && $_GET['filtro_accion'] == 'baja') ? 'selected' : ''; ?>>Baja</option>
                            <option value="eliminacion_completa" <?php echo (isset($_GET['filtro_accion']) && $_GET['filtro_accion'] == 'eliminacion_completa') ? 'selected' : ''; ?>>Eliminación Completa</option>
                            <option value="cambio_estado" <?php echo (isset($_GET['filtro_accion']) && $_GET['filtro_accion'] == 'cambio_estado') ? 'selected' : ''; ?>>Cambio de Estado</option>
                            <option value="actualizacion_montos" <?php echo (isset($_GET['filtro_accion']) && $_GET['filtro_accion'] == 'actualizacion_montos') ? 'selected' : ''; ?>>Actualización de Montos</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label for="fecha_desde" class="form-label">Desde</label>
                        <input type="date" name="fecha_desde" id="fecha_desde" class="form-control" 
                               value="<?php echo isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : ''; ?>">
                    </div>
                    
                    <div class="col-md-2">
                        <label for="fecha_hasta" class="form-label">Hasta</label>
                        <input type="date" name="fecha_hasta" id="fecha_hasta" class="form-control" 
                               value="<?php echo isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : ''; ?>">
                    </div>
                    
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i>Filtrar
                        </button>
                        <a href="index.php?controlador=auditoria&accion=historial" class="btn btn-outline-secondary">
                            <i class="fas fa-times me-1"></i>Limpiar
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tabla de historial -->
    <div class="card">
        <div class="card-header bg-light pt-4">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title text-primary">
                    <i class="fas fa-list me-2"></i>Registros de Auditoría (<?php echo count($historial); ?>)
                </h5>
                <a href="index.php?controlador=auditoria&accion=generarReporte&tipo=historial<?php 
                    echo isset($_GET['fecha_desde']) ? '&fecha_desde=' . $_GET['fecha_desde'] : '';
                    echo isset($_GET['fecha_hasta']) ? '&fecha_hasta=' . $_GET['fecha_hasta'] : '';
                ?>" class="btn btn-danger btn-sm" target="_blank">
                    <i class="fas fa-file-pdf me-1"></i>Exportar PDF
                </a>
            </div>
        </div>
        <div class="card-body">
            <?php if (empty($historial)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No se encontraron registros de auditoría con los filtros aplicados.
                </div>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>ID</th>
                                <th>Reserva</th>
                                <th>Usuario</th>
                                <!-- <th>Rol</th> -->
                                <th>Acción</th>
                                <th>Estado Anterior</th>
                                <th>Estado Nuevo</th>
                                <th>Comentario</th>
                                <th>Fecha</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($historial as $registro): ?>
                                <tr>
                                    <td><?php echo $registro['id']; ?></td>
                                    <td>
                                        <strong>#<?php echo $registro['id_reserva']; ?></strong>
                                        <?php if ($registro['codigo_unico']): ?>
                                            <br><small class="text-muted"><?php echo $registro['codigo_unico']; ?></small>
                                        <?php endif; ?>
                                        <?php if ($registro['tipo_uso']): ?>
                                            <br><small class="text-info"><?php echo $registro['tipo_uso']; ?></small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <strong>
                                            <?php 
                                            // Usar los datos del historial si están disponibles, sino los originales
                                            $nombre = $registro['usuario_nombre'] ?: $registro['usuario_nombre_original'];
                                            $apellido = $registro['usuario_apellido'] ?: $registro['usuario_apellido_original'];
                                            echo $nombre . ' ' . $apellido; 
                                            ?>
                                        </strong>
                                        <br><small class="text-muted">ID: <?php echo $registro['id_usuario']; ?></small>
                                    </td>
                                    <!-- <td>
                                        <?php 
                                        $rol = $registro['usuario_rol'] ?: $registro['usuario_rol_original'];
                                        $badge_class = '';
                                        switch($rol) {
                                            case 'administrador':
                                                $badge_class = 'bg-danger';
                                                break;
                                            case 'encargado':
                                                $badge_class = 'bg-warning';
                                                break;
                                            case 'ingeniero':
                                                $badge_class = 'bg-info';
                                                break;
                                            default:
                                                $badge_class = 'bg-secondary';
                                        }
                                        ?>
                                        <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($rol); ?></span>
                                    </td> -->
                                    <td>
                                        <?php
                                        $accion_class = '';
                                        switch($registro['accion']) {
                                            case 'creación':
                                                $accion_class = 'text-success';
                                                break;
                                            case 'baja':
                                                $accion_class = 'text-warning';
                                                break;
                                            case 'eliminacion_completa':
                                                $accion_class = 'text-danger';
                                                break;
                                            case 'cambio_estado':
                                                $accion_class = 'text-primary';
                                                break;
                                            default:
                                                $accion_class = 'text-info';
                                        }
                                        ?>
                                        <span class="<?php echo $accion_class; ?>">
                                            <strong><?php echo ucfirst($registro['accion']); ?></strong>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if ($registro['estado_anterior']): ?>
                                            <span class="badge bg-light text-dark"><?php echo ucfirst($registro['estado_anterior']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($registro['estado_nuevo']): ?>
                                            <span class="badge bg-light text-dark"><?php echo ucfirst($registro['estado_nuevo']); ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <small><?php echo $registro['comentario'] ?: '-'; ?></small>
                                    </td>
                                    <td>
                                        <small>
                                            <?php echo date('d/m/Y', strtotime($registro['fecha'])); ?><br>
                                            <?php echo date('H:i:s', strtotime($registro['fecha'])); ?>
                                        </small>
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
</style>
