<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Producto</title>
  <link rel="stylesheet" href="style/style_producto.css">
  <?php
  require_once 'funciones/config.php';
  require_once 'funciones/p1_lib.php';
  require_once 'entities/ent_producto.php';
  require_once 'entities/ent_usuario.php';
  require_once 'entities/ent_imagenes.php';
  //Prohibir entradas sin iniciar sesion
  session_start();
  if ($_SESSION['id'] == null) {
    session_destroy();
    $error = 'NEED_SESSION';
    header("Location: index.php?error=" . $error);
  }
  $type = $_SESSION['type'];
  $error = null;
  $success = null;
  ?>
</head>

<body>
  <header class="header">
    <?php
    $linea = obtenerUltimaImagenPorID($_SESSION['ID'], $error);

    $ruta = cargarImagen($error);
    if (!empty($ruta)) {
      ?>
      <div class="logo">
        <img src="<?php echo $ruta; ?>">
      </div>
      <?php
    } else {
      ?>
      <div class="logo">
        <img src="img/icono.jpg">
      </div>
      <?php
    }
    ?>
    <nav>
      <ul class="nav-links">
        <li><a href="home.php">PRODUCTOS</a></li>
        <?php

        if (isset($_SESSION['type']) && $_SESSION['type'] == '1') { //Si es admin, cargar opciones
          ?>
          <li><a href="opciones_admin.php">ADMINISTRADOR</a></li>
          <?php
        } else {
          ?>
          <li><a href="opciones_usuario.php">OPCIONES</a></li>
          <?php
        }
        ?>
        <li><a href="logout.php">LOG OUT</a></li>
      </ul>
    </nav>
  </header>
  <!-- Mostrar el producto -->
  <?php
  if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $productoInfo = $PRODUCTOS[$id];
  } else {
    $error = 'ERROR_ITEM';
    header("Location: home.php?error=" . $error);
  }
  ?>
  <div class="productoInfo">
    <h2>
      <?php echo $productoInfo['nombre']; ?>
    </h2><br><br>
    <img src="<?php echo $productoInfo['ruta'] ?>">
    <br><br>
    <h2>Precio: $
      <?php echo $productoInfo['precio']; ?>
    </h2>
    <div class='texto'>
      <p>Lorem ipsum dolor sit amet consectetur adipisicing elit. Est praesentium veritatis, odio esse doloremque
        inventore corrupti modi labore voluptate quae quod maxime beatae possimus reiciendis similique vel molestias
        vitae. Reprehenderit.</p>
    </div>
  </div>
  <footer class="footer">
    <nav>
      <ul class="nav-links">
        <li><a href="mailto:mickey1198@hotmail.com">CONTACTO</a></li>
        <li><a href="#">HORARIO</a></li>
        <li><a href="https://twitter.com">REDES SOCIALES</a></li>
      </ul>
    </nav>
  </footer>
</body>

</html>