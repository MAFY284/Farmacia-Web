<?php
// Cargar la cabecera (incluye manejador de sesión y db_productos)
include 'header.php';
?>

    <div id="contenedor" class="el-contenedor-padre">

        <!-- Banner de Tienda -->
        <section class="tienda-banner">
            <div class="tienda-banner-contenido">
                <h2>Catálogo Completo de Productos</h2>
                <p>Encuentra todo lo que necesitas para tu salud, higiene y bienestar. Envío a domicilio disponible.</p>
            </div>
        </section>

        <div id="contenido" class="lo-de-enmedio">
            <main class="lo-principal">

                <!-- Sección de Productos -->
                <section class="seccion-productos seccion-productos-tienda">
                    
                    <!-- Barra de Categorías / Filtros -->
                    <div class="barra-categorias">
                        <button class="btn-categoria activo" data-filtro="todos">Todos</button>
                        <button class="btn-categoria" data-filtro="medicamentos">Medicamentos</button>
                        <button class="btn-categoria" data-filtro="vitaminas">Vitaminas y Suplementos</button>
                        <button class="btn-categoria" data-filtro="cuidado-personal">Cuidado Personal</button>
                        <button class="btn-categoria" data-filtro="bebes">Bebés y Maternidad</button>
                    </div>

                    <!-- Grid de Productos -->
                    <div class="grid-productos">
                        <?php 
                        try {
                            require_once 'conexion.php';
                            $stmt_tienda = $pdo->query("SELECT * FROM productos ORDER BY id DESC");
                            $productos_db_real = $stmt_tienda->fetchAll();

                            if ($productos_db_real):
                                foreach ($productos_db_real as $producto): 
                                ?>
                                    <div class="tarjeta-producto" data-categoria="<?php echo $producto['categoria']; ?>">
                                        <div class="badges-producto">
                                            <span class="badge-tag <?php echo $producto['tag_class']; ?>"><?php echo $producto['tag']; ?></span>
                                            <?php if (!empty($producto['oferta_popular'])): ?>
                                                <span class="etiqueta-oferta <?php echo (strpos($producto['oferta_popular'], 'Popular') !== false) ? 'estrella' : ''; ?>">
                                                    <?php echo $producto['oferta_popular']; ?>
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                        <div class="producto-img-container">
                                            <img src="<?php echo $producto['imagen']; ?>" alt="<?php echo $producto['nombre']; ?>">
                                        </div>
                                        <h3><?php echo $producto['nombre']; ?></h3>
                                        <p class="precio">$<?php echo number_format($producto['precio'], 2); ?></p>
                                        
                                        <form action="carrito_handler.php" method="POST" style="width: 100%;">
                                            <input type="hidden" name="action" value="agregar">
                                            <input type="hidden" name="id" value="<?php echo $producto['id']; ?>">
                                            <input type="hidden" name="redirect" value="productos.php">
                                            <button type="submit" class="btn-agregar">Agregar al carrito</button>
                                        </form>
                                    </div>
                                <?php endforeach; 
                            else:
                                echo "<p style='grid-column: 1/-1; text-align: center; padding: 40px; color: #718096;'>No hay productos registrados en el catálogo todavía.</p>";
                            endif;
                        } catch (PDOException $e) {
                            echo "<p style='grid-column: 1/-1; text-align: center; padding: 40px; color: #e53e3e;'>Error al cargar el catálogo: " . $e->getMessage() . "</p>";
                        }
                        ?>
                    </div>
                    
                </section>

            </main>
        </div>

<?php
// Cargar el pie de página
include 'footer.php';
?>
