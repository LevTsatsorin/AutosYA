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

// Crear reserva
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_auto = (int)$_POST['id_auto'];
    $fecha_inicio = trim($_POST['fecha_inicio']);
    $fecha_fin = trim($_POST['fecha_fin']);
    $id_usuario = $_SESSION['id_usuario'];

    // Validar fechas
    if (empty($fecha_inicio) || empty($fecha_fin)) {
        header("Location: alta_reserva.php?auto={$id_auto}&error=fechas_vacias");
        exit();
    }

    if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
        header("Location: alta_reserva.php?auto={$id_auto}&error=fechas_invalidas");
        exit();
    }

    $manana = date('Y-m-d', strtotime('+1 day'));
    if ($fecha_inicio < $manana) {
        header("Location: alta_reserva.php?auto={$id_auto}&error=fecha_pasada");
        exit();
    }

    $stmt = $con->prepare("
        SELECT COUNT(*) as total 
        FROM reservas 
        WHERE fk_auto = ? 
        AND estado IN ('pendiente', 'confirmada')
        AND NOT (fecha_fin < ? OR fecha_inicio > ?)
    ");
    $stmt->bind_param("iss", $id_auto, $fecha_inicio, $fecha_fin);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();

    if ($row['total'] > 0) {
        header("Location: alta_reserva.php?auto={$id_auto}&error=no_disponible");
        exit();
    }

    // Obtener precio del auto
    $stmt = $con->prepare("SELECT precio_por_dia FROM autos WHERE id_auto = ?");
    $stmt->bind_param("i", $id_auto);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        header("Location: ../../pages/inicio.php?error=auto_no_existe");
        exit();
    }

    $auto = $result->fetch_assoc();
    $stmt->close();

    // Calcular precio total
    $dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24);
    $precio_total = $dias * $auto['precio_por_dia'];

    // Crear reserva
    $stmt = $con->prepare("INSERT INTO reservas (fk_usuario, fk_auto, fecha_inicio, fecha_fin, precio_total) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iissd", $id_usuario, $id_auto, $fecha_inicio, $fecha_fin, $precio_total);

    if ($stmt->execute()) {
        $stmt->close();
        $con->close();
        header("Location: ../../cliente/perfil.php?reserva=ok");
        exit();
    } else {
        $stmt->close();
        $con->close();
        header("Location: alta_reserva.php?auto={$id_auto}&error=db_error");
        exit();
    }
}

// GET - Mostrar formulario
if (!isset($_GET['auto'])) {
    header("Location: ../../pages/inicio.php");
    exit();
}

$id_auto = (int)$_GET['auto'];

// Obtener información del auto
$stmt = $con->prepare("SELECT * FROM autos WHERE id_auto = ? AND estado = 'disponible'");
$stmt->bind_param("i", $id_auto);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: ../../pages/inicio.php?error=auto_no_disponible");
    exit();
}

$auto = $result->fetch_assoc();
$stmt->close();

