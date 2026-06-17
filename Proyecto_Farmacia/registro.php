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
$exito = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = htmlspecialchars(trim($_POST['nombre']));
    $email = htmlspecialchars(trim($_POST['email']));
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $rol = isset($_POST['rol']) ? $_POST['rol'] : 'paciente';

    if (!empty($nombre) && !empty($email) && !empty($password) && !empty($confirm_password)) {
        if ($password !== $confirm_password) {
            $error = "Las contraseñas ingresadas no coinciden.";
        } elseif (strlen($password) < 6) {
            $error = "La contraseña debe tener al menos 6 caracteres.";
        } else {
            try {
                // Verificar si el correo ya está registrado
                $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email");
                $stmt->execute(['email' => $email]);
                
                if ($stmt->fetch()) {
                    $error = "Este correo electrónico ya se encuentra registrado.";
                } else {
                    // Cifrar la contraseña de forma segura
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                    // Insertar nuevo usuario con ROL
                    $insert_stmt = $pdo->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (:nombre, :email, :password, :rol)");
                    $insert_stmt->execute([
                        'nombre' => $nombre,
                        'email' => $email,
                        'password' => $hashed_password,
                        'rol' => $rol
                    ]);

                    // Obtener el ID insertado
                    $new_id = $pdo->lastInsertId();

                    // Iniciar sesión automáticamente
                    $_SESSION['usuario_id'] = $new_id;
                    $_SESSION['usuario_nombre'] = $nombre;
                    $_SESSION['usuario_email'] = $email;
                    $_SESSION['usuario_rol'] = $rol;

                    header('Location: index.php');
                    exit;
                }
            } catch (PDOException $e) {
                $error = "Error al intentar crear la cuenta: " . $e->getMessage();
            }
        }
    } else {
        $error = "Por favor, completa todos los campos del formulario.";
    }
}

// Cargar la cabecera compartida
include 'header.php';
?>

    <div id="contenedor" class="el-contenedor-padre">

        <!-- Banner de Registro -->
        <section class="tienda-banner">
            <div class="tienda-banner-contenido">
                <h2>Crear Cuenta</h2>
                <p>Regístrate para obtener acceso a consultas médicas, agendar citas y revisar tu expediente.</p>
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

                    <form action="registro.php" method="POST" class="formulario-contacto">
                        <fieldset>
                            <legend>Formulario de Registro</legend>
                            <p class="form-descripcion">Completa los campos con tu información real de paciente para abrir tu expediente clínico.</p>
                            
                            <div class="grupo-input">
                                <label for="nombre">Nombre Completo:</label>
                                <input type="text" id="nombre" name="nombre" placeholder="Tu nombre y apellidos" required>
                            </div>

                            <div class="grupo-input">
                                <label for="email">Correo Electrónico:</label>
                                <input type="email" id="email" name="email" placeholder="ejemplo@correo.com" required>
                            </div>

                            <div class="grupo-input">
                                <label for="rol">Tipo de Usuario:</label>
                                <select id="rol" name="rol" style="padding: 10px 14px; border: 1.5px solid #e2e8f0; border-radius: 8px; font-family: inherit; font-size: 14px;" required>
                                    <option value="paciente">Paciente</option>
                                    <option value="admin">Administrador</option>
                                </select>
                            </div>
                            
                            <div class="grupo-input">
                                <label for="password">Contraseña (Mínimo 6 caracteres):</label>
                                <input type="password" id="password" name="password" placeholder="Ingresa una contraseña segura" required>
                            </div>

                            <div class="grupo-input">
                                <label for="confirm_password">Confirmar Contraseña:</label>
                                <input type="password" id="confirm_password" name="confirm_password" placeholder="Repite tu contraseña" required>
                            </div>
                            
                            <button type="submit" class="boton-enviar">Crear mi Cuenta</button>
                        </fieldset>
                    </form>

                    <div style="text-align: center; margin-top: 20px;" class="auth-enlace-cambio">
                        <p>¿Ya tienes una cuenta? <a href="login.php" style="color: var(--principal); font-weight: 600; text-decoration: none;">Inicia sesión aquí</a></p>
                    </div>
                </div>

            </main>
        </div>

<?php
// Cargar el pie de página común
include 'footer.php';
?>
