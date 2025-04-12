<?php
include '../includes/auth.php';
include '../includes/db.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "Acceso denegado";
    exit();
}

// precio 
function calcularPrecio($tipo, $personas)
{
    switch ($tipo) {
        case 'mesa':
            return 30000;
        case 'banquete':
            return $personas * 50000;
        case 'bufete':
            return $personas * 40000;
        case 'evento familiar':
            return $personas * 35000;
        case 'evento empresarial':
            return $personas * 60000;
        default:
            return 0;
    }
}

// eliminar
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM reservas WHERE id = $id");
    header("Location: gestionar_reservas.php");
    exit();
}

// editar
if (isset($_POST['editar'])) {
    $id = intval($_POST['reserva_id']);
    $nombre = $_POST['nombre'];
    $fecha = $_POST['fecha'];
    $personas = $_POST['personas'];
    $tipo = $_POST['tipo'];
    $precio = calcularPrecio($tipo, $personas);

    $stmt = $conn->prepare("UPDATE reservas SET nombre=?, fecha=?, personas=?, tipo=?, precio=? WHERE id=?");
    $stmt->bind_param("ssisii", $nombre, $fecha, $personas, $tipo, $precio, $id);
    $stmt->execute();
    header("Location: gestionar_reservas.php");
    exit();
}

// agregar
if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $fecha = $_POST['fecha'];
    $personas = $_POST['personas'];
    $tipo = $_POST['tipo'];
    $usuario_id = $_SESSION['user_id'];
    $precio = calcularPrecio($tipo, $personas);

    $stmt = $conn->prepare("INSERT INTO reservas (nombre, fecha, personas, tipo, precio, usuario_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssisii", $nombre, $fecha, $personas, $tipo, $precio, $usuario_id);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Gestión de Reservas</title>
    <link rel="stylesheet" href="../css/greservas.css">
    <style>

    </style>
</head>

<body>

    <h1>Gestión de Reservas</h1>
    <a href="../dashboard.php"><button>Regresar</button></a>
    <ul>
        <?php
        $result = $conn->query("SELECT r.*, u.usuario AS username FROM reservas r JOIN usuarios u ON r.usuario_id = u.id");
        while ($reserva = $result->fetch_assoc()) {
            echo "<li>
        <strong>{$reserva['nombre']}</strong>
        Fecha: {$reserva['fecha']}<br>
        Personas: {$reserva['personas']}<br>
        Tipo: {$reserva['tipo']}<br>
        Precio: {$reserva['precio']}<br>
        Usuario: {$reserva['username']}<br><br>
        <a href='?eliminar={$reserva['id']}' onclick='return confirm(\"¿Seguro que deseas eliminar esta reserva?\")'>Eliminar</a>
        <button onclick='editarReserva({$reserva["id"]}, \"{$reserva["nombre"]}\", \"{$reserva["fecha"]}\", {$reserva["personas"]}, \"{$reserva["tipo"]}\")'>Editar</button>
    </li>";
        }
        ?>
    </ul>

    <!-- agregar -->
    <h2>Agregar Nueva Reserva</h2>
    <form method="POST">
        <input type="hidden" name="agregar" value="1">
        <label>Nombre:</label>
        <input type="text" name="nombre" required>

        <label>Fecha:</label>
        <input type="date" name="fecha" required>

        <label>Personas:</label>
        <input type="number" name="personas" required>

        <label>Tipo:</label>
        <select name="tipo" required>
            <option value="mesa">Mesa</option>
            <option value="banquete">Banquete</option>
            <option value="bufete">Bufete</option>
            <option value="evento familiar">Evento Familiar</option>
            <option value="otro">Otro</option>
        </select>
        <input type="hidden" name="precio" id="precioAgregar">
       

        
        <button type="submit">Agregar</button>
    </form>

    <!-- editar -->
    <div id="editarForm">
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
            <input type="hidden" name="precio" id="precioEditar">
        

            
            <button type="submit">Guardar Cambios</button>
            <button type="button"
                onclick="document.getElementById('editarForm').style.display='none';">Cancelar</button>
        </form>
    </div>

    <script>
        function editarReserva(id, nombre, fecha, personas, tipo) {
            document.getElementById('editar_id').value = id;
            document.getElementById('editar_nombre').value = nombre;
            document.getElementById('editar_fecha').value = fecha;
            document.getElementById('editar_personas').value = personas;
            document.getElementById('editar_tipo').value = tipo;
            document.getElementById('editarForm').style.display = 'block';
            window.scrollTo(0, document.body.scrollHeight);
        }

        function calcularPrecio(modo) {
            let tipo = document.getElementById(modo === 'agregar' ? 'tipo' : 'editar_tipo').value;
            let personas = parseInt(document.getElementById(modo === 'agregar' ? 'personas' : 'editar_personas').value);
            let precio = 0;

            switch (tipo) {
                case 'mesa': precio = 30000; break;
                case 'banquete': precio = personas * 50000; break;
                case 'bufete': precio = personas * 40000; break;
                case 'evento familiar': precio = personas * 35000; break;
                case 'evento empresarial': precio = personas * 60000; break;
                default: precio = 0;
            }

            if (modo === 'agregar') {
                document.getElementById('precioAgregar').value = precio;
                alert(`Precio estimado: $${precio.toLocaleString('es-CO')}`);
            } else {
                document.getElementById('precioEditar').value = precio;
                alert(`Precio estimado: $${precio.toLocaleString('es-CO')}`);
            }
        }


    </script>

</body>

</html>