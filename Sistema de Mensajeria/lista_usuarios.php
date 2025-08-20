<?php
session_start();
include("conexion.php");

$con = conectar();

$sql = "SELECT * FROM usuarios";
$resultado = $con->query($sql);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Listado de Usuarios</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>


<nav class="navbar">
     <div class="nav-center">
        Bienvenido al sistema de mensajería
    </div>
     <div class="nav-right">
      <a href="logout.php" class="btn-cerrar">Cerrar sesión</a>
  </div>
       
   
</nav>

<div class="container">
    <h1>Listado de Usuarios</h1>

    <table border="1" cellpadding="10" cellspacing="0" style="width:100%; text-align:left; border-collapse: collapse;">
        <thead style="background-color:#0078d7; color:white;">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Usuario</th>
                <th>Contraseña</th>
                <th>Rol</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $resultado->fetch_assoc()): ?>
                <tr>
                    <td><?= $row['idUsuario'] ?></td>
                    <td><?= $row['nombre'] ?></td>
                    <td><?= $row['apellido'] ?></td>
                    <td><?= $row['usuario'] ?></td>
                    <td><?= $row['pass'] ?></td>
                    <td><?= $row['rol'] == 1 ? 'Admin' : 'Usuario' ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>

</body>
</html>
