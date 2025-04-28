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
            <h2>Gestión de Documentos</h2>
            <p class="text-muted">Administra los documentos que los usuarios pueden descargar</p>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2">
            <a href="index.php?controlador=configuracion&accion=crearDocumento" class="btn btn-success-theme">
                <i class="bi bi-plus-circle me-1"></i>
                Nuevo Documento
            </a>
        </div>
    </div>

    <?php if (empty($documentos)): ?>
        <div class="alert alert-success mt-4">
            No hay documentos registrados. Haz clic en "Nuevo Documento" para agregar uno.
        </div>
    <?php else: ?>
        <div class="card">
            <div class="card-header bg-light pt-4 ">
                <h5 class="card-title text-success"><i class="bi bi-file-earmark-text me-2"></i>Lista de Documentos</h5>
            </div>
            <div class="card-body">
                <table id="myTable" class="display nowrap table table-striped" style="width:100%">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Descripción</th>
                            <th>Archivo</th>
                            <th>Tipo</th>
                            <th>Fecha de Creación</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($documentos as $documento): ?>
                            <tr>
                                <td><?php echo $documento['id']; ?></td>
                                <td><?php echo $documento['nombre']; ?></td>
                                <td><?php echo $documento['descripcion']; ?></td>
                                <td>
                                    <a href="assets/docs/<?php echo $documento['archivo']; ?>" target="_blank">
                                        <?php echo $documento['archivo']; ?>
                                    </a>
                                </td>
                                <td>
                                    <?php echo ($documento['tipo'] == 'solicitud') ? 'Formulario de Solicitud' : (($documento['tipo'] == 'municipal') ? 'Formulario Municipal' : 'Otro'); ?>
                                </td>
                                <td><?php echo date('d/m/Y H:i', strtotime($documento['fecha_creacion'])); ?></td>
                                <td>
                                    <a href="index.php?controlador=configuracion&accion=editarDocumento&id=<?php echo $documento['id']; ?>" class="btn btn-sm btn-success">
                                        <i class="bi bi-pencil"></i> Editar
                                    </a>
                                    <a href="index.php?controlador=configuracion&accion=eliminarDocumento&id=<?php echo $documento['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Está seguro de eliminar este documento?');">
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