<?php
if (isset($_GET['folio'])) {
    $folio = intval($_GET['folio']);

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

    // Consultar los detalles de la venta
    $sql = "SELECT * FROM detalle_venta WHERE Folio_Venta = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$folio]);
    $detalles = $stmt->fetchAll();

    header('Content-Type: application/json');
    echo json_encode($detalles);
}
?>
