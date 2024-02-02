<?php
require_once 'funciones/config.php';
require_once 'funciones/p1_lib.php';
require_once 'entities/ent_producto.php';
require_once 'entities/ent_usuario.php';
require_once 'entities/ent_imagenes.php';

session_start();
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    unset($_SESSION['sql_usuarios_filtrado']);
    $conn = get_connection();
    $sql = "SELECT * FROM usuarios WHERE 1=1";

    // Filtro de columna
    if (!empty($_POST['columna'])) {
        $columnaSeleccionada = mysqli_real_escape_string($conn, $_POST['columna']);
        $sql .= " ORDER BY {$columnaSeleccionada}";
    }

    // Filtro de orden
    if (!empty($_POST['ordenar'])) {
        $orden = $_POST['ordenar'] === 'asc' ? 'ASC' : 'DESC';
        $sql .= " {$orden}";
    }

    // Filtro de busqueda
    if (!empty($_POST['buscar'])) {
        $busqueda = mysqli_real_escape_string($conn, $_POST['buscar']);
        $sql .= " AND (username LIKE '%{$busqueda}%' OR email LIKE '%{$busqueda}%')";
    }

    $_SESSION['sql_usuarios_filtrado'] = $sql;
    header("Location: opciones_admin.php");
    exit;
}
?>
