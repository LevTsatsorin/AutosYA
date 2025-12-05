<?php
// Verificar autenticación
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../pages/login.php");
    exit();
}

include_once("../components/header.php");

// Obtener ID del usuario a modificar
if (!isset($_GET['id'])) {
    header("Location: ../pages/inicio.php");
    exit();
}

$id_usuario = (int)$_GET['id'];
$redirect_param = isset($_GET['redirect']) ? $_GET['redirect'] : '';

if ($redirect_param === 'admin_gestion') {
    $cancel_url = '/AutosYA/admin/gestion_usuarios.php';
} else {
    $cancel_url = $_SESSION['fk_rol'] == 1 ? '/AutosYA/admin/index.php' : '/AutosYA/cliente/perfil.php';
}

// Obtener datos del usuario
$stmt = $con->prepare("SELECT id_usuario, nombre, correo, fk_rol FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: ../pages/inicio.php");
    exit();
}

$usuario = $result->fetch_assoc();
$stmt->close();
?>
<link rel="stylesheet" href="/AutosYA/css/admin.css">

<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <h1 class="admin-page-title mb-4">
                <i class="bi bi-person-gear"></i> Modificar Usuario
            </h1>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow-lg mt-4">
                <div class="card-header bg-success text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-pencil"></i> Datos del Usuario
                    </h4>
                </div>
                <div class="card-body">
                    <form action="/AutosYA/usuario/mod_usuario_ok.php" method="POST" class="needs-validation" novalidate>
                        <input type="hidden" name="id_usuario" value="<?php echo $usuario['id_usuario']; ?>">
                        <input type="hidden" name="redirect" value="<?php echo $redirect_param; ?>">

                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre *</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                value="<?php echo htmlspecialchars($usuario['nombre']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo *</label>
                            <input type="email" class="form-control" id="correo" name="correo"
                                value="<?php echo htmlspecialchars($usuario['correo']); ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="fk_rol" class="form-label">Rol *</label>
                            <select class="form-select" id="fk_rol" name="fk_rol" required>
                                <option value="1" <?php echo ($usuario['fk_rol'] == 1) ? 'selected' : ''; ?>>Admin</option>
                                <option value="2" <?php echo ($usuario['fk_rol'] == 2) ? 'selected' : ''; ?>>Cliente</option>
                            </select>
                        </div>

                        <?php if ($id_usuario === $_SESSION['id_usuario']): ?>
                            <hr class="my-4">

                            <h5 class="mb-3">Cambiar Contraseña (opcional)</h5>
                            <p class="text-muted"><small>Deja estos campos vacíos si no deseas cambiar la contraseña</small></p>

                            <div class="mb-3">
                                <label for="clave" class="form-label">Nueva Contraseña</label>
                                <input type="password" class="form-control" id="clave" name="nueva_clave" minlength="6">
                                <small class="form-text text-muted">Mínimo 6 caracteres</small>
                            </div>

                            <div class="mb-3">
                                <label for="clave_rep" class="form-label">Confirmar Nueva Contraseña</label>
                                <input type="password" class="form-control" id="clave_rep" name="confirmar_clave" minlength="6">
                                <div class="invalid-feedback">Repite tu contraseña.</div>
                            </div>
                        <?php endif; ?>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="<?php echo $cancel_url; ?>" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="bi bi-save"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="/AutosYA/js/registro-validation.js"></script>

<?php include_once("../components/footer.php"); ?>