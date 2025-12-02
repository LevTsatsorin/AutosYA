<?php
if (!isset($con)) {
    die("Conexión a la base de datos no disponible");
}

// Construir consulta con filtros
$query = "SELECT * FROM autos WHERE estado='disponible'";
$params = [];
$types = "";

// Filtrar por marca
if (isset($_GET['marca']) && !empty($_GET['marca'])) {
    $query .= " AND marca LIKE ?";
    $params[] = "%" . $_GET['marca'] . "%";
    $types .= "s";
}

// Filtrar por modelo
if (isset($_GET['modelo']) && !empty($_GET['modelo'])) {
    $query .= " AND modelo LIKE ?";
    $params[] = "%" . $_GET['modelo'] . "%";
    $types .= "s";
}

// Filtrar por año mínimo
if (isset($_GET['anio_min']) && !empty($_GET['anio_min'])) {
    $query .= " AND anio >= ?";
    $params[] = intval($_GET['anio_min']);
    $types .= "i";
}

// Filtrar por precio máximo por día
if (isset($_GET['precio_max']) && !empty($_GET['precio_max'])) {
    $query .= " AND precio_por_dia <= ?";
    $params[] = floatval($_GET['precio_max']);
    $types .= "d";
}

$query .= " ORDER BY id_auto DESC";

$stmt = $con->prepare($query);

if (!empty($params)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$resultado = $stmt->get_result();
$totalCars = $resultado->num_rows;
