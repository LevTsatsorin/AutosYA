<?php
// Verificar autenticación y rol
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SESSION['fk_rol'] != 1) {
    header("Location: ../pages/inicio.php");
    exit();
}

include_once("../components/header.php");
?>
<link rel="stylesheet" href="/AutosYA/css/admin.css">

<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <h1 class="admin-page-title">
                <i class="bi bi-speedometer2"></i> Panel de Administración
            </h1>

            <?php
            // Mostrar mensajes de éxito/error
            if (isset($_GET['alta']) && $_GET['alta'] === 'ok') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> Auto agregado exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            }
            if (isset($_GET['mod']) && $_GET['mod'] === 'ok') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> Auto modificado exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            }
            if (isset($_GET['baja']) && $_GET['baja'] === 'ok') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> Auto eliminado exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            }
            ?>
        </div>
    </div>

    <!-- Contenedor de estadísticas para móvil -->
    <div class="row">
        <div class="col-12">
            <div class="mobile-stats-container">
                <div class="mobile-stats-card">
                    <div class="mobile-stat-row">
                        <div class="mobile-stat-icon primary">
                            <i class="bi bi-car-front"></i>
                        </div>
                        <div class="mobile-stat-content">
                            <span class="mobile-stat-title">Total Autos</span>
                            <span class="mobile-stat-number">
                                <?php
                                $stmt = $con->prepare("SELECT COUNT(*) as total FROM autos");
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();
                                echo $row['total'];
                                $stmt->close();
                                ?>
                            </span>
                        </div>
                    </div>

                    <div class="mobile-stat-row">
                        <div class="mobile-stat-icon success">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <div class="mobile-stat-content">
                            <span class="mobile-stat-title">Disponibles</span>
                            <span class="mobile-stat-number">
                                <?php
                                $stmt = $con->prepare("SELECT COUNT(*) as total FROM autos WHERE estado='disponible'");
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();
                                echo $row['total'];
                                $stmt->close();
                                ?>
                            </span>
                        </div>
                    </div>

                    <div class="mobile-stat-row">
                        <div class="mobile-stat-icon warning">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <div class="mobile-stat-content">
                            <span class="mobile-stat-title">Reservas Activas</span>
                            <span class="mobile-stat-number">
                                <?php
                                $stmt = $con->prepare("SELECT COUNT(*) as total FROM reservas WHERE estado IN ('pendiente', 'confirmada')");
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();
                                echo $row['total'];
                                $stmt->close();
                                ?>
                            </span>
                        </div>
                    </div>

                    <div class="mobile-stat-row">
                        <div class="mobile-stat-icon info">
                            <i class="bi bi-people"></i>
                        </div>
                        <div class="mobile-stat-content">
                            <span class="mobile-stat-title">Total Usuarios</span>
                            <span class="mobile-stat-number">
                                <?php
                                $stmt = $con->prepare("SELECT COUNT(*) as total FROM usuarios WHERE fk_rol=2");
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();
                                echo $row['total'];
                                $stmt->close();
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Estadísticas rápidas para desktop -->
    <div class="row mb-4 desktop-stats-container">
        <div class="col-6 col-xl-3 mb-3">
            <div class="card text-white shadow-lg admin-stats-card stats-card-primary">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stats-icon-circle me-3">
                            <i class="bi bi-car-front"></i>
                        </div>
                        <h5 class="card-title mb-0">Total Autos</h5>
                    </div>
                    <h2 class="card-text">
                        <?php
                        $stmt = $con->prepare("SELECT COUNT(*) as total FROM autos");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        $stmt->close();
                        ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3 mb-3">
            <div class="card text-white shadow-lg admin-stats-card stats-card-success">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stats-icon-circle me-3">
                            <i class="bi bi-key-fill"></i>
                        </div>
                        <h5 class="card-title mb-0">Disponibles</h5>
                    </div>
                    <h2 class="card-text">
                        <?php
                        $stmt = $con->prepare("SELECT COUNT(*) as total FROM autos WHERE estado='disponible'");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        $stmt->close();
                        ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3 mb-3">
            <div class="card text-white shadow-lg admin-stats-card stats-card-warning">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stats-icon-circle me-3">
                            <i class="bi bi-calendar-check"></i>
                        </div>
                        <h5 class="card-title mb-0">Reservas Activas</h5>
                    </div>
                    <h2 class="card-text">
                        <?php
                        $stmt = $con->prepare("SELECT COUNT(*) as total FROM reservas WHERE estado IN ('pendiente', 'confirmada')");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        $stmt->close();
                        ?>
                    </h2>
                </div>
            </div>
        </div>

        <div class="col-6 col-xl-3 mb-3">
            <div class="card text-white shadow-lg admin-stats-card stats-card-info">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stats-icon-circle me-3">
                            <i class="bi bi-people"></i>
                        </div>
                        <h5 class="card-title mb-0">Total Usuarios</h5>
                    </div>
                    <h2 class="card-text">
                        <?php
                        $stmt = $con->prepare("SELECT COUNT(*) as total FROM usuarios WHERE fk_rol=2");
                        $stmt->execute();
                        $result = $stmt->get_result();
                        $row = $result->fetch_assoc();
                        echo $row['total'];
                        $stmt->close();
                        ?>
                    </h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg admin-table-card">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-car-front-fill"></i> Gestión de Autos
                    </h4>
                    <div class="card-header-actions">
                        <button class="btn btn-primary admin-action-btn" data-bs-toggle="modal" data-bs-target="#modalAgregarAuto">
                            <i class="bi bi-plus-circle"></i> <span>Agregar Auto</span>
                        </button>
                        <a href="gestion_autos.php" class="btn btn-light admin-action-btn">
                            <i class="bi bi-list-ul"></i> <span>Ver Listado</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-lg admin-table-card admin-table-card-reservas">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-check-fill"></i> Gestión de Reservas
                    </h4>
                    <div class="card-header-actions">
                        <a href="gestion_reservas.php" class="btn btn-light admin-action-btn">
                            <i class="bi bi-list-ul"></i> <span>Ver Listado</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mt-4">
        <div class="col-12">
            <div class="card shadow-lg admin-table-card admin-table-card-usuarios">
                <div class="card-header">
                    <h4 class="mb-0">
                        <i class="bi bi-people-fill"></i> Gestión de Usuarios
                    </h4>
                    <div class="card-header-actions">
                        <a href="gestion_usuarios.php" class="btn btn-light admin-action-btn">
                            <i class="bi bi-list-ul"></i> <span>Ver Listado</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("components/modal_agregar_auto.php"); ?>

<?php include_once("../components/footer.php"); ?>