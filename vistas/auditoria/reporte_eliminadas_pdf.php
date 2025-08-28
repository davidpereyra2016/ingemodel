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
            background-color: #dc3545;
            color: white;
            font-weight: bold;
            font-size: 10px;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .estado-aprobada { color: #28a745; font-weight: bold; }
        .estado-pendiente { color: #ffc107; font-weight: bold; }
        .estado-rechazada { color: #dc3545; font-weight: bold; }
        .estado-baja { color: #6c757d; font-weight: bold; }
        .monto {
            font-weight: bold;
            color: #006633;
        }
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
        .warning-box {
            background-color: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 4px;
            padding: 10px;
            margin: 15px 0;
            color: #856404;
        }
        .warning-box strong {
            color: #dc3545;
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
    <div class="sello">REPORTE CONFIDENCIAL</div>
    <div class="header">
        <!-- <img src="../../assets/img/logo-2.png" alt="Logo"> -->
        <h1>Colegio Público de Ingenieros de Formosa</h1>
        <h2><?php echo $titulo; ?></h2>
        <p><strong>Período:</strong> <?php echo date('d/m/Y', strtotime($fecha_desde)); ?> - <?php echo date('d/m/Y', strtotime($fecha_hasta)); ?></p>
        <p><strong>Generado:</strong> <?php echo date('d/m/Y H:i:s'); ?> | <strong>Por:</strong> <?php echo $_SESSION['nombre'] . ' ' . $_SESSION['apellido']; ?></p>
    </div>

    <div class="warning-box">
        <strong>⚠️ CONFIDENCIAL:</strong> Este reporte contiene información de reservas eliminadas permanentemente del sistema. 
        Mantenga la confidencialidad de estos datos según las políticas de la institución.
    </div>

    <div class="info-section">
        <h3>Resumen del Reporte</h3>
        <div class="info-row">
            <span class="info-label">Total de Reservas Eliminadas:</span>
            <span><?php echo count($datos); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Período de Consulta:</span>
            <span><?php echo date('d/m/Y', strtotime($fecha_desde)); ?> al <?php echo date('d/m/Y', strtotime($fecha_hasta)); ?></span>
        </div>
        <div class="info-row">
            <span class="info-label">Monto Total Afectado:</span>
            <span class="monto">
                $<?php 
                $total = 0;
                foreach ($datos as $reserva) {
                    $total += $reserva['monto'];
                }
                echo number_format($total, 2);
                ?>
            </span>
        </div>
        <div class="info-row">
            <span class="info-label">Fecha de Generación:</span>
            <span><?php echo date('d/m/Y H:i:s'); ?></span>
        </div>
    </div>

    <?php if (empty($datos)): ?>
        <div class="no-data">
            <p>No se encontraron reservas eliminadas para el período consultado.</p>
        </div>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th style="width: 5%;">ID</th>
                    <th style="width: 8%;">Reserva Orig.</th>
                    <th style="width: 18%;">Usuario</th>
                    <th style="width: 12%;">Evento</th>
                    <th style="width: 8%;">Estado</th>
                    <th style="width: 8%;">Monto</th>
                    <th style="width: 15%;">Eliminado Por</th>
                    <th style="width: 12%;">Fecha Eliminación</th>
                    <th style="width: 14%;">Motivo</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($datos as $reserva): ?>
                    <tr>
                        <td><?php echo $reserva['id']; ?></td>
                        <td>
                            <strong>#<?php echo $reserva['reserva_id_original']; ?></strong>
                            <?php if ($reserva['codigo_unico']): ?>
                                <br><small><?php echo $reserva['codigo_unico']; ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo $reserva['nombre_usuario'] . ' ' . $reserva['apellido_usuario']; ?></strong>
                            <br><small><?php echo $reserva['email_usuario']; ?></small>
                            <?php if ($reserva['matricula_usuario']): ?>
                                <br><small>Mat: <?php echo $reserva['matricula_usuario']; ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?php echo $reserva['tipo_uso']; ?></strong>
                            <br><small><?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?></small>
                            <?php if ($reserva['hora_inicio'] && $reserva['hora_fin']): ?>
                                <br><small><?php echo substr($reserva['hora_inicio'], 0, 5) . '-' . substr($reserva['hora_fin'], 0, 5); ?></small>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $estado_class = '';
                            switch($reserva['estado']) {
                                case 'aprobada':
                                    $estado_class = 'estado-aprobada';
                                    break;
                                case 'pendiente':
                                    $estado_class = 'estado-pendiente';
                                    break;
                                case 'rechazada':
                                    $estado_class = 'estado-rechazada';
                                    break;
                                case 'baja':
                                    $estado_class = 'estado-baja';
                                    break;
                            }
                            ?>
                            <span class="<?php echo $estado_class; ?>">
                                <?php echo ucfirst($reserva['estado']); ?>
                            </span>
                        </td>
                        <td>
                            <span class="monto">$<?php echo number_format($reserva['monto'], 2); ?></span>
                        </td>
                        <td>
                            <strong><?php echo $reserva['eliminado_por_nombre'] . ' ' . $reserva['eliminado_por_apellido']; ?></strong>
                            <br><small>Admin ID: <?php echo $reserva['eliminado_por_usuario_id']; ?></small>
                        </td>
                        <td>
                            <small>
                                <?php echo date('d/m/Y', strtotime($reserva['fecha_eliminacion'])); ?><br>
                                <?php echo date('H:i:s', strtotime($reserva['fecha_eliminacion'])); ?>
                            </small>
                        </td>
                        <td>
                            <small><?php echo $reserva['motivo_eliminacion'] ?: 'No especificado'; ?></small>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Resumen por administrador -->
        <?php
        $admin_stats = [];
        foreach ($datos as $reserva) {
            $admin_key = $reserva['eliminado_por_nombre'] . ' ' . $reserva['eliminado_por_apellido'];
            if (!isset($admin_stats[$admin_key])) {
                $admin_stats[$admin_key] = ['count' => 0, 'monto' => 0];
            }
            $admin_stats[$admin_key]['count']++;
            $admin_stats[$admin_key]['monto'] += $reserva['monto'];
        }
        ?>
        
        <div class="info-section">
            <h3>Resumen por Administrador</h3>
            <table style="width: 60%; margin: 10px 0;">
                <thead>
                    <tr>
                        <th>Administrador</th>
                        <th>Eliminaciones</th>
                        <th>Monto Total</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($admin_stats as $admin => $stats): ?>
                        <tr>
                            <td><strong><?php echo $admin; ?></strong></td>
                            <td><?php echo $stats['count']; ?></td>
                            <td class="monto">$<?php echo number_format($stats['monto'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <div class="disclaimer">
        <p>Este reporte tiene carácter oficial y confidencial. Sirve como constancia de las reservas eliminadas del sistema.</p>
    </div>

    <div class="footer">
        <p><strong>Colegio Público de Ingenieros de Formosa</strong> - Sistema de Gestión de Reservas</p>
        <p>Este reporte fue generado automáticamente el <?php echo date('d/m/Y H:i:s'); ?></p>
        <p>Página 1 de 1 | Total de registros: <?php echo count($datos); ?></p>
        <p>Para consultas o modificaciones comuníquese al teléfono: (3704) 043114 o por email a ingenierosformosa@gmail.com</p>
        <p><em>Documento confidencial - Mantenga la seguridad de la información</em></p>
    </div>

    <script>
        // Auto-imprimir al cargar la página
        window.onload = function() {
            window.print();
        };
    </script>
</body>
</html>
