<?php
// Incluir cabecera común
include 'header.php';
require_once 'conexion.php';

// Valores por defecto
$nombre = "Anónimo";
$correo = "";
$sugerencia = "";
$imagen_subida = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitizar y obtener los datos
    if (!empty($_POST['nombre'])) {
        $nombre = htmlspecialchars(trim($_POST['nombre']));
    }
    if (!empty($_POST['correo'])) {
        $correo = htmlspecialchars(trim($_POST['correo']));
    }
    if (!empty($_POST['sugerencia'])) {
        $sugerencia = htmlspecialchars(trim($_POST['sugerencia']));
    }

    // Procesar la subida del archivo si existe
    if (isset($_FILES['imagen_sugerencia']) && $_FILES['imagen_sugerencia']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['imagen_sugerencia']['tmp_name'];
        $file_name = $_FILES['imagen_sugerencia']['name'];
        $file_size = $_FILES['imagen_sugerencia']['size'];
        $file_type = $_FILES['imagen_sugerencia']['type'];

        // Validar que sea una imagen (extensión)
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));

        if (in_array($file_ext, $allowed_extensions)) {
            // Asegurar que el directorio de subidas exista
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0755, true);
            }

            // Generar un nombre único para evitar colisiones
            $new_file_name = uniqid('sug_', true) . '.' . $file_ext;
            $dest_path = $upload_dir . $new_file_name;

            if (move_uploaded_file($file_tmp, $dest_path)) {
                $imagen_subida = $dest_path;
            } else {
                $error = "Ocurrió un error al guardar la imagen en el servidor.";
            }
        } else {
            $error = "Tipo de archivo no permitido. Solo se permiten imágenes JPG, JPEG, PNG o GIF.";
        }
    }

    // GUARDAR EN LA BASE DE DATOS si no hay errores previos
    if (!$error) {
        try {
            $sql = "INSERT INTO sugerencias (nombre, email, mensaje, imagen) VALUES (:nombre, :email, :mensaje, :imagen)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':nombre' => $nombre,
                ':email' => $correo,
                ':mensaje' => $sugerencia,
                ':imagen' => $imagen_subida
            ]);
        } catch (PDOException $e) {
            $error = "Error al guardar en la base de datos: " . $e->getMessage();
        }
    }
} else {
    // Redirigir a inicio si no es petición POST
    header('Location: index.php');
    exit;
}
?>

    <div id="contenedor" class="el-contenedor-padre">

        <!-- Banner de Confirmación -->
        <section class="tienda-banner confirmacion-banner">
            <div class="tienda-banner-contenido">
                <h2>¡Sugerencia Recibida!</h2>
                <p>Agradecemos tu valiosa opinión para ayudarnos a mejorar cada día.</p>
            </div>
        </section>

        <div id="contenido" class="lo-de-enmedio">
            <main class="lo-principal">

                <div class="confirmacion-tarjeta">
                    <?php if ($error): ?>
                        <div class="alerta-error">
                            <h3>⚠️ Error al procesar</h3>
                            <p><?php echo $error; ?></p>
                        </div>
                    <?php else: ?>
                        <div class="alerta-exito">
                            <h3>✅ ¡Muchas gracias, <?php echo $nombre; ?>!</h3>
                            <p>Hemos registrado tu sugerencia exitosamente en nuestro sistema.</p>
                        </div>
                    <?php endif; ?>

                    <div class="detalles-resumen">
                        <h3>Detalles de la Sugerencia:</h3>
                        <p><strong>Remitente:</strong> <?php echo $nombre; ?> (<?php echo $correo; ?>)</p>
                        <p><strong>Mensaje:</strong></p>
                        <blockquote class="mensaje-resumen">
                            "<?php echo nl2br($sugerencia); ?>"
                        </blockquote>

                        <?php if ($imagen_subida): ?>
                            <p><strong>Evidencia/Imagen adjunta:</strong></p>
                            <div class="imagen-sugerencia-contenedor">
                                <img src="<?php echo $imagen_subida; ?>" alt="Imagen de Sugerencia" class="imagen-sugerencia-vista">
                                <p class="imagen-nombre-subido"><?php echo htmlspecialchars($file_name); ?> (<?php echo round($file_size / 1024, 2); ?> KB)</p>
                            </div>
                        <?php endif; ?>
                    </div>

                    <div style="text-align: center; margin-top: 30px;">
                        <a href="index.php" class="btn-cta">Volver al Inicio</a>
                    </div>
                </div>

            </main>
        </div>

<?php
// Incluir pie de página común
include 'footer.php';
?>
