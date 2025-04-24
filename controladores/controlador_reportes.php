<?php
include_once("modelos/modelo_reportes.php");
include_once("conexion.php");

class ControladorReportes {
    private $modelo;
    
    public function __construct() {
        $this->modelo = new ModeloReportes();
    }

    // Método para listar reportes (vista principal)
    public function listar() {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        // Obtener valores para los filtros
        $anosDisponibles = $this->modelo->obtenerAnosDisponibles();
        
        // Si no hay años disponibles, usar el año actual
        if (empty($anosDisponibles)) {
            $anosDisponibles = [date('Y')];
        }
        
        // Establecer valores por defecto
        $mesActual = date('m');
        $anioActual = date('Y');
        
        // Si se seleccionaron filtros, usarlos
        if (isset($_GET['mes']) && isset($_GET['anio'])) {
            $mesSeleccionado = $_GET['mes'];
            $anioSeleccionado = $_GET['anio'];
        } else {
            $mesSeleccionado = $mesActual;
            $anioSeleccionado = $anioActual;
        }
        
        // Obtener datos según el filtro
        $reservas = $this->modelo->obtenerReservasPorMes($mesSeleccionado, $anioSeleccionado);
        $estadisticas = $this->modelo->obtenerEstadisticasMensuales($mesSeleccionado, $anioSeleccionado);
        $tiposUsoPopulares = $this->modelo->obtenerTiposUsoPopulares($mesSeleccionado, $anioSeleccionado);
        
        // Cargar la vista
        include_once 'vistas/reportes/listar.php';
    }

    // Método para generar reporte semanal
    public function reporteSemanal() {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }
        
        // Obtener la fecha actual
        $fechaActual = new DateTime();
        
        // Verificar si se proporciona una fecha específica
        if (isset($_GET['fecha'])) {
            $fechaActual = new DateTime($_GET['fecha']);
        }
        
        // Calcular el inicio y fin de la semana
        $numeroDiaSemana = $fechaActual->format('N'); // 1 (lunes) a 7 (domingo)
        $inicioSemana = clone $fechaActual;
        $inicioSemana->modify('-' . ($numeroDiaSemana - 1) . ' days'); // Retroceder hasta el lunes
        
        $finSemana = clone $inicioSemana;
        $finSemana->modify('+6 days'); // Avanzar 6 días para llegar al domingo
        
        // Formatear las fechas para la consulta SQL
        $fechaInicio = $inicioSemana->format('Y-m-d');
        $fechaFin = $finSemana->format('Y-m-d');
        
        // Obtener reservas de esa semana
        $reservasSemana = $this->modelo->obtenerReservasPorSemana($fechaInicio, $fechaFin);
        
        // Cargar la vista
        include_once 'vistas/reportes/semanal.php';
    }

    // Método para generar reporte mensual
    public function reporteMensual() {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }
        
        // Obtener mes y año actuales
        $mesActual = date('m');
        $anioActual = date('Y');
        
        // Obtener valores de los filtros si se proporcionaron
        if (isset($_GET['mes']) && isset($_GET['anio'])) {
            $mesSeleccionado = $_GET['mes'];
            $anioSeleccionado = $_GET['anio'];
        } else {
            $mesSeleccionado = $mesActual;
            $anioSeleccionado = $anioActual;
        }
        
        // Obtener datos para el reporte
        $reservas = $this->modelo->obtenerReservasPorMes($mesSeleccionado, $anioSeleccionado);
        $estadisticas = $this->modelo->obtenerEstadisticasMensuales($mesSeleccionado, $anioSeleccionado);
        $tiposUsoPopulares = $this->modelo->obtenerTiposUsoPopulares($mesSeleccionado, $anioSeleccionado);
        
        // Obtener todos los años para el selector de filtros
        $anosDisponibles = $this->modelo->obtenerAnosDisponibles();
        
        // Cargar la vista
        include_once 'vistas/reportes/mensual.php';
    }

    // Método para preparar la impresión en PDF
    public function imprimirReporte() {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }
        
        // Determinar tipo de reporte (semanal o mensual)
        $tipoReporte = isset($_GET['tipo']) ? $_GET['tipo'] : 'mensual';
        
        if ($tipoReporte == 'semanal') {
            // Obtener la fecha del reporte semanal
            $fecha = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
            
            // Calcular inicio y fin de la semana
            $fechaObj = new DateTime($fecha);
            $numeroDiaSemana = $fechaObj->format('N');
            $inicioSemana = clone $fechaObj;
            $inicioSemana->modify('-' . ($numeroDiaSemana - 1) . ' days');
            
            $finSemana = clone $inicioSemana;
            $finSemana->modify('+6 days');
            
            // Formatear las fechas para la consulta SQL
            $fechaInicio = $inicioSemana->format('Y-m-d');
            $fechaFin = $finSemana->format('Y-m-d');
            
            // Obtener reservas de esa semana
            $reservas = $this->modelo->obtenerReservasPorSemana($fechaInicio, $fechaFin);
            
            // Cargar la vista para impresión
            include_once 'vistas/reportes/imprimir_semanal.php';
        } else {
            // Reporte mensual
            $mes = isset($_GET['mes']) ? $_GET['mes'] : date('m');
            $anio = isset($_GET['anio']) ? $_GET['anio'] : date('Y');
            
            // Obtener reservas de ese mes
            $reservas = $this->modelo->obtenerReservasPorMes($mes, $anio);
            $estadisticas = $this->modelo->obtenerEstadisticasMensuales($mes, $anio);
            
            // Cargar la vista para impresión
            include_once 'vistas/reportes/imprimir_mensual.php';
        }
    }
}
