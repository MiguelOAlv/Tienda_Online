<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Home</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <link rel="stylesheet" href="style/style_home.css">

    <?php
    require_once 'funciones/config.php';
    require_once 'funciones/p1_lib.php';
    session_start();

    // Prohibir entradas sin iniciar sesión
    if (!isset($_SESSION['id'])) {
        session_destroy();
        $error = 'NEED_SESSION';
        header("Location: index.php?error=" . $error);
        exit;
    }

    $error = null;
    $success = null;
    ?>
</head>

<body>
    <header class="header bg-dark text-white">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <?php
                    $linea = obtenerUltimaImagenPorID($_SESSION['ID'], $error);
                    $ruta = cargarImagen($error);

                    if (!empty($ruta)) {
                    ?>
                        <div class="logo">
                            <img src="<?php echo $ruta; ?>" class="img-fluid" alt="Logo">
                        </div>
                    <?php } else { ?>
                        <div class="logo">
                            <img src="img/icono.jpg" class="img-fluid" alt="Logo">
                        </div>
                    <?php } ?>
                </div>
                <div class="col-md-9">
                    <nav class="navbar navbar-expand-lg navbar-dark">
                        <ul class="navbar-nav ml-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="home.php">PRODUCTOS</a>
                            </li>
                            <?php
                            $type = $_SESSION['type'];
                            if (isset($_SESSION['type']) && $_SESSION['type'] == '1') { ?>
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
                    </nav>
                </div>
            </div>
        </div>
    </header>

    <?php
    if (isset($_GET["error"])) {
        $error = $_GET["error"];
        if (isset($ERROR[$error])) {
            $MensajeError = $ERROR[$error];
    ?>
            <div class="container mt-3">
                <div class="alert alert-danger" role="alert">
                    <?php echo $MensajeError; ?>
                </div>
            </div>
    <?php }
    } ?>

    <div class="container mt-3">
        <div class="row">
            <div class="col-md-4">
                <a href="producto.php?id=1" class="text-dark">
                    <img src="img/productos/monopoly.jpg" alt="Producto 1" class="img-fluid">
                </a>
            </div>
            <div class="col-md-4">
                <a href="producto.php?id=2" class="text-dark">
                    <img src="img/productos/party.jpg" alt="Producto 2" class="img-fluid">
                </a>
            </div>
            <div class="col-md-4">
                <a href="producto.php?id=3" class="text-dark">
                    <img src="img/productos/carcassonne.jpg" alt="Producto 3" class="img-fluid">
                </a>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-4">
                <a href="producto.php?id=4" class="text-dark">
                    <img src="img/productos/uno.jpg" alt="Producto 4" class="img-fluid">
                </a>
            </div>
            <div class="col-md-4">
                <a href="producto.php?id=5" class="text-dark">
                    <img src="img/productos/cluedo.jpg" alt="Producto 5" class="img-fluid">
                </a>
            </div>
            <div class="col-md-4">
                <a href="producto.php?id=6" class="text-dark">
                    <img src="img/productos/catan.jpg" alt="Producto 6" class="img-fluid">
                </a>
            </div>
        </div>

        <div class="row mt-3">
            <div class="col-md-4">
                <a href="producto.php?id=7" class="text-dark">
                    <img src="img/productos/risk.jpg" alt="Producto 7" class="img-fluid">
                </a>
            </div>
            <div class="col-md-4">
                <a href="producto.php?id=8" class="text-dark">
                    <img src="img/productos/trivial.jpg" alt="Producto 8" class="img-fluid">
                </a>
            </div>
            <div class="col-md-4">
                <a href="producto.php?id=9" class="text-dark">
                    <img src="img/productos/preguntados.jpg" alt="Producto 9" class="img-fluid">
                </a>
            </div>
        </div>
    </div>

    <footer class="footer mt-5">
        <div class="container">
            <nav>
                <ul class="nav justify-content-end">
                    <li class="nav-item">
                        <a class="nav-link" href="mailto:mickey1198@hotmail.com">CONTACTO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">HORARIO</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://twitter.com">REDES SOCIALES</a>
                    </li>
                </ul>
            </nav>
        </div>
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
