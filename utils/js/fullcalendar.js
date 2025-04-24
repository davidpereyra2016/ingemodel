document.addEventListener('DOMContentLoaded', function () {
    // FullCalendar para el calendario de inicio
    const calendarEl = document.getElementById('calendarInicio')
    if (calendarEl) {

        const calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'multiMonthYear',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                list: 'Lista'
            },
            views: {
                multiMonthYear: {
                    type: 'multiMonthYear',
                    duration: {
                        months: 6
                    },
                    buttonText: '6 meses'
                }
            },
            initialDate: new Date(),
            editable: true,
            selectable: true,
            selectMirror: true,
            dayMaxEvents: true, // allow "more" link when too many events
            events: 'index.php?controlador=reservas&accion=obtenerEventos',
            eventClick: function (info) {
                // Mostrar modal con detalles del evento
                $('#eventDate').text(info.event.start.toLocaleDateString());
                $('#eventType').text(info.event.title);

                // Determinar estado según el color
                let estado = '';
                switch (info.event.backgroundColor) {
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
            dateClick: function (info) {

                const fecha = info.dateStr;

                // Mostramos la fecha en el offcanvas
                document.getElementById('fecha_evento').value = fecha;

                // Abrir formulario de reserva en un offcanvas
                $('#offcanvasForm').offcanvas('show');
            },

        })

        calendar.render()

    }

    // FullCalendar para reservar el salon por mes
    const calendarSalonEl = document.getElementById('calendarSalon');

    if (calendarSalonEl) {

        const calendarSalon = new FullCalendar.Calendar(calendarSalonEl, {
            initialView: 'dayGridMonth',
            locale: 'es',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,listMonth'
            },
            buttonText: {
                today: 'Hoy',
                month: 'Mes',
                week: 'Semana',
                day: 'Día',
                list: 'Lista'
            },
            events: 'index.php?controlador=reservas&accion=obtenerEventos',
            eventClick: function (info) {
                // Mostrar modal con detalles del evento
                $('#eventDate').text(info.event.start.toLocaleDateString());
                $('#eventType').text(info.event.title);

                // Determinar estado según el color
                let estado = '';
                switch (info.event.backgroundColor) {
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
            dateClick: function (info) {
                // Redireccionar a la página de creación con la fecha seleccionada
                // window.location.href = 'index.php?controlador=reservas&accion=crear&fecha=' + info.dateStr;
                const fecha = info.dateStr; // formato YYYY-MM-DD

                // condicio si la fecha seleccionada es menor a la fecha actual
                // if (fecha < getCurrentDate()) {
                //     alert('La fecha seleccionada es menor a la fecha actual.');
                //     return;
                // }

                // console.log(isDateReserved(fecha));

                // Condicion si echa ya tiene una reserva tirar un alert
                // if (isDateReserved(fecha)) {
                //     alert('La fecha seleccionada ya tiene una reserva.');
                //     return;
                // }

                // Mostramos la fecha en el offcanvas
                document.getElementById('fecha_evento').value = fecha;
                // Abrir formulario de reserva en un offcanvas
                $('#offcanvasForm').offcanvas('show');

            }
        });

        calendarSalon.render();

    }


    // Funcion para obtener todas las reservas y verifar si la fecha ya tiene una reserva
    function isDateReserved(date) {
       $.ajax({
            url: 'index.php?controlador=reservas&accion=buscarFechaEvento',
            data: { fecha_evento: date },
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                if (data.length > 0) {
                    return true;
                } else {
                    return false;
                }
            }
        });
    }
})