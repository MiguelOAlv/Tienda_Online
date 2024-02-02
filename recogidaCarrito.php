<?php
require_once 'funciones/config.php';
require_once 'funciones/p1_lib.php';
require_once 'entities/ent_producto.php';
require_once 'entities/ent_usuario.php';
require_once 'entities/ent_imagenes.php';


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!empty($_POST['idProducto'])) {
        $idProducto = $_POST['idProducto'];
        
        // Verifica si ya existe la cookie del carrito
        if (isset($_COOKIE['carrito'])) {
            $carrito = json_decode($_COOKIE['carrito'], true); // Decodifica el JSON a un array
        } else {
            $carrito = array();
        }

        // Añade el ID del producto al array del carrito si aún no está incluido
        if (!in_array($idProducto, $carrito)) {
            $carrito[] = $idProducto;
        }

        // Codifica el array del carrito a JSON y actualiza la cookie
        setcookie('carrito', json_encode($carrito), time() + (30 * 60), "/"); //30mins
        header('Location: home.php');
    }


}