<?php include_once("vistas/template.php"); ?>

<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-eye me-2"></i>Detalles de la Observación
                    </h5>
                    <a href="index.php?controlador=observaciones&accion=listar" class="btn btn-light btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Volver al Listado
                    </a>
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

                    <div class="row">
                        <!-- Información de la Observación -->
                        <div class="col-lg-8">
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>Información de la Observación</h6>
                                </div>
                                <div class="card-body">
                                    <!-- <div class="row mb-3">
                                        <div class="col-sm-3"><strong>ID:</strong></div>
                                        <div class="col-sm-9">#<?php echo $observacion['id']; ?></div>
                                    </div> -->
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Título:</strong></div>
                                        <div class="col-sm-9"><?php echo htmlspecialchars($observacion['titulo']); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Tipo:</strong></div>
                                        <div class="col-sm-9">
                                            <?php
                                            $tipos = [
                                                'documentacion' => ['text' => 'Documentación Faltante', 'class' => 'warning'],
                                                'daños' => ['text' => 'Daños al Salón/Mobiliario', 'class' => 'danger'],
                                                'incumplimiento' => ['text' => 'Incumplimiento de Normas', 'class' => 'info'],
                                                'otros' => ['text' => 'Otros', 'class' => 'secondary']
                                            ];
                                            $tipo = $tipos[$observacion['tipo_observacion']] ?? $tipos['otros'];
                                            ?>
                                            <span class="badge bg-<?php echo $tipo['class']; ?>"><?php echo $tipo['text']; ?></span>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Estado:</strong></div>
                                        <div class="col-sm-9">
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
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Descripción:</strong></div>
                                        <div class="col-sm-9">
                                            <div class="border p-3 rounded bg-light">
                                                <?php echo nl2br(htmlspecialchars($observacion['descripcion'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ($observacion['comentario_resolucion']): ?>
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Comentario de Resolución:</strong></div>
                                        <div class="col-sm-9">
                                            <div class="border p-3 rounded bg-success bg-opacity-10">
                                                <?php echo nl2br(htmlspecialchars($observacion['comentario_resolucion'])); ?>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Información de la Reserva -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-calendar-alt me-2"></i>Información de la Reserva</h6>
                                </div>
                                <div class="card-body">
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Código:</strong></div>
                                        <div class="col-sm-9">
                                            <code><?php echo $observacion['codigo_unico']; ?></code>
                                            <a href="index.php?controlador=reservas&accion=ver&id=<?php echo $observacion['id_reserva']; ?>" 
                                               class="btn btn-outline-primary btn-sm ms-2">
                                                <i class="fas fa-external-link-alt me-1"></i>Ver Reserva
                                            </a>
                                        </div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Fecha del Evento:</strong></div>
                                        <div class="col-sm-9"><?php echo date('d/m/Y', strtotime($observacion['fecha_evento'])); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Tipo de Uso:</strong></div>
                                        <div class="col-sm-9"><?php echo ucfirst($observacion['tipo_uso']); ?></div>
                                    </div>
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Ingeniero/a:</strong></div>
                                        <div class="col-sm-9">
                                            <?php echo htmlspecialchars($observacion['cliente_nombre'] . ' ' . $observacion['cliente_apellido']); ?>
                                            <small class="text-muted">(<?php echo $observacion['cliente_matricula']; ?>)</small>
                                        </div>
                                    </div>
                                    <?php if (isset($observacion['monto'])): ?>
                                    <div class="row mb-3">
                                        <div class="col-sm-3"><strong>Monto:</strong></div>
                                        <div class="col-sm-9">$<?php echo number_format($observacion['monto'], 2); ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Panel Lateral -->
                        <div class="col-lg-4">
                            <!-- Fechas -->
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-clock me-2"></i>Fechas</h6>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong>Creada:</strong><br>
                                        <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($observacion['fecha_creacion'])); ?></small>
                                    </div>
                                    <?php if ($observacion['fecha_resolucion']): ?>
                                    <div class="mb-3">
                                        <strong>Resuelta:</strong><br>
                                        <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($observacion['fecha_resolucion'])); ?></small>
                                    </div>
                                    <?php endif; ?>
                                    <div class="mb-3">
                                        <strong>Creada por:</strong><br>
                                        <small class="text-muted"><?php echo htmlspecialchars($observacion['admin_nombre'] . ' ' . $observacion['admin_apellido']); ?></small>
                                    </div>
                                </div>
                            </div>

                            <!-- Archivo Adjunto -->
                            <?php if ($observacion['archivo_adjunto']): ?>
                            <div class="card mb-4">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-paperclip me-2"></i>Archivo Adjunto</h6>
                                </div>
                                <div class="card-body text-center">
                                    <i class="fas fa-file fa-3x text-primary mb-3"></i>
                                    <p class="mb-3"><?php echo htmlspecialchars($observacion['archivo_adjunto']); ?></p>
                                    <a href="index.php?controlador=observaciones&accion=descargarArchivo&id=<?php echo $observacion['id']; ?>" 
                                       class="btn btn-primary">
                                        <i class="fas fa-download me-1"></i>Descargar
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>

                            <!-- Acciones (Solo para Administradores) -->
                            <?php if ($_SESSION['rol'] !== 'ingeniero'): ?>
                            <div class="card">
                                <div class="card-header">
                                    <h6 class="mb-0"><i class="fas fa-cogs me-2"></i>Acciones</h6>
                                </div>
                                <div class="card-body">
                                    <!-- Actualizar Estado -->
                                    <form method="POST" action="index.php?controlador=observaciones&accion=actualizarEstado" class="mb-3">
                                        <input type="hidden" name="id_observacion" value="<?php echo $observacion['id']; ?>">
                                        <div class="mb-3">
                                            <label for="estado" class="form-label">Estado:</label>
                                            <select name="estado" id="estado" class="form-select" required>
                                                <option value="activa" <?php echo ($observacion['estado'] == 'activa') ? 'selected' : ''; ?>>Activa</option>
                                                <option value="resuelta" <?php echo ($observacion['estado'] == 'resuelta') ? 'selected' : ''; ?>>Resuelta</option>
                                                <option value="cancelada" <?php echo ($observacion['estado'] == 'cancelada') ? 'selected' : ''; ?>>Cancelada</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="comentario_resolucion" class="form-label">Comentario de Resolución:</label>
                                            <textarea name="comentario_resolucion" id="comentario_resolucion" 
                                                      class="form-control" rows="3" 
                                                      placeholder="Agregar comentario sobre la resolución..."><?php echo htmlspecialchars($observacion['comentario_resolucion'] ?? ''); ?></textarea>
                                        </div>
                                        <button type="submit" class="btn btn-success w-100">
                                            <i class="fas fa-save me-1"></i>Actualizar Estado
                                        </button>
                                    </form>

                                    <!-- Eliminar Observación -->
                                    <button type="button" class="btn btn-danger w-100" onclick="confirmarEliminacion(<?php echo $observacion['id']; ?>)">
                                        <i class="fas fa-trash me-1"></i>Eliminar Observación
                                    </button>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmación para Eliminar -->
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
                <form method="POST" action="index.php?controlador=observaciones&accion=eliminar" style="display: inline;">
                    <input type="hidden" name="id_observacion" id="idObservacionEliminar">
                    <button type="submit" class="btn btn-danger">Eliminar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmarEliminacion(id) {
    document.getElementById('idObservacionEliminar').value = id;
    var modal = new bootstrap.Modal(document.getElementById('modalEliminar'));
    modal.show();
}
</script>
