<?php
include 'includes/auth.php';
include 'includes/db.php';

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRM - My Delights</title>
    <link rel="stylesheet" href="css/styles.css">
</head>

<body>
    <header>
        <h1>CRM - My Delights</h1>
        <a href="logout.php"><button>Cerrar sesión</button></a>
        <a href="index.php"><button>Tienda</button></a>
    </header>

    <main>
        <?php if ($_SESSION['rol'] === 'admin'): ?>
            <div id="adminContent" class="admin-only">

                <h2>Bienvenido Administrador</h2>
                <p>Gestión de pedidos, productos y clientes</p>
                <a href="admin/gestionar_reservas.php"><button>Gestionar reservas</button></a>
                <a href="admin/gestionar_productos.php"><button>Gestionar productos</button></a>
                <a href="admin/gestionar_menu.php"><button>Gestionar menus</button></a>
                <a href="admin/gestionar_pedidos.php"><button>Gestionar pedidos</button></a>
            </div>
        <?php else: ?>
            <div id="clientContent" class="client-only">
                <h2>Bienvenido Cliente</h2>
                <p>Aquí puedes ver tus pedidos y modificar tu información</p>
                <a href="mis_reservas.php"><button>Ver / Editar mis reservas</button></a>
                <a href="perfil.php"><button>Editar perfil</button></a>
                <a href="cliente.php"><button>Comprar</button></a>
                <a href="historial.php"><button>Ver pedidos</button></a>

                <h3>Haz una reserva</h3>
                <form method="POST" action="reservar.php" id="reservaForm" onsubmit="return validarYEnviar()">
                    <input type="text" name="nombre" placeholder="Nombre" required><br><br>
                    <input type="date" name="fecha" required><br><br>
                    <input type="number" name="personas" placeholder="Personas" id="personas" required><br><br>

                    <label for="tipo">Tipo de Reserva:</label><br>
                    <select name="tipo" id="tipo" required>
                        <option value="mesa">Mesa</option>
                        <option value="banquete">Banquete</option>
                        <option value="bufete">Bufete</option>
                        <option value="evento familiar">Evento Familiar</option>
                        <option value="evento empresarial">Evento Empresarial</option>
                        <option value="otro">Otro</option>
                    </select><br><br>
                    <input type="hidden" name="precio" id="precio">

                    <button type="submit">Reservar</button>
                    <button type="button" onclick="cotizarReserva()">Cotizar</button>
                </form>
                <!-- Aquí aparecerá la cotización -->
                <div id="resultadoCotizacion"
                    style="margin-top: 20px; margin-bottom: 40px; font-weight: bold; color: #2c3e50;"></div>

            </div>

        <?php endif; ?>


    </main>

</body>



<script>
    function cotizarReserva() {
        const tipo = document.getElementById('tipo').value;
        const personas = parseInt(document.getElementById('personas').value);
        let total = 0;

        if (tipo === 'mesa') {
            total = 30000;
        } else {
            switch (tipo) {
                case 'banquete':
                    total = personas * 50000;
                    break;
                case 'bufete':
                    total = personas * 40000;
                    break;
                case 'evento familiar':
                    total = personas * 35000;
                    break;
                case 'evento empresarial':
                    total = personas * 60000;
                    break;
                case 'otro':
                    total = 0;
                    break;
            }
        }

        document.getElementById('precio').value = total;

        const resultado = document.getElementById('resultadoCotizacion');
        if (!isNaN(total) && total > 0) {
            resultado.textContent = `Valor estimado de la reserva: $${total.toLocaleString('es-CO')}`;
        } else {
            resultado.textContent = "Por favor, selecciona un tipo válido y número de personas.";
        }

        return total > 0;
    }

    function validarYEnviar() {
        const esValido = cotizarReserva();
        if (!esValido) {
            alert("Debes seleccionar un tipo válido y número de personas antes de reservar.");
        }
        return esValido; 
    }
</script>




</html>