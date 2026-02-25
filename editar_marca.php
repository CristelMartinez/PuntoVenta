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

// Verificar si se ha enviado el formulario para actualizar una marca
if (isset($_POST['id']) && isset($_POST['nombre']) && isset($_POST['descripcion'])) {
    $idMarca = limpiarDatos($_POST['id']);
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

    // Consulta SQL para actualizar la marca
    $queryActualizar = "UPDATE marca SET Nombre_Marca = '$nombre', Descripcion_Marca = '$descripcion' WHERE Id_Marca = $idMarca";
    if (mysqli_query($conexion, $queryActualizar)) {
        echo "Marca actualizada correctamente";
    } else {
        echo "Error al actualizar la marca: " . mysqli_error($conexion);
    }
    exit; // Terminar el script después de procesar la actualización
}

// Obtener los datos de la marca a editar
if (isset($_GET['id'])) {
    $idMarca = limpiarDatos($_GET['id']);
    $query = "SELECT * FROM marca WHERE Id_Marca = $idMarca";
    $resultado = mysqli_query($conexion, $query);
    $marca = mysqli_fetch_assoc($resultado);
    mysqli_free_result($resultado);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Marca</title>
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
            <h1 class="mr-3">Editar Marca</h1>
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
                <div class="row mt-3">
                    <div class="col-md-12">
                        <h3>Editar Marca</h3>
                        <?php if (isset($marca)): ?>
                        <form id="form-editar" method="POST">
                            <input type="hidden" name="id" value="<?php echo $marca['Id_Marca']; ?>">
                            <div class="form-group">
                                <label for="nombre">Nombre:</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo $marca['Nombre_Marca']; ?>" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcion">Descripción:</label>
                                <textarea class="form-control" id="descripcion" name="descripcion" rows="3" required><?php echo $marca['Descripcion_Marca']; ?></textarea>
                            </div>
                            <button type="submit" class="btn btn-success"><i class="fas fa-save"></i> Guardar Cambios</button>
                        </form>
                        <?php else: ?>
                            <p class="text-danger">Marca no encontrada.</p>
                        <?php endif; ?>
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
        $(document).ready(function() {
            $('#form-editar').submit(function(event) {
                event.preventDefault();
                var idMarca = $('input[name="id"]').val().trim();
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
                    url: 'editar_marca.php',
                    method: 'POST',
                    data: {id: idMarca, nombre: nombre, descripcion: descripcion},
                    success: function(response) {
                        Swal.fire(
                            'Éxito',
                            response,
                            'success'
                        ).then(() => {
                            window.location.href = 'marcas.php';
                        });
                    },
                    error: function(xhr, status, error) {
                        Swal.fire(
                            'Error',
                            'Error al actualizar la marca',
                            'error'
                        );
                    }
                });
            });
        });
    </script>
</body>
</html>
