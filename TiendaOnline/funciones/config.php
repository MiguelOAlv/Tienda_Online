<?php

define('DATA_DIR', __DIR__ . '/../appdata/'); //RUTA AL DIRECTORIO DE LOS DATOS
define('DATA_FILE', __DIR__ . '/../SubirImagenes/images/'); //RUTA AL FICHERO DE LAS IMAGENES
define('USER_IMAGES_DIR', __DIR__ . '/../SubirImagenes/images/users/'); //RUTA AL DIRECTORIO DE LAS IMAGENES

$ERROR = [
    'DB_CONNECTION_ERROR' => 'Error al conectar con la base de datos',
    'UNKNOWN' => 'Error desconocido',
    'AUTH_PASS' => 'La contraseña es diferente',
    'NOT_FILE' => 'El fichero no existe',
    'ATUH_USERNAME_EXISTS' => 'El usuario ya existe',
    'AUTH_USERNAME_FAIL' => 'El usuario o la contraseña no coinciden',
    'USER_NOT_FOUND' => 'Usuario no registrado',
    'ERROR_WRITE' => 'Error al escribir en el archivo de usuarios',
    'ERROR_DELETE' => 'Error al borrar en el archivo de usuarios',
    'ERROR_UPDATE' => 'Error al actualizar en el archivo de usuarios',
    'ERROR_REG' => 'No te has podido registrar, intentelo de nuevo en unos momentos',
    'AUTH_AGE_INVALID' => 'Eres menor de 18 años',
    'EMAIL_INVALID' => 'El correo no es valido',
    'AUTH_PASS_LENGTH' => 'La contraseña debe tener al menos 8 caracteres',
    'AUTH_PASS_MAYUS' => 'La contraseña debe tener al menos 1 mayuscula',
    'AUTH_PASS_NUM' => 'La contraseña debe tener al menos 1 numero',
    'PASSWORD_MISMATCH' => 'El campo Confirmar Contraseña no coincide',
    'USED_USER' => 'El nombre de usuario ya esta siendo utilizado',
    'USED_EMAIL' => 'El email ya esta siendo utilizado',
    'ERROR_IMG' => 'Error al cargar la imagen',
    'NEED_SESSION' => 'Necesitas iniciar sesión primero',
    'NO_DATA' => 'Faltan campos por rellenar en el formulario',
    'ERR_USR' => 'El nombre ya esta en uso',
    'ERR_EMAIL' => 'El email ya esta en uso',
    'ERR_ACT' => 'Error al actualizar los datos',
    'NO_FILE' => 'No se ha seleccionado ninguna imagen',
    'SIZE' => 'La imagen es demasiado grande',
    'FORMAT' => 'El archivo no tiene el formato correcto',
    'UPLOAD_ERROR' => 'Error al subir la imagen',
    'ERROR_ITEM' => 'Producto no seleccionado',
    'ERROR_ADMIN' => 'Privilegios insuficientes',
    'REMOVE_USER' => 'Usuario eliminado de la aplicación'
];

$SUCCESS = [
    'USER_REG' => 'Usuario registrado con exito',
    'USER_ACT' => 'Datos actualizados correctamente'
];

$TYPE = [
    'user' => '2',
    'admin' => '1'
];

$PRODUCTOS = [
    1 => ['nombre' => 'MONOPOLY', 'precio' => 20, 'ruta' => 'img/productos/monopoly.jpg'],
    2 => ['nombre' => 'PARTY', 'precio' => 15, 'ruta' => 'img/productos/party.jpg'],
    3 => ['nombre' => 'CARCASSONE', 'precio' => 25, 'ruta' => 'img/productos/carcassonne.jpg'],
    4 => ['nombre' => 'UNO', 'precio' => 10, 'ruta' => 'img/productos/uno.jpg'],
    5 => ['nombre' => 'CLUEDO', 'precio' => 15, 'ruta' => 'img/productos/cluedo.jpg'],
    6 => ['nombre' => 'CATAN', 'precio' => 30, 'ruta' => 'img/productos/catan.jpg'],
    7 => ['nombre' => 'RISK', 'precio' => 25, 'ruta' => 'img/productos/risk.jpg'],
    8 => ['nombre' => 'TRIVIAL', 'precio' => 20, 'ruta' => 'img/productos/trivial.jpg'],
    9 => ['nombre' => 'PREGUNTADOS', 'precio' => 15, 'ruta' => 'img/productos/uno.jpg']
];
?>