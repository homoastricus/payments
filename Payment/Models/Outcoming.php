<?php

namespace Payment\Models;

class Outcoming extends Payment
{
    public function __construct(
        public string $id,
        public string $sender_id,
        public int    $value,
        public int    $type,
        public string $date,
    )
    {
    }
}