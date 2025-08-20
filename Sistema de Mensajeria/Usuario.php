
<?php
session_start();
date_default_timezone_set("America/Argentina/Buenos_Aires");

// Solo usuarios comunes (rol 2)
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 2) {
    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Panel del Usuario</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>
<body>

<nav class="navbar">
    <div class="nav-left">
        <?php echo "Bienvenido, " . htmlspecialchars($_SESSION['usuario']); ?>
    </div>
    <div class="nav-right">
        <a href="LogOut.php"><button>Cerrar sesión</button></a>
    </div>
</nav>

<div class="container">
    <h2>Panel del Usuario</h2>
    <p>Desde aquí podés acceder a tus mensajes:</p>

    <a href="mensajes.php"><button>Ver mensajes</button></a>
    <a href="mensajes.php?crear"><button>Redactar mensaje</button></a>

</div>

</body>
</html>
