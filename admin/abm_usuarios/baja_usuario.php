<?php
session_start();

// Verificar autenticación y rol
if (!isset($_SESSION['id_usuario']) || $_SESSION['fk_rol'] != 1) {
    header("Location: ../../pages/login.php");
    exit();
}

include_once("../../components/config/conf.php");

if (!isset($_GET['id'])) {
    header("Location: ../gestion_usuarios.php");
    exit();
}

$id_usuario = (int)$_GET['id'];

// No permitir que el admin se elimine a sí mismo
if ($id_usuario === $_SESSION['id_usuario']) {
    header("Location: ../gestion_usuarios.php?error=self_delete");
    exit();
}

// Eliminar usuario
$stmt = $con->prepare("DELETE FROM usuarios WHERE id_usuario = ?");
$stmt->bind_param("i", $id_usuario);

if ($stmt->execute()) {
    $stmt->close();
    $con->close();
    header("Location: ../gestion_usuarios.php?baja=ok");
    exit();
} else {
    $stmt->close();
    $con->close();
    header("Location: ../gestion_usuarios.php?error=db");
    exit();
}
