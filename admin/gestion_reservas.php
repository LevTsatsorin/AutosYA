<?php
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
                <i class="bi bi-calendar-check"></i> Gestión de Reservas
            </h1>

            <?php
            // Mostrar mensajes de éxito/error
            if (isset($_GET['baja']) && $_GET['baja'] === 'ok') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> Reserva eliminada exitosamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            }
            if (isset($_GET['error'])) {
                $error_msg = [
                    'reserva_no_encontrada' => 'No se encontró la reserva',
                    'db_error' => 'Error al procesar la solicitud'
                ];
                $error = $_GET['error'];
                if (isset($error_msg[$error])) {
                    echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-triangle"></i> ' . $error_msg[$error] . '
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                      </div>';
                }
            }
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow-lg admin-table-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h4 class="mb-0">
                        <i class="bi bi-list-ul"></i> Listado de Reservas
                    </h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Cliente</th>
                                    <th>Auto</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Precio Total</th>
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
                                $stmt_count = $con->prepare("SELECT COUNT(*) as total FROM reservas");
                                $stmt_count->execute();
                                $result_count = $stmt_count->get_result();
                                $total_reservas = $result_count->fetch_assoc()['total'];
                                $total_paginas = ceil($total_reservas / $registros_por_pagina);
                                $stmt_count->close();

                                // Obtener registros de la página actual
                                $query = "SELECT r.*, 
                                           u.nombre as cliente_nombre, 
                                           u.correo as cliente_email,
                                           a.marca, 
                                           a.modelo, 
                                           a.patente
                                    FROM reservas r
                                    INNER JOIN usuarios u ON r.fk_usuario = u.id_usuario
                                    INNER JOIN autos a ON r.fk_auto = a.id_auto
                                    ORDER BY r.id_reserva DESC 
                                    LIMIT ? OFFSET ?";

                                $stmt = $con->prepare($query);

                                if ($stmt === false) {
                                    echo "<tr><td colspan='8' class='text-center bg-danger text-white'>ERROR: " . htmlspecialchars($con->error) . "</td></tr>";
                                    die();
                                }

                                $stmt->bind_param("ii", $registros_por_pagina, $offset);

                                if (!$stmt->execute()) {
                                    echo "<tr><td colspan='8' class='text-center bg-danger text-white'>ERROR: {$stmt->error}</td></tr>";
                                    die();
                                }

                                $resultado = $stmt->get_result();

                                if ($resultado->num_rows > 0) {
                                    while ($fila = $resultado->fetch_assoc()) {
                                        $badge_class = match ($fila['estado']) {
                                            'confirmada' => 'bg-success',
                                            'pendiente' => 'bg-warning text-dark',
                                            'completada' => 'bg-info',
                                            'cancelada' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };

                                        $precio = $fila['precio_total'] ? '$' . number_format($fila['precio_total'], 2) : '-';

                                        echo '<tr>
                                            <td>' . $fila['id_reserva'] . '</td>
                                            <td>
                                                <strong>' . htmlspecialchars($fila['cliente_nombre']) . '</strong><br>
                                                <small class="text-muted">' . htmlspecialchars($fila['cliente_email']) . '</small>
                                            </td>
                                            <td>
                                                <strong>' . htmlspecialchars($fila['marca']) . ' ' . htmlspecialchars($fila['modelo']) . '</strong><br>
                                                <small class="text-muted">' . htmlspecialchars($fila['patente']) . '</small>
                                            </td>
                                            <td>' . date('d/m/Y', strtotime($fila['fecha_inicio'])) . '</td>
                                            <td>' . date('d/m/Y', strtotime($fila['fecha_fin'])) . '</td>
                                            <td>' . $precio . '</td>
                                            <td><span class="badge ' . $badge_class . '">' . ucfirst($fila['estado']) . '</span></td>
                                            <td class="text-center">
                                                <button type="button" 
                                                   class="btn btn-sm btn-danger" 
                                                   data-bs-toggle="modal" 
                                                   data-bs-target="#eliminarReservaModal' . $fila['id_reserva'] . '"
                                                   title="Eliminar">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </td>
                                          </tr>';

                                        echo '
                                        <div class="modal fade" id="eliminarReservaModal' . $fila['id_reserva'] . '" tabindex="-1" aria-hidden="true">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header bg-danger text-white">
                                                        <h5 class="modal-title">
                                                            <i class="bi bi-exclamation-triangle"></i> Confirmar Eliminación
                                                        </h5>
                                                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p class="mb-2">Estás seguro de que deseas eliminar esta reserva?</p>
                                                        <p class="mb-1"><strong>Cliente:</strong> ' . htmlspecialchars($fila['cliente_nombre']) . '</p>
                                                        <p class="mb-1"><strong>Auto:</strong> ' . htmlspecialchars($fila['marca']) . ' ' . htmlspecialchars($fila['modelo']) . '</p>
                                                        <p class="mb-0"><strong>Fechas:</strong> ' . date('d/m/Y', strtotime($fila['fecha_inicio'])) . ' - ' . date('d/m/Y', strtotime($fila['fecha_fin'])) . '</p>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <div class="d-flex gap-2 w-100 justify-content-center">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                                <i class="bi bi-x-circle"></i> Cancelar
                                                            </button>
                                                            <a href="abm_reservas/baja_reserva_admin.php?id=' . $fila['id_reserva'] . '" class="btn btn-danger">
                                                                <i class="bi bi-trash"></i> Eliminar
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>';
                                    }
                                } else {
                                    echo '<tr><td colspan="8" class="text-center text-muted">No hay reservas registradas</td></tr>';
                                }
                                $stmt->close();
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_paginas > 1): ?>
                        <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3">
                            <div class="text-muted">
                                Mostrando <?php echo min($offset + 1, $total_registros); ?> - <?php echo min($offset + $registros_por_pagina, $total_registros); ?> de <?php echo $total_registros; ?> reservas
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

<?php include_once("../components/footer.php"); ?>