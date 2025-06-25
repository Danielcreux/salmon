<?php
require_once 'includes/auth.php';
require_once 'includes/database.php';
require_once 'includes/functions.php';

checkRole(['cliente']);

$usuario_id = $_SESSION['user_id'];
$fecha_actual = getCurrentDate();
$mensaje = '';

// Verificar si ya hay un registro de entrada hoy
$stmt = $pdo->prepare("SELECT * FROM asistencias WHERE usuario_id = ? AND fecha = ?");
$stmt->execute([$usuario_id, $fecha_actual]);
$asistencia = $stmt->fetch();

// Inicializar hora_salida si no existe
$hora_salida = $asistencia['hora_salida'] ?? null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['entrada']) && !$asistencia) {
        // Registrar entrada
        $hora_entrada = getCurrentTime();
        
        $stmt = $pdo->prepare(
            "INSERT INTO asistencias (usuario_id, fecha, hora_entrada) 
            VALUES (?, ?, ?)"
        );
        
        if ($stmt->execute([$usuario_id, $fecha_actual, $hora_entrada])) {
            $mensaje = "Entrada registrada correctamente a las $hora_entrada";
            $asistencia = ['hora_entrada' => $hora_entrada, 'hora_salida' => null];
            $hora_salida = null;
        } else {
            $mensaje = "Error al registrar la entrada";
        }
    } elseif (isset($_POST['salida']) && $asistencia && !$hora_salida) {
        // Registrar salida
        $hora_salida = getCurrentTime();
        
        $stmt = $pdo->prepare(
            "UPDATE asistencias 
            SET hora_salida = ? 
            WHERE usuario_id = ? AND fecha = ?"
        );
        
        if ($stmt->execute([$hora_salida, $usuario_id, $fecha_actual])) {
            $mensaje = "Salida registrada correctamente a las $hora_salida";
            $asistencia['hora_salida'] = $hora_salida;
        } else {
            $mensaje = "Error al registrar la salida";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar Asistencia - Sistema de Asistencias</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <div class="logo">Sistema de Asistencias</div>
                <ul class="nav-links">
                    <li><a href="panel.php">Inicio</a></li>
                    <li><a href="checkin.php">Registrar Asistencia</a></li>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="attendance-card">
            <h1>Registro de Asistencia</h1>
            <p class="attendance-date"><?php echo formatDate($fecha_actual); ?></p>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-success"><?php echo $mensaje; ?></div>
            <?php endif; ?>
            
            <?php if (!$asistencia): ?>
                <p class="mb-3">Aún no has registrado tu entrada hoy</p>
                <form action="checkin.php" method="POST">
                    <button type="submit" name="entrada" class="btn btn-success">Registrar Entrada</button>
                </form>
            <?php elseif (!$hora_salida): ?>
                <p class="attendance-time">Entrada: <?php echo formatTime($asistencia['hora_entrada']); ?></p>
                <p class="mb-3">Ya registraste tu entrada pero aún no la salida</p>
                <form action="checkin.php" method="POST">
                    <button type="submit" name="salida" class="btn btn-danger">Registrar Salida</button>
                </form>
            <?php else: ?>
                <p class="attendance-time">Entrada: <?php echo formatTime($asistencia['hora_entrada']); ?></p>
                <p class="attendance-time">Salida: <?php echo formatTime($hora_salida); ?></p>
                <p class="mb-3">Hoy ya completaste tu registro de asistencia</p>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>