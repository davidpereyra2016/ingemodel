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
$idUsuario = (int)$_SESSION['id_usuario'];
$isAdmin = $_SESSION['rol'] !== 'ingeniero' ? true : false; // Verifica si el usuario es admin
?>

<div class="container">
    <div class="row mt-5 mb-5 flex-row align-items-center justify-content-between">
        <div class="col-md-8">
            <h2>Calendario de Reservas del Salón</h2>
            <p class="text-muted">Seleccione una fecha para ver la disponibilidad y reservar el salón.</p>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2">
            <a href="index.php?controlador=reservas&accion=listar" class="btn btn-light">
                <i class="bi bi-calendar2-event me-1"></i>
                Mis Reservas
            </a>
            <!-- <a href="index.php?controlador=reservas&accion=crear" class="btn btn-success-theme">
                <i class="bi bi-calendar2-plus me-1"></i>
                Nueva Reserva
            </a> -->
            <button type="button" class="btn btn-success-theme" data-bs-toggle="offcanvas" data-bs-target="#offcanvasForm" aria-controls="offcanvasRight">
                <i class="bi bi-calendar2-plus"></i>
                <span class="ms-2">Nueva Reserva</span>
            </button>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-light mb-4" role="alert">
                        <p class="mb-2"><strong >Instrucciones:</strong> Haga clic en una fecha para reservar o ver detalles de una reserva existente.</p> 
                        <div class="d-flex flex-wrap">
                            <div class="d-flex align-items-center me-3">
                                <span class="dot" style="color: #28a745;">
                                    <i class="bi bi-calendar-fill"></i>
                                </span>
                                <span class="ms-2">Aprobada</span>
                            </div>
                            <div class="d-flex align-items-center me-3">
                                <span class="dot" style="color: #ffc107;">
                                    <i class="bi bi-calendar-fill"></i>
                                </span>
                                <span class="ms-2">Pendiente</span>
                            </div>
                            <div class="d-flex align-items-center me-3">
                                <span class="dot" style="color: #dc3545;">
                                    <i class="bi bi-calendar-fill"></i>
                                </span>
                                <span class="ms-2">Rechazada</span>
                            </div>
                        </div>
                    </div>

                    <div id="calendar" data-name="salon" data-idUsuario="<?php echo $idUsuario; ?>" data-isAdmin="<?php echo $isAdmin; ?>"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar detalles de una reserva -->
<div class="modal fade" id="eventModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="eventModalLabel">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header bg-success-2 text-white">
                <h5 class="modal-title" id="eventModalLabel">Detalles de la Reserva</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="eventDetails">
                    <p><strong>Fecha:</strong> <span id="eventDate"></span></p>
                    <p><strong>Tipo de Uso:</strong> <span id="eventType"></span></p>
                    <p><strong>Estado:</strong> <span id="eventStatus"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <a href="#" class="btn btn-light" id="viewDetailBtn">Ver Detalles</a>
                <a href="index.php?controlador=reservas&accion=crear" class="btn btn-success-theme">Nueva Reserva</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . './formulario.php'; ?>
<?php include __DIR__ . './formulario-documento.php'; ?>