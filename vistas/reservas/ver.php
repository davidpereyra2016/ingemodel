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
                    <span class="badge badge-pill bg-<?php echo $reserva['estado'] == 'aprobada' ? 'success' : ($reserva['estado'] == 'rechazada' ? 'danger' : ($reserva['estado'] == 'pendiente' ? 'warning' : ($reserva['estado'] == 'cancelada' ? 'secondary' : 'danger'))); ?>">
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
                            case 'baja':
                                echo 'Baja';
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
                                        <?php if ($reserva['monto_anticipo'] !== null): ?>
                                            <span class="text-muted"> - Monto: $<?php echo number_format($reserva['monto_anticipo'], 2); ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-danger">Pendiente ✗</span>
                                    <?php endif; ?>
                                    <br><small class="text-muted">Anticipo sugerido (50%): $<?php echo number_format($reserva['monto'] / 2, 2); ?></small>
                                </li>
                                <li class="list-group-item">
                                    <strong>Saldo:</strong>
                                    <?php if ($reserva['saldo_pagado']): ?>
                                        <span class="text-success">Pagado ✓</span>
                                        <?php if ($reserva['monto_saldo'] !== null): ?>
                                            <span class="text-muted"> - Monto: $<?php echo number_format($reserva['monto_saldo'], 2); ?></span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-danger">Pendiente ✗</span>
                                    <?php endif; ?>
                                    <?php 
                                    // Calcular montos reales considerando los flags de pago (misma lógica que PDF)
                                    
                                    // Para anticipo: si anticipo_pagado = 1 pero monto_anticipo es NULL/0, usar 50% del monto total
                                    if ($reserva['anticipo_pagado'] == 1) {
                                        $anticipo_real = ($reserva['monto_anticipo'] !== null && $reserva['monto_anticipo'] > 0) 
                                                       ? $reserva['monto_anticipo'] 
                                                       : ($reserva['monto'] / 2);
                                    } else {
                                        $anticipo_real = $reserva['monto_anticipo'] ?? 0;
                                    }
                                    
                                    // Para saldo: si saldo_pagado = 1 pero monto_saldo es NULL/0, calcular el resto
                                    if ($reserva['saldo_pagado'] == 1) {
                                        if ($reserva['monto_saldo'] !== null && $reserva['monto_saldo'] > 0) {
                                            $saldo_real = $reserva['monto_saldo'];
                                        } else {
                                            // Si pagó saldo pero no hay monto específico, es el resto del monto total menos el anticipo
                                            $saldo_real = $reserva['monto'] - $anticipo_real;
                                        }
                                    } else {
                                        $saldo_real = $reserva['monto_saldo'] ?? 0;
                                    }
                                    
                                    $total_pagado = $anticipo_real + $saldo_real;
                                    $saldo_pendiente = $reserva['monto'] - $total_pagado;
                                    ?>
                                    <br><small class="text-muted">Saldo pendiente: $<?php echo number_format($saldo_pendiente, 2); ?></small>
                                </li>
                                
                                <?php if ($saldo_pendiente > 0): ?>
                                    <li class="list-group-item bg-light border-warning">
                                        <strong class="text-warning">💰 Saldo Pendiente de Pago:</strong>
                                        <span class="text-danger fw-bold">$<?php echo number_format($saldo_pendiente, 2); ?></span>
                                        <br><small class="text-muted">Monto restante por abonar para completar la reserva</small>
                                    </li>
                                <?php elseif ($saldo_pendiente <= 0 && $total_pagado > 0): ?>
                                    <li class="list-group-item bg-light border-success">
                                        <strong class="text-success">✅ Pago Completo:</strong>
                                        <span class="text-success fw-bold">$<?php echo number_format($total_pagado, 2); ?></span>
                                        <br><small class="text-muted">Reserva completamente abonada</small>
                                    </li>
                                <?php endif; ?>
                            </ul>

                            <?php if ($reserva['estado'] == 'cancelada'): ?>
                                <div class="alert alert-danger mt-3">
                                    <strong>Motivo de la cancelación:</strong><br>
                                    <p>La cancelación se dio por superar el plazo de 48 Horas para presentar la documentación del pago de la reserva.</p>
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

                    <?php if ($_SESSION['rol'] == 'administrador'): ?>
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0"><i class="fas fa-dollar-sign me-2"></i> Editar Montos de Pago (Solo Administrador)</h5>
                                    </div>
                                    <div class="card-body">
                                        <form action="index.php?controlador=reservas&accion=actualizarMontosPago" method="POST">
                                            <input type="hidden" name="id_reserva" value="<?php echo $reserva['id']; ?>">
                                            
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="monto_anticipo" class="form-label">Monto Real del Anticipo:</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" step="0.01" class="form-control" id="monto_anticipo" name="monto_anticipo" 
                                                                   value="<?php echo $reserva['monto_anticipo'] ?? ''; ?>" 
                                                                   placeholder="<?php echo number_format($reserva['monto'] / 2, 2); ?>">
                                                        </div>
                                                        <small class="form-text text-muted">Monto sugerido (50%): $<?php echo number_format($reserva['monto'] / 2, 2); ?></small>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-group mb-3">
                                                        <label for="monto_saldo" class="form-label">Monto Real del Saldo:</label>
                                                        <div class="input-group">
                                                            <span class="input-group-text">$</span>
                                                            <input type="number" step="0.01" class="form-control" id="monto_saldo" name="monto_saldo" 
                                                                   value="<?php echo $reserva['monto_saldo'] ?? ''; ?>" 
                                                                   placeholder="<?php echo number_format($reserva['monto'] / 2, 2); ?>">
                                                        </div>
                                                        <?php 
                                                        $anticipo_real = $reserva['monto_anticipo'] ?? 0;
                                                        $saldo_sugerido = $reserva['monto'] - $anticipo_real;
                                                        ?>
                                                        <small class="form-text text-muted">Saldo pendiente: $<?php echo number_format($saldo_sugerido, 2); ?></small>
                                                    </div>
                                                </div>
                                            </div>
                                            
                                            <div class="alert alert-info">
                                                <i class="fas fa-info-circle me-2"></i>
                                                <strong>Información:</strong> Estos campos permiten ajustar los montos reales según los comprobantes de pago subidos por el usuario. 
                                                Deje vacío si no desea modificar el monto.
                                            </div>
                                            
                                            <div class="form-group text-end">
                                                <button type="submit" class="btn btn-info">
                                                    <i class="fas fa-save me-2"></i> Actualizar Montos
                                                </button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

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