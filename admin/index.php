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

<div class="row">
    <div class="col-12">
        <h1 class="mb-4">
            <i class="bi bi-speedometer2 text-primary"></i> Panel de Administración
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

<div class="row mb-4">
    <!-- Estadísticas rápidas -->
    <div class="col-md-3">
        <div class="card text-white bg-primary mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-car-front"></i> Total Autos
                </h5>
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

    <div class="col-md-3">
        <div class="card text-white bg-success mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-check-circle"></i> Disponibles
                </h5>
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

    <div class="col-md-3">
        <div class="card text-white bg-warning mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-calendar-check"></i> Reservas Activas
                </h5>
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

    <div class="col-md-3">
        <div class="card text-white bg-info mb-3">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="bi bi-people"></i> Total Usuarios
                </h5>
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
        <div class="card shadow">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h4 class="mb-0">
                    <i class="bi bi-car-front-fill"></i> Gestión de Autos
                </h4>
                <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#modalAgregarAuto">
                    <i class="bi bi-plus-circle"></i> Agregar Auto
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead class="table-dark">
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
                            $stmt = $con->prepare("SELECT * FROM autos ORDER BY id_auto DESC");
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
                                                   onclick="return confirm(\'¿Eliminar este auto?\')" 
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