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
            <h1 class="admin-page-title mb-4">
                <i class="bi bi-car-front-fill"></i> Gestión de Autos
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

    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg admin-table-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h4 class="mb-0">
                        <i class="bi bi-list-ul"></i> Listado de Autos
                    </h4>
                    <button class="btn btn-primary admin-action-btn" data-bs-toggle="modal" data-bs-target="#modalAgregarAuto">
                        <i class="bi bi-plus-circle"></i> <span>Agregar Auto</span>
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
                                                <button type="button" 
                                                   class="btn btn-sm btn-danger" 
                                                   data-bs-toggle="modal" 
                                                   data-bs-target="#eliminarAutoModal' . $fila['id_auto'] . '"
                                                   title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                          </tr>';

                                        // Modal de confirmación para cada auto
                                        echo '
                                        <div class="modal fade" id="eliminarAutoModal' . $fila['id_auto'] . '" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">
                                                            <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-2">Estás seguro de que deseas eliminar este auto?</p>
                                                        <p class="mb-0"><strong>' . htmlspecialchars($fila['marca']) . ' ' . htmlspecialchars($fila['modelo']) . ' (' . $fila['anio'] . ')</strong></p>
                                                        <p class="text-muted small mb-0">Patente: ' . htmlspecialchars($fila['patente']) . '</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <div class="d-flex gap-2 w-100 justify-content-center">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class="bi bi-x-circle"></i> Cancelar
                                                            </button>
                                                            <a href="abm_autos/baja_auto.php?id=' . $fila['id_auto'] . '" class="btn btn-danger">
                                                                <i class="bi bi-trash"></i> Eliminar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
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
                                    $rango = 2;

                                    for ($i = 1; $i <= $total_paginas; $i++) {
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

<?php include_once("components/modal_agregar_auto.php"); ?>

<?php include_once("../components/footer.php"); ?>