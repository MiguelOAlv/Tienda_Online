<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Mis Productos</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f0f0f0;
        }

        .container {
            margin-top: 10px;
            margin-bottom: 15px;
        }

        footer a {
            color: white;
        }

        form {
            max-width: 800px;
            margin: 0 auto;
        }

        .error {
            margin-top: 10px;
            text-align: center;
            color: red;
        }

        .exito {
            margin-top: 10px;
            text-align: center;
            color: green;
        }

        .navbar-search {
            background-color: #d6d6d6;
        }
    </style>
    <?php
    require_once 'funciones/config.php';
    require_once 'funciones/p1_lib.php';
    require_once 'entities/ent_producto.php';
    require_once 'entities/ent_usuario.php';
    require_once 'entities/ent_imagenes.php';
    session_start();
    //Prohibir entradas sin iniciar sesion
    if ($_SESSION['id'] == null) {
        session_destroy();
        $error = 'NEED_SESSION';
        header("Location: index.php?error=" . $error);
    }
    //Comprobar si es admin o no
    if (isset($_SESSION['modo']) && $_SESSION['modo'] == 'admin' && isset($_SESSION['idUsuarioAdmin'])) {
        $user_id = $_SESSION['idUsuarioAdmin'];
    } else {
        $user_id = $_SESSION['idUsuario'];
    }
    $usuario = $_SESSION['usuario']; //Pasar los parametros del usuario que ha iniciado sesion
    
    $productosPorPagina = 6;
    // Calcular la cantidad total de páginas TotalProductosPorID/ProductosPorPagina
    $totalPaginas = ceil(Producto::obtenerTotalProductosPorID($user_id) / $productosPorPagina);
    // Validar introducir numero mayor que total de paginas o menor que 1
    if (isset($_GET['pag']) && ($_GET['pag'] < 0 || $_GET['pag'] > $totalPaginas)) {
        $paginaSolicitada = intval($_GET['pag']);
        if ($paginaSolicitada < 1 || $paginaSolicitada > $totalPaginas) {
            header('Location: misProductos.php?pag=1');
            exit();
        }
    }
    ?>
</head>

