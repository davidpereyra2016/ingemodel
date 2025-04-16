<?php
// Mostrar mensajes de éxito o error si existen
if (isset($_SESSION['mensaje'])) {
    echo '<div class="alert alert-success">' . $_SESSION['mensaje'] . '</div>';
    unset($_SESSION['mensaje']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="container">
    <div class="row mb-4">
        <div class="col-md-6">
            <h2>Gestión de Aranceles</h2>
            <p>Administra los aranceles y períodos vigentes</p>
        </div>
        <div class="col-md-6 text-right">
            <a href="index.php?controlador=configuracion&accion=crearArancel" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Arancel
            </a>
        </div>
    </div>

    <?php if (empty($aranceles)): ?>
        <div class="alert alert-info">
            No hay aranceles registrados. Haz clic en "Nuevo Arancel" para agregar uno.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Monto antes 22hs</th>
                        <th>Monto después 22hs</th>
                        <th>Fecha Inicio</th>
                        <th>Fecha Fin</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($aranceles as $arancel): ?>
                        <tr>
                            <td><?php echo $arancel['id']; ?></td>
                            <td><?php echo $arancel['nombre']; ?></td>
                            <td><?php echo $arancel['descripcion']; ?></td>
                            <td>$<?php echo sprintf("%.2f", $arancel['monto_antes_22']); ?></td>
                            <td>$<?php echo sprintf("%.2f", $arancel['monto_despues_22']); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($arancel['fecha_inicio'])); ?></td>
                            <td><?php echo date('d/m/Y', strtotime($arancel['fecha_fin'])); ?></td>
                            <td>
                                <?php if ($arancel['activo']): ?>
                                    <span class="badge badge-success">Activo</span>
                                <?php else: ?>
                                    <span class="badge badge-secondary">Inactivo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="index.php?controlador=configuracion&accion=editarArancel&id=<?php echo $arancel['id']; ?>" class="btn btn-sm btn-info">
                                    <i class="bi bi-pencil"></i> Editar
                                </a>
                                <a href="index.php?controlador=configuracion&accion=eliminarArancel&id=<?php echo $arancel['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este arancel?');">
                                    <i class="bi bi-trash"></i> Eliminar
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
