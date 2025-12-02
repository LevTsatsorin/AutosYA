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
        <p class="lead">Bienvenido, <strong><?php echo htmlspecialchars($_SESSION['nombre']); ?></strong></p>
        
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
        <div class="card shadow h-100">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="bi bi-person-badge"></i> Información Personal
                </h5>
            </div>
            <div class="card-body">
                <p><strong><i class="bi bi-person"></i> Nombre:</strong><br>
                <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
                
                <p><strong><i class="bi bi-envelope"></i> Correo:</strong><br>
                <?php echo htmlspecialchars($_SESSION['correo']); ?></p>
                
                <p><strong><i class="bi bi-shield-check"></i> Rol:</strong><br>
                <span class="badge bg-success">Cliente</span></p>
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
                                <i class="bi bi-calendar-check fs-3"></i>
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
                                <i class="bi bi-clock-history fs-3"></i>
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
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <h4 class="mb-0">
                    <i class="bi bi-calendar-event"></i> Mis Últimas Reservas
                </h4>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
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
                                    $badge_class = $fila['estado'] === 'confirmada' ? 'bg-success' : 
                                                  ($fila['estado'] === 'pendiente' ? 'bg-warning text-dark' : 
                                                  ($fila['estado'] === 'completada' ? 'bg-info' : 'bg-danger'));
                                    
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
                
                <?php if ($resultado->num_rows > 0): ?>
                <div class="text-center mt-3">
                    <a href="../pages/mis_reservas.php" class="btn btn-outline-primary">
                        Ver Todas Mis Reservas <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
</div>

<?php include_once("../components/footer.php"); ?>
