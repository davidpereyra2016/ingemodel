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

        if (isset($_SESSION['usuario_id'])) {
            // Verificar el rol del usuario
            if ($_SESSION['usuario_rol'] === 'usuario') {
                // Usuarios con rol 'usuario' solo pueden acceder a ventas
                header('Location: index.php?controlador=ventas&accion=listar');
            } else {
                // Otros roles (admin, etc) pueden acceder al inicio
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
                    $_SESSION['usuario_id'] = $usuario['id'];
                    $_SESSION['usuario_nombre'] = $usuario['nombre'];
                    $_SESSION['usuario_rol'] = $usuario['rol'];

                    echo "Login exitoso. Redirigiendo...";

                    // Redirigir según el rol
                    if ($usuario['rol'] === 'usuario') {
                        header('Location: index.php?controlador=ventas&accion=listar');
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
        $modelo = new ModeloUsuarios();
        $usuarios = $modelo->listar();
        include_once("vistas/usuarios/listarUsuario.php");
    }

    public function crear()
    {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            $nombre = htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8');
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = $_POST['password'];
            $rol = htmlspecialchars($_POST['rol'], ENT_QUOTES, 'UTF-8');

            $modelo = new ModeloUsuarios();

            if ($modelo->crear($nombre, $email, $password, $rol)) {
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

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $nombre = htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8');
            $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
            $password = !empty($_POST['password']) ? $_POST['password'] : null;
            $rol = htmlspecialchars($_POST['rol'], ENT_QUOTES, 'UTF-8');

            $modelo = new ModeloUsuarios();

            if ($modelo->actualizar($id, $nombre, $email, $password, $rol)) {
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
        if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id'])) {
            $id = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
            $modelo = new ModeloUsuarios();
            $usuario = $modelo->buscar($id);

            if ($usuario) {
                // Cargar los datos en el modal
                $_SESSION['temp_usuario'] = $usuario;
                header('Location: index.php?controlador=usuarios&accion=listar');
            } else {
                $_SESSION['error'] = "Usuario no encontrado";
                header('Location: index.php?controlador=usuarios&accion=listar');
            }
            exit;
        }
        header('Location: index.php?controlador=usuarios&accion=listar');
        exit;
    }
} 
