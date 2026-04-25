<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Inventario y Facturación</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,.75);
        }
        .sidebar .nav-link.active {
            color: #fff;
            background-color: #0d6efd;
        }
        .content {
            padding: 20px;
        }
        .table-responsive {
            max-height: 600px;
            overflow-y: auto;
        }
    </style>
</head>
<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12 py-2 bg-light border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <strong>Usuario:</strong> {{ auth()->user()->name ?? auth()->user()->email }}
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="btn btn-outline-danger btn-sm">Cerrar sesión</button>
                </form>
            </div>
        </div>
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 px-0 sidebar">
                <div class="d-flex flex-column p-3">
                    <h5 class="text-white mb-4">Sistema de Inventario</h5>
                    <nav class="nav nav-pills flex-column">
                        <a class="nav-link active" href="#" onclick="showSection('dashboard')">
                            <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                        </a>
                        <a class="nav-link" href="#" onclick="showSection('categories')">
                            <i class="fas fa-tags me-2"></i>Categorías
                        </a>
                        <a class="nav-link" href="#" onclick="showSection('suppliers')">
                            <i class="fas fa-truck me-2"></i>Proveedores
                        </a>
                        <a class="nav-link" href="#" onclick="showSection('products')">
                            <i class="fas fa-box me-2"></i>Productos
                        </a>
                        <a class="nav-link" href="#" onclick="showSection('customers')">
                            <i class="fas fa-users me-2"></i>Clientes
                        </a>
                        <a class="nav-link" href="#" onclick="showSection('invoices')">
                            <i class="fas fa-file-invoice me-2"></i>Facturas
                        </a>
                        <a class="nav-link" href="#" onclick="showSection('stock')">
                            <i class="fas fa-chart-bar me-2"></i>Stock
                        </a>
                    </nav>
                </div>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 content">
                <!-- Dashboard -->
                <div id="dashboard-section" class="section">
                    <h2>Dashboard</h2>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card bg-primary text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Productos</h5>
                                    <h3 id="products-count">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-success text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Categorías</h5>
                                    <h3 id="categories-count">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-warning text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Proveedores</h5>
                                    <h3 id="suppliers-count">-</h3>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card bg-info text-white">
                                <div class="card-body">
                                    <h5 class="card-title">Facturas</h5>
                                    <h3 id="invoices-count">-</h3>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Categories Section -->
                <div id="categories-section" class="section d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Categorías</h2>
                        <button class="btn btn-primary" onclick="showCategoryModal()">Nueva Categoría</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="categories-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="categories-tbody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Suppliers Section -->
                <div id="suppliers-section" class="section d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Proveedores</h2>
                        <button class="btn btn-primary" onclick="showSupplierModal()">Nuevo Proveedor</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="suppliers-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="suppliers-tbody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Products Section -->
                <div id="products-section" class="section d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Productos</h2>
                        <button class="btn btn-primary" onclick="showProductModal()">Nuevo Producto</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="products-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Precio</th>
                                    <th>Stock</th>
                                    <th>Categoría</th>
                                    <th>Proveedor</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="products-tbody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Customers Section -->
                <div id="customers-section" class="section d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Clientes</h2>
                        <button class="btn btn-primary" onclick="showCustomerModal()">Nuevo Cliente</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="customers-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nombre</th>
                                    <th>Documento</th>
                                    <th>Email</th>
                                    <th>Teléfono</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="customers-tbody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Invoices Section -->
                <div id="invoices-section" class="section d-none">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h2>Facturas</h2>
                        <button class="btn btn-primary" onclick="showInvoiceModal()">Nueva Factura</button>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped" id="invoices-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Total</th>
                                    <th>Fecha</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody id="invoices-tbody"></tbody>
                        </table>
                    </div>
                </div>

                <!-- Stock Section -->
                <div id="stock-section" class="section d-none">
                    <h2>Movimientos de Stock</h2>
                    <div class="table-responsive">
                        <table class="table table-striped" id="stock-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Producto</th>
                                    <th>Tipo</th>
                                    <th>Cantidad</th>
                                    <th>Fecha</th>
                                    <th>Referencia</th>
                                </tr>
                            </thead>
                            <tbody id="stock-tbody"></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modals -->
    <!-- Category Modal -->
    <div class="modal fade" id="categoryModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="categoryModalTitle">Nueva Categoría</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="categoryForm">
                        <div class="mb-3">
                            <label for="categoryName" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="categoryName" required>
                        </div>
                        <div class="mb-3">
                            <label for="categoryDescription" class="form-label">Descripción</label>
                            <textarea class="form-control" id="categoryDescription" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveCategory()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Supplier Modal -->
    <div class="modal fade" id="supplierModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="supplierModalTitle">Nuevo Proveedor</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="supplierForm">
                        <div class="mb-3">
                            <label for="supplierName" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="supplierName" required>
                        </div>
                        <div class="mb-3">
                            <label for="supplierEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="supplierEmail">
                        </div>
                        <div class="mb-3">
                            <label for="supplierPhone" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="supplierPhone">
                        </div>
                        <div class="mb-3">
                            <label for="supplierAddress" class="form-label">Dirección</label>
                            <textarea class="form-control" id="supplierAddress" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveSupplier()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Modal -->
    <div class="modal fade" id="productModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="productModalTitle">Nuevo Producto</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="productForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productName" class="form-label">Nombre *</label>
                                    <input type="text" class="form-control" id="productName" required>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productSku" class="form-label">SKU</label>
                                    <input type="text" class="form-control" id="productSku">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label for="productDescription" class="form-label">Descripción</label>
                            <textarea class="form-control" id="productDescription" rows="3"></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productSalePrice" class="form-label">Precio de Venta</label>
                                    <input type="number" step="0.01" class="form-control" id="productSalePrice">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productStock" class="form-label">Stock</label>
                                    <input type="number" class="form-control" id="productStock">
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productCategory" class="form-label">Categoría</label>
                                    <select class="form-control" id="productCategory">
                                        <option value="">Seleccionar categoría</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="productSupplier" class="form-label">Proveedor</label>
                                    <select class="form-control" id="productSupplier">
                                        <option value="">Seleccionar proveedor</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveProduct()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Customer Modal -->
    <div class="modal fade" id="customerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="customerModalTitle">Nuevo Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="customerForm">
                        <div class="mb-3">
                            <label for="customerName" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="customerName" required>
                        </div>
                        <div class="mb-3">
                            <label for="customerDocument" class="form-label">Documento</label>
                            <input type="text" class="form-control" id="customerDocument">
                        </div>
                        <div class="mb-3">
                            <label for="customerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="customerEmail">
                        </div>
                        <div class="mb-3">
                            <label for="customerPhone" class="form-label">Teléfono</label>
                            <input type="text" class="form-control" id="customerPhone">
                        </div>
                        <div class="mb-3">
                            <label for="customerAddress" class="form-label">Dirección</label>
                            <textarea class="form-control" id="customerAddress" rows="3"></textarea>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveCustomer()">Guardar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Invoice Modal -->
    <div class="modal fade" id="invoiceModal" tabindex="-1">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="invoiceModalTitle">Nueva Factura</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="invoiceForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="invoiceCustomer" class="form-label">Cliente *</label>
                                    <select class="form-control" id="invoiceCustomer" required>
                                        <option value="">Seleccionar cliente</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="invoiceCustomerName" class="form-label">Nombre del Cliente</label>
                                    <input type="text" class="form-control" id="invoiceCustomerName" readonly>
                                </div>
                            </div>
                        </div>

                        <h6>Productos</h6>
                        <div id="invoiceItems">
                            <div class="row mb-3 invoice-item">
                                <div class="col-md-4">
                                    <label class="form-label">Producto</label>
                                    <select class="form-control product-select" required>
                                        <option value="">Seleccionar producto</option>
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
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm mb-3" onclick="addInvoiceItem()">Agregar Producto</button>

                        <div class="row">
                            <div class="col-md-6 offset-md-6">
                                <div class="mb-3">
                                    <label for="invoiceSubtotal" class="form-label">Subtotal</label>
                                    <input type="number" step="0.01" class="form-control" id="invoiceSubtotal" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="invoiceTax" class="form-label">Impuestos</label>
                                    <input type="number" step="0.01" class="form-control" id="invoiceTax" readonly>
                                </div>
                                <div class="mb-3">
                                    <label for="invoiceTotal" class="form-label fw-bold">Total</label>
                                    <input type="number" step="0.01" class="form-control fw-bold" id="invoiceTotal" readonly>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="saveInvoice()">Crear Factura</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://kit.fontawesome.com/your-fontawesome-kit.js" crossorigin="anonymous"></script>
    <script src="app.js"></script>
</body>
</html>