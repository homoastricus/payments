<?php

namespace Payment\Dto;

readonly class OutcomingDto
{
    public function __construct(
        public int $sender_id,
        public int $value,
    )
    {
    }
}