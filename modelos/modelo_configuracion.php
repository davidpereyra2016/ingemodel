<?php
class ModeloConfiguracion {
    private $conexion;

    // Constructor
    public function __construct() {
        // Crea una instancia de la conexión a la base de datos
        $this->conexion = BD::crearInstancia();
    }

    // ----- Métodos para documentos -----
    
    // Obtener todos los documentos
    public function obtenerDocumentos() {
        $consulta = $this->conexion->query("SELECT * FROM documentos ORDER BY id DESC");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un documento por su ID
    public function obtenerDocumentoPorId($id) {
        $consulta = $this->conexion->prepare("SELECT * FROM documentos WHERE id = :id");
        $consulta->bindParam(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo documento
    public function crearDocumento($nombre, $descripcion, $archivo) {
        $consulta = $this->conexion->prepare("INSERT INTO documentos (nombre, descripcion, archivo, fecha_creacion, activo) 
                    VALUES (:nombre, :descripcion, :archivo, NOW(), 1)");
        $consulta->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $consulta->bindParam(':archivo', $archivo, PDO::PARAM_STR);
        return $consulta->execute();
    }

    // Actualizar un documento existente
    public function actualizarDocumento($id, $nombre, $descripcion, $archivo = null) {
        // Si se proporciona un nuevo archivo, actualizar también el archivo
        if ($archivo) {
            $consulta = $this->conexion->prepare("UPDATE documentos SET nombre = :nombre, descripcion = :descripcion, 
                     archivo = :archivo, fecha_actualizacion = NOW() 
                     WHERE id = :id");
            $consulta->bindParam(':archivo', $archivo, PDO::PARAM_STR);
        } else {
            $consulta = $this->conexion->prepare("UPDATE documentos SET nombre = :nombre, descripcion = :descripcion, 
                     fecha_actualizacion = NOW() WHERE id = :id");
        }
        
        $consulta->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $consulta->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $consulta->execute();
    }

    // Eliminar un documento
    public function eliminarDocumento($id) {
        // Primero obtenemos el nombre del archivo para eliminarlo físicamente
        $documento = $this->obtenerDocumentoPorId($id);
        
        if ($documento) {
            $consulta = $this->conexion->prepare("DELETE FROM documentos WHERE id = :id");
            $consulta->bindParam(':id', $id, PDO::PARAM_INT);
            return $consulta->execute();
        }
        return false;
    }

    // ----- Métodos para aranceles -----
    
    // Obtener todos los aranceles
    public function obtenerAranceles() {
        $consulta = $this->conexion->query("SELECT * FROM configuracion_aranceles ORDER BY id DESC");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener aranceles activos
    public function obtenerArancelesActivos() {
        $consulta = $this->conexion->query("SELECT * FROM configuracion_aranceles WHERE activo = 1 ORDER BY id DESC");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener un arancel por su ID
    public function obtenerArancelPorId($id) {
        $consulta = $this->conexion->prepare("SELECT * FROM configuracion_aranceles WHERE id = :id");
        $consulta->bindParam(':id', $id, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Crear un nuevo arancel
    public function crearArancel($nombre, $descripcion, $monto_antes_22, $monto_despues_22, $fecha_inicio, $fecha_fin, $activo = 1) {
        $consulta = $this->conexion->prepare("INSERT INTO configuracion_aranceles (nombre, descripcion, monto_antes_22, monto_despues_22, fecha_inicio, fecha_fin, activo) 
                 VALUES (:nombre, :descripcion, :monto_antes_22, :monto_despues_22, :fecha_inicio, :fecha_fin, :activo)");
        
        $consulta->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $consulta->bindParam(':monto_antes_22', $monto_antes_22, PDO::PARAM_STR);
        $consulta->bindParam(':monto_despues_22', $monto_despues_22, PDO::PARAM_STR);
        $consulta->bindParam(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
        $consulta->bindParam(':fecha_fin', $fecha_fin, PDO::PARAM_STR);
        $consulta->bindParam(':activo', $activo, PDO::PARAM_INT);
        
        return $consulta->execute();
    }

    // Actualizar un arancel existente
    public function actualizarArancel($id, $nombre, $descripcion, $monto_antes_22, $monto_despues_22, $fecha_inicio, $fecha_fin, $activo) {
        $consulta = $this->conexion->prepare("UPDATE configuracion_aranceles SET nombre = :nombre, descripcion = :descripcion, 
                 monto_antes_22 = :monto_antes_22, monto_despues_22 = :monto_despues_22, 
                 fecha_inicio = :fecha_inicio, fecha_fin = :fecha_fin, activo = :activo 
                 WHERE id = :id");
        
        $consulta->bindParam(':nombre', $nombre, PDO::PARAM_STR);
        $consulta->bindParam(':descripcion', $descripcion, PDO::PARAM_STR);
        $consulta->bindParam(':monto_antes_22', $monto_antes_22, PDO::PARAM_STR);
        $consulta->bindParam(':monto_despues_22', $monto_despues_22, PDO::PARAM_STR);
        $consulta->bindParam(':fecha_inicio', $fecha_inicio, PDO::PARAM_STR);
        $consulta->bindParam(':fecha_fin', $fecha_fin, PDO::PARAM_STR);
        $consulta->bindParam(':activo', $activo, PDO::PARAM_INT);
        $consulta->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $consulta->execute();
    }

    // Activar o desactivar un arancel
    public function cambiarEstadoArancel($id, $activo) {
        $consulta = $this->conexion->prepare("UPDATE configuracion_aranceles SET activo = :activo WHERE id = :id");
        $consulta->bindParam(':activo', $activo, PDO::PARAM_INT);
        $consulta->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $consulta->execute();
    }

    // Eliminar un arancel
    public function eliminarArancel($id) {
        $consulta = $this->conexion->prepare("DELETE FROM configuracion_aranceles WHERE id = :id");
        $consulta->bindParam(':id', $id, PDO::PARAM_INT);
        
        return $consulta->execute();
    }
}
?>
