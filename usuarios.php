<?php
require_once 'includes/auth.php';
require_once 'includes/database.php';
require_once 'includes/functions.php'; 

checkRole(['admin']);

$mensaje = '';

// Procesar eliminación de usuario
if (isset($_GET['eliminar'])) {
    $id = $_GET['eliminar'];
    
    // No permitir eliminar al admin principal
    if ($id != 1) {
        $stmt = $pdo->prepare("DELETE FROM usuarios WHERE id = ?");
        if ($stmt->execute([$id])) {
            $mensaje = "Usuario eliminado correctamente";
        } else {
            $mensaje = "Error al eliminar el usuario";
        }
    } else {
        $mensaje = "No puedes eliminar al administrador principal";
    }
}

// Procesar cambio de rol
if (isset($_POST['cambiar_rol'])) {
    $id = $_POST['usuario_id'];
    $rol = $_POST['nuevo_rol'];
    
    // No permitir cambiar el rol del admin principal
    if ($id != 1) {
        $stmt = $pdo->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
        if ($stmt->execute([$rol, $id])) {
            $mensaje = "Rol actualizado correctamente";
        } else {
            $mensaje = "Error al actualizar el rol";
        }
    } else {
        $mensaje = "No puedes cambiar el rol del administrador principal";
    }
}

// Obtener lista de usuarios
$stmt = $pdo->query("SELECT * FROM usuarios ORDER BY rol, nombre");
$usuarios = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Usuarios - Sistema de Asistencias</title>
    <link rel="stylesheet" href="css/style.css">
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <div class="logo">Sistema de Asistencias</div>
                <ul class="nav-links">
                    <li><a href="panel.php">Inicio</a></li>
                    <li><a href="usuarios.php">Usuarios</a></li>
                    <li><a href="asistencias.php">Asistencias</a></li>
                    <li><a href="logout.php">Cerrar Sesión</a></li>
                </ul>
            </nav>
        </div>
    </header>

    <div class="container">
        <div class="card">
            <div class="card-header">
                <h1>Administración de Usuarios</h1>
            </div>
            
            <?php if ($mensaje): ?>
                <div class="alert alert-success"><?php echo $mensaje; ?></div>
            <?php endif; ?>
            
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Usuario</th>
                        <th>Rol</th>
                        <th>Fecha Registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usuario): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td><?php echo $usuario['nombre']; ?></td>
                            <td><?php echo $usuario['username']; ?></td>
                            <td>
                                <?php if ($usuario['id'] == 1): ?>
                                    <?php echo ucfirst($usuario['rol']); ?>
                                <?php else: ?>
                                    <form method="POST" style="display: flex; gap: 0.5rem;">
                                        <input type="hidden" name="usuario_id" value="<?php echo $usuario['id']; ?>">
                                        <select name="nuevo_rol" class="form-control" style="width: auto;">
                                            <option value="cliente" <?php echo $usuario['rol'] == 'cliente' ? 'selected' : ''; ?>>Cliente</option>
                                            <option value="jefe" <?php echo $usuario['rol'] == 'jefe' ? 'selected' : ''; ?>>Jefe</option>
                                            <option value="admin" <?php echo $usuario['rol'] == 'admin' ? 'selected' : ''; ?>>Admin</option>
                                        </select>
                                        <button type="submit" name="cambiar_rol" class="btn">Cambiar</button>
                                    </form>
                                <?php endif; ?>
                            </td>
                            <td><?php echo formatDate($usuario['created_at']); ?></td>
                            <td>
                                <?php if ($usuario['id'] != 1): ?>
                                    <a href="usuarios.php?eliminar=<?php echo $usuario['id']; ?>" class="btn btn-danger" onclick="return confirm('¿Estás seguro de eliminar este usuario?')">Eliminar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>