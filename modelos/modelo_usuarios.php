<?php
class ModeloUsuarios
{
    private $conexion;

    public function __construct()
    {
        $this->conexion = BD::crearInstancia();
    }

    public function validarUsuario($email, $password)
    {
        try {
            $stmt = $this->conexion->prepare("
                SELECT * FROM usuarios
                WHERE email = :email AND estado = 'activo' LIMIT 1
            ");
            $stmt->bindParam(":email", $email);
            $stmt->execute();

            if ($usuario = $stmt->fetch(PDO::FETCH_ASSOC)) {
                if (password_verify($password, $usuario['password'])) {
                    return $usuario;
                }
            }
            return false;
        } catch (PDOException $e) {
            error_log("Error en validarUsuario: " . $e->getMessage());
            return false;
        }
    }

    public function listar()
    {
        try {
            $stmt = $this->conexion->prepare("
                SELECT id, matricula, nombre, apellido, email, telefono, domicilio, rol, estado
                FROM usuarios
                ORDER BY id DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en listar: " . $e->getMessage());
            return [];
        }
    }

    public function crear($matricula, $nombre, $apellido, $email, $telefono, $domicilio, $password, $rol = 'ingeniero', $estado = 'activo')
    {
        try {
            // Verificar si ya existe un usuario con esa matrícula o email
            $stmtVerificar = $this->conexion->prepare("
                SELECT COUNT(*) FROM usuarios WHERE matricula = :matricula OR email = :email
            ");
            $stmtVerificar->bindParam(":matricula", $matricula);
            $stmtVerificar->bindParam(":email", $email);
            $stmtVerificar->execute();
            
            if ($stmtVerificar->fetchColumn() > 0) {
                throw new Exception("Ya existe un usuario con esa matrícula o correo electrónico");
            }

            // Insertar usuario
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->conexion->prepare("
                INSERT INTO usuarios (matricula, nombre, apellido, email, telefono, domicilio, password, rol, estado) 
                VALUES (:matricula, :nombre, :apellido, :email, :telefono, :domicilio, :password, :rol, :estado)
            ");
            
            $stmt->bindParam(":matricula", $matricula);
            $stmt->bindParam(":nombre", $nombre);
            $stmt->bindParam(":apellido", $apellido);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":telefono", $telefono);
            $stmt->bindParam(":domicilio", $domicilio);
            $stmt->bindParam(":password", $hashedPassword);
            $stmt->bindParam(":rol", $rol);
            $stmt->bindParam(":estado", $estado);
            
            $stmt->execute();
            return true;
        } catch (Exception $e) {
            error_log("Error en crear usuario por que necesita registrar manualmente los campos y no hacerlo por defecto: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $matricula, $nombre, $apellido, $email, $telefono, $domicilio, $password = null, $rol = null, $estado = null)
    {
        try {
            // Verificar si el usuario existe
            $stmtVerificar = $this->conexion->prepare("SELECT * FROM usuarios WHERE id = :id");
            $stmtVerificar->bindParam(":id", $id);
            $stmtVerificar->execute();
            $usuario = $stmtVerificar->fetch(PDO::FETCH_ASSOC);
            
            if (!$usuario) {
                throw new Exception("Usuario no encontrado");
            }
            
            // Verificar que la matrícula o el email no estén duplicados (excluyendo el usuario actual)
            $stmtDuplicado = $this->conexion->prepare("
                SELECT COUNT(*) FROM usuarios 
                WHERE (matricula = :matricula OR email = :email) AND id != :id
            ");
            $stmtDuplicado->bindParam(":matricula", $matricula);
            $stmtDuplicado->bindParam(":email", $email);
            $stmtDuplicado->bindParam(":id", $id);
            $stmtDuplicado->execute();
            
            if ($stmtDuplicado->fetchColumn() > 0) {
                throw new Exception("La matrícula o el correo electrónico ya están en uso");
            }

            // Construir la consulta de actualización
            $sql = "UPDATE usuarios SET 
                matricula = :matricula, 
                nombre = :nombre, 
                apellido = :apellido, 
                email = :email, 
                telefono = :telefono, 
                domicilio = :domicilio";
            
            $params = [
                ':id' => $id,
                ':matricula' => $matricula,
                ':nombre' => $nombre,
                ':apellido' => $apellido,
                ':email' => $email,
                ':telefono' => $telefono,
                ':domicilio' => $domicilio
            ];
            
            // Añadir contraseña si se ha proporcionado
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $sql .= ", password = :password";
                $params[':password'] = $hashedPassword;
            }
            
            // Añadir rol si se ha proporcionado
            if (!empty($rol)) {
                $sql .= ", rol = :rol";
                $params[':rol'] = $rol;
            }
            
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
            error_log("Error en actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id)
    {
        try {
            // Verificar si el usuario existe
            $stmtVerificar = $this->conexion->prepare("SELECT * FROM usuarios WHERE id = :id");
            $stmtVerificar->bindParam(":id", $id);
            $stmtVerificar->execute();
            
            if (!$stmtVerificar->fetch(PDO::FETCH_ASSOC)) {
                throw new Exception("Usuario no encontrado");
            }
            
            // En lugar de eliminar, se podría cambiar el estado a 'inactivo'
            // $stmt = $this->conexion->prepare("UPDATE usuarios SET estado = 'inactivo' WHERE id = :id");
            
            // O eliminar permanentemente
            $stmt = $this->conexion->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            
            return true;
        } catch (Exception $e) {
            error_log("Error en eliminar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function buscar($id)
    {
        try {
            $stmt = $this->conexion->prepare("
                SELECT * FROM usuarios WHERE id = :id
            ");
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en buscar usuario: " . $e->getMessage());
            return null;
        }
    }
}
