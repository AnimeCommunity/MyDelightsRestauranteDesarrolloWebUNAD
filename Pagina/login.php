<?php
include 'includes/db.php';
session_start();

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user = $_POST['usuario'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM usuarios WHERE usuario='$user' AND password='$pass'";
    $result = $conn->query($sql);

    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        $_SESSION['user_id'] = $usuario['id'];
        $_SESSION['rol'] = $usuario['rol'];
        header("Location: dashboard.php");
        exit();
    } else {
        $error = "⚠️ Credenciales incorrectas. Intenta de nuevo.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - My Delights</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <a href="index.php"><button>volver a la pagina anterior</button></a>
    <div class="login-container">
        <h2>Iniciar Sesión</h2>

        <?php if ($error): ?>
            <p class="error-msg"><?php echo $error; ?></p>
        <?php endif; ?>

        <form method="POST" id="loginForm">
            <label for="usuario">Usuario (Cédula o Código):</label>
            <input type="text" id="usuario" name="usuario" required>
            
            <label for="password">Contraseña:</label>
            <input type="password" id="password" name="password" required>
            
            <button type="submit">Login</button>
        </form>

        <div class="links">
            <a href="#">¿Olvidaste tu clave?</a>
            <a href="register.php">Registrarse</a>
        </div>
    </div>
</body>
</html>
