<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../pages/login.php");
    exit();
}

if ($_SESSION['fk_rol'] != 2) {
    header("Location: ../../pages/inicio.php");
    exit();
}

include_once("../../components/config/conf.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_reserva = (int)$_POST['id_reserva'];
    $fecha_inicio = trim($_POST['fecha_inicio']);
    $fecha_fin = trim($_POST['fecha_fin']);
    $id_usuario = $_SESSION['id_usuario'];

    // Validar fechas
    if (empty($fecha_inicio) || empty($fecha_fin)) {
        header("Location: mod_reserva.php?id={$id_reserva}&error=fechas_vacias");
        exit();
    }

    if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
        header("Location: mod_reserva.php?id={$id_reserva}&error=fechas_invalidas");
        exit();
    }

    $manana = date('Y-m-d', strtotime('+1 day'));
    if ($fecha_inicio < $manana) {
        header("Location: mod_reserva.php?id={$id_reserva}&error=fecha_pasada");
        exit();
    }

    // Obtener información de la reserva
    $stmt = $con->prepare("SELECT fk_auto, fk_usuario, estado FROM reservas WHERE id_reserva = ?");
    $stmt->bind_param("i", $id_reserva);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        $stmt->close();
        header("Location: ../mis_reservas.php?error=reserva_no_encontrada");
        exit();
    }

    $reserva = $result->fetch_assoc();
    $stmt->close();

    if ($reserva['fk_usuario'] != $id_usuario) {
        header("Location: ../mis_reservas.php?error=acceso_denegado");
        exit();
    }

    if ($reserva['estado'] !== 'pendiente') {
        header("Location: ../mis_reservas.php?error=no_se_puede_modificar");
        exit();
    }

    $id_auto = $reserva['fk_auto'];

    // Verificar disponibilidad
    $stmt = $con->prepare("
        SELECT COUNT(*) as total 
        FROM reservas 
        WHERE fk_auto = ? 
        AND id_reserva != ?
        AND estado IN ('pendiente', 'confirmada')
        AND NOT (fecha_fin < ? OR fecha_inicio > ?)
    ");
    $stmt->bind_param("iiss", $id_auto, $id_reserva, $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row['total'] > 0) {
        header("Location: mod_reserva.php?id={$id_reserva}&error=no_disponible");
        exit();
    }

    // Obtener precio del auto
    $stmt = $con->prepare("SELECT precio_por_dia FROM autos WHERE id_auto = ?");
    $stmt->bind_param("i", $id_auto);
    $stmt->execute();
    $result = $stmt->get_result();
    $auto = $result->fetch_assoc();
    $stmt->close();

    // Calcular precio total
    $dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24);
    $precio_total = $dias * $auto['precio_por_dia'];

    // Actualizar reserva
    $stmt = $con->prepare("UPDATE reservas SET fecha_inicio = ?, fecha_fin = ?, precio_total = ? WHERE id_reserva = ?");
    $stmt->bind_param("ssdi", $fecha_inicio, $fecha_fin, $precio_total, $id_reserva);

    if ($stmt->execute()) {
        $stmt->close();
        $con->close();
        header("Location: ../mis_reservas.php?mod=ok");
        exit();
    } else {
        $stmt->close();
        $con->close();
        header("Location: mod_reserva.php?id={$id_reserva}&error=db_error");
        exit();
    }
}

if (!isset($_GET['id'])) {
    header("Location: ../mis_reservas.php");
    exit();
}

$id_reserva = (int)$_GET['id'];

// Obtener información de la reserva con el auto
$stmt = $con->prepare("
    SELECT r.*, a.marca, a.modelo, a.patente, a.precio_por_dia, a.imagen
    FROM reservas r
    INNER JOIN autos a ON r.fk_auto = a.id_auto
    WHERE r.id_reserva = ? AND r.fk_usuario = ?
");
$stmt->bind_param("ii", $id_reserva, $_SESSION['id_usuario']);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: ../mis_reservas.php?error=reserva_no_encontrada");
    exit();
}

$reserva = $result->fetch_assoc();
$stmt->close();

// Verificar que la reserva esté en estado pendiente
if ($reserva['estado'] !== 'pendiente') {
    header("Location: ../mis_reservas.php?error=no_se_puede_modificar");
    exit();
}

