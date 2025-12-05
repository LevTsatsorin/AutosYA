<?php
// Verificar autenticación y rol
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../pages/login.php");
    exit();
}

if ($_SESSION['fk_rol'] != 2) {
    header("Location: ../pages/inicio.php");
    exit();
}

include_once("../components/header.php");
?>
<link rel="stylesheet" href="/AutosYA/css/cliente.css">

<?php
// Paginación
$registros_por_pagina = 10;
$pagina_actual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Contar total de reservas
$stmt_count = $con->prepare("SELECT COUNT(*) as total FROM reservas WHERE fk_usuario = ?");
$stmt_count->bind_param("i", $_SESSION['id_usuario']);
$stmt_count->execute();
$result_count = $stmt_count->get_result();
$total_reservas = $result_count->fetch_assoc()['total'];
$total_paginas = ceil($total_reservas / $registros_por_pagina);
$stmt_count->close();

// Obtener reservas con paginación
$stmt = $con->prepare("
    SELECT r.*, a.marca, a.modelo, a.patente, a.imagen
    FROM reservas r
    INNER JOIN autos a ON r.fk_auto = a.id_auto
    WHERE r.fk_usuario = ?
    ORDER BY r.id_reserva DESC
    LIMIT ? OFFSET ?
");
$stmt->bind_param("iii", $_SESSION['id_usuario'], $registros_por_pagina, $offset);
$stmt->execute();
$result = $stmt->get_result();
?>

<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="bi bi-calendar-event text-primary"></i> Mis Reservas
            </h1>

            <?php
            // Mostrar mensajes de éxito/error
            if (isset($_GET['reserva']) && $_GET['reserva'] === 'ok') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> ¡Reserva creada exitosamente!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            }
            if (isset($_GET['cancelar']) && $_GET['cancelar'] === 'ok') {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle"></i> Reserva cancelada correctamente.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            }
            if (isset($_GET['error'])) {
                $error_msg = [
                    'reserva_no_encontrada' => 'No se encontró la reserva',
                    'no_se_puede_cancelar' => 'Solo se pueden cancelar reservas pendientes',
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
            <div class="card shadow-lg reservas-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h4 class="mb-0">
                        <i class="bi bi-list-ul"></i> Listado de Reservas
                    </h4>
                    <a href="../pages/inicio.php" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> <span>Nueva Reserva</span>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Auto</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Estado</th>
                                    <th>Precio Total</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                if ($result->num_rows > 0) {
                                    while ($reserva = $result->fetch_assoc()) {
                                        $badge_class = match ($reserva['estado']) {
                                            'confirmada' => 'bg-success',
                                            'pendiente' => 'bg-warning text-dark',
                                            'completada' => 'bg-info',
                                            'cancelada' => 'bg-danger',
                                            default => 'bg-secondary'
                                        };

                                        $precio = $reserva['precio_total'] ? '$' . number_format($reserva['precio_total'], 2) : '-';
                                        $puede_cancelar = $reserva['estado'] === 'pendiente';

                                        echo "<tr>";
                                        echo "<td>" . htmlspecialchars($reserva['id_reserva']) . "</td>";
                                        echo "<td>
                                            <strong>" . htmlspecialchars($reserva['marca']) . " " . htmlspecialchars($reserva['modelo']) . "</strong><br>
                                            <small class='text-muted'>" . htmlspecialchars($reserva['patente']) . "</small>
                                        </td>";
                                        echo "<td>" . date('d/m/Y', strtotime($reserva['fecha_inicio'])) . "</td>";
                                        echo "<td>" . date('d/m/Y', strtotime($reserva['fecha_fin'])) . "</td>";
                                        echo "<td><span class='badge {$badge_class}'>" . htmlspecialchars(ucfirst($reserva['estado'])) . "</span></td>";
                                        echo "<td>{$precio}</td>";
                                        echo "<td class='text-center'>";

                                        if ($puede_cancelar) {
                                            echo "<a href='abm_reservas/mod_reserva.php?id={$reserva['id_reserva']}' 
                                                     class='btn btn-sm btn-warning me-1' 
                                                     title='Modificar'>
                                                    <i class='bi bi-pencil'></i>
                                                  </a>";
                                            echo "<button type='button' class='btn btn-sm btn-danger' 
                                                    data-bs-toggle='modal' 
                                                    data-bs-target='#modalCancelarReserva{$reserva['id_reserva']}' 
                                                    title='Eliminar'>
                                                    <i class='bi bi-trash'></i>
                                                  </button>";
                                        } else {
                                            echo "<span class='text-muted small'>-</span>";
                                        }

                                        echo "</td>";
                                        echo "</tr>";

                                        // Modal de confirmación de cancelación
                                        if ($puede_cancelar) {
                                            echo "
                                            <div class='modal fade' id='modalCancelarReserva{$reserva['id_reserva']}' tabindex='-1'>
                                                <div class='modal-dialog'>
                                                    <div class='modal-content'>
                                                        <div class='modal-header bg-danger text-white'>
                                                            <h5 class='modal-title'>
                                                                <i class='bi bi-exclamation-triangle'></i> Confirmar Cancelación
                                                            </h5>
                                                            <button type='button' class='btn-close btn-close-white' data-bs-dismiss='modal'></button>
                                                        </div>
                                                        <div class='modal-body'>
                                                            <p>Estás seguro de que deseas cancelar esta reserva?</p>
                                                            <p><strong>" . htmlspecialchars($reserva['marca']) . " " . htmlspecialchars($reserva['modelo']) . "</strong></p>
                                                            <p class='text-muted'><small>
                                                                Del " . date('d/m/Y', strtotime($reserva['fecha_inicio'])) . " 
                                                                al " . date('d/m/Y', strtotime($reserva['fecha_fin'])) . "
                                                            </small></p>
                                                        </div>
                                                        <div class='modal-footer'>
                                                            <button type='button' class='btn btn-secondary' data-bs-dismiss='modal'>
                                                                <i class='bi bi-arrow-left'></i> Volver
                                                            </button>
                                                            <a href='abm_reservas/baja_reserva.php?id={$reserva['id_reserva']}&redirect=mis_reservas' 
                                                               class='btn btn-danger'>
                                                                <i class='bi bi-x-circle'></i> Cancelar Reserva
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>";
                                        }
                                    }
                                } else {
                                    echo "<tr><td colspan='7' class='text-center text-muted py-4'>No tienes reservas aún. <a href='../pages/inicio.php'>¡Reserva tu primer auto!</a></td></tr>";
                                }
                                $stmt->close();
                                ?>
                            </tbody>
                        </table>
                    </div>

                    <?php if ($total_paginas > 1): ?>
                        <div class="d-flex justify-content-between align-items-center mt-3 px-3 pb-3">
                            <div class="text-muted">
                                Mostrando <?php echo min($offset + 1, $total_reservas); ?> - <?php echo min($offset + $registros_por_pagina, $total_reservas); ?> de <?php echo $total_reservas; ?> reservas
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
</div>

<?php include_once("../components/footer.php"); ?>