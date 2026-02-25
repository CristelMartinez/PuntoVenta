<?php
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

// Consultar todas las ventas
$sql = "SELECT * FROM venta where estatus = 1";
$stmt = $pdo->query($sql);
$ventas = $stmt->fetchAll();

header('Content-Type: application/json');
echo json_encode($ventas);
?>
