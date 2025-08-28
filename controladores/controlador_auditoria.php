<?php
/**
 * Controlador de Auditoría
 * Maneja la visualización de registros de auditoría y reservas eliminadas
 */

include_once("modelos/modelo_auditoria.php");
include_once("conexion.php");

class ControladorAuditoria {
    private $modelo;
    private $conexion;
    
    public function __construct() {
        $this->modelo = new ModeloAuditoria();
        $this->conexion = BD::crearInstancia();
    }
    
    // Mostrar historial de auditoría
    public function historial() {
        // Solo administradores pueden acceder
        if ($_SESSION['rol'] != 'administrador') {
            $_SESSION['error'] = "No tienes permisos para acceder a la auditoría.";
            header("Location: index.php?controlador=reservas&accion=listar");
            exit();
        }
        
        // Obtener filtros si existen
        $filtro_usuario = isset($_GET['usuario']) ? $_GET['usuario'] : '';
        $filtro_accion = isset($_GET['filtro_accion']) ? $_GET['filtro_accion'] : '';
        $fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
        $fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';
        
        // Obtener historial con filtros
        $historial = $this->modelo->obtenerHistorial($filtro_usuario, $filtro_accion, $fecha_desde, $fecha_hasta);
        
        // Obtener lista de usuarios para el filtro
        $usuarios = $this->modelo->obtenerUsuarios();
        
        // Cargar vista
        include_once("vistas/auditoria/historial.php");
    }
    
    // Mostrar reservas eliminadas
    public function reservasEliminadas() {
        // Solo administradores pueden acceder
        if ($_SESSION['rol'] != 'administrador') {
            $_SESSION['error'] = "No tienes permisos para acceder a las reservas eliminadas.";
            header("Location: index.php?controlador=reservas&accion=listar");
            exit();
        }
        
        // Obtener filtros si existen
        $filtro_eliminado_por = isset($_GET['eliminado_por']) ? $_GET['eliminado_por'] : '';
        $fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : '';
        $fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : '';
        
        // Obtener reservas eliminadas con filtros
        $reservas_eliminadas = $this->modelo->obtenerReservasEliminadas($filtro_eliminado_por, $fecha_desde, $fecha_hasta);
        
        // Obtener lista de administradores para el filtro
        $administradores = $this->modelo->obtenerAdministradores();
        
        // Cargar vista
        include_once("vistas/auditoria/reservas_eliminadas.php");
    }
    
    // Ver detalles de una reserva eliminada
    public function verReservaEliminada() {
        // Solo administradores pueden acceder
        if ($_SESSION['rol'] != 'administrador') {
            $_SESSION['error'] = "No tienes permisos para acceder a esta información.";
            header("Location: index.php?controlador=reservas&accion=listar");
            exit();
        }
        
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = "ID de reserva eliminada no especificado.";
            header("Location: index.php?controlador=auditoria&accion=reservasEliminadas");
            exit();
        }
        
        $id = $_GET['id'];
        $reserva_eliminada = $this->modelo->obtenerReservaEliminada($id);
        
        if (!$reserva_eliminada) {
            $_SESSION['error'] = "Reserva eliminada no encontrada.";
            header("Location: index.php?controlador=auditoria&accion=reservasEliminadas");
            exit();
        }
        
        // Cargar vista
        include_once("vistas/auditoria/ver_reserva_eliminada.php");
    }
    
    // Generar reporte PDF
    public function generarReporte() {
        // Solo administradores pueden generar reportes
        if ($_SESSION['rol'] != 'administrador') {
            $_SESSION['error'] = "No tienes permisos para generar reportes.";
            header("Location: index.php?controlador=reservas&accion=listar");
            exit();
        }
        
        // Obtener parámetros del reporte
        $tipo_reporte = isset($_GET['tipo']) ? $_GET['tipo'] : 'historial';
        $fecha_desde = isset($_GET['fecha_desde']) ? $_GET['fecha_desde'] : date('Y-m-01');
        $fecha_hasta = isset($_GET['fecha_hasta']) ? $_GET['fecha_hasta'] : date('Y-m-t');
        
        if ($tipo_reporte === 'eliminadas') {
            $datos = $this->modelo->obtenerReservasEliminadas('', $fecha_desde, $fecha_hasta);
            $titulo = "Reporte de Reservas Eliminadas";
            // Cargar vista PDF para reservas eliminadas
            include_once("vistas/auditoria/reporte_eliminadas_pdf.php");
        } else {
            $datos = $this->modelo->obtenerHistorial('', '', $fecha_desde, $fecha_hasta);
            $titulo = "Reporte de Historial de Auditoría";
            // Cargar vista PDF para historial
            include_once("vistas/auditoria/reporte_historial_pdf.php");
        }
        
        exit();
    }

}
?>
