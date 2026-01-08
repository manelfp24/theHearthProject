// ---------------------------------------------------------
// 1. NAVIGATION LOGIC
// ---------------------------------------------------------

const botonesMenu = document.querySelectorAll(".menu-btn");
const secciones = document.querySelectorAll(".content-section");

botonesMenu.forEach((boton) => {
    boton.addEventListener("click", () => {
        botonesMenu.forEach(b => b.classList.remove('active'));
        boton.classList.add('active');

        const targetId = boton.getAttribute("data-target");
        setActiveSection(targetId);
    });
});

function setActiveSection(targetId) {
    secciones.forEach((seccion) => {
        seccion.classList.add("d-none");
    });
    const targetSection = document.getElementById(targetId);
    if (targetSection) {
        targetSection.classList.remove("d-none");
    }
}

// ---------------------------------------------------------
// 2. PRODUCT CLASS
// ---------------------------------------------------------

class Product {
    constructor(id, name, description, category, price, available, image) {
        this.id = id;
        this.name = name;
        this.description = description;
        this.category = category;
        this.price = parseFloat(price);
        this.available = available;
        this.image = image; 
    }

    getHtmlRow() {
        const statusBadge = this.available == 1 
            ? '<span class="badge bg-success">Active</span>' 
            : '<span class="badge bg-danger">Hidden</span>';

        return `
            <tr id="row-${this.id}">
                <td>${this.id}</td>
                <td><strong>${this.name}</strong></td>
                <td>${this.category}</td>
                <td>$${this.price.toFixed(2)}</td>
                <td>${statusBadge}</td>
                <td>
                    <button class="btn btn-sm btn-outline-primary me-2" onclick="openEditModal(${this.id})">Edit</button>
                    <button class="btn btn-sm btn-outline-danger" onclick="deleteProduct(${this.id})">Delete</button>
                </td>
            </tr>
        `;
    }
}

// ---------------------------------------------------------
// 3. PRODUCT FETCH AND DATA MANAGEMENT
// ---------------------------------------------------------

const arrayProducts = []; 

fetch('index.php?controller=Api&action=products')
    .then(response => response.json())
    .then(data => {
        data.forEach(item => {
            const imgPath = item.image || item.img || 'img/logo.svg'; 
            
            const nuevoProducto = new Product(
                item.id, 
                item.name,
                item.description, 
                item.category, 
                item.price, 
                item.available,
                imgPath 
            );
            
            arrayProducts.push(nuevoProducto);
        });
        renderTable(arrayProducts);
    })
    .catch(error => console.error("Error loading products:", error));


function renderTable(productsList) {
    const tableBody = document.getElementById('productsTableBody');
    if (!tableBody) return;
    tableBody.innerHTML = ""; 
    productsList.forEach(product => {
        tableBody.innerHTML += product.getHtmlRow(); 
    });
}

// ---------------------------------------------------------
// 4. PRODUCT FILTER / SEARCH
// ---------------------------------------------------------

const searchInput = document.getElementById('searchInput');

if(searchInput) {
    searchInput.addEventListener('input', (e) => {
        const text = e.target.value.toLowerCase();
        const filteredProducts = arrayProducts.filter(product => {
            return product.name.toLowerCase().includes(text);
        });
        renderTable(filteredProducts);
    });
}

// ---------------------------------------------------------
// 5. PRODUCT DELETE FUNCTIONALITY
// ---------------------------------------------------------

function deleteProduct(id) {
    if (!confirm("Are you sure you want to delete this product?")) return;

    fetch('index.php?controller=Api&action=delete_product', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const row = document.getElementById(`row-${id}`);
            if (row) row.remove(); 
            
            const index = arrayProducts.findIndex(p => p.id === id);
            if (index > -1) arrayProducts.splice(index, 1);
            
            fetchLogs(); 
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

// ---------------------------------------------------------
// 6. PRODUCT MODAL LOGIC (Create & Edit)
// ---------------------------------------------------------

function openCreateModal() {
    document.getElementById('modalTitle').innerText = "New Product";
    document.getElementById('prodId').value = ""; 
    document.getElementById('prodName').value = "";
    document.getElementById('prodDesc').value = "";
    document.getElementById('prodPrice').value = "";
    document.getElementById('prodCategory').value = "Meats";
    document.getElementById('prodImage').value = "img/logo.svg";

    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
}

function openEditModal(id) {
    const product = arrayProducts.find(p => p.id == id);
    if (!product) return;

    document.getElementById('modalTitle').innerText = "Edit Product";
    document.getElementById('prodId').value = product.id;
    document.getElementById('prodName').value = product.name;
    document.getElementById('prodDesc').value = product.description || "";
    document.getElementById('prodPrice').value = product.price;
    document.getElementById('prodCategory').value = product.category;
    document.getElementById('prodImage').value = product.image; 

    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
}

function saveProduct() {
    const id = document.getElementById('prodId').value;
    const name = document.getElementById('prodName').value;
    const description = document.getElementById('prodDesc').value;
    const category = document.getElementById('prodCategory').value;
    const price = document.getElementById('prodPrice').value;
    const image = document.getElementById('prodImage').value; 

    if(!name || !price) {
        alert("Please fill in all required fields.");
        return;
    }

    const payload = { id, name, description, category, price, image };

    fetch('index.php?controller=Api&action=save_product', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            const modalEl = document.getElementById('productModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            if (id) {
                const product = arrayProducts.find(p => p.id == id);
                Object.assign(product, { name, description, category, price: parseFloat(price), image });
                renderTable(arrayProducts);
            } else {
                const newProd = new Product(data.id, name, description, category, price, 1, image);
                arrayProducts.push(newProd);
                renderTable(arrayProducts);
            }
            alert(data.message);
            fetchLogs(); 
        } else {
            alert("Error saving product.");
        }
    })
    .catch(error => console.error('Error:', error));
}


