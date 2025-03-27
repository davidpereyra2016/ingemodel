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
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        try {
            // Obtener todos los datos necesarios para el dashboard
            $datos = array();
            
            // Ventas de hoy
            $datos['ventas_hoy'] = $this->modelo->obtenerVentasHoy();
            
            if (!$datos['ventas_hoy']) {
                $datos['ventas_hoy'] = array(
                    'total_ventas' => 0,
                    'total_monto' => 0
                );
            }

            // Inversión y Ganancia Total
            $datos['inversion_total'] = $this->modelo->obtenerInversionTotal();
            $datos['ganancia_total'] = $this->modelo->obtenerGananciaTotal();

            // Ventas de la semana
            $datos['ventas_semana'] = $this->modelo->obtenerVentasUltimaSemana();
            if (empty($datos['ventas_semana'])) {
                $datos['ventas_semana'] = array();
            }

            // Productos más vendidos
            $datos['productos_mas_vendidos'] = $this->modelo->obtenerProductosMasVendidos();
            if (empty($datos['productos_mas_vendidos'])) {
                $datos['productos_mas_vendidos'] = array();
            }

            // Ventas por categoría
            $datos['ventas_por_categoria'] = $this->modelo->obtenerVentasPorCategoria();
            if (empty($datos['ventas_por_categoria'])) {
                $datos['ventas_por_categoria'] = array();
            }

            // Productos con stock bajo
            $datos['productos_stock_bajo'] = $this->modelo->obtenerProductosStockBajo();
            if (empty($datos['productos_stock_bajo'])) {
                $datos['productos_stock_bajo'] = array();
            }

            // Estadísticas generales
            $datos['estadisticas'] = $this->modelo->obtenerEstadisticasGenerales();
            if (!$datos['estadisticas']) {
                $datos['estadisticas'] = array(
                    'productos' => 0,
                    'categorias' => 0,
                    'ventas_mes' => 0,
                    'ingresos_mes' => 0
                );
            }

            // Preparar datos para los gráficos
            $datos['grafico_ventas_semana'] = $this->prepararDatosGraficoVentas($datos['ventas_semana']);
            $datos['grafico_categorias'] = $this->prepararDatosGraficoCategorias($datos['ventas_por_categoria']);

            // Depuración
            error_log("Datos del dashboard: " . print_r($datos, true));

        } catch (Exception $e) {
            error_log("Error en ControladorDashboard->inicio(): " . $e->getMessage());
            $datos = array(
                'error' => true,
                'mensaje' => "Error al cargar los datos del dashboard"
            );
        }

        // Incluir la vista y pasar los datos
        include_once("vistas/template.php");
        include_once("vistas/paginas/inicio.php");
    }

    private function prepararDatosGraficoVentas($ventas_semana) {
        $labels = array();
        $datos_ventas = array();
        $datos_montos = array();

        foreach ($ventas_semana as $venta) {
            $labels[] = date('d/m', strtotime($venta['fecha']));
            $datos_ventas[] = intval($venta['total_ventas']);
            $datos_montos[] = floatval($venta['total_monto']);
        }

        return array(
            'labels' => array_reverse($labels),
            'ventas' => array_reverse($datos_ventas),
            'montos' => array_reverse($datos_montos)
        );
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