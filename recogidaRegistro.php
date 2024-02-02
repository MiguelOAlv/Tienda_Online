<?php
require_once 'funciones/config.php';
require_once 'funciones/p1_lib.php';
require_once 'entities/ent_producto.php';
require_once 'entities/ent_usuario.php';
require_once 'entities/ent_imagenes.php';

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
    $camposObligatorios = ["nombreUsuario", "email", "password", "fechaNacimiento"];
    $camposFaltantes = [];

    foreach ($camposObligatorios as $campo) {
        if (empty($_POST[$campo])) {
            $camposFaltantes[] = $campo;
        }
    }

    if (!empty($camposFaltantes)) {
        $error = 'NO_DATA';
        header("Location: registro.php?error=" . $error);
        exit;
    }
    //Permitir campos vacios
    if(empty($_POST["nombre"])){
        $nombre = null;
    }
    if(empty($_POST["apellido1"])){
        $apellido1 = null;
    }
    if(empty($_POST["apellido2"])){
        $apellido2 = null;
    }
    if(empty($_POST["direccion"])){
        $direccion = null;
    }

    if(empty($_FILES["avatar"]['tmp_name'])) {
        $avatar = null;
    }else{
        // Verificar el tipo de archivo
        $permitidos = array("image/jpeg", "image/png", "image/jpg");
        if (!in_array($avatar["type"], $permitidos)) {
            $error = 'FORMAT';
            //header("Location: registro.php?error=" . $error);
            //exit;
        }

        // Verificar el tamaño del archivo 10MB maximo
        $tamanioMaximo = 10000000;
        if ($avatar["size"] > $tamanioMaximo) {
            $error = 'SIZE';
            header("Location: registro.php?error=" . $error);
            exit;
        }
        //Obtener imagen en formato blob
        $avatarNombreSQL = file_get_contents($_FILES['avatar']['tmp_name']);
    }

    $conn = get_connection();

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

    //Verificar que el campo nombre de usuario no contenga caracteres especiales
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $nombreUsuario)) {
        $error = 'USERNAME_INVALID_CHARS';
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
    $resultado = validarPass($password, $confirmarPassword, $error);
    if($resultado==0){
        header("Location: registro.php?error=" . $error);
        exit;
    }

    // Una vez cumple los requisitos, hash ->
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    /*// Verificar que se haya seleccionado un archivo
    if ($avatar["error"] == UPLOAD_ERR_NO_FILE) {
        $error = 'NO_FILE';
        header("Location: registro.php?error=" . $error);
        exit;
    }*/

    

    // Generar un nombre único para el archivo
    /*$avatarNombre = uniqid() . '_' . $avatar["name"];
    $avatarDestino = "img/" . $avatarNombre;*/

    // Mover el archivo al directorio de destino
    /*if (!move_uploaded_file($avatar["tmp_name"], $avatarDestino)) {
        $error = 'UPLOAD_ERROR';
        header("Location: registro.php?error=" . $error);
        exit;
    }*/

    // Preparar la consulta SQL
    $sql = "INSERT INTO usuarios (username, email, pass, nombre, apellido1, apellido2, direccion, fechaNac, fechaCreacion, fechaModificacion, estado, perfil, avatar) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    // Preparar la declaración
    $stmt = $conn->prepare($sql);
    
    // Asociar parámetros y ejecutar la consulta
    $stmt->bind_param("sssssssssssss", $nombreUsuario, $email, $hashedPassword, $nombre, $apellido1, $apellido2, $direccion, $fechaNacimientoString, $fechaCreacion, $fechaModificacion, $estado, $perfil, $avatarNombreSQL);
    //die(var_dump($avatarNombreSQL));
    if ($stmt->execute()) {
        $success = 'USER_REG';
        header("Location: index.php?success=" . $success);
    } else {
        header("Location: registro.php?error=" . $stmt->error);
    }
    
    // Cerrar la conexión
    $stmt->close();
    $conn->close();
}
?>