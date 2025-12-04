<?php
// Verificar autenticación y rol
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['fk_rol'] != 1) {
    header("Location: ../../pages/inicio.php");
    exit();
}

include_once("../../components/config/conf.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../gestion_autos.php?error=metodo_invalido");
    exit();
}

$id_auto = intval($_POST['id_auto']);
$marca = htmlspecialchars(trim($_POST['marca']), ENT_QUOTES, 'UTF-8');
$modelo = htmlspecialchars(trim($_POST['modelo']), ENT_QUOTES, 'UTF-8');
$anio = intval($_POST['anio']);
$patente = htmlspecialchars(trim($_POST['patente']), ENT_QUOTES, 'UTF-8');
$precio_por_dia = floatval($_POST['precio_por_dia']);
$estado = htmlspecialchars(trim($_POST['estado']), ENT_QUOTES, 'UTF-8');

// Validar campos vacíos
if (
    $id_auto <= 0 || empty($marca) || empty($modelo) || empty($patente) ||
    $anio <= 0 || $precio_por_dia <= 0
) {
    header("Location: ../gestion_autos.php?error=campos_invalidos");
    exit();
}

// Validar estado
$estados_validos = ['disponible', 'reservado', 'mantenimiento'];
if (!in_array($estado, $estados_validos)) {
    header("Location: ../gestion_autos.php?error=estado_invalido");
    exit();
}

// Verificar si la patente ya existe en otro auto
$stmt = $con->prepare("SELECT id_auto FROM autos WHERE patente = ? AND id_auto != ?");
$stmt->bind_param("si", $patente, $id_auto);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $stmt->close();
    mysqli_close($con);
    header("Location: mod_auto.php?id=$id_auto&error=patente_existente");
    exit();
}
$stmt->close();

// Actualizar el auto
$stmt = $con->prepare("UPDATE autos SET marca=?, modelo=?, anio=?, patente=?, 
                       precio_por_dia=?, estado=? WHERE id_auto=?");
$stmt->bind_param("ssisssi", $marca, $modelo, $anio, $patente, $precio_por_dia, $estado, $id_auto);

if ($stmt->execute()) {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../gestion_autos.php?mod=ok");
    exit();
} else {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../gestion_autos.php?error=mod_fallida");
    exit();
}
