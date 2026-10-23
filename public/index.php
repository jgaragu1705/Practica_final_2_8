<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\User;

$db = new Database();
$user = new User($db);

// Le pongo al usuario 2 la contraseña "1234", pero guardada con hash
$user->update(2, ['password' => password_hash('1234', PASSWORD_DEFAULT)]);

// Caso 1: email y contraseña correctos -> debería salir int(2)
var_dump($user->findByEmail('iñaki@gmail.com', '1234'));
echo '<br>';

// Caso 2: contraseña incorrecta -> debería salir NULL
var_dump($user->findByEmail('iñaki@gmail.com', 'mala'));
echo '<br>';

// Caso 3: email que no existe -> debería salir NULL
var_dump($user->findByEmail('noexiste@correo.com', '1234'));