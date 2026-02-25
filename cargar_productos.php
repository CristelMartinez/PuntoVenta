<?php
// Conexión a la base de datos (debes completar con tus datos de conexión)
include('conexion/conectar-mysql.php');
// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Obtener el ID del proveedor seleccionado desde la solicitud AJAX
$proveedorId = $_POST['proveedor_id'];

// Consulta para obtener los productos del proveedor seleccionado
$query_productos = "SELECT Codigo_Producto, Nombre_P FROM producto WHERE id_proveedor = '$proveedorId'";
$result_productos = $conexion->query($query_productos);

$productos = array();
if ($result_productos->num_rows > 0) {
    while ($row = $result_productos->fetch_assoc()) {
        $productos[] = $row;
    }
}

// Devolver los productos como respuesta JSON
echo json_encode($productos);

// Cerrar conexión
$conexion->close();
?>
