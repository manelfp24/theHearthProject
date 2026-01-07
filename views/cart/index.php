<div class="container py-5 cart-container">

    <div class="text-center mb-5">
        <h1 class="section-main-title">Your Selection</h1>
        <p class="section-description mx-auto">Review your chosen dishes before sending them to the kitchen.</p>
    </div>

    <?php if (isset($hasPreviousOrder) && $hasPreviousOrder): ?>
        <div class="alert alert-light border d-flex justify-content-between align-items-center mb-4 shadow-sm">
            <div>
                <i class="bi bi-clock-history me-2" style="color: #CB171A;"></i>
                <span class="fw-bold text-dark">Hungry like last time?</span>
                <span class="text-muted d-none d-sm-inline"> - Add items from your previous order instantly.</span>
            </div>

            <a href="index.php?controller=Cart&action=repeatLastOrder" class="btn-hearth-repeat">
                <i class="bi bi-arrow-repeat"></i> Repeat Last Order
            </a>
        </div>
    <?php endif; ?>

    <?php if (empty($cartItems)): ?>

        <div class="text-center py-5">
            <h3 class="text-muted mb-4 cart-empty-msg">Your cart is currently empty.</h3>
            <a href="index.php?controller=Product" class="btn btn-outline-dark rounded-pill px-4 py-2 text-uppercase fw-bold">
                Return to Menu
            </a>
        </div>

    <?php else: ?>

        <div class="row justify-content-center">
            <div class="col-lg-10">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead>
                            <tr class="cart-table-head">
                                <th scope="col" class="cart-col-product">Product</th>
                                <th scope="col" class="cart-col-desc">Description</th>
                                <th scope="col" class="text-center">Price</th>
                                <th scope="col" class="text-center">QUANTITY</th>
                                <th scope="col" class="text-center">Total</th>
                                <th scope="col" class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cartItems as $item):
                                $p = $item['product'];
                                $qty = (int)$item['quantity'];
                                $price = (float)$p->getBasePrice();
                                $total = $price * $qty;
                            ?>
                                <tr id="cart-row-<?= $p->getProductId() ?>" class="cart-row">
                                    <td class="py-3">
                                        <div class="cart-img-box">
                                            <img src="<?= $p->getImage() ?>" alt="<?= $p->getName() ?>">
                                        </div>
                                    </td>

                                    <td>
                                        <h5 class="cart-product-title">
                                            <?= $p->getName() ?>
                                        </h5>
                                        <small class="cart-product-desc">
                                            <?= $p->getDescription() ?>
                                        </small>
                                    </td>

                                    <td class="text-center fw-bold">
                                        $<?= number_format($price, 2) ?>
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex justify-content-center">
                                            <div class="quantity-pill">
                                                <button class="qty-btn" onclick="updateCartItem(<?= $p->getProductId() ?>, -1)">-</button>
                                                <span class="qty-display" id="cart-qty-<?= $p->getProductId() ?>"><?= $qty ?></span>
                                                <button class="qty-btn" onclick="updateCartItem(<?= $p->getProductId() ?>, 1)">+</button>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center fw-bold text-danger">
                                        $<?= number_format($total, 2) ?>
                                    </td>

                                    <td class="text-end">
                                        <button class="btn btn-sm btn-link text-danger text-decoration-none"
                                            onclick="removeFromCart(<?= $p->getProductId() ?>)">
                                            REMOVE
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="row justify-content-center mt-5 mb-5">
            <div class="col-lg-10">
                <div class="d-flex flex-column flex-md-row justify-content-end align-items-center gap-4">

                    <div class="coupon-section">
                        <?php if (isset($_SESSION['applied_coupon'])): ?>
                            <div class="d-flex align-items-center gap-2 text-success">
                                <i class="bi bi-tag-fill"></i>
                                <span class="fw-bold">
                                    Code <?= htmlspecialchars($_SESSION['applied_coupon']['code']) ?> applied!
                                </span>
                                <a href="index.php?controller=Cart&action=removeCoupon" class="text-danger small text-decoration-underline ms-2">Remove</a>
                            </div>
                        <?php else: ?>
                            <div class="input-group">
                                <input type="text" id="couponCode" class="form-control" placeholder="Coupon Code" style="max-width: 150px;">
                                <button class="btn btn-outline-red" type="button" onclick="applyCoupon()">Apply</button>
                            </div>
                            <small id="couponMessage" class="text-danger position-absolute mt-1"></small>
                        <?php endif; ?>
                    </div>

                    <div class="text-end">
                        <span class="cart-total-label d-block text-muted">Subtotal: $<?= number_format($cartTotal, 2) ?></span>

                        <?php if (isset($_SESSION['applied_coupon'])): ?>
                            <span class="d-block text-success small">
                                Discount: -$<?= number_format($discountAmount, 2) ?>
                            </span>
                        <?php endif; ?>

                        <span class="cart-total-amount d-block fs-3 fw-bold" id="displayTotal">
                            $<?= number_format(isset($finalTotal) ? $finalTotal : $cartTotal, 2) ?>
                        </span>
                    </div>

                    <a href="index.php?controller=Cart&action=checkout" class="btn-hearth-add btn-checkout-custom text-decoration-none px-5 py-3">
                        Proceed to Checkout
                    </a>
                </div>
            </div>
        </div>

    <?php endif; ?>
</div>