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
// 2. PRODUCT CLASS (UPDATED WITH IMAGE)
// ---------------------------------------------------------

class Product {
    // 1. ADD 'image' to the constructor arguments
    constructor(id, name, description, category, price, available, image) {
        this.id = id;
        this.name = name;
        this.description = description;
        this.category = category;
        this.price = parseFloat(price);
        this.available = available;
        this.image = image; // 2. Store the image
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
// 3. FETCH AND DATA MANAGEMENT
// ---------------------------------------------------------

const arrayProducts = []; 

fetch('index.php?controller=Api&action=products')
    .then(response => response.json())
    .then(data => {
        data.forEach(item => {
            // 3. PASS 'item.image' when creating the object
            // Make sure your JSON API returns 'image' or 'img' key correctly
            const imgPath = item.image || item.img || 'img/logo.svg'; 
            
            const nuevoProducto = new Product(
                item.id, 
                item.name,
                item.description, 
                item.category, 
                item.price, 
                item.available,
                imgPath // <--- PASS IMAGE HERE
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
// 4. FILTER / SEARCH
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
// 5. DELETE FUNCTIONALITY
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
// 6. MODAL LOGIC (Create & Edit)
// ---------------------------------------------------------

function openCreateModal() {
    document.getElementById('modalTitle').innerText = "New Product";
    
    // Clear Fields
    document.getElementById('prodId').value = ""; 
    document.getElementById('prodName').value = "";
    document.getElementById('prodDesc').value = "";
    document.getElementById('prodPrice').value = "";
    document.getElementById('prodCategory').value = "Meats";
    
    // 4. RESET IMAGE TO DEFAULT FOR NEW PRODUCTS
    document.getElementById('prodImage').value = "img/logo.svg";

    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    modal.show();
}

function openEditModal(id) {
    const product = arrayProducts.find(p => p.id == id);
    if (!product) return;

    document.getElementById('modalTitle').innerText = "Edit Product";
    
    // Fill the form
    document.getElementById('prodId').value = product.id;
    document.getElementById('prodName').value = product.name;
    document.getElementById('prodDesc').value = product.description || "";
    document.getElementById('prodPrice').value = product.price;
    document.getElementById('prodCategory').value = product.category;

    // 5. THIS IS THE FIX: Load the specific image for this product
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
    const image = document.getElementById('prodImage').value; // Get the image

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
        image: image // Send image to API
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
                // UPDATE LOCAL ARRAY
                const product = arrayProducts.find(p => p.id == id);
                product.name = name;
                product.description = description;
                product.category = category;
                product.price = parseFloat(price);
                product.image = image; // 6. Update local image so next edit is correct
                
                renderTable(arrayProducts);
            } else {
                // ADD NEW LOCAL
                // Careful: Ensure 'data.id' is returned by your API
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