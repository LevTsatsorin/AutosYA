<?php
// Verificar autenticación y rol
session_start();

if (!isset($_SESSION['id_usuario']) || $_SESSION['fk_rol'] != 1) {
    header("Location: ../../pages/inicio.php");
    exit();
}

include_once("../../components/config/conf.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id_auto'])) {
    $id_auto = intval($_POST['id_auto']);

    // Obtener el nombre de la imagen actual
    $stmt = $con->prepare("SELECT imagen FROM autos WHERE id_auto = ?");
    $stmt->bind_param("i", $id_auto);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        $auto = $resultado->fetch_assoc();
        $imagen_actual = $auto['imagen'];

        // Eliminar archivo físico si existe
        if (!empty($imagen_actual)) {
            $ruta_imagen = "../../auto_imgs/" . $imagen_actual;
            if (file_exists($ruta_imagen)) {
                unlink($ruta_imagen);
            }
        }

        $stmt_update = $con->prepare("UPDATE autos SET imagen = NULL WHERE id_auto = ?");
        $stmt_update->bind_param("i", $id_auto);

        if ($stmt_update->execute()) {
            $stmt_update->close();
            $stmt->close();
            mysqli_close($con);
            header("Location: mod_auto.php?id=" . $id_auto . "&img_deleted=1");
            exit();
        } else {
            $stmt_update->close();
            $stmt->close();
            mysqli_close($con);
            header("Location: mod_auto.php?id=" . $id_auto . "&error=delete_failed");
            exit();
        }
    } else {
        $stmt->close();
        mysqli_close($con);
        header("Location: ../index.php?error=auto_no_encontrado");
        exit();
    }
} else {
    mysqli_close($con);
    header("Location: ../index.php");
    exit();
}
