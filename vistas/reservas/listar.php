<?php
// Mostrar mensajes de éxito o error si existen
if (isset($_SESSION['mensaje'])) {
    echo '<div class="container mt-4 alert alert-success">' . $_SESSION['mensaje'] . '</div>';
    unset($_SESSION['mensaje']);
}
if (isset($_SESSION['error'])) {
    echo '<div class="container mt-4 alert alert-danger">' . $_SESSION['error'] . '</div>';
    unset($_SESSION['error']);
}
?>

<div class="container">

    <div class="row mt-5 mb-5 flex-row align-items-center justify-content-between">
        <div class="col-md-8">
            <h2>Mis Reservas</h2>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2 header-page-responsive">
        <a href="index.php?controlador=reservas&accion=crear" class="btn btn-primary">Nueva Reserva</a>
        <a href="index.php?controlador=reservas&accion=calendario" class="btn btn-info">Ver Calendario</a>
        </div>
    </div>


    <?php if (empty($reservas)): ?>
        <div class="alert alert-success">
            No tienes reservas registradas. Haz clic en "Nueva Reserva" para solicitar el uso del salón.
        </div>
    <?php else: ?>

        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>ID</th>
                        <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador'): ?>
                            <th>Ingeniero</th>
                            <th>Matrícula</th>
                        <?php endif; ?>
                        <th>Fecha</th>
                        <th>Horarios</th>
                        <th>Tipo de Uso</th>
                        <th>Estado</th>
                        <th>Monto</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservas as $reserva): ?>
                        <tr>
                            <td><?php echo $reserva['id']; ?></td>
                            <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador'): ?>
                                <td><?php echo $reserva['nombre'] . ' ' . $reserva['apellido']; ?></td>
                                <td><?php echo $reserva['matricula']; ?></td>
                            <?php endif; ?>
                            <td><?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?></td>
                            <td><?php echo substr($reserva['hora_inicio'], 0, 5) . ' - ' . substr($reserva['hora_fin'], 0, 5); ?></td>
                            <td><?php echo $reserva['tipo_uso']; ?></td>
                            <td>
                                <?php
                                switch ($reserva['estado']) {
                                    case 'pendiente':
                                        echo '<span class="badge rounded-pill text-bg-warning">Pendiente</span>';
                                        break;
                                    case 'aprobada':
                                        echo '<span class="badge rounded-pill text-bg-success">Aprobada</span>';
                                        break;
                                    case 'rechazada':
                                        echo '<span class="badge rounded-pill text-bg-danger">Rechazada</span>';
                                        break;
                                    case 'cancelada':
                                        echo '<span class="badge rounded-pill text-bg-secondary">Cancelada</span>';
                                        break;
                                }
                                ?>
                            </td>
                            <td>$<?php echo sprintf("%.2f", $reserva['monto']); ?></td>
                            <td>
                                <a href="index.php?controlador=reservas&accion=ver&id=<?php echo $reserva['id']; ?>" class="btn btn-sm btn-success"> <i class="fas fa-eye me-1"></i>  Ver </a>

                                <?php if (($reserva['estado'] == 'pendiente' || $reserva['estado'] == 'aprobada') && 
                                         (!$reserva['archivo_formulario'] || !$reserva['archivo_comprobante'] || 
                                          !$reserva['archivo_municipal'] || !$reserva['archivo_comprobante_total'])): ?>
                                    <a href="index.php?controlador=reservas&accion=subirFormulario&id=<?php echo $reserva['id']; ?>" class="btn btn-sm btn-warning"> <i class="fas fa-upload me-1"></i> Subir Archivos</a>
                                <?php endif; ?>

                                <?php if ($reserva['estado'] == 'aprobada'): ?>
                                    <a href="index.php?controlador=reservas&accion=generarPDF&id=<?php echo $reserva['id']; ?>" class="btn btn-sm btn-light border"> <i class="fas fa-file-pdf me-1"></i> Descargar PDF</a>
                                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador'): ?>
                                        <a href="index.php?controlador=reservas&accion=enviarCorreos&id=<?php echo $reserva['id']; ?>" class="btn btn-sm btn-primary"> <i class="fas fa-envelope me-1"></i> Enviar Correos</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if ($reserva['estado'] == 'rechazada'): ?>
                                    <?php if (isset($_SESSION['rol']) && $_SESSION['rol'] == 'administrador'): ?>
                                        <a href="index.php?controlador=reservas&accion=enviarCorreos&id=<?php echo $reserva['id']; ?>" class="btn btn-sm btn-primary"> <i class="fas fa-envelope me-1"></i> Enviar Correos</a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>

<!-- Offcanvas para el formulario de reserva -->
<?php include __DIR__ . './formulario.php'; ?>
<?php include __DIR__ . './formulario-documento.php'; ?>