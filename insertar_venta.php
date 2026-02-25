<?php
include('conexion/conectar-mysql.php');

// Iniciar el búfer de salida
ob_start();

$response = array("success" => false);

try {
    // Verificar si se recibieron los datos de la venta y los detalles de la venta
    if (!isset($_POST['total_venta']) || !isset($_POST['detalles_venta'])) {
        throw new Exception("Datos de venta incompletos.");
    }

    // Obtener el total de la venta
    $total_venta = $_POST['total_venta'];
    $fecha = date("Y-m-d"); // Obtener la fecha actual
    $id_usuario = 1; // Aquí debes proporcionar el ID del usuario que realiza la venta (puedes obtenerlo de tu sistema de autenticación)
    $estatus = '1'; // Estatus por defecto

    // Iniciar una transacción
    mysqli_begin_transaction($conexion);

    // Verificar el stock de los productos
    foreach ($_POST['detalles_venta'] as $detalle) {
        $cantidad = $detalle['Cantidad'];
        $codigo_producto = $detalle['Codigo_Producto'];

        // Consultar el stock del producto
        $sql_stock = "SELECT Existencias FROM producto WHERE Codigo_Producto = '$codigo_producto'";
        $result = mysqli_query($conexion, $sql_stock);

        if (!$result) {
            throw new Exception("Error al consultar stock: " . mysqli_error($conexion));
        }

        $row = mysqli_fetch_assoc($result);
        if (!$row) {
            throw new Exception("Producto no encontrado: " . $codigo_producto);
        }

        // Verificar si hay suficiente stock
        $existencias = $row['Existencias'];
        if ($existencias < $cantidad) {
            throw new Exception("Stock insuficiente para el producto: " . $codigo_producto);
        }
    }

    // Insertar la venta en la tabla 'venta'
    $sql_venta = "INSERT INTO venta (Fecha, Id_Usuario, Total_Pagar, estatus) VALUES (NOW(), '$id_usuario', '$total_venta', '$estatus')";
    if (!mysqli_query($conexion, $sql_venta)) {
        throw new Exception("Error al insertar venta: " . mysqli_error($conexion));
    }

    // Obtener el ID de la venta insertada
    $folio_venta = mysqli_insert_id($conexion);

    // Insertar los detalles de la venta en la tabla 'detalle_venta' y actualizar el stock
    foreach ($_POST['detalles_venta'] as $detalle) {
        $cantidad = $detalle['Cantidad'];
        $total_pagar = $detalle['Total_Pagar'];
        $codigo_producto = $detalle['Codigo_Producto'];

        // Insertar detalle de la venta
        $sql_detalle = "INSERT INTO detalle_venta (Cantidad, Total_Pagar, Folio_Venta, Codigo_Producto) VALUES ('$cantidad', '$total_pagar', '$folio_venta', '$codigo_producto')";
        if (!mysqli_query($conexion, $sql_detalle)) {
            throw new Exception("Error al insertar detalles de venta: " . mysqli_error($conexion));
        }

        // Actualizar el stock del producto restando la cantidad vendida
        $sql_update_stock = "UPDATE producto SET Existencias = Existencias - '$cantidad' WHERE Codigo_Producto = '$codigo_producto'";
        if (!mysqli_query($conexion, $sql_update_stock)) {
            throw new Exception("Error al actualizar el stock del producto: " . mysqli_error($conexion));
        }
    }

    // Commit de la transacción
    mysqli_commit($conexion);

    // Respuesta JSON de éxito
    $response["success"] = true;
} catch (Exception $e) {
    // Rollback de la transacción en caso de error
    mysqli_rollback($conexion);

    // Añadir mensaje de error al response
    $response["error"] = $e->getMessage();

    // Verificar si el error fue debido a stock insuficiente y añadir un mensaje específico
    if (strpos($e->getMessage(), 'Stock insuficiente') !== false) {
        $response["stock_error"] = "No se puede realizar la venta debido a stock insuficiente para uno o más productos.";
    }
} finally {
    // Limpiar el búfer de salida y enviar la respuesta JSON
    ob_clean();
    echo json_encode($response);
    ob_end_flush();

    // Cerrar la conexión
    mysqli_close($conexion);
}
?>
