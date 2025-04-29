<?php
// Obtener datos para el formulario (para edición)
$accion = isset($_GET['id']) ? 'editarDocumento' : 'crearDocumento';
$titulo = isset($_GET['id']) ? 'Editar Documento' : 'Nuevo Documento';
$boton = isset($_GET['id']) ? 'Actualizar' : 'Guardar';
$id = isset($_GET['id']) ? $_GET['id'] : '';

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
                    <h3 class="mb-0"><?php echo $titulo; ?></h3>
                </div>
                <div class="card-body">
                    <form action="index.php?controlador=configuracion&accion=<?php echo $accion; ?><?php echo $id ? '&id=' . $id : ''; ?>" method="POST" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                            <label for="nombre">Nombre del Documento:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required
                                value="<?php echo isset($documento) ? $documento['nombre'] : ''; ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label for="descripcion">Descripción:</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo isset($documento) ? $documento['descripcion'] : ''; ?></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label for="archivo">Archivo (PDF, DOC, DOCX):</label>
                            <input type="file" class="form-control-file" id="archivo" name="archivo" accept=".pdf,.doc,.docx" <?php echo !isset($documento) ? 'required' : ''; ?>>
                            <?php if (isset($documento) && $documento['archivo']): ?>
                                <div class="mt-2">
                                    <small class="text-info">Archivo actual:
                                        <a href="assets/docs/<?php echo $documento['archivo']; ?>" target="_blank">
                                            <?php echo $documento['archivo']; ?>
                                        </a>
                                    </small>
                                    <br>
                                    <small class="text-muted">Sube un nuevo archivo solo si deseas reemplazar el actual.</small>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="form-group mb-3">
                            <label for="tipo">Tipo de Documento:</label>
                            <select class="form-control" id="tipo" name="tipo" required>
                                <option value="">Seleccione...</option>
                                <option value="solicitud" <?php echo (isset($documento) && $documento['tipo'] == 'solicitud') ? 'selected' : ''; ?>>Formulario de Solicitud</option>
                                <option value="municipal" <?php echo (isset($documento) && $documento['tipo'] == 'municipal') ? 'selected' : ''; ?>>Formulario Municipal</option>
                                <option value="condiciones" <?php echo (isset($documento) && $documento['tipo'] == 'condiciones') ? 'selected' : ''; ?>>Términos y Condiciones</option>
                            </select>
                        </div>
                        <div class="form-group mt-4">
                            <a href="index.php?controlador=configuracion&accion=listarDocumentos" class="btn btn-secondary">Cancelar</a>
                            <button type="submit" class="btn btn-primary"><?php echo $boton; ?></button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>