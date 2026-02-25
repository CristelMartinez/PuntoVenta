<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Principal</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
    <!-- Typeahead.js CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.css" rel="stylesheet">
    <!-- SweetAlert CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.css">
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
                <div>
                    <h2>Buscar Productos y Agregar a Venta</h2>
                    <label for="nombre_producto">Nombre del Producto:</label>
                    <input type="text" id="nombre_producto" class="form-control" required>
                    <button onclick="agregarProducto()" class="btn btn-primary">Agregar</button>

                    <h2>Resultados de la Búsqueda</h2>
                    <div id="resultados_busqueda"></div>

                    <h2>Productos Seleccionados para Venta</h2>
                    <table id="tabla-venta" class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Código</th>
                                <th>Nombre</th>
                                <th>Precio</th>
                                <th>Cantidad</th>
                                <th>Total</th>
                                <th>Eliminar</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                    <div id="total-venta">Total de la Venta: $<span id="total-venta-amount">0.00</span></div>
                    <button onclick="finalizarVenta()" class="btn btn-success mt-3">Finalizar Venta</button>
                </div>
            </main>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.2/bootstrap3-typeahead.min.js"></script>
    <!-- SweetAlert JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>

    <!-- Typeahead.js -->
    <script>
        $(document).ready(function(){
            $('#nombre_producto').typeahead({
                source: function(query, result){
                    $.ajax({
                        url:"buscar_producto.php",
                        method:"GET",
                        data:{termino_busqueda:query},
                        dataType:"json",
                        success:function(data){
                            result($.map(data, function(item){
                                return item.Nombre_P;
                            }));
                        }
                    });
                },
                updater: function(item) {
                    // Una vez seleccionado un producto, mostrar su información en un alert (puedes modificar esto según tus necesidades)
                    //swal("Producto seleccionado", item, "success");
                    return item;
                }
            });
        });

        function agregarProducto() {
            var nombreProducto = $('#nombre_producto').val(); // Obtener el nombre del producto

            // Hacer una solicitud AJAX para obtener más información sobre el producto
            $.ajax({
                url: "obtener_producto.php", // URL para obtener más detalles del producto (debe ser implementada en tu servidor)
                method: "POST",
                data: { termino_busqueda: nombreProducto },
                dataType: "json",
                success: function(data) {
                    // Verificar si se devolvieron datos válidos desde el servidor
                    if (data && data.length > 0) {
                        // Agregar el primer producto encontrado a la tabla de venta
                        var producto = data[0]; // Obtener el primer producto de los datos devueltos
                        var cantidad = 1; // Inicializar la cantidad como 1
                        var total = cantidad * producto.Precio_p; // Calcular el total

                        // Agregar el producto a la tabla de venta
                        var row = $('<tr>' +
                            '<td>' + (producto.Codigo_Producto || '') + '</td>' + // Verificar si el código del producto está definido
                            '<td>' + (producto.Nombre_P || '') + '</td>' + // Verificar si el nombre del producto está definido
                            '<td>' + (producto.Precio_p || '') + '</td>' + // Verificar si el precio del producto está definido
                            '<td><input type="number" value="' + cantidad + '" min="1" class="form-control cantidad"></td>' +
                            '<td>' + total + '</td>' +
                            '<td><button onclick="eliminarProducto(this)" class="btn btn-danger">Eliminar</button></td>' +
                        '</tr>').appendTo('#tabla-venta tbody');

                        // Escuchar cambios en la cantidad y actualizar el total
                        row.find('.cantidad').on('input', function() {
                            var cantidad = $(this).val();
                            var precio = parseFloat(row.find('td:eq(2)').text()); // Obtener el precio del producto
                            row.find('td:eq(4)').text(cantidad * precio); // Actualizar el total
                            actualizarTotalVenta();
                        });

                        // Actualizar el total de la venta al agregar un nuevo producto
                        actualizarTotalVenta();

                        // Limpiar el campo de texto después de agregar el producto
                        $('#nombre_producto').val('');

                        // Mostrar una alerta SweetAlert para informar que el producto ha sido agregado
                        swal("Producto Agregado", "El producto se ha agregado a la venta correctamente", "success");
                    } else {
                        console.error("No se encontraron productos o la estructura de los datos es incorrecta");
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error("Error al obtener el producto:", errorThrown);
                }
            });
        }

        function eliminarProducto(button) {
            // Eliminar un producto de la tabla de venta
            var row = $(button).closest('tr'); // Obtener la fila que contiene el botón Eliminar
            row.remove(); // Eliminar la fila

            // Actualizar el total de la venta
            actualizarTotalVenta();
        }

        // Función para actualizar el total de la venta
        function actualizarTotalVenta() {
            var totalVenta = 0;
            $('#tabla-venta tbody tr').each(function() {
                var cantidad = parseFloat($(this).find('.cantidad').val());
                var precio = parseFloat($(this).find('td:eq(2)').text());
                totalVenta += cantidad * precio;
            });
            $('#total-venta-amount').text(totalVenta.toFixed(2));
        }

        function finalizarVenta() {
            var detallesVenta = [];
            $('#tabla-venta tbody tr').each(function() {
                var detalle = {
                    Cantidad: $(this).find('.cantidad').val(),
                    Total_Pagar: $(this).find('td:eq(4)').text(),
                    Codigo_Producto: $(this).find('td:eq(0)').text()
                };
                detallesVenta.push(detalle);
            });

            var totalVenta = parseFloat($('#total-venta-amount').text());

            // Hacer una solicitud AJAX para insertar los datos de la venta y los detalles de la venta en la base de datos
            $.ajax({
    url: "insertar_venta.php", // URL para insertar la venta en la base de datos (debe ser implementada en tu servidor)
    method: "POST",
    data: {
        total_venta: totalVenta,
        detalles_venta: detallesVenta
    },
    dataType: "json",
    success: function(response) {
        console.log(response); // Muestra la respuesta en la consola del navegador para depuración

        // Manejar la respuesta del servidor
        if (response.success) {
            // Si la inserción fue exitosa, mostrar un mensaje de éxito
            swal("Venta Finalizada", "La venta se ha registrado correctamente", "success");
            
            // Limpiar la tabla de venta después de finalizar la venta
            $('#tabla-venta tbody').empty();
            
            // Actualizar el total de la venta a cero
            $('#total-venta-amount').text('0.00');
        } else if (response.stock_error) {
            // Si hubo un error debido a stock insuficiente, mostrar un mensaje específico
            swal("Stock Insuficiente", response.stock_error, "warning");
        } else {
            // Si hubo algún otro error, mostrar un mensaje de error genérico
            swal("Error", "Hubo un error al registrar la venta. Por favor, inténtalo de nuevo más tarde", "error");
        }
    },
    error: function(jqXHR, textStatus, errorThrown) {
        // Si hubo un error en la solicitud AJAX, mostrar un mensaje de error
        swal("Error", "Hubo un error al comunicarse con el servidor. Por favor, inténtalo de nuevo más tarde", "error");
        
        console.error("Error al finalizar la venta:", errorThrown);
        console.log(jqXHR.responseText); // Muestra la respuesta completa del servidor para depuración
    }
});


        }
    </script>
</body>
</html>
