<?php

namespace App\Core;

use PDO;
use PDOStatement; // Lo necesitamos porque query() devuelve este tipo

class Database
{
    private PDO $pdo;

    public function __construct()
    {
        // Cargamos config.php (devuelve un array). Subimos dos niveles hasta la raíz.
        $config = require __DIR__ . '/../../config.php';
        $db = $config['db'];

        $dsn = 'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'] . ';charset=' . $db['charset'];

        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $this->pdo = new PDO($dsn, $db['user'], $db['pass'], $opciones);
    }

    // Lanza una consulta SQL con sentencia preparada y devuelve el PDOStatement.
    // $sql:    la consulta con marcadores, por ejemplo: 'SELECT * FROM users WHERE id = :id'
    // $params: los valores de los marcadores, por ejemplo: ['id' => 1]
    public function query(string $sql, array $params = []): PDOStatement
    {
        // 1) prepare: MySQL recibe la consulta con los marcadores, sin datos todavía
        $sentencia = $this->pdo->prepare($sql);

        // 2) execute: ahora se envían los valores por separado y se ejecuta
        $sentencia->execute($params);

        return $sentencia;
    }
}