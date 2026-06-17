<?php
// Iniciar sesión para almacenar el carrito si no está iniciada ya
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cargar la "base de datos" de productos para validaciones (ahora desde MySQL)
require_once 'conexion.php';

// Procesar acciones del carrito
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];
    $redirect = isset($_POST['redirect']) ? $_POST['redirect'] : 'index.php';
    
    // Crear el arreglo de carrito en la sesión si no existe
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    if ($action === 'agregar') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        
        try {
            $stmt_cart = $pdo->prepare("SELECT * FROM productos WHERE id = :id");
            $stmt_cart->execute(['id' => $id]);
            $producto = $stmt_cart->fetch();

            if ($producto) {
                // Si ya existe el producto en el carrito, aumentar cantidad
                if (isset($_SESSION['carrito'][$id])) {
                    $_SESSION['carrito'][$id]['cantidad']++;
                } else {
                    // Agregar producto por primera vez
                    $_SESSION['carrito'][$id] = [
                        'nombre' => $producto['nombre'],
                        'precio' => $producto['precio'],
                        'imagen' => $producto['imagen'],
                        'cantidad' => 1
                    ];
                }
            }
        } catch (PDOException $e) {
            // Manejar error silenciosamente o loguear
        }
    } 
    
    elseif ($action === 'eliminar') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if (isset($_SESSION['carrito'][$id])) {
            unset($_SESSION['carrito'][$id]);
        }
    } 
    
    elseif ($action === 'vaciar') {
        $_SESSION['carrito'] = [];
    }

    // Redirigir de vuelta a la página de origen para evitar reenvío de formulario
    header('Location: ' . $redirect);
    exit;
}
?>
