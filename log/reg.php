<?php
include_once("../components/config/conf.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/registro.php?error=metodo_invalido");
    exit();
}

$nombre = htmlspecialchars(trim($_POST['nombre']), ENT_QUOTES, 'UTF-8');
$correo = htmlspecialchars(trim($_POST['correo']), ENT_QUOTES, 'UTF-8');
$clave = $_POST['clave'];
$clave_rep = $_POST['clave_rep'];

// Validar campos vacíos
if (empty($nombre) || empty($correo) || empty($clave) || empty($clave_rep)) {
    header("Location: ../pages/registro.php?error=campos_vacios");
    exit();
}

// Validar formato de correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    header("Location: ../pages/registro.php?error=correo_invalido");
    exit();
}

// Validar longitud de contraseña
if (strlen($clave) < 8) {
    header("Location: ../pages/registro.php?error=clave_corta");
    exit();
}

// Validar que las contraseñas coincidan
if ($clave !== $clave_rep) {
    header("Location: ../pages/registro.php?error=claves_no_coinciden");
    exit();
}

// Verificar si el correo ya existe
$stmt = $con->prepare("SELECT id_usuario FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $stmt->close();
    header("Location: ../pages/registro.php?error=correo_existente");
    exit();
}
$stmt->close();

$clave_hash = password_hash($clave, PASSWORD_BCRYPT);

// Insertar nuevo usuario con rol de cliente (fk_rol = 2)
$stmt = $con->prepare("INSERT INTO usuarios (nombre, correo, clave, fk_rol) VALUES (?, ?, ?, 2)");
$stmt->bind_param("sss", $nombre, $correo, $clave_hash);

if ($stmt->execute()) {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../pages/login.php?registro=exitoso");
    exit();
} else {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../pages/registro.php?error=registro_fallido");
    exit();
}
