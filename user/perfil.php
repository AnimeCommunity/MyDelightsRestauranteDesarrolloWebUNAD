<?php
include '../includes/auth.php';
include '../includes/db.php';

$usuario_id = $_SESSION['user_id'];

// Obtener datos del usuario
$stmt = $conn->prepare("SELECT email, telefono FROM usuarios WHERE id = ?");
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();
$usuario = $result->fetch_assoc();

// Obtener perfil si ya existe
$stmt2 = $conn->prepare("SELECT * FROM perfiles WHERE usuario_id = ?");
$stmt2->bind_param("i", $usuario_id);
$stmt2->execute();
$result2 = $stmt2->get_result();
$perfil = $result2->fetch_assoc();

// Guardar cambios del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $sexo = $_POST['sexo'];
    $fecha_nacimiento = $_POST['fecha_nacimiento'];
    $direccion = $_POST['direccion'];
    $email = $_POST['email'];
    $telefono = $_POST['telefono'];

    // Actualizar o insertar perfil
    if ($perfil) {
        $stmt = $conn->prepare("UPDATE perfiles SET sexo=?, fecha_nacimiento=?, direccion=? WHERE usuario_id=?");
        $stmt->bind_param("sssi", $sexo, $fecha_nacimiento, $direccion, $usuario_id);
    } else {
        $stmt = $conn->prepare("INSERT INTO perfiles (usuario_id, sexo, fecha_nacimiento, direccion) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isss", $usuario_id, $sexo, $fecha_nacimiento, $direccion);
    }
    $stmt->execute();

    // Actualizar correo y teléfono en la tabla usuarios
    $stmt2 = $conn->prepare("UPDATE usuarios SET email = ?, telefono = ? WHERE id = ?");
    $stmt2->bind_param("ssi", $email, $telefono, $usuario_id);
    $stmt2->execute();

    header("Location: perfil.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="../css/perfil.css">
    <link rel="icon" href="../img/favicon.png" type="image/png">
</head>
<body>

<div class="perfil">
    <h2>Mi Perfil</h2>
    <form method="POST">
        <label>Email:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>" required>

        <label>Teléfono:</label>
        <input type="text" name="telefono" value="<?= htmlspecialchars($usuario['telefono']) ?>" required>

        <label>Sexo:</label>
        <select name="sexo">
            <option value="">-- Selecciona --</option>
            <option value="masculino" <?= ($perfil['sexo'] ?? '') === 'masculino' ? 'selected' : '' ?>>Masculino</option>
            <option value="femenino" <?= ($perfil['sexo'] ?? '') === 'femenino' ? 'selected' : '' ?>>Femenino</option>
            <option value="otro" <?= ($perfil['sexo'] ?? '') === 'otro' ? 'selected' : '' ?>>Otro</option>
        </select>

        <label>Fecha de nacimiento:</label>
        <input type="date" name="fecha_nacimiento" value="<?= $perfil['fecha_nacimiento'] ?? '' ?>">

        <label>Dirección:</label>
        <input type="text" name="direccion" value="<?= htmlspecialchars($perfil['direccion'] ?? '') ?>">

        <button type="submit">Guardar</button>
    </form>

    <a href="../login/dashboard.php">← Volver al panel</a>
</div>

</body>
</html>
