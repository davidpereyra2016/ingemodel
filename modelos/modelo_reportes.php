<?php

class ModeloReportes {
    private $conexion;
    
    public function __construct() {
        $this->conexion = BD::crearInstancia();
    }

    // Obtener reportes por semana
    public function obtenerReservasPorSemana($fechaInicio, $fechaFin) {
        $consulta = $this->conexion->prepare("
            SELECT 
                r.id, 
                r.fecha_evento, 
                r.hora_inicio, 
                r.hora_fin, 
                r.tipo_uso, 
                r.estado, 
                r.monto, 
                u.nombre, 
                u.apellido, 
                u.matricula 
            FROM 
                reservas r 
            INNER JOIN 
                usuarios u ON r.id_usuario = u.id 
            WHERE 
                r.fecha_evento BETWEEN :fecha_inicio AND :fecha_fin 
            ORDER BY 
                r.fecha_evento ASC
        ");
        
        $consulta->bindParam(':fecha_inicio', $fechaInicio);
        $consulta->bindParam(':fecha_fin', $fechaFin);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener reportes por mes
    public function obtenerReservasPorMes($mes, $anio) {
        $consulta = $this->conexion->prepare("
            SELECT 
                r.id, 
                r.fecha_evento, 
                r.hora_inicio, 
                r.hora_fin, 
                r.tipo_uso, 
                r.estado, 
                r.monto, 
                u.nombre, 
                u.apellido, 
                u.matricula 
            FROM 
                reservas r 
            INNER JOIN 
                usuarios u ON r.id_usuario = u.id 
            WHERE 
                MONTH(r.fecha_evento) = :mes 
                AND YEAR(r.fecha_evento) = :anio 
            ORDER BY 
                r.fecha_evento ASC
        ");
        
        $consulta->bindParam(':mes', $mes);
        $consulta->bindParam(':anio', $anio);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener estadísticas mensuales
    public function obtenerEstadisticasMensuales($mes, $anio) {
        $consulta = $this->conexion->prepare("
            SELECT 
                COUNT(*) as total_reservas,
                SUM(CASE WHEN estado = 'aprobada' THEN 1 ELSE 0 END) as aprobadas,
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) as pendientes,
                SUM(CASE WHEN estado = 'rechazada' THEN 1 ELSE 0 END) as rechazadas,
                SUM(CASE WHEN estado = 'cancelada' THEN 1 ELSE 0 END) as canceladas,
                SUM(monto) as ingreso_total
            FROM 
                reservas 
            WHERE 
                MONTH(fecha_evento) = :mes 
                AND YEAR(fecha_evento) = :anio
        ");
        
        $consulta->bindParam(':mes', $mes);
        $consulta->bindParam(':anio', $anio);
        $consulta->execute();
        
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener los tipos de uso más solicitados
    public function obtenerTiposUsoPopulares($mes, $anio) {
        $consulta = $this->conexion->prepare("
            SELECT 
                tipo_uso, 
                COUNT(*) as cantidad 
            FROM 
                reservas 
            WHERE 
                MONTH(fecha_evento) = :mes 
                AND YEAR(fecha_evento) = :anio 
            GROUP BY 
                tipo_uso 
            ORDER BY 
                cantidad DESC
        ");
        
        $consulta->bindParam(':mes', $mes);
        $consulta->bindParam(':anio', $anio);
        $consulta->execute();
        
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener lista de años con reservas
    public function obtenerAnosDisponibles() {
        $consulta = $this->conexion->query("
            SELECT DISTINCT YEAR(fecha_evento) as anio 
            FROM reservas 
            ORDER BY anio DESC
        ");
        
        return $consulta->fetchAll(PDO::FETCH_COLUMN);
    }
}
