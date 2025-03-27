<?php
class ModeloDashboard {
    private $conexion;
    
    public function __construct() {
        $this->conexion = BD::crearInstancia();
    }

    // Obtener total de ventas del día
    public function obtenerVentasHoy() {
        date_default_timezone_set('America/Argentina/Buenos_Aires');
        $fecha = date('Y-m-d');
        $consulta = $this->conexion->prepare("
            SELECT COUNT(*) as total_ventas, 
                   COALESCE(SUM(total), 0) as total_monto
            FROM ventas 
            WHERE DATE(fecha_venta) = :fecha
        ");
        $consulta->execute(['fecha' => $fecha]);
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener ventas de los últimos 7 días
    public function obtenerVentasUltimaSemana() {
        $consulta = $this->conexion->query("
            SELECT DATE(fecha_venta) as fecha,
                   COUNT(*) as total_ventas,
                   SUM(total) as total_monto
            FROM ventas
            WHERE fecha_venta >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)
            GROUP BY DATE(fecha_venta)
            ORDER BY fecha_venta DESC
        ");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener productos más vendidos
    public function obtenerProductosMasVendidos($limite = 5) {
        $consulta = $this->conexion->prepare("
            SELECT p.nombre, 
                   SUM(dv.cantidad) as total_vendido,
                   SUM(dv.subtotal) as total_ingresos
            FROM detalles_venta dv
            JOIN productos p ON dv.producto_id = p.id
            GROUP BY p.id, p.nombre
            ORDER BY total_vendido DESC
            LIMIT :limite
        ");
        $consulta->bindValue(':limite', $limite, PDO::PARAM_INT);
        $consulta->execute();
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener ventas por categoría
    public function obtenerVentasPorCategoria() {
        $consulta = $this->conexion->query("
            SELECT c.nombre as categoria,
                   COUNT(DISTINCT v.id) as total_ventas,
                   SUM(dv.subtotal) as total_ingresos
            FROM categorias c
            JOIN productos p ON p.categoria_id = c.id
            JOIN detalles_venta dv ON dv.producto_id = p.id
            JOIN ventas v ON dv.venta_id = v.id
            GROUP BY c.id, c.nombre
            ORDER BY total_ingresos DESC
        ");
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener stock bajo
    public function obtenerProductosStockBajo($limite_stock = 2) {
        $consulta = $this->conexion->prepare("
            SELECT p.nombre, p.stock, c.nombre as categoria
            FROM productos p
            JOIN categorias c ON p.categoria_id = c.id
            WHERE p.stock <= :limite_stock
            ORDER BY p.stock ASC
        ");
        $consulta->execute(['limite_stock' => $limite_stock]);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }

    // Obtener estadísticas generales
    public function obtenerEstadisticasGenerales() {
        return [
            'productos' => $this->contarProductos(),
            'categorias' => $this->contarCategorias(),
            'ventas_mes' => $this->obtenerVentasMes(),
            'ingresos_mes' => $this->obtenerIngresosMes()
        ];
    }

    private function contarProductos() {
        $consulta = $this->conexion->query("SELECT COUNT(*) FROM productos");
        return $consulta->fetchColumn();
    }

    private function contarCategorias() {
        $consulta = $this->conexion->query("SELECT COUNT(*) FROM categorias");
        return $consulta->fetchColumn();
    }

    private function obtenerVentasMes() {
        $consulta = $this->conexion->query("
            SELECT COUNT(*) 
            FROM ventas 
            WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE())
            AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())
        ");
        return $consulta->fetchColumn();
    }

    private function obtenerIngresosMes() {
        $consulta = $this->conexion->query("
            SELECT COALESCE(SUM(total), 0)
            FROM ventas 
            WHERE MONTH(fecha_venta) = MONTH(CURRENT_DATE())
            AND YEAR(fecha_venta) = YEAR(CURRENT_DATE())
        ");
        return $consulta->fetchColumn();
    }

    // Obtener inversión total desde el registro financiero
    public function obtenerInversionTotal() {
        $consulta = $this->conexion->query("
            SELECT COALESCE(SUM(monto), 0) as inversion_total
            FROM registro_financiero
            WHERE tipo_movimiento = 'inversion'
        ");
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener ganancias totales desde el registro financiero
    public function obtenerGananciaTotal() {
        $consulta = $this->conexion->query("
            SELECT COALESCE(SUM(monto), 0) as ganancia_total
            FROM registro_financiero
            WHERE tipo_movimiento = 'ganancia'
        ");
        return $consulta->fetch(PDO::FETCH_ASSOC);
    }

    // Obtener resumen financiero por período
    public function obtenerResumenFinanciero($fecha_inicio = null, $fecha_fin = null) {
        $sql = "
            SELECT 
                tipo_movimiento,
                SUM(monto) as total,
                COUNT(*) as cantidad_movimientos
            FROM registro_financiero
            WHERE 1=1
        ";
        
        $params = array();
        if ($fecha_inicio && $fecha_fin) {
            $sql .= " AND fecha_registro BETWEEN :fecha_inicio AND :fecha_fin";
            $params['fecha_inicio'] = $fecha_inicio;
            $params['fecha_fin'] = $fecha_fin;
        }
        
        $sql .= " GROUP BY tipo_movimiento";
        
        $consulta = $this->conexion->prepare($sql);
        $consulta->execute($params);
        return $consulta->fetchAll(PDO::FETCH_ASSOC);
    }
}
