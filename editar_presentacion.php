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

// Verificar si se ha enviado el formulario para editar una presentación
if(isset($_POST['id']) && isset($_POST['descripcion'])) {
    $idPresentacion = limpiarDatos($_POST['id']);
    $descripcion = limpiarDatos($_POST['descripcion']);

    // Validaciones del lado del servidor
    if (empty($descripcion)) {
        echo "La descripción es obligatoria.";
        exit;
    }

    // Consulta SQL para actualizar la presentación
    $queryActualizar = "UPDATE presentacion SET Descripcion = '$descripcion' WHERE Id_Presentacion = $idPresentacion";
    if(mysqli_query($conexion, $queryActualizar)) {
        echo "Presentación actualizada correctamente";
    } else {
        echo "Error al actualizar la presentación: " . mysqli_error($conexion);
    }
    exit; // Terminar el script después de procesar la actualización
}

// Obtener el ID de la presentación a editar
if(isset($_GET['id'])) {
    $idPresentacion = limpiarDatos($_GET['id']);

    // Consulta SQL para obtener la información de la presentación
    $queryPresentacion = "SELECT * FROM presentacion WHERE Id_Presentacion = $idPresentacion";
    $resultado = mysqli_query($conexion, $queryPresentacion);

    if(mysqli_num_rows($resultado) > 0) {
        $fila = mysqli_fetch_assoc($resultado);
        $descripcion = $fila['Descripcion'];
    } else {
        echo "No se encontró ninguna presentación con ese ID.";
        exit;
    }
} else {
    echo "Se requiere un ID de presentación para editar.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Presentación</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome para los iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <header class="bg-primary text-white p-3 d-flex justify-content-between align-items-center rounded-bottom">
        <div class="header-left">
            <h1><a href="index.php" class="text-white text-decoration-none">Menu</a></h1>
        </div>
        <div class="header-right d-flex align-items-center">
            <h1 class="mr-3">Editar Presentación</h1>
            <img src="src/logo.png" alt="Shopping Cart" class="cart-icon">
        </div>
    </header>
    <div class="container-fluid h-100">
        <div class="row h-100">
            <!-- Barra lateral -->
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

            <!-- Contenido principal -->
            <main role="main" class="col-md-10 ml-sm-auto col-lg-10 px-4">
                <div class="container mt-5">
                    <h1>Editar Presentación</h1>
                    <form id="form-editar" method="POST">
                        <input type="hidden" name="id" value="<?php echo $idPresentacion; ?>">
                        <div class="form-group">
                            <label for="descripcion">Descripción:</label>
                            <input type="text" class="form-control" id="descripcion" name="descripcion" value="<?php echo $descripcion; ?>" required>
                        </div>
                        <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
    <script>
        $(document).ready(function() {
            $('#form-editar').submit(function(event) {
                event.preventDefault();
                var id = $(this).find("input[name='id']").val();
                var descripcion = $(this).find("input[name='descripcion']").val();

                $.ajax({
                    url: 'editar_presentacion.php',
                    method: 'POST',
                    data: {id: id, descripcion: descripcion},
                    success: function(response) {
                         // Manejar la respuesta como desees
                        window.location.href = 'presentaciones.php'; // Redirigir a presentaciones.php
                    },
                    error: function(xhr, status, error) {
                        console.error(xhr.responseText);
                    }
                });
            });
        });
    </script>
</body>
</html>
