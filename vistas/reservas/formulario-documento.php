<?php if (isset($_SESSION['error'])): ?>
    <div class="toast-container top-0 end-0 p-3" style="top: -2rem;">
        <div id="toastError" class="toast align-items-center text-bg-danger border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <?php echo $_SESSION['error']; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
<?php endif; ?>

<div class="offcanvas offcanvas-end w-50 formulario" tabindex="-1" id="offcanvasFormDocumento" aria-labelledby="offcanvasRightLabel">
    <div class="offcanvas-header mt-2 border-bottom">
        <h3 class="offcanvas-title color-success" id="offcanvasRightLabel">Subir Documentación de Reserva</h3>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="alert alert-success">
            <p>
                <strong>Importante:</strong>
                Para completar su solicitud, debe descargar el formulario, completarlo,
                firmarlo y subirlo nuevamente
                junto con el comprobante de pago del anticipo (50% del valor total).
            </p>
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