<?php
// Conectar a la base de datos
include('conexion/conectar-mysql.php');

// Obtener el folio de la compra desde la solicitud GET
$folioCompra = $_GET['folioCompra'];

// Consulta SQL para obtener los detalles de la compra
$queryDetalles = "SELECT dc.Cantidad, dc.Precio_Compra, p.Nombre_P
                  FROM detalle_compra dc
                  JOIN producto p ON dc.Codigo_producto = p.Codigo_Producto
                  WHERE dc.Folio_Compra = '$folioCompra'";

$resultDetalles = mysqli_query($conexion, $queryDetalles);

if (!$resultDetalles) {
    // Manejo de error en la consulta
    echo json_encode(["error" => "Error al realizar la consulta: " . mysqli_error($conexion)]);
    exit();
}

// Construir el array de resultados
$detalles = [];
while ($row = mysqli_fetch_assoc($resultDetalles)) {
    $detalles[] = $row;
}

// Devolver la respuesta en formato JSON
echo json_encode($detalles);

// Cerrar la conexión
mysqli_close($conexion);
?>
