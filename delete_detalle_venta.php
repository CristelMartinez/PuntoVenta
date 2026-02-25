<?php
if (isset($_GET['id'])) {
    $detalleId = intval($_GET['id']);

    // Conectar a la base de datos
    $host = 'localhost';
    $db = 'projecto';
    $user = 'root';
    $pass = '';

    $dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ];

    try {
        $pdo = new PDO($dsn, $user, $pass, $options);
    } catch (PDOException $e) {
        die('Conexión fallida: ' . $e->getMessage());
    }

    // Eliminar el detalle de la venta
    $sql = "DELETE FROM detalle_venta WHERE Id_Detalle_Venta = ?";
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([$detalleId]);

    $response = ['success' => $success];
    header('Content-Type: application/json');
    echo json_encode($response);
}
?>
