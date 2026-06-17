        <!-- Footer Principal -->
        <footer class="el-final-de-to">
            <div class="footer-columna">
                <h3>Nosotros</h3>
                <ul>
                    <li><a href="<?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? '#noticias' : 'index.php#noticias'; ?>">Misión</a></li>
                    <li><a href="<?php echo (basename($_SERVER['PHP_SELF']) === 'index.php') ? '#noticias' : 'index.php#noticias'; ?>">Visión</a></li>
                </ul>
            </div>
            <div class="footer-columna">
                <h3>Legal</h3>
                <ul>
                    <li><a href="#">Privacidad</a></li>
                    <li><a href="#">Términos</a></li>
                </ul>
            </div>
            <div class="footer-columna">
                <h3>Redes</h3>
                <ul>
                    <li><a href="#">Facebook</a></li>
                    <li><a href="#">Instagram</a></li>
                </ul>
            </div>
            <div class="footer-columna">
                <h3>Contacto</h3>
                <ul>
                    <li>Tel: 123-456-7890</li>
                    <li>info@farmacia.com</li>
                    <li><a href="#" id="link-contacto-footer" class="link-destacado-contacto">¡Escríbenos un Mensaje!</a></li>
                </ul>
            </div>
            <div class="footer-columna">
                <h3>Navegación</h3>
                <ul>
                    <li><a href="productos.php">Ir a Tienda</a></li>
                    <li><a href="#inicio" class="link-subir">Subir al Inicio ↑</a></li>
                </ul>
            </div>
        </footer>

    </div> <!-- Fin de #contenedor -->


    <div id="modal-contacto" class="modal-container">
        <div class="modal-contenido">
            <button class="cerrar-modal" id="btn-cerrar-contacto" aria-label="Cerrar formulario">&times;</button>
            <form action="#" method="POST" class="formulario-contacto">
                <fieldset>
                    <legend>Envíanos tus dudas</legend>
                    <p class="form-descripcion">Responderemos tu mensaje al correo proporcionado en menos de 24 horas.</p>
                    <div class="grupo-input">
                        <label for="nombre">Nombre:</label>
                        <input type="text" id="nombre" name="nombre" placeholder="Tu nombre" required>
                    </div>
                    <div class="grupo-input">
                        <label for="correo">Email:</label>
                        <input type="email" id="correo" name="correo" placeholder="tu@email.com" required>
                    </div>
                    <div class="grupo-input">
                        <label for="mensaje">Mensaje:</label>
                        <textarea id="mensaje" name="mensaje" placeholder="Escribe tu duda o consulta aquí..." required></textarea>
                    </div>
                    <button type="submit" class="boton-enviar">Enviar Mensaje</button>
                </fieldset>
            </form>
        </div>
    </div>

    <div id="modal-sugerencias" class="modal-container">
        <div class="modal-contenido">
            <button class="cerrar-modal" id="btn-cerrar-sugerencias" aria-label="Cerrar formulario">&times;</button>
            <!-- Envío al script backend en PHP con multipart/form-data -->
            <form action="procesar_sugerencia.php" method="POST" enctype="multipart/form-data" class="formulario-contacto">
                <fieldset>
                    <legend>Buzón de Sugerencias</legend>
                    <p class="form-descripcion">Tu opinión nos ayuda a mejorar constantemente. Puedes adjuntar una foto o captura si lo deseas.</p>
                    
                    <div class="grupo-input">
                        <label for="sug-nombre">Tu Nombre:</label>
                        <input type="text" id="sug-nombre" name="nombre" placeholder="Tu nombre (opcional)">
                    </div>
                    
                    <div class="grupo-input">
                        <label for="sug-correo">Tu Email:</label>
                        <input type="email" id="sug-correo" name="correo" placeholder="tu@email.com" required>
                    </div>
                    
                    <div class="grupo-input">
                        <label for="sug-mensaje">Sugerencia o Queja:</label>
                        <textarea id="sug-mensaje" name="sugerencia" placeholder="Escribe tu sugerencia detalladamente aquí..." required></textarea>
                    </div>
                    
                    <div class="grupo-input">
                        <label for="sug-imagen">Adjuntar una Imagen (PNG, JPG):</label>
                        <input type="file" id="sug-imagen" name="imagen_sugerencia" accept="image/*" class="input-file">
                    </div>
                    
                    <button type="submit" class="boton-enviar">Enviar Sugerencia</button>
                </fieldset>
            </form>
        </div>
    </div>

    <div id="modal-carrito" class="modal-container">
        <div class="modal-contenido modal-carrito-ancho">
            <button class="cerrar-modal" id="btn-cerrar-carrito" aria-label="Cerrar carrito">&times;</button>
            <div class="carrito-estructura">
                <h2>Tu Carrito de Compras</h2>
                
                <?php if (empty($_SESSION['carrito'])): ?>
                    <div class="carrito-vacio">
                        <p>No tienes productos agregados al carrito de compras.</p>
                        <a href="productos.php" class="btn-cta" style="margin-top: 15px; display: inline-block;">Ir a Tienda</a>
                    </div>
                <?php else: ?>
                    <div class="carrito-lista">
                        <?php 
                        $total_general = 0;
                        foreach ($_SESSION['carrito'] as $id => $item): 
                            $subtotal = $item['precio'] * $item['cantidad'];
                            $total_general += $subtotal;
                        ?>
                            <div class="carrito-item">
                                <img src="<?php echo $item['imagen']; ?>" alt="<?php echo $item['nombre']; ?>" class="carrito-item-img">
                                <div class="carrito-item-detalles">
                                    <h4><?php echo $item['nombre']; ?></h4>
                                    <p class="carrito-item-precio">$<?php echo number_format($item['precio'], 2); ?> c/u</p>
                                </div>
                                <div class="carrito-item-cantidad">
                                    <span>Cant: <?php echo $item['cantidad']; ?></span>
                                    <span class="carrito-item-subtotal">Subtotal: $<?php echo number_format($subtotal, 2); ?></span>
                                </div>
                                <form action="carrito_handler.php" method="POST" style="margin: 0;">
                                    <input type="hidden" name="action" value="eliminar">
                                    <input type="hidden" name="id" value="<?php echo $id; ?>">
                                    <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                    <button type="submit" class="btn-eliminar-item" title="Eliminar del carrito">❌</button>
                                </form>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="carrito-resumen">
                        <div class="carrito-total">
                            <span>Total a Pagar:</span>
                            <span class="total-monto">$<?php echo number_format($total_general, 2); ?></span>
                        </div>
                        <div class="carrito-acciones">
                            <form action="carrito_handler.php" method="POST" style="margin: 0;">
                                <input type="hidden" name="action" value="vaciar">
                                <input type="hidden" name="redirect" value="<?php echo $_SERVER['REQUEST_URI']; ?>">
                                <button type="submit" class="btn-vaciar-carrito">Vaciar Carrito</button>
                            </form>
                            <button class="btn-pagar-carrito" onclick="simularCompra()">Proceder al Pago</button>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        // --- 1. Menú Hamburguesa Responsivo ---
        const btnMenu = document.getElementById('btn-menu');
        const navMenu = document.getElementById('nav-menu');

        if (btnMenu && navMenu) {
            btnMenu.addEventListener('click', () => {
                navMenu.classList.toggle('activo');
                btnMenu.classList.toggle('activo');
            });

            // Cerrar menú al hacer clic en un enlace de navegación
            document.querySelectorAll('.nav-principal a').forEach(link => {
                link.addEventListener('click', () => {
                    navMenu.classList.remove('activo');
                    btnMenu.classList.remove('activo');
                });
            });
        }

        // --- 2. Control de Modales ---
        // Helper para abrir un modal
        function abrirModal(modal) {
            if (modal) {
                modal.classList.add('activo');
                document.body.style.overflow = 'hidden'; // Prevenir scroll
            }
        }

        // Helper para cerrar un modal
        function cerrarModal(modal) {
            if (modal) {
                modal.classList.remove('activo');
                document.body.style.overflow = ''; // Restaurar scroll
            }
        }

        // Configuración Modal Contacto
        const modalContacto = document.getElementById('modal-contacto');
        const btnAbrirContactoNav = document.getElementById('link-contacto-nav');
        const btnAbrirContactoFooter = document.getElementById('link-contacto-footer');
        const btnCerrarContacto = document.getElementById('btn-cerrar-contacto');

        if (modalContacto) {
            if (btnAbrirContactoNav) btnAbrirContactoNav.addEventListener('click', (e) => { e.preventDefault(); abrirModal(modalContacto); });
            if (btnAbrirContactoFooter) btnAbrirContactoFooter.addEventListener('click', (e) => { e.preventDefault(); abrirModal(modalContacto); });
            if (btnCerrarContacto) btnCerrarContacto.addEventListener('click', () => cerrarModal(modalContacto));
            
            modalContacto.addEventListener('click', (e) => { if (e.target === modalContacto) cerrarModal(modalContacto); });
        }

        // Configuración Modal Sugerencias
        const modalSugerencias = document.getElementById('modal-sugerencias');
        const btnAbrirSugerencias = document.getElementById('link-sugerencias-nav');
        const btnCerrarSugerencias = document.getElementById('btn-cerrar-sugerencias');

        if (modalSugerencias) {
            if (btnAbrirSugerencias) btnAbrirSugerencias.addEventListener('click', (e) => { e.preventDefault(); abrirModal(modalSugerencias); });
            if (btnCerrarSugerencias) btnCerrarSugerencias.addEventListener('click', () => cerrarModal(modalSugerencias));
            
            modalSugerencias.addEventListener('click', (e) => { if (e.target === modalSugerencias) cerrarModal(modalSugerencias); });
        }

        // Configuración Modal Carrito
        const modalCarrito = document.getElementById('modal-carrito');
        const btnAbrirCarrito = document.getElementById('btn-cart-header');
        const btnCerrarCarrito = document.getElementById('btn-cerrar-carrito');

        if (modalCarrito) {
            if (btnAbrirCarrito) btnAbrirCarrito.addEventListener('click', (e) => { e.preventDefault(); abrirModal(modalCarrito); });
            if (btnCerrarCarrito) btnCerrarCarrito.addEventListener('click', () => cerrarModal(modalCarrito));
            
            modalCarrito.addEventListener('click', (e) => { if (e.target === modalCarrito) cerrarModal(modalCarrito); });
        }

        // Cerrar cualquier modal abierto con la tecla Escape
        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') {
                cerrarModal(modalContacto);
                cerrarModal(modalSugerencias);
                cerrarModal(modalCarrito);
            }
        });

        // --- 3. Revelar Productos (Ahora con Overlay) ---
        const navLinkProductos = document.getElementById('nav-link-productos');
        const overlayProductos = document.getElementById('overlay-productos');

        if (navLinkProductos && overlayProductos) {
            // Mostrar al pasar el mouse (Desktop)
            navLinkProductos.parentElement.addEventListener('mouseenter', () => {
                if (window.innerWidth > 768) overlayProductos.classList.add('activo');
            });

            navLinkProductos.parentElement.addEventListener('mouseleave', () => {
                if (window.innerWidth > 768) overlayProductos.classList.remove('activo');
            });

            // Toggle al hacer clic (Móvil o Desktop)
            navLinkProductos.addEventListener('click', (e) => {
                // Si estamos en el index, evitamos la navegación para mostrar el overlay
                // Pero el usuario pidió que al presionar salga la label sobrepuesta
                e.preventDefault();
                overlayProductos.classList.toggle('activo');
            });

            // Cerrar al hacer clic fuera
            document.addEventListener('click', (e) => {
                if (!navLinkProductos.contains(e.target) && !overlayProductos.contains(e.target)) {
                    overlayProductos.classList.remove('activo');
                }
            });
        }

        // --- 4. Enviar Formulario de Contacto (Simulado) ---
        const formContactoModal = document.querySelector('#modal-contacto .formulario-contacto');
        if (formContactoModal) {
            formContactoModal.addEventListener('submit', (e) => {
                e.preventDefault();
                alert('¡Gracias por escribirnos! Tu mensaje ha sido enviado correctamente y nos pondremos en contacto contigo pronto.');
                formContactoModal.reset();
                cerrarModal(modalContacto);
            });
        }

        // --- 5. Simular Compra de Carrito ---
        function simularCompra() {
            alert('🛒 ¡Compra Simulada con Éxito!\nHemos recibido tu pedido en nuestro sistema de base de datos simulado de Farmacia Salud y Vida.');
            // Vaciar el carrito mediante un submit simulado
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'carrito_handler.php';
            
            const actionInput = document.createElement('input');
            actionInput.type = 'hidden';
            actionInput.name = 'action';
            actionInput.value = 'vaciar';
            form.appendChild(actionInput);
            
            const redirectInput = document.createElement('input');
            redirectInput.type = 'hidden';
            redirectInput.name = 'redirect';
            redirectInput.value = '<?php echo $_SERVER["REQUEST_URI"]; ?>';
            form.appendChild(redirectInput);
            
            document.body.appendChild(form);
            form.submit();
        }

        // --- 6. Filtrado de Productos por Categoría (Sólo productos.php) ---
        const btnCategorias = document.querySelectorAll('.btn-categoria');
        const tarjetasProductos = document.querySelectorAll('.tarjeta-producto');

        if (btnCategorias.length > 0 && tarjetasProductos.length > 0) {
            btnCategorias.forEach(boton => {
                boton.addEventListener('click', () => {
                    // 1. Manejar estado activo de botones
                    btnCategorias.forEach(b => b.classList.remove('activo'));
                    boton.classList.add('activo');

                    // 2. Filtrar productos
                    const filtro = boton.getAttribute('data-filtro');

                    tarjetasProductos.forEach(tarjeta => {
                        const categoria = tarjeta.getAttribute('data-categoria');
                        
                        if (filtro === 'todos' || filtro === categoria) {
                            tarjeta.classList.remove('oculto');
                            // Pequeña animación de entrada
                            tarjeta.style.animation = 'deslizarAbajo 0.3s ease forwards';
                        } else {
                            tarjeta.classList.add('oculto');
                        }
                    });
                });
            });
        }
    </script>
</body>
</html>
