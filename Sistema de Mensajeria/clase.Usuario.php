<?php 
class Usuario {
    public $id;
    public $usuario;
    public $clave;
    public $nombre_completo;
    public $rol;

    public function __construct($id, $usuario, $clave, $nombre_completo, $rol) {
        $this->id = $id;
        $this->usuario = $usuario;
        $this->clave = $clave;
        $this->nombre_completo = $nombre_completo;
        $this->rol = $rol;
    }

    public static function obtenerPorUsuario($usuario_input) {
        require_once("conexion.php");
        $con = conectar();

        $stmt = $con->prepare("SELECT idUsuario, usuario, pass, nombre, apellido, rol FROM usuarios WHERE usuario = ?");
        if (!$stmt) {
            die("Error al preparar la consulta: " . $con->error);
        }

        $stmt->bind_param("s", $usuario_input);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res && $res->num_rows === 1) {
            $row = $res->fetch_assoc();
            $nombre_completo = $row['nombre'] . ' ' . $row['apellido'];

            return new Usuario(
                $row['idUsuario'],
                $row['usuario'],
                $row['pass'],
                $nombre_completo,
                $row['rol']
            );
        }

        return null;
    }
}
?>
