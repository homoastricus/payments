<?php

namespace Payment\Operations;

use User\UserAccount;

class FillUpOperation extends AbstractOperation
{
    public function __construct(private UserAccount $userAccount, private int $value)
    {
    }

    public function toArray(): array
    {
        $info = [
            'userAccount' => $this->userAccount->getId(),
            'value' => $this->value,
            'type' => OperationTypes::INCOMING,
        ];

        return array_merge(parent::toArray(), $info);
    }

    public function run(): bool
    {
        echo 'Start ' . OperationTypes::INCOMING . ' operation.'
            . ' User: ' . $this->userAccount->getId()
            . ' Value: ' .$this->value . PHP_EOL;

        $this->userAccount->setMoneyValue($this->userAccount->getMoneyValue() + $this->value);

        echo 'Finish ' . OperationTypes::INCOMING . ' operation.' . PHP_EOL;

        return true;
    }

    public function revert(): bool
    {
        if ($this->userAccount->getMoneyValue() < $this->value) {
            echo 'Not enough money on account' . PHP_EOL;
            return false;
        }
        $this->userAccount->setMoneyValue($this->userAccount->getMoneyValue() - $this->value);
        echo 'Revert operation complete.' . PHP_EOL;
        return true;
    }

    public function getUserAccounts(): array
    {
        return [$this->userAccount];
    }
}