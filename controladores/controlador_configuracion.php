<?php
include_once("modelos/modelo_configuracion.php");
include_once("conexion.php");

class ControladorConfiguracion
{
    private $modelo;
    private $conexion;

    public function __construct()
    {
        $this->modelo = new ModeloConfiguracion();
        $this->conexion = BD::crearInstancia();
    }

    // ----- Métodos para documentos -----

    // Listar documentos
    public function listarDocumentos()
    {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        $documentos = $this->modelo->obtenerDocumentos();
        include_once 'vistas/configuracion/documentos.php';
    }

    // Formulario para crear un nuevo documento
    public function crearDocumento()
    {
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
                    $nombre_archivo,
                    $_POST['tipo']
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
    public function editarDocumento()
    {
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
                $_POST['tipo'],
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
    public function eliminarDocumento()
    {
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
    public function listarAranceles()
    {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        $aranceles = $this->modelo->obtenerAranceles();
        include_once 'vistas/configuracion/aranceles.php';
    }

    // Formulario para crear un nuevo arancel
    public function crearArancel()
    {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        // Si se envió el formulario
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Validar datos
            if (
                empty($_POST['nombre']) || empty($_POST['descripcion']) ||
                empty($_POST['monto_antes_22']) || empty($_POST['monto_despues_22']) ||
                empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])
            ) {
                $_SESSION['error'] = "Todos los campos son obligatorios";
                include_once 'vistas/configuracion/arancel_form.php';
                return;
            }

            // Validar que los montos sean números válidos
            if (
                !is_numeric($_POST['monto_antes_22']) || $_POST['monto_antes_22'] <= 0 ||
                !is_numeric($_POST['monto_despues_22']) || $_POST['monto_despues_22'] <= 0
            ) {
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
                // Actualizar reservas afectadas por el nuevo arancel
                $contador_actualizadas = $this->actualizarReservasAfectadas();

                if ($contador_actualizadas > 0) {
                    $_SESSION['mensaje'] = "Arancel creado con éxito. Se actualizaron {$contador_actualizadas} reserva(s) afectada(s).";
                } else {
                    $_SESSION['mensaje'] = "Arancel creado con éxito.";
                }

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
    public function editarArancel()
    {
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
            if (
                empty($_POST['nombre']) || empty($_POST['descripcion']) ||
                empty($_POST['monto_antes_22']) || empty($_POST['monto_despues_22']) ||
                empty($_POST['fecha_inicio']) || empty($_POST['fecha_fin'])
            ) {
                $_SESSION['error'] = "Todos los campos son obligatorios";
                include_once 'vistas/configuracion/arancel_form.php';
                return;
            }

            // Validar que los montos sean números válidos
            if (
                !is_numeric($_POST['monto_antes_22']) || $_POST['monto_antes_22'] <= 0 ||
                !is_numeric($_POST['monto_despues_22']) || $_POST['monto_despues_22'] <= 0
            ) {
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
                // Actualizar reservas afectadas por el cambio de arancel
                $contador_actualizadas = $this->actualizarReservasAfectadas();

                if ($contador_actualizadas > 0) {
                    $_SESSION['mensaje'] = "Arancel actualizado con éxito. Se actualizaron {$contador_actualizadas} reserva(s) afectada(s).";
                } else {
                    $_SESSION['mensaje'] = "Arancel actualizado con éxito.";
                }

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
    public function eliminarArancel()
    {
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

    // =====================================================
    // NUEVO MÉTODO: ACTUALIZACIÓN AUTOMÁTICA DE RESERVAS
    // =====================================================

    /**
     * Actualiza automáticamente todas las reservas futuras afectadas por cambios en aranceles
     * @return int Cantidad de reservas actualizadas
     */
    private function actualizarReservasAfectadas()
    {
        // Incluir el modelo de reservas
        include_once("modelos/modelo_reservas.php");
        $modeloReservas = new ModeloReservas();

        // Incluir el modelo de notificaciones para alertar a usuarios
        include_once("modelos/modelo_notificaciones.php");
        $modeloNotificaciones = new ModeloNotificaciones();

        // Obtener todas las reservas futuras pendientes o aprobadas
        $reservas = $modeloReservas->obtenerReservasParaActualizarArancel();

        $contador_actualizadas = 0;
        $reservas_notificadas = [];

        foreach ($reservas as $reserva) {
            // Intentar actualizar cada reserva
            $resultado = $modeloReservas->actualizarMontosPorCambioArancel($reserva['id']);

            if ($resultado['actualizado']) {
                $contador_actualizadas++;

                // Crear notificación para el usuario
                $tipo_cambio = $resultado['tipo_cambio'];
                $diferencia_abs = abs($resultado['diferencia']);

                if ($resultado['diferencia'] > 0) {
                    // Aumento de monto
                    $mensaje = sprintf(
                        "El arancel de su reserva #%d (%s) ha sido actualizado. "
                            . "Nuevo monto total: $%s (aumento de $%s). "
                            . "Por favor, tenga en cuenta este cambio para completar su pago.",
                        $reserva['id'],
                        date('d/m/Y', strtotime($reserva['fecha_evento'])),
                        number_format($resultado['monto_nuevo'], 2),
                        number_format($diferencia_abs, 2)
                    );
                } else {
                    // Reducción de monto
                    $mensaje = sprintf(
                        "El arancel de su reserva #%d (%s) ha sido actualizado. "
                            . "Nuevo monto total: $%s (reducción de $%s). "
                            . "Este cambio se verá reflejado en su próximo pago.",
                        $reserva['id'],
                        date('d/m/Y', strtotime($reserva['fecha_evento'])),
                        number_format($resultado['monto_nuevo'], 2),
                        number_format($diferencia_abs, 2)
                    );
                }

                // Crear notificación con manejo de errores robusto
                try {
                    // Verificar que la reserva aún existe antes de crear notificación
                    $reserva_actual = $modeloReservas->obtenerReserva($reserva['id']);

                    if ($reserva_actual) {
                        // Orden correcto de parámetros: id_usuario, mensaje, id_reserva, tipo
                        ModeloNotificaciones::crearNotificacion(
                            $reserva['id_usuario'],
                            $mensaje,
                            $reserva['id'],
                            'actualizacion_arancel'
                        );
                    } else {
                        // La reserva fue eliminada entre la consulta y la notificación
                        error_log("Advertencia: No se pudo crear notificación para reserva #{$reserva['id']} - Reserva no encontrada");
                    }
                } catch (PDOException $e) {
                    // Error de constraint o base de datos - no detener el proceso
                    error_log("Error al crear notificación para reserva #{$reserva['id']}: " . $e->getMessage());
                    // Continuar con la siguiente reserva sin fallar
                } catch (Exception $e) {
                    error_log("Error general al notificar reserva #{$reserva['id']}: " . $e->getMessage());
                }

                $reservas_notificadas[] = [
                    'id' => $reserva['id'],
                    'usuario' => $reserva['nombre'] . ' ' . $reserva['apellido'],
                    'email' => $reserva['email'],
                    'diferencia' => $resultado['diferencia'],
                    'tipo' => $tipo_cambio
                ];
            }
        }

        // Log para el administrador (opcional - puede comentarse en producción)
        if ($contador_actualizadas > 0) {
            error_log(sprintf(
                "Sistema de Aranceles: Se actualizaron %d reserva(s) automáticamente. Usuario admin: %s",
                $contador_actualizadas,
                $_SESSION['nombre'] ?? 'Sistema'
            ));
        }

        return $contador_actualizadas;
    }

    /**
     * Método manual para actualizar reservas (puede ser llamado desde la vista de aranceles)
     * Permite al administrador actualizar manualmente las reservas afectadas
     */
    public function actualizarReservasMasivo()
    {
        // Verificar que el usuario tenga permisos de administrador
        if (!isset($_SESSION['rol']) || $_SESSION['rol'] != 'administrador') {
            header('Location: index.php?controlador=paginas&accion=inicio');
            exit;
        }

        $contador_actualizadas = $this->actualizarReservasAfectadas();

        if ($contador_actualizadas > 0) {
            $_SESSION['mensaje'] = "Se actualizaron {$contador_actualizadas} reserva(s) correctamente.";
        } else {
            $_SESSION['mensaje'] = "No hay reservas que requieran actualización en este momento.";
        }

        header('Location: index.php?controlador=configuracion&accion=listarAranceles');
        exit;
    }
}
