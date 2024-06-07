<?php

namespace Payment\Models;

class Debt extends Model
{
    public function __construct(
        public int    $id,
        public int    $debtor_id,
        public int    $creditor_id,
        public int    $credit,
        public int    $basis,
        public string $date,
    )
    {
    }
}