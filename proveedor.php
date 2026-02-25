<?php
// Incluir el archivo de conexión
include('conexion/conectar-mysql.php');

// Función para limpiar y validar datos
function limpiarDatos($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Verificar si se ha enviado el formulario para eliminar un proveedor
if(isset($_POST['id'])) {
    $idProveedor = limpiarDatos($_POST['id']);
    // Consulta SQL para actualizar el estado del proveedor a 0
    $queryActualizar = "UPDATE proveedor SET estatus = 0 WHERE id_proveedor = $idProveedor";
    if(mysqli_query($conexion, $queryActualizar)) {
        echo "Proveedor eliminado correctamente";
    } else {
        echo "Error al eliminar el proveedor: " . mysqli_error($conexion);
    }
    exit; // Terminar el script después de procesar la eliminación
}

// Verificar si se ha enviado el formulario para agregar un nuevo proveedor
if(isset($_POST['nombre']) && isset($_POST['numero_telefono']) && isset($_POST['id_marca'])) {
    $nombre = limpiarDatos($_POST['nombre']);
    $numeroTelefono = limpiarDatos($_POST['numero_telefono']);
    $idMarca = limpiarDatos($_POST['id_marca']);

    // Validaciones del lado del servidor
    if (empty($nombre)) {
        echo "El nombre es obligatorio.";
        exit;
    }
    if (empty($numeroTelefono)) {
        echo "El número de teléfono es obligatorio.";
        exit;
    }

    // Consulta SQL para insertar un nuevo proveedor
    $queryInsertar = "INSERT INTO proveedor (nombre, numero_telefono, id_marca, estatus) 
                     VALUES ('$nombre', '$numeroTelefono', '$idMarca', 1)";
    if(mysqli_query($conexion, $queryInsertar)) {
        echo "Proveedor agregado correctamente";
    } else {
        echo "Error al agregar el proveedor: " . mysqli_error($conexion);
    }
    exit; // Terminar el script después de procesar la adición
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Proveedores</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Font Awesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.css">
</head>
<body>
    <header class="bg-primary text-white p-3 d-flex justify-content-between align-items-center rounded-bottom">
        <div class="header-left">
            <h1><a href="index.php" class="text-white text-decoration-none">Menu</a></h1>
        </div>
        <div class="header-right d-flex align-items-center">
            <h1 class="mr-3">Proveedores</h1>
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
                            <a class="nav-link" href="marcas.php">Marcas</a>
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
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="display:none;">ID</th> <!-- Ocultar la columna de ID -->
                                <th>Nombre</th>
                                <th>Número de Teléfono</th>
                                <th>ID Marca</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Consulta SQL para obtener los proveedores activos
                            $query = "SELECT * FROM proveedor WHERE estatus = 1";
                            $resultado = mysqli_query($conexion, $query);
                            
                            // Iterar sobre los resultados y mostrar cada proveedor
                            while ($fila = mysqli_fetch_assoc($resultado)) {
                                echo "<tr>";
                                echo "<td style='display:none;'>" . $fila['id_proveedor'] . "</td>"; // Ocultar el ID
                                echo "<td>" . $fila['nombre'] . "</td>";
                                echo "<td>" . $fila['numero_telefono'] . "</td>";
                                echo "<td>" . $fila['id_marca'] . "</td>";
                                echo "<td>";
                                echo "<a href='editar_proveedor.php?id=" . $fila['id_proveedor'] . "' class='btn btn-primary'><i class='bi bi-pen'></i> Editar</a> ";
                                echo "<button class='btn btn-danger btn-eliminar' data-id='" . $fila['id_proveedor'] . "'>Eliminar</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            
                            // Liberar resultado
                            mysqli_free_result($resultado);
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- Agregar nuevo proveedor -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h3>Agregar Nuevo Proveedor</h3>
                        <form id="form-agregar" method="POST">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="numero_telefono">Número de Teléfono:</label>
                                <input type="text" class="form-control" id="numero_telefono" name="numero_telefono" required>
                            </div>
                            <div class="form-group">
    <label for="id_marca">Marca:</label>
    <select class="form-control" id="id_marca" name="id_marca" required>
        <option value="">Selecciona una marca</option>
        <?php
        // Consulta SQL para obtener las marcas activas
        $queryMarcas = "SELECT Id_Marca, Nombre_Marca FROM marca WHERE estatus = 1";

        // Ejecutar la consulta
        $resultadoMarcas = mysqli_query($conexion, $queryMarcas);

        // Mostrar opciones en el combobox
        while ($filaMarca = mysqli_fetch_assoc($resultadoMarcas)) {
            echo "<option value='" . $filaMarca['Id_Marca'] . "'>" . $filaMarca['Nombre_Marca'] . "</option>";
        }

        // Liberar resultado
        mysqli_free_result($resultadoMarcas);
        ?>
    </select>
</div>

                            <button type="submit" class="btn btn-success"><i class="fas fa-plus"></i> Agregar Nuevo</button>
                        </form>
                    </div>
                </div>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
    <script>
        // JavaScript para manejar clics en botones de editar y eliminar
        $(document).ready(function() {
            $('.btn-eliminar').click(function() {
                var idProveedor = $(this).data('id');
                // Confirmar la eliminación y luego realizarla mediante AJAX
                Swal.fire({
                    title: '¿Estás seguro?',
                    text: "¡No podrás revertir esto!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Sí, eliminar',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: 'proveedor.php',
                            method: 'POST',
                            data: {id: idProveedor},
                            success: function(response) {
                                Swal.fire(
                                    'Eliminado',
                                    response,
                                    'success'
                                ).then(() => {
                                    window.location.reload();
                                });
                            },
                            error: function(xhr, status, error) {
                                Swal.fire(
                                    'Error',
                                    'Error al eliminar el proveedor',
                                    'error'
                                );
                            }
                        });
                    }
                });
            });

            $('#form-agregar').submit(function(event) {
                event.preventDefault();
                var nombre = $('#nombre').val().trim();
                var numeroTelefono = $('#numero_telefono').val().trim();
                var idMarca = $('#id_marca').val().trim();

                // Validaciones del lado del cliente
                if(nombre === "") {
                    Swal.fire(
                        'Error',
                        'El nombre es obligatorio.',
                        'error'
                    );
                    return;
                }
                
                if(numeroTelefono === "") {
                    Swal.fire(
                        'Error',
                        'El número de teléfono es obligatorio.',
                        'error'
                    );
                    return;
                }

                if(idMarca === "") {
                    Swal.fire(
                        'Error',
                        'El ID de la marca es obligatorio.',
                        'error'
                    );
                    return;
                }

                $.ajax({
                    url: 'proveedor.php',
                    method: 'POST',
                    data: {nombre: nombre, numero_telefono: numeroTelefono, id_marca: idMarca},
                    success: function(response) {
                        Swal.fire(
                            'Éxito',
                            response,
                            'success'
                        ).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error',
                            'Error al agregar el proveedor',
                            'error'
                        );
                    }
                });
            });
        });
    </script>
</body>
</html>
