<?php

header('Content-Type: application/json');

session_start();

// Verificar autenticación
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['error' => 'No autenticado']);
    exit();
}

include_once("../../components/config/conf.php");

// Validar parámetros
if (!isset($_GET['auto']) || !isset($_GET['fecha_inicio']) || !isset($_GET['fecha_fin'])) {
    echo json_encode(['error' => 'Parámetros incompletos']);
    exit();
}

$id_auto = (int)$_GET['auto'];
$fecha_inicio = $_GET['fecha_inicio'];
$fecha_fin = $_GET['fecha_fin'];
$id_reserva_excluir = isset($_GET['reserva_id']) ? (int)$_GET['reserva_id'] : null;

// Validar formato de fechas
if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_inicio) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $fecha_fin)) {
    echo json_encode(['error' => 'Formato de fecha inválido']);
    exit();
}

// Validar que fecha_fin > fecha_inicio
if (strtotime($fecha_fin) <= strtotime($fecha_inicio)) {
    echo json_encode([
        'disponible' => false,
        'mensaje' => 'La fecha de fin debe ser posterior a la fecha de inicio'
    ]);
    exit();
}

// Validar que no sean fechas del pasado
$manana = date('Y-m-d', strtotime('+1 day'));
if ($fecha_inicio < $manana) {
    echo json_encode([
        'disponible' => false,
        'mensaje' => 'Las reservas deben comenzar a partir de mañana'
    ]);
    exit();
}

// Obtener información del auto
$stmt = $con->prepare("SELECT precio_por_dia, marca, modelo FROM autos WHERE id_auto = ? AND estado = 'disponible'");
$stmt->bind_param("i", $id_auto);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode([
        'disponible' => false,
        'mensaje' => 'El auto no está disponible'
    ]);
    $stmt->close();
    exit();
}

$auto = $result->fetch_assoc();
$stmt->close();

// Verificar si hay otras reservas
if ($id_reserva_excluir) {
    $stmt = $con->prepare("
        SELECT COUNT(*) as total 
        FROM reservas 
        WHERE fk_auto = ? 
        AND id_reserva != ?
        AND estado IN ('pendiente', 'confirmada')
        AND NOT (fecha_fin < ? OR fecha_inicio > ?)
    ");
    $stmt->bind_param("iiss", $id_auto, $id_reserva_excluir, $fecha_inicio, $fecha_fin);
} else {
    $stmt = $con->prepare("
        SELECT COUNT(*) as total 
        FROM reservas 
        WHERE fk_auto = ? 
        AND estado IN ('pendiente', 'confirmada')
        AND NOT (fecha_fin < ? OR fecha_inicio > ?)
    ");
    $stmt->bind_param("iss", $id_auto, $fecha_inicio, $fecha_fin);
}
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

$hay_conflicto = $row['total'] > 0;

// Calcular días y precio total
$dias = (strtotime($fecha_fin) - strtotime($fecha_inicio)) / (60 * 60 * 24);
$precio_total = $dias * $auto['precio_por_dia'];

if ($hay_conflicto) {
    echo json_encode([
        'disponible' => false,
        'mensaje' => 'El auto no está disponible en las fechas seleccionadas. Por favor, elige otras fechas.',
        'precio_dia' => (float)$auto['precio_por_dia']
    ]);
} else {
    echo json_encode([
        'disponible' => true,
        'mensaje' => '¡Auto disponible! ' . $auto['marca'] . ' ' . $auto['modelo'],
        'precio_dia' => (float)$auto['precio_por_dia'],
        'dias' => (int)$dias,
        'precio_total' => (float)$precio_total
    ]);
}

$con->close();
