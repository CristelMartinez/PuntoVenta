<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario ya está autenticado
if (isset($_SESSION['usuario'])) {
    header('Location: index.php'); // Redirigir al menú principal si ya está autenticado
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include('conexion/conectar-mysql.php'); // Incluye tu archivo de conexión

    $usuario = mysqli_real_escape_string($conexion, $_POST['usuario']);
    $password = mysqli_real_escape_string($conexion, $_POST['password']);

    // Consulta para obtener el usuario junto con su rol
    $sql = "SELECT U.*, R.nombre_r 
            FROM usuarios U 
            LEFT JOIN rol R ON U.rol = R.id_rol 
            WHERE U.usuario = '$usuario' AND U.password = '$password' AND U.estatus = '1'";

    $result = mysqli_query($conexion, $sql);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['usuario'] = $user['Usuario'];
        $_SESSION['rol'] = $user['nombre_r']; // Guardar el rol en la sesión
        header('Location: index.php'); // Redirigir al menú principal
    } else {
        $error = "Usuario o contraseña incorrectos";
    }

    mysqli_close($conexion);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="text-center mt-5">Inicio de Sesión</h2>
                <form method="POST" action="login.php">
                    <div class="form-group">
                        <label for="usuario">Usuario:</label>
                        <input type="text" id="usuario" name="usuario" class="form-control" required>
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" id="password" name="password" class="form-control" required>
                    </div>
                    <?php if (isset($error)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error; ?>
                        </div>
                    <?php endif; ?>
                    <button type="submit" class="btn btn-primary btn-block">Iniciar Sesión</button>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
