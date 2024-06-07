<?php

namespace Payment\Models;

class Incoming extends Payment
{
    public function __construct(
        public int    $id,
        public int    $type,
        public int    $receiver_id,
        public int    $value,
        public string $date,
    )
    {
    }
}