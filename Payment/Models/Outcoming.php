<?php

namespace Payment\Models;

class Outcoming extends Payment
{
    public function __construct(
        public int    $id,
        public int    $sender_id,
        public int    $value,
        public int    $type,
        public string $date,
    )
    {
    }
}