<?php

namespace App\Models;

use App\Core\Database;

// abstract: esta clase no se puede usar directamente (no puedo hacer new BaseModel).
// Solo sirve para que otras clases hereden de ella, como en Java.
abstract class BaseModel
{
    // protected: la pueden usar BaseModel y las clases que hereden de ella (User, Post...)
    protected Database $db;
    protected string $table;

    // Cada modelo le pasará la conexión (Database) y el nombre de su tabla
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
        // Aquí el id SÍ es un dato, así que va con marcador :id y no concatenado
        return $this->db->fetchOne(
            'SELECT * FROM ' . $this->table . ' WHERE id = :id',
            ['id' => $id]
        );
    }
}