<?php
session_start();

unset($_SESSION['sql_filtrado']);

header('Location: misProductos.php');
exit();
?>