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
        $consulta = $this->conexion->prepare("SELECT r.*, u.nombre, u.apellido, u.matricula FROM reservas r 
        INNER JOIN usuarios u ON r.id_usuario = u.id 
        WHERE id_usuario = :id_usuario 
        ORDER BY r.fecha_evento DESC");
        $consulta->bindParam(':id_usuario', $id_usuario);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
    // Obtener una reserva específica
    public function obtenerReserva($id) {
        $consulta = $this->conexion->prepare("SELECT r.*, u.nombre, u.apellido, u.matricula, u.email, u.telefono, 
                                             r.archivo_formulario, r.archivo_municipal, r.archivo_comprobante, r.archivo_comprobante_total,
                                             r.codigo_unico, r.monto_anticipo, r.monto_saldo, r.anticipo_pagado, r.saldo_pagado 
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
                                             r.archivo_formulario, r.archivo_municipal, r.archivo_comprobante, r.archivo_comprobante_total,
                                             r.monto_anticipo, r.monto_saldo, r.anticipo_pagado, r.saldo_pagado 
                                             FROM reservas r 
                                             INNER JOIN usuarios u ON r.id_usuario = u.id 
                                             WHERE r.codigo_unico = :codigo_unico");
        $consulta->bindParam(':codigo_unico', $codigoUnico);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una nueva reserva
    public function crearReserva($id_usuario, $fecha_evento, $hora_inicio, $hora_fin, $tipo_uso, $motivo_de_uso, $codigo_unico, $fecha_vencimiento, $rol = null) {

        // Verificar si la fecha es fin de semana (6=sábado, 7=domingo)
        // Los administradores no tienen límite de reservas en fin de semana
        if ($rol !== 'administrador') {
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

        // VALIDACIÓN CRÍTICA: Solo se permite UNA reserva por día (independientemente del horario)
        // Esta validación es prioritaria y aplica a todos los roles
        $consulta_dia = $this->conexion->prepare("SELECT COUNT(*) as total FROM reservas 
                                                 WHERE fecha_evento = :fecha_evento 
                                                 AND estado IN ('pendiente', 'aprobada')");
        $consulta_dia->bindParam(':fecha_evento', $fecha_evento);
        $consulta_dia->execute();
        $resultado_dia = $consulta_dia->fetch(PDO::FETCH_ASSOC);
        
        // Si ya existe una reserva para esa fecha (cualquier horario), rechazar
        if ($resultado_dia['total'] > 0) {
            return ['error' => 'Ya existe una reserva para la fecha ' . date('d/m/Y', strtotime($fecha_evento)) . '. Solo se permite una reserva por día.'];
        }
        
        // VALIDACIÓN SECUNDARIA: Verificar conflictos de horarios (redundante pero mantenida por seguridad)
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
        
        // Si hay reservas en ese horario, retornar falso (esta validación ahora es redundante)
        if ($resultado['total'] > 0) {
            return ['error' => 'Ya existe una reserva para esa fecha y horario.'];
        }
        
        // Obtener monto según horario
        $consulta_monto = $this->conexion->prepare("SELECT * FROM configuracion_aranceles 
                                                  WHERE activo = 1 
                                                  AND :fecha_actual BETWEEN fecha_inicio AND fecha_fin");
        $fecha_actual = date('Y-m-d');
        $consulta_monto->bindParam(':fecha_actual', $fecha_actual);
        $consulta_monto->execute();
        $config_arancel = $consulta_monto->fetch(PDO::FETCH_ASSOC);
        
        // Corregir la lógica para aplicar el monto correcto según el horario seleccionado
        $hora_comparacion = "22:00:00";
        $hora_inicio_ts = strtotime($hora_inicio);
        $hora_comparacion_ts = strtotime($hora_comparacion);
        
        // Si la hora de inicio es igual o posterior a las 22:00 o está dentro del rango nocturno, usar monto_despues_22
        if ($hora_inicio_ts >= $hora_comparacion_ts) {
            $monto = $config_arancel['monto_despues_22'];
        } else {
            $monto = $config_arancel['monto_antes_22'];
        }
        
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
    public function subirArchivos($id, $archivo_formulario = null, $archivo_comprobante = null, $archivo_municipal = null, $archivo_comprobante_total = null, $archivo_comprobante_devolucion = null, $monto_devolucion = null, $observaciones_devolucion = null) {
        // Construir la consulta dinámicamente según los parámetros proporcionados
        $campos_actualizar = [];
        $parametros = [':id' => $id];
        
        if ($archivo_formulario !== null) {
            $campos_actualizar[] = "archivo_formulario = :archivo_formulario";
            $parametros[':archivo_formulario'] = $archivo_formulario;
        }
        
        if ($archivo_comprobante !== null) {
            $campos_actualizar[] = "archivo_comprobante = :archivo_comprobante";
            $parametros[':archivo_comprobante'] = $archivo_comprobante;
        }
        
        if ($archivo_municipal !== null) {
            $campos_actualizar[] = "archivo_municipal = :archivo_municipal";
            $parametros[':archivo_municipal'] = $archivo_municipal;
        }
        
        if ($archivo_comprobante_total !== null) {
            $campos_actualizar[] = "archivo_comprobante_total = :archivo_comprobante_total";
            $parametros[':archivo_comprobante_total'] = $archivo_comprobante_total;
        }
        
        // Campos de devolución
        if ($archivo_comprobante_devolucion !== null) {
            $campos_actualizar[] = "archivo_comprobante_devolucion = :archivo_comprobante_devolucion";
            $parametros[':archivo_comprobante_devolucion'] = $archivo_comprobante_devolucion;
        }
        
        if ($monto_devolucion !== null) {
            $campos_actualizar[] = "monto_devolucion = :monto_devolucion";
            $parametros[':monto_devolucion'] = $monto_devolucion;
        }
        
        if ($observaciones_devolucion !== null) {
            $campos_actualizar[] = "observaciones_devolucion = :observaciones_devolucion";
            $parametros[':observaciones_devolucion'] = $observaciones_devolucion;
        }
        
        // Si hay datos de devolución, actualizar la fecha de devolución
        if ($archivo_comprobante_devolucion !== null || $monto_devolucion !== null || $observaciones_devolucion !== null) {
            $campos_actualizar[] = "fecha_devolucion = NOW()";
        }
        
        // Si no hay campos para actualizar, retornar true
        if (empty($campos_actualizar)) {
            return true;
        }
        
        $sql = "UPDATE reservas SET " . implode(", ", $campos_actualizar) . " WHERE id = :id";
        $consulta = $this->conexion->prepare($sql);
        
        foreach ($parametros as $param => $valor) {
            $consulta->bindValue($param, $valor);
        }
        
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
        $consulta = $this->conexion->query("SELECT r.id, r.fecha_evento as start, 
                                           CONCAT(r.tipo_uso, ' (', r.hora_inicio, ' - ', r.hora_fin, ')') as title, 
                                           r.id_usuario, r.estado, r.motivo_de_uso,
                                           u.nombre, u.apellido, u.telefono, u.email as correo,
                                           CONCAT(u.nombre, ' ', u.apellido) as nombre_completo,
                                           CASE 
                                                WHEN r.estado = 'aprobada' THEN '#28a745' 
                                                WHEN r.estado = 'pendiente' THEN '#ffc107'
                                                WHEN r.estado = 'rechazada' THEN '#dc3545'
                                                ELSE '#6c757d'
                                           END as backgroundColor,
                                           CASE 
                                                WHEN r.estado = 'aprobada' THEN '#28a745' 
                                                WHEN r.estado = 'pendiente' THEN '#ffc107'
                                                WHEN r.estado = 'rechazada' THEN '#dc3545'
                                                ELSE '#6c757d'
                                           END as borderColor
                                           FROM reservas r
                                           INNER JOIN usuarios u ON r.id_usuario = u.id
                                           WHERE r.fecha_evento >= CURDATE()
                                           AND r.estado NOT IN ('cancelada', 'baja') -- Modificado para mostrar rechazadas en calendario
                                           ORDER BY r.fecha_evento");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
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

    // Eliminar reserva (dar de baja o eliminar completamente)
    public function eliminarReserva($id, $eliminarCompletamente = false) {
        if ($eliminarCompletamente) {
            // IMPORTANTE: Antes de eliminar, respaldar los datos completos
            $this->respaldarReservaEliminada($id);
            
            // Eliminar completamente de la base de datos
            // La BD maneja automáticamente la eliminación en cascada de:
            // - historial_reservas (ON DELETE CASCADE)
            // - grupo_matriculados (ON DELETE CASCADE) 
            // - notificaciones (ON DELETE CASCADE)
            
            $consulta = $this->conexion->prepare("DELETE FROM reservas WHERE id = :id");
            $consulta->bindParam(':id', $id);
            return $consulta->execute();
        } else {
            // Solo cambiar estado a 'baja' (eliminación lógica)
            $consulta = $this->conexion->prepare("UPDATE reservas SET estado = 'baja' WHERE id = :id");
            $consulta->bindParam(':id', $id);
            return $consulta->execute();
        }
    }

    // Actualizar montos de pago (solo para administradores)
    public function actualizarMontosPago($id_reserva, $monto_anticipo, $monto_saldo) {
        $consulta = $this->conexion->prepare("UPDATE reservas 
                                             SET monto_anticipo = :monto_anticipo, monto_saldo = :monto_saldo 
                                             WHERE id = :id_reserva");
        $consulta->bindParam(':monto_anticipo', $monto_anticipo);
        $consulta->bindParam(':monto_saldo', $monto_saldo);
        $consulta->bindParam(':id_reserva', $id_reserva);
        return $consulta->execute();
    }

    // Registrar en historial con datos completos del usuario
    public function registrarHistorial($id_reserva, $id_usuario, $accion, $estado_anterior, $estado_nuevo, $comentario = null) {
        // Obtener datos del usuario que realiza la acción
        $consultaUsuario = $this->conexion->prepare("SELECT nombre, apellido, rol FROM usuarios WHERE id = :id_usuario");
        $consultaUsuario->bindParam(':id_usuario', $id_usuario);
        $consultaUsuario->execute();
        $usuario = $consultaUsuario->fetch(PDO::FETCH_ASSOC);
        
        $usuario_nombre = $usuario ? $usuario['nombre'] : null;
        $usuario_apellido = $usuario ? $usuario['apellido'] : null;
        $usuario_rol = $usuario ? $usuario['rol'] : null;
        
        $consulta = $this->conexion->prepare("INSERT INTO historial_reservas 
                                             (id_reserva, id_usuario, accion, estado_anterior, estado_nuevo, comentario, usuario_nombre, usuario_apellido, usuario_rol) 
                                             VALUES (:id_reserva, :id_usuario, :accion, :estado_anterior, :estado_nuevo, :comentario, :usuario_nombre, :usuario_apellido, :usuario_rol)");
        $consulta->bindParam(':id_reserva', $id_reserva);
        $consulta->bindParam(':id_usuario', $id_usuario);
        $consulta->bindParam(':accion', $accion);
        $consulta->bindParam(':estado_anterior', $estado_anterior);
        $consulta->bindParam(':estado_nuevo', $estado_nuevo);
        $consulta->bindParam(':comentario', $comentario);
        $consulta->bindParam(':usuario_nombre', $usuario_nombre);
        $consulta->bindParam(':usuario_apellido', $usuario_apellido);
        $consulta->bindParam(':usuario_rol', $usuario_rol);
        return $consulta->execute();
    }
    
    // Respaldar reserva antes de eliminación completa
    public function respaldarReservaEliminada($id_reserva) {
        // Obtener todos los datos de la reserva y del usuario
        $consulta = $this->conexion->prepare("
            SELECT r.*, u.nombre, u.apellido, u.email, u.matricula
            FROM reservas r 
            INNER JOIN usuarios u ON r.id_usuario = u.id 
            WHERE r.id = :id_reserva
        ");
        $consulta->bindParam(':id_reserva', $id_reserva);
        $consulta->execute();
        $reserva = $consulta->fetch(PDO::FETCH_ASSOC);
        
        if (!$reserva) {
            return false; // Reserva no encontrada
        }
        
        // Obtener datos del administrador que está eliminando
        $admin_id = $_SESSION['id_usuario'];
        $consultaAdmin = $this->conexion->prepare("SELECT nombre, apellido FROM usuarios WHERE id = :admin_id");
        $consultaAdmin->bindParam(':admin_id', $admin_id);
        $consultaAdmin->execute();
        $admin = $consultaAdmin->fetch(PDO::FETCH_ASSOC);
        
        // Insertar en tabla de respaldo
        $consultaRespaldo = $this->conexion->prepare("
            INSERT INTO reservas_eliminadas (
                reserva_id_original, id_usuario, nombre_usuario, apellido_usuario, email_usuario, matricula_usuario,
                fecha_evento, hora_inicio, hora_fin, tipo_uso, descripcion, monto, monto_anticipo, monto_saldo,
                estado, codigo_unico, archivo_formulario, archivo_comprobante, archivo_municipal, archivo_comprobante_total,
                fecha_vencimiento_pago, fecha_creacion_original, eliminado_por_usuario_id, eliminado_por_nombre, eliminado_por_apellido,
                motivo_eliminacion
            ) VALUES (
                :reserva_id_original, :id_usuario, :nombre_usuario, :apellido_usuario, :email_usuario, :matricula_usuario,
                :fecha_evento, :hora_inicio, :hora_fin, :tipo_uso, :descripcion, :monto, :monto_anticipo, :monto_saldo,
                :estado, :codigo_unico, :archivo_formulario, :archivo_comprobante, :archivo_municipal, :archivo_comprobante_total,
                :fecha_vencimiento_pago, :fecha_creacion_original, :eliminado_por_usuario_id, :eliminado_por_nombre, :eliminado_por_apellido,
                :motivo_eliminacion
            )
        ");
        
        // Bind de parámetros de la reserva
        $consultaRespaldo->bindParam(':reserva_id_original', $reserva['id']);
        $consultaRespaldo->bindParam(':id_usuario', $reserva['id_usuario']);
        $consultaRespaldo->bindParam(':nombre_usuario', $reserva['nombre']);
        $consultaRespaldo->bindParam(':apellido_usuario', $reserva['apellido']);
        $consultaRespaldo->bindParam(':email_usuario', $reserva['email']);
        $consultaRespaldo->bindParam(':matricula_usuario', $reserva['matricula']);
        $consultaRespaldo->bindParam(':fecha_evento', $reserva['fecha_evento']);
        $consultaRespaldo->bindParam(':hora_inicio', $reserva['hora_inicio']);
        $consultaRespaldo->bindParam(':hora_fin', $reserva['hora_fin']);
        $consultaRespaldo->bindParam(':tipo_uso', $reserva['tipo_uso']);
        $consultaRespaldo->bindParam(':descripcion', $reserva['motivo_de_uso']); // Usar motivo_de_uso como descripción
        $consultaRespaldo->bindParam(':monto', $reserva['monto']);
        $consultaRespaldo->bindParam(':monto_anticipo', $reserva['monto_anticipo']);
        $consultaRespaldo->bindParam(':monto_saldo', $reserva['monto_saldo']);
        $consultaRespaldo->bindParam(':estado', $reserva['estado']);
        $consultaRespaldo->bindParam(':codigo_unico', $reserva['codigo_unico']);
        $consultaRespaldo->bindParam(':archivo_formulario', $reserva['archivo_formulario']);
        $consultaRespaldo->bindParam(':archivo_comprobante', $reserva['archivo_comprobante']);
        $consultaRespaldo->bindParam(':archivo_municipal', $reserva['archivo_municipal']);
        $consultaRespaldo->bindParam(':archivo_comprobante_total', $reserva['archivo_comprobante_total']);
        $consultaRespaldo->bindParam(':fecha_vencimiento_pago', $reserva['fecha_vencimiento']); // Usar fecha_vencimiento
        $consultaRespaldo->bindParam(':fecha_creacion_original', $reserva['fecha_solicitud']); // Usar fecha_solicitud
        
        // Bind de parámetros del administrador que elimina
        $consultaRespaldo->bindParam(':eliminado_por_usuario_id', $admin_id);
        $consultaRespaldo->bindParam(':eliminado_por_nombre', $admin['nombre']);
        $consultaRespaldo->bindParam(':eliminado_por_apellido', $admin['apellido']);
        
        // Motivo de eliminación (se puede personalizar)
        $motivo = "Eliminación completa realizada por administrador {$admin['nombre']} {$admin['apellido']}";
        $consultaRespaldo->bindParam(':motivo_eliminacion', $motivo);
        
        return $consultaRespaldo->execute();
    }

    public function actualizarMotivoUso($id_reserva, $motivo_de_uso, $id_usuario_admin, $motivo_anterior)
    {
        try {
            $this->conexion->beginTransaction();

            $consulta = $this->conexion->prepare("UPDATE reservas SET motivo_de_uso = :motivo_de_uso WHERE id = :id_reserva");
            $consulta->bindParam(':motivo_de_uso', $motivo_de_uso);
            $consulta->bindParam(':id_reserva', $id_reserva, PDO::PARAM_INT);
            $consulta->execute();

            // Solo registrar historial si hubo un cambio real
            if ($consulta->rowCount() > 0) {
                $comentario = "Motivo de uso actualizado por administrador. Anterior: '" . htmlspecialchars($motivo_anterior) . "'. Nuevo: '" . htmlspecialchars($motivo_de_uso) . "'.";
                $this->registrarHistorial($id_reserva, $id_usuario_admin, 'actualizacion_motivo', null, null, $comentario);
                $this->conexion->commit();
                return true;
            } else {
                // Si no se afectaron filas (ej. el texto es idéntico), no es un error. Simplemente no se hace nada.
                $this->conexion->rollBack();
                return false;
            }
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log('Error al actualizar motivo de uso: ' . $e->getMessage());
            return false;
        }
    }
}