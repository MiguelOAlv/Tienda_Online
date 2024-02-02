<?php
require_once 'funciones/config.php';
require_once 'funciones/p1_lib.php';
require_once 'entities/ent_producto.php';
require_once 'entities/ent_usuario.php';
require_once 'entities/ent_imagenes.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['titulo']) && isset($_POST['descripcion']) && isset($_POST['precio']) && isset($_POST['categoria']) && isset($_POST['subcategoria']) && isset($_FILES['imagenes'])) {
        $titulo = $_POST['titulo'];
        $descripcion = $_POST['descripcion'];
        $precio = $_POST['precio'];
        $categoria = $_POST['categoria'];
        $subcategoria = $_POST['subcategoria'];
        $imagenes = $_FILES['imagenes'];
        $idVendedor = $_POST['user_id'];
        $fechaCreacion = date("Y-m-d H:i:s");

        

        //Validacion de precio
        if ($precio<= 0 || $precio>150000){
            $error = 'PRECIO_NEG';
            header('Location: subirProducto.php?error=' . $error);
            exit();
        }
        //Validacion de imagenes
        if (!empty($_FILES['imagenes']['name'][0])) {
            $totalArchivos = count($_FILES['imagenes']['name']);

            // Verifica que no se suban más de 5 archivos
            if ($totalArchivos > 5) {
                $error = 'COUNT_IMG';
                header('Location: subirProducto.php?error=' . $error);
                exit();
            }
            for ($i = 0; $i < $totalArchivos; $i++) {
                // Verifica el tipo de archivo
                $tipoArchivo = $_FILES['imagenes']['type'][$i];
                if (!in_array($tipoArchivo, ['image/jpeg', 'image/png', 'image/jpg'])) {
                    $error = 'FORMAT';
                    header('Location: subirProducto.php?error=' . $error);
                    exit();
                }

                // Verifica el tamaño del archivo (10 MB máximo)
                $tamañoArchivo = $_FILES['imagenes']['size'][$i];
                if ($tamañoArchivo > 10000000) {
                    $error = 'SIZE';
                    header('Location: subirProducto.php?error=' . $error);
                    exit();
                }
            }
        }
    } else {
        $error = 'NO_DATA';
        header('Location: subirProducto.php?error=' . $error);
        exit();
    }
    $conn = get_connection();
    //Realizar la consulta
    $query = "INSERT INTO productos (titulo, descripcion, precio, fechaCreacion, idVendedor, idComprador, categoria_id, subcategoria_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param('ssssssss', $titulo, $descripcion, $precio, $fechaCreacion, $idVendedor, $idComprador, $categoria, $subcategoria);
    $stmt->execute();

    //Obtener id del producto recien insertado
    $idProducto = $conn->insert_id;

    //Insertar imagenes del producto
    $insertImageQuery = "INSERT INTO fotosproductos (imagen, idProducto) VALUES (?, ?)";
    $imageStmt = $conn->prepare($insertImageQuery);
    $imageStmt->bind_param("bi", $null, $idProducto); //$null hace de marcador

    for ($i = 0; $i < $totalArchivos; $i++) {
        $imagen = file_get_contents($_FILES['imagenes']['tmp_name'][$i]);
        //Esto envia las imagenes
        $imageStmt->send_long_data(0, $imagen);
        $imageStmt->execute();
    }

    //Crear el objeto producto
    $producto = new Producto(
        $row["idProducto"],
        $row["titulo"],
        $row["descripcion"],
        $row["precio"],
        $row["fechaCreacion"],
        $row["idVendedor"],
        $row["idComprador"],
        $row["categoria_id"],
        $row["subcategoria_id"],
        $row["imagen"],
    );

    $imageStmt->close();
    $conn->close();
    
    

    $success = 'ITEM_ACT';
    header('Location: subirProducto.php?success=' . $success);
    exit();
}
?>