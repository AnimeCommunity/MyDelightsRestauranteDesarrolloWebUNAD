<?php
include '../includes/db.php';

$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $usuario = $_POST['username'];
    $password = $_POST['password'];
    $email = $_POST['email'];
    $telefono = $_POST['phone'];

    
    $verificar = "SELECT * FROM usuarios WHERE usuario = '$usuario'";
    $resultado = $conn->query($verificar);

    if ($resultado->num_rows > 0) {
        $mensaje = "⚠️ El usuario ya está registrado.";
    } else {
        $sql = "INSERT INTO usuarios (usuario, password, rol, email, telefono) 
                VALUES ('$usuario', '$password', 'cliente', '$email', '$telefono')";
        if ($conn->query($sql)) {
            $mensaje = "✅ Registrado correctamente. Ahora puedes <a href='login.php'>iniciar sesión</a>.";
        } else {
            $mensaje = "❌ Error al registrar: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - My Delights</title>
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="icon" href="../img/favicon.png" type="image/png">
</head>
<body>
    <a href="logout.php"><button>Volver a la pagina anterior</button></a>
    <div class="login-container">
        <h2>Registrarse</h2>

        <?php if ($mensaje): ?>
            <p class="mensaje"><?php echo $mensaje; ?></p>
        <?php endif; ?>

        <form method="POST" id="registerForm">
            <label for="username">Usuario (Cédula):</label>
            <input type="text" id="username" name="username" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" minlength="8" required>

            <label for="email">Correo:</label>
            <input type="email" id="email" name="email" required>

            <label for="phone">Teléfono:</label>
            <input type="number" id="phone" name="phone" required>
            
            <button type="submit">Registrarse</button>
        </form>
        <div class="links">
            <a href="login.php">Iniciar sesión</a>
        </div>
    </div>
</body>
</html>
