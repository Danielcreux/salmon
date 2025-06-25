<?php
require_once 'includes/auth.php';
require_once 'includes/database.php';
require_once 'includes/functions.php'; 


checkRole(['jefe', 'admin']);

// Obtener parámetros de filtrado
$fecha_inicio = $_GET['fecha_inicio'] ?? date('Y-m-01');
$fecha_fin = $_GET['fecha_fin'] ?? date('Y-m-d');
$usuario_id = $_GET['usuario_id'] ?? null;

// Construir consulta
$query = "SELECT a.*, u.nombre as usuario_nombre 
          FROM asistencias a 
          JOIN usuarios u ON a.usuario_id = u.id 
          WHERE a.fecha BETWEEN ? AND ?";
$params = [$fecha_inicio, $fecha_fin];

if ($usuario_id) {
    $query .= " AND a.usuario_id = ?";
    $params[] = $usuario_id;
}

$query .= " ORDER BY a.fecha DESC, u.nombre";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$asistencias = $stmt->fetchAll();

// Obtener lista de usuarios para el filtro
$stmt = $pdo->query("SELECT id, nombre FROM usuarios ORDER BY nombre");
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Asistencias - Sistema de Asistencias</title>
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
                    <li><a href="asistencias.php">Asistencias</a></li>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Registros de Asistencia</h1>
            </div>
            
            <form method="GET" class="mb-3">
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr auto; gap: 1rem; align-items: end;">
                    <div class="form-group">
                        <label for="fecha_inicio">Fecha Inicio</label>
                        <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control" value="<?php echo $fecha_inicio; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="fecha_fin">Fecha Fin</label>
                        <input type="date" id="fecha_fin" name="fecha_fin" class="form-control" value="<?php echo $fecha_fin; ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="usuario_id">Usuario</label>
                        <select id="usuario_id" name="usuario_id" class="form-control">
                            <option value="">Todos</option>
                            <?php foreach ($usuarios as $usuario): ?>
                                <option value="<?php echo $usuario['id']; ?>" <?php echo $usuario_id == $usuario['id'] ? 'selected' : ''; ?>>
                                    <?php echo $usuario['nombre']; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">Filtrar</button>
                </div>
            </form>
            
            <div style="overflow-x: auto;">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Usuario</th>
                            <th>Entrada</th>
                            <th>Salida</th>
                            <th>Horas Trabajadas</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asistencias as $asistencia): ?>
                            <tr>
                                <td><?php echo formatDate($asistencia['fecha']); ?></td>
                                <td><?php echo $asistencia['usuario_nombre']; ?></td>
                                <td><?php echo $asistencia['hora_entrada'] ? formatTime($asistencia['hora_entrada']) : '--:--:--'; ?></td>
                                <td><?php echo $asistencia['hora_salida'] ? formatTime($asistencia['hora_salida']) : '--:--:--'; ?></td>
                                <td>
                                    <?php 
                                    if ($asistencia['hora_entrada'] && $asistencia['hora_salida']) {
                                        $entrada = new DateTime($asistencia['hora_entrada']);
                                        $salida = new DateTime($asistencia['hora_salida']);
                                        $diferencia = $entrada->diff($salida);
                                        echo $diferencia->format('%H:%I:%S');
                                    } else {
                                        echo '--:--:--';
                                    }
                                    ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>