<?php
session_start();
include '../includes/db.php';


//consulta pa traer los pedidos
$query = "
    SELECT 
        p.id AS pedido_id,
        u.usuario AS nombre_usuario,
        p.detalle,
        p.total,
        p.tipo_entrega,
        p.fecha,
        pf.direccion
    FROM pedidos p
    JOIN usuarios u ON p.usuario_id = u.id
    LEFT JOIN perfiles pf ON u.id = pf.usuario_id
    ORDER BY p.fecha DESC
";

$result = $conn->query($query);

// eliminar
if (isset($_GET['eliminar'])) {
    $pedido_id = intval($_GET['eliminar']);
    
    
    $conn->query("DELETE FROM pedidos WHERE id = $pedido_id");


    header("Location: gestionar_pedidos.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestionar Pedidos</title>
    <link rel="stylesheet" href="../css/productos.css">
    <link rel="icon" href="../img/favicon.png" type="image/png">
</head>
<body>
    <h2>Lista de Pedidos</h2>
    <a href="../login/dashboard.php"><button>Regresar</button></a>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Usuario</th>
                <th>Detalle</th>
                <th>Total</th>
                <th>Entrega</th>
                <th>Fecha</th>
                <th>Dirección</th>
                <th>Acción</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['pedido_id'] ?></td>
                    <td><?= htmlspecialchars($row['nombre_usuario']) ?></td>
                    <td><?= htmlspecialchars($row['detalle']) ?></td>
                    <td>$<?= number_format($row['total'], 2) ?></td>
                    <td><?= ucfirst($row['tipo_entrega']) ?></td>
                    <td><?= $row['fecha'] ?></td>
                    <td>
                        <?= $row['tipo_entrega'] === 'domicilio' ? htmlspecialchars($row['direccion']) : '—' ?>
                    </td>
                    <td>
                        <a href="?eliminar=<?= $row['pedido_id'] ?>" class="btn-eliminar" onclick="return confirm('¿Eliminar este pedido?')">Eliminar</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
