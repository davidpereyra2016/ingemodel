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
            <h2>Calendario de Reservas del Salón</h2>
            <p class="text-muted">Seleccione una fecha para ver la disponibilidad y reservar el salón.</p>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2">
            <a href="index.php?controlador=reservas&accion=listar" class="btn btn-light">
                <i class="bi bi-calendar2-event me-1"></i>
                Mis Reservas
            </a>
            <a href="index.php?controlador=reservas&accion=crear" class="btn btn-success-theme">
               <i class="bi bi-calendar2-plus me-1"></i>
                Nueva Reserva
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <div class="alert alert-info">
                        <p><strong>Leyenda:</strong></p>
                        <div class="d-flex flex-wrap">
                            <div class="mr-3 mb-2">
                                <span class="badge badge-success mr-1">&nbsp;&nbsp;&nbsp;</span> Reservas aprobadas
                            </div>
                            <div class="mr-3 mb-2">
                                <span class="badge badge-warning mr-1">&nbsp;&nbsp;&nbsp;</span> Reservas pendientes
                            </div>
                            <div class="mr-3 mb-2">
                                <span class="badge badge-danger mr-1">&nbsp;&nbsp;&nbsp;</span> Reservas rechazadas
                            </div>
                        </div>
                    </div>
                    
                    <div id="calendar"></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para mostrar detalles de una reserva -->
<div class="modal fade" id="eventModal" tabindex="-1" role="dialog" aria-labelledby="eventModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="eventModalLabel">Detalles de la Reserva</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div id="eventDetails">
                    <p><strong>Fecha:</strong> <span id="eventDate"></span></p>
                    <p><strong>Tipo de Uso:</strong> <span id="eventType"></span></p>
                    <p><strong>Estado:</strong> <span id="eventStatus"></span></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                <a href="#" class="btn btn-primary" id="viewDetailBtn">Ver Detalles</a>
                <a href="index.php?controlador=reservas&accion=crear" class="btn btn-success">Nueva Reserva</a>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar FullCalendar
    var calendarEl = document.getElementById('calendar');
    
    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'es',
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek,listMonth'
        },
        events: 'index.php?controlador=reservas&accion=obtenerEventos',
        eventClick: function(info) {
            // Mostrar modal con detalles del evento
            $('#eventDate').text(info.event.start.toLocaleDateString());
            $('#eventType').text(info.event.title);
            
            // Determinar estado según el color
            let estado = '';
            switch(info.event.backgroundColor) {
                case '#28a745':
                    estado = '<span class="badge badge-success">Aprobada</span>';
                    break;
                case '#ffc107':
                    estado = '<span class="badge badge-warning">Pendiente</span>';
                    break;
                case '#dc3545':
                    estado = '<span class="badge badge-danger">Rechazada</span>';
                    break;
                default:
                    estado = '<span class="badge badge-secondary">Otro</span>';
            }
            
            $('#eventStatus').html(estado);
            $('#viewDetailBtn').attr('href', 'index.php?controlador=reservas&accion=ver&id=' + info.event.id);
            
            $('#eventModal').modal('show');
        },
        dateClick: function(info) {
            // Redireccionar a la página de creación con la fecha seleccionada
            window.location.href = 'index.php?controlador=reservas&accion=crear&fecha=' + info.dateStr;
        }
    });
    
    calendar.render();
});
</script>
