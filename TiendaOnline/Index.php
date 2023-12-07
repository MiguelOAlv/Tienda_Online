<!DOCTYPE html>
<html lang="es">

<head>
  <meta charset='utf-8'>
  <meta http-equiv='X-UA-Compatible' content='IE=edge'>
  <title>Inicio de Sesión</title>
  <meta name='viewport' content='width=device-width, initial-scale=1'>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
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

    .form {
      width: 70%;
      margin: 0 auto;
      background-color: #fff;
      padding: 20px;
      border-radius: 10px;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-control {
      width: 100%;
    }

    .boton {
      width: 100%;
    }

    .boton2 {
      width: 100%;
    }
  </style>
  <?php
  $error = null;
  $type = null;
  $success = null;
  ?>
</head>

<body>
  <header>
    <h1 class="text-center">Tienda Online</h1>
  </header>

  <div class="container">
    <div class="row">
      <div class="col-md-6 offset-md-3">
        <div class="form">
          <form method="post" action="recogida.php">
            <div class="form-group">
              <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre/Email" required>
            </div>
            <div class="form-group">
              <input type="password" class="form-control" id="contrasena" name="contrasena" placeholder="Contraseña"
                required>
            </div>
            <?php
            include 'funciones/config.php';
            if (isset($_GET["error"])) {
              $error = $_GET["error"];
              if (isset($ERROR[$error])) {
                $MensajeError = $ERROR[$error];
                echo '<p class="text-danger">' . $MensajeError . '</p>';
              }
            }
            if (isset($_GET["success"])) {
              $success = $_GET["success"];
              if (isset($SUCCESS[$success])) {
                $MensajeExito = $SUCCESS[$success];
                echo '<p class="text-success">' . $MensajeExito . '</p>';
              }
            }
            ?>
            <button type="submit" class="btn btn-primary btn-block boton">ENTRAR</button>
            <a href="registro.php" class="btn btn-secondary btn-block boton2">REGISTRO</a>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
</body>

</html>