<!-- Primer nav -->

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
                        <a class="navbar-brand">
                            <?php
                            $idUsuario = $_SESSION["idUsuario"];
                            $avatar = obtenerAvatarPorID($usuario->idUsuario);
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
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">PRODUCTOS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="subirProducto.php">SUBIR PRODUCTOS</a>
                    </li>
                    <?php
                    if ($usuario->perfil == '1') { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="limpiarFiltrosAdmin.php">ADMINISTRADOR</a>
                        </li>
                    <?php } else { ?>
                        <li class="nav-item active">
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

    <!-- Segundo nav -->
    <nav class="navbar navbar-expand-lg navbar-light bg-secondary">
        <div class="container-fluid">
            <ul class="navbar-nav justify-content-center w-100">
                <?php if (isset($_SESSION['modo']) && $_SESSION['modo'] == 'admin' && isset($_SESSION['idUsuarioAdmin'])) { ?>
                    <li class="nav-item mx-3">
                        <a class="nav-link active" href="limpiarFiltroAdminProductos.php">SUS PRODUCTOS</a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="nav-link" href="misValoraciones.php">SUS VALORACIONES</a>
                    </li>
                <?php } else { ?>
                    <li class="nav-item mx-3">
                        <a class="nav-link active" href="limpiarFiltroAdminProductos.php">MIS PRODUCTOS</a>
                    </li>
                    <li class="nav-item mx-3">
                        <a class="nav-link" href="misValoraciones.php">MIS VALORACIONES</a>
                    </li>
                <?php } ?>
            </ul>
        </div>
    </nav>
    <!-- Buscador -->
    <nav class="navbar navbar-light d-flex justify-content-center align-items-center navbar-search">
        <form class="form-inline" method="post" action="recogidaBuscador.php">
            <input type="hidden" name="flag" value="1">
            <input class="form-control mr-sm-2" type="text" name="buscar" placeholder="Buscar" aria-label="Buscar">
            <button class="btn btn-info my-2 my-sm-2" type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                    height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                    <path
                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                </svg></button>
        </form>
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

    <div class="container mt-3 flex-grow-1 d-flex justify-content-center align-items-center">
        <!-- Contenido principal -->
        <div class="row d-flex flex-grow-1 justify-content-center align-items-center">
            <?php
            if (isset($_SESSION['sql_filtrado'])) {
                //Recupero el sql
                $sql = $_SESSION['sql_filtrado'];
                // Definir los productos por pagina
                $productosPorPagina = 6;
                // Calcular la cantidad total de páginas TotalProductos/ProductosPorPagina
                $totalPaginas = ceil(Producto::obtenerTotalProductosFiltradosPorID($sql, $user_id) / $productosPorPagina);
                // Obtener la página actual de la URL
                $paginaActual = isset($_GET['pag']) ? $_GET['pag'] : 1;
                // Calcular el índice de inicio
                $indiceInicio = ($paginaActual - 1) * $productosPorPagina;
                // Obtener productos paginados filtrados
                $productos = Producto::obtenerProductosPaginadosFiltradosPorID($indiceInicio, $productosPorPagina, $sql, $user_id);
            } else {
                // Obtener la página actual de la URL
                $paginaActual = isset($_GET['pag']) ? $_GET['pag'] : 1;
                // Calcular el índice de inicio
                $indiceInicio = ($paginaActual - 1) * $productosPorPagina;
                // Obtener productos paginados
                $productos = Producto::obtenerProductosPaginadosPorID($indiceInicio, $productosPorPagina, $user_id);
            }
            if (empty($productos)) { //Mostrar mensaje informativo
                if (isset($_SESSION['modo']) && $_SESSION['modo'] == 'admin' && isset($_SESSION['idUsuarioAdmin'])) {
                    echo '<p class="error">' . 'El usuario no tiene productos subidos' . '</p>';
                } else {
                    echo '<p class="error">' . 'No tienes ningun producto subido' . '</p>';
                }
            }
            foreach ($productos as $producto) {
                echo '<div class="col-12 col-md-6 col-lg-3 mb-4 mr-4">';
                echo '<div class="card" style="width: 18rem;">';

                // Carrusel para las imágenes del producto
                echo '<div id="carousel' . $producto->idProducto . '" class="carousel slide" data-ride="carousel">';
                echo '<div class="carousel-inner">';

                $first = true;
                foreach ($producto->imagenes as $imagen) {
                    echo '<div class="carousel-item' . ($first ? ' active' : '') . '">';
                    echo '<img class="d-block w-100" width="286" height="286" src="data:image/jpeg;base64,' . base64_encode($imagen->imagen) . '" alt="Imagen del producto">';
                    echo '</div>';
                    $first = false;
                }

                echo '</div>';
                echo '<a class="carousel-control-prev" href="#carousel' . $producto->idProducto . '" role="button" data-slide="prev">';
                echo '<span class="carousel-control-prev-icon" aria-hidden="true"></span>';
                echo '<span class="sr-only">Anterior</span>';
                echo '</a>';
                echo '<a class="carousel-control-next" href="#carousel' . $producto->idProducto . '" role="button" data-slide="next">';
                echo '<span class="carousel-control-next-icon" aria-hidden="true"></span>';
                echo '<span class="sr-only">Siguiente</span>';
                echo '</a>';
                echo '</div>'; // Fin del carrusel
            
                echo '<div class="card-body" style="height: 13rem";>';
                echo '<h5 class="card-title">' . $producto->titulo . " <br><br> " . $producto->precio . "€" . '</h5>';
                echo '<p class="card-text">' . $producto->descripcion . '</p>';


                //Formulario para borrar el producto
                echo '<form method="POST" action="recogidaBorrar.php" onsubmit="return confirm(\'¿Estás seguro de que quieres borrar este producto?\');">';
                echo '<a href="modificarProducto.php?id=' . $producto->idProducto . '" class="btn btn-info mr-5">Modificar</a>';
                echo '<input type="hidden" name="idProductoBorrar" value="' . $producto->idProducto . '">';
                echo '<button type="submit" class="btn btn-danger ml-4">Borrar</button>';
                echo '</form>';

                echo '</div>'; // Fin del body de la tarjeta
                echo '</div>'; // Fin de la tarjeta
                echo '</div>'; // Fin de la columna
            }
            ?>
        </div>
    </div>

    <!-- Paginacion -->
    <?php if (empty($productos)) {
    //Si no hay productos no mostrar paginacion
} else { ?>
        <div class="container mt-3 flex-grow-1 d-flex justify-content-center align-items-center">
            <div class="col-12 col-md-6 col-lg-3 mb-4 mr-4">
                <nav aria-label="Page navigation">
                    <ul class="pagination justify-content-center">
                        <?php
                        // Previous
                        echo '<li class="page-link"><a href="?pag=1"><span aria-hidden="true">&larr;</span>Primera</a></li>';
                        // Paginacion activa
                        for ($i = 1; $i <= $totalPaginas; $i++) {
                            $activeClass = ($i == $paginaActual) ? 'active' : '';
                            echo '<li class="page-item ' . $activeClass . '"><a class="page-link" href="?pag=' . $i . '">' . $i . '</a></li>';
                        }
                        // Next
                        echo '<li class="page-link"><a href="?pag=' . $totalPaginas . '"><span aria-hidden="true"></span>Ultima</a>&rarr;</li>';
                        ?>
                    </ul>
                </nav>
            </div>
        </div>
    <?php } ?>

    <footer class="footer bg-dark text-white mt-auto">
        <nav class="w-100">
            <ul class="nav d-flex justify-content-between pr-5 pl-5">
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
    </footer>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>