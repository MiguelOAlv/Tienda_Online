<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Home</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f0f0;
        }

        footer a {
            color: white;
        }

        .error {
            color: red;
            display: flex;
            margin-top: 10px;
            align-items: center;
            justify-content: center;
        }

        .exito {
            color: green;
            display: flex;
            margin-top: 5px;
            align-items: center;
            justify-content: center;
        }
    </style>

    <?php
    require_once 'funciones/config.php';
    require_once 'funciones/p1_lib.php';
    require_once 'entities/ent_producto.php';
    require_once 'entities/ent_usuario.php';
    require_once 'entities/ent_imagenes.php';
    session_start();

    // Prohibir entradas sin iniciar sesión
    if (!isset($_SESSION['id'])) {
        session_destroy();
        $error = 'NEED_SESSION';
        header("Location: index.php?error=" . $error);
        exit;
    }
    $usuario = $_SESSION['usuario'];
    $error = null;
    $success = null;
    ?>
</head>

<body class="d-flex flex-column min-vh-100">
    <header>
        <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNavDropdown"
                aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNavDropdown">
                <ul class="navbar-nav justify-content-between align-items-center w-100 pl-5 pr-5">
                    <li class="nav-item d-flex mr-3">
                        <a class="navbar-brand" href="limpiarFiltros.php">
                            <?php
                            $idUsuario = $_SESSION["idUsuario"];
                            $avatar = obtenerAvatarPorID($idUsuario);
                            if (!empty($avatar)) {
                                ?>
                                <div class="logo">
                                    <?php
                                    echo '<img src="data:image/jpeg;base64,' . base64_encode($avatar) . '" width="70" height="50" class="d-inline-block align-top rounded-circle" alt="Avatar"';
                                    ?>
                                </div>
                            <?php } else { ?>
                                <div class="logo">
                                    <img src="img/user.png" width="70" height="50" class="d-inline-block align-top"
                                        alt="Logo">
                                </div>
                            <?php } ?>
                        </a>
                    </li>
                    <li class="nav-item active">
                        <a class="nav-link" href="limpiarFiltros.php">PRODUCTOS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="subirProducto.php">SUBIR PRODUCTOS</a>
                    </li>
                    <?php
                    if ($usuario->perfil == '1') { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="opciones_admin.php">ADMINISTRADOR</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="opciones_usuario.php">OPCIONES</a>
                        </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">LOG OUT</a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <!-- Carrito -->
    <nav class="navbar navbar-light bg-secondary d-flex justify-content-center align-items-center">
        <a href="verCarrito.php" class="btn btn-info ml-3">Mi Carrito <svg xmlns="http://www.w3.org/2000/svg" width="16"
                height="16" fill="currentColor" class="bi bi-cart" viewBox="0 0 16 16">
                <path
                    d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
            </svg></a>
    </nav>
    <?php
    if (isset($_GET["error"])) {
        $error = $_GET["error"];
        if (isset($ERROR[$error])) {
            $MensajeError = $ERROR[$error];
            echo '<p class="error">' . $MensajeError . '</p>';
        }
    }
    if (isset($_GET["success"])) {
        $success = $_GET["success"];
        if (isset($SUCCESS[$success])) {
            $MensajeExito = $SUCCESS[$success];
            echo '<p class="exito">' . $MensajeExito . '</p>';
        }
    }
    ?>

    <?php

    if (isset($_COOKIE['carrito'])) {
        $carrito = json_decode($_COOKIE['carrito'], true);

        if (is_array($carrito) && count($carrito) > 0) {
            foreach ($carrito as $idProducto) {
                $producto = Producto::obtenerProductoPorId($idProducto);
                ?>
                <div class="container mt-4 flex-grow-1">
                    <div class="row flex-grow-1 d-flex justify-content-center align-items-center">
                        <div class="col-12 col-md-8 col-lg-8 mb-4">
                            <div class="card">

                                <!-- Carrusel modificado para adaptarse al nuevo tamaño de la tarjeta -->
                                <div id="carousel<?php echo $producto->idProducto; ?>" class="carousel slide" data-ride="carousel">
                                    <div class="carousel-inner">
                                        <?php
                                        $first = true;
                                        foreach ($producto->imagenes as $imagen) {
                                            echo '<div class="carousel-item' . ($first ? ' active' : '') . '">';
                                            echo '<img class="d-block w-100" src="data:image/jpeg;base64,' . base64_encode($imagen->imagen) . '" alt="Imagen del producto">';
                                            echo '</div>';
                                            $first = false;
                                        }
                                        ?>
                                    </div>
                                    <a class="carousel-control-prev" href="#carousel<?php echo $producto->idProducto; ?>"
                                        role="button" data-slide="prev">
                                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Anterior</span>
                                    </a>
                                    <a class="carousel-control-next" href="#carousel<?php echo $producto->idProducto; ?>"
                                        role="button" data-slide="next">
                                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                        <span class="sr-only">Siguiente</span>
                                    </a>
                                </div>

                                <div class="card-body">
                                    <h5 class="card-title">
                                        <?php echo $producto->titulo . "  " . $producto->precio . "€"; ?>
                                    </h5>
                                    <p class="card-text">
                                        <?php echo $producto->descripcion; ?>
                                    </p>
                                </div>
                            </div>
                            <?php
            }
        }
    } else {
        echo '<p class="error">El carrito esta vacio</p>';
    }
    ?>