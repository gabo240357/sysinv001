# Diseño del Sistema de Inventario y Facturación en Laravel

## 1. Objetivo

Diseñar un sistema web para la gestión de inventarios, clientes, ventas y facturación utilizando Laravel como backend principal, con frontend moderno y una arquitectura escalable.

## 2. Tecnologías recomendadas

- PHP 8.2+ y Laravel 11+.
- MySQL / MariaDB como base de datos relacional.
- Blade para vistas básicas o Vue.js / Livewire para UI interactiva.
- Bootstrap 5 o Tailwind CSS para estilos rápidos.
- Laravel Sanctum para autenticación de API si se necesita SPA.
- Filament / Nova para administración opcional.

## 3. Módulos principales

1. Autenticación y seguridad
2. Gestión de usuarios y roles
3. Gestión de productos e inventario
4. Gestión de clientes y proveedores
5. Creación de facturas y notas de venta
6. Registro de compras y movimientos de stock
7. Reportes e informes
8. Configuración de impuestos y series de facturación

## 4. Entidades del sistema

### 4.1. `users`

- id
- name
- email
- password
- role_id
- activo
- created_at
- updated_at

### 4.2. `roles`

- id
- nombre
- permisos (json opcional)
- created_at
- updated_at

### 4.3. `products`

- id
- sku
- nombre
- descripcion
- precio_compra
- precio_venta
- stock_actual
- stock_minimo
- categoria_id
- impuesto_id
- proveedor_id
- unidad_medida
- creado_por
- updated_at

### 4.4. `categories`

- id
- nombre
- descripcion
- created_at
- updated_at

### 4.5. `suppliers`

- id
- nombre
- ruc
- telefono
- email
- direccion
- contacto
- created_at
- updated_at

### 4.6. `customers`

- id
- nombre
- ruc_dni
- telefono
- email
- direccion
- tipo_persona
- created_at
- updated_at

### 4.7. `invoices`

- id
- tipo (factura, boleta, nota de venta)
- serie
- numero
- customer_id
- user_id
- cliente_nombre
- cliente_documento
- subtotal
- descuento
- impuesto_total
- total
- estado (pendiente, pagado, anulado)
- fecha_emision
- fecha_vencimiento
- nota
- created_at
- updated_at

### 4.8. `invoice_items`

- id
- invoice_id
- product_id
- cantidad
- precio_unitario
- descuento
- importe
- impuesto_porcentaje
- created_at
- updated_at

### 4.9. `stock_movements`

- id
- product_id
- tipo (entrada, salida, ajuste)
- referencia (factura_id, purchase_id, nota)
- cantidad
- saldo_anterior
- saldo_nuevo
- motivo
- user_id
- created_at
- updated_at

### 4.10. `payments`

- id
- invoice_id
- fecha_pago
- monto
- metodo_pago
- referencia
- created_at
- updated_at

### 4.11. `taxes`

- id
- nombre
- porcentaje
- aplica_a_venta
- aplica_a_compra
- created_at
- updated_at

## 5. Flujo funcional básico

1. El usuario inicia sesión y accede al panel principal.
2. Se registra o actualiza el catálogo de productos.
3. Se gestionan clientes/proveedores.
4. Se crea una factura (o boleta) seleccionando productos y cantidades.
5. El sistema descuenta inventario automáticamente.
6. Se generan reportes de ventas, facturación y stock.
7. Se permiten ajustes y anulaciones con trazabilidad.

## 6. Rutas principales sugeridas

- `/dashboard`
- `/productos`
- `/productos/create`
- `/clientes`
- `/proveedores`
- `/facturas`
- `/facturas/create`
- `/facturas/{id}`
- `/ventas/reportes`
- `/inventario/movimientos`
- `/configuracion/impuestos`

## 7. Controladores sugeridos

- `DashboardController`
- `ProductController`
- `CategoryController`
- `SupplierController`
- `CustomerController`
- `InvoiceController`
- `InvoiceItemController`
- `StockMovementController`
- `PaymentController`
- `TaxController`
- `RoleController`

## 8. Servicios y lógica de dominio

- `InvoiceService`: cálculo de totales, impuestos y generación de números.
- `StockService`: actualización de stock y validación de cantidades.
- `ReportService`: exportación a PDF/Excel y filtros por fechas.
- `CustomerService`: CRM básico y validación de datos.

## 9. Pantallas clave

1. Login / Registro
2. Dashboard con resumen de ventas, facturación e inventario
3. Listado y mantenimiento de productos
4. Listado de clientes y proveedores
5. Generación y edición de facturas/boletas
6. Vista de detalles de factura con impresión/PDF
7. Reportes de stock, ventas por fecha, productos más vendidos
8. Configuración de impuestos y series

## 10. Buenas prácticas

- Usar migraciones y seeders.
- Aplicar validación con `FormRequest`.
- Usar Eloquent ORM con relaciones bien definidas.
- Implementar `SoftDeletes` en clientes/proveedores/productos si es necesario.
- Controlar permisos con Policies o Gates.
- Añadir tests unitarios para cálculos de facturas y stock.

## 11. Próximos pasos para implementación

1. Crear nuevo proyecto Laravel.
2. Generar migraciones para las tablas definidas.
3. Modelar las relaciones Eloquent.
4. Implementar autenticación y roles.
5. Desarrollar CRUD de productos, clientes y facturas.
6. Añadir lógica de inventario y reportes.
7. Crear vistas y/o SPA con componentes interactivos.

## 12. Extensiones posibles

- Facturación electrónica.
- Control de lotes y fechas de vencimiento.
- Series automáticas con prefijos por tipo de documento.
- Integración con pasarelas de pago.
- Módulo de compras y recepción de mercancía.
- Gestión de múltiples almacenes.
