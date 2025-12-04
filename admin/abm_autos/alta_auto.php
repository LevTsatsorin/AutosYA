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

$marca = htmlspecialchars(trim($_POST['marca']), ENT_QUOTES, 'UTF-8');
$modelo = htmlspecialchars(trim($_POST['modelo']), ENT_QUOTES, 'UTF-8');
$anio = intval($_POST['anio']);
$patente = htmlspecialchars(trim($_POST['patente']), ENT_QUOTES, 'UTF-8');
$precio_por_dia = floatval($_POST['precio_por_dia']);

// Procesar imagen
$imagen = NULL;
if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    // Obtener extensión original
    $nombre_original = $_FILES['imagen']['name'];
    $extension = strtolower(pathinfo($nombre_original, PATHINFO_EXTENSION));

    // Validar extensiones
    $extensiones_permitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp', 'avif'];
    if (!in_array($extension, $extensiones_permitidas)) {
        header("Location: ../gestion_autos.php?error=formato_no_permitido");
        exit();
    }

    $imagen = time() . "." . $extension;
    $ruta_destino = "../../auto_imgs/$imagen";

    // Verificar la carpeta
    $directorio = "../../auto_imgs";
    if (!is_dir($directorio)) {
        mkdir($directorio, 0777, true);
    }

    // Mover archivo
    if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $ruta_destino)) {
        $imagen = NULL;
    }
}

// Validar campos vacíos
if (empty($marca) || empty($modelo) || empty($patente) || $anio <= 0 || $precio_por_dia <= 0) {
    header("Location: ../gestion_autos.php?error=campos_invalidos");
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
    header("Location: ../gestion_autos.php?error=patente_existente");
    exit();
}
$stmt->close();

// Insertar nuevo auto
$stmt = $con->prepare("INSERT INTO autos (marca, modelo, anio, patente, precio_por_dia, estado, imagen) 
                       VALUES (?, ?, ?, ?, ?, 'disponible', ?)");
$stmt->bind_param("ssisss", $marca, $modelo, $anio, $patente, $precio_por_dia, $imagen);

if ($stmt->execute()) {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../gestion_autos.php?alta=ok");
    exit();
} else {
    $stmt->close();
    mysqli_close($con);
    header("Location: ../gestion_autos.php?error=alta_fallida");
    exit();
}
