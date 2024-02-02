<?php
require_once 'funciones/config.php';
require_once 'funciones/p1_lib.php';
require_once 'entities/ent_producto.php';
require_once 'entities/ent_usuario.php';
require_once 'entities/ent_imagenes.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $contrasena = $_POST["contrasena"];
    $email = $_POST["nombre"];

    $conn = get_connection();

    // Consulta SQL para autenticar al usuario
    $sql = "SELECT idUsuario, username, pass, perfil FROM usuarios WHERE (username=? OR email=?) AND estado=1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $nombre, $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify("$contrasena", $row["pass"])) {
            // Contraseña válida, iniciar sesión
            session_start();
            $_SESSION['id'] = session_id();
            $_SESSION['idUsuario'] = $row["idUsuario"];
            $_SESSION['username'] = $row["username"];
            $_SESSION['perfil'] = $row["perfil"];

            //Crear objeto usuario
            $usuario = new Usuario(
                $row["idUsuario"],
                $row["username"],
                $row["email"],
                $row["pass"],
                $row["nombre"],
                $row["apellido1"],
                $row["apellido2"],
                $row["direccion"],
                $row["fechaNac"],
                $row["fechaCreacion"],
                $row["fechaModificacion"],
                $row["estado"],
                $row["perfil"],
                $row["avatar"]
            );
            //Asignar el objeto usuario a la sesion
            $_SESSION['usuario'] = $usuario;
            header("Location: home.php");
            exit;
        } else {
            // Contraseña incorrecta
            $error = 'AUTH_USERNAME_FAIL';
            header("Location: index.php?error=".$error);
            exit;
        }
    } else {
        // Usuario no encontrado o bloqueado
        $error = 'USER_NOT_FOUND';
        header("Location: index.php?error=".$error);
        exit;
    }
}
?>
