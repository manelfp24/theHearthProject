// ---------------------------------------------------------
// 1. NAVIGATION LOGIC (Exactly like your example)
// ---------------------------------------------------------

// OBTAIN BUTTONS
const botonesMenu = document.querySelectorAll(".menu-btn");

// OBTAIN SECTIONS
const secciones = document.querySelectorAll(".content-section");

botonesMenu.forEach((boton) => {
    boton.addEventListener("click", () => {
        // Remove 'active' class from all buttons to style them
        botonesMenu.forEach(b => b.classList.remove('active'));
        boton.classList.add('active');

        const targetId = boton.getAttribute("data-target");
        setActiveSection(targetId);
    });
});

function setActiveSection(targetId) {
    secciones.forEach((seccion) => {
        // We use Bootstrap's 'd-none' (display: none) to hide elements
        seccion.classList.add("d-none");
    });

    // Remove 'd-none' from the target to show it
    const targetSection = document.getElementById(targetId);
    if (targetSection) {
        targetSection.classList.remove("d-none");
    }
}

// ---------------------------------------------------------
// 2. PRODUCT CLASS (Object Oriented)
// ---------------------------------------------------------

class Product {
    constructor(id, name, description, category, price, available) {
        this.id = id;
        this.name = name;
        this.description = description;
        this.category = category;
        this.price = parseFloat(price);
        this.available = available;
    }

    // Inside class Product ...
    getHtmlRow() {
        const statusBadge = this.available == 1 
            ? '<span class="badge bg-success">Active</span>' 
            : '<span class="badge bg-danger">Hidden</span>';

        // UPDATED: Added the Delete Button with 'onclick'
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
// 3. FETCH AND DATA MANAGEMENT
// ---------------------------------------------------------

const arrayProducts = []; // Array to store our objects

// Call the API we created earlier
fetch('index.php?controller=Api&action=products')
    .then(response => response.json())
    .then(data => {
        
        // Loop through the JSON data
        data.forEach(item => {
            // Create a new Object using the Class
            const nuevoProducto = new Product(
                item.id, 
                item.name,
                item.description, 
                item.category, 
                item.price, 
                item.available
            );
            
            // Add to our array
            arrayProducts.push(nuevoProducto);
        });

        // Once we have data, render the table
        renderTable(arrayProducts);
    })
    .catch(error => console.error("Error loading products:", error));


// Function to draw the array into the HTML
function renderTable(productsList) {
    const tableBody = document.getElementById('productsTableBody');
    tableBody.innerHTML = ""; // Clear existing content

    productsList.forEach(product => {
        // Use the method from the class to get HTML
        tableBody.innerHTML += product.getHtmlRow(); 
    });
}

// ---------------------------------------------------------
// 4. FILTER / SEARCH (Extra requirement: Higher Order Functions)
// ---------------------------------------------------------

const searchInput = document.getElementById('searchInput');

// Event: When user types in search box
searchInput.addEventListener('input', (e) => {
    const text = e.target.value.toLowerCase();

    // Use .filter() (Higher Order Function)
    const filteredProducts = arrayProducts.filter(product => {
        return product.name.toLowerCase().includes(text);
    });

    // Re-draw the table with filtered results
    renderTable(filteredProducts);
});

// ---------------------------------------------------------
// 5. DELETE FUNCTIONALITY
// ---------------------------------------------------------

function deleteProduct(id) {
    // 1. Confirm with user
    if (!confirm("Are you sure you want to delete this product?")) return;

    // 2. Send request to API
    fetch('index.php?controller=Api&action=delete_product', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ id: id })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 3. DOM Manipulation: Remove row without reloading
            const row = document.getElementById(`row-${id}`);
            if (row) {
                row.remove(); 
            }
            
            // Optional: Remove from our local array too
            const index = arrayProducts.findIndex(p => p.id === id);
            if (index > -1) arrayProducts.splice(index, 1);
            
        } else {
            alert("Error: " + data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}
// ---------------------------------------------------------
// 6. MODAL LOGIC (Create & Edit)
// ---------------------------------------------------------

// A. OPEN MODAL FOR NEW PRODUCT
function openCreateModal() {
    // 1. Change Title
    document.getElementById('modalTitle').innerText = "New Product";
    
    // 2. Clear Fields
    document.getElementById('prodId').value = ""; // Empty ID means "Create"
    document.getElementById('prodName').value = "";
    document.getElementById('prodDesc').value = "";
    document.getElementById('prodPrice').value = "";
    document.getElementById('prodCategory').value = "Meats";

    // 3. Show Bootstrap Modal
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
}

// B. OPEN MODAL FOR EDITING
function openEditModal(id) {
    // 1. Find the product in our JS array (No need to ask DB again!)
    const product = arrayProducts.find(p => p.id == id);
    if (!product) return;

    // 2. Fill the form
    document.getElementById('modalTitle').innerText = "Edit Product";
    document.getElementById('prodId').value = product.id; // ID exists means "Update"
    document.getElementById('prodName').value = product.name;
    document.getElementById('prodDesc').value = product.description || "";
    document.getElementById('prodPrice').value = product.price;
    document.getElementById('prodCategory').value = product.category;

    // 3. Show Modal
    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
}

// C. SAVE FUNCTION
function saveProduct() {
    // 1. Collect data from the form
    const id = document.getElementById('prodId').value;
    const name = document.getElementById('prodName').value;
    const description = document.getElementById('prodDesc').value;
    const category = document.getElementById('prodCategory').value;
    const price = document.getElementById('prodPrice').value;
    const image = document.getElementById('prodImage').value;

    // Validation
    if(!name || !price) {
        alert("Please fill in all required fields.");
        return;
    }

    // 2. Prepare Payload
    const payload = {
        id: id, // If empty string, PHP treats as null/new
        name: name,
        description: description,
        category: category,
        price: price,
        image: image
    };

    // 3. Send to API
    fetch('index.php?controller=Api&action=save_product', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // 4. Close Modal
            const modalEl = document.getElementById('productModal');
            const modal = bootstrap.Modal.getInstance(modalEl);
            modal.hide();

            // 5. Refresh Data
            // Option A: Reload everything (Easiest)
            // location.reload(); 
            
            // Option B: Smart Update (Faster)
            if (id) {
                // UPDATE: Find object in array and update it
                const product = arrayProducts.find(p => p.id == id);
                product.name = name;
                product.description = description;
                product.category = category;
                product.price = parseFloat(price);
                // Re-render
                renderTable(arrayProducts);
            } else {
                // CREATE: Make new object and push to array
                const newProd = new Product(data.id, name, description, category, price, 1);
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