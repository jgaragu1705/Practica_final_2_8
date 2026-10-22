<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

$db = new Database();

// Calcula 5 + el valor del marcador :n, que le pasamos aparte (3)
$resultado = $db->query('SELECT 5 + :n AS suma', ['n' => 3]);

// fetch() saca una fila del resultado
$fila = $resultado->fetch();

echo 'La suma es: ' . $fila['suma'];