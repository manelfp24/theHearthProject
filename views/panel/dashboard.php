<div class="container-fluid py-4">
    <div class="row">

        <?php // menú lateral de navegación para el administrador ?>
        <div class="col-md-2">
            <div class="list-group">
                <button class="list-group-item list-group-item-action menu-btn active" data-target="section-products">
                    <i class="bi bi-box-seam"></i> Products
                </button>
                <button class="list-group-item list-group-item-action menu-btn" data-target="section-orders">
                    <i class="bi bi-cart"></i> Orders
                </button>
                <button class="list-group-item list-group-item-action menu-btn" data-target="section-logs">
                    <i class="bi bi-journal-text"></i> Logs
                </button>
                <button class="list-group-item list-group-item-action menu-btn" data-target="section-users">
                    <i class="bi bi-people"></i> Users
                </button>
            </div>
        </div>

        <div class="col-md-10">

            <?php // sección para la gestión de productos (añadir, editar, borrar) ?>
            <div id="section-products" class="content-section">
                <h2 class="mb-4" style="font-family: 'Libre Baskerville', serif;">Products Management</h2>

                <div class="d-flex justify-content-between mb-3">
                    <input type="text" id="searchInput" class="form-control w-25" placeholder="Search by name...">
                    <button class="btn btn-dark" onclick="openCreateModal()">+ New Product</button>
                </div>

                <table class="table table-hover bg-white shadow-sm">
                    <thead class="table-dark">
                        <tr>
                            <th class="sortable" onclick="sortProducts('id')">ID</th>
                            <th class="sortable" onclick="sortProducts('name')">Name</th>
                            <th class="sortable" onclick="sortProducts('category')">Category</th>
                            <th class="sortable" onclick="sortProducts('price')">Price</th>
                            <th class="sortable" onclick="sortProducts('available')">Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="productsTableBody"></tbody>
                </table>
            </div>

            <?php // sección para el control de pedidos de clientes ?>
            <div id="section-orders" class="content-section d-none">
                <h2 class="mb-4" style="font-family: 'Libre Baskerville', serif;">Orders Management</h2>

                <div class="d-flex justify-content-between mb-3">
                    <input type="text" id="searchOrderInput" class="form-control w-25" placeholder="Search Order ID...">
                    <select class="form-select w-25" id="filterStatus" onchange="filterOrders()">
                        <option value="all">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="shipped">Shipped</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                    </select>
                </div>

                <table class="table table-hover bg-white shadow-sm">
                    <thead class="table-dark">
                        <tr>
                            <th class="sortable" onclick="sortOrders('id')">Order ID</th>
                            <th class="sortable" onclick="sortOrders('date')">Date</th>
                            <th class="sortable" onclick="sortOrders('user_id')">User ID</th>
                            <th class="sortable" onclick="sortOrders('total')">Total</th>
                            <th class="sortable" onclick="sortOrders('status')">Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="ordersTableBody"></tbody>
                </table>
            </div>

            <?php // historial de acciones administrativas (logs) ?>
            <div id="section-logs" class="content-section d-none">
                <h2 class="mb-4" style="font-family: 'Libre Baskerville', serif;">System Logs</h2>
                <p class="text-muted mb-4">Track administrative actions and system events.</p>

                <table class="table table-striped table-hover shadow-sm bg-white">
                    <thead class="table-dark">
                        <tr>
                            <th class="sortable" onclick="sortLogs('timestamp')">Time</th>
                            <th class="sortable" onclick="sortLogs('admin_name')">Admin User</th>
                            <th class="sortable" onclick="sortLogs('action')">Action</th>
                            <th class="sortable" onclick="sortLogs('affected_entity')">Target</th>
                        </tr>
                    </thead>
                    <tbody id="logsTableBody"></tbody>
                </table>
            </div>

            <?php // gestión de usuarios y asignación de roles de administrador ?>
            <div id="section-users" class="content-section d-none">
                <h2 class="mb-4" style="font-family: 'Libre Baskerville', serif;">User Management</h2>
                <p class="text-muted mb-4">View all registered users and manage their administrative roles.</p>

                <table class="table table-hover bg-white shadow-sm">
                    <thead class="table-dark">
                        <tr>
                            <th class="sortable" onclick="sortUsers('id')">User ID</th>
                            <th class="sortable" onclick="sortUsers('name')">Name</th>
                            <th class="sortable" onclick="sortUsers('email')">Email</th>
                            <th class="sortable" onclick="sortUsers('role')">Role</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody id="usersTableBody"></tbody>
                </table>
            </div>

        </div>
    </div>
</div>

<?php // modal para crear o editar productos ?>
<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalTitle">New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="productForm">
                    <input type="hidden" id="prodId">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" id="prodName" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" id="prodDesc" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" id="prodCategory" required>
                            <option value="Meats">Meats</option>
                            <option value="Seafood">Seafood</option>
                            <option value="Wines">Wines</option>
                            <option value="Spirits">Spirits</option>
                            <option value="Beverages">Beverages</option>
                            <option value="Sides">Sides</option>
                            <option value="Desserts">Desserts</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Price ($)</label>
                        <input type="number" step="0.01" class="form-control" id="prodPrice" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Image Filename</label>
                        <input type="text" class="form-control" id="prodImage" value="img/logo.svg">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-dark" onclick="saveProduct()">Save Product</button>
            </div>
        </div>
    </div>
</div>

<?php // modal para ver y actualizar el detalle de los pedidos ?>
<div class="modal fade" id="orderModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Order Details: #<span id="modalOrderId"></span></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <strong>Date:</strong> <span id="modalOrderDate"></span><br>
                        <strong>User ID:</strong> <span id="modalOrderUser"></span>
                    </div>
                    <div class="col-md-6 text-end">
                        <strong>Total:</strong> $<span id="modalOrderTotal" class="fs-4"></span>
                    </div>
                </div>

                <div class="mb-3 bg-light p-3 rounded">
                    <label class="form-label fw-bold">Update Status:</label>
                    <div class="d-flex gap-2">
                        <select id="modalOrderStatus" class="form-select">
                            <option value="pending">Pending</option>
                            <option value="shipped">Shipped</option>
                            <option value="delivered">Delivered</option>
                            <option value="cancelled">Cancelled</option>
                        </select>
                        <button class="btn btn-primary" onclick="updateOrderStatus()">Update</button>
                    </div>
                </div>

                <h6>Items in this order:</h6>
                <table class="table table-sm table-bordered">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Price</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody id="modalOrderItems"></tbody>
                </table>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<script src="/DAW2/thehearth/public/js/admin.js"></script>