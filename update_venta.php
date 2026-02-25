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

// Leer los datos enviados por AJAX
$input = json_decode(file_get_contents('php://input'), true);

$folio = $input['Folio_Venta'];
$fecha = $input['Fecha'];
$idUsuario = $input['Id_Usuario'];
$totalPagar = $input['Total_Pagar'];

// Actualizar la venta
$sql = "UPDATE venta SET Fecha = ?, Id_Usuario = ?, Total_Pagar = ? WHERE Folio_Venta = ?";
$stmt = $pdo->prepare($sql);
$success = $stmt->execute([$fecha, $idUsuario, $totalPagar, $folio]);

$response = ['success' => $success];
header('Content-Type: application/json');
echo json_encode($response);
?>
