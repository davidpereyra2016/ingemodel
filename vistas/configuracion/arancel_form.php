<?php
// Obtener datos para el formulario (para edición)
$accion = isset($_GET['id']) ? 'editarArancel' : 'crearArancel';
$titulo = isset($_GET['id']) ? 'Editar Arancel' : 'Nuevo Arancel';
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
            <div class="card mt-4">
                <div class="card-header bg-light text-success">
                    <h3 class="mb-0 card-title"><?php echo $titulo; ?></h3>
                </div>
                <div class="card-body">
                    <form action="index.php?controlador=configuracion&accion=<?php echo $accion; ?><?php echo $id ? '&id=' . $id : ''; ?>" method="POST">
                        <div class="form-group mb-3">
                            <label class="form-label" for="nombre">Nombre del Arancel:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" required
                                value="<?php echo isset($arancel) ? $arancel['nombre'] : ''; ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="descripcion">Descripción:</label>
                            <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo isset($arancel) ? $arancel['descripcion'] : ''; ?></textarea>
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="monto_antes_22">Monto antes de las 22hs ($):</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="monto_antes_22" name="monto_antes_22" required
                                value="<?php echo isset($arancel) ? $arancel['monto_antes_22'] : ''; ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="monto_despues_22">Monto después de las 22hs ($):</label>
                            <input type="number" step="0.01" min="0" class="form-control" id="monto_despues_22" name="monto_despues_22" required
                                value="<?php echo isset($arancel) ? $arancel['monto_despues_22'] : ''; ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="fecha_inicio">Fecha Inicio:</label>
                            <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio" required
                                value="<?php echo isset($arancel) ? date('Y-m-d', strtotime($arancel['fecha_inicio'])) : ''; ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="fecha_fin">Fecha Fin:</label>
                            <input type="date" class="form-control" id="fecha_fin" name="fecha_fin" required
                                value="<?php echo isset($arancel) ? date('Y-m-d', strtotime($arancel['fecha_fin'])) : ''; ?>">
                        </div>

                        <div class="form-group mb-3">
                            <label class="form-label" for="activo">Estado:</label>
                            <select class="form-control" id="activo" name="activo" required>
                                <option value="1" <?php echo isset($arancel) && $arancel['activo'] == 1 ? 'selected' : ''; ?>>Activo</option>
                                <option value="0" <?php echo isset($arancel) && $arancel['activo'] == 0 ? 'selected' : ''; ?>>Inactivo</option>
                            </select>
                        </div>
                </div>
                <div class="card-footer d-flex justify-content-between">
                    <a href="index.php?controlador=configuracion&accion=listarAranceles" class="btn btn-light">Cancelar</a>
                    <button type="submit" class="btn btn-success-theme"><?php echo $boton; ?></button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>