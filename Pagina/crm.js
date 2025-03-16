let tipoUser = localStorage.getItem("userType");

if (tipoUser === "admin") {
    document.getElementById("adminContent").style.display = "block";
            document.getElementById("clientContent").style.display = "none";
} else {
    document.getElementById("adminContent").style.display = "none";
    document.getElementById("clientContent").style.display = "block";
}

function logout() {
    localStorage.removeItem("userType");
    window.location.href = "formulario.html";
}

document.getElementById("cotizacionForm").addEventListener("submit", function(event) {
    event.preventDefault();
    calcularPrecio();
});

function calcularPrecio() {
    const tipoServicio = document.getElementById("tipoServicio").value;
    const numeroPersonas = document.getElementById("numeroPersonas").value;

            
    let precioBase = 0;
    switch (tipoServicio) {
        case "buffet":
            precioBase = 20;
            break;
        case "banquete":
            precioBase = 30;
            break;
        case "mesa":
            precioBase = 15;
            break;
         case "salon":
            precioBase = 50;
            break;
        }

            
    const precioTotal = precioBase * numeroPersonas;
    document.getElementById("precio").textContent = "$" + precioTotal.toFixed(3);
    document.getElementById("precioAproximado").style.display = "block";
}