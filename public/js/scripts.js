/* file: public/js/scripts.js */

// función para controlar el desplazamiento lateral de las listas de productos
function scrollCuts(direction, trackId) {
    // si no se indica un id, usamos por defecto 'cutstrack' para mantener compatibilidad
    const id = trackId || 'cutsTrack';
    const track = document.getElementById(id);
    
    if (!track) return; 

    // definimos cuántos píxeles se moverá la lista en cada clic
    const scrollAmount = 350;

    if (direction === 'left') {
        // movemos el scroll hacia la izquierda
        track.scrollLeft -= scrollAmount;
    } else {
        // movemos el scroll hacia la derecha
        track.scrollLeft += scrollAmount;
    }
}

// lógica para el botón de volver arriba que aparece al hacer scroll
document.addEventListener('DOMContentLoaded', function() {
    
    const scrollBtn = document.getElementById('scrollTopBtn');
    let lastScrollTop = 0;

    if (scrollBtn) {
        
        window.addEventListener('scroll', function() {
            // obtenemos la posición actual del scroll en la ventana
            let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            
            // el botón aparece si el usuario sube y ya ha bajado más de 200 píxeles
            if (currentScroll < lastScrollTop && currentScroll > 200) {
                scrollBtn.classList.add('show');
            } else {
                // ocultamos el botón si baja o si está muy cerca del inicio
                scrollBtn.classList.remove('show');
            }
            
            // guardamos la posición para compararla en el siguiente movimiento
            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; 
        });

        // al hacer clic, subimos suavemente hasta el inicio de la página
        scrollBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});

// scripts generales para inicializar componentes y gestionar el carrito
document.addEventListener("DOMContentLoaded", function() {
    // activamos los tooltips de bootstrap para los iconos de información
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// lógica para los botones de más y menos en el selector de cantidad
window.updateQty = function(id, change) {
    const display = document.getElementById('qty-' + id);
    if (!display) return;

    let currentQty = parseInt(display.innerText);
    let newQty = currentQty + change;
    
    // evitamos que la cantidad pueda ser menor que cero
    if (newQty < 0) newQty = 0; 
    
    display.innerText = newQty;
};

// envía el producto seleccionado al servidor para añadirlo a la sesión del carrito
window.addToCart = function(id) {
    const display = document.getElementById('qty-' + id);
    if (!display) return;
    
    const quantity = parseInt(display.innerText);

    // avisamos si el usuario intenta añadir cero unidades
    if (quantity === 0) {
        alert("Please select at least 1 unit.");
        return;
    }

    const payload = {
        id: id,
        quantity: quantity
    };

    // usamos fetch para mandar los datos al controlador de php sin recargar
    fetch('index.php?controller=Cart&action=add', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // actualizamos el número del contador rojo en el menú al instante
            const badge = document.getElementById('cart-count');
            if (badge) {
                badge.innerText = data.newCount;
                // añadimos una pequeña animación para avisar visualmente del cambio
                badge.classList.add('animate__animated', 'animate__pulse');
            }

            // reseteamos el contador de la ficha a cero tras añadirlo
            display.innerText = "0";
            console.log("Cart updated:", data.newCount);
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
};

// lógica específica para la página de gestión del carrito
// actualiza la cantidad de un objeto ya existente y recarga para ver los totales
window.updateCartItem = function(id, change) {
    fetch('index.php?controller=Cart&action=update_quantity', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, change: change })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // recargamos para que php vuelva a calcular todos los precios y descuentos
            location.reload(); 
        } else {
            alert("Error updating cart: " + (data.message || "Unknown error"));
        }
    })
    .catch(error => console.error('Error:', error));
};

// elimina un producto completo del carrito tras confirmar con el usuario
window.removeFromCart = function(id) {
    if(!confirm("Are you sure you want to remove this item?")) return;

    fetch('index.php?controller=Cart&action=remove', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({ id: id })
    })
    .then(res => res.json())
    .then(data => {
        if(data.success) {
            location.reload(); 
        } else {
            alert("Could not remove item.");
        }
    })
    .catch(error => console.error('Error:', error));
};

// función de seguridad para verificar si el usuario está logueado antes de actuar
function checkAuthAndRedirect() {
    // comprobamos la variable global que definimos con php en el navbar
    if (typeof window.isUserLoggedIn !== 'undefined' && window.isUserLoggedIn === true) {
        return true; // el usuario está dentro, permitimos la acción
    }

    // si no hay sesión, mostramos un aviso y ofrecemos ir al login
    const wantsToLogin = confirm("You must be logged in to order. Would you like to log in now?");

    if (wantsToLogin) {
        window.location.href = 'index.php?controller=User&action=login';
    }

    // devolvemos falso para anular el clic original del enlace
    return false;
}

// procesa la aplicación de un cupón de descuento en el carrito
function applyCoupon() {
    const codeInput = document.getElementById('couponCode');
    const messageBox = document.getElementById('couponMessage');
    const code = codeInput.value;

    if (!code) return;

    fetch('index.php?controller=Cart&action=applyCoupon', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({ code: code })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // recargamos para que se apliquen los nuevos cálculos de forma segura en el servidor
            window.location.reload(); 
        } else {
            // si el cupón falla, mostramos el error y lo borramos a los 3 segundos
            messageBox.textContent = data.message;
            messageBox.style.display = 'block';
            
            setTimeout(() => {
                messageBox.textContent = '';
            }, 3000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        messageBox.textContent = "System error. Try again.";
    });
}