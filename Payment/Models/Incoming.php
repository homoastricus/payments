<?php

namespace Payment\Models;

class Incoming extends Payment
{
    public function __construct(
        public string $id,
        public int    $type,
        public string $receiver_id,
        public int    $value,
        public string $date,
    )
    {
    }
}