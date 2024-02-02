<?php
require_once 'funciones/config.php';
require_once 'funciones/p1_lib.php';
require_once 'entities/ent_producto.php';
require_once 'entities/ent_usuario.php';
require_once 'entities/ent_imagenes.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $conn = get_connection();
        // Obtener datos del formulario
        $user_id = $_POST['user_id'];
        $username = $_POST['username'];
        $email = $_POST['email'];
        $new_pass = $_POST['pass'];
        $confirm_pass = $_POST['confirm_pass'];
        $nombre = $_POST['nombre'];
        $apellido1 = $_POST['apellido1'];
        $apellido2 = $_POST['apellido2'];
        $direccion = $_POST['direccion'];
        $fechaModificacion = date("Y-m-d H:i:s");

        // Verificar si el nombre y el email han cambiado
            $sql = "SELECT username, email, pass FROM usuarios WHERE idUsuario = $user_id";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                $row = $result->fetch_assoc();
            
                // Verificar si el nombre o el email han cambiado
                if ($username !== $row['username'] || $email !== $row['email']) {
                    $error = 'USR/EMAIL';
                    header("Location: opciones_usuario.php?error=" . $error);
                    exit;
                } 
                
                if(empty($new_pass) && empty($confirm_pass)){
                    $sql = "UPDATE usuarios SET username='$username',nombre='$nombre', apellido1='$apellido1', apellido2='$apellido2', direccion='$direccion', fechaModificacion='$fechaModificacion' WHERE idUsuario=$user_id";
                            if ($conn->query($sql) === TRUE) {
                                $success = 'USER_ACT';
                                header("Location: opciones_usuario.php?success=" . $success);
                                exit;
                            } else {
                                $error = 'DB_CONNECTION_ERROR';
                                header("Location: opciones_usuario.php?error=" . $error);
                                exit;
                            }
                } else if ($new_pass === $confirm_pass){
                    $resultado = validarPass($new_pass, $confirm_pass, $error);
                    if ($resultado === 1) {

                    // Hash de la nueva contraseña
                    $hashed_pass = password_hash($new_pass, PASSWORD_BCRYPT);
            
                    // Guardar la contraseña actual en el historial
                    $sqlHistorico = "INSERT INTO historicousuarios (usuarios_id, passAntigua) VALUES ('$user_id', '{$row['pass']}')";
                    if ($conn->query($sqlHistorico) === TRUE) {
                        // Actualizar los datos del usuario
                        $sql = "UPDATE usuarios SET username='$username', pass='$hashed_pass', nombre='$nombre',apellido1='$apellido1', apellido2='$apellido2', direccion='$direccion', fechaModificacion='$fechaModificacion' WHERE idUsuario=$user_id";
            
                        if ($conn->query($sql) === TRUE) {
                            $success = 'USER_ACT';
                            header("Location: opciones_usuario.php?success=" . $success);
                            exit;
                        } else {
                            $error = 'DB_CONNECTION_ERROR';
                            header("Location: opciones_usuario.php?error=" . $error);
                            exit;
                        }
                    }else{
                        $error = 'DB_CONNECTION_ERROR';
                        header("Location: opciones_usuario.php?error=" . $error);
                        exit;
                    }
                }else{
                        //$error parametrizado -> Lo devuelve la funcion
                        header("Location: opciones_usuario.php?error=" . $error);
                        exit;
                }
                }else{
                        $error = 'PASSWORD_MISMATCH';
                        header("Location: opciones_usuario.php?error=" . $error);
                        exit;
                }
                
            } else {
                // Cerrar la conexión
                $conn->close();
                $error = 'USER_NOT_FOUND';
                header("Location: opciones_usuario.php?error=" . $error);
                exit;
            }

            
}
?>