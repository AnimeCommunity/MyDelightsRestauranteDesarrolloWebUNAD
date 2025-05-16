<?php
include '../includes/auth.php';
include '../includes/db.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear Reserva</title>
    <link rel="stylesheet" href="../css/reservas.css">
    <link rel="icon" href="../img/favicon.png" type="image/png">
</head>
<body>

<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nombre = $_POST['nombre'];
    $fecha = $_POST['fecha'];
    $fecha_actual = date("Y-m-d");

    // Validar que la fecha no sea anterior a la actual
    if ($fecha < $fecha_actual) {
        echo "<div class='mensaje error'>
                <h2>❌ Fecha inválida</h2>
                <p>No puedes reservar una fecha pasada.</p>
                <a class='boton-volver' href='../login/dashboard.php'>Volver al panel</a>
            </div>";
        exit();
    }
    $personas = $_POST['personas'];
    $tipo = $_POST['tipo'];
    $precio = $_POST['precio'];
    $usuario_id = $_SESSION['user_id'];

    $stmt = $conn->prepare("INSERT INTO reservas (nombre, fecha, personas, tipo,precio, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisii", $nombre, $fecha, $personas, $tipo, $precio, $usuario_id);
    
    if ($stmt->execute()): ?>
        <div class="mensaje exito">
            <h2>✅ Reserva realizada con éxito</h2>
            <p>Tu reserva ha sido registrada correctamente.</p>
            <a class="boton-volver" href="../login/dashboard.php">Volver al panel</a>
        </div>
    <?php else: ?>
        <div class="mensaje error">
            <h2>❌ Error al realizar la reserva</h2>
            <p><?= htmlspecialchars($conn->error) ?></p>
            <a class="boton-volver" href="../login/dashboard.php">Volver al panel</a>
        </div>
    <?php endif;
} else {
    header("Location: dashboard.php");
    exit();
}
?>

</body>
</html>
