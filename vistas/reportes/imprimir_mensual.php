<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte Mensual de Reservas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            border-bottom: 1px solid #ccc;
            padding-bottom: 10px;
        }
        .header img {
            max-width: 100px;
            height: auto;
        }
        h1 {
            font-size: 18px;
            margin: 5px 0;
            color: #006633;
        }
        h2 {
            font-size: 16px;
            margin: 10px 0;
            color: #006633;
            padding-bottom: 5px;
            border-bottom: 1px solid #eee;
        }
        p {
            margin: 5px 0;
        }
        .info-box {
            margin: 20px 0;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        .info-box h3 {
            margin-top: 0;
            font-size: 14px;
            color: #333;
        }
        .info-item {
            display: inline-block;
            width: 24%;
            text-align: center;
            padding: 10px 0;
            border-right: 1px solid #eee;
        }
        .info-item:last-child {
            border-right: none;
        }
        .info-label {
            display: block;
            color: #666;
            font-size: 11px;
        }
        .info-value {
            display: block;
            font-size: 18px;
            font-weight: bold;
            color: #006633;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 10px;
        }
        .total-box {
            margin: 20px 0;
            padding: 10px;
            background-color: #f9f9f9;
            border-radius: 4px;
            text-align: right;
        }
        .estado {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 10px;
            font-size: 10px;
            color: white;
        }
        .estado-pendiente {
            background-color: #ffc107;
        }
        .estado-aprobada {
            background-color: #28a745;
        }
        .estado-rechazada {
            background-color: #dc3545;
        }
        .estado-cancelada {
            background-color: #6c757d;
        }
    </style>
</head>
<body>
    <?php 
    // Nombres de meses en español
    $nombresMeses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
        5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
        9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    ?>

    <div class="header">
        <!-- <img src="../../assets/img/logo-2.png" alt="Logo"> -->
        <h1>Colegio Público de Ingenieros de Formosa</h1>
        <p>Reporte Mensual de Reservas del Salón</p>
        <p><strong>Periodo:</strong> <?php echo $nombresMeses[intval($mes)]; ?> de <?php echo $anio; ?></p>
        <p><strong>Fecha del reporte:</strong> <?php echo date('d/m/Y H:i'); ?></p>
    </div>

    <h2>Resumen del Mes</h2>
    <div class="info-box">
        <div class="info-item">
            <span class="info-label">Total de Reservas</span>
            <span class="info-value"><?php echo $estadisticas['total_reservas']; ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Aprobadas</span>
            <span class="info-value"><?php echo $estadisticas['aprobadas']; ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Pendientes</span>
            <span class="info-value"><?php echo $estadisticas['pendientes']; ?></span>
        </div>
        <div class="info-item">
            <span class="info-label">Rechazadas</span>
            <span class="info-value"><?php echo $estadisticas['rechazadas']; ?></span>
        </div>
    </div>

    <div class="total-box">
        <p style="font-size: 14px;">Ingresos Totales del Mes: <strong style="font-size: 16px;">$<?php echo number_format($estadisticas['ingreso_total'], 2, ',', '.'); ?></strong></p>
    </div>

    <h2>Listado de Reservas</h2>
    <?php if (empty($reservas)): ?>
        <p>No hay reservas para el mes seleccionado.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ingeniero</th>
                    <th>Matrícula</th>
                    <th>Fecha</th>
                    <th>Horario</th>
                    <th>Tipo de Uso</th>
                    <th>Estado</th>
                    <th>Monto</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($reservas as $reserva): ?>
                    <tr>
                        <td><?php echo $reserva['id']; ?></td>
                        <td><?php echo $reserva['nombre'] . ' ' . $reserva['apellido']; ?></td>
                        <td><?php echo $reserva['matricula']; ?></td>
                        <td><?php echo date('d/m/Y', strtotime($reserva['fecha_evento'])); ?></td>
                        <td><?php echo substr($reserva['hora_inicio'], 0, 5) . ' - ' . substr($reserva['hora_fin'], 0, 5); ?></td>
                        <td><?php echo $reserva['tipo_uso']; ?></td>
                        <td>
                            <?php
                            $class = '';
                            switch ($reserva['estado']) {
                                case 'pendiente':
                                    $class = 'estado-pendiente';
                                    break;
                                case 'aprobada':
                                    $class = 'estado-aprobada';
                                    break;
                                case 'rechazada':
                                    $class = 'estado-rechazada';
                                    break;
                                case 'cancelada':
                                    $class = 'estado-cancelada';
                                    break;
                            }
                            ?>
                            <span class="estado <?php echo $class; ?>"><?php echo ucfirst($reserva['estado']); ?></span>
                        </td>
                        <td>$<?php echo number_format($reserva['monto'], 2, ',', '.'); ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <div class="footer">
        <p>Documento generado automáticamente por el sistema de gestión de reservas del Colegio Público de Ingenieros de Formosa</p>
        <p>Este reporte es para uso interno y administrativo.</p>
    </div>

    <script>
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>
