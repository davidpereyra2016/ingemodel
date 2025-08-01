<?php
include_once("conexion.php");
include_once("modelos/modelo_observaciones.php");

class ControladorObservaciones {
    
    private $modelo;
    
    public function __construct() {
        $this->modelo = new ModeloObservaciones();
    }
    
    public function listar() {
        // Verificar si el usuario está logueado
        if (!isset($_SESSION['id_usuario'])) {
            header('Location: index.php?controlador=usuarios&accion=login');
            exit();
        }
        
        // Obtener observaciones según el rol del usuario
        if ($_SESSION['rol'] === 'ingeniero') {
            // Los ingenieros solo ven sus propias observaciones
            $observaciones = $this->modelo->obtenerObservacionesPorUsuario($_SESSION['id_usuario']);
        } else {
            // Administradores y encargados ven todas las observaciones
            $observaciones = $this->modelo->obtenerObservaciones();
        }
        
        // Obtener estadísticas para administradores
        $estadisticas = null;
        if ($_SESSION['rol'] !== 'ingeniero') {
            $estadisticas = $this->modelo->contarObservacionesPorEstado();
        }
        
        include_once("vistas/observaciones/listar.php");
    }
    
    public function crear() {
        // Solo administradores pueden crear observaciones
        if ($_SESSION['rol'] === 'ingeniero') {
            $_SESSION['error'] = "No tienes permisos para crear observaciones.";
            header('Location: index.php?controlador=observaciones&accion=listar');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validación anti-reenvío: verificar token de sesión
            if (!isset($_POST['form_token']) || !isset($_SESSION['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
                $_SESSION['error'] = "Solicitud inválida. Por favor, recarga la página e inténtalo de nuevo.";
                header('Location: index.php?controlador=observaciones&accion=crear');
                exit();
            }
            
            // Limpiar el token para evitar reutilización
            unset($_SESSION['form_token']);
            
            $id_reserva = $_POST['id_reserva'];
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $tipo_observacion = $_POST['tipo_observacion'];
            $id_usuario_admin = $_SESSION['id_usuario'];
            
            // Manejo de archivo adjunto
            $archivo_adjunto = null;
            if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] == 0) {
                $target_dir = "assets/uploads/observaciones/";
                
                // Crear directorio si no existe
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $archivo_temp = $_FILES['archivo_adjunto']['tmp_name'];
                $nombre_archivo = time() . '_' . $_FILES['archivo_adjunto']['name'];
                $ruta_destino = $target_dir . $nombre_archivo;
                
                if (move_uploaded_file($archivo_temp, $ruta_destino)) {
                    $archivo_adjunto = $nombre_archivo;
                }
            }
            
            $id_observacion = $this->modelo->crearObservacion($id_reserva, $id_usuario_admin, $titulo, $descripcion, $tipo_observacion, $archivo_adjunto);
            
            if ($id_observacion) {
                $_SESSION['mensaje'] = "Observación creada correctamente.";
                header('Location: index.php?controlador=observaciones&accion=listar');
                exit();
            } else {
                $_SESSION['error'] = "Error al crear la observación.";
            }
        } else {
            // Generar token único para el formulario
            $_SESSION['form_token'] = bin2hex(random_bytes(32));
        }
        
        // Obtener reservas disponibles para observación
        $reservas = $this->modelo->obtenerReservasParaObservacion();
        
        include_once("vistas/observaciones/crear.php");
    }
    
    public function ver() {
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = "ID de observación no especificado.";
            header('Location: index.php?controlador=observaciones&accion=listar');
            exit();
        }
        
        $id_observacion = $_GET['id'];
        $observacion = $this->modelo->obtenerObservacion($id_observacion);
        
        if (!$observacion) {
            $_SESSION['error'] = "Observación no encontrada.";
            header('Location: index.php?controlador=observaciones&accion=listar');
            exit();
        }
        
        // Verificar permisos: ingenieros solo pueden ver sus propias observaciones
        if ($_SESSION['rol'] === 'ingeniero') {
            // Obtener la reserva para verificar si pertenece al usuario
            include_once("modelos/modelo_reservas.php");
            $modeloReservas = new ModeloReservas();
            $reserva = $modeloReservas->obtenerReserva($observacion['id_reserva']);
            
            if ($reserva['id_usuario'] != $_SESSION['id_usuario']) {
                $_SESSION['error'] = "No tienes permisos para ver esta observación.";
                header('Location: index.php?controlador=observaciones&accion=listar');
                exit();
            }
        }
        
