<?php
date_default_timezone_set("America/Argentina/Buenos_Aires");
require_once "conexion.php";

interface abm {
    public function alta();
    public static function eliminar($id);
    public static function listar();
    public function modificar($id);
}

class Usuarios implements abm {
    private $usuario, $pass, $nombre, $apellido, $rol;

    public function __construct($nom, $ape, $usu, $contra, $rol) {
        $this->nombre = $nom;
        $this->apellido = $ape;
        $this->usuario = $usu;
        $this->pass = $contra;
        $this->rol = $rol;
    }

    public static function iniciarSesion($usu, $contra) {
        try {
            $c = conectar();
            $usu = $c->real_escape_string($usu);

            $sql = "SELECT * FROM usuarios WHERE usuario = '$usu'";
            $resulset = $c->query($sql);

            if ($resulset && $resulset->num_rows > 0) {
                $user = $resulset->fetch_assoc();

                if ($user['pass'] === $contra) { // ⚠️ En producción usar password_verify
                    session_start();
                    $_SESSION['idUsuario'] = $user['idUsuario'];
                    $_SESSION['nombre'] = $user['nombre'];
                    $_SESSION['usuario'] = $user['usuario'];
                    $_SESSION['rol'] = $user['rol'];
                    $_SESSION['tyf_actual'] = localtime(time(), true);

                    header("Location:" . ($_SESSION['rol'] == 1 ? "admin.php" : "usuario.php"));
                    exit();
                } else {
                    echo "Contraseña incorrecta.";
                }
            } else {
                echo "Usuario no encontrado.";
            }
            $c->close();
        } catch (Throwable $e) {
            echo "Error: " . $e->getMessage();
        }
    }

    public function alta() {
        try {
            $c = conectar();
            $usuarioEsc = $c->real_escape_string($this->usuario);
            $nombreEsc = $c->real_escape_string($this->nombre);
            $apellidoEsc = $c->real_escape_string($this->apellido);
            $passEsc = $c->real_escape_string($this->pass);

            $sql = "SELECT usuario FROM usuarios WHERE usuario = '$usuarioEsc'";
            $resulset = $c->query($sql);

            if ($resulset && $resulset->num_rows > 0) {
                echo "Ese nombre de usuario ya existe.";
            } else {
                $sql = "INSERT INTO usuarios (nombre, apellido, usuario, pass, rol) 
                        VALUES ('$nombreEsc', '$apellidoEsc', '$usuarioEsc', '$passEsc', $this->rol)";
                $c->query($sql);

                echo $c->affected_rows > 0 ? "Usuario registrado con éxito." : "No se pudo registrar el usuario.";
            }
            $c->close();
        } catch (Throwable $e) {
            echo "Error en alta: " . $e->getMessage();
        }
    }

    public static function eliminar($id) {
        try {
            $c = conectar();
            $id = (int)$id;
            $sql = "DELETE FROM usuarios WHERE idUsuario = $id";
            $c->query($sql);

            echo $c->affected_rows > 0 ? "Usuario eliminado con éxito." : "No se pudo eliminar el usuario.";
            $c->close();
        } catch (Throwable $e) {
            echo "Error al eliminar: " . $e->getMessage();
        }
    }

    public static function listar() {
        try {
            $c = conectar();
            $sql = "SELECT * FROM usuarios";
            $resulset = $c->query($sql);
            $usuarios = [];

            if ($resulset && $resulset->num_rows > 0) {
                while ($fila = $resulset->fetch_assoc()) {
                    $usuarios[] = $fila;
                }
            }
            $c->close();
            return $usuarios;
        } catch (Throwable $e) {
            echo "Error en listar: " . $e->getMessage();
            return [];
        }
    }

    public function modificar($id) {
        try {
            $c = conectar();
            $id = (int)$id;
            $nombreEsc = $c->real_escape_string($this->nombre);
            $apellidoEsc = $c->real_escape_string($this->apellido);
            $usuarioEsc = $c->real_escape_string($this->usuario);
            $passEsc = $c->real_escape_string($this->pass);

            $sql = "UPDATE usuarios 
                    SET nombre='$nombreEsc', apellido='$apellidoEsc', usuario='$usuarioEsc', pass='$passEsc', rol=$this->rol 
                    WHERE idUsuario=$id";
            $c->query($sql);

            echo $c->affected_rows > 0 ? "Usuario modificado con éxito." : "No se pudo modificar el usuario.";
            $c->close();
        } catch (Throwable $e) {
            echo "Error al modificar: " . $e->getMessage();
        }
    }
}

