document.addEventListener('DOMContentLoaded', function () {
    $.ajax({
        url: 'index.php?controlador=reservas&accion=obtenerEventos',
        method: 'GET',
        dataType: 'json',
        success: function (eventos) {

            const calendarEl = document.getElementById('calendar');

            // Verificamos si el elemento existe antes de inicializar el calendario
            if (!calendarEl) {
                console.error('El elemento con id "calendar" no existe en el DOM.');
                return;
            }

            eventos = eventos.map(e => {

                const idUsuario = calendarEl.dataset.idusuario; 

                if (e.id_usuario == idUsuario || calendarEl.dataset.isadmin == 1) {
                    return {
                        ...e,
                        title: e.title,
                        backgroundColor: e.backgroundColor,
                        borderColor: e.borderColor,
                        textColor: '#000',
                    };
                } else {
                    return {
                        ...e,
                        title: "Reservado",
                        backgroundColor: '#d1d1d1',
                        borderColor: '#d1d1d1',
                        textColor: '#000',
                    };

                }
            });

            let configInitialView = '';

            // Verificar si elemento tine de atributo data-name de valor "salon"
            if (calendarEl.dataset.name === 'salon') {
                configInitialView = 'dayGridMonth';
            } else if (calendarEl.dataset.name === 'inicio') {
                configInitialView = 'multiMonthYear';
            }

            let calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: configInitialView,
                views: {
                    multiMonthYear: {
                        type: 'multiMonthYear',
                        duration: {
                            months: 6
                        },
                        buttonText: '6 meses'
                    }
                },
                selectable: true,
                selectOverlap: false, // evita superposición
                events: eventos,
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
                selectAllow: function (selectInfo) {
                    // bloqueamos selección si ya hay evento ese día y agregamos un color de fondo
                    const fechaSeleccionada = selectInfo.startStr; // formato YYYY-MM-DD
                    const eventoExistente = eventos.find(evento => evento.start === fechaSeleccionada);
                    if (eventoExistente) {
                        return false; // No permitir la selección si ya hay un evento en esa fecha
                    }
                    return true;
                },
                select: function (info) {
                    const fechaActual = new Date().toISOString().split('T')[0]; // formato YYYY-MM-DD
                    let fechaSeleccionada = info.startStr; // formato YYYY-MM-DD

                    // Condicion si la fecha seleccionada es menor a la fecha actual
                    if (fechaSeleccionada < fechaActual) {
                        // Un mensaje en switch alert para indicar que la fecha seleccionada es menor a la fecha actual
                        Swal.fire({
                            title: 'La fecha seleccionada es menor a la fecha actual',
                            text: "Por favor seleccione una fecha válida.",
                            icon: 'info',
                            confirmButtonText: 'Aceptar',
                            customClass: {
                                confirmButton: 'btn btn-primary'
                            }
                        });
                        return;
                    }

                    // Mostramos la fecha en el offcanvas
                    //document.getElementById('fecha_evento').value = fechaSeleccionada;
                    // Abrir formulario de reserva en un offcanvas
                    // $('#offcanvasForm').offcanvas('show');
                    // Redireccionar a la página de creación con la fecha seleccionada
                    window.location.href = 'index.php?controlador=reservas&accion=crear&fecha=' + fechaSeleccionada;

                },
                eventClick: function (info) {
                    // Mostrar modal con detalles del evento
                    // Si es su propio evento o si administrador
                    let idEventoUsuario = info.event.extendedProps.id_usuario; // Obtener el id_usuario del evento
                    let idUsuario = calendarEl.dataset.idusuario; // Obtener el id_usuario del elemento HTML
                    let isAdmin = calendarEl.dataset.isadmin; // Obtener el id_usuario del elemento HTML

                    // console.log(idUsuario, idEventoUsuario, isAdmin);

                    // Verificamos si el evento pertenece al usuario actual o si es admin
                    if (idUsuario == idEventoUsuario || isAdmin == 1) {
                        $('#eventDate').text(info.event.start.toLocaleDateString());
                        $('#eventType').text(info.event.title);

                        // Determinar estado según el color
                        let estado = info.event.extendedProps.estado;

                        $('#eventStatus').html(estado);
                        $('#viewDetailBtn').attr('href', 'index.php?controlador=reservas&accion=ver&id=' + info.event.id);

                        $('#eventModal').modal('show');
                    }

                },

            });

            calendar.render();
        }
    });
});
