<?php

namespace Payment\Models;

class Debt extends Model
{
    public function __construct(
        public string $id,
        public string $debtor_id,
        public string $creditor_id,
        public int    $credit,
        public string $basis,
        public string $date,
    )
    {
    }
}