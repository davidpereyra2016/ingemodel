<?php


class ModeloNotificaciones {

    private $conexion;

    public function __construct() {
        $this->conexion = BD::crearInstancia();
    }

    // Obtener todas las notificaciones
    public function obtenerNotificaciones() {
        $conexion = BD::crearInstancia();
        $consulta = $conexion->query("SELECT * FROM notificaciones ORDER BY id DESC");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener notificaciones por ID Reservas
    public static function obtenerNotificacionesPorIdReserva($id_reserva) {
        $conexion = BD::crearInstancia();
        $consulta = $conexion->prepare("SELECT * FROM notificaciones WHERE id_reserva = :id_reserva ORDER BY id DESC");
        $consulta->bindParam(':id_reserva', $id_reserva);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener notificaciones por ID Usuario
    public static function obtenerNotificacionesPorIdUsuario($id_usuario) {
        $conexion = BD::crearInstancia();
        $consulta = $conexion->prepare("SELECT * FROM notificaciones WHERE id_usuario = :id_usuario ORDER BY id DESC");
        $consulta->bindParam(':id_usuario', $id_usuario);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Atualizar estado de la notificacion
    public static function actualizarEstadoNotificacion($id, $estado) {
        $conexion = BD::crearInstancia();
        $consulta = $conexion->prepare("UPDATE notificaciones SET leido = :estado WHERE id = :id");
        $consulta->bindParam(':id', $id);
        $consulta->bindParam(':estado', $estado);
        return $consulta->execute();
    }
    

    public static function eliminarNotificacion($id) {
        $conexion = BD::crearInstancia();
        $consulta = $conexion->prepare("DELETE FROM notificaciones WHERE id = :id");
        $consulta->bindParam(':id', $id);
        return $consulta->execute();
    }

}
