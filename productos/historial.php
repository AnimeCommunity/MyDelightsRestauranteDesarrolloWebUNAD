<?php
include("../includes/db.php");


session_start();


$usuario_id = $_SESSION['user_id'];


$query = "
    SELECT 
        p.id AS pedido_id,
        p.detalle,
        p.total,
        p.tipo_entrega,
        p.fecha,
        pf.direccion
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN perfiles pf ON u.id = pf.usuario_id
    WHERE p.usuario_id = $usuario_id
    ORDER BY p.fecha DESC
";

$result = $conn->query($query);
if (isset($_GET['eliminar'])) {
    $pedido_id = $_GET['eliminar'];

    // eliminar
    $delete_query = "DELETE FROM pedidos WHERE id = $pedido_id AND usuario_id = $usuario_id";
    if ($conn->query($delete_query) === TRUE) {
        echo "Pedido eliminado con éxito.";
    } else {
        echo "Error al eliminar el pedido: " . $conn->error;
    }
    header("Location: historial.php"); 
    exit();
}



?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Pedidos</title>
    <link rel="stylesheet" href="../css/historial.css">
    <link rel="icon" href="../img/favicon.png" type="image/png">
</head>
<body>

<h1>Historial de Pedidos</h1>
<a class="volver-btn" href="../login/dashboard.php">Volver</a>
<?php if ($result->num_rows > 0): ?>
    <table border="1">
        <thead>
            <tr>
                <th>Pedido ID</th>
                <th>Detalle</th>
                <th>Total</th>
                <th>Tipo de Entrega</th>
                <th>Fecha</th>
                <th>Dirección (si es a domicilio)</th>
                <th>Eliminar</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($pedido = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo $pedido['pedido_id']; ?></td>
                    <td><?php echo $pedido['detalle']; ?></td>
                    <td><?php echo $pedido['total']; ?></td>
                    <td><?php echo $pedido['tipo_entrega']; ?></td>
                    <td><?php echo $pedido['fecha']; ?></td>
                    <td><?php echo $pedido['tipo_entrega'] == 'a domicilio' ? $pedido['direccion'] : 'N/A'; ?></td>
                    <td>
                        <a href="historial.php?eliminar=<?php echo $pedido['pedido_id']; ?>" onclick="return confirm('¿Estás seguro de que deseas eliminar este pedido?');">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>

        </tbody>
    </table>
<?php else: ?>
    <p>No tienes pedidos realizados aún.</p>
<?php endif; ?>

</body>
</html>
