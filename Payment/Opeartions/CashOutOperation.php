<?php

namespace Payment\Operations;

use User\UserAccount;

class CashOutOperation implements OperationInterface
{

    public function __construct(private UserAccount $userAccount, private int $value)
    {
    }

    public function toArray(): array
    {
        return [
            'userAccount' => $this->userAccount->getId(),
            'value' => $this->value,
            'type' => OperationTypes::OUTCOMING
        ];
    }

    public function execute(): bool
    {
        $moneyValue = $this->userAccount->getMoneyValue();
        if ($moneyValue < $this->value) {
            return false;
        }
        $this->userAccount->setMoneyValue($moneyValue - $this->value);
        return true;
    }
}