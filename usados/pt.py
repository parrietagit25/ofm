import os

# Estructura del proyecto
estructura = {
    "public": {
        "assets": {},
        "evara": {},
        "index.php": "<?php\n// Página principal\n",
        "catalogo.php": "<?php\n// Catálogo de productos\n"
    },
    "admin": {
        "assets": {},
        "dashboard.php": "<?php\n// Panel principal (admin o socio)\n"
    },
    "includes": {
        "db.php": "<?php\n// Conexión a base de datos\n",
        "auth.php": "<?php\n// Funciones de autenticación\n",
        "functions.php": "<?php\n// Funciones reutilizables\n"
    },
    "controllers": {
        "loginController.php": "<?php\n// Lógica de login\n",
        "ventaController.php": "<?php\n// Control de ventas\n",
        "qrController.php": "<?php\n// Generación y validación de QR\n"
    },
    "models": {
        "Usuario.php": "<?php\n// Modelo de Usuario\n",
        "Producto.php": "<?php\n// Modelo de Producto\n",
        "Venta.php": "<?php\n// Modelo de Venta\n"
    },
    "uploads": {},
    "sql": {
        "estructura_inicial.sql": "-- Script para crear la base de datos\n"
    }
}

def crear_estructura(base_path, estructura):
    for nombre, contenido in estructura.items():
        ruta = os.path.join(base_path, nombre)
        if isinstance(content := contenido, dict):  # si es carpeta
            os.makedirs(ruta, exist_ok=True)
            crear_estructura(ruta, content)
        else:  # si es archivo
            with open(ruta, 'w', encoding='utf-8') as archivo:
                archivo.write(content)

def main():
    proyecto = "mi_proyecto_php"
    base_path = os.path.join(os.getcwd(), proyecto)
    os.makedirs(base_path, exist_ok=True)
    crear_estructura(base_path, estructura)
    print(f"✅ Proyecto creado en: {base_path}")

if __name__ == "__main__":
    main()
