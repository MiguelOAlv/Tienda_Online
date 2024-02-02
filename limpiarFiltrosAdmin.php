<?php
session_start();

unset($_SESSION['sql_usuarios_filtrado']);

header('Location: opciones_admin.php');
exit();
?>