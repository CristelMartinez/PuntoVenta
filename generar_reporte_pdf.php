<?php
require('fpdf/fpdf.php');
include('conexion/conectar-mysql.php');

if (!isset($_GET['fecha_inicio']) || !isset($_GET['fecha_fin'])) {
    die("Rango de fechas no proporcionado.");
}

$fecha_inicio = $_GET['fecha_inicio'];
$fecha_fin = $_GET['fecha_fin'];

// Consultar los datos de ventas y productos vendidos en el rango de fechas
$sql_ventas = "
    SELECT 
        v.Folio_Venta, 
        v.Fecha, 
        v.Estatus,
        u.Nombre_Usuario AS Usuario,
        dv.Cantidad, 
        dv.Total_Pagar AS Total_Detalle, 
        p.Nombre_P AS Producto,
        p.precio_c AS Precio_Compra,
        p.Precio_p AS Precio_Venta,
        p.Existencias AS Existencias_Despues
    FROM venta v
    INNER JOIN detalle_venta dv ON v.Folio_Venta = dv.Folio_Venta
    INNER JOIN producto p ON dv.Codigo_Producto = p.Codigo_Producto
    INNER JOIN usuarios u ON v.Id_Usuario = u.Id_Usuario
    WHERE v.Fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    ORDER BY v.Fecha DESC, v.Folio_Venta, p.Nombre_P
";

$result_ventas = mysqli_query($conexion, $sql_ventas);
if (!$result_ventas) {
    die("Error al obtener los datos de ventas: " . mysqli_error($conexion));
}

// Consultar el producto más vendido
$sql_producto_mas_vendido = "
    SELECT 
        p.Nombre_P AS Producto, 
        SUM(dv.Cantidad) AS Total_Vendido
    FROM detalle_venta dv
    INNER JOIN producto p ON dv.Codigo_Producto = p.Codigo_Producto
    INNER JOIN venta v ON dv.Folio_Venta = v.Folio_Venta
    WHERE v.Fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    GROUP BY p.Nombre_P
    ORDER BY Total_Vendido DESC
    LIMIT 1
";

$result_producto_mas_vendido = mysqli_query($conexion, $sql_producto_mas_vendido);
if (!$result_producto_mas_vendido) {
    die("Error al obtener el producto más vendido: " . mysqli_error($conexion));
}
$producto_mas_vendido = mysqli_fetch_assoc($result_producto_mas_vendido);

// Consultar las marcas más vendidas
$sql_marcas_mas_vendidas = "
    SELECT 
        m.Nombre_Marca AS Marca, 
        SUM(dv.Cantidad) AS Total_Vendido
    FROM detalle_venta dv
    INNER JOIN producto p ON dv.Codigo_Producto = p.Codigo_Producto
    INNER JOIN marca m ON p.Id_Marca = m.Id_Marca
    INNER JOIN venta v ON dv.Folio_Venta = v.Folio_Venta
    WHERE v.Fecha BETWEEN '$fecha_inicio' AND '$fecha_fin'
    GROUP BY m.Nombre_Marca
    ORDER BY Total_Vendido DESC
    LIMIT 5
";

$result_marcas_mas_vendidas = mysqli_query($conexion, $sql_marcas_mas_vendidas);
if (!$result_marcas_mas_vendidas) {
    die("Error al obtener las marcas más vendidas: " . mysqli_error($conexion));
}

// Consultar los productos con existencias bajas
$sql_productos_bajos_existencia = "
    SELECT 
        p.Nombre_P, 
        p.Existencias, 
        p.Stock_Minimo
    FROM producto p
    WHERE p.Existencias < p.Stock_Minimo
";

$result_productos_bajos_existencia = mysqli_query($conexion, $sql_productos_bajos_existencia);
if (!$result_productos_bajos_existencia) {
    die("Error al obtener los productos con existencias bajas: " . mysqli_error($conexion));
}

// Clase PDF
class PDF extends FPDF
{
    // Cabecera de página
    function Header()
    {
        // Logo
        $this->Image('src/logo.png',10,6,30);
        // Arial bold 12
        $this->SetFont('Arial','B',12);
        // Título
        $this->Cell(0,10,'Reporte de Ventas',0,1,'C');
        // Fecha y rango del reporte
        $this->SetFont('Arial','I',10);
        $this->Cell(0,10,'Desde: ' . $_GET['fecha_inicio'] . ' Hasta: ' . $_GET['fecha_fin'],0,1,'C');
        // Salto de línea
        $this->Ln(10);
    }

    // Pie de página
    function Footer()
    {
        // Posición: a 1,5 cm del final
        $this->SetY(-15);
        // Arial italic 8
        $this->SetFont('Arial','I',8);
        // Número de página
        $this->Cell(0,10,'Página '.$this->PageNo().'/{nb}',0,0,'C');
    }
}

// Crear el PDF con orientación horizontal
$pdf = new PDF('L', 'mm', 'A4'); // 'L' para horizontal, 'P' para vertical
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','',10);

$current_folio = '';
$total_venta_ganancia = 0;
$ganancia_total_periodo = 0;

