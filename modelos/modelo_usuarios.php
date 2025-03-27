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
                SELECT u.*, r.nombre AS rol,ru.rol_id as rol_id
                FROM usuarios u
                LEFT JOIN rol_usuario ru ON u.id = ru.usuario_id
                LEFT JOIN roles r ON ru.rol_id = r.id
                WHERE u.email = :email LIMIT 1
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
                SELECT u.id, u.nombre, u.email, r.nombre as rol
                FROM usuarios u
                INNER JOIN rol_usuario ru ON u.id = ru.usuario_id
                INNER JOIN roles r ON ru.rol_id = r.id
                ORDER BY u.id DESC
            ");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error en listar: " . $e->getMessage());
            return [];
        }
    }

    public function crear($nombre, $email, $password, $rol)
    {
        try {
            $this->conexion->beginTransaction();

            // Verificar si el rol existe
            $stmtRol = $this->conexion->prepare("SELECT id FROM roles WHERE nombre = :rol");
            $stmtRol->bindParam(":rol", $rol);
            $stmtRol->execute();
            $rolId = $stmtRol->fetchColumn();

            if (!$rolId) {
                throw new Exception("El rol especificado no existe");
            }

            // Insertar usuario
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmtUsuario = $this->conexion->prepare("INSERT INTO usuarios (nombre, email, password) VALUES (:nombre, :email, :password)");
            $stmtUsuario->bindParam(":nombre", $nombre);
            $stmtUsuario->bindParam(":email", $email);
            $stmtUsuario->bindParam(":password", $hashedPassword);
            $stmtUsuario->execute();

            $usuarioId = $this->conexion->lastInsertId();

            // Asignar rol
            $stmtRolUsuario = $this->conexion->prepare("INSERT INTO rol_usuario (usuario_id, rol_id) VALUES (:usuario_id, :rol_id)");
            $stmtRolUsuario->bindParam(":usuario_id", $usuarioId);
            $stmtRolUsuario->bindParam(":rol_id", $rolId);
            $stmtRolUsuario->execute();

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollBack();
            error_log("Error en crear usuario: " . $e->getMessage());
            return false;
        }
    }

    public function actualizar($id, $nombre, $email, $password, $rol)
    {
        try {
            $this->conexion->beginTransaction();

            // Verificar si el rol existe
            $stmtRol = $this->conexion->prepare("SELECT id FROM roles WHERE nombre = :rol");
            $stmtRol->bindParam(":rol", $rol);
            $stmtRol->execute();
            $rolId = $stmtRol->fetchColumn();

            if (!$rolId) {
                throw new Exception("El rol especificado no existe");
            }
            // Actualizar usuario (mejorar consulta para evitar SQL estático)
            $sqlUsuario = "UPDATE usuarios SET nombre = :nombre, email = :email";
            $params = [':nombre' => $nombre, ':email' => $email, ':id' => $id];

            // Actualizar usuario
            if (!empty($password)) {
                $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
                $params[':password'] = $hashedPassword;
                $sqlUsuario .= ", password = :password";
            }
            $sqlUsuario .= " WHERE id = :id";

            $stmtUsuario = $this->conexion->prepare($sqlUsuario);
            $stmtUsuario->execute($params);

            // Actualizar rol
            $stmtRolUsuario = $this->conexion->prepare("UPDATE rol_usuario SET rol_id = :rol_id WHERE usuario_id = :usuario_id");
            $stmtRolUsuario->bindParam(":usuario_id", $id);
            $stmtRolUsuario->bindParam(":rol_id", $rolId);
            $stmtRolUsuario->execute();

            $this->conexion->commit();
            return true;
        } catch (Exception $e) {
            $this->conexion->rollBack();
            error_log("Error en actualizar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function eliminar($id)
    {
        try {
            $this->conexion->beginTransaction();

            // Eliminar relación en rol_usuario
            $stmtRolUsuario = $this->conexion->prepare("DELETE FROM rol_usuario WHERE usuario_id = :id");
            $stmtRolUsuario->bindParam(":id", $id);
            $stmtRolUsuario->execute();

            // Eliminar usuario
            $stmtUsuario = $this->conexion->prepare("DELETE FROM usuarios WHERE id = :id");
            $stmtUsuario->bindParam(":id", $id);
            $stmtUsuario->execute();

            $this->conexion->commit();
            return true;
        } catch (PDOException $e) {
            $this->conexion->rollBack();
            error_log("Error en eliminar usuario: " . $e->getMessage());
            return false;
        }
    }

    public function buscar($id)
    {
        try {
            $stmt = $this->conexion->prepare("
                SELECT u.id, u.nombre, u.email,u.password, r.nombre as rol
                FROM usuarios u
                INNER JOIN rol_usuario ru ON u.id = ru.usuario_id
                INNER JOIN roles r ON ru.rol_id = r.id
                WHERE u.id = :id
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
