<?php

namespace Payment\Models;

use Payment\Dto\DebtDto;
use Payment\Dto\SendDto;

class Send extends Payment
{

    public function __construct(
        public int    $id,
        public int    $type,
        public int    $sender_id,
        public int    $receiver_id,
        public int    $value,
        public string $date,
        public ?int   $revert_operation_id = null,
    )
    {
    }

    public function createDebtDto(): DebtDto
    {
        return new DebtDto(
            $this->receiver_id,
            $this->sender_id,
            $this->value,
            $this->id,
        );
    }

    public function createRevertDto(): SendDto
    {
        return new SendDto(
            $this->receiver_id,
            $this->sender_id,
            $this->value,
        );
    }
}