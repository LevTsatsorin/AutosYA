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

// Validar ID de reserva
if (!isset($_GET['id'])) {
    header("Location: ../../cliente/perfil.php?error=reserva_no_especificada");
    exit();
}

$id_reserva = (int)$_GET['id'];
$id_usuario = $_SESSION['id_usuario'];

$redirect_param = isset($_GET['redirect']) ? $_GET['redirect'] : '';

if ($redirect_param === 'mis_reservas') {
    $return_url = '/AutosYA/cliente/mis_reservas.php';
} else {
    $return_url = '/AutosYA/cliente/perfil.php';
}

$stmt = $con->prepare("SELECT estado FROM reservas WHERE id_reserva = ? AND fk_usuario = ?");
$stmt->bind_param("ii", $id_reserva, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: {$return_url}?error=reserva_no_encontrada");
    exit();
}

$reserva = $result->fetch_assoc();
$stmt->close();

if ($reserva['estado'] !== 'pendiente') {
    header("Location: {$return_url}?error=no_se_puede_cancelar");
    exit();
}

// Cancelar reserva
$stmt = $con->prepare("UPDATE reservas SET estado = 'cancelada' WHERE id_reserva = ? AND fk_usuario = ?");
$stmt->bind_param("ii", $id_reserva, $id_usuario);

if ($stmt->execute()) {
    $stmt->close();
    $con->close();
    header("Location: {$return_url}?cancelar=ok");
    exit();
} else {
    $stmt->close();
    $con->close();
    header("Location: {$return_url}?error=db_error");
    exit();
}
