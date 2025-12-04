<?php
// Verificar autenticación y rol
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['fk_rol'] != 1) {
    header("Location: ../../pages/inicio.php");
    exit();
}

include_once("../../components/config/conf.php");

// Obtener ID del auto
$id_auto = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id_auto <= 0) {
    header("Location: ../index.php?error=id_invalido");
    exit();
}

// Verificar si el auto tiene reservas activas
$stmt = $con->prepare("SELECT COUNT(*) as total FROM reservas 
                       WHERE fk_auto = ? AND estado IN ('pendiente', 'confirmada')");
$stmt->bind_param("i", $id_auto);
$stmt->execute();
$resultado = $stmt->get_result();
$row = $resultado->fetch_assoc();

if ($row['total'] > 0) {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../index.php?error=auto_con_reservas");
    exit();
}
$stmt->close();

$stmt = $con->prepare("SELECT imagen FROM autos WHERE id_auto = ?");
$stmt->bind_param("i", $id_auto);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows > 0) {
    $auto = $resultado->fetch_assoc();
    $imagen = $auto['imagen'];
    $stmt->close();

    // Eliminar el auto
    $stmt = $con->prepare("DELETE FROM autos WHERE id_auto = ?");
    $stmt->bind_param("i", $id_auto);

    if ($stmt->execute()) {
        // Eliminar imagen física si existe
        if (!empty($imagen)) {
            $ruta_imagen = "../../auto_imgs/$imagen";
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }

        $stmt->close();
        mysqli_close($con);
        header("Location: ../index.php?baja=ok");
        exit();
    } else {
        $stmt->close();
        mysqli_close($con);
        header("Location: ../index.php?error=baja_fallida");
        exit();
    }
} else {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../index.php?error=auto_no_encontrado");
    exit();
}
