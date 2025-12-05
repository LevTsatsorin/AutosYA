<?php
session_start();

if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../../pages/login.php");
    exit();
}

if ($_SESSION['fk_rol'] != 1) {
    header("Location: ../../pages/inicio.php");
    exit();
}

include_once("../../components/config/conf.php");

if (!isset($_GET['id'])) {
    header("Location: ../gestion_reservas.php?error=reserva_no_encontrada");
    exit();
}

$id_reserva = (int)$_GET['id'];

$stmt = $con->prepare("SELECT id_reserva FROM reservas WHERE id_reserva = ?");
$stmt->bind_param("i", $id_reserva);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    $stmt->close();
    header("Location: ../gestion_reservas.php?error=reserva_no_encontrada");
    exit();
}
$stmt->close();

// Eliminar reserva
$stmt = $con->prepare("DELETE FROM reservas WHERE id_reserva = ?");
$stmt->bind_param("i", $id_reserva);

if ($stmt->execute()) {
    $stmt->close();
    $con->close();
    header("Location: ../gestion_reservas.php?baja=ok");
    exit();
} else {
    $stmt->close();
    $con->close();
    header("Location: ../gestion_reservas.php?error=db_error");
    exit();
}
