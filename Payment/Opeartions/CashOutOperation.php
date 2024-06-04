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
        echo 'Start ' . OperationTypes::OUTCOMING . 'operation.'
            . ' User: ' . $this->userAccount->getId()
            . ' Value: ' .$this->value . PHP_EOL;

        $moneyValue = $this->userAccount->getMoneyValue();
        if ($moneyValue < $this->value) {
            echo 'Not enough money on account' . PHP_EOL;
            return false;
        }
        $this->userAccount->setMoneyValue($moneyValue - $this->value);

        echo 'Finish ' . OperationTypes::OUTCOMING . ' operation.' . PHP_EOL;

        return true;
    }

    public function revert(): bool
    {
        $this->userAccount->setMoneyValue($this->userAccount->getMoneyValue() + $this->value);
        echo 'Revert operation complete.' . PHP_EOL;
        return true;
    }

    public function getUserAccounts(): array
    {
        return [$this->userAccount];
    }
}