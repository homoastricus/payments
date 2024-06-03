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
        return [
            'userAccount' => $this->userAccount->getId(),
            'value' => $this->value,
            'type' => OperationTypes::INCOMING,
            'status' => $this->status
        ];
    }

    public function run(): bool
    {
        $this->userAccount->setMoneyValue($this->userAccount->getMoneyValue() + $this->value);
        return true;
    }

    public function revert(): bool
    {
        if ($this->userAccount->getMoneyValue() < $this->value) {
            return false;
        }
        $this->userAccount->setMoneyValue($this->userAccount->getMoneyValue() - $this->value);
        return true;
    }

    public function getUserAccounts(): array
    {
        return [$this->userAccount];
    }
}