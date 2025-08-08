<?php 
header("Cache-Control: no-store");
session_start();
date_default_timezone_set("America/Argentina/Buenos_Aires");

require "ClasesFinal.php";

if (!isset($_SESSION['usuario'])) {
    header("Location:index.php");
    exit();
}

$miId = $_SESSION['id_usuario'];
$miUsuario = $_SESSION['usuario'];
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Bandeja de mensajes</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
    <h1>Bandeja de Mensajes de <?= htmlspecialchars($miUsuario) ?></h1>

    <div>
        <a href="?bandejallegada"><button>Mensajes Recibidos</button></a>
        <a href="?bandejasalida"><button>Mensajes Enviados</button></a>
        <a href="?crear"><button>Nuevo Mensaje</button></a>
        <a href="?volver"><button>Volver al inicio</button></a>
    </div>

    <div class="container">
<?php
// NUEVO MENSAJE ==========
if (isset($_GET['crear'])) {
?>
    <h3>Enviar nuevo mensaje</h3>
    <form method="post">
        <input type="hidden" name="nuevo_mensaje">
        Para (usuario): <input type="text" name="destinatario" required><br>
        Asunto: <input type="text" name="asunto" maxlength="100" required><br>
        Mensaje:<br><textarea name="mensaje" rows="5" cols="50" required></textarea><br>
        <input type="submit" value="Enviar mensaje">
    </form>
<?php
}

if (isset($_POST['nuevo_mensaje'])) {
    $destino = $_POST['destinatario'];
    if ($destino === $miUsuario) {
        echo "⚠️ No puedes enviarte un mensaje a vos mismo.";
    } else {
        $mensaje = new Mensajes($miId, $destino, $_POST['asunto'], $_POST['mensaje']);
        $mensaje->alta();
    }
}

// BANDEJA DE ENTRADA ==========
if (isset($_GET['bandejallegada'])) {
    $bandeja = Mensajes::listar();
    $recibidos = $bandeja['recibidos'];

    if ($recibidos):
?>
    <h3>Mensajes recibidos</h3>
    <form method="post">
        <table border="1">
            <tr>
                <th>De</th><th>Asunto</th><th>Fecha</th><th>Mensaje</th><th>Seleccionar</th>
            </tr>
<?php
        foreach ($recibidos as $mensaje) {
            if ($mensaje['estado'] == 2) continue; // Mensaje eliminado, no mostrar

            $clase = ($mensaje['estado'] == 1) ? 'estado-leido' : 'estado-noleido';
?>
            <tr class="<?= $clase ?>">
                <td><?= htmlspecialchars($mensaje['de']) ?></td>
                <td><?= htmlspecialchars($mensaje['asunto']) ?></td>
                <td><?= htmlspecialchars($mensaje['fecha']) ?></td>
                <td><?= htmlspecialchars($mensaje['mensaje']) ?></td>
                <td><input type="radio" name="id" value="<?= $mensaje['idMensaje'] ?>" required></td>
            </tr>
<?php } ?>
            <tr>
                <td colspan="5">
                    <input type="submit" name="abrir" value="Marcar como leído">
                    <input type="submit" name="responder" value="Responder">
                </td>
            </tr>
        </table>
    </form>
<?php
    else:
        echo "<p>No hay mensajes recibidos.</p>";
    endif;
}

// ABRIR MENSAJE ==========
if (isset($_POST['abrir']) && isset($_POST['id'])) {
    Mensajes::marcarLeido($_POST['id']);
    echo "<p>✅ Mensaje marcado como leído.</p>";
}

// RESPONDER MENSAJE ==========
if (isset($_POST['responder']) && isset($_POST['id'])) {
    $m = Mensajes::buscar($_POST['id']);
?>
    <h3>Responder mensaje</h3>
    <form method="post">
        <input type="hidden" name="respuesta_mensaje" value="1">
        <input type="hidden" name="para" value="<?= htmlspecialchars($m['de']) ?>">
        <input type="hidden" name="asunto" value="<?= htmlspecialchars($m['asunto']) ?>">
        Mensaje:<br><textarea name="respuesta" rows="5" cols="50" required></textarea><br>
        <input type="submit" value="Enviar respuesta">
    </form>
<?php
}

if (isset($_POST['respuesta_mensaje'])) {
    $m = new Mensajes($miId, $_POST['para'], "RE: " . $_POST['asunto'], $_POST['respuesta']);
    $m->alta();
}

// BANDEJA DE SALIDA ==========
if (isset($_GET['bandejasalida'])) {
    $bandeja = Mensajes::listar();
    $enviados = $bandeja['enviados'];

    if ($enviados):
?>
    <h3>Mensajes enviados</h3>
    <form method="post">
        <table border="1">
            <tr>
                <th>Para</th><th>Asunto</th><th>Fecha</th><th>Mensaje</th><th>Seleccionar</th>
            </tr>
<?php
        foreach ($enviados as $m):
?>
            <tr>
                <td><?= htmlspecialchars($m['para']) ?></td>
                <td><?= htmlspecialchars($m['asunto']) ?></td>
                <td><?= htmlspecialchars($m['fecha']) ?></td>
                <td><?= htmlspecialchars($m['mensaje']) ?></td>
                <td><input type="radio" name="id" value="<?= $m['idMensaje'] ?>" required></td>
            </tr>
<?php endforeach; ?>
            <tr>
                <td colspan="5">
                    <input type="submit" name="eliminar" value="Eliminar">
                    <input type="submit" name="reenviar" value="Reenviar">
                </td>
            </tr>
        </table>
    </form>
<?php
    else:
        echo "<p>No hay mensajes enviados.</p>";
    endif;
}

//  ELIMINAR MENSAJE ==========
if (isset($_POST['eliminar']) && isset($_POST['id'])) {
    Mensajes::eliminar($_POST['id']);
    echo "<p>✅ Mensaje eliminado.</p>";
}

// REENVIAR MENSAJE ==========
if (isset($_POST['reenviar']) && isset($_POST['id'])) {
    $m = Mensajes::buscar($_POST['id']);
?>
    <h3>Reenviar mensaje</h3>
    <form method="post">
        <input type="hidden" name="reenviar_mensaje" value="1">
        <input type="hidden" name="asunto" value="<?= htmlspecialchars($m['asunto']) ?>">
        <input type="hidden" name="mensaje" value="<?= htmlspecialchars($m['mensaje']) ?>">
        Para (usuario): <input type="text" name="destinatario" required><br>
        <input type="submit" value="Reenviar">
    </form>
<?php
}

if (isset($_POST['reenviar_mensaje'])) {
    if ($_POST['destinatario'] === $miUsuario) {
        echo "⚠️ No puedes reenviarte un mensaje a vos mismo.";
    } else {
        $m = new Mensajes($miId, $_POST['destinatario'], "FWD: " . $_POST['asunto'], $_POST['mensaje']);
        $m->alta();
    }
}

// VOLVER ==========
if (isset($_GET['volver'])) {
    header("Location: " . ($_SESSION['rol'] == 1 ? "admin.php" : "usuario.php"));
    exit();
}
?>
    </div>
</body>
</html>
