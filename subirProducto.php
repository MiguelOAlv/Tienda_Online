<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Subir_Producto</title>
    <meta name='viewport' content='width=device-width, initial-scale=1'>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

    <style>
        body {
            background-color: #f0f0f0;
        }

        footer a {
            color: white;
        }

        form {
            max-width: 700px;
            margin: 0 auto;
        }
        .error{
            color:red;
        }
        .exito{
            color:green;
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
    $totalCat = 18; //Total de categorias en la BBDD
    // Validar introducir numero mayor que total de categorias o menor que 1
    if(isset($_GET['cat']) && ($_GET['cat']<1 || $_GET['cat']>$totalCat)){
        $paginaSolicitada = intval($_GET['cat']);
        if($paginaSolicitada < 1 || $paginaSolicitada > $totalCat){
            header('Location: subirProducto.php?cat=1');
            exit();
        }
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
                        <a class="navbar-brand">
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
                                    <img src="img/user.png" width="70" height="50" class="d-inline-block align-top" alt="Logo">
                                </div>
                            <?php } ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">PRODUCTOS</a>
                    </li>
                    <li class="nav-item active">
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

    <div class="modal-body">
        <form action="recogidaSubirProducto.php" method="post" enctype="multipart/form-data" id="productoForm">
        <div class="form-group">
                <label for="categoria">Categoria</label>
                <select id="categoria" name="categoria" class="form-control" onchange="updateUrl()">
                    <?php
                    $categorias = obtenerCategorias();
                    foreach ($categorias as $categoria) {
                        $selected = ($categoria['idCategoria'] == $_GET['cat']) ? 'selected' : '';
                        echo "<option value='{$categoria['idCategoria']}' {$selected}>{$categoria['descripcion']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="subcategoria">Subcategoria</label>
                <select class="form-control" id="subcategoria" name="subcategoria" required>
                    <?php
                    $subcategorias = obtenerSubcategorias($_GET['cat'] ?? 1);
                    foreach ($subcategorias as $subcategoria) {
                        echo "<option value='{$subcategoria['idSubcategoria']}'{$selected}>{$subcategoria['descripcion']}</option>";
                    }
                    ?>
                </select>
            </div>
            <div class="form-group">
                <label for="titulo">Título:</label>
                <input type="text" class="form-control" id="titulo" name="titulo" required>
            </div>
            <div class="form-group">
                <label for="descripcion">Descripción:</label>
                <textarea class="form-control" id="descripcion" name="descripcion" rows="4" required></textarea>
            </div>
            <div class="form-group">
                <label for="precio">Precio:</label>
                <input type="number" class="form-control" id="precio" name="precio" step="0.01" required>
            </div>
            
            <input type="hidden" name="user_id" value="<?php echo $idUsuario; ?>">
            <div class="form-group">
                <label for="imagenes">Imágenes del Producto (máximo 5):</label>
                <input type="file" id="imagenes" name="imagenes[]" accept="image/jpeg, image/png, image/jpg" multiple
                    required>
            </div>
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
            <button type="submit" class="btn btn-primary btn-block">Subir Producto</button>
            <a href="home.php" class="btn btn-secondary btn-block">Volver al Inicio</a>
        </form>

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
    <script>
        function updateUrl() {
            let categoriaSelect = document.getElementById('categoria');
            let categoriaSeleccionada = categoriaSelect.value;
            
            //Actualizar URL
            location.href = 'subirProducto.php?cat=' + categoriaSeleccionada;
        }
    </script>
</body>

</html>