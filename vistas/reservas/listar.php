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
            <p class="text-muted">Visualiza y gestiona tus reservas</p>
        </div>
        <div class="col-md-4 d-flex justify-content-end align-items-center gap-2">
            <a href="index.php?controlador=reservas&accion=calendario" class="btn btn-light">
                <i class="bi bi-calendar2-event me-1"></i>
                Calendario
            </a>
            <a href="index.php?controlador=reservas&accion=crear" class="btn btn-success-theme">
                <i class="bi bi-calendar2-plus me-1"></i>
                Nueva Reserva
            </a>
        </div>
    </div>

    <?php if (empty($reservas)): ?>
        <div class="alert alert-success">
            No tienes reservas registradas. Haz clic en "Nueva Reserva" para solicitar el uso del salón.
        </div>
    <?php else: ?>

        <div class="card">
            <div class="card-header bg-light pt-4 ">
                <h5 class="card-title text-success"><i class="bi bi-file-earmark-text me-2"></i>Lista de Reservas</h5>
            </div>
            <div class="card-body">
                <table id="myTable" class="display nowrap table table-striped" style="width:100%">
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
                            <th>Tiempo Restante</th>
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
                                <td>
                                    <?php if ($reserva['estado'] === 'pendiente' && !empty($reserva['fecha_vencimiento'])): ?>
                                        <div class="countdown-container"
                                            data-fecha-vencimiento="<?php echo $reserva['fecha_vencimiento']; ?>"
                                            data-codigo="<?php echo $reserva['codigo_unico']; ?>"
                                            data-comprobante="<?php echo (!empty($reserva['archivo_comprobante']) || !empty($reserva['archivo_comprobante_total'])) ? 'true' : 'false'; ?>">
                                            <?php if (!empty($reserva['archivo_comprobante']) || !empty($reserva['archivo_comprobante_total'])): ?>
                                                <span class="countdown-display alert-sm alert-success d-inline-block p-1">Pago registrado</span>
                                            <?php else: ?>
                                                <span class="countdown-display alert-sm alert-warning d-inline-block p-1">Calculando...</span>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>$<?php echo sprintf("%.2f", $reserva['monto']); ?></td>
                                <td>
                                    <a href="index.php?controlador=reservas&accion=ver&id=<?php echo $reserva['id']; ?>" class="btn btn-sm btn-success"> <i class="fas fa-eye me-1"></i> Ver </a>

                                    <?php if (($reserva['estado'] == 'pendiente' || $reserva['estado'] == 'aprobada') &&
                                        (!$reserva['archivo_formulario'] || !$reserva['archivo_comprobante'] ||
                                            !$reserva['archivo_municipal'] || !$reserva['archivo_comprobante_total'])
                                    ): ?>
                                        <a href="index.php?controlador=reservas&accion=subirFormulario&codigo=<?php echo $reserva['codigo_unico']; ?>" class="btn btn-sm btn-warning"> <i class="fas fa-upload me-1"></i> Subir Archivos</a>
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
        </div>
    <?php endif; ?>
</div>
<!-- 
Offcanvas para el formulario de reserva
<?php include '../formulario.php'; ?>
<?php include '../formulario-documento.php'; ?> -->

