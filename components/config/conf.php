<?php

// Establecer zona horaria
date_default_timezone_set('America/Argentina/Buenos_Aires');

// Configuración de conexión
$host = "localhost";
$usuario = "root";
$clave = "";
$base_datos = "alquiler_autos";

$con = mysqli_connect($host, $usuario, $clave, $base_datos);

// Verificar conexión
if (!$con) {
    die("Error de conexión: " . mysqli_connect_error());
}

mysqli_set_charset($con, "utf8");
