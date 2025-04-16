<?php

include_once("modelos/modelo_notificaciones.php");
include_once("conexion.php");

class ControladorNotificaciones
{
    private $modelo;
    private $conexion;

    public function __construct()
    {
        $this->modelo = new ModeloNotificaciones();
        $this->conexion = BD::crearInstancia();
    }

    public function listar()
    {
        try {
            $notificaciones = $this->modelo->obtenerNotificaciones();
        } catch (Exception $e) {
            echo "Error al obtener las notificaciones: " . $e->getMessage();
        }

        include_once("vistas/notificaciones/listar.php");
    }

    public function listaPreviaNotificaciones()
    {
        $notificaciones = $this->modelo->obtenerNotificaciones();

        if (count($notificaciones) > 0) {
            return $notificaciones;
        } else {
            return []; // Retorna un array vacío si no hay notificaciones
        }
        
    }

    public function obtenerNotificacionesPorIdReserva($id_reserva)
    {
        $notificaciones = $this->modelo->obtenerNotificacionesPorIdReserva($id_reserva);
        include_once("vistas/notificaciones/listar.php");
    }

    public function obtenerNotificacionesPorIdUsuario($id_usuario)
    {
        $notificaciones = $this->modelo->obtenerNotificacionesPorIdUsuario($id_usuario);
        include_once("vistas/notificaciones/listar.php");
    }

    public function actualizarEstadoNotificacion($id, $estado)
    {
        $this->modelo->actualizarEstadoNotificacion($id, $estado);
        $notificaciones = $this->modelo->obtenerNotificaciones();

        // Recargar la pagina para mostrar las notificaciones actualizadas
        header("Location: index.php?controlador=notificaciones&accion=listar");

        include_once("vistas/notificaciones/listar.php");
    }

    public function marcarLeido() {
        // Marcar la notificacion como leida
        $id = $_POST['id'];
        $this->modelo->actualizarEstadoNotificacion($id, 1);
        $notificaciones = $this->modelo->obtenerNotificaciones();
    }
}
