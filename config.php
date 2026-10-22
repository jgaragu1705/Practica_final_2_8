<?php
// Configuración de la base de datos.
// Estos valores tienen que ser IGUALES a los del archivo docker-compose.yml.
// Este archivo devuelve un array, y la clase Database lo leerá con un "require".
return [
    'db' => [
        'host'    => 'db',        // Nombre del servicio de MySQL en docker-compose.yml
        'dbname'  => 'practica',  // Es el valor de MYSQL_DATABASE
        'user'    => 'dev',       // Es el valor de MYSQL_USER
        'pass'    => 'dev',       // Es el valor de MYSQL_PASSWORD
        'charset' => 'utf8mb4',   // Codificación de caracteres (admite tildes, ñ y emojis)
    ],
];