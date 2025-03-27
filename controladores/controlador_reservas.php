<?php
include_once("modelos/modelo_reservas.php");
include_once("conexion.php");

class ControladorReservas
{
    private $modelo;
    private $conexion;

    public function __construct() {
        $this->modelo = new ModeloReservas();
        $this->conexion = BD::crearInstancia();
    }

    public function listar() {
        include_once("vistas/reservas/listar.php");
    }
}
