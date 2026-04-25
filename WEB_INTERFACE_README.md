# Interfaz Web del Sistema de Inventario y Facturación

## 🚀 Acceso a la Interfaz Web

### Opción 1: Acceder desde la raíz del proyecto
```
http://localhost:8001/
```
Cuando accedas desde un navegador web, automáticamente se cargará la interfaz web.

### Opción 2: Acceder directamente a la interfaz
```
http://localhost:8001/web
```

## 📋 Funcionalidades de la Interfaz Web

### 1. Dashboard
- **Vista general** del sistema con estadísticas
- Muestra el número total de productos, categorías, proveedores y facturas
- Se actualiza automáticamente al realizar cambios

### 2. Gestión de Categorías
- **Listar** todas las categorías
- **Crear** nuevas categorías
- **Editar** categorías existentes
- **Eliminar** categorías

**Campos requeridos para categorías:**
- Nombre (obligatorio, único)

### 3. Gestión de Proveedores
- **Listar** todos los proveedores
- **Crear** nuevos proveedores
- **Editar** proveedores existentes
- **Eliminar** proveedores

**Campos para proveedores:**
- Nombre (obligatorio)
- Email (opcional)
- Teléfono (opcional)
- Dirección (opcional)

### 4. Gestión de Productos
- **Listar** todos los productos con información completa
- **Crear** nuevos productos
- **Editar** productos existentes
- **Eliminar** productos

**Campos para productos:**
- Nombre (obligatorio)
- SKU (opcional)
- Descripción (opcional)
- Precio de venta (opcional)
- Stock (opcional)
- Categoría (opcional, debe existir)
- Proveedor (opcional, debe existir)

### 5. Gestión de Clientes
- **Listar** todos los clientes
- **Crear** nuevos clientes
- **Editar** clientes existentes
- **Eliminar** clientes

**Campos para clientes:**
- Nombre (obligatorio)
- Documento (opcional)
- Email (opcional)
- Teléfono (opcional)
- Dirección (opcional)

### 6. Gestión de Facturas
- **Listar** todas las facturas
- **Crear** nuevas facturas con múltiples productos
- **Eliminar** facturas

**Proceso para crear facturas:**
1. Seleccionar un cliente existente
2. Agregar uno o más productos
3. Especificar cantidades para cada producto
4. El sistema calcula automáticamente subtotales, impuestos (16% IVA) y totales
5. Al guardar, el stock de productos se reduce automáticamente

### 7. Control de Stock
- **Ver movimientos** de inventario
- Muestra entradas, salidas y ajustes de stock
- Historial completo de cambios en el inventario

## 🎯 Cómo Usar la Interfaz

### Navegación
- Usa el **menú lateral** para navegar entre secciones
- Cada sección se carga dinámicamente sin recargar la página

### Crear Registros
1. Ve a la sección correspondiente
2. Haz clic en "**Nueva [Entidad]**"
3. Completa el formulario
4. Haz clic en "**Guardar**"

### Editar Registros
1. En la tabla, haz clic en "**Editar**" junto al registro
2. Modifica los campos necesarios
3. Haz clic en "**Guardar**"

### Eliminar Registros
1. En la tabla, haz clic en "**Eliminar**" junto al registro
2. Confirma la eliminación

### Crear Facturas
1. Ve a la sección "**Facturas**"
2. Haz clic en "**Nueva Factura**"
3. Selecciona un cliente del dropdown
4. Agrega productos usando "**Agregar Producto**"
5. Selecciona productos y especifica cantidades
6. Revisa los totales calculados automáticamente
7. Haz clic en "**Crear Factura**"

## 🔧 Tecnologías Utilizadas

- **Frontend:** HTML5, CSS3, JavaScript (ES6+)
- **UI Framework:** Bootstrap 5
- **Icons:** Font Awesome
- **API Consumption:** Fetch API (nativo de JavaScript)
- **Backend:** Laravel 11 (APIs RESTful)

## 🌐 API Endpoints Utilizados

La interfaz web consume las siguientes APIs:

```
GET    /api/categories          # Listar categorías
POST   /api/categories          # Crear categoría
PUT    /api/categories/{id}     # Actualizar categoría
DELETE /api/categories/{id}     # Eliminar categoría

GET    /api/suppliers           # Listar proveedores
POST   /api/suppliers           # Crear proveedor
PUT    /api/suppliers/{id}      # Actualizar proveedor
DELETE /api/suppliers/{id}      # Eliminar proveedor

GET    /api/products            # Listar productos
POST   /api/products            # Crear producto
PUT    /api/products/{id}       # Actualizar producto
DELETE /api/products/{id}       # Eliminar producto

GET    /api/customers           # Listar clientes
POST   /api/customers           # Crear cliente
PUT    /api/customers/{id}      # Actualizar cliente
DELETE /api/customers/{id}      # Eliminar cliente

GET    /api/invoices            # Listar facturas
POST   /api/invoices            # Crear factura
DELETE /api/invoices/{id}       # Eliminar factura

GET    /api/stock-movements     # Ver movimientos de stock
```

## ⚠️ Consideraciones Importantes

1. **Dependencias:** Antes de crear productos, debes tener categorías y proveedores
2. **Dependencias:** Antes de crear facturas, debes tener clientes y productos
3. **Stock automático:** Las facturas reducen automáticamente el stock de productos
4. **Validaciones:** El frontend valida los campos requeridos antes de enviar
5. **Errores:** Los errores de la API se muestran en alertas del navegador

## 🚀 Inicio Rápido

1. **Asegúrate de que el servidor esté corriendo:**
   ```bash
   cd /home/gabriel/Proyectos/sysinv001
   php artisan serve --host=0.0.0.0 --port=8001
   ```

2. **Abre tu navegador y ve a:**
   ```
   http://localhost:8001/
   ```

3. **Comienza creando categorías y proveedores, luego productos y clientes**

¡La interfaz web está lista para usar! 🎉