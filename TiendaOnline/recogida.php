<?php
require_once("funciones/p1_lib.php");


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST["nombre"];
    $contrasena = $_POST["contrasena"];
    $email = $_POST["nombre"];

    // Conexión a la base de datos
    $servername = "localhost";
    $username = "gestorDB";
    $password = "1234";
    $dbname = "db_tienda_segunda_mano";

    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conn->connect_error) {
        die("Error de conexión: " . $conn->connect_error);
    }

    // Consulta SQL para autenticar al usuario
    $sql = "SELECT idUsuario, username, pass FROM usuarios WHERE (username=? OR email=?) AND estado=1";
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
            $_SESSION["idUsuario"] = $row["idUsuario"];
            $_SESSION["username"] = $row["username"];
            $_SESSION["perfil"] = $row["perfil"];

            // Redirigir al usuario a producto.php
            header("Location: home.php");
            exit;
        } else {
            // Contraseña incorrecta
            $error = 'AUTH_USERNAME_FAIL';
            header("Location: index.php?error=".$error);
            exit;
        }
    } else {
        // Usuario no encontrado
        $error = 'USER_NOT_FOUND';
        header("Location: index.php?error=".$error);
        exit;
    }
}
?>
