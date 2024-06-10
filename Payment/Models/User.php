<?php

namespace Payment\Models;

class User extends Model
{
    public function __construct(
        public string $id,
        public int    $balance,
    )
    {
    }
}