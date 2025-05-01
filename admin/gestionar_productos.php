<?php
include '../includes/auth.php';
include '../includes/db.php';

if ($_SESSION['rol'] !== 'admin') {
    echo "Acceso denegado";
    exit();
}

// v la carpeta
$imgDir = '../img';
if (!is_dir($imgDir)) {
    mkdir($imgDir, 0777, true);
}

// eliminar
if (isset($_GET['eliminar'])) {
    $id = intval($_GET['eliminar']);
    $conn->query("DELETE FROM productos WHERE id = $id");
    header("Location: gestionar_productos.php");
    exit();
}

// ediar
if (isset($_POST['editar'])) {
    $id = intval($_POST['producto_id']);
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $tipo = $_POST['tipo'];
    $ingredientes = $_POST['ingredientes'];

    $imagen = $_POST['imagen_actual'];
    if (!empty($_FILES['imagen']['name'])) {
        $imagenNombre = basename($_FILES['imagen']['name']);
        $imagenRuta = "$imgDir/$imagenNombre";
        move_uploaded_file($_FILES['imagen']['tmp_name'], $imagenRuta);
        $imagen = "img/$imagenNombre";
    }

    $stmt = $conn->prepare("UPDATE productos SET nombre=?, descripcion=?, precio=?, tipo=?, ingredientes=?, imagen=? WHERE id=?");
    $stmt->bind_param("ssdsssi", $nombre, $descripcion, $precio, $tipo, $ingredientes, $imagen, $id);
    $stmt->execute();
    header("Location: gestionar_productos.php");
    exit();
}

// agregar
if (isset($_POST['agregar'])) {
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];
    $tipo = $_POST['tipo'];
    $ingredientes = $_POST['ingredientes'];

    $imagen = null;
    if (!empty($_FILES['imagen']['name'])) {
        $imagenNombre = basename($_FILES['imagen']['name']);
        $imagenRuta = "$imgDir/$imagenNombre";
        move_uploaded_file($_FILES['imagen']['tmp_name'], $imagenRuta);
        $imagen = "img/$imagenNombre";
    }

    $stmt = $conn->prepare("INSERT INTO productos (nombre, descripcion, precio, tipo, ingredientes, imagen) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("ssdsss", $nombre, $descripcion, $precio, $tipo, $ingredientes, $imagen);
    $stmt->execute();
}

// ver
$result = $conn->query("SELECT * FROM productos");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="../css/productos.css">
    <link rel="icon" href="../img/favicon.png" type="image/png">
</head>
<body>

<h1>Gestión de Productos</h1>

<a href="../login/dashboard.php"><button>Regresar</button></a>

<ul>
<?php while ($producto = $result->fetch_assoc()): ?>
    <li>
        <strong><?= htmlspecialchars($producto['nombre']) ?></strong>
        Tipo: <?= htmlspecialchars($producto['tipo']) ?><br>
        Descripción: <?= htmlspecialchars($producto['descripcion']) ?><br>
        Ingredientes: <?= htmlspecialchars($producto['ingredientes']) ?><br>
        Precio: $<?= number_format($producto['precio'], 2) ?><br>
        <?php if ($producto['imagen']): ?>
            <img src="../<?= htmlspecialchars($producto['imagen']) ?>" alt="Imagen del producto" style="max-width:200px;"><br>
        <?php endif; ?><br>
        <a href="?eliminar=<?= $producto['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este producto?')">Eliminar</a>
        <button onclick="editarProducto(
            <?= $producto['id'] ?>,
            '<?= htmlspecialchars($producto['nombre'], ENT_QUOTES) ?>',
            '<?= htmlspecialchars($producto['descripcion'], ENT_QUOTES) ?>',
            <?= $producto['precio'] ?>,
            '<?= htmlspecialchars($producto['tipo'], ENT_QUOTES) ?>',
            '<?= htmlspecialchars($producto['ingredientes'], ENT_QUOTES) ?>',
            '<?= htmlspecialchars($producto['imagen'], ENT_QUOTES) ?>'
        )">Editar</button>
    </li>
<?php endwhile; ?>
</ul>

<h2>Agregar Nuevo Producto</h2>
<form method="POST" enctype="multipart/form-data">
    <input type="hidden" name="agregar" value="1">
    <label>Nombre:</label>
    <input type="text" name="nombre" required>

    <label>Descripción:</label>
    <textarea name="descripcion" required></textarea>

    <label>Precio:</label>
    <input type="number" step="0.01" name="precio" required>

    <label>Tipo:</label>
    <select name="tipo" required>
        <option value="entrada">Entrada</option>
        <option value="sopa">Sopa</option>
        <option value="principio">Principio</option>
        <option value="carne">Carne</option>
        <option value="bebida">Bebida</option>
        <option value="postre">Postre</option>
    </select>

    <label>Ingredientes:</label>
    <textarea name="ingredientes" required></textarea>

    <label>Imagen:</label>
    <input type="file" name="imagen" accept="image/*" required>

    <button type="submit">Agregar</button>
</form>

<!-- form editar -->
<div id="editarForm">
    <h2>Editar Producto</h2>
    <form method="POST" enctype="multipart/form-data">
        <input type="hidden" name="editar" value="1">
        <input type="hidden" name="producto_id" id="editar_id">

        <label>Nombre:</label>
        <input type="text" name="nombre" id="editar_nombre" required>

        <label>Descripción:</label>
        <textarea name="descripcion" id="editar_descripcion" required></textarea>

        <label>Precio:</label>
        <input type="number" name="precio" step="0.01" id="editar_precio" required>

        <label>Tipo:</label>
        <select name="tipo" id="editar_tipo" required>
            <option value="entrada">Entrada</option>
            <option value="sopa">Sopa</option>
            <option value="principio">Principio</option>
            <option value="carne">Carne</option>
            <option value="bebida">Bebida</option>
            <option value="postre">Postre</option>
        </select>

        <label>Ingredientes:</label>
        <textarea name="ingredientes" id="editar_ingredientes" required></textarea>

        <label>Imagen:</label>
        <input type="file" name="imagen" accept="image/*">
        <input type="hidden" name="imagen_actual" id="editar_imagen">

        <button type="submit">Guardar Cambios</button>
        <button type="button" onclick="document.getElementById('editarForm').style.display='none';">Cancelar</button>
    </form>
</div>

<script>
function editarProducto(id, nombre, descripcion, precio, tipo, ingredientes, imagen) {
    document.getElementById('editar_id').value = id;
    document.getElementById('editar_nombre').value = nombre;
    document.getElementById('editar_descripcion').value = descripcion;
    document.getElementById('editar_precio').value = precio;
    document.getElementById('editar_tipo').value = tipo;
    document.getElementById('editar_ingredientes').value = ingredientes;
    document.getElementById('editar_imagen').value = imagen;
    document.getElementById('editarForm').style.display = 'block';
    window.scrollTo(0, document.body.scrollHeight);
}
</script>

</body>
</html>