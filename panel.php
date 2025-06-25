<?php
require_once 'includes/auth.php';
require_once 'includes/database.php';

redirectIfNotLoggedIn();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel - Sistema de Asistencias</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <div class="logo">Sistema de Asistencias</div>
                <ul class="nav-links">
                    <li><a href="panel.php">Inicio</a></li>
                    <?php if (getUserRole() === 'admin'): ?>
                        <li><a href="usuarios.php">Usuarios</a></li>
                    <?php endif; ?>
                    <?php if (getUserRole() === 'jefe'): ?>
                        <li><a href="asistencias.php">Asistencias</a></li>
                    <?php endif; ?>
                    <?php if (getUserRole() === 'cliente'): ?>
                        <li><a href="checkin.php">Registrar Asistencia</a></li>
                    <?php endif; ?>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card mt-3">
            <h1>Bienvenido, <?php echo $_SESSION['user_nombre']; ?></h1>
            <p>Rol: <?php echo ucfirst($_SESSION['user_rol']); ?></p>
            
            <?php if (getUserRole() === 'cliente'): ?>
                <div class="mt-3">
                    <a href="checkin.php" class="btn">Registrar Asistencia</a>
                </div>
            <?php elseif (getUserRole() === 'jefe'): ?>
                <div class="mt-3">
                    <a href="asistencias.php" class="btn">Ver Asistencias</a>
                </div>
            <?php elseif (getUserRole() === 'admin'): ?>
                <div class="mt-3">
                    <a href="usuarios.php" class="btn">Administrar Usuarios</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>