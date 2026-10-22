<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\User;

$db = new Database();
$user = new User($db);

// Cambio solo el username del usuario 2 (pon el id de tu usuario si es otro)
$user->update(2, ['username' => 'nombre_cambiado']);

// Compruebo: el username debería haber cambiado y el resto seguir igual
var_dump($user->getById(2));