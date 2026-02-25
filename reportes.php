<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reporte de Ventas</title>
    <!-- Incluir SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Incluir CSS de Bootstrap -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <!-- Incluir jQuery -->
    <script src="https://cdn.jsdelivr.net/jquery/latest/jquery.min.js"></script>
    <!-- Incluir Moment.js -->
    <script src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
    <!-- Incluir DateRangePicker -->
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" />
    <!-- Incluir JavaScript de Bootstrap -->
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <style>
        .reportes-container {
            margin-top: 20px;
        }
        .reportes-container .card {
            padding: 20px;
        }
        .reportes-container .resultados {
            margin-top: 20px;
        }
        .reportes-container .resultados .resultado {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <div class="container reportes-container">
        <h2 class="text-center">Reportes</h2>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="row">
                        <div class="col-md-6">
                            <label for="rango-fechas">Seleccione el rango de fechas:</label>
                            <input type="text" id="rango-fechas" class="form-control">
                        </div>
                        <div class="col-md-6 text-center">
                            <button id="generar-reporte" class="btn btn-primary mt-4">Generar</button>
                        </div>
                    </div>
                    <div class="row resultados">
                        <div class="col-md-6 resultado">
                            <label>Total de Ventas Activas:</label>
                            <div id="total-ventas-activas" class="form-control">$ 0.00</div>
                        </div>
                        <div class="col-md-6 resultado">
                            <label>Total de Ventas Dadas de Baja:</label>
                            <div id="total-ventas-bajas" class="form-control">$ 0.00</div>
                        </div>
                        <div class="col-md-6 resultado">
                            <label>Total de productos vendidos en Ventas Activas:</label>
                            <div id="total-productos-activos" class="form-control">0</div>
                        </div>
                        <div class="col-md-6 resultado">
                            <label>Total de productos vendidos en Ventas Dadas de Baja:</label>
                            <div id="total-productos-bajas" class="form-control">0</div>
                        </div>
                    </div>
                    <div class="row justify-content-center">
                        <button id="imprimir-reporte" class="btn btn-secondary mt-4">Imprimir reporte en PDF</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Inicializar el selector de rango de fechas con el calendario
            $('#rango-fechas').daterangepicker({
                locale: {
                    format: 'YYYY-MM-DD',
                    applyLabel: 'Aplicar',
                    cancelLabel: 'Cancelar',
                    daysOfWeek: ['Dom', 'Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb'],
                    monthNames: ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre']
                },
                startDate: moment().startOf('month'),
                endDate: moment().endOf('month')
            });

            // Manejar el botón de generación de reporte
            $('#generar-reporte').click(function() {
                var rangoFechas = $('#rango-fechas').val();
                var fechas = rangoFechas.split(' - ');
                var fechaInicio = fechas[0];
                var fechaFin = fechas[1];

                // Realizar la solicitud AJAX para obtener los datos
                $.ajax({
                    url: "obtener_reportes_ventas.php", // URL del script PHP para obtener los datos
                    method: "GET",
                    data: {
                        fecha_inicio: fechaInicio,
                        fecha_fin: fechaFin
                    },
                    dataType: "json",
                    success: function(response) {
                        if (response.success) {
                            // Actualizar los elementos HTML con los datos del reporte
                            $('#total-ventas-activas').text('$ ' + response.total_ventas_activas.toFixed(2));
                            $('#total-ventas-bajas').text('$ ' + response.total_ventas_bajas.toFixed(2));
                            $('#total-productos-activos').text(response.total_productos_activos);
                            $('#total-productos-bajas').text(response.total_productos_bajas);
                        } else {
                            // Mostrar mensaje de error si no se pudo generar el reporte
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: response.error
                            });
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        console.error("Error al obtener los datos del reporte:", errorThrown);
                        // Mostrar mensaje de error si hubo un error en la solicitud AJAX
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: 'Hubo un error al comunicarse con el servidor. Por favor, inténtalo de nuevo más tarde.'
                        });
                    }
                });
            });

            // Manejar el botón de impresión de reporte en PDF
            $('#imprimir-reporte').click(function() {
                window.location.href = 'generar_reporte_pdf.php?fecha_inicio=' + $('#rango-fechas').data('daterangepicker').startDate.format('YYYY-MM-DD') +
                    '&fecha_fin=' + $('#rango-fechas').data('daterangepicker').endDate.format('YYYY-MM-DD');
            });
        });
    </script>
</body>
</html>
