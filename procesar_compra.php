<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Formulario de Compra</title>
    <!-- Incluye SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>
    <!-- Contenido de tu formulario y otros elementos -->
</body>
</html>
<?php
include('conexion/conectar-mysql.php');

// Verificar conexión
if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

// Función para generar folio único de compra
function generar_folio() {
    return uniqid('COMP-'); // Generar un folio único con prefijo 'COMP-'
}

// Obtener datos del formulario
$folio_compra = generar_folio(); // Generar folio único
$id_proveedor = $_POST['id_proveedor'];
$fecha = $_POST['fecha'];
$cantidad = $_POST['cantidad'];
$productos = $_POST['producto'];
$precios_compra = $_POST['precio_compra'];

// Validar cantidades
$errores_cantidades = false;
foreach ($cantidad as $cant) {
    if ($cant <= 0) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error en detalle de compra',
                    text: 'La cantidad debe ser mayor a cero para todos los productos',
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'registrar_compra.php';
                    }
                });
              </script>";
        $errores_cantidades = true;
        break;
    }
}

if ($errores_cantidades) {
    $conexion->close();
    exit;
}

// Validar si la compra excede el stock máximo actual
$total_compra = 0;
$excede_stock = false;
for ($i = 0; $i < count($productos); $i++) {
    $cantidad_producto = $cantidad[$i];
    $producto = $productos[$i];

    // Consultar el stock máximo y existencias actuales del producto
    $query_stock_info = "SELECT Stock_Maximo, Existencias FROM producto WHERE Codigo_Producto = '$producto' AND id_proveedor = '$id_proveedor'";
    $result_stock_info = $conexion->query($query_stock_info);

    if ($result_stock_info && $result_stock_info->num_rows > 0) {
        $row = $result_stock_info->fetch_assoc();
        $stock_maximo = $row['Stock_Maximo'];
        $existencias = $row['Existencias'];

        // Calcular la cantidad que se intentará comprar
        $cantidad_total = $cantidad_producto + $existencias;

        // Verificar si la cantidad total excede el stock máximo
        if ($cantidad_total > $stock_maximo) {
            $excede_stock = true;
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error en detalle de compra',
                        text: 'La compra excede el stock máximo para el producto $producto ($stock_maximo unidades disponibles)',
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'registrar_compra.php';
                        }
                    });
                  </script>";
            break; // Detener el ciclo si se excede el stock máximo
        }

        // Sumar la cantidad de este producto a la compra total
        $total_compra += $cantidad_producto;
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error en detalle de compra',
                    text: 'No se encontró información del producto $producto para el proveedor seleccionado',
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'registrar_compra.php';
                    }
                });
              </script>";
        $excede_stock = true;
        break; // Detener el ciclo si hay un error o no se encuentra el producto
    }
}

// Si la compra excede el stock máximo, no continuar con el proceso de compra
if ($excede_stock) {
    $conexion->close();
    exit;
}

// Insertar datos en la tabla compra
$insert_compra = "INSERT INTO compra (Folio_Compra, Total_Pagar, Fecha, estatus, id_proveedor)
                  VALUES ('$folio_compra', 0, '$fecha', '1', '$id_proveedor')";
if ($conexion->query($insert_compra) === TRUE) {
    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Compra registrada correctamente',
                showConfirmButton: false,
                timer: 1500
            }).then((result) => {
                window.location.href = 'registrar_compra.php';
            });
          </script>";
} else {
    echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Error al registrar la compra',
                text: '" . $conexion->error . "',
                showConfirmButton: true
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'registrar_compra.php';
                }
            });
          </script>";
}

// Obtener el último ID de compra insertado
$id_compra = $conexion->insert_id;

// Insertar detalles de compra en la tabla detalle_compra
$total_pagar = 0;
$errores_detalle = false;
for ($i = 0; $i < count($productos); $i++) {
    $cantidad_producto = $cantidad[$i];
    $producto = $productos[$i];
    $precio_compra = $precios_compra[$i];

    // Consultar el precio y existencias del producto según el proveedor seleccionado
    $query_info_producto = "SELECT Precio_p, Existencias, Stock_Maximo FROM producto WHERE Codigo_Producto = '$producto' AND id_proveedor = '$id_proveedor'";
    $result_info_producto = $conexion->query($query_info_producto);

    if ($result_info_producto && $result_info_producto->num_rows > 0) {
        $row = $result_info_producto->fetch_assoc();
        $precio_producto = $row['Precio_p'];
        $existencias = $row['Existencias'];
        $stock_maximo = $row['Stock_Maximo'];

        // Insertar detalle de compra
        $insert_detalle = "INSERT INTO detalle_compra (Cantidad, Precio_Compra, Folio_Compra, Codigo_producto)
                           VALUES ('$cantidad_producto', '$precio_compra', '$folio_compra', '$producto')";
        if ($conexion->query($insert_detalle) === TRUE) {
            echo "<script>
                    Swal.fire({
                        icon: 'success',
                        title: 'Detalle de compra registrado correctamente',
                        showConfirmButton: false,
                        timer: 1500
                    });
                  </script>";
        } else {
            echo "<script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error al registrar el detalle de compra',
                        text: '" . $conexion->error . "',
                        showConfirmButton: true
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'registrar_compra.php';
                        }
                    });
                  </script>";
            $errores_detalle = true;
        }

        // Actualizar existencias del producto
        $nuevas_existencias = $existencias + $cantidad_producto;
        $update_existencias = "UPDATE producto SET Existencias = '$nuevas_existencias' WHERE Codigo_Producto = '$producto'";
        $conexion->query($update_existencias);

        // Calcular el total a pagar
        $total_pagar += $cantidad_producto * $precio_compra;
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error en detalle de compra',
                    text: 'No se encontró información del producto $producto para el proveedor seleccionado',
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'registrar_compra.php';
                    }
                });
              </script>";
        $errores_detalle = true;
    }
}

// Si no hubo errores en el detalle, actualizar el total a pagar en la compra
if (!$errores_detalle) {
    $update_total = "UPDATE compra SET Total_Pagar = '$total_pagar' WHERE Folio_Compra = '$folio_compra'";
    if ($conexion->query($update_total) === TRUE) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Compra exitosa',
                    showConfirmButton: false,
                    timer: 1500
                }).then((result) => {
                    window.location.href = 'registrar_compra.php';
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Error al actualizar el total a pagar',
                    text: '" . $conexion->error . "',
                    showConfirmButton: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = 'registrar_compra.php';
                    }
                });
              </script>";
    }
}

// Cerrar conexión
$conexion->close();
?>
