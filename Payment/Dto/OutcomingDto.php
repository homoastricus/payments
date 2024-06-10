<?php

namespace Payment\Dto;

readonly class OutcomingDto
{
    public function __construct(
        public string $sender_id,
        public int    $value,
    )
    {
    }
}