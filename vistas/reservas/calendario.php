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
$rol = $_SESSION['rol'];
$isAdmin = $rol !== 'ingeniero' ? true : false; // Verifica si el usuario es admin
$isEncargado = $rol === 'encargado'; // Verifica si el usuario es encargado
?>

<div class="container">
    <div class="row mt-5 mb-5 flex-row align-items-center justify-content-between">
        <div class="col-md-8">
            <h2>Calendario de Reservas del Salón</h2>
            <p class="text-muted">Seleccione una fecha para ver la disponibilidad y reservar el salón.</p>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2">
            <?php if(!$isEncargado): ?>
            <a href="index.php?controlador=reservas&accion=listar" class="btn btn-light">
                <i class="bi bi-calendar2-event me-1"></i>
                Mis Reservas
            </a>
            <a href="index.php?controlador=reservas&accion=crear" class="btn btn-success-theme">
                <i class="bi bi-calendar2-plus me-1"></i>
                Nueva Reserva
            </a>
            <?php endif; ?>
            <!-- <button type="button" class="btn btn-success-theme" data-bs-toggle="offcanvas" data-bs-target="#offcanvasForm" aria-controls="offcanvasRight">
                <i class="bi bi-calendar2-plus"></i>
                <span class="ms-2">Nueva Reserva</span>
            </button> -->
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

                    <div id="calendar" data-name="salon" data-idUsuario="<?php echo $idUsuario; ?>" data-isAdmin="<?php echo $isAdmin; ?>" data-isEncargado="<?php echo $isEncargado ? '1' : '0'; ?>"></div>
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
                    <p><strong>Nombre:</strong> <span id="eventNombre"></span></p>
                    <p><strong>telefono:</strong> <span id="eventTelefono"></span></p>
                    <p><strong>Correo:</strong> <span id="eventCorreo"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <?php if(!$isEncargado): ?>
                <a href="#" class="btn btn-light" id="viewDetailBtn">Ver Detalles</a>
                
                <a href="index.php?controlador=reservas&accion=crear" class="btn btn-success-theme">Nueva Reserva</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>