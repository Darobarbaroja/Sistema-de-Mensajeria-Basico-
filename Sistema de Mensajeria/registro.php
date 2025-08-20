<?php
header("Cache-Control: no-store");
session_start();
require_once "ClasesFinal.php";

$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = trim($_POST['nombre']);
    $apellido = trim($_POST['apellido']);
    $usuario = trim($_POST['usuario']);
    $pass = trim($_POST['pass']);

    if (!empty($nombre) && !empty($apellido) && !empty($usuario) && !empty($pass)) {
        $nuevo = new Usuarios($nombre, $apellido, $usuario, $pass, 2); // Siempre como usuario común
        $resultado = $nuevo->alta();

        if ($resultado === true) {
            header("Location: index.php?registro=ok");
            exit();
        } else {
            $mensaje = "❌ No se pudo registrar el usuario.";
        }
    } else {
        $mensaje = "❗ Todos los campos son obligatorios.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Usuario</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>

<div class="container">
    <h2>Registro de nuevo usuario</h2>

    <?php if (!empty($mensaje)) echo "<p class='error'>$mensaje</p>"; ?>

    <form method="post" action="">
        <label>Nombre:</label><br>
        <input type="text" name="nombre" required><br><br>

        <label>Apellido:</label><br>
        <input type="text" name="apellido" required><br><br>

        <label>Nombre de usuario:</label><br>
        <input type="text" name="usuario" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="pass" required><br><br>

        <input type="submit" value="Registrarse">
        <a href="index.php"><button type="button">Cancelar</button></a>
    </form>
</div>

</body>
</html>
