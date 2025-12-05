<?php
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['fk_rol'] != 1) {
    header("Location: ../../pages/login.php");
    exit();
}

include_once("../../components/config/conf.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id_reserva']) || !isset($_POST['nuevo_estado'])) {
    header("Location: ../gestion_reservas.php?error=datos_invalidos");
    exit();
}

$id_reserva = (int)$_POST['id_reserva'];
$nuevo_estado = trim($_POST['nuevo_estado']);
$pagina = isset($_POST['pagina']) ? (int)$_POST['pagina'] : 1;

// Validar estados permitidos
$estados_validos = ['pendiente', 'confirmada', 'completada', 'cancelada'];
if (!in_array($nuevo_estado, $estados_validos)) {
    header("Location: ../gestion_reservas.php?error=estado_invalido&pagina=" . $pagina);
    exit();
}

// Obtener estado actual de la reserva
$stmt = $con->prepare("SELECT estado FROM reservas WHERE id_reserva = ?");
$stmt->bind_param("i", $id_reserva);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: ../gestion_reservas.php?error=reserva_no_encontrada&pagina=" . $pagina);
    exit();
}

$reserva = $result->fetch_assoc();
$estado_actual = $reserva['estado'];
$stmt->close();

// Validar que el nuevo estado sea diferente al actual
if ($nuevo_estado === $estado_actual) {
    header("Location: ../gestion_reservas.php?error=mismo_estado&pagina=" . $pagina);
    exit();
}

// Validar transiciones de estado permitidas
if ($nuevo_estado === 'completada' && $estado_actual !== 'confirmada') {
    header("Location: ../gestion_reservas.php?error=solo_confirmadas_completar&pagina=" . $pagina);
    exit();
}

// Actualizar estado
$stmt = $con->prepare("UPDATE reservas SET estado = ? WHERE id_reserva = ?");
$stmt->bind_param("si", $nuevo_estado, $id_reserva);

if ($stmt->execute()) {
    $stmt->close();
    $con->close();
    header("Location: ../gestion_reservas.php?estado_actualizado=" . $nuevo_estado . "&pagina=" . $pagina);
    exit();
} else {
    $stmt->close();
    $con->close();
    header("Location: ../gestion_reservas.php?error=db_error&pagina=" . $pagina);
    exit();
}
