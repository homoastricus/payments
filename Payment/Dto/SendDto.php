<?php

namespace Payment\Dto;

readonly class SendDto
{
    public function __construct(
        public string $sender_id,
        public string $receiver_id,
        public int    $value,
    )
    {
    }
}