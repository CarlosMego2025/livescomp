<?php
require_once '../includes/auth.php';
require_once '../includes/funciones.php';
requireAdmin();

$db = new Database();
$conn = $db->getConnection();

// Aprobar o rechazar usuario
if (isset($_GET['action']) && isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $action = $_GET['action'];
    
    if ($action === 'approve') {
        $stmt = $conn->prepare("UPDATE usuarios SET estado = 'aprobado' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = 'Usuario aprobado exitosamente';
    } elseif ($action === 'reject') {
        $stmt = $conn->prepare("UPDATE usuarios SET estado = 'rechazado' WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = 'Usuario rechazado';
    } elseif ($action === 'delete') {
        $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
        $stmt->execute([$id]);
        $_SESSION['flash_success'] = 'Usuario eliminado';
    }
    
    redirect('usuarios.php');
}

// Obtener todos los usuarios
$usuarios = $conn->query("SELECT * FROM usuarios")->fetchAll(PDO::FETCH_ASSOC);

include '../includes/header-admin.php';
?>

<div class="container mt-4">
    <h2>Gestión de Usuarios</h2>
    
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>Email</th>
                            <th>Rol</th>
                            <th>Estado</th>
                            <th>Fecha Registro</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($usuarios as $usuario): ?>
                            <tr>
                                <td><?= $usuario['id'] ?></td>
                                <td><?= $usuario['nombre'] ?></td>
                                <td><?= $usuario['email'] ?></td>
                                <td><?= $usuario['rol'] ?></td>
                                <td>
                                    <span class="badge badge-<?= 
                                        $usuario['estado'] === 'aprobado' ? 'success' : 
                                        ($usuario['estado'] === 'pendiente' ? 'warning' : 'danger')
                                    ?>">
                                        <?= ucfirst($usuario['estado']) ?>
                                    </span>
                                </td>
                                <td><?= date('d/m/Y', strtotime($usuario['fecha_registro'])) ?></td>
                                <td>
                                    <?php if ($usuario['estado'] === 'pendiente'): ?>
                                        <a href="usuarios.php?action=approve&id=<?= $usuario['id'] ?>" class="btn btn-sm btn-success">Aprobar</a>
                                        <a href="usuarios.php?action=reject&id=<?= $usuario['id'] ?>" class="btn btn-sm btn-danger">Rechazar</a>
                                    <?php endif; ?>
                                    <a href="usuarios.php?action=delete&id=<?= $usuario['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('¿Estás seguro?')">Eliminar</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include '../includes/footer-admin.php'; ?>