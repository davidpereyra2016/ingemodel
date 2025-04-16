<div class="container mt-4 ">
    <div class="row mt-5 mb-5 flex-row align-items-center justify-content-between">
        <div class="col-md-8">
            <h2>Mis Notificaciones</h2>
        </div>
    </div>
    <?php if (empty($notificaciones)): ?>
        <div class="alert alert-success">
            No tienes notificaciones pendientes.
        </div>
    <?php else: ?>
        <?php foreach ($notificaciones as $notificacion): ?>
            <?php 
                // Variables de Información
                $id = $notificacion['id'];
                $id_reserva = $notificacion['id_reserva'];
                $mensaje = $notificacion['mensaje'];
                $leido = $notificacion['leido'];
                $fecha = $notificacion['fecha'];
            ?>
            <div class="alert alert-success mb-4 d-flex justify-content-between align-items-center" role="alert">
                <div>
                    <strong>Fecha: <?= $fecha ?></strong><br>
                    <span>#ID: <?= $id ?></span><br>
                    <span>Reserva ID: <?= $id_reserva ?></span><br>
                    <span>Mensaje: <?= $mensaje ?></span><br>
                    <span>
                        Estado: <?=($leido == 0) ? 'Nuevo' : 'Leído'; ?>
                    </span><br>
                </div>

                <div class="d-flex flex-column align-items-end gap-2">
                    <a href="?controlador=reservas&accion=ver&id=<?= $id_reserva; ?>" class="btn btn-primary btn-sm">
                        <i class="fas fa-eye"></i>
                        <span class="ms-2">Ver Reserva</span>
                    </a>
                    <?php if ($leido == 0): ?>
                        <form action="index.php?controlador=notificaciones&accion=marcarLeido&id=<?=$id?>" method="POST" class="d-inline">
                            <input type="hidden" name="idNotificacion" value="<?=$id?>">
                            <button type="submit" class="btn btn-success btn-sm float-end" id="btn-leido" data-id="<?=$id?>">
                                <i class="fas fa-check"></i>
                                <span class="ms-2">Marcar como leído</span>
                            </button>
                        </form>
                    <?php endif; ?>
                    <a href="index.php?controlador=notificaciones&accion=eliminar&id=<?=$id?>"
                        class="btn btn-danger btn-sm float-end">
                        <i class="fas fa-trash"></i>
                        <span class="ms-2">Eliminar</span>
                    </a>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
  $('#btn-leido').click(function() {
    var id = $(this).data('id');
    $.post(`index.php?controlador=notificaciones&accion=marcarLeido&id=${id}`, function(response) {
      location.reload(); // Recargar la página para mostrar el estado actualizado
      console.log(id);
    //   window.location.href = 'index.php?controlador=notificaciones&accion=listar';
    })
  });
</script>