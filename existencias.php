<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        .table-noborder {
            border: none;
        }
    </style>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script> <!-- Incluye SweetAlert -->
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
            <main class="col-md-10">
                <div class="container mt-4">
                    <h2>Existencias de Productos</h2>
                    <form method="GET" action="existencias.php" class="mb-3">
                        <div class="input-group">
                            <input type="text" name="buscar" class="form-control" placeholder="Buscar producto" value="<?php echo isset($_GET['buscar']) ? $_GET['buscar'] : ''; ?>">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="submit">Buscar</button>
                            </div>
                        </div>
                    </form>
                    <table class="table table-noborder">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Precio al Público</th>
                                <th>Existencias</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            include('conexion/conectar-mysql.php');
                            $queryProductos = "SELECT * FROM producto WHERE estatus = 1";
                            if (isset($_GET['buscar'])) {
                                $buscar = mysqli_real_escape_string($conexion, $_GET['buscar']);
                                $queryProductos .= " AND (Codigo_Producto LIKE '%$buscar%' OR Nombre_P LIKE '%$buscar%' OR Descripcion LIKE '%$buscar%')";
                            }
                            $resultadoProductos = mysqli_query($conexion, $queryProductos);
                            $productosEncontrados = mysqli_num_rows($resultadoProductos);

                            if ($productosEncontrados == 0) {
                                // Si no se encuentran resultados exactos, buscar resultados similares
                                $queryProductosSimilares = "SELECT * FROM producto WHERE estatus = 1 AND (Codigo_Producto LIKE '%$buscar%' OR Nombre_P LIKE '%$buscar%' OR Descripcion LIKE '%$buscar%')";
                                $resultadoProductosSimilares = mysqli_query($conexion, $queryProductosSimilares);
                                $productosSimilaresEncontrados = mysqli_num_rows($resultadoProductosSimilares);

                                if ($productosSimilaresEncontrados > 0) {
                                    echo "<script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            Swal.fire({
                                                icon: 'warning',
                                                title: 'No encontrado',
                                                text: 'No se ha encontrado ningún producto que coincida exactamente, mostrando resultados similares.'
                                            });
                                        });
                                    </script>";
                                } else {
                                    echo "<script>
                                        document.addEventListener('DOMContentLoaded', function() {
                                            Swal.fire({
                                                icon: 'error',
                                                title: 'No encontrado',
                                                text: 'No se ha encontrado ningún producto.'
                                            });
                                        });
                                    </script>";
                                }
                            }

                            while ($producto = mysqli_fetch_assoc($resultadoProductos)):
                            ?>
                            <tr>
                                <td><?php echo $producto['Codigo_Producto']; ?></td>
                                <td><?php echo $producto['Nombre_P']; ?></td>
                                <td><?php echo $producto['Descripcion']; ?></td>
                                <td>$<?php echo $producto['Precio_p']; ?></td>
                                <td><?php echo $producto['Existencias']; ?></td>
                                <td>
                                    <?php if(isset($producto['Codigo_Producto'])): ?>
                                        <a href="editar_producto.php?id=<?php echo $producto['Codigo_Producto']; ?>" class="btn btn-primary btn-sm">Editar</a>
                                        <a href="eliminar_producto.php?id=<?php echo $producto['Codigo_Producto']; ?>" class="btn btn-danger btn-sm">Eliminar</a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