<!-- Script para los contadores -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Obtener todos los contenedores de cuenta regresiva
        const countdownContainers = document.querySelectorAll('.countdown-container');

        // Para cada contenedor, iniciar un contador
        countdownContainers.forEach(container => {
            const fechaVencimientoStr = container.getAttribute('data-fecha-vencimiento');
            const countdownDisplay = container.querySelector('.countdown-display');
            const codigoUnico = container.getAttribute('data-codigo');
            const tienePagoRegistrado = container.getAttribute('data-comprobante') === 'true';

            // Función para actualizar el contador
            function actualizarContador() {
                // Si ya sabemos que tiene un pago registrado, no mostrar el contador
                if (tienePagoRegistrado) {
                    countdownDisplay.innerHTML = "Pago registrado";
                    countdownDisplay.classList.remove('alert-warning', 'alert-danger');
                    countdownDisplay.classList.add('alert-success');
                    return;
                }

                const ahora = new Date().getTime();
                // Convertir la fecha de vencimiento a timestamp
                const fechaVencimiento = new Date(fechaVencimientoStr.replace(' ', 'T')).getTime();
                const diferencia = fechaVencimiento - ahora;

                if (diferencia <= 0) {
                    // Verificar si hay pagos registrados antes de mostrar "Expirado"
                    verificarPagoRegistrado(codigoUnico, countdownDisplay);
                    return;
                }

                // Calcular días, horas, minutos y segundos
                const dias = Math.floor(diferencia / (1000 * 60 * 60 * 24));
                const horas = Math.floor((diferencia % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutos = Math.floor((diferencia % (1000 * 60 * 60)) / (1000 * 60));
                const segundos = Math.floor((diferencia % (1000 * 60)) / 1000);

                // Actualizar el display
                countdownDisplay.innerHTML = `${dias}d ${horas}h ${minutos}m ${segundos}s`;
            }

            // Función para verificar si hay un pago registrado
            function verificarPagoRegistrado(codigo, displayElement) {
                fetch(`index.php?controlador=reservas&accion=verificarEstado&codigo=${codigo}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.estado) {
                            if (data.estado !== 'pendiente') {
                                // Si ya no está pendiente, recargar la página
                                window.location.reload();
                                return;
                            }

                            // Si hay pagos registrados, mostrar mensaje positivo
                            if (data.tiene_pago === true) {
                                displayElement.innerHTML = "Pago registrado";
                                displayElement.classList.remove('alert-warning', 'alert-danger');
                                displayElement.classList.add('alert-success');
                            } else {
                                // Si no hay pagos y ya expiró, mostrar expirado
                                displayElement.innerHTML = "Expirado";
                                displayElement.classList.remove('alert-warning');
                                displayElement.classList.add('alert-danger');
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error al verificar estado:', error);
                        displayElement.innerHTML = "Error de verificación";
                    });
            }

            // Función para verificar el estado de la reserva (para actualizaciones periódicas)
            function verificarEstadoReserva(codigo, container) {
                fetch(`index.php?controlador=reservas&accion=verificarEstado&codigo=${codigo}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data && data.estado) {
                            // Si tiene pago, cambiar el contador por un mensaje positivo
                            if (data.tiene_pago === true) {
                                const displayElement = container.querySelector('.countdown-display');
                                if (displayElement) {
                                    displayElement.innerHTML = "Pago registrado";
                                    displayElement.classList.remove('alert-warning', 'alert-danger');
                                    displayElement.classList.add('alert-success');
                                }
                            }

                            // Si ya no está pendiente, actualizar la interfaz
                            if (data.estado !== 'pendiente') {
                                const row = container.closest('tr');
                                if (row) {
                                    // Refrescar la página para mostrar cambios
                                    window.location.reload();
                                }
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Error al verificar estado:', error);
                    });
            }

            // Si ya tiene pago registrado, no iniciar el contador
            if (tienePagoRegistrado) {
                // No es necesario iniciar el contador, pero sí verificar periódicamente el estado
                verificarEstadoReserva(codigoUnico, container);
            } else {
                // Iniciar el contador
                const intervalId = setInterval(actualizarContador, 1000);
                actualizarContador(); // Llamada inicial

                // Verificar si ya tiene pago al cargar la página
                verificarEstadoReserva(codigoUnico, container);
            }

            // Verificar estado cada cierto tiempo
            setInterval(() => {
                verificarEstadoReserva(codigoUnico, container);
            }, 30000); // Cada 30 segundos
        });
    });
</script>

<!-- Estilos para contadores -->
<style>
    .countdown-display {
        font-size: 0.85rem;
        font-weight: bold;
        min-width: 130px;
        text-align: center;
    }

    .alert-sm {
        padding: 0.25rem 0.5rem;
        margin-bottom: 0;
    }
</style>