<div class="container py-5">
    <div class="row">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="text-center mb-3">
                    <div class="profile-avatar">
                        <?= strtoupper(substr($user->name, 0, 1)) ?>
                    </div>
                    <h5 class="mt-3"><?= htmlspecialchars($user->name) ?></h5>
                    <p class="text-muted small"><?= htmlspecialchars($user->email) ?></p>
                </div>
                <hr>
                <div class="list-group list-group-flush">
                    <a href="#info" class="list-group-item list-group-item-action border-0">
                        <i class="bi bi-person me-2"></i> My Information
                    </a>
                    <a href="#orders" class="list-group-item list-group-item-action border-0">
                        <i class="bi bi-bag me-2"></i> My Orders
                    </a>
                    <a href="index.php?controller=User&action=logout" class="list-group-item list-group-item-action border-0 text-danger mt-3">
                        <i class="bi bi-box-arrow-right me-2"></i> Log Out
                    </a>
                </div>
            </div>
        </div>

        <div class="col-md-9">
            <div id="info" class="card border-0 shadow-sm p-4 mb-4">
                <h4 class="mb-4 profile-title">Personal Information</h4>

                <?php if (isset($_GET['success'])): ?>
                    <div class="alert alert-success py-2 small">Profile updated successfully!</div>
                <?php endif; ?>

                <form action="index.php?controller=User&action=update_profile" method="POST">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($user->name) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user->email) ?>" required>
                        </div>
                        <div class="col-12 text-end mt-4">
                            <button type="submit" class="btn btn-dark px-4">Save Changes</button>
                        </div>
                    </div>
                </form>
            </div>

            <div id="orders" class="card border-0 shadow-sm p-4">
                <h4 class="mb-4 profile-title">Recent Orders</h4>

                <?php if (empty($recentOrders)): ?>
                    <div class="alert alert-light border text-center py-4">
                        <i class="bi bi-cart-x fs-2"></i>
                        <p class="mb-0 mt-2">You haven't placed any orders yet.</p>
                    </div>
                <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Order #</th>
                                    <th>Date</th>
                                    <th>Total Amount</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentOrders as $order): ?>
                                    <tr>
                                        <td><strong>#<?= $order['order_id'] ?></strong></td>
                                        <td><?= date('d M Y', strtotime($order['order_date'])) ?></td>
                                        <td class="fw-bold">$<?= number_format($order['total_price'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-end mt-2">
                        <small class="text-muted">Showing your last 3 orders.</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>