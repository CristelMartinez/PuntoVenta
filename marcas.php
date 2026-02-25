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

// Verificar si se ha enviado el formulario para eliminar una marca
if(isset($_POST['id'])) {
    $idMarca = limpiarDatos($_POST['id']);
    // Consulta SQL para actualizar el estado de la marca a 0
    $queryActualizar = "UPDATE marca SET estatus = 0 WHERE Id_Marca = $idMarca";
    if(mysqli_query($conexion, $queryActualizar)) {
        echo "Marca eliminada correctamente";
    } else {
        echo "Error al eliminar la marca: " . mysqli_error($conexion);
    }
    exit; // Terminar el script después de procesar la eliminación
}

// Verificar si se ha enviado el formulario para agregar una nueva marca
if(isset($_POST['nombre']) && isset($_POST['descripcion'])) {
    $nombre = limpiarDatos($_POST['nombre']);
    $descripcion = limpiarDatos($_POST['descripcion']);

    // Validaciones del lado del servidor
    if (empty($nombre)) {
        echo "El nombre es obligatorio.";
        exit;
    }
    if (empty($descripcion)) {
        echo "La descripción es obligatoria.";
        exit;
    }

    // Consulta SQL para insertar una nueva marca
    $queryInsertar = "INSERT INTO marca (Nombre_Marca, Descripcion_Marca, estatus) VALUES ('$nombre', '$descripcion', 1)";
    if(mysqli_query($conexion, $queryInsertar)) {
        echo "Marca agregada correctamente";
    } else {
        echo "Error al agregar la marca: " . mysqli_error($conexion);
    }
    exit; // Terminar el script después de procesar la adición
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Marcas</title>
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
            <h1 class="mr-3">Marcas</h1>
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
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th style="display:none;">ID</th> <!-- Ocultar la columna de ID -->
                                <th>Nombre</th>
                                <th>Descripción</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            // Consulta SQL para obtener las marcas activas
                            $query = "SELECT * FROM marca WHERE estatus=1";
                            $resultado = mysqli_query($conexion, $query);
                            
                            // Iterar sobre los resultados y mostrar cada marca
                            while ($fila = mysqli_fetch_assoc($resultado)) {
                                echo "<tr>";
                                echo "<td style='display:none;'>" . $fila['Id_Marca'] . "</td>"; // Ocultar el ID
                                echo "<td>" . $fila['Nombre_Marca'] . "</td>";
                                echo "<td>" . $fila['Descripcion_Marca'] . "</td>";
                                echo "<td>";
                                echo "<a href='editar_marca.php?id=" . $fila['Id_Marca'] . "' class='btn btn-primary'><i class='bi bi-pen'></i> Editar</a> ";
                                echo "<button class='btn btn-danger btn-eliminar' data-id='" . $fila['Id_Marca'] . "'>Eliminar</button>";
                                echo "</td>";
                                echo "</tr>";
                            }
                            
                            // Liberar resultado
                            mysqli_free_result($resultado);
                            ?>
                        </tbody>
                    </table>
                </div>
                <!-- Agregar nueva marca -->
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h3>Agregar Nueva Marca</h3>
                        <form id="form-agregar" method="POST">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción:</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required></textarea>
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
                var idMarca = $(this).data('id');
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
                            url: 'marcas.php',
                            method: 'POST',
                            data: {id: idMarca},
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
                                    'Error al eliminar la marca',
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
                var descripcion = $('#descripcion').val().trim();

                // Validaciones del lado del cliente
                if(nombre === "") {
                    Swal.fire(
                        'Error',
                        'El nombre es obligatorio.',
                        'error'
                    );
                    return;
                }
                
                if(descripcion === "") {
                    Swal.fire(
                        'Error',
                        'La descripción es obligatoria.',
                        'error'
                    );
                    return;
                }

                $.ajax({
                    url: 'marcas.php',
                    method: 'POST',
                    data: {nombre: nombre, descripcion: descripcion},
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
                            'Error al agregar la marca',
                            'error'
                        );
                    }
                });
            });
        });
    </script>
</body>
</html>
