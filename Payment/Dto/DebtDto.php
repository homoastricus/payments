<?php

namespace Payment\Dto;

readonly class DebtDto
{
    public function __construct(
        public int $debtor_id,
        public int $creditor_id,
        public int $credit,
        public int $basis,
    )
    {
    }
}