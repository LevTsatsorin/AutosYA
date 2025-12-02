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
        <div class="col-md-6 col-lg-5">
            <div class="card shadow-lg" style="border: none;">
                <div class="card-body p-5">
                    <h2 class="card-title text-center mb-4">
                        <i class="bi bi-box-arrow-in-right text-primary"></i> Iniciar Sesión
                    </h2>

                    <?php
                    // Mostrar mensajes de error o éxito
                    if (isset($_GET['registro']) && $_GET['registro'] === 'exitoso') {
                        echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> ¡Registro exitoso! Inicia sesión con tus credenciales.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                    }

                    if (isset($_GET['error'])) {
                        $error_msg = '';
                        switch ($_GET['error']) {
                            case 'campos_vacios':
                                $error_msg = 'Por favor completa todos los campos.';
                                break;
                            case 'credenciales_invalidas':
                                $error_msg = 'Correo o contraseña incorrectos.';
                                break;
                            case 'metodo_invalido':
                                $error_msg = 'Método de petición inválido.';
                                break;
                            default:
                                $error_msg = 'Error al iniciar sesión. Intenta nuevamente.';
                        }
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> ' . htmlspecialchars($error_msg) . '
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                          </div>';
                    }
                    ?>

                    <form action="../log/log.php" method="POST" class="needs-validation" novalidate>
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
                                placeholder="Tu contraseña" required>
                            <div class="invalid-feedback">
                                Ingresa tu contraseña.
                            </div>
                        </div>

                        <div class="d-grid gap-2 mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </button>
                        </div>
                    </form>

                    <hr class="my-4">

                    <div class="text-center">
                        <p class="mb-0">No tienes cuenta?
                            <a href="registro.php" class="text-decoration-none">
                                Regístrate aquí
                            </a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../js/form-validation.js"></script>

    <?php include_once("../components/footer.php"); ?>