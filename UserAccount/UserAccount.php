<?php

namespace User;

class UserAccount
{
    public function __construct(private int $id, private int $moneyValue)
    {}
    public function getId(): int
    {
        return $this->id;
    }
    public function getMoneyValue(): int
    {
        return $this->moneyValue;
    }

    public function setMoneyValue(int $moneyValue): void
    {
        $this->moneyValue = $moneyValue;
    }
}