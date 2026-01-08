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
    tableBody.innerHTML = ""; 
    productsList.forEach(product => {
        tableBody.innerHTML += product.getHtmlRow(); 
    });
}

// ---------------------------------------------------------
// 4. PRODUCT FILTER / SEARCH
// ---------------------------------------------------------

const searchInput = document.getElementById('searchInput');

searchInput.addEventListener('input', (e) => {
    const text = e.target.value.toLowerCase();
    const filteredProducts = arrayProducts.filter(product => {
        return product.name.toLowerCase().includes(text);
    });
    renderTable(filteredProducts);
});

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
            
            // Reload logs to show the delete action
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

    const payload = {
        id: id,
        name: name,
        description: description,
        category: category,
        price: price,
        image: image 
    };

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
                // UPDATE LOCAL
                const product = arrayProducts.find(p => p.id == id);
                product.name = name;
                product.description = description;
                product.category = category;
                product.price = parseFloat(price);
                product.image = image; 
                renderTable(arrayProducts);
            } else {
                // ADD NEW LOCAL
                const newProd = new Product(data.id, name, description, category, price, 1, image);
                arrayProducts.push(newProd);
                renderTable(arrayProducts);
            }
            alert(data.message);
            fetchLogs(); // Refresh logs to show this action
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

// Fetch Orders
fetch('index.php?controller=Api&action=orders')
    .then(response => response.json())
    .then(data => {
        arrayOrders = data; 
        renderOrdersTable(arrayOrders); 
    })
    .catch(error => console.error("Error loading orders:", error));


function renderOrdersTable(ordersList) {
    const tableBody = document.getElementById('ordersTableBody');
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


// Filter Logic (Search & Status)
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
        const itemRow = `
            <tr>
                <td>${item.product_name}</td>
                <td>$${parseFloat(item.price).toFixed(2)}</td>
                <td>x${item.quantity}</td>
                <td class="fw-bold">$${parseFloat(item.subtotal).toFixed(2)}</td>
            </tr>
        `;
        itemsTbody.innerHTML += itemRow;
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
            
            const modalEl = document.getElementById('orderModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            
            alert("Order updated successfully!");
            fetchLogs(); // Refresh logs to show this action
        } else {
            alert("Error updating order.");
        }
    })
    .catch(error => console.error("Error:", error));
}


// =========================================================
// 9. SYSTEM LOGS
// =========================================================

let arrayLogs = []; // Global variable to store logs for sorting

// A. Fetch Logs Function
function fetchLogs() {
    fetch('index.php?controller=Api&action=logs')
        .then(response => response.json())
        .then(data => {
            arrayLogs = data; // Store data globally
            renderLogsTable(arrayLogs);
        })
        .catch(error => console.error("Error loading logs:", error));
}

// Call it immediately so table fills up on load
fetchLogs();

// B. Render Logs Table
function renderLogsTable(logsList) {
    const tableBody = document.getElementById('logsTableBody');
    if (!tableBody) return; 
    
    tableBody.innerHTML = ""; 

    logsList.forEach(log => {
        let colorClass = 'text-dark';
        if (log.action.includes('Delete')) colorClass = 'text-danger';
        if (log.action.includes('Create')) colorClass = 'text-success';
        if (log.action.includes('Update')) colorClass = 'text-primary';

        const row = `
            <tr>
                <td class="text-muted small">${log.timestamp}</td>
                <td>${log.admin_name || 'System/Unknown'}</td>
                <td class="fw-bold ${colorClass}">${log.action}</td>
                <td>${log.affected_entity}</td>
            </tr>
        `;
        tableBody.innerHTML += row;
    });
}

// =========================================================
// 10. TABLE SORTING LOGIC (NEW SECTION)
// =========================================================

// State to track sorting direction (1 = ASC, -1 = DESC)
let sortState = {
    products: { key: 'id', dir: 1 },
    orders: { key: 'id', dir: 1 },
    logs: { key: 'timestamp', dir: -1 }
};

/**
 * Generic Helper Function to Sort Arrays
 */
function genericSort(list, key, state) {
    // If clicking the same column, flip direction. Else, reset to ASC (1)
    if (state.key === key) {
        state.dir *= -1; 
    } else {
        state.key = key;
        state.dir = 1;
    }

    list.sort((a, b) => {
        let valA = a[key];
        let valB = b[key];

        // Convert strings to lowercase for case-insensitive sorting
        if (typeof valA === 'string') valA = valA.toLowerCase();
        if (typeof valB === 'string') valB = valB.toLowerCase();

        // Handle numeric values (prices, IDs)
        // Check if string is actually a number
        if (!isNaN(parseFloat(valA)) && isFinite(valA)) valA = parseFloat(valA);
        if (!isNaN(parseFloat(valB)) && isFinite(valB)) valB = parseFloat(valB);

        if (valA < valB) return -1 * state.dir;
        if (valA > valB) return 1 * state.dir;
        return 0;
    });
}

// --- SPECIFIC WRAPPERS ---

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