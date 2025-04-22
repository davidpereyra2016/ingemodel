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

                        html += `<div class="alert alert-${notificacion.leido == 0 ? 'success' : 'secondary'}
                             show d-flex justify-content-between align-items-center flex-wrap" role="alert">`;

                        html += '<span >' + "ID: #" + notificacion.id + '</span>';

                        html += `<div class="mb-0">
                                     <p class="mb-0">${notificacion.mensaje}</p>
                                     <p class="mb-0">${fecha}</p>
                                </div>`;

                        html += `<span> Estado: ${notificacion.leido ? 'Leido' : 'No leido'} </span>`;

                        html += `
                                <div class=" d-flex gap-2 flex-wrap">
                                    <button type="button" class="btn btn-primary btn-sm btn-theme btn-ver" id="btn-ver" data-id="${notificacion.id}">
                                        <i class="fas fa-eye"></i>
                                        <span class="ms-2">Ver Reserva</span>
                                    </button>
                                    ${notificacion.leido == 0 ?
                                `<button class="btn btn-success btn-sm btn-theme btn-leido" id="btn-leido" data-id="${notificacion.id}">
                                        <i class="fas fa-check"></i>
                                        <span class="ms-2">Marcar como leido</span>
                                    </button>` : ''}
                                    <button class="btn btn-warning btn-sm btn-theme btn-eliminar" id="btn-eliminar" data-id="${notificacion.id}">
                                        <i class="fas fa-trash"></i>
                                        <span class="ms-2">Eliminar</span>
                                    </button>
                                </div>`;
                        html += '</div>';
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
                    // Convertir fecha a cadena legible ej. "12 de Diciembre."
                    const fecha = new Date(data[0].fecha).toLocaleString('es-ES', {
                        month: 'long',
                        day: 'numeric',
                    });

                    // Mostrar notificaciones
                    for (let i = 0; i < data.length; i++) {
                        const notificacion = data[i];

                        if (notificacion.leido === 0) {
                            countNotificaciones++;
                        }

                        html += `<li>
                                    <button type="button" class="dropdown-item border-bottom d-flex align-items-center gap-2 btn-ver" id="btn-ver" data-id="${notificacion.id}">
                                        <i class="bi bi-eye"></i>
                                        <span class="ms-2">
                                            ${notificacion.mensaje}
                                            <br>
                                            ${fecha}
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
        
        const id = $(this).data('id');

        $.ajax({
            url: '?controlador=notificaciones&accion=marcarLeido',
            type: 'POST',
            dataType: 'json',
            data: { id },
            success: function (data) {
                if (data.success) {
                    cargarNotificaciones();
                    cargarNotificacionesHeader();
                    // Redirigir a la página de reservas
                    window.location.href = '?controlador=reservas&accion=ver&id=' + id;
                } else {
                    console.error('Error al marcar la notificación como leída');
                }
            },
            error: function (xhr, status, error) {
                console.error('Error al marcar la notificación como leída:', error);
            }
        });
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