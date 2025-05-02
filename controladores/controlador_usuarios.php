<?php
include_once("conexion.php");
include_once("modelos/modelo_usuarios.php");

class ControladorUsuarios
{
    public function login()
    {
        // Solo iniciar sesión si no hay una activa
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['id_usuario'])) {
            // Verificar el rol del usuario
            if ($_SESSION['rol'] === 'ingeniero') {
                // Ingenieros van al calendario de reservas
                header('Location: index.php?controlador=reservas&accion=calendario');
            } else {
                // Administradores van al panel de administración
                header('Location: index.php?controlador=paginas&accion=inicio');
            }
            exit;
        }

        $modelo = new ModeloUsuarios();

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Obtener datos del formulario
            if (isset($_POST['email']) && isset($_POST['password'])) {
                $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
                $password = $_POST['password'];

                // Validar usuario
                if ($usuario = $modelo->validarUsuario($email, $password)) {
                    // Iniciar sesión
                    $_SESSION['id_usuario'] = $usuario['id'];
                    $_SESSION['nombre'] = $usuario['nombre'];
                    $_SESSION['apellido'] = $usuario['apellido'];
                    $_SESSION['email'] = $usuario['email'];
                    $_SESSION['matricula'] = $usuario['matricula'];
                    $_SESSION['rol'] = $usuario['rol'];

                    // Redirigir según el rol
                    if ($usuario['rol'] === 'ingeniero') {
                        header('Location: index.php?controlador=reservas&accion=calendario');
                    } else {
                        header('Location: index.php?controlador=paginas&accion=inicio');
                    }
                    exit;
                } else {
                    $error = "Credenciales inválidas";
                    include_once("vistas/usuarios/login.php");
                }
            }
        } else {
            // Mostrar formulario de login
            include_once("vistas/usuarios/login.php");
        }
    }

    public function logout()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        session_destroy();
        header('Location: index.php?controlador=usuarios&accion=login');
        exit;
    }

    public function listar()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }
        
        $modelo = new ModeloUsuarios();
        $usuarios = $modelo->listar();
        include_once("vistas/usuarios/listarUsuario.php");
    }

    public function crear()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $matricula = htmlspecialchars($_POST['matricula'], ENT_QUOTES, 'UTF-8');
            $nombre = htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8');
            $apellido = htmlspecialchars($_POST['apellido'], ENT_QUOTES, 'UTF-8');
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $telefono = htmlspecialchars($_POST['telefono'], ENT_QUOTES, 'UTF-8');
            $domicilio = htmlspecialchars($_POST['domicilio'], ENT_QUOTES, 'UTF-8');
            $password = $_POST['password'];
            $rol = htmlspecialchars($_POST['rol'], ENT_QUOTES, 'UTF-8');
            $estado = isset($_POST['estado']) ? htmlspecialchars($_POST['estado'], ENT_QUOTES, 'UTF-8') : 'activo';

            $modelo = new ModeloUsuarios();

            if ($modelo->crear($matricula, $nombre, $apellido, $email, $telefono, $domicilio, $password, $rol, $estado)) {
                $_SESSION['mensaje'] = "Usuario creado correctamente";
                header('Location: index.php?controlador=usuarios&accion=listar');
            } else {
                $_SESSION['error'] = "Error al crear usuario";
                header('Location: index.php?controlador=usuarios&accion=listar');
            }
            exit;
        }
    }

    public function editar()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $matricula = htmlspecialchars($_POST['matricula'], ENT_QUOTES, 'UTF-8');
            $nombre = htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8');
            $apellido = htmlspecialchars($_POST['apellido'], ENT_QUOTES, 'UTF-8');
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $telefono = htmlspecialchars($_POST['telefono'], ENT_QUOTES, 'UTF-8');
            $domicilio = htmlspecialchars($_POST['domicilio'], ENT_QUOTES, 'UTF-8');
            $password = !empty($_POST['password']) ? $_POST['password'] : null;
            $rol = htmlspecialchars($_POST['rol'], ENT_QUOTES, 'UTF-8');
            $estado = isset($_POST['estado']) ? htmlspecialchars($_POST['estado'], ENT_QUOTES, 'UTF-8') : null;

            $modelo = new ModeloUsuarios();

            if ($modelo->actualizar($id, $matricula, $nombre, $apellido, $email, $telefono, $domicilio, $password, $rol, $estado)) {
                $_SESSION['mensaje'] = "Usuario actualizado correctamente";
                header('Location: index.php?controlador=usuarios&accion=listar');
            } else {
                $_SESSION['error'] = "Error al actualizar usuario";
                header('Location: index.php?controlador=usuarios&accion=listar');
            }
            exit;
        }
    }

    public function eliminar()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $modelo = new ModeloUsuarios();

            if ($modelo->eliminar($id)) {
                $_SESSION['mensaje'] = "Usuario eliminado correctamente";
            } else {
                $_SESSION['error'] = "Error al eliminar usuario";
            }
        }
        header('Location: index.php?controlador=usuarios&accion=listar');
        exit;
    }

    public function buscar()
    {
        // Limpiar cualquier salida previa en el buffer
        ob_clean();
        
        // Asegurar que siempre respondamos con JSON para solicitudes AJAX
        header('Content-Type: application/json');
        
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        
        // Verificar si el usuario es administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'administrador') {
            echo json_encode(['success' => false, 'message' => 'No tiene permisos para realizar esta acción']);
            exit;
        }
        
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            
            try {
                $modelo = new ModeloUsuarios();
                $usuario = $modelo->buscar($id);

                if ($usuario) {
                    echo json_encode(['success' => true, 'data' => $usuario]);
                    exit;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
                    exit;
                }
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
                exit;
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
            exit;
        }
    }
} 
