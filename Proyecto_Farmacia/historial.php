<?php
// Cargar manejador del carrito (este archivo inicia la sesión)
require_once 'carrito_handler.php';
require_once 'conexion.php';

// Si no está logueado, redirigir a iniciar sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$usuario_id = $_SESSION['usuario_id'];
$historial = [];
$error = null;

try {
    // Consultar el historial médico del usuario en orden cronológico descendente (lo más nuevo primero)
    $stmt = $pdo->prepare("SELECT * FROM historial_medico WHERE usuario_id = :usuario_id ORDER BY fecha_consulta DESC");
    $stmt->execute(['usuario_id' => $usuario_id]);
    $historial = $stmt->fetchAll();
} catch (PDOException $e) {
    $error = "Error al consultar tu historial clínico: " . $e->getMessage();
}

// Cargar la cabecera compartida
include 'header.php';
?>

    <div id="contenedor" class="el-contenedor-padre">

        <!-- Banner de Historial -->
        <section class="tienda-banner">
            <div class="tienda-banner-contenido">
                <h2>Historial Clínico Digital</h2>
                <p>Expediente médico personal de consultas, diagnósticos y recetas de medicamentos.</p>
            </div>
        </section>

        <div id="contenido" class="lo-de-enmedio">
            <main class="lo-principal">

                <div class="historial-contenedor-principal">
                    <?php if ($error): ?>
                        <div class="alerta-error">
                            <p>⚠️ <?php echo $error; ?></p>
                        </div>
                    <?php endif; ?>

                    <div class="historial-encabezado-info">
                        <h3>Expediente de: <strong><?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?></strong></h3>
                        <p>ID de Paciente: #<?php echo str_pad($usuario_id, 5, '0', STR_PAD_LEFT); ?> | Correo registrado: <?php echo htmlspecialchars($_SESSION['usuario_email']); ?></p>
                    </div>

                    <?php if (empty($historial)): ?>
                        <div class="confirmacion-tarjeta" style="text-align: center; margin-top: 25px;">
                            <div class="carrito-vacio">
                                <span style="font-size: 50px;">🩺</span>
                                <h3 style="margin: 15px 0 10px 0; color: var(--negro);">Expediente Vacío</h3>
                                <p>Aún no tienes consultas registradas en nuestro sistema médico de Farmacia Salud y Vida.</p>
                                <p style="font-size: 13px; color: #718096; margin-top: 5px;">Tu historial clínico se actualizará automáticamente una vez que asistas a tu primera cita presencial.</p>
                                <a href="citas.php" class="btn-cta" style="margin-top: 20px; display: inline-block;">Agendar tu Primera Consulta</a>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="historial-lista">
                            <?php foreach ($historial as $consulta): ?>
                                <div class="consulta-tarjeta">
                                    <div class="consulta-header">
                                        <div class="consulta-meta">
                                            <span class="consulta-fecha">📅 <?php echo date('d/m/Y', strtotime($consulta['fecha_consulta'])); ?></span>
                                            <span class="consulta-medico">👨‍⚕️ Atendido por: <strong><?php echo htmlspecialchars($consulta['medico']); ?></strong></span>
                                        </div>
                                    </div>
                                    <div class="consulta-cuerpo">
                                        <div class="consulta-seccion">
                                            <h4>🩺 Diagnóstico Médico:</h4>
                                            <p class="diagnostico-texto"><?php echo htmlspecialchars($consulta['diagnostico']); ?></p>
                                        </div>
                                        
                                        <div class="consulta-seccion receta-seccion">
                                            <h4>💊 Receta Prescrita:</h4>
                                            <div class="receta-bloque-vista">
                                                <?php echo nl2br(htmlspecialchars($consulta['receta'])); ?>
                                            </div>
                                        </div>
                                        
                                        <?php if (!empty($consulta['notas'])): ?>
                                            <div class="consulta-seccion notas-seccion">
                                                <h4>📝 Indicaciones y Notas Adicionales:</h4>
                                                <p class="notas-texto"><?php echo htmlspecialchars($consulta['notas']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>

            </main>
        </div>

<?php
// Cargar el pie de página común
include 'footer.php';
?>
