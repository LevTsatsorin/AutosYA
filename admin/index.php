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
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">
                        <i class="bi bi-car-front-fill"></i> Gestión de Autos
                    </h4>
                    <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalAgregarAuto">
                        <i class="bi bi-plus-circle"></i> Agregar Auto
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Marca</th>
                                    <th>Modelo</th>
                                    <th>Año</th>
                                    <th>Patente</th>
                                    <th>Precio/Día</th>
                                    <th>Estado</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Configuración de paginación
                                $registros_por_pagina = 10;
                                $pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                                $offset = ($pagina_actual - 1) * $registros_por_pagina;

                                // Contar total
                                $stmt_count = $con->prepare("SELECT COUNT(*) as total FROM autos");
                                $stmt_count->execute();
                                $result_count = $stmt_count->get_result();
                                $total_registros = $result_count->fetch_assoc()['total'];
                                $total_paginas = ceil($total_registros / $registros_por_pagina);
                                $stmt_count->close();

                                // Obtener registros de la página actual
                                $stmt = $con->prepare("SELECT * FROM autos ORDER BY id_auto DESC LIMIT ? OFFSET ?");
                                $stmt->bind_param("ii", $registros_por_pagina, $offset);
                                $stmt->execute();
                                $resultado = $stmt->get_result();

                                if ($resultado->num_rows > 0) {
                                    while ($fila = $resultado->fetch_assoc()) {
                                        $badge_class = $fila['estado'] === 'disponible' ? 'bg-success' : ($fila['estado'] === 'reservado' ? 'bg-warning' : 'bg-secondary');

                                        echo '<tr>
                                            <td>' . $fila['id_auto'] . '</td>
                                            <td>' . htmlspecialchars($fila['marca']) . '</td>
                                            <td>' . htmlspecialchars($fila['modelo']) . '</td>
                                            <td>' . $fila['anio'] . '</td>
                                            <td><strong>' . htmlspecialchars($fila['patente']) . '</strong></td>
                                            <td>$' . number_format($fila['precio_por_dia'], 2) . '</td>
                                            <td><span class="badge ' . $badge_class . '">' . ucfirst($fila['estado']) . '</span></td>
                                            <td>
                                                <a href="abm_autos/mod_auto.php?id=' . $fila['id_auto'] . '" 
                                                   class="btn btn-sm btn-warning" title="Modificar">
                                                    <i class="bi bi-pencil"></i>
                                                </a>
                                                <a href="abm_autos/baja_auto.php?id=' . $fila['id_auto'] . '" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm(\'Eliminar este auto?\')" 
                                                   title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </a>
                                            </td>
                                          </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="8" class="text-center text-muted">No hay autos registrados</td></tr>';
                                }
                                $stmt->close();
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_paginas > 1): ?>
                        <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3">
                            <div class="text-muted">
                                Mostrando <?php echo min($offset + 1, $total_registros); ?> - <?php echo min($offset + $registros_por_pagina, $total_registros); ?> de <?php echo $total_registros; ?> autos
                            </div>
                            <nav aria-label="Paginación">
                                <ul class="pagination mb-0">
                                    <!-- Botón Anterior -->
                                    <li class="page-item <?php echo $pagina_actual <= 1 ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?pagina=<?php echo $pagina_actual - 1; ?>">
                                            <i class="bi bi-chevron-left"></i>
                                        </a>
                                    </li>

                                    <?php
                                    // Mostrar páginas
                                    $rango = 2; // Cuántas páginas mostrar a cada lado de la actual

                                    for ($i = 1; $i <= $total_paginas; $i++) {
                                        // Mostrar primera página, última página, página actual y páginas cercanas
                                        if ($i == 1 || $i == $total_paginas || ($i >= $pagina_actual - $rango && $i <= $pagina_actual + $rango)) {
                                            $active = $i == $pagina_actual ? 'active' : '';
                                            echo '<li class="page-item ' . $active . '">
                                            <a class="page-link" href="?pagina=' . $i . '">' . $i . '</a>
                                          </li>';
                                        } elseif ($i == $pagina_actual - $rango - 1 || $i == $pagina_actual + $rango + 1) {
                                            echo '<li class="page-item disabled"><span class="page-link">...</span></li>';
                                        }
                                    }
                                    ?>

                                    <!-- Botón Siguiente -->
                                    <li class="page-item <?php echo $pagina_actual >= $total_paginas ? 'disabled' : ''; ?>">
                                        <a class="page-link" href="?pagina=<?php echo $pagina_actual + 1; ?>">
                                            <i class="bi bi-chevron-right"></i>
                                        </a>
                                    </li>
                                </ul>
                            </nav>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Agregar Auto -->
<div class="modal fade" id="modalAgregarAuto" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="abm_autos/alta_auto.php" method="POST">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-plus-circle"></i> Agregar Nuevo Auto
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="marca" class="form-label">Marca</label>
                        <input type="text" class="form-control" id="marca" name="marca" required>
                    </div>
                    <div class="mb-3">
                        <label for="modelo" class="form-label">Modelo</label>
                        <input type="text" class="form-control" id="modelo" name="modelo" required>
                    </div>
                    <div class="mb-3">
                        <label for="anio" class="form-label">Año</label>
                        <input type="number" class="form-control" id="anio" name="anio"
                            min="1900" max="2026" required>
                    </div>
                    <div class="mb-3">
                        <label for="patente" class="form-label">Patente</label>
                        <input type="text" class="form-control" id="patente" name="patente" required>
                    </div>
                    <div class="mb-3">
                        <label for="precio_por_dia" class="form-label">Precio por Día ($)</label>
                        <input type="number" class="form-control" id="precio_por_dia" name="precio_por_dia"
                            step="0.01" min="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save"></i> Guardar Auto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once("../components/footer.php"); ?>