<?php
include('conexion/conectar-mysql.php');

// Verificar si se ha proporcionado un folio de compra para desactivar
if(isset($_GET['folio'])) {
    $folioCompra = $_GET['folio'];

    // Consulta para actualizar el estatus de la compra a 0 (desactivado)
    $queryDesactivar = "UPDATE compra SET estatus = '0' WHERE Folio_Compra = '$folioCompra'";
    mysqli_query($conexion, $queryDesactivar);
}

// Redireccionar a la página de ver compras después de desactivar
header("Location: ver_compras.php");
exit();

// Cerrar conexión
mysqli_close($conexion);
?>
