<?php
include('conexion/conectar-mysql.php');

// Verificar si se ha proporcionado un folio de compra
if(isset($_GET['folio'])) {
    $folioCompra = $_GET['folio'];

    // Consulta para obtener la información de la compra
    $queryCompra = "SELECT * FROM compra WHERE Folio_Compra = '$folioCompra'";
    $resultCompra = mysqli_query($conexion, $queryCompra);

    // Verificar si la compra existe
    if(mysqli_num_rows($resultCompra) == 1) {
        $row = mysqli_fetch_assoc($resultCompra);
        $total = $row['Total_Pagar'];
        $fecha = $row['Fecha'];
    } else {
        // Si la compra no existe, redireccionar a la página de ver compras
        header("Location: ver_compras.php");
        exit();
    }
} else {
    // Si no se proporcionó un folio de compra, redireccionar a la página de ver compras
    header("Location: ver_compras.php");
    exit();
}

// Procesar el formulario de actualización
if(isset($_POST['actualizar'])) {
    $totalNuevo = $_POST['total'];
    $fechaNueva = $_POST['fecha'];

    // Actualizar la compra en la base de datos
    $queryActualizar = "UPDATE compra SET Total_Pagar = '$totalNuevo', Fecha = '$fechaNueva' WHERE Folio_Compra = '$folioCompra'";
    mysqli_query($conexion, $queryActualizar);

    // Redireccionar a la página de ver compras después de actualizar
    header("Location: ver_compras.php");
    exit();
}

// Cerrar conexión
mysqli_close($conexion);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Compra</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="bg-primary text-white p-3 d-flex justify-content-between align-items-center rounded-bottom">
        <div class="header-left">
            <h1><a href="index.php" class="text-white text-decoration-none">Menu</a></h1>
        </div>
        <div class="header-right d-flex align-items-center">
            <h1 class="mr-3">principal</h1>
            <img src="src/logo.png" alt="Shopping Cart" class="cart-icon">
        </div>
    </header>
    <div class="container-fluid h-100">
        <div class="row h-100">
            <nav class="col-md-2 bg-light sidebar rounded-right">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link " href="#">Vender</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="existencias.php">Existencias</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="productos.php">Producto Nuevo</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="categorias.php">Categorías</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="presentaciones.php">Presentaciones</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="marcas.php">Marca</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" href="registrar_compra.php">Compra</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Devolución</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">Reportes</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="col-md-10 d-flex justify-content-center align-items-center rounded-left">
                <div class="container">
                    <h2>Actualizar Compra</h2>
                    <form action="" method="post">
                        <div class="form-group">
                            <label for="total">Total a Pagar</label>
                            <input type="number" class="form-control" id="total" name="total" value="<?php echo $total; ?>" required min="0" step="0.01">
                        </div>
                        <div class="form-group">
                            <label for="fecha">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $fecha; ?>" required>
                        </div>
                        <button type="submit" name="actualizar" class="btn btn-primary">Actualizar Compra</button>
                    </form>
                </div>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
