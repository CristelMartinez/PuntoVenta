<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ver Compras</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Agregar SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
            <main class="col-md-10 d-flex justify-content-center align-items-center rounded-left">
                <div class="container">
                    <h2>Compras Realizadas</h2>
                    <?php
                    // Incluir el archivo de conexión a la base de datos
                    include('conexion/conectar-mysql.php');

                    // Consulta para obtener todas las compras activas
                    $queryCompras = "SELECT * FROM compra WHERE estatus = 1";
                    $resultCompras = mysqli_query($conexion, $queryCompras);

                    // Comprobar si hay compras
                    if (mysqli_num_rows($resultCompras) > 0) {
                        // Mostrar tabla con las compras
                        echo "<table class='table'>";
                        echo "<thead>";
                        echo "<tr><th>Folio de Compra</th><th>Total a Pagar</th><th>Fecha</th><th>Acciones</th></tr>";
                        echo "</thead>";
                        echo "<tbody>";
                        // Iterar sobre el resultado y mostrar cada compra
                        while ($row = mysqli_fetch_assoc($resultCompras)) {
                            echo "<tr>";
                            echo "<td>" . $row['Folio_Compra'] . "</td>";
                            echo "<td>" . $row['Total_Pagar'] . "</td>";
                            echo "<td>" . $row['Fecha'] . "</td>";
                            echo "<td>";
                            echo "<button onclick='verDetalles(\"" . $row['Folio_Compra'] . "\")' class='btn btn-info'>Ver Detalles</button> ";
                            echo "<a href='actualizar_compra.php?folio=" . $row['Folio_Compra'] . "' class='btn btn-primary'>Actualizar</a> ";
                            echo "<button onclick='eliminarCompra(\"" . $row['Folio_Compra'] . "\")' class='btn btn-danger'>Eliminar</button>";
                            echo "</td>";
                            echo "</tr>";
                        }
                        echo "</tbody>";
                        echo "</table>";
                    } else {
                        // Si no hay compras
                        echo "<p>No se encontraron compras.</p>";
                    }

                    // Cerrar conexión
                    mysqli_close($conexion);
                    ?>
                </div>
            </main>
        </div>
    </div>
    <script>
        // Función para obtener y mostrar los detalles de una compra
        // Función para obtener y mostrar los detalles de una compra
// Función para obtener y mostrar los detalles de una compra
function verDetalles(folioCompra) {
    fetch(`obtener_detalle_compra.php?folioCompra=${folioCompra}`)
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener detalles de compra.');
            }
            return response.json(); // Parsear la respuesta JSON
        })
        .then(data => {
            // Verificar si hay un error
            if (data.hasOwnProperty('error')) {
                throw new Error(data.error);
            }

            // Mostrar los detalles de la compra en una tabla flotante
            let detallesHtml = `<h3>Detalles de la Compra - Folio: ${folioCompra}</h3>`;
            detallesHtml += `<table class="table"><thead><tr><th>Cantidad</th><th>Precio de Compra</th><th>Nombre del Producto</th></tr></thead><tbody>`;

            // Iterar sobre los detalles y construir las filas de la tabla
            data.forEach(detalle => {
                detallesHtml += `<tr>`;
                detallesHtml += `<td>${detalle.Cantidad}</td>`;
                detallesHtml += `<td>${detalle.Precio_Compra}</td>`;
                detallesHtml += `<td>${detalle.Nombre_P}</td>`;
                detallesHtml += `</tr>`;
            });

            detallesHtml += `</tbody></table>`;

            // Mostrar la tabla flotante con los detalles
            Swal.fire({
                html: detallesHtml,
                showConfirmButton: false
            });
        })
        .catch(error => {
            console.error('Error al obtener detalles de compra:', error.message);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Error al obtener los detalles de la compra.'
            });
        });
}



        // Función para eliminar una compra
        function eliminarCompra(folioCompra) {
            Swal.fire({
                title: '¿Estás seguro?',
                text: 'Esta acción eliminará la compra permanentemente.',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, eliminar'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Si se confirma, redireccionar a eliminar_compra.php con el folio de la compra
                    window.location.href = 'eliminar_compra.php?folio=' + folioCompra;
                }
            });
        }
    </script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
