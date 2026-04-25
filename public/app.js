// API Base URL
const API_BASE = 'http://localhost:8001/api';

// Global variables
let currentSection = 'dashboard';
let categories = [];
let suppliers = [];
let products = [];
let customers = [];

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    loadDashboard();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Modal close events
    document.querySelectorAll('.btn-close, [data-bs-dismiss="modal"]').forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                bootstrap.Modal.getInstance(modal).hide();
            }
        });
    });
}

// Navigation
function showSection(sectionName) {
    // Hide all sections
    document.querySelectorAll('.section').forEach(section => {
        section.classList.add('d-none');
    });

    // Remove active class from nav links
    document.querySelectorAll('.sidebar .nav-link').forEach(link => {
        link.classList.remove('active');
    });

    // Show selected section
    document.getElementById(sectionName + '-section').classList.remove('d-none');

    // Add active class to current nav link
    event.target.classList.add('active');

    currentSection = sectionName;

    // Load data for the section
    switch(sectionName) {
        case 'dashboard':
            loadDashboard();
            break;
        case 'categories':
            loadCategories();
            break;
        case 'suppliers':
            loadSuppliers();
            break;
        case 'products':
            loadProducts();
            break;
        case 'customers':
            loadCustomers();
            break;
        case 'invoices':
            loadInvoices();
            break;
        case 'stock':
            loadStockMovements();
            break;
    }
}

// API Helper Functions
async function apiRequest(endpoint, options = {}) {
    const url = `${API_BASE}${endpoint}`;
    const config = {
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            ...options.headers
        },
        ...options
    };

    try {
        const response = await fetch(url, config);

        if (!response.ok) {
            if (response.status === 401) {
                window.location.href = '/login';
                return;
            }

            const errorData = await response.json();
            throw new Error(errorData.message || `HTTP error! status: ${response.status}`);
        }

        return await response.json();
    } catch (error) {
        console.error('API Error:', error);
        // Try to parse Laravel validation errors
        try {
            const errorData = JSON.parse(error.message);
            if (errorData.errors) {
                const errorMessages = Object.values(errorData.errors).flat().join('\n');
                alert('Errores de validación:\n' + errorMessages);
            } else if (errorData.message) {
                alert('Error: ' + errorData.message);
            } else {
                alert('Error: ' + error.message);
            }
        } catch (parseError) {
            alert('Error: ' + error.message);
        }
        throw error;
    }
}

// Dashboard Functions
async function loadDashboard() {
    try {
        const [productsRes, categoriesRes, suppliersRes, invoicesRes] = await Promise.all([
            apiRequest('/products'),
            apiRequest('/categories'),
            apiRequest('/suppliers'),
            apiRequest('/invoices')
        ]);

        document.getElementById('products-count').textContent = productsRes.total || productsRes.length || 0;
        document.getElementById('categories-count').textContent = categoriesRes.total || categoriesRes.length || 0;
        document.getElementById('suppliers-count').textContent = suppliersRes.total || suppliersRes.length || 0;
        document.getElementById('invoices-count').textContent = invoicesRes.total || invoicesRes.length || 0;
    } catch (error) {
        console.error('Error loading dashboard:', error);
    }
}

