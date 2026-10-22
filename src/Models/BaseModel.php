<?php

namespace App\Models;

use App\Core\Database;

// abstract: no se puede hacer new BaseModel, solo sirve para que otras clases hereden
abstract class BaseModel
{
    // protected: las pueden usar BaseModel y las clases que hereden (User, Post...)
    protected Database $db;
    protected string $table;

    // Cada modelo le pasa la conexión (Database) y el nombre de su tabla
    public function __construct(Database $db, string $table)
    {
        $this->db = $db;
        $this->table = $table;
    }

    // Devuelve todos los registros de la tabla (o null si está vacía)
    public function getAll(): ?array
    {
        // El nombre de la tabla va concatenado porque no se puede poner como marcador.
        // No pasa nada porque lo escribimos nosotros, no viene del usuario.
        return $this->db->fetchAll('SELECT * FROM ' . $this->table);
    }

    // Devuelve el registro que tenga ese id (o null si no existe)
    public function getById(int $id): ?array
    {
        // El id SÍ es un dato, así que va con marcador :id y no concatenado
        return $this->db->fetchOne(
            'SELECT * FROM ' . $this->table . ' WHERE id = :id',
            ['id' => $id]
        );
    }

    // Borra el registro que tenga ese id. No devuelve nada (void).
    public function delete(int $id): void
    {
        // Uso query() porque un DELETE no devuelve filas para leer
        $this->db->query(
            'DELETE FROM ' . $this->table . ' WHERE id = :id',
            ['id' => $id]
        );
    }

    // Añade un registro nuevo. $data tiene pares campo => valor,
    // por ejemplo: ['username' => 'javi', 'email' => 'javi@correo.com']
    public function insert(array $data): void
    {
        // Aquí voy a ir guardando los nombres de las columnas y los marcadores
        $columnas = [];
        $marcadores = [];

        // Recorro el array: $campo es la clave (username) y $valor lo que vale
        foreach ($data as $campo => $valor) {
            $columnas[] = $campo;            // columnas: username, email...
            $marcadores[] = ':' . $campo;    // marcadores: :username, :email...
        }

        // implode junta los elementos del array separándolos con ", "
        // Queda algo como: INSERT INTO users (username, email) VALUES (:username, :email)
        $sql = 'INSERT INTO ' . $this->table
            . ' (' . implode(', ', $columnas) . ')'
            . ' VALUES (' . implode(', ', $marcadores) . ')';

        // Le paso $data tal cual como valores: sus claves coinciden con los marcadores
        $this->db->query($sql, $data);
    }
}