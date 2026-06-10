// Inicializar Particles.js para el fondo
particlesJS("particles-js", {
    "particles": {
        "number": {
            "value": 80,
            "density": {
                "enable": true,
                "value_area": 800
            }
        },
        "color": {
            "value": "#66fcf1"
        },
        "shape": {
            "type": "circle",
            "stroke": {
                "width": 0,
                "color": "#000000"
            },
            "polygon": {
                "nb_sides": 5
            }
        },
        "opacity": {
            "value": 0.5,
            "random": false,
            "anim": {
                "enable": false,
                "speed": 1,
                "opacity_min": 0.1,
                "sync": false
            }
        },
        "size": {
            "value": 3,
            "random": true,
            "anim": {
                "enable": false,
                "speed": 40,
                "size_min": 0.1,
                "sync": false
            }
        },
        "line_linked": {
            "enable": true,
            "distance": 150,
            "color": "#45a29e",
            "opacity": 0.4,
            "width": 1
        },
        "move": {
            "enable": true,
            "speed": 2,
            "direction": "none",
            "random": false,
            "straight": false,
            "out_mode": "out",
            "bounce": false,
            "attract": {
                "enable": false,
                "rotateX": 600,
                "rotateY": 1200
            }
        }
    },
    "interactivity": {
        "detect_on": "canvas",
        "events": {
            "onhover": {
                "enable": true,
                "mode": "grab"
            },
            "onclick": {
                "enable": true,
                "mode": "push"
            },
            "resize": true
        },
        "modes": {
            "grab": {
                "distance": 140,
                "line_linked": {
                    "opacity": 1
                }
            },
            "bubble": {
                "distance": 400,
                "size": 40,
                "duration": 2,
                "opacity": 8,
                "speed": 3
            },
            "repulse": {
                "distance": 200,
                "duration": 0.4
            },
            "push": {
                "particles_nb": 4
            },
            "remove": {
                "particles_nb": 2
            }
        }
    },
    "retina_detect": true
});

// Vanilla JS Tilt Effect para las tarjetas
const cards = document.querySelectorAll('.tilt-card');

cards.forEach(card => {
    card.addEventListener('mousemove', handleMouseMove);
    card.addEventListener('mouseleave', handleMouseLeave);
});

function handleMouseMove(e) {
    const card = this;
    const cardRect = card.getBoundingClientRect();
    
    // Posición del mouse relativa a la tarjeta
    const x = e.clientX - cardRect.left;
    const y = e.clientY - cardRect.top;
    
    // Centro de la tarjeta
    const centerX = cardRect.width / 2;
    const centerY = cardRect.height / 2;
    
    // Calcular el grado de rotación (max 15 deg)
    const rotateX = ((y - centerY) / centerY) * -15;
    const rotateY = ((x - centerX) / centerX) * 15;
    
    card.style.transform = `perspective(1000px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02, 1.02, 1.02)`;
}

function handleMouseLeave(e) {
    const card = this;
    card.style.transform = `perspective(1000px) rotateX(0deg) rotateY(0deg) scale3d(1, 1, 1)`;
    card.style.transition = 'transform 0.5s ease';
    
    // Limpiar transición para que siga al mouse suavemente cuando entre de nuevo
    setTimeout(() => {
        if (!card.matches(':hover')) {
            card.style.transition = '';
        }
    }, 500);
}

// Agregar smooth transition cuando el mouse entra a la tarjeta
cards.forEach(card => {
    card.addEventListener('mouseenter', () => {
        card.style.transition = 'transform 0.1s ease';
    });
});
