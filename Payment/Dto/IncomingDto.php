<?php

namespace Payment\Dto;

readonly class IncomingDto
{
    public function __construct(
        public string $receiver_id,
        public int    $value,
    )
    {
    }
}