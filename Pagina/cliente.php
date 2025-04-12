<?php
session_start();
include 'includes/db.php';


if (!isset($_SESSION['carrito'])) {
    $_SESSION['carrito'] = [];
}

// agregar
if (isset($_POST['agregar_al_carrito'])) {
    $item_id = $_POST['item_id'];
    $tipo = $_POST['tipo']; 
    $cantidad = $_POST['cantidad'] ?? 1;

    $key = $tipo . '_' . $item_id;

    if (!isset($_SESSION['carrito'][$key])) {
        $_SESSION['carrito'][$key] = $cantidad;
    } else {
        $_SESSION['carrito'][$key] += $cantidad;
    }

    header("Location: cliente.php");
    exit();
}

// eliminar
if (isset($_GET['eliminar'])) {
    $key = $_GET['eliminar'];
    unset($_SESSION['carrito'][$key]);
    header("Location: cliente.php");
    exit();
}

// leer pdtos
$productos = $conn->query("SELECT * FROM productos");
$productos_array = [];
while ($row = $productos->fetch_assoc()) {
    $productos_array[] = $row;
}

// leer mns
$menus = $conn->query("SELECT * FROM menus");
$menus_array = [];
while ($row = $menus->fetch_assoc()) {
    $menus_array[] = $row;
}

// actualizar tipo de user
if (isset($_SESSION['user_id'])) {
    $cliente_id = $_SESSION['user_id'];
    $stmt = $conn->prepare("SELECT total_compras FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $cliente_id);
    $stmt->execute();
    $stmt->bind_result($total_compras);
    $stmt->fetch();
    $stmt->close();

  
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['finalizar_compra'])) {
        $total_compras++;
        $tipo_cliente = 'nuevo';

        
        if ($total_compras >= 10) {
            $tipo_cliente = 'permanente';
        } elseif ($total_compras >= 1) {
            $tipo_cliente = 'recurrente';
        }

        // guardar
        $tipo_entrega = $_POST['tipo_entrega'] ?? 'presencial';
        $recargo = $tipo_entrega === 'domicilio' ? 0.02 : 0;

        $detalle_items = [];
        $total_final = 0;

        foreach ($_SESSION['carrito'] as $key => $cant) {
            [$tipo, $id_item] = explode('_', $key);
            $tabla = $tipo === 'producto' ? 'productos' : 'menus';

            $stmt = $conn->prepare("SELECT nombre, precio FROM $tabla WHERE id = ?");
            $stmt->bind_param("i", $id_item);
            $stmt->execute();
            $stmt->bind_result($nombre, $precio);
            $stmt->fetch();
            $stmt->close();

            $precio_final = $precio * $cant * (1 + $recargo);
            $total_final += $precio_final;

            $detalle_items[] = "$tipo: $nombre x$cant";
        }

        $detalle = implode(', ', $detalle_items);

        
        $stmt = $conn->prepare("INSERT INTO pedidos (usuario_id, detalle, total, tipo_entrega, fecha) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("isds", $cliente_id, $detalle, $total_final, $tipo_entrega);
        $stmt->execute();
        $stmt->close();

        // desocupar carro
        $_SESSION['carrito'] = [];
        $_SESSION['compra_realizada'] = true;
        header("Location: cliente.php");
        exit();




    }


}

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Vista Cliente</title>
    <link rel="stylesheet" href="css/perfil.css">
    <style>
        
    </style>
</head>

