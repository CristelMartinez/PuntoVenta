<?php
// Incluir el archivo de conexión a la base de datos
include('conexion/conectar-mysql.php');

// Verificar si se ha enviado un término de búsqueda
if(isset($_POST['termino_busqueda']) && !empty($_POST['termino_busqueda'])) {
    // Sanitizar el término de búsqueda para evitar inyección de SQL
    $terminoBusqueda = $conexion->real_escape_string($_POST['termino_busqueda']);

    // Consulta SQL para buscar el producto por código, nombre y descripción
    $consulta = "SELECT * FROM producto WHERE 
                 Codigo_Producto LIKE '%$terminoBusqueda%' OR 
                 Nombre_P LIKE '%$terminoBusqueda%' OR 
                 Descripcion LIKE '%$terminoBusqueda%'";

    // Ejecutar la consulta y manejar errores
    $resultado = $conexion->query($consulta);
    if ($resultado) {
        // Crear un array para almacenar todos los productos encontrados
        $productosEncontrados = array();
        while ($fila = $resultado->fetch_assoc()) {
            $productosEncontrados[] = $fila;
        }
        // Devolver los resultados en formato JSON
        echo json_encode($productosEncontrados);
    } else {
        // Si hay un error en la consulta, devolver un mensaje de error en formato JSON
        echo json_encode(array("error" => "Error al ejecutar la consulta: " . $conexion->error));
    }
} else {
    // Si no se ha enviado un término de búsqueda, devolver un mensaje de error en formato JSON
    echo json_encode(array("error" => "Término de búsqueda no proporcionado"));
}

// Cerrar la conexión a la base de datos
$conexion->close();
?>
