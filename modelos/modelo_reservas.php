<?php

class ModeloReservas {
    private $conexion;
    
    public function __construct() {
        $this->conexion = BD::crearInstancia();
    }

    // Obtener todas las reservas
    public function obtenerReservas() {
        $consulta = $this->conexion->query("SELECT r.*, u.nombre, u.apellido, u.matricula FROM reservas r 
                                           INNER JOIN usuarios u ON r.id_usuario = u.id 
                                           ORDER BY r.fecha_evento DESC");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener reservas de un usuario específico
    public function obtenerReservasPorUsuario($id_usuario) {
        $consulta = $this->conexion->prepare("SELECT * FROM reservas WHERE id_usuario = :id_usuario ORDER BY fecha_evento DESC");
        $consulta->bindParam(':id_usuario', $id_usuario);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una reserva específica
    public function obtenerReserva($id) {
        $consulta = $this->conexion->prepare("SELECT r.*, u.nombre, u.apellido, u.matricula, u.email, u.telefono 
                                             FROM reservas r 
                                             INNER JOIN usuarios u ON r.id_usuario = u.id 
                                             WHERE r.id = :id");
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una nueva reserva
    public function crearReserva($id_usuario, $fecha_evento, $hora_inicio, $hora_fin, $tipo_uso) {
        // Verificar si ya existe una reserva para esa fecha
        $consulta = $this->conexion->prepare("SELECT COUNT(*) as total FROM reservas 
                                             WHERE fecha_evento = :fecha_evento 
                                             AND ((hora_inicio <= :hora_inicio AND hora_fin >= :hora_inicio) 
                                             OR (hora_inicio <= :hora_fin AND hora_fin >= :hora_fin)
                                             OR (hora_inicio >= :hora_inicio AND hora_fin <= :hora_fin))
                                             AND estado IN ('pendiente', 'aprobada')");
        $consulta->bindParam(':fecha_evento', $fecha_evento);
        $consulta->bindParam(':hora_inicio', $hora_inicio);
        $consulta->bindParam(':hora_fin', $hora_fin);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        
        // Si hay reservas en ese horario, retornar falso
        if ($resultado['total'] > 0) {
            return false;
        }
        
        // Obtener monto según horario
        $consulta_monto = $this->conexion->prepare("SELECT * FROM configuracion_aranceles 
                                                  WHERE activo = 1 
                                                  AND :fecha_actual BETWEEN fecha_inicio AND fecha_fin");
        $fecha_actual = date('Y-m-d');
        $consulta_monto->bindParam(':fecha_actual', $fecha_actual);
        $consulta_monto->execute();
        $config_arancel = $consulta_monto->fetch(PDO::FETCH_ASSOC);
        
        $hora_comparacion = "22:00:00";
        $monto = ($hora_fin <= $hora_comparacion) ? $config_arancel['monto_antes_22'] : $config_arancel['monto_despues_22'];
        
        // Crear la reserva
        $consulta = $this->conexion->prepare("INSERT INTO reservas (id_usuario, fecha_evento, hora_inicio, hora_fin, tipo_uso, monto) 
                                             VALUES (:id_usuario, :fecha_evento, :hora_inicio, :hora_fin, :tipo_uso, :monto)");
        $consulta->bindParam(':id_usuario', $id_usuario);
        $consulta->bindParam(':fecha_evento', $fecha_evento);
        $consulta->bindParam(':hora_inicio', $hora_inicio);
        $consulta->bindParam(':hora_fin', $hora_fin);
        $consulta->bindParam(':tipo_uso', $tipo_uso);
        $consulta->bindParam(':monto', $monto);
        $consulta->execute();
        
        return $this->conexion->lastInsertId();
    }

    // Actualizar estado de reserva
    public function actualizarEstadoReserva($id, $estado, $motivo = null) {
        $consulta = $this->conexion->prepare("UPDATE reservas SET estado = :estado, motivo_rechazo = :motivo WHERE id = :id");
        $consulta->bindParam(':id', $id);
        $consulta->bindParam(':estado', $estado);
        $consulta->bindParam(':motivo', $motivo);
        return $consulta->execute();
    }

    // Subir archivos relacionados a la reserva
    public function subirArchivos($id, $archivo_formulario = null, $archivo_comprobante = null) {
        $consulta = $this->conexion->prepare("UPDATE reservas SET 
                                             archivo_formulario = COALESCE(:archivo_formulario, archivo_formulario),
                                             archivo_comprobante = COALESCE(:archivo_comprobante, archivo_comprobante) 
                                             WHERE id = :id");
        $consulta->bindParam(':id', $id);
        $consulta->bindParam(':archivo_formulario', $archivo_formulario);
        $consulta->bindParam(':archivo_comprobante', $archivo_comprobante);
        return $consulta->execute();
    }

    // Registrar pago de anticipo o saldo
    public function registrarPago($id, $tipo_pago) {
        $campo = ($tipo_pago == 'anticipo') ? 'anticipo_pagado' : 'saldo_pagado';
        $consulta = $this->conexion->prepare("UPDATE reservas SET $campo = TRUE WHERE id = :id");
        $consulta->bindParam(':id', $id);
        return $consulta->execute();
    }

    // Agregar matriculados adicionales a un grupo
    public function agregarMatriculadoGrupo($id_reserva, $matricula, $nombre_completo) {
        $consulta = $this->conexion->prepare("INSERT INTO grupo_matriculados (id_reserva, matricula, nombre_completo) 
                                             VALUES (:id_reserva, :matricula, :nombre_completo)");
        $consulta->bindParam(':id_reserva', $id_reserva);
        $consulta->bindParam(':matricula', $matricula);
        $consulta->bindParam(':nombre_completo', $nombre_completo);
        return $consulta->execute();
    }

    // Obtener eventos para el calendario
    public function obtenerEventosCalendario() {
        $consulta = $this->conexion->query("SELECT id, fecha_evento as start, 
                                           CONCAT(tipo_uso, ' (', hora_inicio, ' - ', hora_fin, ')') as title, 
                                           CASE 
                                                WHEN estado = 'aprobada' THEN '#28a745' 
                                                WHEN estado = 'pendiente' THEN '#ffc107'
                                                WHEN estado = 'rechazada' THEN '#dc3545'
                                                ELSE '#6c757d'
                                           END as color
                                           FROM reservas 
                                           WHERE fecha_evento >= CURDATE()
                                           ORDER BY fecha_evento");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Registrar en historial
    public function registrarHistorial($id_reserva, $id_usuario, $accion, $estado_anterior, $estado_nuevo, $comentario = null) {
        $consulta = $this->conexion->prepare("INSERT INTO historial_reservas 
                                             (id_reserva, id_usuario, accion, estado_anterior, estado_nuevo, comentario) 
                                             VALUES (:id_reserva, :id_usuario, :accion, :estado_anterior, :estado_nuevo, :comentario)");
        $consulta->bindParam(':id_reserva', $id_reserva);
        $consulta->bindParam(':id_usuario', $id_usuario);
        $consulta->bindParam(':accion', $accion);
        $consulta->bindParam(':estado_anterior', $estado_anterior);
        $consulta->bindParam(':estado_nuevo', $estado_nuevo);
        $consulta->bindParam(':comentario', $comentario);
        return $consulta->execute();
    }
}