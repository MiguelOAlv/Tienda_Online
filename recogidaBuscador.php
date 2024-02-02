<?php
require_once 'funciones/config.php';
require_once 'funciones/p1_lib.php';
require_once 'entities/ent_producto.php';
require_once 'entities/ent_usuario.php';
require_once 'entities/ent_imagenes.php';

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    unset($_SESSION['sql_filtrado']);
    $conn = get_connection();
    $sql = "SELECT * FROM productos WHERE 1=1";

    // Verificar y agregar condiciones segun la entrada del formulario
    if (!empty($_POST['categoria'])) {
        $categoriaSeleccionada = $_POST['categoria'];
        // La categoría seleccionada es un número
        if (is_numeric($categoriaSeleccionada)) {
            $sql .= " AND categoria_id = $categoriaSeleccionada";
        }
    }
    if (!empty($_POST['buscar'])) {
        $busqueda = $_POST['buscar'];
        // Validación de caracteres especiales
        if (preg_match('/^[a-zA-Z0-9 ]*$/', $busqueda)) {
            $busqueda = mysqli_real_escape_string($conn, $busqueda);
            $sql .= " AND (titulo LIKE '%" . $busqueda . "%' OR descripcion LIKE '%" . $busqueda . "%')";
        } else {
            $error = 'NO_RESULT';
            if ($_POST['flag'] == 1) {
                header("Location: misProductos.php=?error=" . $error);
                exit();
            } else {
                header("Location: home.php?error=" . $error);
                exit();
            }
        }
    }
    if (!empty($_POST['ordenarPrecio'])) {
        // OrdenarPrecio válido ('asc' o 'desc')
        if (in_array($_POST['ordenarPrecio'], ['asc', 'desc'])) {
            $sql .= " ORDER BY precio " . $_POST['ordenarPrecio'];
        } else {
            $error = 'INVALID_ORDER';
            if ($_POST['flag'] == 1) {
                header("Location: misProductos.php=?error=" . $error);
                exit();
            } else {
                header("Location: home.php?error=" . $error);
                exit();
            }
        }
    }

    // Ejecutar la consulta
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        /*$productos = [];
        while ($row = $resultado->fetch_assoc()) {
            // Para cada producto, obtener sus imágenes
            $idProducto = $row['idProducto'];
            $sqlImagenes = "SELECT idFotoProducto, imagen, idProducto FROM fotosproductos WHERE idProducto =" . $idProducto . "";
            $resultadoImagenes = $conn->query($sqlImagenes);
            
            $imagenes = [];
            while ($imagen = $resultadoImagenes->fetch_assoc()) {
                $imagenes[] = new Imagen($imagen['idProducto'], $imagen['idFotoProducto'], $imagen['imagen']);
            }
            // Agregar imagenes al producto
            $productos[] = new Producto($row['idProducto'], $row['titulo'], $row['descripcion'], $row['precio'], $row['fechaCreacion'], $row['idVendedor'], $row['idComprador'], $row['categoria_id'], $row['subcategoria_id'], $imagenes);
        }*/

        //Redirecciono dependiendo de donde venga
        if ($_POST['flag'] == 1) {
            $_SESSION['sql_filtrado'] = $sql;
            header("Location: misProductos.php");
            exit();
        } else {
            $_SESSION['sql_filtrado'] = $sql;
            header("Location: home.php");
            exit();
        }
    } else {
        $error = 'NO_RESULT';
        if ($_POST['flag'] == 1) {
            header("Location: misProductos.php");
            exit();
        } else {
            header("Location: home.php?error=" . $error);
            exit();
        }
    }
}
?>