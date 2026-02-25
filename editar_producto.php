<?php
// Verificar si se recibió un ID válido del producto a actualizar
if(isset($_GET['id']) && !empty($_GET['id'])) {
    $id_producto = $_GET['id'];
    
    // Incluir el archivo de conexión a la base de datos
    include('conexion/conectar-mysql.php');
    
    // Consultar los datos del producto
    $queryProducto = "SELECT * FROM producto WHERE Codigo_Producto = '$id_producto'";
    $resultadoProducto = mysqli_query($conexion, $queryProducto);
    
    // Verificar si se encontró el producto
    if(mysqli_num_rows($resultadoProducto) > 0) {
        // Obtener los datos del producto
        $producto = mysqli_fetch_assoc($resultadoProducto);
        
        // Asignar los datos a variables para cargar en el formulario
        $codigo = $producto['Codigo_Producto'];
        $nombre = $producto['Nombre_P'];
        $descripcion = $producto['Descripcion'];
        $precio_publico = $producto['Precio_p'];
        $precio_compra = $producto['precio_c'];
        $existencias = $producto['Existencias'];
        $stock_maximo = $producto['Stock_Maximo'];
        $stock_minimo = $producto['Stock_Minimo'];
        $id_presentacion = $producto['Id_Presentacion'];
        $id_categoria = $producto['Id_Categoria'];
        $id_marca = $producto['Id_Marca'];
    } else {
        // Si no se encontró el producto, redireccionar o manejar el error según tu lógica
        header("Location: existencias.php");
        exit();
    }
} else {
    // Si no se proporcionó un ID de producto válido, redireccionar o manejar el error según tu lógica
    header("Location: existencias.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Actualizar Producto</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="bg-primary text-white p-3 d-flex justify-content-between align-items-center rounded-bottom">
        <div class="header-left">
            <h1><a href="index.php" class="text-white text-decoration-none">Menu</a></h1>
        </div>
        <div class="header-right d-flex align-items-center">
            <h1 class="mr-3">Actualizar Producto</h1>
            <img src="src/logo.png" alt="Shopping Cart" class="cart-icon">
        </div>
    </header>
    <div class="container-fluid">
        <div class="row">
            <nav class="col-md-2 bg-light sidebar">
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
            <main role="main" class="col-md-9 ml-sm-auto col-lg-10 px-md-4">
                <div class="container mt-4">
                    <div class="row justify-content-center">
                        <div class="col-md-6">
                            <form action="actualizar_producto.php" method="POST">
                                <div class="form-group">
                                    <label for="codigo">Código del Producto</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" value="<?php echo $codigo; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nombre">Nombre del Producto
                                    </label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $nombre; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo $descripcion; ?></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="precio_p">Precio al Público</label>
                                    <input type="number" class="form-control" id="precio_p" name="precio_p" value="<?php echo $precio_publico; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="precio_c">Precio de Compra</label>
                                    <input type="number" class="form-control" id="precio_c" name="precio_c" value="<?php echo $precio_compra; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="existencias">Existencias</label>
                                    <input type="number" class="form-control" id="existencias" name="existencias" value="<?php echo $existencias; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="stock_maximo">Stock Máximo</label>
                                    <input type="number" class="form-control" id="stock_maximo" name="stock_maximo" value="<?php echo $stock_maximo; ?>" required>
                                </div>
                                <div class="form-group">
                                    <label for="stock_minimo">Stock Mínimo</label>
                                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" value="<?php echo $stock_minimo; ?>" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="id_presentacion">Presentación</label>
                                    <select class="form-control" id="id_presentacion" name="id_presentacion" required>
                                        <option value="">Selecciona una presentación</option>
                                        <?php
                                        // Consulta SQL para obtener las presentaciones
                                        $queryPresentaciones = "SELECT Id_Presentacion, Descripcion FROM presentacion";
                                        $resultadoPresentaciones = mysqli_query($conexion, $queryPresentaciones);
                                        while($filaPresentacion = mysqli_fetch_assoc($resultadoPresentaciones)) {
                                            $selected = ($filaPresentacion['Id_Presentacion'] == $id_presentacion) ? "selected" : "";
                                            echo "<option value='" . $filaPresentacion['Id_Presentacion'] . "' $selected>" . $filaPresentacion['Descripcion'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_categoria">Categoría</label>
                                    <select class="form-control" id="id_categoria" name="id_categoria" required>
                                        <option value="">Selecciona una categoría</option>
                                        <?php
                                        // Consulta SQL para obtener las categorías
                                        $queryCategorias = "SELECT Id_Categoria, Nombre_Cat FROM categoria";
                                        $resultadoCategorias = mysqli_query($conexion, $queryCategorias);
                                        while($filaCategoria = mysqli_fetch_assoc($resultadoCategorias)) {
                                            $selected = ($filaCategoria['Id_Categoria'] == $id_categoria) ? "selected" : "";
                                            echo "<option value='" . $filaCategoria['Id_Categoria'] . "' $selected>" . $filaCategoria['Nombre_Cat'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="id_marca">Marca</label>
                                    <select class="form-control" id="id_marca" name="id_marca" required>
                                        <option value="">Selecciona una marca</option>
                                        <?php
                                        // Consulta SQL para obtener las marcas
                                        $queryMarcas = "SELECT Id_Marca, Nombre_Marca FROM marca";
                                        $resultadoMarcas = mysqli_query($conexion, $queryMarcas);
                                        while($filaMarca = mysqli_fetch_assoc($resultadoMarcas)) {
                                            $selected = ($filaMarca['Id_Marca'] == $id_marca) ? "selected" : "";
                                            echo "<option value='" . $filaMarca['Id_Marca'] . "' $selected>" . $filaMarca['Nombre_Marca'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>

                                <!-- Resto del formulario -->

                                <button type="submit" class="btn btn-primary">Actualizar Producto</button>
                                
                            </form>
                            <a href="existencias.php"><button class="btn btn-danger">cancelar</button> </a>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
