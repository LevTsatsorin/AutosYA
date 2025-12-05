<?php
session_start();

// Verificar autenticación
if (!isset($_SESSION['id_usuario'])) {
    header("Location: ../pages/login.php");
    exit();
}

include_once("../components/config/conf.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id_usuario = (int)$_POST['id_usuario'];
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $fk_rol = (int)$_POST['fk_rol'];
    $nueva_clave = isset($_POST['nueva_clave']) ? trim($_POST['nueva_clave']) : '';
    $redirect_param = isset($_POST['redirect']) ? $_POST['redirect'] : '';

    // Validar correo
    if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        header("Location: /AutosYA/usuario/mod_usuario.php?id={$id_usuario}&error=correo_invalido");
        exit();
    }

    // Verificar si el correo ya existe en otro usuario
    $stmt_check = $con->prepare("SELECT id_usuario FROM usuarios WHERE correo = ? AND id_usuario != ?");
    $stmt_check->bind_param("si", $correo, $id_usuario);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows > 0) {
        $stmt_check->close();
        header("Location: /AutosYA/usuario/mod_usuario.php?id={$id_usuario}&error=correo_existe");
        exit();
    }
    $stmt_check->close();

    // Si se proporciona nueva contraseña, actualizar con ella
    if (!empty($nueva_clave)) {
        $clave_hash = password_hash($nueva_clave, PASSWORD_DEFAULT);
        $stmt = $con->prepare("UPDATE usuarios SET nombre = ?, correo = ?, clave = ?, fk_rol = ? WHERE id_usuario = ?");
        $stmt->bind_param("sssii", $nombre, $correo, $clave_hash, $fk_rol, $id_usuario);
    } else {
        // Si no se cambia la contraseña, actualizar solo los demás campos
        $stmt = $con->prepare("UPDATE usuarios SET nombre = ?, correo = ?, fk_rol = ? WHERE id_usuario = ?");
        $stmt->bind_param("ssii", $nombre, $correo, $fk_rol, $id_usuario);
    }

    if ($stmt->execute()) {
        if ($id_usuario === $_SESSION['id_usuario']) {
            $_SESSION['nombre'] = $nombre;
            $_SESSION['fk_rol'] = $fk_rol;
        }

        $stmt->close();
        $con->close();


        if ($redirect_param === 'admin_gestion') {
            $redirect = '/AutosYA/admin/gestion_usuarios.php?mod=ok';
        } elseif (empty($redirect_param) && $id_usuario === $_SESSION['id_usuario']) {
            $redirect = '/AutosYA/usuario/mod_usuario.php?id=' . $id_usuario . '&mod=ok';
        } else {
            $redirect = $_SESSION['fk_rol'] == 1 ? '/AutosYA/admin/index.php?mod=ok' : '/AutosYA/cliente/perfil.php?mod=ok';
        }

        header("Location: {$redirect}");
        exit();
    } else {
        $stmt->close();
        $con->close();
        header("Location: /AutosYA/usuario/mod_usuario.php?id={$id_usuario}&error=db");
        exit();
    }
} else {
    $redirect = $_SESSION['fk_rol'] == 1 ? '/AutosYA/admin/index.php' : '/AutosYA/cliente/perfil.php';
    header("Location: {$redirect}");
    exit();
}
