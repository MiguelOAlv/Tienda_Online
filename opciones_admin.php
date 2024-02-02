<!DOCTYPE html>
<html>

<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <title>Opciones_admin</title>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <style>
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

    .footer a {
      color: white;
    }

    table {
      margin-top: 15px;
    }

    tr.cambiar-color td {
      background-color: lightblue;
    }

    .tabla {
      max-width: 100vw;
    }

    @media (max-width: 768px) {
      .tabla {
        max-width: 70vh;
      }
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
    exit();
  }
  $usuario = $_SESSION['usuario'];
  //Prohibir entradas sin ser de tipo administrador
  if ($usuario->perfil != '1') {
    session_destroy();
    $error = 'ERROR_ADMIN';
    header("Location: index.php?error=" . $error);
    exit();
  }

  $error = null;
  $success = null;
  ?>
</head>

<!-- Primer nav -->

<body class="d-flex flex-column min-vh-100 max-width-100">
  <header class="header">
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
          <li class="nav-item">
            <a class="nav-link" href="subirProducto.php">SUBIR PRODUCTOS</a>
          </li>
          <?php
          if ($usuario->perfil == '1') { ?>
            <li class="nav-item active">
                <a class="nav-link" href="limpiarFiltrosAdmin.php">ADMINISTRADOR</a>
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
  <nav class="navbar navbar-light d-flex justify-content-center align-items-center navbar-search">
    <form class="form-inline" method="post" action="recogidaBuscadorAdmin.php">
      <!-- Selector de tabla -->
      <select class="form-control mr-sm-5" id="tabla" name="columna">
        <?php
        $columnas = obtenerColumnas();
        echo "<option disabled selected>Ordenar por Columna</option>";
        foreach ($columnas as $columna) {
          echo "<option value='{$columna['Field']}'>{$columna['Field']}</option>";
        }
        ?>
      </select>

      <!-- Selector para ordenar por precio -->
      <select class="form-control mr-sm-5" name="ordenar">
        <option disabled selected>Orden</option>
        <option value="asc">Ascendente</option>
        <option value="desc">Descendente</option>
      </select>
      <input class="form-control mr-sm-2" type="text" name="buscar" placeholder="Buscar" aria-label="Buscar">
      <button class="btn btn-info my-2 my-sm-2" type="submit"><svg xmlns="http://www.w3.org/2000/svg" width="16"
          height="16" fill="currentColor" class="bi bi-search" viewBox="0 0 16 16">
          <path
            d="M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001q.044.06.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1 1 0 0 0-.115-.1zM12 6.5a5.5 5.5 0 1 1-11 0 5.5 5.5 0 0 1 11 0" />
        </svg></button>
    </form>
  </nav>

  <!-- Tabla con Usuarios -->
  <div class="container flex-grow-1 d-flex justify-content-center align-items-center">
    <!-- Contenido principal -->
    <div class="row d-flex flex-grow-1 justify-content-center align-items-center">
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
      <div class="tabla" style="overflow: auto; max-height: 80vh;">
        <table class="table table-striped table-bordered">
          <tr class="text-center">
            <th class="align-middle">ID</th>
            <th class="align-middle">Nombre de Usuario</th>
            <th class="align-middle">Email</th>
            <th class="align-middle">Nombre</th>
            <th class="align-middle">Apellido1</t>
            <th class="align-middle">Apellido2</th>
            <th class="align-middle">Direccion</th>
            <th class="align-middle">Fecha de Nacimiento</th>
            <th class="align-middle">Fecha de Creacion</th>
            <th class="align-middle">Fecha de Modificacion</th>
            <th class="align-middle">Estado</th>
            <th class="align-middle">Perfil</th>
            <th class="align-middle">Eliminar</th>
            <th class="align-middle">Artículos</th>
          </tr>
          <?php
          if (isset($_SESSION['sql_usuarios_filtrado'])) {
            $sql_usuarios = $_SESSION['sql_usuarios_filtrado'];
            $usuarios = cargarTablaFiltrada($sql_usuarios);
          } else {
            $usuarios = cargarTabla();
          }
          foreach ($usuarios as $usuario) { ?>
            <!-- Cambiar color del usuario actual en la tabla -->
            <?php if ($usuario->idUsuario == $idUsuario) {
              echo "<tr class=" . 'cambiar-color' . ">";
            } else {
              echo "<tr>";
            }
            ?>
            <td class="align-middle">
              <?php echo $usuario->idUsuario; ?>

            </td>
            <td class="align-middle">
              <?php echo $usuario->username; ?>
            </td>
            <td class="align-middle">
              <?php echo $usuario->email; ?>
            </td>
            <td class="align-middle">
              <?php echo $usuario->nombre; ?>
            </td>
            <td class="align-middle">
              <?php echo $usuario->apellido1; ?>
            </td>
            <td class="align-middle">
              <?php echo $usuario->apellido2; ?>
            </td>
            <td class="align-middle">
              <?php echo $usuario->direccion; ?>
            </td>
            <td class="align-middle">
              <?php echo $usuario->fechaNac; ?>
            </td>
            <td class="align-middle">
              <?php echo $usuario->fechaCreacion; ?>
            </td>
            <td class="align-middle">
              <?php echo $usuario->fechaModificacion; ?>
            </td>
            <td class="align-middle">
              <?php if ($usuario->perfil != '1') { ?>
                <!-- Formulario para bloquear/desbloquear -->
                <form action="recogidaAdmin.php" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="idUsuario" value="<?php echo $usuario->idUsuario; ?>">
                  <input type="hidden" name="estado" value="<?php echo $usuario->estado == 0 ? 1 : 0; ?>">
                  <input class="btn btn-warning" type="submit" name="bloquear"
                    value="<?php echo $usuario->estado == 0 ? 'Desbloquear' : 'Bloquear'; ?>">
                </form>
              <?php } ?>
            </td>
            <td class="align-middle">
              <!-- Formulario para hacer admin -->
              <form action="recogidaAdmin.php" method="post" enctype="multipart/form-data">
                <input type="hidden" name="idUsuario" value="<?php echo $usuario->idUsuario; ?>">
                <input type="hidden" name="perfil" value="<?php echo $usuario->perfil == 0 ? 1 : 0; ?>">
                <input class="btn btn-info " type="submit" name="hacerAdmin"
                  value="<?php echo $usuario->perfil == 0 ? 'Hacer Admin' : 'Quitar Admin'; ?>">
              </form>
            </td>
            <td class="align-middle">
              <?php if ($usuario->perfil != '1') { ?>
                <!-- Formulario para eliminar -->
                <form action="recogidaAdmin.php" method="post" enctype="multipart/form-data"
                  onsubmit="return confirm('¿Estás seguro de que quieres eliminar este usuario, todas sus opiniones y todos sus productos?')">
                  <input type="hidden" name="idUsuario" value="<?php echo $usuario->idUsuario; ?>">
                  <input class="btn btn-danger" type="submit" name="eliminar" value="Eliminar">
                </form>
              <?php } ?>
            </td>
            <td class="align-middle">
              <!-- Link a los productos del usuario -->
              <form action="recogidaAdmin.php" method="post">
                <input type="hidden" name="idUsuario" value="<?php echo $usuario->idUsuario; ?>">
                <input class="btn btn-primary" type="submit" value="Artículos">
              </form>
            </td>
            </tr>
            <?php
          } //Fin Bucle
          ?>
        </table>
      </div>
    </div>
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