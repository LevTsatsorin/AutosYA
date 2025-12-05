<?php
include_once("../components/header.php");

// Si ya está autenticado, redirigir según rol
if (isset($_SESSION['id_usuario'])) {
    if ($_SESSION['fk_rol'] == 1) {
        header("Location: ../admin/index.php");
    } else {
        header("Location: ../cliente/perfil.php");
    }
    exit();
}
?>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow-lg" style="border: none;">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">
                        <i class="bi bi-person-plus text-primary"></i> Crear Cuenta
                    </h2>

                    <?php
                    // Mostrar mensajes de error
                    if (isset($_GET['error'])) {
                        $error_msg = '';
                        switch ($_GET['error']) {
                            case 'campos_vacios':
                                $error_msg = 'Por favor completa todos los campos.';
                                break;
                            case 'correo_invalido':
                                $error_msg = 'El formato del correo no es válido.';
                                break;
                            case 'clave_corta':
                                $error_msg = 'La contraseña debe tener al menos 8 caracteres.';
                                break;
                            case 'claves_no_coinciden':
                                $error_msg = 'Las contraseñas no coinciden.';
                                break;
                            case 'correo_existente':
                                $error_msg = 'Este correo ya está registrado.';
                                break;
                            case 'registro_fallido':
                                $error_msg = 'Error al crear la cuenta. Intenta nuevamente.';
                                break;
                            default:
                                $error_msg = 'Error en el registro.';
                        }
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> ' . htmlspecialchars($error_msg) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                    }
                    ?>

                    <form action="../log/reg.php" method="POST" class="needs-validation" novalidate>
                        <div class="mb-3">
                            <label for="nombre" class="form-label">
                                <i class="bi bi-person"></i> Nombre Completo
                            </label>
                            <input type="text" class="form-control" id="nombre" name="nombre"
                                placeholder="Tu nombre" required>
                            <div class="invalid-feedback">
                                Ingresa tu nombre.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="correo" class="form-label">
                                <i class="bi bi-envelope"></i> Correo Electrónico
                            </label>
                            <input type="email" class="form-control" id="correo" name="correo"
                                placeholder="tu@correo.com" required>
                            <div class="invalid-feedback">
                                Ingresa un correo válido.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="clave" class="form-label">
                                <i class="bi bi-lock"></i> Contraseña
                            </label>
                            <input type="password" class="form-control" id="clave" name="clave"
                                placeholder="Mínimo 8 caracteres" minlength="8" required>
                            <div class="invalid-feedback">
                                La contraseña debe tener al menos 8 caracteres.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="clave_rep" class="form-label">
                                <i class="bi bi-lock-fill"></i> Repetir Contraseña
                            </label>
                            <input type="password" class="form-control" id="clave_rep" name="clave_rep"
                                placeholder="Repite tu contraseña" minlength="8" required>
                            <div class="invalid-feedback">
                                Repite tu contraseña.
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-success btn-lg">
                                <i class="bi bi-person-plus"></i> Crear Cuenta
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">Ya tienes cuenta?
                            <a href="login.php" class="text-decoration-none">
                                Inicia sesión aquí
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/registro-validation.js"></script>

    <?php include_once("../components/footer.php"); ?>