<?php

namespace Payment\Models;

class User extends Model
{
    public function __construct(
        public int $id,
        public int $balance,
    )
    {
    }
}