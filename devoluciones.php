<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Ventas</title>
    <!-- Incluye Bootstrap CSS para estilizar -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Gestión de Ventas</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Folio Venta</th>
                    <th>Fecha</th>
                    <th>Id Usuario</th>
                    <th>Total a Pagar</th>
                    <th>Estatus</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="venta-table-body">
                <!-- Aquí se inyectarán dinámicamente las filas de ventas -->
            </tbody>
        </table>

        <!-- Formulario Modal para Editar Venta -->
        <div class="modal fade" id="editVentaModal" tabindex="-1" aria-labelledby="editVentaModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editVentaModalLabel">Editar Venta</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form id="editVentaForm">
                            <input type="hidden" id="editFolioVenta">
                            <div class="mb-3">
                                <label for="editFecha" class="form-label">Fecha</label>
                                <input type="date" class="form-control" id="editFecha" required>
                            </div>
                            <div class="mb-3">
                                <label for="editIdUsuario" class="form-label">Id Usuario</label>
                                <input type="number" class="form-control" id="editIdUsuario" required>
                            </div>
                            <div class="mb-3">
                                <label for="editTotalPagar" class="form-label">Total a Pagar</label>
                                <input type="number" class="form-control" id="editTotalPagar" step="0.01" required readonly>
                            </div>

                            <!-- Sección para mostrar productos de la venta -->
                            <div class="mb-3">
                                <label for="detalleVenta" class="form-label">Productos en la Venta</label>
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Código Producto</th>
                                            <th>Cantidad</th>
                                            <th>Total Pagar</th>
                                            <th>Acción</th>
                                        </tr>
                                    </thead>
                                    <tbody id="detalleVentaTableBody">
                                        <!-- Aquí se mostrarán los productos -->
                                    </tbody>
                                </table>
                            </div>

                            <button type="submit" class="btn btn-primary">Guardar Cambios</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Formulario Modal para Confirmar Eliminación -->
        <div class="modal fade" id="deleteVentaModal" tabindex="-1" aria-labelledby="deleteVentaModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="deleteVentaModalLabel">Confirmar Devolución</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>¿Estás seguro de que quieres marcar esta venta como eliminada?</p>
                        <button type="button" class="btn btn-danger" id="confirmDeleteButton">Confirmar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Incluye Bootstrap JS y sus dependencias -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <!-- JavaScript para manejar la lógica de edición y eliminación -->
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        // Cargar ventas al iniciar la página
        fetchVentas();

        // Cargar datos de ventas desde el servidor
        function fetchVentas() {
            fetch('fetch_ventas.php')
                .then(response => response.json())
                .then(data => {
                    renderVentas(data);
                })
                .catch(error => console.error('Error al cargar las ventas:', error));
        }

        // Renderizar ventas en la tabla
        function renderVentas(ventas) {
            const ventaTableBody = document.getElementById('venta-table-body');
            ventaTableBody.innerHTML = '';
            ventas.forEach(venta => {
                // Convertimos Total_Pagar a un número antes de llamar a toFixed
                const totalPagar = parseFloat(venta.Total_Pagar);
                const row = document.createElement('tr');
                row.innerHTML = `
                    <td>${venta.Folio_Venta}</td>
                    <td>${venta.Fecha}</td>
                    <td>${venta.Id_Usuario}</td>
                    <td>${totalPagar.toFixed(2)}</td> <!-- Convertido a número y luego a cadena con 2 decimales -->
                    <td>${venta.estatus == '1' ? 'Activo' : 'Eliminado'}</td>
                    <td>
                        <button class="btn btn-warning btn-sm me-2" onclick="editVenta(${venta.Folio_Venta})" ${venta.estatus == '0' ? 'disabled' : ''}>Editar</button>
                        <button class="btn btn-danger btn-sm" onclick="deleteVenta(${venta.Folio_Venta})" ${venta.estatus == '0' ? 'disabled' : ''}>Eliminar</button>
                    </td>
                `;
                ventaTableBody.appendChild(row);
            });
        }

        // Función para cargar y mostrar la venta y sus productos en el modal de edición
        window.editVenta = (folioVenta) => {
            fetch(`fetch_venta.php?folio=${folioVenta}`)
                .then(response => response.json())
                .then(venta => {
                    document.getElementById('editFolioVenta').value = venta.Folio_Venta;
                    document.getElementById('editFecha').value = venta.Fecha;
                    document.getElementById('editIdUsuario').value = venta.Id_Usuario;
                    document.getElementById('editTotalPagar').value = venta.Total_Pagar;

                    // Cargar los detalles de la venta
                    return fetch(`fetch_detalle_venta.php?folio=${folioVenta}`);
                })
                .then(response => response.json())
                .then(detalles => {
                    const detalleTableBody = document.getElementById('detalleVentaTableBody');
                    detalleTableBody.innerHTML = '';

                    detalles.forEach(detalle => {
                        // Convertir Total_Pagar a número antes de aplicar toFixed
                        const totalPagarDetalle = parseFloat(detalle.Total_Pagar);
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${detalle.Codigo_Producto}</td>
                            <td>${detalle.Cantidad}</td>
                            <td>${totalPagarDetalle.toFixed(2)}</td> <!-- Asegurar que Total_Pagar es un número -->
                            <td>
                                <button class="btn btn-danger btn-sm" onclick="deleteProduct(${detalle.Id_Detalle_Venta}, ${detalle.Folio_Venta})">Eliminar</button>
                            </td>
                        `;
                        detalleTableBody.appendChild(row);
                    });

                    new bootstrap.Modal(document.getElementById('editVentaModal')).show();
                })
                .catch(error => console.error('Error al cargar la venta o sus detalles:', error));
        };

        // Función para eliminar un producto de una venta
        window.deleteProduct = (detalleId, folioVenta) => {
            if (confirm('¿Estás seguro de que deseas eliminar este producto de la venta?')) {
                fetch(`delete_detalle_venta.php?id=${detalleId}`, { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            // Recargar los detalles de la venta después de eliminar el producto
                            window.editVenta(folioVenta);
                        } else {
                            alert('Error al eliminar el producto de la venta');
                        }
                    })
                    .catch(error => console.error('Error al eliminar el producto de la venta:', error));
            }
        };

        // Función para eliminar la venta
        window.deleteVenta = (folioVenta) => {
            const confirmButton = document.getElementById('confirmDeleteButton');
            confirmButton.onclick = () => {
                fetch(`delete_venta.php?folio=${folioVenta}`, { method: 'POST' })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            fetchVentas();
                        } else {
                            alert('Error al eliminar la venta');
                        }
                    })
                    .catch(error => console.error('Error al eliminar la venta:', error));
                bootstrap.Modal.getInstance(document.getElementById('deleteVentaModal')).hide();
            };
            new bootstrap.Modal(document.getElementById('deleteVentaModal')).show();
        };

        // Manejar la actualización de la venta
        document.getElementById('editVentaForm').addEventListener('submit', (event) => {
            event.preventDefault();
            const folioVenta = parseInt(document.getElementById('editFolioVenta').value);
            const venta = {
                Folio_Venta: folioVenta,
                Fecha: document.getElementById('editFecha').value,
                Id_Usuario: parseInt(document.getElementById('editIdUsuario').value),
                Total_Pagar: parseFloat(document.getElementById('editTotalPagar').value)
            };

            fetch('update_venta.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(venta)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchVentas();
                } else {
                    alert('Error al actualizar la venta');
                }
            })
            .catch(error => console.error('Error al actualizar la venta:', error));
            
            bootstrap.Modal.getInstance(document.getElementById('editVentaModal')).hide();
        });
    });
</script>

</body>
</html>
