<?php
// Conexión a la base de datos y cualquier configuración necesaria
include('conexion/conectar-mysql.php');

// Obtener el término de búsqueda desde la solicitud AJAX
$termino = $_GET['q'];

// Consulta SQL para buscar productos por código o nombre
$query = "SELECT Codigo_Producto, Nombre_P FROM producto WHERE Codigo_Producto LIKE '%$termino%' OR Nombre_P LIKE '%$termino%'";
$resultado = mysqli_query($conexion, $query);

if ($resultado) {
    // Si se encontraron resultados, devolver opciones para el usuario
    while ($row = mysqli_fetch_assoc($resultado)) {
        echo "<option value='" . $row['Codigo_Producto'] . "'>" . $row['Codigo_Producto'] . " - " . $row['Nombre_P'] . "</option>";
    }
} else {
    // Si no se encontraron resultados, devolver un mensaje de error
    echo "<option value=''>No se encontraron resultados</option>";
}

// Cerrar la conexión
mysqli_close($conexion);
?>
