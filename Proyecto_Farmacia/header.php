<?php
// Cargar el manejador del carrito (este archivo ya inicia la sesión e incluye db_productos.php)
require_once 'carrito_handler.php';

// Obtener el nombre del archivo actual para saber en qué página estamos
$current_page = basename($_SERVER['PHP_SELF']);

// Contar los artículos en el carrito
$cart_count = 0;
if (isset($_SESSION['carrito'])) {
    foreach ($_SESSION['carrito'] as $item) {
        $cart_count += $item['cantidad'];
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farmacia Salud y Vida</title>
    <link rel="stylesheet" href="style.css">
    <!-- Google Fonts para un diseño moderno y amigable -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;600;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Header Principal -->
    <header class="header-principal">
        <div class="logo">
            <a href="index.php" style="text-decoration: none; display: flex; align-items: center; gap: 10px;">
                <span class="icono-cruz">✚</span>
                <h1>Farmacia <span>Salud y Vida</span></h1>
            </a>
        </div>

        <!-- Botón Menú Hamburguesa para Móvil -->
        <button class="menu-toggle" id="btn-menu" aria-label="Abrir menú">
            <span class="barra"></span>
            <span class="barra"></span>
            <span class="barra"></span>
        </button>

        <nav class="nav-principal" id="nav-menu">
            <ul>
                <li><a href="<?php echo ($current_page === 'index.php') ? '#inicio' : 'index.php#inicio'; ?>" class="<?php echo ($current_page === 'index.php' && !isset($_GET['section'])) ? 'activo' : ''; ?>">Inicio</a></li>
                <li><a href="<?php echo ($current_page === 'index.php') ? '#noticias' : 'index.php#noticias'; ?>">Noticias</a></li>
                
                <!-- Productos: ahora con overlay -->
                <li class="li-productos-container">
                    <a href="productos.php" id="nav-link-productos" class="<?php echo ($current_page === 'productos.php') ? 'activo' : ''; ?>">Productos</a>
                    
                    <!-- Overlay de Productos (Se activa con JS) -->
                    <div class="productos-overlay-mini" id="overlay-productos">
                        <div class="overlay-header">
                            <span>Muestra de Productos</span>
                        </div>
                        <div class="overlay-grid">
                            <?php 
                            // Intentar obtener productos de la base de datos para el overlay
                            try {
                                require_once 'conexion.php';
                                $stmt_overlay = $pdo->query("SELECT * FROM productos LIMIT 3");
                                $productos_overlay = $stmt_overlay->fetchAll();
                                
                                if ($productos_overlay):
                                    foreach ($productos_overlay as $p): 
                                    ?>
                                        <div class="overlay-item">
                                            <img src="<?php echo $p['imagen']; ?>" alt="<?php echo $p['nombre']; ?>">
                                            <div class="overlay-item-info">
                                                <span class="overlay-nombre"><?php echo $p['nombre']; ?></span>
                                                <span class="overlay-precio">$<?php echo number_format($p['precio'], 2); ?></span>
                                            </div>
                                        </div>
                                    <?php endforeach; 
                                else:
                                    echo "<p style='font-size: 11px; color: #718096;'>No hay productos aún.</p>";
                                endif;
                            } catch (PDOException $e) {
                                echo "<p style='font-size: 11px; color: #718096;'>Error al cargar.</p>";
                            }
                            ?>
                        </div>
                        <a href="productos.php" class="btn-ver-mas-overlay">Ver Catálogo Completo →</a>
                    </div>
                </li>
                
                <!-- Citas: Siempre visible. Si no está logueado, citas.php lo redirigirá a login -->
                <li><a href="citas.php" class="<?php echo ($current_page === 'citas.php') ? 'activo' : ''; ?>">Citas</a></li>
                
                <!-- Gestión de Productos: Solo si es ADMIN -->
                <?php if (isset($_SESSION['usuario_rol']) && strtolower($_SESSION['usuario_rol']) === 'admin'): ?>
                    <li><a href="agregar_producto.php" class="<?php echo ($current_page === 'agregar_producto.php') ? 'activo' : ''; ?>">+ Producto</a></li>
                <?php endif; ?>
                
                <!-- Historial: Visible solo si está logueado -->
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <li><a href="historial.php" class="<?php echo ($current_page === 'historial.php') ? 'activo' : ''; ?>">Historial Clínico</a></li>
                <?php endif; ?>
                
                <li><a href="<?php echo ($current_page === 'index.php') ? '#visitanos' : 'index.php#visitanos'; ?>">Visítanos</a></li>
                <li><a href="#" id="link-sugerencias-nav">Sugerencias</a></li>
                <li><a href="#" id="link-contacto-nav">Contacto</a></li>
            </ul>
        </nav>

        <div class="botones-auth">
            <!-- Botón de Carrito de Compras -->
            <button class="btn-cart-toggle" id="btn-cart-header" aria-label="Ver carrito">
                <?php include 'svg/cart.svg'; ?>
                <span class="cart-badge" id="cart-badge-count"><?php echo $cart_count; ?></span>
            </button>

            <!-- Renderizado dinámico según sesión activa -->
            <?php if (isset($_SESSION['usuario_id'])): ?>
                <span class="user-greeting">Hola, <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></span>
                <a href="logout.php" class="btn-estetico btn-login">Cerrar Sesión</a>
            <?php else: ?>
                <a href="login.php" class="btn-estetico btn-login">Iniciar Sesión</a>
                <a href="registro.php" class="btn-estetico btn-registro">Registrarse</a>
            <?php endif; ?>
        </div>
    </header>