class Mensajes implements abm {
    private $de, $para, $asunto, $mensaje, $estado;

    public function __construct($de, $para, $asunto, $mensaje, $estado = 0) {
        $this->de = $de;
        $this->para = $para;
        $this->asunto = $asunto;
        $this->mensaje = $mensaje;
        $this->estado = $estado;
    }

    public function alta() {
        try {
            $c = conectar();
            $destinoEsc = $c->real_escape_string($this->para);
            $sql = "SELECT idUsuario FROM usuarios WHERE usuario = '$destinoEsc' OR idUsuario = '$destinoEsc'";
            $resulset = $c->query($sql);

            if ($resulset && $resulset->num_rows > 0) {
                $destino = $resulset->fetch_assoc();
                $this->para = $destino['idUsuario'];

                $asuntoEsc = $c->real_escape_string($this->asunto);
                $mensajeEsc = $c->real_escape_string($this->mensaje);

                $sql = "INSERT INTO mensajes (de, para, asunto, mensaje, fecha, estado) 
                        VALUES ($this->de, $this->para, '$asuntoEsc', '$mensajeEsc', NOW(), $this->estado)";
                $c->query($sql);

                echo $c->affected_rows > 0 ? "Mensaje enviado." : "No se pudo enviar.";
            } else {
                echo "Destinatario no encontrado.";
            }
            $c->close();
        } catch (Throwable $e) {
            echo "Error al enviar mensaje: " . $e->getMessage();
        }
    }

    public static function eliminar($id) {
        try {
            $c = conectar();
            $id = (int)$id;
            $sql = "UPDATE mensajes SET estado = 2 WHERE idMensaje = $id";
            $c->query($sql);

            echo $c->affected_rows > 0 ? "Mensaje eliminado." : "No se pudo eliminar.";
            $c->close();
        } catch (Throwable $e) {
            echo "Error al eliminar mensaje: " . $e->getMessage();
        }
    }

    public static function listar() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $idUsuario = $_SESSION['idUsuario'] ?? null;

        if (!$idUsuario) {
            echo "Error: No se encontró el usuario en la sesión.";
            return ['recibidos' => [], 'enviados' => []];
        }

        $c = conectar();

        // Mensajes recibidos
        $sqlRecibidos = "
            SELECT m.*, u.usuario AS de 
            FROM mensajes m 
            JOIN usuarios u ON m.de = u.idUsuario 
            WHERE m.para = $idUsuario AND m.estado != 2 
            ORDER BY m.fecha DESC";
        $resRecibidos = $c->query($sqlRecibidos);
        $recibidos = [];

        if ($resRecibidos) {
            while ($fila = $resRecibidos->fetch_assoc()) {
                $recibidos[] = $fila;
            }
        }

        // Mensajes enviados
        $sqlEnviados = "
            SELECT m.*, u.usuario AS para 
            FROM mensajes m 
            JOIN usuarios u ON m.para = u.idUsuario 
            WHERE m.de = $idUsuario AND m.estado != 2 
            ORDER BY m.fecha DESC";
        $resEnviados = $c->query($sqlEnviados);
        $enviados = [];

        if ($resEnviados) {
            while ($fila = $resEnviados->fetch_assoc()) {
                $enviados[] = $fila;
            }
        }

        $c->close();
        return ['recibidos' => $recibidos, 'enviados' => $enviados];
    }

    public static function buscar($id) {
        try {
            $c = conectar();
            $id = (int)$id;
            $sql = "SELECT * FROM mensajes WHERE idMensaje = $id";
            $resulset = $c->query($sql);

            return ($resulset && $resulset->num_rows > 0) ? $resulset->fetch_assoc() : false;
        } catch (Throwable $e) {
            echo "Error en buscar mensaje: " . $e->getMessage();
            return false;
        }
    }

    public static function marcarLeido($id) {
        try {
            $c = conectar();
            $id = (int)$id;
            $sql = "UPDATE mensajes SET estado = 1 WHERE idMensaje = $id AND estado = 0";
            $c->query($sql);
            $c->close();
        } catch (Throwable $e) {
            echo "Error al marcar mensaje como leído: " . $e->getMessage();
        }
    }

    public function modificar($id) {
        try {
            $c = conectar();
            $id = (int)$id;
            $sql = "UPDATE mensajes SET estado = 1 WHERE idMensaje = $id AND estado = 0";
            $c->query($sql);
            $c->close();
        } catch (Throwable $e) {
            echo "Error al modificar mensaje: " . $e->getMessage();
        }
    }
}
?>
