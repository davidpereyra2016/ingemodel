<?php
/**
 * Modelo de Auditoría
 * Maneja las consultas relacionadas con el historial de auditoría y reservas eliminadas
 */

include_once("conexion.php");

class ModeloAuditoria {
    private $conexion;
    
    public function __construct() {
        $this->conexion = BD::crearInstancia();
    }
    
    // Obtener historial de auditoría con filtros
    public function obtenerHistorial($filtro_usuario = '', $filtro_accion = '', $fecha_desde = '', $fecha_hasta = '') {
        $sql = "SELECT h.*, u.nombre as usuario_nombre_original, u.apellido as usuario_apellido_original, u.rol as usuario_rol_original,
                       r.codigo_unico, r.tipo_uso, r.fecha_evento
                FROM historial_reservas h
                LEFT JOIN usuarios u ON h.id_usuario = u.id
                LEFT JOIN reservas r ON h.id_reserva = r.id
                WHERE 1=1";
        
        $params = [];
        
        // Filtro por usuario
        if (!empty($filtro_usuario)) {
            $sql .= " AND h.id_usuario = :usuario";
            $params[':usuario'] = $filtro_usuario;
        }
        
        // Filtro por acción
        if (!empty($filtro_accion)) {
            $sql .= " AND h.accion = :accion";
            $params[':accion'] = $filtro_accion;
        }
        
        // Filtro por fecha desde
        if (!empty($fecha_desde)) {
            $sql .= " AND DATE(h.fecha) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        
        // Filtro por fecha hasta
        if (!empty($fecha_hasta)) {
            $sql .= " AND DATE(h.fecha) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        
        $sql .= " ORDER BY h.fecha DESC LIMIT 1000";
        
        $consulta = $this->conexion->prepare($sql);
        
        foreach ($params as $param => $value) {
            $consulta->bindValue($param, $value);
        }
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener reservas eliminadas con filtros
    public function obtenerReservasEliminadas($filtro_eliminado_por = '', $fecha_desde = '', $fecha_hasta = '') {
        $sql = "SELECT * FROM reservas_eliminadas WHERE 1=1";
        
        $params = [];
        
        // Filtro por quien eliminó
        if (!empty($filtro_eliminado_por)) {
            $sql .= " AND eliminado_por_usuario_id = :eliminado_por";
            $params[':eliminado_por'] = $filtro_eliminado_por;
        }
        
        // Filtro por fecha desde
        if (!empty($fecha_desde)) {
            $sql .= " AND DATE(fecha_eliminacion) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        
        // Filtro por fecha hasta
        if (!empty($fecha_hasta)) {
            $sql .= " AND DATE(fecha_eliminacion) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        
        $sql .= " ORDER BY fecha_eliminacion DESC";
        
        $consulta = $this->conexion->prepare($sql);
        
        foreach ($params as $param => $value) {
            $consulta->bindValue($param, $value);
        }
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener una reserva eliminada específica
    public function obtenerReservaEliminada($id) {
        $consulta = $this->conexion->prepare("SELECT * FROM reservas_eliminadas WHERE id = :id");
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
    
    // Obtener lista de usuarios para filtros
    public function obtenerUsuarios() {
        $consulta = $this->conexion->prepare("SELECT id, nombre, apellido, rol FROM usuarios ORDER BY nombre, apellido");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener lista de administradores para filtros
    public function obtenerAdministradores() {
        $consulta = $this->conexion->prepare("SELECT id, nombre, apellido FROM usuarios WHERE rol = 'administrador' ORDER BY nombre, apellido");
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    
    // Obtener estadísticas de auditoría
    public function obtenerEstadisticas($fecha_desde = '', $fecha_hasta = '') {
        $sql_base = "SELECT 
                        COUNT(*) as total,
                        COUNT(CASE WHEN accion = 'creación' THEN 1 END) as creaciones,
                        COUNT(CASE WHEN accion = 'baja' THEN 1 END) as bajas,
                        COUNT(CASE WHEN accion = 'eliminacion_completa' THEN 1 END) as eliminaciones,
                        COUNT(CASE WHEN accion = 'cambio_estado' THEN 1 END) as cambios_estado,
                        COUNT(CASE WHEN accion = 'actualizacion_montos' THEN 1 END) as actualizaciones_montos
                     FROM historial_reservas WHERE 1=1";
        
        $params = [];
        
        if (!empty($fecha_desde)) {
            $sql_base .= " AND DATE(fecha) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        
        if (!empty($fecha_hasta)) {
            $sql_base .= " AND DATE(fecha) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        
        $consulta = $this->conexion->prepare($sql_base);
        
        foreach ($params as $param => $value) {
            $consulta->bindValue($param, $value);
        }
        
        $consulta->execute();
        $estadisticas = $consulta->fetch(PDO::FETCH_ASSOC);
        
        // Obtener estadísticas de reservas eliminadas
        $sql_eliminadas = "SELECT COUNT(*) as total_eliminadas FROM reservas_eliminadas WHERE 1=1";
        $params_eliminadas = [];
        
        if (!empty($fecha_desde)) {
            $sql_eliminadas .= " AND DATE(fecha_eliminacion) >= :fecha_desde";
            $params_eliminadas[':fecha_desde'] = $fecha_desde;
        }
        
        if (!empty($fecha_hasta)) {
            $sql_eliminadas .= " AND DATE(fecha_eliminacion) <= :fecha_hasta";
            $params_eliminadas[':fecha_hasta'] = $fecha_hasta;
        }
        
        $consulta_eliminadas = $this->conexion->prepare($sql_eliminadas);
        
        foreach ($params_eliminadas as $param => $value) {
            $consulta_eliminadas->bindValue($param, $value);
        }
        
        $consulta_eliminadas->execute();
        $eliminadas = $consulta_eliminadas->fetch(PDO::FETCH_ASSOC);
        
        $estadisticas['total_eliminadas'] = $eliminadas['total_eliminadas'];
        
        return $estadisticas;
    }
    
    // Obtener actividad por usuario
    public function obtenerActividadPorUsuario($fecha_desde = '', $fecha_hasta = '') {
        $sql = "SELECT 
                    h.id_usuario,
                    COALESCE(h.usuario_nombre, u.nombre) as nombre,
                    COALESCE(h.usuario_apellido, u.apellido) as apellido,
                    COALESCE(h.usuario_rol, u.rol) as rol,
                    COUNT(*) as total_acciones,
                    COUNT(CASE WHEN h.accion = 'baja' THEN 1 END) as bajas,
                    COUNT(CASE WHEN h.accion = 'eliminacion_completa' THEN 1 END) as eliminaciones
                FROM historial_reservas h
                LEFT JOIN usuarios u ON h.id_usuario = u.id
                WHERE 1=1";
        
        $params = [];
        
        if (!empty($fecha_desde)) {
            $sql .= " AND DATE(h.fecha) >= :fecha_desde";
            $params[':fecha_desde'] = $fecha_desde;
        }
        
        if (!empty($fecha_hasta)) {
            $sql .= " AND DATE(h.fecha) <= :fecha_hasta";
            $params[':fecha_hasta'] = $fecha_hasta;
        }
        
        $sql .= " GROUP BY h.id_usuario, nombre, apellido, rol
                  ORDER BY total_acciones DESC";
        
        $consulta = $this->conexion->prepare($sql);
        
        foreach ($params as $param => $value) {
            $consulta->bindValue($param, $value);
        }
        
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}
?>
