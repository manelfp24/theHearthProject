<div class="container py-5 cart-container">
    <?php // encabezado de la sección de pago ?>
    <div class="text-center mb-5">
        <h1 class="section-main-title">Checkout</h1>
        <p class="section-description mx-auto">Where should we send your delicious food?</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <?php // formulario que envía los datos al controlador para procesar el pedido final ?>
            <form action="index.php?controller=Cart&action=processOrder" method="POST" class="needs-validation">
                
                <?php // bloque de información para la dirección de envío ?>
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-3 fw-bold">Delivery Address</h4>
                        
                        <?php 
                            // recuperamos datos de la sesión si el usuario ya está logueado para ahorrarle escribir
                            $defaultName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
                            $defaultAddress = isset($_SESSION['user_address']) ? $_SESSION['user_address'] : '';
                            $defaultPhone = isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : '';
                        ?>

                        <?php // campo para el nombre completo del destinatario ?>
                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="customer_name" class="form-control" value="<?= $defaultName ?>" required>
                        </div>

                        <?php // campo para la dirección física detallada ?>
                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="<?= $defaultAddress ?>" required placeholder="Street, number, floor...">
                        </div>

                        <?php // campo para el teléfono de contacto necesario para el reparto ?>
                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" value="<?= $defaultPhone ?>" required>
                        </div>
                    </div>
                </div>

                <?php // bloque para elegir el método de pago ?>
                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body p-4">
                        <h4 class="mb-3 fw-bold">Payment Method</h4>
                        
                        <?php // opción de pago online con tarjeta (seleccionada por defecto) ?>
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="payCard" value="card" checked>
                            <label class="form-check-label" for="payCard">
                                <i class="bi bi-credit-card me-2"></i>Credit Card (Pay Online)
                            </label>
                        </div>
                        
                        <?php // opción de pago en mano al recibir el pedido ?>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payCash" value="cash">
                            <label class="form-check-label" for="payCash">
                                <i class="bi bi-cash-coin me-2"></i>Cash on Delivery
                            </label>
                        </div>
                    </div>
                </div>

                <?php // botones de acción: volver atrás o confirmar la compra ?>
                <div class="d-flex justify-content-between align-items-center">
                    <a href="index.php?controller=Cart" class="text-decoration-none text-muted">
                        <i class="bi bi-arrow-left"></i> Back to Cart
                    </a>
                    
                    <?php // el botón de confirmar lanza el proceso de guardado en la base de datos ?>
                    <button type="submit" class="btn-hearth-add btn-checkout-custom px-5 py-3 border-0">
                        Confirm Order
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>