<?php

declare(strict_types=1);

namespace Core;

/**
 * Base model — provides database access to all domain models.
 */
abstract class Model
{
    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }
}
