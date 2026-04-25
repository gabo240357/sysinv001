#!/bin/bash

# Script de prueba para la interfaz web del sistema de inventario
echo "🧪 Probando la interfaz web del sistema de inventario..."

# Verificar que el servidor esté corriendo
echo "1. Verificando que el servidor esté corriendo..."
if curl -s http://localhost:8001/ > /dev/null; then
    echo "✅ Servidor corriendo en http://localhost:8001/"
else
    echo "❌ Servidor no está corriendo. Ejecuta: php artisan serve --host=0.0.0.0 --port=8001"
    exit 1
fi

# Verificar que la interfaz web se carga
echo "2. Verificando que la interfaz web se carga..."
if curl -s http://localhost:8001/ | grep -q "Sistema de Inventario"; then
    echo "✅ Interfaz web cargándose correctamente"
else
    echo "❌ Interfaz web no se carga correctamente"
    exit 1
fi

# Verificar que las APIs respondan
echo "3. Probando APIs..."
if curl -s -H "Accept: application/json" http://localhost:8001/api/categories > /dev/null; then
    echo "✅ API de categorías funcionando"
else
    echo "❌ API de categorías no responde"
fi

if curl -s -H "Accept: application/json" http://localhost:8001/api/products > /dev/null; then
    echo "✅ API de productos funcionando"
else
    echo "❌ API de productos no responde"
fi

echo ""
echo "🎉 ¡La interfaz web está lista!"
echo "   Accede desde tu navegador: http://localhost:8001/"
echo ""
echo "📖 Lee el README completo en: WEB_INTERFACE_README.md"