// Categories Functions
async function loadCategories() {
    try {
        const data = await apiRequest('/categories');
        categories = Array.isArray(data) ? data : data.data || [];

        const tbody = document.getElementById('categories-tbody');
        tbody.innerHTML = '';

        categories.forEach(category => {
            const row = `
                <tr>
                    <td>${category.id}</td>
                    <td>${category.name}</td>
                    <td>${category.description || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning me-2" onclick="editCategory(${category.id})">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCategory(${category.id})">Eliminar</button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error loading categories:', error);
    }
}

function showCategoryModal(categoryId = null) {
    const modal = new bootstrap.Modal(document.getElementById('categoryModal'));
    const title = document.getElementById('categoryModalTitle');
    const form = document.getElementById('categoryForm');

    if (categoryId) {
        const category = categories.find(c => c.id === categoryId);
        if (category) {
            document.getElementById('categoryName').value = category.name;
            document.getElementById('categoryDescription').value = category.description || '';
            title.textContent = 'Editar Categoría';
            form.dataset.categoryId = categoryId;
        }
    } else {
        form.reset();
        title.textContent = 'Nueva Categoría';
        delete form.dataset.categoryId;
    }

    modal.show();
}

async function saveCategory() {
    const name = document.getElementById('categoryName').value.trim();
    const description = document.getElementById('categoryDescription').value.trim();
    const form = document.getElementById('categoryForm');
    const categoryId = form.dataset.categoryId;

    if (!name) {
        alert('El nombre es obligatorio');
        return;
    }

    const data = { name, description };

    try {
        if (categoryId) {
            await apiRequest(`/categories/${categoryId}`, {
                method: 'PUT',
                body: JSON.stringify(data)
            });
        } else {
            await apiRequest('/categories', {
                method: 'POST',
                body: JSON.stringify(data)
            });
        }

        bootstrap.Modal.getInstance(document.getElementById('categoryModal')).hide();
        loadCategories();
        loadDashboard();
    } catch (error) {
        // Error already handled in apiRequest
    }
}

async function deleteCategory(id) {
    if (!confirm('¿Estás seguro de que quieres eliminar esta categoría?')) {
        return;
    }

    try {
        await apiRequest(`/categories/${id}`, { method: 'DELETE' });
        loadCategories();
        loadDashboard();
    } catch (error) {
        // Error already handled in apiRequest
    }
}

// Suppliers Functions
async function loadSuppliers() {
    try {
        const data = await apiRequest('/suppliers');
        suppliers = Array.isArray(data) ? data : data.data || [];

        const tbody = document.getElementById('suppliers-tbody');
        tbody.innerHTML = '';

        suppliers.forEach(supplier => {
            const row = `
                <tr>
                    <td>${supplier.id}</td>
                    <td>${supplier.name}</td>
                    <td>${supplier.email || ''}</td>
                    <td>${supplier.phone || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning me-2" onclick="editSupplier(${supplier.id})">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteSupplier(${supplier.id})">Eliminar</button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error loading suppliers:', error);
    }
}

function showSupplierModal(supplierId = null) {
    const modal = new bootstrap.Modal(document.getElementById('supplierModal'));
    const title = document.getElementById('supplierModalTitle');
    const form = document.getElementById('supplierForm');

    if (supplierId) {
        const supplier = suppliers.find(s => s.id === supplierId);
        if (supplier) {
            document.getElementById('supplierName').value = supplier.name;
            document.getElementById('supplierEmail').value = supplier.email || '';
            document.getElementById('supplierPhone').value = supplier.phone || '';
            document.getElementById('supplierAddress').value = supplier.address || '';
            title.textContent = 'Editar Proveedor';
            form.dataset.supplierId = supplierId;
        }
    } else {
        form.reset();
        title.textContent = 'Nuevo Proveedor';
        delete form.dataset.supplierId;
    }

    modal.show();
}

async function saveSupplier() {
    const name = document.getElementById('supplierName').value.trim();
    const email = document.getElementById('supplierEmail').value.trim();
    const phone = document.getElementById('supplierPhone').value.trim();
    const address = document.getElementById('supplierAddress').value.trim();
    const form = document.getElementById('supplierForm');
    const supplierId = form.dataset.supplierId;

    if (!name) {
        alert('El nombre es obligatorio');
        return;
    }

    const data = { name, email, phone, address };

    try {
        if (supplierId) {
            await apiRequest(`/suppliers/${supplierId}`, {
                method: 'PUT',
                body: JSON.stringify(data)
            });
        } else {
            await apiRequest('/suppliers', {
                method: 'POST',
                body: JSON.stringify(data)
            });
        }

        bootstrap.Modal.getInstance(document.getElementById('supplierModal')).hide();
        loadSuppliers();
        loadDashboard();
    } catch (error) {
        // Error already handled in apiRequest
    }
}

async function deleteSupplier(id) {
    if (!confirm('¿Estás seguro de que quieres eliminar este proveedor?')) {
        return;
    }

    try {
        await apiRequest(`/suppliers/${id}`, { method: 'DELETE' });
        loadSuppliers();
        loadDashboard();
    } catch (error) {
        // Error already handled in apiRequest
    }
}

// Products Functions
async function loadProducts() {
    try {
        const data = await apiRequest('/products');
        products = Array.isArray(data) ? data : data.data || [];

        const tbody = document.getElementById('products-tbody');
        tbody.innerHTML = '';

        products.forEach(product => {
            const row = `
                <tr>
                    <td>${product.id}</td>
                    <td>${product.name}</td>
                    <td>$${product.sale_price || 0}</td>
                    <td>${product.stock || 0}</td>
                    <td>${product.category ? product.category.name : ''}</td>
                    <td>${product.supplier ? product.supplier.name : ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning me-2" onclick="editProduct(${product.id})">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteProduct(${product.id})">Eliminar</button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error loading products:', error);
    }
}

async function showProductModal(productId = null) {
    // Load categories and suppliers for dropdowns
    if (categories.length === 0) await loadCategories();
    if (suppliers.length === 0) await loadSuppliers();

    const categorySelect = document.getElementById('productCategory');
    const supplierSelect = document.getElementById('productSupplier');

    categorySelect.innerHTML = '<option value="">Seleccionar categoría</option>';
    categories.forEach(category => {
        categorySelect.innerHTML += `<option value="${category.id}">${category.name}</option>`;
    });

    supplierSelect.innerHTML = '<option value="">Seleccionar proveedor</option>';
    suppliers.forEach(supplier => {
        supplierSelect.innerHTML += `<option value="${supplier.id}">${supplier.name}</option>`;
    });

    const modal = new bootstrap.Modal(document.getElementById('productModal'));
    const title = document.getElementById('productModalTitle');
    const form = document.getElementById('productForm');

    if (productId) {
        const product = products.find(p => p.id === productId);
        if (product) {
            document.getElementById('productName').value = product.name;
            document.getElementById('productSku').value = product.sku || '';
            document.getElementById('productDescription').value = product.description || '';
            document.getElementById('productSalePrice').value = product.sale_price || '';
            document.getElementById('productStock').value = product.stock || '';
            document.getElementById('productCategory').value = product.category_id || '';
            document.getElementById('productSupplier').value = product.supplier_id || '';
            title.textContent = 'Editar Producto';
            form.dataset.productId = productId;
        }
    } else {
        form.reset();
        title.textContent = 'Nuevo Producto';
        delete form.dataset.productId;
    }

    modal.show();
}

async function saveProduct() {
    const name = document.getElementById('productName').value.trim();
    const sku = document.getElementById('productSku').value.trim();
    const description = document.getElementById('productDescription').value.trim();
    const salePrice = document.getElementById('productSalePrice').value;
    const stock = document.getElementById('productStock').value;
    const categoryId = document.getElementById('productCategory').value;
    const supplierId = document.getElementById('productSupplier').value;
    const form = document.getElementById('productForm');
    const productId = form.dataset.productId;

    if (!name) {
        alert('El nombre es obligatorio');
        return;
    }

    const data = {
        name,
        sku,
        description,
        sale_price: salePrice ? parseFloat(salePrice) : null,
        stock: stock ? parseInt(stock) : null,
        category_id: categoryId ? parseInt(categoryId) : null,
        supplier_id: supplierId ? parseInt(supplierId) : null
    };

    try {
        if (productId) {
            await apiRequest(`/products/${productId}`, {
                method: 'PUT',
                body: JSON.stringify(data)
            });
        } else {
            await apiRequest('/products', {
                method: 'POST',
                body: JSON.stringify(data)
            });
        }

        bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
        loadProducts();
        loadDashboard();
    } catch (error) {
        // Error already handled in apiRequest
    }
}

async function deleteProduct(id) {
    if (!confirm('¿Estás seguro de que quieres eliminar este producto?')) {
        return;
    }

    try {
        await apiRequest(`/products/${id}`, { method: 'DELETE' });
        loadProducts();
        loadDashboard();
    } catch (error) {
        // Error already handled in apiRequest
    }
}

// Customers Functions
async function loadCustomers() {
    try {
        const data = await apiRequest('/customers');
        customers = Array.isArray(data) ? data : data.data || [];

        const tbody = document.getElementById('customers-tbody');
        tbody.innerHTML = '';

        customers.forEach(customer => {
            const row = `
                <tr>
                    <td>${customer.id}</td>
                    <td>${customer.name}</td>
                    <td>${customer.document || ''}</td>
                    <td>${customer.email || ''}</td>
                    <td>${customer.phone || ''}</td>
                    <td>
                        <button class="btn btn-sm btn-warning me-2" onclick="editCustomer(${customer.id})">Editar</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteCustomer(${customer.id})">Eliminar</button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error loading customers:', error);
    }
}

function showCustomerModal(customerId = null) {
    const modal = new bootstrap.Modal(document.getElementById('customerModal'));
    const title = document.getElementById('customerModalTitle');
    const form = document.getElementById('customerForm');

    if (customerId) {
        const customer = customers.find(c => c.id === customerId);
        if (customer) {
            document.getElementById('customerName').value = customer.name;
            document.getElementById('customerDocument').value = customer.document || '';
            document.getElementById('customerEmail').value = customer.email || '';
            document.getElementById('customerPhone').value = customer.phone || '';
            document.getElementById('customerAddress').value = customer.address || '';
            title.textContent = 'Editar Cliente';
            form.dataset.customerId = customerId;
        }
    } else {
        form.reset();
        title.textContent = 'Nuevo Cliente';
        delete form.dataset.customerId;
    }

    modal.show();
}

async function saveCustomer() {
    const name = document.getElementById('customerName').value.trim();
    const document = document.getElementById('customerDocument').value.trim();
    const email = document.getElementById('customerEmail').value.trim();
    const phone = document.getElementById('customerPhone').value.trim();
    const address = document.getElementById('customerAddress').value.trim();
    const form = document.getElementById('customerForm');
    const customerId = form.dataset.customerId;

    if (!name) {
        alert('El nombre es obligatorio');
        return;
    }

    const data = { name, document, email, phone, address };

    try {
        if (customerId) {
            await apiRequest(`/customers/${customerId}`, {
                method: 'PUT',
                body: JSON.stringify(data)
            });
        } else {
            await apiRequest('/customers', {
                method: 'POST',
                body: JSON.stringify(data)
            });
        }

        bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
        loadCustomers();
        loadDashboard();
    } catch (error) {
        // Error already handled in apiRequest
    }
}

async function deleteCustomer(id) {
    if (!confirm('¿Estás seguro de que quieres eliminar este cliente?')) {
        return;
    }

    try {
        await apiRequest(`/customers/${id}`, { method: 'DELETE' });
        loadCustomers();
        loadDashboard();
    } catch (error) {
        // Error already handled in apiRequest
    }
}

// Invoices Functions
async function loadInvoices() {
    try {
        const data = await apiRequest('/invoices');
        const invoices = Array.isArray(data) ? data : data.data || [];

        const tbody = document.getElementById('invoices-tbody');
        tbody.innerHTML = '';

        invoices.forEach(invoice => {
            const row = `
                <tr>
                    <td>${invoice.id}</td>
                    <td>${invoice.customer_name}</td>
                    <td>$${invoice.total || 0}</td>
                    <td>${new Date(invoice.created_at).toLocaleDateString()}</td>
                    <td>${invoice.status || 'Pendiente'}</td>
                    <td>
                        <button class="btn btn-sm btn-info me-2" onclick="viewInvoice(${invoice.id})">Ver</button>
                        <button class="btn btn-sm btn-danger" onclick="deleteInvoice(${invoice.id})">Eliminar</button>
                    </td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error loading invoices:', error);
    }
}

async function showInvoiceModal() {
    // Load customers and products for dropdowns
    if (customers.length === 0) await loadCustomers();
    if (products.length === 0) await loadProducts();

    const customerSelect = document.getElementById('invoiceCustomer');
    customerSelect.innerHTML = '<option value="">Seleccionar cliente</option>';
    customers.forEach(customer => {
        customerSelect.innerHTML += `<option value="${customer.id}">${customer.name}</option>`;
    });

    // Setup customer change event
    customerSelect.addEventListener('change', function() {
        const selectedCustomer = customers.find(c => c.id == this.value);
        document.getElementById('invoiceCustomerName').value = selectedCustomer ? selectedCustomer.name : '';
    });

    // Setup invoice items
    setupInvoiceItems();

    const modal = new bootstrap.Modal(document.getElementById('invoiceModal'));
    modal.show();
}

function setupInvoiceItems() {
    const container = document.getElementById('invoiceItems');
    container.innerHTML = `
        <div class="row mb-3 invoice-item">
            <div class="col-md-4">
                <label class="form-label">Producto</label>
                <select class="form-control product-select" required>
                    <option value="">Seleccionar producto</option>
                    ${products.map(p => `<option value="${p.id}" data-price="${p.sale_price}">${p.name}</option>`).join('')}
                </select>
            </div>
            <div class="col-md-2">
                <label class="form-label">Cantidad</label>
                <input type="number" class="form-control quantity-input" min="1" value="1" required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Precio Unit.</label>
                <input type="number" step="0.01" class="form-control unit-price-input" readonly required>
            </div>
            <div class="col-md-2">
                <label class="form-label">Total</label>
                <input type="number" step="0.01" class="form-control total-input" readonly>
            </div>
            <div class="col-md-2">
                <label class="form-label">&nbsp;</label>
                <button type="button" class="btn btn-danger btn-sm w-100 remove-item">Eliminar</button>
            </div>
        </div>
    `;

    // Setup event listeners for the first item
    setupInvoiceItemEvents(container.querySelector('.invoice-item'));
    updateInvoiceTotals();
}

function setupInvoiceItemEvents(item) {
    const productSelect = item.querySelector('.product-select');
    const quantityInput = item.querySelector('.quantity-input');
    const unitPriceInput = item.querySelector('.unit-price-input');
    const totalInput = item.querySelector('.total-input');
    const removeBtn = item.querySelector('.remove-item');

    productSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const price = selectedOption.getAttribute('data-price') || 0;
        unitPriceInput.value = price;
        updateItemTotal(item);
        updateInvoiceTotals();
    });

    quantityInput.addEventListener('input', function() {
        updateItemTotal(item);
        updateInvoiceTotals();
    });

    removeBtn.addEventListener('click', function() {
        if (document.querySelectorAll('.invoice-item').length > 1) {
            item.remove();
            updateInvoiceTotals();
        } else {
            alert('Debe haber al menos un producto en la factura');
        }
    });
}

function updateItemTotal(item) {
    const quantity = parseFloat(item.querySelector('.quantity-input').value) || 0;
    const unitPrice = parseFloat(item.querySelector('.unit-price-input').value) || 0;
    const total = quantity * unitPrice;
    item.querySelector('.total-input').value = total.toFixed(2);
}

function updateInvoiceTotals() {
    const items = document.querySelectorAll('.invoice-item');
    let subtotal = 0;

    items.forEach(item => {
        const total = parseFloat(item.querySelector('.total-input').value) || 0;
        subtotal += total;
    });

    const tax = subtotal * 0.16; // 16% IVA
    const total = subtotal + tax;

    document.getElementById('invoiceSubtotal').value = subtotal.toFixed(2);
    document.getElementById('invoiceTax').value = tax.toFixed(2);
    document.getElementById('invoiceTotal').value = total.toFixed(2);
}

function addInvoiceItem() {
    const container = document.getElementById('invoiceItems');
    const newItem = document.createElement('div');
    newItem.className = 'row mb-3 invoice-item';
    newItem.innerHTML = `
        <div class="col-md-4">
            <label class="form-label">Producto</label>
            <select class="form-control product-select" required>
                <option value="">Seleccionar producto</option>
                ${products.map(p => `<option value="${p.id}" data-price="${p.sale_price}">${p.name}</option>`).join('')}
            </select>
        </div>
        <div class="col-md-2">
            <label class="form-label">Cantidad</label>
            <input type="number" class="form-control quantity-input" min="1" value="1" required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Precio Unit.</label>
            <input type="number" step="0.01" class="form-control unit-price-input" readonly required>
        </div>
        <div class="col-md-2">
            <label class="form-label">Total</label>
            <input type="number" step="0.01" class="form-control total-input" readonly>
        </div>
        <div class="col-md-2">
            <label class="form-label">&nbsp;</label>
            <button type="button" class="btn btn-danger btn-sm w-100 remove-item">Eliminar</button>
        </div>
    `;

    container.appendChild(newItem);
    setupInvoiceItemEvents(newItem);
}

async function saveInvoice() {
    const customerId = document.getElementById('invoiceCustomer').value;
    const customerName = document.getElementById('invoiceCustomerName').value;

    if (!customerId || !customerName) {
        alert('Debe seleccionar un cliente');
        return;
    }

    const items = [];
    const invoiceItems = document.querySelectorAll('.invoice-item');

    for (const item of invoiceItems) {
        const productId = item.querySelector('.product-select').value;
        const quantity = parseInt(item.querySelector('.quantity-input').value);
        const unitPrice = parseFloat(item.querySelector('.unit-price-input').value);

        if (!productId || !quantity || !unitPrice) {
            alert('Todos los campos de productos son obligatorios');
            return;
        }

        items.push({
            product_id: parseInt(productId),
            quantity: quantity,
            unit_price: unitPrice
        });
    }

    if (items.length === 0) {
        alert('Debe agregar al menos un producto');
        return;
    }

    const data = {
        customer_id: parseInt(customerId),
        customer_name: customerName,
        items: items
    };

    try {
        await apiRequest('/invoices', {
            method: 'POST',
            body: JSON.stringify(data)
        });

        bootstrap.Modal.getInstance(document.getElementById('invoiceModal')).hide();
        loadInvoices();
        loadProducts(); // Reload products to show updated stock
        loadDashboard();
    } catch (error) {
        // Error already handled in apiRequest
    }
}

async function deleteInvoice(id) {
    if (!confirm('¿Estás seguro de que quieres eliminar esta factura?')) {
        return;
    }

    try {
        await apiRequest(`/invoices/${id}`, { method: 'DELETE' });
        loadInvoices();
        loadDashboard();
    } catch (error) {
        // Error already handled in apiRequest
    }
}

// Stock Movements Functions
async function loadStockMovements() {
    try {
        const data = await apiRequest('/stock-movements');
        const movements = Array.isArray(data) ? data : data.data || [];

        const tbody = document.getElementById('stock-tbody');
        tbody.innerHTML = '';

        movements.forEach(movement => {
            const row = `
                <tr>
                    <td>${movement.id}</td>
                    <td>${movement.product ? movement.product.name : ''}</td>
                    <td>${movement.type}</td>
                    <td>${movement.quantity}</td>
                    <td>${new Date(movement.created_at).toLocaleDateString()}</td>
                    <td>${movement.reference || ''}</td>
                </tr>
            `;
            tbody.innerHTML += row;
        });
    } catch (error) {
        console.error('Error loading stock movements:', error);
    }
}

// Utility functions for edit operations
function editCategory(id) { showCategoryModal(id); }
function editSupplier(id) { showSupplierModal(id); }
function editProduct(id) { showProductModal(id); }
function editCustomer(id) { showCustomerModal(id); }
function viewInvoice(id) { /* TODO: Implement view invoice details */ }