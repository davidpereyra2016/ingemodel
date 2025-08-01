$(document).ready(function () {

    cargarNotificaciones();
    cargarNotificacionesHeader();

    // Actualizar cada 5 segundos
    setInterval(function () {
        cargarNotificaciones();
        cargarNotificacionesHeader();
    }, 5000);

    function mostrarToast(mensaje) {
        $('#toastMensaje').text(mensaje);
        const toast = new bootstrap.Toast(document.getElementById('toastNotificacion'));
        toast.show();
    }

    function cargarNotificaciones() {
        $.ajax({
            url: '?controlador=notificaciones&accion=obtenerAjax',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                // Limpiar contenedor de notificaciones
                $('#contenedor-notificaciones').empty();
                if (data.length > 0) {
                    // Mostrar notificaciones
                    let html = '';
                    // Recorrer con foreach las notificaciones y crear el HTML
                    for (var i = 0; i < data.length; i++) {
                        const notificacion = data[i];

                        // Convertir la fecha a una cadena legible ej: "12 de diciembre de 2023, 14:30"
                        const fecha = new Date(notificacion.fecha).toLocaleString('es-ES', {
                            year: 'numeric',
                            month: 'long',
                            day: 'numeric',
                            hour: 'numeric',
                            minute: 'numeric',
                            hour12: true
                        });

                        // Detectar tipo de notificación basándose en el contenido del mensaje
                        let alertClass, iconClass, badgeClass, tipoTexto;
                        const mensaje = notificacion.mensaje.toLowerCase();
                        
                        if (mensaje.includes('ha dado de baja') || mensaje.includes('baja su reserva')) {
                            // Notificación de baja de usuario
                            alertClass = notificacion.leido == 0 ? 'alert-warning' : 'alert-secondary';
                            iconClass = 'fas fa-user-times';
                            badgeClass = 'bg-warning';
                            tipoTexto = 'Baja de Usuario';
                        } else if (mensaje.includes('ha subido documentación') || mensaje.includes('subido documentación')) {
                            // Notificación de documentación
                            alertClass = notificacion.leido == 0 ? 'alert-info' : 'alert-secondary';
                            iconClass = 'fas fa-file-upload';
                            badgeClass = 'bg-info';
                            tipoTexto = 'Documentación';
                        } else {
                            // Notificación de nueva reserva o general
                            alertClass = notificacion.leido == 0 ? 'alert-primary' : 'alert-secondary';
                            iconClass = 'fas fa-calendar-plus';
                            badgeClass = 'bg-primary';
                            tipoTexto = 'Nueva Reserva';
                        }

                        html += `<div class="alert ${alertClass} border-start border-4 border-${badgeClass.replace('bg-', '')}
                             show shadow-sm mb-3" role="alert">`;

                        html += `<div class="d-flex align-items-start w-100">`;
                        html += `<div class="me-3 mt-1">
                                    <i class="${iconClass} text-${badgeClass.replace('bg-', '')} fs-4"></i>
                                 </div>`;
                        
                        html += `<div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <span class="badge ${badgeClass} rounded-pill">${tipoTexto}</span>
                                        <small class="text-muted">ID: #${notificacion.id}</small>
                                    </div>
                                    <p class="mb-2 fw-medium">${notificacion.mensaje}</p>
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <small class="text-muted"><i class="fas fa-clock me-1"></i>${fecha}</small>
                                        <span class="badge ${notificacion.leido ? 'bg-success' : 'bg-danger'} rounded-pill">
                                            <i class="fas fa-${notificacion.leido ? 'check' : 'exclamation'} me-1"></i>
                                            ${notificacion.leido ? 'Leído' : 'No leído'}
                                        </span>
                                    </div>
                                    <div class="d-flex gap-2 flex-wrap">
                                        <button type="button" class="btn btn-primary btn-sm btn-theme btn-ver" data-id="${notificacion.id_reserva}">
                                            <i class="fas fa-eye"></i>
                                            <span class="ms-2">Ver Reserva</span>
                                        </button>
                                        ${notificacion.leido == 0 ?
                                        `<button class="btn btn-success btn-sm btn-theme btn-leido" data-id="${notificacion.id}">
                                            <i class="fas fa-check"></i>
                                            <span class="ms-2">Marcar como leído</span>
                                        </button>` : ''}
                                        <button class="btn btn-warning btn-sm btn-theme btn-eliminar" data-id="${notificacion.id}">
                                            <i class="fas fa-trash"></i>
                                            <span class="ms-2">Eliminar</span>
                                        </button>
                                    </div>
                                 </div>
                        </div>
                        </div>`; // Cerrar todos los contenedores
                    }
                    $('#contenedor-notificaciones').html(html);
                }
            },
            error: function (xhr, status, error) {
                console.error('Error al cargar notificaciones:', error);
            }
        });
    }

    // Cargar notificaciones header
    function cargarNotificacionesHeader() {
        $.ajax({
            url: '?controlador=notificaciones&accion=listaPreviaNotificaciones',
            type: 'GET',
            dataType: 'json',
            success: function (data) {
                let countNotificaciones = 0;
                let html = '';
                let htmlCount = '';
                if (data.length > 0) {

                    // Mostrar notificaciones
                    for (let i = 0; i < data.length; i++) {
                        const notificacion = data[i];

                        // Convertir fecha a cadena legible ej. "12 de Diciembre" para cada notificación
                        const fecha = new Date(notificacion.fecha).toLocaleString('es-ES', {
                            month: 'long',
                            day: 'numeric',
                        });

                        if (notificacion.leido === 0) {
                            countNotificaciones++;
                        }

                        // Simplificar mensaje para el dropdown
                        let mensajeSimplificado;
                        const mensajeCompleto = notificacion.mensaje.toLowerCase();
                        
                        if (mensajeCompleto.includes('ha dado de baja') || mensajeCompleto.includes('baja su reserva')) {
                            mensajeSimplificado = 'Nueva baja de reserva';
                        } else if (mensajeCompleto.includes('ha subido documentación') || mensajeCompleto.includes('subido documentación')) {
                            mensajeSimplificado = 'Nueva documentación';
                        } else {
                            mensajeSimplificado = 'Nueva reserva';
                        }

                        html += `<li>
                                    <button type="button" class="dropdown-item border-bottom d-flex align-items-center gap-2 btn-ver" id="btn-ver" data-id="${notificacion.id_reserva}" data-notification-id="${notificacion.id}">
                                        <i class="bi bi-eye"></i>
                                        <span class="ms-2">
                                            ${mensajeSimplificado}
                                            <br>
                                            <small class="text-muted">${fecha}</small>
                                        </span>
                                        <span class="badge ${notificacion.leido === 0 ? 'bg-success' : 'bg-secondary'} rounded-pill">
                                        ${notificacion.leido === 0 ? 'Nuevo' : 'Leído'}
                                        </span>
                                    </button>
                                </li>`;
                        if (i == data.length - 1) {
                            html += `<li>
                                        <a class="dropdown-item border-bottom bg-success-2 text-white" style="border-radius: 0 0 6px 6px;" href="?controlador=notificaciones&accion=listar">
                                            <i class="bi bi-bell-fill"></i>
                                            <span class="ms-2 ">Mostrar Todas</span>
                                        </a>
                                    </li>`;
                        }


                        $('#notificaciones-header').html(html);
                    }

                    if (countNotificaciones > 0) {
                        $('#countNotificaciones').remove(); // Eliminar el contador anterior si existe
                        htmlCount = `<span class="badge bg-danger rounded-pill" id="countNotificaciones">${countNotificaciones}</span>`;
                        $('#notificaciones-header-link').append(htmlCount);
                    }

                } else {
                    html += `<li>
                                <a class="dropdown-item border-bottom" href="">
                                <i class="bi bi-x-circle-fill"></i>
                                No hay notificaciones
                                </a>
                            </li>`;
                    $('#countNotificaciones').text("");
                    $('#notificaciones-header').html(html);
                }
            }
        });
    }

    // Marcar como leido
    $(document).on('click', '.btn-leido', function () {
        const id = $(this).data('id');
        $.ajax({
            url: '?controlador=notificaciones&accion=marcarLeido',
            type: 'POST',
            data: { id: id },
            dataType: 'json',
            success: function (data) {
                if (data.success) {
                    cargarNotificaciones();
                    cargarNotificacionesHeader();
                    mostrarToast('Notificación marcada como leída');
                } else {
                    console.error('Error al marcar la notificación como leída');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error al marcar la notificación como leída:', error);
            }
        });
    });

    // Marcar como leido cuando damos click al ver reservas
    $(document).on('click', '.btn-ver', function () {
        
        const reservaId = $(this).data('id');
        const notificationId = $(this).data('notification-id');

        // Si tenemos notification-id (desde el header), marcar como leído primero
        if (notificationId) {
            $.ajax({
                url: '?controlador=notificaciones&accion=marcarLeido',
                type: 'POST',
                dataType: 'json',
                data: { id: notificationId },
                success: function (data) {
                    if (data.success) {
                        cargarNotificaciones();
                        cargarNotificacionesHeader();
                        // Redirigir a la página de reservas
                        window.location.href = '?controlador=reservas&accion=ver&id=' + reservaId;
                    } else {
                        console.error('Error al marcar la notificación como leída');
                        // Redirigir aunque falle el marcado
                        window.location.href = '?controlador=reservas&accion=ver&id=' + reservaId;
                    }
                },
                error: function (xhr, status, error) {
                    console.error('Error al marcar la notificación como leída:', error);
                    // Redirigir aunque falle el marcado
                    window.location.href = '?controlador=reservas&accion=ver&id=' + reservaId;
                }
            });
        } else {
            // Si no hay notification-id (desde la vista listar), redirigir directamente
            window.location.href = '?controlador=reservas&accion=ver&id=' + reservaId;
        }
    });

    // Eliminar
    $(document).on('click', '.btn-eliminar', function () {
        const id = $(this).data('id');

        Swal.fire({
            title: '¿Estás seguro?',
            text: "¡Esta acción no se puede deshacer!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '?controlador=notificaciones&accion=eliminarNotificacion',
                    type: 'POST',
                    data: { id: id },
                    dataType: 'json',
                    success: function (data) {
                        if (data.success) {
                            // Recargar las notificaciones
                            cargarNotificacionesHeader();
                            cargarNotificaciones();
                            mostrarToast('Notificación eliminada con éxito');
                        } else {
                            console.error('Error al eliminar la notificación');
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error('Error al eliminar la notificación:', error);
                    }
                });
            }
        });
    });

});