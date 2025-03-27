<?php
include_once("modelos/modelo_dashboard.php");
include_once("conexion.php");

class ControladorPaginas
{
    private $modelo;
    private $conexion;

    public function __construct() {
        $this->modelo = new ModeloDashboard();
        $this->conexion = BD::crearInstancia();
    }
    public function inicio() {
        

        // Incluir la vista y pasar los datos
        include_once("vistas/template.php");
        include_once("vistas/paginas/inicio.php");
    }

   

    private function prepararDatosGraficoCategorias($ventas_categoria) {
        $labels = array();
        $datos = array();

        foreach ($ventas_categoria as $categoria) {
            $labels[] = $categoria['categoria'];
            $datos[] = floatval($categoria['total_ingresos']);
        }

        return array(
            'labels' => $labels,
            'datos' => $datos
        );
    }

    public function error()
    {
        include_once("vistas/paginas/error.php");
    }
}