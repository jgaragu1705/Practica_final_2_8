<?php

namespace App\Models;

use App\Core\Database;

// User hereda todo lo de BaseModel: getAll() y getById()
class User extends BaseModel
{
    public function __construct(Database $db)
    {
        // Llamo al constructor de la clase padre diciéndole mi tabla: "users"
        parent::__construct($db, 'users');
    }
}