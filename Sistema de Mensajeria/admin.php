<?php
header("Cache-Control: no-store");

session_start();
date_default_timezone_set("America/Argentina/Buenos_Aires");

require_once "ClasesFinal.php";
//verificamos la sesion con el usuario y rol
if (!isset($_SESSION['usuario']) || $_SESSION['rol'] != 1) {
    header("Location:index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin</title>
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
    <div class="header">
        <div>
            <?php echo "¡Hola " . $_SESSION['usuario'] . "  Bienvenido !"; 
           // var_dump($_COOKIE) ?>
            <a href='LogOut.php'> <button>Cerrar sesión</button></a>
        </div>
    </div>

    <div class="container">
        <h1>Gestión de Usuarios</h1>
        <div>
            <a href="?alta"><button>Alta</button></a>
            <a href="?lista"><button>Lista</button></a>
            <a href="?busqueda"><button>Búsqueda</button></a>
        </div>
    </div>

    <div class="mensajeria">
        <a href='mensajes.php'><button>Bandeja de mensajes</button></a>
    </div>

    <?php
    if (isset($_GET['alta'])) {
    ?>
    <form method="post">
        <input type="hidden" name="altausu">
        <label for="alta">Alta de usuarios </label>
        <input type="text" name="nombre" placeholder="Ingrese nombre" maxlength=30 required>
        <input type="text" name="apellido" placeholder="Ingrese apellido" maxlength=30 required>
        <input type="text" name="usuario" placeholder="Ingrese usuario" required>
        <input type="password" name="pass" placeholder="Ingrese contraseña" maxlength=30 required>
        <select name="rol" id="rol">
            <option value="1">1</option>
            <option value="2">2</option>
        </select>
        <input type="submit" value="Aceptar">
    </form>
    <?php
    }
//creamos el objeto usuario
    if (isset($_POST['altausu'])) {
        $rol = ($_POST['rol'] == "1") ? 1 : 2;
        $usu_nuevo = new Usuarios($_POST['nombre'], $_POST['apellido'], $_POST['usuario'], $_POST['pass'], $rol);
        $usu_nuevo->alta();
    }

    if (isset($_GET['busqueda'])) {
    ?>
    <form method="post">
        <input type="hidden" name="looking">
        <label for="alta">Búsqueda de usuarios</label>
        <select name="op-busqueda" id="op-busqueda">
            <option value="nombre_completo">Nombre completo</option>
            <option value="nombre_usuario">Nombre de usuario</option>
        </select>
        <input type="text" name="buscado" placeholder="Ingrese nombre buscado" maxlength=30 required>
        <input type="submit" value="Buscar">
    </form>
    <?php
        if (isset($_POST['looking'])) {
            $usuarios_encontrados = Usuarios::buscar($_POST['op-busqueda'], $_POST['buscado']);

            if (is_array($usuarios_encontrados) && count($usuarios_encontrados) > 0) {
    ?>
    <table>
        <form method="post">
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Usuario</th>
                <th>Contraseña</th>
                <th>Rol</th>
                <th></th>
            </tr>
            <?php
                        foreach ($usuarios_encontrados as $usuario) {
                            if (!is_null($usuario) && is_array($usuario)) {
                        ?>
            <tr>
                <td><?php echo htmlspecialchars($usuario['idUsuario']) ?> </td>
                <td><?= htmlspecialchars($usuario['nombre']) ?> </td>
                <td><?= htmlspecialchars($usuario['apellido']) ?> </td>
                <td><?= htmlspecialchars($usuario['usuario']) ?> </td>
                <td><?= htmlspecialchars($usuario['pass']) ?> </td>
                <td><?= htmlspecialchars($usuario['rol']) ?> </td>
                <td><input type="radio" name="id" value="<?= htmlspecialchars($usuario['idUsuario']); ?>" required></td>
            </tr>
            <?php
                            } else {
                                echo "<tr><td colspan='7'>Usuario no definido</td></tr>";
                            }
                        }
                        ?>
            <tr>
                <th colspan="7">
                    <input type="submit" value="Modificar" name="modificar">
                    <input type="submit" value="Eliminar" name="eliminar">
                </th>
            </tr>
        </form>
    </table>
    <?php
            } else {
                echo "No hay usuarios encontrados.";
            }
        }
    }

    if (isset($_POST['modificar'])) {
        if (isset($_POST['id'])) {
            $usu_encontrado=Usuarios::obtenerUser($_POST['id']);
           // var_dump($usu_encontrado);


    ?>
    <form method="post">
        <input type="hidden" name="modifi" value="1">
        <input type="hidden" name="id_usuario" value="<?= htmlspecialchars($id_usuario) ?>">
        <table>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Apellido</th>
                <th>Usuario</th>
                <th>Contraseña</th>
                <th>Rol</th>
                <th></th>
            </tr>
            <tr>
                <td><?= htmlspecialchars($id_usuario) ?> </td>
                <td><input type="text" name="nom" placeholder="Nuevo nombre" value="<?= $usu_encontrado['nombre'] ?>"
                        maxlength=30 required></td>
                <td><input type="text" name="ape" placeholder="Nuevo apellido"
                        value="<?= $usu_encontrado['apellido'] ?>" maxlength=30 required></td>
                <td><input type="text" name="usu" placeholder="Nuevo usuario" value="<?= $usu_encontrado[''] ?>"
                        maxlength=30 required></td>
                <td><input type="password" name="contra" placeholder="Nueva contraseña" maxlength=30 required></td>
                <td><select name="rol" id="rol" required>
                        <option value=1>1</option>
                        <option value=2>2</option>
                    </select>
                </td>
                <td><input type="submit" value="Enviar"></td>
            </tr>
        </table>
    </form>
    <?php
        } else {
            echo "No se ha seleccionado ningún usuario para modificar.";
        }
    }

    if (isset($_POST['modifi'])) {
        $id = $_POST['id_usuario'];
        $usu_modificado = new Usuarios($_POST['nom'], $_POST['ape'], $_POST['usu'], $_POST['contra'], $_POST['rol']);
        $usu_modificado->modificar($id);
    }

    if (isset($_POST['eliminar'])) {
        if (isset($_POST['id'])) {
            $id = $_POST['id'];
            echo "ADVERTENCIA: <br>¿Está seguro que desea eliminar al usuario: $id ?";
    ?>
    <form method="post">
        <input type="hidden" name="codEliminar" value="<?= htmlspecialchars($id) ?>">
        <input type="submit" value="Sí" name="confirmado">
        <input type="submit" value="No" name="cancelar">
    </form>
    <?php
        } else {
            echo "No se ha seleccionado ningún usuario para eliminar.";
        }
    }

    if (isset($_POST['codEliminar']) && isset($_POST['confirmado']) && $_POST['confirmado'] == 'Sí') {
        Usuarios::eliminar($_POST['codEliminar']);
        echo "Usuario eliminado con éxito.";
    }

    if (isset($_GET['lista'])) {
        $lista_usuarios = Usuarios::listar();
        if ($lista_usuarios) {
    ?>
    <table>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Apellido</th>
            <th>Usuario</th>
            <th>Contraseña</th>
            <th>Rol</th>
        </tr>
        <?php
                foreach ($lista_usuarios as $usuario) {
                ?>
        <tr>
            <td><?= $usuario['idUsuario'] ?> </td>
            <td><?= $usuario['nombre'] ?> </td>
            <td><?= $usuario['apellido'] ?> </td>
            <td><?= $usuario['usuario'] ?> </td>
            <td><?= $usuario['pass'] ?> </td>
            <td><?= $usuario['rol'] ?> </td>
        </tr>
        <?php
                }
                ?>
    </table>
    <?php
        } else {
            echo "No hay usuarios";
        }
    }
    ?>
    </div>
</body>

</html>