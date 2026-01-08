<?php
// iniciamos el contenedor principal de la página de éxito
?>
<div class="container success-container">
    
    <div class="success-icon-wrapper">
        ✓
    </div>

    <h1 class="section-main-title mb-3">Order Received</h1>
    
    <p class="section-description success-text">
        Thank you for dining with The Hearth. Your order has been sent to the kitchen and is being prepared with care.
    </p>

    <div class="order-box">
        <span class="order-label">Order Number</span>
        <span class="order-number">
            <?php 
            // recuperamos el id del pedido de la sesión para mostrarlo al usuario 
            ?>
            #<?= isset($_SESSION['last_order_id']) ? $_SESSION['last_order_id'] : '---' ?>
        </span>
    </div>

    <a href="index.php?controller=Product" class="btn-sweep-black">
        Return to Menu
    </a>

</div>