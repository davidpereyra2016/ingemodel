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

    <div class="row mt-5 mb-5 flex-row align-items-center justify-content-between">
        <div class="col-md-8">
            <h2>Gestión de Aranceles</h2>
            <p class="text-muted">Administra los aranceles y períodos vigentes</p>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2">
            <a href="index.php?controlador=configuracion&accion=crearArancel" class="btn btn-success-theme">
                <i class="bi bi-plus-circle me-1"></i>
                Nueva Arancel
            </a>
        </div>
    </div>

    <?php if (empty($aranceles)): ?>
        <div class="alert alert-success mt-4">
            No hay aranceles registrados. Haz clic en "Nuevo Arancel" para agregar uno.
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-header bg-light pt-4 ">
                <h5 class="card-title text-success"> <i class="bi bi-file-earmark-text me-2"></i>Lista de Aranceles</h5>
            </div>
            <div class="card-body">
                <table id="myTable" class="display nowrap table table-striped" style="width:100%">
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
                                        <span class="badge rounded-pill text-bg-success">Activo</span>
                                    <?php else: ?>
                                        <span class="badge rounded-pill text-bg-secondary">Inactivo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <a href="index.php?controlador=configuracion&accion=editarArancel&id=<?php echo $arancel['id']; ?>" class="btn btn-sm btn-success">
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
        </div>
    <?php endif; ?>
</div>