<body>
    <h1>¡Bienvenido! Compra tus productos o menús favoritos 🍽️</h1>

    <h2>🍴 Menús</h2>
    <a href="dashboard.php">← Volver al panel</a>
    <br>
    <a href="historial.php"><button>ver pedidos</button></a>
    <div class="productos">
        <?php foreach ($menus_array as $menu): ?>
            <div class="producto">
                <h3><?= htmlspecialchars($menu['nombre']) ?> (<?= htmlspecialchars($menu['tipo']) ?>)</h3>
                <p><?= htmlspecialchars($menu['descripcion']) ?></p>
                <?php if (!empty($menu['imagen'])): ?>
                    <img src="img/<?= htmlspecialchars($menu['imagen']) ?>" alt="Imagen del menú">
                <?php endif; ?>
                <p><strong>Precio:</strong> $<?= number_format($menu['precio'], 0, ',', '.') ?></p>
                <form method="POST">
                    <input type="hidden" name="item_id" value="<?= $menu['id'] ?>">
                    <input type="hidden" name="tipo" value="menu">
                    <label>Cantidad:</label>
                    <input type="number" name="cantidad" value="1" min="1">
                    <button type="submit" name="agregar_al_carrito">Agregar al carrito 🧾</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>🛍️ Productos</h2>
    <div class="productos">
        <?php foreach ($productos_array as $producto): ?>
            <div class="producto">
                <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
                <p><?= htmlspecialchars($producto['descripcion']) ?></p>
                <?php if (!empty($producto['imagen'])): ?>
                    <img src="img/<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen del producto">
                <?php endif; ?>
                <p><strong>Precio:</strong> $<?= number_format($producto['precio'], 0, ',', '.') ?></p>
                <form method="POST">
                    <input type="hidden" name="item_id" value="<?= $producto['id'] ?>">
                    <input type="hidden" name="tipo" value="producto">
                    <label>Cantidad:</label>
                    <input type="number" name="cantidad" value="1" min="1">
                    <button type="submit" name="agregar_al_carrito">Agregar al carrito 🛒</button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <h2>🛒 Carrito de Compras</h2>
    <div class="carrito">
        <?php if (empty($_SESSION['user_id'])): ?>
            <p>Tu carrito está vacío.</p>
        <?php else: ?>
            <ul>
                <?php
                $total = 0;
                foreach ($_SESSION['carrito'] as $key => $cant):
                    [$tipo, $id] = explode('_', $key);

                    if ($tipo === 'producto') {
                        $stmt = $conn->prepare("SELECT nombre, precio FROM productos WHERE id = ?");
                    } else {
                        $stmt = $conn->prepare("SELECT nombre, precio FROM menus WHERE id = ?");
                    }

                    $stmt->bind_param("i", $id);
                    $stmt->execute();
                    $stmt->bind_result($nombre, $precio);
                    $stmt->fetch();
                    $stmt->close();

                    $subtotal = $precio * $cant;
                    $total += $subtotal;
                    ?>
                    <li>
                        <?= htmlspecialchars($nombre) ?> x <?= $cant ?> = $<?= number_format($subtotal, 0, ',', '.') ?>
                        <a href="?eliminar=<?= $key ?>" onclick="return confirm('¿Eliminar del carrito?')">❌</a>
                    </li>
                <?php endforeach; ?>

                <?php
                $descuento = 0;
                $tipo_cliente = 'nuevo';
                if (isset($_SESSION['user_id'])) {
                    $cliente_id = $_SESSION['user_id'];
                    $stmt = $conn->prepare("SELECT tipo_cliente FROM usuarios WHERE id = ?");
                    $stmt->bind_param("i", $cliente_id);
                    $stmt->execute();
                    $stmt->bind_result($tipo_cliente);
                    $stmt->fetch();
                    $stmt->close();
                }

                if ($tipo_cliente == 'nuevo') {
                    if ($total >= 250000) {
                        $descuento = 0.02;
                    }
                } elseif ($tipo_cliente == 'recurrente') {
                    $descuento = 0.02;
                    if ($total >= 200000) {
                        $descuento += 0.04;
                    }
                } elseif ($tipo_cliente == 'permanente') {
                    $descuento = 0.04;
                    if ($total >= 150000) {
                        $descuento += 0.06;
                    }
                }

                $monto_descuento = $total * $descuento;
                $total_final = $total - $monto_descuento;
                ?>
            </ul>

            <form method="POST">
                <label>Tipo de entrega:</label>
                <select name="tipo_entrega">
                    <option value="presencial">Presencial</option>
                    <option value="domicilio">Domicilio (+2%)</option>
                </select>

                <p><strong>Subtotal:</strong> $<?= number_format($total, 0, ',', '.') ?></p>
                <p><strong>Descuento (<?= $tipo_cliente ?>):</strong> -$<?= number_format($monto_descuento, 0, ',', '.') ?>
                    (<?= $descuento * 100 ?>%)</p>
                <p><strong>Total a pagar:</strong> $<?= number_format($total_final, 0, ',', '.') ?></p>
                <input type="hidden" name="finalizar_compra" value="1">
                <button type="submit">Finalizar compra ✅</button>
            </form>
        <?php endif; ?>
    </div>
</body>
<?php if (isset($_SESSION['compra_realizada']) && $_SESSION['compra_realizada']): ?>
    <script>
        alert("¡Compra realizada con éxito! 🎉");
    </script>
    <?php unset($_SESSION['compra_realizada']); ?>
<?php endif; ?>


</html>