include_once("../../components/header.php");
?>
<link rel="stylesheet" href="/AutosYA/css/cliente.css">

<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="bi bi-pencil-square text-warning"></i> Modificar Reserva
            </h1>

            <?php
            // Mostrar mensajes de error
            if (isset($_GET['error'])) {
                $error_msg = [
                    'fechas_vacias' => 'Por favor, selecciona ambas fechas',
                    'fechas_invalidas' => 'La fecha de fin debe ser posterior a la fecha de inicio',
                    'fecha_pasada' => 'No se pueden hacer reservas en fechas pasadas',
                    'no_disponible' => 'El auto no está disponible en las fechas seleccionadas',
                    'db_error' => 'Error al modificar la reserva. Por favor, inténtalo de nuevo'
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
        <div class="col-lg-8">
            <div class="card shadow-lg">
                <div class="card-header bg-warning text-dark d-flex align-items-center" style="min-height: 60px;">
                    <h4 class="mb-0">
                        <i class="bi bi-car-front-fill"></i> Detalles del Auto
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <?php if (!empty($reserva['imagen'])): ?>
                                <img src="../../auto_imgs/<?php echo htmlspecialchars($reserva['imagen']); ?>"
                                    alt="<?php echo htmlspecialchars($reserva['marca'] . ' ' . $reserva['modelo']); ?>"
                                    class="img-fluid rounded">
                            <?php else: ?>
                                <div class="text-center p-5 bg-light rounded">
                                    <i class="bi bi-car-front-fill text-muted" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h3><?php echo htmlspecialchars($reserva['marca']) . ' ' . htmlspecialchars($reserva['modelo']); ?></h3>
                            <p class="mb-2"><i class="bi bi-hash"></i> Patente: <strong><?php echo htmlspecialchars($reserva['patente']); ?></strong></p>
                            <p class="mb-2"><i class="bi bi-currency-dollar"></i> Precio: <strong>$<?php echo number_format($reserva['precio_por_dia'], 2); ?>/día</strong></p>
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock-history"></i> Pendiente
                            </span>
                        </div>
                    </div>

                    <hr class="my-4">

                    <form action="mod_reserva.php" method="POST" class="needs-validation" id="reservaForm" novalidate>
                        <input type="hidden" name="id_reserva" value="<?php echo $reserva['id_reserva']; ?>">

                        <h5 class="mb-3">Modificar Fechas</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                    value="<?php echo $reserva['fecha_inicio']; ?>"
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                <div class="invalid-feedback">Por favor, selecciona una fecha de inicio</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_fin" class="form-label">Fecha de Fin *</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
                                    value="<?php echo $reserva['fecha_fin']; ?>"
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                <div class="invalid-feedback">Por favor, selecciona una fecha de fin</div>
                            </div>
                        </div>

                        <div id="disponibilidadMsg" class="alert alert-persistent d-none mb-3" role="alert"></div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="../mis_reservas.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-warning" id="submitBtn" disabled>
                                <i class="bi bi-check-circle"></i> Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-lg reserva-resumen">
                <div class="card-header bg-warning text-dark d-flex align-items-center" style="min-height: 60px;">
                    <h5 class="mb-0">
                        <i class="bi bi-calculator"></i> Resumen de Reserva
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Precio por día:</span>
                            <strong>$<span id="precio_dia"><?php echo number_format($reserva['precio_por_dia'], 2); ?></span></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Días:</span>
                            <strong><span id="total_dias">-</span></strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0">Total:</h5>
                            <h4 class="mb-0 text-warning">$<span id="precio_total">0.00</span></h4>
                        </div>
                    </div>
                    <div class="alert alert-info alert-persistent small mb-0">
                        <i class="bi bi-info-circle"></i> El precio total se recalculará automáticamente
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const precioPorDia = <?php echo $reserva['precio_por_dia']; ?>;
    const idAuto = <?php echo $reserva['fk_auto']; ?>;
    const idReserva = <?php echo $reserva['id_reserva']; ?>;
</script>
<script src="/AutosYA/js/reserva-validation.js"></script>

<?php include_once("../../components/footer.php"); ?>