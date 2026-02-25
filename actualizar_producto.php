<?php
// Verificar si se recibieron los datos del formulario mediante el método POST
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si se recibió un código de producto válido
    if(isset($_POST['codigo']) && !empty($_POST['codigo'])) {
        // Incluir el archivo de conexión a la base de datos
        include('conexion/conectar-mysql.php');
        
        // Recuperar los datos del formulario
        $codigo_producto = $_POST['codigo'];
        $nombre_producto = $_POST['nombre'];
        $descripcion_producto = $_POST['descripcion'];
        $precio_publico = $_POST['precio_p'];
        $precio_compra = $_POST['precio_c'];
        $existencias = $_POST['existencias'];
        $stock_maximo = $_POST['stock_maximo'];
        $stock_minimo = $_POST['stock_minimo'];
        $id_presentacion = $_POST['id_presentacion'];
        $id_categoria = $_POST['id_categoria'];
        $id_marca = $_POST['id_marca'];
        
        // Realizar la consulta para actualizar el producto en la base de datos
        $queryActualizarProducto = "UPDATE producto SET 
                                    Nombre_P = '$nombre_producto',
                                    Descripcion = '$descripcion_producto',
                                    Precio_p = $precio_publico,
                                    Precio_c = $precio_compra,
                                    Existencias = $existencias,
                                    Stock_Maximo = $stock_maximo,
                                    Stock_Minimo = $stock_minimo,
                                    Id_Presentacion = $id_presentacion,
                                    Id_Categoria = $id_categoria,
                                    Id_Marca = $id_marca
                                    WHERE Codigo_Producto = '$codigo_producto'";
        
        // Ejecutar la consulta de actualización
        if(mysqli_query($conexion, $queryActualizarProducto)) {
            // Redireccionar a la página de existencias después de actualizar el producto
            header("Location: existencias.php");
            exit();
        } else {
            // Manejar cualquier error en la consulta de actualización
            echo "Error al actualizar el producto: " . mysqli_error($conexion);
        }
    } else {
        // Redireccionar si no se proporcionó un código de producto válido
        header("Location: existencias.php");
        exit();
    }
} else {
    // Redireccionar si no se recibieron los datos del formulario mediante el método POST
    header("Location: existencias.php");
    exit();
}
?>
