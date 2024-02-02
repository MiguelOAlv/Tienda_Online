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
            margin-top: 5px;
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
    // Definir los productos por pagina
    $productosPorPagina = 6;
    // Calcular la cantidad total de páginas TotalProductos/ProductosPorPagina
    $totalPaginas = ceil(Producto::obtenerTotalProductos() / $productosPorPagina);
    // Validar introducir numero mayor que total de paginas o menor que 1
    if (isset($_GET['pag']) && ($_GET['pag'] < 0 || $_GET['pag'] > $totalPaginas)) {
        $paginaSolicitada = intval($_GET['pag']);
        if ($paginaSolicitada < 1 || $paginaSolicitada > $totalPaginas) {
            header('Location: home.php?pag=1');
            exit();
        }
    }
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

    <!-- Buscador -->
    <nav class="navbar navbar-light bg-secondary d-flex justify-content-center align-items-center">
        <form class="form-inline ml-5" method="post" action="recogidaBuscador.php">
            <!-- Selector de categorías -->
            <select class="form-control mr-sm-5" id="categoria" name="categoria">
                <?php
                $categorias = obtenerCategorias();
                echo "<option disabled selected>Ordenar por categoria</option>";
                foreach ($categorias as $categoria) {
                    echo "<option value='{$categoria['idCategoria']}'>{$categoria['descripcion']}</option>";
                }
                ?>
            </select>

            <!-- Selector para ordenar por precio -->
            <select class="form-control mr-sm-5" name="ordenarPrecio">
                <option disabled selected>Ordenar por precio</option>
                <option value="asc">Ascendente</option>
                <option value="desc">Descendente</option>
            </select>
            <input class="form-control mr-sm-2" type="text" name="buscar" placeholder="Buscar" aria-label="Buscar">
            <button class="btn btn-info my-2 my-sm-2" type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="16"
                    height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
                    <path
                        d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
                </svg></button>
                <a href="verCarrito.php" class="btn btn-outline-light ml-3">Ver Carrito <svg
                    xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart"
                    viewBox="0 0 16 16">
                    <path
                        d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                </svg></a>
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
                $totalPaginas = ceil(Producto::obtenerTotalProductosFiltrados($sql) / $productosPorPagina);
                // Obtener la página actual de la URL
                $paginaActual = isset($_GET['pag']) ? $_GET['pag'] : 1;
                // Calcular el índice de inicio
                $indiceInicio = ($paginaActual - 1) * $productosPorPagina;
                // Obtener productos paginados filtrados
                $productos = Producto::obtenerProductosPaginadosFiltrados($indiceInicio, $productosPorPagina, $sql);
            } else {
                // Obtener la página actual de la URL
                $paginaActual = isset($_GET['pag']) ? $_GET['pag'] : 1;
                // Calcular el índice de inicio
                $indiceInicio = ($paginaActual - 1) * $productosPorPagina;
                // Obtener productos paginados
                $productos = Producto::obtenerProductosPaginados($indiceInicio, $productosPorPagina);
            }
            foreach ($productos as $producto) {
                echo '<div class="col-12 col-md-6 col-lg-3 mb-4 mr-4 pl-0">';
                echo '<div class="card d-flex mr-5 ml-5" style="width: 18rem;">';

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
                //Formulario carrito
                echo '<form class="form" method="post" action="recogidaCarrito.php">';
                echo '<a href="verProducto.php?id=' . $producto->idProducto . '" class="btn btn-primary">Ver más</a>';
                echo '<input type="hidden" name="idProducto" value='.$producto->idProducto .'>';
                echo '<button class="btn btn-info ml-1" type="submit">Añadir al carrito <svg
                        xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-cart"
                        viewBox="0 0 16 16">
                        <path
                            d="M0 1.5A.5.5 0 0 1 .5 1H2a.5.5 0 0 1 .485.379L2.89 3H14.5a.5.5 0 0 1 .491.592l-1.5 8A.5.5 0 0 1 13 12H4a.5.5 0 0 1-.491-.408L2.01 3.607 1.61 2H.5a.5.5 0 0 1-.5-.5M3.102 4l1.313 7h8.17l1.313-7zM5 12a2 2 0 1 0 0 4 2 2 0 0 0 0-4m7 0a2 2 0 1 0 0 4 2 2 0 0 0 0-4m-7 1a1 1 0 1 1 0 2 1 1 0 0 1 0-2m7 0a1 1 0 1 1 0 2 1 1 0 0 1 0-2" />
                    </svg></button>';
                echo '</form>';
                echo '</div>'; // Fin del body de la tarjeta
                echo '</div>'; // Fin de la tarjeta
                echo '</div>'; // Fin de la columna
            }
            ?>
        </div>
    </div>

    <!-- Paginacion -->
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