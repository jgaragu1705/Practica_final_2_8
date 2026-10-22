<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Models\User;

$db = new Database();
$user = new User($db);

// La tabla users está vacía, así que los dos deberían dar NULL
var_dump($user->getAll());
echo '<br>';
var_dump($user->getById(1));