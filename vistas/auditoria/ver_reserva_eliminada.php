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
            <h2><i class="fas fa-file-alt me-2"></i>Detalles de Reserva Eliminada</h2>
            <p class="text-muted">Información completa de la reserva eliminada #<?php echo $reserva_eliminada['reserva_id_original']; ?></p>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2">
            <a href="index.php?controlador=auditoria&accion=reservasEliminadas" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>
                Volver
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Información de la Reserva -->
        <div class="col-md-8">
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-calendar-times me-2"></i>
                        Información de la Reserva Eliminada
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>ID Original:</strong></td>
                                    <td>#<?php echo $reserva_eliminada['reserva_id_original']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Código Único:</strong></td>
                                    <td><code><?php echo $reserva_eliminada['codigo_unico']; ?></code></td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha del Evento:</strong></td>
                                    <td><?php echo date('d/m/Y', strtotime($reserva_eliminada['fecha_evento'])); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Horario:</strong></td>
                                    <td><?php echo substr($reserva_eliminada['hora_inicio'], 0, 5) . ' - ' . substr($reserva_eliminada['hora_fin'], 0, 5); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Tipo de Uso:</strong></td>
                                    <td><?php echo $reserva_eliminada['tipo_uso']; ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Estado al Eliminar:</strong></td>
                                    <td>
                                        <?php
                                        $badge_class = '';
                                        switch($reserva_eliminada['estado']) {
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
                                        <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($reserva_eliminada['estado']); ?></span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Monto Total:</strong></td>
                                    <td><span class="h5 text-success">$<?php echo number_format($reserva_eliminada['monto'], 2); ?></span></td>
                                </tr>
                                <tr>
                                    <td><strong>Anticipo Pagado:</strong></td>
                                    <td>$<?php echo number_format($reserva_eliminada['monto_anticipo'] ?: 0, 2); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Saldo Pagado:</strong></td>
                                    <td>$<?php echo number_format($reserva_eliminada['monto_saldo'] ?: 0, 2); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Fecha Creación:</strong></td>
                                    <td><?php echo date('d/m/Y H:i:s', strtotime($reserva_eliminada['fecha_creacion_original'])); ?></td>
                                </tr>
                                <tr>
                                    <td><strong>Vencimiento Pago:</strong></td>
                                    <td>
                                        <?php if ($reserva_eliminada['fecha_vencimiento_pago']): ?>
                                            <?php echo date('d/m/Y H:i:s', strtotime($reserva_eliminada['fecha_vencimiento_pago'])); ?>
                                        <?php else: ?>
                                            <span class="text-muted">No definido</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                    
                    <?php if ($reserva_eliminada['descripcion']): ?>
                        <div class="mt-3">
                            <strong>Descripción/Motivo:</strong>
                            <div class="bg-light p-3 rounded mt-2">
                                <?php echo nl2br(htmlspecialchars($reserva_eliminada['descripcion'])); ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Archivos Adjuntos -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-paperclip me-2"></i>
                        Archivos Adjuntos
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-file-alt me-2"></i>Formulario de Solicitud</span>
                                    <?php if ($reserva_eliminada['archivo_formulario']): ?>
                                        <span class="badge bg-success">Subido</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No subido</span>
                                    <?php endif; ?>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-file-alt me-2"></i>Formulario Municipal</span>
                                    <?php if ($reserva_eliminada['archivo_municipal']): ?>
                                        <span class="badge bg-success">Subido</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No subido</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-receipt me-2"></i>Comprobante Anticipo</span>
                                    <?php if ($reserva_eliminada['archivo_comprobante']): ?>
                                        <span class="badge bg-success">Subido</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No subido</span>
                                    <?php endif; ?>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <span><i class="fas fa-receipt me-2"></i>Comprobante Total</span>
                                    <?php if ($reserva_eliminada['archivo_comprobante_total']): ?>
                                        <span class="badge bg-success">Subido</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">No subido</span>
                                    <?php endif; ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Información del Usuario e Información de Eliminación -->
        <div class="col-md-4">
            <!-- Usuario que hizo la reserva -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-user me-2"></i>
                        Usuario de la Reserva
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Nombre:</strong></td>
                            <td><?php echo $reserva_eliminada['nombre_usuario'] . ' ' . $reserva_eliminada['apellido_usuario']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Email:</strong></td>
                            <td><small><?php echo $reserva_eliminada['email_usuario']; ?></small></td>
                        </tr>
                        <tr>
                            <td><strong>Matrícula:</strong></td>
                            <td><code><?php echo $reserva_eliminada['matricula_usuario']; ?></code></td>
                        </tr>
                        <tr>
                            <td><strong>ID Usuario:</strong></td>
                            <td>#<?php echo $reserva_eliminada['id_usuario']; ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Información de eliminación -->
            <div class="card mb-4">
                <div class="card-header bg-danger text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-trash me-2"></i>
                        Información de Eliminación
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-borderless table-sm">
                        <tr>
                            <td><strong>Eliminado Por:</strong></td>
                            <td><?php echo $reserva_eliminada['eliminado_por_nombre'] . ' ' . $reserva_eliminada['eliminado_por_apellido']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>ID Admin:</strong></td>
                            <td>#<?php echo $reserva_eliminada['eliminado_por_usuario_id']; ?></td>
                        </tr>
                        <tr>
                            <td><strong>Fecha Eliminación:</strong></td>
                            <td>
                                <?php echo date('d/m/Y', strtotime($reserva_eliminada['fecha_eliminacion'])); ?><br>
                                <small class="text-muted"><?php echo date('H:i:s', strtotime($reserva_eliminada['fecha_eliminacion'])); ?></small>
                            </td>
                        </tr>
                    </table>
                    
                    <?php if ($reserva_eliminada['motivo_eliminacion']): ?>
                        <div class="mt-3">
                            <strong>Motivo:</strong>
                            <div class="bg-light p-2 rounded mt-1">
                                <small><?php echo htmlspecialchars($reserva_eliminada['motivo_eliminacion']); ?></small>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Acciones -->
            <div class="card">
                <div class="card-header">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cogs me-2"></i>
                        Acciones
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="index.php?controlador=auditoria&accion=historial&usuario=<?php echo $reserva_eliminada['id_usuario']; ?>" 
                           class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-history me-1"></i>
                            Ver Historial del Usuario
                        </a>
                        <a href="index.php?controlador=auditoria&accion=historial&usuario=<?php echo $reserva_eliminada['eliminado_por_usuario_id']; ?>" 
                           class="btn btn-outline-warning btn-sm">
                            <i class="fas fa-user-shield me-1"></i>
                            Ver Acciones del Admin
                        </a>
                        <a href="index.php?controlador=auditoria&accion=reservasEliminadas" 
                           class="btn btn-secondary btn-sm">
                            <i class="fas fa-list me-1"></i>
                            Volver a Lista
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-borderless td {
    padding: 0.25rem 0.5rem;
    border: none;
}

.card-header {
    font-weight: 600;
}

.badge {
    font-size: 0.75rem;
}

code {
    background-color: #f8f9fa;
    padding: 0.2rem 0.4rem;
    border-radius: 0.25rem;
    font-size: 0.875rem;
}
</style>
