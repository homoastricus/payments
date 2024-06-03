<?php

namespace Payment\Operations;

use User\UserAccount;

class FillUpOperation implements OperationInterface
{
    public function __construct(private UserAccount $userAccount, private int $value)
    {
    }

    public function toArray(): array
    {
        return [
            'userAccount' => $this->userAccount->getId(),
            'value' => $this->value,
            'type' => OperationTypes::INCOMING
        ];
    }

    public function execute(): bool
    {
        $this->userAccount->setMoneyValue($this->userAccount->getMoneyValue() + $this->value);
        return true;
    }
}