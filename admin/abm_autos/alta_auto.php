<?php
// Verificar autenticación y rol
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['fk_rol'] != 1) {
    header("Location: ../../pages/inicio.php");
    exit();
}

include_once("../../components/config/conf.php");


if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php?error=metodo_invalido");
    exit();
}

$marca = htmlspecialchars(trim($_POST['marca']), ENT_QUOTES, 'UTF-8');
$modelo = htmlspecialchars(trim($_POST['modelo']), ENT_QUOTES, 'UTF-8');
$anio = intval($_POST['anio']);
$patente = htmlspecialchars(trim($_POST['patente']), ENT_QUOTES, 'UTF-8');
$precio_por_dia = floatval($_POST['precio_por_dia']);

// Validar campos vacíos
if (empty($marca) || empty($modelo) || empty($patente) || $anio <= 0 || $precio_por_dia <= 0) {
    header("Location: ../index.php?error=campos_invalidos");
    exit();
}

// Verificar si la patente ya existe
$stmt = $con->prepare("SELECT id_auto FROM autos WHERE patente = ?");
$stmt->bind_param("s", $patente);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../index.php?error=patente_existente");
    exit();
}
$stmt->close();

// Insertar nuevo auto
$stmt = $con->prepare("INSERT INTO autos (marca, modelo, anio, patente, precio_por_dia, estado) 
                       VALUES (?, ?, ?, ?, ?, 'disponible')");
$stmt->bind_param("ssisd", $marca, $modelo, $anio, $patente, $precio_por_dia);

if ($stmt->execute()) {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../index.php?alta=ok");
    exit();
} else {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../index.php?error=alta_fallida");
    exit();
}
