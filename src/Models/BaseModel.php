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
        // Uso query() porque un DELETE no devuelve filas para leer.
        // Igual que antes: la tabla concatenada, el id con marcador.
        $this->db->query(
            'DELETE FROM ' . $this->table . ' WHERE id = :id',
            ['id' => $id]
        );
    }
}