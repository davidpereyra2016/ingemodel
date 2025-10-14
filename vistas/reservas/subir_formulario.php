<?php
// Asegurar acceso al código único incluso si no se pasó como variable
if (!isset($codigo_unico) && isset($_GET['codigo'])) {
    $codigo_unico = $_GET['codigo'];
}

// Helper function para obtener clase de badge
function obtenerClaseBadge($estado) {
    switch ($estado) {
        case 'pendiente': return 'badge-warning';
        case 'en_revision': return 'badge-info';
        case 'aprobada':
        case 'confirmada': return 'badge-success';
        case 'cancelada':
        case 'rechazada': 
        case 'baja': return 'badge-danger';
        default: return 'badge-secondary';
    }
}

// Mostrar mensajes de error si existen
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger mt-4 container">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm rounded mt-4">
                <div class="card-header">
                    <h3 class="mb-0 ">Subir Documentación de Reserva</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p><strong>Importante:</strong> Para completar su solicitud, debe descargar el formulario, completarlo, firmarlo y subirlo nuevamente junto con el comprobante de pago del anticipo (50% del valor total) o 100% del valor total.</p>
                    </div>

                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">Información de la Reserva</h5>
                                </div>
                                <div class="card-body">
                                    <p><strong>Fecha:</strong> <?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?></p>
                                    <p><strong>Horario:</strong> <?php echo substr($reserva['hora_inicio'], 0, 5) . ' - ' . substr($reserva['hora_fin'], 0, 5); ?></p>
                                    <p><strong>Tipo de Uso:</strong> <?php echo $reserva['tipo_uso']; ?></p>
                                    <p>
                                        <strong>Monto Total:</strong> 
                                        $<?php echo sprintf("%.2f", $reserva['monto']); ?>
                                        <?php if (isset($reserva['requiere_actualizacion']) && $reserva['requiere_actualizacion'] && $reserva['diferencia_monto'] > 0): ?>
                                            <span class="badge bg-warning text-dark ms-2">
                                                <i class="fas fa-info-circle"></i> 
                                                Incluye actualización de arancel: +$<?php echo number_format($reserva['diferencia_monto'], 2); ?>
                                            </span>
                                        <?php elseif (isset($reserva['requiere_actualizacion']) && $reserva['requiere_actualizacion'] && $reserva['diferencia_monto'] < 0): ?>
                                            <span class="badge bg-success ms-2">
                                                <i class="fas fa-check-circle"></i> 
                                                Reducción aplicada: $<?php echo number_format(abs($reserva['diferencia_monto']), 2); ?>
                                            </span>
                                        <?php endif; ?>
                                    </p>
                                    <p><strong>Anticipo (50%):</strong> $<?php echo sprintf("%.2f", $reserva['monto'] / 2); ?></p>
                                    
                                    <?php if (isset($reserva['requiere_actualizacion']) && $reserva['requiere_actualizacion'] && abs($reserva['diferencia_monto']) > 0): ?>
                                    <div class="alert alert-info mt-3 mb-0">
                                        <h6 class="alert-heading"><i class="fas fa-exclamation-circle"></i> Información Importante</h6>
                                        <p class="mb-1">
                                            <strong>El arancel para su fecha de evento ha sido actualizado.</strong>
                                        </p>
                                        <ul class="mb-0">
                                            <li>Monto anterior: $<?php echo number_format($reserva['monto_original'] ?? $reserva['monto'], 2); ?></li>
                                            <li>Nuevo monto: $<?php echo number_format($reserva['monto'], 2); ?></li>
                                            <?php if ($reserva['diferencia_monto'] > 0): ?>
                                                <li class="text-danger fw-bold">Diferencia adicional: $<?php echo number_format($reserva['diferencia_monto'], 2); ?></li>
                                            <?php endif; ?>
                                        </ul>
                                        <small class="text-muted d-block mt-2">
                                            <i class="fas fa-info-circle"></i> 
                                            Los aranceles se actualizan según la fecha del evento. 
                                            Por favor, considere este monto al realizar su pago.
                                        </small>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- INICIO: Bloque de Estado y Contador -->
                    <div id="estado-reserva-info" class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-warning text-dark">
                                    <h5 class="mb-0">Estado de la Reserva</h5>
                                </div>
                                <div class="card-body">
                                    <p id="estado-actual"><strong>Estado:</strong> <span class="badge <?php echo obtenerClaseBadge($reserva['estado']); ?>"><?php echo ucfirst($reserva['estado']); ?></span></p>
                                    <div id="contador-container" <?php echo ($reserva['estado'] !== 'pendiente') ? 'style="display: none;"' : ''; ?>>
                                        <p><strong>Tiempo restante para completar:</strong></p>
                                        <div id="countdown-timer" class="alert alert-warning font-weight-bold h4"></div>
                                    </div>
                                    <div id="mensaje-estado" class="alert" style="display: none;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    

                    <?php if (!empty($formularios)): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Descargar Formulario de Solicitud</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>Descargue el formulario de solicitud, complete todos los datos requeridos y fírmelo.</p>
                                        <?php foreach ($formularios as $form): ?>
                                            <a href="assets/docs/<?php echo $form['archivo']; ?>" class="btn btn-success-theme mb-2" target="_blank">
                                                Descargar <?php echo htmlspecialchars($form['nombre']); ?>
                                            </a><br>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($formularios_municipales)): ?>
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Descargar Formulario Municipal</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>Descargue el formulario Municipal</p>
                                        <?php foreach ($formularios_municipales as $form): ?>
                                            <a href="assets/docs/<?php echo $form['archivo']; ?>" class="btn btn-primary mb-2" target="_blank">
                                                Descargar <?php echo htmlspecialchars($form['nombre']); ?>
                                            </a><br>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                    <!-- fin descargar formularios -->
                    <?php
                    // Determinar si los campos deben estar deshabilitados
                    // Permitimos subir archivos si está pendiente, en revisión, aprobada o confirmada
                    // EXCEPCIÓN: Permitir a administradores en estado 'baja' para devolución
                    $esAdminEnBaja = ($reserva['estado'] === 'baja' && isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador');
                    $camposDeshabilitados = !in_array($reserva['estado'], ['pendiente', 'en_revision', 'aprobada', 'confirmada']) && !$esAdminEnBaja;
                    $motivoDeshabilitado = '';
                    
                    if ($camposDeshabilitados) {
                        // Solo mostrar mensaje de error para estados que realmente impiden subir archivos
                        switch ($reserva['estado']) {
                            case 'cancelada':
                                $motivoDeshabilitado = 'La reserva ha sido cancelada' . ($reserva['motivo_rechazo'] ? ': ' . htmlspecialchars($reserva['motivo_rechazo']) : '.');
                                break;
                            case 'rechazada':
                                $motivoDeshabilitado = 'La reserva ha sido rechazada' . ($reserva['motivo_rechazo'] ? ': ' . htmlspecialchars($reserva['motivo_rechazo']) : '.');
                                break;
                            case 'baja':
                                // Solo mostrar mensaje si NO es administrador
                                if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
                                    $motivoDeshabilitado = 'La reserva ha sido dada de baja.';
                                }
                                break;
                            default:
                                $motivoDeshabilitado = 'La reserva no se puede modificar en este estado.';
                        }
                        if (!empty($motivoDeshabilitado)) {
                            echo '<div class="alert alert-danger">' . $motivoDeshabilitado . ' No se pueden subir más archivos.</div>';
                        }
                    } elseif ($reserva['estado'] === 'aprobada' || $reserva['estado'] === 'confirmada') {
                        // Mostrar mensaje informativo para estados aprobados
                        // echo '<div class="alert alert-success">Reserva ' . $reserva['estado'] . '. Puede continuar subiendo los archivos faltantes (comprobantes de pago o formularios adicionales).</div>';
                    }
                    ?>

                    <form action="index.php?controlador=reservas&accion=subirFormulario&codigo=<?php echo isset($codigo_unico) ? $codigo_unico : (isset($_GET['codigo']) ? $_GET['codigo'] : ''); ?>" method="POST" enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Subir Formulario de solicitud Completado</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="formulario">Seleccione el formulario de solicitud completado (PDF, DOC, DOCX):</label>
                                            <input type="file" class="form-control-file" id="formulario" name="formulario" accept=".pdf,.doc,.docx" <?php echo $camposDeshabilitados ? 'disabled' : ''; ?>>
                                            <?php if ($reserva['archivo_formulario']): ?>
                                                <div class="mt-2">
                                                    <small class="text-success">Ya ha subido un formulario. Si sube otro, reemplazará al anterior.</small>
                                                    <br>
                                                    <a href="assets/uploads/<?php echo $reserva['archivo_formulario']; ?>" target="_blank">Ver formulario actual</a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Subir formulario municipal -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Subir Formulario Municipal</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="formulario_municipal">Seleccione el formulario municipal completado (PDF, DOC, DOCX):</label>
                                            <input type="file" class="form-control-file" id="formulario_municipal" name="formulario_municipal" accept=".pdf,.doc,.docx" <?php echo $camposDeshabilitados ? 'disabled' : ''; ?>>
                                            <?php if ($reserva['archivo_municipal']): ?>
                                                <div class="mt-2">
                                                    <small class="text-success">Ya ha subido un formulario. Si sube otro, reemplazará al anterior.</small>
                                                    <br>
                                                    <a href="assets/uploads/<?php echo $reserva['archivo_municipal']; ?>" target="_blank">Ver formulario actual</a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- subir comprobante de pago 50% -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Subir Comprobante de Pago</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>Suba el comprobante de pago del anticipo (50% del valor total).</p>
                                        <div class="form-group">
                                            <label for="comprobante">Seleccione el comprobante de pago (PDF, JPG, PNG):</label>
                                            <input type="file" class="form-control-file" id="comprobante" name="comprobante" accept=".pdf,.jpg,.jpeg,.png" <?php echo $camposDeshabilitados ? 'disabled' : ''; ?>>
                                            <?php if ($reserva['archivo_comprobante']): ?>
                                                <div class="mt-2">
                                                    <small class="text-success">Ya ha subido un comprobante. Si sube otro, reemplazará al anterior.</small>
                                                    <br>
                                                    <a href="assets/uploads/<?php echo $reserva['archivo_comprobante']; ?>" target="_blank">Ver comprobante actual</a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- subir comprobante de pago 100% -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">Subir Comprobante de Pago (50% del valor total o 100% del valor total)</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>Suba el comprobante de pago del anticipo (50% del valor total o 100% del valor total).</p>
                                        <div class="form-group">
                                            <label for="comprobante_total">Seleccione el comprobante de pago (PDF, JPG, PNG):</label>
                                            <input type="file" class="form-control-file" id="comprobante_total" name="comprobante_total" accept=".pdf,.jpg,.jpeg,.png" <?php echo $camposDeshabilitados ? 'disabled' : ''; ?>>
                                            <?php if ($reserva['archivo_comprobante_total']): ?>
                                                <div class="mt-2">
                                                    <small class="text-success">Ya ha subido un comprobante. Si sube otro, reemplazará al anterior.</small>
                                                    <br>
                                                    <a href="assets/uploads/<?php echo $reserva['archivo_comprobante_total']; ?>" target="_blank">Ver comprobante actual</a>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- fin subir comprobante de pago 100% -->
                        
                        <?php if ($reserva['estado'] == 'baja' && isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador'): ?>
                        <!-- SECCIÓN DE DEVOLUCIÓN - Solo para administradores cuando la reserva está dada de baja -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card border-info">
                                    <div class="card-header bg-info text-white">
                                        <h5 class="mb-0"><i class="fas fa-money-bill-wave me-2"></i>Comprobante de Devolución</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="alert alert-info">
                                            <i class="fas fa-info-circle me-2"></i>
                                            <strong>Reserva dada de baja:</strong> Puede subir el comprobante de devolución del dinero pagado.
                                        </div>
                                        
                                        <div class="row">
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="monto_devolucion" class="form-label"><strong>Monto a Devolver:</strong></label>
                                                    <div class="input-group">
                                                        <span class="input-group-text">$</span>
                                                        <input type="number" class="form-control" id="monto_devolucion" name="monto_devolucion" 
                                                               step="0.01" min="0" max="<?php echo $reserva['monto']; ?>"
                                                               value="<?php echo $reserva['monto_devolucion'] ?? ''; ?>"
                                                               placeholder="Ingrese el monto a devolver">
                                                    </div>
                                                    <small class="text-muted">Monto máximo: $<?php echo sprintf("%.2f", $reserva['monto']); ?></small>
                                                </div>
                                            </div>
                                            <div class="col-md-6">
                                                <div class="form-group mb-3">
                                                    <label for="comprobante_devolucion" class="form-label"><strong>Comprobante de Devolución:</strong></label>
                                                    <input type="file" class="form-control" id="comprobante_devolucion" name="comprobante_devolucion" 
                                                           accept=".pdf,.jpg,.jpeg,.png">
                                                    <?php if (!empty($reserva['archivo_comprobante_devolucion'])): ?>
                                                        <div class="mt-2">
                                                            <small class="text-success">Ya ha subido un comprobante de devolución.</small>
                                                            <br>
                                                            <a href="assets/uploads/<?php echo $reserva['archivo_comprobante_devolucion']; ?>" target="_blank" class="btn btn-sm btn-outline-info">
                                                                <i class="fas fa-eye me-1"></i>Ver comprobante actual
                                                            </a>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <div class="form-group mb-3">
                                            <label for="observaciones_devolucion" class="form-label"><strong>Observaciones de la Devolución:</strong></label>
                                            <textarea class="form-control" id="observaciones_devolucion" name="observaciones_devolucion" 
                                                      rows="3" placeholder="Ingrese observaciones sobre la devolución (motivo, método de pago, etc.)"><?php echo htmlspecialchars($reserva['observaciones_devolucion'] ?? ''); ?></textarea>
                                        </div>
                                        
                                        <?php if (!empty($reserva['fecha_devolucion'])): ?>
                                        <div class="alert alert-success">
                                            <i class="fas fa-check-circle me-2"></i>
                                            <strong>Devolución registrada:</strong> <?php echo date('d/m/Y H:i', strtotime($reserva['fecha_devolucion'])); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <!-- FIN SECCIÓN DE DEVOLUCIÓN -->
                        <?php else: ?>
                        <!-- alerta de recordatorio -->
                        <div class="alert alert-warning">
                            <p><strong>Recuerde:</strong> Una vez que suba estos documentos, su solicitud será revisada por un administrador para su aprobación final.</p>
                        </div>
                        <?php endif; ?>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <button type="submit" id="submit-button" class="btn btn-success-theme" <?php echo $camposDeshabilitados ? 'disabled' : ''; ?>>Subir Documentos</button>
                    <a href="index.php?controlador=reservas&accion=listar" class="btn btn-light">Volver</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- INICIO: Script del Contador y Verificación -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fechaVencimientoStr = "<?php echo $reserva['fecha_vencimiento']; ?>";
        const codigoUnico = "<?php echo isset($codigo_unico) ? $codigo_unico : (isset($_GET['codigo']) ? $_GET['codigo'] : ''); ?>";
        const estadoInicial = "<?php echo $reserva['estado']; ?>";

        const countdownElement = document.getElementById('countdown-timer');
        const estadoActualElement = document.getElementById('estado-actual').querySelector('span');
        const contadorContainer = document.getElementById('contador-container');
        const mensajeEstadoElement = document.getElementById('mensaje-estado');
        const inputs = document.querySelectorAll('input[type=file]');
        const submitButton = document.getElementById('submit-button');

        let intervalId = null;
        let checkIntervalId = null;

        // Verificar si ya hay un comprobante de pago
        function verificarComprobantePago() {
            // Verificar directamente en el DOM si existen enlaces a comprobantes
            const tienePagoAnticipo = <?php echo !empty($reserva['archivo_comprobante']) ? 'true' : 'false'; ?>;
            const tienePagoTotal = <?php echo !empty($reserva['archivo_comprobante_total']) ? 'true' : 'false'; ?>;
            
            return tienePagoAnticipo || tienePagoTotal;
        }

        function actualizarContador() {
            // Si ya tiene pago registrado, no mostrar el contador sino un mensaje positivo
            if (verificarComprobantePago()) {
                countdownElement.innerHTML = "Pago registrado";
                countdownElement.classList.remove('alert-warning', 'alert-danger');
                countdownElement.classList.add('alert-success');
                clearInterval(intervalId);
                return;
            }
            
            const ahora = new Date().getTime();
            // Asegurarse que la fecha de vencimiento se interpreta correctamente
            const fechaVencimiento = new Date(fechaVencimientoStr.replace(' ', 'T')).getTime(); 
            const diferencia = fechaVencimiento - ahora;

            if (diferencia <= 0) {
                // Si el tiempo expiró, verificar en el servidor si hay pagos (por si se subieron despues)
                fetch(`index.php?controlador=reservas&accion=verificarEstado&codigo=${codigoUnico}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.tiene_pago === true) {
                            // Si tiene pago, mostrar mensaje positivo
                            countdownElement.innerHTML = "Pago registrado";
                            countdownElement.classList.remove('alert-warning', 'alert-danger');
                            countdownElement.classList.add('alert-success');
                        } else {
                            // Si no tiene pago y expiró, mostrar mensaje de expiración
                            countdownElement.innerHTML = "Tiempo Expirado";
                            countdownElement.classList.remove('alert-warning');
                            countdownElement.classList.add('alert-danger');
                        }
                    })
                    .catch(error => {
                        console.error('Error al verificar estado:', error);
                        countdownElement.innerHTML = "Tiempo Expirado";
                        countdownElement.classList.remove('alert-warning');
                        countdownElement.classList.add('alert-danger');
                    });
                    
                clearInterval(intervalId);
                verificarEstadoReserva(); 
                return;
            }

            const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
            const horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
            const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);

            countdownElement.innerHTML = `${dias}d ${horas}h ${minutos}m ${segundos}s`;
        }

        function deshabilitarFormulario(mensaje, estado) {
            // Si el estado es aprobada o confirmada, mostramos el mensaje pero NO deshabilitamos los campos
            if (estado === 'aprobada' || estado === 'confirmada') {
                contadorContainer.style.display = 'none'; // Ocultar contador
                mensajeEstadoElement.textContent = mensaje;
                mensajeEstadoElement.className = 'alert alert-success'; // Cambiamos a clase success
                mensajeEstadoElement.style.display = 'block';
                if (intervalId) clearInterval(intervalId);
                if (checkIntervalId) clearInterval(checkIntervalId); // Detener chequeos
            } else {
                // Para estados cancelada, rechazada o baja, deshabilitamos todo EXCEPTO campos de devolución para administradores
                const esAdministrador = <?php echo (isset($_SESSION['rol']) && $_SESSION['rol'] === 'administrador') ? 'true' : 'false'; ?>;
                const esBaja = estado === 'baja';
                
                inputs.forEach(input => {
                    // No deshabilitar campos de devolución si es administrador y estado es baja
                    if (esAdministrador && esBaja && 
                        (input.id === 'monto_devolucion' || 
                         input.id === 'comprobante_devolucion' || 
                         input.id === 'observaciones_devolucion')) {
                        input.disabled = false;
                    } else {
                        input.disabled = true;
                    }
                });
                
                // Habilitar botón de envío si es administrador en estado baja (para devolución)
                if (esAdministrador && esBaja) {
                    submitButton.disabled = false;
                } else {
                    submitButton.disabled = true;
                }
                
                contadorContainer.style.display = 'none'; // Ocultar contador
                mensajeEstadoElement.textContent = mensaje;
                mensajeEstadoElement.className = 'alert alert-danger'; // Clase base + clase específica
                mensajeEstadoElement.style.display = 'block';
                if (intervalId) clearInterval(intervalId);
                if (checkIntervalId) clearInterval(checkIntervalId); // Detener chequeos
            }
        }

        function actualizarEstadoUI(estado, motivo) {
            const badgeClasses = {
                'pendiente': 'badge-warning',
                'en_revision': 'badge-info',
                'aprobada': 'badge-success',
                'confirmada': 'badge-success',
                'cancelada': 'badge-danger',
                'rechazada': 'badge-danger',
                'baja': 'badge-danger',
                'no_encontrada': 'badge-dark',
                'error': 'badge-danger'
            };
            estadoActualElement.textContent = estado.charAt(0).toUpperCase() + estado.slice(1).replace('_', ' ');
            estadoActualElement.className = 'badge ' + (badgeClasses[estado] || 'badge-secondary');

            if (['cancelada', 'rechazada', 'baja', 'aprobada', 'confirmada', 'no_encontrada', 'error'].includes(estado)) {
                let mensaje = '';
                if (estado === 'cancelada') {
                    mensaje = 'Reserva cancelada' + (motivo ? ': ' + motivo : '.');
                } else if (estado === 'rechazada') {
                    mensaje = 'Reserva rechazada' + (motivo ? ': ' + motivo : '.');
                } else if (estado === 'baja') {
                    mensaje = 'Reserva dada de baja.';
                } else if (estado === 'aprobada' || estado === 'confirmada') {
                    mensaje = 'Reserva ' + estado + '. Puede continuar subiendo los archivos faltantes (comprobantes de pago o formularios adicionales).';
                } else {
                    mensaje = 'No se pudo verificar el estado o la reserva no existe.';
                }
                deshabilitarFormulario(mensaje, estado);
            } else {
                // Si vuelve a pendiente o en revisión (poco probable pero posible), rehabilitar
                // (Considerar si esto es deseado)
                // inputs.forEach(input => input.disabled = false);
                // submitButton.disabled = false;
                contadorContainer.style.display = 'block';
                mensajeEstadoElement.style.display = 'none';
                if (!intervalId && estado === 'pendiente') { 
                    // Reiniciar contador si no estaba corriendo y el estado es pendiente
                    intervalId = setInterval(actualizarContador, 1000);
                    actualizarContador(); // Llamada inicial
                }
            }
        }

        function verificarEstadoReserva() {
            fetch(`index.php?controlador=reservas&accion=verificarEstado&codigo=${codigoUnico}`)
                .then(response => response.json())
                .then(data => {
                    console.log('Estado verificado:', data); // Para depuración
                    // Ocultar cualquier mensaje de error previo
                    mensajeEstadoElement.style.display = 'none';
                    
                    if (data && data.estado) {
                        // Si hay comprobantes de pago, mostrar mensaje adecuado en el contador
                        if (data.tiene_pago === true) {
                            countdownElement.innerHTML = "Pago registrado";
                            countdownElement.classList.remove('alert-warning', 'alert-danger');
                            countdownElement.classList.add('alert-success');
                            clearInterval(intervalId); // Detener contador
                        }
                        
                        actualizarEstadoUI(data.estado, data.motivo_rechazo);
                    } else {
                        actualizarEstadoUI('error', 'Respuesta inválida del servidor');
                    }
                })
                .catch(error => {
                    console.error('Error al verificar estado:', error);
                    // No mostrar error si ya hay un pago registrado
                    if (!verificarComprobantePago()) {
                        mensajeEstadoElement.textContent = 'Error de red al verificar estado. Intente recargar la página.';
                        mensajeEstadoElement.className = 'alert alert-danger';
                        mensajeEstadoElement.style.display = 'block';
                    }
                });
        }

        // Comprobar primero si ya hay un comprobante de pago subido
        if (verificarComprobantePago()) {
            // Si ya hay pago, mostrar mensaje positivo y no iniciar el contador
            countdownElement.innerHTML = "Pago registrado";
            countdownElement.classList.remove('alert-warning', 'alert-danger');
            countdownElement.classList.add('alert-success');
            // Iniciar verificación periódica del estado de todos modos
            checkIntervalId = setInterval(verificarEstadoReserva, 30000); 
        }
        // Iniciar contador solo si el estado inicial es 'pendiente' y no hay comprobante de pago
        else if (estadoInicial === 'pendiente') {
            intervalId = setInterval(actualizarContador, 1000);
            actualizarContador(); // Llamada inicial
            // Iniciar verificación periódica del estado
            checkIntervalId = setInterval(verificarEstadoReserva, 30000); // Verificar cada 30 segundos
        } else {
            // Si el estado inicial no es pendiente, mostramos el mensaje correspondiente
            // La lógica PHP ya debería haber deshabilitado los campos, pero reforzamos
            actualizarEstadoUI(estadoInicial, "<?php echo addslashes($reserva['motivo_rechazo'] ?? ''); ?>");
        }

        // Primera verificación inmediata por si cambió entre carga de página y ejecución JS
        // verificarEstadoReserva(); // Descomentar si se quiere una verificación extra inmediata
    });
</script>
<!-- FIN: Script del Contador y Verificación -->