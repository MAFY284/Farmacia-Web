<?php
// Cargar manejador del carrito (este archivo inicia la sesión)
require_once 'carrito_handler.php';
require_once 'conexion.php';

// Si no está logueado, redirigir a iniciar sesión
if (!isset($_SESSION['usuario_id'])) {
    header('Location: login.php');
    exit;
}

$error = null;
$exito = null;

function generar_horarios_30min() {
    $horarios = [];
    $bloques = [
        ['09:00', '13:00'],
        ['15:00', '20:00']
    ];

    foreach ($bloques as $bloque) {
        $inicio = strtotime($bloque[0]);
        $fin = strtotime($bloque[1]);
        
        while ($inicio < $fin) {
            $h_val = date('H:i:s', $inicio);
            $h_txt = date('g:i A', $inicio);
            $horarios[$h_val] = $h_txt;
            $inicio = strtotime('+30 minutes', $inicio);
        }
    }
    return $horarios;
}

$horarios_estandar = generar_horarios_30min();

// Obtener fecha seleccionada (por defecto hoy)
$fecha_seleccionada = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');

// Impedir agendar citas en fechas pasadas
$hoy = date('Y-m-d');
$es_fecha_pasada = ($fecha_seleccionada < $hoy);

// Consultar citas ya agendadas para la fecha seleccionada
$citas_ocupadas = [];
try {
    $stmt = $pdo->prepare("SELECT hora FROM citas WHERE fecha = :fecha");
    $stmt->execute(['fecha' => $fecha_seleccionada]);
    $citas_ocupadas = $stmt->fetchAll(PDO::FETCH_COLUMN);
} catch (PDOException $e) {
    $error = "Error al consultar la disponibilidad: " . $e->getMessage();
}

// Procesar el agendamiento de una cita
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agendar_cita'])) {
    $usuario_id = $_SESSION['usuario_id'];
    $nombre_paciente = htmlspecialchars(trim($_POST['nombre_paciente']));
    $servicio = htmlspecialchars(trim($_POST['servicio']));
    $fecha = $_POST['fecha'];
    $hora = $_POST['hora'];

    if (!empty($nombre_paciente) && !empty($servicio) && !empty($fecha) && !empty($hora)) {
        if ($fecha < $hoy) {
            $error = "No puedes agendar una cita en una fecha pasada.";
        } else {
            try {
                $check_stmt = $pdo->prepare("SELECT id FROM citas WHERE fecha = :fecha AND hora = :hora");
                $check_stmt->execute(['fecha' => $fecha, 'hora' => $hora]);
                
                if ($check_stmt->fetch()) {
                    $error = "Lo sentimos, este horario acaba de ser ocupado. Por favor selecciona otro.";
                } else {
                    $insert_stmt = $pdo->prepare("INSERT INTO citas (usuario_id, nombre_paciente, servicio, fecha, hora) VALUES (:usuario_id, :nombre_paciente, :servicio, :fecha, :hora)");
                    $insert_stmt->execute([
                        'usuario_id' => $usuario_id,
                        'nombre_paciente' => $nombre_paciente,
                        'servicio' => $servicio,
                        'fecha' => $fecha,
                        'hora' => $hora
                    ]);

                    $exito = "¡Cita agendada con éxito para el " . date('d/m/Y', strtotime($fecha)) . " a las " . date('g:i A', strtotime($hora)) . "!";
                    $citas_ocupadas[] = $hora;
                }
            } catch (PDOException $e) {
                $error = "Error al guardar la cita: " . $e->getMessage();
            }
        }
    } else {
        $error = "Por favor, selecciona un horario y completa los campos.";
    }
}

include 'header.php';
?>

<div id="contenedor" class="el-contenedor-padre">

    <section class="tienda-banner">
        <div class="tienda-banner-contenido">
            <h2>Agenda tu Cita con el Dr. X</h2>
            <p>Selecciona el horario que mejor te convenga para tu consulta médica profesional.</p>
        </div>
    </section>

    <div id="contenido" class="lo-de-enmedio">
        <main class="lo-principal">

            <div class="citas-container-flex">
                
                <!-- Lado Izquierdo: Información y Fecha -->
                <div class="citas-sidebar">
                    <div class="doctor-card">
                        <div class="doctor-info">
                            <h3>Dr. X</h3>
                            <p class="especialidad">Médico General</p>
                            <p class="doctor-desc">Especialista en atención primaria y medicina preventiva.</p>
                        </div>
                    </div>

                    <div class="calendario-mini">
                        <h4>Selecciona la Fecha</h4>
                        <form action="citas.php" method="GET">
                            <input type="date" name="fecha" value="<?php echo htmlspecialchars($fecha_seleccionada); ?>" min="<?php echo $hoy; ?>" onchange="this.form.submit()" class="input-fecha-estetica">
                        </form>
                    </div>

                    <div class="info-citas">
                        <p><i class="icono">⏱</i> Duración: 30 minutos</p>
                        <p><i class="icono">📍</i> Sucursal: Central</p>
                    </div>
                </div>

                <!-- Lado Derecho: Grid de Horarios y Formulario -->
                <div class="citas-main-panel">
                    <?php if ($exito): ?>
                        <div class="alerta-exito"><?php echo $exito; ?></div>
                    <?php endif; ?>

                    <?php if ($error): ?>
                        <div class="alerta-error"><?php echo $error; ?></div>
                    <?php endif; ?>

                    <form action="citas.php?fecha=<?php echo urlencode($fecha_seleccionada); ?>" method="POST" id="form-agendar">
                        <input type="hidden" name="fecha" value="<?php echo htmlspecialchars($fecha_seleccionada); ?>">
                        
                        <div class="grid-seccion">
                            <h4>Horarios Disponibles para el <?php echo date('d/m/Y', strtotime($fecha_seleccionada)); ?></h4>
                            <div class="horarios-grid-30min">
                                <?php foreach ($horarios_estandar as $hora_val => $hora_txt): 
                                    $esta_ocupado = in_array($hora_val, $citas_ocupadas);
                                ?>
                                    <div class="hora-item">
                                        <input type="radio" name="hora" id="h-<?php echo $hora_val; ?>" value="<?php echo $hora_val; ?>" <?php echo $esta_ocupado ? 'disabled' : ''; ?> required>
                                        <label for="h-<?php echo $hora_val; ?>" class="<?php echo $esta_ocupado ? 'ocupado' : 'disponible'; ?>">
                                            <?php echo $hora_txt; ?>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>

                        <div class="datos-paciente-seccion">
                            <h4>Confirmar Datos</h4>
                            <div class="form-row">
                                <div class="grupo-input">
                                    <label>Paciente:</label>
                                    <input type="text" name="nombre_paciente" value="<?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>" required>
                                </div>
                                <div class="grupo-input">
                                    <label>Servicio:</label>
                                    <select name="servicio" required>
                                        <option value="Consulta General">Consulta General</option>
                                        <option value="Revisión de Resultados">Revisión de Resultados</option>
                                        <option value="Certificado Médico">Certificado Médico</option>
                                    </select>
                                </div>
                            </div>
                            <button type="submit" name="agendar_cita" class="btn-cta btn-full">Reservar Cita Ahora</button>
                        </div>
                    </form>
                </div>
            </div>

        </main>
    </div>

<?php include 'footer.php'; ?>