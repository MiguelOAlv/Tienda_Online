<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
  <title>Registro</title>
  <style>
    body {
      background-color: #f0f0f0;
      margin-bottom: 50px;
    }

    header {
      background-color: #343a40;
      background-size: cover;
      background-position: center;
      color: white;
      text-align: center;
      padding: 25px;
    }

    .container {
      margin-top: 50px;
    }

    .error {
      color: red;
    }

    .exito {
      color: green;
    }
  </style>
  <?php
  require_once 'funciones/config.php';
  require_once 'funciones/p1_lib.php';
  require_once 'entities/ent_producto.php';
  require_once 'entities/ent_usuario.php';
  require_once 'entities/ent_imagenes.php';
  $error = null;
  $type = null;
  $success = null;
  ?>
</head>

<body>
  <header>
    <h1 class="text-center">Formulario de Registro</h1>
  </header>
  <div class="container">
    <div class="row">
      <div class="col-md-6 offset-md-3">

        <form action="recogidaRegistro.php" method="post" enctype="multipart/form-data">
          <div class="form-group">
            <label for="nombreUsuario">Nombre de Usuario:</label>
            <input type="text" class="form-control" id="nombreUsuario" name="nombreUsuario" required>
          </div>
          <div class="form-group">
            <label for="email">Email:</label>
            <input type="email" class="form-control" id="email" name="email" required>
          </div>
          <div class="form-group">
            <label for="password">Contraseña:</label>
            <input type="password" class="form-control" id="password" name="password" required>
          </div>
          <div class="form-group">
            <label for="confirmarPassword">Confirmar Contraseña:</label>
            <input type="password" class="form-control" id="confirmarPassword" name="confirmarPassword" required>
          </div>
          <div class="form-group">
            <label for="nombre">Nombre:</label>
            <input type="text" class="form-control" id="nombre" name="nombre">
          </div>
          <div class="form-group">
            <label for="apellido1">Primer Apellido:</label>
            <input type="text" class="form-control" id="apellido1" name="apellido1">
          </div>
          <div class="form-group">
            <label for="apellido2">Segundo Apellido:</label>
            <input type="text" class="form-control" id="apellido2" name="apellido2">
          </div>
          <div class="form-group">
            <label for="direccion">Dirección:</label>
            <input type="text" class="form-control" id="direccion" name="direccion">
          </div>
          <div class="form-group">
            <label for="fechaNacimiento">Fecha de Nacimiento:</label>
            <input type="date" class="form-control" id="fechaNacimiento" name="fechaNacimiento" required>
          </div>
          <div class="form-group">
            <label for="avatar">Avatar:</label>
            <input type="file" class="form-control-file" id="avatar" name="avatar"
              accept="image/jpeg, image/png, image/jpg">
            <small class="form-text text-muted">Formatos permitidos: JPG, JPEG, PNG. Tamaño máximo: 10MB.</small>
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
          <button type="submit" class="btn btn-primary btn-block">Registrarse</button>
          <a href="index.php" class="btn btn-secondary btn-block">Volver al Inicio</a>
        </form>

      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>

</body>

</html>