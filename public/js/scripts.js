/* File: public/js/scripts.js */

/* Updated Scroll Function */
function scrollCuts(direction, trackId) {
    // If no ID is provided, default to the main one (backwards compatibility)
    const id = trackId || 'cutsTrack';
    const track = document.getElementById(id);
    
    if (!track) return; 

    const scrollAmount = 350;

    if (direction === 'left') {
        track.scrollLeft -= scrollAmount;
    } else {
        track.scrollLeft += scrollAmount;
    }
}

/* --- FECHA DE VOLVER ARRIBA  --- */
document.addEventListener('DOMContentLoaded', function() {
    
    const scrollBtn = document.getElementById('scrollTopBtn');
    let lastScrollTop = 0;

    if (scrollBtn) {
        
        window.addEventListener('scroll', function() {
            // Get current scroll position
            let currentScroll = window.pageYOffset || document.documentElement.scrollTop;
            
            // Logic:
            // 1. Is the user scrolling UP? (currentScroll < lastScrollTop)
            // 2. Are they past the very top area? (currentScroll > 200) - prevents it showing instantly at the banner
            if (currentScroll < lastScrollTop && currentScroll > 200) {
                scrollBtn.classList.add('show');
            } else {
                // User is scrolling DOWN or is at the very top -> Hide button
                scrollBtn.classList.remove('show');
            }
            
            // Update last scroll position for the next check
            lastScrollTop = currentScroll <= 0 ? 0 : currentScroll; 
        });

        // Click event remains the same
        scrollBtn.addEventListener('click', function() {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
    }
});

/**
 * GENERAL SCRIPTS
 * Handles Tooltips, Quantity inputs, and Cart interactions.
 */

document.addEventListener("DOMContentLoaded", function() {
    // 1. Initialize Bootstrap Tooltips (For the "i" icon)
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
});

// 2. Quantity Logic (+ and -)
// We attach it to window so the HTML onclick="" can find it
window.updateQty = function(id, change) {
    const display = document.getElementById('qty-' + id);
    if (!display) return;

    let currentQty = parseInt(display.innerText);
    let newQty = currentQty + change;
    
    if (newQty < 0) newQty = 0; // Prevent negatives
    
    display.innerText = newQty;
};

// 3. Add to Cart Logic (AJAX)
window.addToCart = function(id) {
    const display = document.getElementById('qty-' + id);
    if (!display) return;
    
    const quantity = parseInt(display.innerText);

    if (quantity === 0) {
        alert("Please select at least 1 unit.");
        return;
    }

    // Prepare data
    const payload = {
        id: id,
        quantity: quantity
    };

    // Send to PHP Controller
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
            // --- THE "AUTO-UPDATE" MAGIC ---
            // This line updates the Red Badge instantly without reload
            const badge = document.getElementById('cart-count');
            if (badge) {
                badge.innerText = data.newCount;
                
                // Optional: Add a small "bump" animation to the badge
                badge.classList.add('animate__animated', 'animate__pulse');
            }

            // Reset the quantity pill to 0
            display.innerText = "0";

            // Visual Confirmation
            // You can replace this alert with a Toast notification later
            console.log("Cart updated:", data.newCount);
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
};

/* --- CART PAGE LOGIC --- */

// 1. Update Quantity (+1 or -1)
// This function forces a page reload to update the Grand Total
window.updateCartItem = function(id, change) {
    console.log("Updating item:", id, "Change:", change); // Debugging line

    fetch('index.php?controller=Cart&action=update_quantity', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, change: change })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload(); // Reload to see new prices
        } else {
            alert("Error updating cart: " + (data.message || "Unknown error"));
        }
    })
    .catch(error => console.error('Error:', error));
};

// 2. Remove Entire Item
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

/**
 * Checks if user is logged in.
 * If NOT: shows confirmation popup and redirects to login.
 * If YES: returns true (allows action to proceed).
 */
function checkAuthAndRedirect() {
    // 1. Check the variable defined in PHP
    if (typeof window.isUserLoggedIn !== 'undefined' && window.isUserLoggedIn === true) {
        return true; // User is logged in, allow the click!
    }

    // 2. User is NOT logged in. Show the browser popup.
    const wantsToLogin = confirm("You must be logged in to order. Would you like to log in now?");

    if (wantsToLogin) {
        // Redirect to your login controller action
        window.location.href = 'index.php?controller=User&action=login';
    }

    // 3. Return false to cancel the original click (don't open cart/menu)
    return false;
}
//USAR CUPONES DESCUENTO
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
            // Reload page to show the "Applied" state and updated calculations securely
            window.location.reload(); 
        } else {
            // Show error message
            messageBox.textContent = data.message;
            messageBox.style.display = 'block';
            
            // Clear message after 3 seconds
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

