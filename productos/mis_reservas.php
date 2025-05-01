<?php
include '../includes/auth.php';
include '../includes/db.php';

$usuario_id = $_SESSION['user_id'];

// eliminar
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM reservas WHERE id = $id AND usuario_id = $usuario_id");
    header("Location: mis_reservas.php");
    exit();
}

// editar
if (isset($_POST['editar'])) {
    $id = intval($_POST['reserva_id']);
    $nombre = $_POST['nombre'];
    $fecha = $_POST['fecha'];
    $personas = $_POST['personas'];
    $precio = $_POST['precio'];
    $tipo = $_POST['tipo'];

    $stmt = $conn->prepare("UPDATE reservas SET nombre=?, fecha=?, personas=?, tipo=?, precio=? WHERE id=? AND usuario_id=?");
    $stmt->bind_param("ssisdii", $nombre, $fecha, $personas, $tipo, $precio, $id, $usuario_id);
    $stmt->execute();
    header("Location: mis_reservas.php");
    exit();
}

$result = $conn->query("SELECT * FROM reservas WHERE usuario_id = $usuario_id");
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Mis Reservas</title>
    <link rel="stylesheet" href="../css/misreservas.css">
    <link rel="icon" href="../img/favicon.png" type="image/png">
</head>

<body>

    <h1>Mis Reservas</h1>

    <a href="../login/dashboard.php"><button>Regresar</button></a>

    <?php while ($reserva = $result->fetch_assoc()): ?>
        <div class="reserva">
            <strong><?= htmlspecialchars($reserva['nombre']) ?></strong><br>
            Fecha: <?= htmlspecialchars($reserva['fecha']) ?><br>
            Personas: <?= htmlspecialchars($reserva['personas']) ?><br>
            Tipo: <?= htmlspecialchars($reserva['tipo']) ?><br>
            Precio: <?= htmlspecialchars($reserva['precio']) ?><br>

            <a href="?eliminar=<?= $reserva['id'] ?>" onclick="return confirm('¿Eliminar esta reserva?')">Eliminar</a>
            <button onclick="editarReserva(
                <?= $reserva['id'] ?>,
                '<?= addslashes($reserva['nombre']) ?>',
                '<?= $reserva['fecha'] ?>',
                <?= $reserva['personas'] ?>,
                '<?= addslashes($reserva['tipo']) ?>',
                '<?= addslashes($reserva['precio']) ?>'
            )">Editar</button>
        </div>
    <?php endwhile; ?>


    <!-- form edi-->
    <div id="editarForm" style="display:none; margin-top:30px;">
        <h2>Editar Reserva</h2>
        <form method="POST">
            <input type="hidden" name="editar" value="1">
            <input type="hidden" name="reserva_id" id="editar_id">

            <label>Nombre:</label>
            <input type="text" name="nombre" id="editar_nombre" required>

            <label>Fecha:</label>
            <input type="date" name="fecha" id="editar_fecha" required>

            <label>Personas:</label>
            <input type="number" name="personas" id="editar_personas" required>

            <label>Tipo:</label>
            <select name="tipo" id="editar_tipo" required>
                <option value="mesa">Mesa</option>
                <option value="banquete">Banquete</option>
                <option value="bufete">Bufete</option>
                <option value="evento familiar">Evento Familiar</option>
                <option value="otro">Otro</option>
            </select>
            <label>Precio:</label>
            <input type="number" name="precio" id="editar_precio" required>


            <button type="submit">Guardar cambios</button>
            <button type="button"
                onclick="document.getElementById('editarForm').style.display='none';">Cancelar</button>
        </form>
    </div>

    <script>
        function editarReserva(id, nombre, fecha, personas, tipo, precio) {
            document.getElementById('editar_id').value = id;
            document.getElementById('editar_nombre').value = nombre;
            document.getElementById('editar_fecha').value = fecha;
            document.getElementById('editar_personas').value = personas;
            document.getElementById('editar_tipo').value = tipo;
            document.getElementById('editar_precio').value = precio;
            document.getElementById('editarForm').style.display = 'block';
            window.scrollTo(0, document.body.scrollHeight);
        }

    </script>

</body>

</html>