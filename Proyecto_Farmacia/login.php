<?php
// Cargar manejador del carrito (este archivo ya inicia sesión)
require_once 'carrito_handler.php';
require_once 'conexion.php';

// Si ya inició sesión, redirigir a inicio
if (isset($_SESSION['usuario_id'])) {
    header('Location: index.php');
    exit;
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);

    if (!empty($email) && !empty($password)) {
        try {
            // Consultar si existe el usuario
            $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email");
            $stmt->execute(['email' => $email]);
            $usuario = $stmt->fetch();

            // Verificar la contraseña usando password_verify
            if ($usuario && password_verify($password, $usuario['password'])) {
                // Credenciales correctas, iniciar sesión
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nombre'] = $usuario['nombre'];
                $_SESSION['usuario_email'] = $usuario['email'];
                $_SESSION['usuario_rol'] = $usuario['rol']; // Guardar el rol (admin o paciente)

                header('Location: index.php');
                exit;
            } else {
                $error = "Correo electrónico o contraseña incorrectos.";
            }
        } catch (PDOException $e) {
            // Mostramos el error real para diagnosticar
            $error = "Error en el servidor: " . $e->getMessage();
        }
    } else {
        $error = "Por favor, completa todos los campos.";
    }
}

// Cargar la cabecera compartida
include 'header.php';
?>

    <div id="contenedor" class="el-contenedor-padre">

        <!-- Banner de Login -->
        <section class="tienda-banner">
            <div class="tienda-banner-contenido">
                <h2>Iniciar Sesión</h2>
                <p>Ingresa a tu cuenta para agendar citas, consultar tu historial clínico y ver tus pedidos.</p>
            </div>
        </section>

        <div id="contenido" class="lo-de-enmedio">
            <main class="lo-principal">

                <div class="confirmacion-tarjeta auth-tarjeta">
                    <?php if ($error): ?>
                        <div class="alerta-error">
                            <p>⚠️ <?php echo $error; ?></p>
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST" class="formulario-contacto">
                        <fieldset>
                            <legend>Identifícate</legend>
                            <p class="form-descripcion">Usa tu correo y contraseña registrados. Puedes usar <strong>paciente@gmail.com</strong> / <strong>123456</strong> para pruebas rápidas.</p>
                            
                            <div class="grupo-input">
                                <label for="email">Correo Electrónico:</label>
                                <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
                            </div>
                            
                            <div class="grupo-input">
                                <label for="password">Contraseña:</label>
                                <input type="password" id="password" name="password" placeholder="Tu contraseña" required>
                            </div>
                            
                            <button type="submit" class="boton-enviar">Iniciar Sesión</button>
                        </fieldset>
                    </form>

                    <div style="text-align: center; margin-top: 20px;" class="auth-enlace-cambio">
                        <p>¿Aún no tienes cuenta? <a href="registro.php" style="color: var(--principal); font-weight: 600; text-decoration: none;">Regístrate aquí</a></p>
                    </div>
                </div>

            </main>
        </div>

<?php
// Cargar el pie de página común
include 'footer.php';
?>
