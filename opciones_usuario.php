<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset='utf-8'>
    <meta http-equiv='X-UA-Compatible' content='IE=edge'>
    <title>Opciones_usuario</title>
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
            color: red;
            display:flex;
            margin-top: 5px;
            align-items: center;
            justify-content: center;
        }

        .exito {
            color: green;
            display:flex;
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
    //Prohibir entradas sin iniciar sesion
    if ($_SESSION['id'] == null) {
        session_destroy();
        $error = 'NEED_SESSION';
        header("Location: index.php?error=" . $error);
    }
    $usuario = $_SESSION['usuario'];
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
                    <li class="nav-item">
                        <a class="nav-link" href="home.php">PRODUCTOS</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="subirProducto.php">SUBIR PRODUCTOS</a>
                    </li>
                    <?php
                    if ($usuario->perfil == '1') { ?>
                        <li class="nav-item">
                            <a class="nav-link" href="opciones_admin">ADMINISTRADOR</a>
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
            <li class="nav-item mx-3">
                <a class="nav-link" href="misProductos.php">MIS PRODUCTOS</a>
            </li>
            <li class="nav-item mx-3">
                <a class="nav-link" href="misValoraciones.php">MIS VALORACIONES</a>
            </li>
        </ul>
    </div>
</nav>




    <!-- Formulario Opciones -->
    <div class="container">
        <?php
        $user_id = $_SESSION['idUsuario'];
        $conn = get_connection();

        // Consultar los datos del usuario
        $sql = "SELECT username, email, pass, nombre, apellido1, apellido2, direccion, fechaModificacion FROM usuarios WHERE idUsuario = $user_id";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            // Mostrar el formulario
            $row = $result->fetch_assoc();
            ?>
            <form action="recogidaUsuario.php" method="post">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" class="form-control" name="username" value="<?php echo $row['username']; ?>"
                        readonly>
                </div>

                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" class="form-control" name="email" value="<?php echo $row['email']; ?>" readonly>
                </div>
                <div class="form-group">
                    <label for="pass">Nueva Contraseña:</label>
                    <input type="password" class="form-control" name="pass">
                </div>
                <div class="form-group">
                    <label for="confirm_pass">Confirmar Contraseña:</label>
                    <input type="password" class="form-control" name="confirm_pass">
                </div>
                <div class="form-group">
                    <label for="nombre">Nombre:</label>
                    <input type="text" class="form-control" name="nombre" value="<?php echo $row['nombre']; ?>">
                </div>

                <div class="form-group">
                    <label for="apellido1">Apellido 1:</label>
                    <input type="text" class="form-control" name="apellido1" value="<?php echo $row['apellido1']; ?>">
                </div>

                <div class="form-group">
                    <label for="apellido2">Apellido 2:</label>
                    <input type="text" class="form-control" name="apellido2" value="<?php echo $row['apellido2']; ?>">
                </div>

                <div class="form-group">
                    <label for="direccion">Dirección:</label>
                    <input type="text" class="form-control" name="direccion" value="<?php echo $row['direccion']; ?>">
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
                <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">
                <button type="submit" class="btn btn-primary">Actualizar</button>
            </form>
            <?php
        } else {
            echo "<h1>Usuario no encontrado</h1>";
        }
        // Cerrar la conexión
        $conn->close();
        ?>
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