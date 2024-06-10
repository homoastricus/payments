<?php

namespace Payment\Dto;

readonly class DebtDto
{
    public function __construct(
        public string $debtor_id,
        public string $creditor_id,
        public int    $credit,
        public string $basis,
    )
    {
    }
}