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

        $notificaciones = $this->modelo->obtenerNotificaciones();
        require "vistas/notificaciones/listar.php";


        // include_once("vistas/notificaciones/listar.php");
    }

    public function listaPreviaNotificaciones()
    {
        ob_clean();
        $notificaciones = $this->modelo->obtenerNotificacionesRecientes();

        if (count($notificaciones) > 0) {
            echo json_encode($notificaciones);
        } else {
            echo []; // Retorna un array vacío si no hay notificaciones
        }

        exit;
    }


    public function marcarLeido()
    {
        ob_clean();
        header('Content-Type: application/json');
        $id = $_POST['id'];
        $this->modelo->actualizarEstadoNotificacion($id, 1);
        echo json_encode(['success' => true]);
        exit;
    }

    public function eliminarNotificacion()
    {
        ob_clean();
        header('Content-Type: application/json');
        $id = $_POST['id'];
        $this->modelo->eliminarNotificacion($id);
        echo json_encode(['success' => true]);
        exit;
    }

    public function obtenerAjax()
    {
        ob_clean();
        header('Content-Type: application/json');

        $notificaciones = $this->modelo->obtenerNotificaciones();
        if (count($notificaciones) > 0) {
            echo json_encode($notificaciones);
        } else {
            echo json_encode([]);
        }
        exit;
    }
}
