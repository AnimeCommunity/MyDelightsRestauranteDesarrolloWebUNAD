let index = 0;
        const images = document.querySelectorAll(".carousel img");
        setInterval(() => {
            images.forEach(img => img.style.transform = `translateX(-${index * 100}%)`);
            index = (index + 1) % images.length;
        }, 3000);


function mostrarInfo(platoId) {

    const infoPlato = document.getElementById(platoId);
            
    
    if (infoPlato.style.display === "none") {
        infoPlato.style.display = "block";
    } else {
        infoPlato.style.display = "none";
        }
    }
        