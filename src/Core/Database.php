<?php

namespace App\Core;

use PDO;
use PDOStatement;

class Database
{
    private PDO $pdo;

    public function __construct()
    {
        $config = require __DIR__ . '/../../config.php';
        $db = $config['db'];

        $dsn = 'mysql:host=' . $db['host'] . ';dbname=' . $db['dbname'] . ';charset=' . $db['charset'];

        $opciones = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        $this->pdo = new PDO($dsn, $db['user'], $db['pass'], $opciones);
    }

    // Lanza una consulta con sentencia preparada y devuelve el PDOStatement
    public function query(string $sql, array $params = []): PDOStatement
    {
        $sentencia = $this->pdo->prepare($sql);
        $sentencia->execute($params);

        return $sentencia;
    }

    // Devuelve TODAS las filas del resultado, o null si no hay ninguna
    public function fetchAll(string $sql, array $params = []): ?array
    {
        // Reutilizamos query() para lanzar la consulta
        $sentencia = $this->query($sql, $params);

        // fetchAll() devuelve un array con todas las filas (vacío si no hay ninguna)
        $filas = $sentencia->fetchAll();

        // Si el array está vacío, devolvemos null como pide el enunciado
        if (empty($filas)) {
            return null;
        }

        return $filas;
    }

    // Devuelve SOLO la primera fila del resultado, o null si no hay ninguna
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $sentencia = $this->query($sql, $params);

        // fetch() devuelve la primera fila, o false si no hay ninguna
        $fila = $sentencia->fetch();

        // Si no hay fila, devolvemos null en vez de false
        if ($fila === false) {
            return null;
        }

        return $fila;
    }
}