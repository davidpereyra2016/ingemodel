<?php
// Mostrar mensajes de éxito o error si existen
if (isset($_SESSION['mensaje'])) {
    echo '<div class="alert alert-success">' . $_SESSION['mensaje'] . '</div>';
    unset($_SESSION['mensaje']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}

?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h3 class="mb-0">Detalles de la Reserva #<?php echo $reserva['id']; ?></h3>
                    <span class="badge badge-light">
                        <?php
                        switch ($reserva['estado']) {
                            case 'pendiente':
                                echo 'Pendiente';
                                break;
                            case 'aprobada':
                                echo 'Aprobada';
                                break;
                            case 'rechazada':
                                echo 'Rechazada';
                                break;
                            case 'cancelada':
                                echo 'Cancelada';
                                break;
                        }
                        ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>Información de la Reserva</h5>
                            <hr>
                            <p><strong>Fecha del Evento:</strong> <?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?></p>
                            <p><strong>Horario:</strong> <?php echo substr($reserva['hora_inicio'], 0, 5) . ' - ' . substr($reserva['hora_fin'], 0, 5); ?></p>
                            <p><strong>Tipo de Uso:</strong> <?php echo $reserva['tipo_uso']; ?></p>
                            <p><strong>Fecha de Solicitud:</strong> <?php echo date('d/m/Y H:i', strtotime($reserva['fecha_solicitud'])); ?></p>
                            <p><strong>Monto Total:</strong> $<?php echo sprintf("%.2f", $reserva['monto']); ?></p>
                            <p><strong>Motivo de Uso:</strong> <?php echo $reserva['motivo_de_uso']; ?></p>
                            <p>
                                <strong>Anticipo:</strong> 
                                <?php if ($reserva['anticipo_pagado']): ?>
                                    <span class="text-success">Pagado ✓</span>
                                <?php else: ?>
                                    <span class="text-danger">Pendiente ✗</span>
                                <?php endif; ?>
                            </p>
                            <p>
                                <strong>Saldo:</strong> 
                                <?php if ($reserva['saldo_pagado']): ?>
                                    <span class="text-success">Pagado ✓</span>
                                <?php else: ?>
                                    <span class="text-danger">Pendiente ✗</span>
                                <?php endif; ?>
                            </p>
                            
                            <?php if ($reserva['estado'] == 'cancelada'): ?>
                                <div class="alert alert-danger mt-3">
                                    <strong>Motivo de la cancelación:</strong><br>
                                    <p>La cancelación se dio por superar el plazo de 48 Horas para presentar la documentación del pago de la reserva</p>
                                </div>
                            <?php endif; ?>
                            <?php if ($reserva['estado'] == 'rechazada' && !empty($reserva['motivo_rechazo'])): ?>
                                <div class="alert alert-danger mt-3">
                                    <strong>Motivo del rechazo:</strong><br>
                                    <?php echo nl2br($reserva['motivo_rechazo']); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6">
                            <h5>Datos del Solicitante</h5>
                            <hr>
                            <p><strong>Nombre:</strong> <?php echo $reserva['nombre'] . ' ' . $reserva['apellido']; ?></p>
                            <p><strong>Matrícula:</strong> <?php echo $reserva['matricula']; ?></p>
                            <p><strong>Email:</strong> <?php echo $reserva['email']; ?></p>
                            <p><strong>Teléfono:</strong> <?php echo $reserva['telefono']; ?></p>
                        </div>
                    </div>
                    
                    <div class="row mt-4">
                        <div class="col-12">
                            <h5>Documentos</h5>
                            <hr>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">Formulario de Solicitud</div>
                                        <div class="card-body">
                                            <?php if ($reserva['archivo_formulario']): ?>
                                                <a href="assets/uploads/<?php echo $reserva['archivo_formulario']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-file-pdf"></i> Ver Formulario
                                                </a>
                                            <?php else: ?>
                                                <p class="text-muted">No se ha subido el formulario</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">Formulario de Solicitud Municipal</div>
                                        <div class="card-body">
                                            <?php if ($reserva['archivo_municipal']): ?>
                                                <a href="assets/uploads/<?php echo $reserva['archivo_municipal']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-file-pdf"></i> Ver Formulario
                                                </a>
                                            <?php else: ?>
                                                <p class="text-muted">No se ha subido el formulario</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">Comprobante de Pago</div>
                                        <div class="card-body">
                                            <?php if ($reserva['archivo_comprobante']): ?>
                                                <a href="assets/uploads/<?php echo $reserva['archivo_comprobante']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-file-invoice-dollar"></i> Ver Comprobante
                                                </a>
                                            <?php else: ?>
                                                <p class="text-muted">No se ha subido el comprobante</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="card mb-3">
                                        <div class="card-header bg-light">Comprobante de Pago Total</div>
                                        <div class="card-body">
                                            <?php if ($reserva['archivo_comprobante_total']): ?>
                                                <a href="assets/uploads/<?php echo $reserva['archivo_comprobante_total']; ?>" target="_blank" class="btn btn-sm btn-info">
                                                    <i class="fas fa-file-invoice-dollar"></i> Ver Comprobante
                                                </a>
                                            <?php else: ?>
                                                <p class="text-muted">No se ha subido el comprobante</p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <?php if ($_SESSION['rol'] == 'administrador' && $reserva['estado'] == 'pendiente'): ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header bg-warning text-dark">
                                        <h5 class="mb-0">Acciones Administrativas</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="index.php?controlador=reservas&accion=aprobarRechazar" method="POST">
                                            <input type="hidden" name="id_reserva" value="<?php echo $reserva['id']; ?>">
                                            
                                            <div class="form-group">
                                                <label>Cambiar Estado:</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="estado" id="estado_aprobar" value="aprobada" required>
                                                    <label class="form-check-label" for="estado_aprobar">Aprobar Solicitud</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="estado" id="estado_rechazar" value="rechazada">
                                                    <label class="form-check-label" for="estado_rechazar">Rechazar Solicitud</label>
                                                </div>
                                            </div>
                                            
                                            <div class="form-group" id="motivo_rechazo_container" style="display: none;">
                                                <label for="motivo">Motivo del Rechazo:</label>
                                                <textarea class="form-control" id="motivo" name="motivo" rows="3"></textarea>
                                            </div>
                                            
                                            <div class="form-group text-right">
                                                <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="form-group mt-4 text-center">
                        <a href="index.php?controlador=reservas&accion=listar" class="btn btn-secondary">Volver a la Lista</a>
                        
                        <?php if (($reserva['estado'] == 'pendiente' || $reserva['estado'] == 'aprobada') && 
                                 (!$reserva['archivo_formulario'] || !$reserva['archivo_comprobante'] || 
                                  !$reserva['archivo_municipal'] || !$reserva['archivo_comprobante_total'])): ?>
                            <a href="index.php?controlador=reservas&accion=subirFormulario&codigo=<?php echo $reserva['codigo_unico']; ?>" class="btn btn-warning">
                                <i class="fas fa-upload"></i> Subir Archivos
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($reserva['estado'] == 'aprobada'): ?>
                            <a href="index.php?controlador=reservas&accion=generarPDF&id=<?php echo $reserva['id']; ?>" class="btn btn-primary">
                                <i class="fas fa-file-pdf"></i> Descargar Comprobante
                            </a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Mostrar/Ocultar campo de motivo según selección
    const estadoRadios = document.querySelectorAll('input[name="estado"]');
    const motivoContainer = document.getElementById('motivo_rechazo_container');
    
    if (estadoRadios) {
        estadoRadios.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'rechazada') {
                    motivoContainer.style.display = 'block';
                    document.getElementById('motivo').setAttribute('required', 'required');
                } else {
                    motivoContainer.style.display = 'none';
                    document.getElementById('motivo').removeAttribute('required');
                }
            });
        });
    }
});
</script>
