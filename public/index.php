<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\User;

$db = new Database();
$user = new User($db);

// Antes de borrar: debería salir un array con los datos del usuario
var_dump($user->getById(1));
echo '<br>';

$user->delete(1);

// Después de borrar: debería salir NULL
var_dump($user->getById(1));