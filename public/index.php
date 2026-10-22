<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\User;

$db = new Database();
$user = new User($db);

// Inserto un usuario de prueba (la contraseña en claro es solo para probar)
$user->insert([
    'username' => 'iñaki',
    'email'    => 'iñaki@gmail.com',
    'password' => 'Loquesea1234',
]);

// Compruebo que está: debería salir un array con 1 usuario
var_dump($user->getAll());