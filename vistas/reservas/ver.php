<?php if (isset($_SESSION['error'])): ?>
    <div class="container row mb-4 mt-4 mx-auto" id="alertError">
        <div class="alert alert-danger alert-dismissible col-md-8 offset-md-2 fade show d-flex align-items-center" role="alert">
            <p class="mb-0"> <?php echo $_SESSION['error']; ?>
                <?php unset($_SESSION['error']); ?>
            </p>
            <button type="button" class="btn-close top-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<?php if (isset($_SESSION['mensaje'])): ?>
    <div class="container row mb-4 mt-4 mx-auto" id="alertError">
        <div class="alert alert-success alert-dismissible col-md-8 offset-md-2 fade show d-flex align-items-center" role="alert">
            <p class="mb-0"> <?php echo $_SESSION['mensaje']; ?>
                <?php unset($_SESSION['mensaje']); ?>
            </p>
            <button type="button" class="btn-close top-2" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    </div>
<?php endif; ?>

<div class="container">

    <div class="row mb-4 mt-4">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-light d-flex justify-content-between align-items-center">
                    <h3 class="mb-0 card-title">Detalles de la Reserva #<?php echo $reserva['id']; ?></h3>
                    <span class="badge badge-pill bg-<?php echo $reserva['estado'] == 'aprobada' ? 'success' : ($reserva['estado'] == 'rechazada' ? 'danger' : ($reserva['estado'] == 'pendiente' ? 'warning' : 'secondary')); ?>">
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

                            <ul class="list-group">
                                <li class="list-group-item">
                                    <h5 class="card-title text-success mb-0">
                                        <i class="bi bi-file-earmark-text me-2"></i>
                                        Información de la Reserva
                                    </h5>
                                </li>
                                <li class="list-group-item">
                                    <strong>Fecha del Evento:</strong> <?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Horario:</strong> <?php echo substr($reserva['hora_inicio'], 0, 5) . ' - ' . substr($reserva['hora_fin'], 0, 5); ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Tipo de Uso:</strong> <?php echo $reserva['tipo_uso']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Fecha de Solicitud:</strong> <?php echo date('d/m/Y H:i', strtotime($reserva['fecha_solicitud'])); ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Monto Total:</strong> $<?php echo sprintf("%.2f", $reserva['monto']); ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Motivo de Uso:</strong> <?php echo $reserva['motivo_de_uso']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Anticipo:</strong>
                                    <?php if ($reserva['anticipo_pagado']): ?>
                                        <span class="text-success">Pagado ✓</span>
                                    <?php else: ?>
                                        <span class="text-danger">Pendiente ✗</span>
                                    <?php endif; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Saldo:</strong>
                                    <?php if ($reserva['saldo_pagado']): ?>
                                        <span class="text-success">Pagado ✓</span>
                                    <?php else: ?>
                                        <span class="text-danger">Pendiente ✗</span>
                                    <?php endif; ?>
                                </li>
                            </ul>

                            <?php if ($reserva['estado'] == 'rechazada' && !empty($reserva['motivo_rechazo'])): ?>
                                <div class="alert alert-danger mt-3">
                                    <strong>Motivo del rechazo:</strong><br>
                                    <?php echo nl2br($reserva['motivo_rechazo']); ?>
                                </div>
                            <?php endif; ?>

                        </div>

                        <div class="col-md-6">
                            <ul class="list-group">
                                <li class="list-group-item">
                                    <h5 class="card-title text-success mb-0"><i class="bi bi-person me-2"></i>Datos del Solicitante</h5>
                                </li>
                                <li class="list-group-item">
                                    <strong>Nombre:</strong> <?php echo $reserva['nombre'] . ' ' . $reserva['apellido']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Matrícula:</strong> <?php echo $reserva['matricula']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Email:</strong> <?php echo $reserva['email']; ?>
                                </li>
                                <li class="list-group-item">
                                    <strong>Teléfono:</strong> <?php echo $reserva['telefono']; ?>
                                </li>
                            </ul>
                        </div>
                    </div>

                    <div class="row mt-4">
                        <div class="col-12">
                            <div class="card border-success p-0">
                                <div class="card-header">
                                    <h5 class="card-title text-success mb-0"><i class="bi bi-file-earmark-text me-2"></i> Documentos</h5>
                                </div>
                                <div class="card-body row">
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
                    </div>

                    <?php if ($_SESSION['rol'] == 'administrador' && $reserva['estado'] == 'pendiente'): ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-warning">
                                    <div class="card-header border-warning">
                                        <h5 class="mb-0 text-success"><i class="bi bi-gear me-2"></i> Acciones Administrativas</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="index.php?controlador=reservas&accion=aprobarRechazar" method="POST">
                                            <input type="hidden" name="id_reserva" value="<?php echo $reserva['id']; ?>">

                                            <div class="form-group">
                                                <label for="estado" class="form-label">Cambiar Estado:</label>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="estado" id="estado_aprobar" value="aprobada" required>
                                                    <label class="form-check-label" for="estado_aprobar">Aprobar Solicitud</label>
                                                </div>
                                                <div class="form-check">
                                                    <input class="form-check-input" type="radio" name="estado" id="estado_rechazar" value="rechazada">
                                                    <label class="form-check-label" for="estado_rechazar">Rechazar Solicitud</label>
                                                </div>
                                            </div>

                                            <div class="form-group ms-2 mt-2 mb-2" id="motivo_rechazo_container" style="display: none;">
                                                <label for="motivo" class="form-label">Motivo del Rechazo:</label>
                                                <textarea class="form-control" id="motivo" name="motivo" rows="3"></textarea>
                                            </div>

                                            <div class="form-group text-right mt-4">
                                                <button type="submit" class="btn btn-light btn-sm border-success"><i class="fas fa-save me-1"></i> Guardar Cambios</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                </div>

                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?controlador=reservas&accion=listar" class="btn btn-light">
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver a la Lista
                    </a>

                    <?php if (($reserva['estado'] == 'pendiente' || $reserva['estado'] == 'aprobada') &&
                        (!$reserva['archivo_formulario'] || !$reserva['archivo_comprobante'] ||
                            !$reserva['archivo_municipal'] || !$reserva['archivo_comprobante_total'])
                    ): ?>
                        <a href="index.php?controlador=reservas&accion=subirFormulario&codigo=<?php echo $reserva['codigo_unico']; ?>" class="btn btn-success-theme">
                            <i class="fas fa-upload me-2"></i> Subir Archivos
                        </a>
                    <?php endif; ?>

                    <?php if ($reserva['estado'] == 'aprobada'): ?>
                        <a href="index.php?controlador=reservas&accion=generarPDF&id=<?php echo $reserva['id']; ?>" class="btn btn-secondary">
                            <i class="fas fa-file-pdf me-2"></i> Descargar Comprobante
                        </a>
                    <?php endif; ?>
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