        include_once("vistas/observaciones/ver.php");
    }
    
    public function editar() {
        // Solo administradores pueden editar observaciones
        if ($_SESSION['rol'] === 'ingeniero') {
            $_SESSION['error'] = "No tienes permisos para editar observaciones.";
            header('Location: index.php?controlador=observaciones&accion=listar');
            exit();
        }
        
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = "ID de observación no especificado.";
            header('Location: index.php?controlador=observaciones&accion=listar');
            exit();
        }
        
        $id_observacion = $_GET['id'];
        $observacion = $this->modelo->obtenerObservacion($id_observacion);
        
        if (!$observacion) {
            $_SESSION['error'] = "Observación no encontrada.";
            header('Location: index.php?controlador=observaciones&accion=listar');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validación anti-reenvío: verificar token de sesión
            if (!isset($_POST['form_token']) || !isset($_SESSION['form_token']) || $_POST['form_token'] !== $_SESSION['form_token']) {
                $_SESSION['error'] = "Solicitud inválida. Por favor, recarga la página e inténtalo de nuevo.";
                header('Location: index.php?controlador=observaciones&accion=editar&id=' . $id_observacion);
                exit();
            }
            
            // Limpiar el token para evitar reutilización
            unset($_SESSION['form_token']);
            
            $id_reserva = $_POST['id_reserva'];
            $titulo = $_POST['titulo'];
            $descripcion = $_POST['descripcion'];
            $tipo_observacion = $_POST['tipo_observacion'];
            
            // Manejo de archivo adjunto
            $archivo_adjunto = null;
            $archivo_anterior = $observacion['archivo_adjunto'];
            
            if (isset($_FILES['archivo_adjunto']) && $_FILES['archivo_adjunto']['error'] == 0) {
                $target_dir = "assets/uploads/observaciones/";
                
                // Crear directorio si no existe
                if (!file_exists($target_dir)) {
                    mkdir($target_dir, 0777, true);
                }
                
                $archivo_temp = $_FILES['archivo_adjunto']['tmp_name'];
                $nombre_archivo = time() . '_' . $_FILES['archivo_adjunto']['name'];
                $ruta_destino = $target_dir . $nombre_archivo;
                
                if (move_uploaded_file($archivo_temp, $ruta_destino)) {
                    $archivo_adjunto = $nombre_archivo;
                    
                    // Eliminar archivo anterior si existía
                    if ($archivo_anterior) {
                        $ruta_anterior = $target_dir . $archivo_anterior;
                        if (file_exists($ruta_anterior)) {
                            unlink($ruta_anterior);
                        }
                    }
                }
            }
            
            if ($this->modelo->actualizarObservacion($id_observacion, $id_reserva, $titulo, $descripcion, $tipo_observacion, $archivo_adjunto)) {
                $_SESSION['mensaje'] = "Observación actualizada correctamente.";
                header('Location: index.php?controlador=observaciones&accion=ver&id=' . $id_observacion);
                exit();
            } else {
                $_SESSION['error'] = "Error al actualizar la observación.";
            }
        } else {
            // Generar token único para el formulario
            $_SESSION['form_token'] = bin2hex(random_bytes(32));
        }
        
        // Obtener reservas disponibles para observación
        $reservas = $this->modelo->obtenerReservasParaObservacion();
        
        include_once("vistas/observaciones/editar.php");
    }
    
    public function actualizarEstado() {
        // Solo administradores pueden actualizar el estado
        if ($_SESSION['rol'] === 'ingeniero') {
            $_SESSION['error'] = "No tienes permisos para actualizar observaciones.";
            header('Location: index.php?controlador=observaciones&accion=listar');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_observacion'])) {
            $id_observacion = $_POST['id_observacion'];
            $estado = $_POST['estado'];
            $comentario_resolucion = isset($_POST['comentario_resolucion']) ? $_POST['comentario_resolucion'] : null;
            
            if ($this->modelo->actualizarEstadoObservacion($id_observacion, $estado, $comentario_resolucion)) {
                $_SESSION['mensaje'] = "Estado de la observación actualizado correctamente.";
            } else {
                $_SESSION['error'] = "Error al actualizar el estado de la observación.";
            }
            
            header('Location: index.php?controlador=observaciones&accion=ver&id=' . $id_observacion);
            exit();
        }
    }
    
    public function eliminar() {
        // Solo administradores pueden eliminar observaciones
        if ($_SESSION['rol'] === 'ingeniero') {
            $_SESSION['error'] = "No tienes permisos para eliminar observaciones.";
            header('Location: index.php?controlador=observaciones&accion=listar');
            exit();
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_observacion'])) {
            $id_observacion = $_POST['id_observacion'];
            
            // Obtener información de la observación antes de eliminarla
            $observacion = $this->modelo->obtenerObservacion($id_observacion);
            
            if ($observacion) {
                // Eliminar archivo adjunto si existe
                if ($observacion['archivo_adjunto']) {
                    $ruta_archivo = "assets/uploads/observaciones/" . $observacion['archivo_adjunto'];
                    if (file_exists($ruta_archivo)) {
                        unlink($ruta_archivo);
                    }
                }
                
                if ($this->modelo->eliminarObservacion($id_observacion)) {
                    $_SESSION['mensaje'] = "Observación eliminada correctamente.";
                } else {
                    $_SESSION['error'] = "Error al eliminar la observación.";
                }
            } else {
                $_SESSION['error'] = "Observación no encontrada.";
            }
        }
        
        header('Location: index.php?controlador=observaciones&accion=listar');
        exit();
    }
    
    public function buscarReserva() {
        // Solo para administradores y encargados
        if (!in_array($_SESSION['rol'], ['administrador', 'encargado'])) {
            echo json_encode(['error' => 'No autorizado']);
            exit();
        }
        
        if (isset($_GET['busqueda'])) {
            $busqueda = $_GET['busqueda'];
            $reserva = null;
            
            // Primero intentar buscar por ID (si es numérico)
            if (is_numeric($busqueda)) {
                $reserva = $this->modelo->buscarReservaPorId($busqueda);
            }
            
            // Si no se encontró por ID, buscar por código único
            if (!$reserva) {
                $reserva = $this->modelo->buscarReservaPorCodigo($busqueda);
            }
            
            if ($reserva) {
                echo json_encode([
                    'success' => true,
                    'reserva' => $reserva
                ]);
            } else {
                echo json_encode([
                    'success' => false,
                    'message' => 'Reserva no encontrada. Puede buscar por ID de reserva o código único.'
                ]);
            }
        } else {
            echo json_encode(['error' => 'Parámetro de búsqueda no proporcionado']);
        }
        
        exit();
    }
    
    public function descargarArchivo() {
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = "ID de observación no especificado.";
            header('Location: index.php?controlador=observaciones&accion=listar');
            exit();
        }
        
        $id_observacion = $_GET['id'];
        $observacion = $this->modelo->obtenerObservacion($id_observacion);
        
        if (!$observacion || !$observacion['archivo_adjunto']) {
            $_SESSION['error'] = "Archivo no encontrado.";
            header('Location: index.php?controlador=observaciones&accion=listar');
            exit();
        }
        
        // Verificar permisos
        if ($_SESSION['rol'] === 'ingeniero') {
            include_once("modelos/modelo_reservas.php");
            $modeloReservas = new ModeloReservas();
            $reserva = $modeloReservas->obtenerReserva($observacion['id_reserva']);
            
            if ($reserva['id_usuario'] != $_SESSION['id_usuario']) {
                $_SESSION['error'] = "No tienes permisos para descargar este archivo.";
                header('Location: index.php?controlador=observaciones&accion=listar');
                exit();
            }
        }
        
        $ruta_archivo = "assets/uploads/observaciones/" . $observacion['archivo_adjunto'];
        
        if (file_exists($ruta_archivo)) {
            // Limpiar cualquier salida previa
            if (ob_get_level()) {
                ob_end_clean();
            }
            
            // Obtener información del archivo
            $file_info = pathinfo($ruta_archivo);
            $extension = strtolower($file_info['extension']);
            
            // Determinar el tipo MIME correcto
            $mime_types = [
                'pdf' => 'application/pdf',
                'jpg' => 'image/jpeg',
                'jpeg' => 'image/jpeg',
                'png' => 'image/png',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            
            $content_type = isset($mime_types[$extension]) ? $mime_types[$extension] : 'application/octet-stream';
            
            // Headers para descarga
            header('Content-Description: File Transfer');
            header('Content-Type: ' . $content_type);
            header('Content-Disposition: attachment; filename="' . basename($observacion['archivo_adjunto']) . '"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($ruta_archivo));
            
            // Leer y enviar el archivo
            readfile($ruta_archivo);
            exit();
        } else {
            $_SESSION['error'] = "El archivo no existe en el servidor.";
            header('Location: index.php?controlador=observaciones&accion=listar');
            exit();
        }
    }
}

?>