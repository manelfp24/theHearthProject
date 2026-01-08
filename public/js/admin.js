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
        } else {
            alert("Error saving product.");
        }
    })
    .catch(error => console.error('Error:', error));
}


// =========================================================
// 7. ORDERS LOGIC (NEW SECTION)
// =========================================================

let arrayOrders = []; // Store fetched orders here
let currentOrderId = null; // To know which order we are editing in the modal

// A. Fetch Orders from API
fetch('index.php?controller=Api&action=orders')
    .then(response => response.json())
    .then(data => {
        arrayOrders = data; // Save to memory
        renderOrdersTable(arrayOrders); // Draw table
    })
    .catch(error => console.error("Error loading orders:", error));


// B. Render Orders Table
function renderOrdersTable(ordersList) {
    const tableBody = document.getElementById('ordersTableBody');
    tableBody.innerHTML = ""; 

    ordersList.forEach(order => {
        // Status Badge Color Logic
        let badgeClass = 'bg-secondary';
        if(order.status === 'delivered') badgeClass = 'bg-success';
        if(order.status === 'shipped') badgeClass = 'bg-info text-dark';
        if(order.status === 'cancelled') badgeClass = 'bg-danger';
        if(order.status === 'pending') badgeClass = 'bg-warning text-dark';

        // Create Row HTML
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


// C. Filter Logic (Search & Status)
const searchOrderInput = document.getElementById('searchOrderInput');
const filterStatus = document.getElementById('filterStatus');

// Helper function to apply both filters
function filterOrders() {
    const searchText = searchOrderInput.value.toLowerCase();
    const statusValue = filterStatus.value;

    const filtered = arrayOrders.filter(order => {
        // Check ID match (converts id to string)
        const matchesId = order.id.toString().includes(searchText);
        
        // Check Status match
        const matchesStatus = (statusValue === 'all') || (order.status === statusValue);

        return matchesId && matchesStatus;
    });

    renderOrdersTable(filtered);
}

// Attach events
if(searchOrderInput) searchOrderInput.addEventListener('input', filterOrders);
if(filterStatus) filterStatus.addEventListener('change', filterOrders);


// =========================================================
// 8. ORDERS MODAL LOGIC
// =========================================================

// A. Open Modal & Fill Data
function openOrderModal(id) {
    const order = arrayOrders.find(o => o.id == id);
    if(!order) return;

    currentOrderId = id; // Store for update function

    // Fill Header Info
    document.getElementById('modalOrderId').innerText = order.id;
    document.getElementById('modalOrderDate').innerText = order.date;
    document.getElementById('modalOrderUser').innerText = order.user_id;
    document.getElementById('modalOrderTotal').innerText = parseFloat(order.total).toFixed(2);
    
    // Set Status Dropdown
    document.getElementById('modalOrderStatus').value = order.status;

    // Fill Items Table
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

    // Show Modal
    const modal = new bootstrap.Modal(document.getElementById('orderModal'));
    modal.show();
}

// B. Update Status
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
            // Update local array
            const order = arrayOrders.find(o => o.id == currentOrderId);
            order.status = newStatus;
            
            // Re-render table to show new color/status
            renderOrdersTable(arrayOrders);
            
            // Close Modal
            const modalEl = document.getElementById('orderModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();
            
            alert("Order updated successfully!");
        } else {
            alert("Error updating order.");
        }
    })
    .catch(error => console.error("Error:", error));
}