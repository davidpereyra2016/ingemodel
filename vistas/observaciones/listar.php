<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="container row mb-4 mt-4 mx-auto" id="alertSuccess">
        <div class="alert alert-success alert-dismissible col-md-8 offset-md-2 fade show d-flex align-items-center" role="alert">
            <p class="mb-0"><?php echo $_SESSION['mensaje']; ?>
                <?php unset($_SESSION['mensaje']); ?>
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['error'])): ?>
    <div class="container row mb-4 mt-4 mx-auto" id="alertError">
        <div class="alert alert-danger alert-dismissible col-md-8 offset-md-2 fade show d-flex align-items-center" role="alert">
            <p class="mb-0"><?php echo $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </p>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<div class="container">
    <div class="row mb-4 mt-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="mb-0 card-title color-success">
                        <i class="bi bi-file-text me-2"></i>
                        <?php if ($_SESSION['rol'] === 'ingeniero'): ?>
                            Mis Observaciones
                        <?php else: ?>
                            Gestión de Observaciones
                        <?php endif; ?>
                    </h3>
                    <?php if ($_SESSION['rol'] !== 'ingeniero'): ?>
                        <a href="index.php?controlador=observaciones&accion=crear" class="btn btn-success-theme">
                            <i class="bi bi-plus-circle me-2"></i>Nueva Observación
                        </a>
                    <?php endif; ?>
                </div>
                
                <div class="card-body">
                    <?php if ($_SESSION['rol'] !== 'ingeniero' && $estadisticas): ?>
                        <!-- Estadísticas para administradores -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <h5 class="text-muted mb-3">Resumen de Observaciones</h5>
                                <div class="row">
                                    <?php 
                                    $total_activas = 0;
                                    $total_resueltas = 0;
                                    $total_canceladas = 0;
                                    
                                    foreach ($estadisticas as $stat) {
                                        switch ($stat['estado']) {
                                            case 'activa':
                                                $total_activas = $stat['total'];
                                                break;
                                            case 'resuelta':
                                                $total_resueltas = $stat['total'];
                                                break;
                                            case 'cancelada':
                                                $total_canceladas = $stat['total'];
                                                break;
                                        }
                                    }
                                    ?>
                                    <div class="col-md-4">
                                        <div class="card border-warning">
                                            <div class="card-body text-center">
                                                <h4 class="text-warning"><?php echo $total_activas; ?></h4>
                                                <p class="mb-0">Activas</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-success">
                                            <div class="card-body text-center">
                                                <h4 class="text-success"><?php echo $total_resueltas; ?></h4>
                                                <p class="mb-0">Resueltas</p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="card border-secondary">
                                            <div class="card-body text-center">
                                                <h4 class="text-secondary"><?php echo $total_canceladas; ?></h4>
                                                <p class="mb-0">Canceladas</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <hr>
                    <?php endif; ?>
                    
                    <?php if (empty($observaciones)): ?>
                        <div class="text-center py-5">
                            <i class="bi bi-file-text" style="font-size: 4rem; color: #ddd;"></i>
                            <h4 class="text-muted mt-3">
                                <?php if ($_SESSION['rol'] === 'ingeniero'): ?>
                                    No tienes observaciones registradas
                                <?php else: ?>
                                    No hay observaciones registradas
                                <?php endif; ?>
                            </h4>
                            <p class="text-muted">
                                <?php if ($_SESSION['rol'] === 'ingeniero'): ?>
                                    Las observaciones aparecerán aquí cuando los administradores las registren.
                                <?php else: ?>
                                    Puedes crear una nueva observación haciendo clic en el botón "Nueva Observación".
                                <?php endif; ?>
                            </p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped table-hover" id="tablaObservaciones">
                                <thead class="table-dark">
                                    <tr>
                                        <th>ID</th>
                                        <th>Reserva</th>
                                        <th>Ingeniero/a</th>
                                        <th>Título</th>
                                        <th>Tipo</th>
                                        <th>Estado</th>
                                        <th>Fecha</th>
                                        <?php if ($_SESSION['rol'] !== 'ingeniero'): ?>
                                            <th>Administrador</th>
                                        <?php endif; ?>
                                        <th>Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($observaciones as $obs): ?>
                                        <tr>
                                            <td><?php echo $obs['id']; ?></td>
                                            <td>
                                                <small class="text-muted"><?php echo $obs['codigo_unico']; ?></small><br>
                                                <strong><?php echo date('d/m/Y', strtotime($obs['fecha_evento'])); ?></strong><br>
                                                <span class="badge bg-info"><?php echo $obs['tipo_uso']; ?></span>
                                            </td>
                                            <td>
                                                <strong><?php echo $obs['cliente_nombre'] . ' ' . $obs['cliente_apellido']; ?></strong><br>
                                                <small class="text-muted">Mat: <?php echo $obs['cliente_matricula']; ?></small>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($obs['titulo']); ?></strong><br>
                                                <small class="text-muted"><?php echo substr(htmlspecialchars($obs['descripcion']), 0, 50) . '...'; ?></small>
                                            </td>
                                            <td>
                                                <?php 
                                                $tipo_class = '';
                                                $tipo_text = '';
                                                switch ($obs['tipo_observacion']) {
                                                    case 'documentacion':
                                                        $tipo_class = 'bg-warning';
                                                        $tipo_text = 'Documentación';
                                                        break;
                                                    case 'daños':
                                                        $tipo_class = 'bg-danger';
                                                        $tipo_text = 'Daños';
                                                        break;
                                                    case 'incumplimiento':
                                                        $tipo_class = 'bg-dark';
                                                        $tipo_text = 'Incumplimiento';
                                                        break;
                                                    default:
                                                        $tipo_class = 'bg-secondary';
                                                        $tipo_text = 'Otros';
                                                }
                                                ?>
                                                <span class="badge <?php echo $tipo_class; ?>"><?php echo $tipo_text; ?></span>
                                            </td>
                                            <td>
                                                <?php 
                                                $estado_class = '';
                                                $estado_text = '';
                                                switch ($obs['estado']) {
                                                    case 'activa':
                                                        $estado_class = 'bg-warning text-dark';
                                                        $estado_text = 'Activa';
                                                        break;
                                                    case 'resuelta':
                                                        $estado_class = 'bg-success';
                                                        $estado_text = 'Resuelta';
                                                        break;
                                                    case 'cancelada':
                                                        $estado_class = 'bg-secondary';
                                                        $estado_text = 'Cancelada';
                                                        break;
                                                }
                                                ?>
                                                <span class="badge <?php echo $estado_class; ?>"><?php echo $estado_text; ?></span>
                                            </td>
                                            <td>
                                                <small><?php echo date('d/m/Y H:i', strtotime($obs['fecha_creacion'])); ?></small>
                                            </td>
                                            <?php if ($_SESSION['rol'] !== 'ingeniero'): ?>
                                                <td>
                                                    <small><?php echo $obs['admin_nombre'] . ' ' . $obs['admin_apellido']; ?></small>
                                                </td>
                                            <?php endif; ?>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="index.php?controlador=observaciones&accion=ver&id=<?php echo $obs['id']; ?>" 
                                                       class="btn btn-sm btn-outline-primary" title="Ver detalles">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                     <?php if ($obs['archivo_adjunto']): ?>
                                                         <a href="index.php?controlador=observaciones&accion=descargarArchivo&id=<?php echo $obs['id']; ?>" 
                                                            class="btn btn-sm btn-outline-success" title="Descargar archivo">
                                                             <i class="bi bi-download"></i>
                                                         </a>
                                                     <?php endif; ?>
                                                     <?php if ($_SESSION['rol'] !== 'ingeniero'): ?>
                                                         <a href="index.php?controlador=observaciones&accion=editar&id=<?php echo $obs['id']; ?>" 
                                                            class="btn btn-sm btn-outline-warning" title="Editar observación">
                                                             <i class="bi bi-pencil-square"></i>
                                                         </a>
                                                         <button type="button" class="btn btn-sm btn-outline-danger" 
                                                                 onclick="confirmarEliminacion(<?php echo $obs['id']; ?>)" title="Eliminar">
                                                             <i class="bi bi-trash"></i>
                                                         </button>
                                                     <?php endif; ?>
                                                </div>
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
    </div>
</div>

<!-- Modal para confirmar eliminación -->
<?php if ($_SESSION['rol'] !== 'ingeniero'): ?>
<div class="modal fade" id="modalEliminar" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Eliminación</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>¿Estás seguro de que deseas eliminar esta observación?</p>
                <p class="text-danger"><strong>Esta acción no se puede deshacer.</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="formEliminar" method="POST" action="index.php?controlador=observaciones&accion=eliminar" style="display: inline;">
                    <input type="hidden" name="id_observacion" id="idObservacionEliminar">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
// Inicializar DataTable
$(document).ready(function() {
    $('#tablaObservaciones').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Spanish.json"
        },
        "responsive": true,
        "order": [[ 6, "desc" ]], // Ordenar por fecha descendente
        "pageLength": 25
    });
});

<?php if ($_SESSION['rol'] !== 'ingeniero'): ?>
// Función para confirmar eliminación
function confirmarEliminacion(id) {
    document.getElementById('idObservacionEliminar').value = id;
    var modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
    modal.show();
}
<?php endif; ?>
</script>