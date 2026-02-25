<?php
// Verificar si se recibió un ID válido del producto a eliminar
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id_producto = $_GET['id'];
    
    // Incluir el archivo de conexión a la base de datos
    include('conexion/conectar-mysql.php');
    
    // Realizar una consulta para actualizar el estatus del producto a 0 (eliminación lógica)
    $queryEliminarProducto = "UPDATE producto SET estatus = 0 WHERE Codigo_Producto = '$id_producto'";
    
    // Ejecutar la consulta de eliminación
    if(mysqli_query($conexion, $queryEliminarProducto)) {
        // Redireccionar a la página de existencias después de eliminar el producto
        header("Location: existencias.php");
        exit();
    } else {
        // Manejar cualquier error en la consulta de eliminación
        echo "Error al eliminar el producto: " . mysqli_error($conexion);
    }
} else {
    // Redireccionar si no se proporcionó un ID de producto válido
    header("Location: existencias.php");
    exit();
}
?>
