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
            <h2>Gestión de Documentos</h2>
            <p>Administra los documentos que los usuarios pueden descargar</p>
        </div>
        <div class="col-md-6 text-right">
            <a href="index.php?controlador=configuracion&accion=crearDocumento" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Nuevo Documento
            </a>
        </div>
    </div>

    <?php if (empty($documentos)): ?>
        <div class="alert alert-info">
            No hay documentos registrados. Haz clic en "Nuevo Documento" para agregar uno.
        </div>
    <?php else: ?>
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Archivo</th>
<th>Tipo</th>
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
                                <a href="index.php?controlador=configuracion&accion=editarDocumento&id=<?php echo $documento['id']; ?>" class="btn btn-sm btn-info">
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
    <?php endif; ?>
</div>
