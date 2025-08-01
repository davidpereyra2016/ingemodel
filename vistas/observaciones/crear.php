

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

<div class="container">
    <div class="row mb-4 mt-4">
        <div class="col-md-10 offset-md-1">
            <div class="card">
                <div class="card-header bg-light">
                    <h3 class="mb-0 card-title color-success">
                        <i class="bi bi-plus-circle me-2"></i>Nueva Observación
                    </h3>
                </div>
                <div class="card-body">
                    <form id="formularioObservacion" action="index.php?controlador=observaciones&accion=crear" method="POST" enctype="multipart/form-data">
                        <div class="alert alert-info">
                            <p>
                                <strong>Información:</strong> Complete este formulario para registrar una observación sobre una reserva.
                                Las observaciones permiten comunicar al ingeniero/a sobre documentación faltante, daños, incumplimientos u otros aspectos relacionados con el uso del salón.
                            </p>
                        </div>

                        <!-- Selección de Reserva -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="id_reserva" class="form-label">Seleccione una reserva:</label>
                                <select class="form-control" id="id_reserva" name="id_reserva" required>
                                    <option value="">Seleccione una reserva</option>
                                    <?php foreach ($reservas as $reserva): ?>
                                        <option value="<?php echo $reserva['id']; ?>"
                                                data-codigo="<?php echo $reserva['codigo_unico']; ?>"
                                                data-fecha="<?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?>"
                                                data-cliente="<?php echo $reserva['nombre'] . ' ' . $reserva['apellido']; ?>"
                                                data-matricula="<?php echo $reserva['matricula']; ?>"
                                                data-tipo="<?php echo $reserva['tipo_uso']; ?>">
                                            <?php echo $reserva['id']; ?> - 
                                            <?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?> - 
                                            <?php echo $reserva['nombre'] . ' ' . $reserva['apellido']; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Información de la Reserva Seleccionada -->
                        <div id="info_reserva" class="alert alert-light" style="display: none;">
                            <h6 class="text-primary">Información de la Reserva:</h6>
                            <div class="row">
                                <div class="col-md-3">
                                    <strong>Código:</strong><br>
                                    <span id="info_codigo">-</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Fecha:</strong><br>
                                    <span id="info_fecha">-</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Ingeniero/a:</strong><br>
                                    <span id="info_cliente">-</span>
                                </div>
                                <div class="col-md-3">
                                    <strong>Tipo:</strong><br>
                                    <span id="info_tipo">-</span>
                                </div>
                            </div>
                        </div>

                        <!-- Datos de la Observación -->
                        <div class="row mb-4">
                            <div class="col-md-8">
                                <label for="titulo" class="form-label">Título de la Observación:</label>
                                <input type="text" class="form-control" id="titulo" name="titulo" required maxlength="255"
                                       placeholder="Ej: Documentación faltante, Daño en mobiliario, etc.">
                            </div>
                            <div class="col-md-4">
                                <label for="tipo_observacion" class="form-label">Tipo de Observación:</label>
                                <select class="form-control" id="tipo_observacion" name="tipo_observacion" required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="documentacion">Documentación Faltante</option>
                                    <option value="daños">Daños al Salón/Mobiliario</option>
                                    <option value="incumplimiento">Incumplimiento de Normas</option>
                                    <option value="otros">Otros</option>
                                </select>
                            </div>
                        </div>

                        <div class="form-group mb-4">
                            <label for="descripcion" class="form-label">Descripción Detallada:</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="5" required
                                      placeholder="Describa detalladamente la observación, incluyendo todos los aspectos relevantes que el ingeniero/a debe conocer..."></textarea>
                            <small class="text-muted">Sea específico y claro en la descripción para que el ingeniero/a comprenda la situación.</small>
                        </div>

                        <!-- Archivo Adjunto -->
                        <div class="form-group mb-4">
                            <label for="archivo_adjunto" class="form-label">Archivo Adjunto (Opcional):</label>
                            <input type="file" class="form-control" id="archivo_adjunto" name="archivo_adjunto" 
                                   accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                            <small class="text-muted">Formatos permitidos: PDF, JPG, PNG, DOC, DOCX. Máximo 5MB.</small>
                        </div>

                        <!-- Información Adicional -->
                        <div class="alert alert-warning">
                            <h6><i class="bi bi-info-circle me-2"></i>Importante:</h6>
                            <ul class="mb-0">
                                <li>La observación será visible para el ingeniero/a asociado a la reserva.</li>
                                <li>Se puede adjuntar documentación de respaldo (fotos, documentos, etc.).</li>
                                <li>El ingeniero/a podrá descargar el archivo adjunto si se proporciona.</li>
                                <li>Una vez creada, podrá cambiar el estado de la observación (activa/resuelta/cancelada).</li>
                            </ul>
                        </div>

                        <!-- Token de seguridad anti-reenvío -->
                        <input type="hidden" name="form_token" value="<?php echo isset($_SESSION['form_token']) ? $_SESSION['form_token'] : ''; ?>">
                        
                        <div class="form-group mt-4 border-top pt-3 d-flex justify-content-between align-items-center">
                            <a href="index.php?controlador=observaciones&accion=listar" class="btn btn-light">
                                <i class="bi bi-arrow-left me-2"></i>Volver
                            </a>
                            <button type="submit" id="btn-enviar" class="btn btn-success-theme">
                                <i class="bi bi-check-circle me-2"></i>Crear Observación
                            </button>
                        </div>

                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Inicializar Select2 si está disponible
if (typeof $ !== 'undefined' && typeof $.fn.select2 !== 'undefined') {
    $('#id_reserva').select2({
        placeholder: 'Seleccione una reserva',
        allowClear: true,
        width: '100%'
    });
}
    
    // Validación del archivo
    document.getElementById('archivo_adjunto').addEventListener('change', function() {
        var file = this.files[0];
        if (file) {
            var maxSize = 5 * 1024 * 1024; // 5MB
            var allowedTypes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png', 
                               'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            
            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'error',
                    title: 'Archivo muy grande',
                    text: 'El archivo no puede superar los 5MB.'
                });
                this.value = '';
                return;
            }
            
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: 'Tipo de archivo no permitido',
                    text: 'Solo se permiten archivos PDF, JPG, PNG, DOC y DOCX.'
                });
                this.value = '';
                return;
            }
        }
    });
    
    // Validación del formulario
    document.getElementById('formularioObservacion').addEventListener('submit', function(e) {
        var reserva = document.getElementById('id_reserva').value;
        var titulo = document.getElementById('titulo').value.trim();
        var descripcion = document.getElementById('descripcion').value.trim();
        var tipo = document.getElementById('tipo_observacion').value;
        
        if (!reserva || !titulo || !descripcion || !tipo) {
            e.preventDefault();
            Swal.fire({
                icon: 'error',
                title: 'Campos incompletos',
                text: 'Por favor, complete todos los campos obligatorios.'
            });
            return false;
        }
        
        // Deshabilitar botón para evitar doble envío
        var btnEnviar = document.getElementById('btn-enviar');
        btnEnviar.disabled = true;
        btnEnviar.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Creando...';
    });
</script>