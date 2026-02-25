<?php
// Incluir archivo de conexión
include('conexion/conectar-mysql.php');

// Obtener el término de búsqueda
$termino_busqueda = $_GET['termino_busqueda'];

// Consulta para buscar productos por nombre, código y descripción
$sql = "SELECT Codigo_Producto, Nombre_P, Precio_p FROM producto 
        WHERE Nombre_P LIKE '%$termino_busqueda%' 
        OR Codigo_Producto LIKE '%$termino_busqueda%' 
        OR Descripcion LIKE '%$termino_busqueda%'";

// Ejecutar la consulta
$resultado = mysqli_query($conexion, $sql);

// Crear un array para almacenar los resultados
$productos = array();

// Iterar sobre los resultados y agregarlos al array
while ($row = mysqli_fetch_assoc($resultado)) {
    $productos[] = $row;
}

// Devolver los resultados en formato JSON
echo json_encode($productos);

// Cerrar conexión
mysqli_close($conexion);
?>
