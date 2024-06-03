<?php

namespace Payment\Operations;

use User\UserAccount;

class CashOutOperation extends AbstractOperation
{
    public function __construct(private UserAccount $userAccount, private int $value)
    {
    }

    public function toArray(): array
    {
        return [
            'userAccount' => $this->userAccount->getId(),
            'value' => $this->value,
            'type' => OperationTypes::OUTCOMING,
            'status' => $this->status
        ];
    }

    public function run(): bool
    {
        $moneyValue = $this->userAccount->getMoneyValue();
        if ($moneyValue < $this->value) {
            echo 'Not enough money on account' . PHP_EOL;
            return false;
        }
        $this->userAccount->setMoneyValue($moneyValue - $this->value);
        return true;
    }

    public function revert(): bool
    {
        $this->userAccount->setMoneyValue($this->userAccount->getMoneyValue() + $this->value);
        return true;
    }

    public function getUserAccounts(): array
    {
        return [$this->userAccount];
    }
}