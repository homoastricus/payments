<?php

namespace Payment\Dto;

readonly class IncomingDto
{
    public function __construct(
        public int $receiver_id,
        public int $value,
    )
    {
    }
}