// Obtener reservas existentes para este auto
$stmt = $con->prepare("
    SELECT fecha_inicio, fecha_fin, estado 
    FROM reservas 
    WHERE fk_auto = ? 
    AND estado IN ('pendiente', 'confirmada')
    AND fecha_fin >= CURDATE()
    ORDER BY fecha_inicio ASC
");
$stmt->bind_param("i", $id_auto);
$stmt->execute();
$result_reservas = $stmt->get_result();
$reservas_existentes = [];
while ($reserva = $result_reservas->fetch_assoc()) {
    $reservas_existentes[] = $reserva;
}
$stmt->close();

include_once("../../components/header.php");
?>
<link rel="stylesheet" href="/AutosYA/css/cliente.css">

<div class="container my-4">
    <div class="row">
        <div class="col-12">
            <h1 class="mb-4">
                <i class="bi bi-calendar-check text-primary"></i> Nueva Reserva
            </h1>

            <?php
            // Mostrar mensajes de error
            if (isset($_GET['error'])) {
                $error_msg = [
                    'fechas_vacias' => 'Por favor, selecciona ambas fechas',
                    'fechas_invalidas' => 'La fecha de fin debe ser posterior a la fecha de inicio',
                    'fecha_pasada' => 'No se pueden hacer reservas en fechas pasadas',
                    'no_disponible' => 'El auto no está disponible en las fechas seleccionadas',
                    'db_error' => 'Error al crear la reserva. Por favor, inténtalo de nuevo'
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
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0">
                        <i class="bi bi-car-front-fill"></i> Detalles del Auto
                    </h4>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <?php if (!empty($auto['imagen'])): ?>
                                <img src="../../auto_imgs/<?php echo htmlspecialchars($auto['imagen']); ?>"
                                    alt="<?php echo htmlspecialchars($auto['marca'] . ' ' . $auto['modelo']); ?>"
                                    class="img-fluid rounded">
                            <?php else: ?>
                                <div class="text-center p-5 bg-light rounded">
                                    <i class="bi bi-car-front-fill text-muted" style="font-size: 4rem;"></i>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="col-md-8">
                            <h3><?php echo htmlspecialchars($auto['marca']) . ' ' . htmlspecialchars($auto['modelo']); ?></h3>
                            <p class="mb-2"><i class="bi bi-calendar3"></i> Año: <strong><?php echo $auto['anio']; ?></strong></p>
                            <p class="mb-2"><i class="bi bi-hash"></i> Patente: <strong><?php echo htmlspecialchars($auto['patente']); ?></strong></p>
                            <p class="mb-2"><i class="bi bi-currency-dollar"></i> Precio: <strong>$<?php echo number_format($auto['precio_por_dia'], 2); ?>/día</strong></p>
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Disponible
                            </span>
                        </div>
                    </div>

                    <hr class="my-4">


                    <div id="reservasMesContainer" class="d-none mb-4"></div>

                    <form action="alta_reserva.php" method="POST" class="needs-validation" id="reservaForm" novalidate>
                        <input type="hidden" name="id_auto" value="<?php echo $auto['id_auto']; ?>">

                        <h5 class="mb-3">Selecciona las Fechas</h5>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="fecha_inicio" class="form-label">Fecha de Inicio *</label>
                                <input type="date" class="form-control" id="fecha_inicio" name="fecha_inicio"
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                <div class="invalid-feedback">Por favor, selecciona una fecha de inicio</div>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fecha_fin" class="form-label">Fecha de Fin *</label>
                                <input type="date" class="form-control" id="fecha_fin" name="fecha_fin"
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>" required>
                                <div class="invalid-feedback">Por favor, selecciona una fecha de fin</div>
                            </div>
                        </div>

                        <div id="disponibilidadMsg" class="alert alert-persistent d-none mb-3" role="alert"></div>

                        <div class="d-flex justify-content-center gap-3 mt-4">
                            <a href="../../pages/inicio.php" class="btn btn-secondary">
                                <i class="bi bi-x-circle"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-primary" id="submitBtn" disabled>
                                <i class="bi bi-check-circle"></i> Confirmar Reserva
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-lg reserva-resumen">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">
                        <i class="bi bi-calculator"></i> Resumen de Reserva
                    </h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Precio por día:</span>
                            <strong>$<span id="precio_dia"><?php echo number_format($auto['precio_por_dia'], 2); ?></span></strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span>Días:</span>
                            <strong><span id="total_dias">-</span></strong>
                        </div>
                        <hr>
                        <div class="d-flex justify-content-between">
                            <h5 class="mb-0">Total:</h5>
                            <h4 class="mb-0 text-success">$<span id="precio_total">0.00</span></h4>
                        </div>
                    </div>
                    <div class="alert alert-info alert-persistent small mb-0">
                        <i class="bi bi-info-circle"></i> El precio total se calculará automáticamente al seleccionar las fechas
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const precioPorDia = <?php echo $auto['precio_por_dia']; ?>;
    const idAuto = <?php echo $auto['id_auto']; ?>;
    const reservasExistentes = <?php echo json_encode($reservas_existentes); ?>;
</script>
<script src="/AutosYA/js/reserva-periodos.js"></script>
<script src="/AutosYA/js/reserva-validation.js"></script>

<?php include_once("../../components/footer.php"); ?>