<?php
class ModeloPlataforma
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = BD::crearInstancia();
    }

    public function listar()
    {
        try {
            $stmt = $this->conexion->prepare("
                SELECT id, nombre, descripcion, telefono, correo, direccion, 
                       contacto_principal, sitio_web, estado, fecha_creacion, fecha_actualizacion
                FROM plataformas
                ORDER BY id DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en listar plataformas: " . $e->getMessage());
            return [];
        }
    }

    public function crear($nombre, $descripcion, $telefono, $correo, $direccion, $contacto_principal, $sitio_web, $estado = 'activo')
    {
        try {
            // Verificar si ya existe una plataforma con ese nombre
            $stmtVerificar = $this->conexion->prepare("
                SELECT COUNT(*) FROM plataformas WHERE nombre = :nombre
            ");
            $stmtVerificar->bindParam(":nombre", $nombre);
            $stmtVerificar->execute();
            
            if ($stmtVerificar->fetchColumn() > 0) {
                throw new Exception("Ya existe una plataforma con ese nombre");
            }

            // Insertar plataforma
            $stmt = $this->conexion->prepare("
                INSERT INTO plataformas (nombre, descripcion, telefono, correo, direccion, contacto_principal, sitio_web, estado) 
                VALUES (:nombre, :descripcion, :telefono, :correo, :direccion, :contacto_principal, :sitio_web, :estado)
            ");
            
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":descripcion", $descripcion);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":correo", $correo);
            $stmt->bindParam(":direccion", $direccion);
            $stmt->bindParam(":contacto_principal", $contacto_principal);
            $stmt->bindParam(":sitio_web", $sitio_web);
            $stmt->bindParam(":estado", $estado);
            
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            error_log("Error en crear plataforma: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $nombre, $descripcion, $telefono, $correo, $direccion, $contacto_principal, $sitio_web, $estado = null)
    {
        try {
            // Verificar si la plataforma existe
            $stmtVerificar = $this->conexion->prepare("SELECT * FROM plataformas WHERE id = :id");
            $stmtVerificar->bindParam(":id", $id);
            $stmtVerificar->execute();
            $plataforma = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
            
            if (!$plataforma) {
                throw new Exception("Plataforma no encontrada");
            }
            
            // Verificar que el nombre no esté duplicado (excluyendo la plataforma actual)
            $stmtDuplicado = $this->conexion->prepare("
                SELECT COUNT(*) FROM plataformas 
                WHERE nombre = :nombre AND id != :id
            ");
            $stmtDuplicado->bindParam(":nombre", $nombre);
            $stmtDuplicado->bindParam(":id", $id);
            $stmtDuplicado->execute();
            
            if ($stmtDuplicado->fetchColumn() > 0) {
                throw new Exception("El nombre de la plataforma ya está en uso");
            }

            // Construir la consulta de actualización
            $sql = "UPDATE plataformas SET 
                nombre = :nombre, 
                descripcion = :descripcion, 
                telefono = :telefono, 
                correo = :correo, 
                direccion = :direccion, 
                contacto_principal = :contacto_principal, 
                sitio_web = :sitio_web";
            
            $params = [
                ':id' => $id,
                ':nombre' => $nombre,
                ':descripcion' => $descripcion,
                ':telefono' => $telefono,
                ':correo' => $correo,
                ':direccion' => $direccion,
                ':contacto_principal' => $contacto_principal,
                ':sitio_web' => $sitio_web
            ];
            
            // Añadir estado si se ha proporcionado
            if (!empty($estado)) {
                $sql .= ", estado = :estado";
                $params[':estado'] = $estado;
            }
            
            $sql .= " WHERE id = :id";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute($params);
            
            return true;
        } catch (Exception $e) {
            error_log("Error en actualizar plataforma: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id)
    {
        try {
            // Verificar si la plataforma existe
            $stmtVerificar = $this->conexion->prepare("SELECT * FROM plataformas WHERE id = :id");
            $stmtVerificar->bindParam(":id", $id);
            $stmtVerificar->execute();
            
            if (!$stmtVerificar->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("Plataforma no encontrada");
            }
            
            // Eliminar permanentemente
            $stmt = $this->conexion->prepare("DELETE FROM plataformas WHERE id = :id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            return true;
        } catch (Exception $e) {
            error_log("Error en eliminar plataforma: " . $e->getMessage());
            return false;
        }
    }

    public function buscar($id)
    {
        try {
            $stmt = $this->conexion->prepare("
                SELECT * FROM plataformas WHERE id = :id
            ");
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en buscar plataforma: " . $e->getMessage());
            return null;
        }
    }
}