<?php
include('conexion/conectar-mysql.php');

header('Content-Type: application/json');

$response = array();

try {
    // Verificar si se proporcionaron las fechas de inicio y fin
    if (!isset($_GET['fecha_inicio']) || !isset($_GET['fecha_fin'])) {
        throw new Exception("Rango de fechas no proporcionado.");
    }

    $fecha_inicio = $_GET['fecha_inicio'];
    $fecha_fin = $_GET['fecha_fin'];

    // Consultar todas las ventas en el rango de fechas especificado
    $sql = "SELECT 
    v.Folio_Venta, 
    v.Fecha, 
    SUM(dv.Total_Pagar) AS Total_Pagar, 
    v.estatus, 
    SUM(dv.Cantidad) AS Total_Productos
FROM venta v
INNER JOIN detalle_venta dv ON v.Folio_Venta = dv.Folio_Venta
WHERE v.Fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
GROUP BY v.Folio_Venta, v.Fecha, v.estatus";

    $result = mysqli_query($conexion, $sql);

    if (!$result) {
        throw new Exception("Error al obtener los datos de ventas: " . mysqli_error($conexion));
    }

    $ventas = array();
    $totalVentasActivas = 0;
    $totalVentasBajas = 0;
    $totalProductosActivos = 0;
    $totalProductosBajas = 0;

    // Verificar si se encontraron ventas
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            // Convertir Total_Pagar a número flotante
            $totalPagar = floatval($row['Total_Pagar']);
            // Convertir Total_Productos a entero
            $totalProductos = intval($row['Total_Productos']);
            
            $estatus = $row['estatus'] == '1' ? 'Activa' : 'Dada de Baja';
            $venta = array(
                "Folio_Venta" => $row['Folio_Venta'],
                "Fecha" => $row['Fecha'],
                "Total_Pagar" => $totalPagar,
                "Total_Productos" => $totalProductos,
                "estatus" => $estatus
            );

            // Calcular totales basados en el estatus
            if ($row['estatus'] == '1') {
                $totalVentasActivas += $totalPagar;
                $totalProductosActivos += $totalProductos;
            } else {
                $totalVentasBajas += $totalPagar;
                $totalProductosBajas += $totalProductos;
            }

            $ventas[] = $venta;
        }
    } else {
        throw new Exception("No se encontraron ventas en el rango de fechas especificado.");
    }

    // Preparar la respuesta
    $response["success"] = true;
    $response["ventas"] = $ventas;
    $response["total_ventas_activas"] = $totalVentasActivas;
    $response["total_ventas_bajas"] = $totalVentasBajas;
    $response["total_productos_activos"] = $totalProductosActivos;
    $response["total_productos_bajas"] = $totalProductosBajas;

} catch (Exception $e) {
    $response["success"] = false;
    $response["error"] = $e->getMessage();
} finally {
    // Enviar la respuesta JSON
    echo json_encode($response);

    // Cerrar la conexión
    mysqli_close($conexion);
}
?>
