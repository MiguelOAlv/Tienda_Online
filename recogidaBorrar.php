<?php
require_once 'funciones/config.php';
require_once 'funciones/p1_lib.php';
require_once 'entities/ent_producto.php';
require_once 'entities/ent_usuario.php';
require_once 'entities/ent_imagenes.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $idProductoBorrar = $_POST['idProductoBorrar'];

    $resultado = Producto::borrarProductoPorID($idProductoBorrar);
    if ($resultado) {
        $success = 'ITEM_DEL';
        header('Location: misProductos.php?success=' . $success);
        exit();
    } else {
        $error = 'ERROR_DEL';
        header('Location: misProductos.php?error=' . $error);
    }

}

?>