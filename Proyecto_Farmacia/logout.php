<?php
// Iniciar sesión para poder destruirla
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Vaciar todas las variables de sesión (manteniendo el carrito si se desea, o destruyendo toda la sesión)
unset($_SESSION['usuario_id']);
unset($_SESSION['usuario_nombre']);
unset($_SESSION['usuario_email']);

// Redirigir a inicio
header('Location: index.php');
exit;
?>
