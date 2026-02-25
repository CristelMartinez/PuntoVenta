<?php
session_start();

// Verificar si el usuario no está autenticado
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php'); // Redirigir a la página de inicio de sesión
    exit();
}

$esAdministrador = isset($_SESSION['rol']) && $_SESSION['rol'] == 'Administrador';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="bg-primary text-white p-3 d-flex justify-content-between align-items-center rounded-bottom">
        <div class="header-left">
            <h1><a href="index.php" class="text-white text-decoration-none">Menu</a></h1>
        </div>
        <div class="header-right d-flex align-items-center">
            <h1 class="mr-3">principal</h1> <a class="text-white text-decoration-none" href="logout.php"><h3>cerrar sesion</h3></a>
            <img src="src/logo.png" alt="Shopping Cart" class="cart-icon">
        </div>
    </header>
    <div class="container-fluid h-100">
        <div class="row h-100">
            <nav class="col-md-2 bg-light sidebar rounded-right">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link " href="realizar_venta.php">Vender</a>
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
                            <a class="nav-link" href="proveedor.php">proveedor</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="registrar_compra.php">Compra</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="devoluciones.php">Devolución</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="reportes.php">Reportes</a>
                        </li>
                    </ul>
                </div>
            </nav>
            <main class="col-md-10 d-flex justify-content-center align-items-center rounded-left">
                <img src="src/logo.png" alt="Shopping Cart" class="main-icon">
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
