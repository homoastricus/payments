<?php

namespace Payment\Dto;

readonly class SendDto
{
    public function __construct(
        public int $sender_id,
        public int $receiver_id,
        public int $value,
    )
    {
    }
}