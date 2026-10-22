<?php

require __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;

$db = new Database();

// Prueba de fetchOne: una sola fila
$fila = $db->fetchOne('SELECT 5 + :n AS suma', ['n' => 3]);
echo 'fetchOne: ' . $fila['suma'] . '<br>';

// Prueba de fetchAll: dos filas unidas con UNION
$filas = $db->fetchAll('SELECT 1 AS numero UNION SELECT 2 AS numero');
echo 'fetchAll devuelve ' . count($filas) . ' filas<br>';