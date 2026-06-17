<?php
// Cargar la cabecera (incluye manejador de sesión y conexión)
require_once 'carrito_handler.php';
require_once 'conexion.php';

// Si no es admin, redirigir a inicio (protección de seguridad)
if (!isset($_SESSION['usuario_rol']) || $_SESSION['usuario_rol'] !== 'admin') {
    header('Location: index.php');
    exit;
}

$error = null;
$exito = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agregar_producto'])) {
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $precio = floatval($_POST['precio']);
    $categoria = $_POST['categoria'];
    $oferta = htmlspecialchars(trim($_POST['oferta']));
    
    // Mapeo automático de tags según categoría
    $tag = "";
    $tag_class = "";
    switch($categoria) {
        case 'medicamentos': $tag = "Medicamento"; $tag_class = "tag-medicamento"; break;
        case 'vitaminas': $tag = "Vitamina"; $tag_class = "tag-vitamina"; break;
        case 'cuidado-personal': $tag = "Cuidado Personal"; $tag_class = "tag-cuidado"; break;
        case 'bebes': $tag = "Bebés"; $tag_class = "tag-bebe"; break;
    }

    // Procesar Imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['imagen']['tmp_name'];
        $file_name = $_FILES['imagen']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];

        if (in_array($file_ext, $allowed)) {
            $upload_dir = 'Imgs/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
            
            $new_file_name = uniqid('prod_', true) . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $dest_path)) {
                try {
                    $sql = "INSERT INTO productos (nombre, precio, imagen, categoria, tag, tag_class, oferta_popular) 
                            VALUES (:nombre, :precio, :imagen, :categoria, :tag, :tag_class, :oferta)";
                    $stmt = $pdo->prepare($sql);
                    $stmt->execute([
                        ':nombre' => $nombre,
                        ':precio' => $precio,
                        ':imagen' => $dest_path,
                        ':categoria' => $categoria,
                        ':tag' => $tag,
                        ':tag_class' => $tag_class,
                        ':oferta' => !empty($oferta) ? $oferta : null
                    ]);
                    $exito = "¡Producto '$nombre' agregado correctamente!";
                } catch (PDOException $e) {
                    $error = "Error en la base de datos: " . $e->getMessage();
                }
            } else {
                $error = "No se pudo guardar la imagen.";
            }
        } else {
            $error = "Formato de imagen no permitido (solo JPG, PNG, WEBP).";
        }
    } else {
        $error = "Debes subir una imagen del producto.";
    }
}

include 'header.php';
?>

<div id="contenedor" class="el-contenedor-padre">
    <section class="tienda-banner">
        <div class="tienda-banner-contenido">
            <h2>Gestión de Inventario</h2>
            <p>Agrega nuevos productos al catálogo de la farmacia de forma rápida.</p>
        </div>
    </section>

    <div id="contenido" class="lo-de-enmedio">
        <main class="lo-principal">
            <div class="confirmacion-tarjeta" style="max-width: 600px; margin: 0 auto;">
                <?php if ($exito): ?>
                    <div class="alerta-exito"><p>✅ <?php echo $exito; ?></p></div>
                <?php endif; ?>
                <?php if ($error): ?>
                    <div class="alerta-error"><p>⚠️ <?php echo $error; ?></p></div>
                <?php endif; ?>

                <form action="agregar_producto.php" method="POST" enctype="multipart/form-data" class="formulario-contacto">
                    <fieldset>
                        <legend>Detalles del Producto</legend>
                        
                        <div class="grupo-input">
                            <label for="nombre">Nombre del Producto:</label>
                            <input type="text" id="nombre" name="nombre" placeholder="Ej. Producto X" required>
                        </div>

                        <div class="form-row">
                            <div class="grupo-input">
                                <label for="precio">Precio ($):</label>
                                <input type="number" id="precio" name="precio" step="0.01" placeholder="0.00" required>
                            </div>
                            <div class="grupo-input">
                                <label for="categoria">Categoría:</label>
                                <select id="categoria" name="categoria" required>
                                    <option value="medicamentos">Medicamentos</option>
                                    <option value="vitaminas">Vitaminas</option>
                                    <option value="cuidado-personal">Cuidado Personal</option>
                                    <option value="bebes">Bebés</option>
                                </select>
                            </div>
                        </div>

                        <div class="grupo-input">
                            <label for="oferta">Etiqueta de Oferta (Opcional):</label>
                            <input type="text" id="oferta" name="oferta" placeholder="Ej. ¡2x1! o ¡Oferta!">
                        </div>

                        <div class="grupo-input">
                            <label for="imagen">Imagen del Producto:</label>
                            <input type="file" id="imagen" name="imagen" accept="image/*" class="input-file" required>
                        </div>

                        <button type="submit" name="agregar_producto" class="boton-enviar">Guardar Producto en BD</button>
                    </fieldset>
                </form>
                
                <div style="text-align: center; margin-top: 20px;">
                    <a href="productos.php" class="btn-secundario">← Volver al Catálogo</a>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include 'footer.php'; ?>
