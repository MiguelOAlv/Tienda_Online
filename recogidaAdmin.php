<?php

require_once 'funciones/config.php';
require_once 'funciones/p1_lib.php';
require_once 'entities/ent_producto.php';
require_once 'entities/ent_usuario.php';
require_once 'entities/ent_imagenes.php';

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idUser = $_SESSION['idUsuario']; //ID del usuario

    if (isset($_POST['bloquear'])) {
        $idUsuario = $_POST['idUsuario'];
        $estado = $_POST['estado']; // 0 bloquear, 1 desbloquear

        //Invalidar actualizacion desde el mismo usuario
        if ($idUser == $idUsuario) {
            $error = 'INV_ACT';
            header('Location: opciones_admin.php?error=' . $error);
            exit();
        }

        $resultado = actualizarEstado($estado, $idUsuario);
        if ($resultado = 1) {
            $success = 'ADMIN_ACT';
            header('Location: opciones_admin.php?success=' . $success);
            exit();
        } else {
            $error = 'ERR_ACT';
            header('Location: opciones_admin.php?error=' . $error);
            exit();
        }
    }

    if (isset($_POST['hacerAdmin'])) {
        $idUsuario = $_POST['idUsuario'];
        $perfil = $_POST['perfil'];

        //Invalidar actualizacion desde el mismo usuario
        if ($idUser == $idUsuario) {
            $error = 'INV_ACT';
            header('Location: opciones_admin.php?error=' . $error);
            exit();
        }

        $resultado = actualizarDatosAdmin($perfil, $idUsuario);
        if ($resultado = 1) {
            $success = 'ADMIN_ACT';
            header('Location: opciones_admin.php?success=' . $success);
            exit();
        } else {
            $error = 'ERR_ACT';
            header('Location: opciones_admin.php?error=' . $error);
            exit();
        }
    }

    if (isset($_POST['eliminar'])) {
        $idUsuario = $_POST['idUsuario'];

        //Invalidar actualizacion desde el mismo usuario
        if ($idUser == $idUsuario) {
            $error = 'INV_ACT';
            header('Location: opciones_admin.php?error=' . $error);
            exit();
        }
        //Invalidar eliminar a un usuario administrador IMPORTANTE
        if ($idUsuario->perfil == $_POST['perfil']) {

        }
        $resultado = eliminarUsuario($idUsuario);
        if ($resultado = 1) {
            $success = 'USR_DEL';
            header('Location: opciones_admin.php?success=' . $success);
            exit();
        } else {
            $error = 'ERR_ACT';
            header('Location: opciones_admin.php?error=' . $error);
            exit();
        }
    }

    //Ver articulos de usuario desde administrador
    session_start();
    if (isset($_POST['idUsuario'])) {
        $_SESSION['modo'] = 'admin';
        $_SESSION['idUsuarioAdmin'] = $_POST['idUsuario'];

        header("Location: misProductos.php");
        exit();
    }
}

?>