<?php
include 'includes/header.php'; 


$loggedIn = isset($_SESSION['user_id']);

//mnus
$menus = $conn->query("SELECT * FROM menus")->fetch_all(MYSQLI_ASSOC);
$productos = $conn->query("SELECT * FROM productos")->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Delights - Restaurante</title>
    <link rel="stylesheet" href="css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script defer src="assets/js/script.js"></script>
    <link rel="icon" href="img/favicon.png" type="image/png">
</head>
<body>


<nav>
    <div class="logo">
        <a href="#inicio"><img src="img/logo.png" alt="My Delights"></a>
    </div>
    <button class="menu-toggle" onclick="toggleMenu()">☰</button> <!-- Botón para abrir el menú -->
    <ul class="menu">
        <li><a href="#inicio">Inicio</a></li>
        <li><a href="#destacados">Destacados</a></li>
        <li><a href="#nosotros">Nosotros</a></li>
        <li><a href="#menu">Menú</a></li>
        <?php if ($loggedIn): ?>
            <li><a href="./login/logout.php">Cerrar sesión</a></li>
            <li><a href="./login/dashboard.php">Dashboard</a></li>
        <?php else: ?>
            <li><a href="login/login.php">Acceder</a></li>
        <?php endif; ?>
    </ul>
</nav>

<section id="destacados" class="carousel-container">
    <div class="carousel">
        <img src="img/1.png" class="slide active" alt="Platillo 1">
        <img src="img/2.png" class="slide active" alt="Platillo 2">
        <img src="img/3.png" class="slide active" alt="Platillo 3">
        <button class="carousel-btn prev" onclick="moverSlide(-1)">&#10094;</button>
        <button class="carousel-btn next" onclick="moverSlide(1)">&#10095;</button>
    </div>
</section>


<section id="nosotros">
    <h2>Sobre Nosotros</h2>
    <p>Bienvenido a My Delights, un restaurante con la mejor selección de platillos gourmet...</p>
</section>

<section id="menu">
    <h2>Nuestro Menú</h2>

    <?php foreach ($menus as $menu): ?>
    <h3><?= htmlspecialchars($menu['nombre']) ?></h3>
    <div class="platos">
    <?php
    // leer dtos
    $menu_id = $menu['id'];
    $res = $conn->query("SELECT p.* FROM productos p
                         JOIN menu_productos mp ON p.id = mp.producto_id
                         WHERE mp.menu_id = $menu_id");

    while ($producto = $res->fetch_assoc()):
    ?>
        <div class="plato">
            <img src="<?= htmlspecialchars($producto['imagen']) ?>" alt="<?= htmlspecialchars($producto['nombre']) ?>" onclick="mostrarInfo('producto<?= $menu_id ?>_<?= $producto['id'] ?>')">
            <h3><?= htmlspecialchars($producto['nombre']) ?></h3>
            <div id="producto<?= $menu_id ?>_<?= $producto['id'] ?>" class="info-plato" style="display:none;">
                <p><?= htmlspecialchars($producto['descripcion']) ?></p>
                <span>$<?= number_format($producto['precio'], 0, ',', '.') ?></span>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<?php endforeach; ?>

</section>
<section id="servicios">
    <h2>Servicios Especiales</h2>
    <div class="servicio">
        <img src="img/7.png" alt="Banquete">
        <h3>Banquetes</h3>
        <p>Ofrecemos banquetes para todo tipo de eventos. Personaliza tu menú según tus gustos y necesidades.</p>
        <p><strong>Precio por persona:</strong> $50,000</p>
        <p>El precio varía según el número de personas.</p>
    </div>
    
    <div class="servicio">
        <img src="img/6.png" alt="Bufet">
        <h3>Bufet</h3>
        <p>Disfruta de un bufet variado con opciones deliciosas para todos los gustos. Ideal para eventos grandes.</p>
        <p><strong>Precio por persona:</strong> $40,000</p>
        <p>El precio varía según el número de personas.</p>
    </div>

    <div class="servicio">
        <img src="img/5.png" alt="Evento Familiar">
        <h3>Eventos Familiares</h3>
        <p>Celebra tu evento familiar con nuestros menús especiales, adecuados para toda la familia.</p>
        <p><strong>Precio por persona:</strong> $35,000</p>
        <p>El precio varía según el número de personas.</p>
    </div>

    <div class="servicio">
        <img src="img/8.png" alt="Evento Empresarial">
        <h3>Eventos Empresariales</h3>
        <p>Organiza tu evento corporativo con nosotros. Menús profesionales y opciones para coffee breaks.</p>
        <p><strong>Precio por persona:</strong> $60,000</p>
        <p>El precio varía según el número de personas.</p>
    </div>
</section>


<a href="https://api.whatsapp.com/send?phone=573028373471" class="btn-wsp" target="_blank">
    <i class="fa-brands fa-whatsapp"></i>
</a>

<footer>
    <p>&copy; 2025 My Delights - Todos los derechos reservados.</p>
</footer>

</body>
</html>
