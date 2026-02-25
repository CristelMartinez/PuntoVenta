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
    <title>Compra</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <style>
        body {
            padding-top: 20px;
        }
        .container {
            max-width: 800px;
        }
        h2 {
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            margin-bottom: 20px;
        }
        table th {
            background-color: #f2f2f2;
        }
        .btn-delete {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 8px 12px;
            cursor: pointer;
        }
        .btn-delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <header class="bg-primary text-white p-3 d-flex justify-content-between align-items-center rounded-bottom">
        <div class="header-left">
            <h1><a href="index.php" class="text-white text-decoration-none">Menu</a></h1>
        </div>
        <div class="header-right d-flex align-items-center">
            <h1 class="mr-3">Principal</h1>
            <a class="text-white text-decoration-none" href="logout.php"><h3>Cerrar Sesión</h3></a>
            <img src="src/logo.png" alt="Shopping Cart" class="cart-icon">
        </div>
    </header>

    <div class="container-fluid h-100">
        <div class="row h-100">
            <nav class="col-md-2 bg-light sidebar rounded-right">
                <div class="sidebar-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link" href="realizar_venta.php">Vender</a>
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
                <div class="container">
                    <h2 class="mt-4">Compra</h2>
                    <form id="form_compra" action="procesar_compra.php" method="POST">
                        <!-- Campo oculto para el folio de compra generado automáticamente -->
                        <input type="hidden" name="folio_compra" value="">

                        <!-- Campo para seleccionar el proveedor -->
                        <div class="form-group">
                            <label for="proveedor">Proveedor:</label>
                            <select name="id_proveedor" id="proveedor" class="form-control" value="">
                                <?php
                                    // Conexión a la base de datos (debes completar con tus datos de conexión)
                                    $conexion = new mysqli("localhost", "root", "", "projecto");

                                    // Verificar conexión
                                    if ($conexion->connect_error) {
                                        die("Conexión fallida: " . $conexion->connect_error);
                                    }

                                    // Consulta para obtener los proveedores
                                    $query_proveedores = "SELECT id_proveedor, nombre FROM proveedor";
                                    $result_proveedores = $conexion->query($query_proveedores);

                                    // Mostrar opciones de proveedores en el select
                                    while ($row = $result_proveedores->fetch_assoc()) {
                                        echo '<option value="' . $row['id_proveedor'] . '">' . $row['nombre'] . '</option>';
                                    }

                                    // Obtener el primer proveedor disponible
                                    $result_proveedores->data_seek(0);
                                    $first_provider = $result_proveedores->fetch_assoc();
                                    $first_provider_id = $first_provider['id_proveedor'];

                                    // Cargar productos del primer proveedor inicialmente
                                    echo '<script>';
                                    echo '$(document).ready(function() {';
                                    echo '$("#proveedor").val("' . $first_provider_id . '").change();'; // Simula el cambio para cargar los productos del primer proveedor
                                    echo '});';
                                    echo '</script>';

                                    // Cerrar conexión
                                    $conexion->close();
                                ?>
                            </select>
                        </div>

                        <!-- Campo para la fecha de la compra -->
                        <div class="form-group">
                            <label for="fecha">Fecha de compra:</label>
                            <input type="date" id="fecha" name="fecha" class="form-control" required>
                        </div>

                        <!-- Tabla dinámica para agregar productos a la compra -->
                        <h3>Detalle de Compra</h3>
                        <table id="detalle_compra" class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Cantidad</th>
                                    <th>Producto</th>
                                    <th>Precio Compra</th>
                                    <th>Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="number" name="cantidad[]" class="form-control" required></td>
                                    <td>
                                        <select name="producto[]" class="form-control select-producto" required>
                                            <!-- Aquí se cargarán dinámicamente los productos relacionados con el proveedor seleccionado -->
                                        </select>
                                    </td>
                                    <td><input type="number" step="0.01" name="precio_compra[]" class="form-control" required></td>
                                    <td><button type="button" class="btn btn-delete" onclick="eliminarFila(this)">Eliminar</button></td>
                                </tr>
                            </tbody>
                        </table>
                        <button type="button" class="btn btn-primary mb-3" onclick="agregarFila()">Agregar Producto</button>

                        <br><br>
                        <input type="submit" class="btn btn-success" value="Guardar Compra">
                    </form>
                    <a href="ver_compras.php"><button class="btn btn-success">ver compras</button></a>
                    
                </div>
                
            </main>
            
        </div>
    </div>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        // Función para cargar productos relacionados con el proveedor seleccionado
        $('#proveedor').change(function() {
            var proveedorId = $(this).val();
            $.ajax({
                url: 'cargar_productos.php',
                type: 'POST',
                data: { proveedor_id: proveedorId },
                dataType: 'json',
                success: function(response) {
                    var options = '';
                    response.forEach(function(producto) {
                        options += '<option value="' + producto.Codigo_Producto + '">' + producto.Nombre_P + '</option>';
                    });
                    $('.select-producto').html(options); // Actualiza todos los select de clase .select-producto
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar productos: ' + error);
                }
            });
        });

        // Función JavaScript para agregar dinámicamente filas a la tabla de detalle_compra
        function agregarFila() {
            var table = document.getElementById("detalle_compra").getElementsByTagName('tbody')[0];
            var newRow = table.insertRow(-1);
            var cell1 = newRow.insertCell(0);
            var cell2 = newRow.insertCell(1);
            var cell3 = newRow.insertCell(2);
            var cell4 = newRow.insertCell(3);
            cell1.innerHTML =            '<input type="number" name="cantidad[]" class="form-control" required>';
            cell2.innerHTML = '<select name="producto[]" class="form-control select-producto" required></select>'; // Se deja vacío para llenarlo dinámicamente
            cell3.innerHTML = '<input type="number" step="0.01" name="precio_compra[]" class="form-control" required>';
            cell4.innerHTML = '<button type="button" class="btn btn-delete" onclick="eliminarFila(this)">Eliminar</button>';

            // Cargar productos del proveedor seleccionado en la nueva fila
            var proveedorId = $('#proveedor').val();
            $.ajax({
                url: 'cargar_productos.php',
                type: 'POST',
                data: { proveedor_id: proveedorId },
                dataType: 'json',
                success: function(response) {
                    var options = '';
                    response.forEach(function(producto) {
                        options += '<option value="' + producto.Codigo_Producto + '">' + producto.Nombre_P + '</option>';
                    });
                    $(cell2).find('.select-producto').html(options); // Actualiza el select específico dentro de la nueva fila
                },
                error: function(xhr, status, error) {
                    console.error('Error al cargar productos: ' + error);
                }
            });
        }

        // Función para eliminar la fila de la tabla de detalle_compra
        function eliminarFila(button) {
            var row = button.closest('tr');
            row.remove();
        }

        $(document).ready(function() {
            // Simular cambio al cargar la página para cargar productos del primer proveedor
            $('#proveedor').val('<?php echo $first_provider_id; ?>').change();
        });
    </script>
</body>
</html>

