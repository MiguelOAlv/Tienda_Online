<?php
require_once("funciones/config.php");
require_once("funciones/p1_lib.php");

// Verifica que se haya enviado el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Recoge los datos del formulario
    $nombreUsuario = $_POST["nombreUsuario"];
    $email = $_POST["email"];
    $password = $_POST["password"];
    $confirmarPassword = $_POST["confirmarPassword"];
    $nombre = $_POST["nombre"];
    $apellido1 = $_POST["apellido1"];
    $apellido2 = $_POST["apellido2"];
    $direccion = $_POST["direccion"];
    $fechaNacimiento = $_POST["fechaNacimiento"];
    
    
    // Fecha de creación y modificación
    $fechaCreacion = $fechaModificacion = date("Y-m-d H:i:s");
    
    // Estado y perfil por defecto
    $estado = 1; // Activo
    $perfil = 0; // Usuario normal

    $avatar = $_FILES["avatar"];

    // Verifica que todos los campos estén llenos
    $camposObligatorios = ["nombreUsuario", "email", "password", "nombre", "apellido1", "apellido2", "direccion", "fechaNacimiento"];
    $camposFaltantes = [];

    foreach ($camposObligatorios as $campo) {
        if (empty($_POST[$campo])) {
            $camposFaltantes[] = $campo;
        }
    }

    // Verifica que el campo de carga de archivos (avatar) en $_FILES no esté vacío
    if (empty($_FILES["avatar"]["name"])) {
        $camposFaltantes[] = "avatar";
    }

    if (!empty($camposFaltantes)) {
        $error = 'NO_DATA';
        header("Location: registro.php?error=" . $error);
        exit;
    }

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

    // Verificar si el nombre de usuario o el correo ya están en uso
    $sqlVerificarUsuario = "SELECT idUsuario FROM usuarios WHERE username=?";
    $stmtUsuario = $conn->prepare($sqlVerificarUsuario);
    $stmtUsuario->bind_param("s", $nombreUsuario);
    $stmtUsuario->execute();
    $resultUsuario = $stmtUsuario->get_result();
    $stmtUsuario->close();

    if ($resultUsuario->num_rows > 0) {
        $error= 'USED_USER';
        header("Location: registro.php?error=" . $error);
        exit;
    }

    $sqlVerificarEmail = "SELECT idUsuario FROM usuarios WHERE email=?";
    $stmtEmail = $conn->prepare($sqlVerificarEmail);
    $stmtEmail->bind_param("s", $email);
    $stmtEmail->execute();
    $resultEmail = $stmtEmail->get_result();
    $stmtEmail->close();
    
    if ($resultEmail->num_rows > 0) {
        $error = 'USED_EMAIL';
        header("Location: registro.php?error=" . $error);
        exit;
    }
    
    // Verificar la edad (mayor de 18)
    $fechaNacimiento = new DateTime($_POST["fechaNacimiento"]);
    // Cambiar formato a string
    $fechaNacimientoString = $fechaNacimiento->format('Y-m-d');
    $hoy = new DateTime();

    // Calcular si es mayor de edad
    $edad = $hoy->diff($fechaNacimiento)->y;
    
    if ($edad < 18) {
        $error = 'AUTH_AGE_INVALID';
        header("Location: registro.php?error=" . $error);
        exit;
    }

    // Verificar el formato del email
    if (!preg_match('/@(gmail\.com|iesgalileo\.es)$/', $email)) {
        $error = 'EMAIL_INVALID';
        header("Location: registro.php?error=" . $error);
        exit;
    }

    // Verificar la contraseña (+8 caracteres, 1 mayuscula, 1 numero, confirmarPass)
    $password = $_POST["password"];
    $passwordLength = strlen($password);
    $passwordMayus = preg_match('/[A-Z]/', $password);
    $passwordNum = preg_match('/\d/', $password);

    if ($passwordLength < 8) {
        $error= 'AUTH_PASS_LENGTH';
        header("Location: registro.php?error=" . $error);
        exit;
    }

    if (!$passwordMayus) {
        $error= 'AUTH_PASS_MAYUS';
        header("Location: registro.php?error=" . $error);
        exit;
    }

    if (!$passwordNum) {
        $error= 'AUTH_PASS_NUM';
        header("Location: registro.php?error=" . $error);
        exit;
    }
    if ($password !== $confirmarPassword) {
        $error = 'PASSWORD_MISMATCH';
        header("Location: registro.php?error=" . $error);
        exit;
    }

    // Verificar que se haya seleccionado un archivo
    if ($avatar["error"] == UPLOAD_ERR_NO_FILE) {
        $error = 'NO_FILE';
        header("Location: registro.php?error=" . $error);
        exit;
    }

    // Verificar el tipo de archivo
    $permitidos = array("image/jpeg", "image/png");
    if (!in_array($avatar["type"], $permitidos)) {
        $error = 'FORMAT';
        header("Location: registro.php?error=" . $error);
        exit;
    }

    // Verificar el tamaño del archivo
    $tamanioMaximo = 10000000; // 10000 KB
    if ($avatar["size"] > $tamanioMaximo) {
        $error = 'SIZE';
        header("Location: registro.php?error=" . $error);
        exit;
    }

    // Generar un nombre único para el archivo
    $avatarNombre = uniqid() . '_' . $avatar["name"];
    $avatarDestino = "img/" . $avatarNombre;

    // Mover el archivo al directorio de destino
    if (!move_uploaded_file($avatar["tmp_name"], $avatarDestino)) {
        $error = 'UPLOAD_ERROR';
        header("Location: registro.php?error=" . $error);
        exit;
    }

    // Una vez cumple los requisitos, hash ->
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // Preparar la consulta SQL
    $sql = "INSERT INTO usuarios (username, email, pass, nombre, apellido1, apellido2, direccion, fechaNac, fechaCreacion, fechaModificacion, estado, perfil, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Preparar la declaración
    $stmt = $conn->prepare($sql);
    
    // Asociar parámetros y ejecutar la consulta
    $stmt->bind_param("sssssssssssss", $nombreUsuario, $email, $hashedPassword, $nombre, $apellido1, $apellido2, $direccion, $fechaNacimientoString, $fechaCreacion, $fechaModificacion, $estado, $perfil, $avatarNombre);
    
    if ($stmt->execute()) {
        $success = 'USER_REG';
        header("Location: index.php?success=" . $success);
    } else {
        header("Location: registro.php?error=" . $stmt->error);
    }
    
    // Cerrar la conexión y liberar recursos
    $stmt->close();
    $conn->close();
}
?>