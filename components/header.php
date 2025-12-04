<?php
include_once(__DIR__ . "/config/conf.php");

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Verificar si el usuario está autenticado
$esta_autenticado = isset($_SESSION['id_usuario']);
$nombre_usuario = $esta_autenticado ? $_SESSION['nombre'] : '';
$rol_usuario = $esta_autenticado ? $_SESSION['fk_rol'] : 0;

// Determinar la página
$current_page = basename($_SERVER['PHP_SELF']);
$current_dir = basename(dirname($_SERVER['PHP_SELF']));
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AutosYA - Alquiler de Autos</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="/AutosYA/css/estilos.css">
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container-fluid px-4">
            <a class="navbar-brand" href="/AutosYA/pages/inicio.php">
                <i class="bi bi-car-front-fill"></i> Autos<span style="color: var(--rosy-taupe);">YA</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link <?php echo ($current_page === 'inicio.php') ? 'active' : ''; ?>" href="/AutosYA/pages/inicio.php">
                            <i class="bi bi-house-fill"></i> Inicio
                        </a>
                    </li>

                    <?php if ($esta_autenticado && $rol_usuario == 1): ?>
                        <!-- Links para Administrador -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_dir === 'admin' && $current_page === 'index.php') ? 'active' : ''; ?>" href="/AutosYA/admin/index.php">
                                <i class="bi bi-speedometer2"></i> Panel Admin
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page === 'gestion_autos.php') ? 'active' : ''; ?>" href="/AutosYA/admin/gestion_autos.php">
                                <i class="bi bi-car-front-fill"></i> Gestión de Autos
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page === 'gestion_usuarios.php') ? 'active' : ''; ?>" href="/AutosYA/admin/gestion_usuarios.php">
                                <i class="bi bi-people-fill"></i> Gestión de Usuarios
                            </a>
                        </li>
                    <?php elseif ($esta_autenticado && $rol_usuario == 2): ?>
                        <!-- Links para Cliente -->
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page === 'perfil.php') ? 'active' : ''; ?>" href="/AutosYA/cliente/perfil.php">
                                <i class="bi bi-person-circle"></i> Mi Perfil
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link <?php echo ($current_page === 'mis_reservas.php') ? 'active' : ''; ?>" href="/AutosYA/pages/mis_reservas.php">
                                <i class="bi bi-calendar-check-fill"></i> Mis Reservas
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>

                <ul class="navbar-nav">
                    <?php if ($esta_autenticado): ?>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($nombre_usuario); ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="<?php echo $rol_usuario == 1 ? '/AutosYA/admin/index.php' : '/AutosYA/cliente/perfil.php'; ?>">
                                        <i class="bi bi-speedometer2"></i> Dashboard
                                    </a></li>
                                <li><a class="dropdown-item" href="/AutosYA/usuario/mod_usuario.php?id=<?php echo $_SESSION['id_usuario']; ?>">
                                        <i class="bi bi-person-gear"></i> Editar Perfil
                                    </a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li><a class="dropdown-item text-danger" href="/AutosYA/log/cerrar.php">
                                        <i class="bi bi-box-arrow-right"></i> Cerrar Sesión
                                    </a></li>
                            </ul>
                        </li>
                    <?php else: ?>
                        <li class="nav-item">
                            <a class="nav-link" href="/AutosYA/pages/login.php">
                                <i class="bi bi-box-arrow-in-right"></i> Iniciar Sesión
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link nav-link-register" href="/AutosYA/pages/registro.php">
                                <i class="bi bi-person-plus"></i> Registrarse
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>

    <main>