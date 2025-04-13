let index = 0;
const slides = document.querySelectorAll(".slide");

function mostrarSlide(n) {
    slides.forEach(slide => slide.classList.remove("active"));
    index = (n + slides.length) % slides.length;
    slides[index].classList.add("active");
}

function moverSlide(n) {
    mostrarSlide(index + n);
}

setInterval(() => moverSlide(1), 5000); 



function mostrarInfo(platoId) {

    const infoPlato = document.getElementById(platoId);
            
    
    if (infoPlato.style.display === "none") {
        infoPlato.style.display = "block";
    } else {
        infoPlato.style.display = "none";
        }
    }
        