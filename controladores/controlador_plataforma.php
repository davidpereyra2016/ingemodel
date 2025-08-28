<?php
include_once("conexion.php");
include_once("modelos/modelo_plataforma.php");

class ControladorPlataforma
{
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
        
        $modelo = new ModeloPlataforma();
        $plataformas = $modelo->listar();
        include_once("vistas/plataforma/listar.php");
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
            $nombre = htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8');
            $descripcion = htmlspecialchars($_POST['descripcion'], ENT_QUOTES, 'UTF-8');
            $telefono = htmlspecialchars($_POST['telefono'], ENT_QUOTES, 'UTF-8');
            $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
            $direccion = htmlspecialchars($_POST['direccion'], ENT_QUOTES, 'UTF-8');
            $contacto_principal = htmlspecialchars($_POST['contacto_principal'], ENT_QUOTES, 'UTF-8');
            $sitio_web = htmlspecialchars($_POST['sitio_web'], ENT_QUOTES, 'UTF-8');
            $estado = isset($_POST['estado']) ? htmlspecialchars($_POST['estado'], ENT_QUOTES, 'UTF-8') : 'activo';

            $modelo = new ModeloPlataforma();

            if ($modelo->crear($nombre, $descripcion, $telefono, $correo, $direccion, $contacto_principal, $sitio_web, $estado)) {
                $_SESSION['mensaje'] = "Plataforma creada correctamente";
                header('Location: index.php?controlador=plataforma&accion=listar');
            } else {
                $_SESSION['error'] = "Error al crear plataforma";
                header('Location: index.php?controlador=plataforma&accion=listar');
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
            $nombre = htmlspecialchars($_POST['nombre'], ENT_QUOTES, 'UTF-8');
            $descripcion = htmlspecialchars($_POST['descripcion'], ENT_QUOTES, 'UTF-8');
            $telefono = htmlspecialchars($_POST['telefono'], ENT_QUOTES, 'UTF-8');
            $correo = filter_var($_POST['correo'], FILTER_SANITIZE_EMAIL);
            $direccion = htmlspecialchars($_POST['direccion'], ENT_QUOTES, 'UTF-8');
            $contacto_principal = htmlspecialchars($_POST['contacto_principal'], ENT_QUOTES, 'UTF-8');
            $sitio_web = htmlspecialchars($_POST['sitio_web'], ENT_QUOTES, 'UTF-8');
            $estado = isset($_POST['estado']) ? htmlspecialchars($_POST['estado'], ENT_QUOTES, 'UTF-8') : null;

            $modelo = new ModeloPlataforma();

            if ($modelo->actualizar($id, $nombre, $descripcion, $telefono, $correo, $direccion, $contacto_principal, $sitio_web, $estado)) {
                $_SESSION['mensaje'] = "Plataforma actualizada correctamente";
                header('Location: index.php?controlador=plataforma&accion=listar');
            } else {
                $_SESSION['error'] = "Error al actualizar plataforma";
                header('Location: index.php?controlador=plataforma&accion=listar');
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
            $modelo = new ModeloPlataforma();

            if ($modelo->eliminar($id)) {
                $_SESSION['mensaje'] = "Plataforma eliminada correctamente";
            } else {
                $_SESSION['error'] = "Error al eliminar plataforma";
            }
        }
        header('Location: index.php?controlador=plataforma&accion=listar');
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
                $modelo = new ModeloPlataforma();
                $plataforma = $modelo->buscar($id);

                if ($plataforma) {
                    echo json_encode(['success' => true, 'data' => $plataforma]);
                    exit;
                } else {
                    echo json_encode(['success' => false, 'message' => 'Plataforma no encontrada']);
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