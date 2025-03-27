<?php
// Mostrar mensajes de error si existen
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Subir Documentación de Reserva</h3>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <p><strong>Importante:</strong> Para completar su solicitud, debe descargar el formulario, completarlo, firmarlo y subirlo nuevamente junto con el comprobante de pago del anticipo (50% del valor total).</p>
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
                                    <p><strong>Monto Total:</strong> $<?php echo number_format($reserva['monto'], 2, ',', '.'); ?></p>
                                    <p><strong>Anticipo (50%):</strong> $<?php echo number_format($reserva['monto'] / 2, 2, ',', '.'); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row mb-4">
                        <div class="col-12">
                            <div class="card">
                                <div class="card-header bg-light">
                                    <h5 class="mb-0">1. Descargar Formulario</h5>
                                </div>
                                <div class="card-body">
                                    <p>Descargue el formulario de solicitud, complete todos los datos requeridos y fírmelo.</p>
                                    <a href="assets/docs/FORMULARIO USO SALON Y REGLAMENTO  ENE MAR 25.pdf" class="btn btn-primary" target="_blank">Descargar Formulario</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <form action="index.php?controlador=reservas&accion=subirFormulario&id=<?php echo $reserva['id']; ?>" method="POST" enctype="multipart/form-data">
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">2. Subir Formulario Completado</h5>
                                    </div>
                                    <div class="card-body">
                                        <div class="form-group">
                                            <label for="formulario">Seleccione el formulario completado (PDF):</label>
                                            <input type="file" class="form-control-file" id="formulario" name="formulario" accept=".pdf" <?php echo !$reserva['archivo_formulario'] ? 'required' : ''; ?>>
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
                        
                        <div class="row mb-4">
                            <div class="col-12">
                                <div class="card">
                                    <div class="card-header bg-light">
                                        <h5 class="mb-0">3. Subir Comprobante de Pago</h5>
                                    </div>
                                    <div class="card-body">
                                        <p>Suba el comprobante de pago del anticipo (50% del valor total).</p>
                                        <div class="form-group">
                                            <label for="comprobante">Seleccione el comprobante de pago (PDF, JPG, PNG):</label>
                                            <input type="file" class="form-control-file" id="comprobante" name="comprobante" accept=".pdf,.jpg,.jpeg,.png" <?php echo !$reserva['archivo_comprobante'] ? 'required' : ''; ?>>
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
                        
                        <div class="alert alert-warning">
                            <p><strong>Recuerde:</strong> Una vez que suba estos documentos, su solicitud será revisada por un administrador para su aprobación final.</p>
                        </div>
                        
                        <div class="form-group mt-4 text-center">
                            <a href="index.php?controlador=reservas&accion=listar" class="btn btn-secondary">Volver</a>
                            <button type="submit" class="btn btn-primary">Subir Documentos</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
