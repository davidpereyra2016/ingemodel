<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $titulo; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 11px;
            line-height: 1.4;
            margin: 0;
            padding: 15px;
            color: #333;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 2px solid #006633;
            padding-bottom: 15px;
        }
        .header h1 {
            font-size: 20px;
            margin: 5px 0;
            color: #006633;
            font-weight: bold;
        }
        .header h2 {
            font-size: 16px;
            margin: 5px 0;
            color: #666;
            font-weight: normal;
        }
        .info-section {
            margin: 15px 0;
            padding: 10px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .info-section h3 {
            margin: 0 0 10px 0;
            font-size: 14px;
            color: #006633;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 15px 0;
            font-size: 10px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px 4px;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #006633;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .accion-creacion { color: #28a745; font-weight: bold; }
        .accion-baja { color: #ffc107; font-weight: bold; }
        .accion-eliminacion { color: #dc3545; font-weight: bold; }
        .accion-cambio { color: #007bff; font-weight: bold; }
        .accion-actualizacion { color: #17a2b8; font-weight: bold; }
        .badge {
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
        }
        .badge-admin { background-color: #dc3545; color: white; }
        .badge-encargado { background-color: #ffc107; color: #212529; }
        .badge-ingeniero { background-color: #17a2b8; color: white; }
        .badge-usuario { background-color: #6c757d; color: white; }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .no-data {
            text-align: center;
            padding: 30px;
            color: #666;
            font-style: italic;
        }
        .documento-oficial {
            position: relative;
        }
        .sello {
            position: absolute;
            top: 150px;
            right: 50px;
            opacity: 0.2;
            transform: rotate(-20deg);
            font-size: 18px;
            color: #006633;
            border: 2px solid #006633;
            border-radius: 5px;
            padding: 10px;
            font-weight: bold;
        }
        .disclaimer {
            margin-top: 20px;
            font-size: 10px;
            font-style: italic;
            text-align: center;
        }
        @media print {
            body { margin: 0; padding: 10px; }
            .header { page-break-after: avoid; }
            table { page-break-inside: auto; }
            tr { page-break-inside: avoid; page-break-after: auto; }
        }
    </style>
</head>
<body class="documento-oficial">
    <div class="sello">REPORTE OFICIAL</div>
    <div class="header">
        <!-- <img src="../../assets/img/logo-2.png" alt="Logo"> -->
        <h1>Colegio Público de Ingenieros de Formosa</h1>
        <h2><?php echo $titulo; ?></h2>
        <p><strong>Período:</strong> <?php echo date('d/m/Y', strtotime($fecha_desde)); ?> - <?php echo date('d/m/Y', strtotime($fecha_hasta)); ?></p>
        <p><strong>Generado:</strong> <?php echo date('d/m/Y H:i:s'); ?> | <strong>Por:</strong> <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?></p>
    </div>

    <div class="info-section">
        <h3>Resumen del Reporte</h3>
        <div class="info-row">
            <span class="info-label">Total de Registros:</span>
            <span><?php echo count($datos); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Período de Consulta:</span>
            <span><?php echo date('d/m/Y', strtotime($fecha_desde)); ?> al <?php echo date('d/m/Y', strtotime($fecha_hasta)); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Generación:</span>
            <span><?php echo date('d/m/Y H:i:s'); ?></span>
        </div>
    </div>

    <?php if (empty($datos)): ?>
        <div class="no-data">
            <p>No se encontraron registros de auditoría para el período consultado.</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 8%;">Reserva</th>
                    <th style="width: 20%;">Usuario</th>
                    <th style="width: 8%;">Rol</th>
                    <th style="width: 12%;">Acción</th>
                    <th style="width: 10%;">Estado Ant.</th>
                    <th style="width: 10%;">Estado Nuevo</th>
                    <th style="width: 15%;">Comentario</th>
                    <th style="width: 12%;">Fecha</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $registro): ?>
                    <tr>
                        <td><?php echo $registro['id']; ?></td>
                        <td>
                            <strong>#<?php echo $registro['id_reserva']; ?></strong>
                            <?php if ($registro['codigo_unico']): ?>
                                <br><small><?php echo $registro['codigo_unico']; ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong>
                                <?php 
                                $nombre = $registro['usuario_nombre'] ?: $registro['usuario_nombre_original'];
                                $apellido = $registro['usuario_apellido'] ?: $registro['usuario_apellido_original'];
                                echo $nombre . ' ' . $apellido; 
                                ?>
                            </strong>
                            <br><small>ID: <?php echo $registro['id_usuario']; ?></small>
                        </td>
                        <td>
                            <?php 
                            $rol = $registro['usuario_rol'] ?: $registro['usuario_rol_original'];
                            $badge_class = '';
                            switch($rol) {
                                case 'administrador':
                                    $badge_class = 'badge-admin';
                                    break;
                                case 'encargado':
                                    $badge_class = 'badge-encargado';
                                    break;
                                case 'ingeniero':
                                    $badge_class = 'badge-ingeniero';
                                    break;
                                default:
                                    $badge_class = 'badge-usuario';
                            }
                            ?>
                            <span class="badge <?php echo $badge_class; ?>"><?php echo ucfirst($rol); ?></span>
                        </td>
                        <td>
                            <?php
                            $accion_class = '';
                            switch($registro['accion']) {
                                case 'creación':
                                    $accion_class = 'accion-creacion';
                                    break;
                                case 'baja':
                                    $accion_class = 'accion-baja';
                                    break;
                                case 'eliminacion_completa':
                                    $accion_class = 'accion-eliminacion';
                                    break;
                                case 'cambio_estado':
                                    $accion_class = 'accion-cambio';
                                    break;
                                default:
                                    $accion_class = 'accion-actualizacion';
                            }
                            ?>
                            <span class="<?php echo $accion_class; ?>">
                                <?php echo ucfirst($registro['accion']); ?>
                            </span>
                        </td>
                        <td>
                            <?php echo $registro['estado_anterior'] ? ucfirst($registro['estado_anterior']) : '-'; ?>
                        </td>
                        <td>
                            <?php echo $registro['estado_nuevo'] ? ucfirst($registro['estado_nuevo']) : '-'; ?>
                        </td>
                        <td>
                            <small><?php echo $registro['comentario'] ?: '-'; ?></small>
                        </td>
                        <td>
                            <small>
                                <?php echo date('d/m/Y', strtotime($registro['fecha'])); ?><br>
                                <?php echo date('H:i:s', strtotime($registro['fecha'])); ?>
                            </small>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="disclaimer">
        <p>Este reporte tiene carácter oficial y sirve como constancia de las acciones registradas en el sistema de auditoría.</p>
    </div>

    <div class="footer">
        <p><strong>Colegio Público de Ingenieros de Formosa</strong> - Sistema de Gestión de Reservas</p>
        <p>Este reporte fue generado automáticamente el <?php echo date('d/m/Y H:i:s'); ?></p>
        <p>Página 1 de 1 | Total de registros: <?php echo count($datos); ?></p>
        <p>Para consultas o modificaciones comuníquese al teléfono: (3704) 043114 o por email a ingenierosformosa@gmail.com</p>
    </div>

    <script>
        // Auto-imprimir al cargar la página
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
