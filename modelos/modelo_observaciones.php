<?php


class ModeloObservaciones {
    
    private $conexion;
    
    public function __construct() {
        $this->conexion = BD::crearInstancia();
    }

    // Obtener todas las observaciones con información de reserva y usuario
    public function obtenerObservaciones() {
        $consulta = $this->conexion->query("SELECT o.*, 
                                           r.fecha_evento, r.codigo_unico, r.tipo_uso,
                                           u_cliente.nombre as cliente_nombre, u_cliente.apellido as cliente_apellido, u_cliente.matricula as cliente_matricula,
                                           u_admin.nombre as admin_nombre, u_admin.apellido as admin_apellido
                                           FROM observaciones o 
                                           INNER JOIN reservas r ON o.id_reserva = r.id
                                           INNER JOIN usuarios u_cliente ON r.id_usuario = u_cliente.id
                                           INNER JOIN usuarios u_admin ON o.id_usuario_admin = u_admin.id
                                           ORDER BY o.fecha_creacion DESC");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener observaciones por usuario (para ingenieros)
    public function obtenerObservacionesPorUsuario($id_usuario) {
        $consulta = $this->conexion->prepare("SELECT o.*, 
                                             r.fecha_evento, r.codigo_unico, r.tipo_uso,
                                             u_cliente.nombre as cliente_nombre, u_cliente.apellido as cliente_apellido, u_cliente.matricula as cliente_matricula,
                                             u_admin.nombre as admin_nombre, u_admin.apellido as admin_apellido
                                             FROM observaciones o 
                                             INNER JOIN reservas r ON o.id_reserva = r.id
                                             INNER JOIN usuarios u_cliente ON r.id_usuario = u_cliente.id
                                             INNER JOIN usuarios u_admin ON o.id_usuario_admin = u_admin.id
                                             WHERE r.id_usuario = :id_usuario
                                             ORDER BY o.fecha_creacion DESC");
        $consulta->bindParam(':id_usuario', $id_usuario);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener una observación específica
    public function obtenerObservacion($id) {
        $consulta = $this->conexion->prepare("SELECT o.*, 
                                             r.fecha_evento, r.codigo_unico, r.tipo_uso, r.monto,
                                             u_cliente.nombre as cliente_nombre, u_cliente.apellido as cliente_apellido, 
                                             u_cliente.matricula as cliente_matricula, u_cliente.email as cliente_email,
                                             u_admin.nombre as admin_nombre, u_admin.apellido as admin_apellido
                                             FROM observaciones o 
                                             INNER JOIN reservas r ON o.id_reserva = r.id
                                             INNER JOIN usuarios u_cliente ON r.id_usuario = u_cliente.id
                                             INNER JOIN usuarios u_admin ON o.id_usuario_admin = u_admin.id
                                             WHERE o.id = :id");
        $consulta->bindParam(':id', $id);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Crear una nueva observación
    public function crearObservacion($id_reserva, $id_usuario_admin, $titulo, $descripcion, $tipo_observacion, $archivo_adjunto = null) {
        $consulta = $this->conexion->prepare("INSERT INTO observaciones 
                                             (id_reserva, id_usuario_admin, titulo, descripcion, tipo_observacion, archivo_adjunto) 
                                             VALUES (:id_reserva, :id_usuario_admin, :titulo, :descripcion, :tipo_observacion, :archivo_adjunto)");
        $consulta->bindParam(':id_reserva', $id_reserva);
        $consulta->bindParam(':id_usuario_admin', $id_usuario_admin);
        $consulta->bindParam(':titulo', $titulo);
        $consulta->bindParam(':descripcion', $descripcion);
        $consulta->bindParam(':tipo_observacion', $tipo_observacion);
        $consulta->bindParam(':archivo_adjunto', $archivo_adjunto);
        
        if ($consulta->execute()) {
            return $this->conexion->lastInsertId();
        }
        return false;
    }

    // Actualizar estado de observación
    public function actualizarEstadoObservacion($id, $estado, $comentario_resolucion = null) {
        $fecha_resolucion = ($estado === 'resuelta') ? date('Y-m-d H:i:s') : null;
        
        $consulta = $this->conexion->prepare("UPDATE observaciones 
                                             SET estado = :estado, 
                                                 comentario_resolucion = :comentario_resolucion,
                                                 fecha_resolucion = :fecha_resolucion
                                             WHERE id = :id");
        $consulta->bindParam(':id', $id);
        $consulta->bindParam(':estado', $estado);
        $consulta->bindParam(':comentario_resolucion', $comentario_resolucion);
        $consulta->bindParam(':fecha_resolucion', $fecha_resolucion);
        return $consulta->execute();
    }

    // Actualizar observación completa
    public function actualizarObservacion($id, $id_reserva, $titulo, $descripcion, $tipo_observacion, $archivo_adjunto = null) {
        // Si se proporciona un nuevo archivo, actualizar también el archivo
        if ($archivo_adjunto !== null) {
            $consulta = $this->conexion->prepare("UPDATE observaciones 
                                                 SET id_reserva = :id_reserva,
                                                     titulo = :titulo, 
                                                     descripcion = :descripcion,
                                                     tipo_observacion = :tipo_observacion,
                                                     archivo_adjunto = :archivo_adjunto,
                                                     fecha_actualizacion = CURRENT_TIMESTAMP
                                                 WHERE id = :id");
            $consulta->bindParam(':archivo_adjunto', $archivo_adjunto);
        } else {
            // Solo actualizar campos de texto, mantener archivo existente
            $consulta = $this->conexion->prepare("UPDATE observaciones 
                                                 SET id_reserva = :id_reserva,
                                                     titulo = :titulo, 
                                                     descripcion = :descripcion,
                                                     tipo_observacion = :tipo_observacion,
                                                     fecha_actualizacion = CURRENT_TIMESTAMP
                                                 WHERE id = :id");
        }
        
        $consulta->bindParam(':id', $id);
        $consulta->bindParam(':id_reserva', $id_reserva);
        $consulta->bindParam(':titulo', $titulo);
        $consulta->bindParam(':descripcion', $descripcion);
        $consulta->bindParam(':tipo_observacion', $tipo_observacion);
        
        return $consulta->execute();
    }

    // Eliminar observación
    public function eliminarObservacion($id) {
        $consulta = $this->conexion->prepare("DELETE FROM observaciones WHERE id = :id");
        $consulta->bindParam(':id', $id);
        return $consulta->execute();
    }

    // Obtener reservas para el dropdown (reservas que pueden tener observaciones)
    public function obtenerReservasParaObservacion() {
        $consulta = $this->conexion->query("SELECT r.id, r.codigo_unico, r.fecha_evento, r.tipo_uso,
                                           u.nombre, u.apellido, u.matricula, r.estado
                                           FROM reservas r
                                           INNER JOIN usuarios u ON r.id_usuario = u.id
                                           WHERE r.estado IN ('aprobada', 'completada', 'pendiente')
                                           ORDER BY r.fecha_evento DESC");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Buscar reserva por código único
    public function buscarReservaPorCodigo($codigo_unico) {
        $consulta = $this->conexion->prepare("SELECT r.id, r.codigo_unico, r.fecha_evento, r.tipo_uso,
                                             u.nombre, u.apellido, u.matricula, r.estado
                                             FROM reservas r
                                             INNER JOIN usuarios u ON r.id_usuario = u.id
                                             WHERE r.codigo_unico = :codigo_unico
                                             AND r.estado IN ('aprobada', 'completada', 'pendiente')");
        $consulta->bindParam(':codigo_unico', $codigo_unico);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Buscar reserva por ID
    public function buscarReservaPorId($id_reserva) {
        $consulta = $this->conexion->prepare("SELECT r.id, r.codigo_unico, r.fecha_evento, r.tipo_uso,
                                             u.nombre, u.apellido, u.matricula, r.estado
                                             FROM reservas r
                                             INNER JOIN usuarios u ON r.id_usuario = u.id
                                             WHERE r.id = :id_reserva
                                             AND r.estado IN ('aprobada', 'completada', 'pendiente')");
        $consulta->bindParam(':id_reserva', $id_reserva);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Contar observaciones por estado
    public function contarObservacionesPorEstado() {
        $consulta = $this->conexion->query("SELECT estado, COUNT(*) as total 
                                           FROM observaciones 
                                           GROUP BY estado");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener observaciones de una reserva específica
    public function obtenerObservacionesPorReserva($id_reserva) {
        $consulta = $this->conexion->prepare("SELECT o.*, 
                                             u_admin.nombre as admin_nombre, u_admin.apellido as admin_apellido
                                             FROM observaciones o 
                                             INNER JOIN usuarios u_admin ON o.id_usuario_admin = u_admin.id
                                             WHERE o.id_reserva = :id_reserva
                                             ORDER BY o.fecha_creacion DESC");
        $consulta->bindParam(':id_reserva', $id_reserva);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Verificar si una reserva tiene observaciones activas
    public function tieneObservacionesActivas($id_reserva) {
        $consulta = $this->conexion->prepare("SELECT COUNT(*) as total 
                                             FROM observaciones 
                                             WHERE id_reserva = :id_reserva 
                                             AND estado = 'activa'");
        $consulta->bindParam(':id_reserva', $id_reserva);
        $consulta->execute();
        $resultado = $consulta->fetch(PDO::FETCH_ASSOC);
        return $resultado['total'] > 0;
    }
}

?>