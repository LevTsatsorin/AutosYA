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

<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="bi bi-person-circle text-primary"></i> Mi Perfil
            </h1>

            <?php
            // Mostrar mensajes de éxito/error
            if (isset($_GET['reserva']) && $_GET['reserva'] === 'ok') {
                echo '<div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle"></i> Reserva creada exitosamente!
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            }
            if (isset($_GET['cancelar']) && $_GET['cancelar'] === 'ok') {
                echo '<div class="alert alert-info alert-dismissible fade show" role="alert">
                    <i class="bi bi-info-circle"></i> Reserva cancelada.
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                  </div>';
            }
            ?>
        </div>
    </div>

    <div class="row mb-4">
        <!-- Información del usuario -->
        <div class="col-md-12 col-lg-4 mb-3">
            <div class="card h-100 shadow-lg border-0 user-info-card">
                <div class="card-body d-flex flex-column">
                    <div class="d-flex align-items-center mb-3">
                        <div class="stats-icon-circle me-3">
                            <i class="bi bi-person-badge fs-3"></i>
                        </div>
                        <h5 class="stats-title mb-0">Información Personal</h5>
                    </div>

                    <div class="user-info-item">
                        <i class="bi bi-person-fill"></i>
                        <span><?php echo htmlspecialchars($_SESSION['nombre']); ?></span>
                    </div>

                    <div class="user-info-item">
                        <i class="bi bi-envelope-fill"></i>
                        <span><?php echo htmlspecialchars($_SESSION['correo']); ?></span>
                    </div>

                    <div class="user-info-item">
                        <i class="bi bi-calendar-check-fill"></i>
                        <span>Miembro desde <?php
                                            $stmt = $con->prepare("SELECT DATE_FORMAT(created_at, '%d/%m/%Y') as fecha FROM usuarios WHERE id_usuario = ?");
                                            $stmt->bind_param("i", $_SESSION['id_usuario']);
                                            $stmt->execute();
                                            $result = $stmt->get_result();
                                            $row = $result->fetch_assoc();
                                            echo $row['fecha'];
                                            $stmt->close();
                                            ?></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estadísticas del usuario -->
        <div class="col-md-12 col-lg-8">
            <div class="row h-100">
                <div class="col-md-6 mb-3">
                    <div class="card text-white h-100 shadow-lg border-0 stats-card-purple">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stats-icon-circle me-3">
                                    <i class="bi bi-clock-history fs-3"></i>
                                </div>
                                <h5 class="stats-title mb-0">Reservas Activas</h5>
                            </div>
                            <h1 class="stats-number mb-0 mt-auto">
                                <?php
                                $stmt = $con->prepare("SELECT COUNT(*) as total FROM reservas 
                                                   WHERE fk_usuario=? AND estado IN ('pendiente', 'confirmada')");
                                $stmt->bind_param("i", $_SESSION['id_usuario']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();
                                echo $row['total'];
                                $stmt->close();
                                ?>
                            </h1>
                        </div>
                    </div>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="card text-white h-100 shadow-lg border-0 stats-card-blue">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex align-items-center mb-3">
                                <div class="stats-icon-circle me-3">
                                    <i class="bi bi-calendar-check fs-3"></i>
                                </div>
                                <h5 class="stats-title mb-0">Reservas Completadas</h5>
                            </div>
                            <h1 class="stats-number mb-0 mt-auto">
                                <?php
                                $stmt = $con->prepare("SELECT COUNT(*) as total FROM reservas 
                                                   WHERE fk_usuario=? AND estado='completada'");
                                $stmt->bind_param("i", $_SESSION['id_usuario']);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                $row = $result->fetch_assoc();
                                echo $row['total'];
                                $stmt->close();
                                ?>
                            </h1>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <div class="card shadow reservas-card">
                <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-3">
                    <h4 class="mb-0">
                        <i class="bi bi-calendar-event"></i> Mis Últimas Reservas
                    </h4>
                    <a href="mis_reservas.php" class="btn btn-primary btn-sm">
                        <i class="bi bi-calendar-check"></i> <span>Ver Todas Mis Reservas</span>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Auto</th>
                                    <th>Fecha Inicio</th>
                                    <th>Fecha Fin</th>
                                    <th>Estado</th>
                                    <th>Precio Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $stmt = $con->prepare("SELECT r.*, a.marca, a.modelo, a.patente 
                                                   FROM reservas r
                                                   JOIN autos a ON r.fk_auto = a.id_auto
                                                   WHERE r.fk_usuario = ?
                                                   ORDER BY r.id_reserva DESC
                                                   LIMIT 5");
                                $stmt->bind_param("i", $_SESSION['id_usuario']);
                                $stmt->execute();
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
                                            <td><strong>' . htmlspecialchars($fila['marca']) . ' ' .
                                            htmlspecialchars($fila['modelo']) . '</strong><br>
                                                <small class="text-muted">' . htmlspecialchars($fila['patente']) . '</small>
                                            </td>
                                            <td>' . date('d/m/Y', strtotime($fila['fecha_inicio'])) . '</td>
                                            <td>' . date('d/m/Y', strtotime($fila['fecha_fin'])) . '</td>
                                            <td><span class="badge ' . $badge_class . '">' . ucfirst($fila['estado']) . '</span></td>
                                            <td>' . $precio . '</td>
                                          </tr>';
                                    }
                                } else {
                                    echo '<tr><td colspan="6" class="text-center text-muted">No tienes reservas aún</td></tr>';
                                }
                                $stmt->close();
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include_once("../components/footer.php"); ?>