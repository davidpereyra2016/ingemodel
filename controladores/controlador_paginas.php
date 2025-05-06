<?php
include_once("modelos/modelo_dashboard.php");
include_once("conexion.php");

class ControladorPaginas
{
    public function __construct() {
    }
    public function inicio() {
        
        // Incluir la vista y pasar los datos
        include_once("vistas/template.php");
        include_once("vistas/paginas/inicio.php");
    }

    public function error()
    {
        include_once("vistas/paginas/error.php");
    }
}