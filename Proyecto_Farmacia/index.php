<?php
// Cargar la cabecera (incluye el manejador de sesión y productos)
include 'header.php';
?>

    <div id="contenedor" class="el-contenedor-padre">

        <!-- Hero Section -->
        <section id="inicio" class="hero-section">
            <img src="Imgs/Hero.png" alt="Hero Farmacia Local" class="imagen-hero">
            <div class="hero-overlay">
                <h2>Cuidamos de ti y de tu familia las 24 horas</h2>
                <p>Tu bienestar es nuestra prioridad. Encuentra medicamentos, vitaminas y cuidado personal con los mejores precios.</p>
                <a href="productos.php" class="btn-cta">Ver Productos</a>
            </div>
        </section>

        <div id="contenido" class="lo-de-enmedio">
            <main class="lo-principal">
                
                <!-- Sección de Noticias/Nosotros -->
                <article id="noticias" class="seccion-noticias">
                    <div class="noticias-contenido">
                        <h2>¡Nuevos Consultorios Médicos!</h2>
                        <p>Nos alegra anunciar la apertura de nuestras nuevas salas de consulta médica. Ahora contamos con médicos generales disponibles para atenderte de forma rápida y profesional todos los días de la semana.</p>
                        <p>Además, ampliamos nuestra farmacia para ofrecerte una mayor variedad de productos dermatológicos y suplementos alimenticios.</p>
                        <a href="#visitanos" class="btn-secundario">Ver Horarios y Ubicación</a>
                    </div>
                    <div class="noticias-imagen">
                        <img src="Imgs/us.jpg" alt="Nuestro equipo médico y farmacia">
                    </div>
                </article>

                <!-- Sección de Productos (Limitada y Oculta por defecto en el Inicio) -->
                <!-- Se activa mediante JavaScript al hacer clic en "Productos" en el navbar -->
                <section id="recomendados" class="seccion-productos seccion-productos-inicio">
                    <h2>Productos Destacados</h2>
                    <p class="subtitulo-seccion">Una pequeña muestra de nuestro catálogo de medicamentos y bienestar.</p>
                    
                    <!-- Grid de Productos (Muestra 4 productos) -->
                    <div class="grid-productos">
                        <?php 
                        try {
                            require_once 'conexion.php';
                            $stmt_inicio = $pdo->query("SELECT * FROM productos LIMIT 4");
                            $productos_inicio = $stmt_inicio->fetchAll();

                            if ($productos_inicio):
                                foreach ($productos_inicio as $producto): 
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
                                            <input type="hidden" name="redirect" value="index.php#recomendados">
                                            <button type="submit" class="btn-agregar">Agregar al carrito</button>
                                        </form>
                                    </div>
                                <?php endforeach; 
                            else:
                                echo "<p style='grid-column: 1/-1; text-align: center; color: #718096;'>Pronto tendremos productos destacados aquí.</p>";
                            endif;
                        } catch (PDOException $e) {
                            echo "<p style='grid-column: 1/-1; text-align: center; color: #e53e3e;'>Error al cargar productos.</p>";
                        }
                        ?>
                    </div>

                    <div style="text-align: center; margin-top: 35px;">
                        <a href="productos.php" class="btn-cta">Ver Todos los Productos →</a>
                    </div>
                </section>

                <!-- Sección Reubicada: Visítanos y Atención (Horarios + Ubicación Maps en 2 Columnas) -->
                <section id="visitanos" class="seccion-visitanos">
                    <div class="visitanos-contenedor">
                        
                        <!-- Columna 1: Horarios de Servicios -->
                        <div class="visitanos-columna-horarios">
                            <h2>Horarios de Servicios</h2>
                            <p class="subtitulo-seccion-izquierda">Atención médica y asistencia en sucursales.</p>
                            
                            <div class="tabla-contenedor">
                                <table class="tabla-chida">
                                    <thead>
                                        <tr>
                                            <th>Servicio</th>
                                            <th>Días</th>
                                            <th>Horario</th>
                                        </tr>
                                    </thead> 
                                    <tbody>
                                        <tr>
                                            <td class="servicio-nombre">Toma de Presión</td>
                                            <td>Lunes a Viernes</td>
                                            <td><span class="badge badge-horario">8:00 am - 2:00 pm</span></td>
                                        </tr>
                                        <tr>
                                            <td class="servicio-nombre">Aplicación de Inyecciones</td>
                                            <td>Todos los días</td>
                                            <td><span class="badge badge-24h">24 horas</span></td>
                                        </tr>
                                        <tr>
                                            <td class="servicio-nombre">Consulta Médica</td>
                                            <td>Lunes a Sábado</td>
                                            <td><span class="badge badge-horario">9:00 am - 8:00 pm</span></td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Columna 2: Ubícanos con Google Maps -->
                        <div class="visitanos-columna-mapa">
                            <h2>Ubícanos</h2>
                            <p class="subtitulo-seccion-izquierda">Encuentra nuestra sucursal más cercana.</p>
                            
                            <div class="mapa-contenedor">
                                <!-- Google Maps Iframe dinámico apuntando a 19.08637, -102.3407 -->
                                <iframe 
                                    src="https://maps.google.com/maps?q=19.08637,-102.3407&t=&z=15&ie=UTF8&iwloc=&output=embed" 
                                    width="100%" 
                                    height="280" 
                                    style="border:0; border-radius: 8px;" 
                                    allowfullscreen="" 
                                    loading="lazy" 
                                    referrerpolicy="no-referrer-when-downgrade">
                                </iframe>
                            </div>
                        </div>

                    </div>
                </section>

            </main>
        </div>

<?php
// Cargar el pie de página
include 'footer.php';
?>
