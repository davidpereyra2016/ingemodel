<?php
include_once("modelos/modelo_configuracion.php");
include_once("conexion.php");

class ControladorConfiguracion {
    private $modelo;
    private $conexion;
    
    public function __construct() {
        $this->modelo = new ModeloConfiguracion();
        $this->conexion = BD::crearInstancia();
    }

    // ----- Métodos para documentos -----

    // Listar documentos
    public function listarDocumentos() {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        $documentos = $this->modelo->obtenerDocumentos();
        include_once 'vistas/configuracion/documentos.php';
    }

    // Formulario para crear un nuevo documento
    public function crearDocumento() {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        // Si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar datos
            if (empty($_POST['nombre']) || empty($_POST['descripcion'])) {
                $_SESSION['error'] = "Todos los campos son obligatorios";
                include_once 'vistas/configuracion/documento_form.php';
                return;
            }

            // Verificar si se ha subido un archivo
            if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] == UPLOAD_ERR_NO_FILE) {
                $_SESSION['error'] = "Debe seleccionar un archivo";
                include_once 'vistas/configuracion/documento_form.php';
                return;
            }

            // Procesar el archivo
            $archivo = $_FILES['archivo'];
            $nombre_archivo = time() . '_' . basename($archivo['name']);
            $directorio_destino = 'assets/docs/';
            
            // Crear el directorio si no existe
            if (!file_exists($directorio_destino)) {
                mkdir($directorio_destino, 0777, true);
            }
            
            $ruta_destino = $directorio_destino . $nombre_archivo;
            
            // Mover el archivo a la carpeta docs
            if (move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                // Guardar en la base de datos
                if ($this->modelo->crearDocumento(
                    $_POST['nombre'],
                    $_POST['descripcion'],
                    $nombre_archivo
                )) {
                    $_SESSION['mensaje'] = "Documento creado con éxito";
                    header('Location: index.php?controlador=configuracion&accion=listarDocumentos');
                    exit;
                } else {
                    // Si hay error en la base de datos, eliminar el archivo subido
                    unlink($ruta_destino);
                    $_SESSION['error'] = "Error al guardar el documento en la base de datos";
                }
            } else {
                $_SESSION['error'] = "Error al subir el archivo";
            }
        }

        // Mostrar el formulario
        include_once 'vistas/configuracion/documento_form.php';
    }

    // Formulario para editar un documento existente
    public function editarDocumento() {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        // Verificar que se proporcionó un ID
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = "ID de documento no proporcionado";
            header('Location: index.php?controlador=configuracion&accion=listarDocumentos');
            exit;
        }

        $id = $_GET['id'];
        $documento = $this->modelo->obtenerDocumentoPorId($id);

        if (!$documento) {
            $_SESSION['error'] = "Documento no encontrado";
            header('Location: index.php?controlador=configuracion&accion=listarDocumentos');
            exit;
        }

        // Si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar datos
            if (empty($_POST['nombre']) || empty($_POST['descripcion'])) {
                $_SESSION['error'] = "Todos los campos son obligatorios";
                include_once 'vistas/configuracion/documento_form.php';
                return;
            }

            $nombre_archivo = null;
            
            // Verificar si se ha subido un nuevo archivo
            if (isset($_FILES['archivo']) && $_FILES['archivo']['error'] != UPLOAD_ERR_NO_FILE) {
                // Procesar el nuevo archivo
                $archivo = $_FILES['archivo'];
                $nombre_archivo = time() . '_' . basename($archivo['name']);
                $directorio_destino = 'assets/docs/';
                $ruta_destino = $directorio_destino . $nombre_archivo;
                
                // Mover el archivo a la carpeta docs
                if (!move_uploaded_file($archivo['tmp_name'], $ruta_destino)) {
                    $_SESSION['error'] = "Error al subir el nuevo archivo";
                    include_once 'vistas/configuracion/documento_form.php';
                    return;
                }
                
                // Eliminar el archivo antiguo si existe
                if ($documento['archivo'] && file_exists($directorio_destino . $documento['archivo'])) {
                    unlink($directorio_destino . $documento['archivo']);
                }
            }

            // Actualizar en la base de datos
            if ($this->modelo->actualizarDocumento(
                $id,
                $_POST['nombre'],
                $_POST['descripcion'],
                $nombre_archivo
            )) {
                $_SESSION['mensaje'] = "Documento actualizado con éxito";
                header('Location: index.php?controlador=configuracion&accion=listarDocumentos');
                exit;
            } else {
                // Si hay error y se subió un nuevo archivo, eliminarlo
                if ($nombre_archivo && file_exists($directorio_destino . $nombre_archivo)) {
                    unlink($directorio_destino . $nombre_archivo);
                }
                $_SESSION['error'] = "Error al actualizar el documento";
            }
        }

        // Mostrar el formulario de edición
        include_once 'vistas/configuracion/documento_form.php';
    }

    // Eliminar un documento
    public function eliminarDocumento() {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        // Verificar que se proporcionó un ID
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = "ID de documento no proporcionado";
            header('Location: index.php?controlador=configuracion&accion=listarDocumentos');
            exit;
        }

        $id = $_GET['id'];
        $documento = $this->modelo->obtenerDocumentoPorId($id);

        if (!$documento) {
            $_SESSION['error'] = "Documento no encontrado";
            header('Location: index.php?controlador=configuracion&accion=listarDocumentos');
            exit;
        }

        // Eliminar el archivo físico si existe
        $ruta_archivo = 'assets/docs/' . $documento['archivo'];
        if (file_exists($ruta_archivo)) {
            unlink($ruta_archivo);
        }

        // Eliminar de la base de datos
        if ($this->modelo->eliminarDocumento($id)) {
            $_SESSION['mensaje'] = "Documento eliminado con éxito";
        } else {
            $_SESSION['error'] = "Error al eliminar el documento";
        }

        header('Location: index.php?controlador=configuracion&accion=listarDocumentos');
        exit;
    }

    // ----- Métodos para aranceles -----

    // Listar aranceles
    public function listarAranceles() {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        $aranceles = $this->modelo->obtenerAranceles();
        include_once 'vistas/configuracion/aranceles.php';
    }

    // Formulario para crear un nuevo arancel
    public function crearArancel() {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        // Si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar datos
            if (empty($_POST['nombre']) || empty($_POST['descripcion']) || 
                empty($_POST['monto_antes_22']) || empty($_POST['monto_despues_22']) || 
                empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) {
                $_SESSION['error'] = "Todos los campos son obligatorios";
                include_once 'vistas/configuracion/arancel_form.php';
                return;
            }

            // Validar que los montos sean números válidos
            if (!is_numeric($_POST['monto_antes_22']) || $_POST['monto_antes_22'] <= 0 || 
                !is_numeric($_POST['monto_despues_22']) || $_POST['monto_despues_22'] <= 0) {
                $_SESSION['error'] = "Los montos deben ser números mayores a cero";
                include_once 'vistas/configuracion/arancel_form.php';
                return;
            }

            // Validar fechas
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];
            if (strtotime($fecha_inicio) > strtotime($fecha_fin)) {
                $_SESSION['error'] = "La fecha de inicio no puede ser posterior a la fecha fin";
                include_once 'vistas/configuracion/arancel_form.php';
                return;
            }
            
            // Determinar estado activo
            $activo = isset($_POST['activo']) ? $_POST['activo'] : 1;

            // Guardar en la base de datos
            if ($this->modelo->crearArancel(
                $_POST['nombre'],
                $_POST['descripcion'],
                $_POST['monto_antes_22'],
                $_POST['monto_despues_22'],
                $fecha_inicio,
                $fecha_fin,
                $activo
            )) {
                $_SESSION['mensaje'] = "Arancel creado con éxito";
                header('Location: index.php?controlador=configuracion&accion=listarAranceles');
                exit;
            } else {
                $_SESSION['error'] = "Error al guardar el arancel en la base de datos";
            }
        }

        // Mostrar el formulario
        include_once 'vistas/configuracion/arancel_form.php';
    }

    // Formulario para editar un arancel existente
    public function editarArancel() {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        // Verificar que se proporcionó un ID
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = "ID de arancel no proporcionado";
            header('Location: index.php?controlador=configuracion&accion=listarAranceles');
            exit;
        }

        $id = $_GET['id'];
        $arancel = $this->modelo->obtenerArancelPorId($id);

        if (!$arancel) {
            $_SESSION['error'] = "Arancel no encontrado";
            header('Location: index.php?controlador=configuracion&accion=listarAranceles');
            exit;
        }

        // Si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar datos
            if (empty($_POST['nombre']) || empty($_POST['descripcion']) || 
                empty($_POST['monto_antes_22']) || empty($_POST['monto_despues_22']) || 
                empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])) {
                $_SESSION['error'] = "Todos los campos son obligatorios";
                include_once 'vistas/configuracion/arancel_form.php';
                return;
            }

            // Validar que los montos sean números válidos
            if (!is_numeric($_POST['monto_antes_22']) || $_POST['monto_antes_22'] <= 0 || 
                !is_numeric($_POST['monto_despues_22']) || $_POST['monto_despues_22'] <= 0) {
                $_SESSION['error'] = "Los montos deben ser números mayores a cero";
                include_once 'vistas/configuracion/arancel_form.php';
                return;
            }

            // Validar fechas
            $fecha_inicio = $_POST['fecha_inicio'];
            $fecha_fin = $_POST['fecha_fin'];
            if (strtotime($fecha_inicio) > strtotime($fecha_fin)) {
                $_SESSION['error'] = "La fecha de inicio no puede ser posterior a la fecha fin";
                include_once 'vistas/configuracion/arancel_form.php';
                return;
            }
            
            // Determinar estado activo
            $activo = isset($_POST['activo']) ? $_POST['activo'] : 1;

            // Actualizar en la base de datos
            if ($this->modelo->actualizarArancel(
                $id,
                $_POST['nombre'],
                $_POST['descripcion'],
                $_POST['monto_antes_22'],
                $_POST['monto_despues_22'],
                $fecha_inicio,
                $fecha_fin,
                $activo
            )) {
                $_SESSION['mensaje'] = "Arancel actualizado con éxito";
                header('Location: index.php?controlador=configuracion&accion=listarAranceles');
                exit;
            } else {
                $_SESSION['error'] = "Error al actualizar el arancel";
            }
        }

        // Mostrar el formulario de edición
        include_once 'vistas/configuracion/arancel_form.php';
    }

    // Eliminar un arancel
    public function eliminarArancel() {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        // Verificar que se proporcionó un ID
        if (!isset($_GET['id'])) {
            $_SESSION['error'] = "ID de arancel no proporcionado";
            header('Location: index.php?controlador=configuracion&accion=listarAranceles');
            exit;
        }

        $id = $_GET['id'];
        
        // Eliminar de la base de datos
        if ($this->modelo->eliminarArancel($id)) {
            $_SESSION['mensaje'] = "Arancel eliminado con éxito";
        } else {
            $_SESSION['error'] = "Error al eliminar el arancel";
        }

        header('Location: index.php?controlador=configuracion&accion=listarAranceles');
        exit;
    }
}
?>