while ($row = mysqli_fetch_assoc($result_ventas)) {
    if ($current_folio != $row['Folio_Venta']) {
        if ($current_folio != '') {
            // Mostrar total ganancia para la venta anterior
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(0, 10, 'Total Ganancia para la venta: $' . number_format($total_venta_ganancia, 2), 0, 1, 'R');
            $pdf->Ln(10); // Espacio entre ventas
            $pdf->SetFont('Arial', '', 10);
            $total_venta_ganancia = 0;
        }
        $current_folio = $row['Folio_Venta'];
        
        // Encabezado de la venta
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'Venta: '. $row['Folio_Venta'], 0, 1);
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, 'Fecha: ' . $row['Fecha'], 0, 1);
        $pdf->Cell(0, 10, 'Usuario: ' . $row['Usuario'], 0, 1);
        // Mostrar estatus de la venta
        $pdf->Cell(0, 10, 'Estatus: ' . ($row['Estatus'] == 1 ? 'Activa' : 'Dada de baja'), 0, 1);
        $pdf->Ln(5);
        
        // Encabezados de productos
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(80, 10, 'Producto', 1);
        $pdf->Cell(20, 10, 'Cantidad', 1, 0, 'R');
        $pdf->Cell(30, 10, 'Precio Compra', 1, 0, 'R');
        $pdf->Cell(40, 10, 'Precio Venta', 1, 0, 'R');
        $pdf->Cell(40, 10, 'Existencias Antes', 1, 0, 'R');
        $pdf->Cell(40, 10, 'Existencias Despues', 1, 1, 'R');
    }

    // Detalles de cada producto
    $pdf->SetFont('Arial', '', 10);
    $precio_compra = $row['Precio_Compra'];
    $precio_venta = $row['Precio_Venta'];
    $cantidad = $row['Cantidad'];
    $ganancia = ($precio_venta - $precio_compra) * $cantidad;
    $total_venta_ganancia += $ganancia;
    $ganancia_total_periodo += $ganancia;

    // Calcular existencias antes sumando la cantidad vendida a las existencias actuales
    $existencias_antes = $row['Existencias_Despues'] + $cantidad;

    $pdf->Cell(80, 10, $row['Producto'], 1);
    $pdf->Cell(20, 10, $cantidad, 1, 0, 'R');
    $pdf->Cell(30, 10, '$' . number_format($precio_compra, 2), 1, 0, 'R');
    $pdf->Cell(40, 10, '$' . number_format($precio_venta, 2), 1, 0, 'R');
    $pdf->Cell(40, 10, $existencias_antes, 1, 0, 'R');
    $pdf->Cell(40, 10, $row['Existencias_Despues'], 1, 1, 'R');
}

// Mostrar total ganancia para la última venta
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 10, 'Total Ganancia para la venta: $' . number_format($total_venta_ganancia, 2), 0, 1, 'R');
$pdf->Ln(10); // Espacio entre secciones
$pdf->SetFont('Arial', '', 10);

// Restar el monto de las ventas dadas de baja de las ganancias totales del periodo
$sql_ventas_dadas_de_baja = "
    SELECT SUM(dv.Total_Pagar) AS Total_Ventas_Dadas_De_Baja
    FROM venta v
    INNER JOIN detalle_venta dv ON v.Folio_Venta = dv.Folio_Venta
    WHERE v.Fecha BETWEEN '$fecha_inicio' AND '$fecha_fin' AND v.Estatus = 0
";

$result_ventas_dadas_de_baja = mysqli_query($conexion, $sql_ventas_dadas_de_baja);
if (!$result_ventas_dadas_de_baja) {
    die("Error al obtener el monto de las ventas dadas de baja: " . mysqli_error($conexion));
}

$row_ventas_dadas_de_baja = mysqli_fetch_assoc($result_ventas_dadas_de_baja);
$ventas_dadas_de_baja = $row_ventas_dadas_de_baja['Total_Ventas_Dadas_De_Baja'];


$ganancia_total_periodo -= $ventas_dadas_de_baja;

// Sección de estadísticas adicionales
$pdf->AddPage(); // Nueva página para estadísticas adicionales
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 10, 'Estadísticas Adicionales', 0, 1, 'C');
$pdf->Ln(10); // Espacio

// Producto más vendido
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Producto Mas Vendido:', 0, 1);
$pdf->SetFont('Arial', '', 12);
if ($producto_mas_vendido) {
    $pdf->Cell(0, 10, $producto_mas_vendido['Producto'] . ' - Cantidad Vendida: ' . $producto_mas_vendido['Total_Vendido'], 0, 1);
} else {
    $pdf->Cell(0, 10, 'No hay ventas en este periodo.', 0, 1);
}
$pdf->Ln(10); // Espacio

// Marcas más vendidas
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Marcas Mas Vendidas:', 0, 1);
$pdf->SetFont('Arial', '', 12);
if (mysqli_num_rows($result_marcas_mas_vendidas) > 0) {
    while ($row_marca = mysqli_fetch_assoc($result_marcas_mas_vendidas)) {
        $pdf->Cell(0, 10, $row_marca['Marca'] . ' - Cantidad Vendida: ' . $row_marca['Total_Vendido'], 0, 1);
    }
} else {
    $pdf->Cell(0, 10, 'No hay ventas en este periodo.', 0, 1);
}
$pdf->Ln(10); // Espacio

// Productos con existencias bajas
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Productos con Existencias Bajas:', 0, 1);
$pdf->SetFont('Arial', '', 12);
if (mysqli_num_rows($result_productos_bajos_existencia) > 0) {
    while ($row_existencias = mysqli_fetch_assoc($result_productos_bajos_existencia)) {
        $pdf->Cell(0, 10, $row_existencias['Nombre_P'] . ' - Existencias: ' . $row_existencias['Existencias'] . ' (Minimo: ' . $row_existencias['Stock_Minimo'] . ')', 0, 1);
    }
} else {
    $pdf->Cell(0, 10, 'No hay productos con existencias bajas.', 0, 1);
}
$pdf->Ln(10); // Espacio

// Ganancia total del periodo
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Ganancia Total del Periodo:', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, '$' . number_format($ganancia_total_periodo, 2), 0, 1);

// Cerrar el documento PDF y enviarlo al navegador
$pdf->Output();
?>