// =========================================================
// 7. ORDERS LOGIC
// =========================================================

let arrayOrders = []; 
let currentOrderId = null; 

fetch('index.php?controller=Api&action=orders')
    .then(response => response.json())
    .then(data => {
        arrayOrders = data; 
        renderOrdersTable(arrayOrders); 
    })
    .catch(error => console.error("Error loading orders:", error));


function renderOrdersTable(ordersList) {
    const tableBody = document.getElementById('ordersTableBody');
    if (!tableBody) return;
    tableBody.innerHTML = ""; 

    ordersList.forEach(order => {
        let badgeClass = 'bg-secondary';
        if(order.status === 'delivered') badgeClass = 'bg-success';
        if(order.status === 'shipped') badgeClass = 'bg-info text-dark';
        if(order.status === 'cancelled') badgeClass = 'bg-danger';
        if(order.status === 'pending') badgeClass = 'bg-warning text-dark';

        const row = `
            <tr>
                <td>#${order.id}</td>
                <td>${order.date}</td>
                <td>User #${order.user_id}</td>
                <td class="fw-bold">$${parseFloat(order.total).toFixed(2)}</td>
                <td><span class="badge ${badgeClass}">${order.status.toUpperCase()}</span></td>
                <td>
                    <button class="btn btn-sm btn-primary" onclick="openOrderModal(${order.id})">
                        View Details
                    </button>
                </td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

const searchOrderInput = document.getElementById('searchOrderInput');
const filterStatus = document.getElementById('filterStatus');

function filterOrders() {
    const searchText = searchOrderInput.value.toLowerCase();
    const statusValue = filterStatus.value;
    const filtered = arrayOrders.filter(order => {
        const matchesId = order.id.toString().includes(searchText);
        const matchesStatus = (statusValue === 'all') || (order.status === statusValue);
        return matchesId && matchesStatus;
    });
    renderOrdersTable(filtered);
}

if(searchOrderInput) searchOrderInput.addEventListener('input', filterOrders);
if(filterStatus) filterStatus.addEventListener('change', filterOrders);


// =========================================================
// 8. ORDERS MODAL LOGIC
// =========================================================

function openOrderModal(id) {
    const order = arrayOrders.find(o => o.id == id);
    if(!order) return;
    currentOrderId = id; 

    document.getElementById('modalOrderId').innerText = order.id;
    document.getElementById('modalOrderDate').innerText = order.date;
    document.getElementById('modalOrderUser').innerText = order.user_id;
    document.getElementById('modalOrderTotal').innerText = parseFloat(order.total).toFixed(2);
    document.getElementById('modalOrderStatus').value = order.status;

    const itemsTbody = document.getElementById('modalOrderItems');
    itemsTbody.innerHTML = "";
    order.items.forEach(item => {
        itemsTbody.innerHTML += `
            <tr>
                <td>${item.product_name}</td>
                <td>$${parseFloat(item.price).toFixed(2)}</td>
                <td>x${item.quantity}</td>
                <td class="fw-bold">$${parseFloat(item.subtotal).toFixed(2)}</td>
            </tr>
        `;
    });

    const modal = new bootstrap.Modal(document.getElementById('orderModal'));
    modal.show();
}

function updateOrderStatus() {
    const newStatus = document.getElementById('modalOrderStatus').value;
    fetch('index.php?controller=Api&action=update_order_status', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: currentOrderId, status: newStatus })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const order = arrayOrders.find(o => o.id == currentOrderId);
            order.status = newStatus;
            renderOrdersTable(arrayOrders);
            bootstrap.Modal.getInstance(document.getElementById('orderModal')).hide();
            alert("Order updated successfully!");
            fetchLogs(); 
        } else {
            alert("Error updating order.");
        }
    });
}


// =========================================================
// 9. SYSTEM LOGS
// =========================================================

let arrayLogs = []; 

function fetchLogs() {
    fetch('index.php?controller=Api&action=logs')
        .then(response => response.json())
        .then(data => {
            arrayLogs = data; 
            renderLogsTable(arrayLogs);
        })
        .catch(error => console.error("Error loading logs:", error));
}

fetchLogs();

function renderLogsTable(logsList) {
    const tableBody = document.getElementById('logsTableBody');
    if (!tableBody) return; 
    tableBody.innerHTML = ""; 

    logsList.forEach(log => {
        let colorClass = 'text-dark';
        if (log.action.includes('Delete')) colorClass = 'text-danger';
        if (log.action.includes('Create')) colorClass = 'text-success';
        if (log.action.includes('Update')) colorClass = 'text-primary';

        tableBody.innerHTML += `
            <tr>
                <td class="text-muted small">${log.timestamp}</td>
                <td>${log.admin_name || 'System/Unknown'}</td>
                <td class="fw-bold ${colorClass}">${log.action}</td>
                <td>${log.affected_entity}</td>
            </tr>
        `;
    });
}

// =========================================================
// 10. TABLE SORTING LOGIC
// =========================================================

let sortState = {
    products: { key: 'id', dir: 1 },
    orders: { key: 'id', dir: 1 },
    logs: { key: 'timestamp', dir: -1 },
    users: { key: 'user_id', dir: 1 } // Added users
};

function genericSort(list, key, state) {
    if (state.key === key) {
        state.dir *= -1; 
    } else {
        state.key = key;
        state.dir = 1;
    }

    list.sort((a, b) => {
        let valA = a[key];
        let valB = b[key];

        if (typeof valA === 'string') valA = valA.toLowerCase();
        if (typeof valB === 'string') valB = valB.toLowerCase();

        if (!isNaN(parseFloat(valA)) && isFinite(valA)) valA = parseFloat(valA);
        if (!isNaN(parseFloat(valB)) && isFinite(valB)) valB = parseFloat(valB);

        if (valA < valB) return -1 * state.dir;
        if (valA > valB) return 1 * state.dir;
        return 0;
    });
}

function sortProducts(key) {
    genericSort(arrayProducts, key, sortState.products);
    renderTable(arrayProducts);
}

function sortOrders(key) {
    genericSort(arrayOrders, key, sortState.orders);
    renderOrdersTable(arrayOrders);
}

function sortLogs(key) {
    genericSort(arrayLogs, key, sortState.logs);
    renderLogsTable(arrayLogs);
}

function sortUsers(key) {
    const sortKey = (key === 'id') ? 'user_id' : key;
    genericSort(arrayUsers, sortKey, sortState.users);
    renderUsersTable(arrayUsers);
}

// =========================================================
// 11. USER MANAGEMENT LOGIC (NEW SECTION)
// =========================================================

let arrayUsers = [];

fetch('index.php?controller=Api&action=users')
    .then(response => response.json())
    .then(data => {
        arrayUsers = data;
        renderUsersTable(arrayUsers);
    })
    .catch(error => console.error("Error loading users:", error));

function renderUsersTable(usersList) {
    const tableBody = document.getElementById('usersTableBody');
    if(!tableBody) return;
    tableBody.innerHTML = "";

    usersList.forEach(u => {
        tableBody.innerHTML += `
            <tr>
                <td>${u.user_id}</td>
                <td><strong>${u.name}</strong></td>
                <td>${u.email}</td>
                <td>
                    <select class="form-select form-select-sm" id="role-${u.user_id}">
                        <option value="customer" ${u.role === 'customer' ? 'selected' : ''}>Customer</option>
                        <option value="admin" ${u.role === 'admin' ? 'selected' : ''}>Admin</option>
                    </select>
                </td>
                <td>
                    <button class="btn btn-sm btn-dark" onclick="updateUserRole(${u.user_id})">Update Role</button>
                </td>
            </tr>
        `;
    });
}

function updateUserRole(id) {
    const newRole = document.getElementById(`role-${id}`).value;
    fetch('index.php?controller=Api&action=update_user_role', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, role: newRole })
    })
    .then(response => response.json())
    .then(data => {
        if(data.success) {
            const user = arrayUsers.find(u => u.user_id == id);
            user.role = newRole;
            alert("Role updated successfully!");
            fetchLogs(); 
        } else {
            alert("Error updating role.");
        }
    })
    .catch(error => console.error("Error:", error));
}