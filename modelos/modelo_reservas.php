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
        $consulta = $this->conexion->prepare("SELECT r.*, u.nombre, u.apellido, u.matricula, u.email, u.telefono, 
                                             r.archivo_formulario, r.archivo_municipal, r.archivo_comprobante, r.archivo_comprobante_total,
                                             r.codigo_unico 
                                             FROM reservas r 
                                             INNER JOIN usuarios u ON r.id_usuario = u.id 
                                             WHERE r.id = :id");
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener una reserva específica por Código Único
    public function obtenerReservaPorCodigo($codigoUnico) {
        $consulta = $this->conexion->prepare("SELECT r.*, u.nombre, u.apellido, u.matricula, u.email, u.telefono, 
                                             r.archivo_formulario, r.archivo_municipal, r.archivo_comprobante, r.archivo_comprobante_total 
                                             FROM reservas r 
                                             INNER JOIN usuarios u ON r.id_usuario = u.id 
                                             WHERE r.codigo_unico = :codigo_unico");
        $consulta->bindParam(':codigo_unico', $codigoUnico);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una nueva reserva
    public function crearReserva($id_usuario, $fecha_evento, $hora_inicio, $hora_fin, $tipo_uso, $motivo_de_uso, $codigo_unico, $fecha_vencimiento) {

        // Verificar si la fecha es fin de semana (6=sábado, 7=domingo)
        $fecha_dia = date('N', strtotime($fecha_evento));
        if ($fecha_dia >= 6) {
            $consulta_limite = $this->conexion->prepare("SELECT COUNT(*) as total FROM reservas 
                                                        WHERE id_usuario = :id_usuario 
                                                        AND DAYOFWEEK(fecha_evento) IN (6,7) 
                                                        AND estado IN ('pendiente', 'aprobada')");
            $consulta_limite->bindParam(':id_usuario', $id_usuario);
            $consulta_limite->execute();
            $limite = $consulta_limite->fetch(PDO::FETCH_ASSOC);
            if ($limite['total'] >= 3) {
                return ['error' => 'Límite alcanzado: Máximo 3 reservas en fines de semana.'];
            }
        }
        
        // Verificar que los horarios estén dentro de los rangos permitidos: 11:00-16:00, 17:00-21:00, 22:00-05:00
        $rangos_permitidos = [
            ['inicio' => '11:00', 'fin' => '16:00'],
            ['inicio' => '17:00', 'fin' => '21:00'],
            ['inicio' => '22:00', 'fin' => '05:00']
        ];
        
        $horario_valido = false;
        $mensaje_error = 'Horario no válido. Debe estar dentro de alguno de estos rangos: 11:00-16:00, 17:00-21:00 o 22:00-05:00.';
        
        foreach ($rangos_permitidos as $rango) {
            // Convertir a timestamp para facilitar la comparación
            $inicio_rango = strtotime($rango['inicio']);
            $fin_rango = strtotime($rango['fin']);
            $hora_inicio_ts = strtotime($hora_inicio);
            $hora_fin_ts = strtotime($hora_fin);
            
            // Caso especial para el rango que cruza medianoche (22:00-05:00)
            if ($rango['inicio'] == '22:00' && $rango['fin'] == '05:00') {
                // Si la hora de inicio es después de las 22:00 o antes de las 05:00
                if (($hora_inicio_ts >= $inicio_rango || $hora_inicio_ts <= $fin_rango) &&
                    ($hora_fin_ts >= $inicio_rango || $hora_fin_ts <= $fin_rango)) {
                    $horario_valido = true;
                    break;
                }
            } else {
                // Para los otros rangos, validación normal
                if ($hora_inicio_ts >= $inicio_rango && 
                    $hora_inicio_ts < $fin_rango && 
                    $hora_fin_ts > $inicio_rango && 
                    $hora_fin_ts <= $fin_rango) {
                    $horario_valido = true;
                    break;
                }
            }
        }
        
        if (!$horario_valido) {
            return ['error' => $mensaje_error];
        }

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
        $consulta = $this->conexion->prepare("INSERT INTO reservas (id_usuario, fecha_evento, hora_inicio, hora_fin, tipo_uso, monto, motivo_de_uso, codigo_unico, fecha_vencimiento, estado) 
                                             VALUES (:id_usuario, :fecha_evento, :hora_inicio, :hora_fin, :tipo_uso, :monto, :motivo_de_uso, :codigo_unico, :fecha_vencimiento, 'pendiente')");
        $consulta->bindParam(':id_usuario', $id_usuario);
        $consulta->bindParam(':fecha_evento', $fecha_evento);
        $consulta->bindParam(':hora_inicio', $hora_inicio);
        $consulta->bindParam(':hora_fin', $hora_fin);
        $consulta->bindParam(':tipo_uso', $tipo_uso);
        $consulta->bindParam(':monto', $monto);
        $consulta->bindParam(':motivo_de_uso', $motivo_de_uso);
        $consulta->bindParam(':codigo_unico', $codigo_unico);
        $consulta->bindParam(':fecha_vencimiento', $fecha_vencimiento);
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

    // Actualizar estado de reserva por Código Único
    public function actualizarEstadoPorCodigo($codigo_unico, $estado, $motivo = null) {
        $consulta = $this->conexion->prepare("UPDATE reservas SET estado = :estado, motivo_rechazo = :motivo WHERE codigo_unico = :codigo_unico");
        $consulta->bindParam(':codigo_unico', $codigo_unico);
        $consulta->bindParam(':estado', $estado);
        $consulta->bindParam(':motivo', $motivo);
        return $consulta->execute();
    }

    // Verificar si la reserva ha expirado y cancelarla si es necesario
    public function verificarYCancelarReservaExpirada($codigoUnico) {
        $reserva = $this->obtenerReservaPorCodigo($codigoUnico);
        
        // Solo proceder si la reserva existe y está pendiente
        if ($reserva && $reserva['estado'] === 'pendiente') {
            $fechaVencimiento = strtotime($reserva['fecha_vencimiento']);
            $ahora = time();
            
            // Verificar si el tiempo ha expirado
            if ($fechaVencimiento < $ahora) {
                // Verificar si ya se subió algún comprobante de pago (50% o 100%)
                $tienePagoRegistrado = !empty($reserva['archivo_comprobante']) || !empty($reserva['archivo_comprobante_total']);
                
                // Solo cancelar si NO hay ningún comprobante de pago subido
                if (!$tienePagoRegistrado) {
                    // Marcar como cancelada
                    $this->actualizarEstadoPorCodigo($codigoUnico, 'cancelada', 'Expiró el tiempo para completar el pago.');
                    // Registro en historial
                    $this->registrarHistorial($reserva['id'], $reserva['id_usuario'], 'cancelacion_auto', 'pendiente', 'cancelada', 'Reserva cancelada automáticamente por expiración sin comprobante de pago.');
                    return ['estado' => 'cancelada', 'motivo' => 'Expiró el tiempo para completar el pago.'];
                } else {
                    // El tiempo expiró pero hay un comprobante de pago, no cancelar
                    return ['estado' => 'pendiente', 'motivo' => 'Tiempo expirado pero se detectó un pago registrado.'];
                }
            }
        }
        // Si no expiró, devolver el estado actual
        return $reserva ? ['estado' => $reserva['estado']] : ['estado' => 'no_encontrada'];
    }

    // Subir archivos relacionados a la reserva
    public function subirArchivos($id, $archivo_formulario = null, $archivo_comprobante = null, $archivo_municipal = null,$archivo_comprobante_total = null) {
        $consulta = $this->conexion->prepare("
            UPDATE reservas SET 
            archivo_formulario = COALESCE(:archivo_formulario, archivo_formulario),
            archivo_comprobante = COALESCE(:archivo_comprobante, archivo_comprobante),
            archivo_municipal = COALESCE(:archivo_municipal, archivo_municipal),
            archivo_comprobante_total = COALESCE(:archivo_comprobante_total, archivo_comprobante_total)
            WHERE id = :id");
        $consulta->bindParam(':id', $id);
        $consulta->bindParam(':archivo_formulario', $archivo_formulario);
        $consulta->bindParam(':archivo_comprobante', $archivo_comprobante);
        $consulta->bindParam(':archivo_municipal', $archivo_municipal);
        $consulta->bindParam(':archivo_comprobante_total', $archivo_comprobante_total);
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
                                           AND estado NOT IN ('cancelada', 'baja', 'rechazada') -- Ajustado para no mostrar canceladas, bajas o rechazadas en calendario
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
    
    // Obtener matriculados de un grupo
    public function obtenerMatriculadosGrupo($id_reserva) {
        $consulta = $this->conexion->prepare("SELECT * FROM grupo_matriculados WHERE id_reserva = :id_reserva");
        $consulta->bindParam(':id_reserva', $id_reserva);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar fecha_evento si existe
    public function buscarFechaEvento($fecha_evento) {
        $consulta = $this->conexion->prepare("SELECT * FROM reservas WHERE fecha_evento = :fecha_evento");
        $consulta->bindParam(':fecha_evento', $fecha_evento);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }
}