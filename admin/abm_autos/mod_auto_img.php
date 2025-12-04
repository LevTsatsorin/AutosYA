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

$id_auto = isset($_POST['id_auto']) ? intval($_POST['id_auto']) : 0;

if ($id_auto <= 0) {
    header("Location: ../index.php?error=id_invalido");
    exit();
}

// Verificar que se subió archivo
if (!isset($_FILES['nueva_imagen']) || $_FILES['nueva_imagen']['error'] !== UPLOAD_ERR_OK) {
    header("Location: mod_auto.php?id=$id_auto&error=archivo_invalido");
    exit();
}

// Obtener imagen actual del auto
$stmt = $con->prepare("SELECT imagen FROM autos WHERE id_auto = ?");
$stmt->bind_param("i", $id_auto);
$stmt->execute();
$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../index.php?error=auto_no_encontrado");
    exit();
}

$auto = $resultado->fetch_assoc();
$imagen_antigua = $auto['imagen'];
$stmt->close();

// Eliminar imagen antigua
if (!empty($imagen_antigua)) {
    $ruta_antigua = "../../auto_imgs/$imagen_antigua";
    if (file_exists($ruta_antigua)) {
        unlink($ruta_antigua);
    }
}

$nombre_original = $_FILES['nueva_imagen']['name'];
$extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

// Validar extensiones
$extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
if (!in_array($extension, $extensiones_permitidas)) {
    header("Location: mod_auto.php?id=$id_auto&error=formato_no_permitido");
    exit();
}

$imagen_nueva = time() . "." . $extension;
$ruta_destino = "../../auto_imgs/$imagen_nueva";

// Verificar que la carpeta existe
$directorio = "../../auto_imgs";
if (!is_dir($directorio)) {
    mkdir($directorio, 0777, true);
}

if (!move_uploaded_file($_FILES['nueva_imagen']['tmp_name'], $ruta_destino)) {
    header("Location: mod_auto.php?id=$id_auto&error=archivo_no_guardado");
    exit();
}

$stmt = $con->prepare("UPDATE autos SET imagen = ? WHERE id_auto = ?");
$stmt->bind_param("si", $imagen_nueva, $id_auto);

if ($stmt->execute()) {
    $stmt->close();
    mysqli_close($con);
    header("Location: mod_auto.php?id=$id_auto&img=ok");
    exit();
} else {
    $stmt->close();
    mysqli_close($con);
    header("Location: mod_auto.php?id=$id_auto&error=actualizacion_fallida");
    exit();
}
