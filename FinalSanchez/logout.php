<?php
session_start();
include("conexion.php");

if (isset($_SESSION['nombre_usuario']) && isset($_SESSION['inicio'])) {
    $minutos = floor((time() - $_SESSION['inicio']) / 60);
    $usuario = $_SESSION['nombre_usuario'];

    $con = conectar();

    // Actualiza minutos en el último acceso registrado
    $stmt = $con->prepare("UPDATE accesos SET minutos = ? 
                           WHERE nombre_usuario = ? 
                           ORDER BY id DESC 
                           LIMIT 1");
    $stmt->bind_param("is", $minutos, $usuario);
    $stmt->execute();
}

// Cerrar sesión
session_unset();
session_destroy();

// Saludo de despedida (puede ser redirigido también)
echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <title>Hasta pronto</title>
    <link rel='stylesheet' href='stylesheet.css'>
</head>
<body>
    <div class='container'>
        <h2>Gracias por usar el sistema</h2>
        <p>Se ha cerrado la sesión correctamente.</p>
        <a href='index.php'><button>Volver al inicio</button></a>
    </div>
</body>
</html>";
?>
