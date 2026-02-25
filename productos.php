<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agregar Producto Nuevo</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- SweetAlert2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
</head>
<body>
    <header class="bg-primary text-white p-3 d-flex justify-content-between align-items-center rounded-bottom">
        <div class="header-left">
            <h1><a href="index.php" class="text-white text-decoration-none">Menu</a></h1>
        </div>
        <div class="header-right d-flex align-items-center">
            <h1 class="mr-3">Agregar Producto Nuevo</h1>
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
                            <form id="productoForm" action="agregar_producto.php" method="POST">
                                <div class="form-group">
                                    <label for="codigo">Código del Producto</label>
                                    <input type="text" class="form-control" id="codigo" name="codigo" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="nombre">Nombre del Producto</label>
                                    <input type="text" class="form-control" id="nombre" name="nombre" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="descripcion">Descripción</label>
                                    <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
                                </div>
                                
                                <div class="form-group">
                                    <label for="precio_p">Precio al Público</label>
                                    <input type="number" class="form-control" id="precio_p" name="precio_p" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="precio_c">Precio de Compra</label>
                                    <input type="number" class="form-control" id="precio_c" name="precio_c" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="existencias">Existencias</label>
                                    <input type="number" class="form-control" id="existencias" name="existencias" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="stock_maximo">Stock Máximo</label>
                                    <input type="number" class="form-control" id="stock_maximo" name="stock_maximo" required>
                                </div>
                                
                                <div class="form-group">
                                    <label for="stock_minimo">Stock Mínimo</label>
                                    <input type="number" class="form-control" id="stock_minimo" name="stock_minimo" required>
                                </div>

                                <div class="form-group">
                                    <label for="id_presentacion">Presentación</label>
                                    <select class="form-control" id="id_presentacion" name="id_presentacion" required>
                                        <option value="">Selecciona una presentación</option>
                                        <?php
                                        // Conexión a la base de datos
                                        include('conexion/conectar-mysql.php');

                                        // Consulta SQL para obtener las presentaciones
                                        $queryPresentaciones = "SELECT Id_Presentacion, Descripcion FROM presentacion WHERE estatus = 1";

                                        
                                        // Ejecutar la consulta
                                        $resultadoPresentaciones = mysqli_query($conexion, $queryPresentaciones);

                                        // Mostrar opciones en el combobox
                                        while($filaPresentacion = mysqli_fetch_assoc($resultadoPresentaciones)) {
                                            echo "<option value='" . $filaPresentacion['Id_Presentacion'] . "'>" . $filaPresentacion['Descripcion'] . "</option>";
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
                                        $queryCategorias = "SELECT Id_Categoria, Nombre_Cat FROM categoria WHERE estatus = 1";

                                        // Ejecutar la consulta
                                        $resultadoCategorias = mysqli_query($conexion, $queryCategorias);

                                        // Mostrar opciones en el combobox
                                        while($filaCategoria = mysqli_fetch_assoc($resultadoCategorias)) {
                                            echo "<option value='" . $filaCategoria['Id_Categoria'] . "'>" . $filaCategoria['Nombre_Cat'] . "</option>";
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
                                        $queryMarcas = "SELECT Id_Marca, Nombre_Marca FROM marca WHERE estatus = 1";

                                        // Ejecutar la consulta
                                        $resultadoMarcas = mysqli_query($conexion, $queryMarcas);

                                        // Mostrar opciones en el combobox
                                        while($filaMarca = mysqli_fetch_assoc($resultadoMarcas)) {
                                            echo "<option value='" . $filaMarca['Id_Marca'] . "'>" . $filaMarca['Nombre_Marca'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-group">
    <label for="id_proveedor">Proveedor</label>
    <select class="form-control" id="id_proveedor" name="id_proveedor" required>
        <option value="">Selecciona un proveedor</option>
        <?php
        // Consulta SQL para obtener los proveedores activos
        $queryProveedores = "SELECT id_proveedor, nombre FROM proveedor WHERE estatus = 1";

        // Ejecutar la consulta
        $resultadoProveedores = mysqli_query($conexion, $queryProveedores);

        // Mostrar opciones en el combobox
        while ($filaProveedor = mysqli_fetch_assoc($resultadoProveedores)) {
            echo "<option value='" . $filaProveedor['id_proveedor'] . "'>" . $filaProveedor['nombre'] . "</option>";
        }
        ?>
    </select>
</div>


                                <button type="submit" class="btn btn-primary">Agregar Producto</button>
                            </form>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.all.min.js"></script>
    
    <script>
        // Función para validar el formulario
        function validarFormulario(event) {
            event.preventDefault(); // Evitar el envío del formulario por defecto

            // Obtener los valores de los campos
            let precioP = parseFloat(document.getElementById('precio_p').value);
            let precioC = parseFloat(document.getElementById('precio_c').value);
            let existencias = parseInt(document.getElementById('existencias').value);
            let stockMaximo = parseInt(document.getElementById('stock_maximo').value);
            let stockMinimo = parseInt(document.getElementById('stock_minimo').value);

            // Validar que los campos numéricos no sean negativos
            if (precioP <= 0 || precioC <= 0 || existencias <= 0 || stockMaximo <= 0 || stockMinimo <= 0) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Los valores numéricos no pueden ser negativos o cero .',
                    confirmButtonText: 'OK'
                });
                return false;
            }

            // Validar que el stock máximo sea mayor que el stock mínimo
            if (stockMaximo <= stockMinimo) {
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El stock máximo debe ser mayor que el stock mínimo.',
                    confirmButtonText: 'OK'
                });
                return false;
            }
            if(precioC < precioP){
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'El precio de compra no puede ser menor al precio en el que vendes el producto.',
                    confirmButtonText: 'OK'
                });
                return false;

            }

            // Si todas las validaciones pasan, enviar el formulario
            document.getElementById('productoForm').submit();
        }

        // Asociar la función de validación al evento submit del formulario
        document.getElementById('productoForm').addEventListener('submit', validarFormulario);
    </script>
</body>
</html>
