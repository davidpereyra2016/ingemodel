<?php include_once("vistas/template.php"); ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-edit me-2"></i>Editar Observación
                    </h5>
                    <div>
                        <a href="index.php?controlador=observaciones&accion=ver&id=<?php echo $observacion['id']; ?>" class="btn btn-light btn-sm me-2">
                            <i class="fas fa-eye me-1"></i>Ver Detalles
                        </a>
                        <a href="index.php?controlador=observaciones&accion=listar" class="btn btn-secondary btn-sm">
                            <i class="fas fa-arrow-left me-1"></i>Volver al Listado
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <?php if (isset($_SESSION['mensaje'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i><?php echo $_SESSION['mensaje']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['mensaje']); ?>
                    <?php endif; ?>

                    <?php if (isset($_SESSION['error'])): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i><?php echo $_SESSION['error']; ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                        <?php unset($_SESSION['error']); ?>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" id="formularioEditarObservacion">
                        <input type="hidden" name="form_token" value="<?php echo $_SESSION['form_token']; ?>">
                        
                        <!-- Información Actual -->
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="alert alert-info">
                                    <h6 class="mb-2"><i class="fas fa-info-circle me-2"></i>Información Actual</h6>
                                    <div class="row">
                                        <div class="col-md-6">
                                            <strong>ID:</strong> #<?php echo $observacion['id']; ?><br>
                                            <strong>Creada:</strong> <?php echo date('d/m/Y H:i', strtotime($observacion['fecha_creacion'])); ?><br>
                                            <strong>Estado:</strong> 
                                            <?php
                                            $estados = [
                                                'activa' => ['text' => 'Activa', 'class' => 'warning'],
                                                'resuelta' => ['text' => 'Resuelta', 'class' => 'success'],
                                                'cancelada' => ['text' => 'Cancelada', 'class' => 'secondary']
                                            ];
                                            $estado = $estados[$observacion['estado']] ?? $estados['activa'];
                                            ?>
                                            <span class="badge bg-<?php echo $estado['class']; ?>"><?php echo $estado['text']; ?></span>
                                        </div>
                                        <div class="col-md-6">
                                            <strong>Reserva Actual:</strong> <?php echo $observacion['codigo_unico']; ?><br>
                                            <strong>Ingeniero/a:</strong> <?php echo htmlspecialchars($observacion['cliente_nombre'] . ' ' . $observacion['cliente_apellido']); ?><br>
                                            <strong>Fecha Evento:</strong> <?php echo date('d/m/Y', strtotime($observacion['fecha_evento'])); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Reserva Asociada -->
                        <div class="row mb-4">
                            <div class="col-md-12">
                                <label for="id_reserva" class="form-label">Reserva Asociada <span class="text-danger">*</span></label>
                                <select name="id_reserva" id="id_reserva" class="form-select" required>
                                    <option value="">Seleccione una reserva</option>
                                    <?php foreach ($reservas as $reserva): ?>
                                        <option value="<?php echo $reserva['id']; ?>" 
                                                <?php echo ($reserva['id'] == $observacion['id_reserva']) ? 'selected' : ''; ?>
                                                data-codigo="<?php echo $reserva['codigo_unico']; ?>"
                                                data-fecha="<?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?>"
                                                data-cliente="<?php echo htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido']); ?>"
                                                data-matricula="<?php echo $reserva['matricula']; ?>"
                                                data-tipo="<?php echo ucfirst($reserva['tipo_uso']); ?>">
                                            <?php echo $reserva['id']; ?> - 
                                            <?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?> - 
                                            <?php echo htmlspecialchars($reserva['nombre'] . ' ' . $reserva['apellido']); ?>
                                            <?php if (isset($reserva['estado'])): ?>
                                                <span class="text-muted">(<?php echo ucfirst($reserva['estado']); ?>)</span>
                                            <?php endif; ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Información de la reserva seleccionada -->
                        <div class="row mb-4" id="info_reserva" style="display: none;">
                            <div class="col-12">
                                <!-- Se llenará dinámicamente con JavaScript -->
                            </div>
                        </div>

                        <!-- Datos de la Observación -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="titulo" class="form-label">Título de la Observación <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="titulo" name="titulo" 
                                       value="<?php echo htmlspecialchars($observacion['titulo']); ?>" 
                                       placeholder="Ej: Documentación faltante" required maxlength="255">
                                <small class="text-muted">Máximo 255 caracteres</small>
                            </div>
                            <div class="col-md-6">
                                <label for="tipo_observacion" class="form-label">Tipo de Observación <span class="text-danger">*</span></label>
                                <select name="tipo_observacion" id="tipo_observacion" class="form-select" required>
                                    <option value="">Seleccione un tipo</option>
                                    <option value="documentacion" <?php echo ($observacion['tipo_observacion'] == 'documentacion') ? 'selected' : ''; ?>>Documentación Faltante</option>
                                    <option value="daños" <?php echo ($observacion['tipo_observacion'] == 'daños') ? 'selected' : ''; ?>>Daños al Salón/Mobiliario</option>
                                    <option value="incumplimiento" <?php echo ($observacion['tipo_observacion'] == 'incumplimiento') ? 'selected' : ''; ?>>Incumplimiento de Normas</option>
                                    <option value="otros" <?php echo ($observacion['tipo_observacion'] == 'otros') ? 'selected' : ''; ?>>Otros</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-4">
                            <div class="col-12">
                                <label for="descripcion" class="form-label">Descripción Detallada <span class="text-danger">*</span></label>
                                <textarea name="descripcion" id="descripcion" class="form-control" rows="4" 
                                          placeholder="Describa detalladamente la observación..." required><?php echo htmlspecialchars($observacion['descripcion']); ?></textarea>
                                <small class="text-muted">Proporcione todos los detalles relevantes sobre la observación</small>
                            </div>
                        </div>

                        <!-- Archivo Adjunto -->
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <label for="archivo_adjunto" class="form-label">Archivo Adjunto (Opcional)</label>
                                <input type="file" class="form-control" id="archivo_adjunto" name="archivo_adjunto" 
                                       accept=".pdf,.jpg,.jpeg,.png,.doc,.docx">
                                <small class="text-muted">Formatos permitidos: PDF, JPG, PNG, DOC, DOCX. Máximo 5MB.</small>
                                <?php if ($observacion['archivo_adjunto']): ?>
                                    <div class="mt-2">
                                        <div class="alert alert-info py-2">
                                            <i class="fas fa-paperclip me-2"></i>
                                            <strong>Archivo actual:</strong> <?php echo htmlspecialchars($observacion['archivo_adjunto']); ?>
                                            <a href="index.php?controlador=observaciones&accion=descargarArchivo&id=<?php echo $observacion['id']; ?>" 
                                               class="btn btn-sm btn-outline-primary ms-2">
                                                <i class="fas fa-download me-1"></i>Descargar
                                            </a>
                                        </div>
                                        <small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Si sube un nuevo archivo, reemplazará el actual</small>
                                    </div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <div class="card bg-light h-100">
                                    <div class="card-body">
                                        <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Información Importante</h6>
                                        <ul class="mb-0 small">
                                            <li>Los cambios se aplicarán inmediatamente.</li>
                                            <li>El ingeniero/a podrá ver la observación actualizada.</li>
                                            <li>Se mantendrá el historial de fechas de creación.</li>
                                            <li>Solo se actualizará la fecha de modificación.</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de Acción -->
                        <div class="row">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="index.php?controlador=observaciones&accion=ver&id=<?php echo $observacion['id']; ?>" 
                                           class="btn btn-secondary">
                                            <i class="fas fa-times me-1"></i>Cancelar
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-warning" id="btn-actualizar">
                                            <i class="fas fa-save me-1"></i>Actualizar Observación
                                        </button>
                                    </div>
                                </div>
                            </div>
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
document.getElementById('formularioEditarObservacion').addEventListener('submit', function(e) {
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
    var btnActualizar = document.getElementById('btn-actualizar');
    btnActualizar.disabled = true;
    btnActualizar.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i>Actualizando...';
});
</script>