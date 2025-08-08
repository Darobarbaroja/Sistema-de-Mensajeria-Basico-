<?php
session_start();
include("conexion.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = trim($_POST['usuario']);
    $clave = trim($_POST['clave']);

    $con = conectar();
    $stmt = $con->prepare("SELECT * FROM usuarios WHERE usuario = ?");
    $stmt->bind_param("s", $usuario);
    $stmt->execute();
    $res = $stmt->get_result();

    if ($res->num_rows === 1) {
        $row = $res->fetch_assoc();

        if ($clave === $row['pass']) { // ⚠️ En producción usar password_verify
            $_SESSION['id_usuario'] = $row['idUsuario'];
            $_SESSION['usuario'] = $row['usuario'];
            $_SESSION['rol'] = $row['rol'];
            $_SESSION['inicio'] = time();

            // Registrar acceso en accesos.txt y tabla accesos
            $fecha = date("Y-m-d H:i:s");
            file_put_contents("accesos.txt", "{$row['usuario']} - {$fecha} - INICIO\n", FILE_APPEND);

            $insert = $con->prepare("INSERT INTO accesos(nombre_usuario, fecha_hora, minutos) VALUES (?, ?, 0)");
            $insert->bind_param("ss", $usuario, $fecha);
            $insert->execute();

            // Redirección según rol
        if ($row['rol'] == 1) {
        header("Location: admin.php");
            } else if ($row['rol'] == 2) {
            header("Location: usuario.php");
        }
        exit();
        } else {
            $error = "Contraseña incorrecta.";
        }
    } else {
        $error = "Usuario no encontrado.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Login - Sistema de Mensajería</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>

<nav class="navbar">
  <div class="nav-center">
    <h2>Bienvenido al sistema de mensajería</h2>
  </div>
</nav>

<div class="container">
    <h3>Iniciar sesión</h3>

    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="post" action="">
        <label>Nombre de usuario:</label><br>
        <input type="text" name="usuario" placeholder="Ingrese usuario" required><br><br>

        <label>Contraseña:</label><br>
        <input type="password" name="clave" placeholder="Ingrese contraseña" required><br><br>

        <input type="submit" value="Ingresar">
        <a href="registro.php"><button type="button">Registrarse</button></a>
    </form>

    <p style="margin-top: 10px;"><a href="#">¿Olvidaste tu contraseña?</a></p>
</div>

</body>
</html>
