<div class="container py-5 cart-container">
    <div class="text-center mb-5">
        <h1 class="section-main-title">Checkout</h1>
        <p class="section-description mx-auto">Where should we send your delicious food?</p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <form action="index.php?controller=Cart&action=processOrder" method="POST" class="needs-validation">
                
                <div class="card border-0 shadow-sm mb-4">
                    <div class="card-body p-4">
                        <h4 class="mb-3 fw-bold">Delivery Address</h4>
                        
                        <?php 
                            $defaultName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
                            $defaultAddress = isset($_SESSION['user_address']) ? $_SESSION['user_address'] : '';
                            $defaultPhone = isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : '';
                        ?>

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="customer_name" class="form-control" value="<?= $defaultName ?>" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Address</label>
                            <input type="text" name="address" class="form-control" value="<?= $defaultAddress ?>" required placeholder="Street, number, floor...">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number</label>
                            <input type="tel" name="phone" class="form-control" value="<?= $defaultPhone ?>" required>
                        </div>
                    </div>
                </div>

                <div class="card border-0 shadow-sm mb-5">
                    <div class="card-body p-4">
                        <h4 class="mb-3 fw-bold">Payment Method</h4>
                        
                        <div class="form-check mb-2">
                            <input class="form-check-input" type="radio" name="payment_method" id="payCard" value="card" checked>
                            <label class="form-check-label" for="payCard">
                                <i class="bi bi-credit-card me-2"></i>Credit Card (Pay Online)
                            </label>
                        </div>
                        
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="payment_method" id="payCash" value="cash">
                            <label class="form-check-label" for="payCash">
                                <i class="bi bi-cash-coin me-2"></i>Cash on Delivery
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <a href="index.php?controller=Cart" class="text-decoration-none text-muted">
                        <i class="bi bi-arrow-left"></i> Back to Cart
                    </a>
                    
                    <button type="submit" class="btn-hearth-add btn-checkout-custom px-5 py-3 border-0">
                        Confirm Order
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>