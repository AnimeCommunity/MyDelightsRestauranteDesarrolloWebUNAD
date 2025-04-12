<?php
include '../includes/auth.php';
include '../includes/db.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "Acceso denegado";
    exit();
}

// crear
if (isset($_POST['crear_menu'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $tipo = $_POST['tipo'];
    $precio = $_POST['precio'];


    $imagen_nombre = null;
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $imagen_tmp = $_FILES['imagen']['tmp_name'];
        $imagen_nombre = basename($_FILES['imagen']['name']);
        $ruta_destino = '../img/' . $imagen_nombre;
        move_uploaded_file($imagen_tmp, $ruta_destino);
    }

    $stmt = $conn->prepare("INSERT INTO menus (nombre, descripcion, tipo, imagen, precio) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssd", $nombre, $descripcion, $tipo, $imagen_nombre, $precio);
    $stmt->execute();
}

// eliminar
if (isset($_GET['eliminar'])) {
    $menu_id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM menus WHERE id = $menu_id");
    header("Location: gestionar_menu.php");
    exit();
}

// pasar los productos a menus
if (isset($_POST['asociar_productos'])) {
    $menu_id = intval($_POST['menu_id']);
    if (isset($_POST['productos'])) {
        foreach ($_POST['productos'] as $producto_id => $categoria) {
            if ($categoria != '') {
                $producto_id = intval($producto_id);

                $check = $conn->prepare("SELECT COUNT(*) FROM menu_productos WHERE menu_id = ? AND producto_id = ?");
                $check->bind_param("ii", $menu_id, $producto_id);
                $check->execute();
                $check->bind_result($count);
                $check->fetch();
                $check->close();

                if ($count == 0) {
                    $stmt = $conn->prepare("INSERT INTO menu_productos (menu_id, producto_id, categoria) VALUES (?, ?, ?)");
                    $stmt->bind_param("iis", $menu_id, $producto_id, $categoria);
                    $stmt->execute();
                }
            }
        }
    }
}

// cocnsultar menus
$menus = $conn->query("SELECT * FROM menus ORDER BY id DESC");

// consultar productos
$productos = $conn->query("SELECT * FROM productos");
$productos_array = [];
while ($p = $productos->fetch_assoc()) {
    $productos_array[] = $p;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Menús</title>
    <link rel="stylesheet" href="../css/productos.css">

</head>
<body>

<h1>Gestión de Menús</h1>
<a href="../dashboard.php"><button>Regresar</button></a>

<h2>Crear Nuevo Menú</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="crear_menu" value="1">

    <label>Nombre:</label>
    <input type="text" name="nombre" required>

    <label>Descripción:</label>
    <textarea name="descripcion" required></textarea>

    <label>Tipo de menú:</label>
    <select name="tipo" required>
        <option value="a_la_carta">A la carta</option>
        <option value="comida_corriente">Comida corriente</option>
    </select>

    <label>Imagen del menú:</label>
    <input type="file" name="imagen" accept="image/*">

    <label>Precio:</label>
    <input type="number" step="0.01" name="precio" required>

    <button type="submit">Crear Menú</button>
</form>

<?php while ($menu = $menus->fetch_assoc()): ?>
    <div class="menu">
        <h3><?= htmlspecialchars($menu['nombre']) ?> (<?= htmlspecialchars($menu['tipo']) ?>)</h3>
        <p><strong>Descripción:</strong> <?= nl2br(htmlspecialchars($menu['descripcion'])) ?></p>
        <?php if ($menu['imagen']): ?>
            <img src="../img/<?= htmlspecialchars($menu['imagen']) ?>" alt="Imagen del menú">
        <?php endif; ?>
        <br>
        <p><strong>Precio:</strong> <?= nl2br(htmlspecialchars($menu['precio'])) ?></p>
        <a href="?eliminar=<?= $menu['id'] ?>" onclick="return confirm('¿Eliminar menú?')">Eliminar</a>

        <h4>Asociar productos</h4>
        <form method="POST">
            <input type="hidden" name="asociar_productos" value="1">
            <input type="hidden" name="menu_id" value="<?= $menu['id'] ?>">

            <?php foreach ($productos_array as $prod): ?>
                <label>
                    <?= htmlspecialchars($prod['nombre']) ?>:
                    <select name="productos[<?= $prod['id'] ?>]">
                        <option value="">-- No asociar --</option>
                        <option value="sopa">Sopa</option>
                        <option value="principio">Principio</option>
                        <option value="carne">Carne</option>
                        <option value="entrada">Entrada</option>
                        <option value="bebida">Bebida</option>
                        <option value="postre">Postre</option>
                    </select>
                </label>
            <?php endforeach; ?>

            <button type="submit">Guardar asociaciones</button>
        </form>

        <h4>Productos asociados</h4>
        <ul>
        <?php
            $mid = $menu['id'];
            $res = $conn->query("SELECT p.nombre, mp.categoria 
                                 FROM menu_productos mp 
                                 JOIN productos p ON p.id = mp.producto_id 
                                 WHERE mp.menu_id = $mid");
            while ($row = $res->fetch_assoc()):
        ?>
            <li><strong><?= htmlspecialchars($row['categoria']) ?>:</strong> <?= htmlspecialchars($row['nombre']) ?></li>
        <?php endwhile; ?>
        </ul>
    </div>
<?php endwhile; ?>

</body>
</html>
