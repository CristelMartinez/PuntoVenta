<?php
// Incluir archivo de conexión a la base de datos
include('conexion/conectar-mysql.php');

// Verificar si el formulario ha sido enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Recibir los datos del formulario
    $codigo = mysqli_real_escape_string($conexion, $_POST['codigo']);
    $nombre = mysqli_real_escape_string($conexion, $_POST['nombre']);
    $descripcion = mysqli_real_escape_string($conexion, $_POST['descripcion']);
    $precio_p = mysqli_real_escape_string($conexion, $_POST['precio_p']);
    $existencias = mysqli_real_escape_string($conexion, $_POST['existencias']);
    $stock_maximo = mysqli_real_escape_string($conexion, $_POST['stock_maximo']);
    $stock_minimo = mysqli_real_escape_string($conexion, $_POST['stock_minimo']);
    $id_presentacion = mysqli_real_escape_string($conexion, $_POST['id_presentacion']);
    $id_categoria = mysqli_real_escape_string($conexion, $_POST['id_categoria']);
    $id_marca = mysqli_real_escape_string($conexion, $_POST['id_marca']);
    $precio_c = mysqli_real_escape_string($conexion, $_POST['precio_c']);
    $id_proveedor = mysqli_real_escape_string($conexion, $_POST['id_proveedor']);
    $estatus = '1'; // Suponiendo que estatus por defecto es '1'

    // Crear la consulta SQL para insertar los datos
    $sql = "INSERT INTO producto (Codigo_Producto, Nombre_P, Descripcion, Precio_p, Existencias, Stock_Maximo, Stock_Minimo, Id_Presentacion, Id_Categoria, Id_Marca, precio_c, id_proveedor, estatus)
            VALUES ('$codigo', '$nombre', '$descripcion', '$precio_p', '$existencias', '$stock_maximo', '$stock_minimo', '$id_presentacion', '$id_categoria', '$id_marca', '$precio_c', '$id_proveedor', '$estatus')";

    // Ejecutar la consulta y verificar si se insertó correctamente
    if (mysqli_query($conexion, $sql)) {
        echo "Producto agregado exitosamente.";
        // Redireccionar a la página de productos o a otra página si se desea
        header("Location: productos.php");
    } else {
        echo "Error: " . $sql . "<br>" . mysqli_error($conexion);
    }

    // Cerrar la conexión a la base de datos
    mysqli_close($conexion);
}

?>