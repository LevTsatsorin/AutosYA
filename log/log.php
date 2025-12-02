<?php

// Iniciar sesión
session_start();

include_once("../components/config/conf.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../pages/login.php?error=metodo_invalido");
    exit();
}

$correo = htmlspecialchars(trim($_POST['correo']), ENT_QUOTES, 'UTF-8');
$clave = $_POST['clave'];

if (empty($correo) || empty($clave)) {
    header("Location: ../pages/login.php?error=campos_vacios");
    exit();
}

// Buscar usuario por correo
$stmt = $con->prepare("SELECT id_usuario, nombre, correo, clave, fk_rol FROM usuarios WHERE correo = ?");
$stmt->bind_param("s", $correo);
$stmt->execute();
$resultado = $stmt->get_result();

// Verificar si el usuario existe
if ($resultado->num_rows === 0) {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../pages/login.php?error=credenciales_invalidas");
    exit();
}

$usuario = $resultado->fetch_assoc();
$stmt->close();
mysqli_close($con);

// Verificar contraseña con password_verify
if (!password_verify($clave, $usuario['clave'])) {
    header("Location: ../pages/login.php?error=credenciales_invalidas");
    exit();
}

session_regenerate_id(true);

// Guardar datos en sesión
$_SESSION['id_usuario'] = $usuario['id_usuario'];
$_SESSION['nombre'] = $usuario['nombre'];
$_SESSION['correo'] = $usuario['correo'];
$_SESSION['fk_rol'] = $usuario['fk_rol'];

// Redirigir según el rol
if ($usuario['fk_rol'] == 1) {
    // Administrador
    header("Location: ../admin/index.php");
} else {
    // Cliente
    header("Location: ../cliente/perfil.php");
}
exit();
